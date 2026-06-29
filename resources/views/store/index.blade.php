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

<section class="home-seller-sec mt-4">
<div class="home-seller-header">
<h2>Top Trending</h2>
</div>

<div class="home-seller-bottom">
<div class="row">

@if($products->count() > 0)
    @foreach($products->take(4) as $product)
    <div class="col-6 col-md-4 col-lg-2 mb-4">
        @include('vshop.partials.product_card', ['product' => $product])
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

<section class="home-seller-sec mt-4">
<div class="home-seller-header">
<h2>Best Discount</h2>
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
    <div class="col-6 col-md-4 col-lg-2 mb-4">
        @include('vshop.partials.product_card', ['product' => $product])
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

<div class="container mt-5">
<h4 class="mb-4">
Best Sellers from {{ $seller->name ?? $seller->org_name }}
</h4>

<div class="row">

@forelse($products as $product)

<div class="col-6 col-md-4 col-lg-2 mb-4">
    @include('vshop.partials.product_card', ['product' => $product])
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