@extends('vtravel.main')

@section('title', $product->name . ' - Travel Details')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')

<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
<div class="dashboardPosPagesLeftContentonner">
<div class="dashboardDtlsArea">
<div class="dashboardDtlsAreaBody">

<div class="bed-cum-sec">
<button class="back-btn" onclick="window.history.back()">
<i class="fa-solid fa-arrow-left"></i>
</button>

<ul>
<li><a href="{{ route('vtravelhome') }}">Home</a> / </li>
<li><a href="{{ route('productmanage.index') }}">Travel</a> /</li>
<li>{{ $product->category->name ?? 'Package' }} /</li>
<li>{{ $product->name }}</li>
</ul>
</div>

<div class="product-details-sec">
<div class="row">

{{-- LEFT SIDE --}}
<div class="col-lg-9">
<div class="product-details-left">
<div class="product-list-box">
<div class="product-list-box-top">

{{-- IMAGE SECTION --}}
<div class="product-list-box-left">

<div class="share-wishlist-sec">
<a href="#"><i class="fa-regular fa-share-nodes"></i></a>
<a href="#"><i class="fa-regular fa-heart"></i></a>
</div>

<div class="product-image-sec">

<div class="product-image-wrapper-one">

<div class="product-image-one-box">
<div class="product-image-one-box-inner">
<img src="{{ asset($product->thumbnail) }}">
</div>
</div>

@foreach($galleryImages as $img)
<div class="product-image-one-box">
<div class="product-image-one-box-inner">
<img src="{{ asset($img) }}">
</div>
</div>
@endforeach

</div>

</div>
</div>

{{-- DETAILS SECTION --}}
<div class="product-list-box-right">

        <div class="product-heading">
                                                <div class="product-heading-left">
                                                    <h3>{{ $product->name }}</h3>
                                                 @if($storeName && $storeSlug)

                                                <p class="store-name">
                                                
                                                @auth('customer')
                                                
                                                <a href="{{ route('vtravel.store.show',$storeSlug) }}">
                                                {{ $storeName }}
                                                </a>
                                                
                                                @else
                                                
                                                <a href="{{ route('vshoplogin',['redirect'=>request()->fullUrl()]) }}">
                                                {{ $storeName }}
                                                </a>
                                                
                                                @endauth
                                                
                                                </p>
                                                
                                                @endif
                                                <ul>
                                                       
                                                    </ul>
                                                </div>
                                            </div>

{{-- RATING SECTION --}}
<div class="rating-sec">
    <div class="rating-left">
        <ul>
            <li class="rating-stars">

                <a href="{{ auth()->check() ? route('vtravel.product.reviews', $product->id) : route('employeelogin') }}"
                   style="text-decoration: none; color: inherit;">

                    <span class="rating-value">
                        {{ number_format($averageRating, 1) }}
                    </span>

                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa{{ $i <= round($averageRating) ? '-solid' : '-regular' }} fa-star"></i>
                    @endfor

                    <span class="review-count">
                        ({{ $totalReviews }} reviews)
                    </span>

                </a>

            </li>
        </ul>
    </div>
</div>
        

{{-- PRICE --}}
<div class="price-sec">
<ul>
    
      
@if($product->regular_price && $product->regular_price > $product->price)

<li class="blackColor">
<p class="old-rupess">
₹ {{ number_format($product->regular_price,2) }}
</p>
</li>

<li>
<p class="off-box">
{{ round((($product->regular_price - $product->price) / $product->regular_price) * 100) }}% Off
</p>
</li>

@endif

<li>
<p class="new-rupess">
₹ {{ number_format($product->price,2) }}
</p>
</li>

</ul>
</div>

{{-- PACKAGE INFO --}}
<div class="product-dtl-sec">

@php
$serviceDays = [];

if(!empty($product->service_days)){
    $serviceDays = is_array($product->service_days) 
        ? $product->service_days 
        : json_decode($product->service_days, true);
}
@endphp

