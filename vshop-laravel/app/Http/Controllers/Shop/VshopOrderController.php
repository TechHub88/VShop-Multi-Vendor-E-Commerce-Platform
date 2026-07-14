<?php

namespace App\Http\Controllers\Shop;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\SellerFinance;
use App\Models\Tax;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\StockMovement;
use App\Services\NotificationService;

use Illuminate\Support\Facades\Session;

class VshopOrderController extends Controller
{
    
    protected function decrementStockAndLog(int $productId, int $qty, int $orderId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }
        $stockBefore = $product->stock_qty ?? 0;
    
        $product->decrement('stock_qty', $qty);
        $stockAfter = $product->stock_qty;

        StockMovement::create([
            'product_id' => $productId,
            'type' => 'out',
            'quantity_changed' => -$qty,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'reference' => 'order_' . $orderId,
            'reason' => 'Order placed',
            'user_name' => Auth::guard('customer')->user()->name ?? 'Customer',
        ]);
    }


    protected $guard = 'customer';



private function notifySeller($sellerId, $type, $message, $route = null, $hash = '')
{
    try {
        if(!$sellerId) return;

        $link = $route ? route($route) . $hash : null;
    NotificationService::notify(
            $sellerId,
            'seller',
            $type,
            $message,
            $link
        );
    } catch (\Exception $e) {
        \Log::error('Notification Error: '.$e->getMessage());
    }
}
 
public function payment(Request $request)
{
    $customer = Auth::guard('customer')->user();

    if (!$customer) {
        return redirect()->route('customer.login')
            ->with('error', 'Please login first');
    }

    
    if ($request->has('is_buy_now')) {

        $product = Product::find($request->product_id);

        if (!$product) {
            return redirect()->route('vshop.cart.index')
                ->with('error', 'Product not found');
        }

        $cartItems = collect([
            (object)[
                'product' => $product,
                'product_id' => $product->id,
                'main_product_price' => $request->price,
                'main_product_quantity' => $request->quantity,
                'gst_percent' => 5,
                'courier_charge' => 0
            ]
        ]);

    } else {

        $cartItems = CartItem::where('customer_id', $customer->id)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('vshop.cart.index')
                ->with('error', 'Cart is empty');
        }
    }

    // Save address parameters to session if present in request
    if ($request->has('delivery_id') && $request->has('billing_id')) {
        session([
            'order.delivery_address_id' => $request->delivery_id,
            'order.billing_address_id'  => $request->billing_id,
        ]);
    }

    // ================= GET ADDRESS FROM SESSION (IMPORTANT) =================
    $deliveryId = session('order.delivery_address_id');
    $billingId  = session('order.billing_address_id');

    if ($deliveryId && $billingId) {

        $deliveryAddress = Address::where('id', $deliveryId)
            ->where('customer_id', $customer->id)
            ->first();

        $billingAddress = Address::where('id', $billingId)
            ->where('customer_id', $customer->id)
            ->first();

    } else {

        // fallback (first/default address)
        $deliveryAddress = Address::where('customer_id', $customer->id)
            ->where('is_default', 1)
            ->first()
            ?? Address::where('customer_id', $customer->id)->first();

        $billingAddress = $deliveryAddress;

        // 🔥 SAVE DEFAULT IN SESSION
        if ($deliveryAddress) {
            session([
                'order.delivery_address_id' => $deliveryAddress->id,
                'order.billing_address_id'  => $billingAddress->id,
            ]);
        }
    }

    if (!$deliveryAddress || !$billingAddress) {
        return redirect()->route('vshop.address.index')
            ->with('error', 'Please select address first');
    }

    
    $orderSummary = $this->calculateOrderSummary($cartItems);

    $availableCoupons = \App\Models\Coupon::where('status', 'active')
        ->where(function($q) {
            $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
        })
        ->get();

    $rewardPoints = \App\Models\Reward::getValidPoints($customer->id);
    $walletBalance = $customer->wallet_balance ?? 0;
    
    $rewardTiers = \App\Models\RewardRedemptionTier::where('status', 'active')
        ->orderBy('points_required', 'asc')
        ->get();

    return view('vshop.order.payment', compact(
        'cartItems',
        'orderSummary',
        'deliveryAddress',
        'billingAddress',
        'availableCoupons',
        'rewardPoints',
        'walletBalance',
        'rewardTiers'
    ))->with([
        'isBuyNow' => $request->has('is_buy_now')
    ]);
}


