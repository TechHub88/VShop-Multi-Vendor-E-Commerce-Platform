<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Customer;
use App\Models\Address;
use App\Models\WalletTransaction;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\Wishlist;
use App\Models\Referral;
use App\Models\Membership;
use App\Models\Reward;
use App\Models\Enquiry;
use App\Models\DeviceSession;
use App\Models\Grievance; // <-- ADD THIS
use Illuminate\Support\Facades\File;
use App\Models\CustomerRecentlyViewed;

class MyAccountController extends Controller
{
    /**
     * Display the My Account dashboard.
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) abort(403);

        // Fetch enquiries grouped by product for the logged-in customer
        $enquiries = Enquiry::where('user_id', $customer->id)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('product_id');

        // ===== FETCH COMPLAINTS =====
        $complaints = Grievance::where('buyer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $complaintCounts = [
            'all' => Grievance::where('buyer_id', $customer->id)->count(),
            'open' => Grievance::where('buyer_id', $customer->id)->where('status', 'open')->count(),
            'in_progress' => Grievance::where('buyer_id', $customer->id)->where('status', 'in_progress')->count(),
            'resolved' => Grievance::where('buyer_id', $customer->id)->where('status', 'resolved')->count(),
            'closed' => Grievance::where('buyer_id', $customer->id)->where('status', 'closed')->count(),
        ];

        return view('vshop.myaccount', [
            'profile' => $customer,
            'addresses' => Address::where('customer_id', $customer->id)->get(),
            'walletBalance' => $customer->wallet_balance ?? 0,
            'transactions' => WalletTransaction::where('customer_id', $customer->id)->orderBy('id','desc')->get(),
            'rewardPoints' => Reward::getValidPoints($customer->id),
            'cashbackEarned' => Reward::where('customer_id', $customer->id)->where('type','cashback')->sum('amount'),
            'referralBalance' => Referral::where('customer_id', $customer->id)->where('status','successful')->sum('reward_amount'),
            'rewardsList' => Reward::where('customer_id', $customer->id)->with('referral')->orderBy('expiry_date')->get(),
            'membership' => Membership::where('customer_id', $customer->id)->first(),
            'wishlist' => Wishlist::where('customer_id', $customer->id)->with('product')->get(),
            'referralCode' => $customer->referral_code,
            'referralHistory' => Referral::where('customer_id', $customer->id)
                ->with(['referredCustomer' => function($query) {
                    $query->withCount('orders');
                }])->get(),
            'orders' => Order::where('customer_id', $customer->id)->with('items.product')->orderBy('id','desc')->get(),
            'reviews' => ProductReview::where('customer_id', $customer->id)->with('product')->get(),
            'recentlyViewed' => CustomerRecentlyViewed::where('customer_id', $customer->id)->with('product')->orderBy('viewed_at','desc')->take(10)->get(),
            'sessions' => DeviceSession::where('customer_id', $customer->id)->get(),
            'paymentMethods' => collect(), // implement later
            'enquiries' => $enquiries,
            'complaints' => $complaints,
            'complaintCounts' => $complaintCounts,
        ]);
    }

    /**
     * Update basic profile info (name, email, mobile, gender, language, occupation, photo).
     */
    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'mobile' => 'required|string|max:20',
            'gender' => 'nullable|string',
            'language' => 'nullable|string',
            'occupation' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('uploads/profiles');
            
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);
            $validated['photo'] = 'uploads/profiles/' . $filename;
        }

        $customer->update($validated);
        $customer->checkAndRewardProfileCompletion();
        return response()->json(['success' => true, 'photo' => asset($customer->photo)]);
    }

    /**
     * Update other info (DOB, marital status, anniversary, kids, workplace).
     */
    public function updateOtherInfo(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $validated = $request->validate([
            'dob' => 'nullable|date',
            'marital_status' => 'nullable|string',
            'anniversary' => 'nullable|date',
            'kids' => 'nullable|integer',
            'workplace' => 'nullable|string',
        ]);

        if ($validated['marital_status'] !== 'Married') {
            $validated['anniversary'] = null;
        }

        $customer->update($validated);
        $customer->checkAndRewardProfileCompletion();
        return response()->json(['success' => true]);
    }

    /**
     * Update bank account details.
     */
    public function updateBankDetails(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $validated = $request->validate([
            'bank_name' => 'nullable|string|max:255',
            'account_holder' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:50',
            'upi_id' => 'nullable|string|max:255',
        ]);

        $customer->update($validated);
        $customer->checkAndRewardProfileCompletion();
        return response()->json(['success' => true]);
    }

    /**
     * Add a new address.
     */
    public function addAddress(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address_line1' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|string',
            'country' => 'required|string',
            'address_type' => 'required|string|in:billing,delivery,shipping',
            'is_default' => 'boolean',
        ]);

        $customer = Auth::guard('customer')->user();
        $address = $customer->addresses()->create($data);

        if ($data['is_default']) {
            $customer->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        return response()->json(['success' => true, 'address' => $address]);
    }

    /**
     * Update an existing address.
     */
    public function updateAddress(Request $request, $id)
    {
        $address = Address::where('customer_id', Auth::guard('customer')->id())->findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address_line1' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|string',
            'country' => 'required|string',
            'address_type' => 'required|string|in:billing,delivery,shipping',
            'is_default' => 'boolean',
        ]);

        $address->update($data);

        if ($data['is_default']) {
            Address::where('customer_id', Auth::guard('customer')->id())
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Delete an address.
     */
    public function deleteAddress($id)
    {
        Address::where('customer_id', Auth::guard('customer')->id())->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Recharge wallet (legacy fallback, now handled by Razorpay).
     */
    public function rechargeWallet(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:10']);
        $customer = Auth::guard('customer')->user();
        $amount = $request->amount;

        $customer->increment('wallet_balance', $amount);
        WalletTransaction::create([
            'customer_id' => $customer->id,
            'user_type' => 'customer',
            'transaction_type' => 'credit',
            'credit' => $amount,
            'debit' => 0,
            'balance_after' => $customer->wallet_balance,
            'description' => 'Wallet recharge',
            'status' => 'success',
            'reference_id' => 'TXN' . time(),
        ]);

        return response()->json(['success' => true, 'new_balance' => $customer->wallet_balance]);
    }

    /**
     * Create Razorpay Order for Wallet Recharge.
     */
    public function createRechargeOrder(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10'
        ]);

        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Please login first'], 401);
        }

        $amount = $request->amount;
        $amountInPaise = round($amount * 100);

        $key = config('services.razorpay.key', 'rzp_live_SSn9AQrMCCO84l');
        $secret = config('services.razorpay.secret', 'gJsZEy9b2Papg9tByq2MYMpW');

        try {
            $response = \Illuminate\Support\Facades\Http::withBasicAuth($key, $secret)
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                    'receipt' => 'rcpt_recharge_' . time(),
                ]);

            if ($response->successful()) {
                $razorpayOrder = $response->json();
                return response()->json([
                    'success' => true,
                    'order_id' => $razorpayOrder['id'],
                    'amount' => $amountInPaise,
                    'key' => $key
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create Razorpay order: ' . $response->body()
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify Razorpay payment signature and credit customer wallet.
     */
    public function verifyRecharge(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Please login first'], 401);
        }

        $razorpayPaymentId = $request->razorpay_payment_id;
        $razorpayOrderId = $request->razorpay_order_id;
        $razorpaySignature = $request->razorpay_signature;

        $keySecret = config('services.razorpay.secret', 'gJsZEy9b2Papg9tByq2MYMpW');
        $expectedSignature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $keySecret);

        if ($expectedSignature !== $razorpaySignature) {
            return response()->json(['success' => false, 'message' => 'Payment signature verification failed'], 400);
        }

        $amount = $request->amount;
        $customer->increment('wallet_balance', $amount);

        WalletTransaction::create([
            'customer_id' => $customer->id,
            'user_type' => 'customer',
            'transaction_type' => 'credit',
            'credit' => $amount,
            'debit' => 0,
            'balance_after' => $customer->wallet_balance,
            'description' => 'Wallet recharge via Razorpay',
            'status' => 'success',
            'reference_id' => $razorpayPaymentId,
        ]);

        return response()->json([
            'success' => true,
            'new_balance' => number_format($customer->wallet_balance, 2),
            'message' => 'Wallet recharged successfully!'
        ]);
    }

    /**
     * Redeem reward points.
     */
    public function redeemReward(Request $request)
    {
        $points = intval($request->points);
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Please login first']);
        }

        $validPoints = Reward::getValidPoints($customer->id);

        if ($validPoints >= $points && $points >= 100) {
            // Deduct reward points in the log using a negative points transaction (FIFO)
            Reward::create([
                'customer_id' => $customer->id,
                'type'        => 'points',
                'points'      => -$points,
                'amount'      => -($points / 10),
                'description' => 'Redeemed ' . $points . ' points',
                'status'      => 'completed',
                'expiry_date' => now(),
            ]);

            // Sync customer's points
            $customer->reward_points = Reward::getValidPoints($customer->id);
            $customer->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Insufficient valid points or below minimum of 100 points']);
    }

    /**
     * Toggle membership auto-renew.
     */
    public function toggleAutoRenew(Request $request)
    {
        $membership = Membership::where('customer_id', Auth::guard('customer')->id())->first();
        if ($membership) {
            $membership->auto_renew = $request->auto_renew;
            $membership->save();
        }
        return response()->json(['success' => true]);
    }

    /**
     * Remove an item from wishlist.
     */
    public function removeFromWishlist($id)
    {
        Wishlist::where('customer_id', Auth::guard('customer')->id())->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Generate referral link.
     */
    public function generateReferralLink()
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer->referral_code) {
            $customer->referral_code = strtoupper(substr($customer->name, 0, 3) . rand(100, 999));
            $customer->save();
        }
        return response()->json(['link' => url('/register?ref=' . $customer->referral_code)]);
    }

    /**
     * Change customer password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $customer = Auth::guard('customer')->user();
        if (!Hash::check($request->current_password, $customer->password)) {
            return response()->json(['success' => false, 'message' => 'Current password incorrect']);
        }

        $customer->password = Hash::make($request->new_password);
        $customer->save();
        return response()->json(['success' => true]);
    }

    /**
     * Logout from all other devices.
     */
    public function logoutAllDevices(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $currentSessionId = Session::getId();
        DeviceSession::where('customer_id', $customer->id)
            ->where('session_id', '!=', $currentSessionId)
            ->delete();

        $request->validate(['password' => 'required|current_password:customer']);
        Auth::guard('customer')->logoutOtherDevices($request->password);

        return response()->json(['success' => true]);
    }

    /**
     * Permanently delete the customer account.
     */
    public function deleteAccount(Request $request)
    {
        $request->validate(['confirmation' => 'required|in:DELETE']);
        $customer = Auth::guard('customer')->user();
        $customer->delete();
        Auth::guard('customer')->logout();
        return response()->json(['success' => true, 'redirect' => '/']);
    }

    /**
     * Buy again (add previous order items to cart).
     */
    public function buyAgain($orderId)
    {
        $order = Order::where('customer_id', Auth::guard('customer')->id())->findOrFail($orderId);
        // Implement your cart logic here
        return response()->json(['success' => true]);
    }

    /**
     * Update settings (language, notifications, default address).
     */
    public function updateSettings(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $customer->update($request->only(['language', 'notification_preferences', 'default_address_id']));
        return response()->json(['success' => true]);
    }
    
    /**
     * Close an enquiry (change status to 'Closed')
     */
    public function closeEnquiry(Request $request)
    {
        $request->validate(['enquiry_id' => 'required|exists:enquiries,id']);
        $enquiry = Enquiry::where('id', $request->enquiry_id)
            ->where('user_id', Auth::guard('customer')->id())
            ->first();

        if (!$enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.']);
        }

        $enquiry->status = 'Closed';
        $enquiry->save();

        return response()->json(['success' => true]);
    }

    /**
     * Upload enquiry attachment.
     */
    public function uploadEnquiryAttachment(Request $request)
    {
        $request->validate([
            'enquiry_id' => 'required|exists:enquiries,id',
            'attachment' => 'required|file|image|max:5120',
        ]);

        $enquiry = Enquiry::where('id', $request->enquiry_id)
            ->where('user_id', Auth::guard('customer')->id())
            ->first();

        if (!$enquiry) {
            return response()->json(['success' => false, 'message' => 'Enquiry not found.']);
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('uploads/enquiries');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $enquiry->attachment = 'uploads/enquiries/' . $filename;
            $enquiry->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Attachment uploaded successfully.',
            'attachment_url' => asset($enquiry->attachment)
        ]);
    }

    /**
     * Participate in a festival campaign to earn 10 points.
     */
    public function participateCampaign($slug)
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Please login first'], 401);
        }

        $campaign = \App\Models\PromotionCampaign::where('slug', $slug)->first();
        if (!$campaign) {
            return response()->json(['success' => false, 'message' => 'Campaign not found'], 404);
        }

        // Check if already participated/rewarded for this campaign
        $exists = Reward::where('customer_id', $customer->id)
            ->where('description', 'like', '%Festival Campaign participation: ' . $campaign->name . '%')
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Already participated in this campaign'], 422);
        }

        Reward::create([
            'customer_id' => $customer->id,
            'type'        => 'points',
            'points'      => 10,
            'amount'      => 1.00, // 10 points = ₹1
            'description' => 'Reward points for Festival Campaign participation: ' . $campaign->name,
            'status'      => 'active',
            'expiry_date' => now()->addDays(60), // 60 days validity
        ]);

        // Sync customer points
        $customer->reward_points = Reward::getValidPoints($customer->id);
        $customer->save();

        return response()->json(['success' => true, 'message' => 'Thank you for participating! 10 reward points credited.']);
    }
}