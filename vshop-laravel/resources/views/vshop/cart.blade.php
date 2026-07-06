@extends('vshop.main')

@section('title','My Cart')

@section('content')

<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
    <div class="dashboardPosPagesLeftContentonner">
        <div class="dashboardDtlsArea">
            <div class="dashboardDtlsAreaBody">

                {{-- ================= BREADCRUMB ================= --}}
                <div class="bed-cum-sec">
                    <button class="back-btn" onclick="window.history.back()">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a> /</li>
                        <li>Wishlist - Cart</li>
                    </ul>
                </div>

                {{-- ================= EMPTY CART ================= --}}
                <div class="emply-cart-sec" @if(count($cartItems) > 0) style="display: none;" @endif>
                    <p>Cart is empty, Add favourite products to start shopping !</p>
                </div>

                {{-- ================= CART SECTION ================= --}}
                @if(count($cartItems) > 0)
                <div class="cart-sec">
                    <ul class="cart-items-list" style="list-style: none; padding: 0;">
                        @foreach($cartItems as $index => $item)
                        @php
                            // Decode main_product_attributes if it's JSON
                            $attributes = is_string($item->main_product_attributes) ? 
                                json_decode($item->main_product_attributes, true) : 
                                $item->main_product_attributes;
                            
                            // Get size and color info from cart_item_sizes if available
                            $itemSizes = isset($item->sizes) ? $item->sizes : [];
                            if(empty($itemSizes) && isset($item->id)) {
                                // Try to load sizes from cart_item_sizes table
                                $itemSizes = \DB::table('cart_item_sizes')
                                    ->where('cart_item_id', $item->id)
                                    ->get();
                            }
                            
                            // Get current size stock - this is the available quantity from cart_item_sizes
                            $currentSizeStock = $itemSizes->where('size_id', $item->size_id)->first();
                            $maxQuantity = $currentSizeStock->quantity ?? 999999; // Set high default if no limit
                            
                            // Calculate item totals
                            $itemPrice = $item->main_product_price ?? 5900;
                            $itemQuantity = $item->main_product_quantity ?? 1;
                            $itemTotal = $itemPrice * $itemQuantity;
                            $itemSubtotal = $itemTotal;
                        @endphp
                        
                        <li class="cart-details-list-box cart-list-sec {{ $loop->first ? 'pt-0' : '' }}" 
                            data-id="{{ $item->id }}"
                            data-is-combo="false"
                            data-main-product-price="{{ $itemPrice }}"
                            data-main-product-quantity="{{ $itemQuantity }}"
                            data-max-quantity="{{ $maxQuantity }}">
                            
                            {{-- LEFT PART: image + details --}}
                            <div class="cart-list-left">
                                <div class="cart-list-left-img">
                                    <a href="{{ route('productmanage.show', $item->product_id ?? $item->product->id ?? 0) }}">
                                        <img src="{{ asset($item->product->thumbnail ?? $item->product_thumbnail ?? 'images/banner-pic-main.webp') }}" 
                                             alt="product" class="img-fluid">
                                    </a>
                                </div>

                                <div class="product-list-box-right">
                                    {{-- Product title --}}
                                    <div class="product-heading">
                                        <div class="product-heading-left">
                                            <h3>{{ $item->product->name ?? $item->product_name ?? 'Product Name' }}</h3>
                                            
                                            
                                         @php
$attributes = is_string($item->main_product_attributes)
    ? json_decode($item->main_product_attributes, true)
    : $item->main_product_attributes;
@endphp

@if(!empty($attributes))
<div class="product-attributes">

@foreach($attributes as $key => $attr)

@if($key === 'Booking Slots')
<span style="display:block;font-size:14px;color:#555;margin-top:5px;">
<strong><i class="fa fa-calendar-alt text-primary me-1"></i> Booking Slots:</strong>
<div class="d-flex flex-wrap gap-1 mt-1">
    @foreach(explode(', ', $attr['name'] ?? '') as $slot)
        <span class="badge bg-light text-dark border p-1" style="font-size: 11px; font-weight: 500;">
            {{ $slot }}
        </span>
    @endforeach
</div>
</span>
@else
<span style="display:block;font-size:14px;color:#555;">
<strong>{{ ucfirst($key) }} :</strong>
{{ $attr['name'] ?? '' }}
</span>
@endif

@endforeach

</div>
@endif

                                        </div>
                                    </div>

                                    {{-- Rating & price row --}}
                                    <div class="rating-sec">
                                        <div class="rating-left">
                                            <ul>
                                                @php
    $reviews = $item->product->reviews ?? collect();
    $avgRating = $reviews->avg('rating') ?? 0;
    $totalReviews = $reviews->count();
