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

<div class="container mt-5 mb-4">
<h4 class="mb-3" style="font-weight: 600; color: #1a253c; border-left: 4px solid #004392; padding-left: 10px;">
    Top Trending
</h4>
<div class="row">

@if($products->count() > 0)
    @foreach($products->take(4) as $product)
    <div class="col-6 col-md-4 col-lg-2 mb-4">
        @include('vshop.partials.product_card', ['product' => $product])
    </div>
    @endforeach
@else
    <div class="col-12">
        <p class="text-muted">No trending products available.</p>
    </div>
@endif

</div>
</div>

{{-- ================= BEST DISCOUNT ================= --}}

<div class="container mb-4">
<h4 class="mb-3" style="font-weight: 600; color: #1a253c; border-left: 4px solid #004392; padding-left: 10px;">
    Best Discount
</h4>
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
        <p class="text-muted">No discounted products available.</p>
    </div>
@endif

</div>
</div>

{{-- ================= ALL PRODUCTS ================= --}}

<div class="container mb-4">
<h4 class="mb-3" style="font-weight: 600; color: #1a253c; border-left: 4px solid #004392; padding-left: 10px;">
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