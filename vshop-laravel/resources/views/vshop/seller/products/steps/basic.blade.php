@extends('vshop.seller.main')

<style>
    .bulk-btn {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border-color: transparent !important;
        color: #fff !important;
        padding: 8px 8px !important;
        height: 36px !important;
    }
    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%) !important;
    }
    .ai-btn {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 50%;
        background: #fff;
        border: 1px solid rgba(0, 82, 204, 0.25) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer;
        box-shadow: 0 0 0 0 rgba(0, 82, 204, 0.4);
        animation: pulse-vai 2s infinite;
        transition: all 0.3s ease;
        padding: 0 !important;
        flex-shrink: 0;
        margin-top: 28px;
        overflow: hidden;
    }
    .ai-btn:hover {
        transform: scale(1.08);
        border-color: rgba(0, 82, 204, 0.5) !important;
        box-shadow: 0 4px 15px rgba(0, 82, 204, 0.3);
    }
    .ai-btn img {
        width: 38px;
        height: 38px;
        object-fit: contain;
    }
    .ai-btn svg, .ai-btn span {
        display: none !important;
    }
    @keyframes pulse-vai {
        0% { box-shadow: 0 0 0 0 rgba(0, 82, 204, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(0, 82, 204, 0); }
        100% { box-shadow: 0 0 0 0 rgba(0, 82, 204, 0); }
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
    .listing-type-dropdown {
        margin-top: 15px;
        margin-bottom: 20px;
        padding: 15px;
        background: #ffffff;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    #listingTypeDropdown {
        max-height: 200px;
        overflow-y: auto;
        font-size: 14px;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: #fff;
    }
    #listingTypeDropdown:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    #listingTypeDropdown::-webkit-scrollbar {
        width: 8px;
    }
    #listingTypeDropdown::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    #listingTypeDropdown::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    #listingTypeDropdown::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    .inventory-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
        border: 1px solid #e9ecef;
    }
    .top-back-heading-sec h5 {
        color: #0f172a !important;
    }
</style>

@section('title','Product - Basic Details')

@section('content')

<div class="container-fluid py-4">
<div class="marketplace-wrapper">

{{-- ================= LEFT ================= --}}
<div class="marketplace-left">
<div class="card shadow-sm border-0">
<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-4">

    {{-- LEFT --}}
    <div class="d-flex top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <h5 class="text-primary mb-0">Basic Details</h5>
    </div>

    {{-- RIGHT ACTIONS --}}
    <div class="d-flex gap-2">
        <a href="{{ route('seller.products.bulk') }}" 
           class="btn btn-sm bulk-btn">
            Bulk Upload
        </a>
    </div>

</div>

@if(isset($product))
    @include('vshop.seller.partials.draft-status', ['product' => $product])
@endif

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" id="basicForm"
action="{{ $product->id 
    ? route('seller.products.basic.update',$product->id) 
    : route('seller.products.basic.save') }}">
@csrf

<div class="row g-3">

<hr class="my-4">

<div class="row g-3 align-items-end">

   {{-- ================= TYPE ================= --}}
<div class="col-12">

    <label class="fw-bold">
        Choose Type <span class="text-danger">*</span>
    </label>

    <div class="mt-2 d-flex gap-4 flex-wrap">

        {{-- ================= ITEMS (VSHOP ONLY) ================= --}}
        @if(in_array('product', session('selected_panels', [])))
            <label class="me-3">
                <input type="radio"
                       name="type"
                       value="vshop-product"
                       class="me-1 type-radio"
                       {{ in_array(old('type', $product->type ?? ''), ['product', 'vshop-product']) ? 'checked' : '' }}>
                Items
            </label>
        @endif

        {{-- ================= PROFESSIONAL (One Time Only) ================= --}}
        @php
            $profValue = 'professional';
            if (in_array('product', session('selected_panels', [])) && !in_array('travel', session('selected_panels', []))) {
                $profValue = 'vshop-professional';
            } elseif (!in_array('product', session('selected_panels', [])) && in_array('travel', session('selected_panels', []))) {
                $profValue = 'vtravel-professional';
            }
        @endphp
        @if(in_array('product', session('selected_panels', [])) || in_array('travel', session('selected_panels', [])))
            <label class="me-3">
                <input type="radio"
                       name="type"
                       id="professionalRadio"
                       value="{{ $profValue }}"
                       class="me-1 type-radio"
                       {{ in_array(old('type', $product->type ?? ''), ['professional', 'vshop-professional', 'vtravel-professional']) ? 'checked' : '' }}>
                Professional
            </label>
        @endif

        {{-- ================= TOUR & INFO (VTRAVEL ONLY) ================= --}}
        @if(in_array('travel', session('selected_panels', [])))
            <label class="me-3">
                <input type="radio"
                       name="type"
                       value="vtravel-tour"
                       class="me-1 type-radio"
                       {{ in_array(old('type', $product->type ?? ''), ['tour', 'vtravel-tour']) ? 'checked' : '' }}>
                Tour Guidance
            </label>

            <label class="me-3">
                <input type="radio"
                       name="type"
                       value="vtravel-information_advisory"
                       class="me-1 type-radio"
                       {{ in_array(old('type', $product->type ?? ''), ['information_advisory', 'vtravel-information_advisory']) ? 'checked' : '' }}>
                Information &amp; Advisory
            </label>
        @endif

        {{-- ================= OTHERS (One Time Only) ================= --}}
        @if(in_array('product', session('selected_panels', [])) || in_array('travel', session('selected_panels', [])))
            <label class="me-3">
                <input type="radio"
                       name="type"
                       value="service"
                       class="me-1 type-radio"
                       {{ in_array(old('type', $product->type ?? ''), ['service', 'vshop-service', 'vtravel-service']) ? 'checked' : '' }}>
                Others
            </label>
        @endif

        {{-- ================= CAMPUS ================= --}}
        @if(in_array('vcampus', session('selected_panels', [])))
            <label class="me-3">
                <input type="radio"
                       name="type"
                       value="campus"
                       class="me-1 type-radio"
                       {{ old('type', $product->type ?? '') == 'campus' ? 'checked' : '' }}>
                Course / Campus
            </label>
        @endif

    </div>

{{-- ================= PROFESSIONAL MODULE SELECTION (When both panels are active) ================= --}}
@if(in_array('product', session('selected_panels', [])) && in_array('travel', session('selected_panels', [])))
<div id="professionalModuleSection" style="display:none;" class="col-12 mt-3">
    <div class="p-3" style="background:#f0f7ff;border-radius:8px;border:1px solid #b8d4f5;">
        <label class="form-label fw-bold mb-2">
            Choose Platform <span class="text-danger">*</span>
        </label>
        <div class="d-flex gap-4 flex-wrap">
            <label>
                <input type="radio" name="professional_module" value="vshop" class="me-1" {{ old('professional_module', (strpos($product->type ?? '', 'vshop-') !== false ? 'vshop' : '')) == 'vshop' ? 'checked' : '' }}>
                Vshop
            </label>
            <label>
                <input type="radio" name="professional_module" value="vtravel" class="me-1" {{ old('professional_module', (strpos($product->type ?? '', 'vtravel-') !== false ? 'vtravel' : '')) == 'vtravel' ? 'checked' : '' }}>
                Vtravel
            </label>
        </div>
        @error('professional_module')
            <small class="text-danger validation-error d-block mt-1">{{ $message }}</small>
        @enderror
    </div>
</div>
@endif

</div>

</div>

