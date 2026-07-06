<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductAttribute; 
use App\Models\Attribute; 
use App\Models\AttributeTerm;


use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
class ProductController extends Controller
{
    /* =======================
       LIST - Role Based
    ======================= */
  public function index(Request $request)
{
    // ✅ Get panel from session
    $panel = session('admin_panel', 'product'); // default product

    // ✅ Map panel → type
    $type = $panel == 'travel' ? 'travel' : 'product';

    $query = Product::query();

    // ✅ Apply type filter
    $query->where('type', $type);

    // ✅ Brand filter
    if ($request->filled('brand_id')) {
        $query->where('brand_id', $request->brand_id);
    }

    // ✅ Category filter
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // ✅ Data
    $products = $query->latest()->paginate(20);
    $brands = Brand::all();
    $categories = Category::all();

    // ✅ Selected brand (optional)
    $brand = $request->brand_id ? Brand::find($request->brand_id) : null;

    // ✅ Single view (dynamic inside blade)
    return view('admin.products', compact(
        'products',
        'brands',
        'categories',
        'brand',
        'panel',
        'type'
    ));
}

  
public function create()
{
    $user = auth()->user();
    $userType = $user->type; // product / travel

    // Categories by same user type
    $categories = Category::where('type', $userType)->get();

    // Brand type mapping
    $brandType = ($userType === 'travel') ? 'location' : 'brand';

    // Fetch brands based on mapped type
    $brands = Brand::where('type', $brandType)->get();

    return view('admin.products.create', compact(
        'categories',
        'brands',
        'userType'
    ));
}


    /* =======================
       STORE PRODUCT/TOUR - Role Based
    ======================= */
    public function store(Request $request)
    {
        $user = auth()->user();
        $userType = $user->type;
        
        // Determine product type based on user
        $productType = $userType; // travel or product
        
        if ($userType == 'travel') {
            // Travel Package Validation
            $request->validate([
                'name'         => 'required|string|max:255',
                'category_id'  => 'required|exists:categories,id',
                'brand_id'     => 'required|exists:brands,id',
                'price'        => 'required|numeric|min:0',
                'regular_price'=> 'nullable|numeric|min:0',
                'duration'     => 'required|string|max:50',
                'tour_type'    => 'required|in:domestic,international',
                'thumbnail'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'images.*'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);
            
            $uploadPath = 'uploads/tours';
            $galleryPath = 'uploads/tours/gallery';
            
        } else {
            // Product Validation
            $request->validate([
                'name'         => 'required|string|max:255',
                'sku'          => 'required|string|max:255|unique:products',
                'category_id'  => 'required|exists:categories,id',
                'brand_id'     => 'required|exists:brands,id',
                'price'        => 'required|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0',
                'product_type' => 'required|in:simple,attribute,combo',
                'thumbnail'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'images.*'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);
            
            $uploadPath = 'uploads/products';
            $galleryPath = 'uploads/products/gallery';
        }
        
        // Create directories
        if (!file_exists(public_path($uploadPath))) {
            mkdir(public_path($uploadPath), 0755, true);
        }
        if (!file_exists(public_path($galleryPath))) {
            mkdir(public_path($galleryPath), 0755, true);
        }
        
        /* ========= THUMBNAIL ========= */
        $thumbnail = null;
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $name = time().'_thumb_'.$file->getClientOriginalName();
            $file->move(public_path($uploadPath), $name);
            $thumbnail = $uploadPath.'/'.$name;
        }
        
        /* ========= GALLERY ========= */
        $galleryImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $name = time().'_gallery_'.$img->getClientOriginalName();
                $img->move(public_path($galleryPath), $name);
                $galleryImages[] = $galleryPath.'/'.$name;
            }
        }
        
        /* ========= CREATE PRODUCT ========= */
        $productData = [
            'type' => $productType,
            'name' => $request->name,
            'sku' => $userType == 'travel' ? 'TOUR-'.time() : $request->sku,
            'product_type' => $userType == 'travel' ? 'simple' : $request->product_type,
            'price' => $request->price,
            'regular_price' => $request->regular_price ?? null,
            'discount_price' => $request->discount_price ?? null,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'description' => $request->description,
            'thumbnail' => $thumbnail,
            'images' => json_encode($galleryImages),
            'is_featured' => $request->is_featured ?? 0,
            'status' => $request->status ?? 1,
            'meta_title' => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->meta_description,
        ];
        
