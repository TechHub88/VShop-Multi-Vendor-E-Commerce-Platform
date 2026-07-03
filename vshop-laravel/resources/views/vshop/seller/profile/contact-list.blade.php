@extends('vshop.seller.main')
<style>
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
</style>

@section('content')

@php
$seller = auth()->guard('seller')->user();
$contacts = $seller->contact_persons ?? [];
@endphp

<div class="container-fluid py-4">

<div class="marketplace-wrapper">

<div class="marketplace-left">

@include('vshop.seller.partials.onboarding-header')

{{-- ================= PAGE HEADER ================= --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
<div class="d-flex top-back-heading-sec ">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <div>
    <h5 class="fw-bold text-dark mb-1">
    Contact & KYC Details
    </h5>
    
    <p class="text-muted mb-0">
    Manage seller contact verification details
    </p>
    </div>
</div>


<a href="{{ route('seller.contact') }}"
   class="btn btn-primary">

    <i class="bi bi-plus-circle me-1"></i>
    Add New Contact

</a>

</div>

{{-- SUCCESS --}}
@if(session('success'))

<div class="alert alert-success alert-dismissible fade show shadow-sm">

    {{ session('success') }}

    <button type="button"
            class="btn-close"
            data-bs-dismiss="alert"></button>

</div>

@endif

{{-- ERROR --}}
@if(session('error'))

<div class="alert alert-danger alert-dismissible fade show shadow-sm">

    {{ session('error') }}

    <button type="button"
            class="btn-close"
            data-bs-dismiss="alert"></button>

</div>

@endif

{{-- ================= ORGANIZATION DETAILS ================= --}}
<div class="card border-0 shadow-sm mb-4">

<div class="card-header bg-white border-bottom">

<h5 class="mb-0 fw-bold text-primary">
    <i class="bi bi-buildings me-2"></i>
    Organization Details
</h5>

</div>

<div class="card-body p-0">

<div class="table-responsive">

<table class="table table-bordered align-middle mb-0 org-table">

<tr>
<th width="250">Organization Name</th>
<td>{{ $seller->org_name ?? '-' }}</td>
</tr>

<tr>
<th>Mobile</th>
<td>{{ $seller->org_mobile ?? '-' }}</td>
</tr>

<tr>
<th>Email</th>
<td>{{ $seller->org_email ?? '-' }}</td>
</tr>

</table>

</div>

</div>

</div>

{{-- ================= CONTACT LIST ================= --}}
@if(!empty($contacts) && count($contacts) > 0)

@foreach($contacts as $index => $c)

@php

$countryName = \App\Models\Country::where('id',$c['address']['country'] ?? null)->value('name');

$stateName = \App\Models\State::where('id',$c['address']['state'] ?? null)->value('name');

$cityName = \App\Models\City::where('id',$c['address']['city'] ?? null)->value('name');

@endphp

<div class="card border-0 shadow-sm mb-4 contact-card">

{{-- HEADER --}}
<div class="card-header bg-white border-bottom py-3">

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

<div>

<h5 class="mb-1 fw-bold text-dark">
Contact {{ $index + 1 }}
</h5>

<small class="text-muted">
KYC & Contact Information
</small>

</div>

<div class="d-flex align-items-center gap-2">

<a href="{{ route('seller.contact') }}?edit={{ $index }}"
   class="btn btn-warning btn-sm px-3">

    <i class="bi bi-pencil-square me-1"></i>
    Edit

</a>

<form action="{{ route('seller.contact.delete',$index) }}"
      method="POST"
      class="m-0"
      onsubmit="return confirm('Are you sure you want to delete this contact?')">

    @csrf
    @method('DELETE')

    <button type="submit"
            class="btn btn-danger btn-sm px-3">

        <i class="bi bi-trash me-1"></i>
        Delete

    </button>

</form>

</div>

</div>

</div>

{{-- BODY --}}
<div class="card-body p-0">

<div class="table-responsive">

<table class="table table-bordered align-middle mb-0 custom-table">

{{-- NAME --}}
<tr>
<th width="250">Name</th>
<td>{{ $c['name'] ?? '-' }}</td>
</tr>

{{-- MOBILE --}}
<tr>
<th>Mobile</th>
<td>{{ $c['mobile'] ?? '-' }}</td>
</tr>

{{-- EMAIL --}}
<tr>
<th>Email</th>
<td>{{ $c['email'] ?? '-' }}</td>
</tr>

{{-- PHOTO --}}
<tr>

<th>Photo</th>

<td>

@if(!empty($c['photo']) && file_exists(public_path($c['photo'])))

<img src="{{ asset($c['photo']) }}"
     width="70"
     height="70"
     class="rounded border object-fit-cover">

@else

<span class="text-muted">
No Photo
</span>

@endif

</td>

</tr>

{{-- PAN NUMBER --}}
<tr>
<th>PAN Number</th>
<td>{{ $c['pan'] ?? '-' }}</td>
</tr>

{{-- PAN FILE --}}
<tr>

<th>PAN File</th>

<td>

@if(!empty($c['pan_file']) && file_exists(public_path($c['pan_file'])))

<a href="{{ asset($c['pan_file']) }}"
   target="_blank"
   class="btn btn-outline-primary btn-sm">

    <i class="bi bi-file-earmark-pdf me-1"></i>
    View PAN

</a>

@else

<span class="text-muted">
No PAN File
</span>

@endif

</td>

</tr>

{{-- PAN STATUS --}}
<tr>

<th>PAN Status</th>

<td>

<span class="badge rounded-pill px-3 py-2
@if(($c['pan_status'] ?? '') == 'pending')
bg-warning text-dark

@elseif(($c['pan_status'] ?? '') == 'approved')
bg-success

@elseif(($c['pan_status'] ?? '') == 'rejected')
bg-danger

@else
bg-secondary
@endif">

{{ ucfirst($c['pan_status'] ?? 'NA') }}

</span>

</td>

</tr>

{{-- AADHAAR NUMBER --}}
<tr>
<th>Aadhaar Number</th>
<td>{{ $c['aadhaar'] ?? '-' }}</td>
</tr>

{{-- AADHAAR FILE --}}
<tr>

<th>Aadhaar File</th>

<td>

@if(!empty($c['aadhaar_file']) && file_exists(public_path($c['aadhaar_file'])))

<a href="{{ asset($c['aadhaar_file']) }}"
   target="_blank"
   class="btn btn-outline-primary btn-sm">

    <i class="bi bi-file-earmark-pdf me-1"></i>
    View Aadhaar

</a>

@else

<span class="text-muted">
No Aadhaar File
</span>

@endif

</td>

</tr>

{{-- AADHAAR STATUS --}}
<tr>

<th>Aadhaar Status</th>

<td>

<span class="badge rounded-pill px-3 py-2
@if(($c['aadhaar_status'] ?? '') == 'pending')
bg-warning text-dark

@elseif(($c['aadhaar_status'] ?? '') == 'approved')
bg-success

@elseif(($c['aadhaar_status'] ?? '') == 'rejected')
bg-danger

@else
bg-secondary
@endif">

{{ ucfirst($c['aadhaar_status'] ?? 'NA') }}

</span>

</td>

</tr>

{{-- COUNTRY --}}
<tr>
<th>Country</th>
<td>{{ $countryName ?? '-' }}</td>
</tr>

{{-- STATE --}}
<tr>
<th>State</th>
<td>{{ $stateName ?? '-' }}</td>
</tr>

{{-- CITY --}}
<tr>
<th>City</th>
<td>{{ $cityName ?? '-' }}</td>
</tr>

{{-- STREET --}}
<tr>
<th>Street Address</th>
<td>{{ $c['address']['street'] ?? '-' }}</td>
</tr>

{{-- ADDRESS STATUS --}}
<tr>

<th>Address Status</th>

<td>

<span class="badge rounded-pill px-3 py-2
@if(($c['address']['status'] ?? '') == 'pending')
bg-warning text-dark

@elseif(($c['address']['status'] ?? '') == 'approved')
bg-success

@elseif(($c['address']['status'] ?? '') == 'rejected')
bg-danger

@else
bg-secondary
@endif">

{{ ucfirst($c['address']['status'] ?? 'NA') }}

</span>

</td>

</tr>

</table>

</div>

</div>

</div>

@endforeach

@else

{{-- EMPTY --}}
<div class="card border-0 shadow-sm">

<div class="card-body text-center py-5">

<div class="mb-4">

<i class="bi bi-person-x"
   style="font-size:70px;color:#adb5bd;"></i>

</div>

<h4 class="fw-bold text-muted mb-2">
No Contact Data Found
</h4>

<p class="text-muted mb-4">
You have not added any contact details yet.
</p>

<a href="{{ route('seller.contact') }}"
   class="btn btn-primary">

    <i class="bi bi-plus-circle me-1"></i>
    Add Contact

</a>

</div>

</div>

@endif

</div>

{{-- RIGHT SIDEBAR --}}
<div class="marketplace-right">

@include('vshop.seller.partials.profile-progress')

</div>

</div>

</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
      rel="stylesheet">

<style>

.contact-card{
border-radius:14px;
overflow:hidden;
transition:all .2s ease;
}

.contact-card:hover{
transform:translateY(-2px);
box-shadow:0 6px 20px rgba(0,0,0,.08);
}

.custom-table th{
background:#f8fafc;
font-weight:600;
color:#334155;
width:250px;
}

.custom-table td{
background:#fff;
color:#111827;
}

.custom-table th,
.custom-table td{
padding:14px 16px;
vertical-align:middle;
}

.org-table th{
background:#f8fafc;
width:250px;
}

.object-fit-cover{
object-fit:cover;
}

.badge{
font-size:12px;
font-weight:600;
}

.btn{
border-radius:8px;
}

.card{
border-radius:14px;
overflow:hidden;
}

@media(max-width:767px){

.custom-table th{
width:140px;
font-size:13px;
}

.custom-table td{
font-size:13px;
}

}

</style>

@endsection