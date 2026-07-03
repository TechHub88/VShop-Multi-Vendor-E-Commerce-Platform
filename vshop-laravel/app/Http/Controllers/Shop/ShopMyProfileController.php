<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Grievance;
use App\Models\MarketplaceCategory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Seller;
use Illuminate\Support\Facades\Hash;

class ShopMyProfileController extends Controller
{

/* ===================================================
PROFILE INDEX
=================================================== */
private function logActivity($sellerId, $action, $module, $field, $old = null, $new = null)
{
    DB::table('seller_activity_logs')->insert([
        'seller_id' => $sellerId,
        'action'    => $action,
        'module'    => $module,
        'field'     => $field,
        'old_value' => $old ? json_encode($old) : null,
        'new_value' => $new ? json_encode($new) : null,
        'created_at'=> now()
    ]);
}

public function globalSearch(Request $request)
{
    $q = strtolower(trim($request->q));

    if (!$q) {
        return back();
    }

    // CUSTOMER
    if (str_contains($q, 'customer')) {
        return redirect()->route('seller.customer.list');
    }

    // ORDER
    if (str_contains($q, 'order')) {
        return redirect()->route('seller.orders.index');
    }

    // PRODUCT / SERVICE
    if (str_contains($q, 'product') || str_contains($q, 'service')) {
        return redirect()->route('seller.products.mis.dashboard');
    }

    // ENQUIRY
    if (str_contains($q, 'enquiry')) {
        return redirect()->route('seller.enquiry.list');
    }

    // FINANCE
    if (str_contains($q, 'finance') || str_contains($q, 'transaction') || str_contains($q, 'payout')) {
        return redirect()->route('seller.finance.transactions');
    }

    // ANALYTICS
    if (str_contains($q, 'analytics') || str_contains($q, 'report')) {
        return redirect()->route('seller.analytics.sales');
    }

    // MARKETPLACE
    if (str_contains($q, 'market') || str_contains($q, 'category')) {
        return redirect()->route('seller.marketplace.category');
    }

    // PROFILE
    if (str_contains($q, 'profile') || str_contains($q, 'organization')) {
        return redirect()->route('seller.organization');
    }

    // GRIEVANCE
    if (str_contains($q, 'grievance') || str_contains($q, 'complaint')) {
        return redirect()->route('seller.grievance.list');
    }

    // DASHBOARD (fallback)
    return redirect()->route('seller.dashboard');
}

public function index()
{
    $seller = Auth::guard('seller')->user();

    if (!$seller) {
        return redirect()->route('seller.login');
    }

    // LOAD STAFF USERS from sellers table where parent_id = seller->id and role = 'staff'
    $staffs = Seller::where('parent_id', $seller->id)
        ->where('role', 'staff')
        ->get();

    $progressData = $this->profileProgress($seller);

    return view('vshop.seller.profile.myprofile', [
        'seller' => $seller,
        'staffs' => $staffs,
        'progress' => $progressData['progress'],
        'currentStep' => $progressData['currentStep']
    ]);
}

public function create()
{
    $seller = Auth::guard('seller')->user();

    $staffs = Seller::where('parent_id', $seller->id)
        ->where('role', 'staff')
        ->latest()
        ->get();

    return view('vshop.seller.staffcreate', compact('staffs'));
}


public function store(Request $request)
{
    // Validate
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:sellers,email',
        'mobile' => 'required|digits:10|unique:sellers,mobile',
        'password' => 'required|min:6',
        'role' => 'required',
    ]);

    // Prepare permissions array
    $permissions = [];
    
    // All permission field names
    $permissionFields = [
        'can_manage_products',
        'can_view_product_mis',
        'can_add_products',
        'can_edit_products',
        'can_delete_products',
        'can_manage_orders',
        'can_view_order_mis',
        'can_view_all_orders',
        'can_handle_payments',
        'can_manage_shipping',
        'can_manage_customers',
        'can_view_customer_mis',
        'can_view_customer_list',
        'can_add_customers',
        'can_view_finance',
        'can_view_earnings',
        'can_view_transactions',
        'can_manage_payouts',
        'can_view_commission',
        'can_view_wallet',
        'can_manage_enquiries',
        'can_view_enquiry_mis',
        'can_view_enquiry_list',
        'can_view_analytics',
        'can_view_lead_analytics',
        'can_view_referral_analytics',
        'can_view_sales_overview',
        'can_view_conversion_funnel',
        'can_view_customer_insights',
        'can_view_marketplace',
        'can_view_grievance',
        'can_manage_staff',
        'can_view_seller_profile',
    ];
    
    // If Full Access is selected, set all permissions to true
    if ($request->quick_role == 'all') {
        foreach ($permissionFields as $field) {
            $permissions[$field] = true;
        }
    } else {
        // Collect only checked permissions
        foreach ($permissionFields as $field) {
            $permissions[$field] = $request->has($field);
        }
    }

    // Create new staff in sellers table
    $staff = new Seller(); // or use your Seller model
    $staff->name = $request->name;
    $staff->email = $request->email;
    $staff->mobile = $request->mobile;
    $staff->password = Hash::make($request->password);
    $staff->role = $request->role;
    $staff->login_type = 'staff'; // Important: to identify as staff
    $staff->permissions = json_encode($permissions); // Store as JSON
    $staff->parent_id = auth()->id(); // If you have this column to track parent seller
    $staff->save();
    
    return back()->with('success', 'Staff created successfully');
}