        // Add travel-specific fields
        if ($userType == 'travel') {
            $productData['duration'] = $request->duration;
            $productData['tour_type'] = $request->tour_type;
            $productData['highlights'] = $request->highlights;
            $productData['itinerary'] = $request->itinerary;
            $productData['inclusions'] = $request->inclusions;
            $productData['exclusions'] = $request->exclusions;
        }
        
        Product::create($productData);
        
        $typeLabel = $userType == 'travel' ? 'Tour Package' : 'Product';
        return redirect()
            ->route('admin.products.index')
            ->with('success', $typeLabel . ' Created Successfully');
    }

    /* =======================
       EDIT FORM - Role Based
    ======================= */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $user = auth()->user();
        $userType = $user->type;
        
        // Check permission
        if ($product->type != $userType && $userType != 'admin') {
            return redirect()->route('admin.products.index')
                ->with('error', 'You do not have permission to edit this item.');
        }
        
        $categories = Category::where('type', $product->type)->get();
        $brands = Brand::all();
        
        if ($product->type == 'travel') {
            return view('admin.products.travel-edit', compact('product', 'categories', 'brands', 'userType'));
        } else {
            return view('admin.products.edit', compact('product', 'categories', 'brands', 'userType'));
        }
    }

    /* =======================
       UPDATE - Role Based
    ======================= */
    public function update(Request $request, Product $product)
    {
        $user = auth()->user();
        $userType = $user->type;
        
        // Check permission
        if ($product->type != $userType && $userType != 'admin') {
            return redirect()->route('admin.products.index')
                ->with('error', 'You do not have permission to update this item.');
        }
        
        if ($product->type == 'travel') {
            // Travel Package Update Validation
            $request->validate([
                'name'         => 'required|string|max:255',
                'category_id'  => 'required|exists:categories,id',
                'brand_id'     => 'required|exists:brands,id',
                'price'        => 'required|numeric|min:0',
                'regular_price'=> 'nullable|numeric|min:0',
                'duration'     => 'required|string|max:50',
                'tour_type'    => 'required|in:domestic,international',
                'thumbnail'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'images.*'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);
            
            $uploadPath = 'uploads/tours';
            $galleryPath = 'uploads/tours/gallery';
            
        } else {
            // Product Update Validation
            $request->validate([
                'name'         => 'required|string|max:255',
                'sku'          => 'required|string|max:255|unique:products,sku,' . $product->id,
                'category_id'  => 'required|exists:categories,id',
                'brand_id'     => 'required|exists:brands,id',
                'price'        => 'required|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0',
                'product_type' => 'required|in:simple,attribute,combo',
                'thumbnail'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'images.*'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);
            
            $uploadPath = 'uploads/products';
            $galleryPath = 'uploads/products/gallery';
        }
        
        /* ========= THUMBNAIL UPDATE ========= */
        $thumbnail = $product->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($thumbnail && file_exists(public_path($thumbnail))) {
                unlink(public_path($thumbnail));
            }
            $file = $request->file('thumbnail');
            $name = time().'_thumb_'.$file->getClientOriginalName();
            $file->move(public_path($uploadPath), $name);
            $thumbnail = $uploadPath.'/'.$name;
        }
        
        /* ========= GALLERY UPDATE ========= */
        $galleryImages = json_decode($product->images, true) ?? [];
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $name = time().'_gallery_'.$img->getClientOriginalName();
                $img->move(public_path($galleryPath), $name);
                $galleryImages[] = $galleryPath.'/'.$name;
            }
        }
        
        /* ========= UPDATE PRODUCT ========= */
        $updateData = [
            'name' => $request->name,
            'sku' => $product->type == 'travel' ? $product->sku : $request->sku,
            'product_type' => $product->type == 'travel' ? 'simple' : $request->product_type,
            'price' => $request->price,
            'regular_price' => $request->regular_price ?? null,
            'discount_price' => $request->discount_price ?? null,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'description' => $request->description,
            'thumbnail' => $thumbnail,
            'images' => json_encode($galleryImages),
            'is_featured' => $request->is_featured ?? 0,
            'status' => $request->status ?? 1,
            'meta_title' => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->meta_description,
        ];
        
        // Update travel-specific fields
        if ($product->type == 'travel') {
            $updateData['duration'] = $request->duration;
            $updateData['tour_type'] = $request->tour_type;
            $updateData['highlights'] = $request->highlights;
            $updateData['itinerary'] = $request->itinerary;
            $updateData['inclusions'] = $request->inclusions;
            $updateData['exclusions'] = $request->exclusions;
        }
        
        $product->update($updateData);
        
        $typeLabel = $product->type == 'travel' ? 'Tour Package' : 'Product';
        return back()->with('success', $typeLabel . ' Updated Successfully');
    }

    /* =======================
       DELETE - Role Based
    ======================= */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $user = auth()->user();
        $userType = $user->type;
        
        // Check permission
        if ($product->type != $userType && $userType != 'admin') {
            return back()->with('error', 'You do not have permission to delete this item.');
        }
        
        // Delete thumbnail
        if ($product->thumbnail && file_exists(public_path($product->thumbnail))) {
            unlink(public_path($product->thumbnail));
        }
        
        // Delete gallery images
        $gallery = json_decode($product->images, true);
        if ($gallery) {
            foreach ($gallery as $img) {
                if (file_exists(public_path($img))) {
                    unlink(public_path($img));
                }
            }
        }
        
        $typeLabel = $product->type == 'travel' ? 'Tour Package' : 'Product';
        $product->delete();
        
        return back()->with('success', $typeLabel . ' Deleted Successfully');
    }
    
    /* =======================
       ATTRIBUTES PAGE (For Product Admin Only)
    ======================= */
  
