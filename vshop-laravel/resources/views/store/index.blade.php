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

{{-- ================= SELLER IDENTITY & OVERALL RATING ================= --}}
@php
    $avgRating = $avgRating ?? 0;
    $totalReviews = $totalReviews ?? 0;
@endphp
<div class="container mt-4 mb-4">
    <div class="d-flex align-items-center bg-white p-3 shadow-sm" style="border-radius: 8px; border: 1px solid #E7E7E7;">
        @if(!empty($seller->logo))
            <img src="{{ asset($seller->logo) }}" alt="{{ $seller->name ?? $seller->org_name }}" class="rounded-circle border" style="width: 70px; height: 70px; object-fit: cover;">
        @else
            <div class="rounded-circle border d-flex align-items-center justify-content-center bg-light text-primary fw-bold" style="width: 70px; height: 70px; font-size: 28px;">
                {{ strtoupper(substr($seller->name ?? $seller->org_name, 0, 1)) }}
            </div>
        @endif
        <div class="ms-3">
            <h2 class="mb-1" style="font-weight: 700; color: #0F1111; font-size: 22px; letter-spacing: -0.5px;">{{ $seller->name ?? $seller->org_name }}</h2>
            <div class="d-inline-flex align-items-center mt-1">
                <span class="fw-bold me-1" style="color: #0F1111; font-size: 14px;">{{ number_format($avgRating, 1) }}</span>
                <div class="text-warning d-flex align-items-center" style="gap: 1px;">
                    @for($i=1; $i<=5; $i++)
                        @if($i <= $avgRating)
                            <i class="fa fa-star" style="font-size: 13px !important;"></i>
                        @elseif($i - 0.5 <= $avgRating)
                            <i class="fa fa-star-half-alt" style="font-size: 13px !important;"></i>
                        @else
                            <i class="far fa-star" style="font-size: 13px !important;"></i>
                        @endif
                    @endfor
                </div>
                <span class="ms-2 text-muted" style="font-size: 13px;">({{ $totalReviews }} global ratings)</span>
            </div>
            @if(($seller->logo_trademark ?? '') == 'Yes' && !empty($seller->trademark_file))
                <div class="mt-2 text-center" style="display: inline-block; padding: 4px 10px; border: 1px dashed #dc3545; border-radius: 4px; background: #fffcfc;">
                    <i class="fa-solid fa-circle-check text-success"></i>
                    <span style="color: #28a745; font-weight: 600; font-size: 13px;">Registered Trademark</span>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ================= DYNAMIC PRODUCT CAROUSELS ================= --}}
@php
$sections = [
    ['title' => 'Top Trending', 'data' => $topTrending ?? collect()],
    ['title' => 'Best Discount', 'data' => $bestDiscount ?? collect()],
    ['title' => 'New Arrivals', 'data' => $newArrivals ?? collect()],
    ['title' => 'Recommended For You', 'data' => $recommendedProducts ?? collect()],
    ['title' => 'Most Purchased', 'data' => $mostPurchased ?? collect()],
    ['title' => 'Most Enquiries', 'data' => $mostEnquiries ?? collect()],
    ['title' => 'Highest Rated', 'data' => $highestReviews ?? collect()],
];
@endphp

@foreach($sections as $section)
@if($section['data']->count() > 0)
<div class="container mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
        <h4 class="mb-0" style="font-weight: 700; color: #0F1111; font-size: 20px;">{{ $section['title'] }}</h4>
    </div>
    
    <div class="d-flex product-slider" style="overflow-x: auto; scroll-snap-type: x mandatory; gap: 15px; padding-bottom: 10px; scrollbar-width: thin; scrollbar-color: #D5D9D9 #F7F7F7;">
        @foreach($section['data'] as $product)
            <div class="product-slider-item" style="flex: 0 0 auto; width: 220px; scroll-snap-align: start;">
                @include('vshop.partials.product_card', ['product' => $product])
            </div>
        @endforeach
    </div>
</div>
@endif
@endforeach

{{-- ================= ABOUT SELLER ================= --}}
@if(($seller->logo_trademark ?? '') == 'Yes' && !empty($seller->trademark_file))
<div class="container mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
        <h4 class="mb-0" style="font-weight: 700; color: #0F1111; font-size: 20px;">About Seller</h4>
    </div>
    
    <div class="bg-white p-4 shadow-sm" style="border-radius: 8px; border: 1px solid #E7E7E7;">
        <h5 style="font-weight: 600; color: #333; margin-bottom: 15px;">Registered Brands</h5>
        <div class="table-responsive">
            <table class="table table-bordered mb-0" style="font-size: 14px;">
                <thead class="table-light">
                    <tr>
                        <th>Brand Name</th>
                        <th>Trademark Status</th>
                        <th>Document</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-weight: 500;">{{ $seller->name ?? $seller->org_name }}</td>
                        <td class="text-success" style="font-weight: 600;"><i class="fa-solid fa-circle-check"></i> Verified</td>
                        <td>
                            <a href="{{ asset($seller->trademark_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-file-pdf"></i> View Trademark PDF
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <p class="mb-1 text-muted" style="font-size: 13px;"><i class="fa-solid fa-check text-success"></i> Registered Brands</p>
            <p class="mb-1 text-muted" style="font-size: 13px;"><i class="fa-solid fa-check text-success"></i> All Brands Legally Verified</p>
            <p class="mb-0 text-muted" style="font-size: 13px;"><i class="fa-solid fa-check text-success"></i> 100% Authentic Products</p>
        </div>
    </div>
</div>
@endif

{{-- ================= ALL PRODUCTS (GRID) ================= --}}
@if($products->count() > 0)
<div class="container mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
        <h4 class="mb-0" style="font-weight: 700; color: #0F1111; font-size: 20px;">All Products</h4>
    </div>

    <div class="row">
        @foreach($products as $product)
            <div class="col-6 col-md-4 col-lg-2 mb-4">
                @include('vshop.partials.product_card', ['product' => $product])
            </div>
        @endforeach
    </div>
    
    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endif

            </div>
        </div>
    </div>
</div>

<style>
/* CSS Slider Scrollbar styling for Webkit */
.product-slider::-webkit-scrollbar {
    height: 8px;
}
.product-slider::-webkit-scrollbar-track {
    background: #F7F7F7;
    border-radius: 4px;
}
.product-slider::-webkit-scrollbar-thumb {
    background-color: #D5D9D9;
    border-radius: 4px;
}
.product-slider::-webkit-scrollbar-thumb:hover {
    background-color: #a0a0a0;
}
</style>

<script>
$(document).ready(function(){
    // Banner carousel
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
