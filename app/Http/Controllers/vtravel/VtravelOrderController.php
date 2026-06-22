<?php

namespace App\Http\Controllers\vtravel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Services\NotificationService;

class VtravelOrderController extends Controller
{

    protected $guard = 'customer';

    /*
    ==========================================
    PAYMENT PAGE
    ==========================================
    */

    public function payment(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return redirect()->route('vtravellogin')
                ->with('error','Please login first');
        }

        $cartItems = CartItem::where('customer_id',$customer->id)
            ->with('product')
            ->get();

        if($cartItems->isEmpty()){
            return redirect()->route('vtravel.cart.index')
                ->with('error','Cart is empty');
        }

        $deliveryAddress = Address::where('customer_id',$customer->id)
            ->where('is_default',1)
            ->first()
            ?? Address::where('customer_id',$customer->id)->first();

        if(!$deliveryAddress){
            return redirect()->route('vtravel.address.index')
                ->with('error','Please add address first');
        }

        $billingAddress = $deliveryAddress;

        session([
            'order.delivery_address_id' => $deliveryAddress->id,
            'order.billing_address_id'  => $billingAddress->id,
        ]);

        $orderSummary = $this->calculateOrderSummary($cartItems);

        return view('vtravel.payment.index', compact(
            'cartItems',
            'orderSummary',
            'deliveryAddress',
            'billingAddress'
        ));
    }


    /*
    ==========================================
    PLACE ORDER
    ==========================================
    */

public function placeOrder(Request $request)
{
    try {

        $request->validate([
            'payment_method' => 'required|in:cod,upi,card',
        ]);

        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return back()->with('error', 'Please login first');
        }

        $deliveryId = session('order.delivery_address_id');
        $billingId  = session('order.billing_address_id');

        if (!$deliveryId || !$billingId) {
            return redirect()->route('vtravel.address.index')
                ->with('error', 'Please select address first');
        }

        $cartItems = CartItem::where('customer_id', $customer->id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('vtravel.cart.index')
                ->with('error', 'Cart is empty');
        }

        DB::beginTransaction();

        $subTotal = 0;
        $gstTotal = 0;
        $courier  = 0;

        foreach ($cartItems as $item) {

            $price = $item->main_product_price ?? 0;
            $qty   = $item->main_product_quantity ?? 1;
            $gst   = $item->gst_percent ?? 5;

            $lineTotal = $price * $qty;
            $gstAmount = ($lineTotal * $gst) / 100;

            $subTotal += $lineTotal;
            $gstTotal += $gstAmount;
        }

        $grandTotal = $subTotal + $gstTotal + $courier;

        /* ==============================
           CREATE ORDER
        ============================== */

        $order = Order::create([
            'customer_id'         => $customer->id,
            'payment_method'      => $request->payment_method,
            'payment_status'      => 'pending',
            'sub_total'           => $subTotal,
            'gst_amount'          => $gstTotal,
            'courier_charge'      => $courier,
            'total_amount'        => $grandTotal,
            'status'              => 'pending',
            'delivery_address_id' => $deliveryId,
            'billing_address_id'  => $billingId,
            'order_date'          => now(),
        ]);

        /* ==============================
           CREATE ORDER ITEMS
        ============================== */

        foreach ($cartItems as $item) {

            $price = $item->main_product_price ?? 0;
            $qty   = $item->main_product_quantity ?? 1;
            $productId = $item->product_id;

            $product = DB::table('products')
                ->where('id', $productId)
                ->select('category_id')
                ->first();

            $commissionPercent = DB::table('commission_settings')
                ->where('category_id', $product->category_id ?? null)
                ->value('commission_percent');

            if (!$commissionPercent) {
                $commissionPercent = 0;
            }

            $totalPrice = $price * $qty;

            $commissionAmount = ($totalPrice * $commissionPercent) / 100;

            $sellerEarning = $totalPrice - $commissionAmount;

            OrderItem::create([
                'order_id'           => $order->id,
                'product_id'         => $productId,
                'quantity'           => $qty,
                'price'              => $price,
                'total'              => $totalPrice,
                'gst_percent'        => $item->gst_percent ?? 5,
                'commission_percent' => $commissionPercent,
                'commission_amount'  => $commissionAmount,
                'seller_earning'     => $sellerEarning
            ]);
        }

        /* ==============================
           CLEAR CART
        ============================== */

        CartItem::where('customer_id', $customer->id)->delete();

        session()->forget([
            'order.delivery_address_id',
            'order.billing_address_id'
        ]);

        DB::commit();

        NotificationService::notify(
            $customer->id,
            'customer',
            'order',
            'Your travel order #' . $order->id . ' has been placed successfully.',
            route('vtravel.order.confirmation', ['id' => $order->id])
        );

        /* ==============================
           REDIRECT TO CONFIRMATION
        ============================== */

        return redirect()->route('vtravel.order.confirmation', ['id' => $order->id])
            ->with('success', 'Order placed successfully!');

    } catch (\Exception $e) {

        DB::rollBack();

        Log::error('Vtravel Order Error: ' . $e->getMessage());

        return back()->with('error', $e->getMessage());
    }
}

    /*
    ==========================================
    ORDER CONFIRMATION
    ==========================================
    */

    public function confirmation($id)
    {
        $customer = Auth::guard('customer')->user();

        $order = Order::with(['items.product'])
            ->where('customer_id',$customer->id)
            ->where('id',$id)
            ->firstOrFail();

        return view('vtravel.payment.confirmation', compact('order'));
    }


    /*
    ==========================================
    ORDER SUMMARY
    ==========================================
    */

    private function calculateOrderSummary($cartItems)
    {
        $subtotal = 0;
        $gstTotal = 0;

        foreach($cartItems as $item){

            $price = $item->main_product_price;
            $qty   = $item->main_product_quantity;
            $gst   = $item->gst_percent ?? 0;

            $lineTotal = $price * $qty;
            $lineGst   = ($lineTotal * $gst)/100;

            $subtotal += $lineTotal;
            $gstTotal += $lineGst;
        }

        $courier = 0;

        $grandTotal = $subtotal + $gstTotal + $courier;

        return [
            'subtotal'=>$subtotal,
            'gst_total'=>$gstTotal,
            'courier_total'=>$courier,
            'grand_total'=>$grandTotal,
            'items'=>$cartItems->map(function($item){
                return [
                    'name'=>$item->product->name ?? 'Travel Package',
                    'price'=>$item->main_product_price,
                    'quantity'=>$item->main_product_quantity
                ];
            })
        ];
    }


    /*
    ==========================================
    ORDER LIST
    ==========================================
    */