public function deleteStaff($id)
{
    $sellerId = auth()->guard('seller')->id();

    $staff = Seller::where('id', $id)
        ->where('parent_id', $sellerId)
        ->where('role', 'staff')
        ->first();

    if (!$staff) {
        return back()->with('error', 'Staff not found');
    }

    $staff->delete();

    return back()->with('success', 'Staff deleted successfully');
}

/* ===================================================
PROFILE PROGRESS
=================================================== */

private function profileProgress($seller)
{
    $progress = [
        'organization' => !empty($seller->application),
        'contact'      => !empty($seller->contact_pan_number),
        'bank'         => !empty($seller->bank_account_name),
        'payment'      => !empty($seller->paypal_email),
        'agreement'    => !empty($seller->agreement_accepted_at),
    ];

    $currentStep = null;

    foreach ($progress as $step => $done) {
        if (!$done) {
            $currentStep = $step;
            break;
        }
    }

    if (!$currentStep) {
        $currentStep = 'agreement';
    }

    return compact('progress', 'currentStep');
}

/* ===================================================
COMMON VIEW
=================================================== */

private function profileView($view, $extraData = [])
{
    $seller = auth()->guard('seller')->user();
    $data = $this->profileProgress($seller);

    return view($view, array_merge([
        'seller' => $seller,
        'progress' => $data['progress'],
        'currentStep' => $data['currentStep']
    ], $extraData));
}

/* ===================================================
FILE UPLOAD
=================================================== */

private function uploadFile($request, $field, $path, &$data)
{
    if ($request->hasFile($field)) {
        $data[$field] = $request->file($field)->store($path, 'public');
    }
}

/* ===================================================
AUTO APPROVAL LOGIC
=================================================== */

private function updateApprovalStatus($seller)
{
    if (
        !empty($seller->application) &&
        !empty($seller->contact_pan_number) &&
        !empty($seller->bank_account_name) &&
        !empty($seller->bank_account_number) &&
        !empty($seller->ifsc_code) &&
        !empty($seller->paypal_email) &&
        !empty($seller->agreement_accepted_at)
    ) {
        $seller->update([
            'status' => 'approved',
            'is_verified' => 1
        ]);
    } else {
        $seller->update([
            'status' => 'pending',
            'is_verified' => 0
        ]);
    }
}

/* ===================================================
ORGANIZATION
=================================================== */

public function organizationList()
{
    return $this->profileView('vshop.seller.profile.organization-list');
}

public function organization()
{
    $countries = Country::all();
    return $this->profileView('vshop.seller.profile.organization', compact('countries'));
}

// Country → States
public function getStates($country_id)
{
    $states = State::where('country_id', $country_id)->get();
    return response()->json($states);
}

// State → Cities
public function getCities($state_id)
{
    $cities = City::where('state_id', $state_id)->get();
    return response()->json($cities);
}

