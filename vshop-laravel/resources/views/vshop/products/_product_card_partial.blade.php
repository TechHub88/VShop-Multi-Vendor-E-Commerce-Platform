<div class="product-box">

{{-- Wishlist --}}
<div class="share-wishlist-sec">
    {{-- SHARE BUTTON --}}
    <a href="javascript:void(0);" 
       class="share-btn" 
       data-url="{{ route('productmanage.show', $product->id) }}"
       data-title="{{ $product->name }}">
        <i class="fa-solid fa-share-nodes"></i>
    </a>

    {{-- WISHLIST --}}
    @auth('customer') 
        <a href="#" class="wishlist-btn" data-product-id="{{ $product->id }}">
            <i class="{{ in_array($product->id, $wishlistIds ?? [])
                ? 'fa-solid fa-heart text-danger'
                : 'fa-solid fa-heart' }}">
            </i>
        </a>
    @else
        <a href="{{ route('vshoplogin', ['redirect' => request()->fullUrl()]) }}">
            <i class="fa-solid fa-heart"></i>
        </a>
    @endauth
</div>

{{-- Image --}}
<div class="product-box-img">
    <a href="{{ route('productmanage.show', $product->id) }}">
        <img src="{{ $product->thumbnail ? asset($product->thumbnail) : asset('vtravelassets/images/banner-pic-main.webp') }}">
    </a>
</div>

<div class="product-bottom">
    {{-- Name --}}
    <h3>
        <a href="{{ route('productmanage.show', $product->id) }}" title="{{ $product->name }}">
            {{ $product->name }}
        </a>
    </h3>

    {{-- Rating --}}
    <div class="rating-size-sec mt-1 d-flex align-items-center justify-content-start gap-1" style="line-height: 1;">
        <span class="rating-num fw-bold" style="font-size: 11px; color: #007185;">{{ number_format($product->average_rating ?? 0, 1) }}</span>
        <span class="stars d-inline-flex gap-0" style="color: #ffa41c;">
            @for($i=1;$i<=5;$i++)
                <i class="fa-solid fa-star {{ $i <= round($product->average_rating ?? 0) ? '' : 'text-secondary opacity-50' }}" style="font-size: 9px; margin-right: 1px;"></i>
            @endfor
        </span>
        <span class="rating-count" style="font-size: 11px; color: #007185;">({{ $product->total_reviews ?? 0 }})</span>
    </div>

    {{-- Discount & Price Calculations --}}
    @php
        $regular = $product->regular_price ?? $product->price;
        $price = $product->discount_price ?? $product->price;
        $discountPct = 0;
        if ($product->is_discount && $product->discount_value > 0) {
            $discountPct = $product->discount_type == 'percentage' ? $product->discount_value : round(($product->discount_value / ($regular ?: 1)) * 100);
        } else if ($regular > $price) {
            $discountPct = round((($regular - $price) / $regular) * 100);
        } else if ($product->price > 0 && !empty($product->discount_price) && $product->price > $product->discount_price) {
            $discountPct = round((($product->price - $product->discount_price) / $product->price) * 100);
            $regular = $product->price;
            $price = $product->discount_price;
        }
    @endphp

    {{-- Price Block --}}
    <div class="mt-1 d-flex flex-wrap align-items-baseline gap-1" style="line-height: 1.1;">
        <span class="fw-bold text-dark" style="font-size: 14px;">₹{{ number_format($price, 0) }}</span>
        @if($regular > $price || $discountPct > 0)
            <span class="text-muted text-decoration-line-through" style="font-size: 10px;">₹{{ number_format($regular, 0) }}</span>
            <span class="text-success fw-bold" style="font-size: 10px;">{{ $discountPct }}% Off</span>
        @endif
    </div>

    {{-- Seller Name --}}
    <div class="mt-1 text-truncate" style="font-size: 10px; color: #565959;">
        By: @if($product->created_by === 'seller') {{ $product->seller->name ?? 'Seller' }} @else {{ $product->brand->name ?? $product->brand_name ?? 'Store' }} @endif
    </div>

    {{-- Action Button --}}
    <div class="product-bottom-sec mt-auto pt-2" style="padding: 0;">
        <button class="add-cart-btn" onclick="window.location.href='{{ route('productmanage.show', $product->id) }}'" style="font-size: 10px !important; padding: 4px 6px !important; border-radius: 4px !important; width: 100% !important; margin: 0 !important; display: block; border: 1px solid #2874f0; background: #2874f0; color: #fff;">
            View Details
        </button>
    </div>
</div>
</div>
