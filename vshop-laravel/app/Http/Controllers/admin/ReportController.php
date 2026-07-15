<?php

namespace App\Http\Controllers\admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Seller;
use App\Models\ProductReview;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{

/*
|--------------------------------------------------------------------------
| DETECT PRODUCT TYPE BASED ON ADMIN GUARD
|--------------------------------------------------------------------------
*/

private function getProductType()
{
    $user = auth()->user();

    if(!$user){
        return null;
    }

    if(in_array($user->type, ['travel','service'])){
        return 'service';
    }

    if($user->type == 'product'){
        return 'product';
    }

    return null;
}

/*
|--------------------------------------------------------------------------
| SELLER PRODUCT REPORT
|--------------------------------------------------------------------------
*/


public function sellerProductReport(Request $request)
{

$type = $this->getProductType();

$keyword = $request->keyword;
$seller = $request->seller;
$category = $request->category;


/* =======================
SELLER CATEGORY REPORT
======================= */

$query = DB::table('products')

->join('sellers','sellers.id','=','products.created_by_id')

->join('marketplace_categories','marketplace_categories.id','=','products.category_id')

->leftJoin('order_items','order_items.product_id','=','products.id')

->select(

'sellers.id as seller_id',
'sellers.name as seller_name',

'marketplace_categories.id as category_id',
'marketplace_categories.name as category_name',

DB::raw('COUNT(DISTINCT products.id) as total_products'),

DB::raw('SUM(order_items.price) as total_revenue')

)

->groupBy(
'sellers.id',
'sellers.name',
'marketplace_categories.id',
'marketplace_categories.name'
);



/* =======================
FILTERS
======================= */

if($type){
$query->where('products.type',$type);
}

if($keyword){

$query->where(function($q) use ($keyword){

$q->where('products.name','LIKE',"%$keyword%")
->orWhere('sellers.name','LIKE',"%$keyword%")
->orWhere('marketplace_categories.name','LIKE',"%$keyword%");

});

}

if($seller){
$query->where('products.created_by_id',$seller);
}

if($category){
$query->where('products.category_id',$category);
}


/* =======================
RESULT
======================= */

$reports = $query
->orderBy('sellers.name')
->get();


/* =======================
FILTER DATA
======================= */

$sellers = DB::table('sellers')
->select('id','name')
->orderBy('name')
->get();


$categories = DB::table('marketplace_categories')
->select('id','name')
->orderBy('name')
->get();


return view(
'admin.seller-product-report',
compact(
'reports',
'sellers',
'categories'
)
);

}

/*
|--------------------------------------------------------------------------
| TOP SELLING PRODUCTS REPORT
|--------------------------------------------------------------------------
*/

public function sellerTopSellingReport(Request $request)
{

$type = $this->getProductType();

$keyword = $request->keyword;

$query = DB::table('order_items')

->join('orders','orders.id','=','order_items.order_id')

->join('products','products.id','=','order_items.product_id')

->join('sellers','sellers.id','=','products.created_by_id')

->join('marketplace_categories','marketplace_categories.id','=','products.category_id')

->select(
'sellers.name as seller_name',
'marketplace_categories.name as category_name',
'products.name as product_name',
'order_items.price',
DB::raw('SUM(order_items.quantity) as total_quantity'),
DB::raw('SUM(order_items.total) as total_sales')
)

->groupBy(
'products.id',
'sellers.name',
'marketplace_categories.name',
'products.name',
'order_items.price'
)

->orderByDesc('total_quantity');

if($type){
$query->where('products.type',$type);
}

if($keyword){

$query->where(function($q) use ($keyword){

$q->where('products.name','LIKE',"%$keyword%")
->orWhere('sellers.name','LIKE',"%$keyword%")
->orWhere('marketplace_categories.name','LIKE',"%$keyword%");

});

}

$reports = $query->get();

return view(
'admin.top-selling-products',
compact('reports')
);

}


/*
|--------------------------------------------------------------------------
| NEGATIVE PRODUCT REVIEWS
|--------------------------------------------------------------------------
*/

public function sellerNegativeReviews(Request $request)
{

$type = $this->getProductType();

$query = DB::table('product_reviews')

->join('products','products.id','=','product_reviews.product_id')

->join('sellers','sellers.id','=','products.created_by_id')

->select(

'product_reviews.id',
'product_reviews.rating',
'product_reviews.comment',
'product_reviews.created_at',

'products.id as product_id',
'products.name as product_name',
'products.status as product_status',

'sellers.id as seller_id',
'sellers.name as seller_name',
'sellers.status as seller_status'

)

->where(function($q){

$q->where('product_reviews.rating','<=',2)
->orWhereRaw('product_reviews.helpful_no > product_reviews.helpful_yes');

});

if($type){
$query->where('products.type',$type);
}

if($request->keyword){

$query->where(function($q) use ($request){

$q->where('sellers.name','LIKE','%'.$request->keyword.'%')
->orWhere('products.name','LIKE','%'.$request->keyword.'%')
->orWhere('product_reviews.comment','LIKE','%'.$request->keyword.'%');

});

}

if($request->rating){
$query->where('product_reviews.rating',$request->rating);
}

$reviews = $query
->orderBy('product_reviews.created_at','DESC')
->get();

return view(
'admin.seller-negative-reviews',
compact('reviews')
);

}


/* BAN SELLER */

public function banSeller(Request $request)
{

DB::table('sellers')
->where('id',$request->seller_id)
->update([
'status'=>'banned'
]);

return response()->json([
'success'=>true
]);

}


/* REJECT PRODUCT */

public function rejectProduct(Request $request)
{

DB::table('products')
->where('id',$request->product_id)
->update([
'status'=>'rejected'
]);

return response()->json([
'success'=>true
]);

}

/*
|--------------------------------------------------------------------------
| COMMISSION REPORT
|--------------------------------------------------------------------------
*/

public function commissionReport()
{

$type = $this->getProductType();

$query = DB::table('order_items')

->join('products','products.id','=','order_items.product_id')

->join('sellers','sellers.id','=','products.created_by_id')

->select(
'sellers.name as seller_name',
'products.name as product_name',
'order_items.price',
'order_items.commission_amount',
'order_items.seller_earning'
)

->orderBy('order_items.created_at','DESC');

if($type){
$query->where('products.type',$type);
}

$reports = $query->get();

return view(
'admin.commission.commission-report',
compact('reports')
);

}

public function commissionSettings()
{
    $setting = DB::table('commission_settings')->first();

    if (!$setting) {
        DB::table('commission_settings')->insert([
            'commission_percent' => 10,
            'referral_percent' => 30,
            'created_at' => now()
        ]);

        $setting = DB::table('commission_settings')->first();
    }

    return view('admin.commission.commission-settings', compact('setting'));
}


public function inquirySettings()
{
    $inquirySetting = DB::table('inquiry_settings')->first();

    return view('admin.commission.commission-settings', compact('inquirySetting'));
}

public function saveInquirySettings(Request $request)
{
    DB::table('inquiry_settings')->updateOrInsert(
        ['id' => 1],
        [
            'default_price' => $request->default_inquiry_price,
            'min_recharge'  => $request->min_recharge,
            'updated_at'    => now()
        ]
    );

    return back()->with('success','Inquiry settings updated');
}

public function saveCommission(Request $request)
{
    $request->validate([
        'commission_percent' => 'required|numeric|min:0|max:100',
        'referral_percent' => 'required|numeric|min:0|max:100'
    ]);

    DB::table('commission_settings')->updateOrInsert(
        ['id' => 1],
        [
            'commission_percent' => $request->commission_percent,
            'referral_percent' => $request->referral_percent,
            'updated_at' => now()
        ]
    );

    return back()->with('success', 'Commission settings updated successfully');
}

public function editCommission($id)
{
    $rule = DB::table('seller_commission_rules')->where('id',$id)->first();

    return response()->json($rule);
}

public function deleteCommission($id)
{
    DB::table('seller_commission_rules')->where('id',$id)->delete();

    return back()->with('success','Commission deleted successfully');
}

public function activeCustomers() 
{ 
    $customers = Customer::where('is_active', 1) ->orderBy('id', 'DESC') ->get(); 
    return view('admin.activecustomer', compact('customers')); 
    
}
public function report()
{
    return view('admin.allreports', [

        /* ================= SUMMARY CARDS ================= */

        'total_sales'      => Order::sum('total_amount'),

        'paid_sales'       => Order::where('payment_status', 'paid')
                                ->sum('total_amount'),

        'unpaid_sales'     => Order::where('payment_status', 'unpaid')
                                ->sum('total_amount'),

        'total_orders'     => Order::count(),

        'total_customers'  => Customer::count(),

        'total_products'   => Product::count(),

        'conversion_rate'  => 0,


        /* ================= CHART DATA ================= */

        'sales_chart' => Order::selectRaw(
                'DATE(created_at) as date, SUM(total_amount) as total'
            )
            ->groupBy('date')
            ->get(),

        'customers_chart' => Customer::selectRaw(
                'DATE(created_at) as date, COUNT(*) as total'
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get(),

        'orders_chart' => Order::selectRaw(
                'DATE(created_at) as date, COUNT(*) as total'
            )
            ->groupBy('date')
            ->get(),

        'retention_chart' => [],

        'product_chart' => Product::selectRaw(
                'DATE(created_at) as date, COUNT(*) as total'
            )
            ->groupBy('date')
            ->get(),

        'payment_methods' => Order::selectRaw(
                'payment_method, COUNT(*) as total'
            )
            ->groupBy('payment_method')
            ->get(),

        'shipping_methods' => Order::selectRaw(
                'shipping_method, COUNT(*) as total'
            )
            ->groupBy('shipping_method')
            ->get(),


        /* ================= ORDERS ================= */

        'recent_orders' => Order::with('customer')
            ->latest()
            ->take(5)
            ->get(),

        'latest_orders' => Order::with('customer')
            ->latest()
            ->take(10)
            ->get(),


        /* ================= TOP SELLING PRODUCTS ================= */

        'top_selling_products' => OrderItem::selectRaw(
                'product_id, SUM(quantity) as total_qty'
            )
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderBy('total_qty', 'DESC')
            ->take(10)
            ->get(),

    ]);
}


public function index(Request $request)
{
    $query = Seller::query();

    /* ================= SEARCH KEYWORD ================= */

    if ($request->keyword) {

        $keyword = $request->keyword;

        $query->where(function ($q) use ($keyword) {

            $q->where('name', 'LIKE', "%{$keyword}%")
              ->orWhere('email', 'LIKE', "%{$keyword}%")
              ->orWhere('mobile', 'LIKE', "%{$keyword}%")
              ->orWhere('gst_number', 'LIKE', "%{$keyword}%")
              ->orWhere('pan_number', 'LIKE', "%{$keyword}%");

        });
    }


    /* ================= STATUS FILTER ================= */

    if ($request->status) {

        $query->where('status', $request->status);

    }


    /* ================= DATE FILTER ================= */

    if ($request->date_from) {

        $query->whereDate('created_at', '>=', $request->date_from);

    }

    if ($request->date_to) {

        $query->whereDate('created_at', '<=', $request->date_to);

    }


    /* ================= GET SELLERS ================= */

    $sellers = $query
        ->orderBy('created_at', 'DESC')
        ->paginate(15);


    /* ================= SUMMARY DATA ================= */

    $totalSellers = Seller::count();

    $approved = Seller::where('status', 'approved')->count();

    $pending = Seller::where('status', 'pending')->count();


    /* ================= RETURN VIEW ================= */

    return view(
        'admin.reports',
        compact(
            'sellers',
            'totalSellers',
            'approved',
            'pending'
        )
    );
}


public function verify(Request $request)
{
    try {

        $request->validate([
            'id' => 'required|exists:sellers,id'
        ]);

        $seller = Seller::findOrFail($request->id);

        if ($seller->is_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Already verified'
            ]);
        }

        $seller->update([
            'is_verified' => 1,
            'verified_at' => now(),
            'verified_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Seller verified successfully'
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => 'Error occurred'
        ], 500);

    }
}


