@extends('vshop.seller.main')

@section('content')

<style>
    .nav-btn {
        border: 1px solid #004392;
        color: #004392;
        background: transparent;
        border-radius: 5px;
        width: 35px;
        height: 38px;
    }

.marketplace-wrapper{
    display:flex;
    gap:24px;
    align-items:flex-start;
    width:100%;
}

.marketplace-left{
    flex:1;
    min-width:0;
}

.marketplace-right{
    width:320px;
    flex-shrink:0;
}

.card{
    border-radius:14px;
    border:1px solid #e5e7eb;
}

.top-back-heading-sec{
    display:flex;
    align-items:center;
    gap:10px;
}

.nav-btn{
    border:none;
    width:38px;
    height:38px;
    border-radius:10px;
    background:#f3f4f6;
}

.is-invalid{
    border-color:#dc3545 !important;
    box-shadow:none !important;
}

.error-text{
    font-size:12px;
    font-weight:500;
}
.top-back-heading-sec h5 {
    color: #0f172a !important;
}

@media(max-width:991px){

    .marketplace-wrapper{
        flex-direction:column;
    }

    .marketplace-right{
        width:100%;
    }
}

</style>


<div class="container-fluid px-3 py-3">

<div class="marketplace-wrapper">


{{-- ================= LEFT CONTENT ================= --}}
<div class="marketplace-left">

<div class="card shadow-sm p-4 mb-4">

<div class="d-flex justify-content-between align-items-center mb-4">

    @php
        $hasId = (isset($product) && !empty($product->id));

        $backUrl = $hasId
            ? route('seller.products.download', $product->id)
            : route('seller.products.download');
    @endphp
    <div class="d-flex align-items-center top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <h5 class="mb-0 d-flex align-items-center gap-2"
            style="
                color:#0d6efd;
                font-weight:600;
            ">
            Sample Code
        </h5>
    </div>

</div>



<form method="POST"
      action="{{ route('seller.products.sample.save',$product->id) }}">

@csrf

<input type="hidden"
       name="edit_mode"
       id="edit_mode"
       value="0">


{{-- LANGUAGE --}}
<div class="mb-3">

<label class="fw-semibold mb-1 d-block">
Language / Technology
<span class="text-danger">*</span>
</label>

<input type="text"
name="language"
id="language"
value="{{ old('language', $product->sample_language) }}"
class="form-control @error('language') is-invalid @enderror">

@error('language')
<small class="text-danger error-text d-block mt-1">
{{ $message }}
</small>
@enderror

</div>


{{-- IDE --}}
<div class="mb-3">

<label class="fw-semibold mb-1 d-block">
IDE / Compiler
<span class="text-danger">*</span>
</label>

<input type="text"
name="ide"
id="ide"
value="{{ old('ide', $product->sample_ide) }}"
class="form-control @error('ide') is-invalid @enderror">

@error('ide')
<small class="text-danger error-text d-block mt-1">
{{ $message }}
</small>
@enderror

</div>


{{-- TYPE --}}
<div class="mb-3">

<label class="fw-semibold mb-1 d-block">
Type
<span class="text-danger">*</span>
</label>

<input type="text"
name="type"
id="type"
value="{{ old('type', $product->sample_type) }}"
class="form-control @error('type') is-invalid @enderror">

@error('type')
<small class="text-danger error-text d-block mt-1">
{{ $message }}
</small>
@enderror

</div>


{{-- OS --}}
<div class="mb-3">

<label class="fw-semibold mb-1 d-block">
Operating System
<span class="text-danger">*</span>
</label>

<input type="text"
name="os"
id="os"
value="{{ old('os', $product->sample_os) }}"
class="form-control @error('os') is-invalid @enderror">

@error('os')
<small class="text-danger error-text d-block mt-1">
{{ $message }}
</small>
@enderror

</div>


{{-- URL --}}
<div class="mb-3">

<label class="fw-semibold mb-1 d-block">
Download URL
<span class="text-danger">*</span>
</label>

<input type="text"
name="url"
id="url"
value="{{ old('url', $product->sample_download_url) }}"
class="form-control @error('url') is-invalid @enderror">

@error('url')
<small class="text-danger error-text d-block mt-1">
{{ $message }}
</small>
@enderror

</div>


<div class="text-end mt-4">

<button class="btn btn-primary px-4">

Save & Continue →

</button>

</div>

</form>

</div>



{{-- ================= SAMPLE LIST ================= --}}
@if($product->sample_language)

<div class="card shadow-sm p-3">

<h6 class="mb-3 fw-bold">

Sample Code List

</h6>

<div class="d-flex justify-content-between align-items-start">

<div>

<p class="mb-1 fw-semibold">
{{ $product->sample_language }}
</p>

<p class="mb-1 text-muted">
{{ $product->sample_ide }}
</p>

<p class="mb-1">
{{ $product->sample_type }}
</p>

<p class="mb-1">
{{ $product->sample_os }}
</p>

<a href="{{ $product->sample_download_url }}"
target="_blank">

Download Link

</a>

</div>


<div class="d-flex gap-2">

<button class="btn btn-sm btn-warning"
onclick="editSample(
'{{ $product->sample_language }}',
'{{ $product->sample_ide }}',
'{{ $product->sample_type }}',
'{{ $product->sample_os }}',
'{{ $product->sample_download_url }}'
)">

<i class="fa fa-edit"></i>

</button>


<a href="{{ route('seller.products.sample.delete',$product->id) }}"
class="btn btn-sm btn-danger">

<i class="fa fa-trash"></i>

</a>

</div>

</div>

</div>

@endif

</div>



{{-- ================= RIGHT SIDEBAR ================= --}}
<div class="marketplace-right">

@include('vshop.seller.partials.product-progress',[
    'product'=>$product
])

</div>


</div>

</div>



<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script>

function editSample(language, ide, type, os, url)
{
    $('#language').val(language);
    $('#ide').val(ide);
    $('#type').val(type);
    $('#os').val(os);
    $('#url').val(url);

    $('#edit_mode').val(1);

    window.scrollTo({
        top:0,
        behavior:'smooth'
    });
}

document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('input').forEach(input => {

        input.addEventListener('input', function () {

            this.classList.remove('is-invalid');

            let error =
            this.parentElement.querySelector('.error-text');

            if(error){
                error.remove();
            }

        });

    });

});

</script>

@endsection
