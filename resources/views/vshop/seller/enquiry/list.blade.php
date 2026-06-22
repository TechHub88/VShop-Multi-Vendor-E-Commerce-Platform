@extends('vshop.seller.main')

<style>
    .btn-secondary {
        background-color: rgb(220 53 69) !important;
        color: #fff !important;
        border-color: rgb(220 53 69) !important;
    }
    .table th {
        font-weight: 600;
        color: #fff !important;
        background-color: #004392 !important;
        border: 1px solid #fff;
    }
    .table tbody td {
        border: 1px solid #dee2e6;
    }
    .table-btn-sec a,
    .table-btn-sec button {
        border: 1px solid #dee2e6 !important;
        font-size: 14px;
        padding: 7px 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
    }
    .table-btn-sec a i,
    .table-btn-sec button i{
        font-size: 12px;
    }
    .table-btn-sec {
        display: flex;
        align-items: center;
    }
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
    .priority-indicator-wrapper {
       display: flex;
        align-items: center;
        justify-content: flex-end;
    }
    .priority-icon-wrap {
        width: 32px !important;
        height: 32px !important;
    }
    .priority-icon-wrap i {
        font-size: 14px !important;
    }
    .dashboard-corner-alert {
        top: 0 !important;
        right:0 !important;
    }
    .modern-card {
        border: 1px solid rgb(201 208 218) !important;
        background: #fff;
        border-radius: 10px;
    }
    .top-back-heading-sec h5 {
        color: #0f172a !important;
    }
    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border-color: #233d79 !important;
    }
    .btn-outline-primary {
        border-color: #233d79 !important;
        color: #233d79 !important;
    }
    .btn-outline-primary:hover {
        background-color: #233d79 !important;
        color: #fff !important;
    }
    .btn-secondary {
        background: #61b3e5 !important;
        color: #fff !important;
        border-color: #61b3e5 !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-dark {
        background: #61b3e5 !important;
        color: #fff !important;
        border-color: #61b3e5 !important;
    }
</style>

@section('content')

<div class="container-fluid py-4">
    
<div calss="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3" style="position: relative;">
<div class="d-flex top-back-heading-sec mb-3">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <h5 class="fw-bold">Enquiry List</h5>
</div>
{{-- ================= CORNER ALERT ================= --}}
@if(($overdueCount ?? 0) > 0)

<div class="dashboard-corner-alert">

    <div class="d-flex align-items-center gap-2">

        {{-- ICON --}}
        <div class="corner-alert-icon">

            <i class="fa-solid fa-triangle-exclamation"></i>

        </div>

        {{-- TEXT --}}
        <div class="corner-alert-text">

            {{ $overdueCount }}
            Overdue Follow-ups pending

        </div>

    </div>

</div>

@endif

</div>

<form id="topSearchForm"
      method="GET"
      action="{{ url()->current() }}">

<input type="hidden"
       name="category_type"
       value="{{ $categoryType }}">

{{-- preserve filters --}}
<input type="hidden" name="category" value="{{ request('category') }}">
<input type="hidden" name="subcategory" value="{{ request('subcategory') }}">
<input type="hidden" name="type" value="{{ request('type') }}">
<input type="hidden" name="source" value="{{ request('source') }}">
<input type="hidden" name="status" value="{{ request('status') }}">
<input type="hidden" name="start" value="{{ request('start') }}">
<input type="hidden" name="end" value="{{ request('end') }}">

<div class="d-flex justify-content-between align-items-center mb-3">

    {{-- LEFT --}}
    <div>

        <a href="{{ url()->current() }}?category_type=all"
           class="btn btn-sm {{ $categoryType=='all'?'btn-primary':'btn-outline-primary' }}">

            All

        </a>

        <a href="{{ url()->current() }}?category_type=product"
           class="btn btn-sm {{ $categoryType=='product'?'btn-primary':'btn-outline-primary' }}">

            Product

        </a>

        <a href="{{ url()->current() }}?category_type=service"
           class="btn btn-sm {{ $categoryType=='service'?'btn-primary':'btn-outline-primary' }}">

            Service

        </a>

    </div>


    {{-- RIGHT SEARCH --}}
    <div style="width:250px;">

        <div class="input-group input-group-sm">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search..."
                   class="form-control">

            <button class="btn btn-primary"
                    type="submit">

                Search

            </button>

        </div>

    </div>
    

