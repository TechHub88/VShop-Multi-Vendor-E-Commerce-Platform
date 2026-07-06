<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\Product;
use App\Models\SellerBanner;
use App\Models\SellerSeo;
use App\Models\SellerWebsiteTemplate;
use App\Models\ProductReview;

class StoreController extends Controller
{
    public function show($slug)
    {
        // Find seller by slug or ID
        $seller = Seller::where('id', $slug)
            ->orWhere('slug', $slug)
            ->firstOrFail();

        // Fixed panel: always vshop
        $panel = 'vshop';

        // ✅ FOR VSHOP ONLY: Products for this seller
        $productsQuery = Product::where('created_by', 'seller')
            ->where('created_by_id', $seller->id)
            ->where('status', 'approved')
            ->where(function($q) {
                $q->where('platform', 'vshop')
                  ->orWhere('platform', 'both');
            });   // ✅ Only vshop/both products

        $products = $productsQuery->latest()->paginate(12);

        // Banners, SEO, Template (no changes)
        $banners = SellerBanner::where('seller_id', $seller->id)
                    ->where('status', 'approved')
                    ->get();

        $seo = SellerSeo::where('seller_id', $seller->id)->first();
        $template = SellerWebsiteTemplate::where('seller_id', $seller->id)
                    ->with('template')
                    ->first();

        // Overall Seller Rating
        $sellerReviews = ProductReview::whereHas('product', function($q) use ($seller) {
            $q->where('created_by_id', $seller->id)
              ->where('created_by', 'seller');
        })->where('is_approved', 1)->get();

        $totalReviews = $sellerReviews->count();
        $avgRating = $totalReviews > 0 ? round($sellerReviews->avg('rating'), 1) : 0;
        
        $ratingBreakdown = [
            5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0
        ];
        
        if ($totalReviews > 0) {
            foreach (range(5, 1, -1) as $star) {
                $count = $sellerReviews->where('rating', $star)->count();
                $ratingBreakdown[$star] = round(($count / $totalReviews) * 100);
            }
        }

        // ------------------ NEW SECTIONS ------------------
        
        $baseProductQuery = function() use ($seller) {
            return Product::where('created_by', 'seller')
                ->where('created_by_id', $seller->id)
                ->where('status', 'approved')
                ->where(function($q) {
                    $q->where('platform', 'vshop')->orWhere('platform', 'both');
                });
        };

        // 1. Top Trending (by sold count)
        $topTrending = $baseProductQuery()->orderBy('sold', 'desc')->take(10)->get();

        // 2. Best Discount (Regular Price - Discount Price)
        $bestDiscount = $baseProductQuery()
            ->whereNotNull('discount_price')
            ->where('discount_price', '>', 0)
            ->whereRaw('regular_price > discount_price')
            ->orderByRaw('(regular_price - discount_price) DESC')
            ->take(10)->get();

        // 3. New Arrival (by created_at)
        $newArrivals = $baseProductQuery()->orderBy('created_at', 'desc')->take(10)->get();

        // 4. Recommended For You (from recommendation_rules where source = seller_id)
        $recommendedProductIds = [];
        $rules = \App\Models\RecommendationRule::where('source_type', 'seller')
                    ->where('source', $seller->id)
                    ->where('is_active', 1)
                    ->get();
        foreach($rules as $rule) {
            $targets = is_array($rule->targets) ? $rule->targets : json_decode($rule->targets, true);
            if(is_array($targets)) {
                $recommendedProductIds = array_merge($recommendedProductIds, $targets);
            }
        }
        $recommendedProducts = $baseProductQuery()->whereIn('id', $recommendedProductIds)->take(10)->get();

        // 5. Most Purchased (from order_items)
        $mostPurchasedIds = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('products.created_by_id', $seller->id)
            ->where('products.created_by', 'seller')
            ->select('order_items.product_id', \Illuminate\Support\Facades\DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('order_items.product_id')
            ->orderBy('total_qty', 'desc')
            ->take(10)
            ->pluck('product_id')->toArray();
        $mostPurchased = $baseProductQuery()->whereIn('id', $mostPurchasedIds)->get()->sortBy(function($model) use ($mostPurchasedIds) {
            return array_search($model->id, $mostPurchasedIds);
        });

        // 6. Most Enquiries (from enquiries)
        $mostEnquiredIds = \Illuminate\Support\Facades\DB::table('enquiries')
            ->where('seller_id', $seller->id)
            ->whereNotNull('product_id')
            ->select('product_id', \Illuminate\Support\Facades\DB::raw('COUNT(id) as total_enq'))
            ->groupBy('product_id')
            ->orderBy('total_enq', 'desc')
            ->take(10)
            ->pluck('product_id')->toArray();
        $mostEnquiries = $baseProductQuery()->whereIn('id', $mostEnquiredIds)->get()->sortBy(function($model) use ($mostEnquiredIds) {
            return array_search($model->id, $mostEnquiredIds);
        });

        // 7. Highest Review Product (from product_reviews)
        $highestReviewIds = \Illuminate\Support\Facades\DB::table('product_reviews')
            ->join('products', 'products.id', '=', 'product_reviews.product_id')
            ->where('products.created_by_id', $seller->id)
            ->where('product_reviews.is_approved', 1)
            ->select('product_reviews.product_id', \Illuminate\Support\Facades\DB::raw('AVG(product_reviews.rating) as avg_rating'))
            ->groupBy('product_reviews.product_id')
            ->orderBy('avg_rating', 'desc')
            ->take(10)
            ->pluck('product_id')->toArray();
        $highestReviews = $baseProductQuery()->whereIn('id', $highestReviewIds)->get()->sortBy(function($model) use ($highestReviewIds) {
            return array_search($model->id, $highestReviewIds);
        });

        // Blade view
        $view = 'store.index';

        return view($view, compact(
            'seller', 'products', 'banners', 'seo', 'template', 'panel', 
            'totalReviews', 'avgRating', 'ratingBreakdown',
            'topTrending', 'bestDiscount', 'newArrivals', 'recommendedProducts',
            'mostPurchased', 'mostEnquiries', 'highestReviews'
        ));
    }
}