@endphp

<li>
    {{ number_format($avgRating, 1) }}
    <i class="fa-solid fa-star"></i>
    <small>({{ $totalReviews }})</small>
</li>

                                            </ul>
                                        </div>
                                    </div>

                                    {{-- SIZE options from cart_item_sizes --}}
                                    @if(count($itemSizes) > 0)
                                    <div class="quantity-sec size-selection">
                                        @foreach($itemSizes as $size)
                                        <label class="quantity-box {{ $size->size_id == $item->size_id ? 'active' : '' }}">
                                            <input type="radio" name="size_{{ $item->id }}" 
                                                   value="{{ $size->size_id }}"
                                                   data-size-id="{{ $size->size_id }}"
                                                   data-size-name="{{ $size->size_name }}"
                                                   data-size-price="{{ $size->price }}"
                                                   data-size-quantity="{{ $size->quantity }}"
                                                   data-cart-item-id="{{ $item->id }}"
                                                   class="size-radio"
                                                   {{ $size->size_id == $item->size_id ? 'checked' : '' }}>
                                            <span>{{ $size->size_name }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                    @elseif(!empty($item->size))
                                    <div class="quantity-sec">
                                        <label class="quantity-box active">
                                            <input type="radio" checked>
                                            <span>{{ $item->size->name ?? $item->size_name ?? 'Size' }}</span>
                                        </label>
                                    </div>
                                    @endif

                                    {{-- COLOR selection --}}
                                    @if(!empty($attributes['color']) || !empty($item->color))
                                    <div class="color-select-sec">
                                        @php
                                            $colorName = $attributes['color']['name'] ?? $item->color->name ?? '';
                                            $colorValue = $attributes['color']['value'] ?? $item->color->value ?? '#000';
                                        @endphp
                                        <label class="color-box">
                                            <input type="radio" checked name="color_{{ $item->id }}">
                                            <span class="color-circle" style="background: {{ $colorValue }};"></span>
                                        </label>
                                        <p>( {{ $colorName }} )</p>
                                    </div>
                                    @endif

                                    {{-- QUANTITY + move/remove links --}}
                                     <div class="product-col-bottom-sec d-flex align-items-center justify-content-start gap-3 mt-3">
                                        <div class="quantity-sub-sec">
                                            <button type="button" class="sub update-qty" 
                                                    data-id="{{ $item->id }}"
                                                    data-product-name="{{ $item->product->name ?? $item->product_name ?? 'Product' }}"
                                                    data-type="{{ Auth::guard('customer')->check() ? 1 : 0 }}">
                                                @if($itemQuantity <= 1)
                                                    <i class="fa-solid fa-trash text-danger" style="font-size: 14px;"></i>
                                                @else
                                                    -
                                                @endif
                                            </button>
                                            <span class="count qty-display">{{ $itemQuantity }}</span>
                                            <button type="button" class="add update-qty"
                                                    data-id="{{ $item->id }}"
                                                    data-type="{{ Auth::guard('customer')->check() ? 1 : 0 }}">+</button>
                                        </div>
                                        
                                        {{-- Modern Wishlist Button --}}
                                        <a href="#" class="moveToWishlist wishlist-action-btn" data-id="{{ $item->id }}" title="Move to Wishlist">
                                            <i class="fa-regular fa-heart"></i>
                                            <span>Move to Wishlist</span>
                                        </a>
                                    </div>

                                    {{-- Delivery address line --}}
                                   <div class="product-dtl-sec mt-3">
                                    @if($deliveryAddress)
                                        <p>
                                            Delivery Address : 
                                            {{ $deliveryAddress->name }},
                                            {{ $deliveryAddress->city }},
                                            {{ $deliveryAddress->state }} - {{ $deliveryAddress->pincode }}
                                            <br>
                                          
                                            <a href="{{ route('vshop.address.index') }}" style="color:black;">
                                                Change Delivery Address
                                            </a>
                                        </p>
                                    @else
                                        <p>
                                            No delivery address found.
                                            <a href="{{ route('vshop.address.create') }}" style="color:#BABABA;">
                                                Add Address
                                            </a>
                                        </p>
                                    @endif
                                </div>

                                   
                                </div>
                            </div>

                            {{-- RIGHT PRICE BOX with breakdown --}}
                            <div class="cart-price-sec cart-price-box">
                                <div class="cart-peice-box pt-0">
                                    @php
                                        $deliveryDays = $item->product->delivery_time ?? 7;
                                        $deliveryDate = \Carbon\Carbon::now()->addDays((int)$deliveryDays)->format('jS M Y');
                                    @endphp
                                    <p class="delivery-date">Delivery by <span>{{ $deliveryDate }}</span></p>
                                </div>
                                <div class="cart-peice-box border0 pb-0">
                                    <ul>
                                        <li class="mb-0">
                                            <p class="subColor">Price</p>
                                            <span class="rupess item-subtotal">
                                                ₹ {{ number_format($itemSubtotal, 2) }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>

                    {{-- ========== TOTALS SECTION ========== --}}
                    @php
                        $totalItemsCount = $totalItems ?? count($cartItems);
                        $totalQuantitySum = $totalQuantity ?? $cartItems->sum('main_product_quantity');
                        $totalSavings = $discount ?? 2400;
                        $cartGrandTotal = $grandTotal ?? 18985.00;
                    @endphp
                    
                    <div class="total-sec">
                        <div class="total-left">
                            <p class="mb-0">Total Items : <span class="total-items-count">{{ $totalItemsCount }}</span></p>
                            <p class="mb-0">Total Quantity : <span class="total-quantity-sum">{{ $totalQuantitySum }}</span></p>
                            
                        </div>
                        <div class="cart-price-sec">
                            <div class="cart-peice-box border0 p-0">
                                <ul>
                                    <li class="mb-0">
                                        <p class="subColor">Total :</p>
                                        <span class="rupess cart-grand-total">₹ {{ number_format($cartGrandTotal, 2) }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- ========== PLACE ORDER BUTTON ========== --}}
                   <div class="place-btn-sec">
                        <button class="add-place-btn" onclick="window.location.href='{{ route('vshop.address.index') }}'">
                            Proceed to Checkout
                        </button>
                    </div>

                </div> {{-- end cart-sec --}}
                @endif

                {{-- ================= RECOMMENDED PRODUCTS SECTION ================= --}}
                @if(isset($recommendedProducts) && count($recommendedProducts) > 0)
                <div class="wishlist-sec mb-4">
                    <div class="wishlist-header">
                        <h2>Recommended Products For You</h2>
                    </div>
                    <div class="wishlist-bottom">
                        <div class="recommended-product-wrapper owl-carousel">
                            @foreach($recommendedProducts as $product)
                                <div class="other-product-box item">
                                    <div class="product-box position-relative">
                                        @if(isset($product->recommendation_type))
                                            <span class="badge bg-info text-white position-absolute top-0 start-50 translate-middle-x mt-2" style="z-index:2; font-size:10px; text-transform:uppercase; letter-spacing:0.5px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); white-space: nowrap;">
                                                {{ str_replace('_', ' ', $product->recommendation_type) }}
                                            </span>
                                        @endif
                                        <div class="product-box-img">
                                           <a href="{{ route('productmanage.show', $product->id ?? 0) }}">
                                                <img src="{{ asset($product->thumbnail ?? 'images/banner-pic-main.webp') }}" alt="{{ $product->name ?? '' }}">
                                            </a>
                                        </div>
                                        <div class="product-bottom">
                                            <h3>
                                                <a href="{{ route('productmanage.show', $product->id ?? 0) }}">
                                                    {{ $product->name ?? '' }}
                                                </a>
                                            </h3>
                                            <div class="off-sec">
                                                @if($product->discount_price && $product->price > $product->discount_price)
                                                    <p class="old-rupess">₹ {{ number_format($product->price, 2) }}</p>
                                                    <p class="off-box">
                                                        {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% Off
                                                    </p>
                                                @else
                                                    <p class="old-rupess">₹ {{ number_format($product->price * 1.14, 2) }}</p>
                                                    <p class="off-box">14% Off</p>
                                                @endif
                                            </div>
                                            <div class="product-bottom-sec">
                                                <a href="#" class="add-cart-btn recommended-add-to-cart" 
                                                   data-product-id="{{ $product->id }}">
                                                    Add to Cart
                                                </a>
                                                <p class="new-rupess">
                                                    ₹ {{ number_format($product->discount_price ?? $product->price, 2) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- ================= WISHLIST SECTION ================= --}}
                <div class="wishlist-sec">
                    <div class="wishlist-header">
                        <h2>Wishlist / Products you would like to buy</h2>
                    </div>
                    <div class="wishlist-bottom">
                        @if(isset($wishlistItems) && count($wishlistItems) > 0)
                            <div class="other-product-wrapper owl-carousel">
                                @foreach($wishlistItems as $wishlist)
                                    @php
                                        $product = $wishlist->product;
                                    @endphp
                                    <div class="other-product-box item">
                                        <div class="product-box">
                                            <div class="share-wishlist-sec">
                                                <a href="#"><i class="fa-solid fa-share-nodes"></i></a>
                                                <a href="#" class="wishlist-remove" data-wishlist-id="{{ $wishlist->id }}">
                                                    <i class="fa-solid fa-heart text-danger"></i>
                                                </a>
                                            </div>
                                            <div class="product-box-img">
                                               <a href="{{ route('productmanage.show', $product->id ?? 0) }}">
                                                    <img src="{{ asset($product->thumbnail ?? 'images/banner-pic-main.webp') }}" alt="{{ $product->name??'' }}">
                                                </a>
                                            </div>
                                            <div class="product-bottom">
                                                <h3>
                                                    <a href="{{ route('productmanage.show', $product->id??'') }}">
                                                        {{ $product->name??'' }}
                                                    </a>
                                                </h3>
                                                <div class="rating-size-sec">
                                                    <p class="rating-sec">
                                                        {{ number_format($product->average_rating ?? 4.5, 1) }} 
                                                        <i class="fa-solid fa-star"></i>
                                                    </p>
                                                   <p class="color-size-sec">

@php

$colors = DB::table('product_attribute')
    ->where('proid', $product->id)
    ->where('attribute_id', 11)
    ->count();

$sizes = DB::table('product_attribute')
    ->where('proid', $product->id)
    ->where('attribute_id', 14)
    ->count();

@endphp

@if($colors)
    {{ $colors }} Color 
@endif

@if($sizes)
                                            {{ $sizes }} Sizes
@endif

</p>
                                                </div>
                                                <div class="off-sec">
                                                  @if(!empty($product->discount_price) && $product->price > $product->discount_price)
                                                      <p class="old-rupess">₹ {{ number_format($product->price, 2) }}</p>
                                                      <p class="off-box">
                                                          {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% Off
                                                      </p>
                                                  @elseif(!empty($product->regular_price) && $product->regular_price > $product->price)
                                                      <p class="old-rupess">₹ {{ number_format($product->regular_price, 2) }}</p>
                                                      <p class="off-box">{{ round((($product->regular_price - $product->price) / $product->regular_price) * 100) }}% Off</p>
                                                  @elseif(!empty($product->is_discount) && $product->discount_value > 0)
                                                      <p class="old-rupess">₹ {{ number_format($product->price, 2) }}</p>
                                                      <p class="off-box">
                                                          {{ $product->discount_type == 'percentage' ? $product->discount_value : round(($product->discount_value / ($product->price ?: 1)) * 100) }}% Off
                                                      </p>
                                                  @endif
                                                </div>
                                                <div class="product-bottom-sec">
                                                    <a href="#" class="add-cart-btn wishlist-add-to-cart" 
                                                       data-product-id="{{ $product->id }}"
                                                       data-wishlist-id="{{ $wishlist->id }}">
                                                        Add to Cart
                                                    </a>
                                                    <p class="new-rupess">
                                                        ₹ {{ number_format($product->discount_price ?? $product->price, 2) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-wishlist-msg text-center py-5">
                                <i class="fa-regular fa-heart text-muted mb-3" style="font-size: 40px;"></i>
                                <h4 class="text-muted">Your Wishlist is Empty</h4>
                                <p class="text-muted">Save items you like to your wishlist to easily buy them later.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div> {{-- dashboardDtlsAreaBody --}}
        </div> {{-- dashboardDtlsArea --}}
    </div> {{-- dashboardPosPagesLeftContentonner --}}
</div> {{-- dashboardPagesContentonner --}}

{{-- ================= SCRIPTS ================= --}}
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Add Toastr for better notifications -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
/* Compact styles for Recommended Products & Wishlist section */
.recommended-product-wrapper .product-box,
.other-product-wrapper .product-box {
    max-width: 190px !important;
    height: auto !important;
    min-height: unset !important;
    margin: 0 auto !important;
    border: 1px solid #e0e0e0 !important;
    border-radius: 8px !important;
    padding: 8px !important;
    background: #fff !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
    display: flex !important;
    flex-direction: column !important;
}
.recommended-product-wrapper .product-box-img,
.other-product-wrapper .product-box-img {
    height: 130px !important;
    width: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    overflow: hidden !important;
    padding-top: 15px !important;
}
.recommended-product-wrapper .product-box-img a,
.other-product-wrapper .product-box-img a {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100% !important;
    height: 100% !important;
}
.recommended-product-wrapper .product-box-img img,
.other-product-wrapper .product-box-img img {
    max-height: 100% !important;
    max-width: 100% !important;
    object-fit: contain !important;
    margin: 0 auto !important;
}
.recommended-product-wrapper .product-bottom h3,
.other-product-wrapper .product-bottom h3 {
    font-size: 12px !important;
    line-height: 1.3 !important;
    height: 32px !important;
    margin: 6px 0 !important;
    overflow: hidden !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
}
.recommended-product-wrapper .off-sec,
.other-product-wrapper .off-sec {
    margin-bottom: 4px !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
}
.recommended-product-wrapper .off-sec p,
.other-product-wrapper .off-sec p {
    font-size: 10px !important;
    margin: 0 !important;
}
.recommended-product-wrapper .product-bottom-sec,
.other-product-wrapper .product-bottom-sec {
    margin-top: 6px !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
}
.recommended-product-wrapper .product-bottom-sec .new-rupess,
.other-product-wrapper .product-bottom-sec .new-rupess {
    font-size: 12px !important;
    font-weight: 700 !important;
    margin: 0 !important;
}
.recommended-product-wrapper .product-bottom-sec .add-cart-btn,
.other-product-wrapper .product-bottom-sec .add-cart-btn {
    padding: 3px 6px !important;
    font-size: 10px !important;
    border-radius: 4px !important;
    line-height: 1.2 !important;
}
.other-product-wrapper .rating-size-sec p {
    font-size: 10px !important;
    margin: 0 !important;
}
.other-product-wrapper .rating-size-sec {
    display: flex !important;
    justify-content: space-between !important;
    margin-bottom: 4px !important;
}

.cart-list-left-img {
    width: 180px;        /* image box width */
    height: 180px;       /* image box height */
    flex-shrink: 0;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cart-list-left-img img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;   /* image stretch nahi hoga */
}
    .quantity-sub-sec {
    display: inline-flex;
    align-items: center;
    border: 1px solid #dcdcdc;
    border-radius: 30px;
    overflow: hidden;
    background: #f8f8f8;
}

/* minus & plus buttons */
.quantity-sub-sec .sub,
.quantity-sub-sec .add {
    width: 36px;
    height: 36px;
    border: none;
    background: transparent;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* hover effect */
.quantity-sub-sec .sub:hover,
.quantity-sub-sec .add:hover {
    background: #eaeaea;
}

/* quantity number */
.quantity-sub-sec .count {
    min-width: 40px;
    text-align: center;
    font-weight: 600;
    font-size: 15px;
}

/* Modern Wishlist Action Button */
.wishlist-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 30px;
    border: 1px solid #ff4d4d;
    background: #fff0f0;
    color: #ff4d4d !important;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.wishlist-action-btn:hover {
    background: #ff4d4d;
    color: #ffffff !important;
}

.wishlist-action-btn i {
    font-size: 15px;
}

</style>
<script>
$(document).ready(function(){
    // Configure Toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Initialize owl carousel for wishlist
    if ($(".other-product-wrapper").length) {
        var itemCount = $(".other-product-wrapper .item").length;
        $(".other-product-wrapper").owlCarousel({
            loop: itemCount > 4 ? true : false,   // Only loop if more than 4 items
            margin: 20,
            nav: false,
            dots: false,
            autoplay: itemCount > 4 ? true : false,
            autoplayTimeout: 3000,
            responsive: {
                0: { items: 1 },
                600: { items: 2 },
                1000: { items: 4 }
            }
        });
    }

    // Initialize owl carousel for recommended products
    if ($(".recommended-product-wrapper").length) {
        var recItemCount = $(".recommended-product-wrapper .item").length;
        $(".recommended-product-wrapper").owlCarousel({
            loop: true,
            margin: 20,
            nav: false,
            dots: false,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            responsive: {
                0: { items: 1 },
                600: { items: 2 },
                1000: { items: 4 }
            }
        });
    }

    // Function to format number with commas
    function formatNumber(num) {
        if (num === undefined || num === null) return '0.00';
        var numStr = parseFloat(num).toFixed(2);
        var parts = numStr.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return parts.join('.');
    }
    
    // Function to format currency with rupee symbol
    function formatCurrency(num) {
        return '₹' + formatNumber(num);
    }

    // Function to update cart totals
    function updateGrandTotal() {
        var totalItems = 0;
        var totalQuantity = 0;
        var grandTotal = 0;

        $('li.cart-details-list-box').each(function() {
            var row = $(this);
            var quantity = parseInt(row.find('.qty-display').text()) || 0;
            var gstPercent = parseFloat(row.data('gst-percent')) || 5;
            var courierCharge = parseFloat(row.data('courier-charge')) || 0;
            
            if (quantity > 0) {
                totalItems++;
                totalQuantity += quantity;
                
                var mainPrice = parseFloat(row.data('main-product-price') || 0);
                var itemSubtotal = mainPrice * quantity;
                var itemGST = itemSubtotal * (gstPercent / 100);
                grandTotal += itemSubtotal + itemGST + courierCharge;
            }
        });

        // Calculate savings (14% of grand total)
        var totalSavings = grandTotal * 0.14;

        // Update totals in UI
        $('.total-items-count').text(totalItems);
        $('.total-quantity-sum').text(totalQuantity);
        $('.cart-grand-total').text('₹' + formatNumber(grandTotal));
        $('.total-savings').text('₹' + formatNumber(totalSavings));
    }

    // Function to update single item price
    function updateItemPrice(row, newQty) {
        var mainPrice = parseFloat(row.data('main-product-price') || 0);
        
        var itemTotal = mainPrice * newQty;
        var subtotal = itemTotal;
        
        row.find('.item-display-qty').text('X ' + newQty);
        row.find('.item-total-price').text('₹' + formatNumber(itemTotal));
        row.find('.item-subtotal').text('₹ ' + formatNumber(subtotal));
        
        // Update sub button icon
        var subBtn = row.find('.sub.update-qty');
        if(newQty <= 1) {
            subBtn.html('<i class="fa-solid fa-trash text-danger" style="font-size: 14px;"></i>');
        } else {
            subBtn.html('-');
        }
        
        // Update unit price display
        var unitPriceEl = row.find('.unit-price');
        if(unitPriceEl.length) {
            var unitPriceHtml = '₹' + formatNumber(mainPrice);
            if(newQty > 1) {
                unitPriceHtml += '<small class="text-muted" style="font-size: 12px;font-weight: 500;"> × ' + newQty + ' = ' + formatCurrency(itemTotal) + '</small>';
            }
            unitPriceEl.html(unitPriceHtml);
        }
    }

    // INCREMENT/DECREMENT - NO LIMITS, CAN INCREASE INFINITELY
    $(document).on('click', '.update-qty', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var button = $(this);
        var row = button.closest('li.cart-details-list-box');
        var id = row.data('id');
        var isDb = button.data('type');
        var qtySpan = row.find('.qty-display');
        var currentQty = parseInt(qtySpan.text()) || 1;
        
        // Calculate new quantity - NO MAX LIMIT, only minimum 1
        var newQty = currentQty;
        if (button.hasClass('add')) {
            newQty = currentQty + 1;
            console.log('Increasing quantity from', currentQty, 'to', newQty);
        } else if (button.hasClass('sub')) {
            if (currentQty > 1) {
                newQty = currentQty - 1;
                console.log('Decreasing quantity from', currentQty, 'to', newQty);
            } else {
                console.log('Quantity is 1, triggering delete');
                var productName = button.data('product-name') || 'this item';
                var delBtn = $('<button style="display:none" class="delete-cart"></button>')
                    .data('id', id)
                    .data('type', isDb)
                    .data('product-name', productName);
                button.after(delBtn);
                delBtn.trigger('click');
                return;
            }
        }
        
        // Store original button text
        var originalText = button.html();
        
        // Show loading state
        button.prop('disabled', true);
        button.html('<i class="fa fa-spinner fa-spin"></i>');
        
        // Update UI immediately for better user experience
        qtySpan.text(newQty);
        row.data('main-product-quantity', newQty);
        updateItemPrice(row, newQty);
        updateGrandTotal();
        
        // Send AJAX request to update server
        $.ajax({
            url: "{{ route('vshop.cart.updateQty') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                cart_item_id: id,
                is_db_item: isDb,
                quantity: newQty
            },
            success: function(response) {
                console.log('Server response:', response);
                
                // Restore button state
                button.prop('disabled', false);
                if (button.hasClass('add')) {
                    button.html('+');
                } else {
                    button.html(newQty <= 1 ? '<i class="fa-solid fa-trash text-danger" style="font-size: 14px;"></i>' : '-');
                }
                
                if (response.success) {
                    // Optional: Show success message
                    // toastr.success('Quantity updated');
                } else {
                    toastr.warning(response.message || 'Updated locally, but server sync failed');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                
                // Restore button state
                button.prop('disabled', false);
                if (button.hasClass('add')) {
                    button.html('+');
                } else {
                    button.html(newQty <= 1 ? '<i class="fa-solid fa-trash text-danger" style="font-size: 14px;"></i>' : '-');
                }
                
                // Show error but keep UI updated - don't revert
                toastr.warning('Updated locally, but server connection failed');
            }
        });
    });

    // Size change handler
    $(document).on('change', '.size-radio', function() {
        if($(this).is(':checked')) {
            var radio = $(this);
            var cartItemId = radio.data('cart-item-id');
            var sizeId = radio.data('size-id');
            var sizeName = radio.data('size-name');
            var sizePrice = parseFloat(radio.data('size-price'));
            var sizeQuantity = parseInt(radio.data('size-quantity'));
            
            var row = radio.closest('li.cart-details-list-box');
            var currentQuantity = parseInt(row.find('.qty-display').text()) || 1;
            
            // Update active class
            row.find('.quantity-box').removeClass('active');
            radio.closest('.quantity-box').addClass('active');
            
            // Update price display only - don't limit quantity
            row.data('main-product-price', sizePrice);
            row.find('.price-value').text(formatNumber(sizePrice));
            row.find('.item-display-price').text('₹' + formatNumber(sizePrice));
            
            // Update UI with new price (keep same quantity)
            updateItemPrice(row, currentQuantity);
            updateGrandTotal();
            
            // Show loading on the radio
            var originalText = radio.closest('.quantity-box').find('span').html();
            radio.closest('.quantity-box').append('<span class="size-loading ms-1"><i class="fa fa-spinner fa-spin"></i></span>');
            
            // AJAX call to update size in database
            $.ajax({
                url: "{{ route('vshop.cart.updateSize') }}",
                type: "POST",
                data: {
                    cart_item_id: cartItemId,
                    size_id: sizeId,
                    size_name: sizeName,
                    price: sizePrice,
                    quantity: sizeQuantity,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    toastr.success('Size updated successfully');
                    row.find('.size-loading').remove();
                },
                error: function(xhr) {
                    console.error('Size update error:', xhr);
                    toastr.error('Failed to update size');
                    row.find('.size-loading').remove();
                }
            });
        }
    });

    // Delete item handler
    $(document).on('click', '.delete-cart', function(e){
        e.preventDefault();
        e.stopPropagation();
        
        var button = $(this);
        var row = button.closest('li.cart-details-list-box');
        var id = row.data('id') || button.data('id');
        var isDb = button.data('type');
        var productName = button.data('product-name') || 'this item';
        
        Swal.fire({
            title: 'Remove from Cart?',
            html: `Are you sure you want to remove <strong>${productName}</strong> from your cart?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            reverseButtons: true,
            width: '400px'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Removing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: "{{ route('vshop.cart.remove') }}",
                    type: "POST",
                    data: {
                        cart_item_id: id,
                        is_db_item: isDb,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res){
                        Swal.close();
                        
                        if(res.success){
                            Swal.fire({
                                title: 'Removed!',
                                text: 'Item has been removed from your cart',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            
                            row.fadeOut(300, function(){
                                $(this).remove();
                                updateGrandTotal();
                                
                                if($('li.cart-details-list-box').length === 0) {
                                    setTimeout(function(){
                                        $('.cart-sec').fadeOut(300, function() {
                                            $('.emply-cart-sec').fadeIn(300);
                                        });
                                    }, 1500);
                                }
                            });
                        }
                    },
                    error: function(xhr, status, error){
                        Swal.close();
                        console.error('Delete error:', error);
                        
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while removing the item. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
        
        return false;
    });

    // Move to wishlist handler
    $(document).on('click', '.moveToWishlist', function(e){
        e.preventDefault();
        
        var button = $(this);
        var id = button.data('id');
        var row = button.closest('li.cart-details-list-box');
        
        // Store original text
        var originalText = button.text();
        button.html('<i class="fa fa-spinner fa-spin"></i> Moving...').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('vshop.cart.moveToWishlist') }}",
            type: "POST",
            data: {
                cart_item_id: id,
                _token: "{{ csrf_token() }}"
            },
            success: function(res){
                if(res.success){
                    row.fadeOut(300, function() {
                        $(this).remove();
                        updateGrandTotal();
                        
                        if($('li.cart-details-list-box').length === 0) {
                            $('.cart-sec').fadeOut(300, function() {
                                $('.emply-cart-sec').fadeIn(300);
                            });
                        }
                    });
                    
                    toastr.success('Item moved to wishlist');
                    
                    // Reload wishlist section after 1 second
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    button.html(originalText).prop('disabled', false);
                    toastr.error(res.message || 'Failed to move item');
                }
            },
            error: function() {
                button.html(originalText).prop('disabled', false);
                toastr.error('Failed to move item to wishlist');
            }
        });
    });

    // Remove from wishlist handler
    $(document).on('click', '.wishlist-remove', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var wishlistId = button.data('wishlist-id');
        var item = button.closest('.other-product-box');
        
        Swal.fire({
            title: 'Remove from Wishlist?',
            text: 'Are you sure you want to remove this item from your wishlist?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isConfirmed) {
                button.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('vshop.wishlist.remove', '') }}/" + wishlistId,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if(res.success) {
                            toastr.success('Item removed from wishlist');
                            item.fadeOut(300, function() {
                                $(this).remove();
                                if($('.other-product-wrapper .other-product-box').length === 0) {
                                    location.reload();
                                }
                            });
                        } else {
                            button.html('<i class="fa-solid fa-heart text-danger"></i>').prop('disabled', false);
                            toastr.error(res.message || 'Failed to remove item');
                        }
                    },
                    error: function() {
                        button.html('<i class="fa-solid fa-heart text-danger"></i>').prop('disabled', false);
                        toastr.error('Failed to remove item from wishlist');
                    }
                });
            }
        });
    });

    // Add to cart from wishlist handler
    $(document).on('click', '.wishlist-add-to-cart', function(e) {
        e.preventDefault();
        
        var btn = $(this);
        var productId = btn.data('product-id');
        var wishlistId = btn.data('wishlist-id');
        var productBox = btn.closest('.other-product-box');
        var originalText = btn.text();
        
        // Get product price from the displayed price
        var priceText = productBox.find('.new-rupess').text().replace('₹', '').replace(/,/g, '').trim();
        var price = parseFloat(priceText) || 0;
        
        console.log('Adding to cart:', {productId, price, wishlistId});
        
        btn.text('Adding...').prop('disabled', true);
        
        // Get CSRF token
        var token = $('meta[name="csrf-token"]').attr('content');
        if (!token) {
            token = "{{ csrf_token() }}";
        }
        
        $.ajax({
            url: "{{ route('vshop.cart.add') }}",
            type: "POST",
            data: {
                _token: token,
                product_id: productId,
                quantity: 1,
                price: price
            },
            dataType: 'json',
            success: function(res) {
                console.log('Add to cart response:', res);
                
                if(res.success) {
                    toastr.success('Product added to cart successfully!');
                    
                    // Update cart count in header
                    if($('.cart-count').length) {
                        $('.cart-count').text(res.cart_count);
                    }
                    
                    // Remove from wishlist after adding to cart
                    if(wishlistId) {
                        $.ajax({
                            url: "{{ route('vshop.wishlist.remove', '') }}/" + wishlistId,
                            type: "DELETE",
                            data: {
                                _token: token
                            },
                            success: function() {
                                productBox.fadeOut(300, function() {
                                    $(this).remove();
                                    if($('.other-product-wrapper .other-product-box').length === 0) {
                                        location.reload();
                                    }
                                });
                            }
                        });
                    } else {
                        productBox.fadeOut(300);
                    }
                    
                    btn.text(originalText).prop('disabled', false);
                } else {
                    toastr.error(res.message || 'Failed to add to cart');
                    btn.text(originalText).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('Add to cart error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    response: xhr.responseJSON,
                    error: error
                });
                
                var errorMsg = 'Failed to add product to cart';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if(xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                
                toastr.error(errorMsg);
                btn.text(originalText).prop('disabled', false);
            }
        });
    });

    // Add to cart from recommended products handler
    $(document).on('click', '.recommended-add-to-cart', function(e) {
        e.preventDefault();
        
        var btn = $(this);
        var productId = btn.data('product-id');
        var productBox = btn.closest('.other-product-box');
        var originalText = btn.text();
        
        var priceText = productBox.find('.new-rupess').text().replace('₹', '').replace(/,/g, '').trim();
        var price = parseFloat(priceText) || 0;
        
        btn.text('Adding...').prop('disabled', true);
        
        var token = $('meta[name="csrf-token"]').attr('content');
        if (!token) {
            token = "{{ csrf_token() }}";
        }
        
        $.ajax({
            url: "{{ route('vshop.cart.add') }}",
            type: "POST",
            data: {
                _token: token,
                product_id: productId,
                quantity: 1,
                price: price
            },
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    toastr.success('Product added to cart successfully!');
                    if($('.cart-count').length) {
                        $('.cart-count').text(res.cart_count);
                    }
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(res.message || 'Failed to add to cart');
                    btn.text(originalText).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                var errorMsg = 'Failed to add product to cart';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
                btn.text(originalText).prop('disabled', false);
            }
        });
    });


    // Initial grand total calculation
    updateGrandTotal();

    console.log('Cart page loaded. Found', $('.update-qty').length, 'quantity buttons');

});
</script>

@endsection
