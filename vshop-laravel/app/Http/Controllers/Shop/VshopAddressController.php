<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Tax;
use App\Models\GlobalSetting;

class VshopAddressController extends Controller
{
    /**
     * Display address selection page with order summary
     */
    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        // ================= ADDRESSES =================
        $addresses = Address::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Find selected address from session, or default, or first
        $deliveryId = session('order.delivery_address_id');
        $address = null;
        if ($deliveryId) {
            $address = Address::where('customer_id', $customer->id)->where('id', $deliveryId)->first();
        }
        if (!$address) {
            $address = Address::where('customer_id', $customer->id)->where('is_default', true)->first()
                ?? Address::where('customer_id', $customer->id)->first();
        }

        $customerState = strtolower(trim($address->state ?? ''));
        $sellerState = 'west bengal';

        // =========================================================
        // 🔥 BUY NOW FLOW (FIXED: handle array values)
        // =========================================================
        if ($request->has('product_id')) {

            $productId = $request->product_id;
            if (is_array($productId)) {
                $productId = reset($productId);
            }
            $product = Product::find($productId);
            if (!$product) {
                abort(404);
            }

            $price = $request->price;
            if (is_array($price)) {
                $price = reset($price);
            }
            $price = floatval($price);

            $quantity = $request->quantity;
            if (is_array($quantity)) {
                $quantity = reset($quantity);
            }
            $quantity = intval($quantity);

            if ($price <= 0) {
                $price = floatval($product->price ?? 0);
            }
            if ($quantity <= 0) {
                $quantity = 1;
            }

            $lineTotal = $price * $quantity;
            $subtotal = $lineTotal;

            // ✅ DYNAMIC TAX LOOKUP (based on buyer state)
            $taxPercent = $this->getTaxPercentForState($customerState);
            $taxAmount = ($lineTotal * $taxPercent) / 100;

            // ✅ CGST / SGST / IGST calculation based on address
            if ($customerState == strtolower($sellerState)) {
                $cgst = $taxAmount / 2;
                $sgst = $taxAmount / 2;
                $igst = 0;
            } else {
                $cgst = 0;
                $sgst = 0;
                $igst = $taxAmount;
            }

            // ✅ COURIER CHARGES (removed)
            $serviceCharges = 0;

            $grandTotal = $lineTotal + $cgst + $sgst + $igst + $serviceCharges;

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
                'courier_total' => $serviceCharges,
                'grand_total' => $grandTotal
            ];