</div>

</form>

{{-- ================= FILTER SECTION ================= --}}
<div class="modern-card p-3 mb-3">

<form id="filterForm"
      method="GET"
      action="{{ url()->current() }}">

<input type="hidden"
       name="category_type"
       value="{{ $categoryType }}">

<div class="row g-3">

    {{-- CATEGORY --}}
    <div class="col-md-2">

        <label class="fw-bold">Category</label>

        <select name="category"
                id="category"
                class="form-control form-control-sm">

            <option value="">Select Category</option>

            @foreach($categories ?? [] as $id => $name)

            <option value="{{ $id }}"
            {{ request('category') == $id ? 'selected' : '' }}>

                {{ $name }}

            </option>

            @endforeach

        </select>

    </div>


    {{-- SUBCATEGORY --}}
    <div class="col-md-2">

        <label class="fw-bold">Subcategory</label>

        <select name="subcategory"
                id="subcategory"
                class="form-control form-control-sm">

            <option value="">Select Subcategory</option>

            @if(!empty($subcategories))

                @foreach($subcategories as $id => $name)

                    <option value="{{ $id }}"
                    {{ (string)request('subcategory') === (string)$id ? 'selected' : '' }}>

                        {{ $name }}

                    </option>

                @endforeach

            @endif

        </select>

    </div>


    {{-- TYPE --}}
    <div class="col-md-2">

        <label class="fw-bold">Type</label>

        <select name="type"
                id="type"
                class="form-control form-control-sm">

            <option value="">Select Type</option>

            @if($categoryType == 'product')

                <option value="Product"
                {{ request('type') == 'Product' ? 'selected' : '' }}>

                    Product

                </option>

            @elseif($categoryType == 'service')

                <option value="Service"
                {{ request('type') == 'Service' ? 'selected' : '' }}>

                    Service

                </option>

            @else

                <option value="Product"
                {{ request('type') == 'Product' ? 'selected' : '' }}>

                    Product

                </option>

                <option value="Service"
                {{ request('type') == 'Service' ? 'selected' : '' }}>

                    Service

                </option>

            @endif

        </select>

    </div>

    {{-- SOURCE --}}
    <div class="col-md-2">

        <label class="fw-bold">Source</label>

        <select name="source"
                id="source"
                class="form-control form-control-sm">

            <option value="">Select Source</option>

            @foreach([
                'In Cart',
                'In Wish List',
                'Vridhee',
                'Direct',
                'In Call',
                'Email',
                'Facebook',
                'Instagram',
                'LinkedIn',
                'Google Ads',
                'Other'
            ] as $s)

            <option value="{{ $s }}"
            {{ request('source') == $s ? 'selected' : '' }}>

                {{ $s }}

            </option>

            @endforeach

        </select>

    </div>


    {{-- STATUS --}}
    <div class="col-md-2">

        <label class="fw-bold">Status</label>

        <select name="status"
                id="status"
                class="form-control form-control-sm">

            <option value="">Select Status</option>

            @foreach(['New','Follow-up','Converted','Lost'] as $s)

            <option value="{{ $s }}"
            {{ request('status') == $s ? 'selected' : '' }}>

                {{ $s }}

            </option>

            @endforeach

        </select>

    </div>


    {{-- FROM --}}
    <div class="col-md-2">

        <label class="fw-bold">From</label>

        <input type="date"
               name="start"
               value="{{ request('start') }}"
               class="form-control form-control-sm">

    </div>


    {{-- TO --}}
    <div class="col-md-2">

        <label class="fw-bold">To</label>

        <input type="date"
               name="end"
               value="{{ request('end') }}"
               class="form-control form-control-sm">

    </div>
    
    {{-- ================= SORT SECTION (Auto-refresh) ================= --}}
    <div class="col-md-2">
        <label class="fw-bold">Sort By</label>
        <select name="sort_by" id="sort_by" class="form-control form-control-sm">
            <option value="user" {{ request('sort_by') == 'user' ? 'selected' : '' }}>By User</option>
            <option value="product_service" {{ request('sort_by') == 'product_service' ? 'selected' : '' }}>By Product/Service</option>
        </select>
    </div>


    {{-- APPLY BUTTON --}}
    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary btn-sm w-100" style="padding: 9px 0;font-size: 16px;">Apply</button>
    </div>


    {{-- RESET BUTTON --}}
    <div class="col-md-2 d-flex align-items-end">
        <a href="{{ url()->current() }}?category_type={{ $categoryType }}" class="btn btn-secondary btn-sm w-100" style="padding: 9px 0;font-size: 16px;">Reset</a>
    </div>
    

  <div class="col d-flex justify-content-end align-items-end">
    <div class="priority-indicator-wrapper">
        <button type="button" class="priority-indicator-btn" onclick="togglePriorityDropdown()">
            <div class="priority-left-sec">
                <div class="priority-icon-wrap">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div class="priority-text-sec">
                    <div class="priority-title" id="priorityTitle">
                        @php
                            $priorityParam = request('priority');
                            if($priorityParam == 'high') {
                                echo '<span class="priority-dot bg-danger" style="display:inline-block; margin-right:6px;"></span>High Priority';
                            } elseif($priorityParam == 'medium') {
                                echo '<span class="priority-dot bg-warning" style="display:inline-block; margin-right:6px;"></span>Medium Priority';
                            } elseif($priorityParam == 'low') {
                                echo '<span class="priority-dot bg-secondary" style="display:inline-block; margin-right:6px;"></span>Low Priority';
                            } else {
                                echo 'Priority Indicator';
                            }
                        @endphp
                    </div>
                </div>
            </div>
            <div class="priority-count" id="priorityCount">{{ $enquiries->total() }}</div>
        </button>
        <div class="priority-dropdown" id="priorityDropdown">
            <div class="priority-filter-item reset" onclick="filterPriority('all')">
                <span class="priority-dot bg-primary"></span>All Priorities
            </div>
            <div class="priority-filter-item high" onclick="filterPriority('danger')">
                <span class="priority-dot bg-danger"></span>High Priority
            </div>
            <div class="priority-filter-item medium" onclick="filterPriority('warning')">
                <span class="priority-dot bg-warning"></span>Medium Priority
            </div>
            <div class="priority-filter-item low" onclick="filterPriority('secondary')">
                <span class="priority-dot bg-secondary"></span>Low Priority
            </div>
        </div>
    </div>
