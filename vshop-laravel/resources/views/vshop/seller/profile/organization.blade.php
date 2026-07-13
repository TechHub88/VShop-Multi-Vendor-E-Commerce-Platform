@extends('vshop.seller.main')

<style>
    /*.document-expiry-mini-alert {background: #e3263629;color: #e32636;padding: 6px 6px;border-radius: 5px;display: flex;align-items: center;gap: 6px;}*/
    .document-expiry-mini-alert {padding: 5px; border-radius: 5px;border: 1px solid transparent;}
    .document-expiry-mini-alert:hover {background: #e326363d;transform:translateY(-2px);border-color: #e32636;}
    .mini-alert-text svg {width: 18px; height: 18px;}
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-outline-primary {
        border-color: #084f9d !important;
        color: #084f9d !important;
    }
    .btn-outline-primary:hover {
        background: #084f9d !important;
        color: #fff !important;
    }
</style>

@section('content')

<div class="container-fluid py-4">
<div class="d-flex top-back-heading-sec mb-4">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
</div>

<div class="marketplace-wrapper">

{{-- ================= LEFT CONTENT ================= --}}
<div class="marketplace-left">

<div class="tab-pane fade show active" id="org">

<form id="orgForm" method="POST"
action="{{ route('seller.organization.save') }}"
enctype="multipart/form-data">

@csrf


<div class="card shadow-sm border-0">
<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-4">

<h5 class="text-primary mb-0">
Organization Setup
</h5>


<a href="{{ route('seller.organization.list') }}"
class="btn btn-outline-primary btn-sm">

<i class="fa fa-link me-1"></i>
View Organization

</a>

</div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-4" id="orgTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic-info" type="button" role="tab" aria-controls="basic-info" aria-selected="true">Basic Info</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="company-tab" data-bs-toggle="tab" data-bs-target="#company" type="button" role="tab" aria-controls="company" aria-selected="false">Company Registration</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tax-tab" data-bs-toggle="tab" data-bs-target="#tax" type="button" role="tab" aria-controls="tax" aria-selected="false">PAN & GST</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab" aria-controls="address" aria-selected="false">Address & Comm</button>
      </li>
    </ul>

    <div class="tab-content" id="orgTabsContent">
        <!-- Basic Info Tab -->
        <div class="tab-pane fade show active p-2" id="basic-info" role="tabpanel" aria-labelledby="basic-tab">

{{-- ================= APPLICATION ================= --}}
<div class="mb-4">

<label class="fw-bold">Application <span class="text-danger">*</span></label>

<div class="row mt-3">

@php
    // session panels (selected from panel page)
    $sessionApps = session('seller_panel', []);

    // fallback DB (if session empty)
    $dbApps = json_decode($seller->application ?? '[]', true) ?? [];

    // final apps to show
    $visibleApps = !empty($sessionApps) ? $sessionApps : $dbApps;
@endphp

@foreach($visibleApps as $app)

@php
    $label = match($app){
        'product' => 'V-Shop',
        'travel' => 'V-Travel',
        default => ucfirst($app)
    };
@endphp

<div class="col-md-3">

<div class="form-check d-flex align-items-center">

<input type="checkbox"
name="application[]"
value="{{ $app }}"
id="app_{{ $app }}"
class="form-check-input me-2"
checked readonly>

<label class="form-check-label" for="app_{{ $app }}">
{{ $label }}
</label>

</div>

</div>

@endforeach

</div>

@error('application')
<div class="text-danger small mt-1">{{ $message }}</div>
@enderror

</div>

<hr>

<div class="mb-3">
<label class="fw-bold">Legal Name <span class="text-danger">*</span></label>

<input type="text"
name="legal_name"
class="form-control"
value="{{ old('legal_name', $seller->legal_name ?? '') }}"
placeholder="Enter legal business name">
@error('legal_name')
    <small class="text-danger">{{ $message }}</small>
@enderror
</div>

{{-- ================= HIDE PRICE ================= --}}
<div class="mb-4">

<label class="fw-bold">
Hide Product Price (Non Logged Users)
</label>

<select name="hide_price" class="form-control">

<option value="Yes"
{{ old('hide_price', $seller->hide_price ?? 'Yes') == 'Yes' ? 'selected' : '' }}>
Yes
</option>

<option value="No"
{{ old('hide_price', $seller->hide_price ?? '') == 'No' ? 'selected' : '' }}>
No
</option>

</select>
</div>

<hr>

{{-- ================= LOGO + TRADEMARK ROW ================= --}}
<div class="row align-items-start g-4">

    {{-- LOGO --}}
    <div class="col-md-6">

        <label class="fw-bold mb-2 d-block">
            Upload Logo
        </label>

        <input type="file"
               name="logo"
               id="logoInput"
               class="form-control"
               accept="image/*">

        @error('logo')
            <small class="text-danger">
                {{ $message }}
            </small>
        @enderror

        <small class="text-muted d-block mt-1">
            PNG/JPG – Min 500x500px
        </small>

        {{-- WRAPPER --}}
        <div class="logo-wrapper mt-3">

            @if(!empty($seller->logo))

                <img id="logoPreview"
                     src="{{ asset($seller->logo) }}"
                     class="logo-img">

                <span id="removeLogo"
                      class="logo-remove-btn">

                    ✕

                </span>

            @else

                <img id="logoPreview"
                     class="logo-img d-none">

                <span id="removeLogo"
                      class="logo-remove-btn d-none">

                    ✕

                </span>

            @endif

        </div>

    </div>

    {{-- TRADEMARK --}}
    <div class="col-md-6">

        <label class="fw-bold d-block mb-2">
            Trademark
        </label>

        {{-- TOGGLE --}}
        <div class="d-flex align-items-center gap-4 mb-3">

            <div class="form-check form-check-inline m-0">

                <input class="form-check-input"
                       type="radio"
                       name="logo_trademark"
                       id="trademarkYes"
                       value="Yes"
                       {{ ($seller->logo_trademark ?? '') == 'Yes' ? 'checked' : '' }}>

                <label class="form-check-label"
                       for="trademarkYes">

                    Yes

                </label>

            </div>

            <div class="form-check form-check-inline m-0">

                <input class="form-check-input"
                       type="radio"
                       name="logo_trademark"
                       id="trademarkNo"
                       value="No"
                       {{ ($seller->logo_trademark ?? 'No') == 'No' ? 'checked' : '' }}>

                <label class="form-check-label"
                       for="trademarkNo">

                    No

                </label>

            </div>

        </div>

        {{-- PDF SECTION --}}
        <div id="trademarkFileSection"
             style="{{ ($seller->logo_trademark ?? '') == 'Yes' ? '' : 'display:none;' }}">

            <input type="file"
                   name="trademark_file"
                   id="trademarkFile"
                   class="form-control"
                   accept="application/pdf">

            @error('trademark_file')
                <small class="text-danger d-block">
                    {{ $message }}
                </small>
            @enderror

            <small class="text-muted d-block mt-1">
                Only PDF allowed
            </small>

          {{-- VIEW FILE --}}
@if(!empty($seller->trademark_file))

    <div class="mt-3">

        <a href="{{ asset($seller->trademark_file) }}"
           target="_blank"
           class="btn btn-sm btn-outline-primary">

            View Trademark PDF

        </a>

    </div>

@endif

        </div>

    </div>

</div>

{{-- ================= TRADEMARK SCRIPT ================= --}}
<script>

document.addEventListener('DOMContentLoaded', function(){

    const yesRadio =
        document.getElementById('trademarkYes');

    const noRadio =
        document.getElementById('trademarkNo');

    const fileSection =
        document.getElementById('trademarkFileSection');

    function toggleTrademarkFile(){

        if(yesRadio.checked){

            fileSection.style.display = 'block';

        }else{

            fileSection.style.display = 'none';
        }
    }

    yesRadio.addEventListener(
        'change',
        toggleTrademarkFile
    );

    noRadio.addEventListener(
        'change',
        toggleTrademarkFile
    );

    toggleTrademarkFile();

});

</script>

<hr>


{{-- ================= BUSINESS TYPE ================= --}}
<h6 class="text-primary">Business Type <span class="text-danger">*</span></h6>

<div class="mb-4">

@foreach(['Individual','Proprietary','Partnership','LLP','Private Ltd','Trust','Other'] as $type)

<div class="form-check form-check-inline">

<input class="form-check-input businessType"
type="radio"
name="business_type"
value="{{ $type }}"
{{ old('business_type',$seller->business_type)==$type?'checked':'' }}>



<label class="form-check-label">
{{ $type }}
</label>



</div>



@endforeach

</div>
        </div> <!-- End Basic Info Tab -->
        
        <!-- Company Tab -->
        <div class="tab-pane fade p-2" id="company" role="tabpanel" aria-labelledby="company-tab">

<div id="companySection" style="display:none;">

    <!-- HEADER -->
    <div class="d-flex align-items-center gap-2 flex-wrap mt-3 mb-3">

        <h6 class="text-primary fw-bold mb-0">
            Company Registration
        </h6>

        @php $status = strtolower($seller->company_status ?? 'na'); @endphp

        @if($status == 'pending')

            <span class="badge pan-badge bg-warning text-dark">
                Pending
            </span>

        @elseif($status == 'approved')

            <span class="badge pan-badge bg-success">
                Approved
            </span>

        @elseif($status == 'rejected')

            <span class="badge pan-badge bg-danger">
                Rejected
            </span>

        @else

            <span class="badge pan-badge bg-secondary">
                NA
            </span>

        @endif

    </div>

    <!-- FORM -->
    <div class="row mb-3">

        <!-- CIN -->
        <div class="col-md-4">

            <label>CIN</label>

            <input type="text"
                name="cin"
                id="cinInput"
                maxlength="21"
                class="form-control text-uppercase"
                placeholder="L12345MH2020PLC123456"
                value="{{ old('cin',$seller->cin ?? '') }}">

            <small class="text-danger d-none" id="cinError">
                Invalid CIN format
            </small>

        </div>

        <!-- INCORPORATION -->
        <div class="col-md-4">

            <label>Incorporation Date</label>

            <input type="date"
                name="incorporation_date"
                class="form-control"
                value="{{ old('incorporation_date',$seller->incorporation_date ?? '') }}">

        </div>

        <!-- CERTIFICATE -->
        <div class="col-md-4">

            <label>Certificate (PDF)</label>

            <input type="file"
                name="company_certificate"
                class="form-control"
                accept="application/pdf">

            @if(!empty($seller->company_certificate))
                <div class="mt-2">
                    <a href="{{ asset($seller->company_certificate) }}"
                       target="_blank"
                       class="btn btn-sm btn-outline-primary">
                        View Uploaded File
                    </a>
                </div>
            @endif

        </div>

    </div>

</div>
        </div> <!-- End Company Tab -->

        <!-- PAN & GST Tab -->
        <div class="tab-pane fade p-2" id="tax" role="tabpanel" aria-labelledby="tax-tab">

@php
use Carbon\Carbon;

$today = Carbon::today();

// PAN
$panExpiry = $seller->pan_expiry ? Carbon::parse($seller->pan_expiry) : null;
$panDaysLeft = $panExpiry ? floor($today->diffInDays($panExpiry, false)) : null;
$panStatus = strtolower($seller->pan_status ?? 'na');

// GST
$gstExpiry = $seller->gst_expiry ? Carbon::parse($seller->gst_expiry) : null;
$gstDaysLeft = $gstExpiry ? (int) $today->diffInDays($gstExpiry, false) : null;
$gstStatus = strtolower($seller->gst_status ?? 'na');
@endphp



<!-- ================= PAN SECTION ================= -->

@php

$panStatus = strtolower($seller->pan_status ?? 'na');

$today = \Carbon\Carbon::today();

$panExpiry = $seller->pan_expiry
    ? \Carbon\Carbon::parse($seller->pan_expiry)
    : null;

$panDaysLeft = $panExpiry
    ? (int) $today->diffInDays($panExpiry, false)
    : null;

@endphp

<div class="card mb-4 border-0 shadow-sm pan-card">

    <div class="card-body">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">

            <div class="d-flex align-items-center gap-2 flex-wrap">

                <h6 class="fw-bold mb-0 pan-heading">
                    PAN Details <span class="text-danger">*</span>
                </h6>

                {{-- STATUS --}}
                @if($panStatus == 'pending')

                    <span class="badge pan-badge bg-warning text-dark">
                        Pending
                    </span>

                @elseif($panStatus == 'approved')

                    <span class="badge pan-badge bg-success">
                        Approved
                    </span>

                @elseif($panStatus == 'rejected')

                    <span class="badge pan-badge bg-danger">
                        Rejected
                    </span>

                @else

                    <span class="badge pan-badge bg-secondary">
                        NA
                    </span>

                @endif

            </div>

            <!-- SMALL DOCUMENT ALERT -->
            @if($panExpiry && $panDaysLeft <= 0)

            <div class="document-expiry-mini-alert">

                <div class="mini-siren">

                    <div class="mini-siren-light">
                        <div class="mini-siren-inner"></div>
                    </div>

                    <div class="mini-siren-base"></div>

                </div>

                <span class="mini-alert-text">
                    Document Expire
                </span>

            </div>

            @endif

        </div>

        <!-- FORM -->
        <div class="row g-3">

            <!-- PAN NUMBER -->
            <div class="col-md-4">

                <label class="form-label">
                    PAN Number <span class="text-danger">*</span>
                </label>

                <input type="text"
                    name="pan_number"
                    id="panInput"
                    maxlength="10"
                    class="form-control text-uppercase"
                    value="{{ old('pan_number', $seller->pan_number ?? '') }}"
                    placeholder="ABCDE1234F">

                @error('pan_number')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

                <small id="panError" class="text-danger d-none">
                    Invalid PAN format
                </small>

            </div>

            <!-- PAN EXPIRY -->
            <div class="col-md-4">

                <label class="form-label">
                    PAN Expiry Date <span class="text-danger">*</span>
                </label>

                <input type="date"
                    id="panExpiry"
                    name="pan_expiry"
                    class="form-control"
                    value="{{ old('pan_expiry', $seller->pan_expiry ? date('Y-m-d', strtotime($seller->pan_expiry)) : '') }}">

                @error('pan_expiry')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

            </div>

            <!-- PAN FILE -->
            <div class="col-md-4">

                <label class="form-label">
                    PAN File (PDF) <span class="text-danger">*</span>
                </label>

                <input type="file"
                    name="pan_file"
                    class="form-control"
                    accept="application/pdf">

                @error('pan_file')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

                <small class="text-muted d-block mt-1">
                    Only PDF allowed
                </small>

                {{-- EXISTING FILE --}}
                @if(!empty($seller->pan_file))

                    <div class="mt-2">

                        <a href="{{ asset($seller->pan_file) }}"
                           target="_blank"
                           class="btn btn-sm btn-outline-primary">

                            View Uploaded File

                        </a>

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>





<!-- ================= GST SECTION ================= -->

@php

$gstStatus = strtolower($seller->gst_status ?? 'na');

$gstExpiry = $seller->gst_expiry
    ? \Carbon\Carbon::parse($seller->gst_expiry)
    : null;

$gstDaysLeft = $gstExpiry
    ? $today->diffInDays($gstExpiry, false)
    : null;

@endphp

<div class="card mb-4 border-0 shadow-sm pan-card">

    <div class="card-body">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">

            <div class="d-flex align-items-center gap-2 flex-wrap">

                <h6 class="fw-bold mb-0 pan-heading">
                    GST Details <span class="text-danger">*</span>
                </h6>

                {{-- STATUS --}}
                @if($gstStatus == 'pending')

                    <span class="badge pan-badge bg-warning text-dark">
                        Pending
                    </span>

                @elseif($gstStatus == 'approved')

                    <span class="badge pan-badge bg-success">
                        Approved
                    </span>

                @elseif($gstStatus == 'rejected')

                    <span class="badge pan-badge bg-danger">
                        Rejected
                    </span>

                @else

                    <span class="badge pan-badge bg-secondary">
                        NA
                    </span>

                @endif

            </div>

            <!-- SMALL DOCUMENT ALERT -->
            @if($gstExpiry && $gstDaysLeft < 0)

            <div class="document-expiry-mini-alert">

                <div class="mini-siren">

                    <div class="mini-siren-light">
                        <div class="mini-siren-inner"></div>
                    </div>

                    <div class="mini-siren-base"></div>

                </div>

                <span class="mini-alert-text">
                    <!--<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="8" height="8" x="0" y="0" viewBox="0 0 6.35 6.35" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M3.176.264C1.572.264.264 1.572.264 3.176s1.308 2.91 2.912 2.91a2.914 2.914 0 0 0 2.91-2.91.265.265 0 0 0-.265-.266.265.265 0 0 0-.264.266 2.376 2.376 0 0 1-2.38 2.381 2.38 2.38 0 1 1 0-4.764.265.265 0 0 0 .263-.264.265.265 0 0 0-.264-.265zM4.18.463a.264.264 0 0 0-.098.51l.008.004a.265.265 0 1 0 .205-.488L4.284.483a.264.264 0 0 0-.104-.02zm.861.572a.265.265 0 0 0-.186.453l.006.006a.265.265 0 1 0 .376-.373l-.008-.008a.265.265 0 0 0-.188-.078zm-1.865.288a.265.265 0 0 0-.266.265v1.588a.265.265 0 0 0 .147.236l1.058.53a.265.265 0 0 0 .356-.12.265.265 0 0 0-.12-.355l-.911-.455V1.588a.265.265 0 0 0-.264-.265zm2.444.569a.265.265 0 0 0-.246.368c0 .003.002.005.003.008a.265.265 0 1 0 .49-.201c0-.004-.002-.007-.004-.01a.265.265 0 0 0-.243-.165z" fill="#e32636" opacity="1" data-original="#000000" class=""></path></g></svg>-->
                    Document Expire
                </span>

            </div>

            @endif

        </div>

        <!-- FORM -->
        <div class="row g-3">

            <!-- GST NUMBER -->
            <div class="col-md-4">

                <label class="form-label">
                    GST Number <span class="text-danger">*</span>
                </label>

                <input type="text"
                    name="gst_number"
                    id="gstInput"
                    maxlength="15"
                    class="form-control text-uppercase"
                    value="{{ old('gst_number', $seller->gst_number ?? '') }}"
                    placeholder="22ABCDE1234F1Z5">

                @error('gst_number')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

                <small id="gstError" class="text-danger d-none">
                    Invalid GST format
                </small>

            </div>

            <!-- GST EXPIRY -->
            <div class="col-md-4">

                <label class="form-label">
                    GST Expiry Date <span class="text-danger">*</span>
                </label>

                <input type="date"
                    id="gstExpiry"
                    name="gst_expiry"
                    class="form-control"
                    value="{{ old('gst_expiry', $seller->gst_expiry ? date('Y-m-d', strtotime($seller->gst_expiry)) : '') }}">

                @error('gst_expiry')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

            </div>

            <!-- GST FILE -->
            <div class="col-md-4">

                <label class="form-label">
                    GST Certificate (PDF) <span class="text-danger">*</span>
                </label>

                <input type="file"
                    name="gst_certificate"
                    class="form-control"
                    accept="application/pdf">

                @error('gst_certificate')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

                <small class="text-muted d-block mt-1">
                    Only PDF allowed
                </small>

                {{-- EXISTING FILE --}}
                @if(!empty($seller->gst_certificate))

                    <div class="mt-2">

                        <a href="{{ asset($seller->gst_certificate) }}"
                           target="_blank"
                           class="btn btn-sm btn-outline-primary">

                            View Uploaded File

                        </a>

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>
        </div> <!-- End PAN & GST Tab -->

        <!-- Address & Comm Tab -->
        <div class="tab-pane fade p-2" id="address" role="tabpanel" aria-labelledby="address-tab">

{{-- ================= IEC ================= --}}

<div class="d-flex align-items-center gap-2 flex-wrap mb-3">

    <h6 class="text-primary fw-bold mb-0">
        Import Export Code <span class="text-danger">*</span>
    </h6>

    @php $iecStatus = strtolower($seller->iec_status ?? 'na'); @endphp

    @if($iecStatus == 'pending')

        <span class="badge pan-badge bg-warning text-dark">
            Pending
        </span>

    @elseif($iecStatus == 'approved')

        <span class="badge pan-badge bg-success">
            Approved
        </span>

    @elseif($iecStatus == 'rejected')

        <span class="badge pan-badge bg-danger">
            Rejected
        </span>

    @else

        <span class="badge pan-badge bg-secondary">
            NA
        </span>

    @endif

</div>

<div class="row mb-3">

    <div class="col-md-4">

        <label>Have IEC?</label>

        <select name="has_iec" id="iecToggle" class="form-control">

            <option value="No" {{ old('has_iec',$seller->has_iec)=='No'?'selected':'' }}>
                No
            </option>

            <option value="Yes" {{ old('has_iec',$seller->has_iec)=='Yes'?'selected':'' }}>
                Yes
            </option>

        </select>

    </div>

</div>

{{-- IEC DETAILS --}}
<div id="iecSection" style="{{ old('has_iec',$seller->has_iec)=='Yes'?'':'display:none;' }}">

    <div class="row mb-3">

        <!-- IEC NUMBER -->
        <div class="col-md-4">

            <label>
                IEC Number <span class="text-danger">*</span>
            </label>

            <input 
                type="text" 
                name="iec_code" 
                class="form-control"
                value="{{ old('iec_code',$seller->iec_code ?? '') }}"
                maxlength="10"
                style="text-transform:uppercase"
                pattern="[A-Z0-9]{10}"
                oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g,'')"
                placeholder="Enter IEC (10 characters)"
            >

            @error('iec_code')
                <small class="text-danger">{{ $message }}</small>
            @enderror

        </div>

        <!-- FILE NUMBER -->
        <div class="col-md-4">

            <label>
                File Number <span class="text-danger">*</span>
            </label>

            <input 
                type="text" 
                name="iec_file_number" 
                class="form-control"
                value="{{ old('iec_file_number',$seller->iec_file_number ?? '') }}"
                maxlength="15"
                style="text-transform:uppercase"
                oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g,'')"
            >

            @error('iec_file_number')
                <small class="text-danger">{{ $message }}</small>
            @enderror

        </div>

        <!-- IEC CERTIFICATE -->
        <div class="col-md-4">

            <label>
                IEC Certificate <span class="text-danger">*</span>
            </label>

            <input type="file"
                name="iec_certificate"
                class="form-control"
                accept="application/pdf">

            <small class="text-muted">
                Only PDF file allowed
            </small>

            @error('iec_certificate')
                <small class="text-danger">{{ $message }}</small>
            @enderror

            {{-- EXISTING FILE --}}
            @if(!empty($seller->iec_certificate))

                <div class="mt-2">

                    <a href="{{ asset($seller->iec_certificate) }}"
                       target="_blank"
                       class="btn btn-sm btn-outline-primary">

                        View Uploaded File

                    </a>

                </div>

            @endif

        </div>

    </div>

