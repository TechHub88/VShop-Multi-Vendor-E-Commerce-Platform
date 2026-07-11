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

<div class="container-fluid p-0">
<div class="marketplace-wrapper">

<div class="marketplace-left">

<div class="card shadow-sm p-4 mb-4">

<div class="d-flex justify-content-between align-items-center mb-3">

    @php
        $backUrl = (isset($product) && !empty($product->id))
            ? route('seller.products.faq', $product->id)
            : route('seller.products.faq');
    @endphp

    {{-- LEFT: HEADING --}}
    <div class="d-flex align-items-center top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <h5 class="mb-0 d-flex align-items-center gap-2"
            style="
                color:#0d6efd;
                font-weight:600;
            ">
            Download
        </h5>
    </div>

</div>

<form method="POST" action="{{ route('seller.products.download.save',$product->id) }}" style="position:relative;">
@csrf

<div class="d-flex justify-content-end mb-3">
    <!-- ADD MORE (corner plus icon) -->
    <button type="button" class="btn btn-outline-primary rounded-circle shadow-sm" 
        onclick="addDownload()" 
        style="width:38px; height:38px; padding:0; display:flex; align-items:center; justify-content:center; font-size:18px;" 
        title="Add More">
        <i class="fa-solid fa-plus"></i>
    </button>
</div>

<div id="downloadWrapper">

@php
$downloads = old('title') 
    ? collect(old('title'))->map(function($t,$i){
        return [
            'title' => $t,
            'date' => old('date')[$i] ?? '',
            'url' => old('url')[$i] ?? ''
        ];
    })->toArray()
    : (is_array($product->download_data) 
        ? $product->download_data 
        : (json_decode($product->download_data,true) ?? []));

if (empty($downloads)) {
    $downloads = [
        ['title' => '', 'date' => '', 'url' => '']
    ];
}
@endphp


@foreach($downloads as $i => $d)

<div class="download-item position-relative">

    {{-- CROSS DELETE --}}
    <button type="button" class="download-remove-btn remove-download-box">
        <i class="bi bi-x-lg"></i>
    </button>

    <!-- TITLE -->
    <div class="mb-2">
        <label>Title</label>

        <input type="text" 
        name="title[]" 
        value="{{ $d['title'] ?? '' }}" 
        class="form-control @error('title.'.$i) is-invalid @enderror">

        @error('title.'.$i)
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="row">

        <!-- DATE -->
        <div class="col-md-4">
            <label>Date</label>

            <input type="date" 
            name="date[]" 
            value="{{ $d['date'] ?? '' }}" 
            class="form-control @error('date.'.$i) is-invalid @enderror">

            @error('date.'.$i)
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- URL -->
        <div class="col-md-8">
            <label>Download URL</label>

            <input type="text" 
            name="url[]" 
            value="{{ $d['url'] ?? '' }}" 
            class="form-control @error('url.'.$i) is-invalid @enderror">

            @error('url.'.$i)
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

    </div>

</div>

@endforeach

</div>



<!-- SAVE -->
<div class="text-end mt-4">
<button class="btn btn-primary px-5">
Save & Continue →
</button>
</div>

</form>

</div>


{{-- LIST --}}
@if(!empty($downloads))

<div class="card shadow-sm p-3 mt-4">
<h6 class="mb-3">Download List</h6>

@foreach($downloads as $key => $d)

<div class="download-list-item">

    {{-- LEFT --}}
    <div class="download-info">
        <strong>{{ $d['title'] }}</strong>

        <div class="text-muted small">
            {{ $d['date'] }}
        </div>

        <a href="{{ $d['url'] }}" target="_blank">
            Download
        </a>
    </div>

    {{-- RIGHT ACTIONS --}}
    <div class="download-actions">

        {{-- EDIT --}}
        <button type="button"
            class="btn btn-sm btn-primary editDownloadBtn"
            data-title="{{ $d['title'] }}"
            data-date="{{ $d['date'] }}"
            data-url="{{ $d['url'] }}">

            <i class="bi bi-pencil"></i>
        </button>

        {{-- DELETE --}}
        <a href="{{ route('seller.products.download.delete',[$product->id,$key]) }}"
           class="btn btn-sm btn-danger"
           onclick="return confirm('Delete this download?')">

            <i class="bi bi-trash"></i>
        </a>

    </div>

</div>

@endforeach

</div>

@endif

</div>


<!-- RIGHT -->
<div class="marketplace-right">
@include('vshop.seller.partials.product-progress',['product'=>$product])
</div>

</div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>

function addDownload(){

$('#downloadWrapper').append(`

<div class="download-item position-relative">

    <!-- CROSS DELETE -->
    <button type="button" class="download-remove-btn remove-download-box">
        <i class="bi bi-x-lg"></i>
    </button>

    <div class="mb-2">
        <label>Title *</label>
        <input type="text" name="title[]" class="form-control">
    </div>

    <div class="row">

        <div class="col-md-4">
            <label>Date *</label>
            <input type="date" name="date[]" class="form-control">
        </div>

        <div class="col-md-8">
            <label>Download URL *</label>
            <input type="text" name="url[]" class="form-control">
        </div>

    </div>

</div>

`);
}


// REMOVE SECTION
$(document).on('click','.remove-download-box',function(){

    if($('.download-item').length <= 1){
        alert('At least one download section required');
        return;
    }

    $(this).closest('.download-item').remove();

});


// EDIT BUTTON CLICK
$(document).on('click','.editDownloadBtn',function(){

    let title = $(this).data('title');
    let date  = $(this).data('date');
    let url   = $(this).data('url');

    $('input[name="title[]"]').first().val(title);
    $('input[name="date[]"]').first().val(date);
    $('input[name="url[]"]').first().val(url);

    $('html, body').animate({
        scrollTop: $("#downloadWrapper").offset().top - 100
    }, 500);
});

</script>


<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

.download-item{
    border:1px solid #e9ecef;
    border-radius:10px;
    padding:15px;
    margin-bottom:12px;
    background:#fff;
}


.download-remove-btn{
    position:absolute;
    top:10px;
    right:10px;

    border:none;
    background:transparent;

    padding:0;
    margin:0;

    display:flex;
    align-items:center;
    justify-content:center;

    cursor:pointer;
    box-shadow:none;
}

.download-remove-btn i{
    font-size:16px;
    font-weight:700;
    color:#dc3545;
    line-height:1;
}


.download-list-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:14px 0;
    border-bottom:1px solid #eee;
    gap:15px;
}

.download-info{
    flex:1;
}

.download-actions{
    display:flex;
    gap:8px;
    align-items:center;
}

.is-invalid{
    border-color:#dc3545;
}

</style>

@endsection
