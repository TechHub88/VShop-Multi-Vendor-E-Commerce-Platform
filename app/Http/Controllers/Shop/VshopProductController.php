<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\VshopBanner;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Brand;
use App\Models\AttributeTerm;
use App\Models\ProductReview;

class VshopProductController extends Controller
{


private function notify($type, $message, $route = null)
{
    try {

        $sellerId = auth('seller')->id();

        if(!$sellerId) return; // ✅ avoid error

        \App\Models\Notification::create([
            'seller_id' => $sellerId,
            'type' => $type,
            'message' => $message,
            'link' => $route ? route($route) : null,
        ]);

    } catch (\Exception $e) {
        \Log::error('Notification Error: '.$e->getMessage());
    }
}


public function index(Request $request)
{
    $query = Product::query();

    // ================= DETERMINE WHICH TYPES TO SHOW =================
    $allowedTypes = [];

    if ($request->filled('type')) {
        $reqType = $request->type;
        switch ($reqType) {
            case 'vshop-product':
                $allowedTypes = ['product', 'vshop-product'];
                break;
            case 'vshop-service':
                $allowedTypes = ['service'];
                break;
            case 'vshop-professional':
                $allowedTypes = ['professional', 'vshop-professional'];
                break;
            case 'vtravel-tour':
                $allowedTypes = ['tour', 'vtravel-tour'];
                break;
            case 'vtravel-information_advisory':
                $allowedTypes = ['vtravel-information_advisory'];
                break;
            case 'vtravel-professional':
                $allowedTypes = ['professional', 'vtravel-professional'];
                break;
            default:
                $allowedTypes = [$reqType];
        }
    } else {
        $panels = session('selected_panels', []);
        if (in_array('product', $panels)) {
            $allowedTypes = array_merge($allowedTypes, ['product', 'vshop-product', 'service', 'professional', 'vshop-professional']);
        }
        if (in_array('travel', $panels)) {
            $allowedTypes = array_merge($allowedTypes, ['tour', 'vtravel-tour', 'vtravel-information_advisory', 'vtravel-professional']);
        }
        if (empty($allowedTypes)) {
            $allowedTypes = ['product', 'vshop-product', 'service', 'professional', 'vshop-professional', 'tour', 'vtravel-information_advisory'];
        }
    }

    $query->whereIn('type', array_unique($allowedTypes));

    // Platform filter
    $query->where(function ($platform) {
        $platform->where('platform', 'vshop')
                 ->orWhere('platform', 'both');
    });

    // Seller/Admin status filter
    $query->where(function ($q) {
        $q->whereNull('created_by')
          ->orWhere(function ($seller) {
              $seller->where('created_by', 'seller')
                     ->where('status', 'approved');
          });
    })->with(['brand', 'category', 'seller']);

    // ================= CATEGORY & SUBCATEGORY FILTER =================
    $selectedCategory = $request->category ?? null;
    $selectedSubcategory = $request->subcategory ?? null;
    $selectedCategoryModel = null;
    $subcategories = collect();

    if ($selectedSubcategory) {
        $subcatObj = DB::table('marketplace_sub_categories')
            ->where('id', $selectedSubcategory)
            ->first();
        if ($subcatObj) {
            // Find the parent category
            $parentCat = DB::table('marketplace_categories')
                ->where('id', $subcatObj->category_id)
                ->first();
            if ($parentCat) {
                // Find ALL categories with the same name (across all sellers)
                $allParentCatIds = DB::table('marketplace_categories')
                    ->where('status', 1)
                    ->whereRaw("LOWER(name) = ?", [strtolower($parentCat->name)])
                    ->pluck('id')
                    ->toArray();

                // Find ALL subcategories with the same name under all matching parent categories
                $allMatchingSubcatIds = DB::table('marketplace_sub_categories')
                    ->whereIn('category_id', $allParentCatIds)
                    ->where('status', 1)
                    ->whereRaw("LOWER(name) = ?", [strtolower($subcatObj->name)])
                    ->pluck('id')
                    ->toArray();

                // Get ALL subcategories for sidebar (de-duplicated by name)
                $subcategories = DB::table('marketplace_sub_categories')
                    ->whereIn('category_id', $allParentCatIds)
                    ->where('status', 1)
                    ->get()
                    ->unique('name')
                    ->values();

                $selectedCategoryModel = $parentCat;
                $selectedCategory = $parentCat->slug ?? strtolower(str_replace(' ', '-', $parentCat->name));
            }
        }
        // Filter products by ALL matching subcategory IDs (cross-seller)
        $query->whereIn('subcategory_id', $allMatchingSubcatIds ?? [$selectedSubcategory]);
    } elseif ($selectedCategory) {
        // Match all categories by slug or name (case-insensitive) to handle duplicate category names across sellers/admin
        $selectedCategoryModels = DB::table('marketplace_categories')
            ->where('status', 1)
            ->where(function($q) use ($selectedCategory) {
                $q->where('slug', $selectedCategory)
                  ->orWhereRaw("LOWER(REPLACE(name,' ','-')) = ?", [strtolower($selectedCategory)])
                  ->orWhereRaw("LOWER(name) = ?", [strtolower(str_replace('-', ' ', $selectedCategory))]);
            })
            ->get();

        if ($selectedCategoryModels->isNotEmpty()) {
            $catIds = $selectedCategoryModels->pluck('id')->toArray();
            $query->whereIn('category_id', $catIds);

            // Fetch subcategories for all matching category IDs (de-duplicated by name)
            $subcategories = DB::table('marketplace_sub_categories')
                ->whereIn('category_id', $catIds)
                ->where('status', 1)
                ->get()
                ->unique('name')
                ->values();

            // Set the selectedCategoryModel (prefer the one with seller_id for display if available)
            $selectedCategoryModel = $selectedCategoryModels->sortBy(function($item) {
                return $item->seller_id ? 0 : 1;
            })->first();
        }
    }

    // ================= SEARCH =================
    $searchTerm = $request->q ?? $request->search ?? null;
    if ($searchTerm) {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('description', 'LIKE', "%{$searchTerm}%")
              ->orWhere('short_description', 'LIKE', "%{$searchTerm}%")
              ->orWhere('sku', 'LIKE', "%{$searchTerm}%")
              ->orWhereHas('brand', fn($b)=>$b->where('name','LIKE',"%{$searchTerm}%"))
              ->orWhereHas('category', fn($c)=>$c->where('name','LIKE',"%{$searchTerm}%"))
              ->orWhereHas('seller', fn($s)=>$s->where('name','LIKE',"%{$searchTerm}%"));
        });
    }

    // ================= RATING =================
    if ($request->filled('rating')) {
        $rating = (int) $request->rating;
        $query->whereExists(function ($q) use ($rating) {
            $q->select(DB::raw(1))
              ->from('product_reviews')
              ->whereColumn('product_reviews.product_id', 'products.id')
              ->where('product_reviews.rating', $rating)
              ->where('is_approved', 1);
        });
    }

    // ================= PRICE =================
    if ($request->filled('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }
    if ($request->filled('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    // ================= SORT / FILTER (Trending, Bestseller, Discount, New) =================
    if ($request->filled('filter')) {
        $filter = $request->filter;
        if ($filter == 'trending' || $filter == 'bestseller') {
            $trendingProductIds = \DB::table('order_items')
                ->select('product_id', \DB::raw('SUM(quantity) as total_sold'))
                ->groupBy('product_id')
                ->orderBy('total_sold', 'desc')
                ->take(100)
                ->pluck('product_id')
                ->toArray();
            
            if (!empty($trendingProductIds)) {
                $query->whereIn('id', $trendingProductIds);
            } else {
                $query->orderBy('views', 'desc');
            }
        } elseif ($filter == 'discount') {
            $query->whereNotNull('regular_price')
                  ->whereColumn('regular_price', '>', 'price')
                  ->orderByRaw('(regular_price - price) DESC');
        } elseif ($filter == 'new') {
            $query->latest();
        }
    } elseif ($request->filled('sort')) {
        switch ($request->sort) {
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'newest': $query->latest(); break;
            case 'popular': $query->orderBy('views', 'desc'); break;
            default: $query->latest(); break;
        }
    } else {
        $query->latest();
    }

    // ================= ATTRIBUTES FILTER =================
    if ($request->filled('attributes') && is_array($request->attributes)) {
        foreach ($request->attributes as $attrId => $termId) {
            if ($termId) {
                $query->whereHas('attributes', function ($q) use ($attrId, $termId) {
                    $q->where('attribute_id', $attrId)
                      ->where('attribute_term_id', $termId);
                });
            }
        }
    }

    // ================= PAGINATION =================
    $products = $query->paginate(6)->withQueryString();

    // ================= WISHLIST IDS =================
    $wishlistIds = [];
    if (Auth::guard('customer')->check()) {
        $wishlistIds = \App\Models\Wishlist::where('customer_id', Auth::guard('customer')->id())
            ->pluck('product_id')
            ->toArray();
    }

    // ================= DYNAMIC MAX PRICE =================
    $maxDbPrice = DB::table('products')
        ->whereNull('created_by')
        ->orWhere('status', 'approved')
        ->max('price') ?? 50000;
    $maxSliderPrice = ceil($maxDbPrice / 1000) * 1000;
    if ($maxSliderPrice < 1000) $maxSliderPrice = 1000;

    // ================= FETCH ADVERTISEMENTS (BANNERS) =================
    $currentDateTime = now();
    $adsQuery = \App\Models\Advertisement::with('media')
        ->where('is_active', 1)
        ->whereIn('target_type', ['product', 'both'])
        ->where(function($q) use ($currentDateTime) {
            $q->whereNull('start_date')
              ->orWhere('start_date', '<=', $currentDateTime);
        })
        ->where(function($q) use ($currentDateTime) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', $currentDateTime);
        });

    if ($selectedCategoryModel) {
        $adsQuery->where(function($q) use ($selectedCategoryModel) {
            $q->whereNull('category_name')
              ->orWhere('category_name', strtolower($selectedCategoryModel->name))
              ->orWhere('category_name', ucfirst($selectedCategoryModel->name))
              ->orWhere('category_name', $selectedCategoryModel->name)
              ->orWhere('category_name', $selectedCategoryModel->slug);
        });
    }

    $banners = $adsQuery->get();

    // ================= OTHER DATA =================
    $sizes  = AttributeTerm::where('attribute_id', 10)->get();
    $colors = AttributeTerm::where('attribute_id', 11)->get();
    $brands = Brand::where('type', 'brand')->get();

    // ✅ Categories: only show seller-created ones (where seller_id IS NOT NULL)
    $categories = DB::table('marketplace_categories')
        ->where('status', 1)
        ->where('type', 'product')
        ->whereNotNull('seller_id')
        ->get();

    $attributes = DB::table('attributes')
        ->where('status', 'approved')
        ->get();

    $attributeTerms = DB::table('attribute_terms')
        ->get()
        ->groupBy('attribute_id');

    $ratings = DB::table('product_reviews')
        ->select(DB::raw('ROUND(rating) as rating'), DB::raw('COUNT(*) as total'))
        ->where('is_approved', 1)
        ->groupBy(DB::raw('ROUND(rating)'))
        ->orderByDesc('rating')
        ->pluck('total','rating');

    return view('vshop.products.index', compact(
        'products',
        'brands',
        'sizes',
        'colors',
        'categories',
        'attributes',
        'attributeTerms',
        'ratings',
        'wishlistIds',
        'subcategories',
        'selectedCategoryModel',
        'maxSliderPrice',
        'banners'
    ));
}
public function create()
{
    $categories = Category::where('status',1)->get();
    return view('vshop.products.create',compact('categories'));
}


/* ======================================================
   STORE
====================================================== */

public function store(Request $request)
{
    $request->validate([
        'name'=>'required|max:255',
        'sku'=>'required|unique:shop_products,sku',
        'category_id'=>'required|exists:categories,id',
        'price'=>'required|numeric',
        'quantity'=>'required|integer'
    ]);

    $data = $request->except(['thumbnail','images']);

    if($request->hasFile('thumbnail')){
        $data['thumbnail']=$request->file('thumbnail')
            ->store('products/thumbnails','public');
    }

    if($request->hasFile('images')){
        $images=[];
        foreach($request->file('images') as $img){
            $images[]=$img->store('products/images','public');
        }
        $data['images']=json_encode($images);
    }

    $data['type']='product';
    $data['user_id']=auth()->guard('customer')->id();

  $product = Product::create($data);

// 🔔 NOTIFICATION
$this->notify(
    'product',
    'New Product Created',
    'seller.products.index'
);
    return redirect()->route('vshop.productmanage.index')
        ->with('success','Product created successfully.');
}


/* ======================================================
   SHOW PRODUCT  ⭐ SELLER CATEGORY LOGIC ADDED
====================================================== */

public function show($id)
{

  
    $product = Product::with([
        'category',
        'seller',
        'brand'
    ])
    ->where('id',$id)
    ->where(function ($q){

        // ADMIN PRODUCT
        $q->whereNull('created_by')

        // SELLER APPROVED PRODUCT
        ->orWhere(function ($seller) {
            $seller->where('created_by','seller')
                   ->where('status','approved');
        });

    })
    ->firstOrFail();



    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    $store = $product->created_by === 'seller'
        ? \App\Models\Seller::find($product->created_by_id)
        : null;



    /*
    |--------------------------------------------------------------------------
    | GALLERY IMAGES
    |--------------------------------------------------------------------------
    */

    $galleryImages = is_array($product->images)
        ? $product->images
        : (json_decode($product->images ?? '', true) ?: []);



    /*
    |--------------------------------------------------------------------------
    | JSON FIELDS
    |--------------------------------------------------------------------------
    */

    $product->features       = json_decode($product->features ?? '[]', true);
    $product->specifications = json_decode($product->specifications ?? '[]', true);
    $product->faqs           = json_decode($product->faqs ?? '[]', true);



    /*
    |--------------------------------------------------------------------------
    | REVIEWS
    |--------------------------------------------------------------------------
    */

    $reviews = ProductReview::where('product_id',$product->id)
        ->latest()
        ->paginate(5);

    $averageRating = ProductReview::where('product_id',$product->id)->avg('rating') ?? 0;

    $totalReviews = ProductReview::where('product_id',$product->id)->count();



    /*
    |--------------------------------------------------------------------------
    | RELATED PRODUCTS
    |--------------------------------------------------------------------------
    */

   $relatedProducts = Product::whereIn('type', ['product', 'vshop-product'])
    ->where('status','approved')
    ->where('id','!=',$product->id)
    ->where('category_id',$product->category_id)
    ->latest()
    ->take(6)
    ->get();

if ($relatedProducts->count() < 6) {

    $remaining = 6 - $relatedProducts->count();

    $moreProducts = Product::whereIn('type', ['product', 'vshop-product'])
        ->where('status','approved')
        ->where('id','!=',$product->id)
        ->whereNotIn('id', $relatedProducts->pluck('id')->toArray()) // ✅ IMPORTANT FIX
        ->latest()
        ->take($remaining)
        ->get();

    // ✅ FIX: remove duplicates after merge
    $relatedProducts = $relatedProducts
        ->merge($moreProducts)
        ->unique('id')   // ⭐ KEY FIX
        ->values();
}


    /*
    |--------------------------------------------------------------------------
    | STORE INFO
    |--------------------------------------------------------------------------
    */

    $storeName = $product->seller->name ?? null;
    $storeSlug = $product->seller->slug ?? null;



    /*
    |--------------------------------------------------------------------------
    | PRODUCT ATTRIBUTE COMBINATIONS
    |--------------------------------------------------------------------------
    */

    $productAttributes = \App\Models\ProductAttribute::with(['attribute','term'])
        ->where('proid',$product->id)
        ->where('status','Y')
        ->get()
        ->groupBy('combination_key');


    $attributeOptions = [];
    $combinations = [];


    foreach ($productAttributes as $combinationKey => $rows) {

        $price = 0;
        $regularPrice = 0;
        $stock = 0;
        $discount = 0;

        $attributes = [];

        foreach ($rows as $row) {

            $attrName = strtolower($row->attribute->name ?? '');
            $termName = $row->term->term ?? '';

            $attributes[$attrName] = $termName;

            if(!isset($attributeOptions[$attrName])){
                $attributeOptions[$attrName] = collect();
            }

            $attributeOptions[$attrName]->push([
                'id' => $row->id,
                'term_id' => $row->attribute_term_id,
                'term_name' => $termName,
                'price' => $row->price,
                'regular_price' => $row->regular_price,
                'stock' => $row->product_stock,
                'combination_key' => $combinationKey,
                'color_code' => $row->term->color_code ?? null
            ]);

            $price = $row->price;
            $regularPrice = $row->regular_price;
            $stock = $row->product_stock;
            $discount = $row->discount_value ?? 0;
        }


        $combinations[] = [
            'combination_key' => $combinationKey,
            'price' => $price,
            'regular_price' => $regularPrice,
            'stock' => $stock,
            'discount' => $discount,
            'attributes' => $attributes
        ];
    }



    /*
    |--------------------------------------------------------------------------
    | REMOVE DUPLICATE ATTRIBUTE OPTIONS
    |--------------------------------------------------------------------------
    */

    foreach ($attributeOptions as $key => $values) {
        $attributeOptions[$key] = $values->unique('term_id')->values();
    }



    /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */
      $productReviews = ProductReview::where('product_id',$product->id)

->latest()
->get();

    return view('vshop.products.show', compact(
        'product',
        'store',
        'storeName',
        'storeSlug',
        'galleryImages',
        'relatedProducts',
        'reviews',
        'averageRating',
        'totalReviews',
        'attributeOptions',
        'combinations','productReviews'
    ));
}

/* ======================================================
   UPDATE
====================================================== */

public function update(Request $request,$id)
{
    $product=Product::findOrFail($id);

    $data=$request->except(['thumbnail','images']);

    if($request->hasFile('thumbnail')){
        Storage::disk('public')->delete($product->thumbnail);
        $data['thumbnail']=$request->file('thumbnail')
            ->store('products/thumbnails','public');
    }

    if($request->hasFile('images')){
        if($product->images){
            foreach(json_decode($product->images,true) as $img){
                Storage::disk('public')->delete($img);
            }
        }

        $images=[];
        foreach($request->file('images') as $img){
            $images[]=$img->store('products/images','public');
        }

        $data['images']=json_encode($images);
    }

    $product->update($data);
    
    // 🔔 NOTIFICATION
$this->notify(
    'product',
    'Product Updated Successfully',
    'seller.products.index'
);

    return redirect()->route('vshop.productmanage.index')
        ->with('success','Product updated successfully.');
}


/* ======================================================
   DELETE
====================================================== */

public function destroy($id)
{
    $product=Product::findOrFail($id);

    Storage::disk('public')->delete($product->thumbnail);

    if($product->images){
        foreach(json_decode($product->images,true) as $img){
            Storage::disk('public')->delete($img);
        }
    }

    $product->delete();
    $this->notify(
    'product',
    'Product Deleted Successfully',
    'seller.products.index'
);

    return back()->with('success','Deleted successfully');
}

}