{{-- ================== DAYS / PACKAGE ================== --}}
@if(!empty($serviceDays))

<div class="travel-duration-box">

    {{-- Nights --}}
    @if(isset($serviceDays['nights']))
    <div class="duration-item">
        <i class="fa-regular fa-moon"></i>
        <span>
            <strong>{{ $serviceDays['nights'] }}</strong> Nights
        </span>
    </div>
    @endif

    {{-- Days --}}
    @if(isset($serviceDays['count']))
    <div class="duration-item">
        <i class="fa-regular fa-calendar"></i>
        <span>
            <strong>{{ $serviceDays['count'] }}</strong> Days
        </span>
    </div>
    @endif

    {{-- Selection Type --}}
    @if(!empty($serviceDays['selection_type']))
    <div class="duration-item">
        <i class="fa-regular fa-clock"></i>
        <span>
            {{ ucfirst($serviceDays['selection_type']) }}
        </span>
    </div>
    @endif

</div>

@endif


{{-- ================== TIME BASED ================== --}}
@if($product->start_time || $product->end_time)

<p class="tour-duration">
    <i class="fa-regular fa-clock"></i>

    Time :
    <span class="blackColor">

        {{ $product->start_time ? \Carbon\Carbon::parse($product->start_time)->format('h:i A') : '' }}

        @if($product->start_time && $product->end_time)
            -
        @endif

        {{ $product->end_time ? \Carbon\Carbon::parse($product->end_time)->format('h:i A') : '' }}

    </span>
</p>

@endif


{{-- ================== HOURS BASED ================== --}}
@if($product->duration_hours)

<p class="tour-duration">
    <i class="fa-regular fa-hourglass-half"></i>
    Duration :
    <span class="blackColor">
        {{ $product->duration_hours }} Hours
    </span>
</p>

@endif

</div>
  <div class="product-dtl-sec">
                                                <p>Expected Delivery : <span class="blackColor">16 - 19 June 25</span> / <a href="{{route('vtravel.address.create')}}">500024 - Change Add</a></p>
                                            </div>

{{-- QUANTITY + ADD TO CART --}}
<div class="product-col-bottom-sec">

<div class="quantity-sub-sec">
<button type="button" class="sub" id="decrementQty">-</button>
<input class="count" type="text" id="quantity" value="1">
<button type="button" class="add" id="incrementQty">+</button>
</div>

<button
class="add-cart-btn"
id="addToCartBtn"
data-product-id="{{ $product->id }}"
data-service-days='@json($product->service_days)'
>

Add to Cart · ₹
<span id="totalPrice">{{ number_format($product->price,2) }}</span>

</button>

</div>

</div>
</div>
</div>
</div>
</div>


   <div class="col-lg-3">
                            <div class="product-advatice-sec">

                                {{-- MAIN PRODUCT IMAGE --}}
                                <div class="right-product-img">
                                    <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->name }}">
                                </div>



                            </div>
                        </div>

</div>
</div>


{{-- DESCRIPTION --}}
<div class="product-details-bottom">

@if($product->description)

<div class="product-details-bottom-box">
<div class="product-details-bottom-box-header">
<h3>Description</h3>
</div>

<p>{!! $product->description !!}</p>

</div>

@endif

{{-- TOUR PACKAGE DETAILS --}}
<div class="product-details-bottom-box">

<div class="product-details-bottom-box-header">
<h3>Tour Package Details</h3>
</div>

<table class="table table-bordered tour-details-table">


<th>Service Type</th>
<td>{{ ucfirst($product->service_type ?? '-') }}</td>
</tr>


<tr>
<th>Tax</th>
<td>
{{ $product->tax_name ?? '-' }}
@if($product->tax_percentage)
({{ $product->tax_percentage }}%)
@endif
</td>
</tr>

<tr>
<th>Service Charges</th>
<td>
@if($product->service_charges)
₹ {{ number_format($product->service_charges,2) }}
@else
-
@endif
</td>
</tr>

