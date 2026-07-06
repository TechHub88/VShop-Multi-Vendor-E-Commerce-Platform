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
.edit-btn {
    width: 32px;
    height: 32px;
}
.top-back-heading-sec h5 {
        color: #0f172a !important;
    }
</style>

@section('content')

<div class="container-fluid py-4">
@include('vshop.seller.partials.marketplace-header',[
'title'=>'SEO'
])


<div class="marketplace-wrapper">

{{-- ================= LEFT ================= --}}
<div class="marketplace-left">

<div class="d-flex justify-content-between align-items-center mb-4">
<div class="d-flex top-back-heading-sec">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <h5 class="">
    SEO List <span class="text-danger">*</span>
    </h5>
</div>


<a href="{{ route('seller.marketplace.seo') }}"
class="add-btn">+</a>
</div>

@if(session('success'))

<div class="alert alert-success successMessage">

    {{ session('success') }}

</div>

@endif


{{-- ================= LIST ================= --}}
@forelse($seos as $seo)

<div class="marketplace-card">

{{-- LEFT INFO --}}
<div class="card-info">

<h6 class="mb-2">
{{ $seo->home_title ?? 'N/A' }}
</h6>

<p class="text-muted small mb-1">
<strong>Description:</strong>
{{ $seo->meta_description ?? 'N/A' }}
</p>

<p class="text-muted small mb-1">
<strong>Keywords:</strong>
{{ $seo->meta_keywords ?? 'N/A' }}
</p>

<p class="text-muted small mb-1">
<strong>Hashtags:</strong>
{{ $seo->search_hashtags ?? 'N/A' }}
</p>

</div>

<div class="card-action">

<div class="action-group">

{{-- EDIT --}}
<a href="{{ route('seller.marketplace.seo.edit',$seo->id) }}"
class="action-btn edit"
title="Edit">
<i class="bi bi-pencil"></i>
</a>

{{-- DELETE --}}
<form action="{{ route('seller.marketplace.seo.delete',$seo->id) }}"
method="POST"
onsubmit="return confirm('Delete this SEO?')">

@csrf
@method('DELETE')

<button type="submit"
class="action-btn delete"
title="Delete">
<i class="bi bi-trash"></i>
</button>

</form>

</div>

</div>

</div>

@empty

<div class="alert alert-info">
No SEO Data Added Yet
</div>

@endforelse

</div>


{{-- ================= RIGHT ================= --}}
<div class="marketplace-right">
@include('vshop.seller.partials.marketplace-progress')
</div>

</div>

</div>

<script>

/* ================= AUTO HIDE SUCCESS ALERT ================= */

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

        }, 5000);

    }

});

</script>

@endsection


{{-- ICON LIBRARY --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


<style>

/* ACTION GROUP */
.action-group{
display:flex;
gap:10px;
}

/* COMMON BUTTON */
.action-btn{
width:36px;
height:36px;
border-radius:10px;
display:flex;
align-items:center;
justify-content:center;
border:none;
cursor:pointer;
font-size:16px;
transition:all 0.25s ease;
background:#f5f7fb;
color:#6b7280;
box-shadow:0 2px 6px rgba(0,0,0,0.05);
}

/* HOVER EFFECT */
.action-btn:hover{
transform:translateY(-2px);
box-shadow:0 6px 14px rgba(0,0,0,0.08);
}

/* EDIT STYLE */
.action-btn.edit:hover{
background:#e6f0ff;
color:#0b4da1;
}

/* DELETE STYLE */
.action-btn.delete:hover{
background:#ffe6e6;
color:#dc3545;
}
/* LAYOUT */
.marketplace-wrapper{
display:flex;
gap:25px;
align-items:flex-start;
}

.marketplace-left{
flex:1;
}

.marketplace-right{
width:300px;
position:sticky;
top:20px;
}

/* CARD */
.marketplace-card{
background:#fff;
padding:18px;
border-radius:12px;
margin-bottom:18px;
box-shadow:0 3px 12px rgba(0,0,0,.05);

display:flex;
justify-content:space-between;
align-items:center;
}

.card-info{
flex:1;
}

.card-action{
margin-left:20px;
}

/* ADD BUTTON */
.add-btn{
width:38px;
height:38px;
border-radius:50%;
background:#f1f3f6;
display:flex;
align-items:center;
justify-content:center;
font-size:22px;
text-decoration:none;
color:#333;
}

/* ICON BUTTON */
.icon-btn{
width:34px;
height:34px;
border-radius:8px;
display:flex;
align-items:center;
justify-content:center;
color:#fff;
border:none;
cursor:pointer;
}

/* EDIT */
.edit-btn{
background:#0b4da1;
}

/* DELETE */
.delete-btn{
background:#dc3545;
}

.icon-btn:hover{
opacity:0.9;
}



</style>
