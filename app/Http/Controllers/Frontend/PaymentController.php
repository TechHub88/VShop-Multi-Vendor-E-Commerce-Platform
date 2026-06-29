<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\About;
use App\Models\Category;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductAttribute;
use App\Models\Address;
use Illuminate\Support\Facades\DB;

use App\Models\Footer;
use Illuminate\Support\Facades\Auth;
use App\Models\Tax;
use App\Models\GlobalSetting;

class PaymentController extends Controller
{
  
  public function paymentMethod()
{
    $customerId = Auth::guard('customer')->id();

    $about = About::first(); 
    $categories = Category::all(); 
    $footer = Footer::first();

    $selectedAddressId = session('selected_address_id');
    $selectedAddress = null;
    if ($selectedAddressId) {
        $selectedAddress = Address::where('customer_id', $customerId)
            ->where('id', $selectedAddressId)
            ->first();
    }
    if (!$selectedAddress) {
        $selectedAddress = Address::where('customer_id', $customerId)->first();
    }

    $customerState = $selectedAddress->state ?? '';
    $gstPercent = $this->getTaxPercentForState($customerState);

    $cartItems = CartItem::where('customer_id', $customerId)
        ->with('product.branch') 
        ->get();

    $totalUniqueItems = 0;
    $totalQuantity = 0;
    $totalSubtotal = 0;
    $totalGST = 0;
    $totalEmbroideryPrice = 0;
    $totalDiscount = 0;
    $youSave = 0;

    foreach($cartItems as $item) {
        if($item->combo_id) {
            $mainProductTotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
            $comboProductTotal = ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1);
            $itemSubtotal = $mainProductTotal + $comboProductTotal;

            $itemEmbroideryTotal = $item->embroidery_total_price ?? 0;
            $totalEmbroideryPrice += $itemEmbroideryTotal;

            $taxableAmount = $itemSubtotal + $itemEmbroideryTotal;
            $itemGST = $taxableAmount * ($gstPercent / 100);

            $totalSubtotal += $itemSubtotal;
            $totalGST += $itemGST;
            $totalUniqueItems++;
            $totalQuantity += ($item->main_product_quantity ?? 1) + ($item->combo_product_quantity ?? 1);
        } else {
            $itemPrice = $item->main_product_price ?? 0;
            $itemQuantity = $item->main_product_quantity ?? 1;
            $itemSubtotal = $itemPrice * $itemQuantity;

            $itemEmbroideryTotal = $item->embroidery_total_price ?? 0;
            $totalEmbroideryPrice += $itemEmbroideryTotal;

            $taxableAmount = $itemSubtotal + $itemEmbroideryTotal;
            $itemGST = $taxableAmount * ($gstPercent / 100);

            $totalSubtotal += $itemSubtotal;
            $totalGST += $itemGST;
            $totalUniqueItems++;
            $totalQuantity += $itemQuantity;
        }
    }

    $appliedCoupon = $cartItems->firstWhere('coupon_code', '!=', null);
    $totalDiscount = $cartItems->sum('discount_amount');
    $youSave = $totalDiscount;

    $baseTotal = $totalSubtotal + $totalEmbroideryPrice + $totalGST;
    $baseTotalAfterDiscount = max(0, $baseTotal - $totalDiscount);
    $shippingCharge = (float) (session('shipping_charge_amount') ?? 0);
    $grandTotal = $baseTotalAfterDiscount + $shippingCharge;

    return view('frontend.paymentmethod', compact(
        'about',
        'categories',
        'footer',
        'cartItems',
        'grandTotal',
        'totalSubtotal',
        'totalGST',
        'totalEmbroideryPrice',
        'totalUniqueItems',
        'totalQuantity',
        'gstPercent',
        'appliedCoupon',
        'totalDiscount',
        'youSave',
        'baseTotal',
        'baseTotalAfterDiscount',
        'shippingCharge',
        'selectedAddressId',
        'selectedAddress'
    ));
}



