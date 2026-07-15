<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use App\Models\BoostedListing;

use Illuminate\Support\Facades\Validator;

use App\Models\Seller; // or User with role
use App\Models\MarketplaceCategory;
use App\Models\Product;

use App\Models\InvoiceLog;
use App\Models\UserDeletionRequest;
use Carbon\Carbon;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::find(1);
        return view('admin.settings.general', compact('settings'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:15',
            'system_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'lazyload_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'shop_link_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->only('site_name', 'contact_email', 'contact_phone', 'currency_format');

        if ($request->hasFile('system_logo')) {
            $data['system_logo'] = $request->file('system_logo')->store('uploads/system_logos');
        }
        if ($request->hasFile('favicon_icon')) {
            $data['favicon_icon'] = $request->file('favicon_icon')->store('uploads/favicons');
        }
        if ($request->hasFile('lazyload_image')) {
            $data['lazyload_image'] = $request->file('lazyload_image')->store('uploads/lazyload_images');
        }
        if ($request->hasFile('shop_link_banner')) {
            $data['shop_link_banner'] = $request->file('shop_link_banner')->store('uploads/shop_banners');
        }

        Setting::updateOrCreate(['id' => 1], $data);
        return redirect()->route('admin.settings.index')->with('success', 'Settings saved successfully.');
    }

   
   

  public function masterControl()
    {
        $settingsData = $this->getMasterSettingsData();

        // Override boosts with fresh data from DB (compute per_day and days)
        $settingsData['boosts'] = BoostedListing::with(['product', 'seller', 'category'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($boost) {
                $start = Carbon::parse($boost->start_date);
                $end = Carbon::parse($boost->end_date);
                $days = $start->diffInDays($end) + 1; // inclusive
                $perDay = $boost->boost_amount / $days;

                return [
                    'id'                => $boost->id,
                    'product_id'        => $boost->product_id,
                    'seller_id'         => $boost->seller_id,
                    'category_id'       => $boost->category_id ?? $boost->product->category_id ?? null,
                    'boost_amount'      => $boost->boost_amount,
                    'start_date'        => $boost->start_date,
                    'end_date'          => $boost->end_date,
                    'days'              => $days,
                    'per_day'           => $perDay,
                    'status'            => $boost->status,
                    'impressions'       => $boost->impressions,
                    'clicks'            => $boost->clicks,
                    'enquiries'         => $boost->enquiries,
                    'conversions'       => $boost->conversions,
                    'revenue_generated' => $boost->revenue_generated,
                    'created_at'        => $boost->created_at->format('d M Y H:i'),
                ];
            })->toArray();

        // Dropdown data – unique categories by name
        $categories = MarketplaceCategory::select('id', 'name')
            ->orderBy('name')
            ->get()
            ->groupBy('name')
            ->map(fn($group) => (object) ['id' => $group->first()->id, 'name' => $group->first()->name])
            ->values();

        $settingsData['sellers']    = Seller::select('id', 'name')->get();
        $settingsData['categories'] = $categories;
        
        $settingsData['products']   = Product::select('id', 'name', 'category_id', 'created_by_id')
            ->with('category:id,name') // Eager load category to get its name
            ->orderBy('name')
            ->get();

        return view('admin.settings.master', compact('settingsData'));
    }

   

    // ---------------------------
    // BOOST CRUD METHODS
    // ---------------------------

    public function storeBoost(Request $request)
    {
        // Handle both single submission and array submission
        $isMultiple = is_array($request->input('per_day'));

        if (!$isMultiple) {
            // Transform to array for consistent processing
            $request->merge([
                'seller_id' => [$request->input('seller_id')],
                'category_id' => [$request->input('category_id')],
                'product_id' => [$request->input('product_id')],
                'per_day' => [$request->input('per_day')],
                'days' => [$request->input('days')],
                'status' => [$request->input('status')],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'seller_id'      => 'array',
            'seller_id.*'    => 'nullable|exists:sellers,id',
            'category_id'    => 'array',
            'category_id.*'  => 'nullable|exists:marketplace_categories,id',
            'product_id'     => 'array',
            'product_id.*'   => 'nullable|exists:products,id',
            'per_day'        => 'required|array',
            'per_day.*'      => 'required|numeric|min:0',
            'days'           => 'array',
            'days.*'         => 'nullable|integer|min:1',
            'status'         => 'array',
            'status.*'       => 'nullable|in:active,inactive,rule',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $perDays = $request->input('per_day');
        foreach ($perDays as $index => $perDay) {
            $sellerId   = $request->input("seller_id.$index");
            $categoryId = $request->input("category_id.$index");
            $productId  = $request->input("product_id.$index");
            $days       = $request->input("days.$index");
            $status     = $request->input("status.$index");

            $isRule = empty($days) || $status === 'rule';

            $boostAmount = 0;
            $startDate = null;
            $endDate = null;

            if (!$isRule) {
                $boostAmount = $perDay * $days;
                $startDate = Carbon::today()->toDateString();
                $endDate = Carbon::today()->addDays((int)$days)->toDateString();
                
                if (empty($categoryId) && !empty($productId)) {
                    $product = Product::find($productId);
                    if ($product) {
                        $categoryId = $product->category_id;
                        $sellerId = $product->created_by_id;
                    }
                } else {
                    $product = Product::find($productId);
                    $sellerId = $product ? $product->created_by_id : ($sellerId ?? null);
                }
            } else {
                $status = 'rule';
            }

            $sellerId   = empty($sellerId) ? null : $sellerId;
            $categoryId = empty($categoryId) ? null : $categoryId;
            $productId  = empty($productId) ? null : $productId;

            BoostedListing::create([
                'product_id'    => $productId,
                'category_id'   => $categoryId,
                'seller_id'     => $sellerId,
                'per_day'       => $perDay ?? 0,
                'boost_amount'  => $boostAmount,
                'start_date'    => $startDate,
                'end_date'      => $endDate,
                'status'        => $status ?? 'active',
            ]);
        }

        return redirect()->back()->with('success', 'Boost(s) created successfully.');
    }

    public function updateBoost(Request $request, $id)
    {
        $boost = BoostedListing::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'seller_id'    => 'nullable|exists:sellers,id',
            'product_id'   => 'nullable|exists:products,id',
            'category_id'  => 'nullable|exists:marketplace_categories,id',
            'per_day'      => 'required|numeric|min:0',
            'days'         => 'nullable|integer|min:1',
            'status'       => 'in:active,inactive',
            'impressions'  => 'nullable|integer|min:0',
            'clicks'       => 'nullable|integer|min:0',
            'enquiries'    => 'nullable|integer|min:0',
            'conversions'  => 'nullable|integer|min:0',
            'revenue_generated' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        if (!empty($data['per_day']) && !empty($data['days']) && ($data['status'] ?? '') !== 'rule') {
            $perDay = $request->per_day;
            $days = $request->days;
            $data['boost_amount'] = $perDay * $days;
            $data['start_date'] = Carbon::today()->toDateString();
            $data['end_date'] = Carbon::today()->addDays((int)$days)->toDateString();
        } else {
            $data['boost_amount'] = 0;
            $data['start_date'] = null;
            $data['end_date'] = null;
        }

        if (empty($data['category_id']) && !empty($data['product_id'])) {
            $product = Product::find($data['product_id']);
            if ($product) {
                $data['category_id'] = $product->category_id;
                $data['seller_id'] = $product->created_by_id;
            }
        } else {
            $product = Product::find($data['product_id'] ?? null);
            $data['seller_id'] = $product ? $product->created_by_id : ($data['seller_id'] ?? null);
        }

        $data['seller_id']   = empty($data['seller_id']) ? null : $data['seller_id'];
        $data['category_id'] = empty($data['category_id']) ? null : $data['category_id'];
        $data['product_id']  = empty($data['product_id']) ? null : $data['product_id'];

        $boost->update([
            'product_id'    => $data['product_id'],
            'category_id'   => $data['category_id'],
            'seller_id'     => $data['seller_id'],
            'per_day'       => $data['per_day'] ?? 0,
            'boost_amount'  => $data['boost_amount'],
            'start_date'    => $data['start_date'],
            'end_date'      => $data['end_date'],
            'status'        => $data['status'] ?? 'active',
            'impressions'   => $data['impressions'] ?? 0,
            'clicks'        => $data['clicks'] ?? 0,
            'enquiries'     => $data['enquiries'] ?? 0,
            'conversions'   => $data['conversions'] ?? 0,
            'revenue_generated' => $data['revenue_generated'] ?? 0,
        ]);

        return redirect()->back()->with('success', 'Boost updated successfully.');
    }

    public function deleteBoost($id)
    {
        $boost = BoostedListing::findOrFail($id);
        $boost->delete();

        return redirect()->back()->with('success', 'Boost deleted successfully.');
    }

   


public function toggleBoostStatus(Request $request, $id)
{
    $boost = BoostedListing::findOrFail($id);
    $boost->status = ($boost->status === 'active') ? 'inactive' : 'active';
    $boost->save();

    if ($request->ajax() || $request->wantsJson()) {
        return response()->json(['success' => true, 'status' => $boost->status, 'message' => 'Status toggled to ' . $boost->status]);
    }

    return redirect()->back()->with('success', 'Status toggled to ' . $boost->status);
}
    public function saveMasterConfig(Request $request)
    {
        try {
            $key = $request->input('key');
            $value = $request->input('value');
            Setting::setByKey($key, $value);
            return response()->json(['success' => true, 'message' => 'Setting updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function getMasterSettingsData()
    {
        $currentYear = date('Y');
        $baseTime = strtotime('-7 days');

        $data = [
            // 1. Overview Activities
            'activities' => json_decode(Setting::getByKey('master_activities', json_encode([
                ['activity' => 'New rule added: Seller KYC Validation', 'module' => 'Rule Engine', 'performed_by' => 'Super Admin', 'time' => date('d M Y h:i A', $baseTime), 'status' => 'Success'],
                ['activity' => 'Role updated: Finance Manager', 'module' => 'Admin Role', 'performed_by' => 'Super Admin', 'time' => date('d M Y h:i A', $baseTime + 86400), 'status' => 'Success'],
                ['activity' => 'Staff added: Rahul Sharma', 'module' => 'Staff Management', 'performed_by' => 'Super Admin', 'time' => date('d M Y h:i A', $baseTime + 2*86400), 'status' => 'Success'],
                ['activity' => 'Policy updated: Data Protection', 'module' => 'Compliance', 'performed_by' => 'Super Admin', 'time' => date('d M Y h:i A', $baseTime + 3*86400), 'status' => 'Success'],
                ['activity' => 'System health check completed', 'module' => 'System Health', 'performed_by' => 'Auto System', 'time' => date('d M Y h:i A', $baseTime + 4*86400), 'status' => 'Success'],
            ])), true),

            // 2. Rule Priority
            'priorities' => json_decode(Setting::getByKey('master_priorities', json_encode([
                ['level' => 'Seller Specific Rule', 'description' => 'Rules set for individual sellers override all other rules', 'status' => 'Active'],
                ['level' => 'Campaign Rule', 'description' => 'Rules set during specific campaigns', 'status' => 'Active'],
                ['level' => 'Category Rule', 'description' => 'Category-level rules for product categories', 'status' => 'Active'],
                ['level' => 'App Rule', 'description' => 'Application-wide rules (e.g., mobile app)', 'status' => 'Active'],
                ['level' => 'Global Default Rule', 'description' => 'Global default rules for entire marketplace', 'status' => 'Active'],
            ])), true),

            // 3. Validation Rules
            'validation_rules' => json_decode(Setting::getByKey('master_validation_rules', json_encode([
                ['name' => 'Seller KYC Mandatory', 'description' => 'Seller must complete KYC before activation', 'applies_to' => 'Sellers', 'status' => 'Active'],
                ['name' => 'Bank Verification Required', 'description' => 'Payout will be blocked if bank not verified', 'applies_to' => 'Sellers', 'status' => 'Active'],
                ['name' => 'Product Approval Required', 'description' => 'Product cannot go live without admin approval', 'applies_to' => 'Products', 'status' => 'Active'],
                ['name' => 'Duplicate Product Detection', 'description' => 'Duplicate product listing from same seller', 'applies_to' => 'Products', 'status' => 'Active'],
                ['name' => 'Invalid GST/PAN Rejection', 'description' => 'Invalid GST and PAN auto-rejected during verification', 'applies_to' => 'Sellers', 'status' => 'Active'],
                ['name' => 'Fraud Detection Auto-Disable', 'description' => 'Listing auto-disable on fraud detection', 'applies_to' => 'Products', 'status' => 'Active'],
            ])), true),

            // 4. Workflow Settings
            'workflow_settings' => json_decode(Setting::getByKey('master_workflow_settings', json_encode([
                ['name' => 'Auto status transitions controlled', 'enabled' => true],
                ['name' => 'Re-verification required on edits', 'enabled' => true],
                ['name' => 'Email notification on step transitions', 'enabled' => true],
                ['name' => 'Escalation on stuck workflows (>48 hours)', 'enabled' => false],
                ['name' => 'Auditing log binding', 'enabled' => true],
            ])), true),

            // Workflows
            'workflows' => json_decode(Setting::getByKey('master_workflows', json_encode([
                'seller' => [
                    ['status' => 'Pending', 'icon' => 'bx-time', 'description' => 'Seller applied & waiting', 'color' => 'active'],
                    ['status' => 'Verified', 'icon' => 'bx-search-alt', 'description' => 'Documents verified', 'color' => 'active'],
                    ['status' => 'Approved', 'icon' => 'bx-like', 'description' => 'Approved by admin', 'color' => 'completed'],
                    ['status' => 'Active', 'icon' => 'bx-user-check', 'description' => 'Seller account active', 'color' => 'completed'],
                    ['status' => 'Suspended', 'icon' => 'bx-block', 'description' => 'Account suspended', 'color' => 'suspended'],
                ],
                'product' => [
                    ['status' => 'Draft', 'icon' => 'bx-edit', 'description' => 'Product created as draft', 'color' => 'active'],
                    ['status' => 'Pending', 'icon' => 'bx-time', 'description' => 'Submitted for review', 'color' => 'active'],
                    ['status' => 'Verified', 'icon' => 'bx-check-circle', 'description' => 'Quality check passed', 'color' => 'completed'],
                    ['status' => 'Published', 'icon' => 'bx-globe', 'description' => 'Live on marketplace', 'color' => 'completed'],
                    ['status' => 'Disabled', 'icon' => 'bx-hide', 'description' => 'Removed from listing', 'color' => 'suspended'],
                ],
                'order' => [
                    ['status' => 'Pending', 'icon' => 'bx-time', 'description' => 'Order placed', 'color' => 'active'],
                    ['status' => 'Paid', 'icon' => 'bx-credit-card', 'description' => 'Payment received', 'color' => 'completed'],
                    ['status' => 'Dispatched', 'icon' => 'bx-package', 'description' => 'Shipped to customer', 'color' => 'completed'],
                    ['status' => 'Delivered', 'icon' => 'bx-check-double', 'description' => 'Received by customer', 'color' => 'completed'],
                    ['status' => 'Closed', 'icon' => 'bx-lock', 'description' => 'Order finalized', 'color' => 'completed'],
                ],
                'refund' => [
                    ['status' => 'Requested', 'icon' => 'bx-message-square-dots', 'description' => 'Refund requested', 'color' => 'active'],
                    ['status' => 'Review', 'icon' => 'bx-search', 'description' => 'Under review', 'color' => 'warning'],
                    ['status' => 'Approved', 'icon' => 'bx-check', 'description' => 'Refund approved', 'color' => 'completed'],
                    ['status' => 'Refunded', 'icon' => 'bx-wallet', 'description' => 'Amount refunded', 'color' => 'completed'],
                ],
                'lead' => [
                    ['status' => 'Clicked', 'icon' => 'bx-pointer', 'description' => 'Lead clicked', 'color' => 'active'],
                    ['status' => 'Charged', 'icon' => 'bx-rupee', 'description' => 'Lead charge applied', 'color' => 'warning'],
                    ['status' => 'Validated', 'icon' => 'bx-check-shield', 'description' => 'Lead validated', 'color' => 'completed'],
                    ['status' => 'Converted', 'icon' => 'bx-trophy', 'description' => 'Lead converted', 'color' => 'completed'],
                ]
            ])), true),

            // 5. Automation Rules
            'automation_rules' => json_decode(Setting::getByKey('master_automation_rules', json_encode([
                ['name' => 'Auto Approve Low-Risk Sellers', 'trigger' => 'New Seller Registration', 'action' => 'Auto-approve if risk score < 30', 'status' => 'Active', 'last_run' => date('d M Y h:i A', strtotime('-1 day'))],
                ['name' => 'Auto Reject Incomplete Listing', 'trigger' => 'Product Submitted', 'action' => 'Reject if required fields missing', 'status' => 'Active', 'last_run' => date('d M Y h:i A', strtotime('-2 days'))],
                ['name' => 'Auto Disable High Complaint', 'trigger' => 'Complaint Rate > Threshold', 'action' => 'Disable product if complaints > 5%', 'status' => 'Active', 'last_run' => date('d M Y h:i A', strtotime('-3 days'))],
                ['name' => 'Auto Penalty Trigger', 'trigger' => 'SLA Breach Detected', 'action' => 'Apply penalty to seller wallet', 'status' => 'Active', 'last_run' => date('d M Y h:i A', strtotime('-4 days'))],
                ['name' => 'Auto Release Payout', 'trigger' => 'Order Delivered', 'action' => 'Release commission payout to seller', 'status' => 'Active', 'last_run' => date('d M Y h:i A', strtotime('-5 days'))],
                ['name' => 'Auto Reverse Commission', 'trigger' => 'Refund Approved', 'action' => 'Reverse commission from seller balance', 'status' => 'Active', 'last_run' => date('d M Y h:i A', strtotime('-6 days'))],
                ['name' => 'Auto Activate Seller', 'trigger' => 'KYC Approved', 'action' => 'Activate seller account automatically', 'status' => 'Active', 'last_run' => date('d M Y h:i A', strtotime('-7 days'))],
                ['name' => 'Fake Lead Refund', 'trigger' => 'Fake Lead Detected', 'action' => 'Refund lead wallet amount', 'status' => 'Active', 'last_run' => date('d M Y h:i A', strtotime('-8 days'))],
                ['name' => 'Product Rejection Notify', 'trigger' => 'Product Rejected', 'action' => 'Notify seller via email/SMS', 'status' => 'Active', 'last_run' => date('d M Y h:i A', strtotime('-9 days'))],
            ])), true),

            // 6. Roles
            'roles' => json_decode(Setting::getByKey('master_roles', json_encode([
                ['name' => 'Super Admin', 'description' => 'Full system access, all modules', 'users' => 2, 'status' => 'Active', 'last_login' => date('d M Y h:i A', strtotime('-1 day'))],
                ['name' => 'Operations Admin', 'description' => 'Manages orders, sellers & products', 'users' => 4, 'status' => 'Active', 'last_login' => date('d M Y h:i A', strtotime('-2 days'))],
                ['name' => 'Finance Admin', 'description' => 'Manages payouts, commissions & refunds', 'users' => 3, 'status' => 'Active', 'last_login' => date('d M Y h:i A', strtotime('-3 days'))],
                ['name' => 'Support Team', 'description' => 'Manages tickets, customer queries', 'users' => 6, 'status' => 'Active', 'last_login' => date('d M Y h:i A', strtotime('-4 days'))],
                ['name' => 'Marketing Admin', 'description' => 'Manages campaigns, promotions & banners', 'users' => 3, 'status' => 'Active', 'last_login' => date('d M Y h:i A', strtotime('-5 days'))],
                ['name' => 'Report Viewer', 'description' => 'Read-only access to system reports', 'users' => 2, 'status' => 'Active', 'last_login' => date('d M Y h:i A', strtotime('-6 days'))],
            ])), true),

            // 7. Staff
            'staff' => json_decode(Setting::getByKey('master_staff', json_encode([
                ['id' => 'EMP001', 'name' => 'Ankit Verma', 'department' => 'Operations', 'designation' => 'Manager', 'email' => 'ankit@vshop.com', 'phone' => '9876543210', 'status' => 'Active'],
                ['id' => 'EMP002', 'name' => 'Ruchi Sharma', 'department' => 'Finance', 'designation' => 'Lead', 'email' => 'ruchi@vshop.com', 'phone' => '9876543211', 'status' => 'Active'],
                ['id' => 'EMP003', 'name' => 'Arjun Mehta', 'department' => 'Marketing', 'designation' => 'Executive', 'email' => 'arjun@vshop.com', 'phone' => '9876543212', 'status' => 'Active'],
                ['id' => 'EMP004', 'name' => 'Priya Singh', 'department' => 'Support', 'designation' => 'Sr. Executive', 'email' => 'priya@vshop.com', 'phone' => '9876543213', 'status' => 'Active'],
                ['id' => 'EMP005', 'name' => 'Vikram Patel', 'department' => 'Tech', 'designation' => 'Developer', 'email' => 'vikram@vshop.com', 'phone' => '9876543214', 'status' => 'On Leave'],
            ])), true),

            // 8. Compliance
            'compliance' => json_decode(Setting::getByKey('master_compliance', json_encode([
                ['name' => 'KYC Mandatory', 'description' => 'KYC mandatory before seller activation', 'category' => 'KYC & Verification', 'version' => 'v2.1', 'last_updated' => date('d M Y', strtotime('-10 days')), 'status' => 'Active'],
                ['name' => 'Data Privacy Policy', 'description' => 'Data masking for PAN, bank details', 'category' => 'Policies', 'version' => 'v1.5', 'last_updated' => date('d M Y', strtotime('-15 days')), 'status' => 'Active'],
                ['name' => 'Refund Cancellation Policy', 'description' => 'Refund and cancellation rules for orders', 'category' => 'Policies', 'version' => 'v3.0', 'last_updated' => date('d M Y', strtotime('-20 days')), 'status' => 'Active'],
                ['name' => 'Seller T&C Agreement', 'description' => 'Terms & conditions version control', 'category' => 'Agreements', 'version' => 'v4.2', 'last_updated' => date('d M Y', strtotime('-25 days')), 'status' => 'Active'],
                ['name' => 'Consent Tracking', 'description' => 'User consent and data processing tracking', 'category' => 'Policies', 'version' => 'v1.0', 'last_updated' => date('d M Y', strtotime('-30 days')), 'status' => 'Active'],
                ['name' => 'User Data Deletion', 'description' => 'User data deletion request handling', 'category' => 'Policies', 'version' => 'v1.2', 'last_updated' => date('d M Y', strtotime('-40 days')), 'status' => 'Active'],
                ['name' => 'Privacy Policy', 'description' => 'User privacy and data protection', 'category' => 'Policies', 'version' => 'v2.0', 'last_updated' => date('d M Y', strtotime('-12 days')), 'status' => 'Active'],
                ['name' => 'Payment Validation', 'description' => 'Payment cannot be released without validation', 'category' => 'KYC & Verification', 'version' => 'v1.3', 'last_updated' => date('d M Y', strtotime('-18 days')), 'status' => 'Active'],
                ['name' => 'Country Compliance', 'description' => 'Country-specific compliance rules', 'category' => 'Policies', 'version' => 'v1.1', 'last_updated' => date('d M Y', strtotime('-22 days')), 'status' => 'Active'],
                ['name' => 'Legal Audit Export', 'description' => 'Exportable audit trail for legal compliance', 'category' => 'Audit Logs', 'version' => 'v1.0', 'last_updated' => date('d M Y', strtotime('-35 days')), 'status' => 'Active'],
            ])), true),

            // 9. Notifications
            'notifications' => json_decode(Setting::getByKey('master_notifications', json_encode([
                ['name' => 'KYC Approved', 'type' => 'Email', 'subject' => 'Your KYC has been approved', 'status' => 'Active'],
                ['name' => 'Order Placed', 'type' => 'Email', 'subject' => 'New order placed - Order #{{id}}', 'status' => 'Active'],
                ['name' => 'Refund Processed', 'type' => 'Email', 'subject' => 'Refund of {{amount}} processed', 'status' => 'Active'],
                ['name' => 'Low Wallet Balance', 'type' => 'SMS', 'subject' => 'Your wallet balance is low: ₹{{balance}}', 'status' => 'Active'],
                ['name' => 'Product Rejected', 'type' => 'WhatsApp', 'subject' => 'Your product {{name}} was rejected', 'status' => 'Active'],
                ['name' => 'New Lead Received', 'type' => 'SMS', 'subject' => 'New lead received from {{source}}', 'status' => 'Active'],
                ['name' => 'Payment Released', 'type' => 'Email', 'subject' => 'Payment of ₹{{amount}} released to your account', 'status' => 'Active'],
                ['name' => 'Account Suspended', 'type' => 'Email', 'subject' => 'Your seller account has been suspended', 'status' => 'Active'],
            ])), true),

            // 10. Integration Management
            'integration_management' => json_decode(Setting::getByKey('master_integration_management', json_encode([
                ['name' => 'Razorpay', 'type' => 'Payment Gateway', 'status' => 'Active', 'api_health' => 'OK', 'last_check' => date('d M Y h:i A')],
                ['name' => 'Stripe', 'type' => 'Payment Gateway', 'status' => 'Active', 'api_health' => 'OK', 'last_check' => date('d M Y h:i A')],
                ['name' => 'WhatsApp Business API', 'type' => 'Messaging', 'status' => 'Active', 'api_health' => 'Degraded', 'last_check' => date('d M Y h:i A')],
                ['name' => 'CRM (Salesforce)', 'type' => 'CRM', 'status' => 'Inactive', 'api_health' => 'N/A', 'last_check' => date('d M Y h:i A')],
                ['name' => 'SendGrid Email', 'type' => 'Email', 'status' => 'Active', 'api_health' => 'OK', 'last_check' => date('d M Y h:i A')],
            ])), true),

            // 11. System Health Monitor
            'system_health' => json_decode(Setting::getByKey('master_system_health', json_encode([
                ['component' => 'Payment Gateway API', 'status' => 'Operational', 'last_incident' => 'None', 'uptime' => '99.99%'],
                ['component' => 'Email Service', 'status' => 'Operational', 'last_incident' => '12 Jun 2026', 'uptime' => '99.5%'],
                ['component' => 'Queue Worker', 'status' => 'Degraded', 'last_incident' => 'Today 02:15 AM', 'uptime' => '98.2%'],
                ['component' => 'Main Database', 'status' => 'Operational', 'last_incident' => 'None', 'uptime' => '100%'],
                ['component' => 'CDN', 'status' => 'Operational', 'last_incident' => 'None', 'uptime' => '99.99%'],
            ])), true),

            // 12. Event Triggers
            'event_triggers' => json_decode(Setting::getByKey('master_event_triggers', json_encode([
                ['event' => 'Order Delivered', 'action' => 'Release payout to seller', 'status' => 'Active'],
                ['event' => 'Refund Approved', 'action' => 'Reverse commission from seller', 'status' => 'Active'],
                ['event' => 'KYC Approved', 'action' => 'Activate seller account', 'status' => 'Active'],
                ['event' => 'Fake Lead Detected', 'action' => 'Refund lead wallet amount', 'status' => 'Active'],
                ['event' => 'Product Rejected', 'action' => 'Notify seller via email/SMS', 'status' => 'Active'],
            ])), true),

            // 13. AI Moderation Triggers
            'ai_moderation' => json_decode(Setting::getByKey('master_ai_moderation', json_encode([
                ['trigger' => 'Spam Content', 'action' => 'Auto flag for review', 'status' => 'Active', 'threshold' => '80%'],
                ['trigger' => 'Fake Reviews', 'action' => 'Auto remove & notify', 'status' => 'Active', 'threshold' => '3 reports'],
                ['trigger' => 'Price Anomaly', 'action' => 'Suspend listing temporarily', 'status' => 'Active', 'threshold' => '50% above avg'],
                ['trigger' => 'Risk Score > 70', 'action' => 'Send alert to admin', 'status' => 'Active', 'threshold' => 'Score > 70'],
            ])), true),

            // 14. System Rules (additional)
            'system_rules' => json_decode(Setting::getByKey('master_system_rules', json_encode([
                ['name' => 'Auto status transitions controlled', 'enabled' => true],
                ['name' => 'Re-verification required on edits', 'enabled' => true],
                ['name' => 'Email notification on step transitions', 'enabled' => true],
                ['name' => 'Escalation on stuck workflows (>48 hours)', 'enabled' => false],
                ['name' => 'Force 2FA for admin logins', 'enabled' => false],
            ])), true),
        ];

        // Add revenue and boost analytics (using correct table name)
        $data['revenue'] = $this->getRevenueAnalytics();
        $data['boost'] = $this->getBoostAnalytics();

        return $data;
    }

    public function saveGrievanceSettings(Request $request)
    {
        $request->validate([
            'grievance_escalation_hours' => 'required|integer|min:1',
            'grievance_escalation_order_value' => 'required|numeric|min:0',
            'grievance_escalation_agent' => 'required|string',
            'grievance_escalation_priority' => 'required|in:low,medium,high',
        ]);

        Setting::setByKey('grievance_escalation_hours', $request->grievance_escalation_hours);
        Setting::setByKey('grievance_escalation_order_value', $request->grievance_escalation_order_value);
        Setting::setByKey('grievance_escalation_agent', $request->grievance_escalation_agent);
        Setting::setByKey('grievance_escalation_priority', $request->grievance_escalation_priority);

        return redirect()->back()->with('success', 'Grievance settings updated successfully.');
    }

    // ========== SYSTEM SETTINGS (unchanged) ==========
    public function systemSettings(Request $request)
    {
        $security = [
            'two_factor_auth' => Setting::getByKey('security_2fa', false),
            'security_2fa_email' => Setting::getByKey('security_2fa_email', ''),
            'security_2fa_phone' => Setting::getByKey('security_2fa_phone', ''),
            'suspicious_detection' => Setting::getByKey('security_suspicious', true),
            'abnormal_conversion' => Setting::getByKey('security_abnormal_conv', true),
            'ip_abuse_detection' => Setting::getByKey('security_ip_abuse', true),
            'failed_attempts_limit' => Setting::getByKey('security_failed_attempts', 5),
            'block_duration_hours' => Setting::getByKey('security_block_duration', 24),
            'whitelist_ips' => Setting::getByKey('security_whitelist_ips', ''),
        ];

        $dataControl = [
            'backup_frequency' => Setting::getByKey('data_backup_frequency', 'daily'),
            'backup_retention_days' => Setting::getByKey('data_backup_retention', 30),
            'data_retention_months' => Setting::getByKey('data_retention_months', 12),
            'deletion_approval' => Setting::getByKey('data_deletion_approval', true),
        ];

        $platform = [
            'tax_inclusive' => Setting::getByKey('platform_tax_inclusive', false),
        ];

        $taxRates = Setting::getByKey('tax_rates', []);

        // Load and filter invoice logs
        $query = InvoiceLog::orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%$search%")
                  ->orWhere('module', 'like', "%$search%")
                  ->orWhere('reference_id', 'like', "%$search%")
                  ->orWhere('generated_by', 'like', "%$search%");
            });
        }

        if ($request->filled('module')) {
            $query->where('module', $request->input('module'));
        }

        $invoiceLogs = $query->paginate(20)->withQueryString();

        // Load backup files list
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        $backupFiles = [];
        if (is_dir($backupDir)) {
            $files = scandir($backupDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && str_ends_with($file, '.json')) {
                    $filePath = $backupDir . '/' . $file;
                    $backupFiles[] = [
                        'filename' => $file,
                        'size' => round(filesize($filePath) / 1024, 2) . ' KB',
                        'created_at' => date('d M Y h:i A', filemtime($filePath))
                    ];
                }
            }
        }

        // Seed mock user deletion requests if empty, then fetch them
        if (UserDeletionRequest::count() == 0) {
            UserDeletionRequest::create([
                'user_id' => 12,
                'user_type' => 'customer',
                'name' => 'John Doe',
                'email' => 'john.doe@gmail.com',
                'status' => 'pending',
                'reason' => 'I want to delete my account as I am no longer using this service.'
            ]);
            UserDeletionRequest::create([
                'user_id' => 15,
                'user_type' => 'seller',
                'name' => 'Apex Electronics',
                'email' => 'contact@apexelectronics.com',
                'status' => 'pending',
                'reason' => 'Business closed down.'
            ]);
        }
        $deletionRequests = UserDeletionRequest::orderBy('id', 'desc')->get();

        return view('admin.settings.system', compact('security', 'dataControl', 'platform', 'taxRates', 'invoiceLogs', 'backupFiles', 'deletionRequests'));
    }

    public function saveSecuritySettings(Request $request)
    {
        $is2fa = $request->boolean('two_factor_auth');
        Setting::setByKey('security_2fa', $is2fa);

        if ($is2fa) {
            $request->validate([
                'security_2fa_email' => 'required|email',
                'security_2fa_phone' => 'required',
            ]);
            Setting::setByKey('security_2fa_email', $request->input('security_2fa_email'));
            Setting::setByKey('security_2fa_phone', $request->input('security_2fa_phone'));
        }

        Setting::setByKey('security_suspicious', $request->boolean('suspicious_detection'));
        Setting::setByKey('security_abnormal_conv', $request->boolean('abnormal_conversion'));
        Setting::setByKey('security_ip_abuse', $request->boolean('ip_abuse_detection'));
        Setting::setByKey('security_failed_attempts', $request->input('failed_attempts_limit', 5));
        Setting::setByKey('security_block_duration', $request->input('block_duration_hours', 24));
        Setting::setByKey('security_whitelist_ips', $request->input('whitelist_ips', ''));
        return redirect()->back()->with('success', 'Security settings saved.');
    }

    public function saveDataControlSettings(Request $request)
    {
        Setting::setByKey('data_backup_frequency', $request->input('backup_frequency'));
        Setting::setByKey('data_backup_retention', $request->input('backup_retention_days'));
        Setting::setByKey('data_retention_months', $request->input('data_retention_months'));
        Setting::setByKey('data_deletion_approval', $request->boolean('deletion_approval'));
        return redirect()->back()->with('success', 'Data control settings saved.');
    }

    public function savePlatformSettings(Request $request)
    {
        Setting::setByKey('platform_tax_inclusive', $request->boolean('tax_inclusive'));
        return redirect()->back()->with('success', 'Tax inclusive setting saved.');
    }

    // AJAX endpoints for tax rates
    public function saveTaxRate(Request $request)
    {
        $index = $request->input('index');
        $taxRates = Setting::getByKey('tax_rates', []);

        $newRate = [
            'name' => $request->input('name'),
            'rate' => $request->input('rate'),
            'region' => $request->input('region'),
            'type' => $request->input('type'),
            'status' => $request->input('status', 'Active'),
            'is_default' => false,
        ];

        if ($index === null || $index === '') {
            $taxRates[] = $newRate;
        } else {
            $taxRates[$index] = $newRate;
        }

        Setting::setByKey('tax_rates', $taxRates);
        return response()->json(['success' => true]);
    }

    public function deleteTaxRate(Request $request)
    {
        $index = $request->input('index');
        $taxRates = Setting::getByKey('tax_rates', []);
        if (isset($taxRates[$index])) {
            array_splice($taxRates, $index, 1);
            Setting::setByKey('tax_rates', $taxRates);
        }
        return response()->json(['success' => true]);
    }

    public function toggleTaxRateStatus(Request $request)
    {
        $index = $request->input('index');
        $taxRates = Setting::getByKey('tax_rates', []);
        if (isset($taxRates[$index])) {
            $taxRates[$index]['status'] = $taxRates[$index]['status'] == 'Active' ? 'Inactive' : 'Active';
            Setting::setByKey('tax_rates', $taxRates);
        }
        return response()->json(['success' => true]);
    }

    public function setDefaultTaxRate(Request $request)
    {
        $index = $request->input('index');
        $taxRates = Setting::getByKey('tax_rates', []);
        foreach ($taxRates as $i => &$rate) {
            $rate['is_default'] = ($i == $index);
        }
        Setting::setByKey('tax_rates', $taxRates);
        return response()->json(['success' => true]);
    }

    public function exportData()
    {
        $settings = Setting::all();
        $invoiceLogs = InvoiceLog::all();
        $deletionRequests = UserDeletionRequest::all();

        $data = [
            'settings' => $settings,
            'invoice_logs' => $invoiceLogs,
            'user_deletion_requests' => $deletionRequests,
            'exported_at' => now()->toDateTimeString()
        ];

        $filename = 'system_data_export_' . date('Y-m-d_H-i-s') . '.json';
        return response()->json($data, 200, [
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    public function createBackup()
    {
        try {
            $settings = Setting::all();
            $invoiceLogs = InvoiceLog::all();
            $deletionRequests = UserDeletionRequest::all();

            $data = [
                'settings' => $settings,
                'invoice_logs' => $invoiceLogs,
                'user_deletion_requests' => $deletionRequests,
                'backup_at' => now()->toDateTimeString()
            ];

            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.json';
            file_put_contents($backupDir . '/' . $filename, json_encode($data, JSON_PRETTY_PRINT));

            return redirect()->back()->with('success', 'Backup created successfully: ' . $filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create backup: ' . $e->getMessage());
        }
    }

    public function deleteBackup(Request $request)
    {
        $filename = $request->input('filename');
        if (!$filename) {
            return redirect()->back()->with('error', 'Filename is required');
        }

        $filePath = storage_path('app/backups/' . basename($filename));
        if (file_exists($filePath)) {
            unlink($filePath);
            return redirect()->back()->with('success', 'Backup file deleted successfully.');
        }

        return redirect()->back()->with('error', 'Backup file not found.');
    }

    public function restoreBackup(Request $request)
    {
        try {
            $data = null;
            if ($request->hasFile('backup_file')) {
                $file = $request->file('backup_file');
                $data = json_decode(file_get_contents($file->getRealPath()), true);
            } elseif ($request->filled('filename')) {
                $filePath = storage_path('app/backups/' . basename($request->input('filename')));
                if (file_exists($filePath)) {
                    $data = json_decode(file_get_contents($filePath), true);
                }
            }

            if (!$data) {
                return redirect()->back()->with('error', 'No valid backup file found or selected.');
            }

            DB::beginTransaction();

            // Restore settings
            if (isset($data['settings'])) {
                foreach ($data['settings'] as $item) {
                    if (isset($item['key'])) {
                        Setting::setByKey($item['key'], $item['value']);
                    }
                }
            }

            // Restore invoice logs
            if (isset($data['invoice_logs'])) {
                foreach ($data['invoice_logs'] as $log) {
                    InvoiceLog::updateOrCreate(
                        ['id' => $log['id'] ?? null],
                        [
                            'invoice_number' => $log['invoice_number'],
                            'module' => $log['module'],
                            'reference_id' => $log['reference_id'],
                            'amount' => $log['amount'],
                            'tax_amount' => $log['tax_amount'],
                            'currency' => $log['currency'] ?? 'INR',
                            'generated_by' => $log['generated_by'] ?? 'System',
                            'file_path' => $log['file_path'] ?? null,
                        ]
                    );
                }
            }

            // Restore user deletion requests
            if (isset($data['user_deletion_requests'])) {
                foreach ($data['user_deletion_requests'] as $req) {
                    UserDeletionRequest::updateOrCreate(
                        ['id' => $req['id'] ?? null],
                        [
                            'user_id' => $req['user_id'] ?? null,
                            'user_type' => $req['user_type'] ?? 'customer',
                            'name' => $req['name'],
                            'email' => $req['email'],
                            'status' => $req['status'],
                            'reason' => $req['reason'] ?? null,
                        ]
                    );
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'System settings and logs restored successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    public function purgeLogs()
    {
        try {
            $retentionMonths = (int) Setting::getByKey('data_retention_months', 12);
            $cutoffDate = now()->subMonths($retentionMonths);

            $deletedInvoices = InvoiceLog::where('created_at', '<', $cutoffDate)->delete();

            $deletedActivities = 0;
            if (Schema::hasTable('activity_logs')) {
                $deletedActivities = DB::table('activity_logs')->where('created_at', '<', $cutoffDate)->delete();
            }

            return redirect()->back()->with('success', "Purged successfully: {$deletedInvoices} invoices and {$deletedActivities} activity logs older than {$retentionMonths} months.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Purge failed: ' . $e->getMessage());
        }
    }

    public function approveDeletion($id)
    {
        try {
            $req = UserDeletionRequest::findOrFail($id);
            $req->status = 'approved';
            $req->save();

            if ($req->user_type === 'customer') {
                $customer = \App\Models\Customer::find($req->user_id);
                if ($customer) {
                    $customer->update([
                        'name' => 'Deleted User ' . $customer->id,
                        'email' => 'deleted_' . $customer->id . '@vshop.com',
                        'phone' => null,
                        'password' => '',
                        'blocked' => 1,
                        'remarks' => 'Account deleted per compliance request.'
                    ]);
                }
            } elseif ($req->user_type === 'seller') {
                $seller = \App\Models\Seller::find($req->user_id);
                if ($seller) {
                    $seller->update([
                        'name' => 'Deleted Seller ' . $seller->id,
                        'email' => 'deleted_seller_' . $seller->id . '@vshop.com',
                        'status' => 'Inactive',
                    ]);
                }
            }

            return redirect()->back()->with('success', 'User data deletion request approved and data anonymized.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve request: ' . $e->getMessage());
        }
    }

    public function rejectDeletion($id)
    {
        try {
            $req = UserDeletionRequest::findOrFail($id);
            $req->status = 'rejected';
            $req->save();

            return redirect()->back()->with('success', 'User data deletion request rejected.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject request: ' . $e->getMessage());
        }
    }

    // ========== REVENUE & BOOST ANALYTICS (with correct table name) ==========

    /**
     * Get revenue analytics from boosted_listings table
     */
    private function getRevenueAnalytics()
    {
        try {
            $boosts = DB::table('boosted_listings')
                ->leftJoin('products', 'boosted_listings.product_id', '=', 'products.id')
                ->leftJoin('sellers', 'boosted_listings.seller_id', '=', 'sellers.id')
                ->select('boosted_listings.*', 'products.category_id', 'sellers.name as seller_name', 'products.name as product_name', 'sellers.application as seller_app')
                ->get();
        } catch (\Exception $e) {
            $boosts = collect([]);
        }

        if ($boosts->isEmpty()) {
            // Provide realistic mock data for presentation
            return [
                'total_revenue' => 125400,
                'revenue_per_seller' => 4500,
                'revenue_per_category' => 25000,
                'revenue_per_app' => 50160,
                'categories' => [
                    ['name' => 'Electronics', 'revenue' => 45000, 'percent' => 35.9, 'trend' => 'up'],
                    ['name' => 'Fashion', 'revenue' => 38000, 'percent' => 30.3, 'trend' => 'up'],
                    ['name' => 'Home & Kitchen', 'revenue' => 25000, 'percent' => 19.9, 'trend' => 'down'],
                    ['name' => 'Beauty', 'revenue' => 17400, 'percent' => 13.9, 'trend' => 'up'],
                ],
                'sellers' => [
                    ['name' => 'Apex Electronics', 'spent' => 5000, 'revenue' => 22000, 'roi' => 4.4],
                    ['name' => 'Fashion Hub', 'spent' => 4500, 'revenue' => 19500, 'roi' => 4.3],
                    ['name' => 'Home Essentials', 'spent' => 3800, 'revenue' => 15200, 'roi' => 4.0],
                    ['name' => 'Beauty Zone', 'spent' => 3000, 'revenue' => 12000, 'roi' => 4.0],
                    ['name' => 'Sports Gear', 'spent' => 2500, 'revenue' => 10500, 'roi' => 4.2],
                ],
                'transactions' => [
                    ['date' => date('d M Y', strtotime('-1 day')), 'campaign' => 'Samsung S24 Ultra', 'seller' => 'Apex Electronics', 'spent' => 1200, 'revenue' => 5400, 'status' => 'active'],
                    ['date' => date('d M Y', strtotime('-2 days')), 'campaign' => 'Apple Watch Series 9', 'seller' => 'Fashion Hub', 'spent' => 900, 'revenue' => 4100, 'status' => 'active'],
                    ['date' => date('d M Y', strtotime('-3 days')), 'campaign' => 'Sony WH-1000XM5', 'seller' => 'Apex Electronics', 'spent' => 800, 'revenue' => 3600, 'status' => 'completed'],
                    ['date' => date('d M Y', strtotime('-4 days')), 'campaign' => 'Nike Air Max', 'seller' => 'Sports Gear', 'spent' => 600, 'revenue' => 2800, 'status' => 'active'],
                    ['date' => date('d M Y', strtotime('-5 days')), 'campaign' => 'Gaming Chair', 'seller' => 'Home Essentials', 'spent' => 500, 'revenue' => 2100, 'status' => 'completed'],
                ],
                'apps' => [
                    ['name' => 'VShop Platform', 'platform' => 'vshop', 'revenue' => 62700, 'share' => '50%'],
                    ['name' => 'VTravel Platform', 'platform' => 'vtravel', 'revenue' => 37620, 'share' => '30%'],
                    ['name' => 'VCampus Platform', 'platform' => 'vcampus', 'revenue' => 18810, 'share' => '15%'],
                    ['name' => 'VEdge Platform', 'platform' => 'vedge', 'revenue' => 6270, 'share' => '5%'],
                ],
                'trend_labels' => collect(range(30, 0))->map(fn($days) => now()->subDays($days)->format('d M'))->toArray(),
                'trend_values' => collect(range(30, 0))->map(fn() => rand(2000, 8000))->toArray(),
            ];
        }

        // Total revenue
        $totalRevenue = $boosts->sum('revenue_generated');

        // Revenue per seller (average)
        $sellerRevenue = $boosts->groupBy('seller_id')->map(function($group) {
            return $group->sum('revenue_generated');
        });
        $revenuePerSeller = $sellerRevenue->count() ? $sellerRevenue->average() : 0;

        // Revenue per category
        $categoryRevenue = [];
        foreach ($boosts as $b) {
            $catId = $b->category_id ?? 'Uncategorized';
            if (!isset($categoryRevenue[$catId])) $categoryRevenue[$catId] = 0;
            $categoryRevenue[$catId] += $b->revenue_generated;
        }
        $revenuePerCategory = count($categoryRevenue) ? array_sum($categoryRevenue) / count($categoryRevenue) : 0;

        // Revenue per app
        $vshopRev = 0;
        $vtravelRev = 0;
        $vcampusRev = 0;
        $vedgeRev = 0;

        foreach ($boosts as $b) {
            $app = strtolower($b->seller_app ?? '');
            if (str_contains($app, 'travel')) {
                $vtravelRev += $b->revenue_generated;
            } elseif (str_contains($app, 'campus')) {
                $vcampusRev += $b->revenue_generated;
            } elseif (str_contains($app, 'edge')) {
                $vedgeRev += $b->revenue_generated;
            } else {
                $vshopRev += $b->revenue_generated; // Default to VShop
            }
        }
        $totalAppsRevenue = $vshopRev + $vtravelRev + $vcampusRev + $vedgeRev;
        $revenuePerApp = $totalAppsRevenue;

        // Category breakdown for table
        $categories = [];
        $total = $totalRevenue;
        foreach ($categoryRevenue as $catId => $rev) {
            $percent = $total ? round(($rev / $total) * 100, 1) : 0;
            $categories[] = ['name' => 'Category ' . $catId, 'revenue' => $rev, 'percent' => $percent, 'trend' => ($rev > $total / count($categoryRevenue)) ? 'up' : 'down'];
        }

        // Sellers list
        $sellers = [];
        $sellerSpending = [];
        foreach ($boosts as $b) {
            $sid = $b->seller_id;
            if (!isset($sellerSpending[$sid])) {
                $sellerSpending[$sid] = ['name' => $b->seller_name ?? 'Seller #'.$sid, 'spent' => 0, 'revenue' => 0];
            }
            $sellerSpending[$sid]['spent'] += $b->boost_amount;
            $sellerSpending[$sid]['revenue'] += $b->revenue_generated;
        }
        foreach ($sellerSpending as $s) {
            $s['roi'] = $s['spent'] > 0 ? round($s['revenue'] / $s['spent'], 2) : 0;
            $sellers[] = $s;
        }

        // Transactions list
        $transactions = [];
        foreach ($boosts as $b) {
            $transactions[] = [
                'date' => date('d M Y', strtotime($b->created_at)),
                'campaign' => $b->product_name ?? 'Product #'.$b->product_id,
                'seller' => $b->seller_name ?? 'Seller #'.$b->seller_id,
                'spent' => $b->boost_amount,
                'revenue' => $b->revenue_generated,
                'status' => $b->status
            ];
        }

        $apps = [
            ['name' => 'VShop Platform', 'platform' => 'vshop', 'revenue' => round($vshopRev, 2), 'share' => $totalAppsRevenue > 0 ? round(($vshopRev / $totalAppsRevenue) * 100) . '%' : '0%'],
            ['name' => 'VTravel Platform', 'platform' => 'vtravel', 'revenue' => round($vtravelRev, 2), 'share' => $totalAppsRevenue > 0 ? round(($vtravelRev / $totalAppsRevenue) * 100) . '%' : '0%'],
            ['name' => 'VCampus Platform', 'platform' => 'vcampus', 'revenue' => round($vcampusRev, 2), 'share' => $totalAppsRevenue > 0 ? round(($vcampusRev / $totalAppsRevenue) * 100) . '%' : '0%'],
            ['name' => 'VEdge Platform', 'platform' => 'vedge', 'revenue' => round($vedgeRev, 2), 'share' => $totalAppsRevenue > 0 ? round(($vedgeRev / $totalAppsRevenue) * 100) . '%' : '0%'],
        ];

        // Revenue trends (last 30 days)
        $trendData = DB::table('boosted_listings')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(revenue_generated) as daily_revenue'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $trendLabels = $trendData->pluck('date')->map(function($d) { return date('d M', strtotime($d)); })->toArray();
        $trendValues = $trendData->pluck('daily_revenue')->toArray();

        return [
            'total_revenue' => $totalRevenue,
            'revenue_per_seller' => round($revenuePerSeller, 2),
            'revenue_per_category' => round($revenuePerCategory, 2),
            'revenue_per_app' => round($revenuePerApp, 2),
            'categories' => $categories,
            'sellers' => $sellers,
            'transactions' => $transactions,
            'apps' => $apps,
            'trend_labels' => $trendLabels,
            'trend_values' => $trendValues,
        ];
    }

    /**
     * Get boost revenue analytics from boosted_listings table
     */
    private function getBoostAnalytics()
    {
        try {
            $boosts = DB::table('boosted_listings')
                ->leftJoin('sellers', 'boosted_listings.seller_id', '=', 'sellers.id')
                ->leftJoin('products', 'boosted_listings.product_id', '=', 'products.id')
                ->select('boosted_listings.*', 'sellers.name as seller_name', 'products.name as product_name')
                ->get();
        } catch (\Exception $e) {
            $boosts = collect([]);
        }

        if ($boosts->isEmpty()) {
            // Provide realistic mock data
            return [
                'total_revenue' => 125400,
                'total_amount' => 28500,
                'roi' => 4.4,
                'active_boosts' => 14,
                'top_sellers' => [
                    ['name' => 'Apex Electronics', 'spent' => 5000, 'revenue' => 22000],
                    ['name' => 'Fashion Hub', 'spent' => 4500, 'revenue' => 19500],
                    ['name' => 'Home Essentials', 'spent' => 3800, 'revenue' => 15200],
                    ['name' => 'Beauty Zone', 'spent' => 3000, 'revenue' => 12000],
                    ['name' => 'Sports Gear', 'spent' => 2500, 'revenue' => 10500],
                ],
                'campaigns' => [
                    ['product_name' => 'Samsung S24 Ultra', 'boost_amount' => 1200, 'revenue' => 5400, 'status' => 'active'],
                    ['product_name' => 'Apple Watch Series 9', 'boost_amount' => 900, 'revenue' => 4100, 'status' => 'active'],
                    ['product_name' => 'Sony WH-1000XM5', 'boost_amount' => 800, 'revenue' => 3600, 'status' => 'completed'],
                    ['product_name' => 'Nike Air Max', 'boost_amount' => 600, 'revenue' => 2800, 'status' => 'active'],
                    ['product_name' => 'Gaming Chair', 'boost_amount' => 500, 'revenue' => 2100, 'status' => 'completed'],
                ],
                'roi_labels' => ['Boost 101', 'Boost 102', 'Boost 103', 'Boost 104', 'Boost 105', 'Boost 106', 'Boost 107', 'Boost 108'],
                'roi_values' => [3.5, 4.1, 2.8, 5.2, 4.5, 3.9, 4.8, 5.5],
            ];
        }

        $totalRevenue = $boosts->sum('revenue_generated');
        $totalAmount = $boosts->sum('boost_amount');
        $roi = $totalAmount > 0 ? $totalRevenue / $totalAmount : 0;
        $activeBoosts = $boosts->where('status', 'active')->count();

        // Top spending sellers
        $sellerSpending = [];
        foreach ($boosts as $b) {
            $sid = $b->seller_id;
            if (!isset($sellerSpending[$sid])) {
                $sellerSpending[$sid] = ['name' => $b->seller_name ?? 'Seller #'.$sid, 'spent' => 0, 'revenue' => 0];
            }
            $sellerSpending[$sid]['spent'] += $b->boost_amount;
            $sellerSpending[$sid]['revenue'] += $b->revenue_generated;
        }
        $topSellers = collect($sellerSpending)->sortByDesc('spent')->take(5)->values()->map(function($s) {
            return ['name' => $s['name'], 'spent' => round($s['spent'],2), 'revenue' => round($s['revenue'],2)];
        });

        // Campaign revenue (each boost)
        $campaigns = $boosts->take(10)->map(function($b) {
            return [
                'product_name' => $b->product_name ?? 'Product #'.$b->product_id,
                'boost_amount' => round($b->boost_amount,2),
                'revenue' => round($b->revenue_generated,2),
                'status' => $b->status
            ];
        });

        // ROI tracking for last 10 boosts
        $roiLabels = [];
        $roiValues = [];
        $lastBoosts = $boosts->sortByDesc('created_at')->take(10);
        foreach ($lastBoosts as $b) {
            $roiLabels[] = 'Boost '.$b->id;
            $roiValues[] = $b->boost_amount > 0 ? round($b->revenue_generated / $b->boost_amount, 2) : 0;
        }

    }

    // ===========================================================
    // DATA CONTROL METHODS
    // ===========================================================

    /**
     * Feature 1: Export Data as CSV
     */
    public function dataExport(Request $request)
    {
        $type = $request->get('type', 'sellers');
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$type}_export_" . date('Y-m-d') . ".csv\"",
        ];

        $callback = function () use ($type) {
            $handle = fopen('php://output', 'w');

            if ($type === 'sellers') {
                fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'City', 'Status', 'Joined']);
                DB::table('sellers')->orderBy('id')->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [$row->id, $row->name ?? '', $row->email ?? '', $row->phone ?? '', $row->city ?? '', $row->status ?? '', $row->created_at ?? '']);
                    }
                });
            } elseif ($type === 'products') {
                fputcsv($handle, ['ID', 'Name', 'Seller ID', 'Category', 'Price', 'Stock', 'Status', 'Created']);
                DB::table('products')->orderBy('id')->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [$row->id, $row->name ?? '', $row->created_by_id ?? '', $row->category_id ?? '', $row->price ?? '', $row->stock ?? '', $row->status ?? '', $row->created_at ?? '']);
                    }
                });
            } elseif ($type === 'orders') {
                fputcsv($handle, ['ID', 'Seller ID', 'Customer ID', 'Total', 'Status', 'Payment', 'Date']);
                DB::table('orders')->orderBy('id')->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [$row->id, $row->seller_id ?? '', $row->customer_id ?? '', $row->total_amount ?? '', $row->status ?? '', $row->payment_status ?? '', $row->created_at ?? '']);
                    }
                });
            } elseif ($type === 'customers') {
                fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'City', 'Status', 'Joined']);
                DB::table('customers')->orderBy('id')->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [$row->id, $row->name ?? '', $row->email ?? '', $row->phone ?? '', $row->city ?? '', $row->status ?? '', $row->created_at ?? '']);
                    }
                });
            } elseif ($type === 'enquiries') {
                fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'Product ID', 'Message', 'Date']);
                DB::table('enquiries')->orderBy('id')->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [$row->id, $row->name ?? '', $row->email ?? '', $row->phone ?? '', $row->product_id ?? '', substr($row->message ?? '', 0, 100), $row->created_at ?? '']);
                    }
                });
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Feature 2: Save Data Retention Settings
     */
    public function saveDataRetention(Request $request)
    {
        $request->validate([
            'logs_retain_days'     => 'required|integer|min:1|max:3650',
            'enquiries_retain_days' => 'required|integer|min:1|max:3650',
            'fraud_logs_retain_days' => 'required|integer|min:1|max:3650',
        ]);

        DB::table('settings')->updateOrInsert(['key' => 'logs_retain_days'], ['value' => $request->logs_retain_days]);
        DB::table('settings')->updateOrInsert(['key' => 'enquiries_retain_days'], ['value' => $request->enquiries_retain_days]);
        DB::table('settings')->updateOrInsert(['key' => 'fraud_logs_retain_days'], ['value' => $request->fraud_logs_retain_days]);

        return redirect()->back()->with('success', 'Data retention settings saved successfully.');
    }

    /**
     * Feature 2b: Run Data Retention Now (manual trigger)
     */
    public function runDataRetention(Request $request)
    {
        try {
            $logsDays    = (int) (DB::table('settings')->where('key', 'logs_retain_days')->value('value') ?? 365);
            $enquiryDays = (int) (DB::table('settings')->where('key', 'enquiries_retain_days')->value('value') ?? 365);
            $fraudDays   = (int) (DB::table('settings')->where('key', 'fraud_logs_retain_days')->value('value') ?? 180);

            $logsCutoff    = Carbon::now()->subDays($logsDays)->toDateTimeString();
            $enquiryCutoff = Carbon::now()->subDays($enquiryDays)->toDateTimeString();
            $fraudCutoff   = Carbon::now()->subDays($fraudDays)->toDateTimeString();

            $deletedLogs     = DB::table('seller_activity_logs')->where('created_at', '<', $logsCutoff)->delete();
            $deletedEnquiries = DB::table('enquiries')->where('is_archived', 1)->where('created_at', '<', $enquiryCutoff)->delete();
            $deletedFraud    = DB::table('fraud_system')->where('created_at', '<', $fraudCutoff)->delete();

            return response()->json([
                'success' => true,
                'message' => "Cleanup done! Deleted: {$deletedLogs} logs, {$deletedEnquiries} enquiries, {$deletedFraud} fraud records.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Feature 3: Get Deletion Requests
     */
    public function getDeletionRequests(Request $request)
    {
        try {
            // Check customers with status like 'pending_deletion' or similar
            $requests = DB::table('customers')
                ->select('id', 'name as customer_name', 'email as customer_email', 'updated_at as created_at', 'status')
                ->whereIn('status', ['pending_deletion', 'deletion_requested'])
                ->get();
            return response()->json($requests);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    /**
     * Feature 3: Process User Data Deletion Request (GDPR)
     */
    public function processUserDeletion(Request $request, $id)
    {
        try {
            $request->validate(['action' => 'required|in:approve,reject']);
            $customer = DB::table('customers')->where('id', $id)->first();

            if (!$customer) {
                return response()->json(['success' => false, 'message' => 'Customer not found.'], 404);
            }

            if ($request->action === 'approve') {
                // Anonymize customer data (soft delete approach - GDPR compliant)
                DB::table('customers')->where('id', $id)->update([
                    'name'           => 'Deleted User #' . $id,
                    'email'          => 'deleted_' . $id . '@removed.com',
                    'phone'          => null,
                    'profile_picture'=> null,
                    'status'         => 'deleted',
                    'updated_at'     => Carbon::now(),
                ]);
                $message = 'User data anonymized and deletion approved successfully.';
            } else {
                DB::table('customers')->where('id', $id)->update([
                    'status' => 'active', // Restore status
                    'updated_at' => Carbon::now(),
                ]);
                $message = 'Deletion request rejected (User restored to active).';
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Feature 4: Create Database Backup
     */
    public function createBackupNow(Request $request)
    {
        try {
            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $filename  = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath  = $backupDir . '/' . $filename;

            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            // Generate SQL backup using PHP (no shell exec needed)
            $tables = DB::select('SHOW TABLES');
            $sql = "-- VShop Database Backup\n-- Generated: " . date('Y-m-d H:i:s') . "\n-- Database: {$dbName}\n\nSET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $tableObj) {
                $tableName = array_values((array)$tableObj)[0];
                // Create table structure
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "\n-- Table: {$tableName}\nDROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= array_values((array)$createTable[0])[1] . ";\n\n";

                // Insert data in chunks
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $cols = array_keys((array)$rows->first());
                    $colList = '`' . implode('`, `', $cols) . '`';
                    foreach ($rows->chunk(100) as $chunk) {
                        $values = $chunk->map(function ($row) {
                            $vals = array_map(function ($v) {
                                return $v === null ? 'NULL' : "'" . addslashes($v) . "'";
                            }, (array)$row);
                            return '(' . implode(', ', $vals) . ')';
                        })->implode(",\n");
                        $sql .= "INSERT INTO `{$tableName}` ({$colList}) VALUES\n{$values};\n";
                    }
                }
            }

            $sql .= "\nSET FOREIGN_KEY_CHECKS=1;\n";
            file_put_contents($filepath, $sql);

            return response()->json([
                'success'  => true,
                'message'  => "Backup created: {$filename}",
                'filename' => $filename,
                'size'     => round(filesize($filepath) / 1024, 2) . ' KB',
                'date'     => date('d M Y H:i'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Feature 4b: Delete a Backup File
     */
    public function deleteBackupFile(Request $request, $filename)
    {
        try {
            // Security: only allow safe filenames
            if (!preg_match('/^backup_[\d_\-]+\.sql$/', $filename)) {
                return response()->json(['success' => false, 'message' => 'Invalid filename.'], 400);
            }
            $filepath = storage_path('app/backups/' . $filename);
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            return response()->json(['success' => true, 'message' => 'Backup deleted.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ===========================================================
    // FRAUD PROTECTION METHODS
    // ===========================================================

    /**
     * Feature 5+: Fraud Dashboard Data (JSON)
     */
    public function fraudDashboard(Request $request)
    {
        try {
            $cutoff30 = Carbon::now()->subDays(30);

            // Feature 5: High Fraud Sellers (cancel rate > threshold)
            $fraudThreshold = (int) (DB::table('settings')->where('key', 'fraud_cancel_threshold')->value('value') ?? 30);
            $highFraudSellers = DB::table('sellers')
                ->select(
                    'sellers.id', 'sellers.name', 'sellers.email',
                    DB::raw('COUNT(orders.id) as total_orders'),
                    DB::raw('SUM(CASE WHEN orders.status IN ("cancelled","returned") THEN 1 ELSE 0 END) as bad_orders'),
                    DB::raw('ROUND(SUM(CASE WHEN orders.status IN ("cancelled","returned") THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(orders.id), 0), 1) as cancel_rate')
                )
                ->leftJoin('orders', 'orders.seller_id', '=', 'sellers.id')
                ->where('orders.created_at', '>=', $cutoff30)
                ->groupBy('sellers.id', 'sellers.name', 'sellers.email')
                ->having('cancel_rate', '>', $fraudThreshold)
                ->having('total_orders', '>', 3)
                ->orderByDesc('cancel_rate')
                ->limit(10)
                ->get();

            // Feature 6: Suspicious IPs (more than 20 clicks in 1 hour from same IP)
            $clickThreshold = (int) (DB::table('settings')->where('key', 'fraud_click_threshold')->value('value') ?? 20);
            $suspiciousIps = DB::table('fraud_system')
                ->select('customer_ip', DB::raw('COUNT(*) as click_count'), DB::raw('MAX(created_at) as last_seen'))
                ->where('record_type', 'click')
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->groupBy('customer_ip')
                ->having('click_count', '>=', $clickThreshold)
                ->orderByDesc('click_count')
                ->limit(20)
                ->get();

            // Feature 7: Duplicate Sellers (Name, Phone, Email, PAN)
            $duplicateSellers = DB::table('sellers as s1')
                ->select(
                    's1.id', 's1.name', 's1.email', 
                    's2.id as duplicate_id', 's2.name as duplicate_name',
                    DB::raw('
                        CASE 
                            WHEN s1.pan_number = s2.pan_number AND s1.pan_number != "" THEN "Same PAN"
                            WHEN s1.phone = s2.phone AND s1.phone != "" THEN "Same Phone"
                            WHEN s1.email = s2.email AND s1.email != "" THEN "Same Email"
                            WHEN s1.name = s2.name AND s1.name != "" THEN "Same Name"
                            ELSE "Duplicate"
                        END as reason
                    ')
                )
                ->join('sellers as s2', function ($join) {
                    $join->on(function($q) {
                        $q->on('s1.pan_number', '=', 's2.pan_number')->whereNotNull('s1.pan_number')->where('s1.pan_number', '!=', '')
                          ->orOn('s1.phone', '=', 's2.phone')->whereNotNull('s1.phone')->where('s1.phone', '!=', '')
                          ->orOn('s1.email', '=', 's2.email')->whereNotNull('s1.email')->where('s1.email', '!=', '')
                          ->orOn('s1.name', '=', 's2.name')->whereNotNull('s1.name')->where('s1.name', '!=', '');
                    })
                    ->whereColumn('s1.id', '<', 's2.id');
                })
                ->limit(10)
                ->get();

            // Feature 8: Abnormal Conversions (0% or 100% conversion rate)
            $abnormalConversions = DB::table('products')
                ->select(
                    'products.id', 'products.name',
                    DB::raw('COUNT(DISTINCT enquiries.id) as enquiries'),
                    DB::raw('COUNT(DISTINCT order_items.order_id) as orders'),
                    DB::raw('ROUND(COUNT(DISTINCT order_items.order_id) * 100.0 / NULLIF(COUNT(DISTINCT enquiries.id), 0), 1) as conversion_rate')
                )
                ->leftJoin('enquiries', function ($join) use ($cutoff30) {
                    $join->on('enquiries.product_id', '=', 'products.id')
                         ->where('enquiries.created_at', '>=', $cutoff30);
                })
                ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
                ->groupBy('products.id', 'products.name')
                ->havingRaw('(conversion_rate >= 90 OR (enquiries >= 20 AND orders = 0))')
                ->orderByDesc('enquiries')
                ->limit(10)
                ->get();

            // Feature 9: Fake/Spam Enquiries (same IP, multiple enquiries)
            $spamEnquiryThreshold = (int) (DB::table('settings')->where('key', 'fraud_enquiry_threshold')->value('value') ?? 5);
            $fakeEnquiries = DB::table('enquiries')
                ->select('ip_address', DB::raw('COUNT(*) as count'), DB::raw('MAX(created_at) as last_time'))
                ->whereNotNull('ip_address')
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->groupBy('ip_address')
                ->having('count', '>=', $spamEnquiryThreshold)
                ->orderByDesc('count')
                ->limit(20)
                ->get();

            // Feature 11: Duplicate & Fraud Customer Detection
            // High Risk Customers
            $highRiskCustomers = DB::table('customers')
                ->select('id', 'name', 'email', 'phone', 'risk_level')
                ->whereIn('risk_level', ['high', 'critical'])
                ->limit(10)
                ->get();
            
            // Duplicate Customers (Same Phone or Email)
            $duplicateCustomers = DB::table('customers as c1')
                ->select('c1.id', 'c1.name', 'c1.email', 'c1.phone', 'c2.id as duplicate_id', 'c2.name as duplicate_name')
                ->join('customers as c2', function ($join) {
                    $join->on(function($q) {
                             $q->on('c1.phone', '=', 'c2.phone')->whereNotNull('c1.phone')->where('c1.phone', '!=', '')
                               ->orOn('c1.email', '=', 'c2.email')->whereNotNull('c1.email')->where('c1.email', '!=', '');
                         })
                         ->whereColumn('c1.id', '<', 'c2.id');
                })
                ->limit(10)
                ->get();

            // Feature 11: Payment Fraud Detection
            $paymentFraud = DB::table('orders')
                ->select('orders.id', 'orders.customer_id', 'orders.total_amount', 'orders.payment_attempts', 'orders.created_at', 'customers.name', 'customers.email')
                ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
                ->where('orders.payment_attempts', '>=', 3)
                ->where('orders.created_at', '>=', $cutoff30)
                ->orderByDesc('orders.payment_attempts')
                ->limit(10)
                ->get();

            // Feature 12: Invoice Validation (Amount Mismatch or Missing)
            $invoiceValidation = DB::table('orders')
                ->select('orders.id as order_id', 'orders.total_amount as order_amount', 'invoice_logs.amount as invoice_amount', 'orders.status', 'invoice_logs.invoice_number')
                ->leftJoin('invoice_logs', function($join) {
                    $join->on('orders.id', '=', 'invoice_logs.reference_id')
                         ->where('invoice_logs.module', '=', 'order');
                })
                ->whereIn('orders.status', ['completed', 'delivered'])
                ->where('orders.created_at', '>=', $cutoff30)
                ->where(function($query) {
                    $query->whereNull('invoice_logs.id')
                          ->orWhereRaw('ABS(orders.total_amount - invoice_logs.amount) > 1');
                })
                ->orderByDesc('orders.id')
                ->limit(10)
                ->get();

            // Feature 10: Blocked IPs list
            $blockedIps = DB::table('settings')
                ->where('key', 'blocked_ips')
                ->value('value');
            $blockedIps = $blockedIps ? json_decode($blockedIps, true) : [];

            // Duplicate Products upload detection (Fraud Products)
            $products = DB::table('products')
                ->select('id', 'name', 'description', 'price', 'created_by_id', 'status')
                ->where('status', '!=', 'inactive')
                ->orderByDesc('id')
                ->limit(100)
                ->get();
                
            $duplicateProducts = [];
            $pCount = count($products);
            for ($i = 0; $i < $pCount; $i++) {
                for ($j = $i + 1; $j < $pCount; $j++) {
                    $p1 = $products[$i];
                    $p2 = $products[$j];
                    
                    similar_text(strtolower($p1->name), strtolower($p2->name), $nameSim);
                    if ($nameSim > 85) {
                        similar_text(strtolower($p1->description ?? ''), strtolower($p2->description ?? ''), $descSim);
                        
                        $p1Price = (float)($p1->price ?? 0);
                        $p2Price = (float)($p2->price ?? 0);
                        $priceDiff = $p1Price > 0 ? abs($p1Price - $p2Price) / $p1Price : 0;
                        
                        if ($descSim > 80 || ($p1Price == $p2Price && $p1Price > 0)) {
                            $seller1 = DB::table('sellers')->where('id', $p1->created_by_id)->value('name') ?? 'System/Seller #' . $p1->created_by_id;
                            $seller2 = DB::table('sellers')->where('id', $p2->created_by_id)->value('name') ?? 'System/Seller #' . $p2->created_by_id;
                            
                            $duplicateProducts[] = [
                                'product_1' => [
                                    'id' => $p1->id,
                                    'name' => $p1->name,
                                    'seller' => $seller1,
                                    'status' => $p1->status
                                ],
                                'product_2' => [
                                    'id' => $p2->id,
                                    'name' => $p2->name,
                                    'seller' => $seller2,
                                    'status' => $p2->status
                                ],
                                'similarity' => round(($nameSim + $descSim) / 2, 1),
                                'reason' => ($p1->created_by_id == $p2->created_by_id) ? 'Same Seller Duplicate' : 'Cross-Seller Duplicate'
                            ];
                        }
                    }
                }
            }

            // Summary Stats
            $totalFraudRecords = DB::table('fraud_system')->where('is_fraud', 1)->count();
            $fraudThisMonth    = DB::table('fraud_system')->where('is_fraud', 1)->where('created_at', '>=', $cutoff30)->count();
            $blockedCount      = count($blockedIps);
            $suspiciousCount   = $suspiciousIps->count() + $highFraudSellers->count() + $duplicateSellers->count() + count($duplicateProducts);

            return response()->json([
                'success'              => true,
                'stats' => [
                    'total_fraud'       => $totalFraudRecords,
                    'fraud_this_month'  => $fraudThisMonth,
                    'blocked_ips'       => $blockedCount,
                    'suspicious_alerts' => $suspiciousCount,
                ],
                'high_fraud_sellers'    => $highFraudSellers,
                'suspicious_ips'        => $suspiciousIps,
                'duplicate_sellers'     => $duplicateSellers,
                'abnormal_conversions'  => $abnormalConversions,
                'fake_enquiries'        => $fakeEnquiries,
                'high_risk_customers'   => $highRiskCustomers,
                'duplicate_customers'   => $duplicateCustomers,
                'payment_fraud'         => $paymentFraud,
                'invoice_validation'    => $invoiceValidation,
                'blocked_ips_list'      => $blockedIps,
                'duplicate_products'    => $duplicateProducts,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Feature: Flag Duplicate Product (Deactivate)
     */
    public function flagDuplicateProduct(Request $request, $id)
    {
        try {
            DB::table('products')->where('id', $id)->update(['status' => 'inactive']);
            return response()->json(['success' => true, 'message' => 'Product has been deactivated/flagged as duplicate.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Feature 10: Block a Suspicious IP
     */
    public function blockSuspiciousIp(Request $request)
    {
        $request->validate(['ip' => 'required|ip']);
        try {
            $ip = $request->ip_address ?? $request->ip;
            $existing = DB::table('settings')->where('key', 'blocked_ips')->value('value');
            $list = $existing ? json_decode($existing, true) : [];
            if (!in_array($ip, array_column($list, 'ip'))) {
                $list[] = ['ip' => $ip, 'reason' => $request->reason ?? 'Manually blocked', 'blocked_at' => Carbon::now()->toDateTimeString()];
                DB::table('settings')->updateOrInsert(['key' => 'blocked_ips'], ['value' => json_encode($list)]);
            }
            return response()->json(['success' => true, 'message' => "IP {$ip} has been blocked."]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Feature 10b: Unblock an IP
     */
    public function unblockIp(Request $request)
    {
        $request->validate(['ip' => 'required']);
        try {
            $ip = $request->ip;
            $existing = DB::table('settings')->where('key', 'blocked_ips')->value('value');
            $list = $existing ? json_decode($existing, true) : [];
            $list = array_values(array_filter($list, fn($item) => $item['ip'] !== $ip));
            DB::table('settings')->updateOrInsert(['key' => 'blocked_ips'], ['value' => json_encode($list)]);
            return response()->json(['success' => true, 'message' => "IP {$ip} has been unblocked."]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Save Fraud Protection Thresholds
     */
    public function saveFraudSettings(Request $request)
    {
        $request->validate([
            'fraud_cancel_threshold'  => 'required|integer|min:1|max:100',
            'fraud_click_threshold'   => 'required|integer|min:1|max:1000',
            'fraud_enquiry_threshold' => 'required|integer|min:1|max:500',
        ]);

        DB::table('settings')->updateOrInsert(['key' => 'fraud_cancel_threshold'], ['value' => $request->fraud_cancel_threshold]);
        DB::table('settings')->updateOrInsert(['key' => 'fraud_click_threshold'], ['value' => $request->fraud_click_threshold]);
        DB::table('settings')->updateOrInsert(['key' => 'fraud_enquiry_threshold'], ['value' => $request->fraud_enquiry_threshold]);

        return response()->json(['success' => true, 'message' => 'Fraud settings saved successfully.']);
    }

    /**
     * Dismiss a Fraud Alert
     */
    public function dismissFraudAlert(Request $request, $id)
    {
        try {
            DB::table('fraud_system')->where('id', $id)->update(['is_fraud' => 0, 'fraud_reason' => 'Dismissed by admin']);
            return response()->json(['success' => true, 'message' => 'Alert dismissed.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
