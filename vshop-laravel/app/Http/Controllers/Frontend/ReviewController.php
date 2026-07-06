<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a newly created review.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'comment' => 'required|string',
            'recommend' => 'nullable|boolean',
        ]);

        try {
            // Check if customer is authenticated using customer guard
            if (Auth::guard('customer')->check()) {
                $customer = Auth::guard('customer')->user();
                $customerId = $customer->id;
                $customerName = $customer->name;
                $customerEmail = $customer->email;
            } else {
                // For guest users
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to submit a review.'
                ], 401);
            }

            // Check if customer has already reviewed this product
            $existingReview = ProductReview::where('product_id', $validated['product_id'])
                ->where('customer_id', $customerId)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this product.'
                ], 422);
            }

            // Create the review
            $review = ProductReview::create([
                'product_id' => $validated['product_id'],
                'customer_id' => $customerId,
                'user_name' => $customerName,
                'user_email' => $customerEmail,
                'rating' => $validated['rating'],
                'title' => $validated['title'],
                'comment' => $validated['comment'],
                'would_recommend' => $validated['recommend'] ?? null,
                'is_approved' => true, // Auto-approve for now
                'is_verified_purchase' => $this->checkVerifiedPurchase($validated['product_id'], $customerId),
            ]);

            // Award 5 reward points for review
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

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully!',
                'review' => $review
            ]);

        } catch (\Exception $e) {
            \Log::error('Review submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error submitting review. Please try again.'
            ], 500);
        }
    }

    /**
     * Check if customer has purchased the product (for verified purchase badge)
     */
    private function checkVerifiedPurchase($productId, $customerId): bool
    {
        // Implement your logic to check if customer has purchased this product
        // Example: Check if there's an order with this product for this customer
        // return Order::where('customer_id', $customerId)
        //     ->whereHas('items', function($query) use ($productId) {
        //         $query->where('product_id', $productId);
        //     })
        //     ->where('status', 'completed')
        //     ->exists();
        
        return false; // Placeholder
    }

    /**
     * Get reviews for a product
     */
    public function getProductReviews($productId)
    {
        $reviews = ProductReview::with('customer')
            ->where('product_id', $productId)
            ->approved()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'reviews' => $reviews
        ]);
    }

    /**
     * Get review statistics for a product
     */
    public function getReviewStats($productId)
    {
        $stats = ProductReview::where('product_id', $productId)
            ->approved()
            ->selectRaw('
                COUNT(*) as total_reviews,
                AVG(rating) as average_rating,
                COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star,
                COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star,
                COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star,
                COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star,
                COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star
            ')
            ->first();

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
