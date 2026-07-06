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
    .b
</style>

@section('content')

@php
$seller = $seller ?? auth()->guard('seller')->user();
@endphp

<div class="container-fluid py-4">
<div class="marketplace-wrapper">

{{-- ================= LEFT CONTENT ================= --}}
<div class="marketplace-left">

@include('vshop.seller.partials.onboarding-header')

<div class="card shadow-sm border-0">
<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-4">

<h5 class="text-primary mb-0">
Seller Agreement
</h5>

<a href="{{ route('seller.agreement') }}"
class="btn btn-primary btn-sm">
<i class="fa fa-plus me-1"></i>
Add / Edit Agreement
</a>

</div>

{{-- ✅ FIXED CONDITION --}}
@if($seller->agreement_file || $seller->agreement_accepted_at)

<table class="table table-bordered align-middle">

<tr>
<th width="220">Agreement Type</th>
<td>{{ $seller->agreement_type ?? '-' }}</td>
</tr>

<tr>
<th>Agreement File</th>
<td>
@if($seller->agreement_file)
<a href="{{ asset($seller->agreement_file) }}" target="_blank" class="text-primary">
<i class="fa fa-file-pdf me-1"></i> View Agreement
</a>
@else
-
@endif
</td>
</tr>

<tr>
<th>Accepted At</th>
<td>
@if($seller->agreement_accepted_at)
{{ \Carbon\Carbon::parse($seller->agreement_accepted_at)->format('d M Y h:i A') }}
@else
-
@endif
</td>
</tr>

<tr>
<th>Status</th>
<td>
<span class="badge bg-success">
Agreement Submitted
</span>
</td>
</tr>

<tr>
<th>Action</th>

<td class="d-flex gap-2">

{{-- EDIT --}}
<a href="{{ route('seller.agreement') }}"
class="btn btn-warning btn-sm">
<i class="fa fa-edit"></i>
</a>

{{-- DELETE --}}
<form method="POST"
action="{{ route('seller.agreement.delete') }}">
@csrf
@method('DELETE')

<button type="submit"
onclick="return confirm('Delete agreement?')"
class="btn btn-danger btn-sm">
<i class="fa fa-trash"></i>
</button>
</form>

</td>
</tr>

</table>

@else

<div class="alert alert-warning mb-0">
No agreement submitted yet. Please upload seller agreement.
</div>

@endif

</div>
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

@endsection
