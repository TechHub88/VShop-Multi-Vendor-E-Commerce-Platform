<?php

namespace App\Http\Controllers\vtravel;
use App\Models\Product;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Storage;
class TravelProductController extends Controller
{
 
 
 public function index(Request $request)
{

$query = Product::whereIn('type',['travel','service'])

    // ✅ PLATFORM FILTER (ADD THIS)
    ->where(function($platform){
        $platform->where('platform','vtravel')
                 ->orWhere('platform','both'); // 🔥 BOTH INCLUDED
    })

    ->where(function ($q){

        $q->whereNull('created_by')

        ->orWhere(function ($seller){
            $seller->where('created_by','seller')
                   ->where('status','approved');
        });

    })

    ->with([
        'brand',
        'category',
        'seller'
    ]);

/*
|--------------------------------------------------------------------------
SEARCH FILTER
|--------------------------------------------------------------------------
*/

if ($request->filled('search')) {

$searchTerm = $request->search;

$query->where(function ($q) use ($searchTerm) {

$q->where('name','LIKE',"%{$searchTerm}%")
->orWhere('description','LIKE',"%{$searchTerm}%")
->orWhere('short_description','LIKE',"%{$searchTerm}%")
->orWhereHas('category',function($c) use ($searchTerm){
$c->where('name','LIKE',"%{$searchTerm}%");
});

});

}

/*
|--------------------------------------------------------------------------
RATING FILTER
|--------------------------------------------------------------------------
*/

if ($request->filled('rating')) {

$query->whereHas('reviews',function ($q) use ($request){

$q->where('rating','>=',$request->rating);

});

}

/*
|--------------------------------------------------------------------------
DAY FILTER (JSON)
|--------------------------------------------------------------------------
*/

if ($request->filled('days')) {

$query->whereJsonContains('service_days->count',(string)$request->days);

}

/*
|--------------------------------------------------------------------------
NIGHT FILTER (JSON)
|--------------------------------------------------------------------------
*/

if ($request->filled('nights')) {

$query->whereJsonContains('service_days->nights',(string)$request->nights);

}

/*
|--------------------------------------------------------------------------
PRICE FILTER
|--------------------------------------------------------------------------
*/

if ($request->filled('min_price')) {

$query->where('price','>=',$request->min_price);

}

if ($request->filled('max_price')) {

$query->where('price','<=',$request->max_price);

}

/*
|--------------------------------------------------------------------------
SORT
|--------------------------------------------------------------------------
*/

if ($request->filled('sort')) {

switch ($request->sort) {

case 'price_asc':

$query->orderBy('price','asc');
break;

case 'price_desc':

$query->orderBy('price','desc');
break;

case 'newest':

$query->latest();
break;

case 'popular':

$query->orderBy('views','desc');
break;

default:

$query->latest();
break;

}

}
else{

$query->latest();

}


$products = $query->paginate(8)->withQueryString();

/*
|--------------------------------------------------------------------------
RATINGS DATA
|--------------------------------------------------------------------------
*/

$ratings = DB::table('product_reviews')
->select(
DB::raw('ROUND(rating) as rating'),
DB::raw('COUNT(*) as total')
)
->where('is_approved',1)
->groupBy(DB::raw('ROUND(rating)'))
->orderByDesc('rating')
->pluck('total','rating');

/*
|--------------------------------------------------------------------------
RETURN VIEW
|--------------------------------------------------------------------------
*/

return view('vtravel.products.index',compact(
'products',
'ratings'
));

}
    public function create()
    {
        return view('vtravel.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */


public function show($id)
{
    /*
    |--------------------------------------------------------------------------
    | PRODUCT FETCH (ONLY TRAVEL / SERVICE)
    |--------------------------------------------------------------------------
    */

    $product = Product::with([
        'category',
        'seller',
        'brand'
    ])
    ->where('id', $id)
    ->whereIn('type', ['travel','service'])
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
    | STORE (SELLER INFO)
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
    | JSON FIELDS (FOR TRAVEL DETAILS)
    |--------------------------------------------------------------------------
    */

    $product->features       = json_decode($product->features ?? '[]', true);
    $product->highlights     = json_decode($product->highlights ?? '[]', true);
    $product->itinerary      = json_decode($product->itinerary ?? '[]', true);
    $product->faqs           = json_decode($product->faqs ?? '[]', true);


    /*
    |--------------------------------------------------------------------------
    | REVIEWS
    |--------------------------------------------------------------------------
    */



    $averageRating = ProductReview::where('product_id',$product->id)->avg('rating') ?? 0;

    $totalReviews = ProductReview::where('product_id',$product->id)->count();


    /*
    |--------------------------------------------------------------------------
    | RELATED PRODUCTS (TRAVEL / SERVICE ONLY)
    |--------------------------------------------------------------------------
    */

    $relatedProducts = Product::whereIn('type',['travel','service'])
        ->where('status','approved')
        ->where('id','!=',$product->id)
        ->where('category_id',$product->category_id)
        ->latest()
        ->take(6)
        ->get();

    if ($relatedProducts->count() < 6) {

        $remaining = 6 - $relatedProducts->count();

        $moreProducts = Product::whereIn('type',['travel','service'])
            ->where('status','approved')
            ->where('id','!=',$product->id)
            ->whereNotIn('id',$relatedProducts->pluck('id'))
            ->latest()
            ->take($remaining)
            ->get();

        $relatedProducts = $relatedProducts->merge($moreProducts);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE INFO
    |--------------------------------------------------------------------------
    */

    $storeName = $product->seller->name ?? null;
    $storeSlug = $product->seller->slug ?? null;

     
    $productReviews = ProductReview::where('product_id',$product->id)
->where('is_approved',1)
->latest()
->get();
    /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */

    return view('vtravel.products.show', compact(
        'product',
        'store',
        'storeName',
        'storeSlug',
        'galleryImages',
        'relatedProducts',
        
        'averageRating',
        'totalReviews','productReviews'
    ));
}
    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        return view('vtravel.products.edit', compact('id'));
    }


    

    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
