<?php

namespace App\Http\Controllers\vtravel;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VtravelAddressController extends Controller
{

  public function index(Request $request)
{
    $customer = Auth::guard('customer')->user();

    // ================= ADDRESSES =================
    $addresses = Address::where('customer_id', $customer->id)
        ->orderBy('created_at', 'desc')
        ->get();

    // ================= BUY NOW =================
    if ($request->has('product_id')) {

        $product = Product::find($request->product_id);

        if (!$product) {
            abort(404);
        }

        $price    = $request->price;
        $quantity = $request->quantity;

        $subtotal = $price * $quantity;

        // ================= GST CALC =================
        $taxPercent = $product->tax_percentage ?? 0;
        $taxAmount  = ($subtotal * $taxPercent) / 100;

        // 👉 Split GST
        $cgst = $taxAmount / 2;
        $sgst = $taxAmount / 2;
        $igst = 0; // optional interstate logic later

        // ================= SERVICE CHARGES =================
        $serviceCharges = $product->service_charges ?? 0;

        $grandTotal = $subtotal + $taxAmount + $serviceCharges;

        $buyNowItem = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $price,
            'quantity' => $quantity,
            'image' => $product->thumbnail,
        ];

        $orderSummary = [
            'items' => [
                [
                    'name' => $product->name,
                    'price' => $price,
                    'quantity' => $quantity,
                ]
            ],
            'cgst' => $cgst,
            'sgst' => $sgst,
            'igst' => $igst,
            'courier_total' => $serviceCharges, // 🔥 travel = service charge
            'grand_total' => $grandTotal
        ];

        return view('vtravel.address', compact(
            'addresses',
            'orderSummary'
        ))->with([
            'isBuyNow' => true,
            'buyNowItem' => $buyNowItem
        ]);
    }

    // ================= CART FLOW =================
    $cartItems = CartItem::where('customer_id', $customer->id)
        ->with('product')
        ->get();

    $items = [];

    $subtotal = 0;
    $totalTax = 0;
    $totalServiceCharge = 0;

    foreach ($cartItems as $item) {

        $price    = $item->main_product_price;
        $quantity = $item->main_product_quantity;

        $lineTotal = $price * $quantity;
        $subtotal += $lineTotal;

        $product = $item->product;

        // ===== GST =====
        $taxPercent = $product->tax_percentage ?? 0;
        $taxAmount  = ($lineTotal * $taxPercent) / 100;
        $totalTax  += $taxAmount;

        // ===== SERVICE CHARGE =====
        $serviceCharges = $product->service_charges ?? 0;
        $totalServiceCharge += $serviceCharges;

        $items[] = [
            'name' => $product->name,
            'price' => $price,
            'quantity' => $quantity,
        ];
    }

    // ===== SPLIT GST =====
    $cgst = $totalTax / 2;
    $sgst = $totalTax / 2;
    $igst = 0;

    $grandTotal = $subtotal + $totalTax + $totalServiceCharge;

    $orderSummary = [
        'items' => $items,
        'cgst' => $cgst,
        'sgst' => $sgst,
        'igst' => $igst,
        'courier_total' => $totalServiceCharge,
        'grand_total' => $grandTotal
    ];

