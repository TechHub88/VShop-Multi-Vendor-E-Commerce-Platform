@extends('vshop.seller.main')
<style>
.top-back-heading-sec h5 {
    color: #0f172a !important;
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

@section('content')

@php
use Carbon\Carbon;

$lastUpdate = $product->updated_at
    ? Carbon::parse($product->updated_at)->timezone('Asia/Kolkata')->format('d M Y, h:i A')
    : 'N/A';
@endphp


<div class="container-fluid py-3">

<div class="marketplace-wrapper">

{{-- ================= LEFT ================= --}}
<div class="marketplace-left">

{{-- HEADER --}}
<div class="d-flex align-items-center top-back-heading-sec mb-3">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <h5 class="mb-0 d-flex align-items-center gap-2"
            style="
                color:#0d6efd;
                font-weight:600;
            ">
            Product Edit History (Audit Log)
        </h5>
    </div>

{{-- SUMMARY --}}
<div class="card shadow-sm border-0 mb-3">
<div class="card-body">

<div class="d-flex justify-content-between">
    <div>
        <strong>Product:</strong> {{ $product->name }}
    </div>

    <div>
        <span class="badge bg-info text-dark">
            Last Update: {{ $lastUpdate }}
        </span>
    </div>
</div>

</div>
</div>


{{-- TIMELINE --}}
<div class="card shadow-sm border-0">

<div class="card-body">

<h6 class="text-primary mb-3">Activity Timeline</h6>

@if(count($logs))

<div class="timeline">

@foreach($logs as $log)

@php
$time = Carbon::parse($log->created_at)
    ->timezone('Asia/Kolkata')
    ->format('d M Y, h:i A');

$old = $log->old_value ?? [];
$new = $log->new_value ?? [];
@endphp

<div class="timeline-item">

<div class="timeline-dot"></div>

<div class="timeline-content">

<div class="d-flex justify-content-between mb-2">
    <strong>{{ ucfirst($log->module) }} Updated</strong>
    <small class="text-muted">{{ $time }}</small>
</div>

@if(is_array($new))

@foreach($new as $key => $value)

@php
$oldVal = $old[$key] ?? '-';
$newVal = $value;
@endphp

@if($oldVal != $newVal)

<div class="change-row">

    <div class="field">
        {{ ucfirst(str_replace('_',' ',$key)) }}
    </div>

    <div class="values">

        <span class="old">
            {{ is_array($oldVal) ? json_encode($oldVal) : $oldVal }}
        </span>

        <i class="bi bi-arrow-right mx-2"></i>

        <span class="new">
            {{ is_array($newVal) ? json_encode($newVal) : $newVal }}
        </span>

    </div>

</div>

@endif

@endforeach

@endif

</div>

</div>

@endforeach

</div>

@else

<div class="alert alert-light">
    No activity logs found
</div>

@endif

</div>
</div>

</div>


{{-- ================= RIGHT ================= --}}
<div class="marketplace-right">

@include('vshop.seller.partials.product-progress', [
    'product' => $product
])

</div>

</div>
</div>


{{-- ================= STYLE ================= --}}
<style>

.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #ddd;
}

.timeline-item {
    position: relative;
    margin-bottom: 18px;
}

.timeline-dot {
    position: absolute;
    left: -2px;
    width: 12px;
    height: 12px;
    background: #0d6efd;
    border-radius: 50%;
}

.timeline-content {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #eee;
}

.change-row {
    margin-bottom: 10px;
}

.field {
    font-weight: 600;
    font-size: 13px;
}

.values {
    font-size: 13px;
}

.old {
    color: #dc3545;
}

.new {
    color: #198754;
}

</style>

@endsection