{{-- ================= INFORMATION & ADVISORY LISTING TYPE DROPDOWN ================= --}}
<div id="infoAdvisorySection" style="display:none;">
    <div class="mt-3 mb-3 p-3" style="background:#f0f7ff;border-radius:8px;border:1px solid #b8d4f5;">
        <label class="form-label fw-bold">
            Select Type <span class="text-danger">*</span>
        </label>
        <select name="listing_type"
                id="listingTypeDropdown"
                class="form-select @error('listing_type') is-invalid @enderror">
            <option value="">-- Select Type --</option>
            <option value="travel service / travel guide / journey assistence" {{ old('listing_type', $product->listing_type ?? '') == 'travel service / travel guide / journey assistence' ? 'selected' : '' }}>travel service / travel guide / journey assistence</option>
            <option value="spiritual information / Cultural tour adviory / historical places / local attract" {{ old('listing_type', $product->listing_type ?? '') == 'spiritual information / Cultural tour adviory / historical places / local attract' ? 'selected' : '' }}>spiritual information / Cultural tour adviory / historical places / local attract</option>
            <option value="Activity, experience, food and culture" {{ old('listing_type', $product->listing_type ?? '') == 'Activity, experience, food and culture' ? 'selected' : '' }}>Activity, experience, food and culture</option>
            <option value="local vendor / shop" {{ old('listing_type', $product->listing_type ?? '') == 'local vendor / shop' ? 'selected' : '' }}>local vendor / shop</option>
        </select>
        @error('listing_type')
            <small class="text-danger validation-error d-block">{{ $message }}</small>
        @enderror
    </div>
</div>

{{-- ================= INVENTORY SECTION (SHOWS ONLY WHEN ITEMS IS SELECTED) ================= --}}

<div id="inventorySection" class="inventory-section" style="display: none;">
    <div class="row">
        <div class="col-md-12">
            <label class="form-label fw-bold">
                Inventory Type 
            </label>
            <select name="inventory_type"
                    id="inventoryType"
                    class="form-select @error('inventory_type') is-invalid @enderror">
                <option value="">Select Type</option>
                <option value="own"
                    {{ old('inventory_type', $product->inventory_type ?? '') == 'own' ? 'selected' : '' }}>
                    Own Inventory
                </option>
                <option value="dropshipping"
                    {{ old('inventory_type', $product->inventory_type ?? '') == 'dropshipping' ? 'selected' : '' }}>
                    Dropshipping
                </option>
            </select>
            @error('inventory_type')
                <small class="text-danger validation-error d-block">{{ $message }}</small>
            @enderror
        </div>
    </div>

    {{-- OWN INVENTORY --}}
    <div id="ownInventoryBox" class="mt-3" style="display:none;">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">SKU</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="form-control @error('sku') is-invalid @enderror">
                @error('sku')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Inventory</label>
                <input type="number" name="inventory" value="{{ old('inventory', $product->inventory ?? '') }}" class="form-control @error('inventory') is-invalid @enderror">
                @error('inventory')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Track Stock </label>
                <select name="track_stock" class="form-select @error('track_stock') is-invalid @enderror">
                    <option value="1" {{ old('track_stock', $product->track_stock ?? '') == 1 ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ old('track_stock', $product->track_stock ?? '') == 0 ? 'selected' : '' }}>No</option>
                </select>
                @error('track_stock')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>

    {{-- DROPSHIPPING --}}
    <div id="dropshippingBox" class="mt-3" style="display:none;">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Supplier Name</label>
                <input type="text" name="supplier_name" value="{{ old('supplier_name', $product->supplier_name ?? '') }}" class="form-control @error('supplier_name') is-invalid @enderror">
                @error('supplier_name')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Supplier Product ID</label>
                <input type="text" name="supplier_product_id" value="{{ old('supplier_product_id', $product->supplier_product_id ?? '') }}" class="form-control @error('supplier_product_id') is-invalid @enderror">
                @error('supplier_product_id')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Supplier Cost <span class="text-danger">*</span></label>
                <input type="number" name="supplier_cost" value="{{ old('supplier_cost', $product->supplier_cost ?? '') }}" class="form-control @error('supplier_cost') is-invalid @enderror">
                @error('supplier_cost')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Shipping Time <span class="text-danger">*</span></label>
                <input type="text" name="shipping_time" value="{{ old('shipping_time', $product->shipping_time ?? '') }}" class="form-control @error('shipping_time') is-invalid @enderror">
                @error('shipping_time')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Auto Order </label>
                <select name="auto_order" class="form-select @error('auto_order') is-invalid @enderror">
                    <option value="1" {{ old('auto_order', $product->auto_order ?? '') == 1 ? 'selected' : '' }}>Enable</option>
                    <option value="0" {{ old('auto_order', $product->auto_order ?? '') == 0 ? 'selected' : '' }}>Disable</option>
                </select>
                @error('auto_order')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>
</div>
{{-- ================= COMMON FIELDS (NAME, ETC) ================= --}}
<div class="row g-3">

    <div class="d-flex gap-2 align-items-start">
        <div class="w-100">
            <label class="form-label fw-bold">Name<span class="text-danger">*</span></label>
            <input type="text"
                   id="name"
                   name="name"
                   value="{{ old('name', $product->name ?? '') }}"
                   class="form-control @error('name') is-invalid @enderror"
                   minlength="1"
                  
                   placeholder="e.g., Summer Beach Tour, Professional">
            @error('name')
                <small class="text-danger validation-error">{{ $message }}</small>
            @enderror
        </div>
        <button type="button"
                class="btn ai-btn"
                onclick="generateAIContent(this)"
                title="V-AI Generate">
            <img src="{{ asset('vtravelassets/images/vai-chip-logo.png') }}?v=2" alt="V-AI" class="ai-logo">
            <span class="spinner-border text-primary d-none ai-spinner" role="status" aria-hidden="true" style="width: 24px; height: 24px; border-width: 3px;"></span>
        </button>
    </div>

    {{-- AUTO CODE --}}
    <div class="col-md-3">
        <label class="form-label fw-bold">Code</label>
        <input type="text" name="product_code" id="product_code" 
               class="form-control bg-light" 
               value="{{ $product->product_code ?? '' }}" 
               placeholder="Auto Generated" readonly>
    </div>

    {{-- STATUS --}}
    <div class="col-md-3">
        <label class="form-label fw-bold">Status</label>
        <div>
            <span class="badge rounded-pill px-3 py-2 
                @php
                    $status = $product->status ?? 'in_progress';
                @endphp
                @if($status == 'in_progress') bg-info
                @elseif($status == 'not_verified') bg-warning text-dark
                @elseif($status == 'verified') bg-success
                @elseif($status == 'unavailable') bg-danger
                @endif
            ">
                {{ ucfirst(str_replace('_',' ', $status)) }}
            </span>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold">
            Select Category <span class="text-danger">*</span>
        </label>
        <select name="category_id"
                id="categoryDropdown"
                class="form-select @error('category_id') is-invalid @enderror">
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <small class="text-danger validation-error">{{ $message }}</small>
        @enderror
    </div>

    {{-- SUB CATEGORY --}}
    <div class="col-md-6">
        <label class="form-label fw-bold">Sub Category</label>
        <select name="subcategory_id" id="subcategoryDropdown" class="form-select">
            <option value="">Select Sub Category</option>
        </select>
    </div>

    <div class="col-md-12">
        <label class="form-label fw-bold">
            Short Summary <span class="text-danger">*</span>
        </label>
        <div class="d-flex gap-2 align-items-start">
            <div class="w-100">
                <input type="text"
                       id="short_summary"
                       name="short_summary"
                       value="{{ old('short_summary', $product->short_summary ?? '') }}"
                       class="form-control required-common @error('short_summary') is-invalid @enderror">
                @error('short_summary')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>
        <small class="text-muted">1–100 characters</small>
    </div>

   
    <div class="col-md-12">
        <label class="form-label fw-bold">
            Full Description <span class="text-danger">*</span>
        </label>
        <textarea name="description"
                  id="description"
                  class="form-control required-common @error('description') is-invalid @enderror"
                  rows="5">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description')
            <small class="text-danger validation-error d-block">{{ $message }}</small>
        @enderror
        <small class="text-muted">1–300 words recommended</small>
    </div>

    
    <div class="col-md-6">
        <label class="form-label fw-bold">Organizer Name</label>
        <input type="text"
               name="organizer_name"
               value="{{ old('organizer_name', $product->organizer_name ?? '') }}"
               class="form-control"
               placeholder="Enter organizer / provider name">
    </div>

