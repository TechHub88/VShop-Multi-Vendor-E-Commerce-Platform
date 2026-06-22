@extends('vshop.seller.main')

@section('content')

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>

/* =====================================================
CONTAINER
===================================================== */

.preview-page{
    width:100%;
    padding:24px;
}

/* =====================================================
LAYOUT
===================================================== */

.preview-wrapper{
    display:flex;
    gap:24px;
    align-items:flex-start;
    width:100%;
}

.preview-left{
    flex:1;
    min-width:0;
}

.preview-right{
    width:320px;
    flex-shrink:0;
}

/* =====================================================
CARD
===================================================== */

.preview-card{
    background:#fff;
    border-radius:16px;
    padding:24px;
    border:1px solid #e5e7eb;
    box-shadow:0 2px 10px rgba(0,0,0,0.04);
}

/* =====================================================
HEADER
===================================================== */

.preview-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.preview-title{
    font-size:28px;
    font-weight:700;
    color:#111827;
}

.back-btn{
    width:42px;
    height:42px;
    border:none;
    border-radius:10px;
    background:#f3f4f6;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#111827;
    text-decoration:none;
    transition:.2s;
}

.back-btn:hover{
    background:#004392;
    color:#fff;
}

/* =====================================================
THUMBNAIL
===================================================== */

.product-thumb{
    width:160px;
    height:110px;
    object-fit:cover;
    border-radius:12px;
    border:1px solid #e5e7eb;
}

/* =====================================================
CONTENT BOX
===================================================== */

.info-box{
    margin-bottom:28px;
}

.info-box h6{
    font-size:18px;
    font-weight:700;
    margin-bottom:10px;
    color:#111827;
}

.info-box p{
    color:#4b5563;
    line-height:1.8;
    margin-bottom:0;
}

/* =====================================================
TITLE
===================================================== */

.product-name{
    font-size:34px;
    font-weight:700;
    color:#004392;
    margin-bottom:10px;
}

.short-summary{
    font-size:15px;
    color:#6b7280;
    line-height:1.8;
}

/* =====================================================
GALLERY
===================================================== */

.gallery{
    display:flex;
    flex-wrap:wrap;
    gap:12px;
}

.gallery img{
    width:95px;
    height:85px;
    object-fit:cover;
    border-radius:10px;
    border:1px solid #e5e7eb;
    transition:.2s;
}

.gallery img:hover{
    transform:scale(1.04);
}

/* =====================================================
PRICE
===================================================== */

.price-box{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    padding:18px;
    border-radius:12px;
    margin-bottom:25px;
}

.price-box h3{
    margin:0;
    font-size:32px;
    font-weight:700;
    color:#16a34a;
}

/* =====================================================
VARIANTS
===================================================== */

.variant-card{
    background:#f9fafb;
    border:1px solid #e5e7eb;
    border-radius:10px;
    padding:14px;
    margin-bottom:10px;
    line-height:1.8;
}

/* =====================================================
BUTTONS
===================================================== */

.action-buttons{
    display:flex;
    gap:12px;
    justify-content:flex-end;
    flex-wrap:wrap;
    margin-top:35px;
}

.action-buttons .btn{
    min-width:140px;
    border-radius:10px;
    padding:10px 18px;
    font-weight:600;
}

/* =====================================================
RESPONSIVE
===================================================== */

@media(max-width:991px){

    .preview-wrapper{
        flex-direction:column;
    }

    .preview-right{
        width:100%;
    }

    .preview-title{
        font-size:22px;
    }

    .product-name{
        font-size:26px;
    }

    .preview-page{
        padding:15px;
    }
}


</style>


<div class="preview-page">

<div class="preview-wrapper">


{{-- =====================================================
LEFT CONTENT
===================================================== --}}
<div class="preview-left">

<div class="preview-card">


{{-- ================= PAGE HEADER ================= --}}
@php
    $hasId = (isset($product) && !empty($product->id));

    $backUrl = $hasId
        ? route('seller.products.productsreview', $product->id)
        : route('seller.products.productsreview');
@endphp

<div class="d-flex align-items-center gap-3 mb-4">

    <a href="{{ $backUrl }}"
       style="
            width:36px;
            height:36px;
            border:1px solid #0d6efd;
            border-radius:6px;
            display:flex;
            align-items:center;
            justify-content:center;
            text-decoration:none;
            color:#0d6efd;
            background:#fff;
            font-size:16px;
            flex-shrink:0;
       ">

        <i class="fa-solid fa-arrow-left"></i>

    </a>

    <h4 class="mb-0 fw-bold d-flex align-items-center gap-2"
        style="
            color:#0d6efd;
            font-weight:600;
        ">

       

        Product Preview

    </h4>

</div>


{{-- STATUS --}}
@if(isset($product))
    @include('vshop.seller.partials.draft-status', [
        'product' => $product
    ])
@endif


{{-- THUMBNAIL --}}
@if($product->thumbnail)

<div class="mb-4">

<img src="{{ asset(str_replace('\\','/',$product->thumbnail)) }}"
class="product-thumb">