            $buyNowItem = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $product->thumbnail,
            ];

            // Fetch recommendations
            $cartProductIds = [(string) $product->id];
            $recommendedProducts = $this->getRecommendedProducts($cartProductIds);

            return view('vshop.address.index', compact('addresses', 'orderSummary', 'subtotal', 'recommendedProducts'))
                ->with(['isBuyNow' => true, 'buyNowItem' => $buyNowItem]);
        }

        // =========================================================
        // 🔥 CART FLOW (FIXED: safe tax/service charges)
        // =========================================================
        $cartItems = CartItem::where('customer_id', $customer->id)
            ->with('product')
            ->get();

        $items = [];
        $subtotal = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $totalIgst = 0;
        $totalCourier = 0;

        foreach ($cartItems as $item) {
            if (!$item->product) continue;

            $price = floatval($item->main_product_price ?? 0);
            $quantity = intval($item->main_product_quantity ?? 1);

            $lineTotal = $price * $quantity;
            $subtotal += $lineTotal;

            $product = $item->product;

            // ✅ DYNAMIC TAX LOOKUP (based on buyer state)
            $gstPercent = $this->getTaxPercentForState($customerState);
            $gstAmount = ($lineTotal * $gstPercent) / 100;

            if ($customerState == strtolower($sellerState)) {
                $cgst = $gstAmount / 2;
                $sgst = $gstAmount / 2;
                $igst = 0;
            } else {
                $cgst = 0;
                $sgst = 0;
                $igst = $gstAmount;
            }

            $totalCgst += $cgst;
            $totalSgst += $sgst;
            $totalIgst += $igst;
            $totalCourier += 0; // Courier removed

            $items[] = [
                'name' => $product->name,
                'price' => $price,
                'quantity' => $quantity,
            ];
        }

        $grandTotal = $subtotal + $totalCgst + $totalSgst + $totalIgst + $totalCourier;

        $orderSummary = [
            'items' => $items,
            'cgst' => $totalCgst,
            'sgst' => $totalSgst,
            'igst' => $totalIgst,
            'courier_total' => $totalCourier,
            'grand_total' => $grandTotal
        ];

        // Fetch recommendations
        $cartProductIds = [];
        foreach ($cartItems as $citem) {
            if ($citem->product_id) {
                $cartProductIds[] = (string) $citem->product_id;
            }
        }
        $recommendedProducts = $this->getRecommendedProducts($cartProductIds);

        return view('vshop.address.index', compact('addresses', 'cartItems', 'orderSummary', 'subtotal', 'recommendedProducts'))
            ->with('isBuyNow', false);
    }

    /**
     * Helper: safely extract numeric value from database field that might be array/JSON.
     * If array, it sums all numeric values or returns the first numeric.
     * If string, converts to float.
     * If null/empty, returns 0.
     */
    private function safeNumericValue($value)
    {
        if (is_array($value)) {
            // If it's an associative array (e.g., {"cgst":2.5, "sgst":2.5}), sum the values
            $sum = 0;
            foreach ($value as $v) {
                if (is_numeric($v)) {
                    $sum += floatval($v);
                }
            }
            return $sum;
        }
        if (is_numeric($value)) {
            return floatval($value);
        }
        if (is_string($value) && trim($value) !== '') {
            // Try to decode JSON string
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $this->safeNumericValue($decoded);
            }
            return floatval($value);
        }
        return 0;
    }

    private function getRecommendedProducts($cartProductIds)
    {
        $cartProductIds = array_unique($cartProductIds);

        $rules = \DB::table('recommendation_rules')
            ->where('is_active', 1)
            ->where('type', 'product')
            ->where(function($q) use ($cartProductIds) {
                if (!empty($cartProductIds)) {
                    $q->whereIn('source', $cartProductIds)
                      ->orWhere('source', 'global');
                } else {
                    $q->where('source', 'global');
                }
            })
            ->orderByRaw("CASE WHEN source = 'global' THEN 1 ELSE 0 END")
            ->orderBy('priority', 'desc')
            ->get();

        $recommendedProductTypes = [];
        $recommendedProductIds = [];
        foreach ($rules as $rule) {
            $targets = $rule->targets;
            if (is_string($targets)) {
                $targets = json_decode($targets, true);
            }
            if (is_array($targets)) {
                foreach ($targets as $target) {
                    if (!isset($recommendedProductTypes[(string)$target])) {
                        $recommendedProductTypes[(string)$target] = $rule->source_type ?? 'recommended';
                    }
                    $recommendedProductIds[] = (string) $target;
                }
            }
        }
        $recommendedProductIds = array_unique($recommendedProductIds);

        // Exclude products already in cart
        $recommendedProductIds = array_diff($recommendedProductIds, $cartProductIds);

        $recommendedProducts = \App\Models\Product::whereIn('id', $recommendedProductIds)
            ->where(function($q) {
                $q->whereNull('created_by')
                  ->orWhere(function($sub) {
                      $sub->where('created_by', 'seller')->where('status', 'approved');
                  });
            })
            ->take(8)
            ->get();

        foreach($recommendedProducts as $rp) {
            $rp->recommendation_type = $recommendedProductTypes[(string)$rp->id] ?? 'recommended';
        }

        return $recommendedProducts;
    }

    // ------------------------------------------------------------------
    // All other methods (create, store, update, destroy, etc.) remain exactly as they were.
    // (No changes needed below – copy from your original controller)
    // ------------------------------------------------------------------

    public function create()
    {
        $countries = DB::table('countries')->orderBy('name')->get();
        $states = DB::table('states')->get();
        $cities = DB::table('cities')->get();

        return view('vshop.address.create', compact('countries', 'states', 'cities'));
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
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|digits:6',
            'country' => 'required|string|max:100',
            'address_type' => 'required|in:shipping,billing,both',
            'company_name' => 'nullable|string|max:255',
            'gstin' => 'nullable|string|max:50',
            'is_default' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            if ($request->has('is_default') && $request->is_default) {
                Address::where('customer_id', $customer->id)->update(['is_default' => false]);
            }

            $address = Address::create([
                'customer_id' => $customer->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country_code' => $request->country_code,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'country' => $request->country,
                'address_type' => $request->address_type,
                'company_name' => $request->company_name,
                'gstin' => $request->gstin,
                'is_default' => $request->has('is_default') ? true : false
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Address added successfully',
                    'address' => $address,
                    'redirect' => route('vshop.address.select', ['type' => $request->address_type])
                ]);
            }

            if ($request->has('redirect_to') && $request->redirect_to === 'checkout') {
                return redirect()->route('vshop.address.select')
                    ->with('success', 'Address added successfully');
            }

            return redirect()->route('vshop.address.index')
                ->with('success', 'Address added successfully');

        } catch (\Exception $e) {
            Log::error('Address creation error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add address: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to add address')
                ->withInput();
        }
    }

    public function show($id)
    {
        $customer = Auth::guard('customer')->user();
        $address = Address::where('customer_id', $customer->id)
            ->where('id', $id)
            ->firstOrFail();

        return view('vshop.address.show', compact('address'));
    }

    public function edit($id)
    {
        $customer = Auth::guard('customer')->user();
        $address = Address::where('customer_id', $customer->id)
            ->where('id', $id)
            ->firstOrFail();

        return view('vshop.address.edit', compact('address'));
    }

    public function update(Request $request, $id)
    {
        $customer = Auth::guard('customer')->user();
        $address = Address::where('customer_id', $customer->id)
            ->where('id', $id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|digits:10',
            'country_code' => 'required|string|max:5',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|digits:6',
            'country' => 'required|string|max:100',
            'address_type' => 'required|in:shipping,billing,both',
            'company_name' => 'nullable|string|max:255',
            'gstin' => 'nullable|string|max:50',
            'is_default' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            if ($request->has('is_default') && $request->is_default) {
                Address::where('customer_id', $customer->id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country_code' => $request->country_code,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'country' => $request->country,
                'address_type' => $request->address_type,
                'company_name' => $request->company_name,
                'gstin' => $request->gstin,
                'is_default' => $request->has('is_default') ? true : false
            ]);

            return redirect()->route('vshop.address.index')
                ->with('success', 'Address updated successfully');

        } catch (\Exception $e) {
            Log::error('Address update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update address')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $customer = Auth::guard('customer')->user();
        $address = Address::where('customer_id', $customer->id)
            ->where('id', $id)
            ->firstOrFail();

        try {
            $address->delete();
            return redirect()->back()->with('success', 'Address deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Delete failed');
        }
    }

    public function setDefault($id)
    {
        $customer = Auth::guard('customer')->user();

        DB::beginTransaction();

        try {
            Address::where('customer_id', $customer->id)->update(['is_default' => false]);
            $address = Address::where('customer_id', $customer->id)->where('id', $id)->firstOrFail();
            $address->update(['is_default' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Default address updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to set default address: ' . $e->getMessage()
            ], 500);
        }
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'delivery_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:cod,online'
        ]);

        $customer = Auth::guard('customer')->user();

        $deliveryAddress = Address::where('customer_id', $customer->id)
            ->where('id', $request->delivery_address_id)
            ->firstOrFail();

        $billingAddress = Address::where('customer_id', $customer->id)
            ->where('id', $request->billing_address_id)
            ->firstOrFail();

        $cartItems = CartItem::where('customer_id', $customer->id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('vshop.cart')
                ->with('error', 'Your cart is empty');
        }

        $orderSummary = $this->calculateOrderSummary($cartItems);

        session([
            'order.delivery_address' => $deliveryAddress,
            'order.billing_address' => $billingAddress,
            'order.payment_method' => $request->payment_method,
            'order.summary' => $orderSummary
        ]);

        return redirect()->route('vshop.order.confirmation')
            ->with('success', 'Please review your order and confirm');
    }

    private function getCartItems()
    {
        $customer = Auth::guard('customer')->user();

        if ($customer) {
            return CartItem::where('customer_id', $customer->id)
                ->where('is_active', true)
                ->get();
        }

        return collect([]);
    }

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

        $customer = Auth::guard('customer')->user();
        $address = Address::where('customer_id', $customer->id)->first();
        $customerState = strtolower($address->state ?? '');
        $sellerState = 'west bengal';

        foreach ($cartItems as $item) {
            $price = floatval($item->main_product_price ?? 0);
            $quantity = intval($item->main_product_quantity ?? 1);
            $gstPercent = $item->gst_percent ?? 5;
            $courierCharge = 0; // Removed

            $itemTotal = $price * $quantity;
            $gstAmount = ($itemTotal * $gstPercent) / 100;

            if ($customerState == strtolower($sellerState)) {
                $cgst = $gstAmount / 2;
                $sgst = $gstAmount / 2;
                $igst = 0;
            } else {
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
                'id' => $item->id,
                'name' => $productName,
                'price' => $price,
                'quantity' => $quantity,
                'item_total' => $itemTotal
            ];
        }

        $summary['grand_total'] = $summary['subtotal'] + $summary['cgst'] + $summary['sgst'] + $summary['igst'];
        return $summary;
    }

    public function select(Request $request)
    {
        $request->validate([
            'delivery_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
        ]);

        session([
            'delivery_address_id' => $request->delivery_address_id,
            'billing_address_id' => $request->billing_address_id,
        ]);

        return response()->json([
            'success' => true,
            'redirect' => route('vshop.payment.index')
        ]);
    }

    private function getCustomerGstin()
    {
        $customer = Auth::guard('customer')->user();
        return $customer->gstin ?? null;
    }

    public function updateAddressSession(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $deliveryId = $request->delivery_id;
        $billingId = $request->billing_id;

        // Save delivery and billing addresses to session
        session([
            'order.delivery_address_id' => $deliveryId,
            'order.billing_address_id'  => $billingId,
        ]);

        $address = Address::where('customer_id', $customer->id)
            ->where('id', $deliveryId)
            ->first();

        if (!$address) {
            return response()->json(['status' => false]);
        }

        $customerState = strtolower(trim($address->state ?? ''));
        $sellerState = 'west bengal';

        $subtotal = 0;
        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        $totalCourier = 0;

        if ($request->has('is_buy_now') && $request->is_buy_now) {
            $product = Product::find($request->product_id);
            if ($product) {
                $price = floatval($request->price ?? $product->price ?? 0);
                $qty = intval($request->quantity ?? 1);
                $lineTotal = $price * $qty;
                $subtotal = $lineTotal;

                $gstPercent = $this->getTaxPercentForState($customerState);
                $gstAmount = ($lineTotal * $gstPercent) / 100;
                $totalCourier = 0; // removed courier

                if ($customerState == strtolower($sellerState)) {
                    $cgst = $gstAmount / 2;
                    $sgst = $gstAmount / 2;
                } else {
                    $igst = $gstAmount;
                }
            }
        } else {
            $cartItems = CartItem::where('customer_id', $customer->id)
                ->with('product')
                ->get();

            foreach ($cartItems as $item) {
                if (!$item->product) continue;

                $price = floatval($item->main_product_price ?? 0);
                $qty = intval($item->main_product_quantity ?? 1);
                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;

                $gstPercent = $this->getTaxPercentForState($customerState);
                $gstAmount = ($lineTotal * $gstPercent) / 100;
                $totalCourier += 0; // Removed

                if ($customerState == strtolower($sellerState)) {
                    $cgst += $gstAmount / 2;
                    $sgst += $gstAmount / 2;
                } else {
                    $igst += $gstAmount;
                }
            }
        }

        $total = $subtotal + $cgst + $sgst + $igst + $totalCourier;

        return response()->json([
            'status' => true,
            'cgst' => number_format($cgst, 2),
            'sgst' => number_format($sgst, 2),
            'igst' => number_format($igst, 2),
            'courier_total' => number_format($totalCourier, 2),
            'subtotal' => number_format($subtotal, 2),
            'total' => number_format($total, 2),
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
}
