<?php

namespace App\Http\Controllers\vtravel;
use App\Models\VshopBanner;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Order;


use App\Models\Category;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // VTRAVEL Home - Only show products with type = 'travel'
    public function vtravelhome()
    {
        $trendingProducts = Product::where('status', 'approved')
            ->where('type', 'travel')  // Only travel products
            ->latest()
            ->take(4)
            ->get();

        $discountProducts = Product::where('status', 'approved')
            ->where('type', 'travel')  // Only travel products
            ->whereNotNull('regular_price')
            ->orderByRaw('(regular_price - price) DESC')
            ->take(4)
            ->get();

        return view('vtravel.home', compact(
            'trendingProducts',
            'discountProducts'
        ));
    }

    // VSHOP Home - Only show products with type = 'product'
public function vshophome(Request $request)
{
    $query = $request->q ?? null;

    // Sanitize search query
    $safeQuery = null;
    if ($query) {
        $safeQuery = trim($query);
        $safeQuery = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $safeQuery);
    }

    // Base filter for Vshop products (platform = vshop or both, status = approved)
    $baseFilter = function($q) use ($safeQuery) {
        $q->where('status', 'approved')
          ->where(function($platform) {
              $platform->where('platform', 'vshop')
                       ->orWhere('platform', 'both');
          });
        if ($safeQuery) {
            $q->where(function($sub) use ($safeQuery) {
                $sub->where('name', 'LIKE', "%{$safeQuery}%")
                    ->orWhere('description', 'LIKE', "%{$safeQuery}%");
            });
        }
    };

    // ========== 1. Trending / Most Sold ==========
    $trendingProductIds = \DB::table('order_items')
        ->select('product_id', \DB::raw('SUM(quantity) as total_sold'))
        ->groupBy('product_id')
        ->orderBy('total_sold', 'desc')
        ->take(50)
        ->pluck('product_id')
        ->toArray();

    $trendingProducts = Product::where($baseFilter)
        ->where(function($q) use ($trendingProductIds) {
            if (!empty($trendingProductIds)) {
                $q->whereIn('id', $trendingProductIds)
                  ->orWhere('boost_status', 1);
            } else {
                $q->where('boost_status', 1);
            }
        })
        ->orderBy('boost_status', 'desc')
        ->paginate(10, ['*'], 'trending_page');

    if ($trendingProducts->isEmpty()) {
        $trendingProducts = Product::where($baseFilter)
            ->orderBy('boost_status', 'desc')
            ->latest()
            ->paginate(10, ['*'], 'trending_page');
    }

    // ========== 2. New Arrivals ==========
    $newArrivals = Product::where($baseFilter)->latest()->paginate(10, ['*'], 'new_page');
    
    // ========== 3. Discount Products ==========
    $discountProducts = Product::where($baseFilter)
        ->where(function($q) {
            $q->where(function($sub) {
                $sub->whereNotNull('regular_price')
                    ->whereColumn('regular_price', '>', 'price');
            })
            ->orWhere('boost_status', 1);
        })
        ->orderBy('boost_status', 'desc')
        ->orderByRaw('COALESCE(regular_price - price, 0) DESC')
        ->paginate(10, ['*'], 'deals_page');

    // ========== 4. Categories with product counts (Vshop only) ==========
    $categories = Category::where('status', 'approved')
        ->withCount(['products' => function($query) {
            $query->where('status', 'approved')
                  ->where(function($platform) {
                      $platform->where('platform', 'vshop')
                               ->orWhere('platform', 'both');
                  });
        }])
        ->having('products_count', '>', 0)
        ->get();

    // ========== 5. Search History & Recently Viewed (based on search terms) ==========
    $searchHistory = session()->get('search_history', []);
    if (!empty($safeQuery)) {
        $searchHistory = array_diff($searchHistory, [$safeQuery]);
        array_unshift($searchHistory, $safeQuery);
        $searchHistory = array_slice($searchHistory, 0, 5);
        session()->put('search_history', $searchHistory);
    }

    $recentlyViewed = collect();
    if (!empty($searchHistory)) {
        $recentlyViewed = Product::where('status', 'approved')
            ->where(function($platform) {
                $platform->where('platform', 'vshop')
                         ->orWhere('platform', 'both');
            })
            ->where(function($q) use ($searchHistory) {
                foreach ($searchHistory as $term) {
                    $q->orWhere('name', 'LIKE', "%{$term}%")
                      ->orWhere('description', 'LIKE', "%{$term}%");
                }
            })
            ->latest()
            ->take(8)
            ->get();
    }

    // ========== 6. Banners ==========
    $currentDateTime = now();
    $banners = \App\Models\Advertisement::with('media')
        ->where('is_active', 1)
        ->whereIn('target_type', ['product', 'both'])
        ->where(function($q) use ($currentDateTime) {
            $q->whereNull('start_date')
              ->orWhere('start_date', '<=', $currentDateTime);
        })
        ->where(function($q) use ($currentDateTime) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', $currentDateTime);
        })
        ->orderBy('id', 'desc')
        ->get();

    // ========== 7. Top Rated Products (rating >= 4.5) – FIXED ==========
    // Join with products table to apply platform filter
    $topRated = Product::where($baseFilter)
        ->whereHas('reviews', function($q) {
            $q->select('product_id')
              ->selectRaw('AVG(rating) as avg_rating')
              ->groupBy('product_id')
              ->havingRaw('AVG(rating) >= 4.5');
        })
        ->with(['reviews' => function($q) {
            $q->orderBy('rating', 'desc');
        }])
        ->paginate(10, ['*'], 'top_page');

    // ========== 8. Recommendations (based on RecommendationRule table) ==========
    $recommendedIds = [];
    $rules = \App\Models\RecommendationRule::active()->get();
    
    foreach ($rules as $rule) {
        if (!empty($rule->targets)) {
            $targets = is_array($rule->targets) ? $rule->targets : json_decode($rule->targets, true);
            if (is_array($targets)) {
                $recommendedIds = array_merge($recommendedIds, $targets);
            }
        }
    }
    
    $recommendedIds = array_unique($recommendedIds);
    $recommendations = collect();

    if (!empty($recommendedIds)) {
        $recommendations = Product::where($baseFilter)
            ->whereIn('id', $recommendedIds)
            ->orderByRaw('FIELD(id, ' . implode(',', $recommendedIds) . ') DESC')
            ->paginate(10, ['*'], 'rec_page');
    }

    // Fallback: if no recommendations, show latest products
    if ($recommendations->isEmpty()) {
        $recommendations = Product::where($baseFilter)->latest()->paginate(10, ['*'], 'rec_page');
    }

    return view('vshop.home', compact(
        'trendingProducts',
        'newArrivals',
        'discountProducts',
        'categories',
        'recentlyViewed',
        'searchHistory',
        'query',
        'banners',
        'topRated',
        'recommendations'
    ));
}
    // Dashboard
    public function Home()
    {
        return view('vtravel.dashboard');
    }
    
 
 public function discountProducts()
{
    $products = Product::query()
        ->whereIn('type', ['product', 'service'])
        ->where(function ($platform) {
            $platform->where('platform', 'vshop')
                     ->orWhere('platform', 'both');
        })
        ->where(function ($q) {
            $q->whereNull('created_by')
              ->orWhere(function ($seller) {
                  $seller->where('created_by', 'seller')
                         ->where('status', 'approved');
              });
        })
        ->whereNotNull('regular_price')
        ->whereColumn('regular_price', '>', 'price')
        ->orderByRaw('(regular_price - price) DESC')
        ->paginate(8);

    // ✅ FETCH BANNERS
    $currentDateTime = now();
    $banners = \App\Models\Advertisement::with('media')
        ->where('is_active', 1)
        ->whereIn('target_type', ['product', 'both'])
        ->where(function($q) use ($currentDateTime) {
            $q->whereNull('start_date')
              ->orWhere('start_date', '<=', $currentDateTime);
        })
        ->where(function($q) use ($currentDateTime) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', $currentDateTime);
        })
        ->orderBy('id', 'desc')
        ->get();

    return view('vshop.products.discountproducts', compact('products','banners'));
}

    public function updateCurrentLocation(Request $request)
    {
        $request->validate([
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'pincode' => 'nullable|string',
        ]);

        $locationString = $request->city;
        if ($request->state) {
            $locationString .= ', ' . $request->state;
        }
        if ($request->pincode) {
            $locationString .= ' - ' . $request->pincode;
        }

        // Store in session
        session()->put('current_location', $locationString);
        session()->save();

        // Store in addresses table if logged in
        if (Auth::guard('customer')->check()) {
            try {
                $customerId = Auth::guard('customer')->id();
                $customer = Auth::guard('customer')->user();
                
                $address = \App\Models\Address::where('customer_id', $customerId)
                    ->where('address_type', 'both')
                    ->where('address_line1', 'Current Location')
                    ->first();
                
                if ($address) {
                    $address->update([
                        'city' => $request->city,
                        'state' => $request->state,
                        'pincode' => $request->pincode,
                        'name' => $customer->name ?? 'Customer',
                        'email' => $customer->email ?? '',
                        'phone' => $customer->phone ?? '',
                    ]);
                } else {
                    \App\Models\Address::create([
                        'customer_id' => $customerId,
                        'address_type' => 'both',
                        'address_line1' => 'Current Location',
                        'city' => $request->city,
                        'state' => $request->state,
                        'pincode' => $request->pincode,
                        'name' => $customer->name ?? 'Customer',
                        'email' => $customer->email ?? '',
                        'phone' => $customer->phone ?? '',
                        'country' => 'India',
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to save current location address: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'location' => $locationString]);
    }
    
    
    public function getCounts()
{
    $customer = auth()->guard('customer')->user();
    if (!$customer) {
        return response()->json(['cart' => 0, 'wishlist' => 0]);
    }

    $cartCount = CartItem::where('customer_id', $customer->id)
        ->whereHas('product', fn($q) => $q->where('status', 'active'))
        ->count();

    $wishlistCount = Wishlist::where('customer_id', $customer->id)
        ->whereHas('product', fn($q) => $q->where('status', 'active'))
        ->count();

    return response()->json([
        'cart'     => $cartCount,
        'wishlist' => $wishlistCount,
    ]);
}
}