<div class="product-list-right product-col-view">
<div class="row">

@forelse($products as $product)

<div class="col-lg-3 col-md-4 col-sm-6 product-box-outer">
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
            <i class="{{ in_array($product->id,$wishlistIds ?? [])
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
<a href="{{ route('productmanage.show',$product->id) }}">
<img src="{{ $product->thumbnail
? asset($product->thumbnail)
: asset('vtravelassets/images/banner-pic-main.webp') }}">
</a>
</div>

<div class="product-bottom">

{{-- Name --}}

<h3>
<a href="{{ route('productmanage.show',$product->id) }}" title="{{ $product->name }}">
{{ \Illuminate\Support\Str::limit($product->name, 28) }}
</a>
</h3>

{{-- Rating + Seller/Brand --}}

<div class="rating-size-sec">

<div class="amazon-rating-sec">
    <span class="rating-num">{{ number_format($product->average_rating ?? 0, 1) }}</span>
    <span class="stars">
        @for($i=1;$i<=5;$i++)
            <i class="fa-solid fa-star {{ $i <= round($product->average_rating ?? 0) ? 'text-warning' : 'text-secondary' }}"></i>
        @endfor
    </span>
    <a href="{{ route('productmanage.show', $product->id) }}#reviews" class="rating-count">
        ({{ $product->total_reviews ?? 0 }})
    </a>
</div>

<p class="color-size-sec">

@if($product->created_by === 'seller')
{{ $product->seller->name ?? 'Seller Store' }}
@else
{{ $product->brand->name ?? $product->brand_name ?? 'Store' }}
@endif

</p>

</div>

{{-- Discount --}}
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

@if($regular > $price || $discountPct > 0)
<div class="off-sec">
<p class="old-rupess">₹ {{ number_format($regular,2) }}</p>

<p class="off-box">
{{ $discountPct }}% Off
</p>
</div>
@endif

{{-- Price + Button --}}

<div class="product-bottom-sec">

<button class="add-cart-btn"
onclick="window.location.href='{{ route('productmanage.show',$product->id) }}'">
View Details </button>

<p class="new-rupess">
₹ {{ number_format($price,2) }}
</p>

</div>

</div>
</div>
</div>

@empty

<div class="col-12">
<div class="alert alert-warning">
No products found with selected filters.
</div>
</div>
@endforelse

</div>
</div>