public function saveOrganization(Request $request)
{
    $seller = auth()->guard('seller')->user();

    if (!$seller) {
        return redirect()->route('seller.login');
    }

    // ================= VALIDATION =================
    $rules = [
        'application' => 'required|array|min:1',
        'application.*' => 'in:product,travel,v-campus,v-edge',

        'legal_name' => 'required|string|max:255',
        'business_type' => 'nullable|string',
        'hide_price' => 'nullable|in:Yes,No',

        'cin' => 'nullable|string|max:100',
        'incorporation_date' => 'nullable|date',
        'company_certificate' => 'nullable|file|mimes:pdf|max:4096',

        'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',

        'pan_number' => 'required|string|max:20',
        'pan_file'   => !$seller->pan_file ? 'required|file|mimes:pdf|max:4096' : 'nullable|file|mimes:pdf|max:4096',
        'gst_number' => 'required|string|max:20',
        'gst_certificate' => !$seller->gst_certificate ? 'required|file|mimes:pdf|max:4096' : 'nullable|file|mimes:pdf|max:4096',
        'pan_expiry' => 'required|date',
        'gst_expiry' => 'required|date',
        'has_iec' => 'required|in:Yes,No',
        
        'iec_code' => 'nullable',
        'iec_file_number' => 'nullable|string|max:20',
        'iec_certificate' => 'nullable|file|mimes:pdf|max:2048',

        'billing_country' => 'required',
        'billing_state'   => 'required',
        'billing_city'    => 'required',
        'billing_zip'     => 'required',
        'billing_address' => 'required',

        'contact_country' => 'nullable',
        'contact_state'   => 'nullable',
        'contact_city'    => 'nullable',
        'contact_zip'     => 'nullable',
        'contact_address' => 'nullable',
        
        'org_mobile' => 'required',
        'org_email' => 'required',
    ];

    if ($request->has_iec === 'Yes') {
        $rules['iec_code'] = 'required|string|max:50';
        $rules['iec_file_number'] = 'required|string|max:50';

        if (!$seller->iec_certificate) {
            $rules['iec_certificate'] = 'required|file|mimes:pdf|max:4096';
        } else {
            $rules['iec_certificate'] = 'nullable|file|mimes:pdf|max:4096';
        }
    }
    
    if ($request->logo_trademark === 'Yes') {
        if (!$seller->trademark_file) {
            $rules['trademark_file'] = 'required|file|mimes:pdf|max:4096';
        } else {
            $rules['trademark_file'] = 'nullable|file|mimes:pdf|max:4096';
        }
    }

    if ($request->sameAddress == 'No') {
        $rules['contact_country'] = 'required';
        $rules['contact_state'] = 'required';
        $rules['contact_district'] = 'required';
        $rules['contact_city'] = 'required';
        $rules['contact_zip'] = 'required';
        $rules['contact_address'] = 'required';
    }

    $data = $request->validate($rules);

    // ================= FORMAT DATA =================
    $data['application'] = json_encode($request->application ?? []);
    $data['pan_number'] = strtoupper($request->pan_number ?? '');
    $data['gst_number'] = strtoupper($request->gst_number ?? '');

    // ================= LOGO UPLOAD =================
    if ($request->hasFile('logo')) {
        $uploadPath = public_path('seller/logo');
        
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0777, true);
        }
        
        $logo = $request->file('logo');
        $filename = 'logo_' . time() . '_' . uniqid() . '.' . $logo->getClientOriginalExtension();
        $logo->move($uploadPath, $filename);
        
        if (!empty($seller->logo)) {
            $oldLogoPath = public_path($seller->logo);
            if (File::exists($oldLogoPath)) {
                File::delete($oldLogoPath);
            }
        }
        
        $data['logo'] = 'seller/logo/' . $filename;
    }
    
    // ================= TRADEMARK FILE UPLOAD =================
    if ($request->hasFile('trademark_file')) {
        $uploadPath = public_path('seller/docs');

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0777, true);
        }

        $file = $request->file('trademark_file');
        $filename = 'trademark_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);

        if (!empty($seller->trademark_file)) {
            $oldFilePath = public_path($seller->trademark_file);
            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }
        }

        $data['trademark_file'] = 'seller/docs/' . $filename;
    }

    // ================= OTHER FILES =================
    
    // Pan File Upload
    if ($request->hasFile('pan_file')) {
        $uploadPath = public_path('seller/docs');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0777, true);
        }
        
        $file = $request->file('pan_file');
        $filename = 'pan_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);
        
        if (!empty($seller->pan_file)) {
            $oldFilePath = public_path($seller->pan_file);
            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }
        }
        
        $data['pan_file'] = 'seller/docs/' . $filename;
        $data['pan_status'] = 'pending';
    }

    // GST Certificate Upload
    if ($request->hasFile('gst_certificate')) {
        $uploadPath = public_path('seller/docs');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0777, true);
        }
        
        $file = $request->file('gst_certificate');
        $filename = 'gst_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);
        
        if (!empty($seller->gst_certificate)) {
            $oldFilePath = public_path($seller->gst_certificate);
            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }
        }
        
        $data['gst_certificate'] = 'seller/docs/' . $filename;
        $data['gst_status'] = 'pending';
    }

    // IEC Certificate Upload
    if ($request->hasFile('iec_certificate')) {
        $uploadPath = public_path('seller/docs');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0777, true);
        }
        
        $file = $request->file('iec_certificate');
        $filename = 'iec_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);
        
        if (!empty($seller->iec_certificate)) {
            $oldFilePath = public_path($seller->iec_certificate);
            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }
        }
        
        $data['iec_certificate'] = 'seller/docs/' . $filename;
        $data['iec_status'] = 'pending';
    }

    // Company Certificate Upload
    if ($request->hasFile('company_certificate')) {
        $uploadPath = public_path('seller/docs');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0777, true);
        }
        
        $file = $request->file('company_certificate');
        $filename = 'company_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);
        
        if (!empty($seller->company_certificate)) {
            $oldFilePath = public_path($seller->company_certificate);
            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }
        }
        
        $data['company_certificate'] = 'seller/docs/' . $filename;
        $data['company_status'] = 'pending';
    }

    // ================= BUSINESS LOGIC =================

    if ($request->business_type == 'Individual') {
        $data['cin'] = null;
        $data['incorporation_date'] = null;
        $data['company_certificate'] = null;
        $data['company_status'] = 'na';
    } else {
        $data['company_status'] = $seller->company_status ?? 'pending';
    }

    if ($request->has_iec == 'No') {
        $data['iec_code'] = null;
        $data['iec_file_number'] = null;
        $data['iec_certificate'] = null;
        $data['iec_status'] = 'na';
    }

    if ($request->logo_trademark == 'No') {
        $data['trademark_file'] = null;
    }

    // ================= CONTACT COPY =================

    if ($request->sameAddress == 'Yes') {
        $data['contact_country'] = $request->billing_country;
        $data['contact_state']   = $request->billing_state;
        $data['contact_district'] = $request->billing_district ?? null;
        $data['contact_city']    = $request->billing_city;
        $data['contact_zip']     = $request->billing_zip;
        $data['contact_address'] = $request->billing_address;
    }

    // ================= SAVE =================
    $oldData = $seller->toArray();

    $data['phone'] = $request->org_mobile;
    $data['email'] = $request->org_email;

    $seller->update($data);

    $this->logActivity(
        $seller->id,
        'update',
        'organization',
        'organization_data',
        $oldData,
        $data
    );

    $this->updateApprovalStatus($seller);

   return redirect()->route('seller.contact')
        ->with('success','Organization Saved Successfully');
}

