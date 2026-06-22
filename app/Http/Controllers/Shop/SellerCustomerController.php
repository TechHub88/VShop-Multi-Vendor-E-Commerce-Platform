<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\ProductReview;
use App\Models\Category;
use App\Models\Product;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Address;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceSubCategory;
use App\Models\CustomerLog;

use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Notification; // ✅ ADDED

class SellerCustomerController extends Controller
{

/* ================= COMMON NOTIFY ================= */
private function notify($type, $message, $route = null)
{
    try {
        Notification::create([
            'seller_id' => auth('seller')->id(),
            'type' => $type,
            'message' => $message,
            'link' => $route ? route($route) : null,
        ]);
    } catch (\Exception $e) {
        \Log::error($e->getMessage());
    }
}


public function mis()
{
    $sellerId = auth()->guard('seller')->id();

    /* ================= KPI ================= */

    // Total customers
    $totalCustomers = Customer::whereHas('orders', function($q) use ($sellerId){
        $q->where('seller_id', $sellerId);
    })->count();

    // Returning customers
    $returningCustomers = Customer::whereHas('orders', function($q) use ($sellerId){
        $q->where('seller_id', $sellerId);
    })->count();

    // New customers
    $newCustomers = Customer::whereHas('orders', function($q) use ($sellerId){
        $q->where('seller_id', $sellerId)
          ->whereMonth('created_at', now()->month)
          ->whereYear('created_at', now()->year);
    })->count();

    // Bad reviews
    $badReviews = ProductReview::where('seller_id',$sellerId)
        ->where('rating','<=',2)
        ->count();


    /* ================= MONTHLY ================= */

    $months = [];
    $monthlyCustomers = [];
    $newCustomersMonthly = [];
    $returningCustomersMonthly = [];

    for ($i = 1; $i <= 12; $i++) {

        $months[] = date('M', mktime(0,0,0,$i,1));

        // TOTAL
        $totalMonth = Customer::whereHas('orders', function($q) use ($sellerId, $i){
            $q->where('seller_id',$sellerId)
              ->whereMonth('created_at','<=',$i)
              ->whereYear('created_at', now()->year);
        })->count();

        // NEW
        $newMonth = Customer::whereHas('orders', function($q) use ($sellerId, $i){
            $q->where('seller_id',$sellerId)
              ->whereMonth('created_at',$i)
              ->whereYear('created_at', now()->year);
        })->count();

        // RETURNING
        $returnMonth = Customer::whereHas('orders', function($q) use ($sellerId, $i){
            $q->where('seller_id',$sellerId)
              ->whereMonth('created_at',$i)
              ->whereYear('created_at', now()->year);
        })->count();

        $monthlyCustomers[] = $totalMonth;
        $newCustomersMonthly[] = $newMonth;
        $returningCustomersMonthly[] = $returnMonth;
    }


    /* ================= TOP / BOTTOM CUSTOMERS ================= */

    $topCustomers = Customer::withCount([
            'orders' => function($q) use ($sellerId){
                $q->where('seller_id',$sellerId);
            }
        ])
        ->orderByDesc('orders_count')
        ->take(5)
        ->get();

    $bottomCustomers = Customer::withCount([
            'orders' => function($q) use ($sellerId){
                $q->where('seller_id',$sellerId);
            }
        ])
        ->orderBy('orders_count')
        ->take(5)
        ->get();


    /* ================= LOCATION ================= */

    $topLocation = \DB::table('orders')
        ->join('addresses','orders.delivery_address_id','=','addresses.id')
        ->where('orders.seller_id',$sellerId)
        ->select(
            'addresses.state',
            \DB::raw('COUNT(*) as total')
        )
        ->groupBy('addresses.state')
        ->orderByDesc('total')
        ->take(5)
        ->get();


    /* ================= REVIEWS ================= */

    $topReviews = ProductReview::where('seller_id',$sellerId)
        ->orderByDesc('rating')
        ->take(5)
        ->get();

    $badReviewList = ProductReview::where('seller_id',$sellerId)
        ->where('rating','<=',2)
        ->orderBy('rating')
        ->take(5)
        ->get();


    /* ================= RATING INSIGHTS ================= */

    $topRated = ProductReview::where('seller_id',$sellerId)
        ->whereNotNull('rating')
        ->select(
            \DB::raw("
                CASE
                    WHEN customer_id IS NOT NULL
                    THEN customer_id
                    ELSE user_name
                END as grp
            "),
            'user_name',
            \DB::raw('AVG(rating) as rating')
        )
        ->groupBy('grp','user_name')
        ->orderByDesc('rating')
        ->take(5)
        ->get();


    $lowRated = ProductReview::where('seller_id',$sellerId)
        ->whereNotNull('rating')
        ->select(
            \DB::raw("
                CASE
                    WHEN customer_id IS NOT NULL
                    THEN customer_id
                    ELSE user_name
                END as grp
            "),
            'user_name',
            \DB::raw('AVG(rating) as rating')
        )
        ->groupBy('grp','user_name')
        ->orderBy('rating')
        ->take(5)
        ->get();


    /* ================= STAR WISE RATING ================= */

    $fiveStar  = ProductReview::where('seller_id',$sellerId)->where('rating',5)->count();
    $fourStar  = ProductReview::where('seller_id',$sellerId)->where('rating',4)->count();
    $threeStar = ProductReview::where('seller_id',$sellerId)->where('rating',3)->count();
    $twoStar   = ProductReview::where('seller_id',$sellerId)->where('rating',2)->count();
    $oneStar   = ProductReview::where('seller_id',$sellerId)->where('rating',1)->count();


    /* ================= INACTIVE ================= */

    $inactiveCustomers = max(0, $totalCustomers - $returningCustomers);


    /* ================= RETURN ================= */

    return view('vshop.seller.customer.mis', compact(

        'totalCustomers',
        'returningCustomers',
        'newCustomers',
        'badReviews',

        'months',
        'monthlyCustomers',
        'newCustomersMonthly',
        'returningCustomersMonthly',

        'topCustomers',
        'bottomCustomers',

        'topLocation',

        'inactiveCustomers',

        'topReviews',
        'badReviewList',

        'topRated',
        'lowRated',

        'fiveStar',
        'fourStar',
        'threeStar',
        'twoStar',
        'oneStar'
    ));
}

public function list(Request $request)
{
    $locations = Address::select('city')
        ->whereNotNull('city')
        ->distinct()
        ->pluck('city');

    $categories = MarketplaceCategory::all();

    $subcategories = MarketplaceSubCategory::all();

    $query = Customer::query();

    /*
    |--------------------------------------------------------------------------
    | 🔍 SEARCH
    |--------------------------------------------------------------------------
    */
    $search = trim($request->q ?? $request->search);

    if ($search) {

        $query->where(function ($q) use ($search) {

            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('gstin', 'like', "%{$search}%");

        });

    }

    /*
    |--------------------------------------------------------------------------
    | 📍 LOCATION
    |--------------------------------------------------------------------------
    */
    if ($request->location) {

        $query->whereHas('addresses', function ($q) use ($request) {

            $q->where('city', $request->location);

        });

    }

    /*
    |--------------------------------------------------------------------------
    | 🧠 CATEGORY / SUBCATEGORY / PRODUCT / TYPE
    |--------------------------------------------------------------------------
    */
    if (
        $request->category ||
        $request->subcategory ||
        $request->product ||
        $request->type
    ) {

        $query->whereHas('orders', function ($q) use ($request) {

            $q->whereHas('items', function ($q2) use ($request) {

                // PRODUCT
                if ($request->product) {

                    $q2->where(
                        'product_id',
                        $request->product
                    );

                }

                // CATEGORY / SUBCATEGORY / TYPE
                $q2->whereHas('product', function ($q3) use ($request) {

                    if ($request->category) {

                        $q3->where(
                            'category_id',
                            $request->category
                        );

                    }

                    if ($request->subcategory) {

                        $q3->where(
                            'subcategory_id',
                            $request->subcategory
                        );

                    }

                    if ($request->type) {

                        $q3->where(
                            'type',
                            $request->type
                        );

                    }

                });

            });

        });

    }

    /*
    |--------------------------------------------------------------------------
    | 🟢 STATUS FILTER
    |--------------------------------------------------------------------------
    */

    // ACTIVE
    if ($request->status == 'active') {

        $query->where('is_blocked', 0);

    }
    
    if ($request->filled('source')) {
    $query->where('source', $request->source);
}

    // BLOCKED
    if ($request->status == 'blocked') {

        $query->where('is_blocked', 1);

    }

    // RISK
    if ($request->status == 'risk') {

        $query->where('risk_level', '>', 70);

    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 ACCOUNT STATUS FILTER FIX
    |--------------------------------------------------------------------------
    */

    if ($request->account_status == 'active') {

        $query->where('is_blocked', 0);

    }

    if ($request->account_status == 'blocked') {

        $query->where('is_blocked', 1);

    }

    /*
    |--------------------------------------------------------------------------
    | 📊 SORTING
    |--------------------------------------------------------------------------
    */

    // MOST PURCHASE
    if ($request->sort == 'most_purchase') {

        $query->withCount('orders')
              ->orderByDesc('orders_count');

    }

    // MOST REVIEW
    if ($request->sort == 'most_review') {

        $query->withCount('reviews')
              ->orderByDesc('reviews_count');

    }

    // NEWEST
    if ($request->sort == 'new') {

        $query->orderByDesc('created_at');

    }

    // OLDEST
    if ($request->sort == 'old') {

        $query->orderBy('created_at');

    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 DEFAULT DESCENDING ORDER
    |--------------------------------------------------------------------------
    */

    if (!$request->sort) {

        $query->orderByDesc('customers.id');

    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 UNIQUE CUSTOMERS
    |--------------------------------------------------------------------------
    */

    $query->select('customers.*')->distinct();

    /*
    |--------------------------------------------------------------------------
    | FINAL DATA
    |--------------------------------------------------------------------------
    */

    $sellerId = auth()->guard('seller')->id();

    $customers = $query
        ->withCount([
            'orders',
            'reviews'
        ])
        ->with([
            'orders.items.product',
            'reviews',
            'addresses',
            'enquiries' => function($q) use ($sellerId) {
                $q->where('seller_id', $sellerId)->latest();
            }
        ])
        ->paginate(15)
        ->appends($request->all());

    $totalCustomers = $customers->total();

    return view(
        'vshop.seller.customer.list',
        compact(
            'customers',
            'locations',
            'categories',
            'subcategories',
            'totalCustomers'
        )
    );
}
/* =============================== CREATE ============================== */

public function create()
{
    // 🔥 MASTER DATA
    $countries = \App\Models\Country::pluck('name','id');
    $states    = \App\Models\State::pluck('name','id');
    $cities    = \App\Models\City::pluck('name','id');

    // 🔥 EMPTY CUSTOMER
    $customer = new \App\Models\Customer();

    /*
    |--------------------------------------------------------------------------
    | MODULE TYPE LOGIC (SAME AS PRODUCT CONTROLLER)
    |--------------------------------------------------------------------------
    */

    $panels = session('selected_panels', []);

    $customerTypes = [];

    // ================= PRODUCT PANEL =================
    if(
        in_array('product', $panels)
    ){

        $customerTypes = array_merge($customerTypes, [

            'Retail'      => 'Retail',
            'Wholesale'   => 'Wholesale',
            'Distributor' => 'Distributor',

        ]);
    }

    // ================= TRAVEL PANEL =================
    if(
        in_array('travel', $panels)
    ){

        $customerTypes = array_merge($customerTypes, [

            'Agents / guides' => 'Agents / guides',
            'Others'          => 'Others',

        ]);
    }

    // ================= DEFAULT =================
    if(empty($customerTypes)){

        $customerTypes = [

            'Retail'      => 'Retail',
            'Wholesale'   => 'Wholesale',
            'Distributor' => 'Distributor',

        ];
    }

    // 🔥 DEFAULT EMPTY ADDRESS BLOCK
    $customer->billingAddresses = collect([
        (object)[
            'id' => null,
            'country_id' => null,
            'state_id' => null,
            'city_id' => null,
            'pincode' => null,
            'address_line1' => null,
        ]
    ]);

    $customer->deliveryAddresses = collect([
        (object)[
            'id' => null,
            'country_id' => null,
            'state_id' => null,
            'city_id' => null,
            'pincode' => null,
            'address_line1' => null,
        ]
    ]);

    return view('vshop.seller.customer.create', compact(
        'customer',
        'countries',
        'states',
        'cities',
        'customerTypes'
    ));
}


public function store(Request $request)
{
    // ================= VALIDATION =================
    $request->validate([

        'name'   => 'required|string|max:255',
        'mobile' => 'required|digits:10',

        'email'  => 'nullable|email|unique:customers,email',

        'company_name' => 'nullable|string|max:255',
        'gstin'        => 'nullable|string|max:255',
        'gender'       => 'nullable|string|max:50',
        'dob'          => 'nullable|date',
        'type'         => 'nullable|string|max:100',

        'billing_country.0' => 'required',
        'billing_state.0'   => 'required',
        'billing_city.0'    => 'required',
        'billing_pincode.0' => 'required',
        'billing_address.0' => 'required',

        'delivery_country.0' => 'required',
        'delivery_state.0'   => 'required',
        'delivery_city.0'    => 'required',
        'delivery_pincode.0' => 'required',
        'delivery_address.0' => 'required',

        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    // ================= CUSTOMER =================
    $customer = new Customer();

    $customer->name  = $request->name;
    $customer->phone = $request->mobile;
    $customer->email = $request->email;

    $customer->company_name = $request->company_name;
    $customer->gstin        = $request->gstin;
    $customer->gender       = $request->gender;
    $customer->dob          = $request->dob;
    $customer->type         = $request->type;

    // ✅ NEW LOGIC
    $customer->seller_id = auth('seller')->id(); // seller who created
    $customer->source    = 'manual';

    // ================= PHOTO =================
    if ($request->hasFile('photo')) {

        $file = $request->file('photo');

        $filename = time().'_'.str_replace(' ','_',$file->getClientOriginalName());

        $path = 'uploads/customer/';

        if (!file_exists(public_path($path))) {
            mkdir(public_path($path), 0777, true);
        }

        $file->move(public_path($path), $filename);

        $customer->profile_picture = $path.$filename;
    }

    $customer->save();

    // ================= BILLING =================
    if ($request->billing_country) {

        foreach ($request->billing_country as $i => $countryId) {

            if (empty($countryId)) continue;

            $country = Country::find($countryId);
            $state   = State::find($request->billing_state[$i] ?? null);
            $city    = City::find($request->billing_city[$i] ?? null);

            $pincode = $request->billing_pincode[$i] ?? null;

            if ($pincode == 'other' && isset($request->manual_billing_pincode[$i])) {
                $pincode = $request->manual_billing_pincode[$i];
            }

            Address::create([
                'customer_id' => $customer->id,
                'type' => 'billing',
                'name'  => $request->name,
                'email' => $request->email,
                'phone' => $request->mobile,
                'country' => $country->name ?? null,
                'state'   => $state->name ?? null,
                'city'    => $city->name ?? null,
                'pincode' => $pincode,
                'address_line1' => $request->billing_address[$i] ?? null,
            ]);
        }
    }

    // ================= DELIVERY =================
    if ($request->delivery_country) {

        foreach ($request->delivery_country as $i => $countryId) {

            if (empty($countryId)) continue;

            $country = Country::find($countryId);
            $state   = State::find($request->delivery_state[$i] ?? null);
            $city    = City::find($request->delivery_city[$i] ?? null);

            $pincode = $request->delivery_pincode[$i] ?? null;

            if ($pincode == 'other' && isset($request->manual_delivery_pincode[$i])) {
                $pincode = $request->manual_delivery_pincode[$i];
            }

            Address::create([
                'customer_id' => $customer->id,
                'type' => 'delivery',
                'name'  => $request->name,
                'email' => $request->email,
                'phone' => $request->mobile,
                'country' => $country->name ?? null,
                'state'   => $state->name ?? null,
                'city'    => $city->name ?? null,
                'pincode' => $pincode,
                'address_line1' => $request->delivery_address[$i] ?? null,
            ]);
        }
    }

    // ================= NOTIFY =================
    $this->notify('customer','Customer Created','seller.customer.list');

    return redirect()
        ->route('seller.customer.list')
        ->with('success','Customer Created Successfully');
}



public function update(Request $request, $id)
{
    // ================= VALIDATION =================
    $request->validate([

        'name'   => 'required|string|max:255',
        'mobile' => 'required|digits:10',

        'email'  => 'nullable|email|unique:customers,email,' . $id,

        'company_name' => 'nullable|string|max:255',
        'gstin'        => 'nullable|string|max:255',
        'gender'       => 'nullable|string|max:50',
        'dob'          => 'nullable|date',
        'type'         => 'nullable|string|max:100',

        'billing_country.0' => 'required',
        'billing_state.0'   => 'required',
        'billing_city.0'    => 'required',
        'billing_pincode.0' => 'required',
        'billing_address.0' => 'required',

        'delivery_country.0' => 'required',
        'delivery_state.0'   => 'required',
        'delivery_city.0'    => 'required',
        'delivery_pincode.0' => 'required',
        'delivery_address.0' => 'required',

        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    // ================= CUSTOMER =================
    $customer = Customer::findOrFail($id);

    $customer->name  = $request->name;
    $customer->phone = $request->mobile;
    $customer->email = $request->email;

    $customer->company_name = $request->company_name;
    $customer->gstin        = $request->gstin;
    $customer->gender       = $request->gender;
    $customer->dob          = $request->dob;
    $customer->type         = $request->type;

    // ✅ KEEP EXISTING SOURCE / SELLER_ID (DO NOT CHANGE)
    if (!$customer->seller_id) {
        $customer->source = 'automatic';
    } else {
        $customer->source = 'manual';
    }

    // ================= PHOTO =================
    if ($request->hasFile('photo')) {

        if (!empty($customer->profile_picture) && file_exists(public_path($customer->profile_picture))) {
            unlink(public_path($customer->profile_picture));
        }

        $file = $request->file('photo');

        $filename = time().'_'.str_replace(' ','_',$file->getClientOriginalName());

        $path = 'uploads/customer/';

        if (!file_exists(public_path($path))) {
            mkdir(public_path($path), 0777, true);
        }

        $file->move(public_path($path), $filename);

        $customer->profile_picture = $path.$filename;
    }

    if ($request->remove_photo == 1) {
        if (!empty($customer->profile_picture) && file_exists(public_path($customer->profile_picture))) {
            unlink(public_path($customer->profile_picture));
        }
        $customer->profile_picture = null;
    }

    $customer->save();

    // ================= RESET ADDRESSES =================
    Address::where('customer_id', $customer->id)->delete();

    // ================= BILLING =================
    if ($request->billing_country) {

        foreach ($request->billing_country as $i => $countryId) {

            if (empty($countryId)) continue;

            $country = Country::find($countryId);
            $state   = State::find($request->billing_state[$i] ?? null);
            $city    = City::find($request->billing_city[$i] ?? null);

            $pincode = $request->billing_pincode[$i] ?? null;

            if ($pincode == 'other' && isset($request->manual_billing_pincode[$i])) {
                $pincode = $request->manual_billing_pincode[$i];
            }

            Address::create([
                'customer_id' => $customer->id,
                'type' => 'billing',
                'name'  => $request->name,
                'email' => $request->email,
                'phone' => $request->mobile,
                'country' => $country->name ?? null,
                'state'   => $state->name ?? null,
                'city'    => $city->name ?? null,
                'pincode' => $pincode,
                'address_line1' => $request->billing_address[$i] ?? null,
            ]);
        }
    }

    // ================= DELIVERY =================
    if ($request->delivery_country) {

        foreach ($request->delivery_country as $i => $countryId) {

            if (empty($countryId)) continue;

            $country = Country::find($countryId);
            $state   = State::find($request->delivery_state[$i] ?? null);
            $city    = City::find($request->delivery_city[$i] ?? null);

            $pincode = $request->delivery_pincode[$i] ?? null;

            if ($pincode == 'other' && isset($request->manual_delivery_pincode[$i])) {
                $pincode = $request->manual_delivery_pincode[$i];
            }

            Address::create([
                'customer_id' => $customer->id,
                'type' => 'delivery',
                'name'  => $request->name,
                'email' => $request->email,
                'phone' => $request->mobile,
                'country' => $country->name ?? null,
                'state'   => $state->name ?? null,
                'city'    => $city->name ?? null,
                'pincode' => $pincode,
                'address_line1' => $request->delivery_address[$i] ?? null,
            ]);
        }
    }

    // ================= NOTIFY =================
    $this->notify('customer','Customer Updated','seller.customer.list');

    return redirect()
        ->route('seller.customer.list')
        ->with('success','Customer Updated Successfully');
}

public function bulkPage()
{
    return view('vshop.seller.customer.bulk-upload');
}


public function downloadSample()
{
    $fileName = "customer_sample.csv";

    $headers = [
        "name",
        "mobile",
        "email"
    ];

    $callback = function() use ($headers) {

        $file = fopen('php://output', 'w');

        // header
        fputcsv($file, $headers);

        // sample row
        fputcsv($file, [
            "John Doe",
            "9876543210",
            "john@example.com"
        ]);

        fclose($file);
    };

    return response()->stream($callback, 200, [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName"
    ]);
}

public function bulkUpload(Request $request)
{
    $request->validate([
        'bulk_file' => 'required|file|mimes:csv,txt'
    ]);

    if (!$request->hasFile('bulk_file')) {
        return back()->withErrors([
            'bulk_file' => 'Please upload a CSV file'
        ]);
    }

    $file = $request->file('bulk_file');
    $handle = fopen($file->getRealPath(), 'r');

    if ($handle === false) {
        return back()->withErrors([
            'bulk_file' => 'Unable to read file'
        ]);
    }

    $inserted = 0;
    $duplicates = [];
    $rowNumber = 0;

    // ✅ Skip header row
    fgetcsv($handle);

    while (($row = fgetcsv($handle, 1000, ',')) !== false) {

        $rowNumber++;

        // ✅ Skip empty rows
        if (empty(array_filter($row))) {
            continue;
        }

        $name  = trim($row[0] ?? '');
        $phone = trim($row[1] ?? '');
        $email = trim($row[2] ?? '');

        // ✅ Basic validation
        if (!$name || !$email) {
            continue;
        }

        // ✅ Check duplicate email
        if (Customer::where('email', $email)->exists()) {
            $duplicates[] = "Row {$rowNumber}: {$email}";
            continue;
        }

        try {
            Customer::create([
                'name'  => $name,
                'phone' => $phone,
                'email' => $email,
            ]);

            $inserted++;

        } catch (\Exception $e) {
            // Extra safety (in case DB still throws error)
            $duplicates[] = "Row {$rowNumber}: {$email}";
            continue;
        }
    }

    fclose($handle);

    // ✅ If duplicates found → show error under input
    if (!empty($duplicates)) {
        return back()->withErrors([
            'bulk_file' => 'Duplicate emails skipped: ' . implode(', ', $duplicates)
        ])->with('success', "$inserted customers uploaded successfully");
    }

    return back()->with('success', "$inserted customers uploaded successfully");
}

public function block($id)
{
    $customer = Customer::findOrFail($id);
    $customer->is_blocked = 1;
    $customer->save();

    return back()->with('success', 'Customer Blocked');
}

public function unblock($id)
{
    $customer = Customer::findOrFail($id);
    $customer->is_blocked = 0;
    $customer->save();

    return back()->with('success', 'Customer Unblocked');
}

public function show($id)
{
    $customer = Customer::findOrFail($id);

    // 🔥 GET ADDRESSES
    $billingAddresses = Address::where('customer_id', $id)
        ->where('type', 'billing')
        ->get();

    $deliveryAddresses = Address::where('customer_id', $id)
        ->where('type', 'delivery')
        ->get();

    return view('vshop.seller.customer.show', compact(
        'customer',
        'billingAddresses',
        'deliveryAddresses'
    ));
}

public function edit($id)
{
    $customer = Customer::with('addresses')->findOrFail($id);

    $countries = Country::pluck('name','id');

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER TYPE LOGIC
    |--------------------------------------------------------------------------
    */

    $panels = session('selected_panels', []);

    $customerTypes = [];

    // ================= PRODUCT PANEL =================
    if(in_array('product', $panels)){

        $customerTypes = array_merge($customerTypes, [

            'Retail'      => 'Retail',
            'Wholesale'   => 'Wholesale',
            'Distributor' => 'Distributor',

        ]);
    }

    // ================= TRAVEL PANEL =================
    if(in_array('travel', $panels)){

        $customerTypes = array_merge($customerTypes, [

            'Agents / guides' => 'Agents / guides',
            'Others'          => 'Others',

        ]);
    }

    // ================= DEFAULT =================
    if(empty($customerTypes)){

        $customerTypes = [

            'Retail'      => 'Retail',
            'Wholesale'   => 'Wholesale',
            'Distributor' => 'Distributor',

        ];
    }

    // ================= BILLING =================
    $billing = $customer->addresses
        ->where('type','billing')
        ->values();

    foreach ($billing as $b) {

        // COUNTRY ID
        $country = Country::where('name',$b->country)->first();
        $b->country_id = $country->id ?? null;

        // STATE ID
        $state = State::where('name',$b->state)->first();
        $b->state_id = $state->id ?? null;

        // CITY ID
        $city = City::where('name',$b->city)->first();
        $b->city_id = $city->id ?? null;
    }

    // ================= DELIVERY =================
    $delivery = $customer->addresses
        ->where('type','delivery')
        ->values();

    foreach ($delivery as $d) {

        $country = Country::where('name',$d->country)->first();
        $d->country_id = $country->id ?? null;

        $state = State::where('name',$d->state)->first();
        $d->state_id = $state->id ?? null;

        $city = City::where('name',$d->city)->first();
        $d->city_id = $city->id ?? null;
    }

    return view('vshop.seller.customer.create', compact(

        'customer',
        'countries',
        'billing',
        'delivery',
        'customerTypes'

    ));
}
public function delete($id)
{
    $customer = Customer::findOrFail($id);
    $customer->delete();

    return back()->with('success','Customer Deleted successfully');
}

public function getStates($country_id)
{
    return \App\Models\State::where('country_id',$country_id)
        ->pluck('name','id');
}

public function getCities($state_id)
{
    return \App\Models\City::where('state_id',$state_id)
        ->pluck('name','id');
}

public function saveLog(Request $request)
{
    try {

        $request->validate([
            'customer_id' => 'required',
            'log_date'    => 'required',
            'notes'       => 'required'
        ]);

        CustomerLog::create([
            'customer_id' => $request->customer_id,
            'log_date'    => $request->log_date,
            'channel'     => $request->channel,
            'type'        => $request->type,
            'notes'       => $request->notes,
            'handled_by'  => $request->handled_by
        ]);

        return response()->json([
            'status' => true,
            'msg' => 'Log saved successfully'
        ]);


    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'msg' => $e->getMessage()
        ]);
    }
}

public function getSubcategoriesAjax($category_id)
{
    try {

        $data = MarketplaceSubCategory::where('category_id', $category_id)->get();

        return response()->json($data);

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'msg' => $e->getMessage()
        ]);
    }
}

public function getLogs($id)
{
    $logs = CustomerLog::where('customer_id', $id)
        ->latest()
        ->get()
        ->map(function ($log) {
            return [
                'id'         => $log->id,
                'date'       => $log->log_date,
                'channel'    => $log->channel,
                'type'       => $log->type,
                'notes'      => $log->notes,
                'handled_by' => $log->handled_by,
                'created_at' => $log->created_at->format('Y-m-d H:i'),
            ];
        });

    return response()->json($logs);
}

public function cityPincode($id)
{
    $city = City::find($id);

    return response()->json([
        'pincode' => $city->pincode ?? null
    ]);
}
}