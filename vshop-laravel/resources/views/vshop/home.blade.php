@extends('vshop.main')

@section('title', 'Dashboard')

<style>
    /* Global fixes for mobile gap and overflow */
    html, body {
        overflow-x: hidden !important;
        max-width: 100vw;
        background-color: #f1f3f6 !important; /* Amazon/Flipkart typical light grey background */
    }
    
    .dashboardPagesContentonner {
        max-width: 100vw;
        overflow-x: hidden;
    }
    
    .home-seller-sec {
        padding: 20px !important;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 1px 4px 0 rgba(0,0,0,0.05);
        margin-bottom: 16px;
    }
    
    /* Override dark blue header for modern Flipkart/Amazon clean look */
    .home-seller-header {
        background: transparent !important;
        color: #212121 !important;
        padding: 0 0 16px 0 !important;
        margin-bottom: 20px !important;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
    }
    
    .home-seller-header h2 {
        font-weight: 600 !important;
        font-size: 22px !important;
        color: #212121 !important;
        margin: 0 !important;
    }
    
    .home-seller-header i {
        color: #2874f0 !important; /* Blue accent color instead of warning/danger if needed, or keep original */
    }
    .custom-pagination-wrapper {
        margin-top: 15px !important;
    }
    .product-bottom-sec {
        flex-direction: column-reverse;
        align-items: stretch !important;
        gap: 4px !important;
        margin-top: 4px !important;
    }
    .product-box {
        padding: 6px !important;
        border-radius: 12px !important;
        box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.04) !important;
        border: 1px solid #f1f3f9 !important;
    }
    .product-box-img a {
        height: 100px !important;
        border-radius: 8px !important;
    }
    .product-box-img a img {
        object-fit: contain !important;
    }
    .product-bottom h3 {
        margin-bottom: 4px !important;
    }
    .product-bottom h3 a {
        font-size: 13px !important;
        line-height: 16px !important;
        font-weight: 600 !important;
        color: #2d3748 !important;
        display: -webkit-box !important;
        overflow: hidden;
        text-overflow: ellipsis;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
    }
    .rating-size-sec {
        margin-bottom: 4px !important;
        gap: 3px !important;
    }
    .rating-sec {
        font-size: 11px !important;
        color: #4a5568 !important;
    }
    .rating-sec i {
        font-size: 9px !important;
    }
    .color-size-sec {
        font-size: 10px !important;
        font-weight: 600 !important;
        color: #004392 !important;
    }
    .off-sec {
        margin-bottom: 4px !important;
        justify-content: flex-start !important;
        gap: 6px !important;
    }
    .old-rupess {
        font-size: 11px !important;
        font-weight: 600 !important;
    }
    .off-box {
        font-size: 11px !important;
        font-weight: 700 !important;
    }
    .new-rupess {
        font-size: 14px !important;
        font-weight: 700 !important;
        margin: 0 !important;
    }
    .add-cart-btn {
        font-size: 11px !important;
        padding: 4px 8px !important;
        border-radius: 6px !important;
        text-align: center !important;
        width: 100% !important;
    }
    .banner-img,
    .banner-wrapper {
        border-radius: 20px;
        overflow: hidden;
        width: 100%;
    }
    .banner-wrapper {
        max-width: 100%;
    }
    .banner-img img {
        width: 100% !important;
        height: 280px !important;
        object-fit: cover !important;
    }
    @media (max-width: 768px) {
        .banner-img img {
            height: 150px !important;
            border-radius: 12px;
        }
        .banner-img, .banner-wrapper {
            border-radius: 12px;
        }
    }

    /* Prevent layout shift/buffering on page load */
    .owl-carousel {
        transition: opacity 0.3s ease-in-out;
    }
    .owl-carousel:not(.owl-loaded) {
        display: flex !important;
        flex-wrap: nowrap;
        overflow: hidden;
        opacity: 0;
    }
    
    .dashboardDtlsAreaBody {
        max-width: 100%;
        overflow-x: hidden;
    }
    .product-bottom {
        padding: 0 !important;
    }
    
    .home-tags-sec {
        margin-bottom: 40px;
    }
    .home-seller-heading h2 {
        font-size: 22px;
        margin-bottom: 20px;
    }

    /* Horizontal Features Scroll styles */
    .features-scroll-wrapper {
        width: 100%;
        margin: 20px 0 25px 0;
        padding: 4px 0;
    }
    .features-scroll-container.owl-carousel .owl-stage-outer {
        padding: 5px 2px;
    }
    .feature-card {
        background: #ffffff;
        border: 1px solid #f1f3f9;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.02);
        border-radius: 12px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .feature-card:hover {
        transform: translateY(-2px);
        box-shadow: 0px 6px 16px rgba(0, 0, 0, 0.05);
    }
    .feature-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .feature-info {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }
    .feature-title {
        font-size: 13px;
        font-weight: 700;
        color: #1a253c;
        margin: 0;
    }
    .feature-desc {
        font-size: 11px;
        color: #7d8a9e;
        margin: 0;
    }
    
    /* Feature custom background/colors */
    .bg-quality {
        background: #eef4ff;
        color: #004392;
    }
    .bg-prices {
        background: #fffaf0;
        color: #dd6b20;
    }
    .bg-delivery {
        background: #ebf8ff;
        color: #2b6cb0;
    }
    .bg-payments {
        background: #f0fff4;
        color: #38a169;
    }
    .bg-returns {
        background: #e6fffa;
        color: #319795;
    }
    .bg-support {
        background: #faf5ff;
        color: #805ad5;
    }

    /* Modern Pagination Styling */
    .custom-pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 30px;
        gap: 8px;
    }
    .custom-pagination-wrapper .pagination {
        display: flex;
        gap: 6px;
        border: none;
        padding-left: 0;
        margin: 0;
        list-style: none;
    }
    .custom-pagination-wrapper .page-item {
        margin: 0;
    }
    .custom-pagination-wrapper .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50% !important;
        border: 1px solid #e2e8f0;
        color: #4a5568;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
        background: #ffffff;
        text-decoration: none;
    }
    .custom-pagination-wrapper .page-item.active .page-link,
    .custom-pagination-wrapper .page-item.active span {
        background: #004392 !important;
        border-color: #004392 !important;
        color: #ffffff !important;
        box-shadow: 0px 4px 10px rgba(0, 67, 146, 0.25);
    }
    .custom-pagination-wrapper .page-item.disabled .page-link,
    .custom-pagination-wrapper .page-item.disabled span {
        color: #cbd5e1 !important;
        background: #f8fafc !important;
        border-color: #e2e8f0 !important;
        pointer-events: none;
    }
    .custom-pagination-wrapper .page-link:hover {
        background: #f1f5f9;
        color: #004392;
        border-color: #cbd5e1;
    }
