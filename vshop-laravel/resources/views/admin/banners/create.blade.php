@extends('layouts.vertical', ['title' => 'Advertisement Management'])

@section('content')

<style>
    /* Professional Royal Blue Design System */
    :root {
        --royal-blue: #0d6efd;
        --royal-blue-dark: #0b5ed7;
        --royal-blue-darker: #084298;
        --royal-blue-light: #e7f1ff;
    }
    
    .btn-royal-blue {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 600;
        padding: 0.6rem 1.5rem;
        border-radius: 0.6rem;
        transition: all 0.2s;
    }
    
    .btn-royal-blue:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }
    
    .btn-outline-royal {
        border: 1.5px solid #0d6efd;
        color: #0d6efd;
        background: transparent;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        border-radius: 0.6rem;
        transition: all 0.2s;
    }
    
    .btn-outline-royal:hover {
        background: #0d6efd;
        color: white;
    }
    
    .btn-secondary {
        background: #6c757d !important;
        border: 0 !important;
        color: #fff !important;
    }
    
    .btn-secondary:hover {
        background: #5a6268 !important;
        color: #fff;
    }
    
    .main-card {
        border: none !important;
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.05) !important;
        border-radius: 1.25rem !important;
        background: #ffffff;
        overflow: hidden;
    }
    
    .form-label-custom {
        font-size: 0.7rem;
        font-weight: 700;
        color: #1e2a3e;
        margin-bottom: 0.45rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }
    
    .form-control, .form-select {
        border: 1px solid #e2e8f0;
        border-radius: 0.6rem;
        padding: 0.55rem 0.9rem;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }
    
    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0d6efd;
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }
    
    .multi-select-container {
        border: 1px solid #e2e8f0;
        border-radius: 0.6rem;
        padding: 0.5rem;
        max-height: 250px;
        overflow-y: auto;
        background: #ffffff;
    }
    
    .multi-select-item {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        margin-bottom: 0.25rem;
        border-radius: 0.4rem;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .multi-select-item:hover {
        background: #e7f1ff;
    }
    
    .multi-select-item input {
        margin-right: 0.75rem;
        cursor: pointer;
        accent-color: #0d6efd;
    }
    
    .multi-select-item label {
        margin-bottom: 0;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        color: #1e293b;
        flex: 1;
    }
    
    .select-all-btn {
        font-size: 0.7rem;
        padding: 0.2rem 0.6rem;
        border-radius: 1rem;
        background: #eef2ff;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-block;
    }
    
    .select-all-btn:hover {
        background: #0d6efd;
        color: white;
    }
    
    .selected-count {
        font-size: 0.7rem;
        color: #6c757d;
    }
    
    .invalid-feedback-custom {
        font-size: 0.7rem;
        color: #dc3545;
        margin-top: 0.3rem;
        display: block;
        font-weight: 500;
    }
    
    .image-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    
    .image-preview-item {
        position: relative;
        width: 80px;
        height: 80px;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    
    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .remove-preview {
        position: absolute;
        top: 2px;
        right: 2px;
        background: rgba(220, 53, 69, 0.8);
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .remove-preview:hover {
        background: rgba(220, 53, 69, 1);
    }
    
    .seller-group-header {
        background: #f8fafc;
        padding: 0.5rem;
        margin-top: 0.5rem;
        margin-bottom: 0.25rem;
        border-radius: 0.4rem;
        font-weight: 600;
        font-size: 0.8rem;
        color: #0a58ca;
        border-left: 3px solid #0d6efd;
    }
</style>

<div class="container-fluid py-3">

    <!-- Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h4 class="fw-bold text-dark mb-1">Add Advertisement</h4>
            <p class="text-muted mb-0 small">Create banner, campaign, or feature advertisements for products and services</p>
        </div>
        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
            <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-royal rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Banners
            </a>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="card main-card">
        <div class="card-body p-4">

            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" id="bannerForm">
                @csrf

                <!-- Basic Information Section -->
                <h5 class="section-title">
                    <i class="bi bi-info-circle me-2"></i> Basic Information
                </h5>

                <div class="row g-4">
                    <!-- TITLE -->
                    <div class="col-md-6">
                        <label class="form-label-custom">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="Summer Sale 2024">
                        @error('title')
                            <div class="invalid-feedback-custom">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- TYPE -->
                    <div class="col-md-3">
                        <label class="form-label-custom">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            <option value="banner" {{ old('type') == 'banner' ? 'selected' : '' }}>Banner</option>
                            <option value="campaign" {{ old('type') == 'campaign' ? 'selected' : '' }}>Campaign</option>
                            <option value="feature" {{ old('type') == 'feature' ? 'selected' : '' }}>Feature</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback-custom">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- PLATFORM -->
                    <div class="col-md-3">
                        <label class="form-label-custom">Platform <span class="text-danger">*</span></label>
                        <select name="platform" id="platform" class="form-select @error('platform') is-invalid @enderror" required>
                            <option value="">Select Platform</option>
                            <option value="vshop" {{ old('platform') == 'vshop' ? 'selected' : '' }}>Vshop (Products)</option>
                            <option value="vtravel" {{ old('platform') == 'vtravel' ? 'selected' : '' }}>Vtravel (Services)</option>
                            <option value="both" {{ old('platform') == 'both' ? 'selected' : '' }}>Both</option>
                        </select>
                        @error('platform')
                            <div class="invalid-feedback-custom">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Category & Seller Selection -->
                <h5 class="section-title mt-4">
                    <i class="bi bi-tags me-2"></i> Category & Seller Selection
                </h5>

                <div class="row g-4">
                    <!-- CATEGORY -->
                    <div class="col-md-6">
                        <label class="form-label-custom">Category</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="all">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback-custom">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- SELLERS (Multiple Select) -->
                    <div class="col-md-6">
                        <label class="form-label-custom">Select Sellers (Multiple Allowed)</label>
                        <div class="multi-select-container" id="sellerMultiSelect">
                            @php
                                $selectedSellers = old('seller_ids', []);
                            @endphp
                            
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <small class="fw-semibold">Select Sellers</small>
                                <div>
                                    <span class="select-all-btn" onclick="toggleAllSellers(true)">Select All</span>
                                    <span class="select-all-btn ms-1" onclick="toggleAllSellers(false)">Clear All</span>
                                </div>
                            </div>
                            
                            @if(isset($sellers) && count($sellers) > 0)
                                @foreach($sellers as $seller)
                                    <div class="multi-select-item" onclick="toggleSellerCheckbox(this, 'seller_{{ $seller->id }}')">
                                        <input type="checkbox" name="seller_ids[]" value="{{ $seller->id }}" id="seller_{{ $seller->id }}" 
                                            {{ (is_array($selectedSellers) && in_array($seller->id, $selectedSellers)) ? 'checked' : '' }}>
                                        <label for="seller_{{ $seller->id }}">
                                            <strong>{{ $seller->name ?? $seller->shop_name ?? $seller->business_name ?? $seller->store_name ?? 'Seller #'.$seller->id }}</strong>
                                            @if($seller->email)
                                                <small class="text-muted ms-2">({{ $seller->email }})</small>
                                            @endif
                                            @if($seller->mobile)
                                                <small class="text-muted ms-2">({{ $seller->mobile }})</small>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-muted text-center py-3">No sellers available</div>
                            @endif
                            
                            <div class="mt-2 pt-2 border-top">
                                <small class="text-muted selected-count" id="sellerSelectedCount">
                                    Selected: {{ is_array($selectedSellers) ? count($selectedSellers) : 0 }} seller(s)
                                </small>
                            </div>
                        </div>
                        <small class="text-muted">Leave empty to include all sellers, or select specific sellers</small>
                        @error('seller_ids')
                            <div class="invalid-feedback-custom">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Date & Media Section -->
                <h5 class="section-title mt-4">
                    <i class="bi bi-calendar me-2"></i> Schedule & Media
                </h5>

                <div class="row g-4">
                    <!-- START DATE -->
                    <div class="col-md-3">
                        <label class="form-label-custom">Start Date</label>
                        <input type="datetime-local" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}">
                        @error('start_date')
                            <div class="invalid-feedback-custom">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- END DATE -->
                    <div class="col-md-3">
                        <label class="form-label-custom">End Date</label>
                        <input type="datetime-local" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                        @error('end_date')
                            <div class="invalid-feedback-custom">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- BANNER IMAGES -->
                    <div class="col-md-6">
                        <label class="form-label-custom">Upload Banners (Multiple Allowed) <span class="text-danger">*</span></label>
                        <input type="file" name="banners[]" id="bannerImages" class="form-control @error('banners.*') is-invalid @enderror" multiple accept="image/jpeg,image/png,image/jpg,image/webp" required>
                        <small class="text-muted">Supported formats: JPG, PNG, WEBP. Max 5MB each</small>
                        <div class="image-preview" id="imagePreview"></div>
                        @error('banners.*')
                            <div class="invalid-feedback-custom">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Description Section -->
                <h5 class="section-title mt-4">
                    <i class="bi bi-file-text me-2"></i> Description
                </h5>

                <div class="row g-4">
                    <!-- DESCRIPTION -->
                    <div class="col-12">
                        <label class="form-label-custom">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Describe your advertisement...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback-custom">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- External Link & Status -->
                <h5 class="section-title mt-4">
                    <i class="bi bi-link me-2"></i> Additional Settings
                </h5>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label-custom">External Link (Optional)</label>
                        <input type="url" name="external_link" class="form-control @error('external_link') is-invalid @enderror" value="{{ old('external_link') }}" placeholder="https://example.com/promotion">
                        @error('external_link')
                            <div class="invalid-feedback-custom">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback-custom">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-5 pt-3 border-top d-flex justify-content-end gap-3">
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-royal px-4">Cancel</a>
                    <button type="submit" class="btn btn-royal-blue px-5">
                        <i class="bi bi-cloud-upload me-2"></i> Upload Advertisement
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    // Toggle all sellers selection
    window.toggleAllSellers = function(selectAll) {
        const checkboxes = document.querySelectorAll('#sellerMultiSelect input[type="checkbox"]');
        checkboxes.forEach(cb => {
            cb.checked = selectAll;
        });
        updateSellerSelectedCount();
    };
    
    // Toggle seller checkbox when clicking div
    window.toggleSellerCheckbox = function(div, checkboxId) {
        const checkbox = document.getElementById(checkboxId);
        if (checkbox) {
            checkbox.checked = !checkbox.checked;
            updateSellerSelectedCount();
        }
    };
    
    // Update seller selected count
    function updateSellerSelectedCount() {
        const checkboxes = document.querySelectorAll('#sellerMultiSelect input[type="checkbox"]');
        const selectedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        const countSpan = document.getElementById('sellerSelectedCount');
        if (countSpan) {
            countSpan.textContent = `Selected: ${selectedCount} seller${selectedCount !== 1 ? 's' : ''}`;
        }
    }
    
    // Image preview functionality
    document.getElementById('bannerImages')?.addEventListener('change', function(e) {
        const previewContainer = document.getElementById('imagePreview');
        previewContainer.innerHTML = '';
        
        const files = Array.from(e.target.files);
        
        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'image-preview-item';
                    previewItem.innerHTML = `
                        <img src="${event.target.result}" alt="Preview">
                        <button type="button" class="remove-preview" data-index="${index}">&times;</button>
                    `;
                    previewContainer.appendChild(previewItem);
                    
                    // Add remove functionality
                    previewItem.querySelector('.remove-preview')?.addEventListener('click', function() {
                        previewItem.remove();
                    });
                };
                reader.readAsDataURL(file);
            }
        });
    });
    
    // Initialize selected count on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateSellerSelectedCount();
        
        // Add event listeners to seller checkboxes
        document.querySelectorAll('#sellerMultiSelect input[type="checkbox"]').forEach(cb => {
            cb.addEventListener('change', updateSellerSelectedCount);
        });
    });
    
    // Form validation
    const form = document.getElementById('bannerForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const bannerInput = document.getElementById('bannerImages');
            if (bannerInput && bannerInput.files.length === 0) {
                e.preventDefault();
                alert('Please upload at least one banner image.');
                return false;
            }
            return true;
        });
    }
</script>

@endsection