public function placeOrder(Request $request)
{
    try {

        $request->validate([
            'payment_method' => 'required|array',
            'razorpay_payment_id' => 'nullable|string',
            'razorpay_order_id' => 'nullable|string',
            'razorpay_signature' => 'nullable|string',
        ]);

        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return back()->with('error', 'Please login first');
        }

        $hasPastOrders = \App\Models\Order::where('customer_id', $customer->id)->exists();
        $referrerId = null;

        $referralCode = $request->referral_code ?? session('referral_code') ?? request()->cookie('referral_code');
        
        $hasPreviousPaidReferralTop = \App\Models\ReferralHistory::where('buyer_id', $customer->id)
            ->where('status', '!=', 'reversed')
            ->exists();

        if (!empty($referralCode) && !$hasPreviousPaidReferralTop) {
            $referrer = \App\Models\Customer::where('referral_code', $referralCode)->first();
            if ($referrer && $referrer->id !== $customer->id) {
                $referrerId = $referrer->id;
                
                if (empty($customer->referred_by)) {
                    $customer->referred_by = $referrerId;
                    $customer->save();
                    
                    \App\Models\Referral::firstOrCreate([
                        'customer_id' => $referrerId,
                        'referred_customer_id' => $customer->id,
                    ], [
                        'referral_code' => $referrer->referral_code,
                        'status' => 'successful',
                        'reward_amount' => 0,
                        'completed_at' => now(),
                    ]);
                }
            } elseif (!$referrer && !empty($referralCode)) {
                return back()->with('error', 'Invalid referral code provided.');
            }
        }

        $primaryPaymentMethod = is_array($request->payment_method) ? ($request->payment_method[0] ?? 'cod') : $request->payment_method;
        $request->merge(['payment_method' => $primaryPaymentMethod]);

        $deliveryId = session('order.delivery_address_id');
        $billingId  = session('order.billing_address_id');

        if (!$deliveryId || !$billingId) {
            return redirect()->route('vshop.address.index')
                ->with('error', 'Please select address first');
        }

        /* ================= BUY NOW OR CART ================= */

        $isBuyNow = $request->is_buy_now;

        if ($isBuyNow) {

            // 👉 BUY NOW FLOW
            $productId = $request->product_id;
            $qty       = $request->quantity ?? 1;
            $price     = $request->price ?? 0;

            $product = DB::table('products')
                ->where('id', $productId)
                ->select('created_by_id')
                ->first();

            if (!$product) {
                return back()->with('error', 'Product not found');
            }

            // Fake cart item
            $buyNowAttributes = [];
            if ($request->has('size_name')) {
                $buyNowAttributes['size'] = [
                    'id' => $request->size_id,
                    'name' => $request->size_name,
                    'value' => null
                ];
            }
            if ($request->has('color_name')) {
                $buyNowAttributes['color'] = [
                    'id' => $request->color_id,
                    'name' => $request->color_name,
                    'value' => null
                ];
            }
            if ($request->has('selected_slots') && is_array($request->selected_slots)) {
                $buyNowAttributes['Booking Slots'] = [
                    'id' => null,
                    'name' => implode(', ', $request->selected_slots),
                    'value' => null
                ];
            }

            $cartItems = collect([
                (object)[
                    'product_id' => $productId,
                    'main_product_price' => $price,
                    'main_product_quantity' => $qty,
                    'gst_percent' => 5,
                    'color_id' => $request->color_id ?? null,
                    'size_id' => $request->size_id ?? null,
                    'main_product_attributes' => json_encode($buyNowAttributes)
                ]
            ]);

        } else {

            // 👉 NORMAL CART FLOW
            $cartItems = CartItem::where('customer_id', $customer->id)->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('vshop.cart.index')
                    ->with('error', 'Cart is empty');
            }
        }

        DB::beginTransaction();

        $subTotal = 0;
        $gstTotal = 0;
        $courier  = 0;
        $sellerId = null;

        $address = Address::find($deliveryId);
        $customerState = $address->state ?? '';
        $gstPercent = $this->getTaxPercentForState($customerState);

        /* ================= CALCULATE TOTAL ================= */

        foreach ($cartItems as $item) {

            $price = $item->main_product_price ?? 0;
            $qty   = $item->main_product_quantity ?? 1;
            $gst   = $gstPercent;

            $product = DB::table('products')
                ->where('id', $item->product_id)
                ->select('name', 'type', 'stock_qty', 'created_by_id')
                ->first();

            if (!$product) {
                throw new \Exception("Product not found");
            }

            $isPhysicalProduct = in_array($product->type, ['product', 'vshop-product']) || empty($product->type);

            if ($isPhysicalProduct) {
                if (is_null($product->stock_qty) || $product->stock_qty <= 0) {
                    throw new \Exception("Product '{$product->name}' is out of stock.");
                }
                if ($qty > $product->stock_qty) {
                    throw new \Exception("Requested quantity for '{$product->name}' exceeds available stock ({$product->stock_qty} available).");
                }
            }

            $sellerId = $product->created_by_id;

            $lineTotal = $price * $qty;
            $gstAmount = ($lineTotal * $gst) / 100;

            $subTotal += $lineTotal;
            $gstTotal += $gstAmount;
            $courier  += ($item->courier_charge ?? 0); // Dynamic courier charge calculation
        }

        // Apply coupon discount if applicable
        $couponDiscount = 0;
        $couponCode = null;
        if (session()->has('order.coupon')) {
            $sessionCoupon = session('order.coupon');
            $couponCode = $sessionCoupon['code'] ?? null;
            $coupon = \App\Models\Coupon::where('discount_code', $couponCode)->first();
            if ($coupon && $coupon->isValid() && $coupon->canApplyTo($subTotal)) {
                $couponDiscount = $coupon->calculateDiscount($subTotal);
                
                // Increment usage counter
                $coupon->used_count += 1;
                $coupon->save();
            }
        }

        $rewardDiscount = 0;
        $appliedPoints = 0;
        if (session()->has('order.reward_discount')) {
            $appliedPoints = session('order.reward_points');
            $rewardDiscount = session('order.reward_discount');

            // Verify points again
            $validPoints = \App\Models\Reward::getValidPoints($customer->id);
            if ($validPoints < $appliedPoints) {
                throw new \Exception("Insufficient reward points.");
            }
        }

        $grandTotalBeforeWallet = $subTotal + $gstTotal + $courier - $couponDiscount - $rewardDiscount;
        if ($grandTotalBeforeWallet < 0) $grandTotalBeforeWallet = 0;

        $useWallet = $request->input('use_wallet') == 1;
        $walletBalance = $customer->wallet_balance ?? 0;
        $walletDeduction = 0;
        $grandTotal = $grandTotalBeforeWallet;

        if ($useWallet) {
            $walletDeduction = min($walletBalance, $grandTotalBeforeWallet);
            $grandTotal = $grandTotalBeforeWallet - $walletDeduction;
        }

        // Perform payment method checks
        if ($grandTotal > 0) {
            if ($request->payment_method === 'wallet') {
                throw new \Exception("Not have sufficient balance, use another method");
            } elseif (in_array($request->payment_method, ['upi', 'card'])) {
                
                $razorpayPaymentId = $request->razorpay_payment_id;
                $razorpayOrderId = $request->razorpay_order_id;
                $razorpaySignature = $request->razorpay_signature;

                if (!$razorpayPaymentId || !$razorpayOrderId || !$razorpaySignature) {
                    throw new \Exception("Razorpay payment details missing.");
                }

                $keySecret = config('services.razorpay.secret', 'gJsZEy9b2Papg9tByq2MYMpW');
                $expectedSignature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $keySecret);

                if ($expectedSignature !== $razorpaySignature) {
                    throw new \Exception("Razorpay payment signature verification failed.");
                }
            }
        }

        // Determine stored payment method description
        $paymentMethodStored = $request->payment_method;
        if ($useWallet && $walletDeduction > 0) {
            if ($grandTotal == 0) {
                $paymentMethodStored = 'wallet';
            } else {
                $paymentMethodStored = 'wallet_' . $request->payment_method;
            }
        }

        $paymentStatus = in_array($request->payment_method, ['upi', 'card', 'cod']) || $grandTotal == 0 ? 'paid' : 'pending';
        $orderStatus = $paymentStatus === 'paid' ? 'paid' : 'pending'; // User requested order status to match payment status

        // ================= CREATE ORDER =================
        $order = Order::create([
            'seller_id'           => $sellerId,
            'customer_id'         => $customer->id,
            'payment_method'      => $paymentMethodStored,
            'payment_status'      => $paymentStatus,
            'sub_total'           => $subTotal,
            'gst_amount'          => $gstTotal,
            'courier_charge'      => $courier,
            'discount_amount'     => $couponDiscount + $rewardDiscount,
            'voucher'             => $couponCode,
            'total_amount'        => $grandTotalBeforeWallet,
            'status'              => $orderStatus,
            'delivery_address_id' => $deliveryId,
            'billing_address_id'  => $billingId,
            'order_date'          => now(),
            'referrer_id'         => $referrerId,
        ]);

        // Referral commission is processed dynamically per item inside the cart items loop below.

        // Record Wallet Transaction if wallet was used
        if ($useWallet && $walletDeduction > 0) {
            $customer->decrement('wallet_balance', $walletDeduction);

            \App\Models\WalletTransaction::create([
                'customer_id' => $customer->id,
                'user_type' => 'customer',
                'transaction_type' => 'payment',
                'debit' => $walletDeduction,
                'credit' => 0,
                'balance_after' => $customer->wallet_balance ?? 0,
                'description' => 'Payment for Order #' . $order->id . ' (Wallet Portion)',
                'status' => 'completed',
                'reference_id' => 'ORD-' . $order->id,
            ]);
        }

        // Deduct reward points if points were used
        if ($appliedPoints > 0) {
            \App\Models\Reward::create([
                'customer_id' => $customer->id,
                'type'        => 'points',
                'points'      => -$appliedPoints,
                'amount'      => -$rewardDiscount,
                'description' => 'Redeemed ' . $appliedPoints . ' points for Order #' . $order->id,
                'status'      => 'completed',
                'expiry_date' => now(),
            ]);

            // Sync customer points
            $customer->reward_points = \App\Models\Reward::getValidPoints($customer->id);
            $customer->save();
        }

        // ================= GENERATE REWARDS =================
        $prevOrdersCount = \App\Models\Order::where('customer_id', $customer->id)
            ->where('id', '!=', $order->id)
            ->count();

        $pointsEarned = 5;
        $description = 'Reward points earned for Order #' . $order->id;

        if ($prevOrdersCount == 0 && empty($referrerId)) {
            // First order sliding scale: 15 points + 50 points Login & First Order bonus
            $pointsEarned = 15 + 50; 
            $description = 'Login & First Order Reward Points (15 base + 50 bonus) for Order #' . $order->id;
        } elseif ($prevOrdersCount == 0 && !empty($referrerId)) {
            $pointsEarned = 0; // No points for the buyer if they use a referral code
        } elseif ($prevOrdersCount == 1) {
            // Second order sliding scale: 10 points
            $pointsEarned = 10;
            $description = 'Reward points earned for Second Order #' . $order->id;
        } else {
            // 3rd order onwards sliding scale: 5 points
            $pointsEarned = 5;
            $description = 'Reward points earned for Order #' . $order->id;
        }

        if ($pointsEarned > 0) {
            \App\Models\Reward::create([
                'customer_id' => $customer->id,
                'type'        => 'points',
                'points'      => $pointsEarned,
                'amount'      => $pointsEarned / 10,
                'description' => $description,
                'status'      => 'active',
                'expiry_date' => now()->addDays(60), // Expires in exactly 60 days
            ]);

            // Sync customer's cumulative points
            $customer->reward_points = \App\Models\Reward::getValidPoints($customer->id);
            $customer->save();
        }

        // Notify customer about the order
        NotificationService::notifyCustomer(
            $customer->id,
            'order_placed',
            'Your order #' . $order->id . ' has been placed successfully.',
            route('vshop.order.confirmation', $order->id)
        );
        
        // 🔔 NOTIFY SELLER (NEW ORDER)
