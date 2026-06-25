<div class="product-box-outer item mb-3">
    <div class="product-box custom-product-card" style="margin: 0 auto !important; border: 1px solid #f0f0f0 !important; border-radius: 4px !important; padding: 10px !important; background: #fff !important; transition: box-shadow 0.3s ease !important; display: flex !important; flex-direction: column !important; justify-content: flex-start !important; width: 100%; height: auto !important;">
        <div class="product-box-img" style="height: 130px !important; width: 100% !important; display: flex !important; align-items: center !important; justify-content: center !important; overflow: hidden !important;">
            <a href="{{ route('productmanage.show', $product->id) }}" style="display: flex !important; align-items: center !important; justify-content: center !important; width: 100% !important; height: 100% !important;">
                <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->name }}" style="max-height: 100% !important; max-width: 100% !important; object-fit: contain !important; margin: 0 auto !important; transition: transform 0.3s ease;">
            </a>
        </div>
        <div class="product-bottom" style="padding: 6px 0 0 0 !important; display: flex; flex-direction: column;">
            <h3 style="font-size: 14px !important; line-height: 1.3 !important; height: 36px !important; margin: 0 0 4px 0 !important; overflow: hidden !important; display: -webkit-box !important; -webkit-line-clamp: 2 !important; -webkit-box-orient: vertical !important;">
                <a href="{{ route('productmanage.show', $product->id) }}" style="color: #212121; text-decoration: none; font-weight: 500;">
                    {{ $product->name }}
                </a>
            </h3>
            
            @php
                $realAvgRating = \App\Models\ProductReview::where('product_id', $product->id)->where('is_approved', 1)->avg('rating') ?? 0;
                $realReviewCount = \App\Models\ProductReview::where('product_id', $product->id)->where('is_approved', 1)->count();
            @endphp
            <div class="rating-size-sec" style="display: flex !important; align-items: center !important; margin-bottom: 8px !important; gap: 8px;">
                <span class="rating-sec" style="font-size: 12px !important; font-weight: bold; margin: 0 !important; color: #212121; display: inline-flex; align-items: center; gap: 3px;">
                    {{ number_format($realAvgRating, 1) }}
                    <i class="fa-solid fa-star" style="font-size: 10px !important; color: #ffc107;"></i>
                    @if($realReviewCount > 0)
                        <span style="color: #878787; font-weight: normal; margin-left: 2px;">({{ $realReviewCount }})</span>
                    @endif
                </span>
                <span class="color-size-sec" style="font-size: 12px !important; margin: 0 !important; color: #878787;">
                    @if(isset($product->created_by) && $product->created_by === 'seller')
                        {{ $product->seller->name ?? 'Seller Store' }}
                    @else
                        {{ $product->brand->name ?? $product->brand_name ?? 'Store' }}
                    @endif
                </span>
            </div>
            
            <div style="margin-top: auto;">
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
                
                @if($regular > $price || $discountPct > 0)
                <div class="off-sec" style="margin-bottom: 4px !important; display: flex !important; align-items: center !important; gap: 8px !important;">
                    <span class="old-rupess" style="font-size: 13px !important; margin: 0 !important; color: #878787; text-decoration: line-through;">₹{{ number_format($regular) }}</span>
                    <span class="off-box" style="font-size: 13px !important; margin: 0 !important; font-weight: bold; color: #388e3c;">
                        {{ $discountPct }}% off
                    </span>
                </div>
                @else
                <div class="off-sec" style="margin-bottom: 4px !important; display: flex !important; align-items: center !important; gap: 8px !important; visibility: hidden;">
                    <span class="old-rupess" style="font-size: 13px !important; margin: 0 !important;">₹0</span>
                    <span class="off-box" style="font-size: 13px !important; margin: 0 !important;">0% off</span>
                </div>
                @endif

                <div class="product-bottom-sec" style="display: flex !important; justify-content: space-between !important; align-items: center !important; margin-top: 0 !important; margin-bottom: 0 !important; padding-bottom: 0 !important; flex-direction: row !important;">
                    <span class="new-rupess" style="font-size: 18px !important; font-weight: 600 !important; margin: 0 !important; color: #212121;">₹{{ number_format($product->price) }}</span>
                    <a href="{{ route('productmanage.show', $product->id) }}" class="add-cart-btn" style="padding: 6px 12px !important; font-size: 12px !important; font-weight: bold !important; border-radius: 4px !important; line-height: 1.2 !important; background: #2874f0; color: #fff; text-decoration: none; display: inline-block; width: auto !important; min-width: unset !important; margin: 0 !important;">
                        View
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.custom-product-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}
.custom-product-card:hover img {
    transform: scale(1.05);
}
</style>
