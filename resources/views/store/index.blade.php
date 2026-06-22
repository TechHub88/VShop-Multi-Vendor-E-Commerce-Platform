@extends('vshop.main')

@section('title', $seo->home_title ?? ($seller->name ?? $seller->org_name))

@section('content')

{{-- Always use vshop product route --}}
@php
$routeName = 'productmanage.show';
@endphp

<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
    <div class="dashboardPosPagesLeftContentonner">
        <div class="dashboardDtlsArea">
            <div class="dashboardDtlsAreaBody">

                {{-- ================= STORE BANNER ================= --}}

                <section class="travel-banner-sec">
<div class="banner-wrapper owl-carousel">

@forelse($banners as $banner)
<div class="banner-img item">
<img src="{{ asset($banner->banner_image) }}" alt="{{ $banner->banner_title }}">
</div>
@empty
<div class="banner-img item">
<img src="{{ asset('images/banner-pic-main.webp') }}">
</div>
@endforelse

</div>
</section>

{{-- ================= TOP TRENDING ================= --}}

<section class="home-seller-sec">
<div class="home-seller-header">
<h2>Top Trending</h2>

<div class="discount-box">
<div class="discount-box-inner">
<span class="discount-top">Discount</span>
<p>up to <span>75%</span> off</p>
</div>
</div>

<p>Top Trending Items At The Best Price Just A Click Away, Order Now !</p>
</div>

<div class="home-seller-bottom">
<div class="row">

@if($products->count() > 0)
    @foreach($products->take(4) as $product)

    @php
    $regular = $product->regular_price ?? 0;
    $price = $product->price;
    $discount = $regular > $price ? round((($regular-$price)/$regular)*100) : 0;
    @endphp

    <div class="col-lg-3 col-md-4 col-sm-6 product-box-outer">
    <div class="product-box">

    <div class="product-box-img">
    <a href="{{ route($routeName,$product->id) }}">
    <img src="{{ asset($product->thumbnail) }}">
    </a>
    </div>

    <div class="product-bottom">
    <h3>
    <a href="{{ route($routeName,$product->id) }}">
    {{ $product->name }}
    </a>
    </h3>

    <div class="rating-size-sec">
    <p class="rating-sec">
    {{ number_format($product->reviews_avg_rating ?? 0,1) }}
    <i class="fa-solid fa-star"></i>
    </p>

    <p class="color-size-sec">
    {{ $product->variants_count ?? 1 }} Variants
    </p>
    </div>

    @if($discount>0)
    <div class="off-sec">
    <p class="old-rupess">₹ {{ number_format($regular,2) }}</p>
    <p class="off-box">{{ $discount }} % Off</p>
    </div>
    @endif

    <div class="product-bottom-sec">
    <a href="{{ route($routeName,$product->id) }}" class="add-cart-btn">
    View Details
    </a>

    <p class="new-rupess">₹ {{ number_format($price,2) }}</p>
    </div>

    </div>
    </div>
    </div>

    @endforeach
@else
    <div class="col-12">
        <p class="text-center">No trending products available.</p>
    </div>
@endif

</div>
</div>
</section>

{{-- ================= BEST DISCOUNT ================= --}}

<section class="home-seller-sec">
<div class="home-seller-header">
<h2>Best Discount</h2>

<div class="discount-box">
<div class="discount-box-inner">
<span class="discount-top">Discount</span>
<p>up to <span>75%</span> off</p>
</div>
</div>

<p>Crazy Discount ! Best Price Available Just A Click Away, Order Now !</p>
</div>

<div class="home-seller-bottom">
<div class="row">

@if($products->count() > 0)
    @php
        $discountProducts = $products->sortByDesc(function($p){
            return ($p->regular_price - $p->price);
        })->take(4);
    @endphp

    @foreach($discountProducts as $product)

    @php
    $regular = $product->regular_price ?? 0;
    $price = $product->price;
    $discount = $regular > $price ? round((($regular-$price)/$regular)*100) : 0;
    @endphp

    <div class="col-lg-3 col-md-4 col-sm-6 product-box-outer">
    <div class="product-box">

    <div class="product-box-img">
    <a href="{{ route($routeName,$product->id) }}">
    <img src="{{ asset($product->thumbnail) }}">
    </a>
    </div>

    <div class="product-bottom">
    <h3>
    <a href="{{ route($routeName,$product->id) }}">
    {{ $product->name }}
    </a>
    </h3>

    @if($discount>0)
    <div class="off-sec">
    <p class="old-rupess">₹ {{ number_format($regular,2) }}</p>
    <p class="off-box">{{ $discount }} % Off</p>
    </div>
    @endif

    <div class="product-bottom-sec">
    <a href="{{ route($routeName,$product->id) }}" class="add-cart-btn">
    View Details
    </a>

    <p class="new-rupess">₹ {{ number_format($price,2) }}</p>
    </div>

    </div>
    </div>
    </div>

    @endforeach
@else
    <div class="col-12">
        <p class="text-center">No discounted products available.</p>
    </div>
@endif

</div>
</div>
</section>

{{-- ================= ALL PRODUCTS ================= --}}

<div class="container">
<h4 class="mb-4">
Best Sellers from {{ $seller->name ?? $seller->org_name }}
</h4>

<div class="row">

@forelse($products as $product)

<div class="col-md-3 mb-4">
<div class="card">

<a href="{{ route($routeName,$product->id) }}">
<img src="{{ asset($product->thumbnail) }}"
class="card-img-top"
style="height:220px;object-fit:cover;">
</a>

<div class="card-body">
<h6>{{ $product->name }}</h6>
<p>₹ {{ number_format($product->price,2) }}</p>
</div>

</div>
</div>

@empty
<div class="col-12">
    <p class="text-center">No products found for this store.</p>
</div>
@endforelse

</div>

{{ $products->links() }}
</div>

            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    if ($(".banner-wrapper").length) {
        $(".banner-wrapper").owlCarousel({
            items: 1,
            loop: true,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            nav: false,
            dots: true
        });
    }
});
</script>

@endsection