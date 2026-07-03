@extends('vshop.seller.main', ['title' => 'Add Category'])

@section('content')
@php
    $seller = auth()->guard('seller')->user();
    $userType = $seller->type;
    $isTravel = $userType == 'travel';
@endphp

<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h5>Add {{ $isTravel ? 'Travel' : 'Product' }} Category</h5>
        </div>

        <div class="card-body">

            {{-- ================= TRAVEL ================= --}}
            @if($isTravel)

            <form action="{{ route('seller.categories.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf
                <input type="hidden" name="type" value="travel">

                <div class="col-md-6">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Category Title *</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tour Type</label>
                    <select name="tour_type" class="form-select">
                        <option value="domestic">Domestic</option>
                        <option value="international">International</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Ideal For</label>
                    <input type="text" name="ideal_for" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Duration Range</label>
                    <input type="text" name="duration_range" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Best Season</label>
                    <input type="text" name="best_season" class="form-control">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Banner Image</label>
                    <input type="file" name="banner_image" class="form-control">
                </div>

                {{-- SEO --}}
                <div class="col-md-6">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" class="form-control"></textarea>
                </div>

                {{-- SELLER NOTE --}}
                <div class="col-12">
                    <div class="alert alert-info">
                        Category will be sent for <strong>Admin Approval</strong>.
                    </div>
                </div>

                <div class="col-12 text-end">
                    <a href="{{ route('seller.categories.index') }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Save Travel Category</button>
                </div>

            </form>

            {{-- ================= PRODUCT ================= --}}
            @else

            <form action="{{ route('seller.categories.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf
                <input type="hidden" name="type" value="product">

                <div class="col-md-6">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Category Title *</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Banner Image</label>
                    <input type="file" name="banner_image" class="form-control">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                {{-- SEO --}}
                <div class="col-md-6">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" class="form-control"></textarea>
                </div>

                {{-- SELLER NOTE --}}
                <div class="col-12">
                    <div class="alert alert-info">
                        Category will be sent for <strong>Admin Approval</strong>.
                    </div>
                </div>

                <div class="col-12 text-end">
                    <a href="{{ route('seller.categories.index') }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Save Product Category</button>
                </div>

            </form>

            @endif
        </div>
    </div>
</div>
@endsection