$this->notifySeller(
    $sellerId,
    'order',
    'New Order Received (#'.$order->id.')',
    'seller.orders.index'
);

        // 🟢 WhatsApp Auto-Send Disabled by User Request
        /* ================= GLOBAL COMMISSION SETTINGS ================= */

        // Fetch all commission rules once to avoid repeated queries in loop
        $commissionRules = \Illuminate\Support\Facades\DB::table('commission_management')->get();
        $referralConfigs = \Illuminate\Support\Facades\DB::table('referral_configurations')->where('status', 1)->get();

        /* ================= ORDER ITEMS ================= */
        
        foreach ($cartItems as $item) {

            $price = $item->main_product_price ?? 0;
            $qty   = $item->main_product_quantity ?? 1;
            $productId = $item->product_id;

            $hasPreviousPaidReferralForProduct = \App\Models\ReferralHistory::where('buyer_id', $customer->id)
                ->where('status', '!=', 'reversed')
                ->whereHas('order.items', function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                ->exists();

            $product = DB::table('products')
                ->where('id', $productId)
                ->select('created_by_id', 'category_id', 'name')
                ->first();

            $sellerId = $product->created_by_id;

            $totalPrice = $price * $qty;

            /* ================= DYNAMIC COMMISSION CALCULATION ================= */

            $referrerComm = 0;
            $platformComm = 1.5; // Default fallback

            $appType = 'vshop';
            // Priority 1: Exact Match for this Seller
            $specificRule = $commissionRules->first(function ($rule) use ($sellerId, $appType) {
                return $rule->seller_id == $sellerId && strtolower($rule->app_type) == $appType;
            });
            
            if ($specificRule) {
                $platformComm = (float) $specificRule->platform_commission;
                $fallbackReferrerComm = (float) $specificRule->referrer_commission;
            } else {
                // Priority 2: Generic Match
                $generalRule = $commissionRules->whereNull('seller_id')->where('app_type', $appType)->first() 
                                ?? $commissionRules->whereNull('seller_id')->first();
                if ($generalRule) {
                    $platformComm = (float) $generalRule->platform_commission;
                    $fallbackReferrerComm = (float) $generalRule->referrer_commission;
                }
            }

            $referralCode = session('referral_code') ?? request()->cookie('referral_code');
            $referralUserId = null;
            if (!empty($referralCode) && !$hasPreviousPaidReferralForProduct) {
                $referrer = \App\Models\Customer::where('referral_code', $referralCode)->first();
                if ($referrer && $referrer->id !== $customer->id) {
                    $referralUserId = $referrer->id;
                }
            }
            $referralAmount = 0;
            $isSelfReferral = false;
            $appliedConfigType = 'level_commission';

            if ($referralUserId) {
                if ($referralUserId == $customer->id) {
                    $isSelfReferral = true;
                } else {
                    $appliedConfigType = 'level_commission';
                    // Priority B: Global Referral configurations
                    $config = $referralConfigs->whereIn('type', ['buyer_to_buyer', 'seller_to_seller'])
                                              ->where('reference_id', $productId)
                                              ->first();
                                              
                    if (!$config) {
                        $config = $referralConfigs->whereIn('type', ['buyer_to_buyer', 'seller_to_seller'])
                                                  ->where('reference_id', $sellerId)
                                                  ->first();
                    }
                                              
                    if (!$config) {
                        $config = $referralConfigs->where('type', 'level_commission')
                                                  ->where('reference_id', 1)
                                                  ->first();
                    }
                    
                    if ($config) {
                        $referrerComm = (float) $config->commission_percentage;
                        $referralAmount = ($totalPrice * $referrerComm) / 100;
                        $appliedConfigType = $config->type;
                        
                        // Apply Referee Reward Points
                        $configDesc = json_decode($config->description, true);
                        $refereeDiscount = $configDesc['referee_discount'] ?? 0;
                        if ($refereeDiscount > 0) {
                            $refereeRewardAmount = ($totalPrice * $refereeDiscount) / 100;
                            \App\Models\Reward::create([
                                'customer_id' => $customer->id,
                                'type' => 'points',
                                'points' => (int) $refereeRewardAmount,
                                'amount' => $refereeRewardAmount / 10,
                                'description' => "Referral Reward for purchasing {$product->name}",
                                'status' => 'active',
                                'expiry_date' => now()->addDays(180)
                            ]);
                            
                            // Sync customer points
                            $customer->reward_points = \App\Models\Reward::getValidPoints($customer->id);
                            $customer->save();
                        }
                    }
                }
            }
            
            $platformAmount = ($totalPrice * $platformComm) / 100;
            $commissionAmount = $referralAmount + $platformAmount;
            $sellerEarning = $totalPrice - $commissionAmount;

            // --- BUYER CATEGORY & PRODUCT REWARD POINTS ---
            $catReward = $referralConfigs->where('type', 'category_reward')->where('reference_id', $product->category_id)->first();
            if ($catReward) {
                $rewardValue = 0;
                if ($catReward->reward_type === 'fixed') {
                    $rewardValue = $catReward->reward_value * $qty;
                } else {
                    $rewardValue = ($totalPrice * $catReward->reward_value) / 100;
                }
                if ($rewardValue > 0) {
                    \App\Models\Reward::create([
                        'customer_id' => $customer->id,
                        'type' => 'points',
                        'points' => (int) $rewardValue,
                        'amount' => 0,
                        'description' => "Category Reward for purchasing {$product->name}",
                        'status' => 'active',
                        'expiry_date' => now()->addDays(180)
                    ]);
                }
            }

            $prodReward = $referralConfigs->where('type', 'product_campaign')->where('reference_id', $productId)->first();
            if ($prodReward) {
                $today = now()->format('Y-m-d');
                if ($today >= $prodReward->start_date && $today <= $prodReward->end_date) {
                    $rewardValue = $prodReward->reward_value * $qty;
                    if ($rewardValue > 0) {
                        \App\Models\Reward::create([
                            'customer_id' => $customer->id,
                            'type' => 'points',
                            'points' => (int) $rewardValue,
                            'amount' => 0,
                            'description' => "Product Campaign Reward for purchasing {$product->name}",
                            'status' => 'active',
                            'expiry_date' => now()->addDays(180)
                        ]);
                    }
                }
            }

            // --- REFERRER PAYOUT & LEDGER ---
            if ($referralUserId && $referralAmount > 0) {
                $referrerCustomer = \App\Models\Customer::find($referralUserId);
                if ($referrerCustomer) {
                    $shouldGivePoints = ($appliedConfigType === 'buyer_to_buyer');

                    // Check if referrer is listed in seller panel
                    $referrerSeller = null;
                    if (!$shouldGivePoints) {
                        $referrerSeller = \App\Models\Seller::where('email', $referrerCustomer->email)
                            ->orWhere('mobile', $referrerCustomer->phone)
                            ->first();
                    }

                    if ($referrerSeller) {
                        // Credit cash to referrer's seller wallet
                        $referrerWallet = \App\Models\Wallet::firstOrCreate(
                            ['user_type' => 'seller', 'seller_id' => $referrerSeller->id, 'customer_id' => null],
                            ['balance' => 0]
                        );
                        $referrerWallet->increment('balance', $referralAmount);

                        // Log transaction in wallet_transactions for the referrer seller
                        \App\Models\WalletTransaction::create([
                            'wallet_id' => $referrerWallet->id,
                            'seller_id' => $referrerSeller->id,
                            'user_type' => 'seller',
                            'transaction_type' => 'commission',
                            'credit' => $referralAmount,
                            'debit' => 0,
                            'balance_after' => $referrerWallet->balance,
                            'description' => "Referral commission earning for Order #{$order->id} (Seller partner)",
                            'reference_id' => 'ORD-' . $order->id,
                            'status' => 'completed'
                        ]);

                        // Log to seller_finances table so they see it in their seller panel finance ledger
                        DB::table('seller_finances')->insert([
                            'seller_id'         => $referrerSeller->id,
                            'order_id'          => $order->id,
                            'transaction_type'  => 'earning',
                            'reference_no'      => 'ORD-' . $order->id,
                            'credit'            => $referralAmount,
                            'debit'             => 0,
                            'commission_amount' => $referralAmount,
                            'status'            => 'success',
                            'note'              => 'Referral earning',
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ]);
                    } else {
                        // Dynamically fetch points from RewardRedemptionTier based on commission %
                        $pointsToGive = (int) $referralAmount;

                        // Credit reward points to customer referrer
                        $referrerCustomer->increment('reward_points', $pointsToGive);

                        // Log to rewards table
                        \App\Models\Reward::create([
                            'customer_id' => $referrerCustomer->id,
                            'type' => 'points',
                            'points' => (int) $pointsToGive,
                            'amount' => $referralAmount,
                            'description' => "Referral commission points for Customer #{$customer->id}'s purchase on Order #{$order->id}",
                            'status' => 'active',
                            'expiry_date' => now()->addDays(365)
                        ]);

                        // Create transaction record for customer in wallet_transactions with cash credit 0 since it is reward points
                        $referrerCustomerWallet = \App\Models\Wallet::firstOrCreate(
                            ['user_type' => 'customer', 'customer_id' => $referrerCustomer->id, 'seller_id' => null],
                            ['balance' => 0]
                        );
                        \App\Models\WalletTransaction::create([
                            'wallet_id' => $referrerCustomerWallet->id,
                            'customer_id' => $referrerCustomer->id,
                            'user_type' => 'customer',
                            'transaction_type' => 'commission',
                            'credit' => 0,
                            'debit' => 0,
                            'balance_after' => $referrerCustomerWallet->balance,
                            'description' => "Referral commission earning (credited as {$pointsToGive} reward points) for Order #{$order->id}",
                            'reference_id' => 'ORD-' . $order->id,
                            'status' => 'completed'
                        ]);
                    }

                    // Record Referral History so seller, buyer, and admin see it
                    \App\Models\ReferralHistory::create([
                        'referrer_id' => $referralUserId,
                        'buyer_id' => $customer->id,
                        'seller_id' => $sellerId,
                        'order_id' => $order->id,
                        'amount' => $referralAmount, 
                        'commission_percent' => $referrerComm,
                        'status' => 'paid',
                    ]);
                }
            }

            /* ================= CANCELLATION POLICY & HOLD LOGIC ================= */
            $cancelPolicy = $product->refund_policy ?? $product->cancel_policy_tour ?? $product->cancel_policy_prof ?? $product->cancel_policy_other ?? '';
            $holdDays = 7; // default 7 days
            
            if (!empty($cancelPolicy)) {
                // Try to extract number from string (e.g. "10 days", "24 hrs")
                if (preg_match('/(\d+)\s*(day|days|hrs|hours)/i', $cancelPolicy, $matches)) {
                    $num = (int)$matches[1];
                    $unit = strtolower($matches[2]);
                    if (str_starts_with($unit, 'hr') || str_starts_with($unit, 'hour')) {
                        $holdDays = max(1, ceil($num / 24));
                    } else {
                        $holdDays = $num;
                    }
                } elseif (stripos($cancelPolicy, 'no cancel') !== false || stripos($cancelPolicy, 'non-refundable') !== false) {
                    $holdDays = 0; // No hold if non-refundable? Or maybe standard 7 days. We'll use 7 days as default for non-refundable to be safe, or 0 if immediate. Let's use 7.
                    $holdDays = 7;
                }
            }
            
            $releaseDate = now()->addDays($holdDays)->toDateString();
            $financeStatus = $holdDays > 0 ? 'hold' : 'success';

            // --- SELLER WALLET CREDIT & LEDGER ---
            if ($holdDays == 0) {
                $sellerWallet = \App\Models\Wallet::firstOrCreate(
                    ['user_type' => 'seller', 'seller_id' => $sellerId, 'customer_id' => null],
                    ['balance' => 0]
                );
                $sellerWallet->increment('balance', $sellerEarning);

                // Log individual transactions in wallet_transactions for the seller:
                // 1. Credit 100% of order sale
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $sellerWallet->id,
                    'seller_id' => $sellerId,
                    'user_type' => 'seller',
                    'transaction_type' => 'payout',
                    'credit' => $totalPrice,
                    'debit' => 0,
                    'balance_after' => $sellerWallet->balance + $commissionAmount,
                    'description' => "Order sale earning for Order #{$order->id}",
                    'reference_id' => 'ORD-' . $order->id,
                    'status' => 'completed'
                ]);

                // 2. Debit platform fee
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $sellerWallet->id,
                    'seller_id' => $sellerId,
                    'user_type' => 'seller',
                    'transaction_type' => 'commission_deduction',
                    'credit' => 0,
                    'debit' => $platformAmount,
                    'balance_after' => $sellerWallet->balance + ($referralAmount > 0 ? $referralAmount : 0),
                    'description' => "Platform fee deduction for Order #{$order->id}",
                    'reference_id' => 'ORD-' . $order->id,
                    'status' => 'completed'
                ]);

                // 3. Debit referral commission
                if ($referralAmount > 0) {
                    \App\Models\WalletTransaction::create([
                        'wallet_id' => $sellerWallet->id,
                        'seller_id' => $sellerId,
                        'user_type' => 'seller',
                        'transaction_type' => 'referral_deduction',
                        'credit' => 0,
                        'debit' => $referralAmount,
                        'balance_after' => $sellerWallet->balance,
                        'description' => "Referral commission deduction for Order #{$order->id}",
                        'reference_id' => 'ORD-' . $order->id,
                        'status' => 'completed'
                    ]);
                }
            }

            // Log to seller_finances table so seller sees it in their finance panel
            DB::table('seller_finances')->insert([
                'seller_id'         => $sellerId,
                'order_id'          => $order->id,
                'transaction_type'  => 'earning',
                'reference_no'      => 'ORD-' . $order->id,
                'credit'            => $totalPrice,
                'debit'             => 0,
                'commission_amount' => 0,
                'status'            => $financeStatus,
                'note'              => $holdDays > 0 ? "Order sale earning (On Hold for {$holdDays} days)" : 'Order sale earning',
                'release_date'      => $releaseDate,
                'created_at'        => now(),
                'updated_at'        => now()
            ]);

            DB::table('seller_finances')->insert([
                'seller_id'         => $sellerId,
                'order_id'          => $order->id,
                'transaction_type'  => 'commission_deduction',
                'reference_no'      => 'ORD-' . $order->id,
                'credit'            => 0,
                'debit'             => $platformAmount,
                'commission_amount' => $platformAmount,
                'status'            => $financeStatus,
                'note'              => 'Platform fee deduction',
                'release_date'      => $releaseDate,
                'created_at'        => now(),
                'updated_at'        => now()
            ]);

            if ($referralAmount > 0) {
                DB::table('seller_finances')->insert([
                    'seller_id'         => $sellerId,
                    'order_id'          => $order->id,
                    'transaction_type'  => 'commission_deduction',
                    'reference_no'      => 'ORD-' . $order->id,
                    'credit'            => 0,
                    'debit'             => $referralAmount,
                    'commission_amount' => $referralAmount,
                    'status'            => $financeStatus,
                    'note'              => 'Referral commission deduction',
                    'release_date'      => $releaseDate,
                    'created_at'        => now(),
                    'updated_at'        => now()
                ]);
            }

            // --- ADMIN ESCROW WALLET LOAD & PAYOUTS ---
            $escrowWallet = \App\Models\Wallet::firstOrCreate(
                ['user_type' => 'platform', 'seller_id' => null, 'customer_id' => null],
                ['balance' => 0]
            );
            
            // 1. Credit full order amount to escrow
            $escrowWallet->increment('balance', $totalPrice);
            \App\Models\WalletTransaction::create([
                'wallet_id' => $escrowWallet->id,
                'user_type' => 'platform',
                'transaction_type' => 'payment',
                'credit' => $totalPrice,
                'debit' => 0,
                'balance_after' => $escrowWallet->balance,
                'description' => "Order payment loaded to escrow for Order #{$order->id}",
                'reference_id' => 'ORD-' . $order->id,
                'status' => 'completed'
            ]);

            // 2. Debit seller payout from escrow
            $escrowWallet->decrement('balance', $sellerEarning);
            \App\Models\WalletTransaction::create([
                'wallet_id' => $escrowWallet->id,
                'user_type' => 'platform',
                'transaction_type' => 'payout',
                'credit' => 0,
                'debit' => $sellerEarning,
                'balance_after' => $escrowWallet->balance,
                'description' => "Payout to Seller #{$sellerId} for Order #{$order->id}",
                'reference_id' => 'ORD-' . $order->id,
                'status' => 'completed'
            ]);

            // 3. Debit referral payout from escrow (if active)
            if ($referralAmount > 0) {
                $escrowWallet->decrement('balance', $referralAmount);
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $escrowWallet->id,
                    'user_type' => 'platform',
                    'transaction_type' => 'commission',
                    'credit' => 0,
                    'debit' => $referralAmount,
                    'balance_after' => $escrowWallet->balance,
                    'description' => "Referral commission payout for Order #{$order->id}",
                    'reference_id' => 'ORD-' . $order->id,
                    'status' => 'completed'
                ]);
            }

            /* ================= ORDER ITEM ================= */

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $productId,
                'quantity'   => $qty,
                'price'      => $price,
                'total'      => $totalPrice,

                'gst_percent'=> $gstPercent,

                'color_id'                 => $item->color_id ?? null,
                'size_id'                  => $item->size_id ?? null,
                'main_product_attributes'  => $item->main_product_attributes ?? null,

                'commission_percent' => $platformComm + ($referralAmount > 0 ? $referrerComm : 0),
                'commission_amount'  => $commissionAmount,
                'seller_earning'     => $sellerEarning,
                'referral_earning'   => $referralAmount,
                'platform_earning'   => $platformAmount
            ]);
            $this->decrementStockAndLog($productId, $qty, $order->id);
        }

        // Update the order's referral payout columns
        $totalReferralAmount = OrderItem::where('order_id', $order->id)->sum('referral_earning');
        // Retrieve the latest commission percent from the cart loop (assumes same rate for simplicity)
        $order->referral_commission_percent = $totalReferralAmount > 0 ? (isset($referrerComm) ? $referrerComm : 0) : 0;
        $order->referral_commission_amount = $totalReferralAmount;
        $order->referral_paid = $totalReferralAmount > 0 ? 1 : 0;
        if ($totalReferralAmount > 0 && isset($referralUserId)) {
            $order->referrer_id = $referralUserId;
        }
        $order->save();

        /* ================= CLEAR CART (ONLY IF NOT BUY NOW) ================= */

        if (!$isBuyNow) {
            CartItem::where('customer_id', $customer->id)->delete();
        NotificationService::notifyCustomer(
            $customer->id,
            'wishlist_remove',
            'Product removed from your wishlist.',
            route('vshop.wishlist.index')
        );
        }

        session()->forget([
            'order.delivery_address_id',
            'order.billing_address_id',
            'order.coupon',
            'order.reward_points',
            'order.reward_discount'
        ]);


        DB::commit();

        // Send booking confirmation email AFTER response is sent to avoid 502 Bad Gateway timeouts
        app()->terminating(function () use ($customer, $order) {
            try {
                $order->load('items.product');
                $customerEmail = $customer->email ?? '';
                $customerName  = $customer->name ?? 'Customer';
                if (!empty($customerEmail)) {
                    \App\Mail\BookingMsg::send($customerEmail, $customerName, $order, $order->items->all());
                }
            } catch (\Exception $mailEx) {
                \Illuminate\Support\Facades\Log::error('BookingMsg email failed: ' . $mailEx->getMessage());
            }
        });

        return redirect()->route('vshop.order.confirmation', $order->id)
            ->with('success', 'Order placed successfully!');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with('error', $e->getMessage());
    }
}






    public function confirmation($id)
    {
        $customer = Auth::guard('customer')->user();

        $order = Order::with(['items.product', 'items.color', 'items.size'])
            ->where('customer_id', $customer->id)
            ->where('id', $id)
            ->firstOrFail();

        return view('vshop.order.confirmation', compact('order'));
    }

    /**
     * Calculate order summary
     */