</style>

@section('content')
<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
<div class="dashboardPosPagesLeftContentonner">
<div class="dashboardDtlsArea">
<div class="dashboardDtlsAreaBody">

{{-- BANNER --}}
<div class="travel-banner-sec">
    <div class="banner-wrapper owl-carousel">

        @if(isset($banners) && count($banners) > 0 && $banners->contains(function($ad) { return $ad->media->count() > 0; }))

            @foreach($banners as $banner)
                @if($banner->media->count() > 0)
                    @foreach($banner->media as $media)
                        @if($media->media_type == 'image')
                            <div class="banner-img item">
                                <a href="{{ $banner->redirect_url ? $banner->redirect_url : route('discount.products') }}">
                                    <img src="{{ asset($media->file_path) }}" class="w-100" alt="{{ $banner->title }}">
                                </a>
                            </div>
                        @endif
                    @endforeach
                @endif
            @endforeach

        @else

            <div class="banner-img item">
                <img src="{{ asset('vtravelassets/images/product_banner1.png') }}">
            </div>

        @endif

    </div>
</div>

{{-- FEATURES SCROLL ROW --}}
<div class="features-scroll-wrapper">
    <div class="features-scroll-container owl-carousel owl-theme">
        <!-- Feature 1: Top Quality -->
        <div class="feature-card item">
            <div class="feature-icon-box bg-quality">
                <i class="bi bi-gem"></i>
            </div>
            <div class="feature-info">
                <h4 class="feature-title">Top Quality</h4>
                <p class="feature-desc">Premium Products</p>
            </div>
        </div>
        
        <!-- Feature 2: Best Prices -->
        <div class="feature-card item">
            <div class="feature-icon-box bg-prices">
                <i class="bi bi-tag"></i>
            </div>
            <div class="feature-info">
                <h4 class="feature-title">Best Prices</h4>
                <p class="feature-desc">Unbeatable Deals</p>
            </div>
        </div>
        
        <!-- Feature 3: Fast Delivery -->
        <div class="feature-card item">
            <div class="feature-icon-box bg-delivery">
                <i class="bi bi-truck"></i>
            </div>
            <div class="feature-info">
                <h4 class="feature-title">Fast Delivery</h4>
                <p class="feature-desc">On Time, Every Time</p>
            </div>
        </div>
        
        <!-- Feature 4: Secure Payments -->
        <div class="feature-card item">
            <div class="feature-icon-box bg-payments">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="feature-info">
                <h4 class="feature-title">Secure Payments</h4>
                <p class="feature-desc">100% Safe & Secure</p>
            </div>
        </div>
        
        <!-- Feature 5: Easy Returns -->
        <div class="feature-card item">
            <div class="feature-icon-box bg-returns">
                <i class="bi bi-arrow-counterclockwise"></i>
            </div>
            <div class="feature-info">
                <h4 class="feature-title">Easy Returns</h4>
                <p class="feature-desc">Hassle Free Returns</p>
            </div>
        </div>
        
        <!-- Feature 6: 24/7 Support -->
        <div class="feature-card item">
            <div class="feature-icon-box bg-support">
                <i class="bi bi-headset"></i>
            </div>
            <div class="feature-info">
                <h4 class="feature-title">24/7 Support</h4>
                <p class="feature-desc">We're Here to Help</p>
            </div>
        </div>
    </div>
