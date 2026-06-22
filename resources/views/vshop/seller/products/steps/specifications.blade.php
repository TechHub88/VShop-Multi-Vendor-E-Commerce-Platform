@extends('vshop.seller.main')

@section('content')

<style>

.btn-primary {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%) !important;
    border: 0;
}

.required::after{
    content:" *";
    color:red;
    font-weight:700;
}

.validation-error{
    color:red;
    font-size:13px;
}

/* =========================
   MAIN LAYOUT
========================= */

.specification-layout{
    display:flex;
    align-items:flex-start;
    gap:22px;
    width:100%;
}

/* LEFT SIDE */
.specification-left{
    flex:1;
    min-width:0;
}

/* RIGHT SIDE */
.specification-right{
    width:260px;
    min-width:260px;
    position:sticky;
    top:20px;
}

/* CARD */
.specification-left .card{
    border-radius:12px;
    overflow:hidden;
    border:1px solid #e9ecef;
}

/* FORM CONTROL */
.form-control{
    height:46px;
    border-radius:8px;
}

textarea.form-control{
    min-height:120px;
    height:auto;
}

/* CKEDITOR FIX */
.ck-editor__editable{
    min-height:180px !important;
}

/* ALERT BOX */
.alert{
    border-radius:10px;
}

/* BUTTON */
.btn-primary{
    height:46px;
    border-radius:10px;
    padding-left:30px;
    padding-right:30px;
    font-weight:600;
}

/* Stock Movement Table Styles */
.stock-movement-section {
    margin-top: 40px;
    border-top: 2px solid #e9ecef;
    padding-top: 30px;
}

.stock-movement-section h5 {
    font-weight: 600;
    color: #0d6efd;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.stock-movement-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.stock-movement-table th {
    background-color: #f8f9fa;
    padding: 12px 15px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: #495057;
    border-bottom: 1px solid #e9ecef;
}