</div>

<hr>
{{-- ================= BILLING ADDRESS ================= --}}
<h6 class="text-primary">Billing Address <span class="text-danger">*</span></h6>

<div class="row">

<div class="col-md-4">
<label>Country <span class="text-danger">*</span></label>
<select name="billing_country" id="country" class="form-control mb-2" >
    <option value="">Select Country</option>
    @foreach($countries as $country)
        <option value="{{ $country->id }}"
           {{ old('billing_country', $seller->billing_country ?? '') == $country->id ? 'selected' : '' }}>
            {{ $country->name }}
        </option>
    @endforeach
</select>

   @error('billing_country')
    <small class="text-danger">{{ $message }}</small>
@enderror
</div>

<div class="col-md-4">
<label>State <span class="text-danger">*</span></label>
<select name="billing_state" id="state" class="form-control mb-2" >
    <option value="">Select State</option>
</select>

 @error('billing_state')
    <small class="text-danger">{{ $message }}</small>
@enderror
</div>

<div class="col-md-4">
<label>City <span class="text-danger">*</span></label>
<select name="billing_city" id="city" class="form-control mb-2" >
    <option value="">Select City</option>
</select>

 @error('billing_city')
    <small class="text-danger">{{ $message }}</small>
@enderror
</div>

<div class="col-md-4">

