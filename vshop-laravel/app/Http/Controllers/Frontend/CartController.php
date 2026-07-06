<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\AttributeTerm;
use App\Models\ProductAttribute;
use App\Models\CartItem;
use App\Models\CartItemSize;
use App\Models\ComboProductItem;
use App\Models\About;
use App\Models\Category;
use App\Models\Footer;
use App\Models\Coupon;
use App\Models\ProductSamplePrice;
use App\Models\ProductEmbroideryPrice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Get price based on quantity tier for a product
     * 
     * @param int $productId
     * @param int $totalQuantity
     * @return float
     */
    private function getPriceByQuantityTier($productId, $totalQuantity)
    {
        // Get all pricing tiers for this product
        $pricingTiers = ProductSamplePrice::where('product_id', $productId)
            ->get();
        
        // If no tiers found, return product base price
        if ($pricingTiers->isEmpty()) {
            $product = Product::find($productId);
            return $product ? (float)$product->price : 0;
        }
        
        // Sort tiers by quantity (descending) - handle both numeric and text names
        $sortedTiers = $pricingTiers->sortByDesc(function($tier) {
            $name = strtolower(trim($tier->name));
            // If it's "sample" or "1", treat as 1
            if ($name === 'sample' || $name === '1') {
                return 1;
            }
            // Try to parse as integer
            $num = (int)$name;
            return $num > 0 ? $num : 0;
        });
        
        // Find the appropriate tier based on quantity
        foreach ($sortedTiers as $tier) {
            $tierName = strtolower(trim($tier->name));
            $tierQuantity = (int)$tierName;
            
            // If tier name is "sample" or "1", treat it as sample tier (quantity 1)
            if ($tierName === 'sample' || $tierName === '1' || $tierQuantity == 1) {
                // Sample tier applies to quantity 1
                if ($totalQuantity == 1) {
                    return (float)$tier->price;
                }
            } else {
                // For other tiers (5+, 20+, 50+, etc.), check if quantity meets the minimum
                if ($tierQuantity > 0 && $totalQuantity >= $tierQuantity) {
                    return (float)$tier->price;
                }
            }
        }
        
        // If quantity doesn't match any tier, use the lowest tier (sample or first tier)
        // For quantities less than the minimum tier, use the sample/base price
        $sampleTier = $pricingTiers->firstWhere(function($tier) {
            $name = strtolower(trim($tier->name));
            return $name === 'sample' || $name === '1';
        });
        
        if ($sampleTier) {
            return (float)$sampleTier->price;
        }
        
        // If no sample tier, use the lowest numeric tier
        $lowestTier = $pricingTiers->sortBy(function($tier) {
            $name = strtolower(trim($tier->name));
            $num = (int)$name;
            return $num > 0 ? $num : 999999;
        })->first();
        
        return $lowestTier ? (float)$lowestTier->price : 0;
    }

    /**
     * Get embroidery price based on quantity tier for a product
     * 
     * @param int $productId
     * @param int $totalQuantity
     * @return float
     */
    private function getEmbroideryPriceByQuantityTier($productId, $totalQuantity)
    {
        // Get all embroidery pricing tiers for this product
        $pricingTiers = ProductEmbroideryPrice::where('product_id', $productId)
            ->get();
        
        // If no tiers found, return 0 (no embroidery charge)
        if ($pricingTiers->isEmpty()) {
            return 0;
        }
        
        // Sort tiers by quantity (descending) - handle both numeric and text names
        $sortedTiers = $pricingTiers->sortByDesc(function($tier) {
            $name = strtolower(trim($tier->name));
            // If it's "sample" or "1", treat as 1
            if ($name === 'sample' || $name === '1') {
                return 1;
            }
            // Try to parse as integer
            $num = (int)$name;
            return $num > 0 ? $num : 0;
        });
        
        // Find the appropriate tier based on quantity
        foreach ($sortedTiers as $tier) {
            $tierName = strtolower(trim($tier->name));
            $tierQuantity = (int)$tierName;
            
            // If tier name is "sample" or "1", treat it as sample tier (quantity 1)
            if ($tierName === 'sample' || $tierName === '1' || $tierQuantity == 1) {
                // Sample tier applies to quantity 1
                if ($totalQuantity == 1) {
                    return (float)$tier->price;
                }
            } else {
                // For other tiers (6+, 25+, etc.), check if quantity meets the minimum
                if ($tierQuantity > 0 && $totalQuantity >= $tierQuantity) {
                    return (float)$tier->price;
                }
            }
        }
        
        // If quantity doesn't match any tier, use the lowest tier (sample or first tier)
        // For quantities less than the minimum tier, use the sample/base price
        $sampleTier = $pricingTiers->firstWhere(function($tier) {
            $name = strtolower(trim($tier->name));
            return $name === 'sample' || $name === '1';
        });
        
        if ($sampleTier) {
            return (float)$sampleTier->price;
        }
        
        // If no sample tier, use the lowest numeric tier
        $lowestTier = $pricingTiers->sortBy(function($tier) {
            $name = strtolower(trim($tier->name));
            $num = (int)$name;
            return $num > 0 ? $num : 999999;
        })->first();
        
        return $lowestTier ? (float)$lowestTier->price : 0;
    }


public function index()
{
    $session_id = Session::getId();

    $cartItems = CartItem::where(function ($q) use ($session_id) {
        $q->where('session_id', $session_id);

        if (Auth::guard('customer')->check()) {
            $q->orWhere('customer_id', Auth::guard('customer')->id());
        }
    })
    ->with(['product', 'color', 'size', 'sizes.size'])
    ->get();

    // Fetch product attributes for each cart item
    $productAttributesMap = [];
    foreach ($cartItems as $item) {
        if ($item->product_id && !isset($productAttributesMap[$item->product_id])) {
            $productAttributes = ProductAttribute::where('proid', $item->product_id)
                ->with(['attribute', 'term'])
                ->orderBy('id')
                ->get();
            
            // Format attributes similar to product details page
            $attributeOptions = $this->formatAttributeOptions($productAttributes);
            $productAttributesMap[$item->product_id] = $attributeOptions;
        }
    }

    $about = About::first();
    $categories = Category::all();
    $footer = Footer::first();
    $colors = AttributeTerm::where('attribute_id', 1)->get();
    $sizes = AttributeTerm::where('attribute_id', 2)->get();
    $coupons = Coupon::where('status', 'active')
        ->where('expiry_date', '>=', now()->toDateString())
        ->orderBy('coupon_amount', 'desc')
        ->get();

    // --- calculate totals here so view always has them ---
    $grandTotal = 0;
    $totalUniqueItems = 0;
    $totalQuantity = 0;

    foreach ($cartItems as $item) {
        if ($item->combo_id) {
            $mainTotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
            $comboTotal = ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1);
            $totalCombined = $mainTotal + $comboTotal;
            
            // Add embroidery price for combo items (use stored total)
            if ($item->embroidery_total_price > 0) {
                $totalCombined += $item->embroidery_total_price;
            }

            $gstPercent = $item->gst_percent ?? 5;
            $gstAmount = $totalCombined * ($gstPercent / 100);
            $courier = $item->courier_charge ?? 0;

            $grandTotal += $totalCombined + $gstAmount + $courier;
            $totalUniqueItems++;
            $totalQuantity += ($item->main_product_quantity ?? 1) + ($item->combo_product_quantity ?? 1);
        } else {
            $price = $item->main_product_price ?? 0;
            
            // If item has multiple sizes, calculate from sizes; otherwise use main_product_quantity
            if ($item->sizes && $item->sizes->count() > 0) {
                $qty = $item->sizes->sum('quantity');
                // Calculate total from all sizes
                $itemTotal = 0;
                foreach ($item->sizes as $sizeItem) {
                    $sizePrice = $sizeItem->price ?? $price;
                    $itemTotal += $sizePrice * $sizeItem->quantity;
                }
            } else {
                $qty = $item->main_product_quantity ?? 1;
                $itemTotal = $price * $qty;
            }
            
            // Add embroidery price to item total (use stored total)
            if ($item->embroidery_total_price > 0) {
                $itemTotal += $item->embroidery_total_price;
            }

            $gstPercent = $item->gst_percent ?? 5;
            $gstAmount = $itemTotal * ($gstPercent / 100);
            $courier = $item->courier_charge ?? 0;

            $grandTotal += $itemTotal + $gstAmount + $courier;
            $totalUniqueItems++;
            $totalQuantity += $qty;
        }
    }

    return view('frontend.cart', compact(
        'cartItems', 'about', 'categories', 'footer',
        'colors', 'sizes', 'coupons',
        'grandTotal', 'totalQuantity', 'totalUniqueItems',
        'productAttributesMap'
    ));
}