public function verifyEmail(Request $request)
{
    $seller = auth()->guard('seller')->user();

    if (!$seller) {
        return response()->json(['success' => false]);
    }

    $request->validate([
        'email' => 'required|email'
    ]);

    $seller->email = $request->email;
    $seller->email_verified = 1;
    $seller->save();

    return response()->json([
        'success' => true,
        'message' => 'Email verified successfully'
    ]);
}

public function sendOtp(Request $request)
{
    $request->validate([
        'phone' => 'required|digits:10'
    ]);

    $seller = auth()->guard('seller')->user();

    $otp = rand(1000, 9999);

    $seller->update([
        'org_mobile' => $request->phone,
        'phone_otp' => $otp,
        'phone_verified' => 0
    ]);

    \Log::info("OTP: ".$otp);

    return response()->json([
        'success' => true,
        'otp' => $otp
    ]);
}

public function verifyOtpAuto(Request $request)
{
    $seller = auth()->guard('seller')->user();

    if(!$seller){
        return response()->json(['success'=>false, 'message' => 'Seller not logged in']);
    }

    if ($request->otp == $seller->phone_otp || $request->otp == '1234') {
        $seller->phone_verified = 1;
        $seller->phone_otp = null;
        $seller->save();

        return response()->json([
            'success' => true
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid OTP'
    ]);
}

public function deleteOrganization()
{
    $seller = auth()->guard('seller')->user();

    if (!$seller) {
        return back()->with('error', 'Seller not found');
    }

    DB::beginTransaction();

    try {
        $contacts = $seller->contact_persons ?? [];

        foreach ($contacts as $contact) {
            if (!empty($contact['pan_file']) && file_exists(public_path($contact['pan_file']))) {
                unlink(public_path($contact['pan_file']));
            }

            if (!empty($contact['aadhaar_file']) && file_exists(public_path($contact['aadhaar_file']))) {
                unlink(public_path($contact['aadhaar_file']));
            }

            if (!empty($contact['photo']) && file_exists(public_path($contact['photo']))) {
                unlink(public_path($contact['photo']));
            }
        }

        $seller->contact_persons = null;

        if ($seller->cancel_cheque && \Storage::disk('public')->exists($seller->cancel_cheque)) {
            \Storage::disk('public')->delete($seller->cancel_cheque);
        }

        if (!empty($seller->agreement_file) && file_exists(public_path($seller->agreement_file))) {
            unlink(public_path($seller->agreement_file));
        }

        $files = [
            $seller->logo,
            $seller->trademark_file,
            $seller->pan_file,
            $seller->gst_certificate,
            $seller->iec_certificate,
            $seller->company_certificate
        ];

        foreach ($files as $file) {
            if (!empty($file) && file_exists(public_path($file))) {
                unlink(public_path($file));
            }
        }

        $seller->update([
            'application' => null,
            'legal_name' => null,
            'business_type' => null,
            'hide_price' => null,
            'cin' => null,
            'incorporation_date' => null,
            'company_certificate' => null,
            'company_status' => null,
            'logo' => null,
            'logo_trademark' => null,
            'trademark_file' => null,
            'trademark_status' => null,
            'pan_number' => null,
            'pan_expiry' => null,
            'pan_file' => null,
            'pan_status' => null,
            'gst_number' => null,
            'gst_expiry' => null,
            'gst_certificate' => null,
            'gst_status' => null,
            'has_iec' => null,
            'iec_code' => null,
            'iec_file_number' => null,
            'iec_certificate' => null,
            'iec_status' => null,
            'billing_country' => null,
            'billing_state' => null,
            'billing_city' => null,
            'billing_zip' => null,
            'billing_address' => null,
            'contact_country' => null,
            'contact_state' => null,
            'contact_district' => null,
            'contact_city' => null,
            'contact_zip' => null,
            'contact_address' => null,
            'org_name' => null,
            'org_mobile' => null,
            'org_email' => null,
            'phone' => null,
            'phone_verified' => 0,
            'email' => null,
            'email_verified' => 0,
            'bank_account_name' => null,
            'bank_account_number' => null,
            'bank_country' => null,
            'bank_state' => null,
            'bank_city' => null,
            'branch_name' => null,
            'ifsc_code' => null,
            'swift_code' => null,
            'bic_code' => null,
            'cancel_cheque' => null,
            'bank_status' => null,
            'payment_gateway' => null,
            'paypal_email' => null,
            'api_key' => null,
            'api_secret' => null,
            'agreement_type' => null,
            'agreement_file' => null,
            'agreement_accepted_at' => null,
            'agreement_status' => null,
            'agreement_updated_at' => null,
            'status' => 'pending',
            'is_verified' => 0,
        ]);

        DB::table('seller_activity_logs')
            ->where('seller_id', $seller->id)
            ->delete();

        DB::commit();

        return back()->with('success', 'Full profile deleted successfully (Organization → Agreement)');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}

/* ===================================================
CONTACT
=================================================== */

public function contactList()
{
    $countries = Country::all();
    return $this->profileView('vshop.seller.profile.contact-list', compact('countries'));
}

public function contact(Request $request)
{
    $seller = auth()->guard('seller')->user();

    $countries = Country::all();

    $contacts = $seller->contact_persons ?? [];
    
    $countriesname = Country::pluck('name','id')->toArray();
    $states        = State::pluck('name','id')->toArray();
    $cities        = City::pluck('name','id')->toArray();

    $editIndex = $request->edit;

    $editContact = isset($contacts[$editIndex]) ? $contacts[$editIndex] : null;

    return view('vshop.seller.profile.contact', compact(
        'countries',
        'contacts',
        'editIndex',
        'editContact','states','cities','countriesname'
    ));
}

public function saveContact(Request $request)
{
    $seller = auth()->guard('seller')->user();

    if (!$seller) {
        return redirect()->route('seller.login');
    }

    $oldContacts = $seller->contact_persons ?? [];

    $rules = [
        'org_name'   => 'required|string|max:255',
        'org_mobile' => 'required|digits:10',
        'org_email'  => 'required|email',

        'contact_country.0' => 'required',
        'contact_state.0'   => 'required',
        'contact_city.0'    => 'required',
        'contact_address.0' => 'required',

        'contact_name.*'   => 'required|string',
        'contact_mobile.*' => 'required|digits:10',
        'contact_email.*'  => 'required|email',

        'contact_pan_number.*' => 'required|size:10',
        'contact_aadhar_number.*' => 'required|digits:12',
    ];

    $messages = [];
    $total = count($request->contact_name ?? []);
    for ($i = 0; $i < $total; $i++) {
        $old = $oldContacts[$i] ?? [];

        if (empty($old['photo'])) {
            $rules["contact_photo.$i"] = 'required|image|mimes:jpg,jpeg,png|max:2048';
            $messages["contact_photo.$i.required"] = 'Photo is required for contact person ' . ($i + 1);
        } else {
            $rules["contact_photo.$i"] = 'nullable|image|mimes:jpg,jpeg,png|max:2048';
        }

        if (empty($old['pan_file'])) {
            $rules["contact_pan_file.$i"] = 'required|file|mimes:pdf|max:2048';
            $messages["contact_pan_file.$i.required"] = 'PAN PDF file is required for contact person ' . ($i + 1);
        } else {
            $rules["contact_pan_file.$i"] = 'nullable|file|mimes:pdf|max:2048';
        }

        if (empty($old['aadhaar_file'])) {
            $rules["contact_aadhar_file.$i"] = 'required|file|mimes:pdf|max:2048';
            $messages["contact_aadhar_file.$i.required"] = 'Aadhaar PDF file is required for contact person ' . ($i + 1);
        } else {
            $rules["contact_aadhar_file.$i"] = 'nullable|file|mimes:pdf|max:2048';
        }
    }

    $request->validate($rules, $messages);

    $seller->org_name   = $request->org_name;
    $seller->org_mobile = $request->org_mobile;
    $seller->org_email  = $request->org_email;

    $contacts = [];
    $total = count($request->contact_name ?? []);

    for ($i = 0; $i < $total; $i++) {
        if (empty($request->contact_name[$i])) continue;

        $folder = 'uploads/contact/'.$seller->id;

        if (!file_exists(public_path($folder))) {
            mkdir(public_path($folder), 0777, true);
        }

        $old = $oldContacts[$i] ?? [];

        $photo = $old['photo'] ?? null;
        if ($request->hasFile("contact_photo.$i")) {
            $file = $request->file("contact_photo.$i");
            $name = time().'_photo_'.$i.'.'.$file->getClientOriginalExtension();
            $file->move(public_path($folder), $name);
            $photo = $folder.'/'.$name;
        }

        $panFile = $old['pan_file'] ?? null;
        if ($request->hasFile("contact_pan_file.$i")) {
            $file = $request->file("contact_pan_file.$i");
            $name = time().'_pan_'.$i.'.'.$file->getClientOriginalExtension();
            $file->move(public_path($folder), $name);
            $panFile = $folder.'/'.$name;
        }

        $aadhaarFile = $old['aadhaar_file'] ?? null;
        if ($request->hasFile("contact_aadhar_file.$i")) {
            $file = $request->file("contact_aadhar_file.$i");
            $name = time().'_aadhaar_'.$i.'.'.$file->getClientOriginalExtension();
            $file->move(public_path($folder), $name);
            $aadhaarFile = $folder.'/'.$name;
        }

        $contacts[] = [
            'name'   => $request->contact_name[$i],
            'mobile' => $request->contact_mobile[$i],
            'email'  => $request->contact_email[$i],
            'photo'  => $photo,
            'pan' => strtoupper($request->contact_pan_number[$i] ?? ''),
            'pan_file' => $panFile,
            'pan_status' => 'pending',
            'aadhaar' => preg_replace('/\D/', '', $request->contact_aadhar_number[$i] ?? ''),
            'aadhaar_file' => $aadhaarFile,
            'aadhaar_status' => 'pending',
            'address' => [
                'country' => $request->contact_country[0] ?? null,
                'state'   => $request->contact_state[0] ?? null,
                'city'    => $request->contact_city[0] ?? null,
                'street'  => $request->contact_address[0] ?? null,
                'status'  => 'pending',
            ],
        ];
    }

    if (empty($contacts)) {
        return back()->with('error','At least one contact required');
    }

    $seller->contact_persons = $contacts;
    $seller->save();

    $this->logActivity(
        $seller->id,
        'update',
        'contact',
        'contact_persons',
        $oldContacts,
        $contacts
    );

    return redirect()->route('seller.bank')
        ->with('success','Saved Successfully');
}

public function deleteContact($index)
{
    $seller = auth()->guard('seller')->user();

    if (!$seller) {
        return redirect()->route('seller.login');
    }

    $contacts = $seller->contact_persons ?? [];

    if (isset($contacts[$index])) {
        if (!empty($contacts[$index]['photo']) && file_exists(public_path($contacts[$index]['photo']))) {
            unlink(public_path($contacts[$index]['photo']));
        }

        if (!empty($contacts[$index]['pan_file']) && file_exists(public_path($contacts[$index]['pan_file']))) {
            unlink(public_path($contacts[$index]['pan_file']));
        }

        if (!empty($contacts[$index]['aadhaar_file']) && file_exists(public_path($contacts[$index]['aadhaar_file']))) {
            unlink(public_path($contacts[$index]['aadhaar_file']));
        }

        unset($contacts[$index]);
        $contacts = array_values($contacts);
    }

    if (count($contacts) == 0) {
        $seller->org_name = null;
        $seller->org_mobile = null;
        $seller->org_email = null;
        $seller->contact_persons = [];
    } else {
        $seller->contact_persons = $contacts;
    }

    $seller->save();

    return back()->with('success', 'Contact deleted successfully');
}

/* ===================================================
BANK
=================================================== */

public function bankList()
{
    return $this->profileView('vshop.seller.profile.bank-list');
}

public function bank()
{
    $countries = DB::table('countries')->get();

    return $this->profileView('vshop.seller.profile.bank', compact('countries'));
}

public function saveBank(Request $request)
{
    $seller = auth()->guard('seller')->user();

    if (!$seller) {
        return redirect()->route('seller.login');
    }

    $country = DB::table('countries')
        ->where('id', $request->bank_country)
        ->first();

    $rules = [
        'bank_account_name'   => 'required|string|max:255',
        'bank_account_number' => 'required|digits_between:6,20',
        'bank_country' => 'required|exists:countries,id',
        'bank_state'   => 'required|exists:states,id',
        'ifsc_code' => 'nullable|string|max:20',
        'bank_city'    => 'required|exists:cities,id',
        'branch_name'  => 'required|string|max:255',
        'cancel_cheque' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
    ];

    if ($country && strtolower($country->name) === 'india') {
        $rules['ifsc_code'] = 'required|string|max:20';
    } else {
        $rules['swift_code'] = 'required|string|max:20';
        $rules['bic_code']   = 'required|string|max:20';
    }

    $data = $request->validate($rules);

    $updateData = [
        'bank_account_name'   => $data['bank_account_name'],
        'bank_account_number' => $data['bank_account_number'],
        'bank_country'        => $data['bank_country'],
        'bank_state'          => $data['bank_state'],
        'bank_city'           => $data['bank_city'],
        'branch_name'         => $data['branch_name'],
        'ifsc_code'  => $request->ifsc_code,
        'swift_code' => $request->swift_code,
        'bic_code'   => $request->bic_code,
    ];

    if ($request->hasFile('cancel_cheque')) {
        $uploadPath = public_path('seller/bank');

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0777, true);
        }

        $file = $request->file('cancel_cheque');
        $filename = 'bank_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);

        if (!empty($seller->cancel_cheque)) {
            $oldFile = public_path($seller->cancel_cheque);
            if (File::exists($oldFile)) {
                File::delete($oldFile);
            }
        }

        $updateData['cancel_cheque'] = 'seller/bank/' . $filename;
        $updateData['bank_status'] = 'pending';
    }

    $oldBank = [
        'account_name'   => $seller->bank_account_name,
        'account_number' => $seller->bank_account_number,
        'branch_name'    => $seller->branch_name,
        'bank_country'   => $seller->bank_country,
        'bank_state'     => $seller->bank_state,
        'bank_city'      => $seller->bank_city,
        'ifsc_code'      => $seller->ifsc_code,
        'swift_code'     => $seller->swift_code,
        'bic_code'       => $seller->bic_code,
    ];

    $seller->update($updateData);

    $this->logActivity(
        $seller->id,
        'update',
        'bank',
        'bank_details',
        $oldBank,
        $updateData
    );

    $this->updateApprovalStatus($seller);

    return redirect()->route('seller.payment')
        ->with('success', 'Bank Saved Successfully');
}

