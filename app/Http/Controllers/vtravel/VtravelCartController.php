<?php
namespace App\Http\Controllers\vtravel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Wishlist;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VtravelCartController extends Controller
{

/*
================================
ADD TRAVEL PACKAGE TO CART
================================
*/

public function add(Request $request)
{

    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1'
    ]);

    $product = Product::findOrFail($request->product_id);

    /*
    ============================
    GUEST USER SESSION CART
    ============================
    */

    if (!Auth::guard('customer')->check()) {

        $cart = Session::get('travel_cart', []);

        if(isset($cart[$product->id])){
            $cart[$product->id]['quantity'] += $request->quantity;
        }else{

            $cart[$product->id] = [

                'product_id'=>$product->id,
                'name'=>$product->name,
                'thumbnail'=>$product->thumbnail,
                'price'=>$product->price,
                'quantity'=>$request->quantity

            ];
        }

        Session::put('travel_cart',$cart);

        return response()->json([
            'success'=>true,
            'message'=>'Package added to cart'
        ]);
    }


/*
==================================
LOGGED USER DATABASE CART
==================================
*/

$customer = Auth::guard('customer')->user();

$cartItem = CartItem::where('customer_id',$customer->id)
            ->where('product_id',$product->id)
            ->first();

if($cartItem){

    $cartItem->main_product_quantity += $request->quantity;
    $cartItem->save();

}else{

    CartItem::create([

        'customer_id'=>$customer->id,
        'product_id'=>$product->id,
        'main_product_price'=>$product->price,
        'main_product_quantity'=>$request->quantity

    ]);
}

return response()->json([

    'success'=>true,
    'message'=>'Travel package added to cart'

]);

}

/*
================================
CART PAGE
================================
*/

public function index()
{
    $cartItems = [];
    $totalPrice = 0;
    $wishlistItems = [];

    if(Auth::guard('customer')->check()){

        $customer = Auth::guard('customer')->user();

        $cartItems = CartItem::where('customer_id', $customer->id)
    ->whereHas('product', function($q){
        $q->whereIn('type', ['service','travel']); // 🔥 FIXED
    })
    ->with(['product.reviews','color','size'])
    ->get();

        foreach($cartItems as $item){
            $totalPrice += $item->main_product_price * $item->main_product_quantity;
        }

        // ================= WISHLIST (🔥 ADD THIS) =================
        $wishlistItems = Wishlist::with('product')
            ->where('customer_id', $customer->id)
            ->where('type', 'service') // 🔥 IMPORTANT (vtravel only)
            ->latest()
            ->get();

    } else {

        $cartItems = Session::get('travel_cart',[]);

        foreach($cartItems as $item){
            $totalPrice += $item['price'] * $item['quantity'];
        }

        $wishlistItems = []; // guest case
    }

    return view('vtravel.cart',compact('cartItems','totalPrice','wishlistItems'));
}

/*
================================
UPDATE QUANTITY
================================
*/

public function updateQty(Request $request)
{

$cartItem = CartItem::find($request->cart_item_id);

$cartItem->main_product_quantity=$request->quantity;

$cartItem->save();

return response()->json([

'success'=>true

]);

}


/*
================================
REMOVE CART ITEM
================================
*/

public function remove(Request $request)
{

CartItem::where('id',$request->cart_item_id)->delete();

return response()->json([

'success'=>true,
'message'=>'Item removed'

]);

}


/*
================================
CLEAR CART
================================
*/

public function clear()
{

CartItem::where('customer_id',Auth::guard('customer')->id())->delete();

Session::forget('travel_cart');

return response()->json([

'success'=>true

]);

}


/*
================================
CART COUNT
================================
*/

public function getCartCount()
{

if(Auth::guard('customer')->check()){

$count = CartItem::where('customer_id',Auth::guard('customer')->id())
            ->sum('main_product_quantity');

}else{

$cart=Session::get('travel_cart',[]);

$count = array_sum(array_column($cart,'quantity'));

}

return response()->json([

'count'=>$count

]);

}


public function moveToWishlist(Request $request)
{
    try {

        $validator = Validator::make($request->all(), [
            'cart_item_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422);
        }

        if (!Auth::guard('customer')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $customerId = Auth::guard('customer')->id();

        $cartItem = CartItem::where('id',$request->cart_item_id)
            ->where('customer_id',$customerId)
            ->first();

        if(!$cartItem){
            return response()->json([
                'success'=>false,
                'message'=>'Cart item not found'
            ],404);
        }

        /*
        -----------------------------
        INSERT INTO WISHLIST
        -----------------------------
        */

        $exists = Wishlist::where('customer_id',$customerId)
            ->where('product_id',$cartItem->product_id)
            ->where('type','travel')
            ->first();

        if(!$exists){

            Wishlist::create([
                'customer_id'=>$customerId,
                'product_id'=>$cartItem->product_id,
                'type'=>'travel'
            ]);

        }

        /*
        -----------------------------
        DELETE CART ITEM
        -----------------------------
        */

        $cartItem->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Item moved to wishlist'
        ]);

    } catch (\Exception $e){

        Log::error('Vtravel MoveToWishlist Error: '.$e->getMessage());

        return response()->json([
            'success'=>false,
            'message'=>$e->getMessage()
        ],500);
    }
}


}