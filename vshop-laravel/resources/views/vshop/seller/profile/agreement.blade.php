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
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-outline-primary {
        border-color: #084f9d !important;
        color: #084f9d !important;
    }
    .btn-outline-primary:hover {
        background: #084f9d !important;
        color: #fff !important;
    }
</style>

@section('content')

@php
$seller = auth()->guard('seller')->user();
@endphp

<div class="container-fluid py-4">
<div class="d-flex top-back-heading-sec mb-4">
   <button onclick="window.location.href='{{ route('seller.payment') }}'" 
        class="nav-btn">
    <i class="fa-solid fa-arrow-left"></i>
</button>
</div>
<div class="marketplace-wrapper">

<div class="marketplace-left">

<form method="POST"
action="{{ route('seller.agreement') }}"
enctype="multipart/form-data">

@csrf



<div class="card shadow-sm border-0">
<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-4">
<h5 class="text-primary mb-0">Seller Agreement Submission</h5>

<a href="{{ route('seller.agreement.list') }}"
class="btn btn-outline-primary btn-sm">
<i class="fa fa-link me-1"></i>
View Agreement List
</a>
</div>

{{-- AGREEMENT TYPE --}}
<div class="mb-4">
<label class="fw-semibold d-block mb-2">
Agreement Type <span class="text-danger">*</span>
</label>

<div class="form-check form-check-inline">
<input class="form-check-input @error('agreement_type') is-invalid @enderror"
type="radio"
name="agreement_type"
value="Digital"
id="digital"
{{ old('agreement_type',$seller->agreement_type ?? 'Digital')=='Digital'?'checked':'' }}>
<label for="digital">Digital Agreement</label>
</div>

<div class="form-check form-check-inline">
<input class="form-check-input @error('agreement_type') is-invalid @enderror"
type="radio"
name="agreement_type"
value="Physical"
id="physical"
{{ old('agreement_type',$seller->agreement_type)=='Physical'?'checked':'' }}>
<label for="physical">Physical Agreement</label>
</div>

@error('agreement_type')
<div class="text-danger small mt-1">{{ $message }}</div>
@enderror
</div>

<hr>

{{-- DOWNLOAD --}}
<div id="downloadSection" class="mb-3">
@if($seller->agreement_file)
<a href="{{ asset($seller->agreement_file) }}"
class="btn btn-outline-primary btn-sm"
download>
<i class="fa fa-download me-1"></i>
Download Agreement
</a>
@else
<div class="text-danger small">
⚠ Please upload agreement first to enable download
</div>
@endif
</div>

{{-- FILE --}}
<div id="uploadSection" class="mb-4">
<label class="fw-semibold">
Upload Signed Agreement <span class="text-danger">*</span>
</label>

<input type="file"
name="agreement_file"
id="agreementFile"
class="form-control @error('agreement_file') is-invalid @enderror">

@error('agreement_file')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

<hr>

<div class="form-check align-items-center d-flex mt-2">

    <input type="checkbox"
        name="agree"
        value="1"
        id="agreeCheck"
        class="form-check-input custom-checkbox
        @error('agree') is-invalid @enderror"
        {{ old('agree', $seller->agreement_accepted_at ? 1 : 0) ? 'checked' : '' }}>

    <label for="agreeCheck" class="form-check-label ms-2">
        I confirm that I agree to the Seller Terms & Conditions
        <span class="text-danger">*</span>
    </label>

</div>

<hr>

{{-- STATUS --}}
@php $status = strtolower($seller->agreement_status ?? 'na'); @endphp
<span class="badge 
{{ $status=='approved'?'bg-success':($status=='rejected'?'bg-danger':($status=='pending'?'bg-warning text-dark':'bg-secondary')) }}">
Verification Status: {{ ucfirst($status) }}
</span>

<div class="text-end mt-4">
<button class="btn btn-success px-5">
<i class="fa fa-check-circle me-2"></i>
Submit Agreement
</button>
</div>

</div>
</div>

</form>

</div>

{{-- RIGHT --}}
<div class="marketplace-right">
@include('vshop.seller.partials.profile-progress')
</div>

</div>
</div>

{{-- CSS --}}
<style>
.custom-checkbox { width:18px; height:18px; }
</style>

{{-- JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){

function toggleAgreement(){
    let type = $('input[name="agreement_type"]:checked').val();

    if(type === 'Digital'){
        $('#uploadSection').hide();
    } else {
        $('#uploadSection').show();
    }
}
toggleAgreement();

$('input[name="agreement_type"]').on('change', toggleAgreement);

// ===== CHECKBOX VALIDATION =====
$('form').on('submit', function(e){

    if(!$('#agreeCheck').is(':checked')){
        e.preventDefault();

        $('#agreeCheck').addClass('is-invalid');
        $('#agreeError').removeClass('d-none');

        return false;
    }

});

// remove error when checked
$('#agreeCheck').on('change', function(){
    if($(this).is(':checked')){
        $(this).removeClass('is-invalid');
        $('#agreeError').addClass('d-none');
    }
});

});
</script>


<style>
    /* FORCE FIX CHECKBOX */
.custom-checkbox {
    width: 18px !important;
    height: 18px !important;
    cursor: pointer;
    accent-color: #198754; /* green */
    border: 1px solid #ccc;
}

/* if something hiding it */
.form-check-input {
    opacity: 1 !important;
    position: relative !important;
    display: inline-block !important;
}

/* fix label alignment */
.form-check {
    gap: 8px;
}
</style>
@endsection
