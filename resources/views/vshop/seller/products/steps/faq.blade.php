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
        font-size: 9px !important; 
        font-weight: 700 !important;
        gap: 6px !important;
        white-space: nowrap;
        margin-top: 28px;
        padding: 8px 18px !important;
        border-radius: 60px !important;
        border: 0 !important;
        background: linear-gradient(180deg, #d7f1ff 0%, #9fd0ff 25%, #7bb8ff 55%, #6aa8ff 75%, #8fd4ff 100%);
        color: #fff !important;
        letter-spacing: 2px;
        line-height: 14px;
        text-transform: uppercase;
    }
    .ai-btn:hover {
        box-shadow: inset 0 4px 10px rgba(255, 255, 255, 0.6), inset 0 -6px 12px rgba(0, 0, 0, 0.12), 0 0 20px rgba(120, 190, 255, 0.8);
    }
    .ai-btn svg {
        width: 22px;
        height: 22px;
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
    .top-back-heading-sec h5 {
    color: #0f172a !important;
}
</style>

@section('content')

<div class="container-fluid px-3 py-3">
<div class="marketplace-wrapper">

<div class="marketplace-left">

<form method="POST" action="{{ route('seller.products.faq.save',$product->id) }}">
@csrf

<div class="card shadow-sm p-4">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">

    @php
        $hasId = (isset($product) && !empty($product->id));

        $backUrl = $hasId
            ? route('seller.products.price.edit', $product->id)
            : route('seller.products.price');
    @endphp

    {{-- LEFT: TITLE --}}
    <div class="d-flex align-items-center top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <h5 class="mb-0 d-flex align-items-center gap-2"
            style="
                color:#0d6efd;
                font-weight:600;
            ">
            FAQ Management
        </h5>
    </div>

    {{-- RIGHT: ACTIONS --}}
    <div class="d-flex gap-2">

        {{-- LIST PAGE --}}
        @if($hasId)

            <a href="{{ route('seller.products.faq.list', $product->id) }}"
               class="btn btn-light btn-sm">

                <i class="bi bi-list"></i>

            </a>

        @endif

    </div>

</div>
@if(isset($product))
    @include('vshop.seller.partials.draft-status', ['product' => $product])
@endif


<!-- ================= FAQ LIST ================= -->
<div id="faqWrapper">

@php
$faqs = is_array($product->faqs) ? $product->faqs : json_decode($product->faqs,true);
@endphp

@if(!empty($faqs))

@foreach($faqs as $faq)

<div class="faq-item">

    <!-- TOP -->
    <div class="faq-top">
        <span class="faq-label">FAQ</span>

        <!-- ❌ ONLY CROSS -->
        <i class="bi bi-x-lg text-danger deleteLocal"></i>
    </div>

    <!-- QUESTION -->
    <div class="input-group mb-2">
        <input type="text" name="question[]" 
        value="{{ $faq['question'] ?? '' }}" 
        class="form-control" placeholder="Enter Question">
    </div>

    <!-- ANSWER -->
    <textarea name="answer[]" class="form-control"
    placeholder="Enter Answer">{{ $faq['answer'] ?? '' }}</textarea>

</div>

@endforeach

@else

<!-- DEFAULT EMPTY -->
<div class="faq-item">

    <div class="faq-top">
        <span class="faq-label">FAQ</span>
        <i class="bi bi-x-lg text-danger deleteLocal"></i>
    </div>

    <div class="input-group mb-2">
        <input type="text" name="question[]" class="form-control" placeholder="Enter Question">
    </div>

    <textarea name="answer[]" class="form-control" placeholder="Enter Answer"></textarea>

</div>

@endif

</div>


<div class="text-end mt-4">

<button type="button" class="add-faq-btn" onclick="addFaq()">
    <i class="bi bi-plus-circle"></i>
    <span>Add FAQ</span>
</button>

</div>

<!-- SAVE -->
<div class="text-end mt-4">
<button class="btn btn-success px-5">
Save & Continue →
</button>
</div>

</div>
</form>

</div>


<!-- RIGHT -->
<div class="marketplace-right">
@include('vshop.seller.partials.product-progress',['product'=>$product])
</div>

</div>
</div>


<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

function addFaq(){

$('#faqWrapper').append(`

<div class="faq-item">

    <div class="faq-top">
        <span class="faq-label">FAQ</span>
        <i class="bi bi-x-lg text-danger deleteLocal"></i>
    </div>

    <div class="input-group mb-2">
        <input type="text" name="question[]" class="form-control" placeholder="Enter Question">
    </div>

    <textarea name="answer[]" class="form-control" placeholder="Enter Answer"></textarea>

</div>

`);
}


// DELETE LOCAL (CROSS)
$(document).on('click','.deleteLocal',function(){
    $(this).closest('.faq-item').remove();
});

</script>


<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

.add-faq-btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:10px 18px;
    border-radius:50px;
    border:1px dashed #0d6efd;
    background:#f5f9ff;
    color:#0d6efd;
    font-weight:500;
    transition:0.2s;
}

.add-faq-btn i{
    font-size:18px;
}

.add-faq-btn:hover{
    background:#e7f0ff;
    transform:translateY(-1px);
}

.faq-item{
    border:1px solid #eef1f4;
    border-radius:12px;
    padding:15px;
    margin-bottom:15px;
    background:#fff;
}

.faq-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:10px;
}

.faq-label{
    font-size:13px;
    color:#6c757d;
    font-weight:600;
}

.deleteLocal{
    cursor:pointer;
    font-size:14px;
    padding:6px;
    border-radius:6px;
}

.deleteLocal:hover{
    background:#ffe5e5;
}

.add-btn{
    border:1px dashed #ccc;
    background:#fafafa;
}

.add-btn:hover{
    background:#f1f3f5;
}

</style>

@endsection