private function calculateOrderSummary($cartItems)
{
    $summary = [
        'subtotal' => 0,
        'cgst' => 0,
        'sgst' => 0,
        'igst' => 0,
        'courier_total' => 0,
        'grand_total' => 0,
        'items_count' => 0,
        'items' => []
    ];

    // Customer state (delivery address state)
    $deliveryId = session('order.delivery_address_id');
    $address = Address::where('id', $deliveryId)->first();
    $customerState = strtolower($address->state ?? '');
    $sellerState = 'west bengal';

    $gstPercent = $this->getTaxPercentForState($customerState);

    foreach ($cartItems as $item) {

        $price = $item->main_product_price ?? 0;
        $quantity = $item->main_product_quantity ?? 1;
        $courierCharge = $item->courier_charge ?? 0;

        $itemTotal = $price * $quantity;
        $gstAmount = ($itemTotal * $gstPercent) / 100;

        // SAME STATE
        if ($customerState == strtolower($sellerState)) {
            $cgst = $gstAmount / 2;
            $sgst = $gstAmount / 2;
            $igst = 0;
        } 
        // DIFFERENT STATE
        else {
            $cgst = 0;
            $sgst = 0;
            $igst = $gstAmount;
        }

        $summary['subtotal'] += $itemTotal;
        $summary['cgst'] += $cgst;
        $summary['sgst'] += $sgst;
        $summary['igst'] += $igst;
        $summary['courier_total'] += $courierCharge;
        $summary['items_count'] += $quantity;

        $productName = $item->product->name ?? $item->product_name ?? 'Product';

        $summary['items'][] = [
            'id' => $item->id ?? $item->product_id,
            'name' => $productName,
            'price' => $price,
            'quantity' => $quantity,
            'item_total' => $itemTotal
        ];
    }

    // Calculate coupon discount
    $couponDiscount = 0;
    $couponCode = null;
    if (session()->has('order.coupon')) {
        $sessionCoupon = session('order.coupon');
        $couponCode = $sessionCoupon['code'] ?? null;
        $coupon = \App\Models\Coupon::where('discount_code', $couponCode)->first();
        if ($coupon && $coupon->isValid() && $coupon->canApplyTo($summary['subtotal'])) {
            $couponDiscount = $coupon->calculateDiscount($summary['subtotal']);
            // update session with recalculated discount
            session(['order.coupon.discount' => $couponDiscount]);
        } else {
            session()->forget('order.coupon');
        }
    }

    $summary['coupon_code'] = $couponCode;
    $summary['coupon_discount'] = $couponDiscount;

    $rewardPercentage = session('order.reward_discount_percentage', 0);
    $rewardDiscount = 0;

    if ($rewardPercentage > 0) {
        $rewardDiscount = ($summary['subtotal'] * $rewardPercentage) / 100;
    }

    $summary['reward_discount'] = $rewardDiscount;
    $summary['reward_points'] = session('order.reward_points', 0);

    $summary['referral_discount'] = 0;

    $summary['grand_total'] =
        $summary['subtotal'] +
        $summary['cgst'] +
        $summary['sgst'] +
        $summary['igst'] -
        $couponDiscount -
        $rewardDiscount;

    return $summary;
}
    /**
     * Common error response handler
     */
    private function errorResponse($message, $isAjax, $status = 400, $errors = null, $redirect = null)
    {
        if ($isAjax) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors'  => $errors
            ], $status);
        }

        return $redirect
            ? redirect($redirect)->with('error', $message)
            : back()->with('error', $message);
    }
    
    public function storeAddress(Request $request)
{
    $request->validate([
        'delivery_id' => 'required|exists:addresses,id',
        'billing_id'  => 'required|exists:addresses,id',
    ]);

    session([
        'order.delivery_address_id' => $request->delivery_id,
        'order.billing_address_id'  => $request->billing_id,
    ]);

    return redirect()->route('vshop.payment.index');
}





