@extends('vtravel.main')

@section('title','Services')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')

<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
<div class="dashboardPosPagesLeftContentonner">
<div class="dashboardDtlsArea">
<div class="dashboardDtlsAreaBody">

{{-- ================= TOP BAR ================= --}}

<div class="bed-cum-sec">
<button class="back-btn" onclick="window.history.back()">
<i class="fa-solid fa-arrow-left"></i>
</button>
<ul>
<li>Home /</li>
<li>Travel</li>
</ul>
</div>

<div class="product-dtl-banner">
<img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}">
</div>

{{-- ================= FILTER TAGS ================= --}}

<div class="product-list-top-sec">

<div class="product-list-top-left">

<button class="filter-btn">
<i class="fa-solid fa-filter"></i>
</button>

<ul>

@if(request('rating'))
<li>
{{ request('rating') }} Star
<a href="{{ route('vtravelproduct.index',request()->except('rating')) }}" class="closs-btn">
<i class="fa-solid fa-x"></i>
</a>
</li>
@endif

@if(request('brand'))

@php
$brand=$brands->firstWhere('id',request('brand'));
@endphp

@if($brand)
<li>
{{ $brand->name }}
<a href="{{ route('vtravelproduct.index',request()->except('brand')) }}" class="closs-btn">
<i class="fa-solid fa-x"></i>
</a>
</li>
@endif

@endif

@if(request('sort'))

<li>
{{ request('sort') }}
<a href="{{ route('vtravelproduct.index',request()->except('sort')) }}" class="closs-btn">
<i class="fa-solid fa-x"></i>
</a>
</li>

@endif

@if(!request()->hasAny(['rating','brand','sort']))
<li>All Packages</li>
@endif

</ul>

</div>

{{-- SORT DROPDOWN --}}

<div class="product-list-top-right">

<div class="dropdown high-low-dropdown">

<button class="btn btn-secondary dropdown-toggle"
type="button"
data-bs-toggle="dropdown">

Sort By
<i class="fa-solid fa-filter"></i>

</button>

<ul class="dropdown-menu">

<li>
<a class="dropdown-item"
href="{{ route('vtravelproduct.index',array_merge(request()->except('sort'),['sort'=>'price_asc'])) }}">
Price Low to High
</a>
</li>

<li>
<a class="dropdown-item"
href="{{ route('vtravelproduct.index',array_merge(request()->except('sort'),['sort'=>'price_desc'])) }}">
Price High to Low
</a>
</li>

<li>
<a class="dropdown-item"
href="{{ route('vtravelproduct.index',array_merge(request()->except('sort'),['sort'=>'newest'])) }}">
Newest
</a>
</li>

<li>
<a class="dropdown-item"
href="{{ route('vtravelproduct.index',array_merge(request()->except('sort'),['sort'=>'popular'])) }}">
Most Popular
</a>
</li>

</ul>

</div>

<div class="productColumnBtnArea">

<button class="productColumnBtn productColumnBtn_2 active">
<i class="fa-sharp fa-solid fa-grid-2"></i>
</button>

<button class="productColumnBtn productColumnBtn_3">
<i class="fa-solid fa-list"></i>
</button>

</div>

</div>

</div>

{{-- ================= PRODUCTS AREA ================= --}}

<div class="home-seller-sec">
<div class="home-seller-bottom">
<div class="product-list-wrapper productRow">

<div class="row">

{{-- ================= FILTER SIDEBAR ================= --}}

<div class="col-lg-3">

<div class="product-list-filter-sec">

<form method="GET" action="{{ route('vtravelproduct.index') }}">

{{-- ⭐ CUSTOMER REVIEW --}}

<div class="filter-box">

<h3>Customer Review</h3>

<ul>

@foreach($ratings as $star=>$total)

<li>

<label class="filter-list">

@for($i=1;$i<=5;$i++)
<i class="fa-solid fa-star {{ $i <= $star ? 'text-warning':'text-secondary' }}"></i>
@endfor

<span>({{ $total }})</span>

<input type="radio"
name="rating"
value="{{ $star }}"
{{ request('rating')==$star ? 'checked':'' }}>

<span class="checkmark"></span>

</label>

</li>

@endforeach

</ul>

</div>


{{-- ⭐ DAYS FILTER --}}

<div class="filter-box">

<h3>Days</h3>

<ul>

@foreach([3,4,5,7,10] as $day)

<li>

<label class="filter-list">

{{ $day }} Days

<input type="radio"
name="days"
value="{{ $day }}"
{{ request('days')==$day ? 'checked':'' }}>

<span class="checkmark"></span>

</label>

</li>

@endforeach

</ul>

</div>


{{-- ⭐ NIGHTS FILTER --}}

<div class="filter-box">

<h3>Nights</h3>

<ul>

@foreach([2,3,4,5,7] as $night)

<li>

<label class="filter-list">

{{ $night }} Nights

