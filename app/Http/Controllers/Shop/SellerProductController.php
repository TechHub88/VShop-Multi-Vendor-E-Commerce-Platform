<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceSubCategory;
use App\Models\Attribute;
use App\Models\AttributeTerm;
use App\Models\ProductAttribute;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\ProductLog;
use App\Models\RecommendationRule;

use App\Models\ProductReview;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class SellerProductController extends Controller
{

    private function createNotification($sellerId, $type, $message, $link = null)
    {
        if ($sellerId) {
            Notification::create([
                'seller_id' => $sellerId,
                'type' => $type,
                'message' => $message,
                'link' => $link,
            ]);
        }
    }

public function index(Request $request)
{
    $sellerId = Auth::id();
    
    $moduleType = $this->getModuleType();

    // ================= NEW PANEL LOGIC =================
    $selectedPanels = session('selected_panels', []);

    $allowedTypes = [];

    if(in_array('product', $selectedPanels)){
        $allowedTypes = array_merge($allowedTypes, [
            'product', 'vshop-product',
            'professional', 'vshop-professional',
            'vshop-service', 'vshop-tour', 'vshop-information_advisory'
        ]);
    }

    if(in_array('travel', $selectedPanels)){
        $allowedTypes = array_merge($allowedTypes, [
            'service', 'vtravel-service',
            'tour', 'vtravel-tour',
            'vtravel-professional', 'vtravel-information_advisory',
            'information_advisory'
        ]);
    }

    if(empty($allowedTypes)){
        $allowedTypes = ['product'];
    }
    // ================= END =================

    $query = Product::where('created_by_id', $sellerId);

    // ================= SERVICE TYPES FILTER =================
    // Check if service_types parameter is present in the URL
    $serviceTypesParam = $request->get('service_types');
    
    if($serviceTypesParam) {
        // Split the comma-separated values
        $selectedServiceTypes = explode(',', $serviceTypesParam);
        
        // Map service types to product type values
        $typeFilters = [];
        foreach($selectedServiceTypes as $serviceType) {
            $serviceType = trim($serviceType);
            if($serviceType == 'tour_guide' || $serviceType == 'tour') {
                $typeFilters = array_merge($typeFilters, ['tour', 'vshop-tour', 'vtravel-tour']);
            } elseif($serviceType == 'professional') {
                $typeFilters = array_merge($typeFilters, ['professional', 'vshop-professional', 'vtravel-professional']);
            } elseif($serviceType == 'others') {
                $typeFilters = array_merge($typeFilters, [
                    'service', 'vshop-service', 'vtravel-service',
                    'information_advisory', 'vshop-information_advisory', 'vtravel-information_advisory'
                ]);
            }
        }
        
        if(!empty($typeFilters)) {
            $query->whereIn('type', $typeFilters);
        }
    }
    // ================= END SERVICE TYPES FILTER =================

    // ================= UPDATED FILTER =================
    elseif($moduleType == 'product'){
        $query->whereIn('type', [
            'product', 'vshop-product',
            'professional', 'vshop-professional',
            'vshop-service', 'vshop-tour', 'vshop-information_advisory'
        ]);
    }
    elseif($moduleType == 'service'){
        $query->whereIn('type', [
            'service', 'vtravel-service',
            'tour', 'vtravel-tour',
            'vtravel-professional', 'vtravel-information_advisory',
            'information_advisory'
        ]);
    }
    elseif($moduleType == 'course'){
        $query->where('type', 'course');
    }
    else{
        // For 'both' or any other session combination
        $query->whereIn('type', [
            'product', 'vshop-product',
            'professional', 'vshop-professional', 'vtravel-professional',
            'service', 'vshop-service', 'vtravel-service',
            'tour', 'vshop-tour', 'vtravel-tour',
            'information_advisory', 'vshop-information_advisory', 'vtravel-information_advisory',
            'course'
        ]);
    }
    // ================= END =================

    $query->withCount('reviews')
          ->withAvg('reviews', 'rating')
          ->with('recommendations');

    /*
    |--------------------------------------------------
    | GLOBAL SEARCH
    |--------------------------------------------------
    */
    $search = trim($request->q ?? $request->search);

    if ($search) {
        $query->where(function ($q) use ($search) {

            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('sku', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")

              ->orWhereHas('category', function ($c) use ($search) {
                  $c->where('name', 'LIKE', "%{$search}%");
              })

              ->orWhereHas('subcategory', function ($sc) use ($search) {
                  $sc->where('name', 'LIKE', "%{$search}%");
              });

        });
    }

    /*
    |--------------------------------------------------
    | FILTERS (UNCHANGED)
    |--------------------------------------------------
    */

    $query->when($request->filled('category'), function ($q) use ($request) {
        $q->where('category_id', $request->category);
    });

    $query->when($request->filled('subcategory'), function ($q) use ($request) {
        $q->where('subcategory_id', $request->subcategory);
    });

    $query->when($request->filled('status'), function ($q) use ($request) {
        $q->where('status', $request->status);
    });

    $query->when($request->filled('type'), function ($q) use ($request) {
        $q->where('type', $request->type);
    });

    $query->when($request->filled('payment_type'), function ($q) use ($request) {
        $q->where('payment_type', $request->payment_type);
    });
    
    if ($request->filled('rating')) {

        $rating = (float) $request->rating;

        $query->havingRaw(
            'COALESCE(reviews_avg_rating,0) >= ? 
             AND COALESCE(reviews_avg_rating,0) < ?',
            [$rating, $rating + 1]
        );
    }
    
    if ($request->discount === 'yes') {
        $query->where('discount', '>', 0);
    } elseif ($request->discount === 'no') {
        $query->where(function ($q) {
            $q->where('discount', 0)->orWhereNull('discount');
        });
    }

    $query->when($request->filled('city'), function ($q) use ($request) {
        $q->where('city', 'LIKE', "%{$request->city}%");
    });

    $query->when($request->filled('duration'), function ($q) use ($request) {
        $q->where('duration', 'LIKE', "%{$request->duration}%");
    });

    /*
    |--------------------------------------------------
    | SORTING
    |--------------------------------------------------
    */

    switch ($request->sort) {

        case 'high_price':
            $query->orderBy('price', 'desc');
            break;

        case 'low_price':
            $query->orderBy('price', 'asc');
            break;

        case 'popular':
            $query->orderBy('reviews_count', 'desc');
            break;

        case 'high_review':
            $query->orderBy('reviews_avg_rating', 'desc');
            break;

        case 'low_review':
            $query->orderBy('reviews_avg_rating', 'asc');
            break;

        default:
            $query->latest();
            break;
    }

    /*
    |--------------------------------------------------
    | RESULT
    |--------------------------------------------------
    */

    $products = $query->paginate(10)->withQueryString();

    $categories = MarketplaceCategory::query();

    // ================= UPDATED CATEGORY =================
    if($moduleType == 'product'){
        $categories->whereIn('type', ['product','professional']);
    }
    elseif($moduleType == 'service'){
        $categories->whereIn('type', ['service','tour']);
    }
    elseif($moduleType == 'course'){
        $categories->where('type', 'course');
    }
    // ================= END =================

    $categories = $categories->orderBy('name')->get();

    // Pass the selected service types to the view for maintaining checkboxes
    $selectedServiceTypes = $request->get('service_types');
    
    return view('vshop.seller.products.index', compact(
        'products',
        'categories',
        'selectedServiceTypes'
    ));
}

public function getSubcategories($id)
{
    return response()->json(
        MarketplaceSubCategory::where('category_id',$id)->get()
    );
}


public function misDashboard(Request $request)
{
    $sellerId = auth()->guard('seller')->id();
    $seller = auth()->guard('seller')->user();

    /* =========================================
        PANEL ALLOWED TYPES
    ========================================= */
    $selectedPanels = session('selected_panels', []);
    $allowedTypes = [];
    if (in_array('product', $selectedPanels)) {
        $allowedTypes = array_merge($allowedTypes, [
            'product', 'vshop-product',
            'professional', 'vshop-professional',
            'vshop-service', 'vshop-tour', 'vshop-information_advisory'
        ]);
    }
    if (in_array('travel', $selectedPanels)) {
        $allowedTypes = array_merge($allowedTypes, [
            'service', 'vtravel-service',
            'tour', 'vtravel-tour',
            'vtravel-professional', 'vtravel-information_advisory',
            'information_advisory'
        ]);
    }
    if (empty($allowedTypes)) {
        $allowedTypes = ['product'];
    }

    /* =========================================
        FILTERS LOGIC
    ========================================= */
    $typeFilters = [];
    if ($request->has('type') && $request->type === 'product') {
        $typeFilters = [
            'product', 'vshop-product',
            'professional', 'vshop-professional',
            'vshop-service', 'vshop-tour', 'vshop-information_advisory'
        ];
    } elseif ($request->filled('service_types')) {
        $selectedServiceTypes = explode(',', $request->service_types);
        foreach ($selectedServiceTypes as $st) {
            $st = trim($st);
            if ($st == 'tour_guide' || $st == 'tour') {
                $typeFilters = array_merge($typeFilters, ['tour', 'vshop-tour', 'vtravel-tour']);
            } elseif ($st == 'professional') {
                $typeFilters = array_merge($typeFilters, ['professional', 'vshop-professional', 'vtravel-professional']);
            } elseif ($st == 'others') {
                $typeFilters = array_merge($typeFilters, [
                    'service', 'vshop-service', 'vtravel-service',
                    'information_advisory', 'vshop-information_advisory', 'vtravel-information_advisory'
                ]);
            }
        }
    }

    $currentQueryTypes = !empty($typeFilters) ? $typeFilters : $allowedTypes;

    /* =========================================
        PRODUCT QUERY (GLOBAL + FILTER NOT REQUIRED)
    ========================================= */
    $productTypes = [
        'product', 'vshop-product',
        'professional', 'vshop-professional',
        'vshop-service', 'vshop-tour', 'vshop-information_advisory'
    ];

    $productQuery = Product::where('created_by_id', $sellerId)
        ->whereIn('type', $productTypes);

    $totalProducts = (clone $productQuery)->count();

    $verifiedProducts = (clone $productQuery)
        ->where('status', 'approved')
        ->count();

    $notVerifiedProducts = (clone $productQuery)
        ->where('status', '!=', 'approved')
        ->count();

    /* =========================================
        SERVICE QUERY SPLIT
    ========================================= */
    $serviceTypesList = [
        'service', 'vtravel-service',
        'tour', 'vtravel-tour',
        'vtravel-professional', 'vtravel-information_advisory',
        'information_advisory'
    ];

    // KPI (GLOBAL - NEVER FILTERED)
    $serviceQueryAll = Product::where('created_by_id', $sellerId)
        ->whereIn('type', $serviceTypesList);

    $totalServices = (clone $serviceQueryAll)->count();

    $verifiedServices = (clone $serviceQueryAll)
        ->where('status', 'approved')
        ->count();

    $pendingServices = (clone $serviceQueryAll)
        ->where('status', 'pending')
        ->count();

    /* =========================================
        REVIEWS (FILTERED BY ACTIVE TYPES)
    ========================================= */
    // Get seller product IDs matching current filter
    $sellerProductIdsSub = Product::where('created_by_id', $sellerId)
        ->whereIn('type', $currentQueryTypes)
        ->pluck('id');

    $reviewQuery = ProductReview::whereIn('product_id', $sellerProductIdsSub);

    $totalReviews  = (clone $reviewQuery)->count();
    $totalRatings  = (int) ((clone $reviewQuery)->avg('rating') ?? 0);
    $rating4plus   = (clone $reviewQuery)->where('rating', '>=', 4)->count();
    $rating3below  = (clone $reviewQuery)->where('rating', '<', 3)->count();
    $goodReviews   = (clone $reviewQuery)->where('rating', '>=', 4)->count();
    $badReviews    = (clone $reviewQuery)->where('rating', '<=', 2)->count();

    /* =========================================
        DYNAMIC CHART DATA
    ========================================= */
    // Bad Reviews Distribution: 1 Star, 2 Star, 3 Star, 4 Star, 5 Star
    $reviewDistribution = [
        (clone $reviewQuery)->where('rating', 1)->count(),
        (clone $reviewQuery)->where('rating', 2)->count(),
        (clone $reviewQuery)->where('rating', 3)->count(),
        (clone $reviewQuery)->where('rating', 4)->count(),
        (clone $reviewQuery)->where('rating', 5)->count()
    ];

    // Grievance Analytics
    $grievanceData = [
        \App\Models\Grievance::where('seller_id', $sellerId)->where('type', 'Delivery')->count(),
        \App\Models\Grievance::where('seller_id', $sellerId)->where('type', 'Refund')->count(),
        \App\Models\Grievance::where('seller_id', $sellerId)->where('type', 'Quality')->count(),
        \App\Models\Grievance::where('seller_id', $sellerId)->where('type', 'Support')->count(),
        \App\Models\Grievance::where('seller_id', $sellerId)->where('type', 'Service')->count()
    ];

    /* =========================================
        TIME SERIES (CHART DATA)
    ========================================= */
    // Dynamic time series: last 6 months of actual order data, filtered by active query types
    $months = [];
    $salesData = [];
    $returnData = [];

    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $months[] = $date->format('M');

        $salesData[] = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('products.created_by_id', $sellerId)
            ->whereIn('products.type', $currentQueryTypes)
            ->whereYear('order_items.created_at', $date->year)
            ->whereMonth('order_items.created_at', $date->month)
            ->count();

        $returnData[] = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('products.created_by_id', $sellerId)
            ->whereIn('products.type', $currentQueryTypes)
            ->where('order_items.status', 'returned')
            ->whereYear('order_items.created_at', $date->year)
            ->whereMonth('order_items.created_at', $date->month)
            ->count();
    }

    /* =========================================
        MOST VIEWED LISTINGS (TOP 5)
    ========================================= */
    $mostViewedListings = Product::where('created_by_id', $sellerId)
        ->whereIn('type', $currentQueryTypes)
        ->orderByRaw('COALESCE(views, 0) DESC')
        ->limit(5)
        ->get();

    /* =========================================
        MOST ENQUIRED LISTINGS (TOP 5)
    ========================================= */
    $rawEnquiries = DB::table('enquiries')
        ->select('enquiries.product_service', DB::raw('COUNT(*) as total_enquiries'), 'products.thumbnail', 'products.images')
        ->join('products', function($join) use ($sellerId, $currentQueryTypes) {
            $join->on('products.name', '=', 'enquiries.product_service')
                 ->where('products.created_by_id', '=', $sellerId)
                 ->whereIn('products.type', $currentQueryTypes);
         })
        ->where('enquiries.seller_id', $sellerId)
        ->groupBy('enquiries.product_service', 'products.thumbnail', 'products.images')
        ->orderByDesc('total_enquiries')
        ->limit(5)
        ->get();

    $mostEnquiredListings = collect($rawEnquiries);

    /* =========================================
        BOOST PERFORMANCE GRAPH DATA
    ========================================= */
    $boostDates = [];
    $boostAdSpend = [];
    $boostConversions = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $boostDates[] = $date->format('d M');

        $boostAdSpend[] = (float) DB::table('products')
            ->where('created_by_id', $sellerId)
            ->where('is_boosted', true)
            ->whereDate('boost_expires_at', '>=', $date->toDateString())
            ->sum('boost_amount');

        $boostConversions[] = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('products.created_by_id', $sellerId)
            ->where('products.is_boosted', true)
            ->whereDate('order_items.created_at', $date->toDateString())
            ->count();
    }

    $boostChartData = [
        'dates' => $boostDates,
        'ad_spend' => $boostAdSpend,
        'conversions' => $boostConversions
    ];

    /* =========================================
        REFERRAL SETTINGS
    ========================================= */
    $referralSettings = $seller->referral_settings ?? [
        'enabled' => false,
        'reward_type' => 'percentage',
        'percentage' => 5,
        'fixed_amount' => 10,
    ];

    /* =========================================
        AJAX RESPONSE (FIXED STRUCTURE)
    ========================================= */
    if ($request->ajax()) {
        return response()->json([
            'stats' => [
                'totalProducts' => $totalProducts,
                'verifiedProducts' => $verifiedProducts,
                'notVerifiedProducts' => $notVerifiedProducts,

                'totalServices' => $totalServices,
                'verifiedServices' => $verifiedServices,
                'pendingServices' => $pendingServices,

                'totalRatings' => round($totalRatings, 1),
                'rating4plus' => $rating4plus,
                'rating3below' => $rating3below,

                'totalReviews' => $totalReviews,
                'goodReviews' => $goodReviews,
                'badReviews' => $badReviews,
            ],
            'charts' => [
                'sales' => $salesData,
                'returns' => $returnData,
                'rating' => [
                    'good' => $rating4plus,
                    'bad' => $rating3below
                ],
                'bad_reviews' => $reviewDistribution,
                'grievance' => $grievanceData,
                'boost' => $boostChartData
            ],
            'mostViewedListings' => $mostViewedListings,
            'mostEnquiredListings' => $mostEnquiredListings,
            'boostChartData' => $boostChartData
        ]);
    }

    /* =========================================
        VIEW RESPONSE
    ========================================= */
    return view('vshop.seller.products.mis', compact(
        'totalProducts',
        'verifiedProducts',
        'notVerifiedProducts',

        'totalServices',
        'verifiedServices',
        'pendingServices',

        'totalReviews',
        'totalRatings',
        'rating4plus',
        'rating3below',
        'goodReviews',
        'badReviews',
        'mostViewedListings',
        'mostEnquiredListings',
        'boostChartData',
        'referralSettings',
        'seller',
        'salesData',
        'returnData',
        'reviewDistribution',
        'grievanceData',
        'months'
    ));
}

