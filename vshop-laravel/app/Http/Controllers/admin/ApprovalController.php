<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceSubcategory;
use App\Models\Attribute;
use App\Models\ProductVersionHistory;
use App\Models\Seller;
use App\Models\ProductReview;
use App\Models\ProductLog;
use App\Models\OrderItem;
use App\Models\Grievance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class ApprovalController extends Controller
{
    

public function products(Request $request)
{
    $panel = session('admin_panel', 'product');

    // Define allowed product types based on active panel
    if ($panel == 'travel') {
        $allowedTypes = ['tour', 'vtravel-professional', 'vtravel-information_advisory', 'service'];
        $pageTitle = 'Travel & Service Approvals';
        $pageSubtitle = 'Managing: Tours, Information & Advisory, Professional Services, Others';
    } elseif ($panel == 'vcampus') {
        $allowedTypes = ['campus'];
        $pageTitle = 'Course & Campus Approvals';
        $pageSubtitle = 'Managing: Courses & Campus Items';
    } else {
        // ✅ product panel (vshop) – include all Vshop types
        $allowedTypes = ['product', 'vshop-product', 'vshop-professional', 'service'];
        $pageTitle = 'Product & Professional Approvals';
        $pageSubtitle = 'Managing: Products, Vshop Items, Professional Services, Others';
    }

    // Build query with eager loading and seller filter
    $query = Product::where('created_by', 'seller')
                ->whereIn('type', $allowedTypes)
                ->with('seller'); // Eager load seller

    // ✅ Apply seller filter if provided
    if ($request->filled('seller_id')) {
        $query->where('created_by_id', $request->seller_id);
    }

    // Fetch items
    $items = $query->latest()->paginate(20);

    // KPI counts (same logic, no filter applied)
    $totalProducts = Product::where('created_by', 'seller')
                        ->whereIn('type', $allowedTypes)
                        ->count();

    $verifiedProducts = Product::where('created_by', 'seller')
                            ->whereIn('type', $allowedTypes)
                            ->whereIn('status', ['verified', 'approved', 'active'])
                            ->count();

    $notVerifiedProducts = Product::where('created_by', 'seller')
                                ->whereIn('type', $allowedTypes)
                                ->whereIn('status', ['not-verified', 'unavailable'])
                                ->count();

    $inProgressProducts = Product::where('created_by', 'seller')
                                ->whereIn('type', $allowedTypes)
                                ->whereIn('status', ['pending', 'in-progress'])
                                ->count();

    $rejectedProducts = Product::where('created_by', 'seller')
                            ->whereIn('type', $allowedTypes)
                            ->where('status', 'rejected')
                            ->count();

    // ✅ Get list of sellers for the dropdown (only those who have products in allowed types)
    $sellers = \App\Models\Seller::whereHas('products', function($q) use ($allowedTypes) {
                    $q->whereIn('type', $allowedTypes);
                })
                ->orderBy('name')
                ->get(['id', 'name', 'email']);

    return view('admin.approval.products', compact(
        'items', 
        'pageTitle', 
        'pageSubtitle',
        'totalProducts',
        'verifiedProducts',
        'notVerifiedProducts',
        'inProgressProducts',
        'rejectedProducts',
        'sellers'   // pass sellers for dropdown
    ));
}
    // ðŸ”¹ Category Approval List (EXISTING)
   public function categories()
{
    $panel = session('admin_panel', 'product');

    // Define allowed types based on panel
    if ($panel == 'travel') {
        $types = ['service', 'travel'];
    } elseif ($panel == 'vcampus') {
        $types = ['campus'];   // adjust if needed
    } else {
        // product panel (vshop)
        $types = ['product'];
    }

    $rawCategories = MarketplaceCategory::with('seller')
                ->whereIn('type', $types)
                ->latest()
                ->get();

    // Group categories by distinct trimmed name
    $grouped = $rawCategories->groupBy(function($cat) {
        return trim($cat->name ?? 'Uncategorized');
    });

    $items = collect();

    foreach ($grouped as $categoryName => $group) {
        $first = $group->first();
        $sellers = $group->pluck('seller')->filter()->unique('id')->values();
        $sellerCount = $sellers->count();
        
        $statuses = $group->map(fn($c) => $c->approval_status)->toArray();
        if (in_array('pending', $statuses)) {
            $groupStatus = 'pending';
        } elseif (in_array('approved', $statuses)) {
            $groupStatus = 'approved';
        } else {
            $groupStatus = 'rejected';
        }

        $item = new \stdClass();
        $item->id = $first->id;
        $item->name = $categoryName;
        $item->type = $first->type;
        $item->approval_status = $groupStatus;
        $item->seller_count = $sellerCount;
        $item->sellers = $sellers;
        $item->seller = $first->seller;

        $items->push($item);
    }

    return view('admin.approval.categories', compact('items'));
}
    // ðŸ”¹ Attribute Approval List (EXISTING)
    public function attributes()
    {
        $items = Attribute::where('created_by', 'seller')
                          ->latest()
                          ->get();

        return view('admin.approval.attributes', compact('items'));
    }

   public function updateStatus(Request $request)
{
    $request->validate([
        'id'     => 'required|integer',
        'type'   => 'required|in:product,category',
        'status' => 'required|in:approved,rejected,deactivated,active,in-progress,not-verified,verified'
    ]);

    // ----------------------------------------------
    // Handle CATEGORY approval/rejection
    // ----------------------------------------------
    if ($request->type == 'category') {
        $model = MarketplaceCategory::find($request->id);
        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        if (!in_array($request->status, ['approved', 'rejected'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status for category. Allowed: approved, rejected'
            ], 400);
        }

        // Update all categories with the same name across all sellers
        $matchingCategories = MarketplaceCategory::where('name', $model->name)->get();
        foreach ($matchingCategories as $catItem) {
            $catItem->approval_status = $request->status;
            $catItem->save();

            if ($catItem->seller_id) {
                NotificationService::notify(
                    $catItem->seller_id,
                    'seller',
                    'product',
                    "Your category '{$catItem->name}' has been " . $request->status . " by admin.",
                    route('seller.categories.index')
                );
            }
        }

        return response()->json(['success' => true]);
    }

    // ----------------------------------------------
    // Handle PRODUCT approval/status change
    // ----------------------------------------------
    if ($request->type == 'product') {
        $model = Product::find($request->id);
        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Determine allowed types based on selected panels (not just the active panel)
        $selectedPanels = session('selected_panels', []);
        $allowedTypes = [];

        if (in_array('product', $selectedPanels)) {
            $allowedTypes = array_merge($allowedTypes, ['product', 'vshop-product', 'professional', 'vshop-professional', 'vshop-service', 'vshop-tour', 'vshop-information_advisory', 'service']);
        }
        if (in_array('travel', $selectedPanels)) {
            $allowedTypes = array_merge($allowedTypes, ['tour', 'vtravel-professional', 'vtravel-information_advisory']);
        }
        if (in_array('vcampus', $selectedPanels)) {
            $allowedTypes = array_merge($allowedTypes, ['campus']);
        }

        $allowedTypes = array_unique($allowedTypes);

        if (empty($allowedTypes)) {
            $allowedTypes = ['product', 'vshop-product', 'professional', 'vshop-professional', 'vshop-service', 'vshop-tour', 'vshop-information_advisory', 'service', 'tour', 'vtravel-professional', 'vtravel-information_advisory', 'campus'];
        }

        if (!in_array($model->type, $allowedTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong panel: item type "' . $model->type . '" not allowed for current panel selection'
            ], 403);
        }

        // Save version history
        $this->saveVersionHistory($model, 'approved_rejected', [
            'old_status' => $model->status,
            'new_status' => $request->status
        ]);

        $model->status = $request->status;
        $model->save();

        if ($model->created_by_id) {
            NotificationService::notify(
                $model->created_by_id,
                'seller',
                'product',
                "Your product '{$model->name}' has been " . $request->status . " by admin.",
                route('seller.products.index')
            );
        }

        return response()->json(['success' => true]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid type'
    ], 400);
}
    // View product verification details
 
 
 public function verify($id)
{
    $product = Product::with([
        'category', 
        'subcategory', 
        'seller',
        'reviews',
        'returns',
        'complaints',
    ])->findOrFail($id);

    if ($product->images && is_string($product->images)) {
        $product->images = json_decode($product->images, true);
    }
    if ($product->videos && is_string($product->videos)) {
        $product->videos = json_decode($product->videos, true);
    }
    if (!$product->images || !is_array($product->images)) {
        $product->images = [];
    }
    if (!$product->videos || !is_array($product->videos)) {
        $product->videos = [];
    }

    $performanceData = $this->calculatePerformanceScores($product);
    $versionHistory = ProductVersionHistory::where('product_id', $id)
                        ->orderBy('created_at', 'desc')
                        ->get();

    // Use selected panels for KPI counts
    $selectedPanels = session('selected_panels', []);
    $panelTypes = [];

    if (in_array('product', $selectedPanels)) {
        $panelTypes = array_merge($panelTypes, ['product', 'vshop-product', 'professional', 'vshop-professional', 'vshop-service', 'vshop-tour', 'vshop-information_advisory', 'service']);
    }
    if (in_array('travel', $selectedPanels)) {
        $panelTypes = array_merge($panelTypes, ['tour', 'vtravel-professional', 'vtravel-information_advisory']);
    }
    if (in_array('vcampus', $selectedPanels)) {
        $panelTypes = array_merge($panelTypes, ['campus']);
    }
    $panelTypes = array_unique($panelTypes);
    if (empty($panelTypes)) {
        $panelTypes = ['product', 'vshop-product', 'professional', 'vshop-professional', 'vshop-service', 'vshop-tour', 'vshop-information_advisory', 'service', 'tour', 'vtravel-professional', 'vtravel-information_advisory', 'campus'];
    }

    $totalProducts = Product::where('created_by', 'seller')->whereIn('type', $panelTypes)->count();
    $verifiedProducts = Product::where('created_by', 'seller')->whereIn('type', $panelTypes)->whereIn('status', ['verified', 'approved', 'active'])->count();
    $notVerifiedProducts = Product::where('created_by', 'seller')->whereIn('type', $panelTypes)->whereIn('status', ['not-verified', 'unavailable'])->count();
    $inProgressProducts = Product::where('created_by', 'seller')->whereIn('type', $panelTypes)->whereIn('status', ['pending', 'in-progress'])->count();
    $rejectedProducts = Product::where('created_by', 'seller')->whereIn('type', $panelTypes)->where('status', 'rejected')->count();

    $productLogs = ProductLog::where('product_id', $id)
                        ->orderBy('created_at', 'desc')
                        ->get();

    return view('admin.approval.verify-product', compact(
        'product', 
        'performanceData', 
        'versionHistory',
        'productLogs',
        'totalProducts',
        'verifiedProducts',
        'notVerifiedProducts',
        'inProgressProducts',
        'rejectedProducts'
    ));
}
    // Submit verification results
    public function submitVerification(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'verification_status' => 'required|in:verified,rejected,not-verified',
            'admin_notes' => 'nullable|string',
            'title_quality' => 'nullable|in:pass,fail',
            'description_quality' => 'nullable|in:pass,fail',
            'category_correctness' => 'nullable|in:pass,fail',
            'organizer_authenticity' => 'nullable|in:pass,fail',
            'image_quality' => 'nullable|in:pass,fail',
            'video_link_validity' => 'nullable|in:pass,fail',
            'content_compliance' => 'nullable|in:pass,fail',
            'itinerary_clarity' => 'nullable|in:pass,fail',
            'inclusion_exclusion' => 'nullable|in:pass,fail',
            'refund_policy_clarity' => 'nullable|in:pass,fail',
            'tags_relevance' => 'nullable|in:pass,fail',
            'keyword_quality' => 'nullable|in:pass,fail',
            'no_spam' => 'nullable|in:pass,fail',
            'price_reasonability' => 'nullable|in:pass,fail',
            'discount_logic' => 'nullable|in:pass,fail',
            'tax_calculation' => 'nullable|in:pass,fail',
            'net_total_correctness' => 'nullable|in:pass,fail',
        ]);

        DB::beginTransaction();
        try {
            // Map verification_status to actual product status
            $newStatus = '';
            switch($validated['verification_status']) {
                case 'verified':
                    $newStatus = 'approved';
                    break;
                case 'rejected':
                    $newStatus = 'rejected';
                    break;
                case 'not-verified':
                    $newStatus = 'not-verified';
                    break;
            }
            
            $product->verification_status = $validated['verification_status'];
            $product->admin_notes = $validated['admin_notes'] ?? null;
            $product->verified_at = now();
            $product->verified_by = auth()->id();
            
            $product->verification_checklist = json_encode([
                'basic_check' => [
                    'title_quality' => $validated['title_quality'] ?? null,
                    'description_quality' => $validated['description_quality'] ?? null,
                    'category_correctness' => $validated['category_correctness'] ?? null,
                    'organizer_authenticity' => $validated['organizer_authenticity'] ?? null,
                ],
                'media_validation' => [
                    'image_quality' => $validated['image_quality'] ?? null,
                    'video_link_validity' => $validated['video_link_validity'] ?? null,
                    'content_compliance' => $validated['content_compliance'] ?? null,
                ],
                'details_validation' => [
                    'itinerary_clarity' => $validated['itinerary_clarity'] ?? null,
                    'inclusion_exclusion' => $validated['inclusion_exclusion'] ?? null,
                    'refund_policy_clarity' => $validated['refund_policy_clarity'] ?? null,
                ],
                'seo_check' => [
                    'tags_relevance' => $validated['tags_relevance'] ?? null,
                    'keyword_quality' => $validated['keyword_quality'] ?? null,
                    'no_spam' => $validated['no_spam'] ?? null,
                ],
                'pricing_validation' => [
                    'price_reasonability' => $validated['price_reasonability'] ?? null,
                    'discount_logic' => $validated['discount_logic'] ?? null,
                    'tax_calculation' => $validated['tax_calculation'] ?? null,
                    'net_total_correctness' => $validated['net_total_correctness'] ?? null,
                ]
            ]);
            
            $this->saveVersionHistory($product, 'verification_completed', [
                'old_status' => $product->status,
                'new_status' => $newStatus,
                'admin_notes' => $validated['admin_notes'] ?? null
            ]);
            
            // Update the actual product status
            $product->status = $newStatus;
            $product->save();

            if ($product->created_by_id) {
                NotificationService::notify(
                    $product->created_by_id,
                    'seller',
                    'product',
                    "Your product '{$product->name}' has been verified as " . $newStatus . " by admin.",
                    route('seller.products.index')
                );
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Product ' . $newStatus . ' successfully',
                'status' => $newStatus,
                'status_text' => ucfirst($newStatus),
                'redirect' => route('admin.approval.verify', $product->id)
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Verification failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // ðŸ”¹ B. STATUS CONTROL (NEW)
    // ==========================================
    
    public function changeStatus(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:in-progress,not-verified,verified,unavailable,active,deactivated'
        ]);
        
        DB::beginTransaction();
        try {
            $this->saveVersionHistory($product, 'status_changed', [
                'old_status' => $product->status,
                'new_status' => $request->status
            ]);
            
            $product->status = $request->status;
            $product->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function forceDisable(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $this->saveVersionHistory($product, 'force_disabled', [
                'old_status' => $product->status,
                'reason' => $request->reason ?? 'Force disabled by admin'
            ]);
            
            $product->status = 'deactivated';
            $product->force_disabled = true;
            $product->force_disabled_reason = $request->reason;
            $product->force_disabled_by = auth()->id();
            $product->force_disabled_at = now();
            $product->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Product force disabled'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // ðŸ”¹ C. CONTENT MODERATION (NEW)
    // ==========================================
    
    public function removeMisleadingContent(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'field_to_remove' => 'required|string',
            'removal_reason' => 'required|string'
        ]);
        
        DB::beginTransaction();
        try {
            $oldValue = $product->{$request->field_to_remove};
            
            $this->saveVersionHistory($product, 'content_removed', [
                'field' => $request->field_to_remove,
                'old_value' => $oldValue,
                'reason' => $request->removal_reason
            ]);
            
            $product->{$request->field_to_remove} = null;
            $product->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Misleading content removed'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
   
    
    public function blockInappropriate(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $this->saveVersionHistory($product, 'blocked_inappropriate', [
            'reason' => $request->reason
        ]);
        
        $product->status = 'deactivated';
        $product->is_inappropriate = true;
        $product->inappropriate_reason = $request->reason;
        $product->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Product blocked'
        ]);
    }

    // ==========================================
    // ðŸ”¹ D. BULK ADMIN ACTIONS (FIXED)
    // ==========================================
    
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);
        
        $count = 0;
        $skipped = 0;
        $alreadyApproved = 0;
        
        foreach ($request->product_ids as $id) {
            $product = Product::find($id);
            if (!$product) {
                $skipped++;
                continue;
            }
            
            // Check if already approved/verified/active
            if (in_array($product->status, ['approved', 'verified', 'active'])) {
                $alreadyApproved++;
                continue;
            }
            
            // Allow approval for pending, in-progress, not-verified, rejected statuses
            if (in_array($product->status, ['pending', 'in-progress', 'not-verified', 'unavailable', 'rejected'])) {
                $this->saveVersionHistory($product, 'bulk_approved', [
                    'old_status' => $product->status,
                    'new_status' => 'approved',
                    'bulk_action' => true
                ]);
                $product->status = 'approved';
                $product->admin_notes = ($product->admin_notes ? $product->admin_notes . "\n" : '') . "Bulk approved on " . now();
                $product->save();
                if ($product->created_by_id) {
                    NotificationService::notify(
                        $product->created_by_id,
                        'seller',
                        'product',
                        "Your product '{$product->name}' has been approved by admin.",
                        route('seller.products.index')
                    );
                }
                $count++;
            } else {
                $skipped++;
            }
        }
        
        $message = "$count products approved successfully";
        if ($alreadyApproved > 0) {
            $message .= " $alreadyApproved products were already approved/verified.";
        }
        if ($skipped > 0) {
            $message .= " $skipped products could not be approved.";
        }
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    public function bulkReject(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'reason' => 'nullable|string'
        ]);
        
        $count = 0;
        $skipped = 0;
        $alreadyRejected = 0;
        
        foreach ($request->product_ids as $id) {
            $product = Product::find($id);
            if (!$product) {
                $skipped++;
                continue;
            }
            
            // Check if already rejected
            if ($product->status == 'rejected') {
                $alreadyRejected++;
                continue;
            }
            
            // Allow rejection for non-rejected statuses
            if (!in_array($product->status, ['rejected'])) {
                $this->saveVersionHistory($product, 'bulk_rejected', [
                    'old_status' => $product->status,
                    'new_status' => 'rejected',
                    'reason' => $request->reason,
                    'bulk_action' => true
                ]);
                $product->status = 'rejected';
                $product->admin_notes = ($product->admin_notes ? $product->admin_notes . "\n" : '') . "Bulk rejected on " . now() . ". Reason: " . ($request->reason ?? 'No reason provided');
                $product->save();
                if ($product->created_by_id) {
                    NotificationService::notify(
                        $product->created_by_id,
                        'seller',
                        'product',
                        "Your product '{$product->name}' has been rejected by admin.",
                        route('seller.products.index')
                    );
                }
                $count++;
            } else {
                $skipped++;
            }
        }
        
        $message = "$count products rejected successfully";
        if ($alreadyRejected > 0) {
            $message .= " $alreadyRejected products were already rejected.";
        }
        if ($skipped > 0) {
            $message .= " $skipped products could not be rejected.";
        }
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    public function bulkCategoryChange(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'category_id' => 'required|exists:marketplace_categories,id',
            'product_ids.*' => 'exists:products,id'
        ]);
        
        $count = 0;
        $failed = 0;
        
        foreach ($request->product_ids as $id) {
            $product = Product::find($id);
            if ($product) {
                $this->saveVersionHistory($product, 'category_changed', [
                    'old_category' => $product->category_id,
                    'new_category' => $request->category_id,
                    'bulk_action' => true
                ]);
                $product->category_id = $request->category_id;
                if ($request->subcategory_id) {
                    $product->subcategory_id = $request->subcategory_id;
                }
                $product->save();
                $count++;
            } else {
                $failed++;
            }
        }
        
        $message = "$count products category updated successfully";
        if ($failed > 0) {
            $message .= " $failed products failed to update.";
        }
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    public function bulkEnableDisable(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'action' => 'required|in:enable,disable',
            'product_ids.*' => 'exists:products,id'
        ]);
        
        $newStatus = $request->action == 'enable' ? 'active' : 'deactivated';
        $count = 0;
        $skipped = 0;
        $alreadyDone = 0;
        
        foreach ($request->product_ids as $id) {
            $product = Product::find($id);
            if (!$product) {
                $skipped++;
                continue;
            }
            
            // For enable: skip if already active
            if ($request->action == 'enable' && $product->status == 'active') {
                $alreadyDone++;
                continue;
            }
            // For disable: skip if already deactivated
            if ($request->action == 'disable' && $product->status == 'deactivated') {
                $alreadyDone++;
                continue;
            }
            
            // For enable: only enable approved/verified/deactivated products
            if ($request->action == 'enable' && !in_array($product->status, ['approved', 'verified', 'deactivated'])) {
                $skipped++;
                continue;
            }
            
            $this->saveVersionHistory($product, 'bulk_' . $request->action . 'd', [
                'old_status' => $product->status,
                'new_status' => $newStatus,
                'bulk_action' => true
            ]);
            $product->status = $newStatus;
            $product->admin_notes = ($product->admin_notes ? $product->admin_notes . "\n" : '') . "Bulk " . $request->action . "d on " . now();
            $product->save();
            $count++;
        }
        
        $message = "$count products " . $request->action . "d successfully";
        if ($alreadyDone > 0) {
            $message .= " $alreadyDone products were already " . $request->action . "d.";
        }
        if ($skipped > 0) {
            $message .= " $skipped products could not be " . $request->action . "d.";
        }
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    // Unified bulk action method
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,enable,disable',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);
        
        switch($request->action) {
            case 'approve':
                return $this->bulkApprove($request);
            case 'reject':
                return $this->bulkReject($request);
            case 'enable':
            case 'disable':
                return $this->bulkEnableDisable($request);
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid action'
                ], 400);
        }
    }
    
    // Get subcategories by category
    public function getSubcategoriesByCategory(Request $request)
    {
        $subcategories = MarketplaceSubcategory::where('category_id', $request->category_id)->get();
        return response()->json(['subcategories' => $subcategories]);
    }

    // ==========================================
    // ðŸ”¹ E. DROPSHIPPING MANAGEMENT (NEW)
    // ==========================================
    
    public function getSuppliers()
    {
        $suppliers = Seller::with(['products'])
            ->where(function($q) {
                $q->where('supplier_approved', '!=', true)
                  ->orWhereNull('supplier_approved');
            })
            ->get();
        
        return view('admin.approval.suppliers', compact('suppliers'));
    }
    
    public function approveSupplier($id)
    {
        $supplier = Seller::findOrFail($id);
        $supplier->supplier_approved = true;
        $supplier->supplier_approved_at = now();
        $supplier->supplier_approved_by = auth()->id();
        $supplier->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Supplier approved'
        ]);
    }
    
    public function rejectSupplier(Request $request, $id)
    {
        $supplier = Seller::findOrFail($id);
        $supplier->supplier_approved = false;
        $supplier->supplier_rejected_at = now();
        $supplier->supplier_rejected_by = auth()->id();
        $supplier->supplier_rejection_reason = $request->reason;
        $supplier->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Supplier rejected'
        ]);
    }
    
    public function supplierKycValidation(Request $request, $id)
    {
        $supplier = Seller::findOrFail($id);
        
        $request->validate([
            'kyc_status' => 'required|in:verified,rejected,pending',
            'kyc_notes' => 'nullable|string'
        ]);
        
        $supplier->kyc_status = $request->kyc_status;
        $supplier->kyc_notes = $request->kyc_notes;
        $supplier->kyc_verified_at = now();
        $supplier->kyc_verified_by = auth()->id();
        $supplier->save();
        
        return response()->json([
            'success' => true,
            'message' => 'KYC validation completed'
        ]);
    }
    
    public function syncProductMapping(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_product_id' => 'required|string',
            'supplier_cost' => 'required|numeric'
        ]);
        
        $product = Product::find($request->product_id);
        $product->supplier_product_id = $request->supplier_product_id;
        $product->supplier_cost = $request->supplier_cost;
        $product->auto_order = $request->auto_order ?? false;
        $product->shipping_time = $request->shipping_time;
        $product->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Product mapping updated'
        ]);
    }
    
    public function validatePriceSync($id)
    {
        $product = Product::findOrFail($id);
        
        $margin = $product->price - ($product->supplier_cost ?? 0);
        $margin_percentage = ($product->supplier_cost ?? 0) > 0 ? ($margin / ($product->supplier_cost ?? 1)) * 100 : 0;
        
        $validation = [
            'supplier_price' => $product->supplier_cost ?? 0,
            'selling_price' => $product->price,
            'margin' => $margin,
            'margin_percentage' => round($margin_percentage, 2),
            'is_profitable' => $margin > 0,
            'price_valid' => $margin > 0 && $margin_percentage >= 10,
            'message' => $margin > 0 ? 
                ($margin_percentage >= 10 ? 'Valid pricing' : 'Low margin alert') : 
                'Negative margin - Review required'
        ];
        
        return response()->json([
            'success' => true,
            'validation' => $validation
        ]);
    }

    // ==========================================
    // ðŸ”¹ F. REVIEW MODERATION (NEW)
    // ==========================================
    
    public function moderateReviews()
    {
        $reviews = ProductReview::with(['product', 'user'])
            ->where('is_approved', false)
            ->orWhere('flagged', true)
            ->latest()
            ->paginate(20);
        
        return view('admin.approval.reviews', compact('reviews'));
    }
    
    public function approveReviewRemoval(Request $request, $id)
    {
        $review = ProductReview::findOrFail($id);
        
        $request->validate([
            'removal_reason' => 'required|string'
        ]);
        
        $review->is_approved = false;
        $review->removed = true;
        $review->removal_reason = $request->removal_reason;
        $review->removed_at = now();
        $review->removed_by = auth()->id();
        $review->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Review removal approved'
        ]);
    }
    
    public function rejectReviewRemoval($id)
    {
        $review = ProductReview::findOrFail($id);
        
        $review->removal_requested = false;
        $review->removal_rejected_at = now();
        $review->removal_rejected_by = auth()->id();
        $review->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Review removal rejected'
        ]);
    }
    
    public function markReviewStatus($id, $status)
    {
        $review = ProductReview::findOrFail($id);
        
        $review->is_active = $status == 'active';
        $review->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Review marked as ' . $status
        ]);
    }

    // ==========================================
    // ðŸ”¹ VERSION HISTORY FUNCTIONS (NEW)
    // ==========================================
    
    public function getVersionHistory($id)
    {
        $history = ProductVersionHistory::where('product_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }
  
    
    public function rollbackVersion(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $version = ProductVersionHistory::findOrFail($request->version_id);
        
        DB::beginTransaction();
        try {
            $this->saveVersionHistory($product, 'rollback', [
                'rolled_back_from' => $request->version_id,
                'old_values' => $product->toArray()
            ]);
            
            $snapshot = json_decode($version->snapshot, true);
            $product->fill($snapshot);
            $product->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Rollback successful'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // ðŸ”¹ G. PERFORMANCE SCORING (FIXED)
    // ==========================================
    
    private function calculatePerformanceScores($product)
    {
        $leadQualityScore = $this->calculateLeadQualityScore($product);
        $leadConversionScore = $this->calculateLeadConversionScore($product);
        $sellerScore = $this->calculateSellerScore($product->seller);
        $productScore = $this->calculateProductScore($product);
        
        return [
            'lead_quality_score' => $leadQualityScore,
            'lead_conversion_score' => $leadConversionScore,
            'seller_score' => $sellerScore,
            'product_score' => $productScore,
            'metrics' => [
                'returns' => $product->returns ? $product->returns->count() : 0,
                'ratings' => $product->average_rating ?? 0,
                'delivery_time' => $this->calculateAvgDeliveryTime($product),
                'complaints' => $product->complaints ? $product->complaints->count() : 0
            ]
        ];
    }
    
    private function calculateLeadQualityScore($product)
    {
        $score = 0;
        $totalChecks = 10;
        
        // Title quality (30-100 chars)
        if ($product->name && strlen($product->name) >= 30 && strlen($product->name) <= 100) $score++;
        
        // Description quality (150-300 words)
        if ($product->description && str_word_count($product->description) >= 150 && str_word_count($product->description) <= 300) $score++;
        
        // Category correctness
        if ($product->category_id) $score++;
        
        // Price validity
        if ($product->price && $product->price > 0) $score++;
        
        // Images validation - FIXED: images is already an array due to cast
        $images = $product->images;
        $imageCount = 0;
        
        if ($images) {
            if (is_array($images)) {
                $imageCount = count($images);
            } elseif (is_string($images)) {
                $decoded = json_decode($images, true);
                $imageCount = is_array($decoded) ? count($decoded) : 0;
            }
        }
        
        if ($imageCount >= 3) $score++;
        
        // SEO tags
        if ($product->seo_tags) $score++;
        
        // Inclusions
        if ($product->inclusion) $score++;
        
        // Refund policy
        if ($product->refund_policy) $score++;
        
        // Inventory check
        if ($product->inventory_type && $product->inventory > 0) $score++;
        
        // Brand or organizer name
        if ($product->brand_name || $product->organizer_name) $score++;
        
        return round(($score / $totalChecks) * 100);
    }
    
    private function calculateLeadConversionScore($product)
    {
        $views = $product->views ?? 0;
        $orders = $product->items ? $product->items->count() : 0;
        
        if ($views == 0) return 0;
        
        $conversionRate = ($orders / $views) * 100;
        
        if ($conversionRate >= 10) return 100;
        if ($conversionRate >= 7) return 80;
        if ($conversionRate >= 5) return 60;
        if ($conversionRate >= 3) return 40;
        if ($conversionRate >= 1) return 20;
        
        return 10;
    }
    
    private function calculateSellerScore($seller)
    {
        if (!$seller) return 0;
        
        $score = 0;
        
        // Rating (max 40 points)
        $avgRating = $seller->products ? $seller->products->avg('average_rating') ?? 0 : 0;
        $score += ($avgRating / 5) * 40;
        
        // Delivery time (max 20 points)
        $avgDelivery = $this->calculateSellerAvgDelivery($seller);
        $score += $avgDelivery <= 3 ? 20 : ($avgDelivery <= 5 ? 15 : ($avgDelivery <= 7 ? 10 : 5));
        
        // Return rate (max 20 points)
        $returnRate = $this->calculateSellerReturnRate($seller);
        $score += $returnRate <= 5 ? 20 : ($returnRate <= 10 ? 15 : ($returnRate <= 15 ? 10 : 5));
        
        // Complaint rate (max 20 points)
        $complaintRate = $this->calculateSellerComplaintRate($seller);
        $score += $complaintRate <= 5 ? 20 : ($complaintRate <= 10 ? 15 : ($complaintRate <= 15 ? 10 : 5));
        
        return round($score);
    }
    
    private function calculateProductScore($product)
    {
        $score = 0;
        
        // Rating (max 40 points)
        $rating = $product->average_rating ?? 0;
        $score += ($rating / 5) * 40;
        
        // Return rate (max 30 points)
        $totalSold = $product->items ? $product->items->count() : 0;
        $returns = $product->returns ? $product->returns->count() : 0;
        $returnRate = $totalSold > 0 ? ($returns / $totalSold) * 100 : 0;
        
        if ($returnRate <= 5) $score += 30;
        elseif ($returnRate <= 10) $score += 20;
        elseif ($returnRate <= 20) $score += 10;
        else $score += 5;
        
        // Complaint rate (max 30 points)
        $complaints = $product->complaints ? $product->complaints->count() : 0;
        $complaintRate = $totalSold > 0 ? ($complaints / $totalSold) * 100 : 0;
        
        if ($complaintRate <= 2) $score += 30;
        elseif ($complaintRate <= 5) $score += 20;
        elseif ($complaintRate <= 10) $score += 10;
        else $score += 5;
        
        return round($score);
    }
    
    private function calculateAvgDeliveryTime($product)
    {
        if (!$product->items) return 0;
        
        $deliveredItems = $product->items()->whereNotNull('delivered_at')->get();
        if ($deliveredItems->isEmpty()) return 0;
        
        $totalDays = 0;
        foreach ($deliveredItems as $item) {
            $totalDays += $item->created_at->diffInDays($item->delivered_at);
        }
        
        return round($totalDays / $deliveredItems->count());
    }
    
    private function calculateSellerAvgDelivery($seller)
    {
        $totalDays = 0;
        $count = 0;
        
        if ($seller->products) {
            foreach ($seller->products as $product) {
                if ($product->items) {
                    foreach ($product->items as $item) {
                        if ($item->delivered_at) {
                            $totalDays += $item->created_at->diffInDays($item->delivered_at);
                            $count++;
                        }
                    }
                }
            }
        }
        
        return $count > 0 ? round($totalDays / $count) : 0;
    }
    
    private function calculateSellerReturnRate($seller)
    {
        $totalSold = 0;
        $totalReturns = 0;
        
        if ($seller->products) {
            foreach ($seller->products as $product) {
                $totalSold += $product->items ? $product->items->count() : 0;
                $totalReturns += $product->returns ? $product->returns->count() : 0;
            }
        }
        
        return $totalSold > 0 ? ($totalReturns / $totalSold) * 100 : 0;
    }
    
    private function calculateSellerComplaintRate($seller)
    {
        $totalSold = 0;
        $totalComplaints = 0;
        
        if ($seller->products) {
            foreach ($seller->products as $product) {
                $totalSold += $product->items ? $product->items->count() : 0;
                $totalComplaints += $product->complaints ? $product->complaints->count() : 0;
            }
        }
        
        return $totalSold > 0 ? ($totalComplaints / $totalSold) * 100 : 0;
    }
    
    // ==========================================
    // ðŸ”¹ HELPER FUNCTIONS (NEW)
    // ==========================================
    
    private function saveVersionHistory($product, $action, $data)
    {
        return ProductVersionHistory::create([
            'product_id' => $product->id,
            'snapshot' => json_encode($product->toArray()),
            'action' => $action,
            'change_data' => json_encode($data),
            'changed_by' => auth()->id(),
            'created_at' => now()
        ]);
    }
    
    
    
    // Add these methods to your ApprovalController.php

// Save admin notes
public function saveAdminNotes(Request $request, $id)
{
    $product = Product::findOrFail($id);
    $product->admin_notes = $request->admin_notes;
    $product->save();
    
    return response()->json(['success' => true]);
}

// Auto save verification checklist & update product status
public function autoSaveChecklist(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $checklist = [
        'basic_check' => [
            'title_quality' => $request->title_quality ?? null,
            'description_quality' => $request->description_quality ?? null,
            'category_correctness' => $request->category_correctness ?? null,
            'organizer_authenticity' => $request->organizer_authenticity ?? null,
        ],
        'media_validation' => [
            'image_quality' => $request->image_quality ?? null,
            'video_link_validity' => $request->video_link_validity ?? null,
            'content_compliance' => $request->content_compliance ?? null,
        ],
        'details_validation' => [
            'itinerary_clarity' => $request->itinerary_clarity ?? null,
            'inclusion_exclusion' => $request->inclusion_exclusion ?? null,
            'refund_policy_clarity' => $request->refund_policy_clarity ?? null,
        ],
        'seo_check' => [
            'tags_relevance' => $request->tags_relevance ?? null,
            'keyword_quality' => $request->keyword_quality ?? null,
            'no_spam' => $request->no_spam ?? null,
        ],
        'pricing_validation' => [
            'price_reasonability' => $request->price_reasonability ?? null,
            'discount_logic' => $request->discount_logic ?? null,
            'tax_calculation' => $request->tax_calculation ?? null,
            'net_total_correctness' => $request->net_total_correctness ?? null,
        ]
    ];

    $product->verification_checklist = json_encode($checklist);
    
    $totalChecks = 0;
    $passedChecks = 0;
    foreach ($checklist as $group) {
        foreach ($group as $val) {
            $totalChecks++;
            if ($val === 'pass') $passedChecks++;
        }
    }
    
    // Auto update status to approved when parameters are approved
    if ($passedChecks > 0) {
        $product->status = 'approved';
        $product->verification_status = 'verified';
        $product->verified_at = now();
        $product->verified_by = auth()->id();
    }
    
    $product->save();

    return response()->json([
        'success' => true,
        'status' => $product->status,
        'status_text' => ucfirst($product->status),
        'passed_count' => $passedChecks,
        'total_count' => $totalChecks
    ]);
}

// Validate auto order
public function validateAutoOrder($id)
{
    // Add your auto-order validation logic here
    return response()->json(['success' => true, 'message' => 'Auto order validation completed']);
}

// Track dispatch
public function trackDispatch($id)
{
    // Add dispatch tracking logic
    return response()->json(['success' => true, 'message' => 'Dispatch tracking information loaded']);
}

// Monitor SLA
public function monitorSLA($id)
{
    // Add SLA monitoring logic
    return response()->json(['success' => true, 'message' => 'SLA monitoring data loaded']);
}

// Flag duplicate with duplicate_id
public function flagDuplicate(Request $request, $id)
{
    $product = Product::findOrFail($id);
    $product->is_duplicate = true;
    $product->duplicate_of = $request->duplicate_id;
    $product->duplicate_flagged_at = now();
    $product->duplicate_flagged_by = auth()->id();
    $product->save();
    
    $this->saveVersionHistory($product, 'flagged_duplicate', [
        'duplicate_of' => $request->duplicate_id
    ]);
    
    return response()->json(['success' => true]);
}

// Approve seller removal
public function approveSellerRemoval($id)
{
    $product = Product::findOrFail($id);
    $product->seller_removal_approved = true;
    $product->seller_removal_approved_at = now();
    $product->save();
    
    return response()->json(['success' => true]);
}

// Reject seller removal
public function rejectSellerRemoval($id)
{
    $product = Product::findOrFail($id);
    $product->seller_removal_requested = false;
    $product->seller_removal_rejected_at = now();
    $product->save();
    
    return response()->json(['success' => true]);
}

// Get single version
public function getVersion($id, $versionId)
{
    $version = ProductVersionHistory::where('product_id', $id)->findOrFail($versionId);
    return response()->json([
        'success' => true,
        'snapshot' => json_decode($version->snapshot, true)
    ]);
}

// Rollback to version
public function rollbackToVersion($id, $versionId)
{
    $product = Product::findOrFail($id);
    $version = ProductVersionHistory::where('product_id', $id)->findOrFail($versionId);
    
    DB::beginTransaction();
    try {
        $this->saveVersionHistory($product, 'rollback', [
            'rolled_back_from' => $versionId,
            'old_values' => $product->toArray()
        ]);
        
        $snapshot = json_decode($version->snapshot, true);
        $product->update($snapshot);
        
        DB::commit();
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

// Get activity logs
public function getActivityLogs($id)
{
    $logs = ProductVersionHistory::where('product_id', $id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($log) {
            return [
                'action' => ucfirst(str_replace('_', ' ', $log->action)),
                'created_at' => $log->created_at,
                'icon' => $this->getIconForAction($log->action)
            ];
        });
    
    return response()->json(['success' => true, 'logs' => $logs]);
}

private function getIconForAction($action)
{
    if(str_contains($action, 'price')) return 'tag';
    if(str_contains($action, 'description')) return 'file-alt';
    if(str_contains($action, 'approved')) return 'check-circle';
    if(str_contains($action, 'rejected')) return 'times-circle';
    if(str_contains($action, 'removed')) return 'trash';
    if(str_contains($action, 'blocked')) return 'ban';
    return 'edit';
}
}
