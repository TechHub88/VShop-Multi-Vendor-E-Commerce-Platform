@extends('vshop.main')

@section('title','Best Discount Products')

@section('content')


{{-- BANNER --}}
<div class="travel-banner-sec">
    <div class="banner-wrapper owl-carousel">

        @if(isset($banners) && count($banners) > 0 && $banners->contains(function($ad) { return $ad->media->count() > 0 && $ad->type == 'banner'; }))

            @foreach($banners as $banner)
                @if($banner->type == 'banner' && $banner->media->count() > 0)
                    @foreach($banner->media as $media)
                        @if($media->media_type == 'image')
                            <div class="banner-img item">
                                @if($banner->redirect_url)
                                    <a href="{{ $banner->redirect_url }}">
                                        <img src="{{ asset($media->file_path) }}" class="w-100" alt="{{ $banner->title }}">
                                    </a>
                                @else
                                    <img src="{{ asset($media->file_path) }}" class="w-100" alt="{{ $banner->title }}">
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endif
            @endforeach

        @else

            <div class="banner-img item">
                <img src="{{ asset('vtravelassets/images/product_banner1.png') }}" class="w-100">
            </div>

        @endif

    </div>
</div>

<div class="container mt-4">

<h3 class="mb-4 d-flex justify-content-between align-items-center">
    <span>Best Discount Products</span>
</h3>

<div class="mb-4 d-flex gap-2 flex-wrap">
    <a href="{{ route('discount.products') }}" class="btn btn-{{ request('campaign') ? 'outline-primary' : 'primary' }} rounded-pill px-4 shadow-sm">All Offers</a>
    <a href="{{ route('discount.products', ['campaign' => 'percentage_20']) }}" class="btn btn-{{ request('campaign') == 'percentage_20' ? 'primary' : 'outline-primary' }} rounded-pill px-4 shadow-sm">20% Offers</a>
    <a href="{{ route('discount.products', ['campaign' => 'bogo']) }}" class="btn btn-{{ request('campaign') == 'bogo' ? 'primary' : 'outline-primary' }} rounded-pill px-4 shadow-sm">Buy 1 Get 1</a>
    <a href="{{ route('discount.products', ['campaign' => 'buy2_get1']) }}" class="btn btn-{{ request('campaign') == 'buy2_get1' ? 'primary' : 'outline-primary' }} rounded-pill px-4 shadow-sm">Buy 2 Get 1</a>
</div>

<div class="row">
    @foreach($products as $product)
        <div class="col-6 col-md-4 col-lg-2 mb-4">
            @include('vshop.partials.product_card', ['product' => $product])
        </div>
    @endforeach
</div>

{{ $products->links('pagination::bootstrap-5') }}

</div>

<script>
$(document).ready(function(){

    $(".banner-wrapper").owlCarousel({
        items:1,
        loop:true,
        autoplay:true,
        autoplayTimeout:3000, // ✅ 3 sec
        autoplayHoverPause:true,
        nav:false,
        dots:true
    });

});
</script>

@endsection