/**
 * Order List Page
 */
public function orderList(Request $request)
{
    $customer = Auth::guard('customer')->user();

    $query = Order::where('customer_id', $customer->id);

    // Status filter
    if ($request->status && $request->status !== 'all') {
        $query->where('status', $request->status);
    }

    // Date filter
    if ($request->start_date && $request->end_date) {
        $query->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ]);
    }

    $orders = $query->latest()->paginate(10);

    return view('vshop.order.list', compact('orders'));
}

/**
 * Single Order Details
 */
public function orderDetails($id)
{
    $customer = Auth::guard('customer')->user();

    if (!$customer) {
        return redirect()->route('customer.login')
            ->with('error', 'Please login to view order details');
    }

    $order = Order::with([
        'items.product', 
        'items.color', 
        'items.size',
        'shippingAddress',
        'billingAddress',
        'customer',
        'activityLogs'
    ])
    ->where('customer_id', $customer->id)
    ->where('id', $id)
    ->firstOrFail();

    // Fallback estimated delivery date (Order date + 5 days) if null
    if (!$order->estimated_delivery_date) {
        $order->estimated_delivery_date = $order->order_date ? $order->order_date->addDays(5) : $order->created_at->addDays(5);
    }

    // Generate secure 4-digit Delivery OTP dynamically
    $deliveryOtp = str_pad(($order->id * 31 + 17) % 10000, 4, '0', STR_PAD_LEFT);

    // Fetch wallet portion used for this order
    $walletTransaction = \App\Models\WalletTransaction::where('customer_id', $customer->id)
        ->where(function($q) use ($order) {
            $q->where('reference_id', $order->id)
              ->orWhere('description', 'like', '%' . $order->id . '%');
        })
        ->where('debit', '>', 0)
        ->first();
    $walletDeduction = $walletTransaction ? $walletTransaction->debit : 0;

    // Return SLA tracking & eligibility (7 days from delivery date)
    $isReturnEligible = false;
    $returnDisabledReason = '';
    if ($order->status === 'delivered') {
        $deliveryDate = $order->delivered_at ?? $order->updated_at;
        if ($deliveryDate) {
            $daysSinceDelivery = now()->diffInDays($deliveryDate);
            if ($daysSinceDelivery <= 7) {
                $isReturnEligible = true;
            } else {
                $returnDisabledReason = "Return window (7 days) expired. Delivered " . $daysSinceDelivery . " days ago.";
            }
        } else {
            $isReturnEligible = true; // Fallback
        }
    }

    // Calculate order summary for each item
    foreach ($order->items as $item) {
        $item->gst_amount = $item->total * ($item->gst_percent / 100);
        $item->subtotal = $item->total + $item->gst_amount + ($order->courier_charge ?? 0);
    }

    // Calculate total items and quantity
    $totalItems = $order->items->count();
    $totalQuantity = $order->items->sum('quantity');

    // Get status badge class
    $statusClasses = [
        'pending' => 'badge-warning',
        'paid' => 'badge-success',
        'processing' => 'badge-info',
        'confirmed' => 'badge-primary',
        'dispatched' => 'badge-info',
        'shipped' => 'badge-primary',
        'delivered' => 'badge-success',
        'cancelled' => 'badge-danger',
        'refunded' => 'badge-secondary',
        'returned' => 'badge-secondary',
        'return_requested' => 'badge-warning',
        'return_approved' => 'badge-success',
        'replacement_requested' => 'badge-warning',
        'replacement_approved' => 'badge-success',
    ];

    // Get payment status badge class
    $paymentStatusClasses = [
        'pending' => 'badge-warning',
        'processing' => 'badge-info',
        'completed' => 'badge-success',
        'paid' => 'badge-success',
        'failed' => 'badge-danger',
        'refunded' => 'badge-secondary'
    ];
    
    $shippingAddress = Address::where('customer_id',$customer->id)
        ->where('address_type','shipping')
        ->first();

    $billingAddress = Address::where('customer_id',$customer->id)
        ->where('address_type','billing')
        ->first();

    return view('vshop.order.details', compact(
        'order', 
        'totalItems', 
        'totalQuantity',
        'statusClasses',
        'paymentStatusClasses',
        'shippingAddress',
        'billingAddress',
        'deliveryOtp',
        'walletDeduction',
        'isReturnEligible',
        'returnDisabledReason'
    ));
}

