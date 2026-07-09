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
use App\Models\StockMovement;
use App\Services\NotificationService;


use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;

class VshopOrderController extends Controller
{
    // ... existing methods ...

    /**
     * Decrement stock and log movement
     */
    protected function decrementStockAndLog(int $productId, int $qty, int $orderId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }
        $stockBefore = $product->stock_qty ?? 0;
        // Decrement stock
        $product->decrement('stock_qty', $qty);
        $stockAfter = $product->stock_qty;

        // Log movement
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

    // ================= CART / BUY NOW =================
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

    // ================= CALCULATE ORDER SUMMARY (AFTER ADDRESS) =================
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

        $referrerId = $customer->referred_by ?? null;
        if (empty($referrerId) && $request->filled('referral_code')) {
            $referrer = \App\Models\Customer::where('referral_code', $request->referral_code)
                ->where('id', '!=', $customer->id)
                ->first();
                
            if ($referrer) {
                $referrerId = $referrer->id;
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
            } else {
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

        $paymentStatus = 'paid'; // Force paid for all payment methods (COD + UPI)
        $orderStatus = 'delivered'; // Force delivered so Return/Refund button is immediately available

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

        // Process Buyer-to-Buyer Referral Commission
        $historyCommPercent = 0;
        $historyAmount = 0;
        
        if ($referrerId) {
            $hasOrderedBefore = \App\Models\Order::where('customer_id', $customer->id)
                ->where('seller_id', $sellerId)
                ->where('id', '!=', $order->id)
                ->exists();

            if (!$hasOrderedBefore) {
                $config = DB::table('referral_configurations')
                    ->where('type', 'buyer_to_buyer')
                    ->where('reference_id', $sellerId)
                    ->where('status', 1)
                    ->first();

                if ($config) {
                    $commPercent = $config->commission_percentage ?? 0;
                    if ($commPercent > 0) {
                        // Determine points from the RewardRedemptionTier set by admin
                        $tier = \App\Models\RewardRedemptionTier::where('discount_amount', $commPercent)
                            ->where('status', 'active')
                            ->first();

                        if ($tier) {
                            $rewardPoints = $tier->points_required;
                            $rewardAmount = $rewardPoints; // Assuming 1 point = 1 currency unit for display
                        } else {
                            $rewardAmount = ($subTotal * $commPercent) / 100;
                            $rewardPoints = (int) $rewardAmount;
                        }
                        
                        $historyCommPercent = $commPercent;
                        
                        // We also need to factor in the discount the seller gave to the buyer
                        $configExtra = json_decode($config->description, true);
                        $refereeDiscountPercent = $configExtra['referee_discount'] ?? 0;
                        $discountGiven = 0;
                        if ($refereeDiscountPercent > 0) {
                            $discountGiven = ($subTotal * $refereeDiscountPercent) / 100;
                        }
                        
                        // Amount deducted from seller: Commission given to referrer + Discount given to buyer
                        $historyAmount = $rewardAmount + $discountGiven;
                        
                        \App\Models\Reward::create([
                            'customer_id' => $referrerId,
                            'type' => 'points',
                            'points' => $rewardPoints,
                            'amount' => $rewardAmount,
                            'description' => "Buyer-to-Buyer referral commission for Customer #{$customer->id}'s first purchase from Seller #{$sellerId}",
                            'status' => 'active',
                            'expiry_date' => now()->addDays(365)
                        ]);

                        \App\Models\Referral::where('customer_id', $referrerId)
                            ->where('referred_customer_id', $customer->id)
                            ->increment('reward_amount', $rewardPoints);
                    }
                }
            }
        }

        // Record Referral History immediately so seller sees it
        if ($referrerId) {
            \App\Models\ReferralHistory::create([
                'referrer_id' => $referrerId,
                'buyer_id' => $customer->id,
                'seller_id' => $sellerId,
                'order_id' => $order->id,
                'amount' => $historyAmount, 
                'commission_percent' => $historyCommPercent,
                'status' => 'pending',
            ]);
        }

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

        /* ================= ORDER ITEMS ================= */

        foreach ($cartItems as $item) {

            $price = $item->main_product_price ?? 0;
            $qty   = $item->main_product_quantity ?? 1;
            $productId = $item->product_id;

            $product = DB::table('products')
                ->where('id', $productId)
                ->select('created_by_id')
                ->first();

            $sellerId = $product->created_by_id;

            $totalPrice = $price * $qty;

            /* ================= DYNAMIC COMMISSION CALCULATION ================= */

            $sellerComm = 20;
            $referrerComm = 10;
            $platformComm = 70;

            $appType = 'vshop';

            $specificRule = $commissionRules->first(function ($rule) use ($sellerId, $appType) {
                return $rule->seller_id == $sellerId && strtolower($rule->app_type) == $appType;
            });

            if ($specificRule) {
                $sellerComm = $specificRule->seller_commission;
                $referrerComm = $specificRule->referrer_commission;
                $platformComm = $specificRule->platform_commission;
            } else {
                $appRule = $commissionRules->whereNull('seller_id')->first(function ($rule) use ($appType) {
                    return strtolower($rule->app_type) == $appType;
                });
                if ($appRule) {
                    $sellerComm = $appRule->seller_commission;
                    $referrerComm = $appRule->referrer_commission;
                    $platformComm = $appRule->platform_commission;
                } else {
                    $generalRule = $commissionRules->whereNull('seller_id')->first();
                    if ($generalRule) {
                        $sellerComm = $generalRule->seller_commission;
                        $referrerComm = $generalRule->referrer_commission;
                        $platformComm = $generalRule->platform_commission;
                    }
                }
            }

            // Calculate amounts based on percentage rules
            $sellerEarning = ($totalPrice * $sellerComm) / 100;
            $commissionAmount = $totalPrice - $sellerEarning;

            $referralUserId = $customer->referred_by ?? null;
            $referralAmount = 0;

            if ($referralUserId) {
                $referralAmount = ($totalPrice * $referrerComm) / 100;

                DB::table('seller_finances')->insert([
                    'seller_id'         => $referralUserId,
                    'order_id'          => $order->id,
                    'transaction_type'  => 'referral',
                    'reference_no'      => 'ORD-' . $order->id,
                    'credit'            => $referralAmount,
                    'debit'             => 0,
                    'commission_amount' => $referralAmount,
                    'status'            => 'success',
                    'note'              => 'Referral earning',
                    'created_at'        => now(),
                    'updated_at'        => now()
                ]);
            }

            $platformAmount = ($totalPrice * $platformComm) / 100;

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

                'commission_percent' => $platformComm + $referrerComm,
                'commission_amount'  => $commissionAmount,
                'seller_earning'     => $sellerEarning,
                'referral_earning'   => $referralAmount,
                'platform_earning'   => $platformAmount
            ]);
            $this->decrementStockAndLog($productId, $qty, $order->id);



            /* ================= SELLER EARNING ================= */

            DB::table('seller_finances')->insert([
                'seller_id'         => $sellerId,
                'order_id'          => $order->id,
                'transaction_type'  => 'earning',
                'reference_no'      => 'ORD-' . $order->id,
                'credit'            => $sellerEarning,
                'debit'             => 0,
                'commission_amount' => $commissionAmount,
                'status'            => 'success',
                'note'              => 'Order sale earning',
                'created_at'        => now(),
                'updated_at'        => now()
            ]);
        }

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

