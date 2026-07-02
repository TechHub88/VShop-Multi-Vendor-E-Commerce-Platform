@extends('vshop.seller.main')
<style>
    .nav-btn {
        border: 1px solid #004392;
        color: #004392;
        background: transparent;
        border-radius: 5px;
        font-size: 4px;
        width: 35px;
        height: 38px;
    }
    .top-back-heading-sec {
        align-items: center;
        gap: 10px;
    }
    .top-back-heading-sec h5 {
        color: #0f172a !important;
    }
    .edit-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef3fb !important;
        border-radius: 6px;
        color: #0b4da1 !important;
        text-decoration: none;
        font-size: 15px !important;
    }
    .delete-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffecec !important;
        border-radius: 6px;
        color: #d11a2a !important;
        text-decoration: none;
        font-size: 14px !important;
    }
    .action-icons {
        display: flex;
    }
</style>

@section('content')

<div class="container-fluid py-3">

@include('vshop.seller.partials.marketplace-header',['title'=>'Banner'])

<div class="marketplace-wrapper">

<div class="marketplace-left">

<div class="d-flex justify-content-between align-items-center mb-4">
<div class="d-flex top-back-heading-sec">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <h5>Banner</h5>
</div>

<a href="{{ route('seller.marketplace.banner') }}" class="add-btn">+</a>
</div>

@if(session('success'))

<div class="alert alert-success successMessage">

    {{ session('success') }}

</div>

@endif

@forelse($banners as $banner)

@php
$images = is_array($banner->banner_image)
    ? $banner->banner_image
    : json_decode($banner->banner_image, true);
@endphp

<div class="banner-card">

{{-- LEFT TEXT --}}
<div class="banner-info">

<h6 class="banner-title">
{{ $banner->banner_title }}
</h6>

<p class="banner-sub">
Banner Sub Title :
{{ $banner->banner_sub_title ?? '-' }}
</p>

<span class="status {{ $banner->status }}">
{{ ucfirst($banner->status) }}
</span>

</div>


{{-- RIGHT --}}
<div class="banner-actions">

{{-- MULTIPLE IMAGES --}}
<div class="banner-images">

@foreach($images as $img)
    <img src="{{ asset($img) }}" class="banner-thumb">
@endforeach

</div>

<div class="action-icons">

<a href="{{ route('seller.marketplace.banner.edit',$banner->id) }}"
class="edit-icon">✎</a>

<form action="{{ route('seller.marketplace.banner.delete',$banner->id) }}"
method="POST" style="display:inline-block">

@csrf
@method('DELETE')

<button class="delete-icon"
onclick="return confirm('Delete this banner?')">
🗑
</button>

</form>

</div>

</div>

</div>

@empty
<div class="alert alert-info">No Banner Uploaded</div>
@endforelse

</div>



<div class="marketplace-right">
@include('vshop.seller.partials.marketplace-progress')
</div>

</div>

</div>


<style>

/* LAYOUT */
.marketplace-wrapper{
display:flex;
gap:25px;
}

.marketplace-left{ flex:1; }

.marketplace-right{
width:300px;
position:sticky;
top:20px;
}

/* ADD BTN */
.add-btn{
width:38px;
height:38px;
border-radius:50%;
background:#eef2f7;
display:flex;
align-items:center;
justify-content:center;
font-size:22px;
text-decoration:none;
color:#333;
}

/* CARD */
.banner-card{
background:#fff;
padding:18px 22px;
border-radius:12px;
margin-bottom:18px;
box-shadow:0 2px 10px rgba(0,0,0,.05);
display:flex;
justify-content:space-between;
align-items:center;
}

/* TEXT */
.banner-title{
font-weight:600;
color:#0b4da1;
margin-bottom:4px;
}

.banner-sub{
font-size:13px;
color:#777;
margin-bottom:8px;
}

/* STATUS */
.status{
font-size:12px;
padding:4px 12px;
border-radius:20px;
display:inline-block;
}

.status.pending{
background:#fff3cd;
color:#856404;
}

.status.approved{
background:#d4edda;
color:#2e7d32;
}

.status.rejected{
background:#f8d7da;
color:#c62828;
}

/* RIGHT */
.banner-actions{
display:flex;
align-items:center;
gap:15px;
}

/* MULTI IMAGE */
.banner-images{
display:flex;
gap:6px;
flex-wrap:wrap;
max-width:220px;
}

.banner-thumb{
width:70px;
height:50px;
object-fit:cover;
border-radius:6px;
border:1px solid #eee;
}

/* ICONS */
.action-icons a,
.action-icons button{
margin-left:6px;
font-size:16px;
background:none;
border:none;
cursor:pointer;
}

.edit-icon{ color:#0b4da1; }
.delete-icon{ color:#dc3545; }

</style>

<script>

document.addEventListener('DOMContentLoaded', function () {

    let successBox = document.querySelector('.successMessage');

    if(successBox){

        setTimeout(function(){

            successBox.style.transition = "all 0.5s ease";
            successBox.style.opacity = "0";
            successBox.style.transform = "translateY(-10px)";

            setTimeout(function(){

                successBox.remove();

            }, 500);

        }, 3500);

    }

});

</script>
@endsection