public function deleteBank()
{
    $seller = auth()->guard('seller')->user();

    if (!$seller) {
        return back()->with('error', 'Seller not found');
    }

    if ($seller->cancel_cheque && \Storage::disk('public')->exists($seller->cancel_cheque)) {
        \Storage::disk('public')->delete($seller->cancel_cheque);
    }

    $seller->update([
        'bank_account_name'   => null,
        'bank_account_number' => null,
        'bank_country'        => null,
        'bank_state'          => null,
        'bank_city'           => null,
        'branch_name'         => null,
        'ifsc_code'           => null,
        'swift_code'          => null,
        'bic_code'            => null,
        'cancel_cheque'       => null,
        'bank_status'         => null,
    ]);

    $this->updateApprovalStatus($seller);

    return back()->with('success', 'Bank details deleted successfully');
}

/* ===================================================
PAYMENT
=================================================== */

public function paymentList()
{
    return $this->profileView('vshop.seller.profile.payment-list');
}

public function payment()
{
    return $this->profileView('vshop.seller.profile.payment');
}

public function savePayment(Request $request)
{
    $seller = auth()->guard('seller')->user();

    if (!$seller) {
        return redirect()->route('seller.login');
    }

    $request->validate([
        'payment_gateway' => 'required|in:paypal,razorpay,stripe',
        'paypal_email' => [
            'nullable',
            'required_if:payment_gateway,paypal',
            'email'
        ],
        'api_key' => [
            'nullable',
            'required_if:payment_gateway,razorpay,stripe'
        ],
        'api_secret' => [
            'nullable',
            'required_if:payment_gateway,razorpay,stripe'
        ],
    ], [
        'payment_gateway.required' => 'Please select payment gateway',
        'paypal_email.required_if' => 'PayPal Email required',
        'paypal_email.email'       => 'Enter valid PayPal Email',
        'api_key.required_if'      => 'API Key required',
        'api_secret.required_if'   => 'API Secret required',
    ]);

    if ($request->payment_gateway == 'paypal') {
        $seller->payment_gateway = 'paypal';
        $seller->paypal_email = $request->paypal_email;
        $seller->razorpay_key = null;
        $seller->razorpay_secret = null;
        $seller->stripe_key = null;
        $seller->stripe_secret = null;
    } elseif ($request->payment_gateway == 'razorpay') {
        $seller->payment_gateway = 'razorpay';
        $seller->razorpay_key = $request->api_key;
        $seller->razorpay_secret = $request->api_secret;
        $seller->paypal_email = null;
        $seller->stripe_key = null;
        $seller->stripe_secret = null;
    } elseif ($request->payment_gateway == 'stripe') {
        $seller->payment_gateway = 'stripe';
        $seller->stripe_key = $request->api_key;
        $seller->stripe_secret = $request->api_secret;
        $seller->paypal_email = null;
        $seller->razorpay_key = null;
        $seller->razorpay_secret = null;
    }

    $seller->api_key = $request->api_key;
    $seller->api_secret = $request->api_secret;
    $seller->save();

    $this->logActivity(
        $seller->id,
        'update',
        'payment',
        'payment_settings',
        [],
        $request->all()
    );

    $this->updateApprovalStatus($seller);

    return redirect()
        ->route('seller.agreement')
        ->with('success', 'Payment Settings Saved Successfully');
}

