@extends('layouts.vertical', ['title' => 'Edit Category'])

@section('content')
@php
    $user = auth()->user();
    $userType = $user->type;
    $isTravel = $userType == 'travel';
    $isProduct = $userType == 'product';
    
    $title = $isTravel ? 'Edit Travel Category' : ($isProduct ? 'Edit Product Category' : 'Edit Category');
@endphp

<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h5>{{ $title }}</h5>
        </div>

        <div class="card-body">
            @if($isTravel || ($userType != 'product' && $category->type == 'travel'))
                <!-- =======================
                     TRAVEL CATEGORY EDIT FORM
                     ======================= -->
                <form action="{{ route('admin.categories.update', $category->id) }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="row g-3">
                    @csrf
                    @method('PUT')
                    
                    <!-- Hidden type field for travel -->
                    <input type="hidden" name="type" value="travel">

                    {{-- ================= BASIC INFO ================= --}}
                    <div class="col-12">
                        <h4>Basic Information</h4>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Category Name *</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $category->name) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Category Title *</label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               value="{{ old('title', $category->title) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text"
                               name="slug"
                               class="form-control"
                               value="{{ old('slug', $category->slug) }}"
                               placeholder="Auto-generated if empty">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tour Type</label>
                        <select name="tour_type" class="form-select">
                            <option value="domestic" {{ $category->tour_type == 'domestic' ? 'selected' : '' }}>
                                Domestic
                            </option>
                            <option value="international" {{ $category->tour_type == 'international' ? 'selected' : '' }}>
                                International
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Ideal For</label>
                        <input type="text"
                               name="ideal_for"
                               class="form-control"
                               value="{{ old('ideal_for', $category->ideal_for) }}"
                               placeholder="Family, Honeymoon, Adventure">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Duration Range</label>
                        <input type="text"
                               name="duration_range"
                               class="form-control"
                               value="{{ old('duration_range', $category->duration_range) }}"
                               placeholder="3–5 Days">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Best Season</label>
                        <input type="text"
                               name="best_season"
                               class="form-control"
                               value="{{ old('best_season', $category->best_season) }}"
                               placeholder="Oct – Mar">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="3">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Banner Image</label>
                        <input type="file" name="banner_image" class="form-control">

                        @if($category->banner_image)
                            <img src="{{ asset($category->banner_image) }}"
                                 class="img-thumbnail mt-2"
                                 style="max-height:120px;">
                        @endif
                    </div>

                    {{-- ================= SEO ================= --}}
                    <div class="col-12 mt-4">
                        <h4>SEO Information</h4>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Meta Title</label>
                        <input type="text"
                               name="meta_title"
                               class="form-control"
                               value="{{ old('meta_title', $category->meta_title) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text"
                               name="meta_keywords"
                               class="form-control"
                               value="{{ old('meta_keywords', $category->meta_keywords) }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description"
                                  class="form-control"
                                  rows="2">{{ old('meta_description', $category->meta_description) }}</textarea>
                    </div>

                    {{-- ================= SETTINGS ================= --}}
                    <div class="col-md-4">
                        <label class="form-label">Featured</label>
                        <select name="featured" class="form-select">
                            <option value="0" {{ $category->featured == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $category->featured == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Show in Store</label>
                        <select name="show_in_store" class="form-select">
                            <option value="1" {{ $category->show_in_store == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ $category->show_in_store == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ $category->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $category->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- ================= SUBMIT ================= --}}
                    <div class="col-12 text-end mt-3">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            Update Travel Category
                        </button>
                    </div>
                </form>

            @else
                <!-- =======================
                     PRODUCT CATEGORY EDIT FORM
                     ======================= -->
                <form action="{{ route('admin.categories.update', $category->id) }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="row g-3"
                      id="categoryForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Hidden type field for product -->
                    <input type="hidden" name="type" value="product">

                    <!-- =======================
                         BASIC INFORMATION
                         ======================= -->
                    <div class="col-12">
                        <h4 class="mb-3">Basic Information</h4>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $category->name) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Category Title <span class="text-danger">*</span></label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               value="{{ old('title', $category->title) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text"
                               name="slug"
                               class="form-control"
                               value="{{ old('slug', $category->slug) }}"
                               placeholder="Auto-generated if empty">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Banner Image</label>
                        <input type="file" name="banner_image" class="form-control">
                        @if($category->banner_image)
                            <img src="{{ asset($category->banner_image) }}"
                                 class="img-thumbnail mt-2"
                                 style="max-height:120px;">
                        @endif
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="3">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <!-- =======================
                         SEO INFORMATION
                         ======================= -->
                    <div class="col-12 mt-4">
                        <h4 class="mb-3">SEO Information</h4>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Meta Title</label>
                        <input type="text"
                               name="meta_title"
                               class="form-control"
                               value="{{ old('meta_title', $category->meta_title) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text"
                               name="meta_keywords"
                               class="form-control"
                               value="{{ old('meta_keywords', $category->meta_keywords) }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description"
                                  class="form-control"
                                  rows="3">{{ old('meta_description', $category->meta_description) }}</textarea>
                    </div>

                    <!-- =======================
                         SETTINGS
                         ======================= -->
                    <div class="col-12 mt-4">
                        <h4 class="mb-3">Settings</h4>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Featured</label>
                        <select name="featured" class="form-select">
                            <option value="0" {{ $category->featured == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $category->featured == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Show in Store</label>
                        <select name="show_in_store" class="form-select">
                            <option value="1" {{ $category->show_in_store == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ $category->show_in_store == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ $category->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $category->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- =======================
                         SIZE GUIDE
                         ======================= -->
                    <div class="col-12 mt-4">
                        <h4 class="mb-3">Size Guide</h4>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label class="form-label">Size Guide Image</label>
                        <input type="file" name="size_guide_image" class="form-control">
                        <small class="text-muted">Upload a size guide chart/image for reference</small>
                        
                        @if($category->size_guide_image)
                            <div class="mt-2">
                                <img src="{{ asset($category->size_guide_image) }}"
                                     class="img-thumbnail"
                                     style="max-height:120px;">
                            </div>
                        @endif
                    </div>

                    <!-- =======================
                         SIZE GUIDE - INCHES
                         ======================= -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header"><h5>Add Size Measurements (IN)</h5></div>
                            <div class="card-body">
                                <div class="row g-3" id="sizeEntryFormIN">
                                    <div class="col-md-3">
                                        <label class="form-label">Size Label</label>
                                        <select class="form-select" id="size_label_in">
                                            <option value="">Select Size</option>
                                            <option value="XS">XS</option>
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                            <option value="XL">XL</option>
                                            <option value="XXL">XXL</option>
                                            <option value="XXXL">XXXL</option>
                                            <option value="Custom">Custom</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Chest (IN)</label>
                                        <input type="number" step="0.01" class="form-control" id="chest_in">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Front Length (IN)</label>
                                        <input type="number" step="0.01" class="form-control" id="front_length_in">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Sleeve Length (IN)</label>
                                        <input type="number" step="0.01" class="form-control" id="sleeve_length_in">
                                    </div>
                                    <div class="col-md-12 mt-2 d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary" id="addSizeInBtn">
                                            <i class="mdi mdi-plus"></i> Add Size (IN)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- INCHES Table -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header"><h5>Added Sizes (IN)</h5></div>
                            <div class="card-body">
                                <table class="table table-bordered" id="sizeTableIN">
                                    <thead>
                                        <tr>
                                            <th>Size Label</th>
                                            <th>Chest (IN)</th>
                                            <th>Front Length (IN)</th>
                                            <th>Sleeve Length (IN)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sizeTableBodyIN">
                                        @if($category->sizeGuides->where('unit', 'in')->count() > 0)
                                            @foreach($category->sizeGuides->where('unit', 'in') as $index => $size)
                                                <tr id="size_in_{{ $index }}">
                                                    <td>{{ $size->size_label }}</td>
                                                    <td>{{ $size->chest }}</td>
                                                    <td>{{ $size->front_length }}</td>
                                                    <td>{{ $size->sleeve_length }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeSize('size_in_{{ $index }}', 'in')">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr id="noSizesRowIN">
                                                <td colspan="5" class="text-center text-muted">No sizes added yet</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- =======================
                         SIZE GUIDE - CM
                         ======================= -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header"><h5>Add Size Measurements (CM)</h5></div>
                            <div class="card-body">
                                <div class="row g-3" id="sizeEntryFormCM">
                                    <div class="col-md-3">
                                        <label class="form-label">Size Label</label>
                                        <select class="form-select" id="size_label_cm">
                                            <option value="">Select Size</option>
                                            <option value="XS">XS</option>
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                            <option value="XL">XL</option>
                                            <option value="XXL">XXL</option>
                                            <option value="XXXL">XXXL</option>
                                            <option value="Custom">Custom</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Chest (CM)</label>
                                        <input type="number" step="0.01" class="form-control" id="chest_cm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Front Length (CM)</label>
                                        <input type="number" step="0.01" class="form-control" id="front_length_cm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Sleeve Length (CM)</label>
                                        <input type="number" step="0.01" class="form-control" id="sleeve_length_cm">
                                    </div>
                                    <div class="col-md-12 mt-2 d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary" id="addSizeCmBtn">
                                            <i class="mdi mdi-plus"></i> Add Size (CM)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CM Table -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header"><h5>Added Sizes (CM)</h5></div>
                            <div class="card-body">
                                <table class="table table-bordered" id="sizeTableCM">
                                    <thead>
                                        <tr>
                                            <th>Size Label</th>
                                            <th>Chest (CM)</th>
                                            <th>Front Length (CM)</th>
                                            <th>Sleeve Length (CM)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sizeTableBodyCM">
                                        @if($category->sizeGuides->where('unit', 'cm')->count() > 0)
                                            @foreach($category->sizeGuides->where('unit', 'cm') as $index => $size)
                                                <tr id="size_cm_{{ $index }}">
                                                    <td>{{ $size->size_label }}</td>
                                                    <td>{{ $size->chest }}</td>
                                                    <td>{{ $size->front_length }}</td>
                                                    <td>{{ $size->sleeve_length }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeSize('size_cm_{{ $index }}', 'cm')">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr id="noSizesRowCM">
                                                <td colspan="5" class="text-center text-muted">No sizes added yet</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Inputs for existing sizes -->
                    <div id="sizeInputsContainer">
                        @foreach($category->sizeGuides as $index => $size)
                            <input type="hidden" name="size_guides[{{ $index }}][id]" value="{{ $size->id }}">
                            <input type="hidden" name="size_guides[{{ $index }}][unit]" value="{{ $size->unit }}">
                            <input type="hidden" name="size_guides[{{ $index }}][size_label]" value="{{ $size->size_label }}">
                            <input type="hidden" name="size_guides[{{ $index }}][chest]" value="{{ $size->chest }}">
                            <input type="hidden" name="size_guides[{{ $index }}][front_length]" value="{{ $size->front_length }}">
                            <input type="hidden" name="size_guides[{{ $index }}][sleeve_length]" value="{{ $size->sleeve_length }}">
                        @endforeach
                    </div>

                    <!-- Submit -->
                    <div class="col-12 text-end mt-3">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Product Category</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

</div>

@if($isProduct || $category->type == 'product')
<!-- JavaScript for Size Guide -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let sizeCounter = {{ $category->sizeGuides->count() }};
    
    // Function to add size (IN)
    document.getElementById('addSizeInBtn').addEventListener('click', function() {
        addSize('in');
    });
    
    // Function to add size (CM)
    document.getElementById('addSizeCmBtn').addEventListener('click', function() {
        addSize('cm');
    });
    
    function addSize(unit) {
        const sizeLabel = document.getElementById(`size_label_${unit}`).value;
        const chest = document.getElementById(`chest_${unit}`).value;
        const frontLength = document.getElementById(`front_length_${unit}`).value;
        const sleeveLength = document.getElementById(`sleeve_length_${unit}`).value;
        
        if (!sizeLabel || !chest || !frontLength || !sleeveLength) {
            alert('Please fill all fields');
            return;
        }
        
        sizeCounter++;
        const rowId = `size_${unit}_new_${sizeCounter}`;
        
        // Add to table
        const tableBody = document.getElementById(`sizeTableBody${unit.toUpperCase()}`);
        const noSizesRow = document.getElementById(`noSizesRow${unit.toUpperCase()}`);
        
        if (noSizesRow) noSizesRow.style.display = 'none';
        
        const newRow = document.createElement('tr');
        newRow.id = rowId;
        newRow.innerHTML = `
            <td>${sizeLabel}</td>
            <td>${chest}</td>
            <td>${frontLength}</td>
            <td>${sleeveLength}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeSize('${rowId}', '${unit}')">
                    <i class="mdi mdi-delete"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(newRow);
        
        // Add hidden inputs
        const container = document.getElementById('sizeInputsContainer');
        container.innerHTML += `
            <input type="hidden" name="size_guides[new_${sizeCounter}][unit]" value="${unit}">
            <input type="hidden" name="size_guides[new_${sizeCounter}][size_label]" value="${sizeLabel}">
            <input type="hidden" name="size_guides[new_${sizeCounter}][chest]" value="${chest}">
            <input type="hidden" name="size_guides[new_${sizeCounter}][front_length]" value="${frontLength}">
            <input type="hidden" name="size_guides[new_${sizeCounter}][sleeve_length]" value="${sleeveLength}">
        `;
        
        // Clear form
        document.getElementById(`size_label_${unit}`).value = '';
        document.getElementById(`chest_${unit}`).value = '';
        document.getElementById(`front_length_${unit}`).value = '';
        document.getElementById(`sleeve_length_${unit}`).value = '';
    }
    
    // Global function to remove size
    window.removeSize = function(rowId, unit) {
        const row = document.getElementById(rowId);
        if (row) {
            row.remove();
            
            // Add deletion marker for existing sizes
            if (rowId.includes('_new_')) {
                // New size - just remove the hidden input
                const hiddenInputs = document.querySelectorAll(`input[value*="${rowId}"]`);
                hiddenInputs.forEach(input => input.remove());
            } else {
                // Existing size - mark for deletion
                const sizeId = rowId.split('_')[2];
                const container = document.getElementById('sizeInputsContainer');
                container.innerHTML += `<input type="hidden" name="deleted_size_ids[]" value="${sizeId}">`;
            }
            
            // Show no sizes message if table is empty
            const tableBody = document.getElementById(`sizeTableBody${unit.toUpperCase()}`);
            const rows = tableBody.querySelectorAll('tr');
            let hasVisibleRows = false;
            
            rows.forEach(row => {
                if (row.style.display !== 'none' && row.id !== `noSizesRow${unit.toUpperCase()}`) {
                    hasVisibleRows = true;
                }
            });
            
            if (!hasVisibleRows) {
                const noSizesRow = document.getElementById(`noSizesRow${unit.toUpperCase()}`);
                if (noSizesRow) noSizesRow.style.display = '';
            }
        }
    }
});
</script>
@endif

@endsection
