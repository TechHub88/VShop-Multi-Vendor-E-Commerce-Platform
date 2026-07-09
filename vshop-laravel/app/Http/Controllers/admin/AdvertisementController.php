<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\AdvertisementMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdvertisementController extends Controller
{
    /**
     * LIST PAGE
     */
    public function index()
    {
        $ads = Advertisement::latest()->with(['media', 'category'])->get();

        // Single table (Product + Service)
        $products = DB::table('products')->get();

        return view('admin.advertisements.index', compact('ads','products'));
    }

    /**
     * CREATE PAGE
     */
    public function create()
    {
        $products = \App\Models\Product::with('category')->get();
        $categories = DB::table('marketplace_categories')->get();

        return view('admin.advertisements.create', compact('products', 'categories'));
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        // VALIDATION 🔥
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required',
            'target_type' => 'required',
            'product_id' => 'nullable|exists:products,id',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'videos.*' => 'nullable|mimes:mp4,mov,avi|max:15360',
        ]);

        $catName = null;
        if ($request->category_id) {
            $cat = DB::table('marketplace_categories')->where('id', $request->category_id)->first();
            if ($cat) $catName = $cat->name;
        }

        $ad = Advertisement::create([
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'redirect_url' => $request->redirect_url,
            'target_type' => $request->target_type,
            'category_id' => $request->category_id,
            'category_name' => $catName,
            'product_id' => $request->product_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => 1,
        ]);

        // Send notification to all active sellers
        $categoryName = 'All Categories';
        if ($request->category_id) {
            $category = DB::table('marketplace_categories')->where('id', $request->category_id)->first();
            if ($category) {
                $categoryName = $category->name;
            }
        }
        
        $sellers = \App\Models\Seller::where('status', 'approved')->get();
        foreach ($sellers as $seller) {
            \App\Models\Notification::create([
                'seller_id' => $seller->id,
                'type' => 'campaign',
                'message' => 'New ' . ucfirst($request->type) . ' "' . $request->title . '" created for category: ' . $categoryName . '. Please list your products with offers to participate!',
                'link' => 'https://vshop.zelofx.com/seller/products',
                'channel' => 'in_app',
                'is_read' => 0
            ]);
        }

        // IMAGE UPLOAD
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $filename = \Illuminate\Support\Str::random(40) . '.' . $img->getClientOriginalExtension();
                $path = uploadToR2($img, 'ads/images', $filename);

                AdvertisementMedia::create([
                    'advertisement_id' => $ad->id,
                    'media_type' => 'image',
                    'file_path' => $path
                ]);
            }
        }

        // VIDEO UPLOAD
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $vid) {
                $filename = \Illuminate\Support\Str::random(40) . '.' . $vid->getClientOriginalExtension();
                $path = uploadToR2($vid, 'ads/videos', $filename);

                AdvertisementMedia::create([
                    'advertisement_id' => $ad->id,
                    'media_type' => 'video',
                    'file_path' => $path
                ]);
            }
        }

        return redirect()->route('admin.ads.index')->with('success', 'Advertisement Created Successfully');
    }

    /**
     * EDIT PAGE
     */
    public function edit($id)
    {
        $ad = Advertisement::with('media')->findOrFail($id);

        $products = \App\Models\Product::with('category')->get();
        $categories = DB::table('marketplace_categories')->get();

        return view('admin.advertisements.edit', compact('ad', 'products', 'categories'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $ad = Advertisement::findOrFail($id);

        // VALIDATION
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required',
            'target_type' => 'required',
            'product_id' => 'nullable|exists:products,id',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'videos.*' => 'nullable|mimes:mp4,mov,avi|max:15360',
        ]);

        $catName = null;
        if ($request->category_id) {
            $cat = DB::table('marketplace_categories')->where('id', $request->category_id)->first();
            if ($cat) $catName = $cat->name;
        }

        $ad->update([
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'redirect_url' => $request->redirect_url,
            'target_type' => $request->target_type,
            'category_id' => $request->category_id,
            'category_name' => $catName,
            'product_id' => $request->product_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        // ADD NEW IMAGES
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $filename = \Illuminate\Support\Str::random(40) . '.' . $img->getClientOriginalExtension();
                $path = uploadToR2($img, 'ads/images', $filename);

                AdvertisementMedia::create([
                    'advertisement_id' => $ad->id,
                    'media_type' => 'image',
                    'file_path' => $path
                ]);
            }
        }

        // ADD NEW VIDEOS
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $vid) {
                $filename = \Illuminate\Support\Str::random(40) . '.' . $vid->getClientOriginalExtension();
                $path = uploadToR2($vid, 'ads/videos', $filename);

                AdvertisementMedia::create([
                    'advertisement_id' => $ad->id,
                    'media_type' => 'video',
                    'file_path' => $path
                ]);
            }
        }

        return redirect()->route('admin.ads.index')->with('success', 'Advertisement Updated Successfully');
    }

    /**
     * DELETE
     */
    public function destroy($id)
    {
        // delete media first and cleanup R2 files
        $medias = AdvertisementMedia::where('advertisement_id', $id)->get();
        foreach ($medias as $media) {
            if ($media->file_path) {
                deleteFromR2($media->file_path);
            }
            $media->delete();
        }

        // delete ad
        Advertisement::destroy($id);

        return back()->with('success', 'Advertisement Deleted Successfully');
    }

    /**
     * DELETE MEDIA ITEM
     */
    public function deleteMedia($id)
    {
        try {
            $media = AdvertisementMedia::findOrFail($id);
            if ($media->file_path) {
                deleteFromR2($media->file_path);
            }
            $media->delete();
            return response()->json(['success' => true, 'message' => 'Media removed successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete media: ' . $e->getMessage()], 500);
        }
    }

    /**
     * TOGGLE STATUS
     */
    public function toggleStatus(Request $request)
    {
        $ad = Advertisement::findOrFail($request->id);
        $ad->is_active = $request->status;
        $ad->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    /**
     * GET CAMPAIGN PARTICIPANTS
     */
    public function getCampaignParticipants($id)
    {
        $ad = Advertisement::findOrFail($id);

        if (!$ad->category_id) {
            return response()->json(['sellers' => []]);
        }

        // Find products created or updated during campaign with discount in this category
        $products = DB::table('products')
            ->where('category_id', $ad->category_id)
            ->where(function($query) use ($ad) {
                if ($ad->start_date && $ad->end_date) {
                    $query->whereBetween('created_at', [$ad->start_date, $ad->end_date])
                          ->orWhereBetween('updated_at', [$ad->start_date, $ad->end_date]);
                }
            })
            // ->where('discount_price', '>', 0) // Assume they offered a discount
            ->where('created_by', 'seller')
            ->get();

        $sellerIds = $products->pluck('created_by_id')->unique();
        $sellers = \App\Models\Seller::whereIn('id', $sellerIds)->select('id', 'first_name', 'last_name', 'org_name', 'email')->get();

        return response()->json(['sellers' => $sellers]);
    }
}
