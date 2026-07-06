@extends('vshop.seller.main')

@section('content')

<style>
.profile-card{
    border-radius:16px;
    border:none;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}
.label{
    font-size:12px;
    color:#6b7280;
}
.value{
    font-weight:600;
}
.section{
    margin-bottom:25px;
}
.address-box{
    background:#f9fafb;
    padding:12px;
    border-radius:10px;
    margin-bottom:10px;
}
.nav-btn {
    border: 1px solid #004392 !important;
    color: #004392;
    background: transparent;
    border-radius: 5px;
    font-size: 4px;
    width: 35px;
    height: 38px;
    padding: 6px 1px;
}
.top-back-heading-sec {
    align-items: center;
    gap: 10px;
}
.card {
    border: 1px solid rgb(201 208 218) !important;
}
.top-back-heading-sec h5 {
    color: #0f172a !important;
}
</style>

<div class="container-fluid py-4">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex top-back-heading-sec">
        <a href="{{ route('seller.customer.list') }}" class="btn btn-light border nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h5 class="fw-bold mb-0">Customer Profile</h5>
    </div>

    
</div>

<div class="row">

{{-- LEFT --}}
<div class="col-lg-4">
<div class="card profile-card p-4 text-center">

<img src="{{ $customer->profile_picture ? asset($customer->profile_picture) : 'https://ui-avatars.com/api/?name='.$customer->name }}"
width="100" class="rounded-circle mb-3">

<h5 class="fw-bold">{{ $customer->name }}</h5>

<span class="badge bg-{{ $customer->is_active ? 'success' : 'danger' }}">
{{ $customer->is_active ? 'Active' : 'Inactive' }}
</span>

<hr>

<div class="text-start">

<div class="mb-2">
<span class="label">Email</span><br>
<span class="value">{{ $customer->email }}</span>
</div>

<div class="mb-2">
<span class="label">Phone</span><br>
<span class="value">{{ $customer->phone }}</span>
</div>

<div class="mb-2">
<span class="label">Company</span><br>
<span class="value">{{ $customer->company_name ?? '-' }}</span>
</div>

<div class="mb-2">
<span class="label">GSTIN</span><br>
<span class="value">{{ $customer->gstin ?? '-' }}</span>
</div>

</div>

</div>
</div>

{{-- RIGHT --}}
<div class="col-lg-8">

{{-- BASIC --}}
<div class="card profile-card p-4 section">
<h6 class="fw-bold mb-3">Basic Information</h6>

<div class="row">

<div class="col-md-4 mb-3">
<span class="label">Gender</span><br>
<span class="value">{{ $customer->gender ?? '-' }}</span>
</div>

<div class="col-md-4 mb-3">
<span class="label">DOB</span><br>
<span class="value">{{ $customer->dob ?? '-' }}</span>
</div>

<div class="col-md-4 mb-3">
<span class="label">Type</span><br>
<span class="value">{{ $customer->type ?? '-' }}</span>
</div>

</div>
</div>


{{-- 🔥 BILLING ADDRESS --}}
<div class="card profile-card p-4 section">
<h6 class="fw-bold mb-3">Billing Address</h6>

@if($billingAddresses->count())
    @foreach($billingAddresses as $addr)
    <div class="address-box">
        <b>{{ $addr->name }}</b><br>
        {{ $addr->address_line1 }}<br>
        {{ $addr->city }}, {{ $addr->state }} - {{ $addr->pincode }}<br>
        {{ $addr->country }}<br>
        📞 {{ $addr->phone }}
    </div>
    @endforeach
@else
    <span class="text-muted">No Billing Address</span>
@endif

</div>


{{-- 🔥 DELIVERY ADDRESS --}}
<div class="card profile-card p-4 section">
<h6 class="fw-bold mb-3">Delivery Address</h6>

@if($deliveryAddresses->count())
    @foreach($deliveryAddresses as $addr)
    <div class="address-box">
        <b>{{ $addr->name }}</b><br>
        {{ $addr->address_line1 }}<br>
        {{ $addr->city }}, {{ $addr->state }} - {{ $addr->pincode }}<br>
        {{ $addr->country }}<br>
        📞 {{ $addr->phone }}
    </div>
    @endforeach
@else
    <span class="text-muted">No Delivery Address</span>
@endif

</div>


{{-- LOCATION --}}
<div class="card profile-card p-4 section">
<h6 class="fw-bold mb-3">Geo Location</h6>

<div class="row">

<div class="col-md-6">
<span class="label">Latitude</span><br>
<span class="value">{{ $customer->latitude ?? '-' }}</span>
</div>

<div class="col-md-6">
<span class="label">Longitude</span><br>
<span class="value">{{ $customer->longitude ?? '-' }}</span>
</div>

</div>
</div>

</div>

</div>

</div>

@endsection
