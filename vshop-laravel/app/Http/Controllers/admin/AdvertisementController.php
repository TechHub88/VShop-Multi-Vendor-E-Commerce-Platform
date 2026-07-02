<?php

namespace App\Http\Controllers\Admin;

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
        $ads = Advertisement::latest()->with('media')->get();

        // Single table (Product + Service)
        $products = DB::table('products')->get();

        return view('admin.advertisements.index', compact('ads','products'));
    }

    /**
     * CREATE PAGE
     */
    public function create()
    {
        $products = DB::table('products')->get();
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
            'videos.*' => 'nullable|mimes:mp4,mov,avi|max:10240',
        ]);

        $ad = Advertisement::create([
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'redirect_url' => $request->redirect_url,
            'target_type' => $request->target_type,
            'product_id' => $request->product_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => 1,
        ]);

        // IMAGE UPLOAD
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('ads/images', 'public');

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
                $path = $vid->store('ads/videos', 'public');

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

        $products = DB::table('products')->get();
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
        ]);

        $ad->update([
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'redirect_url' => $request->redirect_url,
            'target_type' => $request->target_type,
            'product_id' => $request->product_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        // ADD NEW IMAGES
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('ads/images', 'public');

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
                $path = $vid->store('ads/videos', 'public');

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
        // delete media first
        AdvertisementMedia::where('advertisement_id', $id)->delete();

        // delete ad
        Advertisement::destroy($id);

        return back()->with('success', 'Advertisement Deleted Successfully');
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
}