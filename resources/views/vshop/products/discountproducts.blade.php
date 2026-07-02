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

<h3 class="mb-4">Best Discount Products</h3>

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