// Format attribute options similar to FrontendProductController
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
                'attribute_slug' => \Illuminate\Support\Str::slug($attribute->name ?? ''),
                'terms' => $terms,
                'default_term_id' => $terms[0]['term_id'] ?? null,
            ];
        })
        ->filter()
        ->values()
        ->toArray();
}

public function addToCart(Request $request)
{
    // Debug: Log request data for embroidery BEFORE validation
    Log::info('Add to Cart - Before Validation', [
        'has_embroidery_checkbox' => $request->has('interested_in_embroidery'),
        'embroidery_value' => $request->input('interested_in_embroidery'),
        'has_file' => $request->hasFile('embroidery_logo'),
        'file_name' => $request->hasFile('embroidery_logo') ? $request->file('embroidery_logo')->getClientOriginalName() : 'no file',
        'content_type' => $request->header('Content-Type'),
    ]);
    
    $request->validate([
        'product_id' => 'nullable|integer',
        'combo_id'   => 'nullable|string',  // Changed to string to accept generated combo IDs
        'moq'        => 'required|integer|min:1',
        'price'      => 'required|numeric',
        'color_id'   => 'nullable|integer',              // Color ID for individual products
        'size_id'    => 'nullable|integer',              // Size ID for individual products
        'size_quantities' => 'nullable|array',           // Array of size quantities: [size_id => quantity]
        'interested_in_embroidery' => 'nullable',         // Allow embroidery checkbox
        'embroidery_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', // Allow embroidery logo
        'combo_item_color_ids' => 'nullable|array',     // Array for combo items' color_id
        'combo_item_size_ids'  => 'nullable|array',     // Array for combo items' size_id
        'combo_item_fabric_ids' => 'nullable|array',    // Array for combo items' fabric_id
        'main_product_attributes' => 'nullable|string',  // JSON string of main product attributes
        'combo_product_attributes' => 'nullable|string', // JSON string of combo product attributes
        'main_product_quantity' => 'nullable|integer|min:1',
        'main_product_price' => 'nullable|numeric',
        'combo_product_quantity' => 'nullable|integer|min:1',
        'combo_product_price' => 'nullable|numeric',
    ]);

    $session_id = Session::getId();
    $customer_id = Auth::guard('customer')->id();
    $quantity = $request->moq;

    // Check if it's a combo product
    if ($request->has('combo_id') && $request->combo_id) {
        // Get main product ID (the product page we're on)
        $mainProductId = $request->product_id ?? null;
        
        if (!$mainProductId) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Main product ID is required for combo products'], 400);
            }
            return back()->with('error', 'Main product ID is required for combo products');
        }
        
        // Get the selected combo product ID from the request
        // The combo_id in the request is the generated identifier, but we need the actual combo product ID
        // Get it from combo_item_color_ids, combo_item_size_ids, or combo_item_fabric_ids keys
        $selectedComboProductId = null;
        
        if ($request->has('combo_item_color_ids') && is_array($request->combo_item_color_ids) && !empty($request->combo_item_color_ids)) {
            // Get first combo product ID from the array keys
            $selectedComboProductId = array_key_first($request->combo_item_color_ids);
        } elseif ($request->has('combo_item_size_ids') && is_array($request->combo_item_size_ids) && !empty($request->combo_item_size_ids)) {
            $selectedComboProductId = array_key_first($request->combo_item_size_ids);
        } elseif ($request->has('combo_item_fabric_ids') && is_array($request->combo_item_fabric_ids) && !empty($request->combo_item_fabric_ids)) {
            $selectedComboProductId = array_key_first($request->combo_item_fabric_ids);
        } else {
            // If no attributes provided, get the first combo product ID from combo_product_items
            $firstComboItem = ComboProductItem::where('product_id', $mainProductId)->first();
            $selectedComboProductId = $firstComboItem ? $firstComboItem->combo_id : null;
        }
        
        if (!$selectedComboProductId) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Combo product ID is required'], 400);
            }
            return back()->with('error', 'Combo product ID is required');
        }

        // Parse JSON attributes
        $mainProductAttributes = null;
        if ($request->has('main_product_attributes') && $request->main_product_attributes) {
            $mainProductAttributes = json_decode($request->main_product_attributes, true);
        }
        
        $comboProductAttributes = null;
        if ($request->has('combo_product_attributes') && $request->combo_product_attributes) {
            $comboProductAttributes = json_decode($request->combo_product_attributes, true);
        }

        // Get main product price from product_attribute table based on selected attributes
        $mainProductPrice = $request->main_product_price ?? $request->price ?? 0;
        $mainProductQuantity = $request->main_product_quantity ?? 1;
        
        // If main_product_attributes are provided, fetch the price from product_attribute table
        if ($mainProductAttributes && is_array($mainProductAttributes) && !empty($mainProductAttributes)) {
            // Build attribute map: attribute_id => term_id
            $attributeMap = [];
            foreach ($mainProductAttributes as $attr) {
                if (isset($attr['attribute_id']) && isset($attr['term_id'])) {
                    $attributeMap[$attr['attribute_id']] = $attr['term_id'];
                }
            }
            
            if (!empty($attributeMap)) {
                // Get all unique combination keys for this product
                $combinationKeys = ProductAttribute::where('proid', $mainProductId)
                    ->whereNotNull('combination_key')
                    ->distinct()
                    ->pluck('combination_key');
                
                // Find the combination_key that matches ALL selected attributes
                $matchedCombinationKey = null;
                foreach ($combinationKeys as $key) {
                    // Get all attributes for this combination
                    $combinationAttrs = ProductAttribute::where('proid', $mainProductId)
                        ->where('combination_key', $key)
                        ->get();
                    
                    // Check if this combination matches all selected attributes
                    $matches = true;
                    $combinationAttrMap = [];
                    foreach ($combinationAttrs as $attr) {
                        $combinationAttrMap[$attr->attribute_id] = $attr->attribute_term_id;
                    }
                    
                    // Check if all selected attributes match
                    foreach ($attributeMap as $attrId => $termId) {
                        if (!isset($combinationAttrMap[$attrId]) || $combinationAttrMap[$attrId] != $termId) {
                            $matches = false;
                            break;
                        }
                    }
                    
                    // Also check that combination has the same number of attributes
                    if ($matches && count($combinationAttrMap) === count($attributeMap)) {
                        $matchedCombinationKey = $key;
                        break;
                    }
                }
                
                // If we found a matching combination, get the price
                if ($matchedCombinationKey) {
                    $productAttribute = ProductAttribute::where('proid', $mainProductId)
                        ->where('combination_key', $matchedCombinationKey)
                        ->first();
                    
                    if ($productAttribute && $productAttribute->price) {
                        // Use the price from product_attribute table
                        $mainProductPrice = $productAttribute->price;
                    }
                }
            }
        }
        
        // Get combo product price and quantity
        $comboProductPrice = $request->combo_product_price ?? $request->price ?? 0;
        $comboProductQuantity = $request->combo_product_quantity ?? $quantity;

        // Check if this exact combo combination already exists in cart
        // Match by: main product ID + combo product ID + same attributes
        $existingCombo = CartItem::where('product_id', $mainProductId)
            ->where('combo_id', $selectedComboProductId)
            ->where(function($q) use ($session_id, $customer_id){
                $q->where('session_id', $session_id);
                if ($customer_id) {
                    $q->orWhere('customer_id', $customer_id);
                }
            })
            ->first();

        if ($existingCombo) {
            // Update quantity for the existing combo
            $existingCombo->update([
                'combo_product_quantity' => $comboProductQuantity,
                'combo_product_price' => $comboProductPrice,
                'main_product_quantity' => $mainProductQuantity,
                'main_product_price' => $mainProductPrice,
                'main_product_attributes' => $mainProductAttributes ? json_encode($mainProductAttributes) : null,
                'combo_product_attributes' => $comboProductAttributes ? json_encode($comboProductAttributes) : null,
            ]);
        } else {
            // Insert ONLY ONE row: main product ID + selected combo product ID
            CartItem::create([
                'product_id' => $mainProductId,  // Main product ID
                'combo_id'   => $selectedComboProductId,  // Selected combo product ID
                'color_id'   => $request->color_id ?? null,
                'size_id'    => $request->size_id ?? null,
                'session_id' => $session_id,
                'customer_id' => $customer_id,
                'is_combo_item' => false,
                'main_product_attributes' => $mainProductAttributes ? json_encode($mainProductAttributes) : null,
                'combo_product_attributes' => $comboProductAttributes ? json_encode($comboProductAttributes) : null,
                'main_product_quantity' => $mainProductQuantity,
                'main_product_price' => $mainProductPrice,
                'combo_product_quantity' => $comboProductQuantity,
                'combo_product_price' => $comboProductPrice,
                // Store combo item attributes for the selected combo product
                'combo_item_color_id' => $request->combo_item_color_ids[$selectedComboProductId] ?? null,
                'combo_item_size_id'  => $request->combo_item_size_ids[$selectedComboProductId] ?? null,
            ]);
        }

        $successMessage = 'Combo added to cart successfully!';
    } else {
        // Handle individual product
        $product = Product::find($request->product_id);

        if (!$product) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Product not found'], 404);
            }
            return back()->with('error', 'Product not found');
        }

        // Build main_product_attributes from request
        $mainProductAttributes = null;
        if ($request->has('main_product_attributes') && $request->main_product_attributes) {
            // If it's already a JSON string, use it; otherwise encode it
            $mainProductAttributes = is_string($request->main_product_attributes) 
                ? $request->main_product_attributes 
                : json_encode($request->main_product_attributes);
        } elseif ($request->has('attributes') && is_array($request->attributes)) {
            // Build attributes array from legacy format
            $attributes = [];
            foreach ($request->attributes as $attributeId => $termId) {
                if ($termId) {
                    // Try to get attribute and term details
                    $attribute = \App\Models\ProductAttribute::find($attributeId);
                    $term = \App\Models\AttributeTerm::find($termId);
                    if ($attribute && $term) {
                        $attributes[] = [
                            'attribute_id' => $attributeId,
                            'attribute_slug' => $attribute->slug ?? '',
                            'term_id' => $termId,
                            'term_name' => $term->term ?? ''
                        ];
                    }
                }
            }
            if (!empty($attributes)) {
                $mainProductAttributes = json_encode($attributes);
            }
        }
        
        // Get main product quantity and price (for individual products, these are the same as quantity and price)
        $mainProductQuantity = $request->main_product_quantity ?? $quantity;
        $mainProductPrice = $request->main_product_price ?? $request->price ?? 0;
        
        // If main_product_attributes are provided, fetch the price from product_attribute table
        if ($mainProductAttributes) {
            $decodedAttributes = is_string($mainProductAttributes) 
                ? json_decode($mainProductAttributes, true) 
                : $mainProductAttributes;
            
            if (is_array($decodedAttributes) && !empty($decodedAttributes)) {
                // Build attribute map: attribute_id => term_id
                $attributeMap = [];
                foreach ($decodedAttributes as $attr) {
                    if (isset($attr['attribute_id']) && isset($attr['term_id'])) {
                        $attributeMap[$attr['attribute_id']] = $attr['term_id'];
                    }
                }
                
                if (!empty($attributeMap)) {
                    // Get all unique combination keys for this product
                    $combinationKeys = ProductAttribute::where('proid', $request->product_id)
                        ->whereNotNull('combination_key')
                        ->distinct()
                        ->pluck('combination_key');
                    
                    // Find the combination_key that matches ALL selected attributes
                    $matchedCombinationKey = null;
                    foreach ($combinationKeys as $key) {
                        // Get all attributes for this combination
                        $combinationAttrs = ProductAttribute::where('proid', $request->product_id)
                            ->where('combination_key', $key)
                            ->get();
                        
                        // Check if this combination matches all selected attributes
                        $matches = true;
                        $combinationAttrMap = [];
                        foreach ($combinationAttrs as $attr) {
                            $combinationAttrMap[$attr->attribute_id] = $attr->attribute_term_id;
                        }
                        
                        // Check if all selected attributes match
                        foreach ($attributeMap as $attrId => $termId) {
                            if (!isset($combinationAttrMap[$attrId]) || $combinationAttrMap[$attrId] != $termId) {
                                $matches = false;
                                break;
                            }
                        }
                        
                        // Also check that combination has the same number of attributes
                        if ($matches && count($combinationAttrMap) === count($attributeMap)) {
                            $matchedCombinationKey = $key;
                            break;
                        }
                    }
                    
                    // If we found a matching combination, get the price
                    if ($matchedCombinationKey) {
                        $productAttribute = ProductAttribute::where('proid', $request->product_id)
                            ->where('combination_key', $matchedCombinationKey)
                            ->first();
                        
                        if ($productAttribute && $productAttribute->price) {
                            // Use the price from product_attribute table
                            $mainProductPrice = $productAttribute->price;
                        }
                    }
                }
            }
        }
        
        // Handle multiple sizes with quantities
        $sizeQuantities = $request->size_quantities ?? [];
        $hasMultipleSizes = !empty($sizeQuantities) && is_array($sizeQuantities);
        
        // Calculate total quantity from size quantities if provided
        $totalQuantity = 0;
        if ($hasMultipleSizes) {
            foreach ($sizeQuantities as $sizeId => $qty) {
                $qty = (int)$qty;
                if ($qty > 0) {
                    $totalQuantity += $qty;
                }
            }
            if ($totalQuantity > 0) {
                $quantity = $totalQuantity;
                $mainProductQuantity = $totalQuantity;
            }
        } else {
            $totalQuantity = $mainProductQuantity ?? $quantity ?? 1;
        }
        
        // Get price based on quantity tier
        $tierBasedPrice = $this->getPriceByQuantityTier($request->product_id, $totalQuantity);
        
        // Use tier-based price if available, otherwise use the calculated price
        if ($tierBasedPrice > 0) {
            $mainProductPrice = $tierBasedPrice;
        }
        
        // Handle embroidery logo upload and price calculation
        $embroideryLogo = null;
        $embroideryPrice = 0;
        
        // Check if embroidery is selected - check multiple ways
        $interestedInEmbroidery = false;
        $embValue = $request->input('interested_in_embroidery');
        
        // Check if checkbox is checked (can be '1', 1, true, 'on', 'true', or just present)
        if ($request->has('interested_in_embroidery') || $embValue !== null) {
            $interestedInEmbroidery = (
                $embValue == '1' || 
                $embValue == 1 || 
                $embValue === true ||
                $embValue == 'on' ||
                $embValue === 'true' ||
                $embValue !== null && $embValue !== '' && $embValue !== '0' && $embValue !== 0 && $embValue !== false
            );
        }
        
        // Calculate embroidery price if interested (regardless of file upload)
        $embroideryTotalPrice = 0;
        if ($interestedInEmbroidery) {
            $embroideryPrice = $this->getEmbroideryPriceByQuantityTier($request->product_id, $totalQuantity);
            // Calculate total embroidery price (per-piece × quantity)
            $embroideryTotalPrice = $embroideryPrice * $totalQuantity;
            
            // Upload logo file if provided - check multiple ways
            $hasFile = $request->hasFile('embroidery_logo');
            $fileInput = $request->file('embroidery_logo');
            
            // Upload logo file if provided
            if ($request->hasFile('embroidery_logo')) {
                try {
                    $file = $request->file('embroidery_logo');
                    
                    if ($file && $file->isValid()) {
                        $originalName = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        
                        // Sanitize filename
                        $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                        $fileName = time() . '_' . uniqid() . '_' . $sanitizedName . '.' . $extension;
                        
                        // Ensure directory exists
                        $directory = storage_path('app/public/embroidery_logos');
                        if (!file_exists($directory)) {
                            mkdir($directory, 0755, true);
                        }
                        
                        // Upload file
                        $filePath = $file->storeAs('embroidery_logos', $fileName, 'public');
                        
                        if ($filePath) {
                            $embroideryLogo = $filePath;
                            Log::info('Embroidery logo uploaded successfully', [
                                'file_path' => $embroideryLogo,
                                'full_path' => storage_path('app/public/' . $embroideryLogo),
                                'file_exists' => file_exists(storage_path('app/public/' . $embroideryLogo))
                            ]);
                        } else {
                            Log::error('Embroidery logo upload failed: storeAs returned false');
                        }
                    } else {
                        Log::error('Embroidery file is invalid', [
                            'file_exists' => $file !== null,
                            'is_valid' => $file ? $file->isValid() : false,
                            'error' => $file ? $file->getError() : 'file is null'
                        ]);
                    }
                } catch (\Exception $e) {
                    // Log error but continue - price will still be saved
                    Log::error('Embroidery logo upload exception: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                // Log that no file was uploaded
                Log::info('Embroidery checkbox checked but hasFile returned false');
            }
        }

        // Check for existing cart item with same attributes (for single size or when no multiple sizes)
        $cartItem = null;
        if (!$hasMultipleSizes) {
            $cartItem = CartItem::where('product_id', $request->product_id)
                ->where('combo_id', null) // Not a combo product
                ->where('color_id', $request->color_id)
                ->where('size_id', $request->size_id)
                ->where(function($q) use ($session_id, $customer_id){
                    $q->where('session_id', $session_id);
                    if ($customer_id) {
                        $q->orWhere('customer_id', $customer_id);
                    }
                })->first();
        } else {
            // For multiple sizes, check if there's a cart item with same product and color but no size_id
            // (since we'll store sizes in cart_item_sizes table)
            $cartItem = CartItem::where('product_id', $request->product_id)
                ->where('combo_id', null)
                ->where('color_id', $request->color_id)
                ->whereNull('size_id')
                ->where(function($q) use ($session_id, $customer_id){
                    $q->where('session_id', $session_id);
                    if ($customer_id) {
                        $q->orWhere('customer_id', $customer_id);
                    }
                })->first();
        }

        if ($cartItem) {
            // Calculate new total quantity after adding
            $newTotalQuantity = $cartItem->main_product_quantity + $totalQuantity;
            
            // Get updated price based on new total quantity tier
            $updatedTierPrice = $this->getPriceByQuantityTier($request->product_id, $newTotalQuantity);
            
            // Get updated embroidery price based on new total quantity tier
            $updatedEmbroideryPrice = 0;
            $updatedEmbroideryTotalPrice = 0;
            if ($interestedInEmbroidery) {
                $updatedEmbroideryPrice = $this->getEmbroideryPriceByQuantityTier($request->product_id, $newTotalQuantity);
                // Calculate total embroidery price (per-piece × new total quantity)
                $updatedEmbroideryTotalPrice = $updatedEmbroideryPrice * $newTotalQuantity;
            }
            
            // Update existing cart item
            $cartItem->quantity += $quantity;
            $cartItem->main_product_quantity = $newTotalQuantity;
            $cartItem->main_product_price = $updatedTierPrice;
            $cartItem->price = $updatedTierPrice; // Update price based on new tier
            if ($mainProductAttributes) {
                $cartItem->main_product_attributes = $mainProductAttributes;
            }
            
            // Update embroidery fields
            Log::info('Updating cart item embroidery', [
                'cart_item_id' => $cartItem->id,
                'interested' => $interestedInEmbroidery,
                'logo' => $embroideryLogo,
                'price' => $updatedEmbroideryPrice,
                'total_price' => $updatedEmbroideryTotalPrice,
                'has_file' => $request->hasFile('embroidery_logo'),
            ]);
            
            if ($interestedInEmbroidery) {
                // Update embroidery price (per-piece) and total price
                $cartItem->embroidery_price = $updatedEmbroideryPrice;
                $cartItem->embroidery_total_price = $updatedEmbroideryTotalPrice;
                
                // Update logo if new one is provided
                if ($embroideryLogo) {
                    // Delete old logo if exists
                    if ($cartItem->embroidery_logo && Storage::disk('public')->exists($cartItem->embroidery_logo)) {
                        Storage::disk('public')->delete($cartItem->embroidery_logo);
                    }
                    $cartItem->embroidery_logo = $embroideryLogo;
                    Log::info('Updated embroidery logo: ' . $embroideryLogo);
                }
                // If no new logo but embroidery is checked, keep existing logo (if any)
            } else {
                // Remove embroidery if unchecked
                if ($cartItem->embroidery_logo && Storage::disk('public')->exists($cartItem->embroidery_logo)) {
                    Storage::disk('public')->delete($cartItem->embroidery_logo);
                }
                $cartItem->embroidery_logo = null;
                $cartItem->embroidery_price = 0;
                $cartItem->embroidery_total_price = 0;
            }
            
            $cartItem->save();
            
            // Verify embroidery was saved
            $cartItem->refresh();
            Log::info('Cart item updated', [
                'cart_item_id' => $cartItem->id,
                'embroidery_logo_saved' => $cartItem->embroidery_logo,
                'embroidery_price_saved' => $cartItem->embroidery_price,
                'embroidery_total_price_saved' => $cartItem->embroidery_total_price,
            ]);
            
            // If multiple sizes, update or create cart_item_sizes entries
            if ($hasMultipleSizes) {
                foreach ($sizeQuantities as $sizeId => $qty) {
                    $qty = (int)$qty;
                    if ($qty > 0) {
                        $sizeTerm = AttributeTerm::find($sizeId);
                        $existingSize = CartItemSize::where('cart_item_id', $cartItem->id)
                            ->where('size_id', $sizeId)
                            ->first();
                        
                        if ($existingSize) {
                            // Update existing size quantity and price
                            $existingSize->quantity += $qty;
                            $existingSize->price = $updatedTierPrice; // Update price to new tier price
                            $existingSize->save();
                        } else {
                            // Create new size entry
                            CartItemSize::create([
                                'cart_item_id' => $cartItem->id,
                                'size_id' => $sizeId,
                                'size_name' => $sizeTerm ? $sizeTerm->term : null,
                                'quantity' => $qty,
                                'price' => $updatedTierPrice, // Use tier-based price
                            ]);
                        }
                    }
                }
            }
        } else {
            // Create new cart item
            // Log embroidery data for debugging
            Log::info('Creating cart item with embroidery', [
                'interested' => $interestedInEmbroidery,
                'logo' => $embroideryLogo,
                'price' => $embroideryPrice,
                'has_file' => $request->hasFile('embroidery_logo'),
                'checkbox_value' => $request->input('interested_in_embroidery'),
            ]);
            
            $cartItem = CartItem::create([
                'product_id' => $request->product_id,
                'combo_id'   => null,
                'color_id'   => $request->color_id, // Assign color_id
                'size_id'    => $hasMultipleSizes ? null : $request->size_id,  // Set to null if multiple sizes
                'price'      => $request->price,
                'combo_price' => null,
                'quantity'   => $quantity,
                'session_id' => $session_id,
                'customer_id' => $customer_id,
                'is_combo_item' => false,
                'main_product_attributes' => $mainProductAttributes,
                'main_product_quantity' => $mainProductQuantity,
                'main_product_price' => $mainProductPrice,
                'combo_product_attributes' => null,
                'combo_product_quantity' => null,
                'combo_product_price' => null,
                'embroidery_logo' => $embroideryLogo,
                'embroidery_price' => $embroideryPrice,
                'embroidery_total_price' => $embroideryTotalPrice,
            ]);
            
            // Verify embroidery was saved
            $cartItem->refresh();
            Log::info('Cart item created', [
                'cart_item_id' => $cartItem->id,
                'embroidery_logo_saved' => $cartItem->embroidery_logo,
                'embroidery_price_saved' => $cartItem->embroidery_price,
                'embroidery_total_price_saved' => $cartItem->embroidery_total_price,
            ]);
            
            // If multiple sizes, create cart_item_sizes entries
            if ($hasMultipleSizes) {
                foreach ($sizeQuantities as $sizeId => $qty) {
                    $qty = (int)$qty;
                    if ($qty > 0) {
                        $sizeTerm = AttributeTerm::find($sizeId);
                        CartItemSize::create([
                            'cart_item_id' => $cartItem->id,
                            'size_id' => $sizeId,
                            'size_name' => $sizeTerm ? $sizeTerm->term : null,
                            'quantity' => $qty,
                            'price' => $mainProductPrice, // Use tier-based price
                        ]);
                    }
                }
            }
        }

        $successMessage = 'Product added to cart';
    }

        // Update cart count in session
        // For combo products, use main_product_quantity + combo_product_quantity
        // For individual products, use quantity or sum of cart_item_sizes quantities
        $totalItems = CartItem::where(function($q) use ($session_id, $customer_id) {
                $q->where('session_id', $session_id);
                if ($customer_id) {
                    $q->orWhere('customer_id', $customer_id);
                }
            })
            ->with('sizes')
            ->get()
            ->sum(function($item) {
                if ($item->combo_id) {
                    // Combo product: sum main and combo quantities
                    return ($item->main_product_quantity ?? 0) + ($item->combo_product_quantity ?? 0);
                } else {
                    // Individual product: if has sizes, sum them; otherwise use quantity
                    if ($item->sizes && $item->sizes->count() > 0) {
                        return $item->sizes->sum('quantity');
                    }
                    return $item->quantity ?? 0;
                }
            });

    session(['cart_count' => $totalItems]);

    // Check if it's an AJAX request
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => $successMessage,
            'cart_count' => $totalItems,
            'redirect' => $request->has('buy_now') ? route('frontend.cart') : null
        ]);
    }

    // If buy now, redirect to cart
    if ($request->has('buy_now')) {
        return redirect()->route('frontend.cart')->with('success', $successMessage);
    }

    return back()->with('success', $successMessage);
}


    // Update cart item sizes and quantities
    public function updateCartItemSizes(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|integer|exists:cart_items,id',
            'size_quantities' => 'required|array',
            'size_quantities.*' => 'integer|min:0',
        ]);

        $cartItem = CartItem::with('sizes')->find($request->cart_item_id);
        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Cart item not found'], 404);
        }

        $sizeQuantities = $request->size_quantities;
        $totalQuantity = 0;

        // Calculate total quantity first
        foreach ($sizeQuantities as $sizeId => $qty) {
            $qty = (int)$qty;
            if ($qty > 0) {
                $totalQuantity += $qty;
            }
        }

        // Get price based on new total quantity tier
        $tierBasedPrice = $this->getPriceByQuantityTier($cartItem->product_id, $totalQuantity);

        // Update or create cart_item_sizes entries with new tier price
        foreach ($sizeQuantities as $sizeId => $qty) {
            $qty = (int)$qty;
            $sizeId = (int)$sizeId;
            
            if ($qty > 0) {
                $sizeTerm = AttributeTerm::find($sizeId);
                $existingSize = CartItemSize::where('cart_item_id', $cartItem->id)
                    ->where('size_id', $sizeId)
                    ->first();
                
                if ($existingSize) {
                    // Update existing size quantity and price
                    $existingSize->quantity = $qty;
                    $existingSize->price = $tierBasedPrice; // Update to new tier price
                    $existingSize->save();
                } else {
                    // Create new size entry
                    CartItemSize::create([
                        'cart_item_id' => $cartItem->id,
                        'size_id' => $sizeId,
                        'size_name' => $sizeTerm ? $sizeTerm->term : null,
                        'quantity' => $qty,
                        'price' => $tierBasedPrice, // Use tier-based price
                    ]);
                }
            } else {
                // Remove size entry if quantity is 0
                CartItemSize::where('cart_item_id', $cartItem->id)
                    ->where('size_id', $sizeId)
                    ->delete();
            }
        }

        // Recalculate embroidery price if embroidery is enabled
        $embroideryPrice = 0;
        $embroideryTotalPrice = 0;
        if ($cartItem->embroidery_logo) {
            $embroideryPrice = $this->getEmbroideryPriceByQuantityTier($cartItem->product_id, $totalQuantity);
            // Calculate total embroidery price (per-piece × quantity)
            $embroideryTotalPrice = $embroideryPrice * $totalQuantity;
        }
        
        // Update cart item total quantity and price
        $cartItem->quantity = $totalQuantity;
        $cartItem->main_product_quantity = $totalQuantity;
        $cartItem->main_product_price = $tierBasedPrice; // Update to new tier price
        $cartItem->price = $tierBasedPrice; // Update to new tier price
        $cartItem->embroidery_price = $embroideryPrice; // Update embroidery price (per-piece)
        $cartItem->embroidery_total_price = $embroideryTotalPrice; // Update embroidery total price
        $cartItem->save();

        // Reload cart item with updated sizes
        $cartItem->refresh();
        $cartItem->load('sizes');

        // Recalculate cart totals
        $session_id = Session::getId();
        $customer_id = Auth::guard('customer')->id();
        
        $totalItems = CartItem::where(function($q) use ($session_id, $customer_id) {
                $q->where('session_id', $session_id);
                if ($customer_id) {
                    $q->orWhere('customer_id', $customer_id);
                }
            })
            ->with('sizes')
            ->get()
            ->sum(function($item) {
                if ($item->combo_id) {
                    return ($item->main_product_quantity ?? 0) + ($item->combo_product_quantity ?? 0);
                } else {
                    if ($item->sizes && $item->sizes->count() > 0) {
                        return $item->sizes->sum('quantity');
                    }
                    return $item->quantity ?? 0;
                }
            });

        session(['cart_count' => $totalItems]);

        // Recalculate full cart totals
        $allCartItems = CartItem::where(function($q) use ($session_id, $customer_id) {
                $q->where('session_id', $session_id);
                if ($customer_id) {
                    $q->orWhere('customer_id', $customer_id);
                }
            })
            ->with('sizes')
            ->get();

        $grandTotal = 0;
        $totalUniqueItems = 0;
        $totalQty = 0;
        $totalSubtotal = 0;
        $totalGST = 0;
        $totalCourierCharge = 0;

        foreach ($allCartItems as $item) {
            if ($item->combo_id) {
                $mainTotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
                $comboTotal = ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1);
                $itemSubtotal = $mainTotal + $comboTotal;
                $totalUniqueItems++;
                $totalQty += ($item->main_product_quantity ?? 1) + ($item->combo_product_quantity ?? 1);
            } else {
                $itemPrice = $item->main_product_price ?? 0;
                
                // If item has multiple sizes, calculate from sizes; otherwise use main_product_quantity
                if ($item->sizes && $item->sizes->count() > 0) {
                    $itemQty = $item->sizes->sum('quantity');
                    $itemSubtotal = 0;
                    foreach ($item->sizes as $sizeItem) {
                        $sizePrice = $sizeItem->price ?? $itemPrice;
                        $itemSubtotal += $sizePrice * $sizeItem->quantity;
                    }
                } else {
                    $itemQty = $item->main_product_quantity ?? 1;
                    $itemSubtotal = $itemPrice * $itemQty;
                }
                
                $totalUniqueItems++;
                $totalQty += $itemQty;
            }

            // Store base subtotal (without embroidery) for display
            $baseSubtotal = $itemSubtotal;
            
            // Add embroidery price to subtotal before calculating GST
            $embroideryPrice = $item->embroidery_total_price ?? 0;
            $itemSubtotalWithEmbroidery = $itemSubtotal + $embroideryPrice;
            
            $gstPercent = $item->gst_percent ?? 5;
            $gstAmount = $itemSubtotalWithEmbroidery * ($gstPercent / 100);
            $courierCharge = $item->courier_charge ?? 0;
            $itemTotal = $itemSubtotalWithEmbroidery + $gstAmount + $courierCharge;

            // Apply discount if exists
            if ($item->discount_amount) {
                $itemTotal -= $item->discount_amount;
            }

            // Add base subtotal (without embroidery) to totalSubtotal for display
            $totalSubtotal += $baseSubtotal;
            $totalGST += $gstAmount;
            $totalCourierCharge += $courierCharge;
            $grandTotal += $itemTotal;
        }
        
        // Calculate total embroidery price across all cart items
        $totalEmbroideryPrice = 0;
        foreach ($allCartItems as $item) {
            $totalEmbroideryPrice += $item->embroidery_total_price ?? 0;
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'cart_count' => $totalItems,
            'cart_item_id' => $cartItem->id,
            'total_quantity' => $totalQuantity,
            'grand_total' => $grandTotal,
            'total_items' => $totalUniqueItems,
            'total_quantity_all' => $totalQty,
            'total_subtotal' => $totalSubtotal,
            'total_gst' => $totalGST,
            'total_courier_charge' => $totalCourierCharge,
            'total_embroidery_price' => $totalEmbroideryPrice,
            'embroidery_price' => $embroideryPrice,
            'embroidery_total_price' => $embroideryTotalPrice,
        ]);
    }

    // Update quantity via AJAX
    public function ajaxUpdateQuantity(Request $request)
    {
        $cart = CartItem::find($request->id);
        if (!$cart) return response()->json(['status' => 'error', 'message' => 'Item not found']);

        // If it's a combo product (has combo_id), update using new columns
        // Check explicitly for non-null and non-empty combo_id
        if (!empty($cart->combo_id) && $cart->combo_id !== null) {
            $updateType = $request->input('update_type'); // 'main', 'combo', or null
            
            // If update_type is not provided, default to updating both (legacy behavior)
            // Otherwise, update only the specified quantity
            if ($request->action == 'increment') {
                
                if ($updateType === 'main') {
                    // Only update main product quantity
                    $cart->main_product_quantity = ($cart->main_product_quantity ?? 1) + 1;
                } elseif ($updateType === 'combo') {

                    // Only update combo product quantity
                    $cart->combo_product_quantity = ($cart->combo_product_quantity ?? 1) + 1;
                } else {
                
                }
            } elseif ($request->action == 'decrement') {
                if ($updateType === 'main') {
                    // Only update main product quantity
                    $newMainQty = max(1, ($cart->main_product_quantity ?? 1) - 1);
                    $cart->main_product_quantity = $newMainQty;
                } elseif ($updateType === 'combo') {
                    // Only update combo product quantity
                    $newComboQty = max(1, ($cart->combo_product_quantity ?? 1) - 1);
                    $cart->combo_product_quantity = $newComboQty;
                } else {
                    // Update both (default/legacy behavior - should not happen with new code)
                    $newMainQty = max(1, ($cart->main_product_quantity ?? 1) - 1);
                    $newComboQty = max(1, ($cart->combo_product_quantity ?? 1) - 1);
                    
                    // If both quantities reach 0, delete the item
                    if ($newMainQty < 1 && $newComboQty < 1) {
                        $cart->delete();
                        return response()->json(['status' => 'removed', 'id' => $request->id]);
                    }
                    
                    $cart->main_product_quantity = $newMainQty;
                    $cart->combo_product_quantity = $newComboQty;
                }
            }

            $cart->save();

            // Calculate subtotal for this item
            $mainProductTotal = ($cart->main_product_price ?? 0) * ($cart->main_product_quantity ?? 1);
            $comboProductTotal = ($cart->combo_product_price ?? 0) * ($cart->combo_product_quantity ?? 1);
            $combinedPrice = $mainProductTotal + $comboProductTotal;
            
            // Add embroidery to subtotal before calculating GST
            $embroideryPrice = $cart->embroidery_total_price ?? 0;
            $subtotalWithEmbroidery = $combinedPrice + $embroideryPrice;
            
            $gstPercent = $cart->gst_percent ?? 5; // Default to 5% if not set
            $gstAmount = $subtotalWithEmbroidery * ($gstPercent / 100);
            $courierCharge = $cart->courier_charge ?? 0;
            $subtotal = $subtotalWithEmbroidery + $gstAmount + $courierCharge;

            // Fetch all cart items for the same session to calculate grand total
            $session_id = Session::getId();
            $customer_id = Auth::guard('customer')->id();
            
            $allCartItems = CartItem::where(function ($q) use ($session_id, $customer_id) {
                $q->where('session_id', $session_id);
                if ($customer_id) {
                    $q->orWhere('customer_id', $customer_id);
                }
            })->get();
            
            // Calculate grand total for all items
            $grandTotal = 0;
            $totalItems = 0;
            $totalQuantity = 0;
            $totalSubtotal = 0;
            $totalGST = 0;
            $totalCourierCharge = 0;
            $processedCombos = [];
            
            foreach ($allCartItems as $item) {
                if ($item->combo_id) {
                    // Combo product calculation
                    $itemMainTotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
                    $itemComboTotal = ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1);
                    $itemCombinedPrice = $itemMainTotal + $itemComboTotal;
                    
                    // Add embroidery to subtotal before calculating GST
                    $embroideryPrice = $item->embroidery_total_price ?? 0;
                    $subtotalWithEmbroidery = $itemCombinedPrice + $embroideryPrice;
                    
                    $itemGSTPercent = $item->gst_percent ?? 5; // Default to 5% if not set
                    $itemGST = $subtotalWithEmbroidery * ($itemGSTPercent / 100);
                    $itemCourierCharge = $item->courier_charge ?? 0;
                    $itemSubtotal = $subtotalWithEmbroidery + $itemGST + $itemCourierCharge;
                    
                    $grandTotal += $itemSubtotal;
                    $totalSubtotal += $itemCombinedPrice;
                    $totalGST += $itemGST;
                    $totalCourierCharge += $itemCourierCharge;
                    
                    // Count combo only once
                    if (!in_array($item->combo_id, $processedCombos)) {
                        $totalItems++;
                        $processedCombos[] = $item->combo_id;
                    }
                    $totalQuantity += ($item->main_product_quantity ?? 1) + ($item->combo_product_quantity ?? 1);
                } else {
                    // Individual product calculation - use main_product_price and main_product_quantity
                    $itemPrice = $item->main_product_price ?? 0;
                    $itemQuantity = $item->main_product_quantity ?? 1;
                    $itemTotal = $itemPrice * $itemQuantity;
                    
                    // Add embroidery to subtotal before calculating GST
                    $embroideryPrice = $item->embroidery_total_price ?? 0;
                    $subtotalWithEmbroidery = $itemTotal + $embroideryPrice;
                    
                    $itemGSTPercent = $item->gst_percent ?? 5; // Default to 5% if not set
                    $itemGST = $subtotalWithEmbroidery * ($itemGSTPercent / 100);
                    $itemCourierCharge = $item->courier_charge ?? 0;
                    $itemSubtotal = $subtotalWithEmbroidery + $itemGST + $itemCourierCharge;
                    
                    $grandTotal += $itemSubtotal;
                    $totalSubtotal += $itemTotal;
                    $totalGST += $itemGST;
                    $totalCourierCharge += $itemCourierCharge;
                    $totalItems++;
                    $totalQuantity += $itemQuantity;
                }
            }

            return response()->json([
                'status' => 'success', 
                'id' => $cart->id, 
                'main_product_quantity' => $cart->main_product_quantity ?? 1,
                'combo_product_quantity' => $cart->combo_product_quantity ?? 1,
                'main_product_price' => $cart->main_product_price ?? 0,
                'combo_product_price' => $cart->combo_product_price ?? 0,
                'subtotal' => $subtotal,
                'grand_total' => $grandTotal,
                'total_items' => $totalItems,
                'total_quantity' => $totalQuantity,
                'total_subtotal' => $totalSubtotal,
                'total_gst' => $totalGST,
                'total_courier_charge' => $totalCourierCharge
            ]);

        } else {
            // Handle individual product - use main_product_quantity and main_product_price
            $currentQty = $cart->main_product_quantity ?? 1;
            
            if ($request->action == 'increment') {
                $newQty = $currentQty + 1;
            } elseif ($request->action == 'decrement') {
                $newQty = max(1, $currentQty - 1);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Invalid action']);
            }

            if ($newQty < 1) {
                $cart->delete();
                return response()->json(['status' => 'removed', 'id' => $request->id]);
            }
            
            // Update the quantity using update() method to ensure it's saved
            $cart->update([
                'main_product_quantity' => $newQty
            ]);
            
            // Refresh the model to get the updated values
            $cart->refresh();
            
            // Calculate proper subtotal for individual product using main_product_price and main_product_quantity
            $mainProductPrice = $cart->main_product_price ?? 0;
            $mainProductQuantity = $cart->main_product_quantity ?? 1;
            $totalUnitPrice = $mainProductPrice * $mainProductQuantity;
            
            // Add embroidery to subtotal before calculating GST
            $embroideryPrice = $cart->embroidery_total_price ?? 0;
            $subtotalWithEmbroidery = $totalUnitPrice + $embroideryPrice;
            
            $gstPercent = $cart->gst_percent ?? 5; // Default to 5% if not set
            $gstAmount = $subtotalWithEmbroidery * ($gstPercent / 100);
            $courierCharge = $cart->courier_charge ?? 0;
            $subtotal = $subtotalWithEmbroidery + $gstAmount + $courierCharge;
            
            // Fetch all cart items for the same session to calculate grand total
            $session_id = Session::getId();
            $customer_id = Auth::guard('customer')->id();
            
            $allCartItems = CartItem::where(function ($q) use ($session_id, $customer_id) {
                $q->where('session_id', $session_id);
                if ($customer_id) {
                    $q->orWhere('customer_id', $customer_id);
                }
            })->get();
            
            // Calculate grand total for all items
            $grandTotal = 0;
            $totalItems = 0;
            $totalQuantity = 0;
            $totalSubtotal = 0;
            $totalGST = 0;
            $totalCourierCharge = 0;
            $processedCombos = [];
            
            foreach ($allCartItems as $item) {
                if ($item->combo_id) {
                    // Combo product calculation
                    $itemMainTotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
                    $itemComboTotal = ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1);
                    $itemCombinedPrice = $itemMainTotal + $itemComboTotal;
                    
                    // Add embroidery to subtotal before calculating GST
                    $embroideryPrice = $item->embroidery_total_price ?? 0;
                    $subtotalWithEmbroidery = $itemCombinedPrice + $embroideryPrice;
                    
                    $itemGSTPercent = $item->gst_percent ?? 5; // Default to 5% if not set
                    $itemGST = $subtotalWithEmbroidery * ($itemGSTPercent / 100);
                    $itemCourierCharge = $item->courier_charge ?? 0;
                    $itemSubtotal = $subtotalWithEmbroidery + $itemGST + $itemCourierCharge;
                    
                    $grandTotal += $itemSubtotal;
                    $totalSubtotal += $itemCombinedPrice;
                    $totalGST += $itemGST;
                    $totalCourierCharge += $itemCourierCharge;
                    
                    // Count combo only once
                    if (!in_array($item->combo_id, $processedCombos)) {
                        $totalItems++;
                        $processedCombos[] = $item->combo_id;
                    }
                    $totalQuantity += ($item->main_product_quantity ?? 1) + ($item->combo_product_quantity ?? 1);
                } else {
                    // Individual product calculation - use main_product_price and main_product_quantity
                    $itemPrice = $item->main_product_price ?? 0;
                    $itemQuantity = $item->main_product_quantity ?? 1;
                    $itemTotal = $itemPrice * $itemQuantity;
                    
                    // Add embroidery to subtotal before calculating GST
                    $embroideryPrice = $item->embroidery_total_price ?? 0;
                    $subtotalWithEmbroidery = $itemTotal + $embroideryPrice;
                    
                    $itemGSTPercent = $item->gst_percent ?? 5; // Default to 5% if not set
                    $itemGST = $subtotalWithEmbroidery * ($itemGSTPercent / 100);
                    $itemCourierCharge = $item->courier_charge ?? 0;
                    $itemSubtotal = $subtotalWithEmbroidery + $itemGST + $itemCourierCharge;
                    
                    $grandTotal += $itemSubtotal;
                    $totalSubtotal += $itemTotal;
                    $totalGST += $itemGST;
                    $totalCourierCharge += $itemCourierCharge;
                    $totalItems++;
                    $totalQuantity += $itemQuantity;
                }
            }
            
            return response()->json([
                'status' => 'success', 
                'id' => $cart->id, 
                'main_product_quantity' => $cart->main_product_quantity ?? 1,
                'main_product_price' => $cart->main_product_price ?? 0,
                'quantity' => $cart->main_product_quantity ?? 1, // For backward compatibility
                'subtotal' => $subtotal,
                'grand_total' => $grandTotal,
                'total_items' => $totalItems,
                'total_quantity' => $totalQuantity,
                'total_subtotal' => $totalSubtotal,
                'total_gst' => $totalGST,
                'total_courier_charge' => $totalCourierCharge
            ]);
        }
    }
       public function remove(Request $request)
    {
        // Find the cart item by its ID from the form input
        $cartItemId = $request->input('cart_item_id');
        $cartItem = CartItem::find($cartItemId);

        // Check if the item exists before trying to delete it
        if (!$cartItem) {
            return redirect()->back()->with('error', 'Item not found in cart!');
        }

        // Delete the item from the database
        $cartItem->delete();

        // Redirect back to the cart page with a success message
        return redirect()->route('frontend.cart')->with('success', 'Product removed from cart successfully!');
    }

    // Delete cart item via AJAX
    public function ajaxDeleteCartItem($id)
    {
        $cart = CartItem::find($id);
        if (!$cart) {
            return response()->json(['status' => 'error', 'message' => 'Item not found']);
        }

        $cart->delete();
        return response()->json(['status' => 'removed', 'id' => $id]);
    }



   public function applyCoupon(Request $request)
{
    try {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $user = Auth::user();
        $cartItems = CartItem::where('customer_id', $user->id)->get();
        
        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty'
            ]);
        }

        // Find coupon by code (not by ID)
        $couponCode = strtoupper(trim($request->coupon_code));
        $coupon = Coupon::where('discount_code', $couponCode)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code. Please check and try again.'
            ], 400);
        }

        // Validate coupon is still valid (use your isValid() method)
        if (!$coupon->isValid()) {
            $message = 'This coupon is not valid.';
            if ($coupon->status !== 'active') {
                $message = 'This coupon is currently inactive.';
            } elseif ($coupon->expiry_date < now()) {
                $message = 'This coupon has expired.';
            } elseif ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                $message = 'This coupon has reached its usage limit.';
            }

            return response()->json([
                'success' => false,
                'message' => $message
            ], 400);
        }

        // Calculate total cart value for validation (excluding existing discounts)
        $cartTotal = 0;
        foreach ($cartItems as $item) {
            if ($item->combo_id) {
                $mainTotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
                $comboTotal = ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1);
                $itemSubtotal = $mainTotal + $comboTotal;
            } else {
                $itemSubtotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
            }
            $gstPercent = $item->gst_percent ?? 5;
            $gstAmount = $itemSubtotal * ($gstPercent / 100);
            $cartTotal += $itemSubtotal + $gstAmount + ($item->courier_charge ?? 0);
        }

        // Check minimum spend
        if ($cartTotal < $coupon->minimum_spend) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum spend of ₹' . number_format((float)$coupon->minimum_spend, 2) . ' required'
            ]);
        }

        // Calculate discount amount based on coupon type
        $discountAmount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discountAmount = ($cartTotal * $coupon->coupon_amount) / 100;
            // If there's a maximum discount limit
            if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }
        } else {
            $discountAmount = min($coupon->coupon_amount, $cartTotal); // Fixed amount, but not more than cart total
        }

        // Calculate discount per item (distribute discount proportionally based on item price)
        $discountPerItem = [];
        $totalSubtotal = 0;
        
        foreach ($cartItems as $item) {
            if ($item->combo_id) {
                $mainTotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
                $comboTotal = ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1);
                $itemSubtotal = $mainTotal + $comboTotal;
            } else {
                $itemSubtotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
            }
            
            // Calculate item's share of the total (subtotal + GST + courier)
            $gstPercent = $item->gst_percent ?? 5;
            $gstAmount = $itemSubtotal * ($gstPercent / 100);
            $itemTotal = $itemSubtotal + $gstAmount + ($item->courier_charge ?? 0);
            
            // Calculate proportional discount for this item
            $itemShare = $itemTotal / $cartTotal;
            $itemDiscount = $discountAmount * $itemShare;
            
            $discountPerItem[$item->id] = round($itemDiscount, 2);
            $totalSubtotal += $itemSubtotal;
        }

        // Apply coupon to all cart items with proportional discount
        foreach ($cartItems as $item) {
            $itemDiscount = $discountPerItem[$item->id] ?? 0;
            
            // Calculate new effective price after discount
            if ($item->combo_id) {
                $mainTotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
                $comboTotal = ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1);
                $itemSubtotal = $mainTotal + $comboTotal;
            } else {
                $itemSubtotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
            }
            
            $gstPercent = $item->gst_percent ?? 5;
            $gstAmount = $itemSubtotal * ($gstPercent / 100);
            $itemTotalBeforeDiscount = $itemSubtotal + $gstAmount + ($item->courier_charge ?? 0);
            
            // Store coupon details in cart table
            $item->update([
                'coupon_id' => $coupon->id, // Use $coupon->id
                'coupon_code' => $coupon->discount_code, // Use $coupon->discount_code
                'discount_amount' => $itemDiscount,
                'discount_type' => $coupon->discount_type, // Use $coupon->discount_type
                'coupon_details' => json_encode([
                    'original_coupon_amount' => $coupon->coupon_amount, // Use $coupon->coupon_amount
                    'applied_discount' => $itemDiscount,
                    'discount_percentage' => $coupon->discount_type === 'percentage' ? $coupon->coupon_amount : null,
                    'minimum_spend' => $coupon->minimum_spend,
                    'expiry_date' => $coupon->expiry_date,
                    'total_cart_discount' => $discountAmount // Use calculated $discountAmount
                ]),
                // Optional: Store effective price after discount
                'effective_price' => max(0, $itemTotalBeforeDiscount - $itemDiscount)
            ]);
        }

        // Also update the main cart record if you have a cart table
        // Note: If you don't have a separate Cart table, you can skip this part
        // or calculate totals from cart items
        $cart = CartItem::where('customer_id', $user->id)->first();
        if ($cart) {
            $cart->update([
                'applied_coupon_id' => $coupon->id,
                'applied_coupon_code' => $coupon->discount_code,
                'total_discount' => $discountAmount,
                'discount_type' => $coupon->discount_type,
                'subtotal' => $totalSubtotal,
                'total' => $cartTotal - $discountAmount, // Auto subtract from total
                'grand_total' => $cartTotal - $discountAmount
            ]);
        }

        // Increment coupon usage count
        $coupon->increment('used_count');

        // Recalculate totals for response
        $newCartTotal = $cartTotal - $discountAmount;
        
        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount_amount' => $discountAmount,
            'coupon_code' => $coupon->discount_code,
            'new_total' => $newCartTotal,
            'formatted_new_total' => '₹' . number_format($newCartTotal, 2),
            'formatted_discount' => '₹' . number_format($discountAmount, 2),
            'items_updated' => $cartItems->count()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to apply coupon: ' . $e->getMessage()
        ], 500);
    }
}

public function removeCoupon(Request $request)
{
    Log::info('RemoveCoupon Called');



        // FIXED: Correct guard
        $customer = Auth::guard('customer')->user();
$userId = $customer->id;

        if (!$customer) {
            
            return response()->json([
                'success' => false,
                'message' => 'User not logged in'
            ], 401);
        }
        
        // Check if cart exists
        if (!CartItem::where('customer_id', $userId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty'
            ], 400);
        }

        // Get items with coupon
        $cartItemsWithCoupon = CartItem::where('customer_id', $userId)
            ->whereNotNull('coupon_id')
            ->get();
    
        if ($cartItemsWithCoupon->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No coupon is currently applied'
            ], 400);
        }

        $appliedCouponCode = $cartItemsWithCoupon->first()->coupon_code;
        // Remove coupon
        CartItem::where('customer_id', $userId)->update([
            'coupon_id' => null,
            'coupon_code' => null,
            'discount_amount' => null,
            'discount_type' => null,
            'updated_at' => now()
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully!'
        ]);

   
}


}
