@extends('vshop.main')

@section('title', $product->name . ' Reviews')

@section('content')

<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
<div class="dashboardPosPagesLeftContentonner">
<div class="dashboardDtlsArea">
<div class="dashboardDtlsAreaBody">

{{-- BREADCRUMB --}}
<div class="bed-cum-sec">

<button class="back-btn" onclick="window.history.back()">
<i class="fa-regular fa-arrow-left"></i>
</button>

<ul>
<li><a href="{{ route('vtravelhome') }}">Home</a> / </li>
<li>{{ $product->category->name ?? 'Package' }} /</li>
<li>{{ $product->name }}</li>
</ul>

</div>


{{-- PRODUCT HEADER --}}
<div class="product-details-sec">

<div class="row">

<div class="col-lg-9">

<div class="product-details-left">

<div class="product-list-box">

<div class="product-list-box-top">


{{-- PRODUCT IMAGE --}}
<div class="product-list-box-left">

<div class="share-wishlist-sec">

<a href="#"><i class="fa-regular fa-share-nodes"></i></a>

<a href="#"><i class="fa-regular fa-heart"></i></a>

</div>

<div class="product-image-sec">

<div class="product-image-wrapper-one">

<div class="product-image-one-box">

<div class="product-image-one-box-inner">

<img src="{{ asset($product->thumbnail) }}" alt="{{ $product->name }}">

</div>

</div>

</div>

</div>

</div>


{{-- PRODUCT DETAILS --}}
<div class="product-list-box-right">

<div class="product-heading">

<div class="product-heading-left">

<h3>{{ $product->name }}</h3>

<ul>

<li>{{ $product->category->name ?? 'Package' }}</li>

<li>/</li>

@if($product->created_by == 'seller' && $product->seller)

<li>
Sold by :
<strong>{{ $product->seller->name }}</strong>
</li>

@endif

</ul>

</div>

</div>


{{-- RATING --}}
<div class="rating-sec">

<div class="rating-left">

<ul>

<li>

{{ number_format($averageRating,1) }}

@for($i=1;$i<=5;$i++)
<i class="fa{{ $i <= round($averageRating) ? '-solid' : '-regular' }} fa-star"></i>
@endfor

</li>

<li>

<span>({{ $totalReviews }} Reviews)</span>

</li>

<li class="blackColor">

<p class="old-rupess">
₹ {{ number_format($product->regular_price ?? $product->price,2) }}
</p>

</li>

<li>

<p class="new-rupess">
₹ {{ number_format($product->price,2) }}
</p>

</li>

</ul>

</div>

</div>


</div>
</div>
</div>
</div>

</div>

{{-- RIGHT AD --}}
<div class="col-lg-3">

<div class="product-advatice-sec">

<img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}">

</div>

</div>

</div>

</div>



{{-- REVIEW SUMMARY --}}
<div class="product-list-top-sec">

<div class="product-list-top-left company-top-info-left">

<h2>{{ $totalReviews }} User Reviews</h2>

<div class="review-rating-box">

@for($i=1;$i<=5;$i++)
<i class="fa-solid fa-star"></i>
@endfor

<p>{{ number_format($averageRating,1) }} / 5</p>

</div>


<div class="product-search">

<i class="fa-light fa-magnifying-glass"></i>

<input type="text" id="reviewSearch" placeholder="Search reviews...">

</div>

</div>

</div>


{{-- REVIEWS --}}
<div class="user-review-sec">

<ul class="review-list" id="reviewList">

@forelse($reviews as $review)

<li>

<div class="review-top">

<div class="review-top-left">

<h3>{{ $review->user->name ?? 'Anonymous' }}</h3>

<div class="review-top-lft-bottom">

<p>

{{ number_format($review->rating,1) }}

@for($i=1;$i<=5;$i++)
<i class="fa{{ $i <= $review->rating ? '-solid' : '-regular' }} fa-star"></i>
@endfor

</p>

<span>{{ $review->created_at->format('d M Y') }}</span>

</div>

</div>

<div class="review-top-right">

<a href="#" class="report-link">Report this</a>

</div>

</div>


{{-- REVIEW TEXT --}}
<p class="review-text">

{{ $review->review_text }}

</p>


{{-- REVIEW IMAGES --}}
@if(!empty($review->images))

<div class="review-img-sec">

@foreach(json_decode($review->images,true) ?? [] as $img)

<div class="review-img-box">

<a href="{{ asset($img) }}" target="_blank">

<img src="{{ asset($img) }}">

</a>

</div>

@endforeach

</div>

@endif


</li>

@empty

<li>

<p>No reviews yet for this product.</p>

</li>

@endforelse

</ul>

</div>


</div>
</div>
</div>
</div>
</div>

@endsection


@push('scripts')

<script>

$(document).ready(function(){

$('#reviewSearch').on('keyup',function(){

let value=$(this).val().toLowerCase();

$("#reviewList li").filter(function(){

$(this).toggle($(this).text().toLowerCase().indexOf(value)>-1)

});

});

});

</script>

@endpush