    // --- BUYER-TO-BUYER REFERRAL LOGIC FOR DISPLAY ---
    $customer = Auth::guard('customer')->user();
    $referralDiscount = 0;
    
    if ($customer && $customer->referred_by) {
        // Group items by seller to check first purchase per seller
        $sellerIds = [];
        foreach ($cartItems as $item) {
            $sId = $item->product->created_by_id ?? null;
            if ($sId) {
                $sellerIds[$sId] = ($sellerIds[$sId] ?? 0) + (($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1));
            }
        }

        foreach($sellerIds as $sellerId => $sellerSubtotal) {
            $hasOrderedBefore = DB::table('orders')
                ->where('customer_id', $customer->id)
                ->where('seller_id', $sellerId)
                ->exists();

            if (!$hasOrderedBefore) {
                $config = DB::table('referral_configurations')
                    ->where('type', 'buyer_to_buyer')
                    ->where('reference_id', $sellerId)
                    ->where('status', 1)
                    ->first();

                if ($config) {
                    $configExtra = json_decode($config->description, true);
                    $refereeDiscountPercent = $configExtra['referee_discount'] ?? 0;

                    if ($refereeDiscountPercent > 0) {
                        $referralDiscount += $sellerSubtotal * ($refereeDiscountPercent / 100);
                    }
                }
            }
        }
    }
    
    $summary['referral_discount'] = $referralDiscount;

    $summary['grand_total'] =
        $summary['subtotal'] +
        $summary['cgst'] +
        $summary['sgst'] +
        $summary['igst'] -
        $couponDiscount -
        $rewardDiscount -
        $referralDiscount;

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

        DB::table('order_cancels')->insert([
            'order_id' => $order->id,
            'customer_id' => auth()->id(),
            'reason' => $request->reason,
            'note' => $request->note,
            'status' => 'pending_seller',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // If order was paid, create return request routed to seller first
        if (in_array($order->payment_status, ['paid', 'completed']) && $order->total_amount > 0) {
            \App\Models\OrderReturn::create([
                'order_id'    => $order->id,
                'customer_id' => $order->customer_id,
                'reason'      => 'Order Cancelled: ' . $request->reason,
                'note'        => $request->note,
                'status'      => 'requested', // Goes to seller first
                'seller_action_deadline' => $sellerDeadline,
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

        return response()->json(['message' => 'Cancellation request sent to seller. If not resolved in 3 days, it will auto-escalate to admin.']);
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
}
