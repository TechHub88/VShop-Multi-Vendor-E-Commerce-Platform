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

        // Blade view
        $view = 'store.index';

        return view($view, compact('seller', 'products', 'banners', 'seo', 'template', 'panel', 'totalReviews', 'avgRating', 'ratingBreakdown'));
    }
}