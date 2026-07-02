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
    @php
        $images = [];
        if (is_array($banner->banner_image)) {
            $images = $banner->banner_image;
        } elseif (is_string($banner->banner_image)) {
            if (str_starts_with($banner->banner_image, '[')) {
                $images = json_decode($banner->banner_image, true);
            } else {
                $images = [$banner->banner_image];
            }
        }
    @endphp

    @if(!empty($images) && is_array($images))
        @foreach($images as $img)
            @if(!empty($img))
            <div class="banner-img item">
                <img src="{{ asset($img) }}" alt="{{ $banner->banner_title }}">
            </div>
            @endif
        @endforeach
    @endif
@empty
<div class="banner-img item">
<img src="{{ asset('images/banner-pic-main.png') }}">
</div>
@endforelse

</div>
</section>

{{-- ================= OVERALL RATING ================= --}}
@php
    $avgRating = $avgRating ?? 0;
    $totalReviews = $totalReviews ?? 0;
    $ratingBreakdown = $ratingBreakdown ?? [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
@endphp
<div class="container mt-4 mb-2">
    <div class="d-flex justify-content-end">
        <div class="seller-rating-dropdown-wrapper" style="position: relative; display: inline-block;">
            
            {{-- Button --}}
            <div class="d-inline-flex align-items-center justify-content-center" onclick="toggleRatingPopup(event)" style="cursor: pointer; border: 1px solid #D5D9D9; border-radius: 4px; padding: 2px 5px; background-color: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.05); user-select: none;">
                <span class="fw-bold me-1" style="color: #0F1111; font-size: 12px;">{{ number_format($avgRating, 1) }}</span>
                <div class="text-warning me-1 d-flex align-items-center" style="gap: 1px;">
                    @for($i=1; $i<=5; $i++)
                        @if($i <= $avgRating)
                            <i class="fa fa-star" style="font-size: 11px !important;"></i>
                        @elseif($i - 0.5 <= $avgRating)
                            <i class="fa fa-star-half-alt" style="font-size: 11px !important;"></i>
                        @else
                            <i class="far fa-star" style="font-size: 11px !important;"></i>
                        @endif
                    @endfor
                </div>
                <i class="fas fa-chevron-down text-muted" style="font-size: 8px !important; padding-top: 1px;"></i>
            </div>
            
            {{-- Popup Menu --}}
            <div id="ratingDropdownMenu" class="p-3 border bg-white" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 5px; width: 300px; z-index: 1000; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <div class="d-flex align-items-center mb-1">
                    <div class="text-warning me-2" style="font-size: 16px;">
                        @for($i=1; $i<=5; $i++)
                            @if($i <= $avgRating)
                                <i class="fa fa-star"></i>
                            @elseif($i - 0.5 <= $avgRating)
                                <i class="fa fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="mb-0 fw-bold me-1" style="color: #0F1111; font-size: 16px;">{{ number_format($avgRating, 1) }} out of 5</span>
                </div>
                <p class="text-muted mb-3" style="font-size: 13px;">{{ $totalReviews }} global ratings</p>
                
                <div class="rating-breakdown">
                    @foreach([5, 4, 3, 2, 1] as $star)
                    <div class="d-flex align-items-center mb-2" style="font-size: 13px; cursor: default;">
                        <span class="text-primary me-2 text-nowrap" style="width: 45px; text-decoration: none;">{{ $star }} star</span>
                        <div class="progress flex-grow-1 me-2" style="height: 14px; border-radius: 4px; background-color: #F0F2F2; border: 1px solid #D5D9D9; overflow: hidden;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $ratingBreakdown[$star] ?? 0 }}%; background-color: #FFA41C; border-radius: 0;" aria-valuenow="{{ $ratingBreakdown[$star] ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="text-primary text-end" style="width: 35px; font-size: 13px;">{{ $ratingBreakdown[$star] ?? 0 }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
    function toggleRatingPopup(event) {
        event.stopPropagation();
        var menu = document.getElementById('ratingDropdownMenu');
        if (menu.style.display === 'none' || menu.style.display === '') {
            menu.style.display = 'block';
        } else {
            menu.style.display = 'none';
        }
    }

    document.addEventListener('click', function(event) {
        var wrapper = document.querySelector('.seller-rating-dropdown-wrapper');
        var menu = document.getElementById('ratingDropdownMenu');
        if (wrapper && !wrapper.contains(event.target)) {
            if (menu) menu.style.display = 'none';
        }
    });
</script>

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