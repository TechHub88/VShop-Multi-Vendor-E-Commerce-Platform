@extends('vshop.seller.main', ['title' => 'Edit Category'])

@section('content')
@php
    $seller = auth()->guard('seller')->user();
    $userType = $seller->type;
    $isTravel = $userType == 'travel';
@endphp

<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h5>Edit {{ $isTravel ? 'Travel' : 'Product' }} Category</h5>
        </div>

        <div class="card-body">

            {{-- ================= TRAVEL ================= --}}
            @if($isTravel)

            <form action="{{ route('seller.categories.update', $category->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="row g-3">
                @csrf
                @method('PUT')

                <input type="hidden" name="type" value="travel">

                <div class="col-md-6">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $category->name) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Category Title *</label>
                    <input type="text" name="title" class="form-control"
                           value="{{ old('title', $category->title) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tour Type</label>
                    <select name="tour_type" class="form-select">
                        <option value="domestic" {{ $category->tour_type=='domestic'?'selected':'' }}>Domestic</option>
                        <option value="international" {{ $category->tour_type=='international'?'selected':'' }}>International</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Ideal For</label>
                    <input type="text" name="ideal_for" class="form-control"
                           value="{{ old('ideal_for', $category->ideal_for) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Duration Range</label>
                    <input type="text" name="duration_range" class="form-control"
                           value="{{ old('duration_range', $category->duration_range) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Best Season</label>
                    <input type="text" name="best_season" class="form-control"
                           value="{{ old('best_season', $category->best_season) }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control">{{ old('description', $category->description) }}</textarea>
                </div>

                {{-- Banner preview --}}
                @if($category->banner_image)
                <div class="col-md-6">
                    <img src="{{ asset($category->banner_image) }}" width="120" class="mb-2 rounded">
                </div>
                @endif

                <div class="col-md-6">
                    <label class="form-label">Change Banner</label>
                    <input type="file" name="banner_image" class="form-control">
                </div>

                {{-- SEO --}}
                <div class="col-md-6">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control"
                           value="{{ old('meta_title', $category->meta_title) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control"
                           value="{{ old('meta_keywords', $category->meta_keywords) }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" class="form-control">{{ old('meta_description', $category->meta_description) }}</textarea>
                </div>

                <div class="col-12">
                    <div class="alert alert-warning">
                        After update, category will go for <strong>Admin Re-Approval</strong>.
                    </div>
                </div>

                <div class="col-12 text-end">
                    <a href="{{ route('seller.categories.index') }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Update Travel Category</button>
                </div>

            </form>


            {{-- ================= PRODUCT ================= --}}
            @else

            <form action="{{ route('seller.categories.update', $category->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="row g-3">
                @csrf
                @method('PUT')

                <input type="hidden" name="type" value="product">

                <div class="col-md-6">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $category->name) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Category Title *</label>
                    <input type="text" name="title" class="form-control"
                           value="{{ old('title', $category->title) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control"
                           value="{{ old('slug', $category->slug) }}">
                </div>

                {{-- Banner preview --}}
                @if($category->banner_image)
                <div class="col-md-6">
                    <img src="{{ asset($category->banner_image) }}" width="120" class="mb-2 rounded">
                </div>
                @endif

                <div class="col-md-6">
                    <label class="form-label">Change Banner</label>
                    <input type="file" name="banner_image" class="form-control">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control">{{ old('description', $category->description) }}</textarea>
                </div>

                {{-- SEO --}}
                <div class="col-md-6">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control"
                           value="{{ old('meta_title', $category->meta_title) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control"
                           value="{{ old('meta_keywords', $category->meta_keywords) }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" class="form-control">{{ old('meta_description', $category->meta_description) }}</textarea>
                </div>

                <div class="col-12">
                    <div class="alert alert-warning">
                        After update, category will go for <strong>Admin Re-Approval</strong>.
                    </div>
                </div>

                <div class="col-12 text-end">
                    <a href="{{ route('seller.categories.index') }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Update Product Category</button>
                </div>

            </form>

            @endif

        </div>
    </div>
</div>
@endsection
