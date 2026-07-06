@extends('vshop.seller.main')
<style>
    .nav-btn {
        border: 1px solid #004392;
        color: #004392;
        background: transparent;
        border-radius: 5px;
        width: 35px;
        height: 38px;
    }
    .bulk-btn {
        background-color : #004392 !important;
        border-color: #004392 !important;
        color: #fff !important;
        padding: 8px 8px !important;
        height: 36px !important;
    }
    .ai-btn {
        display: flex !important;
        align-items: center !important;
        font-size: 12px !important; 
        font-weight: 700 !important;
        gap: 6px !important;
        white-space: nowrap;
        margin-top: 32px;
        padding-top: 7px !important;
    }
    .ai-btn i {
        color: orange;
        font-size: 14px;
    }
    .top-back-heading-sec {
        align-items: center;
        gap: 10px;
    }
    .marketplace-wrapper {
        display: flex;
        gap: 25px;
        align-items: flex-start;
    }
    .marketplace-left {
        flex: 1;
    }
    .marketplace-right {
        width: 300px;
    }
</style>

@section('content')

<div class="container-fluid px-3 py-3">
<div class="marketplace-wrapper">

<div class="marketplace-left">

<div class="card shadow-sm p-4">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">

    {{-- LEFT --}}
     <div class="d-flex top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <h5 class="mb-0">
             FAQ Listing
        </h5>
    </div>

    {{-- RIGHT ACTIONS --}}
    <div class="d-flex gap-2">

      

        <!-- ADD FAQ -->
        <a href="{{ route('seller.products.faq',$product->id) }}" 
           class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Add FAQ
        </a>

    </div>

</div>

@if(isset($product))
    @include('vshop.seller.partials.draft-status', ['product' => $product])
@endif


@if(!empty($faqs))

@foreach($faqs as $key => $faq)

<div class="faq-row">

    <!-- LEFT -->
    <div class="faq-content">
        <div class="faq-question">
            {{ $faq['question'] ?? '' }}
        </div>

        <div class="faq-answer">
            {{ $faq['answer'] ?? '' }}
        </div>
    </div>

    <!-- RIGHT -->
    <div class="faq-actions">

        <!-- EDIT -->
        <a href="{{ route('seller.products.faq.edit',[$product->id,$key]) }}"
           class="btn-icon edit" title="Edit">
           <i class="bi bi-pencil"></i>
        </a>

        <!-- DELETE -->
        <a href="{{ route('seller.products.faq.delete',[$product->id,$key]) }}"
           onclick="return confirm('Delete this FAQ?')"
           class="btn-icon delete" title="Delete">
           <i class="bi bi-trash"></i>
        </a>

    </div>

</div>

@endforeach

@else

<div class="text-center text-muted py-5">
    <i class="bi bi-question-circle fs-1 mb-2"></i>
    <div>No FAQ found</div>
</div>

@endif

</div>

</div>


<!-- RIGHT SIDE -->
<div class="marketplace-right">
@include('vshop.seller.partials.product-progress',['product'=>$product])
</div>

</div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

.faq-row{
display:flex;
justify-content:space-between;
align-items:flex-start;
border:1px solid #eef1f4;
padding:15px;
border-radius:12px;
margin-bottom:12px;
background:#fff;
transition:0.2s;
}

.faq-row:hover{
box-shadow:0 6px 18px rgba(0,0,0,0.06);
}

.faq-question{
font-weight:600;
margin-bottom:5px;
}

.faq-answer{
font-size:14px;
color:#666;
}

.faq-actions{
display:flex;
gap:8px;
}

.btn-icon{
width:36px;
height:36px;
border-radius:8px;
display:flex;
align-items:center;
justify-content:center;
text-decoration:none;
transition:0.2s;
}

.btn-icon.edit{
background:#eef3ff;
color:#0d6efd;
}

.btn-icon.edit:hover{
background:#dbe7ff;
}

.btn-icon.delete{
background:#ffe5e5;
color:#dc3545;
}

.btn-icon.delete:hover{
background:#ffd6d6;
}

</style>

@endsection
