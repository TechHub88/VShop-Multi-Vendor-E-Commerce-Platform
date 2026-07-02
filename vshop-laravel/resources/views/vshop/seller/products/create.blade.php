@extends('vshop.seller.main')

@section('title','Add Product')

@section('content')
<div class="formCard">

@if ($errors->any())
<div class="alert alert-danger">
    @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach
</div>
@endif

<form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data">
@csrf

{{-- BASIC --}}
<h5 class="mb-3">Add Products</h5>
<div class="row g-3">

<div class="col-md-6">
<input type="text" name="name" class="form-control" placeholder="Product Name" required>
</div>

@if($userType!='travel')
<div class="col-md-6">
<input type="text" name="sku" class="form-control" placeholder="SKU" required>
</div>

<div class="col-md-6">
<select name="product_type" class="form-select" required>
<option value="">Select Type</option>
<option value="simple">Simple</option>
<option value="attribute">Attribute</option>

</select>
</div>
@endif

<div class="col-md-6">
<select name="category_id" class="form-select" required>
<option value="">Select Category</option>
@foreach($categories as $c)
<option value="{{ $c->id }}">{{ $c->name }}</option>
@endforeach
</select>
</div>

<div class="col-md-6">
<select name="brand_id" class="form-select" required>
<option value="">Select Brand</option>
@foreach($brands as $b)
<option value="{{ $b->id }}">{{ $b->name }}</option>
@endforeach
</select>
</div>

</div>

{{-- PRICING --}}
<h5 class="mt-4">Pricing</h5>
<div class="row g-3">
<div class="col-md-4">
<input type="number" step="0.01" name="price" class="form-control" placeholder="Selling Price" required>
</div>
<div class="col-md-4">
<input type="number" step="0.01" name="regular_price" class="form-control" placeholder="Regular Price">
</div>
<div class="col-md-4">
<input type="number" step="0.01" name="discount_price" class="form-control" placeholder="Discount Price">
</div>
</div>

{{-- TRAVEL --}}
@if($userType=='travel')
<h5 class="mt-4">Tour Details</h5>
<div class="row g-3">
<div class="col-md-6">
<input type="text" name="duration" class="form-control" placeholder="Duration">
</div>
<div class="col-md-6">
<select name="tour_type" class="form-select">
<option value="domestic">Domestic</option>
<option value="international">International</option>
</select>
</div>
</div>
@endif

{{-- MEDIA --}}
<h5 class="mt-4">Media</h5>
<div class="row g-3">
<div class="col-md-6">
<input type="file" name="thumbnail" class="form-control">
</div>
<div class="col-md-6">
<input type="file" name="images[]" class="form-control" multiple>
</div>
</div>

<button class="btn btn-success mt-4">Save Product</button>
</form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".next-tab").forEach(btn => {
        btn.addEventListener("click", function () {
            new bootstrap.Tab(
                document.querySelector('[data-bs-target="' + this.dataset.next + '"]')
            ).show();
        });
    });

    document.querySelectorAll(".prev-tab").forEach(btn => {
        btn.addEventListener("click", function () {
            new bootstrap.Tab(
                document.querySelector('[data-bs-target="' + this.dataset.prev + '"]')
            ).show();
        });
    });

});
</script>
@endpush