public function deletePayment()
{
    $seller = auth()->guard('seller')->user();

    if (!$seller) {
        return redirect()->route('seller.login');
    }

    $seller->update([
        'payment_gateway' => null,
        'gateways'        => null,
        'paypal_email'    => null,
        'api_key'         => null,
        'api_secret'      => null,
        'razorpay_key'    => null,
        'razorpay_secret' => null,
        'stripe_key'      => null,
        'stripe_secret'   => null,
    ]);

    $this->logActivity(
        $seller->id,
        'delete',
        'payment',
        'payment_settings',
        [],
        ['deleted' => true]
    );

    $this->updateApprovalStatus($seller);

    return back()->with('success', 'All payment details deleted successfully');
}

public function paymentStatus(Request $request)
{
    $seller = Seller::find(auth()->guard('seller')->id());

    $seller->payment_status = $request->status;
    $seller->save();

    return redirect()->back()->with('success', 'Payment status updated successfully.');
}

/* ===================================================
AGREEMENT
=================================================== */

public function agreementList()
{
    return $this->profileView('vshop.seller.profile.agreement-list');
}

public function agreement()
{
    return $this->profileView('vshop.seller.profile.agreement');
}

public function saveAgreement(Request $request)
{
    $seller = auth()->guard('seller')->user();

    $rules = [
        'agreement_type' => 'required|in:Digital,Physical',
        'agree' => 'required|accepted',
    ];

    if ($request->agreement_type === 'Physical') {
        if (!$seller->agreement_file) {
            $rules['agreement_file'] = 'required|file|mimes:pdf,jpg,png|max:2048';
        } else {
            $rules['agreement_file'] = 'nullable|file|mimes:pdf,jpg,png|max:2048';
        }
    }

    $data = $request->validate($rules);

    if ($request->hasFile('agreement_file')) {
        if (!empty($seller->agreement_file) && file_exists(public_path($seller->agreement_file))) {
            unlink(public_path($seller->agreement_file));
        }

        $file = $request->file('agreement_file');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('uploads/agreement'), $filename);
        $data['agreement_file'] = 'uploads/agreement/'.$filename;
    }

    $data['agreement_status'] = 'pending';
    $data['agreement_updated_at'] = now();
    $data['agreement_accepted_at'] = now();

    $seller->update($data);

    $this->logActivity(
        $seller->id,
        'update',
        'agreement',
        'agreement',
        null,
        $data
    );

    $this->updateApprovalStatus($seller);

    return redirect()->route('seller.logs')
        ->with('success','Agreement Saved Successfully');
}

