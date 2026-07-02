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
</style>

@section('content')

<div class="container-fluid">

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <h4 class="fw-bold">
            Sales Overview & Trends
        </h4>
    </div>
</div>

{{-- DATA --}}
@php
$totalRevenue = collect($salesTrend)->sum('total');
$totalOrdersCount = $totalOrders;
$aov = $totalOrdersCount > 0 ? $totalRevenue / $totalOrdersCount : 0;

/* Proper previous calculation (last period same size) */
$half = ceil(count($salesTrend)/2);
$current = collect($salesTrend)->slice($half);
$previous = collect($salesTrend)->slice(0, $half);

$currentTotal = $current->sum('total');
$previousTotal = $previous->sum('total');

$growth = $previousTotal > 0 ? (($currentTotal - $previousTotal)/$previousTotal)*100 : 0;
@endphp

{{-- KPI --}}
<div class="row mb-4">

    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <small>Total Revenue</small>
            <h4 class="text-success">₹ {{ number_format($totalRevenue,2) }}</h4>
            <small class="{{ $growth >= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format($growth,1) }}% vs previous period
            </small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <small>Total Orders</small>
            <h4>{{ $totalOrdersCount }}</h4>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <small>Avg Order Value</small>
            <h4>₹ {{ number_format($aov,2) }}</h4>
        </div>
    </div>

</div>

{{-- TREND GRAPH --}}
<div class="card shadow-sm mb-4 p-3">
    <h6 class="fw-bold mb-3">Sales & Revenue Trend</h6>
    <canvas id="trendChart" height="100"></canvas>
</div>

{{-- COMPARISON GRAPH --}}
<div class="card shadow-sm p-3">
    <h6 class="fw-bold mb-3">Comparative Analysis</h6>
    <canvas id="compareChart" height="100"></canvas>
</div>

</div>

{{-- CHART --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

let trend = @json($salesTrend);

/* TREND CHART */
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trend.map(t => t.date),
        datasets: [
            {
                label: 'Revenue',
                data: trend.map(t => t.total),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.1)',
                fill: true,
                tension: 0.4
            }
        ]
    },
    options: {
        animation: false,
        plugins: { legend: { display: true } }
    }
});

/* COMPARISON */
let half = Math.ceil(trend.length/2);

let current = trend.slice(half);
let previous = trend.slice(0, half);

new Chart(document.getElementById('compareChart'), {
    type: 'bar',
    data: {
        labels: current.map(t => t.date),
        datasets: [
            {
                label: 'Current',
                data: current.map(t => t.total),
                backgroundColor: '#198754'
            },
            {
                label: 'Previous',
                data: previous.map(t => t.total),
                backgroundColor: '#6c757d'
            }
        ]
    },
    options: {
        animation: false,
        plugins: { legend: { display: true } }
    }
});

</script>

@endsection