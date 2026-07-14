<div class="product-box-outer item mb-3">
    <a href="{{ route('productmanage.show', $product->id) }}" class="custom-product-card">
        <div class="product-box-img">
            <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->name }}">
        </div>
        
        <div class="product-bottom">
            <h3 class="product-title" title="{{ $product->name }}">
                {{ $product->name }}
            </h3>
            
            @php
                $realAvgRating = \App\Models\ProductReview::where('product_id', $product->id)->where('is_approved', 1)->avg('rating') ?? 0;
                $realReviewCount = \App\Models\ProductReview::where('product_id', $product->id)->where('is_approved', 1)->count();
            @endphp
            
            <div class="brand-rating-row" style="align-items: flex-start;">
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    <span class="product-brand-name" style="display: flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-circle-check text-primary" style="font-size: 11px;"></i> 
                        @if(isset($product->created_by) && $product->created_by === 'seller')
                            {{ $product->seller->name ?? 'Seller Store' }}
                        @else
                            {{ $product->brand->name ?? $product->brand_name ?? 'Store' }}
                        @endif
                    </span>
                    @if(isset($product->created_by) && $product->created_by === 'seller' && ($product->seller->logo_trademark ?? '') == 'Yes' && !empty($product->seller->trademark_file))
                        <span class="text-success" style="font-size: 9px; font-weight: 600; display: flex; align-items: center; gap: 3px;">
                            <i class="fa-solid fa-circle-check" style="font-size: 9px;"></i> Trademark Verified
                        </span>
                    @endif
                </div>
                
                <div style="display: flex; align-items: center; gap: 4px;">
                @if($realAvgRating > 0)
                <div class="rating-badge {{ $realAvgRating >= 3 ? 'rating-good' : 'rating-avg' }}">
                    {{ number_format($realAvgRating, 1) }}
                    <i class="fa-solid fa-star"></i>
                </div>
                @endif
                    <span class="rating-count">({{ $realReviewCount }})</span>
                </div>
            </div>
            
            <div class="price-section">
                @php
                    $regular = $product->regular_price ?? $product->price;
                    $price = $product->price;
                    $discountPct = 0;
                    if ($product->is_discount && $product->discount_value > 0) {
                        $discountPct = $product->discount_type == 'percentage' ? $product->discount_value : round(($product->discount_value / ($regular ?: 1)) * 100);
                    } else if ($regular > $price) {
                        $discountPct = round((($regular - $price) / $regular) * 100);
                    }
                @endphp
                
                <div class="new-rupess">₹{{ number_format($product->price) }}</div>
                
                @if($regular > $price || $discountPct > 0)
                    <div class="old-rupess">₹{{ number_format($regular) }}</div>
                    <div class="off-box">{{ $discountPct }}% off</div>
                @endif
            </div>
        </div>
    </a>
</div>

<style>
.custom-product-card {
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border-radius: 4px;
    border: 1px solid #f0f0f0;
    transition: box-shadow 0.2s ease-in-out;
    overflow: hidden;
    text-decoration: none !important;
    height: 100%;
}

.custom-product-card:hover {
    box-shadow: 0 2px 8px 0 rgba(0,0,0,.1);
}

.custom-product-card .product-box-img {
    height: 100px;
    width: 100%;
    padding: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
}

.custom-product-card .product-box-img img {
    max-height: 100%;
    max-width: 100%;
    object-fit: contain;
}

.custom-product-card .product-bottom {
    padding: 10px 12px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.custom-product-card .product-title {
    font-size: 13px;
    font-weight: 500;
    color: #212121;
    line-height: 1.3;
    margin: 0 0 6px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    height: 34px;
}

.custom-product-card .brand-rating-row {
    display: flex;
    align-items: center;
    margin-bottom: 6px;
    gap: 6px;
    flex-wrap: wrap;
}

.custom-product-card .product-brand-name {
    font-size: 11px;
    color: #878787;
    font-weight: 500;
}

.custom-product-card .rating-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 1px 4px;
    border-radius: 3px;
    color: #fff;
    font-size: 10px;
    font-weight: bold;
    gap: 2px;
}

.custom-product-card .rating-badge i {
    font-size: 8px;
}

.custom-product-card .rating-good {
    background-color: #388e3c;
}

.custom-product-card .rating-avg {
    background-color: #ff9f00;
}

.custom-product-card .rating-none {
    background-color: #878787;
    display: none; /* Hide if no rating */
}

.custom-product-card .rating-count {
    font-size: 12px;
    color: #878787;
    font-weight: 500;
}

.custom-product-card .price-section {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: auto;
}

.custom-product-card .new-rupess {
    font-size: 14px;
    font-weight: 600;
    color: #212121;
}

.custom-product-card .old-rupess {
    font-size: 11px;
    color: #878787;
    text-decoration: line-through;
}

.custom-product-card .off-box {
    font-size: 11px;
    font-weight: bold;
    color: #388e3c;
}

@media (max-width: 768px) {
    .custom-product-card .product-box-img {
        height: 90px;
        padding: 4px;
    }
    .custom-product-card .product-title {
        font-size: 12px;
        height: 32px;
    }
    .custom-product-card .new-rupess {
        font-size: 13px;
    }
    .custom-product-card .old-rupess, .custom-product-card .off-box {
        font-size: 10px;
    }
    .custom-product-card .product-bottom {
        padding: 8px 10px;
    }
}
</style>