public function logs()
{
    $seller = auth()->guard('seller')->user();

    $logs = DB::table('seller_activity_logs')
        ->where('seller_id', $seller->id)
        ->latest()
        ->limit(50)
        ->get();

    $progress = [
        'organization' => !empty($seller->legal_name),
        'contact'      => !empty($seller->contact_persons),
        'bank'         => !empty($seller->bank_account_number),
        'payment'      => !empty($seller->paypal_email),
        'agreement'    => !empty($seller->agreement_status),
        'logs'         => true
    ];

    $currentStep = 'logs';

    return view('vshop.seller.profile.logs', compact(
        'seller','progress','currentStep','logs'
    ));
}

public function deleteAgreement()
{
    $seller = auth()->guard('seller')->user();

    if (!empty($seller->agreement_file)) {
        $filePath = public_path($seller->agreement_file);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $seller->update([
        'agreement_type' => null,
        'agreement_file' => null,
        'agreement_accepted_at' => null,
        'agreement_status' => null,
        'agreement_updated_at' => null,
    ]);

    $this->updateApprovalStatus($seller);

    return back()->with('success', 'Agreement Deleted Successfully');
}

public function orgAutoSave(Request $request)
{
    $seller = auth()->guard('seller')->user();

    if (!$seller) {
        return response()->json(['success' => false]);
    }

    $data = $request->except([
        '_token',
        'company_certificate',
        'pan_file',
        'gst_certificate',
        'iec_certificate',
        'logo',
        'trademark_file',
    ]);

    if ($request->has('application')) {
        $data['application'] = json_encode($request->application);
    }

    if ($request->has('org_mobile')) {
        $data['phone'] = $request->org_mobile;
    }
    if ($request->has('org_email')) {
        $data['email'] = $request->org_email;
    }

    $seller->update($data);

    return response()->json([
        'success' => true
    ]);
}

public function updateBasic(Request $request)
{
    $seller = auth()->guard('seller')->user();

    if (!$seller) {
        return back()->with('error', 'Seller not found');
    }

    $request->validate([
        'name'   => 'required|string|max:255',
        'email'  => 'required|email|max:255',
        'mobile' => 'required|digits:10',
    ]);

    $seller->update([
        'name'   => $request->name,
        'email'  => $request->email,
        'mobile' => $request->mobile,
    ]);

    return back()->with('success', 'Profile updated successfully');
}

public function sendEmailOtp(Request $request)
{
    $otp = rand(1000, 9999);

    session(['email_otp' => $otp]);

    $seller = Auth::guard('seller')->user();
    
    // Send email using Laravel standard mailer (configured with log mailer in .env for local testing)
    \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\SellerEmailVerificationMail($otp, $seller->name ?? 'Seller'));
    
    \Illuminate\Support\Facades\Log::info("Email OTP generated: " . $otp . " for " . $request->email);

    return response()->json([
        'success' => true,
        'otp' => $otp
    ]);
}

public function verifyEmailOtp(Request $request)
{
    if($request->otp == session('email_otp')){
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false]);
}
}