</div>



<div id="productSection">
<hr class="my-4">
<h5 class="text-primary mb-3">Product Details</h5>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-bold">Brand Name</label>
        <input type="text" name="brand_name" value="{{ old('brand_name', $product->brand_name ?? '') }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Manufactured By</label>
        <input type="text" name="manufactured_by" value="{{ old('manufactured_by', $product->manufactured_by ?? '') }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Manufacturing Date</label>
        <input type="date" name="manufacturing_date" value="{{ old('manufacturing_date', $product->manufacturing_date ?? '') }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Country Of Origin</label>
        <input type="text" name="country_origin" value="{{ old('country_origin', $product->country_origin ?? '') }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Generic Name</label>
        <input type="text" name="generic_name" value="{{ old('generic_name', $product->generic_name ?? '') }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">ASIN / UPC / EAN</label>
        <input type="text" name="asin" value="{{ old('asin', $product->asin ?? '') }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Ingredients</label>
        <input type="text" name="ingredients" id="ingredients" value="{{ old('ingredients', $product->ingredients ?? '') }}" class="form-control">
    </div>
    <div class="col-md-6" id="expiryDateWrapper">
        <label class="form-label fw-bold">Expiry Date</label>
        <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', isset($product->expiry_date) && !empty($product->expiry_date) && $product->expiry_date !== '0000-00-00' ? \Carbon\Carbon::parse($product->expiry_date)->format('Y-m-d') : '') }}" class="form-control">
    </div>
    <div class="col-md-6 course-fields">
        <label class="form-label fw-bold">Start Date</label>
        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', isset($product->start_date) && !empty($product->start_date) && $product->start_date !== '0000-00-00' ? \Carbon\Carbon::parse($product->start_date)->format('Y-m-d') : '') }}" class="form-control">
    </div>
    <div class="col-md-6 course-fields">
        <label class="form-label fw-bold">End Date</label>
        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', isset($product->end_date) && !empty($product->end_date) && $product->end_date !== '0000-00-00' ? \Carbon\Carbon::parse($product->end_date)->format('Y-m-d') : '') }}" class="form-control">
    </div>
    <div class="col-md-6 course-fields">
        <label class="form-label fw-bold">Time Slot</label>
        <input type="time" name="time_slot" id="time_slot" value="{{ old('time_slot', $product->start_time ?? '') }}" class="form-control">
    </div>
    <div class="col-md-6 course-fields">
        <label class="form-label fw-bold">Meeting Link</label>
        <input type="text" name="meeting_link_basic" id="meeting_link_basic" value="{{ old('meeting_link_basic', $product->meeting_link ?? '') }}" class="form-control" placeholder="https://zoom.us/j/123456789">
    </div>
    <div class="col-12">
        <label class="form-label fw-bold">Storage Instruction</label>
        <textarea name="storage_instruction" id="storage_instruction" class="form-control">{{ old('storage_instruction', $product->storage_instruction ?? '') }}</textarea>
    </div>
</div>
</div>

<div id="professionalSection" style="display:none;">
<hr class="my-4">
<h5 class="text-primary">Professional Details</h5>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-bold">Professional Type<span class="text-danger">*</span></label>
        <input type="text" name="professional_type" value="{{ old('professional_type', $product->professional_type ?? '') }}" class="form-control required-prof @error('professional_type') is-invalid @enderror">
        @error('professional_type')
            <small class="text-danger validation-error d-block">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Session Mode<span class="text-danger">*</span></label>
        <select name="session_mode" class="form-select required-prof @error('session_mode') is-invalid @enderror">
            <option value="">Select</option>
            <option value="Video" {{ old('session_mode', $product->session_mode ?? '') == 'Video' ? 'selected' : '' }}>Video</option>
            <option value="Call" {{ old('session_mode', $product->session_mode ?? '') == 'Call' ? 'selected' : '' }}>Call</option>
            <option value="In-person" {{ old('session_mode', $product->session_mode ?? '') == 'In-person' ? 'selected' : '' }}>In-person</option>
        </select>
        @error('session_mode')
            <small class="text-danger validation-error d-block">{{ $message }}</small>
        @enderror
    </div>
    {{-- PROFESSIONAL SLOTS (Multiple Date + Time) --}}
    <div class="col-md-12">
        <label class="form-label fw-bold">Available Date & Time Slots</label>
        <div class="slot-picker-card">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Date(s)</label>
                    <input type="text" id="slotDate" class="form-control" placeholder="Select Date(s)">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Start Time</label>
                    <input type="time" id="slotStartTime" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">End Time</label>
                    <input type="time" id="slotEndTime" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary w-100" onclick="addProfSlot()" style="height:38px;">
                        <i class="fa fa-plus me-1"></i> Add
                    </button>
                </div>
            </div>
        </div>

        {{-- Slot Tags --}}
        <div id="profSlotTags" class="d-flex flex-wrap gap-2 mt-3"></div>

        {{-- Hidden field to store JSON --}}
        <input type="hidden" name="professional_slots" id="professionalSlotsInput"
               value="{{ old('professional_slots', $product->professional_slots ?? '[]') }}">

        @error('professional_slots')
            <small class="text-danger validation-error d-block">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Languages<span class="text-danger">*</span></label>
        <input type="text" name="languages" value="{{ old('languages', $product->languages ?? '') }}" class="form-control required-prof">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Max Booking<span class="text-danger">*</span></label>
        <input type="number" name="max_booking" value="{{ old('max_booking', $product->max_booking ?? '') }}" class="form-control required-prof">
    </div>
    <div class="col-md-12">
        <label class="form-label fw-bold">Portfolio</label>
        <textarea name="portfolio" class="form-control">{{ old('portfolio', $product->portfolio ?? '') }}</textarea>
    </div>
    <div class="col-md-12">
        <label class="form-label fw-bold">Cancellation Policy<span class="text-danger">*</span></label>
        <textarea name="cancel_policy_prof" class="form-control required-prof">{{ old('cancel_policy_prof', $product->cancel_policy_prof ?? '') }}</textarea>
    </div>
</div>
</div>

