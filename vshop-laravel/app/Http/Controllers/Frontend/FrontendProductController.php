<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ComboProduct;
use App\Models\ComboProductItem;
use App\Models\About;
use App\Models\Footer;
use App\Models\Attribute;
use App\Models\ProductAttribute;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\SizeGuide;


use Illuminate\Support\Facades\Auth; 
class FrontendProductController extends Controller
{

public function listing(Request $request, $slug = null)
{
    $customer = Auth::guard('customer')->user();
    $userLinkedId = $customerId = $customerBrandId = null;
    $institution = null;

    if ($customer) {
        $userLinkedId = $customer->user_id;
        $customerId = $customer->id;

        $institution = DB::table('branches')
            ->where('user_id', $userLinkedId)
            ->first();

        if ($customer->status === 'approved') {
            $customerBrandId = $customer->brand_id;
        }
    }

    // ===========================
    // MAIN PRODUCTS (BRAND PRODUCTS)
    // ===========================
    $query = Product::with(['attributes.term', 'brand'])->select('products.*');


    if ($customer && $customer->status === 'approved' && !empty($customerBrandId)) {
        $query->where('brand_id', $customerBrandId);
    }

    if ($slug) {
        $category = Category::where('slug', $slug)->first();
        if ($category) $query->where('category_id', $category->id);
    }

    $search = $request->get('search', '');
    if ($search !== '') $query->where('name', 'like', '%' . $search . '%');

    if ($request->has('category')) $query->whereIn('category_id', $request->category);

    if ($request->has('filter')) {
        foreach ($request->filter as $attrId => $termIds) {
            $query->whereHas('attributes', fn($q) => $q->whereIn('attribute_term_id', $termIds));
        }
    }

    $minPrice = $request->get('min_price');
    $maxPrice = $request->get('max_price');
    if ($minPrice !== null && $maxPrice !== null) {
        $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    $sort = $request->get('sort', 'popularity');
    switch ($sort) {
        case 'price_low_high': $query->orderBy('price', 'asc'); break;
        case 'price_high_low': $query->orderBy('price', 'desc'); break;
        case 'popularity':
        default: $query->orderBy('popularity', 'desc'); break;
    }

    $products = $query->paginate(5)->withQueryString();

    // Combo check
    $productIds = $products->pluck('id')->toArray();
    if (!empty($productIds)) {
        $mainComboProducts = DB::table('combo_products')
            ->whereIn('main_product_id', $productIds)
            ->where('status', 'active')
            ->pluck('main_product_id')
            ->toArray();
        foreach ($products as $product) {
            $product->is_combo_product = in_array($product->id, $mainComboProducts);
        }
    }

 $otherQuery = Product::with(['attributes.term', 'brand'])->select('products.*');

if ($customer && $customer->status === 'approved' && $customerBrandId) {
    $otherQuery->where(function($q) use ($customerBrandId) {
        $q->where('brand_id', '!=', $customerBrandId)
          ->orWhereNull('brand_id');
    });
} else {
    $otherQuery->whereNull('brand_id'); // For guests, show only non-branded
}

if ($slug && isset($category)) $otherQuery->where('category_id', $category->id);
if ($search !== '') $otherQuery->where('name', 'like', '%' . $search . '%');
if ($request->has('category')) $otherQuery->whereIn('category_id', $request->category);

if ($request->has('filter')) {
    foreach ($request->filter as $attrId => $termIds) {
        $otherQuery->whereHas('attributes', fn($q) => $q->whereIn('attribute_term_id', $termIds));
    }
}

if ($minPrice !== null && $maxPrice !== null) {
    $otherQuery->whereBetween('price', [$minPrice, $maxPrice]);
}

switch ($sort) {
    case 'price_low_high': $otherQuery->orderBy('price', 'asc'); break;
    case 'price_high_low': $otherQuery->orderBy('price', 'desc'); break;
    case 'popularity':
    default: $otherQuery->orderBy('popularity', 'desc'); break;
}

$otherProducts = $otherQuery->distinct()->get();


    // Combo check for other products
    if ($otherProducts->count() > 0) {
        $otherProductIds = $otherProducts->pluck('id')->toArray();
        $otherMainComboProducts = DB::table('combo_products')
            ->whereIn('main_product_id', $otherProductIds)
            ->where('status', 'active')
            ->pluck('main_product_id')
            ->toArray();
        foreach ($otherProducts as $product) {
            $product->is_combo_product = in_array($product->id, $otherMainComboProducts);
        }
    }

    // ===========================
    // COMMON DATA
    // ===========================
    $about = About::first();
    $categories = Category::all();
    $attributes = Attribute::with('terms')->get();
    $footer = Footer::first();

    return view('frontend.products', compact(
        'products', 'otherProducts', 'about', 'categories', 'attributes',
        'footer', 'institution', 'customerId', 'search', 'minPrice', 'maxPrice', 'slug', 'sort'
    ));
}


public function productdetails($sku)
{
    $product = Product::where('sku', $sku)->firstOrFail();

    // Existing logic...
    $productAttributes = ProductAttribute::where('proid', $product->id)
        ->with(['attribute', 'term'])
        ->orderBy('id')
        ->get();

    $attributeOptions = $this->formatAttributeOptions($productAttributes);
    $variationMatrix = $this->buildVariationMatrix($product->id, $productAttributes);
    $defaultSelections = $this->buildDefaultSelections($attributeOptions);
    $attributeSlugMap = $this->buildAttributeSlugMap($attributeOptions);
    $defaultColorTerm = $this->getDefaultTermBySlug($attributeOptions, ['colour', 'color']);
    $defaultSizeTerm = $this->getDefaultTermBySlug($attributeOptions, ['size']);

    $images = is_array($product->images)
        ? $product->images
        : (json_decode($product->images, true) ?? explode(',', $product->images));

    $about = About::first();
    $footer = Footer::first();
    $categories = Category::orderBy('name')->get();

    $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->inRandomOrder()
        ->distinct()
        ->take(5)
        ->get();

    // Load combos from combo_product_items table where product_id = product ID
    // Group by creation timestamp to get distinct combos
    $allComboItems = ComboProductItem::with('comboProduct')
        ->where('product_id', $product->id)
        ->orderBy('created_at', 'desc')
        ->get();
    
    // Group items by creation time (items created within same second are one combo)
    $comboProducts = $allComboItems->groupBy(function($item) {
        return $item->created_at->format('Y-m-d H:i:s');
    })->map(function($items, $timestamp) use ($product) {
        // Get combo products from items (comboProduct relationship gets the combo product)
        $comboProductsList = $items->pluck('comboProduct')->filter();
        
        // Calculate total price: main product + all combo products
        $totalPrice = $product->price + $comboProductsList->sum('price');
        
        // Apply 10% discount for combo
        $comboPrice = $totalPrice * 0.9;
        
        // Generate combo name
        $comboName = $product->name . ' Combo Pack';
        if ($comboProductsList->count() > 0) {
            $comboName = $product->name . ' + ' . $comboProductsList->take(2)->pluck('name')->implode(' + ');
            if ($comboProductsList->count() > 2) {
                $comboName .= ' + ' . ($comboProductsList->count() - 2) . ' more';
            }
        }
        
        // Get first combo product for thumbnail
        $firstComboProduct = $comboProductsList->first();
        $comboThumbnail = $firstComboProduct && $firstComboProduct->thumbnail 
            ? asset($firstComboProduct->thumbnail) 
            : ($product->thumbnail ? asset($product->thumbnail) : 'https://via.placeholder.com/80x80');
        
        return (object)[
            'id' => $product->id . '_' . str_replace([' ', ':'], ['_', ''], $timestamp), // Unique combo ID
            'name' => $comboName,
            'price' => round($comboPrice, 2),
            'regular_price' => round($totalPrice, 2),
            'thumbnail' => $comboThumbnail,
            'status' => 'active',
            'items' => $items,
            'mainProduct' => $product,
            'created_at' => $items->first()->created_at ?? now()
        ];
    })->filter(function($combo) {
        // Only include combos that have at least one combo product
        return $combo->items->count() > 0;
    })->values()->take(10);

    $reviews = $product->reviews ?? collect();

    $categoryId = $product->category_id;

    $sizeGuides = SizeGuide::where('category_id', $categoryId)
        ->orderByRaw("FIELD(size_label, 'S','M','L','XL','2XL','3XL')")
        ->get();

    // ---------------- Review Permission Logic ----------------
    $canReview = false;
    if (Auth::guard('customer')->check()) {
        $customer = Auth::guard('customer')->user();

        // Check if the customer has a successful order containing this product
        $hasOrder = \App\Models\Order::where('customer_id', $customer->id)
            ->where('status', 'success')
            ->whereHas('items', function($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->exists();

        $canReview = $hasOrder;
    }
    // --------------------------------------------------------

    return view('frontend.productdetails', compact(
        'product',
        'images',
        'about',
        'categories',
        'footer',
        'relatedProducts',
        'comboProducts',
        'reviews',
        'attributeOptions',
        'variationMatrix',
        'defaultSelections',
        'attributeSlugMap',
        'defaultColorTerm',
        'defaultSizeTerm',
        'sizeGuides',
        'canReview'   // Pass review permission to Blade
    ));
}


public function showFilters() {
    // Get all product attributes, grouped by type
    $attributes = ProductAttribute::all()->groupBy('type');

    return view('frontend.products', [
        'companies' => $attributes['company'] ?? [],
        'genders' => $attributes['gender'] ?? [],
        'sizes' => $attributes['size'] ?? [],
        'categories' => $attributes['category'] ?? [],
        'colors' => $attributes['color'] ?? []
    ]);
}


// app/Http/Controllers/Frontend/ProductController.php

public function ajaxUpdateQuantity(Request $request)
{
    $productId = $request->input('product_id');
    $action = $request->input('action');
    $currentQty = (int) $request->input('current_qty', 1);

    // Minimum order quantity logic (optional)
    $product = Product::find($productId);
    $minQty = $product->moq ?? 1;

    if($action === 'increment') {
        $newQty = $currentQty + 1;
    } elseif($action === 'decrement') {
        $newQty = max($minQty, $currentQty - 1);
    } else {
        return response()->json(['status' => 'error', 'message' => 'Invalid action']);
    }

    // Optionally calculate subtotal or price if needed
    // For simplicity, returning quantity
    return response()->json([
        'status' => 'success',
        'quantity' => $newQty,
        // 'subtotal' => $product->price * $newQty, // optional
    ]);
}

// Get product attributes and images for combo products
public function getProductAttributes(Request $request)
{
    $productId = $request->input('product_id');
    
    if (!$productId) {
        return response()->json([
            'status' => 'error',
            'message' => 'Product ID is required'
        ], 400);
    }

    try {
        $product = Product::findOrFail($productId);
        
        // Get product attributes
        $productAttributes = ProductAttribute::where('proid', $product->id)
            ->with(['attribute', 'term'])
            ->orderBy('id')
            ->get();

        $attributeOptions = $this->formatAttributeOptions($productAttributes);
        $defaultSelections = $this->buildDefaultSelections($attributeOptions);

        // Get product images
        $images = [];
        if ($product->images) {
            if (is_array($product->images)) {
                $images = $product->images;
            } else {
                $decoded = json_decode($product->images, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $images = $decoded;
                } else {
                    $images = explode(',', $product->images);
                }
            }
        }
        
        // Format image URLs
        $images = array_map(function($img) {
            return asset(trim($img));
        }, array_filter($images));

        // Get thumbnail
        $thumbnail = $product->thumbnail ? asset($product->thumbnail) : null;

        return response()->json([
            'status' => 'success',
            'attributeOptions' => $attributeOptions,
            'defaultSelections' => $defaultSelections,
            'images' => $images,
            'thumbnail' => $thumbnail,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('Get product attributes error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch product attributes'
        ], 500);
    }
}

public function fetchVariation(Request $request)
{
    $validated = $request->validate([
        'product_id' => ['required', 'integer'],
        'term_ids' => ['required', 'array'],
        'term_ids.*' => ['integer'],
        'attributes' => ['nullable', 'array'],
        'attributes.*' => ['integer'],
        'combination_key' => ['nullable', 'string'], // Optional combination_key
    ]);

    $termIds = collect($validated['term_ids'])
        ->filter()
        ->map(fn ($id) => (int) $id)
        ->sort()
        ->values();

    if ($termIds->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No attribute terms provided.',
        ], 422);
    }

    $variationMatrix = $this->buildVariationMatrix((int) $validated['product_id']);
    
    // Build requested attributes map
    $requestedAttributes = collect($validated['attributes'] ?? [])
        ->mapWithKeys(function ($termId, $attributeId) {
            $attributeId = (int) $attributeId;
            $termId = (int) $termId;

            if ($attributeId <= 0 || $termId <= 0) {
                return [];
            }

            return [$attributeId => $termId];
        });

    // First, try to find by combination_key if provided
    if (!empty($validated['combination_key']) && array_key_exists($validated['combination_key'], $variationMatrix)) {
        return response()->json([
            'status' => 'success',
            'success' => true,
            'variation' => $variationMatrix[$validated['combination_key']],
            'data' => $variationMatrix[$validated['combination_key']],
        ]);
    }

    // Try to find by matching attribute_term_ids
    $matchedVariation = null;
    $matchedKey = null;

    foreach ($variationMatrix as $key => $variation) {
        $variationTermIds = collect($variation['attribute_term_ids'] ?? [])
            ->sort()
            ->values()
            ->toArray();
        
        $requestedTermIds = $termIds->toArray();

        // Check if term_ids match exactly
        if ($variationTermIds === $requestedTermIds) {
            $matchedVariation = $variation;
            $matchedKey = $key;
            break;
        }

        // Also check by attribute_map if attributes are provided
        if ($requestedAttributes->isNotEmpty()) {
            $variationAttributes = collect($variation['attribute_map'] ?? [])
                ->mapWithKeys(function ($termId, $attributeId) {
                    $attributeId = (int) $attributeId;
                    $termId = (int) $termId;

                    if ($attributeId <= 0 || $termId <= 0) {
                        return [];
                    }

                    return [$attributeId => $termId];
                });

            if ($variationAttributes->isNotEmpty()) {
                $match = true;
                foreach ($requestedAttributes as $attributeId => $termId) {
                    if ((int) ($variationAttributes[$attributeId] ?? null) !== $termId) {
                        $match = false;
                        break;
                    }
                }

                if ($match) {
                    $matchedVariation = $variation;
                    $matchedKey = $key;
                    break;
                }
            }
        }
    }

    if ($matchedVariation) {
        return response()->json([
            'status' => 'success',
            'success' => true,
            'variation' => $matchedVariation,
            'data' => $matchedVariation,
        ]);
    }

    // Fallback: try legacy key format (term_ids joined)
    $legacyKey = $termIds->join('_');
    if (array_key_exists($legacyKey, $variationMatrix)) {
        return response()->json([
            'status' => 'success',
            'success' => true,
            'variation' => $variationMatrix[$legacyKey],
            'data' => $variationMatrix[$legacyKey],
        ]);
    }

    return response()->json([
        'status' => 'not_found',
        'success' => false,
        'message' => 'Combination not available.',
    ], 404);
}

protected function formatAttributeOptions($productAttributes)
{
    if ($productAttributes->isEmpty()) {
        return [];
    }

    return $productAttributes
        ->groupBy('attribute_id')
        ->map(function ($items) {
            $attribute = $items->first()->attribute;

            if (!$attribute) {
                return null;
            }

            $terms = $items->map(function ($item) {
                $term = $item->term;

                return [
                    'term_id' => $item->attribute_term_id,
                    'term_name' => $term->term ?? '',
                    'color_code' => $term->color_code ?? null,
                    'price' => $item->price,
                    'regular_price' => $item->regular_price,
                    'stock' => $item->product_stock,
                    'product_attribute_id' => $item->id,
                    'no_pack' => $item->no_pack,
                    'product_unit' => $item->product_unit,
                ];
            })
            ->unique('term_id')
            ->values()
            ->toArray();

            if (empty($terms)) {
                return null;
            }

            return [
                'attribute_id' => $attribute->id,
                'attribute_name' => $attribute->name,
                'attribute_slug' => Str::slug($attribute->name ?? ''),
                'terms' => $terms,
                'default_term_id' => $terms[0]['term_id'] ?? null,
            ];
        })
        ->filter()
        ->values()
        ->toArray();
}

protected function buildVariationMatrix(int $productId, $productAttributes = null): array
{
    $items = $productAttributes;

    if (!$items) {
        $items = ProductAttribute::where('proid', $productId)
            ->with(['attribute', 'term'])
            ->get();
    }

    if ($items->isEmpty()) {
        return [];
    }

    $variations = [];

    // Group by combination_key first, then fallback to legacy grouping
    $items
        ->groupBy(function ($item) {
            // Use combination_key if available, otherwise fallback to legacy method
            if ($item->combination_key) {
                return $item->combination_key;
            }
            // Legacy grouping for old records without combination_key
            return $item->no_pack ?? $item->product_unit ?? ('attr_' . $item->id);
        })
        ->each(function ($group) use (&$variations) {
            $termIds = $group->pluck('attribute_term_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values();

            if ($termIds->isEmpty()) {
                return;
            }

            $first = $group->first();
            
            // Use combination_key as the key if available, otherwise use term_ids join
            $key = $first->combination_key ?? $termIds->join('_');

            $variations[$key] = [
                'combination_key' => $first->combination_key,
                'product_attribute_ids' => $group->pluck('id')->values(),
                'attribute_term_ids' => $termIds,
                'attribute_map' => $group->mapWithKeys(function ($item) {
                    return [$item->attribute_id => $item->attribute_term_id];
                }),
                'price' => $first->price,
                'regular_price' => $first->regular_price,
                'stock' => $first->product_stock,
                'unit' => $first->product_unit,
                'no_pack' => $first->no_pack,
            ];
        });

    return $variations;
}

protected function buildDefaultSelections(array $attributeOptions): array
{
    $defaults = [];

    foreach ($attributeOptions as $option) {
        $term = $option['terms'][0] ?? null;
        if ($term) {
            $defaults[$option['attribute_id']] = $term;
        }
    }

    return $defaults;
}

protected function buildAttributeSlugMap(array $attributeOptions): array
{
    $map = [];

    foreach ($attributeOptions as $option) {
        if (!empty($option['attribute_slug'])) {
            $map[$option['attribute_slug']] = $option['attribute_id'];
        }
    }

    return $map;
}

protected function getDefaultTermBySlug(array $attributeOptions, array $slugs): ?array
{
    foreach ($attributeOptions as $option) {
        if (in_array($option['attribute_slug'], $slugs, true)) {
            $term = $option['terms'][0] ?? null;
            if ($term) {
                return $term;
            }
        }
    }

    return null;
}

}

