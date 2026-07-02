<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\CartItemSize;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Address;
use App\Models\AttributeTerm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Models\ProductReview;

class VshopCartController extends Controller
{
    /**
     * ===============================
use Illuminate\Http\Request;
     * ADD PRODUCT TO CART
     * ===============================
     */
    
   public function add(Request $request)
{
    try {

        Log::info('Add to cart request:', $request->all());

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'color_id'   => 'nullable|exists:attribute_terms,id',
            'size_id'    => 'nullable|exists:attribute_terms,id',
            'price'      => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'=>false,
                'errors'=>$validator->errors()
            ],422);
        }

        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        $existingQty = 0;
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            $cartItem = CartItem::where('customer_id', $customer->id)
                ->where('product_id', $request->product_id)
                ->where('color_id', $request->color_id)
                ->where('size_id', $request->size_id)
                ->first();
            if ($cartItem) {
                $existingQty = $cartItem->main_product_quantity;
            }
        } else {
            $cart = Session::get('cart', []);
            $cartKey = $request->product_id.'_'.$request->color_id.'_'.$request->size_id;
            if (isset($cart[$cartKey])) {
                $existingQty = $cart[$cartKey]['main_product_quantity'];
            }
        }
        $totalRequestedQty = $existingQty + $request->quantity;

        $isPhysicalProduct = in_array($product->type, ['product', 'vshop-product']) || empty($product->type);

        if ($isPhysicalProduct) {
            // Check main product stock
            if (is_null($product->stock_qty) || $product->stock_qty <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is out of stock.'
                ], 400);
            }

            if ($totalRequestedQty > $product->stock_qty) {
                $avail = $product->stock_qty - $existingQty;
                $msg = $avail > 0 
                    ? "Cannot add more. Only {$avail} more items can be added (available stock: {$product->stock_qty})."
                    : "Cannot add more. You already have all available stock ({$product->stock_qty}) in your cart.";
                return response()->json([
                    'success' => false,
                    'message' => $msg
                ], 400);
            }

            // Check variant combination stock if color_id or size_id is provided
            if ($request->color_id || $request->size_id) {
                $combinationStock = \App\Models\ProductAttribute::where('proid', $product->id)
                    ->when($request->color_id, function($q) use ($request) {
                        $q->where('attribute_term_id', $request->color_id);
                    })
                    ->when($request->size_id, function($q) use ($request) {
                        $q->where('attribute_term_id', $request->size_id);
                    })
                    ->value('product_stock');

                if (!is_null($combinationStock)) {
                    if ($combinationStock <= 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Selected combination is out of stock.'
                        ], 400);
                    }
                    if ($totalRequestedQty > $combinationStock) {
                        $avail = $combinationStock - $existingQty;
                        $msg = $avail > 0 
                            ? "Cannot add more. Only {$avail} more items of this combination can be added (available stock: {$combinationStock})."
                            : "Cannot add more. You already have all available stock of this combination ({$combinationStock}) in your cart.";
                        return response()->json([
                            'success' => false,
                            'message' => $msg
                        ], 400);
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FETCH FULL ATTRIBUTE COMBINATION
        |--------------------------------------------------------------------------
        */

        $attributes = [];

        $combinationAttributes = \App\Models\ProductAttribute::with(['attribute','term'])
            ->where('proid',$product->id)
            ->when($request->color_id,function($q) use ($request){
                $q->where('attribute_term_id',$request->color_id);
            })
            ->orWhere(function($q) use ($request){
                if($request->size_id){
                    $q->where('attribute_term_id',$request->size_id);
                }
            })
            ->get();

        foreach($combinationAttributes as $attr){
            if (!$attr->attribute || !$attr->term) {
                continue;
            }

            $attrName = strtolower($attr->attribute->name);

            $attributes[$attrName] = [
                'id'    => $attr->attribute_term_id,
                'name'  => $attr->term->term,
                'value' => $attr->term->color_code ?? null
            ];
        }

        if ($request->has('selected_slots') && is_array($request->selected_slots)) {
            $attributes['Booking Slots'] = [
                'id'    => null,
                'name'  => implode(', ', $request->selected_slots),
                'value' => null
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | GUEST CART (SESSION)
        |--------------------------------------------------------------------------
        */

        if(!Auth::guard('customer')->check()){

            $cart = Session::get('cart',[]);

            $cartKey = $request->product_id.'_'.$request->color_id.'_'.$request->size_id;

            if(isset($cart[$cartKey])){

                $cart[$cartKey]['main_product_quantity'] += $request->quantity;

            }else{

                $cart[$cartKey] = [

                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_thumbnail' => $product->thumbnail,
                    'color_id' => $request->color_id,
                    'size_id'  => $request->size_id,

                    'main_product_attributes' => json_encode($attributes),

                    'main_product_quantity' => $request->quantity,
                    'main_product_price' => $request->price

                ];
            }

            Session::put('cart',$cart);

            $cartCount = array_sum(array_column($cart,'main_product_quantity'));

            if ($customer) {
                NotificationService::notifyCustomer(
                    $customer->id,
                    'cart_add',
                    'Product added to your cart',
                    route('vshop.cart.index')
                );
            }
            return response()->json([
                'success'=>true,
                'message'=>'Product added to cart',
                'cart_count'=>$cartCount
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | DATABASE CART
        |--------------------------------------------------------------------------
        */

        $customer = Auth::guard('customer')->user();

        $cartItem = CartItem::where('customer_id',$customer->id)
            ->where('product_id',$request->product_id)
            ->where('color_id',$request->color_id)
            ->where('size_id',$request->size_id)
            ->first();

        if($cartItem){

            $cartItem->main_product_quantity += $request->quantity;
            $cartItem->main_product_price = $request->price;

            $cartItem->main_product_attributes = json_encode($attributes);

            $cartItem->save();

        }else{

            $cartItem = CartItem::create([

                'product_id' => $request->product_id,
                'customer_id'=> $customer->id,
                'session_id' => Session::getId(),

                'color_id' => $request->color_id,
                'size_id'  => $request->size_id,

                'main_product_attributes' => json_encode($attributes),

                'main_product_quantity' => $request->quantity,
                'main_product_price' => $request->price,

                'embroidery_logo' => $request->embroidery_logo,
                'embroidery_price'=> $request->embroidery_price ?? 0,
                'embroidery_total_price'=> ($request->embroidery_price ?? 0) * $request->quantity

            ]);

            /*
            |--------------------------------------------------------------------------
            | SAVE SIZE ROW
            |--------------------------------------------------------------------------
            */

            if($request->size_id){

                CartItemSize::create([

                    'cart_item_id'=>$cartItem->id,
                    'size_id'=>$request->size_id,
                    'size_name'=>$attributes['size']['name'] ?? null,
                    'quantity'=>$request->quantity,
                    'price'=>$request->price

                ]);
            }

        }

        /*
        |--------------------------------------------------------------------------
        | RETURN UPDATED CART COUNT
        |--------------------------------------------------------------------------
        */

        $cartCount = CartItem::where('customer_id',$customer->id)
            ->sum('main_product_quantity');

        if ($customer) {
            NotificationService::notifyCustomer(
                $customer->id,
                'cart_add',
                'Product added to your cart',
                route('vshop.cart.index')
            );
        }

        return response()->json([
            'success'=>true,
            'message'=>'Product added to cart successfully!',
            'cart_count'=>$cartCount
        ]);

    }catch(\Exception $e){

        Log::error('Add to cart error '.$e->getMessage());

        return response()->json([
            'success'=>false,
            'message'=>$e->getMessage()
        ],500);
    }
}
    
    
public function index()
{
    $cartItems = [];
    $totalPrice = 0;
    $totalItems = 0;
    $subtotal = 0;

    if (Auth::guard('customer')->check()) {

        $customer = Auth::guard('customer')->user();

        $cartItems = CartItem::where('customer_id', $customer->id)
            ->whereHas('product')
            ->with(['product.reviews','color','size'])
            ->get();

        foreach ($cartItems as $item) {

            // ⭐ Combination attributes fetch
            if(!empty($item->combination_key)){

                $item->combinationAttributes = \App\Models\ProductAttribute::with(['attribute','term'])
                    ->where('proid',$item->product_id)
                    ->where('combination_key',$item->combination_key)
                    ->get();

            }else{
                $item->combinationAttributes = collect();
            }

            $lineTotal = $item->main_product_price * $item->main_product_quantity;

            $subtotal += $lineTotal;
            $totalPrice += $lineTotal;
            $totalItems += $item->main_product_quantity;

            if ($item->embroidery_total_price > 0) {
                $subtotal += $item->embroidery_total_price;
                $totalPrice += $item->embroidery_total_price;
            }
        }

    } else {

        $sessionCart = Session::get('cart', []);

        foreach ($sessionCart as $item) {

            $item = (object) $item;

            // ⭐ Session cart attributes
            if(!empty($item->combination_key)){

                $item->combinationAttributes = \App\Models\ProductAttribute::with(['attribute','term'])
                    ->where('proid',$item->product_id)
                    ->where('combination_key',$item->combination_key)
                    ->get();

            }else{
                $item->combinationAttributes = collect();
            }

            $cartItems[] = $item;

            $lineTotal = $item->main_product_price * $item->main_product_quantity;

            $subtotal += $lineTotal;
            $totalPrice += $lineTotal;
            $totalItems += $item->main_product_quantity;

            if (!empty($item->embroidery_total_price)) {
                $subtotal += $item->embroidery_total_price;
                $totalPrice += $item->embroidery_total_price;
            }
        }
    }

    // ===== Discount =====
    $discount = 0;

    foreach ($cartItems as $item) {

        if (!empty($item->discount_amount)) {

            if ($item->discount_type === 'percentage') {

                $discount += ($item->main_product_price * $item->main_product_quantity)
                             * ($item->discount_amount / 100);

            } else {

                $discount += $item->discount_amount;

            }
        }
    }

    // ===== Recommendation Rules Logic =====
    $cartProductIds = [];
    foreach ($cartItems as $item) {
        if (is_object($item) && isset($item->product_id)) {
            $cartProductIds[] = (string) $item->product_id;
        } elseif (is_array($item) && isset($item['product_id'])) {
            $cartProductIds[] = (string) $item['product_id'];
        }
    }
    $cartProductIds = array_unique($cartProductIds);
    \Log::info("vshop cart debug - Cart Product IDs: " . json_encode($cartProductIds));

    // Fetch rules directly from recommendation_rules table via DB query builder
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
    \Log::info("vshop cart debug - Rules matched count: " . count($rules));

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

    // Query recommended products details
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
    \Log::info("vshop cart debug - Final products retrieved count: " . $recommendedProducts->count());




    $wishlistItems = Wishlist::with('product')
        ->where('customer_id', Auth::guard('customer')->id())
        ->whereHas('product')
        ->latest()
        ->get();

    // ===== Delivery Address =====
    $deliveryAddress = Auth::guard('customer')->check()
        ? Address::where('customer_id', $customer->id)->first()
        : null;

    $grandTotal = $subtotal - $discount;

    return view('vshop.cart', compact(
        'cartItems',
        'totalPrice',
        'totalItems',
        'subtotal',
        'discount',
        'grandTotal',
        'wishlistItems',
        'deliveryAddress',
        'recommendedProducts'
    ));
}

public function updateQty(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'cart_item_id' => 'required',
            'quantity' => 'required|integer|min:1',
            'is_db_item' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->is_db_item == 1) {
            // Database cart (logged in user)
            if (!Auth::guard('customer')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $cartItem = CartItem::where('id', $request->cart_item_id)
                ->where('customer_id', Auth::guard('customer')->id())
                ->first();
            
            if (!$cartItem) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cart item not found'
                ], 404);
            }

            $product = Product::find($cartItem->product_id);
            if ($product && (in_array($product->type, ['product', 'vshop-product']) || empty($product->type))) {
                if (is_null($product->stock_qty) || $product->stock_qty <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product is out of stock.'
                    ], 400);
                }
                if ($request->quantity > $product->stock_qty) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Only ' . $product->stock_qty . ' items available.'
                    ], 400);
                }

                if ($cartItem->color_id || $cartItem->size_id) {
                    $combinationStock = \App\Models\ProductAttribute::where('proid', $product->id)
                        ->when($cartItem->color_id, function($q) use ($cartItem) {
                            $q->where('attribute_term_id', $cartItem->color_id);
                        })
                        ->when($cartItem->size_id, function($q) use ($cartItem) {
                            $q->where('attribute_term_id', $cartItem->size_id);
                        })
                        ->value('product_stock');
                    
                    if (!is_null($combinationStock)) {
                        if ($combinationStock <= 0) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Selected combination is out of stock.'
                            ], 400);
                        }
                        if ($request->quantity > $combinationStock) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Only ' . $combinationStock . ' items of this combination available.'
                            ], 400);
                        }
                    }
                }
            }

            $cartItem->main_product_quantity = $request->quantity;
            $cartItem->save();
            
            // Also update cart_item_sizes if needed (optional)
            $sizeStock = CartItemSize::where('cart_item_id', $cartItem->id)
                ->where('size_id', $cartItem->size_id)
                ->first();
                
            if ($sizeStock) {
                $sizeStock->quantity = $request->quantity;
                $sizeStock->save();
            }

        } else {
            // Session cart (guest user)
            $cart = session()->get('cart', []);
            $found = false;
            
            foreach ($cart as $key => $item) {
                if ($key == $request->cart_item_id || 
                    (isset($item['product_id']) && $item['product_id'] == $request->cart_item_id)) {
                    
                    $product = Product::find($item['product_id'] ?? null);
                    if ($product && (in_array($product->type, ['product', 'vshop-product']) || empty($product->type))) {
                        if (is_null($product->stock_qty) || $product->stock_qty <= 0) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Product is out of stock.'
                            ], 400);
                        }
                        if ($request->quantity > $product->stock_qty) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Only ' . $product->stock_qty . ' items available.'
                            ], 400);
                        }

                        $colorId = $item['color_id'] ?? null;
                        $sizeId = $item['size_id'] ?? null;
                        if ($colorId || $sizeId) {
                            $combinationStock = \App\Models\ProductAttribute::where('proid', $product->id)
                                ->when($colorId, function($q) use ($colorId) {
                                    $q->where('attribute_term_id', $colorId);
                                })
                                ->when($sizeId, function($q) use ($sizeId) {
                                    $q->where('attribute_term_id', $sizeId);
                                })
                                ->value('product_stock');
                            
                            if (!is_null($combinationStock)) {
                                if ($combinationStock <= 0) {
                                    return response()->json([
                                        'success' => false,
                                        'message' => 'Selected combination is out of stock.'
                                    ], 400);
                                }
                                if ($request->quantity > $combinationStock) {
                                    return response()->json([
                                        'success' => false,
                                        'message' => 'Only ' . $combinationStock . ' items of this combination available.'
                                    ], 400);
                                }
                            }
                        }
                    }

                    $cart[$key]['main_product_quantity'] = $request->quantity;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found in session'
                ], 404);
            }
            
            session()->put('cart', $cart);
        }

        // Recalculate totals
        $totalItems = $this->calculateTotalItems();
        $grandTotal = $this->calculateGrandTotal();

        return response()->json([
            'success' => true,
            'message' => 'Quantity updated successfully',
            'total_items' => $totalItems,
            'grand_total' => $grandTotal
        ]);

    } catch (\Exception $e) {
        Log::error('Quantity update error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error updating quantity: ' . $e->getMessage()
        ], 500);
    }
}

 public function updateSize(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'cart_item_id' => 'required|integer',
            'size_id' => 'required|integer',
            'size_name' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user is logged in
        if (!Auth::guard('customer')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $cartItem = CartItem::where('id', $request->cart_item_id)
            ->where('customer_id', Auth::guard('customer')->id())
            ->first();
        
        if (!$cartItem) {
            return response()->json([
                'success' => false, 
                'message' => 'Cart item not found'
            ], 404);
        }

        // Update cart item with new size
        $cartItem->size_id = $request->size_id;
        $cartItem->main_product_price = $request->price;
        
        // Update main_product_attributes JSON
        $attributes = json_decode($cartItem->main_product_attributes ?? '{}', true);
        $attributes['size'] = [
            'id' => $request->size_id,
            'name' => $request->size_name,
            'price' => $request->price
        ];
        $cartItem->main_product_attributes = json_encode($attributes);
        
        // REMOVED THE QUANTITY CHECK - Don't limit by stock
        // Just save without checking stock
        $cartItem->save();

        // Update or create cart_item_sizes record
        CartItemSize::updateOrCreate(
            [
                'cart_item_id' => $cartItem->id,
                'size_id' => $request->size_id
            ],
            [
                'size_name' => $request->size_name,
                'quantity' => $request->quantity, // This is the available stock, not a limit
                'price' => $request->price
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Size updated successfully',
            'data' => [
                'new_price' => $request->price,
                'new_quantity' => $cartItem->main_product_quantity
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Size update error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error updating size: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Move cart item to wishlist
     */
    public function moveToWishlist(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cart_item_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if user is logged in
            if (!Auth::guard('customer')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $cartItem = CartItem::where('id', $request->cart_item_id)
                ->where('customer_id', Auth::guard('customer')->id())
                ->first();
            
            if (!$cartItem) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cart item not found'
                ], 404);
            }

            // Here you would implement your wishlist logic
            // For example, create a wishlist item and delete cart item
            // WishlistItem::create([...]);
            
            // Delete associated sizes
            CartItemSize::where('cart_item_id', $cartItem->id)->delete();
            
            // Delete cart item
            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item moved to wishlist successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Move to wishlist error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error moving to wishlist: ' . $e->getMessage()
            ], 500);
        }
    }
    
  

    /**
     * Remove item from cart
     */
    public function remove(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cart_item_id' => 'required',
                'is_db_item' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->is_db_item == 1) {
                // Database cart
                // Check if user is logged in
                if (!Auth::guard('customer')->check()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not authenticated'
                    ], 401);
                }

                $cartItem = CartItem::where('id', $request->cart_item_id)
                    ->where('customer_id', Auth::guard('customer')->id())
                    ->first();
                
                if ($cartItem) {
                    // Delete associated sizes
                    CartItemSize::where('cart_item_id', $cartItem->id)->delete();
                    // Delete cart item
                    $cartItem->delete();
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cart item not found'
                    ], 404);
                }
            } else {
                // Session cart
                $cart = session()->get('cart', []);
                $found = false;
                
                foreach ($cart as $key => $item) {
                    if ($key == $request->cart_item_id || 
                        (isset($item['product_id']) && $item['product_id'] == $request->cart_item_id)) {
                        unset($cart[$key]);
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cart item not found in session'
                    ], 404);
                }
                
                session()->put('cart', $cart);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item removed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Remove item error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error removing item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate total items count
     */
    private function calculateTotalItems()
    {
        if (auth()->guard('customer')->check()) {
            return CartItem::where('customer_id', auth()->guard('customer')->id())->count();
        } else {
            $cart = session()->get('cart', []);
            return count($cart);
        }
    }

    /**
     * Calculate grand total
     */
    private function calculateGrandTotal()
    {
        $total = 0;
        
        if (auth()->guard('customer')->check()) {
            $items = CartItem::where('customer_id', auth()->guard('customer')->id())->get();
            foreach ($items as $item) {
                $subtotal = $item->main_product_price * $item->main_product_quantity;
                $total += $subtotal;
            }
        } else {
            $cart = session()->get('cart', []);
            foreach ($cart as $item) {
                $subtotal = $item['main_product_price'] * $item['main_product_quantity'];
                $total += $subtotal;
            }
        }
        
        return $total;
    }
    
    public function getCartCount()
{
    if (Auth::guard('customer')->check()) {
        $count = CartItem::where('customer_id', Auth::guard('customer')->id())
            ->sum('main_product_quantity');
    } else {
        $cart = Session::get('cart', []);
        $count = array_sum(array_column($cart, 'main_product_quantity'));
    }

    return response()->json([
        'success' => true,
        'count' => $count
    ]);
}



 public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $customer = Auth::guard('customer')->user();

        $exists = Wishlist::where('customer_id', $customer->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($exists) {
            $exists->delete();
            return response()->json([
                'status' => 'removed'
            ]);
        }

        Wishlist::create([
            'customer_id' => $customer->id,
            'product_id'  => $request->product_id
        ]);

        return response()->json([
            'status' => 'added'
        ]);
    }
}