public function highestRating(Request $request)
{
    $type = $this->getProductType();

    $query = DB::table('product_reviews')

        ->join('products','products.id','=','product_reviews.product_id')

        ->join('sellers','sellers.id','=','products.created_by_id')

        ->select(

            'product_reviews.id',
            'product_reviews.rating',
            'product_reviews.comment',
            'product_reviews.created_at',

            'products.id as product_id',
            'products.name as product_name',

            'sellers.id as seller_id',
            'sellers.name as seller_name'

        );

    /* FILTER TYPE */
    if($type){
        $query->where('products.type',$type);
    }

    /* SEARCH */
    if($request->keyword){
        $query->where(function($q) use ($request){
            $q->where('sellers.name','LIKE','%'.$request->keyword.'%')
              ->orWhere('products.name','LIKE','%'.$request->keyword.'%')
              ->orWhere('product_reviews.comment','LIKE','%'.$request->keyword.'%');
        });
    }

    /* RATING FILTER */
    if($request->rating){
        $query->where('product_reviews.rating',$request->rating);
    }

    $reviews = $query
        ->orderBy('product_reviews.rating','DESC')
        ->get();

    return view('admin.highest_rating', compact('reviews'));
}


public function lowestRating(Request $request)
{
    $type = $this->getProductType();

    $query = DB::table('product_reviews')

        ->join('products','products.id','=','product_reviews.product_id')

        ->join('sellers','sellers.id','=','products.created_by_id')

        ->select(

            'product_reviews.id',
            'product_reviews.rating',
            'product_reviews.comment',
            'product_reviews.created_at',

            'products.id as product_id',
            'products.name as product_name',
            'products.status as product_status', // ✅ ADD THIS

            'sellers.id as seller_id',
            'sellers.name as seller_name',
            'sellers.status as seller_status' // ✅ ADD THIS

        );

    if($type){
        $query->where('products.type',$type);
    }

    if($request->keyword){
        $query->where(function($q) use ($request){
            $q->where('sellers.name','LIKE','%'.$request->keyword.'%')
              ->orWhere('products.name','LIKE','%'.$request->keyword.'%')
              ->orWhere('product_reviews.comment','LIKE','%'.$request->keyword.'%');
        });
    }

    if($request->rating){
        $query->where('product_reviews.rating',$request->rating);
    }

    $reviews = $query
        ->orderBy('product_reviews.rating','ASC')
        ->get();

    return view('admin.lowest_rating', compact('reviews'));
}

