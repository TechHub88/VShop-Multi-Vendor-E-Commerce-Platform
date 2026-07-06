<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VshopRatingController extends Controller
{
   public function index($productId)
    {
        $product = Product::with('brand')->findOrFail($productId);

        $reviews = ProductReview::with('customer')
            ->where('product_id', $productId)
            ->latest()
            ->paginate(10);

        $totalReviews = $reviews->count();

        $averageRating = $reviews->avg('rating') ?? 0;

        return view('vshop.ratings.index', compact(
            'product',
            'reviews',
             'totalReviews',
            'averageRating'
        ));
    }
}