    return view('vtravel.address', compact(
        'addresses',
        'cartItems',
        'orderSummary'
    ))->with('isBuyNow', false);
}

    public function create()
    {
        return view('vtravel.addresscreate');
    }


    public function store(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add address'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|digits:10',
            'country_code' => 'required|string|max:5',
            'address_line1' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|digits:6',
            'country' => 'required|string|max:100',
            'address_type' => 'required|in:shipping,billing,both'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {

            if ($request->has('is_default') && $request->is_default) {
                Address::where('customer_id',$customer->id)
                    ->update(['is_default'=>false]);
            }

            $address = Address::create([
                'customer_id'=>$customer->id,
                'name'=>$request->name,
                'email'=>$request->email,
                'phone'=>$request->phone,
                'country_code'=>$request->country_code,
                'address_line1'=>$request->address_line1,
                'address_line2'=>$request->address_line2,
                'city'=>$request->city,
                'state'=>$request->state,
                'pincode'=>$request->pincode,
                'country'=>$request->country,
                'address_type'=>$request->address_type,
                'company_name'=>$request->company_name,
                'gstin'=>$request->gstin,
                'is_default'=>$request->has('is_default')
            ]);

            return redirect()->route('vtravel.address.index')
                ->with('success','Address added successfully');

        } catch (\Exception $e){

            return redirect()->back()
                ->with('error','Failed to add address')
                ->withInput();
        }
    }


    public function edit($id)
    {
        $customer = Auth::guard('customer')->user();

        $address = Address::where('customer_id',$customer->id)
            ->where('id',$id)
            ->firstOrFail();

        return view('vtravel.address.edit', compact('address'));
    }


    public function update(Request $request, $id)
    {
        $customer = Auth::guard('customer')->user();

        $address = Address::where('customer_id',$customer->id)
            ->where('id',$id)
            ->firstOrFail();

        $validator = Validator::make($request->all(),[
            'name'=>'required|string|max:255',
            'email'=>'required|email|max:255',
            'phone'=>'required|digits:10',
            'address_line1'=>'required|string|max:255',
            'city'=>'required|string|max:100',
            'state'=>'required|string|max:100',
            'pincode'=>'required|digits:6'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $address->update($request->all());

        return redirect()->route('vtravel.address.index')
            ->with('success','Address updated successfully');
    }


    public function destroy($id)
    {
        $customer = Auth::guard('customer')->user();

        $address = Address::where('customer_id',$customer->id)
            ->where('id',$id)
            ->firstOrFail();

        $address->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Address deleted successfully'
        ]);
    }


    public function setDefault($id)
    {
        $customer = Auth::guard('customer')->user();

        DB::beginTransaction();

        try{

            Address::where('customer_id',$customer->id)
                ->update(['is_default'=>false]);

            $address = Address::where('customer_id',$customer->id)
                ->where('id',$id)
                ->firstOrFail();

            $address->update(['is_default'=>true]);

            DB::commit();

            return response()->json([
                'success'=>true,
                'message'=>'Default address updated'
            ]);

        }catch(\Exception $e){

            DB::rollBack();

            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage()
            ],500);
        }
    }


    public function placeOrder(Request $request)
    {
        $request->validate([
            'delivery_address_id'=>'required|exists:addresses,id',
            'billing_address_id'=>'required|exists:addresses,id',
            'payment_method'=>'required|in:cod,online'
        ]);

        $customer = Auth::guard('customer')->user();

        $cartItems = CartItem::where('customer_id',$customer->id)->get();

        if($cartItems->isEmpty()){
            return redirect()->route('vtravel.cart.index')
                ->with('error','Your cart is empty');
        }

        $orderSummary = $this->calculateOrderSummary($cartItems);

        session([
            'order.delivery_address'=>$request->delivery_address_id,
            'order.billing_address'=>$request->billing_address_id,
            'order.payment_method'=>$request->payment_method,
            'order.summary'=>$orderSummary
        ]);

        return redirect()->route('vtravel.order.confirmation')
            ->with('success','Please confirm your order');
    }


  private function calculateOrderSummary($cartItems)
{
    $summary = [
        'subtotal' => 0,
        'gst_total' => 0,
        'courier_total' => 0,
        'grand_total' => 0,
        'items_count' => 0,
        'items' => []
    ];

    foreach ($cartItems as $item) {

        $price = $item->main_product_price ?? 5900;
        $quantity = $item->main_product_quantity ?? 1;
        $gstPercent = $item->gst_percent ?? 5;
        $courierCharge = $item->courier_charge ?? 220;

        $itemTotal = $price * $quantity;
        $gstAmount = $itemTotal * ($gstPercent / 100);

        $summary['subtotal'] += $itemTotal;
        $summary['gst_total'] += $gstAmount;
        $summary['courier_total'] += $courierCharge;
        $summary['items_count'] += $quantity;

        $summary['items'][] = [
            'id' => $item->id,
            'name' => $item->product->name ?? 'Travel Package',
            'price' => $price,
            'quantity' => $quantity,
            'gst_percent' => $gstPercent,
            'item_total' => $itemTotal
        ];
    }

    $summary['grand_total'] =
        $summary['subtotal'] +
        $summary['gst_total'] +
        $summary['courier_total'];

    return $summary;
}


    public function select(Request $request)
    {
        $request->validate([
            'delivery_address_id'=>'required|exists:addresses,id',
            'billing_address_id'=>'required|exists:addresses,id'
        ]);

        session([
            'delivery_address_id'=>$request->delivery_address_id,
            'billing_address_id'=>$request->billing_address_id
        ]);

        return response()->json([
            'success'=>true,
            'redirect'=>route('vtravel.payment.index')
        ]);
    }

}