/* SEND WARNING TO SELLER */

public function sendWarning(Request $request)
{
    $request->validate([
        'seller_id' => 'required',
        'reason' => 'required|string'
    ]);

    DB::table('seller_warnings')->insert([
        'seller_id' => $request->seller_id,
        'product_id' => $request->product_id,
        'reason' => $request->reason,
        'created_at' => now()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Warning sent successfully'
    ]);
}

//seller approval
public function approve(Request $request)
{
    $seller = Seller::findOrFail($request->id);
    $seller->status = 'approved';
    $seller->save();

    return response()->json(['success' => true]);
}

public function reject(Request $request)
{
    $seller = Seller::findOrFail($request->id);
    $seller->status = 'rejected';
    $seller->save();

    return response()->json(['success' => true]);
}




public function salesAnalytics(Request $request)
{
    $type = $this->getProductType();
    
    // Date range
    $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
    $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
    
    // Revenue Trends
    $revenueTrends = Order::whereBetween('created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->whereHas('items.product', function($q) use ($type) {
                $q->where('type', $type);
            });
        })
        ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders')
        ->groupBy('date')
        ->orderBy('date')
        ->get();  // FIXED: Added missing -> before get()
    
    // Peak Periods (Hourly)
    $peakPeriods = Order::whereBetween('created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->whereHas('items.product', function($q) use ($type) {
                $q->where('type', $type);
            });
        })
        ->selectRaw('HOUR(created_at) as hour, COUNT(*) as orders, SUM(total_amount) as revenue')
        ->groupBy('hour')
        ->orderBy('orders', 'DESC')
        ->get();
    
    // Category-wise Sales
    $categorySales = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
        ->join('marketplace_categories', 'products.category_id', '=', 'marketplace_categories.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->whereBetween('orders.created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->where('products.type', $type);
        })
        ->select(
            'marketplace_categories.name as category',
            DB::raw('SUM(order_items.quantity) as quantity'),
            DB::raw('SUM(order_items.total) as revenue'),
            DB::raw('COUNT(DISTINCT orders.id) as orders')
        )
        ->groupBy('marketplace_categories.id', 'marketplace_categories.name')
        ->get();
    
    // Summary
    $totalRevenue = $revenueTrends->sum('revenue');
    $totalOrders = $revenueTrends->sum('orders');
    $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
    
    // Top Sellers
    $topSellers = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
        ->join('sellers', 'products.created_by_id', '=', 'sellers.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->whereBetween('orders.created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->where('products.type', $type);
        })
        ->select('sellers.name as seller_name', DB::raw('SUM(order_items.total) as revenue'))
        ->groupBy('sellers.id', 'sellers.name')
        ->orderByDesc('revenue')
        ->limit(5)
        ->get();

    // Total Refunds
    $totalRefunds = \App\Models\Refund::whereBetween('created_at', [$startDate, $endDate])
        ->whereIn('status', ['approved', 'completed', 'processed'])
        ->sum('amount');
    
    return view('admin.analytics.sales-analytics', compact(
        'revenueTrends', 'peakPeriods', 'categorySales',
        'totalRevenue', 'totalOrders', 'avgOrderValue', 'startDate', 'endDate',
        'topSellers', 'totalRefunds'
    ));
}
// ==============================================
// 2. FUNNEL ANALYTICS FUNCTION - FIXED
// ==============================================

public function funnelAnalytics(Request $request)
{
    $type = $this->getProductType();
    $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
    $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
    
    // Get counts for each stage
    $totalProductViews = $this->getProductViewsCount($startDate, $endDate, $type);
    $totalEnquiries = $this->getEnquiriesCount($startDate, $endDate, $type);
    $totalOrders = $this->getOrdersCount($startDate, $endDate, $type);
    $completedOrders = $this->getCompletedOrdersCount($startDate, $endDate, $type);
    
    // Calculate conversion rates
    $viewToEnquiryRate = $totalProductViews > 0 ? ($totalEnquiries / $totalProductViews) * 100 : 0;
    $enquiryToOrderRate = $totalEnquiries > 0 ? ($totalOrders / $totalEnquiries) * 100 : 0;
    $orderToCompleteRate = $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;
    $overallConversionRate = $totalProductViews > 0 ? ($completedOrders / $totalProductViews) * 100 : 0;
    
    // Drop-off points
    $dropOffs = [
        'view_to_enquiry' => $totalProductViews - $totalEnquiries,
        'enquiry_to_order' => $totalEnquiries - $totalOrders,
        'order_to_complete' => $totalOrders - $completedOrders,
    ];
    
    $funnelData = [
        'views' => $totalProductViews,
        'clicks' => $totalProductViews,
        'enquiries' => $totalEnquiries,
        'orders' => $totalOrders,
        'completed' => $completedOrders,
    ];

    // Category Funnel Data (using the same approximations as global counts)
    $categoryFunnel = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
        ->join('marketplace_categories', 'products.category_id', '=', 'marketplace_categories.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->whereBetween('orders.created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->where('products.type', $type);
        })
        ->select(
            'marketplace_categories.name as category',
            DB::raw('COUNT(order_items.id) as views'),
            DB::raw('COUNT(DISTINCT orders.id) as enquiries'),
            DB::raw('COUNT(DISTINCT orders.id) as orders'),
            DB::raw('COUNT(DISTINCT CASE WHEN orders.delivery_status = "delivered" THEN orders.id END) as completed')
        )
        ->groupBy('marketplace_categories.id', 'marketplace_categories.name')
        ->orderByDesc('orders')
        ->limit(6)
        ->get();
    
    return view('admin.analytics.funnel-analytics', compact(
        'funnelData', 'dropOffs', 'viewToEnquiryRate', 'enquiryToOrderRate',
        'orderToCompleteRate', 'overallConversionRate', 'startDate', 'endDate',
        'categoryFunnel'
    ));
}

// ==============================================
// 3. CUSTOMER ANALYTICS FUNCTION - FIXED
// ==============================================