.stock-movement-table td {
    padding: 12px 15px;
    font-size: 13px;
    color: #212529;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

.stock-movement-table tr:last-child td {
    border-bottom: none;
}

.stock-movement-table .badge-in {
    background-color: #d1e7dd;
    color: #0f5132;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.stock-movement-table .badge-out {
    background-color: #f8d7da;
    color: #842029;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.stock-movement-table .badge-adjust {
    background-color: #fff3cd;
    color: #997404;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.empty-stock-movement {
    text-align: center;
    padding: 40px 20px;
    background: #f8f9fa;
    border-radius: 12px;
    color: #6c757d;
}

.empty-stock-movement i {
    font-size: 40px;
    margin-bottom: 15px;
    opacity: 0.5;
}

/* =========================
   RESPONSIVE
========================= */

@media(max-width:991px){

    .specification-layout{
        flex-direction:column;
    }

    .specification-right{
        width:100%;
        min-width:100%;
        position:relative;
        top:0;
    }

}

/* MOBILE */
@media(max-width:767px){

    .container-fluid{
        padding-left:10px !important;
        padding-right:10px !important;
    }

    .card{
        padding:15px !important;
    }

    .col-md-6,
    .col-md-12{
        width:100%;
    }

    .stock-movement-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
}
.top-back-heading-sec h5 {
    color: #0f172a !important;
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
.nav-btn {
    border: 1px solid #004392;
    color: #004392;
    background: transparent;
    border-radius: 5px;
    width: 35px;
    height: 38px;
}
.top-back-heading-sec {
    align-items: center;
    gap: 10px;
}
.btn-save {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%) !important;
}

</style>

<div class="container-fluid px-2 py-3">

<div class="marketplace-wrapper">

{{-- LEFT --}}
<div class="marketplace-left">

@if(session('success'))
<div class="alert alert-success successMessage">
    {{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('seller.products.specifications.update',$product->id) }}" enctype="multipart/form-data" id="productForm">
@csrf
<div class="card shadow-sm p-4">

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">

    @php
    $backUrl = (isset($product) && !empty($product->id))
    ? route('seller.products.media.edit', $product->id)
    : route('seller.products.media');
    @endphp

    <div class="d-flex align-items-center top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <h5 class="mb-0 d-flex align-items-center gap-2"
            style="
                color:#0d6efd;
                font-weight:600;
            ">
            Details
            <span class="badge bg-info text-uppercase">
                {{ $product->type ?? 'product' }}
            </span>
        </h5>
    </div>

    <!-- LEAD SETTINGS BUTTON -->
    <button type="button" id="leadSettingsBtn" class="btn btn-outline-primary d-flex align-items-center gap-2"
            style="
                border: 1.5px solid #084f9d;
                color: #084f9d;
                background: transparent;
                border-radius: 8px;
                height: 38px;
                padding: 0 16px;
                font-weight: 600;
                font-size: 13px;
                transition: all 0.2s ease-in-out;
            ">
        <i class="fa-solid fa-gears"></i> Lead Settings
    </button>

</div>

@if(isset($product))
@include('vshop.seller.partials.draft-status', ['product' => $product])
@endif

{{-- ================= PRODUCT / PROFESSIONAL ================= --}}

@if(in_array($product->type, ['product', 'vshop-product']))

<div class="alert alert-warning mb-4">
Product Based Details
</div>

<div class="row">

{{-- SKU --}}
<div class="col-md-12 mb-3">
<label class="form-label required">SKU</label>
<input type="text" name="sku"
class="form-control @error('sku') is-invalid @enderror"
value="{{ old('sku',$product->sku) }}">
@error('sku') <div class="validation-error">{{ $message }}</div> @enderror
</div>

{{-- ITEM CODE --}}
<div class="col-md-6 mb-3">
<label class="form-label required">Item Code</label>
<input type="text" name="item_code"
class="form-control @error('item_code') is-invalid @enderror"
value="{{ old('item_code',$product->item_code ?? '') }}">
@error('item_code') <div class="validation-error">{{ $message }}</div> @enderror
</div>

{{-- COLOR --}}
<div class="col-md-6 mb-3">
<label class="form-label">Color</label>
<input type="text" name="color" class="form-control"
value="{{ old('color',$product->color ?? '') }}">
</div>

{{-- VARIANT --}}
<div class="col-md-6 mb-3">
<label class="form-label">Variant</label>
<input type="text" name="variant" class="form-control"
value="{{ old('variant',$product->variant ?? '') }}">
</div>

{{-- SIZE --}}
<div class="col-md-6 mb-3">
<label class="form-label">Size</label>
<input type="text" name="size" class="form-control"
value="{{ old('size',$product->size ?? '') }}">
</div>

{{-- QUANTITY --}}
<div class="col-md-6 mb-3">
<label class="form-label required">Quantity</label>
<input type="text" name="quantity"
class="form-control @error('quantity') is-invalid @enderror"
value="{{ old('quantity',$product->quantity ?? '') }}">
@error('quantity') <div class="validation-error">{{ $message }}</div> @enderror
</div>

{{-- BATCH --}}
<div class="col-md-6 mb-3">
<label class="form-label required">Batch Number</label>
<input type="text" name="batch_number"
class="form-control @error('batch_number') is-invalid @enderror"
value="{{ old('batch_number',$product->batch_number ?? '') }}">
@error('batch_number') <div class="validation-error">{{ $message }}</div> @enderror
</div>

{{-- STOCK --}}
<div class="col-md-6 mb-3">
<label class="form-label required">Stock Quantity</label>
<input type="number" name="stock_qty" id="stock_qty"
class="form-control @error('stock_qty') is-invalid @enderror"
value="{{ old('stock_qty',$product->inventory ?? '') }}">
@error('stock_qty') <div class="validation-error">{{ $message }}</div> @enderror
</div>

{{-- DELIVERY --}}
<div class="col-md-6 mb-3">
    <label class="form-label required">Delivery Time (Days)</label>

    <input type="text"
        name="delivery_time"
        class="form-control @error('delivery_time') is-invalid @enderror"
        placeholder="e.g. 3-5 days"
        value="{{ old('delivery_time',$product->delivery_time ?? '') }}">

    @error('delivery_time')
    <div class="validation-error">{{ $message }}</div>
    @enderror
</div>

{{-- DELIVERY TIME (CLOCK) --}}
<div class="col-md-6 mb-3">
    <label class="form-label required">Delivery Time (Hour)</label>

    <input type="time"
        name="delivery_hour"
        class="form-control @error('delivery_hour') is-invalid @enderror"
        value="{{ old('delivery_hour',$product->delivery_hour ?? '') }}">

    @error('delivery_hour')
    <div class="validation-error">{{ $message }}</div>
    @enderror
</div>


<div class="col-md-12 mb-3">
    <label class="form-label mb-1">Low Stock Alert</label>
    
    <div class="position-relative">
        <input type="number"
               name="low_stock_threshold"
               id="low_stock_threshold"
               class="form-control"
               value="{{ old('low_stock_threshold',$product->low_stock_threshold) }}">
        
        <!-- Alert icon + text -->
        <div id="rightStockAlert" class="stock-alert position-absolute d-flex align-items-center gap-1 small fw-bold">
            <i id="rightStockIcon" class="fa-solid fa-triangle-exclamation"></i>
            <span id="rightStockText">Checking...</span>
        </div>
    </div>
</div>



{{-- AUTO DISABLE --}}
<div class="col-md-12 mb-3">
<div class="form-check">
<input type="checkbox" name="auto_disable" value="1"
{{ old('auto_disable',$product->auto_disable) ? 'checked' : '' }}>
<label>Auto Disable when stock = 0</label>
</div>
</div>

{{-- SUPPLIER --}}
<div class="col-md-12 mb-4">
<label class="form-label">Supplier Performance</label>
<select name="supplier_performance_tag" class="form-control">
<option value="">Select</option>
<option value="excellent" {{ $product->supplier_performance_tag=='excellent'?'selected':'' }}>Excellent</option>
<option value="good" {{ $product->supplier_performance_tag=='good'?'selected':'' }}>Good</option>
<option value="average" {{ $product->supplier_performance_tag=='average'?'selected':'' }}>Average</option>
<option value="poor" {{ $product->supplier_performance_tag=='poor'?'selected':'' }}>Poor</option>
</select>
</div>

{{-- SPECIFICATIONS --}}
<div class="col-md-12 mb-4">
<label class="form-label">Specifications</label>
<textarea name="product_specifications" class="form-control editor">{!! old('product_specifications',$product->product_specifications) !!}</textarea>
</div>

</div>
@endif


{{-- ================= PROFESSIONAL ================= --}}

@if(in_array($product->type, ['professional', 'vshop-professional', 'vtravel-professional', 'service', 'vshop-service', 'vtravel-service', 'information_advisory', 'vtravel-information_advisory']))

<div class="alert alert-warning mb-4">
Professional Service Details
</div>

<div class="row">

{{-- SERVICE DURATION --}}
<div class="col-md-6 mb-3">
<label class="form-label required">Service Duration</label>
<input type="number" name="service_duration"
class="form-control @error('service_duration') is-invalid @enderror"
value="{{ old('service_duration',$product->service_duration ?? '') }}">
@error('service_duration')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

{{-- SESSION TYPE --}}
<div class="col-md-6 mb-3">
<label class="form-label required">Session Type</label>
<select name="session_type"
class="form-control @error('session_type') is-invalid @enderror">
<option value="">Select</option>
<option value="online" {{ old('session_type',$product->session_type ?? '')=='online'?'selected':'' }}>Online</option>
<option value="offline" {{ old('session_type',$product->session_type ?? '')=='offline'?'selected':'' }}>Offline</option>
<option value="hybrid" {{ old('session_type',$product->session_type ?? '')=='hybrid'?'selected':'' }}>Hybrid</option>
</select>
@error('session_type')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

<div class="col-md-6 mb-3">
<label class="form-label required">Start Time</label>
<input type="time" name="start_time"
class="form-control @error('start_time') is-invalid @enderror"
value="{{ old('start_time',$product->start_time ?? '') }}">
@error('start_time')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

<div class="col-md-6 mb-3">
<label class="form-label required">End Time</label>
<input type="time" name="end_time"
class="form-control @error('end_time') is-invalid @enderror"
value="{{ old('end_time',$product->end_time ?? '') }}">
@error('end_time')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

{{-- PROVIDER --}}
<div class="col-md-6 mb-3">
<label class="form-label required">Instructor / Provider Name</label>
<input type="text" name="provider_name"
class="form-control @error('provider_name') is-invalid @enderror"
value="{{ old('provider_name',$product->provider_name ?? '') }}">
@error('provider_name')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

{{-- MAX PARTICIPANTS --}}
<div class="col-md-6 mb-3">
<label class="form-label required">Max Participants</label>
<input type="number" name="max_participants"
class="form-control @error('max_participants') is-invalid @enderror"
value="{{ old('max_participants',$product->max_participants ?? '') }}">
@error('max_participants')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

{{-- BOOKING REQUIRED --}}
<div class="col-md-6 mb-3">
<label class="form-label required">Booking Required</label>
<select name="booking_required"
class="form-control @error('booking_required') is-invalid @enderror">
<option value="">Select</option>
<option value="yes" {{ (old('booking_required', $product->booking_required ?? '') == 'yes' || old('booking_required', $product->booking_required ?? '') == 1) ? 'selected' : '' }}>Yes</option>
<option value="no" {{ (old('booking_required', $product->booking_required ?? '') == 'no' || old('booking_required', $product->booking_required ?? '') == 0) ? 'selected' : '' }}>No</option>
</select>
@error('booking_required')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

{{-- VALIDITY --}}
<div class="col-md-6 mb-3">
<label class="form-label required">Service Validity</label>

<input type="date"
name="service_validity"
class="form-control @error('service_validity') is-invalid @enderror"
value="{{ old('service_validity', isset($product->service_validity) ? \Carbon\Carbon::parse($product->service_validity)->format('Y-m-d') : '') }}">

@error('service_validity')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>
{{-- LOCATION --}}
<div class="col-md-6 mb-3">
<label class="form-label required">Location / Meeting Link</label>
<input type="text" name="meeting_link"
class="form-control @error('meeting_link') is-invalid @enderror"
value="{{ old('meeting_link',$product->meeting_link ?? '') }}">
@error('meeting_link')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

{{-- MATERIAL --}}
<div class="col-md-12 mb-3">
<label class="form-label">Materials Included</label>
<textarea name="materials_included"
class="form-control @error('materials_included') is-invalid @enderror">{{ old('materials_included',$product->materials_included ?? '') }}</textarea>
@error('materials_included')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

</div>

@endif


{{-- ===================================================== --}}
{{-- SERVICE / TOUR PACKAGE --}}
{{-- ===================================================== --}}
@if(in_array($product->type, ['service', 'vshop-service', 'vtravel-service', 'tour', 'vtravel-tour']))

@php
    $labels = [
        'itinerary' => 'Itinerary',
        'accommodation' => 'Accommodation',
        'included_excluded' => 'Inclusion / Exclusion',
        'refund_policy' => 'Refund Policy',
        'luggage' => 'Luggage',
        'free_days' => 'Free Days',
        'benefits' => 'Benefits',
        'research' => 'Research'
    ];
@endphp

<div class="alert alert-info mb-4">

    @if(in_array($product->type, ['tour', 'vtravel-tour']))
        Tour Package Details
    @elseif(in_array($product->type, ['information_advisory', 'vtravel-information_advisory']))
        Information & Advisory Details 
        <br><strong class="mt-2 d-inline-block">Selected Type:</strong> <span class="badge bg-primary fs-6">{{ $product->listing_type }}</span>
    @else
        Service Based Details
    @endif

</div>

<div class="mb-4 @error('itinerary') border border-danger rounded p-2 @enderror">

<label class="form-label required">{{ $labels['itinerary'] }}</label>

<textarea name="itinerary"
class="form-control editor">{{ old('itinerary',$product->itinerary) }}</textarea>

@error('itinerary')
<div class="validation-error">{{ $message }}</div>
@enderror

</div>

{{-- ACCOMMODATION --}}
<div class="mb-4">
<label class="form-label">{{ $labels['accommodation'] }}</label>

<textarea name="accommodation"
class="form-control editor">{!! old('accommodation',$product->accommodation) !!}</textarea>
</div>

{{-- INCLUDED EXCLUDED --}}
<div class="mb-4">
<label class="form-label">{{ $labels['included_excluded'] }}</label>

<textarea name="included_excluded"
class="form-control editor">{!! old('included_excluded',$product->included_excluded) !!}</textarea>
</div>

{{-- LUGGAGE --}}
<div class="mb-4">
<label class="form-label">{{ $labels['luggage'] }}</label>

<input type="text"
name="luggage"
class="form-control"
value="{{ old('luggage',$product->luggage) }}">
</div>

<div class="mb-4 @error('refund_policy') border border-danger rounded p-2 @enderror">

<label class="form-label required">{{ $labels['refund_policy'] }}</label>

<textarea name="refund_policy"
class="form-control editor">{{ old('refund_policy',$product->refund_policy) }}</textarea>

@error('refund_policy')
<div class="validation-error">{{ $message }}</div>
@enderror

</div>

{{-- FREE DAYS --}}
<div class="mb-4">
<label class="form-label">{{ $labels['free_days'] }}</label>

<textarea name="free_days"
class="form-control editor">{!! old('free_days',$product->free_days) !!}</textarea>
</div>

{{-- BENEFITS --}}
<div class="mb-4">
<label class="form-label">{{ $labels['benefits'] }}</label>

<textarea name="benefits"
class="form-control editor">{!! old('benefits',$product->benefits) !!}</textarea>
</div>

{{-- RESEARCH --}}
<div class="mb-4">
<label class="form-label">{{ $labels['research'] }}</label>

<textarea name="research"
class="form-control editor">{!! old('research',$product->research) !!}</textarea>
</div>

@elseif(in_array($product->type, ['information_advisory', 'vtravel-information_advisory']))

@php
    $specs = json_decode($product->product_specifications, true) ?? [];
@endphp

<div class="alert alert-info mb-4">
    Information & Advisory Details
    <br><strong class="mt-2 d-inline-block">Selected Type:</strong> <span class="badge bg-primary fs-6">{{ $product->listing_type }}</span>
</div>

<div class="row">
    @if ($product->listing_type == 'travel service / travel guide / journey assistence')
        {{-- Service Type --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Service Type</label>
            <input type="text" name="travel_service_type" class="form-control" value="{{ old('travel_service_type', $product->travel_service_type ?? '') }}" placeholder="e.g. Flight Assistance, Local Guide">
        </div>
        
        {{-- Documents --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Required Documents Description</label>
            <input type="text" name="travel_documents" class="form-control" value="{{ old('travel_documents', $product->travel_documents ?? '') }}" placeholder="e.g. Passport, Visa, ID Card">
        </div>
        
        {{-- Pricing --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Pricing details</label>
            <input type="text" name="travel_pricing" class="form-control" value="{{ old('travel_pricing', $product->travel_pricing ?? '') }}" placeholder="e.g. $50 per day, Custom rates">
        </div>
        
        {{-- Policy / Trip Types --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Policy / Trip Types</label>
            <input type="text" name="travel_policy_trip_types" class="form-control" value="{{ old('travel_policy_trip_types', $product->travel_policy_trip_types ?? '') }}" placeholder="e.g. Cancel 24h before, Adventure/Leisure">
        </div>
        
        {{-- License --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">License Information</label>
            <input type="text" name="travel_license" class="form-control" value="{{ old('travel_license', $product->travel_license ?? '') }}" placeholder="e.g. Gov Certified, Lic #12345">
        </div>
        
        {{-- Rate / Assistance Type --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Rate / Assistance Type</label>
            <input type="text" name="travel_rate_assistance_type" class="form-control" value="{{ old('travel_rate_assistance_type', $product->travel_rate_assistance_type ?? '') }}" placeholder="e.g. Hourly, Daily, On-call">
        </div>
        
        {{-- Areas --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Operational Areas</label>
            <input type="text" name="travel_areas" class="form-control" value="{{ old('travel_areas', $product->travel_areas ?? '') }}" placeholder="e.g. Northern Region, Metro Area">
        </div>
        
        {{-- Emergency --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Emergency Contacts / Protocol</label>
            <input type="text" name="travel_emergency" class="form-control" value="{{ old('travel_emergency', $product->travel_emergency ?? '') }}" placeholder="e.g. 24/7 Helpline +123456">
        </div>

    @elseif ($product->listing_type == 'spiritual information / Cultural tour adviory / historical places / local attract')
        {{-- Dress Code --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Dress Code Guidelines</label>
            <input type="text" name="spiritual_dress_code" class="form-control" value="{{ old('spiritual_dress_code', $product->spiritual_dress_code ?? '') }}" placeholder="e.g. Conservative dress required">
        </div>
        
        {{-- Festivals --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Key Festivals & Times</label>
            <input type="text" name="spiritual_festivals" class="form-control" value="{{ old('spiritual_festivals', $product->spiritual_festivals ?? '') }}" placeholder="e.g. Holi, Diwali, Annual Festival">
        </div>
        
        {{-- Prasad / Advisory Type --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Prasad / Advisory Details</label>
            <input type="text" name="spiritual_prasad_advisory_type" class="form-control" value="{{ old('spiritual_prasad_advisory_type', $product->spiritual_prasad_advisory_type ?? '') }}" placeholder="e.g. Prasad distribution timings">
        </div>
        
        {{-- Contact --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Contact / Inquiry Phone</label>
            <input type="text" name="spiritual_contact" class="form-control" value="{{ old('spiritual_contact', $product->spiritual_contact ?? '') }}" placeholder="e.g. Temple Office +123456">
        </div>
        
        {{-- Guidelines / Era --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Historical Era / Guidelines</label>
            <input type="text" name="spiritual_guidelines_era" class="form-control" value="{{ old('spiritual_guidelines_era', $product->spiritual_guidelines_era ?? '') }}" placeholder="e.g. 12th Century Chalukya era">
        </div>
        
        {{-- UNESCO --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">UNESCO Heritage Status</label>
            <input type="text" name="spiritual_unesco" class="form-control" value="{{ old('spiritual_unesco', $product->spiritual_unesco ?? '') }}" placeholder="e.g. Certified Heritage Site, None">
        </div>
        
        {{-- Guide / Type --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Guide availability & Type</label>
            <input type="text" name="spiritual_guide_type" class="form-control" value="{{ old('spiritual_guide_type', $product->spiritual_guide_type ?? '') }}" placeholder="e.g. Audio guide, Local guide">
        </div>
        
        {{-- Duration --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Recommended Visit Duration</label>
            <input type="text" name="spiritual_duration" class="form-control" value="{{ old('spiritual_duration', $product->spiritual_duration ?? '') }}" placeholder="e.g. 2-3 hours">
        </div>
        
        {{-- Facilities --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Facilities Available</label>
            <input type="text" name="spiritual_facilities" class="form-control" value="{{ old('spiritual_facilities', $product->spiritual_facilities ?? '') }}" placeholder="e.g. Parking, Lockers, Washrooms">
        </div>
        
        {{-- Family Friendly --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Family Friendly Status</label>
            <input type="text" name="spiritual_family_friendly" class="form-control" value="{{ old('spiritual_family_friendly', $product->spiritual_family_friendly ?? '') }}" placeholder="e.g. Suitable for all age groups">
        </div>

    @elseif ($product->listing_type == 'Activity, experience, food and culture')
        {{-- Type --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Activity / Culinary Type</label>
            <input type="text" name="activity_type" class="form-control" value="{{ old('activity_type', $product->activity_type ?? '') }}" placeholder="e.g. Cooking Class, Trekking">
        </div>
        
        {{-- Difficulty --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Difficulty Level</label>
            <input type="text" name="activity_difficulty" class="form-control" value="{{ old('activity_difficulty', $product->activity_difficulty ?? '') }}" placeholder="e.g. Easy, Moderate, High">
        </div>
        
        {{-- Equipment --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Required Equipment / Clothes</label>
            <input type="text" name="activity_equipment" class="form-control" value="{{ old('activity_equipment', $product->activity_equipment ?? '') }}" placeholder="e.g. Sports shoes, Apron">
        </div>
        
        {{-- Safety --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Safety Instructions & Guidelines</label>
            <input type="text" name="activity_safety" class="form-control" value="{{ old('activity_safety', $product->activity_safety ?? '') }}" placeholder="e.g. Life jackets provided, Age 12+">
        </div>
        
        {{-- Guide --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Guide Details</label>
            <input type="text" name="activity_guide" class="form-control" value="{{ old('activity_guide', $product->activity_guide ?? '') }}" placeholder="e.g. Certified Local Guide, Self-guided">
        </div>
        
        {{-- Group Size / Food Name --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Max Group Size / Special Food Name</label>
            <input type="text" name="activity_group_food_name" class="form-control" value="{{ old('activity_group_food_name', $product->activity_group_food_name ?? '') }}" placeholder="e.g. Max 10 people, Specialty Dumplings">
        </div>
        
        {{-- Restaurant --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Restaurant / Venue Details</label>
            <input type="text" name="activity_restaurant" class="form-control" value="{{ old('activity_restaurant', $product->activity_restaurant ?? '') }}" placeholder="e.g. Golden Dragon Palace, Rooftop CafÃ©">
        </div>
        
        {{-- Food Type --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Food Category / Cuisine Type</label>
            <input type="text" name="activity_food_type" class="form-control" value="{{ old('activity_food_type', $product->activity_food_type ?? '') }}" placeholder="e.g. Vegetarian, Asian Fusion">
        </div>
        
        {{-- Must-Try --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Must-Try Items / Highlights</label>
            <input type="text" name="activity_must_try" class="form-control" value="{{ old('activity_must_try', $product->activity_must_try ?? '') }}" placeholder="e.g. Szechuan Noodle, Sunrise hike">
        </div>
        
        {{-- Diet Options --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Dietary Options Available</label>
            <input type="text" name="activity_diet_options" class="form-control" value="{{ old('activity_diet_options', $product->activity_diet_options ?? '') }}" placeholder="e.g. Vegan, Gluten-free, Nut-free">
        </div>
        
        {{-- Takeaway --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Takeaway / Delivery Available</label>
            <input type="text" name="activity_takeaway" class="form-control" value="{{ old('activity_takeaway', $product->activity_takeaway ?? '') }}" placeholder="e.g. Takeaway yes, Delivery no">
        </div>

    @elseif ($product->listing_type == 'local vendor / shop')
        {{-- Category --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Store Category</label>
            <input type="text" name="vendor_category" class="form-control" value="{{ old('vendor_category', $product->vendor_category ?? '') }}" placeholder="e.g. Souvenirs, Clothing, Grocery">
        </div>
        
        {{-- Famous For --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Famous For / Specialty</label>
            <input type="text" name="vendor_famous_for" class="form-control" value="{{ old('vendor_famous_for', $product->vendor_famous_for ?? '') }}" placeholder="e.g. Handmade pottery, Silk scarves">
        </div>
        
        {{-- Operational Areas --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Operational Areas / Locations</label>
            <input type="text" name="vendor_operational_areas" class="form-control" value="{{ old('vendor_operational_areas', $product->vendor_operational_areas ?? '') }}" placeholder="e.g. Downtown Main Street, Online delivery">
        </div>
        
        {{-- Payment Methods --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Payment Methods Accepted</label>
            <input type="text" name="vendor_payment_methods" class="form-control" value="{{ old('vendor_payment_methods', $product->vendor_payment_methods ?? '') }}" placeholder="e.g. Cash, Credit Card, Apple Pay">
        </div>
        
        {{-- Delivery --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Delivery / Shipping Terms</label>
            <input type="text" name="vendor_delivery" class="form-control" value="{{ old('vendor_delivery', $product->vendor_delivery ?? '') }}" placeholder="e.g. Free local delivery over $30">
        </div>
        
        {{-- Customization --}}
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Customization / Order Options</label>
            <input type="text" name="vendor_customization" class="form-control" value="{{ old('vendor_customization', $product->vendor_customization ?? '') }}" placeholder="e.g. Custom engraving available">
        </div>
    @endif
</div>



@endif


{{-- ================= STOCK MOVEMENT HISTORY LOG ================= --}}
@if(in_array($product->type, ['product', 'vshop-product']))
<div class="stock-movement-section">
    <h5>
        <i class="fa-solid fa-clock-rotate-left"></i>
        Stock Movement History Log
    </h5>

    <div class="table-responsive">
        <table class="stock-movement-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date & Time</th>
                    <th>Type</th>
                    <th>Quantity Changed</th>
                    <th>Stock Before</th>
                    <th>Stock After</th>
                    <th>Reference / Order #</th>
                    <th>Reason / Notes</th>
                    <th>Staff / User</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $movements = $product->stockMovements ?? collect();
                @endphp
                
                @forelse($movements as $index => $movement)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $movement->created_at ? \Carbon\Carbon::parse($movement->created_at)->format('d M Y, h:i A') : '-' }}</td>
                    <td>
                        @if($movement->type == 'in')
                            <span class="badge-in"><i class="fa-solid fa-arrow-down"></i> Stock In</span>
                        @elseif($movement->type == 'out')
                            <span class="badge-out"><i class="fa-solid fa-arrow-up"></i> Stock Out</span>
                        @elseif($movement->type == 'adjustment')
                            <span class="badge-adjust"><i class="fa-solid fa-pen"></i> Adjustment</span>
                        @else
                            <span class="badge-adjust">{{ ucfirst($movement->type) }}</span>
                        @endif
                    </td>
                    <td class="{{ $movement->quantity_changed < 0 ? 'text-danger' : 'text-success' }}">
                        {{ $movement->quantity_changed > 0 ? '+' : '' }}{{ $movement->quantity_changed ?? 0 }}
                    </td>
                    <td>{{ $movement->stock_before ?? '-' }}</td>
                    <td>{{ $movement->stock_after ?? '-' }}</td>
                    <td>{{ $movement->reference ?? $movement->order_number ?? '-' }}</td>
                    <td>{{ $movement->reason ?? $movement->notes ?? '-' }}</td>
                    <td>{{ $movement->user_name ?? $movement->staff_name ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px;">
                        <i class="fa-solid fa-boxes-stacked" style="font-size: 40px; opacity: 0.5; display: block; margin-bottom: 10px;"></i>
                        No stock movement history found
                        <br>
                        <small class="text-muted">When stock is added or sold, movements will appear here</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="text-center mt-4">
<button type="submit" class="btn btn-primary px-5">
Save & Continue
</button>
</div>

</div>
</form>

</div>

<div class="marketplace-right">

@include('vshop.seller.partials.product-progress',[
'product'=>$product
])

</div>

</div>

</div>

{{-- CKEDITOR --}}
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
    let editors = [];

document.querySelectorAll('.editor').forEach((el) => {

    ClassicEditor.create(el)
    .then(editor => {

        editors.push(editor);

        editor.model.document.on('change:data', () => {

            let wrapper = $(el).closest(
                '.mb-3, .mb-4, .col-md-6, .col-md-12'
            );

            // REMOVE ERROR UI
            wrapper.find('.validation-error').remove();
            $(el).removeClass('is-invalid');

        });

    })
    .catch(err => console.error(err));

});
</script>

<script>
function inventoryCheck() {
    let stock = parseInt($('#stock_qty').val() || 0);
    let threshold = parseInt($('#low_stock_threshold').val() || 0);

    let rightAlert = $('#rightStockAlert');   // Wrapper
    let rightText = $('#rightStockText');     // Text
    let rightIcon = $('#rightStockIcon');     // Icon <i>

    rightAlert.removeClass('text-success text-warning text-danger');

    if(stock <= 0){
        rightAlert.addClass('text-danger');
        rightText.html('Out of Stock');
        rightIcon.attr('class','fa-solid fa-triangle-exclamation'); // 🔺 red
    }
    else if(stock <= threshold){
        rightAlert.addClass('text-warning');
        rightText.html('Low Stock');
        rightIcon.attr('class','fa-solid fa-triangle-exclamation'); // 🔺 yellow
    }
    else{
        rightAlert.addClass('text-success');
        rightText.html('In Stock');
        rightIcon.attr('class','fa-solid fa-circle-check'); // ✅ green
    }
}

// Trigger inventory check on input change
$('#stock_qty,#low_stock_threshold').on('input', inventoryCheck);

// Run on page load
$(document).ready(function() {
    inventoryCheck();
    
    // Auto-hide success messages
    setTimeout(()=>$('.successMessage').fadeOut(300),3000);
});
</script>

<style>
    /* Position the alert inside input container */
.stock-alert {
    top: 50%;
    right: 12px;
    transform: translateY(-50%);
    pointer-events: none; /* avoid blocking input */
}

.stock-alert i {
    font-size: 14px;
}

/* Colors */
.text-success { color: #28a745 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }

/* Stock Movement Table Styles - Add to existing <style> section */
.stock-movement-section {
    margin-top: 40px;
    border-top: 2px solid #e9ecef;
    padding-top: 30px;
}

.stock-movement-section h5 {
    font-weight: 600;
    color: #0d6efd;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.stock-movement-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.stock-movement-table th {
    background-color: #f8f9fa;
    padding: 14px 12px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: #495057;
    border-bottom: 2px solid #e9ecef;
}

.stock-movement-table td {
    padding: 12px;
    font-size: 13px;
    color: #212529;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

.stock-movement-table tr:last-child td {
    border-bottom: none;
}

.stock-movement-table tr:hover td {
    background-color: #f8f9fa;
}

.stock-movement-table .badge-in {
    background-color: #d1e7dd;
    color: #0f5132;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.stock-movement-table .badge-out {
    background-color: #f8d7da;
    color: #842029;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.stock-movement-table .badge-adjust {
    background-color: #fff3cd;
    color: #997404;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.text-success {
    color: #28a745 !important;
    font-weight: 600;
}

.text-danger {
    color: #dc3545 !important;
    font-weight: 600;
}

/* Responsive */
@media(max-width: 767px) {
    .stock-movement-table th,
    .stock-movement-table td {
        padding: 8px;
        font-size: 11px;
    }
    
    .stock-movement-table .badge-in,
    .stock-movement-table .badge-out,
    .stock-movement-table .badge-adjust {
        padding: 3px 8px;
        font-size: 10px;
    }
}
</style>

@php
    $visibleContacts = $product->contact_visibility ?? ['Phone', 'WhatsApp', 'Email', 'Website'];
    $enableEnquiry = isset($product->enable_enquiry) ? (bool)$product->enable_enquiry : true;
@endphp

<!-- LEAD SETTINGS BACKDROP MODAL -->
<div class="lead-settings-backdrop" id="leadSettingsModal" style="display: none;">
    <div class="lead-settings-modal-dialog">
        <div class="lead-settings-modal-content">
            <!-- Header -->
            <div class="lead-settings-header">
                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2" style="font-family: 'Outfit', sans-serif;">
                    <span class="text-uppercase" style="letter-spacing: 0.5px; color: #0d6efd;">Lead Settings</span>
                    <span class="text-muted fw-normal" style="font-size: 11px;">(Purpose: Buyer interactions become trackable)</span>
                </h6>
                <button type="button" class="lead-settings-close-btn" id="closeLeadSettingsBtn">&times;</button>
            </div>
            
            <!-- Body -->
            <div class="lead-settings-body" style="font-family: 'Outfit', sans-serif;">
                <!-- Enable Enquiry System -->
                <div class="enable-enquiry-wrapper mb-3">
                    <label class="custom-checkbox-container">
                        <input type="checkbox" id="modalEnableEnquiry" {{ $enableEnquiry ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        <span class="label-text fw-bold" style="font-size: 14px; color: #0f172a;">[ Enable Enquiry System ]</span>
                    </label>
                </div>

                <!-- Contact Visibility -->
                <div class="contact-visibility-card mb-4" id="contactVisibilityCard">
                    <div class="contact-visibility-title mb-3 fw-bold" style="font-size: 13px; color: #475569;">
                        Contact Visibility:
                    </div>
                    <div class="contact-visibility-options">
                        <label class="custom-checkbox-container">[ 
                            <input type="checkbox" class="visibility-checkbox" value="Phone" {{ in_array('Phone', $visibleContacts) ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            <span class="label-text fw-semibold">Phone</span> ]
                        </label>
                        <label class="custom-checkbox-container">[ 
                            <input type="checkbox" class="visibility-checkbox" value="WhatsApp" {{ in_array('WhatsApp', $visibleContacts) ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            <span class="label-text fw-semibold">WhatsApp</span> ]
                        </label>
                        <label class="custom-checkbox-container">[ 
                            <input type="checkbox" class="visibility-checkbox" value="Email" {{ in_array('Email', $visibleContacts) ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            <span class="label-text fw-semibold">Email</span> ]
                        </label>
                        <label class="custom-checkbox-container">[ 
                            <input type="checkbox" class="visibility-checkbox" value="Website" {{ in_array('Website', $visibleContacts) ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            <span class="label-text fw-semibold">Website</span> ]
                        </label>
                    </div>
                </div>

                <!-- Interaction Buttons Section -->
                <div class="interaction-buttons-section" id="interactionButtonsSection">
                    <div class="section-title mb-3 fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 0.5px; color: #0f172a;">
                        INTERACTION BUTTONS
                    </div>
                    <div class="interaction-grid">
                        <!-- Button 1: View Contact -->
                        <div class="interaction-preview-btn" id="btnPreviewPhone" data-visibility="Phone">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-address-book text-primary"></i>
                                <span>View Contact</span>
                            </div>
                        </div>
                        <!-- Button 2: WhatsApp -->
                        <div class="interaction-preview-btn" id="btnPreviewWhatsApp" data-visibility="WhatsApp">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-brands fa-whatsapp text-success"></i>
                                <span>WhatsApp</span>
                            </div>
                        </div>
                        <!-- Button 3: Call -->
                        <div class="interaction-preview-btn" id="btnPreviewCall" data-visibility="Phone">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-phone text-primary"></i>
                                <span>Call</span>
                            </div>
                        </div>
                        <!-- Button 4: Request Callback -->
                        <div class="interaction-preview-btn" id="btnPreviewCallback" data-visibility="Phone">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-phone-volume text-info"></i>
                                <span>Request Callback</span>
                            </div>
                        </div>
                        <!-- Button 5: Visit Enquiry -->
                        <div class="interaction-preview-btn" id="btnPreviewWebsite" data-visibility="Website">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-user text-warning"></i>
                                <span>Visit Enquiry</span>
                            </div>
                        </div>
                        <!-- Button 6: Send Enquiry -->
                        <div class="interaction-preview-btn" id="btnPreviewEmail" data-visibility="Email">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-envelope text-danger"></i>
                                <span>Send Enquiry</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer / Action Buttons -->
            <div class="lead-settings-footer" style="font-family: 'Outfit', sans-serif;">
                <button type="button" class="btn-cancel" id="cancelLeadSettingsBtn">Cancel</button>
                <button type="button" class="btn-save" id="saveLeadSettingsBtn">Save Settings</button>
            </div>
        </div>
    </div>
</div>

<!-- PREMIUM TOAST NOTIFICATION -->
<div id="premiumToast" class="premium-toast" style="display: none;">
    <div class="premium-toast-content" style="font-family: 'Outfit', sans-serif;">
        <i id="premiumToastIcon" class="fa-solid fa-circle-check"></i>
        <div class="premium-toast-message">
            <div id="premiumToastTitle" class="toast-title">Success</div>
            <div id="premiumToastDesc" class="toast-desc">Settings saved successfully.</div>
        </div>
    </div>
</div>

<style>
/* Lead Settings Modal Styles */
.lead-settings-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 99999;
    animation: leadSettingsFadeIn 0.3s ease;
}

.lead-settings-modal-dialog {
    width: 100%;
    max-width: 580px;
    margin: 1.75rem;
    animation: leadSettingsSlideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.lead-settings-modal-content {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.lead-settings-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    border-bottom: 1px solid #f1f5f9;
    background-color: #f8fafc;
}

.lead-settings-close-btn {
    background: transparent;
    border: none;
    font-size: 24px;
    color: #64748b;
    cursor: pointer;
    line-height: 1;
    transition: color 0.2s;
}

.lead-settings-close-btn:hover {
    color: #ef4444;
}

.lead-settings-body {
    padding: 24px;
    max-height: 70vh;
    overflow-y: auto;
}

.enable-enquiry-wrapper {
    background-color: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
    padding: 14px 20px;
    transition: all 0.3s ease;
}

.enable-enquiry-wrapper:hover {
    background-color: #f1f5f9;
    border-color: #0d6efd;
}

.custom-checkbox-container {
    display: inline-flex;
    align-items: center;
    position: relative;
    padding-left: 28px;
    cursor: pointer;
    font-size: 14px;
    color: #334155;
    user-select: none;
    margin-right: 5px;
}

.custom-checkbox-container input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.checkmark {
    position: absolute;
    top: 50%;
    left: 8px;
    transform: translateY(-50%);
    height: 16px;
    width: 16px;
    background-color: #fff;
    border: 1.5px solid #cbd5e1;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.custom-checkbox-container:hover input ~ .checkmark {
    border-color: #0d6efd;
}

.custom-checkbox-container input:checked ~ .checkmark {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

.custom-checkbox-container input:checked ~ .checkmark:after {
    display: block;
}

.custom-checkbox-container .checkmark:after {
    left: 5px;
    top: 1px;
    width: 4px;
    height: 8px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.custom-checkbox-container .label-text {
    font-weight: 600;
    margin-left: 4px;
}

.contact-visibility-card {
    background-color: #f0f7ff;
    border: 1px solid #d0e7ff;
    border-radius: 12px;
    padding: 18px 20px;
    transition: all 0.3s ease;
}

.contact-visibility-options {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.interaction-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    transition: all 0.3s ease;
}

.interaction-preview-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 10px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    transition: all 0.2s ease;
    cursor: default;
}

.interaction-preview-btn i {
    font-size: 15px;
}

/* Disabled States */
.lead-settings-disabled-panel {
    opacity: 0.45;
    pointer-events: none;
    user-select: none;
}

.interaction-preview-btn.disabled-btn {
    opacity: 0.35;
    background: #f8fafc;
    border-color: #e2e8f0;
    color: #94a3b8 !important;
}

.interaction-preview-btn.disabled-btn i {
    color: #94a3b8 !important;
}

/* Footer & Buttons */
.lead-settings-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    padding: 16px 24px;
    border-top: 1px solid #f1f5f9;
    background-color: #f8fafc;
}

.btn-cancel {
    height: 40px;
    padding: 0 20px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.btn-save {
    height: 40px;
    padding: 0 20px;
    border: none;
    background: #0d6efd;
    color: #ffffff;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.15);
}

.btn-save:hover {
    background: #0b5ed7;
    box-shadow: 0 4px 6px rgba(13, 110, 253, 0.25);
}

/* Premium Toast */
.premium-toast {
    position: fixed;
    top: 24px;
    right: 24px;
    z-index: 999999;
    background: #ffffff;
    border-left: 4px solid #10b981;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    padding: 16px 20px;
    min-width: 300px;
    animation: toastSlideIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    border: 1px solid #f1f5f9;
}

.premium-toast-content {
    display: flex;
    align-items: center;
    gap: 14px;
}

.premium-toast-content i {
    font-size: 20px;
    color: #10b981;
}

.premium-toast-message {
    display: flex;
    flex-direction: column;
}

.toast-title {
    font-weight: 700;
    font-size: 14px;
    color: #0f172a;
}

.toast-desc {
    font-size: 12px;
    color: #64748b;
    margin-top: 1px;
}

.premium-toast.toast-error {
    border-left-color: #ef4444;
}

.premium-toast.toast-error i {
    color: #ef4444;
}

@keyframes leadSettingsFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes leadSettingsSlideUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes toastSlideIn {
    from {
        transform: translateX(100%) translateY(-10px);
        opacity: 0;
    }
    to {
        transform: translateX(0) translateY(0);
        opacity: 1;
    }
}

@keyframes toastSlideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

@media(max-width: 576px) {
    .interaction-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('leadSettingsModal');
    const openBtn = document.getElementById('leadSettingsBtn');
    const closeBtn = document.getElementById('closeLeadSettingsBtn');
    const cancelBtn = document.getElementById('cancelLeadSettingsBtn');
    const saveBtn = document.getElementById('saveLeadSettingsBtn');
    
    const enableEnquiryCheckbox = document.getElementById('modalEnableEnquiry');
    const visibilityCheckboxes = document.querySelectorAll('.visibility-checkbox');
    
    const contactVisibilityCard = document.getElementById('contactVisibilityCard');
    const interactionButtonsSection = document.getElementById('interactionButtonsSection');
    
    function updateModalState() {
        const isEnquiryEnabled = enableEnquiryCheckbox.checked;
        
        if (isEnquiryEnabled) {
            contactVisibilityCard.classList.remove('lead-settings-disabled-panel');
            interactionButtonsSection.classList.remove('lead-settings-disabled-panel');
            
            const activeVisibilities = Array.from(visibilityCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
                
            document.querySelectorAll('.interaction-preview-btn').forEach(btn => {
                const requiredVisibility = btn.getAttribute('data-visibility');
                if (activeVisibilities.includes(requiredVisibility)) {
                    btn.classList.remove('disabled-btn');
                } else {
                    btn.classList.add('disabled-btn');
                }
            });
        } else {
            contactVisibilityCard.classList.add('lead-settings-disabled-panel');
            interactionButtonsSection.classList.add('lead-settings-disabled-panel');
            
            document.querySelectorAll('.interaction-preview-btn').forEach(btn => {
                btn.classList.add('disabled-btn');
            });
        }
    }
    
    if (openBtn) {
        openBtn.addEventListener('click', () => {
            modal.style.display = 'flex';
            updateModalState();
        });
    }
    
    const hideModal = () => {
        modal.style.display = 'none';
    };
    
    if (closeBtn) closeBtn.addEventListener('click', hideModal);
    if (cancelBtn) cancelBtn.addEventListener('click', hideModal);
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) hideModal();
    });
    
    if (enableEnquiryCheckbox) {
        enableEnquiryCheckbox.addEventListener('change', updateModalState);
    }
    
    visibilityCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateModalState);
    });
    
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Saving...';
            
            const isEnquiryEnabled = enableEnquiryCheckbox.checked;
            const selectedVisibilities = Array.from(visibilityCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
                
            fetch('{{ route("seller.products.specifications.lead-settings.update", $product->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    enable_enquiry: isEnquiryEnabled ? 1 : 0,
                    contact_visibility: selectedVisibilities
                })
            })
            .then(res => res.json())
            .then(data => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = 'Save Settings';
                
                if (data.success) {
                    showToast('Success', data.message || 'Lead Settings saved successfully.', 'success');
                    setTimeout(() => {
                        hideModal();
                    }, 1000);
                } else {
                    showToast('Error', data.message || 'Failed to save settings.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                saveBtn.disabled = false;
                saveBtn.innerHTML = 'Save Settings';
                showToast('Error', 'An error occurred while saving settings.', 'error');
            });
        });
    }
    
    function showToast(title, desc, type = 'success') {
        const toast = document.getElementById('premiumToast');
        const toastTitle = document.getElementById('premiumToastTitle');
        const toastDesc = document.getElementById('premiumToastDesc');
        const toastIcon = document.getElementById('premiumToastIcon');
        
        toastTitle.textContent = title;
        toastDesc.textContent = desc;
        
        toast.className = 'premium-toast';
        if (type === 'error') {
            toast.classList.add('toast-error');
            toastIcon.className = 'fa-solid fa-circle-xmark';
        } else {
            toastIcon.className = 'fa-solid fa-circle-check';
        }
        
        toast.style.display = 'block';
        
        setTimeout(() => {
            toast.style.animation = 'toastSlideOut 0.3s ease forwards';
            setTimeout(() => {
                toast.style.display = 'none';
                toast.style.animation = '';
            }, 300);
        }, 3000);
    }
});

// ================= AUTO-HIDE VALIDATION MESSAGES =================
$(document).ready(function() {
    // Remove validation error when user starts typing
    $(document).on('input change keyup', 'input, select, textarea', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.validation-error').remove();
        $(this).parent().find('.validation-error').remove();
        $(this).closest('.mb-3, .mb-4').find('.validation-error').remove();
    });
    
    // Remove error on focus
    $(document).on('focus', 'input, select, textarea', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.validation-error').remove();
    });
    
    // Clear all errors before form submission
    $('#productForm').on('submit', function() {
        $('.validation-error').remove();
        $('.is-invalid').removeClass('is-invalid');
    });
});
</script>

@endsection