{{-- ================= TOUR GUIDANCE DETAILS SECTION ================= --}}
<div id="tourGuidanceSection" style="display:none;">
    <hr class="my-4">
    <h5 class="text-primary mb-3">Tour Guidance Details</h5>

    {{-- Service Location Fields --}}
    <div class="row g-3 mt-2">
        <div class="col-md-4">
            <label class="form-label fw-bold">Country <span class="text-danger">*</span></label>
            <select name="service_country" id="countryDropdown" class="form-select @error('service_country') is-invalid @enderror">
                <option value="">Select Country</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ old('service_country', $product->country ?? '') == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
            @error('service_country')
                <small class="text-danger validation-error d-block">{{ $message }}</small>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">State <span class="text-danger">*</span></label>
            <select name="service_state" id="stateDropdown" class="form-select @error('service_state') is-invalid @enderror">
                <option value="">Select State</option>
            </select>
            @error('service_state')
                <small class="text-danger validation-error d-block">{{ $message }}</small>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">City <span class="text-danger">*</span></label>
            <select name="service_district" id="cityDropdown" class="form-select @error('service_district') is-invalid @enderror">
                <option value="">Select City</option>
            </select>
            @error('service_district')
                <small class="text-danger validation-error d-block">{{ $message }}</small>
            @enderror
        </div>
    </div>

    {{-- Service Type (Hourly/Day/Fixed) --}}
    <div class="row g-3 mt-3">
        <div class="col-12">
            <label class="form-label fw-bold">Service Type <span class="text-danger">*</span></label>
            <div class="d-flex gap-4 mt-2 flex-wrap">
                <label><input type="radio" name="service_type" value="hourly" class="service-type-radio" {{ old('service_type', $product->service_type ?? '') == 'hourly' ? 'checked' : '' }}> Hourly Based</label>
                <label><input type="radio" name="service_type" value="day" class="service-type-radio" {{ old('service_type', $product->service_type ?? '') == 'day' ? 'checked' : '' }}> Day Based</label>
                <label><input type="radio" name="service_type" value="fixed" class="service-type-radio" {{ old('service_type', $product->service_type ?? '') == 'fixed' ? 'checked' : '' }}> Fixed Duration</label>
            </div>
            @error('service_type')
                <small class="text-danger validation-error d-block">{{ $message }}</small>
            @enderror
        </div>
    </div>

    {{-- Common Service Fields (Hourly/Day) --}}
    <div id="commonServiceFields" class="service-type-fields mt-3" style="display:none;">
        <div class="mb-3">
            <label class="form-label fw-bold">Service Days per Week <span class="text-danger">*</span></label>
            @php
                $selectedDays = old('service_days', $product->service_days ?? []);
                if(is_string($selectedDays)){ $selectedDays = json_decode($selectedDays, true); }
            @endphp
            <div class="day-selection d-flex gap-2 flex-wrap">
                @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                    <label class="day-box">
                        <input type="checkbox" name="service_days[]" value="{{ $day }}" {{ in_array($day, $selectedDays ?? []) ? 'checked' : '' }}>
                        <span>{{ $day }}</span>
                    </label>
                @endforeach
            </div>
            @error('service_days')
                <small class="text-danger validation-error d-block">{{ $message }}</small>
            @enderror
        </div>
        <div class="row">
            <div class="col-md-6">
                <label class="form-label fw-bold">Start Time <span class="text-danger">*</span></label>
                <input type="time" name="start_time" value="{{ old('start_time', $product->start_time ?? '') }}" class="form-control @error('start_time') is-invalid @enderror">
                @error('start_time')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">End Time <span class="text-danger">*</span></label>
                <input type="time" name="end_time" value="{{ old('end_time', $product->end_time ?? '') }}" class="form-control @error('end_time') is-invalid @enderror">
                <small id="durationText" class="text-primary fw-bold"></small>
                @error('end_time')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>

    {{-- Fixed Fields --}}
    <div id="fixedFields" class="service-type-fields mt-4" style="display:none;">
        <label class="form-label fw-bold">Fixed Duration is <span class="text-danger">*</span></label>
        <div class="d-flex gap-4 flex-wrap">
            <label><input type="radio" name="fixed_type" value="monthly" {{ old('fixed_type') == 'monthly' ? 'checked' : '' }}> Monthly</label>
            <label><input type="radio" name="fixed_type" value="weekly" {{ old('fixed_type') == 'weekly' ? 'checked' : '' }}> Weekly</label>
        </div>
        @error('fixed_type')
            <small class="text-danger validation-error d-block">{{ $message }}</small>
        @enderror
        <div class="mt-3">
            <label class="form-label fw-bold">Select Dates</label>
            <input type="text" name="fixed_dates" value="{{ old('fixed_dates') }}" class="form-control @error('fixed_dates') is-invalid @enderror" placeholder="e.g. 1,15,24">
            @error('fixed_dates')
                <small class="text-danger validation-error d-block">{{ $message }}</small>
            @enderror
        </div>
        <div class="mt-3">
            <label class="form-label fw-bold">Service Type <span class="text-danger">*</span></label>
            <div class="d-flex gap-4 flex-wrap">
                <label><input type="radio" name="fixed_service_mode" value="days" {{ old('fixed_service_mode') == 'days' ? 'checked' : '' }}> Service in Days</label>
                <label><input type="radio" name="fixed_service_mode" value="hours" {{ old('fixed_service_mode') == 'hours' ? 'checked' : '' }}> Service in Hours</label>
            </div>
            @error('fixed_service_mode')
                <small class="text-danger validation-error d-block">{{ $message }}</small>
            @enderror
        </div>
        <div id="fixedDaysFields" class="row mt-3" style="display:none;">
            <div class="col-md-6">
                <label class="form-label fw-bold">Service Days <span class="text-danger">*</span></label>
                <input type="number" name="service_days_count" id="totalDays" value="{{ old('service_days_count') }}" class="form-control @error('service_days_count') is-invalid @enderror">
                @error('service_days_count')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Service Nights <span class="text-danger">*</span></label>
                <input type="number" name="service_nights" id="totalNights" value="{{ old('service_nights') }}" class="form-control @error('service_nights') is-invalid @enderror">
                @error('service_nights')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>
        <div id="fixedHoursFields" class="row mt-3" style="display:none;">
            <div class="col-md-6">
                <label class="form-label fw-bold">Start Time <span class="text-danger">*</span></label>
                <input type="time" name="start_time_fixed" value="{{ old('start_time_fixed') }}" class="form-control @error('start_time_fixed') is-invalid @enderror">
                @error('start_time_fixed')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">End Time <span class="text-danger">*</span></label>
                <input type="time" name="end_time_fixed" value="{{ old('end_time_fixed') }}" class="form-control @error('end_time_fixed') is-invalid @enderror">
                <small id="durationTextFixed" class="text-primary"></small>
                @error('end_time_fixed')
                    <small class="text-danger validation-error d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>

    {{-- Suitability Information --}}
    <div class="col-12 mt-3">
        <hr>
        <h6 class="fw-bold mb-3">Suitability Information</h6>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">No. of Adults</label>
            <input type="number" name="no_of_adults" min="0" value="{{ old('no_of_adults', $product->adult_count ?? '') }}" class="form-control @error('no_of_adults') is-invalid @enderror">
            @error('no_of_adults')
                <small class="text-danger validation-error d-block">{{ $message }}</small>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">No. of Kids</label>
            <input type="number" name="no_of_kids" min="0" value="{{ old('no_of_kids', $product->kids_count ?? '') }}" class="form-control @error('no_of_kids') is-invalid @enderror">
            @error('no_of_kids')
                <small class="text-danger validation-error d-block">{{ $message }}</small>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Suitable for Kids?</label>
            <select name="suitable_kids" class="form-select @error('suitable_kids') is-invalid @enderror">
                <option value="1" {{ old('suitable_kids', $product->kids_allowed ?? '') == 1 ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ old('suitable_kids', $product->kids_allowed ?? '') == 0 ? 'selected' : '' }}>No</option>
            </select>
            @error('suitable_kids')
                <small class="text-danger validation-error d-block">{{ $message }}</small>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Suitable for Adults?</label>
            <select name="suitable_adults" class="form-select @error('suitable_adults') is-invalid @enderror">
                <option value="1" {{ old('suitable_adults', $product->adult_allowed ?? '') == 1 ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ old('suitable_adults', $product->adult_allowed ?? '') == 0 ? 'selected' : '' }}>No</option>
            </select>
            @error('suitable_adults')
                <small class="text-danger validation-error d-block">{{ $message }}</small>
            @enderror
        </div>
    </div>

    {{-- Tour Extra Fields --}}
    <div id="tourExtraFields" style="display:none;">
        <hr>
        <h6 class="fw-bold">Tour Guidance Extra Details</h6>
        <br>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Guide Type <span class="text-danger">*</span></label>
                <select name="guide_service_type" class="form-select required-tour @error('guide_service_type') is-invalid @enderror">
                    <option value="">Select</option>
                    <option value="Tour Guide (physical)" {{ old('guide_service_type',$product->guide_service_type ?? '')=='Tour Guide (physical)'?'selected':'' }}>Tour Guide (physical)</option>
                    <option value="Virtual Guide (video/audio)" {{ old('guide_service_type',$product->guide_service_type ?? '')=='Virtual Guide (video/audio)'?'selected':'' }}>Virtual Guide (video/audio)</option>
                    <option value="Trip Assistance (on-call support)" {{ old('guide_service_type',$product->guide_service_type ?? '')=='Trip Assistance (on-call support)'?'selected':'' }}>Trip Assistance (on-call support)</option>
                    <option value="Specialized Expert" {{ old('guide_service_type',$product->guide_service_type ?? '')=='Specialized Expert'?'selected':'' }}>Specialized Expert</option>
                </select>
                @error('guide_service_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Guide Category <span class="text-danger">*</span></label>
                <select name="guide_category" class="form-select required-tour @error('guide_category') is-invalid @enderror">
                    <option value="">Select</option>
                    <option value="Local" {{ old('guide_category',$product->guide_category ?? '')=='Local'?'selected':'' }}>Local</option>
                    <option value="Expert" {{ old('guide_category',$product->guide_category ?? '')=='Expert'?'selected':'' }}>Expert</option>
                    <option value="Certified" {{ old('guide_category',$product->guide_category ?? '')=='Certified'?'selected':'' }}>Certified</option>
                </select>
                @error('guide_category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Capacity <span class="text-danger">*</span></label>
                <input type="number" name="capacity" class="form-control required-tour @error('capacity') is-invalid @enderror" value="{{ old('capacity',$product->capacity ?? '') }}">
                @error('capacity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Start Point</label>
                <input type="text" name="start_point" class="form-control" value="{{ old('start_point',$product->start_point ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">End Point</label>
                <input type="text" name="end_point" class="form-control" value="{{ old('end_point',$product->end_point ?? '') }}">
            </div>
            <div class="col-md-12">
                <label class="form-label fw-bold">Inclusions / Exclusions <span class="text-danger">*</span></label>
                <textarea name="inclusion" class="form-control required-tour @error('inclusion') is-invalid @enderror">{{ old('inclusion',$product->inclusion ?? '') }}</textarea>
                @error('inclusion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-12">
                <label class="form-label fw-bold">Availability <span class="text-danger">*</span></label>
                <input type="hidden" name="availability" id="availabilityInput" value="{{ old('availability',$product->availability ?? '') }}">
                <div class="availability-wrapper">
                    <div class="availability-top">
                        <input type="date" id="availabilityPicker" class="form-control availability-input">
                        <button type="button" class="btn availability-btn" id="addAvailabilityBtn">
                            <i class="bi bi-plus-circle"></i> Add
                        </button>
                    </div>
                    <div id="availabilityTags" class="availability-tags"></div>
                </div>
                @error('availability')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-12">
                <label class="form-label fw-bold">Cancellation Policy <span class="text-danger">*</span></label>
                <textarea name="cancel_policy_tour" class="form-control required-tour @error('cancel_policy_tour') is-invalid @enderror">{{ old('cancel_policy_tour',$product->cancel_policy_tour ?? '') }}</textarea>
                @error('cancel_policy_tour')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>


<div id="otherServiceFields" style="display:none;">
<hr>
<h6 class="fw-bold">Other Service Details</h6>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-bold">Service Type <span class="text-danger">*</span></label>
        <input type="text" name="other_service_type" class="form-control required-other @error('other_service_type') is-invalid @enderror" value="{{ old('other_service_type',$product->other_service_type ?? '') }}">
        @error('other_service_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Processing Time <span class="text-danger">*</span></label>
        <input type="time" name="processing_time" class="form-control required-other @error('processing_time') is-invalid @enderror" value="{{ old('processing_time',$product->processing_time ?? '') }}">
        @error('processing_time')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Required Documents <span class="text-danger">*</span></label>
        <input type="text" name="documents" class="form-control required-other @error('documents') is-invalid @enderror" value="{{ old('documents',$product->documents ?? '') }}">
        @error('documents')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Validity Period <span class="text-danger">*</span></label>
        <input type="number" name="validity" class="form-control required-other @error('validity') is-invalid @enderror" value="{{ old('validity',$product->validity ?? '') }}">
        @error('validity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Gov Fees / Extra Charges</label>
        <input type="number" name="gov_fee" class="form-control" value="{{ old('gov_fee',$product->gov_fee ?? '') }}">
    </div>
    <div class="col-md-12">
        <label class="form-label fw-bold">Cancellation Policy <span class="text-danger">*</span></label>
        <textarea name="cancel_policy_other" class="form-control required-other @error('cancel_policy_other') is-invalid @enderror">{{ old('cancel_policy_other',$product->cancel_policy_other ?? '') }}</textarea>
        @error('cancel_policy_other')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
</div>

{{-- Submit Button --}}
<div class="text-end mt-4">
<button type="submit" class="btn btn-primary px-5 py-2">
Save & Continue <i class="bi bi-arrow-right ms-2"></i>
</button>
</div>

</div>
</form>

</div>
</div>
</div>

{{-- RIGHT SIDEBAR --}}
<div class="marketplace-right">
@include('vshop.seller.partials.product-progress',['product'=>$product ?? null])
</div>

</div>
</div>

<script>
$(document).ready(function(){
    
    
    
    // ================= PROFESSIONAL SESSION DURATION =================
$('#prof_start_time, #prof_end_time').on('change keyup', function() {
    calculateProfessionalDuration();
});

function calculateProfessionalDuration() {
    var start = $('#prof_start_time').val();
    var end = $('#prof_end_time').val();
    if (start && end) {
        var s = new Date('1970-01-01T' + start + ':00');
        var e = new Date('1970-01-01T' + end + ':00');
        var diffMs = e - s;
        if (diffMs > 0) {
            var totalMinutes = Math.floor(diffMs / 60000);
            var hours = Math.floor(totalMinutes / 60);
            var minutes = totalMinutes % 60;
            var display = '';
            if (hours > 0) display += hours + ' Hr ';
            if (minutes > 0) display += minutes + ' Min';
            if (display === '') display = '0 Min';
            $('#prof_duration_display').val(display);
            $('#prof_session_duration').val(totalMinutes);   // stores in minutes
        } else {
            $('#prof_duration_display').val('');
            $('#prof_session_duration').val('');
        }
    } else {
        $('#prof_duration_display').val('');
        $('#prof_session_duration').val('');
    }
}

// Initialize if editing (when page loads with pre-filled values)
if ($('#prof_start_time').val() && $('#prof_end_time').val()) {
    calculateProfessionalDuration();
}

// ================= TOGGLE INVENTORY SECTION (SHOWS ONLY FOR ITEMS - NOT FOR PROFESSIONAL) =================
function toggleInventorySection() {
    let type = $('input[name="type"]:checked').val();
    
    let showInventory = false;
    
    // ONLY show inventory for "Items" (vshop-product or product)
    // DO NOT show inventory for ANY professional type
    if (type === 'vshop-product' || type === 'product') {
        showInventory = true;
    }

    if(showInventory) {
        $('#inventorySection').show();
        $('#inventoryType').trigger('change');
    } else {
        $('#inventorySection').hide();
        $('#ownInventoryBox, #dropshippingBox').hide();
    }
}

// ================= TOGGLE INFO & ADVISORY SECTION =================
function toggleInfoAdvisorySection() {
    let type = $('input[name="type"]:checked').val();
    if(type === 'information_advisory' || type === 'vtravel-information_advisory') {
        $('#infoAdvisorySection').slideDown();
    } else {
        $('#infoAdvisorySection').slideUp();
        $('#listingTypeDropdown').val('');
    }
}

// ================= TOGGLE PROFESSIONAL MODULE SECTION =================
function toggleProfessionalModule() {
    let type = $('input[name="type"]:checked').val();
    if(type === 'professional' && $('#professionalModuleSection').length > 0) {
        $('#professionalModuleSection').slideDown();
    } else {
        $('#professionalModuleSection').slideUp();
    }
}

// ================= TOGGLE ALL SECTIONS =================
function toggleAllSections() {
    let type = $('input[name="type"]:checked').val();

    $('#productSection, #professionalSection, #tourGuidanceSection, #otherServiceFields, #infoAdvisorySection').hide();

    if(type === 'product' || type === 'vshop-product') {
        $('#productSection').show();
    }
    if(type === 'professional' || type === 'vshop-professional' || type === 'vtravel-professional') {
        $('#productSection').show();
        $('#professionalSection').show();
    }
    if(type === 'tour' || type === 'vtravel-tour') {
        $('#tourGuidanceSection').show();
        $('#tourExtraFields').show();
    }
    if(type === 'information_advisory' || type === 'vtravel-information_advisory') {
        $('#infoAdvisorySection').show();
        $('#tourGuidanceSection').show();
        $('#tourExtraFields').hide();
    }
    if(type === 'service' || type === 'vshop-service' || type === 'vtravel-service') {
        $('#otherServiceFields').show();
    }
}

// ================= INVENTORY TYPE CHANGE =================
$('#inventoryType').on('change', function() {
    let val = $(this).val();
    $('#ownInventoryBox, #dropshippingBox').hide();
    if(val === 'own') {
        $('#ownInventoryBox').fadeIn();
    }
    if(val === 'dropshipping') {
        $('#dropshippingBox').fadeIn();
    }
});

// ================= SERVICE TYPE FIELDS =================
function toggleServiceTypeFields() {
    let type = $('input[name="service_type"]:checked').val();
    $('.service-type-fields').hide();
    if(type === 'hourly' || type === 'day') {
        $('#commonServiceFields').fadeIn();
        calculateDuration();
    }
    if(type === 'fixed') {
        $('#fixedFields').fadeIn();
        toggleFixedServiceMode();
    }
}

function toggleFixedServiceMode() {
    let mode = $('input[name="fixed_service_mode"]:checked').val();
    $('#fixedDaysFields, #fixedHoursFields').hide();
    if(mode === 'days') $('#fixedDaysFields').fadeIn();
    if(mode === 'hours') {
        $('#fixedHoursFields').fadeIn();
        calculateDurationFixed();
    }
}

$(document).on('change', 'input[name="fixed_service_mode"]', toggleFixedServiceMode);
$('input[name="service_type"]').on('change', toggleServiceTypeFields);

// ================= DURATION CALCULATION =================
function calculateDuration() {
    let start = $('input[name="start_time"]').val();
    let end = $('input[name="end_time"]').val();
    if(start && end) {
        let s = new Date("1970-01-01 " + start);
        let e = new Date("1970-01-01 " + end);
        let diffMs = e - s;
        if(diffMs > 0) {
            let totalMinutes = Math.floor(diffMs / 60000);
            let hours = Math.floor(totalMinutes / 60);
            let minutes = totalMinutes % 60;
            let text = "Duration : ";
            if(hours > 0) text += hours + " Hr ";
            if(minutes > 0) text += minutes + " Min";
            $('#durationText').text(text);
        } else {
            $('#durationText').text('');
        }
    }
}

function calculateDurationFixed() {
    let start = $('input[name="start_time_fixed"]').val();
    let end = $('input[name="end_time_fixed"]').val();
    if(start && end) {
        let s = new Date("1970-01-01 " + start);
        let e = new Date("1970-01-01 " + end);
        let diffMs = e - s;
        if(diffMs > 0) {
            let totalMinutes = Math.floor(diffMs / 60000);
            let hours = Math.floor(totalMinutes / 60);
            let minutes = totalMinutes % 60;
            let text = "Duration : ";
            if(hours > 0) text += hours + " Hr ";
            if(minutes > 0) text += minutes + " Min";
            $('#durationTextFixed').text(text);
        } else {
            $('#durationTextFixed').text('');
        }
    }
}

$('input[name="start_time"], input[name="end_time"]').on('change', calculateDuration);
$('input[name="start_time_fixed"], input[name="end_time_fixed"]').on('change', calculateDurationFixed);

// ================= COUNTRY -> STATE -> CITY =================
$('#countryDropdown').on('change', function() {
    let country_id = $(this).val();
    if(!country_id) {
        $('#stateDropdown').html('<option value="">Select State</option>');
        $('#cityDropdown').html('<option value="">Select City</option>');
        return;
    }
    $('#stateDropdown').html('<option>Loading...</option>');
    $.get("{{ url('seller/products/findstates') }}/" + country_id, function(res) {
        let html = '<option value="">Select State</option>';
        res.forEach(item => html += `<option value="${item.id}">${item.name}</option>`);
        $('#stateDropdown').html(html);
        
        // Preselect state if editing
        let oldState = '{{ old("service_state", $product->state ?? "") }}';
        if(oldState) {
            $('#stateDropdown').val(oldState).trigger('change');
        }
    }).fail(() => $('#stateDropdown').html('<option>Error</option>'));
});

$('#stateDropdown').on('change', function() {
    let state_id = $(this).val();
    if(!state_id) {
        $('#cityDropdown').html('<option value="">Select City</option>');
        return;
    }
    $('#cityDropdown').html('<option>Loading...</option>');
    $.get("{{ url('seller/products/findcities') }}/" + state_id, function(res) {
        let html = '<option value="">Select City</option>';
        res.forEach(item => html += `<option value="${item.id}">${item.name}</option>`);
        $('#cityDropdown').html(html);
        
        // Preselect city if editing
        let oldCity = '{{ old("service_district", $product->district ?? "") }}';
        if(oldCity) {
            $('#cityDropdown').val(oldCity);
        }
    }).fail(() => $('#cityDropdown').html('<option>Error</option>'));
});


function loadCategories(type, preselect) {
    $('#categoryDropdown').html('<option>Loading...</option>');

    let categoryTypes = [];

    // Determine which category types to load
    // Always load both product and service categories so seller-created categories show up across all types
    categoryTypes = ['product', 'service'];

    // If only one type, call endpoint as before
    if (categoryTypes.length === 1) {
        $.get("{{ url('seller/products/categories') }}/" + categoryTypes[0], function(res) {
            let html = '<option value="">Select Category</option>';
            res.forEach(cat => html += `<option value="${cat.id}" ${preselect == cat.id ? 'selected' : ''}>${cat.name}</option>`);
            $('#categoryDropdown').html(html);
            if (preselect) {
                loadSubcategories(preselect);
            }
        }).fail(function() {
            $('#categoryDropdown').html('<option value="">Select Category</option>');
            console.error('Failed to load categories for type:', categoryTypes[0]);
        });
    } else {
        // Multiple types: fetch both and merge
        let requests = categoryTypes.map(catType => {
            return $.get("{{ url('seller/products/categories') }}/" + catType);
        });

        $.when.apply($, requests).done(function() {
            let allResults = [];
            // arguments contains [data, statusText, jqXHR] for each request
            $.each(arguments, function(index, response) {
                // response[0] is the actual data array
                if (response && response[0] && response[0].length) {
                    allResults = allResults.concat(response[0]);
                }
            });
            // Sort by name
            allResults.sort((a, b) => a.name.localeCompare(b.name));
            let html = '<option value="">Select Category</option>';
            allResults.forEach(cat => html += `<option value="${cat.id}" ${preselect == cat.id ? 'selected' : ''}>${cat.name}</option>`);
            $('#categoryDropdown').html(html);
            if (preselect) {
                loadSubcategories(preselect);
            }
        }).fail(function() {
            $('#categoryDropdown').html('<option value="">Select Category</option>');
            console.error('Failed to load categories');
        });
    }
}

function loadSubcategories(categoryId) {
    if(!categoryId) {
        $('#subcategoryDropdown').html('<option value="">Select Sub Category</option>');
        return;
    }
    $('#subcategoryDropdown').html('<option>Loading...</option>');
    $.get("{{ url('seller/products/get-subcategories') }}/" + categoryId, function(res) {
        let html = '<option value="">Select Sub Category</option>';
        res.forEach(sub => html += `<option value="${sub.id}" ${sub.id == '{{ old("subcategory_id", $product->subcategory_id ?? "") }}' ? 'selected' : ''}>${sub.name}</option>`);
        $('#subcategoryDropdown').html(html);
    }).fail(function() {
        $('#subcategoryDropdown').html('<option>Error loading</option>');
    });
}

$('#categoryDropdown').on('change', function() {
    loadSubcategories($(this).val());
});

    // Handle Course Name Logic
    function handleCourseLogic() {
        let nameVal = $('[name="name"]').val().toLowerCase();
        let isCourse = nameVal.includes('course') || nameVal.includes('cources');
        let typeVal = $('input[name="type"]:checked').val();
        let isItem = typeVal === 'product' || typeVal === 'vshop-product';

        if (isItem) {
            $('.course-fields').show();
            if (isCourse) {
                $('#expiryDateWrapper').hide();
            } else {
                $('#expiryDateWrapper').show();
            }
        } else {
            $('.course-fields').hide();
            $('#expiryDateWrapper').show();
        }
    }

    $('[name="name"]').on('input change', handleCourseLogic);
    $('input[name="type"]').on('change', handleCourseLogic);
    handleCourseLogic(); // Run on load

    // ================= REMOVE VALIDATION ERRORS =================
function removeValidationErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.validation-error').remove();
}

$(document).on('input change', 'input,select,textarea', function() {
    $(this).removeClass('is-invalid');
    $(this).next('.validation-error').remove();
});

// ================= FORM VALIDATION =================
$('#basicForm').on('submit', function(e) {
    let type = $('input[name="type"]:checked').val();
    let serviceType = $('input[name="service_type"]:checked').val();
    let valid = true;
    
    removeValidationErrors();
    
    function showError(field, message = 'This field is required') {
        field.addClass('is-invalid');
        if(field.next('.validation-error').length === 0) {
            field.after('<small class="text-danger validation-error d-block mt-1">' + message + '</small>');
        }
        valid = false;
    }
    
    function checkField(field) {
        if(field.length && field.is(':visible') && !field.val()) {
            showError(field);
        }
    }
    
    // Common fields
    checkField($('[name="name"]'));
    checkField($('#short_summary'));
    checkField($('[name="description"]'));
    checkField($('#categoryDropdown'));
    
    let requireInventory = false;
    // ONLY require inventory for "Items" type (vshop-product or product)
    if (type === 'vshop-product' || type === 'product') {
        requireInventory = true;
    }

    // ================= INVENTORY VALIDATION (ONLY FOR ITEMS) =================
    if(requireInventory) {
        let inventoryType = $('#inventoryType').val();
        if(!inventoryType) {
            showError($('#inventoryType'), 'Please select inventory type');
        }
        
        if(inventoryType === 'own') {
            checkField($('[name="sku"]'));
            checkField($('[name="inventory"]'));
        }
        
        if(inventoryType === 'dropshipping') {
            checkField($('[name="supplier_name"]'));
            checkField($('[name="supplier_product_id"]'));
            checkField($('[name="supplier_cost"]'));
            checkField($('[name="shipping_time"]'));
        }
    }
    
    // Professional fields
    if(type === 'professional' || type === 'vshop-professional' || type === 'vtravel-professional') {
        if(type === 'professional' && $('#professionalModuleSection').length > 0) {
            if(!$('input[name="professional_module"]:checked').length) {
                showError($('input[name="professional_module"]').first().closest('.d-flex'), 'Please select professional origin');
            }
        }
        $('.required-prof').each(function() { checkField($(this)); });
    }
    
    // Tour Guidance fields
    if(type === 'tour' || type === 'vtravel-tour' || type === 'information_advisory' || type === 'vtravel-information_advisory') {
        checkField($('#countryDropdown'));
        checkField($('#stateDropdown'));
        checkField($('#cityDropdown'));

        if(!$('input[name="service_type"]:checked').length) {
            $('.service-type-radio').first().closest('.col-12').append('<small class="text-danger validation-error d-block mt-2">Please select service type</small>');
            valid = false;
        }

        if(serviceType === 'hourly' || serviceType === 'day') {
            if($('input[name="service_days[]"]:checked').length === 0) {
                $('.day-selection').after('<small class="text-danger validation-error d-block mt-2">Please select at least one day</small>');
                valid = false;
            }
            checkField($('[name="start_time"]'));
            checkField($('[name="end_time"]'));
            let start = $('[name="start_time"]').val();
            let end = $('[name="end_time"]').val();
            if(start && end && end <= start) {
                showError($('[name="end_time"]'), 'End time must be greater than start time');
            }
        }

        if(serviceType === 'fixed') {
            if(!$('input[name="fixed_type"]:checked').length) {
                $('input[name="fixed_type"]').first().closest('#fixedFields').append('<small class="text-danger validation-error d-block mt-2">Please select fixed duration type</small>');
                valid = false;
            }
            if(!$('input[name="fixed_service_mode"]:checked').length) {
                $('input[name="fixed_service_mode"]').first().closest('.mt-3').append('<small class="text-danger validation-error d-block mt-2">Please select service mode</small>');
                valid = false;
            }
            let fixedMode = $('input[name="fixed_service_mode"]:checked').val();
            if(fixedMode === 'days') {
                checkField($('[name="service_days_count"]'));
                checkField($('[name="service_nights"]'));
            }
            if(fixedMode === 'hours') {
                checkField($('[name="start_time_fixed"]'));
                checkField($('[name="end_time_fixed"]'));
                let startFixed = $('[name="start_time_fixed"]').val();
                let endFixed = $('[name="end_time_fixed"]').val();
                if(startFixed && endFixed && endFixed <= startFixed) {
                    showError($('[name="end_time_fixed"]'), 'End time must be greater than start time');
                }
            }
        }

        if(type === 'tour' || type === 'vtravel-tour') {
            $('.required-tour').each(function() { checkField($(this)); });
        }
    }

    // Information & Advisory - require listing type
    if(type === 'information_advisory' || type === 'vtravel-information_advisory') {
        if(!$('#listingTypeDropdown').val()) {
            showError($('#listingTypeDropdown'), 'Please select a listing type');
        }
    }

    // Other Service fields
    if(type === 'service' || type === 'vshop-service' || type === 'vtravel-service') {
        $('.required-other').each(function() { checkField($(this)); });
    }

    if(!valid) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: $('.is-invalid:first').offset().top - 120 }, 400);
    }
});

// ================= EVENT LISTENERS =================
$('input[name="professional_module"]').on('change', function() {
    toggleInventorySection();
    let currentType = $('input[name="type"]:checked').val();
    if (currentType) {
        loadCategories(currentType);
    }
});

$('.type-radio').on('change', function() {
    toggleAllSections();
    toggleInventorySection();
    toggleProfessionalModule();
    loadCategories($(this).val());
});

// ================= AVAILABILITY DATES =================
let availabilityDates = [];
let oldAvailability = $('#availabilityInput').val();
if(oldAvailability) {
    availabilityDates = oldAvailability.split(',');
    renderAvailabilityDates();
}

$('#addAvailabilityBtn').on('click', function() {
    let date = $('#availabilityPicker').val();
    if(!date) return;
    if(availabilityDates.includes(date)) {
        alert('Date already added');
        return;
    }
    availabilityDates.push(date);
    $('#availabilityPicker').val('');
    renderAvailabilityDates();
});

function renderAvailabilityDates() {
    $('#availabilityTags').html('');
    availabilityDates.forEach((date, index) => {
        $('#availabilityTags').append(`
            <div class="badge bg-primary p-2 d-flex align-items-center gap-2">
                <span>${date}</span>
                <button type="button" class="btn-close btn-close-white remove-date" data-index="${index}" style="font-size:10px;"></button>
            </div>
        `);
    });
    $('#availabilityInput').val(availabilityDates.join(','));
}

$(document).on('click', '.remove-date', function() {
    let index = $(this).data('index');
    availabilityDates.splice(index, 1);
    renderAvailabilityDates();
});

// ================= INITIALIZATION =================
$('#inventorySection').hide();
$('#infoAdvisorySection').hide();

let selectedType = $('input[name="type"]:checked').val();

// Trigger country change to load states/cities if editing
if($('#countryDropdown').val()) {
    $('#countryDropdown').trigger('change');
}

toggleAllSections();
toggleInventorySection();
toggleServiceTypeFields();
toggleProfessionalModule();

// Load categories matching the selected type on page init (e.g. after validation failure)
if(selectedType) {
    let oldCategoryId = '{{ old("category_id", $product->category_id ?? "") }}';
    loadCategories(selectedType, oldCategoryId);
}

let requireInitInventory = false;
// ONLY initialize inventory for "Items" type (vshop-product or product)
if (selectedType === 'vshop-product' || selectedType === 'product') {
    requireInitInventory = true;
}

if(requireInitInventory) {
    let oldInventoryType = $('#inventoryType').val();
    if(oldInventoryType) {
        $('#inventoryType').trigger('change');
    }
}

if(selectedType === 'information_advisory' || selectedType === 'vtravel-information_advisory') {
    $('#infoAdvisorySection').show();
    $('#tourGuidanceSection').show();
    $('#tourExtraFields').hide();
}

// Load service days if editing
let selectedDays = @json(old('service_days', $product->service_days ?? []));
if(typeof selectedDays === 'string') {
    try { selectedDays = JSON.parse(selectedDays); } catch(e) { selectedDays = []; }
}
if(selectedDays.length) {
    selectedDays.forEach(day => {
        $(`input[name="service_days[]"][value="${day}"]`).prop('checked', true);
    });
}

// Load availability if editing
if(oldAvailability) {
    renderAvailabilityDates();
}

});

function generateAIContent(btn) {
    let name = $('#name').val().trim();
    if (!name) {
        alert('Please enter a Product Name first.');
        $('#name').focus();
        return;
    }

    $(btn).find('.ai-logo').addClass('d-none');
    $(btn).find('.ai-spinner').removeClass('d-none');
    $(btn).prop('disabled', true);

    $.ajax({
        url: "{{ route('seller.products.ai.generate') }}",
        type: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            name: name
        },
        success: function(response) {
            $(btn).find('.ai-spinner').addClass('d-none');
            $(btn).find('.ai-logo').removeClass('d-none');
            $(btn).prop('disabled', false);
            if (response.success) {
                $('#short_summary').val(response.short_summary);
                
                // For full description (check if tinyMCE is used, if not set normal textarea)
                let editor = null;
                if(typeof tinymce !== 'undefined') {
                    editor = tinymce.get('description');
                }
                
                if (editor) {
                    editor.setContent(response.full_description);
                } else {
                    $('#description').val(response.full_description);
                }
                
                if (response.product_code) {
                    $('#product_code').val(response.product_code);
                }
                
                if (response.ingredients) {
                    $('#ingredients').val(response.ingredients);
                }
                if (response.storage_instruction) {
                    $('#storage_instruction').val(response.storage_instruction);
                }
            } else {
                alert(response.message || 'Error generating content.');
            }
        },
        error: function(xhr) {
            $(btn).find('.ai-spinner').addClass('d-none');
            $(btn).find('.ai-logo').removeClass('d-none');
            $(btn).prop('disabled', false);
            let errMsg = 'Failed to connect to the server. Please check the API key or try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errMsg = xhr.responseJSON.message;
            }
            alert(errMsg);
        }
    });
}
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