<input type="radio"
name="nights"
value="{{ $night }}"
{{ request('nights')==$night ? 'checked':'' }}>

<span class="checkmark"></span>

</label>

</li>

@endforeach

</ul>

</div>


{{-- ⭐ PRICE FILTER --}}

<div class="filter-box">

<h3>Price Range</h3>

<div class="row g-2">

<div class="col-6">

<input type="number"
name="min_price"
class="form-control"
placeholder="Min"
value="{{ request('min_price') }}">

</div>

<div class="col-6">

<input type="number"
name="max_price"
class="form-control"
placeholder="Max"
value="{{ request('max_price') }}">

</div>

</div>

</div>


{{-- APPLY FILTER --}}

<div class="mt-3">

<button class="btn btn-dark w-100">
Apply Filter
</button>

</div>


{{-- CLEAR FILTER --}}

@if(request()->hasAny(['rating','days','nights','min_price','max_price']))

<div class="mt-2">

<a href="{{ route('vtravelproduct.index') }}"
class="btn btn-outline-secondary w-100">

Clear Filter

</a>

</div>

@endif

</form>

</div>

</div>

{{-- ================= PRODUCTS GRID ================= --}}

<div class="col-lg-9">

<div class="product-list-right product-col-view">

<div class="row">

@forelse($products as $product)

<div class="col-lg-4 col-md-4 col-sm-6 product-box-outer">

<div class="product-box">

<div class="share-wishlist-sec">

<a href="#">
<i class="fa-solid fa-share-nodes"></i>
</a>

@auth('customer')
<a href="#" class="wishlist-btn" data-product-id="{{ $product->id }}">
    <i class="{{ in_array($product->id,$wishlistIds ?? [])
        ? 'fa-solid fa-heart text-danger'
        : 'fa-regular fa-heart' }}"></i>
</a>
@else
<a href="{{ route('vshoplogin', ['redirect' => request()->fullUrl()]) }}">
    <i class="fa-regular fa-heart"></i>
</a>
@endauth

</div>

<div class="product-box-img">

<a href="{{ route('products.show',$product->id) }}">

<img src="{{ $product->thumbnail
? asset($product->thumbnail)
: asset('vtravelassets/images/banner-pic-main.webp') }}">

</a>

</div>

<div class="product-bottom">

<h3>

<a href="{{ route('products.show',$product->id) }}">
{{ $product->name }}
</a>

</h3>

<div class="rating-size-sec">

<p class="rating-sec">

{{ number_format($product->average_rating,1) }}

@for($i=1;$i<=5;$i++)
<i class="fa-solid fa-star {{ $i <= round($product->average_rating)
? 'text-warning':'text-secondary' }}"></i>
@endfor

</p>

<p class="color-size-sec">

@if($product->created_by==='seller')

{{ $product->seller->name ?? 'Seller' }}

@else

{{ $product->brand->name ?? 'Travel Store' }}

@endif

</p>

</div>

@if($product->discount_price)

<div class="off-sec">

<p class="old-rupess">
₹ {{ number_format($product->price,2) }}
</p>

<p class="off-box">

{{ round((($product->price-$product->discount_price)/$product->price)*100) }}% Off

</p>

</div>

@endif

<div class="product-bottom-sec">

<button class="add-cart-btn"
onclick="window.location.href='{{ route('products.show',$product->id) }}'">

View Details

</button>

<p class="new-rupess">
₹ {{ number_format($product->discount_price ?? $product->price,2) }}
</p>

</div>

</div>

</div>

</div>

@empty

<div class="col-12">

<div class="alert alert-warning">
No travel packages found.
</div>

</div>

@endforelse

</div>

</div>

{{-- PAGINATION --}}

<div class="mt-4">
{{ $products->links('pagination::bootstrap-5') }}
</div>

</div>

</div>

</div>

</div>

</div>

</div>

</div>

</div>

@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>

document.addEventListener('click', function(e){

    const btn = e.target.closest('.wishlist-btn');
    if(!btn) return;

    e.preventDefault();

    @auth('customer')

    let productId = btn.dataset.productId;
    let icon = btn.querySelector('i');
    const originalClass = icon.className;

    icon.className = "fa fa-spinner fa-spin";

    fetch("{{ route('vtravel.wishlist.toggle') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ product_id: parseInt(productId) })
    })
    .then(res => res.json())
    .then(data => {

        if(data.success && data.status === "added"){
            icon.className = "fa-solid fa-heart text-danger";
            toastr.success("Added to wishlist");
        }
        else if(data.success && data.status === "removed"){
            icon.className = "fa-regular fa-heart";
            toastr.info("Removed from wishlist");
        }
        else{
            icon.className = originalClass;
            toastr.error("Wishlist error");
        }

    })
    .catch(() => {
        icon.className = originalClass;
        toastr.error("Network error");
    });

    @else
    toastr.warning("Please login first", "", {
        onclick: () => window.location = "{{ route('vshoplogin') }}"
    });
    @endauth

});

</script>

@endpush
