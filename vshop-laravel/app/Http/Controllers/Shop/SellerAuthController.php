<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Enquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Complaint;
use App\Models\ProductReview;
use App\Models\OrderItem;
use App\Models\SellerStaff;

use App\Models\Grievance;
use App\Models\SellerFinance;
use Carbon\Carbon;
use App\Models\Notification;
use App\Mail\SellerOtpMail;
class SellerAuthController extends Controller
{
    /**
     * Show seller registration form
     */
    public function showRegister()
    {
        return view('vshop.seller.register');
    }

    /**
     * Store seller registration (status = pending)
     */
   
   public function storeRegister(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:100',
        'mobile'   => 'required|string|max:15|unique:sellers,mobile',
        'email'    => 'required|email|unique:sellers,email',
       'password' => 'required|string|min:6',
    ]);

    // Generate 4-digit OTP
    $otp = rand(1000, 9999);

    // Store registration data + OTP in session for verification
    session([
        'seller_register_data' => [
            'name'          => $request->name,
            'mobile'        => $request->mobile,
            'email'         => $request->email,
            'password'      => $request->password,
            'referral_code' => $request->referral_code,
        ],
        'seller_register_otp'    => $otp,
        'seller_register_otp_at' => now(),
    ]);

    // Send OTP to seller's email
    SellerOtpMail::send(
        $request->email,
        $request->name,
        $otp,
        'registration'
    );

    return response()->json([
        'status'  => true,
        'message' => 'OTP sent to your email. Please verify.',
    ]);
}

    /**
     * Verify registration OTP and create seller account
     */
    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:4',
        ]);

        $sessionOtp   = session('seller_register_otp');
        $sessionOtpAt = session('seller_register_otp_at');
        $registerData  = session('seller_register_data');

        if (!$sessionOtp || !$registerData) {
            return response()->json([
                'status'  => false,
                'message' => 'Session expired. Please register again.',
            ], 400);
        }

        // Check expiry (5 minutes)
        if (now()->diffInMinutes($sessionOtpAt) > 5) {
            return response()->json([
                'status'  => false,
                'message' => 'OTP expired. Please resend.',
            ], 400);
        }

        // OTP mismatch
        if ((string) $sessionOtp !== (string) $request->otp) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid OTP',
            ], 400);
        }

        // ✅ OTP verified — create seller
        $sellerData = [
            'name'     => $registerData['name'],
            'mobile'   => $registerData['mobile'],
            'email'    => $registerData['email'],
            'password' => Hash::make($registerData['password']),
            'status'   => 'pending',
        ];

        // Check if referral code was provided and find referring seller
        if (!empty($registerData['referral_code'])) {
            $referrer = Seller::where('referral_code', $registerData['referral_code'])->first();
            if ($referrer) {
                $sellerData['referred_by'] = $referrer->id;
            }
        }

        $seller = Seller::create($sellerData);

        // ✅ Initialize Wallet with 50k
        \App\Models\Wallet::create([
            'seller_id' => $seller->id,
            'user_type' => 'seller',
            'balance'   => 50000,
            'is_frozen' => 0,
        ]);

        // Clear session
        session()->forget(['seller_register_data', 'seller_register_otp', 'seller_register_otp_at']);

        // ✅ Login automatically after verification
        Auth::guard('seller')->login($seller);
        auth()->shouldUse('seller');

        session([
            'login_type' => 'seller',
            'seller_id'  => $seller->id
        ]);

        session()->forget([
            'selected_panels',
            'seller_panel',
            'login_otp_seller_id'
        ]);

        session()->save();

        return response()->json([
            'status'   => true,
            'message'  => 'Registration successful!',
            'redirect' => route('seller.panel.select'),
        ]);
    }

    /**
     * Resend registration OTP
     */
    public function resendRegisterOtp(Request $request)
    {
        $registerData = session('seller_register_data');

        if (!$registerData) {
            return response()->json([
                'status'  => false,
                'message' => 'Session expired. Please register again.',
            ], 400);
        }

        $otp = rand(1000, 9999);

        session([
            'seller_register_otp'    => $otp,
            'seller_register_otp_at' => now(),
        ]);

        SellerOtpMail::send(
            $registerData['email'],
            $registerData['name'],
            $otp,
            'registration'
        );

        return response()->json([
            'status'  => true,
            'message' => 'OTP resent to your email.',
        ]);
    }

    /**
     * Show seller login form
     */
    public function showLogin()
    {
        return view('vshop.seller.login');
    }



