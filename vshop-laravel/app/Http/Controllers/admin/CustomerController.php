<?php

namespace App\Http\Controllers\admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Brand;
use App\Models\Address;
use App\Models\ProductReview;
use App\Models\Enquiry;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Seller;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        $query = Customer::with('brand', 'seller');
        
        // Apply filters if needed
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('customer_type')) {
            if ($request->customer_type === 'direct_selling') {
                $query->whereNull('seller_id');
            } elseif ($request->customer_type === 'dropshipping') {
                $query->whereNotNull('seller_id');
            } else {
                $query->where('customer_type', $request->customer_type);
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $customers = $query->latest()->paginate(15)->withQueryString();
        
        // KPI data
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 'active')->count();
        $inactiveCustomers = Customer::where('status', 'inactive')->count();
        $blockedCustomers = Customer::where('status', 'blocked')->count();
        $pendingCustomers = Customer::where('status', 'pending')->count();
        $newThisMonth = Customer::whereMonth('created_at', now()->month)
                               ->whereYear('created_at', now()->year)
                               ->count();
        
        return view('admin.customers.index', compact(
            'customers', 
            'totalCustomers', 
            'activeCustomers', 
            'inactiveCustomers', 
            'blockedCustomers', 
            'pendingCustomers', 
            'newThisMonth'
        ));
    }
    
    /**
     * Update customer status via AJAX
     */
    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'status' => 'required|in:active,inactive,pending,blocked'
            ]);
            
            $customer = Customer::findOrFail($request->customer_id);
            
            // Don't allow status change if blocked (can be changed by admin only)
            // You can remove this condition if you want to allow status change from blocked state
            if ($customer->status === 'blocked' && $request->status !== 'blocked') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot change status of blocked customer. Please unblock first.'
                ], 422);
            }
            
            $customer->status = $request->status;
            $customer->save();
            
            // Optional: Update corresponding user status if customers are linked to users
            if ($customer->user_id) {
                $user = User::find($customer->user_id);
                if ($user) {
                    $user->status = $request->status;
                    $user->save();
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Customer status updated successfully',
                'status' => $customer->status
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update customer type via AJAX
     */
    public function updateType(Request $request)
    {
        try {
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'customer_type' => 'required|in:direct_selling,dropshipping'
            ]);
            
            $customer = Customer::findOrFail($request->customer_id);
            $customer->customer_type = $request->customer_type;
            $customer->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Customer type updated successfully',
                'customer_type' => $customer->customer_type
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer type: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Bulk update customer status
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $request->validate([
                'customer_ids' => 'required|array',
                'customer_ids.*' => 'exists:customers,id',
                'status' => 'required|in:active,inactive,pending,blocked'
            ]);
            
            $count = Customer::whereIn('id', $request->customer_ids)
                ->update(['status' => $request->status]);
            
            return response()->json([
                'success' => true,
                'message' => "{$count} customer(s) status updated successfully"
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Bulk update customer type
     */
    public function bulkUpdateType(Request $request)
    {
        try {
            $request->validate([
                'customer_ids' => 'required|array',
                'customer_ids.*' => 'exists:customers,id',
                'customer_type' => 'required|in:direct_selling,dropshipping'
            ]);
            
            $count = Customer::whereIn('id', $request->customer_ids)
                ->update(['customer_type' => $request->customer_type]);
            
            return response()->json([
                'success' => true,
                'message' => "{$count} customer(s) type updated successfully"
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update type: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove the specified customer from storage.
     */
    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            
            // Optional: Also delete linked user
            if ($customer->user_id) {
                $user = User::find($customer->user_id);
                if ($user) {
                    $user->delete();
                }
            }
            
            $customer->delete();
            
            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer deleted successfully');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Failed to delete customer: ' . $e->getMessage());
        }
    }
    
    /**
     * Export customers to CSV
     */
    public function export(Request $request)
    {
        $query = Customer::with('brand', 'seller');
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }
        
        $customers = $query->get();
        
        $filename = 'customers_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers
        fputcsv($handle, ['ID', 'Name', 'Email', 'Brand', 'Customer Type', 'Status', 'Seller ID', 'Seller Name', 'Created At']);
        
        // Data
        foreach ($customers as $customer) {
            fputcsv($handle, [
                $customer->id,
                $customer->name,
                $customer->email,
                $customer->brand->name ?? '',
                $customer->customer_type === 'direct_selling' ? 'Direct Selling' : 'Dropshipping',
                ucfirst($customer->status),
                $customer->seller->id ?? '',
                $customer->seller->name ?? '',
                $customer->created_at->format('Y-m-d H:i:s')
            ]);
        }
        
        fclose($handle);
        
        return response()->stream(
            function() use ($handle) {
                // Content already written
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
    
    public function blockControl(Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        $customers = Customer::with('brand')
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%");
                });
            })
            ->when($status, function($query) use ($status) {
                if ($status === 'blocked') {
                    $query->where('is_blocked', 1);
                } elseif ($status === 'active') {
                    $query->where('is_blocked', 0)->where('status', 'active');
                } elseif ($status === 'inactive') {
                    $query->where('is_blocked', 0)->where('status', 'inactive');
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Stats calculation
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('is_blocked', 0)->where('status', 'active')->count();
        $inactiveCustomers = Customer::where('is_blocked', 0)->where('status', 'inactive')->count();
        $blockedCustomers = Customer::where('is_blocked', 1)->count();

        return view('admin.customers.block', compact('customers', 'totalCustomers', 'activeCustomers', 'inactiveCustomers', 'blockedCustomers', 'search', 'status'));
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $isBlocked = $request->block ? 1 : 0;
            
            $customer->is_blocked = $isBlocked;
            
            // Optional: Also change status if blocked
            // if ($isBlocked) {
            //     $customer->status = 'blocked';
            // }
            
            $customer->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Customer block status updated successfully.',
                'is_blocked' => $isBlocked
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating customer status: ' . $e->getMessage()
            ], 500);
        }
    }


    
     public function validationDuplicates()
    {
        // Find duplicate emails
        $duplicateEmails = Customer::select('email')
            ->groupBy('email')
            ->havingRaw('count(*) > 1')
            ->pluck('email');

        // Find duplicate phone numbers (excluding null/empty)
        $duplicatePhones = Customer::select('phone')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->groupBy('phone')
            ->havingRaw('count(*) > 1')
            ->pluck('phone');

        // Find duplicate names
        $duplicateNames = Customer::select('name')
            ->groupBy('name')
            ->havingRaw('count(*) > 1')
            ->pluck('name');

        // Query customers that fall into any of these duplicate groups
        $customers = Customer::whereIn('email', $duplicateEmails)
            ->orWhereIn('phone', $duplicatePhones)
            ->orWhereIn('name', $duplicateNames)
            ->orderBy('email')
            ->orderBy('phone')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.customers.validation.duplicates', compact('customers'));
    }

/**
 * Verify and merge duplicate accounts (inline merge)
 */
public function mergeVerify(Request $request)
{
    try {
        $request->validate([
            'source_customer_id' => 'required|exists:customers,id',
            'primary_customer_id' => 'required|exists:customers,id'
        ]);
        
        $sourceCustomer = Customer::findOrFail($request->source_customer_id);
        $primaryCustomer = Customer::findOrFail($request->primary_customer_id);
        
        // Start database transaction
        DB::beginTransaction();
        
        try {
            // 1. Transfer orders from source to primary (if orders relationship exists)
            if (method_exists($sourceCustomer, 'orders')) {
                $sourceCustomer->orders()->update(['customer_id' => $primaryCustomer->id]);
            }
            
            // 2. Transfer any related data (addresses, carts, wishlists, etc.)
            if (method_exists($sourceCustomer, 'addresses')) {
                $sourceCustomer->addresses()->update(['customer_id' => $primaryCustomer->id]);
            }
            
            if (method_exists($sourceCustomer, 'carts')) {
                $sourceCustomer->carts()->update(['customer_id' => $primaryCustomer->id]);
            }
            
            if (method_exists($sourceCustomer, 'wishlists')) {
                $sourceCustomer->wishlists()->update(['customer_id' => $primaryCustomer->id]);
            }
            
            // 3. Update any other foreign key references
            DB::table('sales_orders')->where('customer_id', $sourceCustomer->id)
                ->update(['customer_id' => $primaryCustomer->id]);
            
            // 4. Keep the best data from both customers
            if (empty($primaryCustomer->phone) && !empty($sourceCustomer->phone)) {
                $primaryCustomer->phone = $sourceCustomer->phone;
            }
            
            if (empty($primaryCustomer->alternate_phone) && !empty($sourceCustomer->alternate_phone)) {
                $primaryCustomer->alternate_phone = $sourceCustomer->alternate_phone;
            }
            
            if (empty($primaryCustomer->gst_number) && !empty($sourceCustomer->gst_number)) {
                $primaryCustomer->gst_number = $sourceCustomer->gst_number;
            }
            
            if (empty($primaryCustomer->company_name) && !empty($sourceCustomer->company_name)) {
                $primaryCustomer->company_name = $sourceCustomer->company_name;
            }
            
            $primaryCustomer->save();
            
            // 5. Delete the source customer (or mark as merged)
            $sourceCustomer->delete();
            
            DB::commit();
            
            return redirect()->route('admin.customers.duplicates')
                ->with('success', "Customer '{$sourceCustomer->name}' has been merged into '{$primaryCustomer->name}' successfully!");
                
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        
    } catch (\Exception $e) {
        return redirect()->route('admin.customers.duplicates')
            ->with('error', 'Failed to merge customers: ' . $e->getMessage());
    }
}

/**
 * Get duplicate details for AJAX (optional, for dynamic loading)
 */
public function getDuplicateDetails(Request $request)
{
    try {
        $customerId = $request->customer_id;
        $customer = Customer::findOrFail($customerId);
        
        // Find all duplicates
        $duplicates = Customer::where(function($query) use ($customer) {
                $query->where('email', $customer->email)
                      ->orWhere('phone', $customer->phone)
                      ->orWhere('name', $customer->name);
            })
            ->where('id', '!=', $customerId)
            ->get();
        
        return response()->json([
            'success' => true,
            'primary_customer' => $customer,
            'duplicates' => $duplicates
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

/**
 * Alternative: Direct merge without modal (quick merge)
 */
public function quickMerge(Request $request)
{
    try {
        $request->validate([
            'primary_id' => 'required|exists:customers,id',
            'duplicate_id' => 'required|exists:customers,id|different:primary_id'
        ]);
        
        $primary = Customer::findOrFail($request->primary_id);
        $duplicate = Customer::findOrFail($request->duplicate_id);
        
        DB::beginTransaction();
        
        try {
            // Transfer all relationships
            $this->transferCustomerData($duplicate, $primary);
            
            // Delete duplicate
            $duplicate->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Customer merged successfully into {$primary->name}"
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

/**
 * Helper function to transfer data between customers
 */
private function transferCustomerData($fromCustomer, $toCustomer)
{
    // Transfer orders
    if (method_exists($fromCustomer, 'orders')) {
        $fromCustomer->orders()->update(['customer_id' => $toCustomer->id]);
    }
    
    // Transfer addresses
    if (method_exists($fromCustomer, 'addresses')) {
        $fromCustomer->addresses()->update(['customer_id' => $toCustomer->id]);
    }
    
    // Transfer carts
    if (method_exists($fromCustomer, 'carts')) {
        $fromCustomer->carts()->update(['customer_id' => $toCustomer->id]);
    }
    
    // Transfer wishlists
    if (method_exists($fromCustomer, 'wishlists')) {
        $fromCustomer->wishlists()->update(['customer_id' => $toCustomer->id]);
    }
    
    // Transfer reviews
    if (method_exists($fromCustomer, 'reviews')) {
        $fromCustomer->reviews()->update(['customer_id' => $toCustomer->id]);
    }
    
    // Transfer support tickets
    if (method_exists($fromCustomer, 'tickets')) {
        $fromCustomer->tickets()->update(['customer_id' => $toCustomer->id]);
    }
    
    // Merge additional info
    $fieldsToMerge = ['phone', 'alternate_phone', 'gst_number', 'company_name', 'billing_address', 'shipping_address'];
    foreach ($fieldsToMerge as $field) {
        if (empty($toCustomer->$field) && !empty($fromCustomer->$field)) {
            $toCustomer->$field = $fromCustomer->$field;
        }
    }
    $toCustomer->save();
}

  public function insightsHighValue()
    {
        // Select customers with total spent calculated from completed orders
        $customers = Customer::select('customers.*')
            ->selectSub(function($query) {
                $query->selectRaw('COALESCE(SUM(total_amount), 0)')
                    ->from('orders')
                    ->whereColumn('orders.customer_id', 'customers.id')
                    ->where('orders.status', 'completed');
            }, 'total_spent')
            ->selectSub(function($query) {
                $query->selectRaw('count(*)')
                    ->from('orders')
                    ->whereColumn('orders.customer_id', 'customers.id');
            }, 'orders_count')
            ->orderBy('total_spent', 'desc')
            ->paginate(20);

        return view('admin.customers.insights.high_value', compact('customers'));
    }
    
      
    public function insightsFrequentReturn()
    {
        // Fetch customers with high volume of returned orders or returns logs
        $customers = Customer::select('customers.*')
            ->selectSub(function($query) {
                $query->selectRaw('count(*)')
                    ->from('order_returns')
                    ->whereColumn('order_returns.customer_id', 'customers.id');
            }, 'returns_count')
            ->selectSub(function($query) {
                $query->selectRaw('count(*)')
                    ->from('orders')
                    ->whereColumn('orders.customer_id', 'customers.id');
            }, 'orders_count')
            ->having('returns_count', '>', 0)
            ->orderBy('returns_count', 'desc')
            ->paginate(20);

        return view('admin.customers.insights.frequent_return', compact('customers'));
    }


  public function insightsBadReviews(Request $request)
{
    // Get per_page value (default 10)
    $perPage = $request->get('per_page', 10);
    
    // Start with sellers query
    $query = Seller::query();
    
    // Apply business type filter
    if ($request->type && $request->type != 'all') {
        $query->where('type', $request->type);
    }
    
    // Apply search filter on seller name
    if ($request->search) {
        $query->where('name', 'like', '%'.$request->search.'%');
    }
    
    // Get all sellers first
    $sellers = $query->get();
    
    // Prepare seller-wise bad review data
    $sellerData = [];
    
    foreach ($sellers as $seller) {
        // Get all products for this seller
        $products = Product::where('created_by_id', $seller->id)->get();
        
        $badReviewCount = 0;
        $badProducts = [];
        $platformName = '';
        
        foreach ($products as $product) {
            // Query reviews directly from ProductReview table for this product
            $reviewsQuery = ProductReview::where('product_id', $product->id);
            
            // Apply date range filter if needed
            if ($request->date_range && $request->date_range != 'all') {
                if ($request->date_range == 'today') {
                    $reviewsQuery->whereDate('created_at', today());
                } elseif ($request->date_range == 'week') {
                    $reviewsQuery->whereDate('created_at', '>=', now()->subDays(7));
                } elseif ($request->date_range == 'month') {
                    $reviewsQuery->whereDate('created_at', '>=', now()->subDays(30));
                }
            }
            
            // Get bad reviews (rating 1 or 2)
            $badReviews = $reviewsQuery->whereIn('rating', [1, 2])->get();
            $badReviewsCount = $badReviews->count();
            
            if ($badReviewsCount > 0) {
                $badReviewCount += $badReviewsCount;
                
                // Get total reviews for this product (for percentage calculation)
                $totalReviews = ProductReview::where('product_id', $product->id)->count();
                $badPercentage = $totalReviews > 0 ? round(($badReviewsCount / $totalReviews) * 100, 1) : 0;
                
                // Calculate average rating for this product
                $avgRating = ProductReview::where('product_id', $product->id)->avg('rating');
                
                $badProducts[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'bad_review_count' => $badReviewsCount,
                    'total_reviews' => $totalReviews,
                    'bad_review_percentage' => $badPercentage,
                    'avg_rating' => round($avgRating, 1),
                    'reviews' => $badReviews
                ];
                
                // Get platform name from product
                if (empty($platformName) && !empty($product->platform)) {
                    $platformName = $product->platform;
                }
            }
        }
        
        // Only include sellers who have at least one bad review
        if ($badReviewCount > 0) {
            // Sort products by bad review count (highest first)
            usort($badProducts, function($a, $b) {
                return $b['bad_review_count'] - $a['bad_review_count'];
            });
            
            $sellerData[] = [
                'seller_id' => $seller->id,
                'seller_name' => $seller->name,
                'seller_email' => $seller->email,
                'business_type' => $seller->type ?? 'direct',
                'platform_name' => $platformName ?: 'N/A',
                'bad_review_count' => $badReviewCount,
                'bad_products_count' => count($badProducts),
                'total_products' => $products->count(),
                'bad_products' => $badProducts,
            ];
        }
    }
    
    // Sort by bad review count (highest first)
    usort($sellerData, function($a, $b) {
        return $b['bad_review_count'] - $a['bad_review_count'];
    });
    
    // Manual pagination
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $currentItems = array_slice($sellerData, ($currentPage - 1) * $perPage, $perPage);
    $sellerBadReviews = new LengthAwarePaginator(
        $currentItems,
        count($sellerData),
        $perPage,
        $currentPage,
        ['path' => LengthAwarePaginator::resolveCurrentPath()]
    );
    
    // Preserve query parameters
    $sellerBadReviews->appends($request->all());
    
    return view('admin.customers.insights.bad_reviews', compact('sellerBadReviews'));
}
    
     public function mergeView(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        
        $source = null;
        $target = null;
        
        if ($request->source_id) {
            $source = Customer::withCount(['orders', 'reviews', 'addresses'])->find($request->source_id);
        }
        
        if ($request->target_id) {
            $target = Customer::withCount(['orders', 'reviews', 'addresses'])->find($request->target_id);
        }

        return view('admin.customers.merge', compact('customers', 'source', 'target'));
    }
    
     public function mergeSubmit(Request $request)
    {
        $request->validate([
            'source_id' => 'required|exists:customers,id',
            'target_id' => 'required|exists:customers,id|different:source_id',
        ]);

        try {
            DB::transaction(function() use ($request) {
                $source = Customer::findOrFail($request->source_id);
                $target = Customer::findOrFail($request->target_id);

                // 1. Transfer Orders
                Order::where('customer_id', $source->id)->update(['customer_id' => $target->id]);

                // 2. Transfer Product Reviews
                ProductReview::where('customer_id', $source->id)->update(['customer_id' => $target->id]);

                // 3. Transfer Addresses
                Address::where('customer_id', $source->id)->update(['customer_id' => $target->id]);

                // 4. Transfer Enquiries
                Enquiry::where('user_id', $source->id)->update(['user_id' => $target->id]);

                // 5. Absorb missing profile info
                $fieldsToMerge = [
                    'phone', 'company_name', 'gstin', 'country', 'state', 
                    'city', 'address_line1', 'pincode', 'gender', 'brand_id', 'branch_id'
                ];
                
                $targetUpdated = false;
                foreach ($fieldsToMerge as $field) {
                    if (empty($target->$field) && !empty($source->$field)) {
                        $target->$field = $source->$field;
                        $targetUpdated = true;
                    }
                }
                
                if ($targetUpdated) {
                    $target->save();
                }

                // 6. Delete source customer
                $source->delete();
            });

            return redirect()->route('admin.customers.index')
                ->with('success', 'Customers successfully merged! All orders, addresses, reviews, and enquiries have been consolidated.');

        } catch (\Exception $e) {
            Log::error('Merge duplicate customers error: ' . $e->getMessage());
            return back()->with('error', 'Failed to merge customers: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    
    /**
 * Display address validation/audit page
 * Fetches data from addresses table directly
 */
public function validationAddress(Request $request)
{
    // Get per_page value
    $perPage = $request->get('per_page', 6);
    
    // Get addresses with customer relationship
    $query = Address::with('customer');
    
    // Apply search filter if needed
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('address_line1', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%")
              ->orWhere('pincode', 'like', "%{$search}%");
        });
    }
    
    // Filter by address type (billing/delivery)
    if ($request->filled('address_type')) {
        $query->where('type', $request->address_type);
    }
    
    // Filter by validation issues
    if ($request->filled('filter')) {
        $filter = $request->filter;
        switch($filter) {
            case 'no_phone':
                $query->where(function($q) {
                    $q->whereNull('phone')->orWhere('phone', '');
                });
                break;
            case 'no_address':
                $query->where(function($q) {
                    $q->whereNull('address_line1')->orWhere('address_line1', '');
                });
                break;
            case 'no_pincode':
                $query->where(function($q) {
                    $q->whereNull('pincode')->orWhere('pincode', '');
                });
                break;
            case 'no_city':
                $query->where(function($q) {
                    $q->whereNull('city')->orWhere('city', '');
                });
                break;
            case 'no_email':
                $query->where(function($q) {
                    $q->whereNull('email')->orWhere('email', '');
                });
                break;
        }
    }
    
    // Get addresses with pagination
    $addresses = $query->orderBy('created_at', 'desc')->paginate($perPage);
    
    return view('admin.customers.validation.address', compact('addresses'));
}

public function show($id)
{
    $customer = Customer::with(['seller', 'orders' => function($q) {
        $q->latest()->take(10);
    }])->findOrFail($id);
    
    $totalSpent = $customer->orders->where('payment_status', 'paid')->sum('total_amount');
    $totalOrders = $customer->orders->count();
    
    return view('admin.customers.show', compact('customer', 'totalSpent', 'totalOrders'));
}

}