<label>ZIP Code <span class="text-danger">*</span></label>

<div id="zipWrapper">

    <select name="billing_zip"
            id="zip"
            class="form-control mb-2">

        <option value="">Select ZIP</option>

        @if(old('billing_zip', $seller->billing_zip ?? ''))
            <option value="{{ old('billing_zip', $seller->billing_zip ?? '') }}" selected>
                {{ old('billing_zip', $seller->billing_zip ?? '') }}
            </option>
        @endif

    </select>

</div>

@error('billing_zip')
    <small class="text-danger">
        {{ $message }}
    </small>
@enderror

</div>

<div class="col-md-12">
<label>Street Address <span class="text-danger">*</span></label>
<textarea 
    name="billing_address" 
    
    class="form-control"
    placeholder="Street Address">{{ old('billing_address', $seller->billing_address ?? '') }}</textarea>
    
     @error('billing_address')
    <small class="text-danger">{{ $message }}</small>
@enderror
</div>

</div>


<h6 class="text-primary mt-4">Contact Address</h6>

<div class="mb-3">
<label class="fw-bold">Same as Billing?</label>

<select id="sameAddress" name="sameAddress" class="form-control">
    <option value="Yes" {{ old('sameAddress', 'Yes') == 'Yes' ? 'selected' : '' }}>Yes</option>
    <option value="No" {{ old('sameAddress') == 'No' ? 'selected' : '' }}>No</option>
