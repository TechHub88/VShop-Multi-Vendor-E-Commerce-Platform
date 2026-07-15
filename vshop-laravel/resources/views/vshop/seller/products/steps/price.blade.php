@extends('vshop.seller.main')

@push('styles')
<style>
    .nav-btn {
        border: 1px solid #004392;
        color: #004392;
        background: transparent;
        border-radius: 5px;
        width: 35px;
        height: 38px;
    }
    .bulk-btn {
        background-color : #004392 !important;
        border-color: #004392 !important;
        color: #fff !important;
        padding: 8px 8px !important;
        height: 36px !important;
    }
    .marketplace-wrapper {
        display: flex;
        gap: 25px;
        align-items: flex-start;
        flex-wrap: nowrap;
        overflow: hidden;
    }
    .marketplace-left {
        flex: 1;
        min-width: 0;
    }
    .marketplace-right {
        width: 280px;
        flex-shrink: 0;
        position: sticky;
        top: 20px;
    }
    .pricing-panel {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .pricing-section {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 15px;
        position: relative;
    }
    .pricing-section-title {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 15px;
        text-transform: uppercase;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .btn-boost-duration {
        background: #fff;
        border: 1px solid #cbd5e1;
        color: #475569;
        font-size: 11px;
        font-weight: 600;
        padding: 6px 10px;
        border-radius: 4px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-boost-duration.active, .btn-boost-duration:hover {
        background: #0b4da1;
        border-color: #0b4da1;
        color: #fff;
    }
    .budget-input-group {
        display: flex;
        align-items: center;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        overflow: hidden;
    }
    .budget-input-group .currency {
        background: #f1f5f9;
        padding: 4px 8px;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        border-right: 1px solid #cbd5e1;
    }
    .budget-input-group input {
        border: none;
        padding: 4px 8px;
        width: 100%;
        outline: none;
        font-size: 12px;
    }
    
    /* AI Recommendation Modal - Centered Light Sky Blue */
    .ai-modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.3);
        backdrop-filter: blur(2px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .ai-modal-content {
        background: #e6f7ff;
        border-radius: 16px;
        width: 450px;
        max-width: 90%;
        box-shadow: 0 20px 35px rgba(0,0,0,0.15);
        animation: modalSlideIn 0.25s ease;
        border: 1px solid #87ceeb;
        position: relative;
        margin: auto;
    }
    .ai-modal-header {
        background: #b0e0e6;
        color: #0369a1;
        padding: 14px 18px;
        border-radius: 16px 16px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #87ceeb;
    }
    .ai-modal-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 15px;
    }
    .ai-modal-close {
        background: transparent;
        border: none;
        color: #64748b;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.2s;
    }
    .ai-modal-close:hover {
        background: #f0f0f0;
        color: #ef4444;
    }
    .ai-modal-body {
        padding: 20px;
        max-height: 55vh;
        overflow-y: auto;
    }
    .ai-modal-footer {
        padding: 14px 18px;
        border-top: 1px solid #87ceeb;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        background: #b0e0e6;
        border-radius: 0 0 16px 16px;
    }
    .ai-modal-footer .btn {
        padding: 6px 18px;
        font-size: 12px;
        border-radius: 6px;
        cursor: pointer;
    }
    .ai-modal-footer .btn-outline-secondary {
        background: white;
        border: 1px solid #cbd5e1;
        color: #475569;
    }
    .ai-modal-footer .btn-primary {
        background: #0b4da1;
        border: none;
        color: white;
    }
    
    .goal-option {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .goal-option:hover, .goal-option.active {
        background: #0b4da1;
        border-color: #0b4da1;
        color: white;
    }
    .goal-option.active span, .goal-option.active i {
        color: white;
    }
    .tax-action-group {
        display: flex;
        gap: 4px;
        align-items: center;
    }
    .tax-btn {
        width: 32px !important;
        height: 32px !important;
        padding: 0 !important;
        border: none;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff !important;
        cursor: pointer;
    }
    .addTax { background: #198754 !important; }
    .delete-tax-btn, .removeTax { background: #dc3545 !important; }
    .tax-btn i { color: #fff !important; font-size: 12px; }
    .custom-check { width: 16px; height: 16px; accent-color: #0d6efd; }
    .payment-option {
        background: #f8fafc;
        padding: 10px 20px;
        border-radius: 8px;
        border: 2px solid #cbd5e1;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease-in-out;
        font-weight: 600;
        color: #475569;
        user-select: none;
    }
    
    .payment-option:hover {
        border-color: #0b4da1;
        background: #f8fafc;
    }
    
    .payment-option.active {
        background: #eff6ff;
        border-color: #0b4da1;
        color: #0b4da1;
        box-shadow: 0 0 0 3px rgba(11, 77, 161, 0.1);
    }

    .payment-option input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #0b4da1;
        cursor: pointer;
    }
    .discount-display { background: #f8f9fa; }
    
    /* Premium Toggle Switch Styles */
    .boost-toggle-label {
        width: 44px;
        height: 24px;
        background-color: #cbd5e1;
        border-radius: 12px;
        position: relative;
        cursor: pointer;
        transition: background-color 0.2s ease;
        display: inline-block;
        margin-bottom: 0;
    }
    .boost-toggle-label::after {
        content: '';
        width: 18px;
        height: 18px;
        background-color: white;
        border-radius: 50%;
        position: absolute;
        top: 3px;
        left: 3px;
        transition: transform 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    }
    .boost-toggle-switch:checked + .boost-toggle-label {
        background-color: #198754;
    }
    .boost-toggle-switch:checked + .boost-toggle-label::after {
        transform: translateX(20px);
    }

    .v-ai-button {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #0b4da1;
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 11px;
        cursor: pointer;
    }
    .ai-recommendation-card {
        border: 1px solid #d0e7ff;
        border-radius: 8px;
        background: #f0f9ff;
        overflow: hidden;
        margin-top: 10px;
    }
    .ai-recommendation-header {
        background: #0b4da1;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 8px 10px;
        text-align: center;
    }
    .ai-recommendation-body {
        padding: 12px;
    }
    
    @keyframes modalSlideIn {
        from { transform: translateY(-30px) scale(0.95); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }
    
    .form-range {
        width: 100%;
        height: 6px;
        -webkit-appearance: none;
        background: #e2e8f0;
        border-radius: 3px;
        outline: none;
    }
    .form-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #0b4da1;
        cursor: pointer;
        border: none;
    }
    .form-range::-webkit-slider-thumb:hover {
        background: #1e3a8a;
        transform: scale(1.2);
    }
    .professional-price-section {
        background: linear-gradient(135deg, #0b4da1 0%, #1e3a8a 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    .common-listing-header {
        background: linear-gradient(135deg, #0b4da1 0%, #1e3a8a 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        margin-top: 20px;
    }
    .age-range-container {
        background: #f8fafc;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .age-value {
        background: #0b4da1;
        color: white;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .validation-error {
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
    }
    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    /* Required field star */
    .required-star {
        color: #dc3545;
        font-size: 14px;
        margin-left: 4px;
    }
    
    /* Error message styling */
    .field-error {
        color: #dc3545;
        font-size: 11px;
        margin-top: 4px;
        display: block;
    }
    
    /* City Multi-Select Dropdown Styles */
    .city-multiselect-container {
        position: relative;
        width: 100%;
    }
    .city-multiselect-header {
        background: white;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 8px 12px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        min-height: 38px;
    }
    .city-multiselect-header.error {
        border-color: #dc3545;
    }
    .city-multiselect-header:hover {
        border-color: #0b4da1;
    }
    .city-multiselect-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        margin-top: 4px;
        z-index: 1000;
        display: none;
        max-height: 250px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .city-multiselect-container.show .city-multiselect-dropdown {
        display: block;
    }
    .city-search-box {
        padding: 8px;
        border-bottom: 1px solid #e2e8f0;
    }
    .city-search-box input {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-size: 12px;
    }
    .city-options-list {
        max-height: 200px;
        overflow-y: auto;
        padding: 5px 0;
    }
    .city-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        cursor: pointer;
        font-size: 12px;
        transition: background 0.2s;
    }
    .city-option:hover {
        background: #f1f5f9;
    }
    .city-option input {
        width: 16px;
        height: 16px;
        margin: 0;
        cursor: pointer;
    }
    .city-option span {
        flex: 1;
        cursor: pointer;
    }
</style>
@endpush

@section('content')

<div class="marketplace-wrapper">

<div class="marketplace-left">
<div class="card shadow-sm p-4">

<div class="d-flex justify-content-between align-items-center mb-4">
    @php
        $backUrl = (isset($product) && !empty($product->id))
            ? route('seller.products.seo.edit', $product->id)
            : route('seller.products.seo');
    @endphp

    <div class="d-flex align-items-center gap-3">
        <a href="{{ $backUrl }}"
           style="width:36px; height:36px; border:1px solid #0d6efd; border-radius:6px; display:flex; align-items:center; justify-content:center; text-decoration:none; color:#0d6efd; background:#fff; font-size:16px;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h5 class="mb-0" style="color:#0d6efd; font-weight:600;">
            @if(in_array($product->type, ['product', 'vshop-product']))
                Product Pricing
            @elseif(in_array($product->type, ['professional', 'vshop-professional', 'vtravel-professional']))
                Professional Service Pricing
            @else
                Service Pricing
            @endif
        </h5>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" id="pricingForm" action="{{ $product->id ? route('seller.products.price.update', $product->id) : route('seller.products.price.save') }}">
@csrf

@php
    $subcatName = $product->subcategory ? strtolower($product->subcategory->name) : '';
    $prodName = $product->name ? strtolower($product->name) : '';
    $isCourseProduct = str_contains($subcatName, 'course') || str_contains($prodName, 'course');
@endphp
@if(in_array($product->type, ['product', 'vshop-product']))
{{-- PRODUCT PRICING --}}
<h6 class="mb-3">Product Pricing <span class="required-star">*</span></h6>
<table class="table table-bordered" id="variantTable">
<thead class="table-light">
<tr>
@if($isCourseProduct)
<th>Course Duration</th>
@endif
<th>Price <span class="required-star">*</span></th><th>MRP</th><th>Discount %</th><th>Stock</th><th>Shipping</th>
@if($isCourseProduct)
<th>Action</th>
@endif
</tr>
</thead>
<tbody>
@php
    $variants = DB::table('product_attribute')->where('proid', $product->id)->get();
@endphp
@if($variants->count() > 0)
    @foreach($variants as $vIndex => $variant)
    <tr class="variant-row">
        @if($isCourseProduct)
        <td><input type="text" name="combinations[{{ $vIndex }}][variant_name]" class="form-control" value="{{ $variant->variant_name ?? '' }}" placeholder="e.g. 4month"></td>
        @else
        <input type="hidden" name="combinations[{{ $vIndex }}][variant_name]" value="{{ $variant->variant_name ?? 'Default' }}">
        @endif
        <td><input type="number" name="combinations[{{ $vIndex }}][price]" class="form-control price-input required-price" value="{{ $variant->price }}"><span class="field-error price-error"></span></td>
        <td><input type="number" name="combinations[{{ $vIndex }}][regular_price]" class="form-control regular-price-input" value="{{ $variant->regular_price }}"></td>
        <td><input type="number" name="combinations[{{ $vIndex }}][discount]" class="form-control discount-display" value="{{ $variant->discount_value }}"></td>
        <td><input type="number" name="combinations[{{ $vIndex }}][stock]" class="form-control" value="{{ $variant->product_stock }}"></td>
        <td><input type="number" name="combinations[{{ $vIndex }}][shipping]" class="form-control shipping-input" value="{{ $variant->shipping_amount }}"></td>
        @if($isCourseProduct)
        <td>@if($loop->first)<button type="button" class="btn btn-success addRow">+</button>@else<button type="button" class="btn btn-danger removeRow">-</button>@endif</td>
        @endif
    </tr>
    @endforeach
@else
    <tr class="variant-row">
        @if($isCourseProduct)
        <td><input type="text" name="combinations[0][variant_name]" class="form-control" value="{{ old('combinations.0.variant_name', '') }}" placeholder="e.g. 4month"></td>
        @else
        <input type="hidden" name="combinations[0][variant_name]" value="Default">
        @endif
        <td><input type="number" name="combinations[0][price]" class="form-control price-input required-price" value="{{ old('combinations.0.price', $product->price ?? '') }}"><span class="field-error price-error"></span></td>
        <td><input type="number" name="combinations[0][regular_price]" class="form-control regular-price-input" value="{{ old('combinations.0.regular_price', $product->regular_price ?? '') }}"></td>
        <td><input type="number" name="combinations[0][discount]" class="form-control discount-display" value="{{ old('combinations.0.discount', '') }}"></td>
        <td><input type="number" name="combinations[0][stock]" class="form-control" value="{{ old('combinations.0.stock', $product->stock ?? '') }}"></td>
        <td><input type="number" name="combinations[0][shipping]" class="form-control shipping-input" value="{{ old('combinations.0.shipping', $product->shipping ?? '') }}"></td>
        @if($isCourseProduct)
        <td><button type="button" class="btn btn-success addRow">+</button></td>
        @endif
    </tr>
@endif
</tbody>
</table>
@endif

{{-- PROFESSIONAL SERVICE PRICING --}}
@if(in_array($product->type, ['professional', 'vshop-professional', 'vtravel-professional']))
<div class="professional-price-section">
    <div class="d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-star-fill" style="font-size: 20px;"></i>
        <h6 class="mb-0 fw-bold" style="color: white;">Professional Service Fee</h6>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label style="font-size:13px; font-weight:600;" class="mb-2">Session Price <span class="required-star" style="color: #ffcccc;">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-white text-muted">₹</span>
                <input type="number" name="professional_price" id="professionalPrice" value="{{ old('professional_price', $product->professional_price ?? '') }}" class="form-control shadow-none service-price-input required-service-price">
            </div>
            <span class="field-error" id="professionalPriceError"></span>
        </div>
        <div class="col-md-6">
            <label style="font-size:13px; font-weight:600;" class="mb-2">Price Type</label>
            <select name="price_type" id="priceType" class="form-select shadow-none">
                <option value="fixed" {{ old('price_type', $product->price_type ?? 'fixed') == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                <option value="hourly" {{ old('price_type', $product->price_type ?? 'fixed') == 'hourly' ? 'selected' : '' }}>Hourly Rate</option>
                <option value="negotiable" {{ old('price_type', $product->price_type ?? 'fixed') == 'negotiable' ? 'selected' : '' }}>Negotiable</option>
            </select>
        </div>
    </div>
</div>
@endif

{{-- SERVICE / TOUR PRICING --}}
@if(in_array($product->type, ['service', 'vshop-service', 'vtravel-service', 'tour', 'vtravel-tour', 'information_advisory', 'vtravel-information_advisory']))
<div class="pricing-panel mt-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-tags-fill text-primary" style="font-size: 18px;"></i>
        <h6 class="mb-0 fw-bold" style="color: #0b4da1;">Service Pricing Details</h6>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="pricing-section">
                <label style="font-size:12px; font-weight:700;">Adult Price <span class="required-star">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light">₹</span>
                    <input type="number" name="adult_price" id="adultPrice" value="{{ old('adult_price', $product->adult_price) }}" class="form-control service-price-input required-service-price">
                </div>
                <span class="field-error" id="adultPriceError"></span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pricing-section">
                <label style="font-size:12px; font-weight:700;">Child Price</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">₹</span>
                    <input type="number" name="child_price" id="childPrice" value="{{ old('child_price', $product->child_price) }}" class="form-control service-price-input">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pricing-section">
                <label style="font-size:12px; font-weight:700;">Group Price</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">₹</span>
                    <input type="number" name="group_price" id="groupPrice" value="{{ old('group_price', $product->group_price) }}" class="form-control service-price-input">
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- PROMOTION & BOOSTING SECTION --}}
<div class="pricing-panel mt-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-rocket-takeoff-fill text-danger" style="font-size: 18px;"></i>
        <h6 class="mb-0 fw-bold" style="color: #0b4da1;">Promotion & Boosting</h6>
        <a href="{{ url('seller/grievance') }}" class="ms-auto text-warning fw-bold text-decoration-none" style="font-size: 12px;">
            <i class="bi bi-lightbulb-fill"></i> Help Center
        </a>
    </div>
    <p style="font-size: 12px; color: #64748b; margin-bottom: 15px;">Configure your next boost campaign to reach your target buyers and convert them.</p>

    <!-- Proper Toggle Switch Bar -->
    <div class="pricing-section mb-3 p-3 bg-light border-0 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-0 fw-bold" style="font-size: 13px; color: #1e3a8a;">Enable Promotion & Boosting</h6>
            <small class="text-muted" style="font-size: 11px;">Promote this listing to increase reach, impressions, and sales conversions.</small>
        </div>
        <div class="form-check form-switch p-0 m-0 d-flex align-items-center">
            <input type="checkbox" name="boost_status" id="boostStatus" value="1" {{ old('boost_status', $product->boost_status ?? false) ? 'checked' : '' }} class="boost-toggle-switch" style="display: none;">
            <label for="boostStatus" class="boost-toggle-label"></label>
            <span id="boostToggleStateText" class="ms-2 fw-bold text-muted" style="font-size: 12px; min-width: 60px;">Disabled</span>
        </div>
    </div>

    <!-- Sliding Config Container -->
    <div id="boostConfigContainer" style="{{ old('boost_status', $product->boost_status ?? false) ? '' : 'display: none;' }}">
        <div class="row g-3">
            <div class="col-md-5">
                <div class="pricing-section mb-3">
                    <div class="pricing-section-title">
                        DURATION & BUDGET
                    </div>
                    
                    @if(isset($adminBoostRule))
                        <div class="alert alert-info py-2" style="font-size: 11px;">
                            <i class="bi bi-info-circle"></i> Duration is set by admin category rule. If you have any issues regarding price, connect admin.
                        </div>
                        <input type="text" class="form-control bg-light" value="{{ $adminBoostDays ?? 7 }} Days" readonly>
                        <input type="hidden" name="boost_duration" id="boostDuration" value="{{ $adminBoostDays ?? 7 }}">
                    @else
                        <div class="alert alert-info py-2" style="font-size: 11px;">
                            <i class="bi bi-info-circle"></i> Duration is set by admin. If you have any issues regarding price, connect admin.
                        </div>
                        <input type="text" class="form-control bg-light" value="{{ old('boost_duration', $product->boost_duration ?? 7) }} Days" readonly>
                        <input type="hidden" name="boost_duration" id="boostDuration" value="{{ old('boost_duration', $product->boost_duration ?? 7) }}">
                    @endif
                </div>

                <div class="pricing-section">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label style="font-size:10px; font-weight:600;">Daily Budget</label>
                            <div class="budget-input-group">
                                <span class="currency">₹</span>
                                <input type="number" name="daily_budget" id="dailyBudget" value="{{ old('daily_budget', $product->daily_budget ?? ($boostPerDay !== null ? number_format($boostPerDay, 2, '.', '') : '')) }}" readonly style="background-color:#f1f5f9;">
                            </div>
                        </div>
                        <div class="col-6">
                            <label style="font-size:10px; font-weight:600;">Daily spend limit</label>
                            <div class="budget-input-group">
                                <span class="currency">₹</span>
                                <input type="number" name="daily_limit" id="dailyLimit" value="{{ old('daily_limit', $product->daily_limit ?? 0) }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <label style="font-size:10px; font-weight:600;">Total Budget (Cost)</label>
                            <div class="budget-input-group">
                                <span class="currency">₹</span>
                                <input type="number" name="total_budget" id="totalBudget" value="{{ old('total_budget', $product->total_budget ?? 0) }}" readonly style="background-color: #f1f5f9;">
                            </div>
                            <small class="text-muted" style="font-size: 9px;">This amount will be deducted from your wallet if you enable boost.</small>
                        </div>
                    </div>
                </div>

           
            </div>

            <div class="col-md-3">
                <div class="pricing-section mb-3">
                    <div class="pricing-section-title">SECTION 3: TARGET AUDIENCE</div>
                    <div class="row g-2">
                        <div class="col-12">
                            <label style="font-size: 11px; font-weight: 600;">Country</label>
                            <select name="target_country" id="targetCountry" class="form-select form-select-sm">
                                <option value="">Select Country</option>
                                @foreach($countries ?? [] as $c)
                                    <option value="{{ $c->id }}" {{ (old('target_country', $product->target_country ?? '') == $c->id) ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label style="font-size: 11px; font-weight: 600;">State</label>
                            <select name="target_state" id="targetState" class="form-select form-select-sm">
                                <option value="">Select State</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label style="font-size: 11px; font-weight: 600;">City (Multi-select)</label>
                            <div class="city-multiselect-container">
                                <div class="city-multiselect-header" id="cityDropdownHeader">
                                    <span id="selectedCitiesText">Select Cities</span>
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                                <div class="city-multiselect-dropdown" id="cityDropdownList">
                                    <div class="city-search-box">
                                        <input type="text" id="citySearchInput" placeholder="Search cities..." class="form-control form-control-sm">
                                    </div>
                                    <div class="city-options-list" id="cityOptionsList">
                                        @foreach($cities ?? [] as $city)
                                            <label class="city-option">
                                                <input type="checkbox" name="target_city[]" value="{{ $city->id }}" class="city-checkbox" 
                                                    {{ (is_array(old('target_city', json_decode($product->target_city ?? '[]', true))) && in_array($city->id, old('target_city', json_decode($product->target_city ?? '[]', true)))) ? 'checked' : '' }}>
                                                <span>{{ $city->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="pricing-section mb-3">
                    <div class="pricing-section-title">SECTION 4: PROMOTION GOAL</div>
                    <div class="mb-3">
                        <label style="font-size: 11px; font-weight: 600;">Age Group</label>
                        <div class="age-range-container">
                            <div class="d-flex justify-content-between mb-2">
                                <span style="font-size: 11px;">Min Age: <span class="age-value" id="ageMinDisplay">20</span></span>
                                <span style="font-size: 11px;">Max Age: <span class="age-value" id="ageMaxDisplay">45</span></span>
                            </div>
                            <input type="range" name="age_min" id="ageMinRange" class="form-range" min="18" max="65" step="1" value="{{ old('age_min', $product->age_min ?? 20) }}">
                            <input type="range" name="age_max" id="ageMaxRange" class="form-range mt-2" min="18" max="65" step="1" value="{{ old('age_max', $product->age_max ?? 45) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label style="font-size: 11px; font-weight: 600;">Language</label>
                        <select name="language" id="languageSelect" class="form-select form-select-sm">
                            <option value="english" {{ old('language', $product->language ?? 'english') == 'english' ? 'selected' : '' }}>English</option>
                            <option value="hindi" {{ old('language', $product->language ?? 'english') == 'hindi' ? 'selected' : '' }}>Hindi</option>
                            <option value="bengali" {{ old('language', $product->language ?? 'english') == 'bengali' ? 'selected' : '' }}>Bengali</option>
                            <option value="tamil" {{ old('language', $product->language ?? 'english') == 'tamil' ? 'selected' : '' }}>Tamil</option>
                            <option value="telugu" {{ old('language', $product->language ?? 'english') == 'telugu' ? 'selected' : '' }}>Telugu</option>
                        </select>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <div class="goal-option {{ old('promotion_goal', $product->promotion_goal ?? 'enquiries') == 'views' ? 'active' : '' }}" data-goal="views"><i class="bi bi-eye"></i> <span>More Views</span></div>
                        <div class="goal-option {{ old('promotion_goal', $product->promotion_goal ?? 'enquiries') == 'enquiries' ? 'active' : '' }}" data-goal="enquiries"><i class="bi bi-chat-dots"></i> <span>More Enquiries</span></div>
                        <div class="goal-option {{ old('promotion_goal', $product->promotion_goal ?? 'enquiries') == 'orders' ? 'active' : '' }}" data-goal="orders"><i class="bi bi-cart"></i> <span>More Orders</span></div>
                        <div class="goal-option {{ old('promotion_goal', $product->promotion_goal ?? 'enquiries') == 'awareness' ? 'active' : '' }}" data-goal="awareness"><i class="bi bi-megaphone"></i> <span>Brand Awareness</span></div>
                        <div class="goal-option {{ old('promotion_goal', $product->promotion_goal ?? 'enquiries') == 'clicks' ? 'active' : '' }}" data-goal="clicks"><i class="bi bi-mouse"></i> <span>More Clicks</span></div>
                    </div>
                    <input type="hidden" name="promotion_goal" id="promotionGoal" value="{{ old('promotion_goal', $product->promotion_goal ?? 'enquiries') }}">
                </div>

            </div>
        </div>
    </div>
</div>


{{-- COMMON LISTING PRICING --}}
<div class="common-listing-header">
    <h6 class="mb-0 fw-bold" style="color: white !important;">COMMON LISTING PRICING</h6>
</div>

{{-- PAYMENT TYPE SELECTION --}}
<div class="mt-3">
    <label class="fw-bold">Payment Type Selection <span class="required-star">*</span></label>
    <div class="payment-box mt-2 d-flex gap-3 flex-wrap">
        @php
            $selectedPayments = old('payment_type');
            if (empty($selectedPayments)) {
                $selectedPayments = $product->payment_type;
                if (is_string($selectedPayments)) {
                    $selectedPayments = json_decode($selectedPayments, true);
                }
            }
            if (empty($selectedPayments)) {
                $selectedPayments = ['Multi-Pay'];
            }
        @endphp
        <label class="payment-option d-inline-flex align-items-center gap-2">
            <input type="checkbox" class="payment-check custom-check required-payment" name="payment_type[]" value="Multi-Pay" {{ in_array('Multi-Pay', $selectedPayments) ? 'checked' : '' }}>
            <span>Multi-Pay</span>
        </label>
        <label class="payment-option d-inline-flex align-items-center gap-2">
            <input type="checkbox" class="payment-check custom-check required-payment" name="payment_type[]" value="Single-Pay" {{ in_array('Single-Pay', $selectedPayments) ? 'checked' : '' }}>
            <span>Single-Pay</span>
        </label>
    </div>
    <span class="field-error" id="paymentError"></span>
    @error('payment_type')
    <div class="validation-error">{{ $message }}</div>
    @enderror
</div>

{{-- DISCOUNT SECTION --}}
<div class="mt-4 p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
    <div class="row align-items-end g-3">
        <div class="col-md-3">
            <label class="fw-bold" style="font-size: 12px;">Is Discount?</label>
            <select name="is_discount" id="isDiscount" class="form-select shadow-none mt-1">
                <option value="0" {{ old('is_discount', $product->is_discount ?? 0) == 0 ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('is_discount', $product->is_discount ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="fw-bold" style="font-size: 12px;">Discount Type</label>
            <select name="discount_type" id="discountType" class="form-select shadow-none mt-1" {{ old('is_discount', $product->is_discount ?? 0) == 0 ? 'disabled' : '' }}>
                <option value="percentage" {{ old('discount_type', $product->discount_type ?? 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                <option value="fixed" {{ old('discount_type', $product->discount_type ?? 'percentage') == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="fw-bold" style="font-size: 12px;">Discount Value</label>
            <div class="input-group mt-1">
                <span class="input-group-text bg-white"><i class="bi bi-tag"></i></span>
                <input type="number" name="discount_value" id="discountValue" class="form-control" value="{{ old('discount_value', $product->discount_value ?? '') }}" {{ old('is_discount', $product->is_discount ?? 0) == 0 ? 'readonly' : '' }}>
            </div>
        </div>
        <div class="col-md-3"></div>
        <div class="col-md-3">
            <label class="fw-bold" style="font-size: 12px;">Start Date</label>
            <input type="date" name="start_date" id="startDate" class="form-control shadow-none mt-1" value="{{ old('start_date', $product->discount_start_date ?? '') }}">
        </div>
        <div class="col-md-3">
            <label class="fw-bold" style="font-size: 12px;">End Date</label>
            <input type="date" name="end_date" id="endDate" class="form-control shadow-none mt-1" value="{{ old('end_date', $product->discount_end_date ?? '') }}">
        </div>
    </div>
</div>

{{-- TAX CALCULATION SECTION --}}
<div class="mt-4 p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <label class="fw-bold mb-0 text-uppercase" style="font-size: 12px; color: #475569;">Tax Calculation</label>
        <small class="text-muted">Auto-calculated</small>
    </div>

    @php
        $taxNames = $product->tax_name;
        if (is_string($taxNames)) {
            $taxNames = json_decode($taxNames, true);
        }
        if (!is_array($taxNames)) {
            $taxNames = [];
        }

        $taxPercentages = $product->tax_percentage;
        if (is_string($taxPercentages)) {
            $taxPercentages = json_decode($taxPercentages, true);
        }
        if (!is_array($taxPercentages)) {
            $taxPercentages = [];
        }
    @endphp

    <div id="taxContainer">
    @if(!empty($taxNames) && count($taxNames) > 0)
        @foreach($taxNames as $i => $name)
        <div class="row g-2 tax-row mb-2 align-items-center">
            <div class="col-md-4"><input type="text" name="taxes[{{ $i }}][name]" value="{{ $name }}" placeholder="Tax Name" class="form-control tax-name" style="font-size: 13px;"></div>
            <div class="col-md-3"><div class="input-group"><input type="number" name="taxes[{{ $i }}][percentage]" value="{{ $taxPercentages[$i] ?? '' }}" placeholder="Tax %" class="form-control tax-percentage" style="font-size: 13px;"><span class="input-group-text bg-white">%</span></div></div>
            <div class="col-md-3"><div class="input-group"><span class="input-group-text bg-light">₹</span><input type="text" class="form-control tax-amount bg-light" placeholder="Tax Amount" readonly style="font-size: 13px;"></div></div>
            <div class="col-md-2"><div class="tax-action-group"><button type="button" class="tax-btn addTax"><i class="bi bi-plus-lg"></i></button><button type="button" class="tax-btn delete-tax-btn removeTax"><i class="bi bi-trash"></i></button></div></div>
        </div>
        @endforeach
    @else
        <div class="row g-2 tax-row mb-2 align-items-center">
            <div class="col-md-4"><input type="text" name="taxes[0][name]" placeholder="GST" class="form-control tax-name" style="font-size: 13px;"></div>
            <div class="col-md-3"><div class="input-group"><input type="number" name="taxes[0][percentage]" placeholder="18" class="form-control tax-percentage" style="font-size: 13px;"><span class="input-group-text bg-white">%</span></div></div>
            <div class="col-md-3"><div class="input-group"><span class="input-group-text bg-light">₹</span><input type="text" class="form-control tax-amount bg-light" placeholder="Tax Amount" readonly style="font-size: 13px;"></div></div>
            <div class="col-md-2"><div class="tax-action-group"><button type="button" class="tax-btn addTax"><i class="bi bi-plus-lg"></i></button><button type="button" class="tax-btn delete-tax-btn removeTax"><i class="bi bi-trash"></i></button></div></div>
        </div>
    @endif
    </div>
</div>

{{-- NET TOTAL --}}
<div class="mt-4 p-3 d-flex align-items-center justify-content-between" style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px;">
    <div>
        <label class="fw-bold mb-0 text-uppercase" style="font-size: 13px; color: #1e3a8a;">Net Total Auto</label>
        <p class="mb-0 text-muted" style="font-size: 11px;">Includes base price, discount and all taxes.</p>
    </div>
    <div class="input-group" style="width: 250px;">
        <span class="input-group-text bg-white fw-bold text-primary" style="font-size: 18px;">₹</span>
        <input type="text" name="net_total" id="netTotal" value="{{ old('net_total', $product->net_total ?? '0') }}" class="form-control bg-white fw-bold text-primary" readonly style="font-size: 18px; text-align: right;">
    </div>
</div>

<div class="mt-4 text-end">
    <button class="btn btn-outline-secondary me-2 px-4" type="button">Cancel</button>
    <button class="btn btn-primary px-5" style="background: #0b4da1; border-color: #0b4da1;" type="submit" id="submitBtn">Save Details</button>
</div>

</form>

</div>
</div>

{{-- RIGHT SIDEBAR --}}
<div class="marketplace-right">
    @include('vshop.seller.partials.product-progress',['product'=>$product])
</div>

</div>



<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<script>
$(document).ready(function(){
    let rowIndex = $('.variant-row').length;
    let taxIndex = $('.tax-row').length;

    // ================= REAL-TIME VALIDATION & ERROR REMOVAL =================
    function removeFieldError(inputElement) {
        $(inputElement).removeClass('is-invalid');
        $(inputElement).closest('.input-group').find('.is-invalid').removeClass('is-invalid');
        $(inputElement).siblings('.field-error').text('');
        $(inputElement).parent().find('.field-error').text('');
        // Also remove error from next sibling if exists
        if($(inputElement).next('.field-error').length) {
            $(inputElement).next('.field-error').text('');
        }
    }

    function showFieldError(inputElement, message) {
        $(inputElement).addClass('is-invalid');
        let errorSpan = $(inputElement).siblings('.field-error');
        if(errorSpan.length === 0) {
            errorSpan = $(inputElement).closest('.input-group').parent().find('.field-error');
        }
        if(errorSpan.length === 0) {
            errorSpan = $(inputElement).parent().find('.field-error');
        }
        if(errorSpan.length === 0 && $(inputElement).next('.field-error').length) {
            errorSpan = $(inputElement).next('.field-error');
        }
        if(errorSpan.length) errorSpan.text(message);
    }

    // Product price validation - remove error on typing
    $(document).on('input', '.required-price', function() {
        if($(this).val() && parseFloat($(this).val()) > 0) {
            removeFieldError(this);
        } else if($(this).val() === '') {
            removeFieldError(this);
        } else if(parseFloat($(this).val()) <= 0) {
            showFieldError(this, 'Price must be greater than 0');
        }
    });

    // Professional price validation
    $('#professionalPrice').on('input', function() {
        if($(this).val() && parseFloat($(this).val()) > 0) {
            removeFieldError(this);
            $('#professionalPriceError').text('');
        } else if($(this).val() === '') {
            removeFieldError(this);
        } else if(parseFloat($(this).val()) <= 0) {
            showFieldError(this, 'Session price must be greater than 0');
            $('#professionalPriceError').text('Session price must be greater than 0');
        }
    });

    // Adult price validation
    $('#adultPrice').on('input', function() {
        if($(this).val() && parseFloat($(this).val()) > 0) {
            removeFieldError(this);
            $('#adultPriceError').text('');
        } else if($(this).val() === '') {
            removeFieldError(this);
        } else if(parseFloat($(this).val()) <= 0) {
            showFieldError(this, 'Adult price must be greater than 0');
            $('#adultPriceError').text('Adult price must be greater than 0');
        }
    });

    // Function to update payment option visual active state
    function updatePaymentOptionStyles() {
        $('.payment-check').each(function() {
            let label = $(this).closest('.payment-option');
            if($(this).is(':checked')) {
                label.addClass('active');
            } else {
                label.removeClass('active');
            }
        });
    }

    // Call initially on page load
    updatePaymentOptionStyles();

    // Payment type validation - remove error when any checkbox is checked
    $('.payment-check').on('change', function() {
        updatePaymentOptionStyles();
        let checked = $('.payment-check:checked').length;
        if(checked > 0) {
            $('#paymentError').text('');
        } else {
            $('#paymentError').text('Please select at least one payment type');
        }
    });

    // ================= FORM SUBMIT VALIDATION =================
    $('#pricingForm').on('submit', function(e) {
        let isValid = true;

        // Product type validation
        @if(in_array($product->type, ['product', 'vshop-product']))
            let priceValid = false;
            $('.required-price').each(function() {
                let priceVal = $(this).val();
                if(priceVal && parseFloat(priceVal) > 0) {
                    priceValid = true;
                    removeFieldError(this);
                } else if(!priceVal || parseFloat(priceVal) <= 0) {
                    showFieldError(this, 'Price is required and must be greater than 0');
                    isValid = false;
                }
            });
        @endif

        // Professional service validation
        @if(in_array($product->type, ['professional', 'vshop-professional', 'vtravel-professional']))
            let profPrice = $('#professionalPrice').val();
            if(!profPrice || parseFloat(profPrice) <= 0) {
                showFieldError($('#professionalPrice'), 'Session price is required');
                $('#professionalPriceError').text('Session price is required');
                isValid = false;
            } else {
                removeFieldError($('#professionalPrice'));
                $('#professionalPriceError').text('');
            }
        @endif

        // Service/Tour validation
        @if(in_array($product->type, ['service', 'vshop-service', 'vtravel-service', 'tour', 'vtravel-tour', 'information_advisory', 'vtravel-information_advisory']))
            let adultPrice = $('#adultPrice').val();
            if(!adultPrice || parseFloat(adultPrice) <= 0) {
                showFieldError($('#adultPrice'), 'Adult price is required');
                $('#adultPriceError').text('Adult price is required');
                isValid = false;
            } else {
                removeFieldError($('#adultPrice'));
                $('#adultPriceError').text('');
            }
        @endif

        // Payment type validation
        let paymentChecked = $('.payment-check:checked').length;
        if(paymentChecked === 0) {
            $('#paymentError').text('Please select at least one payment type');
            isValid = false;
        } else {
            $('#paymentError').text('');
        }

        if(!isValid) {
            e.preventDefault();
            // Scroll to first error
            $('html, body').animate({
                scrollTop: $('.is-invalid:first, #paymentError:not(:empty)').first().offset().top - 100
            }, 500);
            return false;
        }
    });

    // ================= AGE RANGE SLIDER =================
    function updateAgeDisplay() {
        let minVal = $('#ageMinRange').val();
        let maxVal = $('#ageMaxRange').val();
        if(parseInt(minVal) > parseInt(maxVal)) {
            $('#ageMinRange').val(maxVal);
            minVal = maxVal;
        }
        $('#ageMinDisplay').text(minVal);
        $('#ageMaxDisplay').text(maxVal);
    }
    
    $('#ageMinRange, #ageMaxRange').on('input', function() {
        let minVal = parseInt($('#ageMinRange').val());
        let maxVal = parseInt($('#ageMaxRange').val());
        if($(this).attr('id') == 'ageMinRange' && minVal > maxVal) {
            $('#ageMaxRange').val(minVal);
        }
        if($(this).attr('id') == 'ageMaxRange' && maxVal < minVal) {
            $('#ageMinRange').val(maxVal);
        }
        updateAgeDisplay();
    });
    updateAgeDisplay();

    // ================= BOOST DURATION BUTTONS =================
    let currentDuration = $('#boostDuration').val();
    $('.btn-boost-duration').each(function() {
        if($(this).data('days') == currentDuration) {
            $(this).addClass('active');
        }
    });
    
    $('.btn-boost-duration').on('click', function() {
        $('.btn-boost-duration').removeClass('active');
        $(this).addClass('active');
        $('#boostDuration').val($(this).data('days'));
    });

    // ================= BOOST CAMPAIGN SECTION ENABLE/DISABLE =================
    function updateBoostSections() {
        let isBoostEnabled = $('#boostStatus').is(':checked');
        if(isBoostEnabled) {
            $('#boostConfigContainer').slideDown(300);
            $('#boostToggleStateText').text('Active').removeClass('text-muted').addClass('text-success');
            $('#dailyBudget, #dailyLimit, #totalBudget, #stopDailyBudget, #stopTotalBudget, #notifyBeforeExhaust, select[name="notify_percentage"], #targetCountry, #targetState, .city-checkbox, #ageMinRange, #ageMaxRange, #languageSelect, .goal-option, #openAiModalBtn').prop('disabled', false).css('opacity', '1');
            $('.btn-boost-duration').removeClass('disabled').css('pointer-events', 'auto');
            $('.goal-option').css('pointer-events', 'auto');
            $('#modalCityOptionsList input').prop('disabled', false);
        } else {
            $('#boostConfigContainer').slideUp(300);
            $('#boostToggleStateText').text('Disabled').removeClass('text-success').addClass('text-muted');
            $('#dailyBudget, #dailyLimit, #totalBudget, #stopDailyBudget, #stopTotalBudget, #notifyBeforeExhaust, select[name="notify_percentage"], #targetCountry, #targetState, .city-checkbox, #ageMinRange, #ageMaxRange, #languageSelect, .goal-option, #openAiModalBtn').prop('disabled', true).css('opacity', '0.6');
            $('.btn-boost-duration').addClass('disabled').css('pointer-events', 'none');
            $('.goal-option').css('pointer-events', 'none');
            $('#modalCityOptionsList input').prop('disabled', true);
        }
        calculateNetTotal();
    }

    $('#boostStatus').on('change', function() {
        updateBoostSections();
    });
    
    // Run initially on page load
    let isBoostEnabled = $('#boostStatus').is(':checked');
    if(isBoostEnabled) {
        $('#boostConfigContainer').show();
        $('#boostToggleStateText').text('Active').removeClass('text-muted').addClass('text-success');
        $('#dailyBudget, #dailyLimit, #totalBudget, #stopDailyBudget, #stopTotalBudget, #notifyBeforeExhaust, select[name="notify_percentage"], #targetCountry, #targetState, .city-checkbox, #ageMinRange, #ageMaxRange, #languageSelect, .goal-option, #openAiModalBtn').prop('disabled', false).css('opacity', '1');
    } else {
        $('#boostConfigContainer').hide();
        $('#boostToggleStateText').text('Disabled').removeClass('text-success').addClass('text-muted');
        $('#dailyBudget, #dailyLimit, #totalBudget, #stopDailyBudget, #stopTotalBudget, #notifyBeforeExhaust, select[name="notify_percentage"], #targetCountry, #targetState, .city-checkbox, #ageMinRange, #ageMaxRange, #languageSelect, .goal-option, #openAiModalBtn').prop('disabled', true).css('opacity', '0.6');
    }

    // ================= BOOST DURATION & BUDGET =================
    function calculateTotalBudget() {
        let dailyBudget = parseFloat($('#dailyBudget').val()) || 0;
        let days = parseInt($('#boostDuration').val()) || 7;
        let total = dailyBudget * days;
        $('#totalBudget').val(total.toFixed(2));
        calculateNetTotal();
    }

    $('.btn-boost-duration').on('click', function() {
        $('.btn-boost-duration').removeClass('active');
        $(this).addClass('active');
        let days = $(this).data('days');
        $('#boostDuration').val(days);
        
        calculateTotalBudget();
        triggerAiAutoFetch();
    });

    $(document).on('input', '#dailyBudget', function() {
        calculateTotalBudget();
        triggerAiAutoFetch();
    });
    
    // Initialize default total budget if there is a selected duration
    if ($('#boostDuration').val()) {
        let days = $('#boostDuration').val();
        $('.btn-boost-duration[data-days="'+days+'"]').addClass('active');
        calculateTotalBudget();
    }

    // ================= PROMOTION GOAL SELECTION =================
    $('.goal-option').on('click', function() {
        $('.goal-option').removeClass('active');
        $(this).addClass('active');
        $('#promotionGoal').val($(this).data('goal'));
    });

    // ================= DISCOUNT TOGGLE =================
    $('#isDiscount').on('change', function() {
        if($(this).val() == '0') {
            $('#discountType').prop('disabled', true);
            $('#discountValue').prop('readonly', true).val('');
        } else {
            $('#discountType').prop('disabled', false);
            $('#discountValue').prop('readonly', false);
        }
        calculateNetTotal();
    });
    $('#isDiscount').trigger('change');

    // ================= ADD/REMOVE VARIANT ROW =================
    $(document).on('click', '.addRow', function(e) {
        e.preventDefault();
        let $firstRow = $('#variantTable tbody tr:first');
        let newRow = $firstRow.clone(true);
        newRow.find('input').val('');
        newRow.find('.discount-display').val('');
        newRow.find('.addRow').removeClass('btn-success addRow').addClass('btn-danger removeRow').text('-');
        newRow.find('.field-error').text('');
        newRow.find('.price-input').removeClass('is-invalid');
        newRow.find('input,select').each(function() {
            let name = $(this).attr('name');
            if(name) $(this).attr('name', name.replace(/\[\d+\]/, `[${rowIndex}]`));
        });
        $('#variantTable tbody').append(newRow);
        rowIndex++;
    });

    $(document).on('click', '.removeRow', function() {
        if($('.variant-row').length > 1) $(this).closest('tr').remove();
        else alert('At least one variant is required');
        calculateNetTotal();
    });

    // ================= PRICE CALCULATIONS =================
    $(document).on('input', '.price-input, .regular-price-input', function() {
        let row = $(this).closest('tr');
        let price = parseFloat(row.find('.price-input').val()) || 0;
        let regular = parseFloat(row.find('.regular-price-input').val()) || 0;
        let discount = 0;
        if(regular > price && regular > 0) discount = ((regular - price) / regular) * 100;
        row.find('.discount-display').val(discount.toFixed(2));
        calculateNetTotal();
    });

    $(document).on('input', '.service-price-input', function() { calculateNetTotal(); });
    $(document).on('input change', '#discountValue, #discountType', function() { calculateNetTotal(); });
    $(document).on('input', '.tax-percentage', function() { calculateNetTotal(); });
    $(document).on('input', '.shipping-input', function() { calculateNetTotal(); });

    function getBasePrice() {
        if($('.price-input').length > 0 && $('.price-input').first().val()) {
            return parseFloat($('.price-input').first().val()) || 0;
        }
        if($('#professionalPrice').length > 0 && $('#professionalPrice').val()) {
            return parseFloat($('#professionalPrice').val()) || 0;
        }
        let adult = parseFloat($('#adultPrice').val()) || 0;
        let child = parseFloat($('#childPrice').val()) || 0;
        let group = parseFloat($('#groupPrice').val()) || 0;
        return adult + child + group;
    }
    
    function getShipping() {
        if($('.shipping-input').length > 0 && $('.shipping-input').first().val()) {
            return parseFloat($('.shipping-input').first().val()) || 0;
        }
        return 0;
    }

    function calculateDiscount(price) {
        let isDiscount = $('#isDiscount').val();
        if(isDiscount == '0') return 0;
        let discountValue = parseFloat($('#discountValue').val()) || 0;
        if(discountValue <= 0) return 0;
        let discountType = $('#discountType').val();
        let discountAmount = discountType == 'percentage' ? (price * discountValue) / 100 : discountValue;
        return discountAmount > price ? price : discountAmount;
    }

    function calculateTax(price) {
        let totalTax = 0;
        $('.tax-row').each(function() {
            let percent = parseFloat($(this).find('.tax-percentage').val()) || 0;
            let amount = (price * percent) / 100;
            $(this).find('.tax-amount').val(amount.toFixed(2));
            totalTax += amount;
        });
        return totalTax;
    }

    function calculateNetTotal() {
        let basePrice = getBasePrice();
        let discountAmount = calculateDiscount(basePrice);
        let priceAfterDiscount = basePrice - discountAmount;
        let taxAmount = calculateTax(priceAfterDiscount);
        let shippingAmount = getShipping();
        let netTotal = priceAfterDiscount + taxAmount + shippingAmount;
        
        if ($('#boostStatus').is(':checked')) {
            let boostTotal = parseFloat($('#totalBudget').val()) || 0;
            netTotal += boostTotal;
        }
        
        $('#netTotal').val(netTotal.toFixed(2));
    }

    // ================= TAX ROW MANAGEMENT =================
    $(document).on('click', '.addTax', function(e) {
        e.preventDefault();
        let html = `<div class="row g-2 tax-row mb-2 align-items-center">
            <div class="col-md-4"><input type="text" name="taxes[${taxIndex}][name]" placeholder="Tax Name" class="form-control tax-name" style="font-size: 13px;"></div>
            <div class="col-md-3"><div class="input-group"><input type="number" name="taxes[${taxIndex}][percentage]" placeholder="Tax %" class="form-control tax-percentage" style="font-size: 13px;"><span class="input-group-text bg-white">%</span></div></div>
            <div class="col-md-3"><div class="input-group"><span class="input-group-text bg-light">₹</span><input type="text" class="form-control tax-amount bg-light" placeholder="Tax Amount" readonly style="font-size: 13px;"></div></div>
            <div class="col-md-2"><div class="tax-action-group"><button type="button" class="tax-btn addTax"><i class="bi bi-plus-lg"></i></button><button type="button" class="tax-btn delete-tax-btn removeTax"><i class="bi bi-trash"></i></button></div></div>
        </div>`;
        $('#taxContainer').append(html);
        taxIndex++;
    });

    $(document).on('click', '.removeTax', function(e) {
        e.preventDefault();
        if($('.tax-row').length <= 1) { alert('At least one tax row required'); return; }
        $(this).closest('.tax-row').remove();
        calculateNetTotal();
    });

    // ================= COUNTRY -> STATE -> CITY =================
    $('#targetCountry').on('change', function() {
        let country_id = $(this).val();
        if(!country_id) {
            $('#targetState').html('<option value="">Select State</option>');
            $('#targetState').trigger('change');
            return;
        }
        $('#targetState').html('<option>Loading...</option>');
        $.get("{{ url('seller/products/findstates') }}/" + country_id, function(res) {
            let html = '<option value="">Select State</option>';
            res.forEach(item => html += `<option value="${item.id}">${item.name}</option>`);
            $('#targetState').html(html);
            let oldState = '{{ old("target_state", $product->target_state ?? "") }}';
            if(oldState) $('#targetState').val(oldState);
            $('#targetState').trigger('change');
        });
    });

    $('#targetState').on('change', function() {
        let state_id = $(this).val();
        if(!state_id) {
            $('#cityOptionsList').html('<p class="text-muted p-2">Select a state first</p>');
            updateSelectedCitiesText();
            updateModalCitiesList([]);
            return;
        }
        $('#cityOptionsList').html('<p class="text-muted p-2">Loading cities...</p>');
        $.get("{{ url('seller/products/findcities') }}/" + state_id, function(res) {
            let html = '';
            let selectedCities = {!! json_encode(is_array(old('target_city', json_decode($product->target_city ?? '[]', true))) ? old('target_city', json_decode($product->target_city ?? '[]', true)) : []) !!};
            
            if (res.length === 0) {
                html = '<p class="text-muted p-2">No cities found for this state</p>';
            } else {
                res.forEach(city => {
                    let checked = selectedCities.includes(city.id) || selectedCities.includes(city.id.toString()) ? 'checked' : '';
                    html += `<label class="city-option">
                        <input type="checkbox" name="target_city[]" value="${city.id}" class="city-checkbox" ${checked}>
                        <span>${city.name}</span>
                    </label>`;
                });
            }
            $('#cityOptionsList').html(html);
            updateSelectedCitiesText();
            updateModalCitiesList(res);
        });
    });

    // Also update modal city checklist with state-level cities
    function updateModalCitiesList(citiesJson) {
        let html = '';
        let currentCities = $('#aiCities').text().split(', ');
        if (citiesJson.length === 0) {
            html = '<p class="text-muted p-2">No cities available</p>';
        } else {
            citiesJson.forEach(city => {
                let checked = currentCities.includes(city.name) ? 'checked' : '';
                html += `<label class="city-option">
                    <input type="checkbox" value="${city.name}" class="modal-city-checkbox" ${checked}>
                    <span>${city.name}</span>
                </label>`;
            });
        }
        $('#modalCityOptionsList').html(html);
        updateModalSelectedCitiesText();
    }

    if($('#targetCountry').val()) $('#targetCountry').trigger('change');

    // ================= CITY MULTI-SELECT DROPDOWN (Main Form) =================
    $('#cityDropdownHeader').on('click', function(e) {
        e.stopPropagation();
        $('.city-multiselect-container').toggleClass('show');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.city-multiselect-container').length) {
            $('.city-multiselect-container').removeClass('show');
        }
    });

    function updateSelectedCitiesText() {
        let selected = [];
        $('.city-checkbox:checked').each(function() {
            selected.push($(this).closest('.city-option').find('span').text());
        });
        if(selected.length === 0) $('#selectedCitiesText').text('Select Cities');
        else if(selected.length <= 2) $('#selectedCitiesText').text(selected.join(', '));
        else $('#selectedCitiesText').text(selected.length + ' cities selected');
    }

    $('#citySearchInput').on('keyup', function() {
        let searchTerm = $(this).val().toLowerCase();
        $('.city-option').each(function() {
            let cityName = $(this).find('span').text().toLowerCase();
            $(this).toggle(cityName.indexOf(searchTerm) > -1);
        });
    });

    $(document).on('change', '.city-checkbox', function() { updateSelectedCitiesText(); });
    updateSelectedCitiesText();

    // ================= INITIAL CALCULATIONS =================
    calculateNetTotal();
});
</script>

@endsection