public function attributes($id)
{
    $product = Product::findOrFail($id);
    $user = auth()->user();
    $userType = $user->type;

    // Permission check
    if ($userType != 'product' && $userType != 'admin') {
        return redirect()->route('admin.products.index')
            ->with('error', 'You do not have permission to access attributes.');
    }

    /* =========================
       LOAD SIZE + COLOR WITH TERMS
    ========================= */
    $attributes = Attribute::with('terms')
        ->whereIn(DB::raw('LOWER(name)'), ['size','color'])
        ->get()
        ->keyBy(fn($a) => strtolower($a->name));

    /* =========================
       LOAD EXISTING VARIANTS
    ========================= */
    $variants = ProductAttribute::with(['attribute', 'term'])
    ->where('proid', $product->id)
    ->get()
    ->groupBy('combination_key');


    return view('admin.products.attributes', compact('product','attributes','variants'));
}
public function attributesStore(Request $request, $productId)
{
    $request->validate([
        'variants' => 'required|array',
    ]);

    foreach ($request->variants as $variant) {

        if(empty($variant['size']) || empty($variant['color'])) {
            continue;
        }

        // combination key generate
        $combinationKey = $variant['size'].'-'.$variant['color'];

        // delete duplicate
        ProductAttribute::where('proid', $productId)
            ->where('combination_key', $combinationKey)
            ->delete();

        // insert TWO rows (size + color)
        foreach (['size' => $variant['size'], 'color' => $variant['color']] as $type => $termId) {

            $attributeId = AttributeTerm::find($termId)->attribute_id;

            ProductAttribute::create([
                'proid' => $productId,
                'combination_key' => $combinationKey,
                'attribute_id' => $attributeId,
                'attribute_term_id' => $termId,
                'regular_price' => $variant['regular_price'] ?? 0,
                'price' => $variant['price'] ?? 0,
                'product_stock' => $variant['stock'] ?? 0,
                'product_unit' => 1,
                'status' => 'Y',
            ]);
        }
    }

    return back()->with('success', 'Variants saved successfully');
}
    public function combo($id)
    {
        $product = Product::findOrFail($id);
        $user = auth()->user();
        $userType = $user->type;
        
        // Only product admin can access combo
        if ($userType != 'product' && $userType != 'admin') {
            return redirect()->route('admin.products.index')
                ->with('error', 'You do not have permission to create combos.');
        }
        
        return view('admin.products.combo', compact('product'));
    }
}
