<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SizeGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SellerCategoryController extends Controller
{
    /* =======================
       INDEX – Seller Categories
    ======================= */
    public function index()
    {
        $seller = auth()->guard('seller')->user();

        $categories = Category::where('created_by', 'seller')
            ->where('created_by_id', $seller->id)
            ->latest()
            ->get();

        return view('vshop.seller.categories.index', compact('categories'));
    }


    /* =======================
       CREATE
    ======================= */
    public function create()
    {
        $seller = auth()->guard('seller')->user();
        $userType = $seller->type; // product / travel

        return view('vshop.seller.categories.create', compact('userType'));
    }


    /* =======================
       STORE – Seller Category
    ======================= */
    public function store(Request $request)
    {
        $seller = auth()->guard('seller')->user();
        $sellerId = $seller->id;
        $type = $seller->type; // force seller type

        /* ========= VALIDATION ========= */
        $request->validate([
            'name' => 'required|unique:categories,name',
            'title' => 'required',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($type === 'travel') {
            $request->validate([
                'tour_type' => 'required|in:domestic,international',
                'ideal_for' => 'required',
                'duration_range' => 'required',
                'best_season' => 'required',
            ]);
        }

        /* ========= BANNER UPLOAD ========= */
        $bannerImage = null;
        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/category_banners'), $filename);
            $bannerImage = 'uploads/category_banners/' . $filename;
        }

        /* ========= SIZE GUIDE IMAGE ========= */
        $sizeGuideImage = null;
        if ($type === 'product' && $request->hasFile('size_guide_image')) {
            $file = $request->file('size_guide_image');
            $filename = time() . '_size_guide_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/size_guides'), $filename);
            $sizeGuideImage = 'uploads/size_guides/' . $filename;
        }

        /* ========= CREATE CATEGORY ========= */
        $category = Category::create([
            'type' => $type,
            'name' => $request->name,
            'title' => $request->title,
            'slug' => $request->slug ? Str::slug($request->slug) : Str::slug($request->name),
            'description' => $request->description,

            // travel fields
            'tour_type' => $request->tour_type,
            'ideal_for' => $request->ideal_for,
            'duration_range' => $request->duration_range,
            'best_season' => $request->best_season,

            // SEO
            'meta_title' => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->meta_description,

            'featured' => 0,
            'show_in_store' => $type === 'product' ? 1 : 0,

            // ⭐ SELLER LOGIC
            'created_by' => 'seller',
            'created_by_id' => $sellerId,
            'status' => 'pending',

            'banner_image' => $bannerImage,
            'size_guide_image' => $sizeGuideImage,
        ]);

        /* ========= SIZE GUIDES ========= */
        if ($type === 'product' && $request->has('size_guides')) {
            foreach ($request->size_guides as $sizeData) {
                if (!empty($sizeData['size_label']) && !empty($sizeData['chest'])) {
                    SizeGuide::create([
                        'category_id' => $category->id,
                        'unit' => $sizeData['unit'] ?? 'in',
                        'size_label' => $sizeData['size_label'],
                        'chest' => $sizeData['chest'],
                        'front_length' => $sizeData['front_length'],
                        'sleeve_length' => $sizeData['sleeve_length'],
                    ]);
                }
            }
        }

        return redirect()
            ->route('seller.categories.index')
            ->with('success', 'Category created and sent for admin approval.');
    }


    /* =======================
       EDIT
    ======================= */
    public function edit($id)
    {
        $seller = auth()->guard('seller')->user();

        $category = Category::where('id', $id)
            ->where('created_by', 'seller')
            ->where('created_by_id', $seller->id)
            ->firstOrFail();

        return view('vshop.seller.categories.edit', compact('category'));
    }


    /* =======================
       UPDATE
    ======================= */
    public function update(Request $request, $id)
    {
        $seller = auth()->guard('seller')->user();

        $category = Category::where('id', $id)
            ->where('created_by', 'seller')
            ->where('created_by_id', $seller->id)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|unique:categories,name,' . $id,
            'title' => 'required',
        ]);

        $category->update([
            'name' => $request->name,
            'title' => $request->title,
            'slug' => $request->slug ? Str::slug($request->slug) : Str::slug($request->name),
            'description' => $request->description,

            // ⭐ re-approval required after edit
            'status' => 'pending',
        ]);

        return back()->with('success', 'Category updated & sent for approval.');
    }


    /* =======================
       DELETE
    ======================= */
    public function destroy($id)
    {
        $seller = auth()->guard('seller')->user();

        $category = Category::where('id', $id)
            ->where('created_by', 'seller')
            ->where('created_by_id', $seller->id)
            ->firstOrFail();

        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }
}