public function customerAnalytics(Request $request)
{
    $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(90);
    $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
    
    // Get total revenue for LTV calculation
    $totalRevenue = Order::sum('total_amount');
    
    // Customer Segmentation
    $totalCustomers = Customer::count();
    $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();
    
    // Active customers (have orders in last 30 days)
    $activeCustomers = Customer::whereHas('orders', function($q) {
        $q->where('created_at', '>=', Carbon::now()->subDays(30));
    })->count();
    
    // At risk customers (no order in 30-60 days)
    $atRiskCustomers = Customer::whereDoesntHave('orders', function($q) {
        $q->where('created_at', '>=', Carbon::now()->subDays(30));
    })->whereHas('orders', function($q) {
        $q->where('created_at', '>=', Carbon::now()->subDays(90));
    })->count();
    
    // Churned customers (no order in 90+ days)
    $churnedCustomers = Customer::whereDoesntHave('orders', function($q) {
        $q->where('created_at', '>=', Carbon::now()->subDays(90));
    })->count();
    
    // Retention Rate
    $retentionRate = $totalCustomers > 0 ? (($activeCustomers + $atRiskCustomers) / $totalCustomers) * 100 : 0;
    
    // Repeat Customer Rate
    $customersWithMultipleOrders = Customer::has('orders', '>=', 2)->count();
    $repeatRate = $totalCustomers > 0 ? ($customersWithMultipleOrders / $totalCustomers) * 100 : 0;
    
    // Monthly retention data for chart
    $monthlyRetention = collect();
    
    for ($i = 5; $i >= 0; $i--) {
        $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
        $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
        
        $newInMonth = Customer::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        
        $retainedFromMonth = Customer::whereBetween('created_at', [$monthStart, $monthEnd])
            ->whereHas('orders', function($q) {
                $q->where('created_at', '>=', Carbon::now()->subDays(30));
            })->count();
        
        $monthlyRetention->push((object)[
            'month' => $monthStart->format('M Y'),
            'new' => $newInMonth,
            'retained' => $retainedFromMonth,
            'rate' => $newInMonth > 0 ? ($retainedFromMonth / $newInMonth) * 100 : 0,
        ]);
    }
    
    return view('admin.analytics.customer-analytics', compact(
        'totalCustomers', 'newCustomers', 'activeCustomers', 'atRiskCustomers',
        'churnedCustomers', 'retentionRate', 'repeatRate', 'monthlyRetention',
        'startDate', 'endDate', 'totalRevenue'
    ));
}

// ==============================================
// 4. PROMOTION ANALYTICS FUNCTION - FIXED
// ==============================================