public function login(Request $request)
{
    $request->validate([
        'login'    => 'required',
        'password' => 'required',
    ]);

    $loginInput = trim($request->login);

    // CHECK EMAIL OR MOBILE
    if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
        $seller = Seller::where('email', $loginInput)->first();
    } else {
        $seller = Seller::where('mobile', $loginInput)->first();
    }

    // SELLER NOT FOUND
    if (!$seller) {
        return response()->json([
            'status' => false,
            'message' => 'Seller account not found'
        ], 404);
    }

    // CHECK IF BLOCKED OR INACTIVE
    if ($seller->status === 'blocked' || $seller->status === 'inactive' || $seller->is_active === 0 || $seller->is_active === '0') {
        $reason = $seller->deactivated_reason ?? 'Your account has been deactivated or blocked.';
        if ($seller->status === 'blocked') {
            $metadata = json_decode($seller->block_metadata, true);
            $reason = $metadata['reason'] ?? $reason;
        }
        return response()->json([
            'status' => false,
            'message' => $reason . ' Please contact support.'
        ], 403);
    }

    // INVALID PASSWORD
    if (!Hash::check($request->password, $seller->password)) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid password'
        ], 401);
    }

    // ✅ Password OK → Generate OTP and send to email
    $otp = rand(1000, 9999);

    $seller->update([
        'otp' => $otp,
        'otp_generated_at' => now(),
    ]);

    // Store seller id in session for OTP verification
    session(['login_otp_seller_id' => $seller->id]);

    $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);

    if ($isEmail) {
        // Send OTP to email
        if (!empty($seller->email)) {
            SellerOtpMail::send(
                $seller->email,
                $seller->name ?? 'Seller',
                $otp,
                'login'
            );
        }
        $message = 'OTP sent to your registered email';
    } else {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_FROM');

        if ($sid && $token && $from) {
            try {
                // Ensure number has country code for Twilio
                $toNumber = $seller->mobile;
                if (!str_starts_with($toNumber, '+')) {
                    $toNumber = '+91' . ltrim($toNumber, '0');
                }

                // Use Laravel Http Facade instead of SDK
                $response = \Illuminate\Support\Facades\Http::asForm()
                    ->withBasicAuth($sid, $token)
                    ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                        'From' => $from,
                        'To' => $toNumber,
                        'Body' => "Your Seller Login OTP is: {$otp}"
                    ]);

                if ($response->successful()) {
                    $message = 'OTP sent to your registered mobile number via SMS';
                } else {
                    throw new \Exception($response->body());
                }

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Twilio SMS Error: " . $e->getMessage());
                // Fallback to dev mode if SMS fails so user isn't stuck
                $message = 'Twilio Error: OTP sent in Dev Mode. Check logs.';
            }
        } else {
            \Illuminate\Support\Facades\Log::info("Seller Login OTP for Mobile ({$seller->mobile}): {$otp}");
            $message = 'OTP sent to your registered mobile number (Twilio keys not set, dev mode used)';
        }
    }

    return response()->json([
        'status' => true,
        'otp_required' => true,
        'message' => $message,
        'dev_otp' => !$isEmail ? $otp : null, // returning OTP for dev testing since no SMS gateway exists
    ]);
}

/**
 * Verify login email OTP and complete login
 */
public function verifyLoginEmailOtp(Request $request)
{
    $request->validate([
        'otp' => 'required|digits:4',
    ]);

    $sellerId = session('login_otp_seller_id');

    if (!$sellerId) {
        return response()->json([
            'status' => false,
            'message' => 'Session expired. Please login again.',
        ], 400);
    }

    $seller = Seller::find($sellerId);

    if (!$seller || !$seller->otp || !$seller->otp_generated_at) {
        return response()->json([
            'status' => false,
            'message' => 'Please request a new OTP.',
        ], 400);
    }

    // Check expiry (5 minutes)
    if (now()->diffInMinutes($seller->otp_generated_at) > 5) {
        return response()->json([
            'status' => false,
            'message' => 'OTP expired. Please login again.',
        ], 400);
    }

    // OTP mismatch
    if ((string) $seller->otp !== (string) $request->otp) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid OTP',
        ], 400);
    }

    // ✅ Clear OTP
    $seller->update([
        'otp' => null,
        'otp_generated_at' => null,
    ]);

    // ✅ Login
    Auth::guard('seller')->login($seller);
    auth()->shouldUse('seller');

    session([
        'login_type' => 'seller',
        'seller_id'  => $seller->id
    ]);

    session()->forget([
        'selected_panels',
        'seller_panel',
        'login_otp_seller_id'
    ]);

    session()->save();

    return response()->json([
        'status' => true,
        'redirect' => route('seller.panel.select')
    ]);
}




