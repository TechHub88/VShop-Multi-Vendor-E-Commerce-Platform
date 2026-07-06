<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminReviewController extends Controller
{

   

    private function getProductType()
    {
        $type = auth()->user()->type ?? null;

        if(in_array($type, ['travel','service'])){
            return 'service';
        }

        return 'product';
    }


    /*
    |--------------------------------------------------------------------------
    | Display Reviews
    |--------------------------------------------------------------------------
    */

public function index(Request $request)
{
    $type = $this->getProductType();
    
    // Get seller_id from request (from bad reviews page click)
    $sellerId = $request->seller_id;
    
    // Store seller info if filtering by seller
    $filteredSeller = null;
    
    $query = ProductReview::with(['product', 'product.seller', 'customer'])
        ->whereHas('product', function($q) use ($type) {
            $q->where('type', $type);
        });
    
    /* ================= FILTER BY SELLER (from bad reviews page) ================= */
    if ($sellerId) {
        $query->whereHas('product', function($q) use ($sellerId) {
            $q->where('created_by_id', $sellerId);
        });
        
        // Get seller info for display
        $filteredSeller = \App\Models\Seller::find($sellerId);
    }
    
    /* ================= SEARCH ================= */
    if ($request->search) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('user_name', 'like', "%{$search}%")
              ->orWhere('title', 'like', "%{$search}%")
              ->orWhere('comment', 'like', "%{$search}%")
              ->orWhereHas('product', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              });
        });
    }
    
    /* ================= STATUS ================= */
    if ($request->status) {
        if ($request->status == 'approved') {
            $query->where('is_approved', true);
        }
        if ($request->status == 'pending') {
            $query->where('is_approved', false);
        }
    }
    
    /* ================= RATING ================= */
    if ($request->rating) {
        $query->where('rating', $request->rating);
    }
    
    /* ================= PAGINATION ================= */
    $reviews = $query
        ->latest()
        ->paginate(20)
        ->appends($request->all()); // Preserve all filter parameters
    
    /* ================= STATS ================= */
    // Get stats for cards (considering product type)
    $stats = [
        'total_reviews' => ProductReview::whereHas('product', function($q) use ($type) {
            $q->where('type', $type);
        })->count(),
        
        'approved_reviews' => ProductReview::whereHas('product', function($q) use ($type) {
            $q->where('type', $type);
        })->where('is_approved', true)->count(),
        
        'pending_reviews' => ProductReview::whereHas('product', function($q) use ($type) {
            $q->where('type', $type);
        })->where('is_approved', false)->count(),
        
        'average_rating' => round(ProductReview::whereHas('product', function($q) use ($type) {
            $q->where('type', $type);
        })->avg('rating'), 1),
    ];
    
    return view('admin.reviews.index', compact('reviews', 'stats', 'filteredSeller'));
}



    /*
    |--------------------------------------------------------------------------
    | Edit Review
    |--------------------------------------------------------------------------
    */

    public function edit(ProductReview $review)
    {
        return view(
            'admin.reviews.edit',
            compact('review')
        );
    }



    /*
    |--------------------------------------------------------------------------
    | Update Review
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, ProductReview $review)
    {

        $validated = $request->validate([

            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'comment' => 'required|string',
            'is_approved' => 'required|boolean',
            'is_verified_purchase' => 'boolean',
            'would_recommend' => 'nullable|boolean',

        ]);

        $review->update($validated);

        return redirect()
            ->route('admin.reviews.index')
            ->with('success','Review updated successfully!');

    }
    
    
    public function approve($id)
{
    $review = ProductReview::findOrFail($id);

    $review->is_approved = 1;
    $review->save();

    return response()->json([
        'success' => true
    ]);
}


public function reject($id)
{
    $review = ProductReview::findOrFail($id);

    $review->is_approved = 0;
    $review->save();

    return response()->json([
        'success' => true
    ]);
}

    /*
    |--------------------------------------------------------------------------
    | Block / Inactivate Seller from Review
    |--------------------------------------------------------------------------
    */

    public function blockSeller(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $review = ProductReview::with('product.seller')->findOrFail($id);
        $seller = $review->product->seller;

        if ($seller) {
            $seller->block($request->reason);
            return response()->json(['success' => true, 'message' => 'Seller blocked successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Seller not found.'], 404);
    }

    public function inactiveSeller(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $review = ProductReview::with('product.seller')->findOrFail($id);
        $seller = $review->product->seller;

        if ($seller) {
            $seller->update([
                'status' => 'inactive', 
                'is_active' => 0,
                'deactivated_reason' => $request->reason,
                'deactivated_at' => now()
            ]);
            return response()->json(['success' => true, 'message' => 'Seller inactivated successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Seller not found.'], 404);
    }



    /*
    |--------------------------------------------------------------------------
    | Delete Review
    |--------------------------------------------------------------------------
    */

    public function destroy(ProductReview $review)
    {

        $review->delete();

        return redirect()
            ->route('admin.reviews.index')
            ->with('success','Review deleted successfully!');

    }



    /*
    |--------------------------------------------------------------------------
    | Toggle Review Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(ProductReview $review)
    {

        $review->update([
            'is_approved' => !$review->is_approved
        ]);

        $status = $review->is_approved ? 'approved' : 'pending';

        return response()->json([
            'success' => true,
            'message' => "Review {$status} successfully!",
            'is_approved' => $review->is_approved
        ]);

    }



    /*
    |--------------------------------------------------------------------------
    | Admin Reply
    |--------------------------------------------------------------------------
    */

    public function addReply(Request $request, ProductReview $review)
    {

        $validated = $request->validate([
            'admin_reply' => 'required|string'
        ]);

        $review->update([
            'admin_reply' => $validated['admin_reply'],
            'replied_at' => now(),
            'replied_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply added successfully!',
            'reply' => $review->admin_reply,
            'replied_at' => $review->replied_at->format('M j, Y g:i A')
        ]);

    }



    /*
    |--------------------------------------------------------------------------
    | Review Stats
    |--------------------------------------------------------------------------
    */

    public function getStats()
    {

        $type = $this->getProductType();

        $query = ProductReview::whereHas('product',function($q) use ($type){
            $q->where('type',$type);
        });


        $stats = [

            'total_reviews' => $query->count(),

            'approved_reviews' => $query
                ->where('is_approved',true)
                ->count(),

            'pending_reviews' => $query
                ->where('is_approved',false)
                ->count(),

            'average_rating' => round(
                $query->where('is_approved',true)
                    ->avg('rating') ?? 0,1
            ),

            'recent_reviews' => ProductReview::with('product')
                ->whereHas('product',function($q) use ($type){
                    $q->where('type',$type);
                })
                ->latest()
                ->take(5)
                ->get()

        ];

        return response()->json($stats);

    }

}
