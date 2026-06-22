@extends('vtravel.main')

@section('title', 'Dashboard')

@section('content')
<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
<div class="dashboardPosPagesLeftContentonner">
<div class="dashboardDtlsArea">
<div class="dashboardDtlsAreaBody">

{{-- BANNER --}}
<div class="travel-banner-sec">
    <div class="banner-wrapper owl-carousel">
        <div class="banner-img item">
            <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}">
        </div>
        <div class="banner-img item">
            <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}">
        </div>
    </div>
</div>

{{-- TOP TRENDING --}}
<div class="home-seller-sec">
    <div class="home-seller-header">
        <h2>Top Trending</h2>
        <p>Top Trending Tour Packages</p>
    </div>

    <div class="home-seller-bottom">
        <div class="row">
            @foreach($trendingProducts as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 product-box-outer">
                <div class="product-box">

                    <div class="product-box-img">
                        <a href="{{ route('products.show', $product->id) }}">
                            <img src="{{ asset($product->thumbnail) }}">
                        </a>
                    </div>

                    <div class="product-bottom">
                        <h3>
                            <a href="{{ route('products.show', $product->id) }}">
                                {{ $product->name }}
                            </a>
                        </h3>

                        <div class="rating-size-sec">
                            <p class="rating-sec">4.5 <i class="fa-solid fa-star"></i></p>
                        </div>

                        @if($product->regular_price)
                        <div class="off-sec">
                            <p class="old-rupess">₹ {{ number_format($product->regular_price) }}</p>
                            <p class="off-box">
                                {{ round((($product->regular_price - $product->price)/$product->regular_price)*100) }}% Off
                            </p>
                        </div>
                        @endif

                        <div class="product-bottom-sec">
                            <a href="{{ route('products.show', $product->id) }}" class="add-cart-btn">
                                View Details
                            </a>
                            <p class="new-rupess">₹ {{ number_format($product->price) }}</p>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- BEST DISCOUNT --}}
<div class="home-seller-sec">
    <div class="home-seller-header">
        <h2>Best Discount</h2>
        <p>Best Discounted Tour Packages</p>
    </div>

    <div class="home-seller-bottom">
        <div class="row">
            @foreach($discountProducts as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 product-box-outer">
                <div class="product-box">

                    <div class="product-box-img">
                        <a href="{{ route('products.show', $product->id) }}">
                            <img src="{{ asset($product->thumbnail) }}">
                        </a>
                    </div>

                    <div class="product-bottom">
                        <h3>
                            <a href="{{ route('products.show', $product->id) }}">
                                {{ $product->name }}
                            </a>
                        </h3>

                        <div class="off-sec">
                            <p class="old-rupess">₹ {{ number_format($product->regular_price) }}</p>
                            <p class="off-box">
                                {{ round((($product->regular_price - $product->price)/$product->regular_price)*100) }}% Off
                            </p>
                        </div>

                        <div class="product-bottom-sec">
                            <a href="{{ route('products.show', $product->id) }}" class="add-cart-btn">
                                View Details
                            </a>
                            <p class="new-rupess">₹ {{ number_format($product->price) }}</p>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

</div>
</div>
</div>
</div>
@endsection
