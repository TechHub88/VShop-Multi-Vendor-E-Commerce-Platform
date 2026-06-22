@extends('layouts.vertical',['title'=>'Commission Settings'])

@section('content')

<style>
    .table th {
        background: #004392 !important;
        color: #fff !important;
    }
    .btn-primary,
    .btn-warning,
    .btn-danger,
    .btn-info {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-light border {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
    .btn-secondary,
    .btn-outline-secondary {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .nav-tabs .nav-link.active {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
    .reset-btn {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
        margin-left: 12px;
    }
    .reset-btn:hover {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
</style>

<div class="container-fluid pt-2">

<h4 class="mb-4 fw-bold">
{{ session('admin_panel') == 'travel' ? 'Inquiry Settings (VTravel)' : 'Commission Settings (VShop)' }}
</h4>

<div class="card shadow-sm">
<div class="card-body">

@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif

{{-- ================= VSHOP (COMMISSION) ================= --}}
@if(session('admin_panel') != 'travel')

<form method="POST" action="{{ route('admin.commission.save') }}">
@csrf

<div class="row g-4">

<div class="col-md-4">
<label class="form-label fw-semibold">Platform Commission (%)</label>
<input type="number" step="0.01" name="commission_percent"
class="form-control"
value="{{ $setting->commission_percent ?? 10 }}" required>
</div>

<div class="col-md-4">
<label class="form-label fw-semibold">Referral Share (%)</label>
<input type="number" step="0.01" name="referral_percent"
class="form-control"
value="{{ $setting->referral_percent ?? 30 }}" required>
</div>

<div class="col-md-2 d-flex align-items-end">
<button class="btn btn-success w-100">Save Settings</button>
<button class="btn reset-btn ">Reset</button>
</div>

</div>
</form>

<hr class="my-4">

<div class="alert alert-info">
<strong>Example:</strong><br>
Order ₹1000 → Commission ₹100 → Seller ₹900 → Referral ₹30 → Platform ₹70
</div>

@endif


{{-- ================= VTRAVEL (INQUIRY SYSTEM) ================= --}}
@if(session('admin_panel') == 'travel')

<form method="POST" action="{{ route('admin.inquiry.settings.save') }}">
@csrf

<div class="row g-4">

<div class="col-md-4">
<label class="form-label fw-semibold">Default Inquiry Cost (₹)</label>
<input type="number" step="0.01" name="default_inquiry_price"
class="form-control"
value="{{ $inquirySetting->default_price ?? 1 }}" required>

<small class="text-muted">Per lead charge (₹1, ₹0.50 etc.)</small>
</div>

<div class="col-md-4">
<label class="form-label fw-semibold">Minimum Recharge Amount (₹)</label>
<input type="number" step="0.01" name="min_recharge"
class="form-control"
value="{{ $inquirySetting->min_recharge ?? 5000 }}" required>

<small class="text-muted">Example: ₹5000 minimum recharge</small>
</div>

<div class="col-md-2 d-flex align-items-end">
<button class="btn btn-primary w-100">Save Settings</button>
</div>

</div>

</form>

<hr class="my-4">

<div class="alert alert-warning">

<strong>How it works:</strong>

<ul class="mb-0">
<li>Seller recharges wallet (₹5000)</li>
<li>Per inquiry charge deducted (₹1 etc.)</li>
<li>Invalid inquiry → refund</li>
<li>Balance runs out → seller must recharge again</li>
</ul>

</div>

@endif

</div>
</div>

</div>

@endsection