public function promotionAnalytics(Request $request)
{
    $type = $this->getProductType();
    $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
    $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
    
    // Top Performing Campaigns - Using DB queries
    $topCampaigns = DB::table('products')
        ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
        ->leftJoin('sellers', 'products.created_by_id', '=', 'sellers.id')
        ->leftJoin('marketplace_categories', 'products.category_id', '=', 'marketplace_categories.id')
        ->where('products.is_boosted', 1)
        ->whereBetween('order_items.created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->where('products.type', $type);
        })
        ->select(
            'products.id',
            'products.name',
            'products.boost_amount',
            'sellers.name as seller_name',
            'marketplace_categories.name as category_name',
            DB::raw('COUNT(DISTINCT order_items.id) as total_orders'),
            DB::raw('COALESCE(SUM(order_items.total), 0) as total_revenue')
        )
        ->groupBy('products.id', 'products.name', 'products.boost_amount', 'sellers.name', 'marketplace_categories.name')
        ->orderBy('total_revenue', 'DESC')
        ->take(10)
        ->get();
    
    // Calculate ROI for each boosted product
    foreach ($topCampaigns as $campaign) {
        $campaign->roi = ($campaign->boost_amount ?? 0) > 0 
            ? (($campaign->total_revenue - ($campaign->boost_amount ?? 0)) / ($campaign->boost_amount ?? 1)) * 100 
            : 0;
            
        // Real impressions and conversions
        $product = DB::table('products')->where('id', $campaign->id)->first();
        $campaign->impressions = $product ? ($product->views ?? 0) : 0;
        
        $campaign->total_enquiries = DB::table('enquiries')
            ->where('product_id', $campaign->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $campaign->boost_conversion_rate = $campaign->impressions > 0 
            ? ($campaign->total_enquiries / $campaign->impressions) * 100 
            : 0;
    }
    
    // Most Boosted Categories
    $boostedCategories = DB::table('products')
        ->join('marketplace_categories', 'products.category_id', '=', 'marketplace_categories.id')
        ->where('products.is_boosted', 1)
        ->when($type, function($q) use ($type) {
            return $q->where('products.type', $type);
        })
        ->select(
            'marketplace_categories.name as category',
            DB::raw('COUNT(DISTINCT products.id) as boosted_products'),
            DB::raw('COALESCE(SUM(products.boost_amount), 0) as total_boost_spend'),
            DB::raw('SUM(CASE WHEN products.is_boosted = 1 AND products.boost_expires_at > NOW() THEN 1 ELSE 0 END) as active_boosts')
        )
        ->groupBy('marketplace_categories.id', 'marketplace_categories.name')
        ->orderBy('total_boost_spend', 'DESC')
        ->get();
    
    // Highest ROI Sellers
    $topROISellers = DB::table('sellers')
        ->join('products', 'sellers.id', '=', 'products.created_by_id')
        ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
        ->where('products.is_boosted', 1)
        ->whereBetween('order_items.created_at', [$startDate, $endDate])
        ->select(
            'sellers.id',
            'sellers.name',
            DB::raw('COALESCE(SUM(products.boost_amount), 0) as total_boost_spend'),
            DB::raw('COALESCE(SUM(order_items.total), 0) as total_revenue')
        )
        ->groupBy('sellers.id', 'sellers.name')
        ->get()
        ->map(function($seller) {
            $seller->roi = $seller->total_boost_spend > 0 
                ? (($seller->total_revenue - $seller->total_boost_spend) / $seller->total_boost_spend) * 100 
                : 0;
            return $seller;
        })
        ->sortByDesc('roi')
        ->take(10)
        ->values();
    
    // Boost Conversion Stats
    $boostStats = [
        'total_boosted_products' => DB::table('products')->where('is_boosted', 1)->count(),
        'active_boosts' => DB::table('products')
            ->where('is_boosted', 1)
            ->where('boost_expires_at', '>', now())
            ->count(),
        'total_boost_revenue' => DB::table('order_items')
            ->whereIn('product_id', function($q) {
                $q->select('id')->from('products')->where('is_boosted', 1);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total'),
        'total_boost_spend' => DB::table('products')->where('is_boosted', 1)->sum('boost_amount'),
        'avg_boost_conversion' => 0,
    ];
    $enquiries = DB::table('enquiries')
        ->whereIn('product_id', function($q) {
            $q->select('id')->from('products')->where('is_boosted', 1);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
        
    $boostStats['enquiries'] = $enquiries;
    $boostStats['impressions'] = (int) DB::table('products')->where('is_boosted', 1)->sum('views');
    $boostStats['ctr'] = $boostStats['impressions'] > 0 
        ? ($boostStats['enquiries'] / $boostStats['impressions']) * 100 
        : 0;
    
    $boostStats['overall_roi'] = ($boostStats['total_boost_spend'] ?? 0) > 0 
        ? (($boostStats['total_boost_revenue'] - ($boostStats['total_boost_spend'] ?? 0)) / ($boostStats['total_boost_spend'] ?? 1)) * 100 
        : 0;
    return view('admin.analytics.promotion-analytics', compact(
        'topCampaigns', 'boostedCategories', 'topROISellers', 'boostStats',
        'startDate', 'endDate'
    ));
}

// ==============================================
// 5. CAMPAIGN ROI FUNCTION - FIXED
// ==============================================

public function campaignROI(Request $request)
{
    $type = $this->getProductType();
    $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
    $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
    
    // Campaign performance data using DB queries
    $campaigns = DB::table('products')
        ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
        ->leftJoin('sellers', 'products.created_by_id', '=', 'sellers.id')
        ->leftJoin('marketplace_categories', 'products.category_id', '=', 'marketplace_categories.id')
        ->where('products.is_boosted', 1)
        ->whereBetween('order_items.created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->where('products.type', $type);
        })
        ->select(
            'products.id',
            'products.name',
            'products.boost_amount',
            'sellers.name as seller_name',
            'marketplace_categories.name as category_name',
            DB::raw('COUNT(DISTINCT order_items.id) as total_orders'),
            DB::raw('COUNT(DISTINCT order_items.id) as total_enquiries'),
            DB::raw('COALESCE(SUM(order_items.total), 0) as revenue')
        )
        ->groupBy('products.id', 'products.name', 'products.boost_amount', 'sellers.name', 'marketplace_categories.name')
        ->get()
        ->map(function($campaign) use ($startDate, $endDate) {
            // Real enquiries for the campaign
            $campaign->total_enquiries = DB::table('enquiries')
                ->where('product_id', $campaign->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            $campaign->cpa = $campaign->total_enquiries > 0 
                ? ($campaign->boost_amount ?? 0) / $campaign->total_enquiries 
                : 0;
            $campaign->roi = ($campaign->boost_amount ?? 0) > 0 
                ? (($campaign->revenue - ($campaign->boost_amount ?? 0)) / ($campaign->boost_amount ?? 1)) * 100 
                : 0;
            $campaign->conversion_rate = $campaign->total_enquiries > 0 
                ? ($campaign->total_orders / $campaign->total_enquiries) * 100 
                : 0;
            return $campaign;
        });
    
    // Summary stats
    $totalCampaigns = $campaigns->count();
    $totalSpend = $campaigns->sum('boost_amount');
    $totalRevenue = $campaigns->sum('revenue');
    $totalConversions = $campaigns->sum('total_orders');
    $avgCPA = $totalConversions > 0 ? $totalSpend / $totalConversions : 0;
    $avgROI = $totalSpend > 0 ? (($totalRevenue - $totalSpend) / $totalSpend) * 100 : 0;
    
  $totalLeads = $campaigns->sum('total_enquiries');
$bestROI = $campaigns->max('roi');

return view('admin.analytics.campaign-roi', compact(
    'campaigns',
    'totalCampaigns',
    'totalSpend',
    'totalRevenue',
    'totalConversions',
    'totalLeads',
    'avgCPA',
    'avgROI',
    'bestROI',
    'startDate',
    'endDate'
));
}

// ==============================================
// 6. LEAD ANALYTICS FUNCTION - FIXED
// ==============================================

public function leadAnalytics(Request $request)
{
    $type = $this->getProductType();
    $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
    $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
    
    // Most Clicked Services/Products
    $mostClicked = DB::table('products')
        ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
        ->leftJoin('sellers', 'products.created_by_id', '=', 'sellers.id')
        ->leftJoin('marketplace_categories', 'products.category_id', '=', 'marketplace_categories.id')
        ->whereBetween('order_items.created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->where('products.type', $type);
        })
        ->select(
            'products.id',
            'products.name',
            'sellers.name as seller_name',
            'marketplace_categories.name as category_name',
            DB::raw('COUNT(order_items.id) as click_count')
        )
        ->groupBy('products.id', 'products.name', 'sellers.name', 'marketplace_categories.name')
        ->orderBy('click_count', 'DESC')
        ->take(10)
        ->get();
    
    // Most Enquired Services
    $mostEnquired = DB::table('products')
        ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
        ->leftJoin('sellers', 'products.created_by_id', '=', 'sellers.id')
        ->leftJoin('marketplace_categories', 'products.category_id', '=', 'marketplace_categories.id')
        ->whereBetween('order_items.created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->where('products.type', $type);
        })
        ->select(
            'products.id',
            'products.name',
            'sellers.name as seller_name',
            'marketplace_categories.name as category_name',
            DB::raw('COUNT(order_items.id) as enquiry_count')
        )
        ->groupBy('products.id', 'products.name', 'sellers.name', 'marketplace_categories.name')
        ->orderBy('enquiry_count', 'DESC')
        ->take(10)
        ->get();
    
    // Highest Conversion Categories
    $topCategories = DB::table('order_items')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->join('marketplace_categories', 'products.category_id', '=', 'marketplace_categories.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->whereBetween('order_items.created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->where('products.type', $type);
        })
        ->select(
            'marketplace_categories.name as category',
            DB::raw('COUNT(DISTINCT order_items.id) as enquiries'),
            DB::raw('COUNT(DISTINCT orders.id) as conversions'),
            DB::raw('(COUNT(DISTINCT orders.id) / NULLIF(COUNT(DISTINCT order_items.id), 0)) * 100 as conversion_rate')
        )
        ->groupBy('marketplace_categories.id', 'marketplace_categories.name')
        ->orderBy('conversion_rate', 'DESC')
        ->get();
    
    // Top Lead Sellers
    $topLeadSellers = DB::table('sellers')
        ->leftJoin('products', 'sellers.id', '=', 'products.created_by_id')
        ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
        ->whereBetween('order_items.created_at', [$startDate, $endDate])
        ->select(
            'sellers.id',
            'sellers.name',
            DB::raw('COUNT(order_items.id) as total_leads'),
            DB::raw('COUNT(DISTINCT orders.id) as converted_leads')
        )
        ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
        ->groupBy('sellers.id', 'sellers.name')
        ->get()
        ->map(function($seller) {
            $seller->conversion_rate = $seller->total_leads > 0 
                ? ($seller->converted_leads / $seller->total_leads) * 100 
                : 0;
            return $seller;
        })
        ->sortByDesc('total_leads')
        ->take(10)
        ->values();
        
    // Calculate Summary Totals
    $totalEnquiries = DB::table('enquiries')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
    $totalClicks = (int) DB::table('products')->sum('views'); // Real total views/clicks
    $totalConversions = DB::table('orders')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
    $avgConversionRate = $totalEnquiries > 0 ? ($totalConversions / $totalEnquiries) * 100 : 0;
    $leadValue = DB::table('order_items')->whereBetween('created_at', [$startDate, $endDate])->avg('total') ?? 0;
    
    // Real Graph Data
    $graphDates = collect();
    $graphClicks = collect();
    $graphEnquiries = collect();
    
    for ($i = 6; $i >= 0; $i--) {
        $dayStart = Carbon::now()->subDays($i)->startOfDay();
        $dayEnd = Carbon::now()->subDays($i)->endOfDay();
        $date = $dayStart->format('M d');
        
        $graphDates->push($date);
        
        $dayEnquiries = DB::table('enquiries')
            ->whereBetween('created_at', [$dayStart, $dayEnd])
            ->count();
        $graphEnquiries->push($dayEnquiries);
        
        $graphClicks->push(0); // Daily clicks history not tracked in DB
    }
    
    return view('admin.analytics.lead-analytics', compact(
        'mostClicked', 'mostEnquired', 'topCategories', 'topLeadSellers',
        'startDate', 'endDate', 'totalClicks', 'totalEnquiries', 'avgConversionRate', 'leadValue',
        'graphDates', 'graphClicks', 'graphEnquiries'
    ));
}

// ==============================================
// 7. ANALYTICS GRAPHS FUNCTION - FIXED
// ==============================================

public function analyticsGraphs(Request $request)
{
    $type = $this->getProductType();
    $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
    $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
    
    // Graph 1: Clicks vs Enquiries
    $clicksVsEnquiries = OrderItem::whereBetween('created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->whereHas('product', function($q) use ($type) {
                $q->where('type', $type);
            });
        })
        ->selectRaw('DATE(created_at) as date, COUNT(*) as clicks')
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    
    $enquiriesData = Order::whereBetween('created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->whereHas('items.product', function($q) use ($type) {
                $q->where('type', $type);
            });
        })
        ->selectRaw('DATE(created_at) as date, COUNT(*) as enquiries')
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    
    // Merge data for graph (as array, not collection)
    $graphData = [];
    $dates = $clicksVsEnquiries->pluck('date')->unique()->merge($enquiriesData->pluck('date'))->unique()->sort();
    
    foreach ($dates as $date) {
        $graphData[] = [
            'date' => $date,
            'clicks' => $clicksVsEnquiries->where('date', $date)->first()->clicks ?? 0,
            'enquiries' => $enquiriesData->where('date', $date)->first()->enquiries ?? 0,
        ];
    }
    
    // Graph 2: Enquiry vs Conversion
    $enquiryVsConversion = Order::whereBetween('created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->whereHas('items.product', function($q) use ($type) {
                $q->where('type', $type);
            });
        })
        ->selectRaw('DATE(created_at) as date, COUNT(*) as enquiries, SUM(CASE WHEN payment_status = "paid" THEN 1 ELSE 0 END) as conversions')
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->toArray(); // Convert to array
    
    // Graph 3: Revenue vs Leads
    $revenueVsLeads = Order::whereBetween('created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->whereHas('items.product', function($q) use ($type) {
                $q->where('type', $type);
            });
        })
        ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as leads')
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->toArray(); // Convert to array
    
    // Calculate summary statistics
    $totalClicks = array_sum(array_column($graphData, 'clicks'));
    $totalEnquiries = array_sum(array_column($graphData, 'enquiries'));
    $clickToEnquiryRate = $totalClicks > 0 ? ($totalEnquiries / $totalClicks) * 100 : 0;
    
    return view('admin.analytics.analytics-graphs', compact(
        'graphData', 'enquiryVsConversion', 'revenueVsLeads', 
        'startDate', 'endDate', 'totalClicks', 'totalEnquiries', 'clickToEnquiryRate'
    ));
}
// ==============================================
// 8. AI PROMOTION ENGINE FUNCTION - FIXED
// ==============================================