</select>
</div>


{{-- CONTACT ADDRESS FIELDS --}}
<div id="contactAddressSection" style="display:none;">

<div class="row">

<div class="col-md-4">
<label>Country <span class="text-danger">*</span></label>
<input name="contact_country"
class="form-control mb-2 contact-required"
placeholder="Country">
</div>

<div class="col-md-4">
<label>State <span class="text-danger">*</span></label>
<input name="contact_state"
class="form-control mb-2 contact-required"
placeholder="State">
</div>

<div class="col-md-4">
<label>District <span class="text-danger">*</span></label>
<input name="contact_district"
class="form-control mb-2 contact-required"
placeholder="District">
</div>

<div class="col-md-4">
<label>City <span class="text-danger">*</span></label>
<input name="contact_city"
class="form-control mb-2 contact-required"
placeholder="City">
</div>

<div class="col-md-4">
<label>ZIP Code <span class="text-danger">*</span></label>
<input name="contact_zip"
class="form-control mb-2 contact-required"
placeholder="ZIP Code">
</div>

<div class="col-md-12">
<label>Street Address <span class="text-danger">*</span></label>
<textarea name="contact_address"
class="form-control contact-required"
placeholder="Street Address"></textarea>
</div>

</div>

</div>

{{-- ================= COMMUNICATION DETAILS ================= --}}
<div class="mt-4">

    <h6 class="text-primary">Communication Details</h6>

    <div class="row">
        {{-- PHONE WITH COUNTRY CODE --}}
        <div class="col-md-6">
            <label>Phone Number <span class="text-danger">*</span></label>

            <div class="d-flex align-items-center gap-2">

                {{-- COUNTRY CODE --}}
                <select name="phone_code" class="form-control" style="max-width:100px;">
                    <option value="+91">+91</option>
                    <option value="+1">+1</option>
                    <option value="+44">+44</option>
                </select>

                {{-- hidden flag --}}
                <input type="hidden" name="phone_verified" id="phoneVerified" value="{{ $seller->phone_verified ?? 0 }}">

                {{-- PHONE --}}
               <input type="text"
                    name="org_mobile"
                    id="phoneInput"
                    class="form-control"
                    maxlength="10"
                    value="{{ old('org_mobile', $seller->org_mobile ?? '') }}"
                    placeholder="Enter phone number">

                {{-- VERIFY BUTTON --}}
                <button type="button"
                    class="btn btn-sm btn-outline-primary"
                    id="sendOtpBtn"
                    @if($seller->phone_verified ?? 0) style="display:none;" @endif>
                    Verify
                </button>

            </div>

            @error('org_mobile')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror

            <small id="phoneStatus"
                class="d-block mt-1 
                {{ $seller->phone_verified ? 'text-success' : 'text-warning' }}">
                {{ $seller->phone_verified ? 'Verified ✔' : 'Pending' }}
            </small>

            {{-- OTP BOX --}}
            <div id="otpBox" style="display:none;" class="mt-2">
                <div class="d-flex align-items-center gap-1">
                    <input type="text" class="form-control text-center otp-digit" maxlength="1" style="width:38px; height:38px; font-weight:bold;" autocomplete="off">
                    <input type="text" class="form-control text-center otp-digit" maxlength="1" style="width:38px; height:38px; font-weight:bold;" autocomplete="off">
                    <input type="text" class="form-control text-center otp-digit" maxlength="1" style="width:38px; height:38px; font-weight:bold;" autocomplete="off">
                    <input type="text" class="form-control text-center otp-digit" maxlength="1" style="width:38px; height:38px; font-weight:bold;" autocomplete="off">

                    <button type="button"
                        class="btn btn-success btn-sm ms-2"
                        id="verifyOtpBtn">
                        Submit
                    </button>
                </div>
            </div>
        </div>


      {{-- EMAIL --}}