@push('scripts')
<script>
// ================= PROFESSIONAL SLOTS SYSTEM =================
var profSlots = [];

$(document).ready(function() {
    // Load existing slots from hidden input
    try {
        var raw = $('#professionalSlotsInput').val();
        if (raw && raw !== '[]') {
            profSlots = JSON.parse(raw);
        }
    } catch(e) { profSlots = []; }
    renderProfSlots();

    // Initialize Flatpickr for multiple dates
    flatpickr("#slotDate", {
        mode: "multiple",
        dateFormat: "Y-m-d"
    });
});

function addProfSlot() {
    var dateVal = $('#slotDate').val();
    var start = $('#slotStartTime').val();
    var end = $('#slotEndTime').val();

    if (!dateVal) { alert('Please select at least one date'); return; }
    if (!start) { alert('Please select start time'); return; }
    if (!end) { alert('Please select end time'); return; }
    if (start >= end) { alert('End time must be after start time'); return; }

    // Calculate duration
    var s = new Date('1970-01-01T' + start + ':00');
    var e = new Date('1970-01-01T' + end + ':00');
    var diffMs = e - s;
    var hrs = Math.floor(diffMs / 3600000);
    var mins = Math.floor((diffMs % 3600000) / 60000);
    var duration = (hrs > 0 ? hrs + 'h ' : '') + (mins > 0 ? mins + 'm' : '');

    var dates = dateVal.split(', ');
    dates.forEach(function(date) {
        profSlots.push({ date: date, start: start, end: end, duration: duration.trim() });
    });
    
    renderProfSlots();

    // Clear inputs for next entry
    document.querySelector('#slotDate')._flatpickr.clear();
    $('#slotStartTime').val('');
    $('#slotEndTime').val('');
}

