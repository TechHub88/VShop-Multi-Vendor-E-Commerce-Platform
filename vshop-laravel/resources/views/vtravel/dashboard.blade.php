@extends('vtravel.main')

@section('title', 'Vtravel Dashboard')

@section('content')
<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
    <div class="dashboardPosPagesLeftContentonner">
        <div class="dashboardDtlsArea">
            <div class="dashboardDtlsAreaBody">
                <div class="travel-banner-sec">
                    <div class="banner-wrapper owl-carousel">
                        <div class="banner-img item">
                            <img src="{{ asset('vtravelassets/images/banner-pic-main.webp')}}" alt="">
                        </div>
                        <div class="banner-img item">
                            <img src="{{ asset('vtravelassets/images/banner-pic-main.webp')}}" alt="">
                        </div>
                    </div>
                </div>
                <div class="home-seller-sec">
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
                            @for($i = 0; $i < 4; $i++)
                            <div class="col-lg-3 col-md-4 col-sm-6 product-box-outer">
                                <div class="product-box">
                                    <div class="share-wishlist-sec">
                                        <a href="#">
                                            <i class="fa-solid fa-share-nodes"></i>
                                        </a>
                                        <a href="#">
                                            <i class="fa-solid fa-heart"></i>
                                        </a>
                                    </div>
                                    <div class="product-box-img">
                                        <a href="#">
                                            <img src="{{ asset('vtravelassets/images/banner-pic-main.webp')}}" alt="">
                                        </a>
                                    </div>
                                    <div class="product-bottom">
                                        <h3><a href="#">Selinium Yoga Mat 8mm Thick, 173 x 61 cm</a></h3>
                                        <div class="rating-size-sec">
                                            <p class="rating-sec">4.6 <i class="fa-solid fa-star"></i></p>
                                            <p class="color-size-sec">4 Color / 3 Sizes</p>
                                        </div>
                                        <div class="off-sec">
                                            <p class="old-rupess">₹ 6,700.00</p>
                                            <p class="off-box">14 % Off</p>
                                        </div>
                                        <div class="product-bottom-sec">
                                            <a href="#" class="add-cart-btn">Add to Cart</a>
                                            <p class="new-rupess">₹ 5,900.00</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="home-seller-sec">
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
                            @for($i = 0; $i < 4; $i++)
                            <div class="col-lg-3 col-md-4 col-sm-6 product-box-outer">
                                <div class="product-box">
                                    <div class="share-wishlist-sec">
                                        <a href="#">
                                            <i class="fa-solid fa-share-nodes"></i>
                                        </a>
                                        <a href="#">
                                            <i class="fa-solid fa-heart"></i>
                                        </a>
                                    </div>
                                    <div class="product-box-img">
                                        <a href="#">
                                            <img src="{{ asset('vtravelassets/images/banner-pic-main.webp')}}" alt="">
                                        </a>
                                    </div>
                                    <div class="product-bottom">
                                        <h3><a href="#">Selinium Yoga Mat 8mm Thick, 173 x 61 cm</a></h3>
                                        <div class="rating-size-sec">
                                            <p class="rating-sec">4.6 <i class="fa-solid fa-star"></i></p>
                                            <p class="color-size-sec">4 Color / 3 Sizes</p>
                                        </div>
                                        <div class="off-sec">
                                            <p class="old-rupess">₹ 6,700.00</p>
                                            <p class="off-box">14 % Off</p>
                                        </div>
                                        <div class="product-bottom-sec">
                                            <a href="#" class="add-cart-btn">Add to Cart</a>
                                            <p class="new-rupess">₹ 5,900.00</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    $('.banner-wrapper').owlCarousel({
        items:1,
        loop:true,
        autoplay:true,
        autoplayTimeout:3000,
        autoplayHoverPause:true
    });
});
</script>
@endpush