<div class="col-md-6">
    <label>Email ID <span class="text-danger">*</span></label>

    <div class="d-flex align-items-center gap-2">

        {{-- hidden flag --}}
        <input type="hidden" name="email_verified" id="emailVerified" value="{{ $seller->email_verified ?? 0 }}">

        {{-- email input --}}
        <input type="email"
            name="org_email"
            id="emailInput"
            class="form-control"
            value="{{ old('org_email', $seller->org_email ?? '') }}"
            placeholder="Enter email">

        {{-- verify button --}}
        <button type="button"
            class="btn btn-sm btn-outline-primary"
            id="sendEmailOtpBtn"
            @if($seller->email_verified ?? 0) style="display:none;" @endif>
            Verify
        </button>

    </div>

    {{-- OTP BOX --}}
    <div id="emailOtpBox" style="display:none;" class="mt-2">
        <div class="d-flex align-items-center gap-1">

            <input type="text" class="form-control text-center email-otp-digit" maxlength="1" style="width:38px; height:38px; font-weight:bold;" autocomplete="off">
            <input type="text" class="form-control text-center email-otp-digit" maxlength="1" style="width:38px; height:38px; font-weight:bold;" autocomplete="off">
            <input type="text" class="form-control text-center email-otp-digit" maxlength="1" style="width:38px; height:38px; font-weight:bold;" autocomplete="off">
            <input type="text" class="form-control text-center email-otp-digit" maxlength="1" style="width:38px; height:38px; font-weight:bold;" autocomplete="off">

            <button type="button"
                class="btn btn-success btn-sm ms-2"
                id="verifyEmailOtpBtn">
                Submit
            </button>

        </div>
    </div>

    {{-- STATUS --}}
    <small id="emailStatus"
        class="d-block mt-1 {{ ($seller->email_verified ?? 0) ? 'text-success' : 'text-warning' }}">
        {{ ($seller->email_verified ?? 0) ? 'Verified ✔' : 'Not Verified' }}
    </small>
</div>

    </div>

</div>


{{-- OTP STATUS --}}
<div class="mb-3">

{{-- Phone --}}
@php $phoneVerified = (int) ($seller->phone_verified ?? 0); @endphp

<span id="phoneVerifiedBadgeContainer">
@if($phoneVerified === 1)
    <span class="badge bg-success">Phone Verified</span>
@else
    <span class="badge bg-warning text-dark">Phone Not Verified</span>
@endif
</span>


{{-- Email --}}
@php $emailVerified = (int) ($seller->email_verified ?? 0); @endphp

<span id="emailVerifiedBadgeContainer">
@if($emailVerified === 1)
    <span class="badge bg-success">Email Verified</span>
@else
    <span class="badge bg-warning text-dark">Email Not Verified</span>
@endif
</span>

</div>

        </div> <!-- End Address & Comm Tab -->
    </div> <!-- End Tab Content -->

<div class="text-end mt-4">

<button class="btn btn-success px-5">
Save Organization
</button>

</div>

</div>
</div>

</form>

</div>

</div>



{{-- ================= RIGHT SIDEBAR ================= --}}
<div class="marketplace-right">

<div class="profile-right">

@include('vshop.seller.partials.profile-progress')

</div>

</div>

</div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<script>
let selectedState  = "{{ old('billing_state', $seller->billing_state ?? '') }}";
let selectedCity   = "{{ old('billing_city', $seller->billing_city ?? '') }}";
</script>




<script>

// ================= RESET WHEN EMAIL CHANGED =================
$('#emailInput').on('input', function(){

    $('#emailVerified').val(0);

    $('#emailStatus')
        .removeClass()
        .addClass('text-warning')
        .html('Not Verified');

    $('#emailOtpBox').hide();
    $('.email-otp-digit').val('');
    
    // Show Verify button again when email is changed
    $('#sendEmailOtpBtn').show();
    
    // Update badge back to Not Verified
    $('#emailVerifiedBadgeContainer').html('<span class="badge bg-warning text-dark">Email Not Verified</span>');
});


// ================= SEND EMAIL OTP =================
$('#sendEmailOtpBtn').click(function(){

    let email = $('#emailInput').val();

    if(!email){
        alert('Enter email');
        return;
    }

    let pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if(!pattern.test(email)){
        alert('Enter valid email');
        return;
    }

    // Clear split inputs
    $('.email-otp-digit').val('');

    $('#emailStatus')
        .removeClass()
        .addClass('text-warning')
        .html('Sending OTP...');

    $.post('/seller/send-email-otp', {
        _token: '{{ csrf_token() }}',
        email: email
    }, function(res){

        if(res.success){

            $('#emailStatus')
                .removeClass()
                .addClass('text-info')
                .html('Sent OTP to your mail');

            // Removed OTP autofill as per requirement

            // ✅ SHOW OTP BOX
            $('#emailOtpBox').fadeIn();

        } else {

            $('#emailStatus')
                .removeClass()
                .addClass('text-danger')
                .html('Failed to send OTP');
        }

    });

});


// ================= VERIFY EMAIL OTP =================
$('#verifyEmailOtpBtn').click(function(){

    let otp = '';
    $('.email-otp-digit').each(function() {
        otp += $(this).val();
    });

    if(otp.length < 4){
        alert('Enter valid OTP');
        return;
    }

    $('#emailStatus')
        .removeClass()
        .addClass('text-warning')
        .html('Verifying...');

    $.post('/seller/verify-email-otp', {
        _token: '{{ csrf_token() }}',
        otp: otp
    }, function(res){

        if(res.success){

            // ✅ UI UPDATE
            $('#emailStatus')
                .removeClass()
                .addClass('text-success')
                .html('Verified ✔');

            $('#emailOtpBox').fadeOut();

            // Hide verify button upon success
            $('#sendEmailOtpBtn').hide();

            // Update badge at bottom
            $('#emailVerifiedBadgeContainer').html('<span class="badge bg-success">Email Verified</span>');

            // ✅ SET VALUE
            $('#emailVerified').val(1);

            // 🔥 IMPORTANT: FORCE SAVE TO DB (FIX YOUR ISSUE)
            $.ajax({
                url: '/seller/org-autosave',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    org_email: $('#emailInput').val(),
                    email_verified: 1
                }
            });

        } else {

            $('#emailStatus')
                .removeClass()
                .addClass('text-danger')
                .html('Invalid OTP');
        }

    });

});

// Shift focus to the next input or previous input for split OTP digit inputs
$(document).on('input', '.email-otp-digit', function() {
    let inputs = $('.email-otp-digit');
    let idx = inputs.index(this);
    if (this.value.length === 1 && idx < inputs.length - 1) {
        inputs.eq(idx + 1).focus();
    }
});
$(document).on('keydown', '.email-otp-digit', function(e) {
    let inputs = $('.email-otp-digit');
    let idx = inputs.index(this);
    if (e.key === 'Backspace' && this.value.length === 0 && idx > 0) {
        inputs.eq(idx - 1).focus();
    }
});

</script>


<script>