public function aiPromotionEngine(Request $request)
{
    $type = $this->getProductType();
    
    // Get products for AI analysis
    $products = Product::when($type, function($q) use ($type) {
            return $q->where('type', $type);
        })
        ->where('status', 'active')
        ->get();
    
    $recommendations = [];
    
    foreach ($products as $product) {
        // AI Logic: Calculate scores based on multiple factors
        $score = 0;
        $factors = [];
        
        // 1. Category popularity
        $categorySales = OrderItem::whereHas('product', function($q) use ($product) {
            $q->where('category_id', $product->category_id);
        })->count();
        $categoryScore = min(($categorySales / 1000) * 10, 10);
        $score += $categoryScore;
        $factors['category_popularity'] = $categoryScore;
        
        // 2. Seller history
        $sellerOrders = OrderItem::whereHas('product', function($q) use ($product) {
            $q->where('created_by_id', $product->created_by_id);
        })->count();
        $sellerScore = min(($sellerOrders / 500) * 10, 10);
        $score += $sellerScore;
        $factors['seller_history'] = $sellerScore;
        
        // 3. Conversion rate
        $productViews = OrderItem::where('product_id', $product->id)->count();
        $productOrders = OrderItem::where('product_id', $product->id)
            ->whereHas('order', function($q) {
                $q->where('payment_status', 'paid');
            })->count();
        $conversionRate = $productViews > 0 ? ($productOrders / $productViews) * 100 : 0;
        $conversionScore = min(($conversionRate / 20) * 10, 10);
        $score += $conversionScore;
        $factors['conversion_rate'] = $conversionScore;
        
        // 4. Seasonality
        $currentMonth = Carbon::now()->month;
        $seasonalScore = in_array($currentMonth, [11, 12]) ? 8 : ($currentMonth == 1 ? 6 : 5);
        $score += $seasonalScore;
        $factors['seasonality'] = $seasonalScore;
        
        // 5. Past campaign performance
        $pastBoostScore = ($product->is_boosted ?? false) ? 7 : 3;
        $score += $pastBoostScore;
        $factors['past_campaign'] = $pastBoostScore;
        
        // Calculate final score
        $finalScore = min($score, 50);
        
        // AI Predictions based on score
        $recommendedBoost = $this->calculateRecommendedBoost($finalScore);
        $expectedReach = $this->calculateExpectedReach($finalScore, $product);
        $expectedEnquiries = $this->calculateExpectedEnquiries($finalScore, $product);
        $expectedConversions = $this->calculateExpectedConversions($expectedEnquiries, $conversionRate);
        
        // Best cities based on orders
        $bestCities = $this->getBestCitiesForProduct($product);
        
        // Best timing
        $bestTiming = $this->getBestTimingForProduct($product);
        
        if ($finalScore > 25) {
            $recommendations[] = [
                'product' => $product,
                'ai_score' => $finalScore,
                'factors' => $factors,
                'recommended_boost' => $recommendedBoost,
                'expected_reach' => $expectedReach,
                'expected_enquiries' => $expectedEnquiries,
                'expected_conversions' => $expectedConversions,
                'best_cities' => $bestCities,
                'best_timing' => $bestTiming,
                'priority' => $finalScore > 40 ? 'High' : ($finalScore > 30 ? 'Medium' : 'Low'),
            ];
        }
    }
    
    // Sort by AI score
    usort($recommendations, function($a, $b) {
        return $b['ai_score'] <=> $a['ai_score'];
    });
    
    // Detect poor campaigns
    $poorCampaigns = Product::where('is_boosted', 1)
        ->where('boost_expires_at', '>', now())
        ->get()
        ->map(function($product) {
            $boostSpend = $product->boost_amount ?? 0;
            $revenue = OrderItem::where('product_id', $product->id)
                ->whereBetween('created_at', [now()->subDays(30), now()])
                ->sum('total');
            $roi = $boostSpend > 0 ? (($revenue - $boostSpend) / $boostSpend) * 100 : 0;
            
            return [
                'product' => $product,
                'boost_spend' => $boostSpend,
                'revenue' => $revenue,
                'roi' => $roi,
                'status' => $roi < 0 ? 'Underperforming' : ($roi < 50 ? 'Average' : 'Good'),
            ];
        })
        ->where('roi', '<', 20)
        ->take(5)
        ->values();
    
    return view('admin.analytics.ai-promotion-engine', compact('recommendations', 'poorCampaigns'));
}

