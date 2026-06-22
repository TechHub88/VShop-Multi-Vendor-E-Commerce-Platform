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
    .top-back-heading-sec h5 {
        color: #0f172a !important;
    }
    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        display: flex !important;
        align-items: center;
        padding: 9px !important;
    }
    .btn-success {
        background: #233d78 !important;
        color: #fff !important;
        border: 0 !important;
    }
</style>

@section('content')

<div class="container-fluid py-4">
<div class="d-flex top-back-heading-sec mb-4">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <h5 class="fw-bold">Seller Profile</h5>
</div>


{{-- ================= ONBOARDING ================= --}}
<div class="card mb-4 shadow-sm border-0">
<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold">Seller Onboarding</h5>

    <div>
        @if($seller->status == 'approved')
            <span class="badge bg-success">Approved</span>
        @elseif($seller->status == 'pending')
            <span class="badge bg-warning text-dark">Under Review</span>
        @else
            <span class="badge bg-secondary">Incomplete</span>
        @endif

        <a href="{{ route('seller.staff.create') }}" class="btn btn-success btn-sm ms-2">
            + Staff
        </a>
    </div>
</div>

@php
    $completed = collect($progress)->filter()->count();
    $total = count($progress);
    $percent = ($completed / $total) * 100;
@endphp

<div class="progress mb-4" style="height:8px;">
    <div class="progress-bar bg-success" style="width: {{ $percent }}%"></div>
</div>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

@foreach($progress as $step => $done)
<div class="text-center">
    <div class="rounded-circle {{ $done ? 'bg-success text-white' : 'bg-light' }}"
         style="width:40px;height:40px;line-height:40px;">
        {{ $loop->iteration }}
    </div>
    <small>{{ ucfirst($step) }}</small><br>
    <small class="{{ $done ? 'text-success' : 'text-warning' }}">
        {{ $done ? 'Done' : 'Pending' }}
    </small>
</div>
@endforeach

</div>

</div>
</div>

{{-- ================= MAIN CARD ================= --}}
<div class="card shadow-sm">
<div class="card-body">

<ul class="nav nav-tabs mb-4">
<li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#basic">Basic</a></li>
<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#organization">Organization</a></li>
<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#contact">Contact</a></li>
<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#bank">Bank</a></li>
<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#payment">Payment</a></li>
<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#agreement">Agreement</a></li>
</ul>

<div class="tab-content">

{{-- ================= BASIC (EDITABLE) ================= --}}
<div class="tab-pane fade show active" id="basic">

<form id="basicForm" method="POST" action="{{ route('seller.basic.update') }}">
@csrf

<div class="d-flex justify-content-end mb-2">
    <button type="button" id="editBtn" class="btn btn-primary btn-sm">Edit</button>
    <button type="submit" id="saveBtn" class="btn btn-success btn-sm d-none">Save</button>
</div>

<div class="row">
<div class="col-md-3 text-center">
<img src="{{ $seller->logo ? asset($seller->logo) : asset('vtravelassets/images/avatar.png') }}"
class="img-fluid rounded mb-3 shadow-sm" width="120">
</div>

<div class="col-md-9">

<table class="table table-bordered">
<tr>
    <th>Name</th>
    <td>
        <span class="view-mode">{{ $seller->name }}</span>
        <input type="text" name="name" value="{{ $seller->name }}" class="form-control edit-mode d-none">
    </td>
</tr>

<tr>
    <th>Email</th>
    <td>
        <span class="view-mode">{{ $seller->email }}</span>
        <input type="email" name="email" value="{{ $seller->email }}" class="form-control edit-mode d-none">
    </td>
</tr>

<tr>
    <th>Mobile</th>
    <td>
        <span class="view-mode">{{ $seller->mobile }}</span>
        <input type="text" name="mobile" value="{{ $seller->mobile }}" class="form-control edit-mode d-none">
    </td>
</tr>

<tr>
<th>Status</th>
<td>
<span class="badge bg-success">{{ $seller->status }}</span>
</td>
</tr>

</table>

</div>
</div>

</form>

</div>

{{-- ================= READ ONLY SECTIONS ================= --}}
<div class="tab-pane fade" id="organization">
<table class="table table-bordered">
<tr><th>Legal Name</th><td>{{ $seller->legal_name }}</td></tr>
<tr><th>PAN</th><td>{{ $seller->pan_number }}</td></tr>
<tr><th>GST</th><td>{{ $seller->gst_number }}</td></tr>
</table>
</div>

<div class="tab-pane fade" id="contact">Read Only</div>
<div class="tab-pane fade" id="bank">Read Only</div>
<div class="tab-pane fade" id="payment">Read Only</div>
<div class="tab-pane fade" id="agreement">Read Only</div>

</div>
</div>
</div>

</div>

{{-- ================= SCRIPT ================= --}}
<script>
document.getElementById('editBtn').addEventListener('click', function(){

    document.querySelectorAll('.view-mode').forEach(el => el.classList.add('d-none'));
    document.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('d-none'));

    document.getElementById('editBtn').classList.add('d-none');
    document.getElementById('saveBtn').classList.remove('d-none');

});
</script>

@endsection