<tr>
<th>Kids Allowed</th>
<td>
@if($product->kids_allowed)
Yes (Age {{ $product->kids_age_from }} - {{ $product->kids_age_to }})
@else
No
@endif
</td>
</tr>

<tr>
<th>Adults Allowed</th>
<td>
@if($product->adult_allowed)
Yes (Age {{ $product->adult_age_from }} - {{ $product->adult_age_to }})
@else
No
@endif
</td>
</tr>

</table>

</div>

{{-- FEATURES --}}
@if(!empty($product->features))

<div class="product-details-bottom-box">

<div class="product-details-bottom-box-header">
<h3>Features</h3>
</div>

<div class="feature-content">
{!! $product->features !!}
</div>

</div>

@endif

{{-- SPECIFICATION --}}
@if(!empty($product->specification))

<div class="product-details-bottom-box">

<div class="product-details-bottom-box-header">
<h3>Specification</h3>
</div>

<div class="feature-content">
{!! $product->specification !!}
</div>

</div>

@endif
{{-- REFUND POLICY --}}
@if(!empty($product->refund_policy))

<div class="product-details-bottom-box">

<div class="product-details-bottom-box-header">
<h3>Refund Policy</h3>
</div>

<div class="feature-content">
{!! $product->refund_policy !!}
</div>

</div>

@endif

{{-- ================= REVIEW SECTION ================= --}}
<div class="product-details-bottom-box mt-4">

<div class="product-details-bottom-box-header d-flex justify-content-between align-items-center">

<h3>Customer Reviews ({{ $totalReviews }})</h3>

<button class="btn btn-warning btn-sm" id="openReviewForm">
Write a Review
</button>

</div>


{{-- REVIEW FORM (HIDDEN) --}}
<div id="reviewFormBox" style="display:none; margin-top:20px;">

@auth('customer')

<form action="{{ route('vtravel.review.store') }}" method="POST">
@csrf

<input type="hidden" name="product_id" value="{{ $product->id }}">
<input type="hidden" name="seller_id" value="{{ $product->created_by_id }}">

<div class="review-box">

{{-- STAR RATING --}}
<div class="mb-4">

<label class="form-label"><strong>Overall Rating</strong></label>

<div class="star-rating">

<input type="radio" name="rating" value="5" id="star5">
<label for="star5">★</label>

<input type="radio" name="rating" value="4" id="star4">
<label for="star4">★</label>

<input type="radio" name="rating" value="3" id="star3">
<label for="star3">★</label>

<input type="radio" name="rating" value="2" id="star2">
<label for="star2">★</label>

<input type="radio" name="rating" value="1" id="star1">
<label for="star1">★</label>

</div>

</div>


{{-- TITLE --}}
<div class="mb-3">
<label>Review Title</label>
<input type="text" name="title" class="form-control">
</div>


{{-- COMMENT --}}
<div class="mb-3">
<label>Your Review</label>
<textarea name="comment" class="form-control" rows="4"></textarea>
</div>


{{-- RECOMMEND --}}
<div class="mb-3">

<label>Would you recommend?</label>

<div class="form-check">
<input type="radio" name="would_recommend" value="1" class="form-check-input">
<label class="form-check-label">Yes</label>
</div>

<div class="form-check">
<input type="radio" name="would_recommend" value="0" class="form-check-input">
<label class="form-check-label">No</label>
</div>

</div>


<button class="btn btn-primary">
Submit Review
</button>

</div>

</form>

@else

<div class="alert alert-warning">
Please <a href="{{ route('employeelogin') }}">login</a> to write a review.
</div>

@endauth

</div>


{{-- ================= REVIEW LIST ================= --}}

<div class="review-list mt-4">

@forelse($productReviews as $review)

<div class="review-card">

<div class="review-top">

<strong>{{ $review->user_name }}</strong>