function removeProfSlot(index) {
    profSlots.splice(index, 1);
    renderProfSlots();
}

function renderProfSlots() {
    var container = $('#profSlotTags');
    container.empty();

    profSlots.forEach(function(slot, i) {
        // Format date nicely
        var d = new Date(slot.date + 'T00:00:00');
        var dateStr = d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });

        container.append(
            '<div class="prof-slot-tag">' +
                '<i class="fa fa-calendar-day me-1"></i>' +
                '<span class="slot-date">' + dateStr + '</span>' +
                '<span class="slot-time">' + slot.start + ' - ' + slot.end + '</span>' +
                '<span class="slot-dur">(' + slot.duration + ')</span>' +
                '<button type="button" class="slot-remove" onclick="removeProfSlot(' + i + ')">&times;</button>' +
            '</div>'
        );
    });

    // Update hidden input
    $('#professionalSlotsInput').val(JSON.stringify(profSlots));
}
</script>
@endpush
<style>
.input-group-text {
 cursor: pointer; }
.day-box { cursor: pointer; }
.day-box input { display: none; }
.day-box span {
    padding: 8px 16px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background: #f8f9fa;
    transition: .2s;
}
.day-box input:checked + span {
    background: #0d6efd;
    color: #fff;
    border-color: #0d6efd;
}
.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 .25rem rgba(13,110,253,.15);
}
.btn-primary {
    background: linear-gradient(45deg, #0d6efd, #0b5ed7);
    border: none;
}
.btn-primary:hover {
    transform: translateY(-2px);
}
.nav-btn {
    border: 1px solid #004392;
    color: #004392;
    background: transparent;
    border-radius: 5px;
    width: 35px;
    height: 38px;
}
@media (max-width: 768px) {
    .col-auto { width: 100%; }
}
.availability-wrapper {
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 14px;
    background: #fff;
}
.availability-top {
    display: flex;
    gap: 10px;
    align-items: center;
}
.availability-input {
    height: 44px;
    border-radius: 10px;
}
.availability-btn {
    height: 44px;
    padding: 0 18px;
    border-radius: 10px;
    background: #0d6efd;
    color: #fff;
    border: none;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 600;
    white-space: nowrap;
}
.availability-btn:hover {
    background: #0b5ed7;
    color: #fff;
}
.availability-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 14px;
}
@media (max-width: 576px) {
    .availability-top {
        flex-direction: column;
        align-items: stretch;
    }
    .availability-btn {
        width: 100%;
        justify-content: center;
    }
}
.inventory-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #e9ecef;
}
/* Professional Slot Picker */
.slot-picker-card {
    background: #f8f9fb;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 14px;
}
.prof-slot-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #e8f0fe, #d4e4fc);
    border: 1px solid #b6d4fe;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 13px;
    color: #1a4d8f;
    font-weight: 500;
}
.prof-slot-tag .slot-date { font-weight: 600; }
.prof-slot-tag .slot-time { color: #0d6efd; }
.prof-slot-tag .slot-dur { color: #6c757d; font-size: 12px; }
.prof-slot-tag .slot-remove {
    background: none;
    border: none;
    color: #dc3545;
    font-size: 18px;
    line-height: 1;
    padding: 0 0 0 4px;
    cursor: pointer;
    font-weight: 700;
}
.prof-slot-tag .slot-remove:hover { color: #a71d2a; }
</style>
@endsection