</div>

{{-- CATEGORY PROMOTIONS --}}
@if(isset($categories) && count($categories) > 0)
<div class="home-category-sec mt-4 mb-4">
    <div class="home-seller-header">
        <h2>Shop By Category</h2>
    </div>
    <div class="category-wrapper owl-carousel">
        @foreach($categories as $category)
            <div class="category-box item text-center" style="padding: 10px;">
                <a href="{{ route('vshop.category.products', $category->slug ?? $category->id) }}" style="text-decoration:none;">
                    <div class="category-img-box mx-auto" style="width:80px; height:80px; border-radius:50%; background:#f1f3f9; display:flex; align-items:center; justify-content:center; overflow:hidden; border:2px solid #e2e8f0; transition: transform 0.3s ease;">
                        <img src="{{ asset($category->image ?? 'images/default-category.png') }}" style="max-width:60%; max-height:60%; object-fit:contain;">
                    </div>
                    <h4 class="mt-2" style="font-size:12px; color:#333; font-weight:600;">{{ $category->name }}</h4>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- TODAY'S DEALS --}}
@if(isset($discountProducts) && count($discountProducts) > 0)
<div class="home-seller-sec ajax-section" id="deals-section">
    <div class="home-seller-header d-flex justify-content-between align-items-center">
        <h2><i class="fa-solid fa-bolt text-warning"></i> Today's Deals</h2>
        <div class="deal-countdown" style="font-size:14px; font-weight:600; color:#e53e3e; background:#fed7d7; padding:4px 12px; border-radius:20px;">
            Ends in: <span id="deal-timer">12h 45m 30s</span>
        </div>
    </div>
    <div class="home-seller-bottom">
        <div class="row">
            @foreach($discountProducts as $product)
                <div class="col-6 col-md-4 col-lg-2 mb-4">
                    @include('vshop.partials.product_card', ['product' => $product])
                </div>
            @endforeach
        </div>
        <div class="custom-pagination-wrapper">
            {{ $discountProducts->appends(request()->except('deals_page'))->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endif

{{-- TOP TRENDING --}}
@if(isset($trendingProducts) && count($trendingProducts) > 0)
<div class="home-seller-sec ajax-section" id="trending-section">
    <div class="home-seller-header">
        <h2><i class="fa-solid fa-fire text-danger"></i> Top Trending</h2>
    </div>
    <div class="home-seller-bottom">
        <div class="row">
            @foreach($trendingProducts as $product)
                <div class="col-6 col-md-4 col-lg-2 mb-4">
                    @include('vshop.partials.product_card', ['product' => $product])
                </div>
            @endforeach
        </div>
        <div class="custom-pagination-wrapper">
            {{ $trendingProducts->appends(request()->except('trending_page'))->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endif

{{-- NEW ARRIVALS --}}
@if(isset($newArrivals) && count($newArrivals) > 0)
<div class="home-seller-sec ajax-section" id="new-section">
    <div class="home-seller-header">
        <h2>New Arrivals</h2>
    </div>
    <div class="home-seller-bottom">
        <div class="row">
            @foreach($newArrivals as $product)
                <div class="col-6 col-md-4 col-lg-2 mb-4">
                    @include('vshop.partials.product_card', ['product' => $product])
                </div>
            @endforeach
        </div>
        <div class="custom-pagination-wrapper">
            {{ $newArrivals->appends(request()->except('new_page'))->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endif

{{-- TOP RATED --}}
@if(isset($topRated) && count($topRated) > 0)
<div class="home-seller-sec ajax-section" id="top-section">
    <div class="home-seller-header">
        <h2><i class="fa-solid fa-star text-warning"></i> Top Rated Products</h2>
    </div>
    <div class="home-seller-bottom">
        <div class="row">
            @foreach($topRated as $product)
                <div class="col-6 col-md-4 col-lg-2 mb-4">
                    @include('vshop.partials.product_card', ['product' => $product])
                </div>
            @endforeach
        </div>
        <div class="custom-pagination-wrapper">
            {{ $topRated->appends(request()->except('top_page'))->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endif

{{-- RECOMMENDED FOR YOU --}}
@if(isset($recommendations) && count($recommendations) > 0)
<div class="home-seller-sec ajax-section" id="rec-section">
    <div class="home-seller-header">
        <h2>Recommended For You</h2>
    </div>
    <div class="home-seller-bottom">
        <div class="row">
            @foreach($recommendations as $product)
                <div class="col-6 col-md-4 col-lg-2 mb-4">
                    @include('vshop.partials.product_card', ['product' => $product])
                </div>
            @endforeach
        </div>
        <div class="custom-pagination-wrapper">
            {{ $recommendations->appends(request()->except('rec_page'))->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endif

{{-- RECENTLY SEARCHED --}}
@if(isset($searchHistory) && count($searchHistory) > 0)
<div class="home-seller-sec" id="recent-section">
    <div class="home-seller-header">
        <h2>Recently Searched</h2>
    </div>
    <div class="home-seller-bottom">
        <div class="row">
            @foreach($recentlyViewed as $product)
                <div class="col-6 col-md-4 col-lg-2 mb-4">
                    @include('vshop.partials.product_card', ['product' => $product])
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

</div>
</div>
</div>
</div>


@push('scripts')
<script>
   $(document).ready(function(){

        // Banner Slider
        $(".banner-wrapper").owlCarousel({
            items:1,
            loop:true,
            autoplay:true,
            autoplayTimeout:2000,
            smartSpeed: 800, 
            autoplayHoverPause:true,
            nav:false,
            dots:true
        });
        
        // Features Smooth Scroller
        $(".features-scroll-container").owlCarousel({
            margin: 16,
            nav: false,
            dots: false,
            loop: true,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplaySpeed: 3000, // Make speed match timeout for linear motion
            slideTransition: 'linear', // Smooth sliding
            autoplayHoverPause: true,
            responsive: {
                0: { items: 1.3 },
                576: { items: 2.3 },
                768: { items: 3.5 },
                992: { items: 4.8 },
                1200: { items: 5.5 }
            }
        });
        
        // Category Carousel
        $(".category-wrapper").owlCarousel({
            margin: 10,
            nav: false,
            dots: false,
            loop: false,
            responsive: {
                0: { items: 3 },
                576: { items: 5 },
                768: { items: 7 },
                992: { items: 9 }
            }
        });

        // Universal Product Carousel for all sections
        $(".home-product-carousel").owlCarousel({
            margin: 15,
            nav: false,
            dots: false,
            loop: false,
            responsive: {
                0: { items: 2 },
                576: { items: 3 },
                768: { items: 4 },
                992: { items: 5 },
                1200: { items: 6 }
            }
        });

        // Countdown Timer Logic
        function updateTimer() {
            var now = new Date();
            var endOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, 0, 0, 0);
            var diff = endOfDay - now;
            
            var hours = Math.floor(diff / 1000 / 60 / 60);
            var minutes = Math.floor((diff / 1000 / 60) % 60);
            var seconds = Math.floor((diff / 1000) % 60);
            
            $('#deal-timer').text(
                hours.toString().padStart(2, '0') + 'h ' + 
                minutes.toString().padStart(2, '0') + 'm ' + 
                seconds.toString().padStart(2, '0') + 's'
            );
        }
        setInterval(updateTimer, 1000);
        updateTimer();
        
        // AJAX Pagination Logic
        $(document).on('click', '.custom-pagination-wrapper .pagination a', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var section = $(this).closest('.ajax-section');
            var sectionId = section.attr('id');
            
            if (url && sectionId) {
                // Show a simple loading state
                section.find('.row').css('opacity', '0.5');
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        // Extract just the section we want from the returned HTML
                        var newContent = $(data).find('#' + sectionId).html();
                        if (newContent) {
                            section.html(newContent);
                            
                            // Re-initialize any JS if needed inside the section
                            // Scroll to top of section smoothly
                            $('html, body').animate({
                                scrollTop: section.offset().top - 100
                            }, 300);
                        }
                    },
                    error: function() {
                        alert('Error loading more products. Please try again.');
                        section.find('.row').css('opacity', '1');
                    }
                });
            }
        });
    
    });
</script>
@endpush




@endsection


