<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceSubCategory;
// ✅ MODELS
use App\Models\ShippingSetting;
use App\Models\ShippingZone;
use App\Models\CourierIntegration;
use App\Models\OrderSlaLog;
use App\Models\PaymentAttempt;
use App\Models\ActivityLog;
use App\Models\SupplierPerformance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\NotificationService;

class SellerOrderController extends Controller
{
    
   
   
   
   private function getModuleType()
{
    /*
    |--------------------------------------------------------------------------
    | SESSION PANELS
    |--------------------------------------------------------------------------
    */

    $panels = session('selected_panels', []);

    if(!is_array($panels)){
        $panels = [];
    }

    /*
    |--------------------------------------------------------------------------
    | FLAGS
    |--------------------------------------------------------------------------
    */

    $hasProduct = in_array('product', $panels);

    $hasTravel  = in_array('travel', $panels);

    $hasCourse  = in_array('vcampus', $panels);

    /*
    |--------------------------------------------------------------------------
    | COURSE ONLY
    |--------------------------------------------------------------------------
    */

    if(
        $hasCourse
        &&
        !$hasProduct
        &&
        !$hasTravel
    ){
        return 'course';
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT ONLY
    |--------------------------------------------------------------------------
    */

    if(
        $hasProduct
        &&
        !$hasTravel
        &&
        !$hasCourse
    ){
        return 'product';
    }

    /*
    |--------------------------------------------------------------------------
    | SERVICE ONLY
    |--------------------------------------------------------------------------
    */

    if(
        !$hasProduct
        &&
        $hasTravel
        &&
        !$hasCourse
    ){
        return 'service';
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT + SERVICE
    |--------------------------------------------------------------------------
    */

    if(
        $hasProduct
        &&
        $hasTravel
        &&
        !$hasCourse
    ){
        return 'both';
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT + COURSE
    |--------------------------------------------------------------------------
    */

    if(
        $hasProduct
        &&
        $hasCourse
        &&
        !$hasTravel
    ){
        return 'product_course';
    }

    /*
    |--------------------------------------------------------------------------
    | SERVICE + COURSE
    |--------------------------------------------------------------------------
    */

    if(
        $hasTravel
        &&
        $hasCourse
        &&
        !$hasProduct
    ){
        return 'service_course';
    }

    /*
    |--------------------------------------------------------------------------
    | ALL MODULES
    |--------------------------------------------------------------------------
    */

    if(
        $hasProduct
        &&
        $hasTravel
        &&
        $hasCourse
    ){
        return 'all';
    }

    /*
    |--------------------------------------------------------------------------
    | DEFAULT
    |--------------------------------------------------------------------------
    */

    return 'product';
}
   


private function sellerOrders()
{
    $seller = auth()->guard('seller')->user();

    $moduleType = $this->getModuleType();

    $query = Order::where('seller_id', $seller->id);

    // Removed the whereHas('items.product') filter that restricted to specific product types.
    // Now all orders assigned to this seller_id will be displayed.

    return $query
        ->with('customer')
        ->orderByDesc('id');
}

public function index(Request $request)
{
    $query = $this->sellerOrders()->with(['customer','items.product']);

    $search = trim($request->q);

    /*
    |--------------------------------------------------
    | SEARCH
    |--------------------------------------------------
    */
    if ($search) {
        $query->where(function($q) use ($search){

            $q->where('id', $search)
              ->orWhere('transaction_id', 'like', '%'.$search.'%')
              ->orWhereHas('customer', function($c) use ($search){
                  $c->where('name','like','%'.$search.'%')
                    ->orWhere('email','like','%'.$search.'%')
                    ->orWhere('phone','like','%'.$search.'%');
              })
              ->orWhereHas('items.product', function($p) use ($search){
                  $p->where('name','like','%'.$search.'%')
                    ->orWhere('sku','like','%'.$search.'%')
                    ->orWhere('description','like','%'.$search.'%');
              });

        });
    }

    /*
    |--------------------------------------------------
    | FILTERS
    |--------------------------------------------------
    */

    if ($request->category) {
        $query->whereHas('items.product', function ($q) use ($request) {
            $q->where('category_id', $request->category);
        });
    }

    if ($request->subcategory) {
        $query->whereHas('items.product', function ($q) use ($request) {
            $q->where('subcategory_id', $request->subcategory);
        });
    }

    if ($request->type) {
        $query->whereHas('items.product', function ($q) {
            $q->where('type', request('type'));
        });
    }

    if ($request->payment_type) {
        $query->where('payment_status', $request->payment_type);
    }

    if ($request->status) {
        $query->where('status', $request->status);
    }

    if ($request->from_date) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->to_date) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    /*
    |--------------------------------------------------
    | RESULT
    |--------------------------------------------------
    */
    $orders = $query->latest()->paginate(6)->withQueryString();

    /*
    |--------------------------------------------------
    | FIX: CATEGORIES + SUBCATEGORIES
    |--------------------------------------------------
    */
    $categories = MarketplaceCategory::where('seller_id', auth()->id())->get();

    $subcategories = MarketplaceSubCategory::when($request->category, function ($q) use ($request) {
        $q->where('category_id', $request->category);
    })
    ->where('seller_id', auth()->id())
    ->get();

    // Defining variables to prevent undefined variable error in view
    $ratings = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
    $maxSliderPrice = 10000;
    $attributes = [];
    $attributeTerms = [];
    $products = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
    $banners = [];
    $brands = [];

    return view('vshop.seller.orders.index', compact(
        'orders',
        'categories',
        'subcategories',
        'ratings',
        'maxSliderPrice',
        'attributes',
        'attributeTerms',
        'products',
        'banners',
        'brands'
    ));
}
    public function processing()
    {
        $orders = $this->sellerOrders()->where('status', 'processing')->get();
        return view('vshop.seller.orders.processing', compact('orders'));
    }

    public function completed()
    {
        $orders = $this->sellerOrders()->where('status', 'completed')->get();
        return view('vshop.seller.orders.completed', compact('orders'));
    }

    public function outForDelivery()
    {
        $orders = $this->sellerOrders()->where('status', 'out for delivery')->get();
        return view('vshop.seller.orders.outfordelivery', compact('orders'));
    }

    public function refund()
    {
        $orders = $this->sellerOrders()->where('status', 'refund')->get();
        return view('vshop.seller.orders.refund', compact('orders'));
    }

    public function declined()
    {
        $orders = $this->sellerOrders()->where('status', 'declined')->get();
        return view('vshop.seller.orders.declined', compact('orders'));
    }

    public function failed()
    {
        $orders = $this->sellerOrders()->where('status', 'failed')->get();
        return view('vshop.seller.orders.failed', compact('orders'));
    }

    public function cancelled()
    {
        $orders = $this->sellerOrders()->where('status', 'cancelled')->get();
        return view('vshop.seller.orders.cancelled', compact('orders'));
    }

    public function cancelRequest()
    {
        $orders = $this->sellerOrders()->where('status', 'cancel request')->get();
        return view('vshop.seller.orders.cancelrequest', compact('orders'));
    }

    /* =======================
       SHOW SINGLE ORDER
    ======================= */
    public function show($id)
    {
        $seller = auth()->guard('seller')->user();

        $order = Order::where('seller_id', $seller->id)
            ->with([
                'customer',
                'items.product',
                'items.color',
                'items.size'
            ])
            ->findOrFail($id);

        return view('vshop.seller.orders.show', compact('order'));
    }

    /* =======================
       UPDATE STATUS (Optional)
    ======================= */
    public function updateStatus(Request $request, $id)
    {
        $seller = auth()->guard('seller')->user();

        $order = Order::where('seller_id', $seller->id)->findOrFail($id);

        $finalStatus = $request->status ?: $request->prev_status;

        $order->status = $finalStatus;

        $startDate = $request->start_date ?: null;
        $endDate   = $request->end_date ?: null;

        $order->start_date = $startDate ?? now()->setTimezone('Asia/Kolkata');
        $order->end_date   = $endDate;
        $order->remarks    = $request->remarks;
        $order->privacy    = $request->privacy;

        $order->save();

        NotificationService::notify(
            $order->customer_id,
            'customer',
            'order',
            "Your order #{$order->id} status has been updated to {$finalStatus}.",
            route('vshop.order.details', $order->id)
        );

        return back()->with('success', 'Order status updated successfully.');
    }

    /* =======================
       MANAGE ORDER VIEW
    ======================= */
   public function showManageOrder($id)
{
    $seller = auth()->guard('seller')->user();

    $order = Order::with([
        'customer',
        'items.product',
        'items.color',
        'items.size'
    ])
    ->where('id', $id)
    ->where('seller_id', $seller->id)
    ->first();

    if (!$order) {
        return redirect()->route('seller.orders.index')
            ->with('error', 'Order not found or access denied');
    }

    return view('vshop.seller.orders.manage', compact('order'));
}
    /* =======================
       INVOICE
    ======================= */
public function invoice($id)
{
    $seller = auth()->guard('seller')->user();

    $order = Order::where('seller_id', $seller->id) // ✅ MAIN FIX
        ->with([
            'customer',
            'items.product',
            'items.color',
            'items.size',
            'customer.firstAddress'
        ])
        ->findOrFail($id);

    return view('vshop.seller.orders.invoice', compact('order'));
}
    
    
    
    
   public function ordermis(Request $request)
{
    $query = $this->sellerOrders();

    // =========================
    // DATE FILTER
    // =========================
    if (!empty($request->from) && !empty($request->to)) {
        $query->whereBetween('created_at', [$request->from, $request->to]);
    }

    $orders = $query->get();

    // =========================
    // ORDER COUNTS
    // =========================
    $counts = [
        'all'        => $orders->count(),
        'processing' => $orders->where('status', 'processing')->count(),
        'completed'  => $orders->where('status', 'completed')->count(),
        'cancelled'  => $orders->where('status', 'cancelled')->count(),
        'returned'   => $orders->where('status', 'refund')->count(),
    ];

    // =========================
    // REVENUE
    // =========================
    $grossRevenue = $orders->sum('total_amount');

    $revenueLoss = $orders
        ->whereIn('status', ['cancelled', 'refund'])
        ->sum('total_amount');

    // =========================
    // CUSTOMER GROUP
    // =========================
    $groupedCustomers = $orders->groupBy('customer_id');

    $newCustomers = $groupedCustomers->count();

    $returningCustomers = $groupedCustomers
        ->filter(fn($items) => $items->count() > 1)
        ->count();

    // =========================
    // TOP CUSTOMERS
    // =========================
    $topCustomers = $groupedCustomers
        ->map(function ($items) {
            return [
                'customer' => optional($items->first()->customer)->name ?? 'N/A',
                'total'    => $items->sum('total_amount')
            ];
        })
        ->sortByDesc('total')
        ->take(5);

    // =========================
    // HIGH VALUE CUSTOMERS âœ… FIX
    // =========================
    $highValueCustomers = $groupedCustomers
        ->map(function ($items) {
            return (object)[
                'name'  => optional($items->first()->customer)->name ?? 'N/A',
                'total' => $items->sum('total_amount')
            ];
        })
        ->sortByDesc('total')
        ->take(5);

    // =========================
    // RETURNING LIST
    // =========================
    $returningList = $groupedCustomers
        ->filter(fn($items) => $items->count() > 1)
        ->map(function ($items) {
            return (object)[
                'name' => optional($items->first()->customer)->name ?? 'N/A'
            ];
        })
        ->take(5);

    // =========================
    // REFERRED (dummy for now)
    // =========================
    $referredCustomers = collect([]);

    // =========================
    // STATUS WISE REVENUE
    // =========================
    $statusRevenue = $orders
        ->groupBy('status')
        ->map(fn($items) => $items->sum('total_amount'));

    // =========================
    // MONTHLY REVENUE
    // =========================
    $monthlyRevenue = $orders
        ->groupBy(fn($order) =>
            \Carbon\Carbon::parse($order->created_at)->format('M Y')
        )
        ->map(fn($items) => $items->sum('total_amount'));

    // =========================
    // SALES FUNNEL âœ…
    // =========================
    $funnel = [
        'enquiry'   => $orders->count(), // adjust if enquiry table exists
        'order'     => $orders->count(),
        'success'   => $orders->where('status', 'completed')->count(),
        'delivered' => $orders->where('status', 'completed')->count(),
    ];

    // =========================
    // GRIEVANCE (dummy)
    // =========================
    $grievance = collect([
        'Mon' => 2,
        'Tue' => 5,
        'Wed' => 3,
        'Thu' => 6,
        'Fri' => 2,
    ]);

    // =========================
    // REVIEWS (dummy)
    // =========================
    $reviews = collect([
        '1â˜…' => 5,
        '2â˜…' => 3,
        '3â˜…' => 2,
    ]);

    // =========================
    // LOCATION (dummy)
    // =========================
    $locations = collect([
        'Kolkata' => 20,
        'Delhi'   => 15,
        'Mumbai'  => 10,
    ]);

    // =========================
    // PRODUCTS (dummy)
    // =========================
    $products = collect([
        'Service A' => 25,
        'Service B' => 18,
        'Service C' => 10,
    ]);

    // =========================
    // RETURN VIEW
    // =========================
    return view('vshop.seller.orders.mis', compact(
        'orders',
        'counts',
        'grossRevenue',
        'revenueLoss',
        'newCustomers',
        'returningCustomers',
        'topCustomers',
        'statusRevenue',
        'monthlyRevenue',

        // âœ… NEW VARIABLES (FIX)
        'highValueCustomers',
        'returningList',
        'referredCustomers',
        'funnel',
        'grievance',
        'reviews',
        'locations',
        'products'
    ));
}




public function cancel($id){
    $order = Order::findOrFail($id);
    $order->status = 'cancelled';
    $order->save();

    NotificationService::notify(
        $order->customer_id,
        'customer',
        'order',
        "Your order #{$order->id} has been cancelled by the seller.",
        route('vshop.order.details', $order->id)
    );

    return back()->with('success','Order cancelled');
}

public function returnRequest(Request $request, $id)
{
    // ✅ Validate reason
    $request->validate([
        'return_reason' => 'string|max:500'
    ]);

    $order = Order::findOrFail($id);

    $order->status = 'return_requested';
    $order->refund_status = 'initiated';

    // ✅ Save reason
    $order->return_reason = $request->return_reason;

    // ✅ Save timestamp (important for SLA)
    $order->return_requested_at = now();

    $order->save();

    return back()->with('success', 'Return request submitted successfully');
}

public function approveReturn(Request $request)
{
    try {

        if(empty($request->id)){
            return response()->json(['status'=>false,'msg'=>'ID missing']);
        }

        $order = Order::find($request->id);

        if(!$order){
            return response()->json(['status'=>false,'msg'=>'Order not found']);
        }

        $order->refund_status = 'processing';
        $order->status = 'return_approved';
        $order->save();

        NotificationService::notify(
            $order->customer_id,
            'customer',
            'order',
            "Return request for order #{$order->id} has been approved.",
            route('vshop.order.details', $order->id)
        );

        return response()->json(['status'=>true]);

    } catch (\Exception $e) {

        return response()->json([
            'status'=>false,
            'msg'=>$e->getMessage()
        ]);
    }
}

public function rejectReturn(Request $request)
{
    try {

        $order = Order::find($request->id);

        if(!$order){
            return response()->json(['status'=>false]);
        }

        $order->refund_status = 'failed';
        $order->status = 'return_rejected';
        $order->save();

        NotificationService::notify(
            $order->customer_id,
            'customer',
            'order',
            "Return request for order #{$order->id} has been rejected.",
            route('vshop.order.details', $order->id)
        );

        return response()->json(['status'=>true]);

    } catch (\Exception $e) {

        return response()->json([
            'status'=>false,
            'msg'=>$e->getMessage()
        ]);
    }
}


public function verifyReturn(Request $request)
{
    try {

        $order = Order::find($request->id);

        if(!$order){
            return response()->json(['status'=>false]);
        }

        $order->refund_status = 'completed';
        $order->status = 'returned';
        $order->refund_completed_at = now();
        $order->save();

        NotificationService::notify(
            $order->customer_id,
            'customer',
            'order',
            "Return package verified for order #{$order->id}. Refund completed.",
            route('vshop.order.details', $order->id)
        );

        return response()->json(['status'=>true]);

    } catch (\Exception $e) {

        return response()->json([
            'status'=>false,
            'msg'=>$e->getMessage()
        ]);
    }
}
public function updateTracking(Request $request, $id)
{
    $request->validate([
        'tracking_id' => 'required|string|max:255'
    ]);

    $order = Order::findOrFail($id);

    $order->tracking_id = $request->tracking_id;
    $order->save();

    NotificationService::notify(
        $order->customer_id,
        'customer',
        'order',
        "Tracking ID updated for order #{$order->id}: {$request->tracking_id}.",
        route('vshop.order.details', $order->id)
    );

    return back()->with('success', 'Tracking ID updated successfully');
}


public function getSubcategories(Request $request)
{
    return MarketplaceSubCategory::where('category_id', $request->category_id)
        ->where('seller_id', auth()->id())
        ->select('id','name')
        ->get();
}

public function getProducts(Request $request)
{
    return \App\Models\Product::where('subcategory_id', $request->subcategory_id)
        ->where('created_by','seller')
        ->where('created_by_id', auth()->id())
        ->select('id','name')
        ->get();
}

public function payment(Request $request)
{
    $sellerId = auth()->guard('seller')->id();

    /*
    |--------------------------------------------------------------------------
    | MODULE TYPE
    |--------------------------------------------------------------------------
    */

    $moduleType = $this->getModuleType();

    /*
    |--------------------------------------------------------------------------
    | PAYMENT TYPE
    |--------------------------------------------------------------------------
    */

    $type = $request->type ?? 'full';

    /*
    |--------------------------------------------------------------------------
    | BASE QUERY
    |--------------------------------------------------------------------------
    */

    $query = Order::where('seller_id', $sellerId)

        ->whereHas('items.product', function($q) use ($moduleType){

            /*
            |--------------------------------------------------------------------------
            | MODULE FILTER
            |--------------------------------------------------------------------------
            */

            if($moduleType == 'product'){

                $q->where('type', 'product');

            }
            elseif($moduleType == 'service'){

                $q->where('type', 'service');

            }
            elseif($moduleType == 'course'){

                $q->where('type', 'course');

            }
            elseif($moduleType == 'both'){

                $q->whereIn('type', ['product','service']);

            }
            elseif($moduleType == 'product_course'){

                $q->whereIn('type', ['product','course']);

            }
            elseif($moduleType == 'service_course'){

                $q->whereIn('type', ['service','course']);

            }
            elseif($moduleType == 'all'){

                $q->whereIn('type', ['product','service','course']);
            }

        });

    /*
    |--------------------------------------------------------------------------
    | PAYMENT TOGGLE FILTER
    |--------------------------------------------------------------------------
    */

    if ($type === 'partial') {

        $query->where('payment_type', 'partial');

    } else {

        $query->where('payment_type', 'full');
    }

    /*
    |--------------------------------------------------------------------------
    | FETCH ORDERS
    |--------------------------------------------------------------------------
    */

    $orders = $query
        ->with(['customer','items.product'])
        ->latest()
        ->get()
        ->map(function ($o) {

            $o->customer_name =
                optional($o->customer)->name ?? 'N/A';

            /*
            |--------------------------------------------------------------------------
            | PAID AMOUNT
            |--------------------------------------------------------------------------
            */

            $o->paid_amount =
                $o->payment_status == 'paid'
                ? $o->total_amount
                : 0;

            /*
            |--------------------------------------------------------------------------
            | BALANCE
            |--------------------------------------------------------------------------
            */

            $o->balance =
                $o->payment_status == 'paid'
                ? 0
                : $o->total_amount;

            return $o;
        });

    /*
    |--------------------------------------------------------------------------
    | KPI
    |--------------------------------------------------------------------------
    */

    $totalRevenue = $orders
        ->where('payment_status','success')
        ->sum('total_amount');

    $revenueLoss = $orders
        ->where('payment_status','failed')
        ->sum('total_amount');

    $newCustomers = $orders
        ->groupBy('customer_id')
        ->count();

    $totalPayments = $orders->count();

    $successPayments = $orders
        ->where('payment_status','success')
        ->count();

    /*
    |--------------------------------------------------------------------------
    | SUCCESS RATE
    |--------------------------------------------------------------------------
    */

    $successRate = $totalPayments > 0
        ? round(($successPayments / $totalPayments) * 100, 2)
        : 0;

    /*
    |--------------------------------------------------------------------------
    | REFUND
    |--------------------------------------------------------------------------
    */

    $refundInitiated = $orders
        ->where('refund_status','initiated')
        ->count();

    $slaBreached = $orders
        ->where('sla_status','breached')
        ->count();

    /*
    |--------------------------------------------------------------------------
    | REFUND EFFICIENCY
    |--------------------------------------------------------------------------
    */

    $refundEfficiency = $totalPayments > 0
        ? round((($totalPayments - $slaBreached) / $totalPayments) * 100, 2)
        : 0;

    /*
    |--------------------------------------------------------------------------
    | AJAX RESPONSE
    |--------------------------------------------------------------------------
    */

    if ($request->ajax()) {

        return response()->json([

            'orders' => $orders,

            'totalRevenue' => $totalRevenue,

            'revenueLoss' => $revenueLoss,

            'newCustomers' => $newCustomers,

            'successRate' => $successRate,

            'refundInitiated' => $refundInitiated,

            'slaBreached' => $slaBreached,

            'refundEfficiency' => $refundEfficiency,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */

    return view(
        'vshop.seller.orders.partials.payment',
        compact(
            'orders',
            'totalRevenue',
            'revenueLoss',
            'newCustomers',
            'successRate',
            'refundInitiated',
            'slaBreached',
            'refundEfficiency'
        )
    );
}

public function sendReminder(Request $request)
{
    try {

        $order = Order::find($request->id);

        if(!$order){
            return response()->json(['status'=>false]);
        }

        // update
        $order->reminder_sent_at = now();
        $order->reminder_count = ($order->reminder_count ?? 0) + 1;
        $order->last_reminder_note = $request->note;
        $order->save();

        return response()->json([
            'status' => true,
            'message' => 'Reminder sent',
            'count' => $order->reminder_count
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'status'=>false,
            'error'=>$e->getMessage()
        ]);
    }
}
public function syncCourier(Request $request)
{
    $order = Order::find($request->order_id);

    if (!$order) {
        return response()->json(['status' => false]);
    }

    // 🔥 FAKE COURIER RESPONSE (replace later with API)
    $trackingId = 'TRK'.rand(10000,99999);

    $order->update([
        'tracking_id' => $trackingId,
        'shipping_method' => $request->courier,
        'status' => 'shipped'
    ]);

    return response()->json([
        'status' => true,
        'tracking_id' => $trackingId
    ]);
}



public function shipping()
{
    $sellerId = auth()->guard('seller')->id();

    $settings = ShippingSetting::firstOrCreate(
        ['seller_id' => $sellerId],
        [
            'shipping_type' => 'free',
            'pricing_type' => 'flat'
        ]
    );

    $zones = ShippingZone::where('seller_id', $sellerId)->get();

    $couriers = CourierIntegration::where('seller_id', $sellerId)->get();

    $onTime = OrderSlaLog::where('status', 'on_time')->count();

    $delayed = OrderSlaLog::where('status', 'delayed')->count();

    $breached = OrderSlaLog::where('status', 'breached')->count();

    $total = $onTime + $delayed + $breached;

    $onTimeRate = $total
        ? round(($onTime / $total) * 100)
        : 0;

    $success = PaymentAttempt::where('status', 'success')->count();

    $fail = PaymentAttempt::where('status', 'fail')->count();

    $paymentSuccessRate = ($success + $fail)
        ? round(($success / ($success + $fail)) * 100)
        : 0;

    $retrySuccessRate = 80;

    $refundSuccess = Order::where('refund_status', 'success')->count();

    $refundTotal = Order::whereNotNull('refund_status')->count();

    $refundRate = $refundTotal
        ? round(($refundSuccess / $refundTotal) * 100)
        : 0;

    $logs = ActivityLog::latest()
        ->limit(5)
        ->get();

    $payments = Order::whereNotNull('payment_method')
        ->where('seller_id', $sellerId)
        ->latest()
        ->limit(10)
        ->get();

    $suppliers = SupplierPerformance::get();

    // ===== IMPORTANT =====
    // Latest seller order for shipping section
    $order = Order::where('seller_id', $sellerId)
        ->latest()
        ->first();

    return view(
        'vshop.seller.orders.partials.shipping',
        compact(
            'settings',
            'zones',
            'couriers',
            'onTimeRate',
            'delayed',
            'breached',
            'paymentSuccessRate',
            'retrySuccessRate',
            'refundRate',
            'logs',
            'payments',
            'suppliers',
            'order'
        )
    );
}

public function saveShipping(Request $req)
{
    $sellerId = auth()->guard('seller')->id();

    ShippingSetting::updateOrCreate(
        ['seller_id'=>$sellerId],
        [
            'shipping_type'=>$req->shipping_type,
            'pricing_type'=>$req->pricing_type,
            'auto_delivery_calc'=>$req->auto_calc,
            'tracking_mode'=>$req->tracking_mode
        ]
    );

    return response()->json(['status'=>true]);
}


public function saveRefundReason(Request $request)
{
    try {

        // VALIDATION
        $request->validate([
            'id'     => 'required',
            'reason' => 'required|string'
        ]);

        // FIND ORDER
        $order = Order::find($request->id);

        if(!$order){

            return response()->json([
                'status'  => false,
                'message' => 'Order not found'
            ]);

        }

        // ===============================
        // SAVE REFUND REQUEST
        // ===============================

        // refund reason
        $order->refund_reason = $request->reason;

        // refund requested date
        $order->return_requested_at = now();

        // main status
        $order->status = 'return_requested';

        // refund flow status
        $order->refund_status = 'initiated';

        // optional return status
        $order->return_status = 'requested';

        // save
        $order->save();

        // RESPONSE
        return response()->json([

            'status' => true,

            'message' => 'Refund request submitted successfully',

            'data' => [

                'id' => $order->id,

                'refund_reason' => $order->refund_reason,

                'return_requested_at' => $order->return_requested_at,

                'status' => $order->status,

                'refund_status' => $order->refund_status

            ]

        ]);

    } catch (\Illuminate\Validation\ValidationException $e){

        return response()->json([

            'status' => false,

            'message' => $e->errors()

        ],422);

    } catch (\Exception $e){

        return response()->json([

            'status' => false,

            'message' => $e->getMessage()

        ]);

    }
}


public function checkShippingLive(Request $request)
{
    try {

        $order = Order::with(['customer','items.product','shippingAddress'])
            ->findOrFail($orderId);

        // ================= SELLER =================
        $seller = auth()->guard('seller')->user();

        if(!$seller || !$seller->pincode){
            return response()->json([
                'status'=>false,
                'msg'=>'Seller location missing'
            ]);
        }

        // ================= ADDRESS =================
        $address = $order->shippingAddress;

        if(!$address){
            return response()->json([
                'status'=>false,
                'msg'=>'Shipping address missing'
            ]);
        }

        // ================= ITEMS =================
        $items = [];

        foreach($order->items as $item){
            $items[] = [
                "name" => $item->product->name ?? 'Product',
                "sku" => $item->product->sku ?? 'SKU001',
                "units" => $item->quantity ?? 1,
                "selling_price" => $item->price ?? $order->total_amount,
            ];
        }

        // ================= AUTH =================
        $auth = Http::post('https://apiv2.shiprocket.in/v1/external/auth/login', [
            "email" => env('SHIPROCKET_EMAIL'),
            "password" => env('SHIPROCKET_PASSWORD')
        ]);

        if(!$auth->successful()){
            return response()->json([
                'status'=>false,
                'msg'=>'Shiprocket auth failed'
            ]);
        }

        $token = $auth['token'];

        // ================= PAYLOAD =================
        $payload = [

            "order_id" => (string)$order->id,
            "order_date" => now()->format('Y-m-d H:i'),

            "pickup_location" => "Primary",

            "billing_customer_name" => $order->customer->name ?? 'Customer',
            "billing_address" => $address->address ?? 'Address',
            "billing_city" => $address->city,
            "billing_pincode" => $address->pincode,
            "billing_state" => $address->state,
            "billing_country" => "India",
            "billing_phone" => $order->customer->phone ?? '9999999999',

            "shipping_is_billing" => true,

            "order_items" => $items,

            "payment_method" => $order->payment_method == 'cod' ? "COD" : "Prepaid",
            "sub_total" => $order->total_amount,

            "length" => 10,
            "breadth" => 10,
            "height" => 10,
            "weight" => 0.5,

            // OPTIONAL BUT GOOD
            "shipping_charges" => $order->courier_charge ?? 0,
            "total_discount" => 0,
        ];

        // ================= CREATE ORDER =================
        $response = Http::withToken($token)
            ->post('https://apiv2.shiprocket.in/v1/external/orders/create/adhoc', $payload);

        if(!$response->successful()){
            return response()->json([
                'status'=>false,
                'msg'=>'Shipment creation failed',
                'error'=>$response->body()
            ]);
        }

        $data = $response->json();

        // ================= SAVE RESPONSE =================
        $order->update([

            'sr_order_id' => $data['order_id'] ?? null,
            'sr_shipment_id' => $data['shipment_id'] ?? null,

            'shipping_method' => 'shiprocket',
            'status' => 'processing',

            'courier_response' => json_encode($data)
        ]);

        // ================= GENERATE AWB =================
        if(!empty($data['shipment_id'])){

            $awb = Http::withToken($token)
                ->post('https://apiv2.shiprocket.in/v1/external/courier/assign/awb', [
                    "shipment_id" => $data['shipment_id']
                ]);

            if($awb->successful()){

                $awbData = $awb->json();

                $order->update([
                    'sr_awb_code' => $awbData['awb_code'] ?? null,
                    'courier_name' => $awbData['courier_name'] ?? null,
                    'shipped_at' => now()
                ]);
            }
        }

        return response()->json([
            'status'=>true,
            'msg'=>'Shipment created successfully',
            'data'=>$data
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'status'=>false,
            'msg'=>$e->getMessage()
        ]);
    }
}






}