$(document).ready(function(){

    // ================= CITY CHANGE =================
    $(document).on('change', '#city', function(){

        let cityName = $("#city option:selected").text().trim();

        if(!cityName || cityName === 'Select City'){

            $('#zip').html(`
                <option value="">Select ZIP</option>
            `);

            return;
        }

        // loading
        $('#zip').html(`
            <option value="">Loading...</option>
        `);

        fetch(`https://api.postalpincode.in/postoffice/${cityName}`)

        .then(res => res.json())

        .then(data => {

            console.log(data);

            if(
                data &&
                data[0] &&
                data[0].Status === 'Success' &&
                data[0].PostOffice
            ){

                let pincodes = [];

                data[0].PostOffice.forEach(function(item){

                    if(item.Pincode && !pincodes.includes(item.Pincode)){
                        pincodes.push(item.Pincode);
                    }

                });

                if(pincodes.length > 0){

                    let options = `
                        <option value="">Select Pincode</option>
                    `;

                    let selectedZip = "{{ old('billing_zip', $seller->billing_zip ?? '') }}";

                    pincodes.forEach(function(pin){

                        let selected = (pin == selectedZip)
                            ? 'selected'
                            : '';

                        options += `
                            <option value="${pin}" ${selected}>
                                ${pin}
                            </option>
                        `;
                    });

                    $('#zip').html(options);

                }else{

                    manualInput();

                }

            }else{

                manualInput();

            }

        })

        .catch(err => {

            console.log(err);

            manualInput();

        });

    });


    // ================= FALLBACK =================
    function manualInput(){

        $('#zipWrapper').html(`
            <input type="text"
                   name="billing_zip"
                   id="zip"
                   class="form-control mb-2"
                   placeholder="Enter ZIP manually"
                   value="{{ old('billing_zip', $seller->billing_zip ?? '') }}">
        `);

    }

});

</script>






{{-- ================= STYLE ================= --}}
<style>

.form-check-label{
cursor:pointer;
font-weight:500;
}

.form-check-input{
    display:inline-block !important;
    width:16px !important;
    height:16px !important;
    opacity:1 !important;
    appearance:auto !important;
}

.logo-wrapper{
    position: relative;
    display: inline-block;
}

.logo-img{
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
}

/* 🔥 PERFECT CROSS POSITION */
.logo-remove-btn{
    position: absolute;
    top: -8px;
    right: -8px;
    background: red;
    color: #fff;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    font-size: 14px;
    text-align: center;
    line-height: 22px;
    cursor: pointer;
}
</style>


<style id="q8w2nd">

/* ================= PAN CARD ================= */

.pan-card{
    border-radius:14px;
    overflow:hidden;
}

.pan-heading{
    color:#0047c3;
    font-size:18px;
}

/* ================= STATUS ================= */

.pan-badge{
    font-size:11px;
    padding:5px 10px;
    border-radius:6px;
    font-weight:600;
}

/* ================= SMALL DOCUMENT ALERT ================= */

/* ================= SMALL DOCUMENT ALERT ================= */

.document-expiry-mini-alert{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:6px;
}

/* ICON */
.mini-siren{
    position:relative;
    display:flex;
    flex-direction:column;
    align-items:center;
}

/* RED TOP */
.mini-siren-light{
    width:12px;
    height:16px;
    background:#e32636;
    border-radius:8px 8px 4px 4px;
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
}

/* WHITE ! */
.mini-siren-inner{
    width:2px;
    height:7px;
    background:#fff;
    border-radius:10px;
    position:relative;
}

.mini-siren-inner::after{
    content:'';
    position:absolute;
    bottom:-3px;
    left:50%;
    transform:translateX(-50%);
    width:2px;
    height:2px;
    background:#fff;
    border-radius:50%;
}

/* GREY BASE */
.mini-siren-base{
    width:16px;
    height:3px;
    background:#4b4b4b;
    border-radius:2px;
    margin-top:2px;
}

/* TEXT */
.mini-alert-text{
    font-size:10px;
    font-weight:600;
    color:#e32636;
    text-align:center;
    line-height:1.2;
}

/* ================= MOBILE ================= */

@media(max-width:768px){

    .pan-heading{
        font-size:16px;
    }

    .document-expiry-mini-alert{
        width:100%;
        justify-content:flex-start;
    }

}

</style>
<style>
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
    </style>
<script>
$(document).ready(function () {

    // ================= COUNTRY → STATE =================
    $('#country').on('change', function () {
        let country_id = $(this).val();

        $('#state').html('<option value="">Loading...</option>');
        $('#city').html('<option value="">Select City</option>');

        if (country_id) {
            $.ajax({
                url: "/seller/organization-states/" + country_id,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    let options = '<option value="">Select State</option>';

                    if (data.length > 0) {
                        $.each(data, function (key, state) {
                            options += `<option value="${state.id}">${state.name}</option>`;
                        });
                    } else {
                        options = '<option value="">No State Found</option>';
                    }

                    $('#state').html(options);
                },
                error: function () {
                    alert('Error fetching states');
                    $('#state').html('<option value="">Select State</option>');
                }
            });
        } else {
            $('#state').html('<option value="">Select State</option>');
            $('#city').html('<option value="">Select City</option>');
        }
    });


    // ================= STATE → CITY =================
    $('#state').on('change', function () {
        let state_id = $(this).val();

        $('#city').html('<option value="">Loading...</option>');

        if (state_id) {
            $.ajax({
                url: "/seller/organization-cities/" + state_id,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    let options = '<option value="">Select City</option>';

                    if (data.length > 0) {
                        $.each(data, function (key, city) {
                            options += `<option value="${city.id}">${city.name}</option>`;
                        });
                    } else {
                        options = '<option value="">No City Found</option>';
                    }

                    $('#city').html(options);
                },
                error: function () {
                    alert('Error fetching cities');
                    $('#city').html('<option value="">Select City</option>');
                }
            });
        } else {
            $('#city').html('<option value="">Select City</option>');
        }
    });

});

function loadStates(country_id, selectedState = null){

    if(!country_id) return;

    $('#state').html('<option>Loading...</option>');

    $.get("/seller/organization-states/" + country_id, function(data){

        let options = '<option value="">Select State</option>';

        $.each(data, function(key, state){

            let selected = (state.id == selectedState) ? 'selected' : '';

            options += `<option value="${state.id}" ${selected}>${state.name}</option>`;
        });

        $('#state').html(options);

        // 🔥 load cities after state
        if(selectedState){
            loadCities(selectedState, selectedCity);
        }
    });
}


function loadCities(state_id, selectedCity = null){

    if(!state_id) return;

    $('#city').html('<option>Loading...</option>');

    $.get("/seller/organization-cities/" + state_id, function(data){

        let options = '<option value="">Select City</option>';

        $.each(data, function(key, city){

            let selected = (city.id == selectedCity) ? 'selected' : '';

            options += `<option value="${city.id}" ${selected}>${city.name}</option>`;
        });

        $('#city').html(options);
    });
}


$(document).ready(function(){

    let country = $('#country').val();

    if(country){
        loadStates(country, selectedState);
    }

});

</script>

<script>

$(document).ready(function(){

function toggleContactAddress(){

    let val = $('#sameAddress').val();

    if(val === 'No'){
        $('#contactAddressSection').slideDown();
        $('.contact-required').prop('required', true);
    }else{
        $('#contactAddressSection').slideUp();
        $('.contact-required').prop('required', false);
    }
}

// run on load
toggleContactAddress();

// on change
$('#sameAddress').on('change', function(){
    toggleContactAddress();
});

});

function toggleCompanySection(){

let val = $('input[name="business_type"]:checked').val();

if(val === 'Individual'){
$('#companySection').hide();
}else{
$('#companySection').show();
}

}

$('input[name="incorporation_date"]').on('change', function(){

    let selectedDate = new Date(this.value);
    let today = new Date();

    today.setHours(0,0,0,0);

    if(selectedDate > today){
        alert('Incorporation date cannot be future date');
        this.value = '';
    }

});

$('#iecToggle').change(function(){
$('#iecSection').toggle(this.value === 'Yes');
});

// run on load
$(document).ready(function(){
toggleCompanySection();
});

// run on change
$('input[name="business_type"]').on('change', function(){
toggleCompanySection();
});

