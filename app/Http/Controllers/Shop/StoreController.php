<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\Product;
use App\Models\SellerBanner;
use App\Models\SellerSeo;
use App\Models\SellerWebsiteTemplate;

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

        // Blade view
        $view = 'store.index';

        return view($view, compact('seller', 'products', 'banners', 'seo', 'template', 'panel'));
    }
}