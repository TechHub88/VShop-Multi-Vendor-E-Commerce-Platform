<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Reward;
use Illuminate\Http\Request;

class VshopReviewController extends Controller
{
    /**
     * Store or update a product review submitted from the vshop product page.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id'       => 'required|integer|exists:products,id',
            'rating'           => 'required|integer|min:1|max:5',
            'title'            => 'required|string|max:255',
            'comment'          => 'required|string',
            'would_recommend'  => 'required|in:0,1',
        ]);

        $customerId = auth()->guard('customer')->id();
        $customer   = auth()->guard('customer')->user();

        $existingReview = ProductReview::where('product_id', $request->product_id)
                            ->where('customer_id', $customerId)
                            ->first();

        if ($existingReview) {
            // Update existing review
            $existingReview->update([
                'rating'           => $request->rating,
                'title'            => $request->title,
                'comment'          => $request->comment,
                'would_recommend'  => $request->would_recommend,
                'is_approved'      => 0, // back to pending after edit
            ]);
            $isNew = false;
        } else {
            // Create new review
            ProductReview::create([
                'product_id'       => $request->product_id,
                'seller_id'        => $request->seller_id,
                'customer_id'      => $customerId,
                'user_name'        => $customer->name,
                'user_email'       => $customer->email,
                'rating'           => $request->rating,
                'title'            => $request->title,
                'comment'          => $request->comment,
                'would_recommend'  => $request->would_recommend,
                'is_approved'      => 0,
                'platform'         => 'vshop',
            ]);

            // Award reward points for writing a review
            Reward::create([
                'customer_id' => $customerId,
                'type'        => 'points',
                'points'      => 5,
                'amount'      => 0.50,
                'description' => 'Reward points for Product Review',
                'status'      => 'active',
                'expiry_date' => now()->addDays(60),
            ]);

            $customer->reward_points = Reward::getValidPoints($customerId);
            $customer->save();

            $isNew = true;
        }

        $message = $isNew
            ? 'Review submitted successfully. It will appear after approval.'
            : 'Review updated successfully. It will appear after approval.';

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
