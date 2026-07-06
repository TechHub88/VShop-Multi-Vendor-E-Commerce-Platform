@extends('layouts.vertical', ['title' => 'Add Category'])

@section('content')
@php
    $user = auth()->user();
    $userType = $user->type; // user table ka 'type' column
    $isTravel = $userType == 'travel';
    $isProduct = $userType == 'product';
@endphp

<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h5>Add {{ $isTravel ? 'Travel' : 'Product' }} Category</h5>
        </div>

        <div class="card-body">
            @if($isTravel)
                <!-- =======================
                     TRAVEL CATEGORY FORM
                     ======================= -->
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    <!-- Hidden type field for travel -->
                    <input type="hidden" name="type" value="travel">

                    {{-- BASIC INFO --}}
                    <div class="col-12">
                        <h4>Basic Information</h4>
                    </div>

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
                        <input type="text" name="ideal_for" class="form-control" placeholder="Family, Honeymoon, Adventure">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Duration Range</label>
                        <input type="text" name="duration_range" class="form-control" placeholder="3–5 Days">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Best Season</label>
                        <input type="text" name="best_season" class="form-control" placeholder="Oct – Mar">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Banner Image</label>
                        <input type="file" name="banner_image" class="form-control">
                    </div>

                    {{-- SEO --}}
                    <div class="col-12 mt-4">
                        <h4>SEO Information</h4>
                    </div>

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
                        <textarea name="meta_description" class="form-control" rows="2"></textarea>
                    </div>

                    {{-- SETTINGS --}}
                    <div class="col-md-4">
                        <label class="form-label">Featured</label>
                        <select name="featured" class="form-select">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Show in Store</label>
                        <select name="show_in_store" class="form-select">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    {{-- SUBMIT --}}
                    <div class="col-12 text-end">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Travel Category</button>
                    </div>
                </form>

            @else
                <!-- =======================
                     PRODUCT CATEGORY FORM
                     ======================= -->
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="row g-3" id="categoryForm">
                    @csrf
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
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Category Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" placeholder="Auto-generated if empty">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Banner Image</label>
                        <input type="file" name="banner_image" class="form-control">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <!-- =======================
                         SEO INFORMATION
                         ======================= -->
                    <div class="col-12 mt-4">
                        <h4 class="mb-3">SEO Information</h4>
                    </div>

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
                        <textarea name="meta_description" class="form-control" rows="3"></textarea>
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
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Show in Store</label>
                        <select name="show_in_store" class="form-select">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

           

                    <!-- Submit -->
                    <div class="col-12 text-end mt-3">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Product Category</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

</div>

@if(!$isTravel)
<!-- JavaScript for Size Guide -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let sizeCounter = 0;
    
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
        const rowId = `size_${sizeCounter}`;
        
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
            <input type="hidden" name="size_guides[${sizeCounter}][unit]" value="${unit}">
            <input type="hidden" name="size_guides[${sizeCounter}][size_label]" value="${sizeLabel}">
            <input type="hidden" name="size_guides[${sizeCounter}][chest]" value="${chest}">
            <input type="hidden" name="size_guides[${sizeCounter}][front_length]" value="${frontLength}">
            <input type="hidden" name="size_guides[${sizeCounter}][sleeve_length]" value="${sleeveLength}">
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
        if (row) row.remove();
        
        // Show no sizes message if table is empty
        const tableBody = document.getElementById(`sizeTableBody${unit.toUpperCase()}`);
        if (tableBody.children.length === 1) { // Only noSizesRow remains
            const noSizesRow = document.getElementById(`noSizesRow${unit.toUpperCase()}`);
            if (noSizesRow) noSizesRow.style.display = '';
        }
    }
});
</script>
@endif

@endsection