public function dashboard(Request $request)
{
    $sellerId = auth()->guard('seller')->id();
    $search = trim($request->q);

    /*
    |--------------------------------------------------------------------------
    | MODULE TYPE
    |--------------------------------------------------------------------------
    */

    $moduleType = $this->getModuleType();

    /*
    |--------------------------------------------------------------------------
    | ACTIVE ENQUIRIES (GLOBAL SEARCH)
    |--------------------------------------------------------------------------
    */

    $activeEnquiries = Enquiry::where('seller_id',$sellerId)
        ->when($search, function($q) use ($search){
            $q->where(function($query) use ($search){
                $query->where('message','like','%'.$search.'%')
                      ->orWhere('user_name','like','%'.$search.'%')
                      ->orWhere('user_email','like','%'.$search.'%')
                      ->orWhere('user_mobile','like','%'.$search.'%');
            });
        })
        ->where('status','active')
        ->count();

    /*
    |--------------------------------------------------------------------------
    | SALES (LAST 8 MONTHS)
    |--------------------------------------------------------------------------
    */

    $months = [];
    $salesData = [];

    for ($i = 7; $i >= 0; $i--) {

        $date = \Carbon\Carbon::now()->subMonths($i);

        $months[] = $date->format('M');

        $total = Order::where('seller_id', $sellerId)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->whereIn('status', ['completed','paid'])
            ->sum('total_amount');

        $salesData[] = (float) $total;
    }

    /*
    |--------------------------------------------------------------------------
    | STAR PRODUCTS WITH FILTER
    |--------------------------------------------------------------------------
    */

    $starFilter = $request->get('star_filter', 'sold');
    
    $starProductsQuery = Product::withCount([
            'items as total_sold' => function($q){
                $q->select(DB::raw("SUM(quantity)"));
            },
            'complaints',
        ])
        ->withAvg('reviews','rating')
        ->where('created_by_id',$sellerId);

    if($moduleType == 'product'){

        $starProductsQuery->where('type','product');

    }elseif($moduleType == 'service'){

        $starProductsQuery->where('type','service');

    }elseif($moduleType == 'course'){

        $starProductsQuery->where('type','course');
    }

    $starProductsQuery->when($search, function($q) use ($search){

        $q->where(function($query) use ($search){

            $query->where('name','like','%'.$search.'%')
                  ->orWhere('description','like','%'.$search.'%')
                  ->orWhere('sku','like','%'.$search.'%')
                  ->orWhereHas('brand', function($b) use ($search){
                      $b->where('name','like','%'.$search.'%');
                  })
                  ->orWhereHas('category', function($c) use ($search){
                      $c->where('name','like','%'.$search.'%');
                  });
        });
    });

    switch ($starFilter) {

        case 'rating':
            $starProductsQuery->orderBy('reviews_avg_rating', 'desc');
            break;

        case 'grievance':
            $starProductsQuery->orderBy('complaints_count', 'desc');
            break;

        case 'sold':
        default:
            $starProductsQuery->orderByDesc('total_sold');
            break;
    }

    $starProducts = $starProductsQuery->paginate(6,['*'],'star_page');

    /*
    |--------------------------------------------------------------------------
    | SLOW MOVERS WITH FILTER
    |--------------------------------------------------------------------------
    */

    $slowFilter = $request->get('slow_filter', 'latest');
    
    $slowQuery = Product::withCount([
            'items as total_sold' => function($q){
                $q->select(DB::raw("SUM(quantity)"));
            },
            'returns',
        ])
        ->withAvg('reviews','rating')
        ->where('created_by_id',$sellerId);

    if($moduleType == 'product'){

        $slowQuery->where('type','product');

    }elseif($moduleType == 'service'){

        $slowQuery->where('type','service');

    }elseif($moduleType == 'course'){

        $slowQuery->where('type','course');
    }

    $slowQuery->when($search, function($q) use ($search){

        $q->where(function($query) use ($search){

            $query->where('name','like','%'.$search.'%')
                  ->orWhere('description','like','%'.$search.'%')
                  ->orWhere('sku','like','%'.$search.'%')
                  ->orWhereHas('brand', function($b) use ($search){
                      $b->where('name','like','%'.$search.'%');
                  })
                  ->orWhereHas('category', function($c) use ($search){
                      $c->where('name','like','%'.$search.'%');
                  });
        });
    });

    switch ($slowFilter) {

        case 'lowest_review':
            $slowQuery->orderBy('reviews_avg_rating','asc');
            break;

        case 'lowest_sold':
            $slowQuery->orderBy('total_sold','asc');
            break;

        case 'highest_returned':
            $slowQuery->orderBy('returns_count','desc');
            break;

        case 'oldest':
            $slowQuery->oldest();
            break;

        case 'latest':
        default:
            $slowQuery->latest();
            break;
    }

    $slowProducts = $slowQuery->paginate(6,['*'],'slow_page');

    /*
    |--------------------------------------------------------------------------
    | GRIEVANCES
    |--------------------------------------------------------------------------
    */

    $grievanceFilter = $request->get('grievance_filter', 'latest');
    
    $grievanceQuery = Grievance::where('seller_id',$sellerId)
        ->when($search, function($q) use ($search){

            $q->where(function($query) use ($search){

                $query->where('message','like','%'.$search.'%')
                      ->orWhere('subject','like','%'.$search.'%');
            });
        });

    if ($grievanceFilter == 'oldest') {

        $grievanceQuery->oldest();

    } else {

        $grievanceQuery->latest();
    }

    $grievances = $grievanceQuery->paginate(6,['*'],'grievance_page');

    /*
    |--------------------------------------------------------------------------
    | REVIEWS
    |--------------------------------------------------------------------------
    */

    $reviewFilter = $request->get('review_filter', 'latest');
    
    $reviewsQuery = ProductReview::where('seller_id',$sellerId);

    if($moduleType == 'product'){

        $reviewsQuery->whereHas('product', function($q){
            $q->where('type','product');
        });

    }elseif($moduleType == 'service'){

        $reviewsQuery->whereHas('product', function($q){
            $q->where('type','service');
        });

    }elseif($moduleType == 'course'){

        $reviewsQuery->whereHas('product', function($q){
            $q->where('type','course');
        });
    }

    if($search){

        $reviewsQuery->where(function($q) use ($search){
            $q->where('comment','like','%'.$search.'%');
        });
    }

    if ($reviewFilter == 'asked_remove') {

        $reviewsQuery->where('asked_to_remove', 1);
    }
    
    $reviewsQuery->latest();

    $reviews = $reviewsQuery->paginate(6,['*'],'review_page');

    /*
    |--------------------------------------------------------------------------
    | ORDERS
    |--------------------------------------------------------------------------
    */

    $orderQuery = Order::where('seller_id',$sellerId)
        ->whereIn('status',['completed','paid']);

    if($search){

        $orderQuery->where(function($q) use ($search){

            $q->where('transaction_id','like','%'.$search.'%')
              ->orWhere('id',$search)
              ->orWhereHas('customer', function($c) use ($search){

                  $c->where('name','like','%'.$search.'%')
                    ->orWhere('email','like','%'.$search.'%')
                    ->orWhere('phone','like','%'.$search.'%');
              });
        });
    }

    $totalSales = $orderQuery->sum('total_amount');

    $totalOrders = $orderQuery->count();

    /*
    |--------------------------------------------------------------------------
    | CUSTOMERS
    |--------------------------------------------------------------------------
    */

    $customers = \App\Models\Customer::whereHas('orders', function($q) use ($sellerId){

            $q->where('seller_id', $sellerId);

        })
        ->when($search, function($q) use ($search){

            $q->where(function($query) use ($search){

                $query->where('name','like','%'.$search.'%')
                      ->orWhere('email','like','%'.$search.'%')
                      ->orWhere('phone','like','%'.$search.'%');
            });
        })
        ->limit(5)
        ->get();

    /*
    |--------------------------------------------------------------------------
    | OTHER STATS
    |--------------------------------------------------------------------------
    */

    $newCustomers = Order::where('seller_id',$sellerId)
        ->whereDate('created_at','>=', now()->subDays(30))
        ->distinct('customer_id')
        ->count('customer_id');

    $totalIncome = $totalSales;

    $totalReturns = Order::where('seller_id',$sellerId)
        ->where('status','returned')
        ->when($search, function($q) use ($search){

            $q->where('transaction_id','like','%'.$search.'%');

        })
        ->sum('total_amount');

    /*
    |--------------------------------------------------------------------------
    | PAYMENT SUCCESS RATE
    |--------------------------------------------------------------------------
    */

    $totalPayments = Order::where('seller_id',$sellerId)->count();

    $successfulPayments = Order::where('seller_id',$sellerId)
        ->where('payment_status','paid')
        ->count();

    $paymentSuccessRate = $totalPayments > 0 
        ? round(($successfulPayments / $totalPayments) * 100) . '%' 
        : '0%';

    /*
    |--------------------------------------------------------------------------
    | SLA PERFORMANCE
    |--------------------------------------------------------------------------
    */

    $totalSlaOrders = Order::where('seller_id',$sellerId)->count();

    $onTimeOrders = Order::where('seller_id',$sellerId)
        ->where('status','delivered')
        ->count();

    $slaPerformance = $totalSlaOrders > 0 
        ? round(($onTimeOrders / $totalSlaOrders) * 100) . '%' 
        : '0%';

    /*
    |--------------------------------------------------------------------------
    | REFUND EFFICIENCY
    |--------------------------------------------------------------------------
    */

    $totalRefunds = Order::where('seller_id',$sellerId)
        ->where('status','returned')
        ->count();

    $processedRefunds = Order::where('seller_id',$sellerId)
        ->where('status','refunded')
        ->count();

    $refundEfficiency = $totalRefunds > 0 
        ? round(($processedRefunds / $totalRefunds) * 100) . '%' 
        : '0%';

    /*
    |--------------------------------------------------------------------------
    | DELAYED ORDERS
    |--------------------------------------------------------------------------
    */

    $delayedOrders = Order::where('seller_id',$sellerId)
        ->where('status','!=','delivered')
        ->where('created_at','<', now()->subDays(3))
        ->count();

    /*
    |--------------------------------------------------------------------------
    | ORDER ACTIVITY HEATMAP
    |--------------------------------------------------------------------------
    */

    $heatmapMatrix = [];

    $days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

    $hours = [];

    for ($i = 0; $i < 24; $i++) {

        $hours[] = date('gA', strtotime("$i:00"));
    }

    foreach ($days as $day) {

        foreach ($hours as $hour) {

            $heatmapMatrix[$day][$hour] = 0;
        }
    }

    $ordersHeatmap = Order::where('seller_id',$sellerId)
        ->selectRaw("
            DAYNAME(created_at) as day,
            DATE_FORMAT(created_at, '%l%p') as hour,
            COUNT(*) as total
        ")
        ->groupBy('day','hour')
        ->get();

    foreach ($ordersHeatmap as $o) {

        $day = substr($o->day,0,3);

        $hour = strtoupper($o->hour);

        if(isset($heatmapMatrix[$day][$hour])){

            $heatmapMatrix[$day][$hour] = $o->total;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT EXPIRY ALERT (FIXED)
    |--------------------------------------------------------------------------
    */

    $seller = auth()->guard('seller')->user();

    $today = \Carbon\Carbon::today();

    $expiryAlerts = [];

    /*
    |--------------------------------------------------------------------------
    | PAN EXPIRY
    |--------------------------------------------------------------------------
    */

    if (!empty($seller->pan_expiry)) {

        $panDate = \Carbon\Carbon::parse($seller->pan_expiry);

        $days = (int) $today->diffInDays($panDate, false);

        // expired + upcoming 30 days
        if ($days <= 30) {

            $expiryAlerts[] = [

                'document_name' => 'PAN Card',

                'expiry_date' => $panDate->format('d M Y'),

                'days_left' => $days,

                'status' => $days < 0 ? 'expired' : 'expiring',
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GST EXPIRY
    |--------------------------------------------------------------------------
    */

    if (!empty($seller->gst_expiry)) {

        $gstDate = \Carbon\Carbon::parse($seller->gst_expiry);

        $days = (int) $today->diffInDays($gstDate, false);

        // expired + upcoming 30 days
        if ($days <= 30) {

            $expiryAlerts[] = [

                'document_name' => 'GST Certificate',

                'expiry_date' => $gstDate->format('d M Y'),

                'days_left' => $days,

                'status' => $days < 0 ? 'expired' : 'expiring',
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD DELAYED ORDERS FOR ALERT
    |--------------------------------------------------------------------------
    */

    $productTypes = ['product','professional'];

    $travelTypes = ['service','tour'];

    $alertTypes = [];

    if(($moduleType ?? '') == 'product'){

        $alertTypes = $productTypes;

    }elseif(($moduleType ?? '') == 'service'){

        $alertTypes = $travelTypes;

    }elseif(($moduleType ?? '') == 'course'){

        $alertTypes = ['course'];

    }else{

        $alertTypes = array_merge($productTypes, $travelTypes);
    }

    $dashboardDelayedOrders = Order::where('seller_id', $sellerId)
        ->whereHas('items.product', function($q) use ($alertTypes){

            $q->whereIn('type', $alertTypes);

        })
        ->where('status','!=','delivered')
        ->where('created_at','<', now()->subDays(3))
        ->count();

    /*
    |--------------------------------------------------------------------------
    | TOUR PACKAGES
    |--------------------------------------------------------------------------
    */
/*
|--------------------------------------------------------------------------
| TOUR PACKAGES
|--------------------------------------------------------------------------
*/

$tourPackages = collect([]);

$bookingTrendsData = [];

if ($moduleType == 'service' || $moduleType == '') {

    $tourPackages = \App\Models\Product::where('created_by_id', $sellerId)
        ->latest()
        ->take(5)
        ->get();

    for ($i = 7; $i >= 0; $i--) {

        $date = \Carbon\Carbon::now()->subMonths($i);

        $orders = \App\Models\Order::where('seller_id', $sellerId)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();

        $bookingTrendsData[] = $orders;
    }
}
    /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */

    return view('vshop.seller.dashboard', compact(
        'moduleType',
        'activeEnquiries',
        'months',
        'salesData',
        'starProducts',
        'slowProducts',
        'grievances',
        'reviews',
        'totalSales',
        'totalOrders',
        'newCustomers',
        'totalIncome',
        'totalReturns',
        'customers',
        'paymentSuccessRate',
        'slaPerformance',
        'refundEfficiency',
        'delayedOrders',
        'heatmapMatrix',
        'expiryAlerts',
        'dashboardDelayedOrders',
        'tourPackages',
        'bookingTrendsData'
    ));
}


public function logout(Request $request)
{
    // logout seller
    Auth::guard('seller')->logout();

    // clear custom sessions
    session()->forget([
        'login_type',
        'staff_id',
        'seller_id',
        'can_manage_orders',
        'can_view_finance',
        'can_manage_products'
    ]);

    // invalidate session
    $request->session()->invalidate();

    // regenerate csrf
    $request->session()->regenerateToken();

    // clear browser cache + redirect homepage
    return redirect('/seller/login')
        ->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
}
    
public function sendLoginOtp(Request $request)
{
    $request->validate([
        'mobile' => 'required|exists:sellers,mobile',
    ]);

    $seller = Seller::where('mobile', $request->mobile)->first();

    // Generate 4-digit OTP
    $otp = rand(1000, 9999);

    $seller->update([
        'otp' => $otp,
        'otp_generated_at' => now(),
    ]);

    // Send OTP to seller's email
    if (!empty($seller->email)) {
        SellerOtpMail::send(
            $seller->email,
            $seller->name ?? 'Seller',
            $otp,
            'login'
        );
    }

    return response()->json([
        'message' => 'OTP sent to your registered email',
        'otp' => $otp, // ⚠ remove in production
    ]);
}


public function verifyLoginOtp(Request $request)
{
    $request->validate([
        'mobile' => 'required|exists:sellers,mobile',
        'otp'    => 'required|digits:4',
    ]);

    $seller = Seller::where('mobile', $request->mobile)->first();

    // ❌ Check OTP exists
    if (!$seller->otp || !$seller->otp_generated_at) {
        return response()->json([
            'message' => 'Please request a new OTP.'
        ], 400);
    }

    // ❌ Check expiry (5 minutes)
    if (now()->diffInMinutes($seller->otp_generated_at) > 5) {
        return response()->json([
            'message' => 'OTP expired. Please resend.'
        ], 400);
    }

    // ❌ OTP mismatch
    if ((string)$seller->otp !== (string)$request->otp) {
        return response()->json([
            'message' => 'Invalid OTP'
        ], 400);
    }

    // ✅ Clear OTP
    $seller->update([
        'otp' => null,
        'otp_generated_at' => null,
    ]);

    // ❌ REMOVED ADMIN APPROVAL CHECK

    // ✅ Login
    Auth::guard('seller')->login($seller);

    return response()->json([
        'message' => 'Login successful',
      'redirect' => route('seller.panel.select'),
    ]);
}




public function forgotPassword()
{
    return view('vshop.seller.forgot-password'); // apna blade path set karo
}


public function sendForgotOtp(Request $request)
{
    $request->validate([
        'input' => 'required|string'
    ]);

    $input = trim($request->input);

    // Find seller by email or mobile
    if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
        $seller = Seller::where('email', $input)->first();
    } else {
        $seller = Seller::where('mobile', $input)->first();
    }

    if (!$seller) {
        return response()->json([
            'status' => false,
            'message' => 'No seller account found'
        ], 404);
    }

    if (empty($seller->email)) {
        return response()->json([
            'status' => false,
            'message' => 'No email registered with this account. Please contact support.'
        ], 400);
    }

    $otp = rand(1000, 9999);

    // Save OTP + seller in session
    session([
        'forgot_seller_id' => $seller->id,
        'forgot_otp'       => $otp,
        'forgot_otp_at'    => now(),
        'forgot_verified'  => false,
    ]);

    // Send OTP to seller's email
    SellerOtpMail::send(
        $seller->email,
        $seller->name ?? 'Seller',
        $otp,
        'reset'
    );

    // Mask email for display
    $email = $seller->email;
    $parts = explode('@', $email);
    $masked = substr($parts[0], 0, 2) . '***@' . $parts[1];

    return response()->json([
        'status'  => true,
        'message' => 'OTP sent to ' . $masked,
        'email'   => $masked,
    ]);
}

public function verifyForgotOtp(Request $request)
{
    $request->validate([
        'otp' => 'required|digits:4'
    ]);

    $sessionOtp   = session('forgot_otp');
    $sessionOtpAt = session('forgot_otp_at');

    if (!$sessionOtp) {
        return response()->json([
            'status' => false,
            'message' => 'Session expired. Please try again.'
        ], 400);
    }

    // Check expiry (5 minutes)
    if ($sessionOtpAt && now()->diffInMinutes($sessionOtpAt) > 5) {
        return response()->json([
            'status' => false,
            'message' => 'OTP expired. Please resend.'
        ], 400);
    }

    if ((string) $request->otp !== (string) $sessionOtp) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid OTP'
        ], 400);
    }

    // Mark as verified
    session(['forgot_verified' => true]);

    return response()->json([
        'status' => true,
        'message' => 'OTP Verified'
    ]);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'password' => 'required|min:6|confirmed'
    ]);

    // Check if OTP was verified
    if (!session('forgot_verified')) {
        return response()->json([
            'status' => false,
            'message' => 'Please verify OTP first'
        ], 400);
    }

    $sellerId = session('forgot_seller_id');

    $seller = Seller::find($sellerId);

    if (!$seller) {
        return response()->json([
            'status' => false,
            'message' => 'Session expired. Please try again.'
        ], 404);
    }

    $seller->password = bcrypt($request->password);
    $seller->save();

    // Clear forgot sessions
    session()->forget(['forgot_seller_id', 'forgot_otp', 'forgot_otp_at', 'forgot_verified']);

    // Auto-login seller after password reset
    Auth::guard('seller')->login($seller);
    auth()->shouldUse('seller');

    session([
        'login_type' => 'seller',
        'seller_id'  => $seller->id
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Password updated successfully',
        'redirect' => route('seller.panel.select')
    ]);
}


public function getNotifications()
{
    try{

        $sellerId = Auth::guard('seller')->id();

        $notifications = Notification::where('seller_id', $sellerId)
            ->latest()
            ->take(10)
            ->get();

        return response()->json($notifications);

    }catch(\Exception $e){

        return response()->json([
            'error' => $e->getMessage()
        ],500);
    }
}

 
 public function markRead(Request $request)
{
    $sellerId = Auth::guard('seller')->id();

    Notification::where('id', $request->id)
        ->where('seller_id', $sellerId) // ✅ security fix
        ->update(['is_read'=>1]);

    return response()->json(['success'=>true]);
}


public function markUnread(Request $request)
{
    $sellerId = Auth::guard('seller')->id();

    Notification::where('id', $request->id)
        ->where('seller_id', $sellerId)
        ->update(['is_read'=>0]);

    return response()->json(['success'=>true]);
}


public function clear(Request $request)
{
    try {
        $seller = auth('seller')->user();

        if (!$seller) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        DB::table('notifications')
            ->where('seller_id', $seller->id) // ✅ FIXED
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Notifications cleared successfully'
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'message' => 'Failed to clear notifications',
            'error' => $e->getMessage()
        ], 500);
    }
}

private function createNotification($sellerId, $type, $message, $link)
{
    Notification::create([
        'seller_id' => $sellerId,
        'type' => $type,
        'message' => $message,
        'link' => $link,
        'is_read' => 0
    ]);
}



public function panelSelect()
{
    $seller = auth()->guard('seller')->user();

    // Lazy check for 24h auto-approval
    if (!empty($seller->requested_platforms) && empty($seller->approved_platforms) && !empty($seller->platform_request_time)) {
        if (\Carbon\Carbon::parse($seller->platform_request_time)->addHours(24)->isPast()) {
            $seller->approved_platforms = $seller->requested_platforms;
            $seller->save();

            // Send email
            $subject = "Platform Request Approved";
            $messageBody = "Hello " . $seller->name . ",\n\nYour request for platform access has been automatically approved by the admin after 24 hours.\n\nThank you.";
            
            try {
                \Illuminate\Support\Facades\Mail::raw($messageBody, function ($message) use ($seller, $subject) {
                    $message->to($seller->email)
                            ->subject($subject)
                            ->from(env('MAIL_FROM_ADDRESS', 'support@vridhee.com'), env('MAIL_FROM_NAME', 'VSHOP'));
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Platform Request Email Failed: ' . $e->getMessage());
            }
        }
    }

    return view('vshop.seller.panel-select', compact('seller'));
}



public function switchPanelMulti(Request $request)
{
    $request->validate([
        'panels' => 'required|array|min:1'
    ]);

    $panels = $request->panels;
    $seller = auth()->guard('seller')->user();

    // Lazy check for 24h auto-approval
    if (!empty($seller->requested_platforms) && empty($seller->approved_platforms) && !empty($seller->platform_request_time)) {
        if (\Carbon\Carbon::parse($seller->platform_request_time)->addHours(24)->isPast()) {
            $seller->approved_platforms = $seller->requested_platforms;
            $seller->save();

            // Send email
            $subject = "Platform Request Approved";
            $messageBody = "Hello " . $seller->name . ",\n\nYour request for platform access has been automatically approved by the admin after 24 hours.\n\nThank you.";
            
            try {
                \Illuminate\Support\Facades\Mail::raw($messageBody, function ($message) use ($seller, $subject) {
                    $message->to($seller->email)
                            ->subject($subject)
                            ->from(env('MAIL_FROM_ADDRESS', 'support@vridhee.com'), env('MAIL_FROM_NAME', 'VSHOP'));
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Platform Request Email Failed: ' . $e->getMessage());
            }
        }
    }

    // If seller has not yet requested any platforms
    if (empty($seller->requested_platforms)) {
        $seller->requested_platforms = $panels;
        $seller->platform_request_time = now();
        $seller->save();
    }

    // Allowed panels are approved platforms if not empty, otherwise requested platforms.
    $allowed = !empty($seller->approved_platforms) ? $seller->approved_platforms : $seller->requested_platforms;
    if (empty($allowed)) {
        $allowed = $panels; // fallback
    }

    $allowedPanels = array_intersect($panels, $allowed);
    if (empty($allowedPanels)) {
        return redirect()->back()->with('error', 'Selected platforms are not approved or are rejected.');
    }

    session()->forget([
        'selected_panels',
        'seller_panel',
        'admin_panel'
    ]);

    session([
        'selected_panels' => $allowedPanels
    ]);

    if (in_array('product', $allowedPanels)) {
        session(['admin_panel' => 'product']);
    } elseif (in_array('travel', $allowedPanels)) {
        session(['admin_panel' => 'travel']);
    } elseif (in_array('vcampus', $allowedPanels)) {
        session(['admin_panel' => 'vcampus']);
    }

    session(['seller_panel' => $allowedPanels]);
    session()->save();

    if (count($allowedPanels) == 1) {
        $type = array_values($allowedPanels)[0];
        session(['seller_panel' => [$type]]);
    }

    return redirect()->route('seller.dashboard')->with('success', 'Workspace selected successfully');
}





public function index(Request $request)
{
    $search = strtolower(trim($request->q));

    if (!$search) {
        return response()->json([]);
    }

    $results = [];

    // 🔐 SESSION PERMISSIONS
    $isStaff = session('login_type') === 'staff';
    $canManageOrders = session('can_manage_orders');
    $canViewFinance = session('can_view_finance');
    $canManageProducts = session('can_manage_products');

    /*
    |--------------------------------------------------------------------------
    | ALL SIDEBAR + SUBMENU ROUTES
    |--------------------------------------------------------------------------
    */

    $menuPages = [

        // DASHBOARD
        [
            'name' => 'Seller Dashboard',
            'keywords' => ['dashboard','home'],
            'route' => route('seller.dashboard'),
            'icon' => '🏠'
        ],

        // PRODUCTS
        [
            'name' => 'Service/Products',
            'keywords' => ['product','products','service'],
            'route' => route('seller.products.mis.dashboard'),
            'icon' => '📦',
            'permission' => (!$isStaff || $canManageProducts)
        ],

        // CUSTOMERS
        [
            'name' => 'Customer MIS',
            'keywords' => ['customer mis'],
            'route' => route('seller.customer.mis'),
            'icon' => '👤'
        ],
        [
            'name' => 'Customer List',
            'keywords' => ['customer list','customers'],
            'route' => route('seller.customer.list'),
            'icon' => '👥'
        ],
        [
            'name' => 'Add Customer',
            'keywords' => ['add customer','new customer'],
            'route' => route('seller.customer.create'),
            'icon' => '➕'
        ],

        // ORDERS
        [
            'name' => 'Order MIS',
            'keywords' => ['order mis'],
            'route' => route('seller.orders.mis'),
            'icon' => '🛒',
            'permission' => (!$isStaff || $canManageOrders)
        ],
        [
            'name' => 'All Orders',
            'keywords' => ['orders','order list'],
            'route' => route('seller.orders.index'),
            'icon' => '📋',
            'permission' => (!$isStaff || $canManageOrders)
        ],
        [
            'name' => 'Payment Handling',
            'keywords' => ['payment'],
            'route' => route('seller.paymenthandle'),
            'icon' => '💰',
            'permission' => (!$isStaff || $canManageOrders)
        ],
        [
            'name' => 'Shipping & Delivery Setting',
            'keywords' => ['shipping','delivery'],
            'route' => route('seller.shipping'),
            'icon' => '🚚',
            'permission' => (!$isStaff || $canManageOrders)
        ],

        // PROFILE
        [
            'name' => 'Seller Profile',
            'keywords' => ['profile','organization'],
            'route' => route('seller.organization'),
            'icon' => '👤'
        ],

        // MARKETPLACE
        [
            'name' => 'Market Place',
            'keywords' => ['marketplace','market','shop'],
            'route' => route('seller.marketplace.category'),
            'icon' => '🏪'
        ],

        // ENQUIRY
        [
            'name' => 'Enquiry MIS',
            'keywords' => ['enquiry mis'],
            'route' => route('seller.enquiry.mis'),
            'icon' => '📩'
        ],
        [
            'name' => 'Enquiry List',
            'keywords' => ['enquiry'],
            'route' => route('seller.enquiry.list'),
            'icon' => '📨'
        ],

        // FINANCE
        [
            'name' => 'Earnings Dashboard',
            'keywords' => ['finance','earnings'],
            'route' => route('seller.financedashboard'),
            'icon' => '💳',
            'permission' => (!$isStaff || $canViewFinance)
        ],
        [
            'name' => 'Transaction History',
            'keywords' => ['transactions'],
            'route' => route('seller.finance.transactions'),
            'icon' => '📜',
            'permission' => (!$isStaff || $canViewFinance)
        ],
        [
            'name' => 'Payout Management',
            'keywords' => ['payout'],
            'route' => route('seller.finance.payouts'),
            'icon' => '💵',
            'permission' => (!$isStaff || $canViewFinance)
        ],
        [
            'name' => 'Commission & Fees',
            'keywords' => ['commission','fees'],
            'route' => route('seller.finance.commission'),
            'icon' => '📊',
            'permission' => (!$isStaff || $canViewFinance)
        ],
        [
            'name' => 'Wallet Settlement',
            'keywords' => ['wallet'],
            'route' => route('seller.finance.wallet'),
            'icon' => '👛',
            'permission' => (!$isStaff || $canViewFinance)
        ],

        // ANALYTICS
        [
            'name' => 'Sales Overview',
            'keywords' => ['sales','analytics'],
            'route' => route('seller.analytics.sales'),
            'icon' => '📈'
        ],
        [
            'name' => 'Conversion Funnel',
            'keywords' => ['funnel'],
            'route' => route('seller.analytics.funnel'),
            'icon' => '🔻'
        ],
        [
            'name' => 'Customer Insights',
            'keywords' => ['insights'],
            'route' => route('seller.analytics.customers'),
            'icon' => '🧠'
        ],
        [
            'name' => 'Campaign ROI',
            'keywords' => ['campaign','roi'],
            'route' => route('seller.analytics.campaigns'),
            'icon' => '🎯'
        ],

        // GRIEVANCE
        [
            'name' => 'Grievance',
            'keywords' => ['grievance','complaint'],
            'route' => route('seller.grievance.list'),
            'icon' => '⚠️'
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | MATCH MENU SEARCH
    |--------------------------------------------------------------------------
    */

    foreach ($menuPages as $menu) {

        // 🔐 PERMISSION CHECK
        if (isset($menu['permission']) && $menu['permission'] === false) {
            continue;
        }

        foreach ($menu['keywords'] as $keyword) {

            similar_text($search, $keyword, $percent);

            if (
                str_contains($keyword, $search) ||
                str_contains($search, $keyword) ||
                $percent > 60
            ) {
                $results[] = [
                    'type' => 'menu', // 🔥 VERY IMPORTANT
                    'name' => $menu['name'],
                    'route' => $menu['route'],
                    'icon' => $menu['icon']
                ];
                break;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DATABASE SEARCH (OPTIONAL)
    |--------------------------------------------------------------------------
    */

    // ORDERS
    $orders = \App\Models\Order::where('order_number', 'like', "%$search%")->limit(5)->get();

    foreach ($orders as $order) {
        $results[] = [
            'type' => 'order',
            'name' => 'Order #' . $order->order_number,
            'route' => route('seller.orders.show', $order->id),
            'icon' => '🛒'
        ];
    }

    // CUSTOMERS
    $customers = \App\Models\Customer::where('name', 'like', "%$search%")->limit(5)->get();

    foreach ($customers as $customer) {
        $results[] = [
            'type' => 'customer',
            'name' => $customer->name,
            'route' => route('seller.customer.view', $customer->id),
            'icon' => '👤'
        ];
    }

    // PRODUCTS
    $products = \App\Models\Product::where('name', 'like', "%$search%")->limit(5)->get();

    foreach ($products as $product) {
        $results[] = [
            'type' => 'product',
            'name' => $product->name,
            'route' => route('seller.products.edit', $product->id),
            'icon' => '📦'
        ];
    }

    return response()->json($results);
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
    
}