</div>

</div>

</form>

</div>

{{-- ================= UPDATED PROFESSIONAL TABLE CSS ================= --}}

<style>

/* ================= TABLE WRAPPER ================= */
.enquiry-table-wrapper{
    background:#fff;
    border:1px solid #dbe4ee;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 8px 30px rgba(15,23,42,.05);
}

/* ================= TABLE ================= */
.enquiry-table{
    margin-bottom:0 !important;
    vertical-align:middle;
    min-width:1700px;
}

/* ================= TABLE HEAD ================= */
.enquiry-table thead th{
    background:linear-gradient(180deg,#1f4d99 0%,#12326b 100%) !important;
    color:#fff !important;
    font-size:13px;
    font-weight:700;
    border:none !important;
    padding:15px 12px;
    white-space:nowrap;
    vertical-align:middle;
}

/* ================= TABLE BODY ================= */
.enquiry-table tbody td{
    padding:14px 12px;
    border-bottom:1px solid #edf2f7 !important;
    vertical-align:top;
    font-size:13px;
    color:#334155;
    background:#fff;
}

/* ================= HOVER ================= */
.enquiry-table tbody tr{
    transition:.25s;
}

.enquiry-table tbody tr:hover{
    background:#f8fbff;
}

/* ================= USER INFO ================= */
.user-name{
    font-size:14px;
    font-weight:700;
    color:#0f172a;
    margin-bottom:2px;
}

.user-meta{
    color:#64748b;
    font-size:12px;
    line-height:1.5;
}

/* ================= ENQUIRY ID ================= */
.enquiry-id{
    font-weight:700;
    color:#1e293b;
    font-size:14px;
}

.enquiry-date{
    color:#64748b;
    font-size:12px;
}

/* ================= PRODUCT ================= */
.product-title{
    font-weight:700;
    color:#0f172a;
}

.product-qty{
    font-size:12px;
    color:#64748b;
}

/* ================= BADGES ================= */
.custom-badge{
    padding:6px 10px;
    border-radius:30px;
    font-size:11px;
    font-weight:700;
    display:inline-flex;
    align-items:center;
    gap:5px;
}

/* ================= FOLLOWUP STATUS ================= */
.followup-overdue{
    background:#fff1f2;
    color:#dc2626;
}

.followup-scheduled{
    background:#eff6ff;
    color:#2563eb;
}

.followup-progress{
    background:#fefce8;
    color:#ca8a04;
}

/* ================= SOURCE ================= */
.source-badge{
    background:#e0f2fe;
    color:#0369a1;
}

/* ================= STATUS ================= */
.status-select{
    border:1px solid #d7dfeb !important;
    border-radius:10px !important;
    height:38px;
    font-size:13px;
    box-shadow:none !important;
}

/* ================= PRIORITY ================= */
.priority-box{
    margin-top:8px;
}

.priority-text{
    font-size:12px;
    font-weight:700;
}

/* ================= ACTION CARD ================= */
.action-card{
    background:#f8fbff;
    border:1px solid #dce8f5;
    border-radius:16px;
    padding:12px;
}

/* ================= INPUT ================= */
.action-input{
    border:1px solid #d7dfeb !important;
    border-radius:10px !important;
    height:38px;
    font-size:12px;
    box-shadow:none !important;
}

/* ================= BUTTON WRAP ================= */
.action-btn-wrap{
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
}

/* ================= ICON BUTTONS ================= */
.icon-btn{
    width:38px;
    height:38px;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    transition:.25s;
    text-decoration:none;
    border:none;
}

/* ================= EMAIL ================= */
.email-btn{
    background:#e8f1ff;
    color:#0d6efd !important;
    border:1px solid #bfd7ff;
}

.email-btn:hover{
    background:#0d6efd;
    color:#fff !important;
}

/* ================= WHATSAPP ================= */
.whatsapp-btn{
    background:#e9fff1;
    color:#16a34a !important;
    border:1px solid #bbf7d0;
}

.whatsapp-btn:hover{
    background:#16a34a;
    color:#fff !important;
}

/* ================= FOLLOWUP BTN ================= */
.followup-btn{
    background:linear-gradient(180deg,#2c7fc3 0%,#233d79 100%);
    color:#fff !important;
    border:none !important;
    border-radius:10px !important;
    height:38px;
    padding:0 16px !important;
    font-size:12px !important;
    font-weight:700 !important;
}

/* ================= CONVERT BTN ================= */
.convert-btn{
    background:#0f172a !important;
    color:#fff !important;
    border:none !important;
    border-radius:10px !important;
    padding:9px 14px !important;
    font-size:12px !important;
    font-weight:700 !important;
}

/* ================= SAVE BTN ================= */
.save-btn{
    background:linear-gradient(180deg,#2c7fc3 0%,#233d79 100%);
    border:none !important;
    border-radius:10px !important;
    color:#fff !important;
    height:40px;
    font-size:13px !important;
    font-weight:700 !important;
}

/* ================= NOTE ================= */
.note-box{
    border:1px solid #d7dfeb !important;
    border-radius:10px !important;
    font-size:12px;
    min-height:70px;
    box-shadow:none !important;
}

/* ================= TABLE SCROLL ================= */
.table-responsive{
    overflow-x:auto;
    scrollbar-width:thin;
}

/* ================= MOBILE ================= */
@media(max-width:768px){

    .action-btn-wrap{
        flex-direction:column;
        align-items:stretch;
    }

    .followup-btn,
    .convert-btn{
        width:100%;
    }

}

</style>

{{-- ================= UPDATED TABLE ================= --}}
<div class="enquiry-table-wrapper">

<div class="table-responsive">

<table class="table enquiry-table align-middle">

<thead>

<tr>
<th>Enquiry ID</th>
<th>Created Date</th>
<th>User Details</th>
<th>Product / Service</th>
<th>Interaction</th>
<th>Interested Service</th>
<th>Follow-up</th>
<th>Source</th>
<th>Assign To</th>
<th>Status</th>
<th width="420">Actions</th>
</tr>

</thead>

<tbody>

@foreach($enquiries as $e)

@php

$score = $e->lead_score ?? 0;

if($score >= 70){
    $color = 'danger';
    $priority = 'High Priority';
}elseif($score >= 40){
    $color = 'warning';
    $priority = 'Medium Priority';
}else{
    $color = 'secondary';
    $priority = 'Low Priority';
}

$followupStatus = 'In Progress';
$followupClass = 'followup-progress';

if(!empty($e->next_followup_date)){

    $followDate = \Carbon\Carbon::parse($e->next_followup_date);

    if($followDate->isPast()){
        $followupStatus = 'Overdue';
        $followupClass = 'followup-overdue';
    }elseif($followDate->isToday()){
        $followupStatus = 'Scheduled';
        $followupClass = 'followup-scheduled';
    }
}

@endphp

<tr>

{{-- ================= ENQUIRY ID ================= --}}
<td>

<div class="enquiry-id">
    #{{ $e->enquiry_no }}
</div>

<div class="enquiry-date">
    {{ \Carbon\Carbon::parse($e->created_at)->format('d M Y') }}
</div>

</td>

{{-- ================= CREATED DATE ================= --}}
<td>

<div class="user-meta">
    {{ \Carbon\Carbon::parse($e->created_at)->format('d-m-Y') }}
</div>

<div class="user-meta">
    {{ \Carbon\Carbon::parse($e->created_at)->format('h:i A') }}
</div>

</td>

{{-- ================= USER DETAILS ================= --}}
<td>

<div class="user-name">
    {{ $e->user_name }}
</div>

<div class="user-meta">
    {{ $e->user_email ?? '-' }}
</div>

<div class="user-meta">
    {{ $e->user_mobile }}
</div>

<div class="user-meta">
    {{ $e->country ?? 'India' }}
    /
    {{ $e->state ?? 'West Bengal' }}
</div>

</td>

{{-- ================= PRODUCT ================= --}}
<td>

<div class="product-title">
    {{ $e->product_service ?? '-' }}
</div>

<div class="product-qty">
    Qty :
    {{ $e->quantity ?? 1 }}
</div>

</td>

{{-- ================= INTERACTION ================= --}}
<td>

@if(($e->interaction_type ?? '') == 'Call')

<span class="custom-badge bg-primary text-white">
    <i class="fa-solid fa-phone"></i>
    Call
</span>

@elseif(($e->interaction_type ?? '') == 'WhatsApp')

<span class="custom-badge bg-success text-white">
    <i class="fa-brands fa-whatsapp"></i>
    WhatsApp
</span>

@elseif(($e->interaction_type ?? '') == 'Email')

<span class="custom-badge bg-dark text-white">
    <i class="fa-solid fa-envelope"></i>
    Email
</span>

@else

<span class="custom-badge bg-secondary text-white">
    <i class="fa-solid fa-circle-info"></i>
    Enquiry
</span>

@endif

</td>

{{-- ================= INTERESTED ================= --}}
<td>

<div class="user-meta">
    {{ $e->interested_service ?? 'Campaign / Product Enquiry' }}
</div>

</td>

{{-- ================= FOLLOWUP ================= --}}
<td>

<span class="custom-badge {{ $followupClass }}">
    {{ $followupStatus }}
</span>

<div class="user-meta mt-1">
    {{ $e->next_followup_date ?? '-' }}
</div>

</td>

{{-- ================= SOURCE ================= --}}
<td>

<span class="custom-badge source-badge">
    {{ $e->source ?? 'Direct' }}
</span>

</td>

{{-- ================= ASSIGN ================= --}}
<td>

<select class="form-control form-control-sm status-select">

<option>Assign To</option>

<option selected>
Seller
</option>

<option>
Sales Team
</option>

<option>
Support Team
</option>

</select>

</td>

{{-- ================= STATUS ================= --}}
<td>

<select class="form-control form-control-sm status-select">

@foreach(['New','Follow-up','Converted','Lost'] as $s)

<option value="{{ $s }}"
{{ $e->status==$s?'selected':'' }}>

{{ $s }}

</option>

@endforeach

</select>

<div class="priority-box">

<span class="badge bg-{{ $color }}">
    Score :
    {{ $score }}
</span>

<div class="priority-text text-{{ $color }} mt-1">
    {{ $priority }}
</div>

</div>

</td>

{{-- ================= ACTIONS ================= --}}
<td>

<form method="POST"
      action="{{ route('seller.enquiry.update') }}">

@csrf

<input type="hidden"
       name="enquiry_id"
       value="{{ $e->id }}">

<div class="action-card">

<div class="row g-2 mb-2">

<div class="col-6">

<input type="date"
       name="date"
       value="{{ $e->next_followup_date }}"
       class="form-control form-control-sm action-input">

</div>

<div class="col-6">

<input type="time"
       name="time"
       value="{{ $e->next_followup_time }}"
       class="form-control form-control-sm action-input">

</div>

</div>

<div class="action-btn-wrap mb-2">

<button type="button"
        class="btn followup-btn">

    Next Follow-up

</button>

<a href="https://mail.google.com/mail/?view=cm&fs=1&to={{ $e->user_email }}"
   target="_blank"
   class="icon-btn email-btn">

    <i class="fa-solid fa-envelope"></i>

</a>

<a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $e->user_mobile) }}"
   target="_blank"
   class="icon-btn whatsapp-btn">

    <i class="fa-brands fa-whatsapp"></i>