public function saveReferralSettings(Request $request)
{
    $seller = auth()->guard('seller')->user();
    if (!$seller) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    $data = $request->validate([
        'enabled' => 'required|boolean',
        'reward_type' => 'required|in:percentage,fixed',
        'percentage' => 'nullable|numeric|min:0|max:100',
        'fixed_amount' => 'nullable|numeric|min:0',
    ]);

    $seller->update([
        'referral_settings' => [
            'enabled' => (bool)$data['enabled'],
            'reward_type' => $data['reward_type'],
            'percentage' => floatval($data['percentage'] ?? 0),
            'fixed_amount' => floatval($data['fixed_amount'] ?? 0),
        ]
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Referral settings updated successfully',
        'settings' => $seller->referral_settings
    ]);
}


private function checkOwner($product)
{
    abort_if(
        $product->created_by_id != auth()->id(),
        403
    );
}


/* ===================================================
CATEGORY AJAX
=================================================== */
public function getCategories($type)
{
    $sellerId = auth()->guard('seller')->id();

    return MarketplaceCategory::where('type', $type)
        ->where('seller_id', $sellerId)   // ← add this line
        ->orderBy('name')
        ->get(['id', 'name']);
}
private function createLog($productId, $action, $old = null, $new = null)
{
    ProductLog::create([
        'product_id' => $productId,
        'user_id' => auth()->id(),
        'action' => $action,
        'old_value' => $old ? json_encode($old) : null,
        'new_value' => $new ? json_encode($new) : null,
    ]);
}



/* ===================================================
STEP 1 : BASIC
=================================================== */