<div class="product-list-right product-list-view">
    <div class="row">
                                                        <div class="col-lg-12 product-box-outer">
                                                            <div class="product-list-box">
                                                                <div class="product-list-box-top">
                                                                    <div class="product-list-box-left">
                                                                        <div class="share-wishlist-sec">
                                                                            <a href="#">
                                                                                <i class="fa-solid fa-share-nodes"></i>
                                                                            </a>
                                                                            <a href="#">
                                                                                <i class="fa-solid fa-heart"></i>
                                                                            </a>
                                                                        </div>
                                                                        <div class="product-image-sec">
                                                                            <div class="product-image-wrapper-one">
                                                                                <div class="product-image-one-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="product-image-one-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="product-image-one-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="product-image-one-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="product-image-wrapper-two">
                                                                                <div class="product-image-two-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="product-image-two-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="product-image-two-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="product-image-two-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="product-list-box-right">
                                                                        <div class="product-heading">
                                                                            <div class="product-heading-left">
                                                                                <h3>Yoga Mat, 8 mm thick, 173 x 61 cm</h3>
                                                                                <ul>
                                                                                    <li>
                                                                                        <a href="#">Visit Selinium Yoga store</a>
                                                                                    </li>
                                                                                    <li>/</li>
                                                                                    <li>Yoga Accessories</li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                        <div class="rating-sec">
                                                                            <div class="rating-left">
                                                                                <ul>
                                                                                    <li>
                                                                                        4.6
                                                                                        <i class="fa-solid fa-star"></i>
                                                                                    </li>
                                                                                    <li class="blackColor">
                                                                                        <p class="old-rupess">₹ 6,700.00</p>
                                                                                    </li>
                                                                                    <li>
                                                                                        <p class="off-box">14 % Off</p>
                                                                                    </li>
                                                                                    <li>
                                                                                        <p class="new-rupess">₹ 5,900.00</p>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                        <div class="quantity-sec">
                                                                            <label class="quantity-box">
                                                                                <input type="radio" name="radio1" checked>
                                                                                <span>8 mm</span>
                                                                            </label>
                                                                            <label class="quantity-box">
                                                                                <input type="radio" name="radio1">
                                                                                <span>6 mm</span>
                                                                            </label>
                                                                            <label class="quantity-box">
                                                                                <input type="radio" name="radio1">
                                                                                <span>4 mm</span>
                                                                            </label>
                                                                            <label class="quantity-box">
                                                                                <input type="radio" name="radio1">
                                                                                <span>2 mm</span>
                                                                            </label>
                                                                        </div>
                                                                        <div class="color-select-sec">
                                                                            <label class="color-box">
                                                                                <input type="radio" checked name="radio2">
                                                                                <span class="color-circle merun-box"></span>
                                                                            </label>
                                                                            <label class="color-box">
                                                                                <input type="radio" name="radio2">
                                                                                <span class="color-circle green-box"></span>
                                                                            </label>
                                                                            <label class="color-box">
                                                                                <input type="radio" name="radio2">
                                                                                <span class="color-circle chocolate-box"></span>
                                                                            </label>
                                                                            <p>( Barbarian Red )</p>
                                                                        </div>
                                                                        <div class="product-dtl-sec">
                                                                            <p>Expected Delivery : <span class="blackColor">16 - 19 June 25</span></p>
                                                                        </div>
                                                                        <div class="product-col-bottom-sec">
                                                                            <div class="quantity-sub-sec">
                                                                                <button type="button" id="sub" class="sub">-</button>
                                                                                <input class="count" type="text" id="1" value="1" min="1" max="100">
                                                                                <button type="button" id="add" class="add">+</button>
                                                                            </div>
                                                                            <a href="#" class="add-cart-btn">Add to Cart</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 product-box-outer">
                                                            <div class="product-list-box">
                                                                <div class="product-list-box-top">
                                                                    <div class="product-list-box-left">
                                                                        <div class="share-wishlist-sec">
                                                                            <a href="#">
                                                                                <i class="fa-solid fa-share-nodes"></i>
                                                                            </a>
                                                                            <a href="#">
                                                                                <i class="fa-solid fa-heart"></i>
                                                                            </a>
                                                                        </div>
                                                                        <div class="product-image-sec">
                                                                            <div class="product-image-wrapper-one">
                                                                                <div class="product-image-one-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="product-image-one-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="product-image-one-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="product-image-one-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="product-image-wrapper-two">
                                                                                <div class="product-image-two-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="product-image-two-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="product-image-two-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="product-image-two-box">
                                                                                    <div class="product-image-one-box-inner">
                                                                                        <img src="images/banner-pic-main.webp"
                                                                                            alt="">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="product-list-box-right">
                                                                        <div class="product-heading">
                                                                            <div class="product-heading-left">
                                                                                <h3>Yoga Mat, 8 mm thick, 173 x 61 cm</h3>
                                                                                <ul>
                                                                                    <li>
                                                                                        <a href="#">Visit Selinium Yoga store</a>
                                                                                    </li>
                                                                                    <li>/</li>
                                                                                    <li>Yoga Accessories</li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                        <div class="rating-sec">
                                                                            <div class="rating-left">
                                                                                <ul>
                                                                                    <li>
                                                                                        4.6
                                                                                        <i class="fa-solid fa-star"></i>
                                                                                    </li>
                                                                                    <li class="blackColor">
                                                                                        <p class="old-rupess">₹ 6,700.00</p>
                                                                                    </li>
                                                                                    <li>
                                                                                        <p class="off-box">14 % Off</p>
                                                                                    </li>
                                                                                    <li>
                                                                                        <p class="new-rupess">₹ 5,900.00</p>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                        <div class="quantity-sec">
                                                                            <label class="quantity-box">
                                                                                <input type="radio" name="radio1" checked>
                                                                                <span>8 mm</span>
                                                                            </label>
                                                                            <label class="quantity-box">
                                                                                <input type="radio" name="radio1">
                                                                                <span>6 mm</span>
                                                                            </label>
                                                                            <label class="quantity-box">
                                                                                <input type="radio" name="radio1">
                                                                                <span>4 mm</span>
                                                                            </label>
                                                                            <label class="quantity-box">
                                                                                <input type="radio" name="radio1">
                                                                                <span>2 mm</span>
                                                                            </label>
                                                                        </div>
                                                                        <div class="color-select-sec">
                                                                            <label class="color-box">
                                                                                <input type="radio" checked name="radio2">
                                                                                <span class="color-circle merun-box"></span>
                                                                            </label>
                                                                            <label class="color-box">
                                                                                <input type="radio" name="radio2">
                                                                                <span class="color-circle green-box"></span>
                                                                            </label>
                                                                            <label class="color-box">
                                                                                <input type="radio" name="radio2">
                                                                                <span class="color-circle chocolate-box"></span>
                                                                            </label>
                                                                            <p>( Barbarian Red )</p>
                                                                        </div>
                                                                        <div class="product-dtl-sec">
                                                                            <p>Expected Delivery : <span class="blackColor">16 - 19 June 25</span></p>
                                                                        </div>
                                                                        <div class="product-col-bottom-sec">
                                                                            <div class="quantity-sub-sec">
                                                                                <button type="button" id="sub" class="sub">-</button>
                                                                                <input class="count" type="text" id="1" value="1" min="1" max="100">
                                                                                <button type="button" id="add" class="add">+</button>
                                                                            </div>
                                                                            <a href="#" class="add-cart-btn">Add to Cart</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
</div>

{{-- Pagination --}}

<div class="mt-4">
{{ $products->links('pagination::bootstrap-5') }}
</div>