$('#logoInput').on('change', function(){

    let file = this.files[0];

    if(!file){
        $('#logoPreview').addClass('d-none');
        $('#removeLogo').addClass('d-none');
        return;
    }

    let allowed = ['image/jpeg','image/png','image/jpg'];

    if(!allowed.includes(file.type)){
        alert('Only JPG/PNG allowed');
        this.value = '';
        return;
    }

    let reader = new FileReader();

    reader.onload = function(e){

        $('#logoPreview')
            .attr('src', e.target.result)
            .removeClass('d-none');

        $('#removeLogo').removeClass('d-none');
    }

    reader.readAsDataURL(file);

});

// REMOVE LOGO
$('#removeLogo').on('click', function(){

    $('#logoInput').val('');
    $('#logoPreview').attr('src','').addClass('d-none');
    $(this).addClass('d-none');

});



// ================= TRADEMARK TOGGLE =================
function toggleTrademark(){
    let val = $('#trademarkSelect').val();

    if(val === 'Yes'){
        $('#trademarkBox').slideDown();
        $('#trademarkStatusBadge').show();
    }else{
        $('#trademarkBox').slideUp();
        $('#trademarkStatusBadge').hide();
    }
}

// ON LOAD
$(document).ready(function(){
    toggleTrademark();
});

// ON CHANGE
$('#trademarkSelect').on('change', function(){
    toggleTrademark();
});




$(document).ready(function(){

$('#trademarkSelect').change(function(){
$('#trademarkBox').toggle(this.value=='Yes');
});

$('#iecSelect').change(function(){
$('.iecFields').toggle(this.value=='Yes');
});

});
$('#cinInput').on('input', function(){

    let value = this.value.toUpperCase();

    // allow only A-Z and 0-9
    value = value.replace(/[^A-Z0-9]/g, '');
    this.value = value;

    // CIN pattern
    let cinPattern = /^[LU][0-9]{5}[A-Z]{2}[0-9]{4}[A-Z]{3}[0-9]{6}$/;

    if(value.length >= 21 && !cinPattern.test(value)){
        $('#cinError').removeClass('d-none');
    } else {
        $('#cinError').addClass('d-none');
    }

});

$('#trademarkSelect').on('change', function(){

    let val = $(this).val();

    if(val === 'Yes'){
        $('#trademarkBox').show();              // show file upload
        $('#trademarkStatusBadge').show();      // show status
    }else{
        $('#trademarkBox').hide();              // hide file
        $('#trademarkStatusBadge').hide();      // hide status
    }

});

// PAGE LOAD FIX
$(document).ready(function(){

    let selected = $('#trademarkSelect').val();

    if(selected === 'Yes'){
        $('#trademarkBox').show();
        $('#trademarkStatusBadge').show();
    }else{
        $('#trademarkBox').hide();
        $('#trademarkStatusBadge').hide();
    }

});

// PAN VALIDATION (NO SPECIAL CHAR)
$('#panInput').on('input', function(){

    let value = this.value.toUpperCase();

    // allow only A-Z and 0-9
    value = value.replace(/[^A-Z0-9]/g, '');

    this.value = value;

    // PAN format check
    let panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;

    if(value.length === 10 && !panPattern.test(value)){
        $('#panError').removeClass('d-none');
    } else {
        $('#panError').addClass('d-none');
    }

});


// GST VALIDATION (NO SPECIAL CHAR)
$('#gstInput').on('input', function(){

    let value = this.value.toUpperCase();

    // allow only A-Z and 0-9
    value = value.replace(/[^A-Z0-9]/g, '');

    this.value = value;

    // GST format check (basic)
    let gstPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{3}$/;

    if(value.length === 15 && !gstPattern.test(value)){
        $('#gstError').removeClass('d-none');
    } else {
        $('#gstError').addClass('d-none');
    }

});

$('#logoInput').on('change', function(){

    let file = this.files[0];

    if(!file){
        $('#logoPreview').hide();
        $('#removeLogo').hide();
        return;
    }

    let allowed = ['image/jpeg','image/png','image/jpg'];

    if(!allowed.includes(file.type)){
        alert('Only JPG/PNG allowed');
        this.value = '';
        return;
    }

    let reader = new FileReader();

    reader.onload = function(e){
        $('#logoPreview').attr('src', e.target.result).show();
        $('#removeLogo').show();
    }

    reader.readAsDataURL(file);

});


// PHONE VALIDATION
$('#phoneInput').on('input', function(){
    this.value = this.value.replace(/\D/g,'').slice(0,10);
    
    // Reset phone verification flag
    $('#phoneVerified').val(0);

    $('#phoneStatus')
        .removeClass()
        .addClass('text-warning')
        .text('Pending');

    $('#otpBox').hide();
    $('.otp-digit').val('');
    
    // Show Verify button again when phone is changed
    $('#sendOtpBtn').show();
    
    // Update badge back to Not Verified
    $('#phoneVerifiedBadgeContainer').html('<span class="badge bg-warning text-dark">Phone Not Verified</span>');
});

$('#sendOtpBtn').click(function(){

    let phone = $('#phoneInput').val();

    if(phone.length !== 10){
        alert('Enter valid phone number');
        return;
    }

    // Clear split inputs
    $('.otp-digit').val('');

    $('#phoneStatus')
        .removeClass()
        .addClass('text-warning')
        .text('Sending OTP...');

    $.post('/seller/send-otp', {
        _token: '{{ csrf_token() }}',
        phone: phone
    }, function(res){

        if(res.success){

            $('#phoneStatus')
                .removeClass()
                .addClass('text-info')
                .text('OTP Sent ✔');

            // Autofill split OTP inputs
            if(res.otp) {
                let otpStr = res.otp.toString();
                $('.otp-digit').each(function(index) {
                    $(this).val(otpStr[index] || '');
                });
            }

            // ✅ SHOW OTP BOX
            $('#otpBox').fadeIn();

        } else {

            $('#phoneStatus')
                .removeClass()
                .addClass('text-danger')
                .text('Failed to send OTP');
        }

    });

});

$('#verifyOtpBtn').click(function(){

    let otp = '';
    $('.otp-digit').each(function() {
        otp += $(this).val();
    });

    if(otp.length < 4){
        alert('Enter valid OTP');
        return;
    }

    $('#phoneStatus')
        .removeClass()
        .addClass('text-warning')
        .text('Verifying...');

    $.post('/seller/verify-otp', {
        _token: '{{ csrf_token() }}',
        otp: otp
    }, function(res){

        if(res.success){

            $('#phoneStatus')
                .removeClass()
                .addClass('text-success')
                .text('Verified ✔');

            $('#otpBox').fadeOut();

            // Hide verify button upon success
            $('#sendOtpBtn').hide();

            // Update badge at bottom
            $('#phoneVerifiedBadgeContainer').html('<span class="badge bg-success">Phone Verified</span>');

            // ✅ SET VALUE
            $('#phoneVerified').val(1);

            // 🔥 IMPORTANT: FORCE SAVE TO DB
            $.ajax({
                url: '/seller/org-autosave',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    org_mobile: $('#phoneInput').val(),
                    phone_verified: 1
                }
            });

        } else {

            $('#phoneStatus')
                .removeClass()
                .addClass('text-danger')
                .text('Invalid OTP');
        }

    });

});

