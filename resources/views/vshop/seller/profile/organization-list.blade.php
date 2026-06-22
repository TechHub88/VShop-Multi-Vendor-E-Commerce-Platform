@extends('vshop.seller.main')
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
    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        display: flex !important;
        align-items: center;
        padding: 9px !important;
    }
    .btn-primary i {
        font-size: 16px;
        margin-right: 8px !important;
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

@php
$seller = $seller ?? auth()->guard('seller')->user();
@endphp

<div class="container-fluid py-4">
<div class="d-flex top-back-heading-sec mb-4">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
</div>

<div class="marketplace-wrapper">

<div class="marketplace-left">

@include('vshop.seller.partials.onboarding-header')
<div class="card shadow-sm border-0">
<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-4">

<h5 class="text-primary mb-0">
Organization Details
</h5>

@if($seller->legal_name)

    {{-- EDIT BUTTON --}}
    <a href="{{ route('seller.organization') }}"
       class="btn btn-primary btn-sm">
        <i class="fa fa-edit me-1"></i>
        Edit Organization
    </a>

@else

    {{-- ADD BUTTON --}}
    <a href="{{ route('seller.organization') }}"
       class="btn btn-success btn-sm">
        <i class="fa fa-plus me-1"></i>
        Add Organization
    </a>

@endif

</div>

@if($seller->legal_name)

<table class="table table-bordered">

<tr><th>Application</th><td>{{ $seller->application }}</td></tr>
<tr><th>Legal Name</th><td>{{ $seller->legal_name }}</td></tr>
<tr><th>Business Type</th><td>{{ $seller->business_type }}</td></tr>

<tr><th>Hide Price</th><td>{{ $seller->hide_price }}</td></tr>

<tr><th>CIN</th><td>{{ $seller->cin }}</td></tr>
<tr><th>Incorporation Date</th><td>{{ $seller->incorporation_date }}</td></tr>

<tr><th>PAN Number</th><td>{{ $seller->pan_number }}</td></tr>
<tr><th>PAN Expiry</th><td>{{ $seller->pan_expiry }}</td></tr>

<tr><th>GST Number</th><td>{{ $seller->gst_number }}</td></tr>
<tr><th>GST Expiry</th><td>{{ $seller->gst_expiry }}</td></tr>

<tr><th>IEC</th><td>{{ $seller->has_iec }}</td></tr>
<tr><th>IEC Code</th><td>{{ $seller->iec_code }}</td></tr>

<tr><th>Phone</th><td>{{ $seller->phone }}</td></tr>
<tr><th>Email</th><td>{{ $seller->email }}</td></tr>

<tr><th>Billing Address</th>
<td>
{{ $seller->billing_address }}<br>
ZIP: {{ $seller->billing_zip }}
</td>
</tr>

<tr><th>Contact Address</th>
<td>
{{ $seller->contact_address ?? '-' }}
</td>
</tr>

<tr><th>Logo</th>
<td>
@if($seller->logo)
<img src="{{ asset($seller->logo) }}" width="80">
@else -
@endif
</td>
</tr>

<tr>
<th>Action</th>
<td>

<a href="{{ route('seller.organization') }}"
class="btn btn-warning btn-sm">
Edit
</a>

<form method="POST"
action="{{ route('seller.organization.delete') }}"
class="d-inline">
@csrf
@method('DELETE')

<button type="submit"
onclick="return confirm('Delete all organization data?')"
class="btn btn-danger btn-sm">
Delete
</button>

</form>

</td>
</tr>

</table>

@else

<div class="alert alert-warning">
No organization data found.
</div>

@endif

</div>
</div>

</div>

<div class="marketplace-right">
@include('vshop.seller.partials.profile-progress')
</div>

</div>
</div>

@endsection