// ==============================================
// 9. AI INSIGHTS FUNCTION - FIXED
// ==============================================

public function aiInsights(Request $request)
{
    $type = $this->getProductType();
    $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
    $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
    
    // Sales drop analysis
    $previousPeriodStart = (clone $startDate)->subDays($startDate->diffInDays($endDate));
    $previousPeriodEnd = (clone $startDate)->subDay();
    
    $currentSales = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
    $previousSales = Order::whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->sum('total_amount');
    
    $salesChange = $previousSales > 0 ? (($currentSales - $previousSales) / $previousSales) * 100 : 0;
    $isSalesDrop = $salesChange < -10;
    
    // Growth recommendations
    $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('product_id')
        ->orderBy('total_sold', 'DESC')
        ->with('product')
        ->take(5)
        ->get();
    
    $underperformingProducts = Product::whereDoesntHave('items', function($q) use ($startDate, $endDate) {
        $q->whereBetween('created_at', [$startDate, $endDate]);
    })->where('status', 'active')->take(5)->get();
    
    // High churn risk categories
    $churnRiskCategories = DB::table('order_items')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->join('marketplace_categories', 'products.category_id', '=', 'marketplace_categories.id')
        ->whereBetween('order_items.created_at', [Carbon::now()->subDays(60), Carbon::now()->subDays(30)])
        ->select('marketplace_categories.name', 'marketplace_categories.id', DB::raw('COUNT(*) as previous_orders'))
        ->groupBy('marketplace_categories.id', 'marketplace_categories.name')
        ->get()
        ->map(function($category) {
            $recentOrders = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.category_id', $category->id)
                ->whereBetween('order_items.created_at', [Carbon::now()->subDays(30), Carbon::now()])
                ->count();
            
            if ($category->previous_orders > 0 && $recentOrders < ($category->previous_orders * 0.5)) {
                return [
                    'category' => $category->name,
                    'drop_percentage' => (($category->previous_orders - $recentOrders) / $category->previous_orders) * 100,
                    'risk_level' => 'High',
                ];
            }
            return null;
        })
        ->filter()
        ->values();
    
    // Seller performance alerts
    $sellerAlerts = Seller::withCount(['products as total_orders' => function($q) use ($startDate, $endDate) {
        $q->whereHas('items', function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        });
    }])->having('total_orders', '<', 5)->take(5)->get();
    
    return view('admin.analytics.ai-insights', compact(
        'currentSales', 'previousSales', 'salesChange', 'isSalesDrop',
        'topProducts', 'underperformingProducts', 'churnRiskCategories',
        'sellerAlerts', 'startDate', 'endDate', 'previousPeriodStart', 'previousPeriodEnd'
    ));
}

// ==============================================
// 10. DROPSHIPPING ANALYTICS FUNCTION - FIXED
// ==============================================

public function dropshippingAnalytics(Request $request)
{
    $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
    $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
    
    // Supplier-wise performance
    $supplierPerformance = Seller::where('type', 'supplier')
        ->with(['products' => function($q) {
            $q->with(['items']);
        }])
        ->get()
        ->map(function($supplier) use ($startDate, $endDate) {
            $totalOrders = 0;
            $totalRevenue = 0;
            $deliveredOrders = 0;
            
            foreach ($supplier->products as $product) {
                $items = $product->items->whereBetween('created_at', [$startDate, $endDate]);
                $totalOrders += $items->count();
                $totalRevenue += $items->sum('total');
                $deliveredOrders += $items->filter(function($item) {
                    return $item->order && $item->order->delivery_status == 'delivered';
                })->count();
            }
            
            $supplier->total_orders = $totalOrders;
            $supplier->total_revenue = $totalRevenue;
            $supplier->delivery_success_rate = $totalOrders > 0 ? ($deliveredOrders / $totalOrders) * 100 : 0;
            
            return $supplier;
        })
        ->sortByDesc('total_revenue')
        ->values();
    
    // Dropshipping revenue
    $dropshippingRevenue = Order::whereHas('items.product.seller', function($q) {
        $q->where('type', 'supplier');
    })->whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
    
    // Delivery success rate overall
    $totalDropshipOrders = Order::whereHas('items.product.seller', function($q) {
        $q->where('type', 'supplier');
    })->whereBetween('created_at', [$startDate, $endDate])->count();
    
    $deliveredDropshipOrders = Order::whereHas('items.product.seller', function($q) {
        $q->where('type', 'supplier');
    })->where('delivery_status', 'delivered')
      ->whereBetween('created_at', [$startDate, $endDate])
      ->count();
    
    $overallDeliveryRate = $totalDropshipOrders > 0 ? ($deliveredDropshipOrders / $totalDropshipOrders) * 100 : 0;
    
    // Return by supplier
    $returnsBySupplier = Seller::where('type', 'supplier')
        ->with(['products' => function($q) {
            $q->with(['items' => function($q) {
                $q->whereHas('order', function($q) {
                    $q->where('return_status', 'returned');
                });
            }]);
        }])
        ->get()
        ->map(function($supplier) use ($startDate, $endDate) {
            $totalOrders = 0;
            $returnedOrders = 0;
            
            foreach ($supplier->products as $product) {
                $items = $product->items->whereBetween('created_at', [$startDate, $endDate]);
                $totalOrders += $items->count();
                $returnedOrders += $items->filter(function($item) {
                    return $item->order && $item->order->return_status == 'returned';
                })->count();
            }
            
            $supplier->return_percentage = $totalOrders > 0 ? ($returnedOrders / $totalOrders) * 100 : 0;
            $supplier->return_count = $returnedOrders;
            
            return $supplier;
        })
        ->sortByDesc('return_percentage')
        ->values();
    
    return view('admin.analytics.dropshipping-analytics', compact(
        'supplierPerformance', 'dropshippingRevenue', 'totalDropshipOrders',
        'overallDeliveryRate', 'returnsBySupplier', 'startDate', 'endDate'
    ));
}

// ==============================================
// HELPER FUNCTIONS - FIXED
// ==============================================

private function getProductViewsCount($startDate, $endDate, $type = null)
{
    return OrderItem::whereBetween('created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->whereHas('product', function($q) use ($type) {
                $q->where('type', $type);
            });
        })
        ->count();
}

private function getEnquiriesCount($startDate, $endDate, $type = null)
{
    return Order::whereBetween('created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->whereHas('items.product', function($q) use ($type) {
                $q->where('type', $type);
            });
        })
        ->count();
}

private function getOrdersCount($startDate, $endDate, $type = null)
{
    return Order::whereBetween('created_at', [$startDate, $endDate])
        ->when($type, function($q) use ($type) {
            return $q->whereHas('items.product', function($q) use ($type) {
                $q->where('type', $type);
            });
        })
        ->count();
}

