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
    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-success {
        background: #233d78 !important; 
        border: 0 !important;
    }
</style>

@section('content')

<div class="container-fluid py-4">

<div class="mb-4 d-flex top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <div>
            <h5 class="mb-0">Bulk Customer Upload</h5>
        </div>
    </div>
<h4 class="fw-bold mb-4"></h4>

<div class="card shadow-sm">
<div class="card-body">

{{-- SUCCESS --}}
@if(session('success'))
<div class="alert alert-success auto-hide-alert">
    {{ session('success') }}
</div>
@endif

{{-- ERROR --}}
@if(session('error'))
<div class="alert alert-danger auto-hide-alert">
    {{ session('error') }}
</div>
@endif



{{-- DOWNLOAD SAMPLE --}}
<div class="mb-4">
    <a href="{{ route('seller.customer.sample') }}" class="btn btn-success">
        ⬇ Download Sample Excel
    </a>
</div>

<hr>

{{-- UPLOAD FORM --}}
<form method="POST" action="{{ route('seller.customer.bulk.upload') }}" enctype="multipart/form-data">
@csrf

<div class="mb-3">
    <label class="form-label fw-bold">
        Upload Excel File <span class="text-danger">*</span>
    </label>

    <input type="file" 
        name="bulk_file"
        id="bulk_file"
        class="form-control @error('bulk_file') is-invalid @enderror">

    <small class="text-muted">Allowed: .xlsx, .csv</small>

    {{-- ERROR MESSAGE --}}
    @error('bulk_file')
    <div class="invalid-feedback d-block" id="bulk_file_error">
        {{ $message }}
    </div>
    @enderror
</div>

{{-- BUTTON ROW --}}
<div class="d-flex gap-2">
    <button class="btn btn-primary">
        Upload Customers
    </button>

    {{-- BACK BUTTON --}}
    <a href="{{ url('seller/create') }}" class="btn btn-secondary">
        ← Back
    </a>
</div>

</form>

</div>
</div>

</div>

<style>
.red-star{
    color:#ef4444;
    font-weight:bold;
    margin-right:6px;
}
</style>

{{-- AUTO HIDE ALERT SCRIPT --}}
<script>
setTimeout(() => {
    document.querySelectorAll('.auto-hide-alert').forEach(el => {
        el.style.transition = "opacity 0.5s ease";
        el.style.opacity = "0";
        setTimeout(() => el.remove(), 500);
    });
}, 5000);


// REMOVE VALIDATION ERROR AFTER FILE SELECT
document.getElementById('bulk_file').addEventListener('change', function () {

    if(this.files.length > 0){

        // remove red border
        this.classList.remove('is-invalid');

        // remove error message
        let errorDiv = document.getElementById('bulk_file_error');

        if(errorDiv){
            errorDiv.remove();
        }
    }
});
</script>

@endsection