<span class="review-date">
{{ $review->created_at->format('d M Y') }}
</span>

</div>

<div class="review-rating">

@for($i=1;$i<=5;$i++)

<i class="fa{{ $i <= $review->rating ? '-solid':'-regular' }} fa-star text-warning"></i>

@endfor

</div>

<h6>{{ $review->title }}</h6>

<p>{{ $review->comment }}</p>

@if($review->would_recommend == 1)

<span class="text-success">✔ Recommends this package</span>

@elseif($review->would_recommend == 0)

<span class="text-danger">✖ Doesn't recommend</span>

@endif

</div>

@empty

<p class="text-muted">No reviews yet.</p>

@endforelse

</div>

</div>

{{-- RELATED PRODUCTS --}}
@if($relatedProducts->count())

<div class="product-details-bottom-box">

<div class="product-details-bottom-box-header">
<h3>Related Packages</h3>
</div>

<div class="other-product-sec">

<div class="other-product-wrapper owl-carousel">

@foreach($relatedProducts as $item)

<div class="other-product-box item">

<div class="product-box">

<div class="product-box-img">
<a href="{{ route('products.show',$item->id) }}">
<img src="{{ asset($item->thumbnail) }}">
</a>
</div>

<div class="product-bottom">

<h3>
<a href="{{ route('products.show',$item->id) }}">
{{ $item->name }}
</a>
</h3>

<div class="product-rating">
<span>⭐ 4.6</span>
</div>

<p class="old-price">
₹ {{ number_format($item->price ,2) }}
</p>

<div class="product-price-row">

<button
class="add-cart-btn related-cart-btn"
data-product-id="{{ $item->id }}"
data-price="{{ $item->price }}"
data-service-days='@json($item->service_days)'
>
Add to Cart · ₹ {{ number_format($item->price,2) }}
</button>


</div>

</div>
</div>
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
</div>
</div>