public function orderList(Request $request)
{
    $customer = Auth::guard('customer')->user();

    if (!$customer) {
        return redirect()->route('vtravellogin')
            ->with('error', 'Please login first');
    }

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

    return view('vtravel.orders.index', compact('orders'));
}


/**
 * Single Order Details (VTRAVEL)
 */
public function orderDetails($id)
{
    $customer = Auth::guard('customer')->user();

    if (!$customer) {
        return redirect()->route('vtravellogin')
            ->with('error', 'Please login to view order details');
    }

    $order = Order::with([
        'items.product',
        'shippingAddress',
        'billingAddress',
        'customer'
    ])
    ->where('customer_id', $customer->id)
    ->where('id', $id)
    ->firstOrFail();

    // Calculate order summary for each item
    foreach ($order->items as $item) {

        $item->gst_amount = $item->total * ($item->gst_percent / 100);

        $item->subtotal = $item->total
            + $item->gst_amount
            + ($order->courier_charge ?? 0);
    }

    // Totals
    $totalItems = $order->items->count();
    $totalQuantity = $order->items->sum('quantity');

    // Status badge class
    $statusClasses = [
        'pending' => 'badge-warning',
        'processing' => 'badge-info',
        'confirmed' => 'badge-primary',
        'dispatched' => 'badge-info',
        'shipped' => 'badge-primary',
        'delivered' => 'badge-success',
        'cancelled' => 'badge-danger',
        'refunded' => 'badge-secondary',
        'returned' => 'badge-secondary'
    ];

    // Payment badge class
    $paymentStatusClasses = [
        'pending' => 'badge-warning',
        'processing' => 'badge-info',
        'completed' => 'badge-success',
        'failed' => 'badge-danger',
        'refunded' => 'badge-secondary'
    ];
    
        $shippingAddress = Address::where('customer_id',$customer->id)
        ->where('address_type','shipping')
        ->first();

$billingAddress = Address::where('customer_id',$customer->id)
        ->where('address_type','billing')
        ->first();

    return view('vtravel.orders.details', compact(
        'order',
        'totalItems',
        'totalQuantity',
        'statusClasses',
        'paymentStatusClasses',  'shippingAddress',
    'billingAddress'
    ));
}

}