</a>

@if($e->status!='Converted')

<a href="{{ url('seller/enquiry/convert/'.$e->id) }}"
   class="btn convert-btn">

    Convert to Order

</a>

@else

<span class="badge bg-success p-2">
    Converted
</span>

@endif

</div>

<textarea name="followup_note"
          placeholder="Add follow-up note..."
          class="form-control note-box mb-2">{{ $e->followup_note }}</textarea>

<button class="btn save-btn w-100">

    Save Follow-up

</button>

</div>

</form>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

<div class="mt-3">
{{ $enquiries->appends(request()->all())->links() }}
</div>

</div>

<style>
    .priority-checkbox{
        width:14px;
        height:14px;
        opacity:1 !important;
    }

    .priority-danger{
        accent-color:#dc3545;
    }

    .priority-warning{
        accent-color:#ffc107;
    }

    .priority-secondary{
        accent-color:#6c757d;
    }

    /* ================= DASHBOARD CORNER ALERT ================= */
    .dashboard-corner-alert{
        position:absolute;
        top:12px;
        right:20px;
        background:#fff5f5;
        border:1px solid #ff4d4f;
        border-radius:10px;
        padding:8px 14px;
        display:flex;
        align-items:center;
        box-shadow:0 4px 10px rgba(255,77,79,0.08);
        z-index:99;
    }

    .corner-alert-icon{
        width:22px;
        height:22px;
        min-width:22px;
        border-radius:6px;
        background:#ff4d4f;
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:10px;
    }

    .corner-alert-text{
        font-size:13px;
        font-weight:600;
        color:#dc3545;
        white-space:nowrap;
    }

    /* ================= PRIORITY FILTER ================= */
    .priority-indicator-wrapper{
        position:relative;
        width:100%;
    }

    /* ================= BUTTON ================= */
    .priority-indicator-btn{
        border:none;
        background:#fff;
        border-radius:12px;
        padding:10px 16px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:20px;
        font-size:13px;
        font-weight:600;
        cursor:pointer;
        border:1px solid #eee;
        box-shadow:0 4px 14px rgba(0,0,0,0.08);
        transition:.3s;
    }

    .priority-indicator-btn:hover{
        transform:translateY(-2px);
        box-shadow:0 8px 20px rgba(0,0,0,0.12);
    }

    /* ================= COUNT BADGE ================= */
    .priority-count{
        background:#dc3545;
        color:#fff;
        border-radius:50px;
        min-width:24px;
        height:24px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:11px;
        font-weight:700;
        padding:0 8px;
    }

    /* ================= DROPDOWN ================= */
    .priority-dropdown{
        position:absolute;
        top:45px;
        right:0;
        width:240px;
        background:#fff;
        border-radius:14px;
        overflow:hidden;
        box-shadow:0 10px 35px rgba(0,0,0,0.12);
        display:none;
        z-index:999;
        animation:priorityFade .2s ease;
    }

    @keyframes priorityFade{
        from{
            opacity:0;
            transform:translateY(10px);
        }
        to{
            opacity:1;
            transform:translateY(0);
        }
    }

    /* ================= DROPDOWN ITEMS ================= */
    .priority-filter-item{
        padding:13px 15px;
        display:flex;
        align-items:center;
        gap:12px;
        cursor:pointer;
        font-size:13px;
        font-weight:600;
        transition:.2s;
    }

    .priority-filter-item:hover{
        background:#f8f9fa;
    }

    /* ================= COLORS ================= */
    .priority-filter-item.high{
        color:#dc3545;
    }

    .priority-filter-item.medium{
        color:#e0a800;
    }

    .priority-filter-item.low{
        color:#6c757d;
    }

    .priority-filter-item.reset{
        color:#0d6efd;
        border-top:1px solid #eee;
    }

    /* ================= DOT ================= */
    .priority-dot{
        width:10px;
        height:10px;
        border-radius:50%;
        display:inline-block;
    }

    /* ================= LEFT SECTION ================= */
    .priority-left-sec{
        display:flex;
        align-items:center;
        gap:12px;
    }

    /* ================= ICON ================= */
    .priority-icon-wrap{
        width:38px;
        height:38px;
        border-radius:12px;
        background:linear-gradient(135deg, #dc3545, #ff6b81);
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:15px;
        box-shadow:0 4px 12px rgba(220,53,69,0.25);
    }

    /* ================= TEXT ================= */
    .priority-text-sec{
        text-align:left;
    }

    .priority-title{
        font-size:13px;
        font-weight:700;
        color:#212529;
        line-height:1.1;
    }

    /* ================= MOBILE ================= */
    @media(max-width:768px){
        .priority-indicator-wrapper{
            justify-content:flex-start;
            margin-top:10px;
        }
        .priority-dropdown{
            width:100%;
            right:0;
        }
    }
</style>

<script>
    function goBack() {
        window.history.back();
    }

    // CATEGORY -> SUBCATEGORY (without auto-submit)
    document.getElementById('category').addEventListener('change', function () {
        let id = this.value;
        let sub = document.getElementById('subcategory');
        sub.innerHTML = '<option value="">Loading...</option>';
        if (!id) {
            sub.innerHTML = '<option value="">Select Subcategory</option>';
            return;
        }
        fetch(`/seller/subcategories/${id}`)
        .then(res => res.json())
        .then(data => {
            sub.innerHTML = '<option value="">Select Subcategory</option>';
            data.forEach(i => {
                sub.innerHTML += `<option value="${i.id}">${i.name}</option>`;
            });
        });
    });

    /* ================= PRIORITY FILTER ================= */
    function togglePriorityDropdown(){
        let dropdown = document.getElementById('priorityDropdown');
        if(dropdown.style.display === 'block'){
            dropdown.style.display = 'none';
        }else{
            dropdown.style.display = 'block';
        }
    }

    function filterPriority(priority){
        let visibleCount = 0;
        let title = 'All Priorities';
        
        // Get current URL and parameters
        let url = new URL(window.location.href);
        
        if(priority === 'all'){
            url.searchParams.delete('priority');
            title = 'All Priorities';
        } else if(priority === 'danger'){
            url.searchParams.set('priority', 'high');
            title = 'High Priority';
        } else if(priority === 'warning'){
            url.searchParams.set('priority', 'medium');
            title = 'Medium Priority';
        } else if(priority === 'secondary'){
            url.searchParams.set('priority', 'low');
            title = 'Low Priority';
        }
        
        // Redirect to apply filter
        window.location.href = url.toString();
    }

    /* ================= CLOSE OUTSIDE ================= */
    document.addEventListener('click',function(e){
        let wrapper = document.querySelector('.priority-indicator-wrapper');
        if(wrapper && !wrapper.contains(e.target)){
            document.getElementById('priorityDropdown').style.display = 'none';
        }
    });
</script>

@endsection