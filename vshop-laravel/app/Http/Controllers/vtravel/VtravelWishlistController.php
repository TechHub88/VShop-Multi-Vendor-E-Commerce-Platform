<?php

namespace App\Http\Controllers\vtravel;



use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class VtravelWishlistController extends Controller
{
    protected $guard = 'customer';

    /**
     * Display products with wishlist support
     */
    public function index()
    {
        $products = Product::paginate(12);

        // Service days for vtravel
        $serviceDays = ['1 Day','2 Days','3 Days','5 Days','7 Days'];

        $ratings = [5,4,3,2,1];

        $wishlistIds = [];

        if (auth()->guard('customer')->check()) {
            $wishlistIds = Wishlist::where('customer_id', auth()->guard('customer')->id())
                ->pluck('product_id')
                ->toArray();
        }

        return view('vtravel.products.index', compact(
            'products',
            'ratings',
            'serviceDays',
            'wishlistIds'
        ));
    }


 public function toggle(Request $request)
{
    \Log::info('VTRAVEL Wishlist toggle called', [
        'request_data' => $request->all(),
        'headers' => $request->headers->all(),
        'method' => $request->method()
    ]);

    try {

        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            \Log::warning('Unauthorized wishlist attempt');
            return response()->json([
                'success' => false,
                'message' => 'Please login to continue'
            ], 401);
        }

        \Log::info('Customer found', ['customer_id' => $customer->id]);

        // 🔥 GET PRODUCT (IMPORTANT FIX)
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ]);
        }

        $existing = Wishlist::where('customer_id', $customer->id)
            ->where('product_id', $request->product_id)
            ->first();

        // ================= REMOVE =================
        if ($existing) {

            \Log::info('Removing from wishlist', [
                'wishlist_id' => $existing->id
            ]);

            $existing->delete();

            return response()->json([
                'success' => true,
                'status' => 'removed',
                'message' => 'Removed from wishlist'
            ]);
        }

        // ================= ADD =================

        \Log::info('Adding to wishlist', [
            'customer_id' => $customer->id,
            'product_id' => $request->product_id,
            'type' => $product->type
        ]);

        $wishlist = Wishlist::create([
            'customer_id' => $customer->id,
            'product_id' => $request->product_id,
            'type' => $product->type // 🔥 FIXED
        ]);

        \Log::info('Wishlist item created', [
            'wishlist_id' => $wishlist->id
        ]);

        return response()->json([
            'success' => true,
            'status' => 'added',
            'message' => 'Added to wishlist'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {

        \Log::error('Validation error', ['errors' => $e->errors()]);

        return response()->json([
            'success' => false,
            'message' => 'Invalid product ID',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {

        \Log::error('Vtravel wishlist error: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to update wishlist'
        ], 500);
    }
}

    /**
     * Get wishlist items (AJAX)
     */
    public function getItems()
    {
        try {

            $customer = Auth::guard('customer')->user();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $wishlistItems = Wishlist::where('customer_id', $customer->id)
                ->pluck('product_id')
                ->toArray();

            return response()->json([
                'success' => true,
                'items' => $wishlistItems,
                'count' => count($wishlistItems)
            ]);

        } catch (\Exception $e) {

            Log::error('Get wishlist items error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wishlist items'
            ], 500);
        }
    }


    /**
     * Add to wishlist
     */
    public function add(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product'
            ], 422);
        }

        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Please login'
            ], 401);
        }

        $productId = $request->product_id;

        $existing = Wishlist::where('customer_id', $customer->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in wishlist'
            ]);
        }

        Wishlist::create([
            'customer_id' => $customer->id,
            'product_id' => $productId
        ]);

        $wishlistCount = Wishlist::where('customer_id', $customer->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist',
            'wishlist_count' => $wishlistCount
        ]);
    }


    /**
     * Remove from wishlist
     */
    public function remove($id)
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Please login'
            ], 401);
        }

        $wishlistItem = Wishlist::where('customer_id', $customer->id)
            ->where('id', $id)
            ->first();

        if (!$wishlistItem) {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist item not found'
            ]);
        }

        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist'
        ]);
    }


    /**
     * Clear wishlist
     */
    public function clear()
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Please login'
            ], 401);
        }

        Wishlist::where('customer_id', $customer->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist cleared'
        ]);
    }


    /**
     * Wishlist count
     */
    public function getCount()
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return response()->json([
                'success' => true,
                'count' => 0
            ]);
        }

        $count = Wishlist::where('customer_id', $customer->id)->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}