public function basic($id = null)
{
    /*
    |--------------------------------------------------------------------------
    | PRODUCT
    |--------------------------------------------------------------------------
    */
    $product = $id ? Product::findOrFail($id) : new Product();

    /*
    |--------------------------------------------------------------------------
    | MODULE TYPE
    |--------------------------------------------------------------------------
    */
    $moduleType = $this->getModuleType();

    /*
    |--------------------------------------------------------------------------
    | CURRENT MODULE
    |--------------------------------------------------------------------------
    */
    $currentModule = 'both';
    if ($moduleType == 'product') {
        $currentModule = 'product';
    } elseif ($moduleType == 'service') {
        $currentModule = 'service';
    } elseif ($moduleType == 'course') {
        $currentModule = 'course';
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORY FILTER – ONLY SELLER‑CREATED CATEGORIES
    |--------------------------------------------------------------------------
    */
    $sellerId = auth()->guard('seller')->id();

    $categories = MarketplaceCategory::query()
        ->where('seller_id', $sellerId);   // ← only this seller's categories

    // Further filter by type based on module
    if ($moduleType == 'product') {
        $categories->where('type', 'product');
    } elseif ($moduleType == 'service') {
        $categories->where('type', 'service');
    } elseif ($moduleType == 'course') {
        $categories->where('type', 'course');
    }

    $categories = $categories->orderBy('name')->get();

    /*
    |--------------------------------------------------------------------------
    | COUNTRIES
    |--------------------------------------------------------------------------
    */
    $countries = Country::orderBy('name')->get();

    /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */
    return view('vshop.seller.products.steps.basic', compact(
        'product',
        'categories',
        'countries',
        'moduleType',
        'currentModule'
    ));
}

private function getNextStatus($currentStatus)
{
    switch ($currentStatus) {
        case 'in_progress':
            return 'not_verified';

        case 'not_verified':
            return 'verified';

        case 'verified':
            return 'unavailable';

        default:
            return 'in_progress';
    }
}


public function saveBasic(Request $request, Product $product = null)
{
    $seller = Auth::user();

    // ================= BASE VALIDATION =================
    $data = $request->validate([
        'type' => 'required|in:product,service,professional,tour,information_advisory,vshop-product,vshop-professional,vshop-service,vtravel-professional,vtravel-tour,vtravel-information_advisory,vtravel-service',
        'name' => 'required|string|min:1',
        'category_id' => 'required|exists:marketplace_categories,id',
        'short_summary' => 'required|string|min:1|max:100',
        'description' => 'required|string|min:1|max:300',
        'inventory_type' => 'nullable|in:own,dropshipping',
    ]);

    // Handle professional sub-selection
    if ($request->type === 'professional' && $request->has('professional_module')) {
        $data['type'] = $request->professional_module . '-professional';
        $request->merge(['type' => $data['type']]);
    }

    // ================= COMMON =================
    $data['subcategory_id'] = $request->subcategory_id;
    $data['organizer_name'] = $request->organizer_name;
    $data['product_code'] = $product->product_code ?? $request->product_code ?? 'PRD' . rand(10000, 99999);
    $data['status'] = 'pending';
    $data['tour_service_type'] = $request->tour_service_type;
    $data['listing_type'] = $request->listing_type;

    // =====================================================
    // ✅ PROFESSIONAL
    // =====================================================
    if (in_array($request->type, ['professional', 'vshop-professional', 'vtravel-professional'])) {

        $request->validate([
            'professional_type' => 'required',
            'prof_session_duration' => 'required',
            'session_mode' => 'required',
            'languages' => 'required',
            'max_booking' => 'required|integer|min:1',
            'cancel_policy_prof' => 'required',
            'prof_start_time' => 'required',
            'prof_end_time' => 'required',
        ]);

        $data['professional_type'] = $request->professional_type;
        $data['session_duration'] = $request->prof_session_duration;
        $data['session_mode'] = $request->session_mode;
        $data['languages'] = $request->languages;
        $data['max_booking'] = $request->max_booking;
        $data['portfolio'] = $request->portfolio;
        $data['cancel_policy_prof'] = $request->cancel_policy_prof;
        $data['start_time'] = $request->prof_start_time;
        $data['end_time'] = $request->prof_end_time;
    }

    // =====================================================
    // ✅ PRODUCT (Items)
    // =====================================================
    if (in_array($request->type, ['product', 'vshop-product', 'professional', 'vshop-professional', 'vtravel-professional'])) {

        $data['brand_name'] = $request->brand_name;
        $data['manufactured_by'] = $request->manufactured_by;
        $data['manufacturing_date'] = $request->manufacturing_date;
        $data['country_origin'] = $request->country_origin;
        $data['generic_name'] = $request->generic_name;
        $data['asin'] = $request->asin;
        $data['ingredients'] = $request->ingredients;
        $data['expiry_days'] = $request->expiry_days;
        $data['storage_instruction'] = $request->storage_instruction;

        if ($request->inventory_type === 'own') {
            $data['sku'] = $request->sku;
            $data['inventory'] = $request->inventory;
            $data['track_stock'] = $request->track_stock;
        }

        if ($request->inventory_type === 'dropshipping') {
            $data['supplier_name'] = $request->supplier_name;
            $data['supplier_product_id'] = $request->supplier_product_id;
            $data['supplier_cost'] = $request->supplier_cost;
            $data['shipping_time'] = $request->shipping_time;
            $data['auto_order'] = $request->auto_order;
        }
    }

    // =====================================================
    // ✅ TOUR GUIDANCE & INFORMATION ADVISORY
    // =====================================================
    if (in_array($request->type, ['tour', 'information_advisory', 'vtravel-tour', 'vtravel-information_advisory'])) {

        $rules = [
            'service_country'  => 'required',
            'service_state'    => 'required',
            'service_district' => 'required',
            'service_type'     => 'required',
        ];

        // Availability is only required for tour types (field is hidden for info_advisory)
        if (in_array($request->type, ['tour', 'vtravel-tour'])) {
            $rules['availability'] = 'required|string';
        }

        // Require listing_type for information_advisory
        if (in_array($request->type, ['information_advisory', 'vtravel-information_advisory'])) {
            $rules['listing_type'] = 'required|string';
        }

        // CONDITIONAL VALIDATION for service type
        if (in_array($request->service_type, ['hourly', 'day'])) {
            $rules['service_days'] = 'required|array|min:1';
            $rules['start_time']   = 'required';
            $rules['end_time']     = 'required|after:start_time';
        }

        if ($request->service_type == 'fixed') {
            $rules['fixed_type'] = 'required';
            $rules['fixed_service_mode'] = 'required';

            if ($request->fixed_service_mode == 'days') {
                $rules['service_days_count'] = 'required|integer|min:1';
                $rules['service_nights']     = 'required|integer|min:0';
            }

            if ($request->fixed_service_mode == 'hours') {
                $rules['start_time_fixed'] = 'required';
                $rules['end_time_fixed']   = 'required|after:start_time_fixed';
            }
        }

        $request->validate($rules);

        $data['country'] = $request->service_country;
        $data['state'] = $request->service_state;
        $data['district'] = $request->service_district;
        $data['service_type'] = $request->service_type;
        $data['service_days'] = $request->service_days ? json_encode($request->service_days) : null;
        $data['start_time'] = $request->start_time;
        $data['end_time'] = $request->end_time;
        $data['fixed_type'] = $request->fixed_type;
        $data['fixed_dates'] = $request->fixed_dates;
        $data['service_days_count'] = $request->service_days_count;
        $data['service_nights'] = $request->service_nights;
        $data['availability'] = $request->availability;

        // Suitability fields
        $data['adult_count'] = $request->no_of_adults;
        $data['kids_count'] = $request->no_of_kids;
        $data['kids_allowed'] = $request->suitable_kids;
        $data['adult_allowed'] = $request->suitable_adults;
    }

    // =====================================================
    // ✅ OTHER SERVICES (with Language Support)
    // =====================================================
    if (in_array($request->type, ['service', 'vshop-service', 'vtravel-service'])) {

        $request->validate([
            'other_service_type'  => 'required',
            'processing_time'     => 'required',
            'documents'           => 'required',
            'validity'            => 'required',
            'cancel_policy_other' => 'required',
            'service_languages'   => 'required|string', // Language Support Validation
        ]);

        $data['other_service_type']  = $request->other_service_type;
        $data['processing_time']     = $request->processing_time;
        $data['documents']           = $request->documents;
        $data['validity']            = $request->validity;
        $data['gov_fee']             = $request->gov_fee;
        $data['cancel_policy_other'] = $request->cancel_policy_other;
        $data['service_languages']   = $request->service_languages; // Save languages (comma-separated)
    }

    // =====================================================
    // ✅ SAVE
    // =====================================================
    if ($product && $product->exists) {
        $product->update($data);
        $productId = $product->id;
        
        // Notification for update
        $this->createNotification($seller->id, 'product', 'Product "' . $data['name'] . '" basic information updated successfully', route('seller.products.index'));
        
        // Log the update
        $this->createLog($productId, 'basic_update', null, $data);
        
    } else {
        $data['created_by']    = 'seller';
        $data['created_by_id'] = $seller->id;

        $product = Product::create($data);
        $productId = $product->id;
        
        // Notification for new product
        $this->createNotification($seller->id, 'product', 'New product "' . $data['name'] . '" created successfully. Please complete all steps.', route('seller.products.index'));
        
        // Log the creation
        $this->createLog($productId, 'create', null, $data);
    }

    return redirect(route('seller.products.media.edit', $productId))
        ->with('success', 'Basic Saved Successfully');
}

/* ===================================================
STEP 2 : MEDIA
=================================================== */

public function getStates($country_id)
{
    $states = \App\Models\State::where('country_id', $country_id)
        ->orderBy('name')
        ->get(['id','name']);

    return response()->json($states);
}

public function getCities($state_id)
{
    $cities = \App\Models\City::where('state_id', $state_id)
        ->orderBy('name')
        ->get(['id','name']);

    return response()->json($cities);
}

public function media(Product $product = null)
{
    $product = $product ?? new Product();

    if($product->id){
        $this->checkOwner($product);
    }

    return view(
        'vshop.seller.products.steps.media',
        compact('product')
    );
}


public function saveMedia(Request $request, Product $product)
{
    $this->checkOwner($product);

    /*
    |--------------------------------------------------------------------------
    | PATHS
    |--------------------------------------------------------------------------
    */
    $galleryPath = 'uploads/products/gallery';
    $thumbPath   = 'uploads/products/thumbnail';

    /*
    |--------------------------------------------------------------------------
    | CREATE FOLDERS
    |--------------------------------------------------------------------------
    */
    if (!file_exists(public_path($galleryPath))) {
        mkdir(public_path($galleryPath), 0755, true);
    }

    if (!file_exists(public_path($thumbPath))) {
        mkdir(public_path($thumbPath), 0755, true);
    }

    /*
    |--------------------------------------------------------------------------
    | EXISTING DATA
    |--------------------------------------------------------------------------
    */
    $oldImages = json_decode($product->images, true) ?? [];

    $images = [];
    $videos = [];

    /*
    |--------------------------------------------------------------------------
    | THUMBNAIL
    |--------------------------------------------------------------------------
    */
    $hasOldThumb =
        !empty($product->thumbnail) &&
        file_exists(public_path($product->thumbnail));

    if ($request->hasFile('thumbnail')) {

        // delete old
        if ($product->thumbnail && file_exists(public_path($product->thumbnail))) {
            unlink(public_path($product->thumbnail));
        }

        $file = $request->file('thumbnail');

        $name = time() . '_thumb_' . $file->getClientOriginalName();

        $file->move(public_path($thumbPath), $name);

        $product->thumbnail = $thumbPath . '/' . $name;

    } elseif (!$hasOldThumb) {

        return response()->json([
            'status' => false,
            'error'  => 'Thumbnail image is required'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | KEEP EXISTING IMAGES
    |--------------------------------------------------------------------------
    */
    $existingImages = $request->existing_images ?? [];

    foreach ($existingImages as $img) {
        if (!empty($img) && file_exists(public_path($img))) {
            $images[] = $img;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE REMOVED OLD IMAGES
    |--------------------------------------------------------------------------
    */
    foreach ($oldImages as $oldImg) {
        if (!in_array($oldImg, $existingImages)) {
            if (file_exists(public_path($oldImg))) {
                unlink(public_path($oldImg));
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | NEW GALLERY IMAGES
    |--------------------------------------------------------------------------
    */
    if ($request->hasFile('images')) {

        foreach ($request->file('images') as $file) {

            $name = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();

            $file->move(public_path($galleryPath), $name);

            $images[] = $galleryPath . '/' . $name;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VIDEOS (CLEAN EMPTY VALUES)
    |--------------------------------------------------------------------------
    */
    if ($request->video_urls) {

        foreach ($request->video_urls as $url) {

            if (!empty(trim($url))) {
                $videos[] = trim($url);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FINAL MEDIA VALIDATION (🔥 FIXED)
    |--------------------------------------------------------------------------
    */
    $imageCount = count($images);
    $videoCount = count($videos);

    // ❌ NOTHING UPLOADED
    if ($imageCount == 0 && $videoCount == 0) {
        return response()->json([
            'status' => false,
            'error'  => 'Upload at least 1 image or add 1 video URL'
        ]);
    }

    // ✅ ONLY IMAGE LIMIT (IMPORTANT FIX)
    if ($imageCount > 4) {
        return response()->json([
            'status' => false,
            'error'  => 'Maximum 4 images allowed'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE
    |--------------------------------------------------------------------------
    */
    $product->images = json_encode($images);
    $product->videos = json_encode($videos);

    $product->save();

    // Notification for media update
    $this->createNotification($product->created_by_id, 'product', 'Product "' . $product->name . '" media updated successfully', route('seller.products.index'));
    
    // Log the media update
    $this->createLog($product->id, 'media_update', null, ['images' => $images, 'videos' => $videos, 'thumbnail' => $product->thumbnail]);

    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */
    return response()->json([
        'status'   => true,
        'redirect' => route(
            'seller.products.specifications.edit',
            $product->id
        )
    ]);
}

/* ===================================================
STEP 3 : SPECIFICATIONS
=================================================== */

public function specifications(Product $product = null)
{
    // If no product passed, create empty instance
    if (!$product || !$product->id) {
        $product = new Product();
    } else {
        // Security check
        $this->checkOwner($product);
    }

    return view(
        'vshop.seller.products.steps.specifications',
        compact('product')
    );
}

public function getStockMovements(Request $request, Product $product)
{
    $this->checkOwner($product);

    $perPage = (int) $request->get('per_page', 4);
    $perPage = min($perPage, 50); // safety cap

    $movements = \App\Models\StockMovement::where('product_id', $product->id)
        ->orderByDesc('created_at')
        ->paginate($perPage);

    return response()->json($movements);
}

public function saveSpecifications(Request $request, Product $product)
{
    $this->checkOwner($product);

    $messages = [
        'required' => 'This field is mandatory',
    ];

    $rules = [];

    // ================= PRODUCT =================
    if (in_array($product->type, ['product', 'vshop-product'])) {
        $rules = [
            'sku'           => 'required|string|max:100',
            'item_code'     => 'required|string|max:100',
            'quantity'      => 'required|string|max:100',
            'batch_number'  => 'required|string|max:100',
            'stock_qty'     => 'required|integer|min:0',
            'delivery_time' => 'required|string|max:100',
            // Optional: add validation for delivery_hour if needed
            'delivery_hour' => 'nullable|regex:/^\d{1,2}:\d{2}$/',
        ];
    }

    // ================= PROFESSIONAL / SERVICE =================
    if (in_array($product->type, ['professional', 'vshop-professional', 'vtravel-professional', 'service', 'vshop-service', 'vtravel-service', 'information_advisory', 'vtravel-information_advisory'])) {
        $rules = array_merge($rules, [
            'service_duration' => 'required|string|max:255',
            'session_type'     => 'required|string|max:50',
            'start_time'       => 'required',
            'end_time'         => 'required|after:start_time',
            'provider_name'    => 'required|string|max:255',
            'max_participants' => 'required|integer|min:1',
            'booking_required' => 'required|string|max:10',
            'service_validity' => 'required|date',
            'meeting_link'     => 'required|string|max:500',
        ]);
    }

    // ================= SERVICE / TOUR =================
    if (in_array($product->type, ['service', 'vshop-service', 'vtravel-service', 'tour', 'vtravel-tour'])) {
        $rules = array_merge($rules, [
            'itinerary'        => 'required|string|min:10',
            'refund_policy'    => 'required|string|min:10',
            'accommodation'    => 'nullable|string',
            'inclusion'        => 'nullable|string',
            'included_excluded'=> 'nullable|string',
            'luggage'          => 'nullable|string',
            'free_days'        => 'nullable|string',
            'benefits'         => 'nullable|string',
            'research'         => 'nullable|string',
        ]);
    }

    // ================= INFORMATION & ADVISORY =================
    // (no extra validation rules – fields are optional)

    $validated = $request->validate($rules, $messages);

    /*
    |--------------------------------------------------
    | SAVE DATA
    |--------------------------------------------------
    */
    $data = $request->except('_token');

    /*
    |--------------------------------------------------
    | 🔥 FIX: quantity – extract numeric value only
    | The form may send a descriptive string like
    | "1 kit, manual guide, sticker" but the DB column
    | is INTEGER. Extract the leading number and store
    | the full text in product_specifications instead.
    |--------------------------------------------------
    */
    if (isset($data['quantity'])) {
        $rawQuantity = $data['quantity'];
        // If non-numeric, extract the leading integer
        if (!is_numeric($rawQuantity)) {
            // Store descriptive text in product_specifications if not already set
            if (empty($data['product_specifications'])) {
                $data['product_specifications'] = $rawQuantity;
            }
            // Extract leading number (e.g. "1 kit" → 1)
            if (preg_match('/^(\d+)/', $rawQuantity, $m)) {
                $data['quantity'] = (int) $m[1];
            } else {
                $data['quantity'] = null;
            }
        } else {
            $data['quantity'] = (int) $rawQuantity;
        }
    }

    // Convert booking_required to integer
    if ($request->has('booking_required')) {
        $data['booking_required'] = ($request->booking_required === 'yes' || $request->booking_required == 1) ? 1 : 0;
    }

    $data['auto_disable'] = $request->has('auto_disable');

    /*
    |--------------------------------------------------
    | 🔥 FIX: delivery_hour – convert HH:MM to integer (hour)
    |--------------------------------------------------
    */
    if ($request->filled('delivery_hour')) {
        $time = $request->delivery_hour; // e.g., "10:38"

        if (preg_match('/^(\d{1,2}):(\d{2})$/', $time, $matches)) {
            $hour = (int) $matches[1]; // extract hour
            $data['delivery_hour'] = $hour;
        } else {
            // If not in HH:MM format, set null (or handle as needed)
            $data['delivery_hour'] = null;
        }
    }

    /*
    |--------------------------------------------------
    | TIME SLOT
    |--------------------------------------------------
    */
    if ($request->filled('start_time') && $request->filled('end_time')) {
        $data['schedule_slots'] = $request->start_time . ' - ' . $request->end_time;
    }

    /*
    |--------------------------------------------------
    | FORCE SAVE EXTRA FIELDS FOR SERVICE / TOUR
    |--------------------------------------------------
    */
    if (in_array($product->type, ['service', 'vshop-service', 'vtravel-service', 'tour', 'vtravel-tour'])) {
        $data['itinerary']         = $request->itinerary;
        $data['accommodation']     = $request->accommodation;
        $data['inclusion']         = $request->inclusion ?? $request->included_excluded;
        $data['included_excluded'] = $request->included_excluded ?? $request->inclusion;
        $data['luggage']           = $request->luggage;
        $data['refund_policy']     = $request->refund_policy;
        $data['free_days']         = $request->free_days;
        $data['benefits']          = $request->benefits;
        $data['research']          = $request->research;
    }

    /*
    |--------------------------------------------------
    | UPDATE PRODUCT
    |--------------------------------------------------
    */
    $product->update($data);

    // Notification & Log
    $this->createNotification($product->created_by_id, 'product', 'Product "' . $product->name . '" specifications saved successfully', route('seller.products.index'));
    $this->createLog($product->id, 'specifications_update', null, $data);

    return redirect()
        ->route('seller.products.seo.edit', $product->id)
        ->with('success', 'Specifications Saved Successfully');
}

public function saveLeadSettings(Request $request, Product $product)
{
    $this->checkOwner($product);

    $request->validate([
        'enable_enquiry' => 'required|in:0,1,true,false',
        'contact_visibility' => 'nullable|array',
    ]);

    // Convert request inputs
    $enable_enquiry = filter_var($request->enable_enquiry, FILTER_VALIDATE_BOOLEAN);
    $contact_visibility = $request->contact_visibility ?? [];

    $product->update([
        'enable_enquiry' => $enable_enquiry,
        'contact_visibility' => $contact_visibility,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Lead Settings saved successfully!',
        'data' => [
            'enable_enquiry' => $product->enable_enquiry,
            'contact_visibility' => $product->contact_visibility ?? [],
        ]
    ]);
}

/* ===================================================
STEP 4 : SEO
=================================================== */

public function seo(Product $product = null)
{
    $product = $product ?? new Product();

    if($product->id){
        $this->checkOwner($product);
    }

    return view(
        'vshop.seller.products.steps.seo',
        compact('product')
    );
}


public function saveSeo(Request $request, Product $product = null)
{
    if ($product && $product->id) {
        $this->checkOwner($product);
    }

    // ✅ STRONG VALIDATION
    $request->validate([
        'seo_tags'         => 'required|string|min:3|max:255',
        'meta_title'       => 'required|string|min:10|max:255',
        'meta_keywords'    => 'required|string|min:5|max:255',
        'meta_description' => 'required|string|min:30|max:1000',
        'bullet_points'    => 'required|string|min:20|max:2000',
        'target_audience' => [
    'required',
    'in:kids,students,professionals,seniors,all',
],
    ],[
        'seo_tags.required'         => 'SEO Tags required',
        'meta_title.required'       => 'Meta title required',
        'meta_keywords.required'    => 'Meta keywords required',
        'meta_description.required' => 'Meta description required',
        'bullet_points.required'    => 'Bullet points required',
        'target_audience.required'  => 'Target audience required',
    ]);

    // ✅ SAVE
    $seoData = [
        'seo_tags'         => $request->seo_tags,
        'meta_title'       => $request->meta_title,
        'meta_keywords'    => $request->meta_keywords,
        'meta_description' => $request->meta_description,
        'bullet_points'    => $request->bullet_points,
        'target_audience'  => $request->target_audience,
    ];
    
    $product->update($seoData);

    // Notification for SEO update
    $this->createNotification($product->created_by_id, 'product', 'Product "' . $product->name . '" SEO information saved successfully', route('seller.products.index'));
    
    // Log the SEO update
    $this->createLog($product->id, 'seo_update', null, $seoData);

    return redirect(
        $product->id
            ? route('seller.products.price.edit', $product->id)
            : route('seller.products.price')
    )->with('success', 'SEO Saved Successfully');
}

/* ===================================================
STEP 5 : PRICE
=================================================== */

public function price(Product $product = null)
{
    $product = $product ?? new Product();

    if ($product->id) {
        $this->checkOwner($product);
    }

    $attributes = Attribute::with('terms')->get();

    // ✅ LOAD VARIANTS FROM product_attribute TABLE
    $combinations = DB::table('product_attribute')
        ->where('proid', $product->id)
        ->get();

    // ✅ CONVERT TAX TO ARRAY
    $taxes = [];
    if ($product->tax_name && $product->tax_percentage) {
        $taxes[] = [
            'name' => $product->tax_name,
            'percentage' => $product->tax_percentage
        ];
    }

    // Fetch Location Data for Target Audience
    $countries = \App\Models\Country::all();
    $states = \App\Models\State::all();
    $cities = \App\Models\City::all();

    return view(
        'vshop.seller.products.steps.price',
        compact('product', 'attributes', 'combinations', 'taxes', 'countries', 'states', 'cities')
    );
}




public function savePrice(Request $request, Product $product)
{
    $this->checkOwner($product);

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */
    $request->validate([
        'combinations.*.price' => 'nullable|numeric|min:0',
        'combinations.*.regular_price' => 'nullable|numeric|min:0',
        'combinations.*.stock' => 'nullable|numeric|min:0',
        'combinations.*.shipping' => 'nullable|numeric|min:0',
        
        'professional_price' => 'nullable|numeric|min:0',
        'price_type' => 'nullable|string',
        
        'adult_price' => 'nullable|numeric|min:0',
        'child_price' => 'nullable|numeric|min:0',
        'group_price' => 'nullable|numeric|min:0',

        'payment_type' => 'required|array|min:1|max:3',
        
        'is_discount' => 'nullable|boolean',
        'discount_type' => 'nullable|in:percentage,fixed',
        'discount_value' => 'nullable|numeric|min:0',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        
        'boost_status' => 'nullable|boolean',
        'boost_duration' => 'nullable|integer',
        'daily_budget' => 'nullable|numeric|min:0',
        'daily_limit' => 'nullable|numeric|min:0',
        'total_budget' => 'nullable|numeric|min:0',
        
        'target_country' => 'nullable|integer',
        'target_state' => 'nullable|integer',
        'target_city' => 'nullable|array',
        
        'age_min' => 'nullable|integer|min:18|max:65',
        'age_max' => 'nullable|integer|min:18|max:65',
        'language' => 'nullable|string',
        'interests' => 'nullable|array',
        'categories' => 'nullable|array',
        
        'promotion_goal' => 'nullable|string',
        
        'stop_daily_budget' => 'nullable|boolean',
        'stop_total_budget' => 'nullable|boolean',
        'notify_before_exhaust' => 'nullable|boolean',
        'notify_percentage' => 'nullable|integer|in:80,90,95',
        
        'taxes' => 'nullable|array',
        'taxes.*.name' => 'nullable|string',
        'taxes.*.percentage' => 'nullable|numeric|min:0|max:100',
    ]);

    /*
    |--------------------------------------------------------------------------
    | PRODUCT VARIANTS
    |--------------------------------------------------------------------------
    */
    $firstPrice = $product->price;

    if(in_array($product->type, ['product', 'vshop-product'])){
        // Delete existing combinations
        DB::table('product_attribute')->where('proid', $product->id)->delete();
        
        $combinations = $request->input('combinations', []);
        $combinationIndex = 0;

        foreach($combinations as $index => $combo){
            $price = $combo['price'] ?? 0;
            $regularPrice = $combo['regular_price'] ?? 0;
            
            // Skip empty rows
            if ($price == 0 && $regularPrice == 0 && empty($combo['stock'])) {
                continue;
            }

            $discount = $combo['discount'] ?? 0;
            if (empty($discount) && $regularPrice > $price && $regularPrice > 0) {
                $discount = round((($regularPrice - $price) / $regularPrice) * 100);
            }

            $combinationKey = 'variant-' . $combinationIndex;
            
            DB::table('product_attribute')->insert([
                'proid' => $product->id,
                'combination_key' => $combinationKey,
                'attribute_id' => 0,
                'attribute_term_id' => $combinationIndex,
                'price' => $price,
                'regular_price' => $regularPrice,
                'discount_value' => $discount,
                'product_stock' => $combo['stock'] ?? 0,
                'shipping_amount' => $combo['shipping'] ?? 0,
                'status' => 'Y',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $combinationIndex++;
        }

        if(!empty($combinations)){
            $firstPrice = collect($combinations)->first()['price'] ?? $product->price;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | BASE PRICE CALCULATION
    |--------------------------------------------------------------------------
    */
    $basePrice = 0;

    // PRODUCT
    if(in_array($product->type, ['product', 'vshop-product'])){
        $basePrice = $firstPrice;
    }
    
    // PROFESSIONAL
    elseif(in_array($product->type, ['professional', 'vshop-professional', 'vtravel-professional'])){
        $basePrice = $request->professional_price ?? 0;
    }
    
    // SERVICE / TOUR / INFORMATION
    else {
        $adult = $request->adult_price ?? 0;
        $child = $request->child_price ?? 0;
        $group = $request->group_price ?? 0;
        $basePrice = $adult + $child + $group;
    }

    /*
    |--------------------------------------------------------------------------
    | TAX CALCULATION
    |--------------------------------------------------------------------------
    */
    $taxNames = [];
    $taxPercentages = [];
    $totalTax = 0;

    if($request->has('taxes')){
        foreach($request->taxes as $tax){
            if(!empty($tax['name']) && !empty($tax['percentage'])){
                $taxNames[] = $tax['name'];
                $taxPercentages[] = (float)$tax['percentage'];
                $totalTax += ($basePrice * (float)$tax['percentage']) / 100;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DISCOUNT CALCULATION
    |--------------------------------------------------------------------------
    */
    $discountAmount = 0;
    $isDiscount = $request->is_discount ?? 0;
    
    if($isDiscount == 1 && !empty($request->discount_value) && $request->discount_value > 0){
        $discountValue = (float)$request->discount_value;
        $discountType = $request->discount_type ?? 'percentage';
        
        if($discountType == 'percentage'){
            $discountAmount = ($basePrice * $discountValue) / 100;
        } else {
            $discountAmount = $discountValue;
        }
        
        // Ensure discount doesn't exceed base price
        if($discountAmount > $basePrice){
            $discountAmount = $basePrice;
        }
    }
    
    $priceAfterDiscount = $basePrice - $discountAmount;
    
    /*
    |--------------------------------------------------------------------------
    | NET TOTAL CALCULATION
    |--------------------------------------------------------------------------
    */
    $netTotal = $priceAfterDiscount + $totalTax;

    /*
    |--------------------------------------------------------------------------
    | UPDATE PRODUCT
    |--------------------------------------------------------------------------
    */
    $priceData = [
        // Base pricing
        'price' => $basePrice,
        'professional_price' => $request->professional_price,
        'price_type' => $request->price_type,
        'adult_price' => $request->adult_price,
        'child_price' => $request->child_price,
        'group_price' => $request->group_price,
        
        // Payment
        'payment_type' => json_encode($request->payment_type),
        
        // Discount
        'is_discount' => $request->is_discount ?? 0,
        'discount_type' => $request->discount_type,
        'discount_value' => $request->discount_value,
        'discount_start_date' => $request->start_date,
        'discount_end_date' => $request->end_date,
        
        // Tax
        'tax_name' => json_encode($taxNames),
        'tax_percentage' => json_encode($taxPercentages),
        
        // Net total
        'net_total' => $netTotal,
        
        // Boost Campaign - Fixed boolean handling
        'boost_status' => $request->has('boost_status') ? 1 : 0,
        'boost_duration' => $request->boost_duration ?? 7,
        'daily_budget' => $request->daily_budget ?? 700,
        'daily_limit' => $request->daily_limit ?? 0,
        'total_budget' => $request->total_budget ?? 0,
        
        // Target Audience
        'target_country' => $request->target_country,
        'target_state' => $request->target_state,
        'target_city' => json_encode($request->target_city ?? []),
        
        // Age & Language
        'age_min' => $request->age_min ?? 20,
        'age_max' => $request->age_max ?? 45,
        'language' => $request->language ?? 'english',
        'interests' => json_encode($request->interests ?? []),
        'categories' => json_encode($request->categories ?? []),
        
        // Promotion Goal
        'promotion_goal' => $request->promotion_goal ?? 'enquiries',
        
        // Hard Stop Rules - Fixed boolean handling
        'stop_daily_budget' => $request->has('stop_daily_budget') ? 1 : 0,
        'stop_total_budget' => $request->has('stop_total_budget') ? 1 : 0,
        'notify_before_exhaust' => $request->has('notify_before_exhaust') ? 1 : 0,
        'notify_percentage' => $request->notify_percentage ?? 95,
    ];
    
    $product->update($priceData);

    // Notification for price update
    $this->createNotification($product->created_by_id, 'product', 'Product "' . $product->name . '" pricing information saved successfully', route('seller.products.index'));
    
    // Log the price update
    $this->createLog($product->id, 'price_update', null, $priceData);

    return redirect()->route('seller.products.faq', $product->id)->with('success', 'Pricing saved successfully!');
}
/* ===================================================
PRODUCT PREVIEW
=================================================== */

public function productList(Product $product)
{
    $this->checkOwner($product);

    $attributes = \DB::table('product_attribute')
        ->where('proid',$product->id)
        ->select('combination_key','price')
        ->groupBy('combination_key','price')
        ->get();

    $termIds = [];

    foreach($attributes as $attr){

        $keys = explode('-',$attr->combination_key);

        foreach($keys as $k){
            $termIds[] = $k;
        }
    }

    $termIds = array_unique($termIds);

    $terms = \App\Models\AttributeTerm::whereIn('id',$termIds)
        ->pluck('term','id');

    return view(
        'vshop.seller.products.steps.list',
        compact('product','attributes','terms')
    );
}

/* ===================================================
REVIEW
=================================================== */

public function review(Request $request)
{
    $sellerId = auth()->guard('seller')->id();

    $status = $request->get('status'); // '4plus' / 'low' / null

    // Get all product IDs belonging to this seller
    $sellerProductIds = Product::where('created_by_id', $sellerId)->pluck('id');

    $query = ProductReview::whereIn('product_id', $sellerProductIds)
        ->with(['product'])
        ->when($status === '4plus', fn($q) => $q->where('rating', '>=', 4))
        ->when($status === 'low',   fn($q) => $q->where('rating', '<=', 2))
        ->latest();

    $reviews    = $query->paginate(15);
    $totalCount = ProductReview::whereIn('product_id', $sellerProductIds)->count();
    $goodCount  = ProductReview::whereIn('product_id', $sellerProductIds)->where('rating', '>=', 4)->count();
    $badCount   = ProductReview::whereIn('product_id', $sellerProductIds)->where('rating', '<=', 2)->count();

    return view(
        'vshop.seller.products.steps.review',
        compact('reviews', 'status', 'totalCount', 'goodCount', 'badCount')
    );
}


public function productReview(Request $request, $productId)
{
    $sellerId = auth()->guard('seller')->id();

    $rating = $request->get('rating'); // top / low
    $review = $request->get('review'); // good / bad

    $product = Product::where('id', $productId)
        ->where('created_by_id', $sellerId)
        ->firstOrFail();

    $reviews = ProductReview::where('seller_id', $sellerId)
        ->where('product_id', $productId)

        // Лѓлйла RATING FILTER
        ->when($rating == 'top', function ($q) {
            $q->where('rating', '>=', 4);
        })
        ->when($rating == 'low', function ($q) {
            $q->where('rating', '<=', 2);
        })

        // лЂл»лбл╝ REVIEW FILTER
        ->when($review == 'good', function ($q) {
            $q->where('rating', '>=', 4);
        })
        ->when($review == 'bad', function ($q) {
            $q->where('rating', '<=', 2);
        })

        ->latest()
        ->get();

    return view(
        'vshop.seller.products.steps.productreview',
        compact('reviews', 'rating', 'review', 'product')
    );
}

/* ===================================================
DELETE PRODUCT
=================================================== */

public function destroy(Product $product)
{
    $this->checkOwner($product);
    
    $productName = $product->name;
    $sellerId = $product->created_by_id;

    $product->delete();

    // Notification for product deletion
    $this->createNotification($sellerId, 'product', 'Product "' . $productName . '" has been deleted successfully', route('seller.products.index'));
    
    // Log the deletion
    $this->createLog($product->id, 'delete', null, null);

    return redirect()
        ->route('seller.products.index')
        ->with('success','Product Deleted Successfully');
}


public function saveDownload(Request $request, $id)
{
    $product = Product::findOrFail($id);

    // ✅ VALIDATION
    $request->validate([

        'title' => 'required|array|min:1',
        'title.*' => 'required|string|max:255',

        'date' => 'required|array|min:1',
        'date.*' => 'required|date',

        'url' => 'required|array|min:1',
        'url.*' => 'required|url',

    ],[
        'title.*.required' => 'Title is required',
        'date.*.required' => 'Date is required',
        'date.*.date' => 'Invalid date',
        'url.*.required' => 'URL is required',
        'url.*.url' => 'Enter valid URL',
    ]);


    // ✅ SAVE DATA
    $downloads = [];

    foreach ($request->title as $i => $title) {

        $downloads[] = [
            'title' => $title,
            'date'  => $request->date[$i],
            'url'   => $request->url[$i],
        ];
    }

    // ✅ NO JSON ENCODE (MODEL CAST HANDLE)
    $product->download_data = $downloads;
    $product->save();

    // Notification for download update
    $this->createNotification($product->created_by_id, 'product', 'Product "' . $product->name . '" download links updated successfully', route('seller.products.download', $product->id));

    return redirect()
        ->route('seller.products.samplecode', $product->id)
        ->with('success', 'Downloads Saved Successfully');
}




public function saveSample(Request $request, $id)
{
    $product = Product::findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | FORCE ARRAY CONVERSION
    |--------------------------------------------------------------------------
    */

    $languages = (array) $request->language;
    $ides      = (array) $request->ide;
    $types     = (array) $request->type;
    $oses      = (array) $request->os;
    $urls      = (array) $request->url;

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    $request->validate([
        'language.*' => 'required|string|max:255',
        'ide.*'      => 'required|string|max:255',
        'type.*'     => 'required|string|max:255',
        'os.*'       => 'required|string|max:255',
        'url.*'      => 'required|url',
    ],[
        'language.*.required' => 'Language is required',
        'ide.*.required'      => 'IDE is required',
        'type.*.required'     => 'Type is required',
        'os.*.required'       => 'Operating System is required',
        'url.*.required'      => 'Download URL is required',
        'url.*.url'           => 'Enter valid URL',
    ]);

    /*
    |--------------------------------------------------------------------------
    | BUILD SAMPLE ARRAY
    |--------------------------------------------------------------------------
    */

    $samples = [];

    if(!empty($languages)){

        foreach($languages as $key => $lang){

            if(
                !empty($lang) &&
                !empty($ides[$key] ?? null) &&
                !empty($types[$key] ?? null) &&
                !empty($oses[$key] ?? null) &&
                !empty($urls[$key] ?? null)
            ){

                $samples[] = [
                    'language' => $lang,
                    'ide'      => $ides[$key],
                    'type'     => $types[$key],
                    'os'       => $oses[$key],
                    'url'      => $urls[$key],
                ];
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE JSON
    |--------------------------------------------------------------------------
    */

    $product->update([
        'sample_codes' => json_encode($samples)
    ]);

    // Notification for sample update
    $this->createNotification($product->created_by_id, 'product', 'Product "' . $product->name . '" sample codes updated successfully', route('seller.products.samplecode', $product->id));

    return redirect()
        ->route('seller.products.productsreview', $product->id)
        ->with('success', 'Sample Saved Successfully');
}

public function download($id)
{
    $product = Product::findOrFail($id);

    return view('vshop.seller.products.steps.download', compact('product'));
}


public function deleteDownload($id, $download_id)
{
    $product = Product::findOrFail($id);

    // decode JSON
    $downloads = is_array($product->download_data)
        ? $product->download_data
        : json_decode($product->download_data, true);

    if(!empty($downloads) && isset($downloads[$download_id])){

        unset($downloads[$download_id]);

        // reindex
        $downloads = array_values($downloads);

        // save
        $product->download_data = $downloads;
        $product->save();
        
        // Notification for download deletion
        $this->createNotification($product->created_by_id, 'product', 'Download link deleted from product "' . $product->name . '"', route('seller.products.download', $product->id));
    }

    return back()->with('success','Download Deleted Successfully');
}

public function sample($id)
{
    $product = Product::findOrFail($id);

    return view('vshop.seller.products.steps.sample', compact('product'));
}

public function deleteSample($id)
{
    $product = Product::findOrFail($id);

    $product->update([
        'sample_language' => null,
        'sample_ide'      => null,
        'sample_type'     => null,
        'sample_os'       => null,
        'sample_download_url' => null,
    ]);

    // Notification for sample deletion
    $this->createNotification($product->created_by_id, 'product', 'Sample codes deleted from product "' . $product->name . '"', route('seller.products.sample', $product->id));

    return redirect()->back()->with('success','Deleted');
}

public function updateDownload(Request $request, $id, $download_id)
{
    $product = Product::findOrFail($id);

    $downloads = json_decode($product->downloads, true) ?? [];

    $downloads[$download_id] = [
        'title' => $request->title,
        'date'  => $request->date,
        'url'   => $request->url,
    ];

    $product->downloads = json_encode(array_values($downloads));
    $product->save();

    // Notification for download update
    $this->createNotification($product->created_by_id, 'product', 'Download link updated for product "' . $product->name . '"', route('seller.products.download', $product->id));

    return back()->with('success','Download Updated');
}

public function editDownload($id, $download_id)
{
    $product = Product::findOrFail($id);

    $downloads = json_decode($product->downloads, true) ?? [];

    $editData = $downloads[$download_id] ?? null;

    return view('vshop.seller.products.steps.download', compact('product','downloads','editData','download_id'));
}

public function editSample($id, $sample_id)
{
    $product = Product::findOrFail($id);

    $samples = json_decode($product->sample_codes, true) ?? [];

    $editData = $samples[$sample_id] ?? null;

    return view('vshop.seller.products.steps.sample', compact(
        'product',
        'samples',
        'editData',
        'sample_id'
    ));
}

public function updateSample(Request $request, $id, $sample_id)
{
    $product = Product::findOrFail($id);

    $samples = json_decode($product->sample_codes, true) ?? [];

    $samples[$sample_id] = [
        'language' => $request->language,
        'ide'      => $request->ide,
        'type'     => $request->type,
        'os'       => $request->os,
        'url'      => $request->url,
    ];

    // РћюРќЉРћ╝РЋЋЛѓлљлГРћглх re-index array
    $product->sample_codes = json_encode(array_values($samples));
    $product->save();

    // Notification for sample update
    $this->createNotification($product->created_by_id, 'product', 'Sample code updated for product "' . $product->name . '"', route('seller.products.sample', $product->id));

    return back()->with('success','Sample Code Updated');
}


public function faq($id)
{
    $product = Product::findOrFail($id);

    return view('vshop.seller.products.steps.faq', compact('product'));
}


public function faqList($id)
{
    $product = Product::findOrFail($id);

    // IMPORTANT FIX
    $faqs = json_decode($product->faqs, true) ?? [];

    return view('vshop.seller.products.steps.faq-list', compact('product','faqs'));
}

public function saveFaq(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $faqs = [];

    if($request->question){
        foreach($request->question as $index => $q){

            if(!empty($q)){
                $faqs[] = [
                    'question' => $q,
                    'answer'   => $request->answer[$index] ?? ''
                ];
            }
        }
    }

    $product->update([
        'faqs' => json_encode($faqs)
    ]);

    // Notification for FAQ update
    $this->createNotification($product->created_by_id, 'product', 'FAQs updated for product "' . $product->name . '"', route('seller.products.faq.list', $product->id));

    return redirect()->route('seller.products.download', $product->id)
        ->with('success','FAQ Saved Successfully');
}

public function deleteFaq($id, $faq_id)
{
    $product = Product::findOrFail($id);

    $faqs = json_decode($product->faqs, true) ?? [];

    unset($faqs[$faq_id]);

    $product->faqs = json_encode(array_values($faqs));
    $product->save();

    // Notification for FAQ deletion
    $this->createNotification($product->created_by_id, 'product', 'FAQ deleted from product "' . $product->name . '"', route('seller.products.faq.list', $product->id));

    return back()->with('success','FAQ Deleted');
}

public function editFaq($id, $faq_id)
{
    $product = Product::findOrFail($id);

    $faqs = json_decode($product->faqs, true) ?? [];

    $editData = $faqs[$faq_id] ?? null;

    return view('vshop.seller.products.steps.faq', compact(
        'product','faqs','editData','faq_id'
    ));
}

public function updateFaq(Request $request, $id, $faq_id)
{
    $product = Product::findOrFail($id);

    $faqs = json_decode($product->faqs, true) ?? [];

    $faqs[$faq_id] = [
        'question' => $request->question,
        'answer'   => $request->answer
    ];

    $product->faqs = json_encode(array_values($faqs));
    $product->save();

    // Notification for FAQ update
    $this->createNotification($product->created_by_id, 'product', 'FAQ updated for product "' . $product->name . '"', route('seller.products.faq.list', $product->id));

    return back()->with('success','FAQ Updated');
}



public function bulkindex()
{
    /*
    |--------------------------------------------------------------------------
    | MODULE TYPE
    |--------------------------------------------------------------------------
    */

    $moduleType = $this->getModuleType();


    /*
    |--------------------------------------------------------------------------
    | QUERY
    |--------------------------------------------------------------------------
    */

    $products = Product::where(
        'created_by_id',
        auth()->guard('seller')->id()
    );


    /*
    |--------------------------------------------------------------------------
    | PRODUCT ONLY
    |--------------------------------------------------------------------------
    */

    if($moduleType == 'product'){

        $products->where('type', 'product');
    }


    /*
    |--------------------------------------------------------------------------
    | SERVICE ONLY
    |--------------------------------------------------------------------------
    */

    elseif($moduleType == 'service'){

        $products->where('type', 'service');
    }


    /*
    |--------------------------------------------------------------------------
    | CAMPUS ONLY
    |--------------------------------------------------------------------------
    */

    elseif($moduleType == 'course'){

        $products->where('type', 'campus');
    }


    /*
    |--------------------------------------------------------------------------
    | FINAL
    |--------------------------------------------------------------------------
    */

    $products = $products
        ->with('category')
        ->latest()
        ->paginate(20);

    $categories = \App\Models\MarketplaceCategory::query();
    if($moduleType == 'product'){
        $categories->whereIn('type', ['product','professional']);
    }
    elseif($moduleType == 'service'){
        $categories->whereIn('type', ['service','tour']);
    }
    elseif($moduleType == 'course'){
        $categories->where('type', 'course');
    }
    $categories = $categories->orderBy('name')->get();

    return view(
        'vshop.seller.products.bulk',
        compact(
            'products',
            'moduleType',
            'categories'
        )
    );
}





public function bulkUpdate(Request $request)
{
    if (!$request->ids || !is_array($request->ids)) {

        return back()->with(
            'error',
            'No items selected'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MODULE TYPE
    |--------------------------------------------------------------------------
    */

    $moduleType = $this->getModuleType();


    /*
    |--------------------------------------------------------------------------
    | QUERY
    |--------------------------------------------------------------------------
    */

    $products = Product::whereIn(
            'id',
            $request->ids
        )
        ->where(
            'created_by_id',
            auth()->guard('seller')->id()
        );


    /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

    if($moduleType == 'product'){

        $products->where('type', 'product');
    }

    elseif($moduleType == 'service'){

        $products->where('type', 'service');
    }

    elseif($moduleType == 'course'){

        $products->where('type', 'campus');
    }


    /*
    |--------------------------------------------------------------------------
    | FINAL
    |--------------------------------------------------------------------------
    */

    $products = $products->get();
    
    $updatedCount = 0;

    foreach ($products as $product) {

        $id = $product->id;
        
        $oldData = [
            'price' => $product->price,
            'status' => $product->status,
            'inventory' => $product->inventory,
            'category_id' => $product->category_id,
            'boost' => $product->boost,
        ];
        
        if ($request->has('bulk_edit_apply')) {
            $newData = [
                'price' => $request->filled('bulk_price') ? $request->bulk_price : $product->price,
                'status' => $request->filled('bulk_status') ? $request->bulk_status : $product->status,
                'inventory' => $request->filled('bulk_stock') ? $request->bulk_stock : $product->inventory,
                'category_id' => $request->filled('bulk_category_id') ? $request->bulk_category_id : $product->category_id,
                'boost' => $request->filled('bulk_boost') ? $request->bulk_boost : $product->boost,
            ];
        } else {
            $newData = [
                'price' => $request->price[$id] ?? $product->price,
                'status' => $request->status[$id] ?? $product->status,
                'inventory' => $request->stock[$id] ?? $product->inventory,
                'category_id' => $request->category_id[$id] ?? $product->category_id,
                'boost' => $request->boost[$id] ?? $product->boost,
            ];
        }

        $product->price = $newData['price'];
        $product->status = $newData['status'];
        $product->inventory = $newData['inventory'];
        $product->category_id = $newData['category_id'];
        $product->boost = $newData['boost'];
        $product->save();
        
        $updatedCount++;
        
        // Log each product update
        $this->createLog($product->id, 'bulk_update', $oldData, $newData);
    }

    // Notification for bulk update
    if ($updatedCount > 0) {
        $this->createNotification(auth()->guard('seller')->id(), 'product', $updatedCount . ' products updated successfully via bulk action', route('seller.products.bulk'));
    }

    return back()->with(
        'success',
        'Updated successfully'
    );
}


public function bulkDelete(Request $request)
{
    if(!$request->ids){

        return back()->with(
            'error',
            'No items selected'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MODULE TYPE
    |--------------------------------------------------------------------------
    */

    $moduleType = $this->getModuleType();


    /*
    |--------------------------------------------------------------------------
    | QUERY
    |--------------------------------------------------------------------------
    */

    $products = Product::whereIn(
            'id',
            $request->ids
        )
        ->where(
            'created_by_id',
            auth()->guard('seller')->id()
        );


    /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

    if($moduleType == 'product'){

        $products->where('type', 'product');
    }

    elseif($moduleType == 'service'){

        $products->where('type', 'service');
    }

    elseif($moduleType == 'course'){

        $products->where('type', 'campus');
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    
    $productsList = $products->get();
    $deletedCount = $productsList->count();
    
    foreach ($productsList as $product) {
        // Log each deletion before deleting
        $this->createLog($product->id, 'bulk_delete', null, null);
    }
    
    $products->delete();

    // Notification for bulk deletion
    if ($deletedCount > 0) {
        $this->createNotification(auth()->guard('seller')->id(), 'product', $deletedCount . ' products deleted successfully via bulk action', route('seller.products.bulk'));
    }

    return back()->with(
        'success',
        'Selected items deleted'
    );
}




public function downloadSample()
{
    $fileName = "sample_products.csv";

    $headers = [
        "type","platform","name","category_id","subcategory_id","description",
        "brand_name","manufactured_by","manufacturing_date","country_origin",
        "generic_name","asin","ingredients","expiry_days","storage_instruction",
        "inventory_type","sku","inventory","track_stock",
        "price","payment_type","discount_type","discount_value","tax_percentage",
        "thumbnail_url","gallery_urls","video_urls",
        "meta_title","meta_keywords","meta_description",
        "faq_question","faq_answer"
    ];

    $sampleRows = [
        [
            "product","vshop","Demo Product 1",
            "22","43","Test Product Description 1",
            "Nike","Company","2024-01-01","India",
            "Shirt","SKU001","Cotton","365","Keep dry",
            "own","SKU001","50","1",
            "500","cod|online","percentage","10","18",
            "https://img.jpg","img1.jpg|img2.jpg","https://youtube.com",
            "Title 1","keywords1","desc 1",
            "What is this?","Answer 1"
        ],
        [
            "product","vshop","Demo Product 2",
            "22","43","Test Product Description 2",
            "Adidas","Company","2024-01-01","India",
            "Shoes","SKU002","Leather","365","Keep dry",
            "own","SKU002","30","1",
            "1000","cod|online","fixed","200","18",
            "https://img2.jpg","img3.jpg|img4.jpg","https://youtube.com",
            "Title 2","keywords2","desc 2",
            "What is this?","Answer 2"
        ],
        [
            "product","vshop","Demo Product 3",
            "22","43","Test Product Description 3",
            "Puma","Company","2024-01-01","India",
            "Shoes","SKU003","Leather","365","Keep dry",
            "own","SKU003","25","1",
            "800","cod|online","percentage","5","18",
            "https://img3.jpg","img5.jpg|img6.jpg","https://youtube.com",
            "Title 3","keywords3","desc 3",
            "What is this?","Answer 3"
        ],
    ];

    $callback = function() use ($headers, $sampleRows) {
        $file = fopen('php://output', 'w');

        // UTF-8 BOM
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write header
        fputcsv($file, $headers);

        // Write sample rows
        foreach($sampleRows as $row) {
            fputcsv($file, $row);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName"
    ]);
}





public function bulkUpload(Request $request)
{
    // ✅ FILE VALIDATION
    $request->validate([
        'file' => 'required|mimes:csv,txt|max:4096'
    ]);

    try {

        $file = $request->file('file');

        // ✅ READ CSV
        $rows = [];
        if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        if(empty($rows) || count($rows) < 2){
            return back()->with('error','File empty ya invalid hai');
        }

        // ✅ HEADER CLEAN
        $header = array_map(fn($h) => strtolower(trim($h)), $rows[0]);
        unset($rows[0]);

        $successCount = 0;
        $errorRows = [];

        foreach ($rows as $index => $row) {

            if(count($row) != count($header)){
                $errorRows[] = "Row ".($index+1)." : Column mismatch";
                continue;
            }

            $data = array_combine($header, $row);

            // ================= VALIDATION =================
            $validator = Validator::make($data, [
                'name' => 'required|max:100|unique:products,name',
                'sku' => 'nullable|unique:products,sku',
                'price' => 'nullable|numeric',
                'inventory' => 'nullable|integer|min:0',
            ],[
                'name.required' => 'Product name is required',
                'name.unique' => 'Duplicate product name',
                'sku.unique' => 'Duplicate SKU',
            ]);

            if($validator->fails()){
                $errorRows[] = "Row ".($index+1)." : ".$validator->errors()->first();
                continue;
            }

            DB::beginTransaction();

            try {

                $sellerId = auth('seller')->id();

                // ================= CREATE PRODUCT =================
                $product = Product::create([
                    'type' => $data['type'] ?? 'product',
                    'platform' => $data['platform'] ?? 'vshop',
                    'name' => $data['name'],
                    'category_id' => $data['category_id'] ?? null,
                    'subcategory_id' => $data['subcategory_id'] ?? null,
                    'description' => $data['description'] ?? '',
                    'product_code' => 'PRD'.rand(10000,99999),
                    'status' => 'pending',
                    'created_by' => 'seller',
                    'created_by_id' => $sellerId,
                ]);

                // ================= PRODUCT DETAILS =================
                if(($data['type'] ?? '') == 'product'){

                    $product->update([
                        'brand_name' => $data['brand_name'] ?? null,
                        'manufactured_by' => $data['manufactured_by'] ?? null,
                        'manufacturing_date' => $data['manufacturing_date'] ?? null,
                        'country_origin' => $data['country_origin'] ?? null,
                        'generic_name' => $data['generic_name'] ?? null,
                        'asin' => $data['asin'] ?? null,
                        'ingredients' => $data['ingredients'] ?? null,
                        'expiry_days' => $data['expiry_days'] ?? null,
                        'storage_instruction' => $data['storage_instruction'] ?? null,
                    ]);

                    // INVENTORY
                    if(($data['inventory_type'] ?? '') == 'own'){
                        $product->update([
                            'inventory_type' => 'own',
                            'sku' => $data['sku'] ?? null,
                            'inventory' => $data['inventory'] ?? 0,
                            'track_stock' => $data['track_stock'] ?? 0
                        ]);
                    }

                    if(($data['inventory_type'] ?? '') == 'dropshipping'){
                        $product->update([
                            'inventory_type' => 'dropshipping',
                            'supplier_name' => $data['supplier_name'] ?? null,
                            'supplier_product_id' => $data['supplier_product_id'] ?? null,
                            'supplier_cost' => $data['supplier_cost'] ?? 0,
                            'shipping_time' => $data['shipping_time'] ?? null,
                            'auto_order' => $data['auto_order'] ?? 0
                        ]);
                    }
                }

                // ================= MEDIA =================
                $product->update([
                    'thumbnail' => $data['thumbnail_url'] ?? null,
                    'images' => isset($data['gallery_urls']) ? json_encode(explode('|',$data['gallery_urls'])) : null,
                    'videos' => isset($data['video_urls']) ? json_encode(explode('|',$data['video_urls'])) : null,
                ]);

                // ================= SEO =================
                $product->update([
                    'meta_title' => $data['meta_title'] ?? null,
                    'meta_keywords' => $data['meta_keywords'] ?? null,
                    'meta_description' => $data['meta_description'] ?? null,
                ]);

                // ================= PRICE =================
                $product->update([
                    'price' => $data['price'] ?? 0,
                    'adult_price' => $data['adult_price'] ?? null,
                    'child_price' => $data['child_price'] ?? null,
                    'group_price' => $data['group_price'] ?? null,
                    'payment_type' => isset($data['payment_type']) ? json_encode(explode('|',$data['payment_type'])) : null,
                    'discount_type' => $data['discount_type'] ?? null,
                    'discount_value' => $data['discount_value'] ?? null,
                    'tax_percentage' => $data['tax_percentage'] ?? null,
                ]);

                // ================= FAQ =================
                if(!empty($data['faq_question'])){
                    $product->update([
                        'faqs' => json_encode([[
                            'question' => $data['faq_question'],
                            'answer' => $data['faq_answer'] ?? ''
                        ]])
                    ]);
                }

                DB::commit();
                $successCount++;

            } catch (QueryException $e){

                DB::rollBack();

                // ✅ DUPLICATE ERROR
                if($e->getCode() == 23000){
                    $errorRows[] = "Row ".($index+1)." : Duplicate entry (name / SKU)";
                } else {
                    $errorRows[] = "Row ".($index+1)." failed";
                }
            }
        }

        // ✅ NOTIFICATION
        if($successCount > 0){
            Notification::create([
                'seller_id' => auth('seller')->id(),
                'type' => 'product',
                'message' => "$successCount Products Uploaded Successfully",
                'link' => route('seller.products.index'),
            ]);
        }

        return back()
            ->with('success',"Uploaded: $successCount rows")
            ->with('errorRows', $errorRows);

    } catch (\Exception $e){
        return back()->with('error',$e->getMessage());
    }
}


public function productLogs($id)
{
    $product = Product::findOrFail($id);

    $logs = ProductLog::where('product_id', $id)
        ->orderBy('created_at', 'desc')
        ->get();

    return view(
        'vshop.seller.products.logs',
        compact('product', 'logs')
    );
}



private function getModuleType()
{
    /*
    |--------------------------------------------------------------------------
    | SESSION PANELS
    |--------------------------------------------------------------------------
    */

    $panels = session('selected_panels', []);


    /*
    |--------------------------------------------------------------------------
    | PRODUCT ONLY
    |--------------------------------------------------------------------------
    */

    if(
        in_array('product', $panels)
        &&
        !in_array('travel', $panels)
    ){
        return 'product';
    }


    /*
    |--------------------------------------------------------------------------
    | SERVICE ONLY
    |--------------------------------------------------------------------------
    */

    if(
        !in_array('product', $panels)
        &&
        in_array('travel', $panels)
    ){
        return 'service';
    }


    /*
    |--------------------------------------------------------------------------
    | BOTH
    |--------------------------------------------------------------------------
    */

    if(
        in_array('product', $panels)
        &&
        in_array('travel', $panels)
    ){
        return 'both';
    }


    /*
    |--------------------------------------------------------------------------
    | COURSE
    |--------------------------------------------------------------------------
    */

    if(
        in_array('vcampus', $panels)
    ){
        return 'course';
    }


    /*
    |--------------------------------------------------------------------------
    | DEFAULT
    |--------------------------------------------------------------------------
    */

    return 'product';
}

public function searchAjax(Request $request)
{
    $q = trim($request->q);
    $excludeId = $request->exclude_id;
    $sellerId = Auth::id();

    $query = Product::query()->where('created_by_id', $sellerId);

    // ================= PANEL TYPE FILTER =================
    $selectedPanels = session('selected_panels', []);
    $allowedTypes = [];

    if(in_array('product', $selectedPanels)){
        $allowedTypes = array_merge($allowedTypes, [
            'product', 'vshop-product',
            'professional', 'vshop-professional',
            'vshop-service', 'vshop-tour', 'vshop-information_advisory'
        ]);
    }

    if(in_array('travel', $selectedPanels)){
        $allowedTypes = array_merge($allowedTypes, [
            'service', 'vtravel-service',
            'tour', 'vtravel-tour',
            'vtravel-professional', 'vtravel-information_advisory',
            'information_advisory'
        ]);
    }

    if(!empty($allowedTypes)){
        $query->whereIn('type', $allowedTypes);
    }
    // ================= END =================

    if ($excludeId) {
        $query->where('id', '!=', $excludeId);
    }

    if ($q) {
        $query->where(function($sub) use ($q) {
            $sub->where('name', 'LIKE', "%{$q}%")
                ->orWhere('sku', 'LIKE', "%{$q}%");
        });
    }

    $products = $query->limit(20)->get(['id', 'name', 'price', 'thumbnail', 'sku']);

    foreach ($products as $product) {
        $product->thumbnail_url = $product->thumbnail ? asset($product->thumbnail) : asset('images/no-image.png');
    }

    return response()->json($products);
}

public function getRecommendations($id)
{
    $product = Product::findOrFail($id);
    $this->checkOwner($product);

    $rules = RecommendationRule::where('type', 'product')
        ->where('source', $product->id)
        ->get();

    $categories = [
        'related' => [],
        'upsell' => [],
        'cross_sell' => [],
        'bought_together' => []
    ];

    foreach ($rules as $rule) {
        $cat = $rule->source_type;
        if (array_key_exists($cat, $categories)) {
            $targetIds = is_array($rule->targets) ? $rule->targets : json_decode($rule->targets, true);
            if (!empty($targetIds)) {
                $targetProducts = Product::whereIn('id', $targetIds)
                    ->get(['id', 'name', 'price', 'thumbnail', 'sku']);
                foreach ($targetProducts as $tp) {
                    $tp->thumbnail_url = $tp->thumbnail ? asset($tp->thumbnail) : asset('images/no-image.png');
                }
                $categories[$cat] = $targetProducts;
            }
        }
    }

    return response()->json([
        'product' => [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $product->price,
            'thumbnail_url' => $product->thumbnail ? asset($product->thumbnail) : asset('images/no-image.png'),
        ],
        'recommendations' => $categories
    ]);
}

public function saveRecommendations(Request $request, $id)
{
    $product = Product::findOrFail($id);
    $this->checkOwner($product);

    $categories = ['related', 'upsell', 'cross_sell', 'bought_together'];

    foreach ($categories as $cat) {
        $targetIds = $request->input($cat, []);

        if (empty($targetIds)) {
            RecommendationRule::where('type', 'product')
                ->where('source', $product->id)
                ->where('source_type', $cat)
                ->delete();
        } else {
            RecommendationRule::updateOrCreate(
                [
                    'type' => 'product',
                    'source' => $product->id,
                    'source_type' => $cat
                ],
                [
                    'targets' => $targetIds,
                    'is_active' => true,
                    'priority' => 0
                ]
            );
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Recommendations saved successfully.'
    ]);
}

public function generateAIContent(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255'
    ]);

    $name = $request->name;
    // Hardcoded API key to bypass env and config cache issues on XAMPP/Apache
    $apiKey = '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';

    if (empty($apiKey)) {
        return response()->json([
            'success' => false,
            'message' => 'OpenRouter API Key not configured.'
        ], 500);
    }

    try {
        $prompt = "You are an e-commerce expert. Generate details for a product named '{$name}'. STRICT REQUIREMENTS: 1) 'short_summary' MUST be strictly under 100 characters in length. 2) 'full_description' MUST be strictly under 290 characters in length total (do not exceed 300 characters under any circumstance). 3) 'product_code' MUST be a realistic 6-8 character alphanumeric code related to the name (e.g. ECO-KW-01). 4) 'ingredients' MUST list realistic ingredients/materials (max 100 chars). 5) 'storage_instruction' MUST be a short realistic storage instruction (max 100 chars). Return strictly valid JSON format with keys 'short_summary', 'full_description', 'product_code', 'ingredients', and 'storage_instruction'. Do not include markdown code blocks like ```json.";
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json'
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'openrouter/auto', // Auto select a working model
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'response_format' => ['type' => 'json_object']
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';
            
            // Clean markdown block if any
            $content = str_replace(['```json', '```'], '', $content);
            $parsed = json_decode(trim($content), true);

            if ($parsed && isset($parsed['short_summary']) && isset($parsed['full_description'])) {
                return response()->json([
                    'success' => true,
                    'short_summary' => $parsed['short_summary'],
                    'full_description' => $parsed['full_description'],
                    'product_code' => $parsed['product_code'] ?? '',
                    'ingredients' => $parsed['ingredients'] ?? '',
                    'storage_instruction' => $parsed['storage_instruction'] ?? ''
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to generate AI content. ' . $response->body()
        ], 500);

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("AI Generate Error: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'AI Generation Error. ' . $e->getMessage()
        ], 500);
    }
}

public function generateSeoAIContent(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255'
    ]);

    $name = $request->name;
    // Hardcoded API key to bypass env and config cache issues on XAMPP/Apache
    $apiKey = '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';

    if (empty($apiKey)) {
        return response()->json([
            'success' => false,
            'message' => 'OpenRouter API Key not configured.'
        ], 500);
    }

    try {
        $prompt = "You are an e-commerce SEO expert. Generate SEO details for a product named '{$name}'. Return strictly valid JSON format with keys: 'seo_tags' (comma separated tags, max 100 chars), 'meta_title' (around 60 chars), 'meta_keywords' (comma separated keywords, max 150 chars), 'meta_description' (around 150 chars), 'bullet_points' (3-5 short bullet points as an HTML unordered list), and 'target_audience' (choose ONE from: 'kids', 'students', 'professionals', 'seniors', 'all'). Do not include markdown code blocks like ```json.";
        
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json'
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'openrouter/auto',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'response_format' => ['type' => 'json_object']
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';
            
            // Clean markdown block if any
            $content = str_replace(['```json', '```'], '', $content);
            $parsed = json_decode(trim($content), true);

            if ($parsed && isset($parsed['seo_tags'])) {
                return response()->json([
                    'success' => true,
                    'seo_tags' => $parsed['seo_tags'] ?? '',
                    'meta_title' => $parsed['meta_title'] ?? '',
                    'meta_keywords' => $parsed['meta_keywords'] ?? '',
                    'meta_description' => $parsed['meta_description'] ?? '',
                    'bullet_points' => $parsed['bullet_points'] ?? '',
                    'target_audience' => strtolower($parsed['target_audience'] ?? 'all')
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to generate AI content. ' . $response->body()
        ], 500);

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("SEO AI Generate Error: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'SEO AI Generation Error. ' . $e->getMessage()
        ], 500);
    }
}
}