<script>

        document.addEventListener("DOMContentLoaded",function(){
        
        let price={{ $product->price }};
        let qty=1;
        
        const qtyInput=document.getElementById("quantity");
        const totalPrice=document.getElementById("totalPrice");
        
        const inc=document.getElementById("incrementQty");
        const dec=document.getElementById("decrementQty");
        
        const addBtn=document.getElementById("addToCartBtn");
        
        
        /*
        =================================
        QUANTITY CONTROL
        =================================
        */
        
        if(inc){
        
        inc.addEventListener("click",function(){
        
        qty++;
        
        qtyInput.value=qty;
        
        updateTotal();
        
        });
        
        }
        
        if(dec){
        
        dec.addEventListener("click",function(){
        
        if(qty>1){
        qty--;
        }
        
        qtyInput.value=qty;
        
        updateTotal();
        
        });
        
        }
        
        
        
        
        function updateTotal(){
        
        let total=price*qty;
        
        if(totalPrice){
        totalPrice.innerText=total.toFixed(2);
        }
        
        }
        
        
        
        /*
        =================================
        ADD TO CART (VSHOP STYLE)
        =================================
        */
        
        if(addBtn){
        
        addBtn.addEventListener("click",function(e){
        
        e.preventDefault();
        
        @auth('customer')
        
        const btn=this;
        
        const originalText=btn.innerHTML;
        
        btn.innerHTML='<i class="fa fa-spinner fa-spin"></i> Adding...';
        
        btn.disabled=true;
        
        
        /*
        ==============================
        PAYLOAD
        ==============================
        */
        
        
        
        const payload={
        
        product_id:btn.dataset.productId,
        
        quantity:qty,
        
        price:price,
        
        service_days:btn.dataset.serviceDays,
        
        _token:"{{ csrf_token() }}"
        
        };
        
        
        
        fetch("{{ route('vtravel.cart.add') }}",{
        
        method:"POST",
        
        headers:{
        
        "X-CSRF-TOKEN":"{{ csrf_token() }}",
        
        "X-Requested-With":"XMLHttpRequest",
        
        "Content-Type":"application/json"
        
        },
        
        body:JSON.stringify(payload)
        
        })
        
        .then(res=>res.json())
        
        .then(data=>{
        
        if(data.success){
        
        showToast("Package added to cart","success");
        
        /*
        ==============================
        UPDATE CART COUNT
        ==============================
        */
        
        const cartCount=document.querySelector("#cartCount,.cart-count");
        
        if(cartCount){
        
        cartCount.textContent=data.cart_count;
        
        cartCount.classList.add("cart-bounce");
        
        setTimeout(()=>{
        
        cartCount.classList.remove("cart-bounce");
        
        },500);
        
        }
        
        }else{
        
        showToast(data.message || "Cart error","error");
        
        }
        
        })
        
        .catch(error=>{
        
        console.error(error);
        
        showToast("Server error","error");
        
        })
        
        .finally(()=>{
        
        btn.innerHTML=originalText;
        
        btn.disabled=false;
        
        });
        
        
        @else
        
        showToast("Login required","error");
        
        setTimeout(()=>{
        
        window.location="{{ route('employeelogin') }}";
        
        },1500);
        
        @endauth
        
        
        });
        
        }
        
        
        
        /*
        =================================
        TOAST MESSAGE
        =================================
        */
        
        function showToast(message,type="success"){
        
        let box=document.getElementById("variantMessage");
        
        if(!box){
        
        box=document.createElement("div");
        
        box.id="variantMessage";
        
        box.style.position="fixed";
        
        box.style.bottom="20px";
        
        box.style.right="20px";
        
        box.style.padding="12px 18px";
        
        box.style.borderRadius="6px";
        
        box.style.zIndex="9999";
        
        document.body.appendChild(box);
        
        }
        
        box.style.background= type=="error" ? "#dc3545" : "#28a745";
        
        box.style.color="#fff";
        
        box.innerHTML=message;
        
        box.style.display="block";
        
        setTimeout(()=>{
        
        box.style.display="none";
        
        },3000);
        
        }
        
        });
        
        document.getElementById("openReviewForm").addEventListener("click",function(){

let form=document.getElementById("reviewFormBox");

if(form.style.display==="none"){

form.style.display="block";

}else{

form.style.display="none";

}

});
</script>

<style>

.star-rating{
display:flex;
flex-direction:row-reverse;
justify-content:flex-end;
gap:4px;
}

.star-rating input{
display:none;
}

.star-rating label{
font-size:32px;
color:#ccc;
cursor:pointer;
transition:0.2s;
}

.star-rating input:checked ~ label{
color:#ffc107;
}

.star-rating label:hover,
.star-rating label:hover ~ label{
color:#ffc107;
}

    .other-product-sec{
    margin-bottom:0;
    padding-bottom:0;
    }
    
    .other-product-wrapper{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
    }
    
    .other-product-box{
    flex:0 0 23%;
    margin-bottom:0;
    }
    
    .product-box{
    background:#fff;
    border-radius:10px;
    padding:10px;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
    height:auto;
    }
        .product-meta{
    display:flex;
    gap:10px;
    font-size:14px;
    margin-top:6px;
    color:#666;
    }
    
    .seller-name strong{
    color:#0d6efd;
    }
    .travel-duration-box{
    display:flex;
    gap:20px;
    margin-top:10px;
    }
    
    .duration-item{
    display:flex;
    align-items:center;
    gap:6px;
    background:#f5f7fb;
    padding:6px 12px;
    border-radius:6px;
    font-size:14px;
    }
    
    .duration-item i{
    color:#0d6efd;
    }
    
    .tour-duration{
    margin-top:8px;
    font-size:14px;
    }
    .cart-bounce{
    animation:bounce .5s ease;
    }
    
    @keyframes bounce{
    
    0%,100%{transform:scale(1);}
    50%{transform:scale(1.3);}
    
    }

</style>

@endsection