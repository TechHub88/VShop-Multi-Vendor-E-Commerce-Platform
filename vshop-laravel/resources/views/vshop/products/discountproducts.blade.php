@extends('vshop.main')

@section('title','Best Discount Products')

@section('content')


{{-- BANNER --}}
<div class="travel-banner-sec">
    <div class="banner-wrapper owl-carousel">

        @if(isset($banners) && count($banners) > 0 && $banners->contains(function($ad) { return $ad->media->count() > 0; }))

            @foreach($banners as $banner)
                @if($banner->media->count() > 0)
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
                <img src="{{ asset('vtravelassets/images/product_banner1.png') }}">
            </div>

        @endif

    </div>
</div>

<div class="container mt-4">

<h3 class="mb-4">Best Discount Products</h3>

<div class="row">

@foreach($products as $product)

@php
$regular = $product->regular_price ?? 0;
$price = $product->price;
$discount = $regular > $price ? round((($regular-$price)/$regular)*100) : 0;
@endphp

<div class="col-md-3 mb-4">
<div class="card h-100">

<a href="{{ route('productmanage.show',$product->id) }}">
<img src="{{ asset($product->thumbnail) }}"
class="card-img-top"
style="height:200px;object-fit:cover;">
</a>

<div class="card-body">

<h6>{{ $product->name }}</h6>

@if($discount > 0)
<p class="text-danger fw-bold">{{ $discount }}% OFF</p>
@endif

<p>
<span class="text-muted text-decoration-line-through">
â‚¹ {{ number_format($regular,2) }}
</span>
<br>
<span class="fw-bold text-success">
â‚¹ {{ number_format($price,2) }}
</span>
</p>

<a href="{{ route('productmanage.show',$product->id) }}"
class="btn btn-primary w-100">
View Product
</a>

</div>

</div>
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