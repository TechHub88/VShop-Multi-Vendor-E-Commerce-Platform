<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;

use App\Models\AttributeTerm;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class VshopWishlistController extends Controller
{
    protected $guard = 'customer';

    /**
     * Display wishlist page
     */
   // In your ProductController or wherever you're loading the products page
public function index()
{
    $products = Product::with('brand')->paginate(12);
   
    $ratings = [5,4,3,2,1];
    $sizes = AttributeTerm::where('attribute_id', $thicknessAttributeId)->get(); // adjust as needed
    $colors = AttributeTerm::where('attribute_id', $colorAttributeId)->get(); // adjust as needed
    
    // Get wishlist IDs for authenticated customer
    $wishlistIds = [];
    if (auth()->guard('customer')->check()) {
        $wishlistIds = Wishlist::where('customer_id', auth()->guard('customer')->id())
            ->pluck('product_id')
            ->toArray();
    }
    
    return view('vshop.products.index', compact('products', 'ratings', 'sizes', 'colors', 'wishlistIds'));
}
    /**
     * Toggle wishlist item (add/remove)
     */
   
    public function toggle(Request $request)
{
    // Add debug logging
    \Log::info('Wishlist toggle called', [
        'request_data' => $request->all(),
        'headers' => $request->headers->all(),
        'method' => $request->method(),
        'content_type' => $request->header('Content-Type')
    ]);

    try {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $customer = auth()->guard('customer')->user();

        if (!$customer) {
            \Log::warning('Unauthorized wishlist attempt');
            return response()->json([
                'success' => false,
                'message' => 'Please login to continue'
            ], 401);
        }

        \Log::info('Customer found', ['customer_id' => $customer->id]);

        $existing = Wishlist::where('customer_id', $customer->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            \Log::info('Removing from wishlist', [
                'wishlist_id' => $existing->id,
                'product_id' => $request->product_id
            ]);
            
            $existing->delete();

            $wishlistCount = Wishlist::where('customer_id', $customer->id)->count();

            return response()->json([
                'success' => true,
                'status' => 'removed',
                'message' => 'Removed from wishlist',
                'wishlist_count' => $wishlistCount
            ]);
        }

        \Log::info('Adding to wishlist', [
            'customer_id' => $customer->id,
            'product_id' => $request->product_id
        ]);

        $wishlist = Wishlist::create([
            'customer_id' => $customer->id,
            'product_id' => $request->product_id
        ]);

        \Log::info('Wishlist item created', ['wishlist_id' => $wishlist->id]);

        $wishlistCount = Wishlist::where('customer_id', $customer->id)->count();

        return response()->json([
            'success' => true,
            'status' => 'added',
            'message' => 'Added to wishlist',
            'wishlist_count' => $wishlistCount
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation error', ['errors' => $e->errors()]);
        return response()->json([
            'success' => false,
            'message' => 'Invalid product ID',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Wishlist toggle error: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to update wishlist: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Get all wishlist items (for AJAX)
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
            Log::error('Get wishlist items error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wishlist items'
            ], 500);
        }
    }

    /**
     * Add product to wishlist
     */
    public function add(Request $request)
    {
        try {
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
                    'message' => 'Please login to add to wishlist'
                ], 401);
            }

            $productId = $request->product_id;
            
            // Check if already in wishlist
            $existing = Wishlist::where('customer_id', $customer->id)
                ->where('product_id', $productId)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product already in wishlist'
                ], 400);
            }

            // Add to wishlist
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

        } catch (\Exception $e) {
            Log::error('Add to wishlist error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add to wishlist'
            ], 500);
        }
    }

    /**
     * Remove product from wishlist
     */
    public function remove($id)
    {
        try {
            $customer = Auth::guard('customer')->user();
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to continue'
                ], 401);
            }

            $wishlistItem = Wishlist::where('customer_id', $customer->id)
                ->where('id', $id)
                ->first();

            if (!$wishlistItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wishlist item not found'
                ], 404);
            }

            $wishlistItem->delete();

            $wishlistCount = Wishlist::where('customer_id', $customer->id)->count();

            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist',
                'wishlist_count' => $wishlistCount
            ]);

        } catch (\Exception $e) {
            Log::error('Remove from wishlist error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove from wishlist'
            ], 500);
        }
    }

    /**
     * Move wishlist item to cart
     */
    public function moveToCart($id)
    {
        try {
            $customer = Auth::guard('customer')->user();
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to continue'
                ], 401);
            }

            $wishlistItem = Wishlist::with('product')
                ->where('customer_id', $customer->id)
                ->where('id', $id)
                ->first();

            if (!$wishlistItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wishlist item not found'
                ], 404);
            }

            // Here you would add to cart logic
            // You can call your cart controller or implement directly
            
            // Delete from wishlist after moving to cart
            $wishlistItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product moved to cart'
            ]);

        } catch (\Exception $e) {
            Log::error('Move to cart error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to move to cart'
            ], 500);
        }
    }

    /**
     * Clear entire wishlist
     */
    public function clear()
    {
        try {
            $customer = Auth::guard('customer')->user();
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to continue'
                ], 401);
            }

            Wishlist::where('customer_id', $customer->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Wishlist cleared successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Clear wishlist error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear wishlist'
            ], 500);
        }
    }

    /**
     * Get wishlist count
     */
    public function getCount()
    {
        try {
            $customer = Auth::guard('customer')->user();
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'count' => 0
                ]);
            }

            $count = Wishlist::where('customer_id', $customer->id)->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Get wishlist count error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'count' => 0
            ], 500);
        }
    }
}