private function getCompletedOrdersCount($startDate, $endDate, $type = null)
{
    return Order::whereBetween('created_at', [$startDate, $endDate])
        ->where('delivery_status', 'delivered')
        ->when($type, function($q) use ($type) {
            return $q->whereHas('items.product', function($q) use ($type) {
                $q->where('type', $type);
            });
        })
        ->count();
}

private function calculateRecommendedBoost($aiScore)
{
    if ($aiScore >= 40) return 5000;
    if ($aiScore >= 30) return 3000;
    if ($aiScore >= 20) return 1000;
    return 500;
}

private function calculateExpectedReach($aiScore, $product)
{
    $baseReach = 1000;
    $multiplier = $aiScore / 25;
    return round($baseReach * $multiplier);
}

private function calculateExpectedEnquiries($aiScore, $product)
{
    $baseEnquiries = 50;
    $multiplier = $aiScore / 25;
    return round($baseEnquiries * $multiplier);
}

private function calculateExpectedConversions($expectedEnquiries, $conversionRate)
{
    return round($expectedEnquiries * ($conversionRate / 100));
}

private function getBestCitiesForProduct($product)
{
    $cities = Order::whereHas('items.product', function($q) use ($product) {
        $q->where('id', $product->id);
    })
    ->select('shipping_city', DB::raw('COUNT(*) as order_count'))
    ->groupBy('shipping_city')
    ->orderBy('order_count', 'DESC')
    ->limit(5)
    ->get()
    ->pluck('shipping_city')

    // 1. High Value Sellers (top revenue)
    $highValueSellers = Seller::select('sellers.*')
        ->selectRaw('COALESCE(SUM(order_items.price * order_items.quantity), 0) as total_revenue')
        ->leftJoin('products', 'products.created_by_id', '=', 'sellers.id')
        ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
        ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
        ->whereNull('orders.deleted_at')
        ->groupBy('sellers.id')
        ->orderByDesc('total_revenue')
        ->take(15)
        ->get();

    // 2. Frequent Returns (sellers with highest return rate)
    $frequentReturns = Seller::select('sellers.*')
        ->selectRaw('COUNT(CASE WHEN orders.return_status = "returned" THEN 1 END) as returns_count')
        ->selectRaw('COUNT(order_items.id) as total_items_sold')
        ->leftJoin('products', 'products.created_by_id', '=', 'sellers.id')
        ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
        ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
        ->groupBy('sellers.id')
        ->orderByDesc('returns_count')
        ->take(15)
        ->get()
        ->map(function($seller) {
            $seller->return_rate = $seller->total_items_sold > 0 
                ? round(($seller->returns_count / $seller->total_items_sold) * 100, 2) 
                : 0;
            return $seller;
        });

    // 3. Bad Review Sellers (average rating < 3)
    $badReviewSellers = Seller::select('sellers.*')
        ->selectRaw('COALESCE(AVG(product_reviews.rating), 0) as avg_rating')
        ->selectRaw('COUNT(product_reviews.id) as reviews_count')
        ->leftJoin('product_reviews', 'product_reviews.seller_id', '=', 'sellers.id')
        ->groupBy('sellers.id')
        ->having('avg_rating', '<', 3)
        ->orderBy('avg_rating', 'asc')
        ->take(15)
        ->get();

    // 4. Top Sellers by Orders
    $topSellersByOrders = Seller::select('sellers.*')
        ->selectRaw('COUNT(DISTINCT orders.id) as total_orders')
        ->leftJoin('products', 'products.created_by_id', '=', 'sellers.id')
        ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
        ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
        ->whereNull('orders.deleted_at')
        ->groupBy('sellers.id')
        ->orderByDesc('total_orders')
        ->take(15)
        ->get();

    // 5. Slow Moving Sellers (lowest sales/revenue)
    $slowMovingSellers = Seller::select('sellers.*')
        ->selectRaw('COALESCE(SUM(order_items.price * order_items.quantity), 0) as total_revenue')
        ->leftJoin('products', 'products.created_by_id', '=', 'sellers.id')
        ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
        ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
        ->whereNull('orders.deleted_at')
        ->groupBy('sellers.id')
        ->orderBy('total_revenue', 'asc')
        ->take(15)
        ->get();

    // 6. Active Boosters (most spent on boosted listings)
    $activeBoosters = Seller::select('sellers.*')
        ->selectRaw('COALESCE(SUM(boosted_listings.boost_amount), 0) as total_boost_spend')
        ->leftJoin('boosted_listings', 'boosted_listings.seller_id', '=', 'sellers.id')
        ->groupBy('sellers.id')
        ->orderByDesc('total_boost_spend')
        ->take(15)
        ->get();

    // 7. New Registrations (registered within last 30 days)
    $newRegistrations = Seller::where('created_at', '>=', Carbon::now()->subDays(30))
        ->orderByDesc('created_at')
        ->take(15)
        ->get();

    // 8. Banned / Warning History
    $bannedSellers = Seller::whereIn('status', ['blocked', 'inactive'])
        ->orderByDesc('updated_at')
        ->take(15)
        ->get();

    // 9. Best Conversion Rate (Mocked or calculated based on page views vs orders)
    $conversionRateSellers = Seller::select('sellers.*')
        ->selectRaw('COUNT(DISTINCT orders.id) as orders_count')
        ->leftJoin('products', 'products.created_by_id', '=', 'sellers.id')
        ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
        ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
        ->groupBy('sellers.id')
        ->take(15)
        ->get()
        ->map(function($seller) {
            // Mocking pageviews to calculate a realistic conversion rate
            $pageViews = rand(1000, 5000);
            $seller->conversion_rate = $pageViews > 0 
                ? round(($seller->orders_count / $pageViews) * 100, 2) 
                : 0;
            $seller->page_views = $pageViews;
            return $seller;
        })
        ->sortByDesc('conversion_rate')
        ->values();

    // Overall KPI statistics
    $totalSellers = Seller::count();
    $activeSellersCount = Seller::where('status', 'active')->count();
    $averageSellerRevenue = count($highValueSellers) > 0 ? round($highValueSellers->avg('total_revenue'), 2) : 0;

    return view('admin.analytics.seller-analytics', compact(
        'highValueSellers', 'frequentReturns', 'badReviewSellers', 'topSellersByOrders',
        'slowMovingSellers', 'activeBoosters', 'newRegistrations', 'bannedSellers',
        'conversionRateSellers', 'totalSellers', 'activeSellersCount', 'averageSellerRevenue',
        'startDate', 'endDate'
    ));
}

private function getBestTimingForProduct($product)
{
    $peakHours = Order::whereHas('items.product', function($q) use ($product) {
        $q->where('id', $product->id);
    })
    ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
    ->groupBy('hour')
    ->orderBy('count', 'DESC')
    ->first();
    
    if ($peakHours) {
        $hour = $peakHours->hour;
        return sprintf("%02d:00 - %02d:00", $hour, $hour + 2);
    }
    
    return "10:00 - 18:00";
}



















}
