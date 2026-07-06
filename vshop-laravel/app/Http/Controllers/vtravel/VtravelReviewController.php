<?php

namespace App\Http\Controllers\vtravel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductReview;

class VtravelReviewController extends Controller
{

    public function reviews($product_id)
    {

        $product = Product::with('category','seller')->findOrFail($product_id);

        $reviews = ProductReview::with('customer')
                    ->where('product_id',$product_id)
                    ->latest()
                    ->get();

        $totalReviews = $reviews->count();

        $averageRating = $reviews->avg('rating') ?? 0;

        return view('vtravel.reviews.index',compact(
            'product',
            'reviews',
            'totalReviews',
            'averageRating'
        ));

    }
    
    public function store(Request $request)
    {
        $request->validate([
            'product_id'=>'required',
            'rating'=>'required|integer|min:1|max:5',
            'title'=>'required|string|max:255',
            'comment'=>'required|string'
        ]);

        $customerId = auth()->guard('customer')->id();
        $customer = auth()->guard('customer')->user();

        $existingReview = ProductReview::where('product_id', $request->product_id)
                            ->where('customer_id', $customerId)
                            ->first();

        if ($existingReview) {
            $existingReview->update([
                'rating'=>$request->rating,
                'title'=>$request->title,
                'comment'=>$request->comment,
                'would_recommend'=>$request->would_recommend,
                'is_approved'=>0
            ]);
            $isNew = false;
        } else {
            ProductReview::create([
                'product_id'=>$request->product_id,
                'seller_id'=>$request->seller_id,
                'customer_id'=>$customerId,
                'user_name'=>$customer->name,
                'user_email'=>$customer->email,
                'rating'=>$request->rating,
                'title'=>$request->title,
                'comment'=>$request->comment,
                'would_recommend'=>$request->would_recommend,
                'is_approved'=>0
            ]);

            \App\Models\Reward::create([
                'customer_id' => $customerId,
                'type'        => 'points',
                'points'      => 5,
                'amount'      => 0.50,
                'description' => 'Reward points for Product Review',
                'status'      => 'active',
                'expiry_date' => now()->addDays(60),
            ]);

            $customer->reward_points = \App\Models\Reward::getValidPoints($customerId);
            $customer->save();
            $isNew = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $isNew ? 'Review submitted successfully. Waiting for approval.' : 'Review updated successfully. Waiting for approval.'
            ]);
        }

        return back()->with('success', $isNew ? 'Review submitted successfully. Waiting for approval.' : 'Review updated successfully. Waiting for approval.');
    }

}