// Shift focus to the next input or previous input for split OTP digit inputs
$(document).on('input', '.otp-digit', function() {
    let inputs = $('.otp-digit');
    let idx = inputs.index(this);
    if (this.value.length === 1 && idx < inputs.length - 1) {
        inputs.eq(idx + 1).focus();
    }
});
$(document).on('keydown', '.otp-digit', function(e) {
    let inputs = $('.otp-digit');
    let idx = inputs.index(this);
    if (e.key === 'Backspace' && this.value.length === 0 && idx > 0) {
        inputs.eq(idx - 1).focus();
    }
});
$('#verifyEmailBtn').click(function(){

    let email = $('#emailInput').val();

    if(!email){
        alert('Enter email');
        return;
    }

    let pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if(!pattern.test(email)){
        alert('Enter valid email');
        return;
    }

    $('#emailStatus')
        .removeClass()
        .addClass('text-warning')
        .html('Verifying...');

    $.post('/seller/verify-email', {
        _token: '{{ csrf_token() }}',
        email: email
    }, function(res){

        if(res.success){

            // 🔥 DIRECT VERIFIED
            $('#emailStatus')
                .removeClass()
                .addClass('text-success')
                .html('Verified ✔');

            $('#emailVerified').val(1);

        } else {

            $('#emailStatus')
                .removeClass()
                .addClass('text-danger')
                .html('Failed');

        }

    });

});


$('#orgForm').on('submit', function(){

    let phoneVerified = $('#phoneStatus').text().includes('Verified');
    let emailVerified = $('#emailVerified').val() == 1;

  
    return true; // allow submit
});




// DELETE
$('#removeLogo').click(function(){
    $('#logoInput').val('');
    $('#logoPreview').hide();
    $('#removeLogo').hide();
});

</script>


<script>
$(document).ready(function(){

    function validateDate(input){
        let selected = $(input).val();

        if(!selected) return;

        // Convert to comparable format
        let selectedDate = new Date(selected + "T00:00:00");
        let today = new Date();
        today.setHours(0,0,0,0);

        if(selectedDate < today){
            alert('Expiry date cannot be earlier than today');
            $(input).val('');
        }
    }

    // PAN
    $('#panExpiry').on('change blur', function(){
        validateDate(this);
    });

    // GST
    $('#gstExpiry').on('change blur', function(){
        validateDate(this);
    });

    // Auto-switch to tab containing errors
    @if ($errors->any())
        var firstErrorTab = null;
        @if ($errors->has('application') || $errors->has('legal_name') || $errors->has('hide_price') || $errors->has('logo') || $errors->has('logo_trademark') || $errors->has('trademark_file'))
            firstErrorTab = '#basic-tab';
        @elseif ($errors->has('business_type') || $errors->has('cin') || $errors->has('incorporation_date') || $errors->has('company_certificate'))
            firstErrorTab = '#company-tab';
        @elseif ($errors->has('pan_number') || $errors->has('pan_expiry') || $errors->has('pan_file') || $errors->has('gst_number') || $errors->has('gst_expiry') || $errors->has('gst_certificate'))
            firstErrorTab = '#tax-tab';
        @else
            firstErrorTab = '#address-tab';
        @endif

        if (firstErrorTab) {
            var tabEl = document.querySelector(firstErrorTab);
            if (tabEl) {
                var tab = new bootstrap.Tab(tabEl);
                tab.show();
            }
        }
    @endif

});
</script>

<script>
    // 🔥 AUTO SAVE ON INPUT CHANGE
$('#orgForm').on('input change', 'input, select, textarea', function(){

    let formData = {};

    $('#orgForm').find('input, select, textarea').each(function(){

        let name = $(this).attr('name');
        if(!name) return;

        // checkbox handle
        if($(this).attr('type') === 'checkbox'){
            formData[name] = $(this).is(':checked');
        }

        // radio handle
        else if($(this).attr('type') === 'radio'){
            if($(this).is(':checked')){
                formData[name] = $(this).val();
            }
        }

        // normal input
        else{
            formData[name] = $(this).val();
        }

    });

    localStorage.setItem('org_form_data', JSON.stringify(formData));

});

// 🔥 RESTORE DATA ON PAGE LOAD
$(document).ready(function(){

    let saved = localStorage.getItem('org_form_data');

    if(saved){

        let data = JSON.parse(saved);

        for(let key in data){

            let el = $(`[name="${key}"]`);

            if(!el.length) continue;

            // checkbox
            if(el.attr('type') === 'checkbox'){
                el.prop('checked', data[key]);
            }

            // radio
            else if(el.attr('type') === 'radio'){
                $(`[name="${key}"][value="${data[key]}"]`).prop('checked', true);
            }

            // others
            else{
                el.val(data[key]);
            }

        }

    }

});

$('#orgForm').on('submit', function(){
    localStorage.removeItem('org_form_data');
});


// 🔥 FIX: STATE + CITY RESTORE AFTER AUTOSAVE
$(document).ready(function(){

    let saved = localStorage.getItem('org_form_data');

    if(saved){

        let data = JSON.parse(saved);

        let country = data['billing_country'];
        let state   = data['billing_state'];
        let city    = data['billing_city'];

        if(country){

            // load states first
            loadStates(country, state);

            // load cities after delay (because ajax)
            setTimeout(function(){
                if(state){
                    loadCities(state, city);
                }
            }, 500);

        }

    }

});
</script>

<script>
$(document).ready(function(){

    let timer;

    function autoSave(){

        clearTimeout(timer);

        timer = setTimeout(function(){

            let form = $('#orgForm')[0];
            let formData = new FormData(form);
            formData.set('email_verified', $('#emailVerified').val());

            // ❌ remove file inputs (important)
            formData.delete('company_certificate');
            formData.delete('pan_file');
            formData.delete('gst_certificate');
            formData.delete('iec_certificate');
            formData.delete('logo');
            formData.delete('trademark_file');

            $.ajax({
                url: '/seller/org-autosave',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                success: function(res){

                    if(res.success){

                        // UI feedback
                        if(!$('#autosaveMsg').length){
                            $('body').append('<div id="autosaveMsg" style="position:fixed;bottom:20px;right:20px;background:#28a745;color:#fff;padding:8px 15px;border-radius:5px;font-size:12px;z-index:9999;">Auto Saved ✔</div>');
                        }

                        $('#autosaveMsg').fadeIn();

                        setTimeout(()=>{
                            $('#autosaveMsg').fadeOut();
                        },1500);
                    }
                },

                error: function(){
                    console.log('Auto save failed');
                }
            });

        }, 1000); // debounce

    }

    // 🔥 trigger on all inputs
    $('#orgForm').on('input change', 'input, select, textarea', function(){
        autoSave();
    });

});
</script>

<script>

$(document).ready(function(){

    // ================= INPUT / TEXTAREA =================
    $(document).on('input', 'input, textarea', function(){

        removeError($(this));

    });

    // ================= SELECT =================
    $(document).on('change', 'select', function(){

        removeError($(this));

    });

    // ================= FILE INPUT =================
    $(document).on('change', 'input[type="file"]', function(){

        removeError($(this));

    });

    // ================= RADIO =================
    $(document).on('change', 'input[type="radio"]', function(){

        let name = $(this).attr('name');

        $(`input[name="${name}"]`)
            .closest('.form-check-inline')
            .find('.text-danger')
            .fadeOut();

    });


    // ================= COMMON FUNCTION =================
    function removeError(el){

        // remove red border
        el.removeClass('is-invalid');

        // hide nearest error
        el.closest('div').find('.text-danger').fadeOut();

        // hide small tag errors
        el.closest('div').find('small.text-danger').fadeOut();

        // hide span errors
        el.closest('div').find('span.text-danger').fadeOut();

    }

});

</script>

@endsection