</div>

@endif


{{-- BASIC INFO --}}
<div class="info-box">

<h1 class="product-name">
{{ $product->name }}
</h1>

<div class="short-summary">
{!! $product->short_summary !!}
</div>

</div>


{{-- DESCRIPTION --}}
@if($product->description)

<div class="info-box">

<h6>Description</h6>

<p>
{!! $product->description !!}
</p>

</div>

@endif



{{-- GALLERY --}}
@php
$images = [];

if(!empty($product->images)){
    $images = is_array($product->images)
        ? $product->images
        : json_decode($product->images,true);
}
@endphp

@if(!empty($images))

<div class="info-box">

<h6>Gallery</h6>

<div class="gallery">

@foreach($images as $img)

<img src="{{ asset(str_replace('\\','/',$img)) }}">

@endforeach

</div>

</div>

@endif



{{-- VIDEOS --}}
@php
$videos = [];

if(!empty($product->videos)){
    $videos = is_array($product->videos)
        ? $product->videos
        : json_decode($product->videos,true);
}
@endphp

@if(!empty($videos))

<div class="info-box">

<h6>Videos</h6>

@foreach($videos as $video)

<div class="mb-2">

<a href="{{ $video }}"
target="_blank">

{{ $video }}

</a>

</div>

@endforeach

</div>

@endif



{{-- SPECIFICATIONS --}}
@if($product->specification)

<div class="info-box">

<h6>Specifications</h6>

<p>
{!! $product->specification !!}
</p>

</div>

@endif



{{-- FEATURES --}}
@if($product->features)

<div class="info-box">

<h6>Features</h6>

<p>
{!! $product->features !!}
</p>

</div>

@endif



{{-- SEO --}}
@if($product->meta_title)

<div class="info-box">

<h6>SEO Details</h6>

<p>
<b>Meta Title:</b>
{{ $product->meta_title }}
</p>

<p>
<b>Meta Description:</b>
{{ $product->meta_description }}
</p>

</div>

@endif



{{-- FAQS --}}
@php
$faqs = is_array($product->faqs ?? null) ? $product->faqs : json_decode($product->faqs ?? '[]', true);
@endphp

@if(!empty($faqs))
<div class="info-box">
<h6>FAQs</h6>
@foreach($faqs as $faq)
<div class="mb-3">
    <p class="mb-1 fw-bold text-dark">Q. {{ $faq['question'] ?? '' }}</p>
    <p class="mb-0 text-muted">A. {{ $faq['answer'] ?? '' }}</p>
</div>
@endforeach
</div>
@endif


{{-- DOWNLOADS --}}
@php
$downloads = is_array($product->download_data ?? null) ? $product->download_data : json_decode($product->download_data ?? '[]', true);
@endphp

@if(!empty($downloads))
<div class="info-box">
<h6>Downloads</h6>
@foreach($downloads as $dw)
<div class="mb-2">
    <a href="{{ asset($dw['file'] ?? '') }}" target="_blank" class="text-primary text-decoration-none fw-semibold">
        <i class="fa-solid fa-download me-2"></i> {{ $dw['title'] ?? 'Download File' }}
    </a>
</div>
@endforeach
</div>
@endif


{{-- SAMPLE CODE --}}
@if($product->sample_download_url)
<div class="info-box">
<h6>Sample Code / Reference</h6>
<p>
<a href="{{ $product->sample_download_url }}" target="_blank" class="text-primary text-decoration-none">
    <i class="fa-solid fa-link me-2"></i> {{ $product->sample_download_url }}
</a>
</p>
</div>
@endif


{{-- PRICE --}}
<div class="price-box">

<h3>
₹ {{ number_format($product->price,2) }}
</h3>

</div>



{{-- VARIANTS --}}
@if(isset($attributes) && $attributes->count())

<div class="info-box">

<h6>Variants</h6>

@foreach($attributes as $attr)

<div class="variant-card">

@php
$keys = explode('-', $attr->combination_key);
@endphp

<b>Combination :</b>

@foreach($keys as $k)

{{ $terms[$k] ?? $k }}

@if(!$loop->last)
|
@endif

@endforeach

<br>

<b>Price :</b>
₹{{ $attr->price }}

</div>

@endforeach

</div>

@endif



{{-- ACTION BUTTONS --}}
<div class="action-buttons">

<a href="{{ route('seller.products.basic.edit',$product->id) }}"
class="btn btn-warning">

Edit

</a>


<form action="{{ route('seller.products.destroy',$product->id) }}"
method="POST">

@csrf
@method('DELETE')

<button class="btn btn-danger">

Delete

</button>

</form>


<a href="{{ route('seller.products.mis.dashboard',$product->id) }}"
class="btn btn-success">

Save & Continue

</a>

</div>


</div>

</div>



{{-- =====================================================
RIGHT SIDEBAR
===================================================== --}}
<div class="preview-right">

@include('vshop.seller.partials.product-progress',[
    'product'=>$product
])

</div>


</div>

</div>

@endsection