public function orderAction(Request $request)
{
    $order = Order::find($request->order_id);

    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    if ($request->type == 'cancel') {
        if (!in_array($order->status, ['pending', 'paid', 'confirmed', 'processing', 'delivered'])) {
            return response()->json(['message' => 'Order cannot be cancelled at this stage'], 422);
        }

        $sellerDeadline = now()->addDays(3);
        
        $cancelledDates = null;
        if ($request->has('cancelled_dates') && is_array($request->cancelled_dates)) {
            $cancelledDates = json_encode($request->cancelled_dates);
        }

        DB::table('order_cancels')->insert([
            'order_id' => $order->id,
            'customer_id' => auth()->id(),
            'reason' => $request->reason,
            'note' => $request->note,
            'cancelled_dates' => $cancelledDates,
            'status' => 'pending_seller',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // If order was paid, send to admin refund page instead of auto-refund
        if (in_array($order->payment_status, ['paid', 'completed']) && $order->total_amount > 0) {
            \App\Models\OrderReturn::create([
                'order_id'    => $order->id,
                'customer_id' => $order->customer_id,
                'reason'      => 'Order Cancelled: ' . $request->reason,
                'note'        => $request->note,
                'status'      => 'approved', // Maps to 'pending' in Admin Buyer Refunds
            ]);
        }

        $order->status = 'cancelled';
        $order->cancel_status = 'pending_seller';
        $order->seller_action_deadline = $sellerDeadline;
        $order->save();
        
        $this->notifySeller(
            $order->seller_id,
            'order',
            'Cancel & Refund Request for Order #' . $order->id . ' — Please act within 3 days',
            'seller.paymenthandle'
        );

        return response()->json(['message' => 'Order cancelled successfully. Refund request sent to admin.']);
    }

    if ($request->type == 'return' || $request->type == 'replace') {
        if ($order->status !== 'delivered') {
            return response()->json(['message' => 'Only delivered orders can be returned/replaced'], 422);
        }

        $deliveryDate = $order->delivered_at ?? $order->updated_at;
        if ($deliveryDate && now()->diffInDays($deliveryDate) > 7) {
            return response()->json(['message' => 'Return/Replacement window has expired (7 days policy)'], 422);
        }

        foreach ($order->items as $item) {
            if ($item->product && !$item->product->isReturnable()) {
                return response()->json([
                    'message' => 'Product "' . $item->product->name . '" is non-returnable due to health/hygiene policy.'
                ], 422);
            }
        }

        $actionType = $request->type;
        $refundMode = $request->refund_mode ?? 'wallet';
        $pickupDate = $request->pickup_date;
        $pickupTime = $request->pickup_time;

        $metaText = " [Refund Mode: " . ucfirst($refundMode) . 
                    " | Pickup: " . ($pickupDate ?? 'N/A') . " " . ($pickupTime ?? '') . 
                    " | Type: " . ucfirst($actionType) . "]";
        $fullNote = ($request->note ?? '') . $metaText;

        $data = [
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'reason' => $request->reason,
            'note' => $fullNote,
            'status' => 'requested',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            $destinationPath = public_path('uploads/returns');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            
            $file->move($destinationPath, $filename);
            $data['attachment'] = 'uploads/returns/' . $filename;
        }

        if (Schema::hasColumn('order_returns', 'refund_mode')) {
            $data['refund_mode'] = $refundMode;
        }
        if (Schema::hasColumn('order_returns', 'pickup_date')) {
            $data['pickup_date'] = $pickupDate;
        }
        if (Schema::hasColumn('order_returns', 'pickup_time')) {
            $data['pickup_time'] = $pickupTime;
        }
        if (Schema::hasColumn('order_returns', 'return_type')) {
            $data['return_type'] = $actionType;
        }
        if (Schema::hasColumn('order_returns', 'otp')) {
            $data['otp'] = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        }

        DB::table('order_returns')->insert($data);

        $sellerDeadline = now()->addDays(3);

        // Update the newly inserted return record with seller deadline
        DB::table('order_returns')
            ->where('order_id', $order->id)
            ->where('customer_id', $order->customer_id)
            ->whereNull('seller_action_deadline')
            ->update(['seller_action_deadline' => $sellerDeadline]);

        $order->status = ($actionType == 'replace') ? 'replacement_requested' : 'return_requested';
        $order->return_status_workflow = 'pending_seller';
        $order->seller_action_deadline = $sellerDeadline;
        $order->save();

        $this->notifySeller(
            $order->seller_id,
            'order',
            ucfirst($actionType) . ' Request for Order #' . $order->id . ' — Please act within 3 days',
            'seller.paymenthandle'
        );

        return response()->json([
            'message' => ucfirst($actionType) . ' request sent to seller for approval. If not resolved in 3 days, it will auto-escalate to admin.'
        ]);
    }

    // ========== RATE / REVIEW ==========
    if ($request->type == 'rate') {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['message' => 'Please login to submit review'], 401);
        }

        $productId = $request->product_id;
        $rating = (int)$request->rating;
        $review = $request->review;

        if ($rating < 1 || $rating > 5) {
            return response()->json(['message' => 'Invalid rating value'], 422);
        }

        // Check if already reviewed
        $existing = \App\Models\ProductReview::where('product_id', $productId)
            ->where('customer_id', $customer->id)
            ->first();

        if ($existing) {
            $existing->update([
                'rating' => $rating,
                'comment' => $review,
            ]);
            return response()->json(['message' => 'Review updated successfully!']);
        }

        \App\Models\ProductReview::create([
            'product_id' => $productId,
            'customer_id' => $customer->id,
            'user_name' => $customer->name,
            'user_email' => $customer->email ?? '',
            'rating' => $rating,
            'comment' => $review,
            'is_approved' => true,
            'is_verified_purchase' => true,
        ]);

        return response()->json(['message' => 'Review submitted successfully! Thank you.']);
    }

        return response()->json(['message' => 'Invalid action type'], 400);
    }



    public function downloadInvoice($id)
{
    $customer = Auth::guard('customer')->user();
    if (!$customer) {
        return redirect()->route('customer.login')->with('error', 'Please login to download invoice');
    }

    $order = Order::with([
        'items.product.category',
        'items.color',
        'items.size',
        'shippingAddress',
        'billingAddress',
        'customer'
    ])
    ->where('customer_id', $customer->id)
    ->where('id', $id)
    ->firstOrFail();

    if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('frontend.invoice', compact('order'))
                  ->setPaper('A4');
        return $pdf->download('Invoice_' . $order->id . '.pdf');
    }

    return redirect()->back()->with('error', 'PDF library not found.');
}