public function placeOrder(Request $request)
{
    if (!Auth::guard('customer')->check()) {
        return redirect()->route('frontend.login')->with('error', 'Please login to continue.');
    }

    $customer_id = Auth::guard('customer')->id();

    $cartItems = CartItem::where('customer_id', $customer_id)
        ->with('product.branch')
        ->get();

    if ($cartItems->isEmpty()) {
        return redirect()->back()->with('error', 'Your cart is empty.');
    }

    // Get selected address from session, or fallback to first address
    $selectedAddressId = session('selected_address_id');
    $address = null;
    if ($selectedAddressId) {
        $address = Address::where('customer_id', $customer_id)
            ->where('id', $selectedAddressId)
            ->first();
    }
    
    if (!$address) {
        $address = Address::where('customer_id', $customer_id)->first();
    }
    
    if (!$address) {
        return redirect()->route('frontend.add-address')->with('error', 'Please provide a valid address.');
    }

    $customerState = $address->state ?? '';
    $gstPercent = $this->getTaxPercentForState($customerState);

    $payment_method = $request->payment_method ?? 'UPI';

    // Calculate totals the same way as checkout page
    $totalSubtotal = 0;
    $totalGST = 0;
    $totalEmbroideryPrice = 0;
    
    foreach($cartItems as $item) {
        if($item->combo_id) {
            $mainProductTotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
            $comboProductTotal = ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1);
            $itemSubtotal = $mainProductTotal + $comboProductTotal;

            $itemEmbroideryTotal = $item->embroidery_total_price ?? 0;
            $totalEmbroideryPrice += $itemEmbroideryTotal;

            $taxableAmount = $itemSubtotal + $itemEmbroideryTotal;
            $itemGST = $taxableAmount * ($gstPercent / 100);

            $totalSubtotal += $itemSubtotal;
            $totalGST += $itemGST;
        } else {
            $itemPrice = $item->main_product_price ?? 0;
            $itemQuantity = $item->main_product_quantity ?? 1;
            $itemSubtotal = $itemPrice * $itemQuantity;

            $itemEmbroideryTotal = $item->embroidery_total_price ?? 0;
            $totalEmbroideryPrice += $itemEmbroideryTotal;

            $taxableAmount = $itemSubtotal + $itemEmbroideryTotal;
            $itemGST = $taxableAmount * ($gstPercent / 100);

            $totalSubtotal += $itemSubtotal;
            $totalGST += $itemGST;
        }
    }

    // --- BUYER-TO-BUYER REFERRAL LOGIC ---
    $customer = Auth::guard('customer')->user();
    $referredBy = $customer->referred_by;
    $referralDiscounts = 0;

    if ($referredBy) {
        $itemsBySeller = $cartItems->groupBy(function($item) {
            return $item->product->created_by_id ?? null;
        });

        foreach($itemsBySeller as $sellerId => $items) {
            if (!$sellerId) continue;

            $hasOrderedBefore = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.customer_id', $customer_id)
                ->where('products.created_by_id', $sellerId)
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
                    $commissionPercent = $config->commission_percentage ?? 0;

                    if ($refereeDiscountPercent > 0 || $commissionPercent > 0) {
                        $sellerSubtotal = 0;
                        foreach($items as $item) {
                            $itemPrice = $item->combo_id 
                                ? (($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1) + ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1))
                                : (($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1));
                            $sellerSubtotal += $itemPrice;
                        }

                        $discountAmount = $sellerSubtotal * ($refereeDiscountPercent / 100);
                        $referralDiscounts += $discountAmount;

                        if ($commissionPercent > 0) {
                            $rewardAmount = $sellerSubtotal * ($commissionPercent / 100);
                            $rewardPoints = (int) $rewardAmount;
                            
                            \App\Models\Reward::create([
                                'customer_id' => $referredBy,
                                'type' => 'points',
                                'points' => $rewardPoints,
                                'amount' => $rewardAmount,
                                'description' => "Buyer-to-Buyer referral commission for Customer #{$customer_id}'s first purchase from Seller #{$sellerId}",
                                'status' => 'active',
                                'expiry_date' => now()->addDays(365)
                            ]);
                        }
                    }
                }
            }
        }
    }
    // -------------------------------------

    $totalDiscount = $cartItems->sum('discount_amount') + $referralDiscounts;
    $baseTotal = $totalSubtotal + $totalEmbroideryPrice + $totalGST;
    $baseTotalAfterDiscount = max(0, $baseTotal - $totalDiscount);

    $shippingCharge = (float) (session('shipping_charge_amount') ?? 0);
    if ($shippingCharge <= 0) {
        $shippingCharge = (float) $cartItems->sum('courier_charge');
    }

    // Calculate CGST/SGST/IGST based on states (for backward compatibility)
    $billingState = $cartItems->first()?->product?->institution?->state ?? null;
    $shippingState = $address->state ?? null;

    if ($billingState && $shippingState) {
        if ($billingState == $shippingState) {
            $cgst = $totalGST / 2;
            $sgst = $totalGST / 2;
            $igst = 0;
        } else {
            $cgst = 0;
            $sgst = 0;
            $igst = $totalGST;
        }
    } else {
        $cgst = 0;
        $sgst = 0;
        $igst = $totalGST;
    }

    $totalAmount = $baseTotalAfterDiscount + $shippingCharge;
    $shippingChargePerItem = $cartItems->count() > 0 ? ($shippingCharge / $cartItems->count()) : 0;
    $order = null;

    // -------------------------
    // CREATE ORDER
    // -------------------------
    DB::transaction(function () use (
        $cartItems, $customer_id, $address, $payment_method,
        $cgst, $sgst, $igst, $shippingCharge, $shippingChargePerItem, $totalSubtotal, $totalGST, $totalAmount, &$order
    ) {
        $order = Order::create([
            'customer_id'    => $customer_id,
            'user_id'        => $customer_id, // Assuming user_id is same as customer_id
            'address_id'     => $address->id,
            'payment_method' => $payment_method,
            'payment_status' => 'pending',
            'sub_total'      => $totalSubtotal,
            'gst_amount'     => $totalGST,
            'cgst'           => $cgst,
            'sgst'           => $sgst,
            'igst'           => $igst,
            'courier_charge' => $shippingCharge,
            'total_amount'   => $totalAmount,
            'status'         => 'pending',
        ]);

        foreach ($cartItems as $item) {
            if($item->combo_id) {
                // Combo product: insert all combo-related data
                OrderItem::create([
                    'order_id'                => $order->id,
                    'product_id'              => $item->product_id,
                    'combo_id'                => $item->combo_id,
                    'combo_item_color_id'     => $item->combo_item_color_id,
                    'combo_item_size_id'       => $item->combo_item_size_id,
                    'color_id'                 => $item->color_id,
                    'size_id'                  => $item->size_id,
                    'quantity'                 => ($item->main_product_quantity ?? 1) + ($item->combo_product_quantity ?? 1), // Total quantity
                    'price'                    => ($item->main_product_price ?? 0) + ($item->combo_product_price ?? 0), // Combined unit price
                    'total'                    => (($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1)) + (($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1)),
                    'main_product_attributes'  => $item->main_product_attributes,
                    'combo_product_attributes' => $item->combo_product_attributes,
                    'main_product_quantity'    => $item->main_product_quantity,
                    'main_product_price'       => $item->main_product_price,
                    'combo_product_quantity'   => $item->combo_product_quantity,
                    'combo_product_price'      => $item->combo_product_price,
                    'is_combo_item'            => true,
                    'gst_percent'              => $gstPercent,
                    'courier_charge'           => $shippingChargePerItem,
                ]);
            } else {
                // Individual product: use main_product_quantity and main_product_price
                $mainProductQuantity = $item->main_product_quantity ?? 1;
                $mainProductPrice = $item->main_product_price ?? 0;
                
                OrderItem::create([
                    'order_id'                => $order->id,
                    'product_id'              => $item->product_id,
                    'combo_id'                => null,
                    'color_id'                => $item->color_id,
                    'size_id'                 => $item->size_id,
                    'quantity'                => $mainProductQuantity, // Use main_product_quantity
                    'price'                   => $mainProductPrice, // Use main_product_price
                    'total'                   => $mainProductPrice * $mainProductQuantity,
                    'main_product_attributes' => $item->main_product_attributes,
                    'combo_product_attributes'=> null,
                    'main_product_quantity'   => $mainProductQuantity,
                    'main_product_price'      => $mainProductPrice,
                    'combo_product_quantity'  => null,
                    'combo_product_price'     => null,
                    'is_combo_item'           => false,
                    'gst_percent'             => $gstPercent,
                    'courier_charge'          => $shippingChargePerItem,
                ]);
            }
        }

        CartItem::where('customer_id', $customer_id)->delete();
    });


    // -------------------------
    // FIREBASE
    // -------------------------
    $firebaseURL = 'https://mafatlalshop-90155-default-rtdb.asia-southeast1.firebasedatabase.app/orders.json';

    $payloadArray = [
        'customer_id' => $customer_id,
        'order_id' => $order->id,
        'payment_method' => $payment_method,
        'sub_total' => $totalSubtotal,
        'gst_amount' => $totalGST,
        'total_amount' => $totalAmount,
        'courier_charge' => $shippingCharge,
        'cgst' => $cgst,
        'sgst' => $sgst,
        'igst' => $igst,
        'status' => 'pending',
        'timestamp' => now()->timestamp,
        'items' => $cartItems->map(function ($item) {
            if($item->combo_id) {
                return [
                    'product_id' => $item->product_id,
                    'combo_id' => $item->combo_id,
                    'name' => $item->product->name,
                    'combo_name' => \App\Models\Product::find($item->combo_id)?->name ?? null,
                    'main_product_quantity' => $item->main_product_quantity,
                    'main_product_price' => $item->main_product_price,
                    'combo_product_quantity' => $item->combo_product_quantity,
                    'combo_product_price' => $item->combo_product_price,
                    'total' => (($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1)) + (($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1)),
                    'is_combo' => true,
                ];
            } else {
                // Individual product: use main_product_quantity and main_product_price
                $mainProductQuantity = $item->main_product_quantity ?? 1;
                $mainProductPrice = $item->main_product_price ?? 0;
                
                return [
                    'product_id' => $item->product_id,
                    'name' => $item->product->name,
                    'quantity' => $mainProductQuantity,
                    'price' => $mainProductPrice,
                    'total' => $mainProductPrice * $mainProductQuantity,
                    'is_combo' => false,
                ];
            }
        }),
    ];

    $payload = json_encode($payloadArray);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseURL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $firebaseResponse = curl_exec($ch);
    curl_close($ch);

    $firebaseSaved = json_decode($firebaseResponse, true);
    $firebaseOrderKey = $firebaseSaved['name'] ?? null;


    // ----------------------------------------------------
    // 🚀 ADD STOCK DEDUCTION LOGIC (ONLY WHEN SUCCESS)
    // ----------------------------------------------------
    if ($order->status === 'success') {

        foreach ($order->items as $item) {

            // Deduct Color Stock
            if ($item->color_id) {
                ProductAttribute::where('product_id', $item->product_id)
                    ->where('attribute_id', 1)   // 1 = Color
                    ->where('attribute_term_id', $item->color_id)
                    ->decrement('product_stock', $item->quantity);
            }

            // Deduct Size Stock
            if ($item->size_id) {
                ProductAttribute::where('product_id', $item->product_id)
                    ->where('attribute_id', 2)   // 2 = Size
                    ->where('attribute_term_id', $item->size_id)
                    ->decrement('product_stock', $item->quantity);
            }
        }
    }


    // -------------------------
    // REDIRECT SUCCESS PAGE
    // -------------------------
    return redirect()->route('frontend.order.success')
        ->with('firebase_order', [
            'firebase_key' => $firebaseOrderKey,
            'customer_id' => $customer_id,
            'order_id' => $order->id,
            'payment_method' => $payment_method,
            'sub_total' => $totalSubtotal,
            'gst_amount' => $totalGST,
            'total_amount' => $totalAmount,
            'courier_charge' => $shippingCharge,
            'cgst' => $cgst,
            'sgst' => $sgst,
            'igst' => $igst,
            'timestamp' => now()->timestamp,
            'items' => $payloadArray['items'],
        ]);
}






public function orderSuccess()
{
    $about = About::first(); 
    $categories = Category::all(); 
    $footer = Footer::first();
    
    // Get order from session
    $order = null;
    if (session('firebase_order') && isset(session('firebase_order')['order_id'])) {
        $orderId = session('firebase_order')['order_id'];
        $order = Order::with([
            'items.product.category',
            'items.color',
            'items.size',
            'customer'
        ])->find($orderId);
    }
    
    return view('frontend.ordersuccess', compact('about', 'categories', 'footer', 'order'));
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
}