public function updateAddressSession(Request $request)
{
    $request->validate([
        'delivery_id' => 'required|exists:addresses,id',
        'billing_id'  => 'required|exists:addresses,id',
    ]);

    session([
        'order.delivery_address_id' => $request->delivery_id,
        'order.billing_address_id'  => $request->billing_id,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Address updated successfully'
    ]);
}

    public function applyCoupon(Request $request)
    
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'subtotal' => 'required|numeric'
        ]);

        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to apply coupon'
            ], 401);
        }

        $couponCode = strtoupper(trim($request->coupon_code));
        $subtotal = floatval($request->subtotal);

        $coupon = \App\Models\Coupon::where('discount_code', $couponCode)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ]);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon is expired or inactive'
            ]);
        }

        if (!$coupon->canApplyTo($subtotal)) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum spend of ₹ ' . number_format($coupon->minimum_spend, 2) . ' is required to use this coupon'
            ]);
        }

        $discount = $coupon->calculateDiscount($subtotal);

        // Save coupon details to session
        session([
            'order.coupon' => [
                'code' => $couponCode,
                'discount' => $discount
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'coupon_code' => $couponCode,
            'discount' => number_format($discount, 2),
            'raw_discount' => $discount
        ]);
    }

    public function removeCoupon()
    {
        session()->forget('order.coupon');

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully!'
        ]);
    }

    /**
     * Helper: retrieve dynamic tax rate from database based on buyer state/region.
     * Fallback to default global setting if not found.
     */
    private function getTaxPercentForState($state)
    {
        if (empty($state)) {
            return floatval(GlobalSetting::get('gst_rate', 18));
        }

        $tax = Tax::where('region', 'like', $state)->first();
        if (!$tax) {
            $tax = Tax::whereRaw('LOWER(region) = ?', [strtolower(trim($state))])->first();
        }

        if ($tax) {
            return floatval($tax->rate);
        }

        return floatval(GlobalSetting::get('gst_rate', 18));
    }

    public function createRazorpayOrder(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Please login first'], 401);
        }

        // ================= CART / BUY NOW =================
        if ($request->has('is_buy_now')) {
            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Product not found'], 404);
            }
            $cartItems = collect([
                (object)[
                    'product' => $product,
                    'product_id' => $product->id,
                    'main_product_price' => $request->price,
                    'main_product_quantity' => $request->quantity,
                    'gst_percent' => 5,
                    'courier_charge' => 220
                ]
            ]);
        } else {
            $cartItems = CartItem::where('customer_id', $customer->id)
                ->with('product')
                ->get();
            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
            }
        }

        $orderSummary = $this->calculateOrderSummary($cartItems);
        $grandTotal = $orderSummary['grand_total'];

        $useWallet = $request->input('use_wallet') == 1;
        if ($useWallet) {
            $walletBalance = $customer->wallet_balance ?? 0;
            $walletDeduction = min($walletBalance, $grandTotal);
            $grandTotal -= $walletDeduction;
        }

        // Amount in paise
        $amountInPaise = round($grandTotal * 100);

        // Call Razorpay API
        $key = config('services.razorpay.key', 'rzp_live_SSn9AQrMCCO84l');
        $secret = config('services.razorpay.secret', 'gJsZEy9b2Papg9tByq2MYMpW');

        try {
            $response = \Illuminate\Support\Facades\Http::withBasicAuth($key, $secret)
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                    'receipt' => 'rcpt_' . time(),
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
                'message' => 'Razorpay API error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function applyRewards(Request $request)
    {
        $request->validate([
            'points' => 'required|integer',
        ]);

        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to redeem reward points'
            ], 401);
        }

        $points = intval($request->points);
        $validPoints = \App\Models\Reward::getValidPoints($customer->id);

        if ($validPoints < $points) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient reward points! You have ' . $validPoints . ' points.'
            ]);
        }

        $tier = \App\Models\RewardRedemptionTier::where('points_required', $points)->where('status', 'active')->first();
        if (!$tier) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reward points tier selected.'
            ]);
        }

        $percentage = $tier->discount_percentage;

        // Save reward details to session
        session([
            'order.reward_points' => $points,
            'order.reward_discount_percentage' => $percentage
        ]);

        return response()->json([
            'success' => true,
            'message' => $points . ' Reward Points applied! Page will reload to calculate discount.',
            'points' => $points,
            'reload' => true
        ]);
    }

    public function removeRewards()
    {
        session()->forget([
            'order.reward_points',
            'order.reward_discount_percentage'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reward points removed successfully!'
        ]);
    }

    /**
     * Proxy route to validate meeting link
     */
    public function meetingProxy($itemId)
    {
        $item = OrderItem::with('product')->findOrFail($itemId);
        $product = $item->product;

        if (!$product || empty($product->meeting_link)) {
            return abort(404, 'Meeting link not found.');
        }

        // If it's a course (items with course category) or professional type, check end date
        if (!empty($product->end_date)) {
            $endDate = \Carbon\Carbon::parse($product->end_date);
            if (now()->startOfDay()->gt($endDate->endOfDay())) {
                return response()->view('errors.invalid_meeting', [
                    'message' => 'This meeting link has expired as the course/session end date has passed.'
                ], 403);
            }
        }

        // Redirect to the actual meeting link
        return redirect()->away($product->meeting_link);
    }

    /**
     * Apply referral code via AJAX
     */
    public function applyReferral(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'referral_code' => 'required|string'
        ]);

        $code = strtoupper(trim($request->referral_code));
        $customer = \Illuminate\Support\Facades\Auth::guard('customer')->user();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Please login first.']);
        }


        if ($customer->referral_code === $code) {
            return response()->json(['success' => false, 'message' => 'You cannot refer yourself.']);
        }

        $referrer = \App\Models\Customer::where('referral_code', $code)->first();
        if (!$referrer) {
            return response()->json(['success' => false, 'message' => 'Invalid referral code.']);
        }

        // Store in session and cookie
        session(['referral_code' => $code]);
        cookie()->queue('referral_code', $code, 60 * 24 * 30);

        return response()->json([
            'success' => true,
            'message' => 'Referral code successfully applied!'
        ]);
    }

    /**
     * Remove referral code via AJAX
     */
    public function removeReferral()
    {
        session()->forget('referral_code');
        cookie()->queue(cookie()->forget('referral_code'));

        return response()->json([
            'success' => true,
            'message' => 'Referral code removed.'
        ]);
    }
}
