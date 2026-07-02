@extends('vshop.seller.main')

<style>
.card{
    transition: 0.3s;
}
.card:hover {
    transform: translateY(-4px);
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
.nav-btn{
        border:1px solid #004392;
        color:#004392;
        background:transparent;
        border-radius:5px;
        font-size:14px;
        width:35px;
        height:38px;
    }
    
    .top-back-heading-sec{
        align-items:center;
        gap:10px;
    }
    .top-back-heading-sec h5 {
        color: #0f172a !important;
    }
    .chart-box div {
        width: 100% !important;
        height: 100% !important;
    }
    .chart-box div canvas {
        width: 100% !important;
        height: 100% !important;
    }
    .card {
        border: 1px solid rgb(201 208 218) !important;
    }
</style>

@section('content')

<div class="container-fluid py-4">
<div class="d-flex top-back-heading-sec mb-4">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <h5 class="fw-bold">
        Conversion Funnel Analytics
    </h5>
</div>


@php
$conversionRate = $visits > 0 ? ($orders / $visits) * 100 : 0;
$cartPercent = $visits > 0 ? ($cartAbandon / $visits) * 100 : 0;
$orderPercent = $cartAbandon > 0 ? ($orders / $cartAbandon) * 100 : 0;
@endphp

{{-- ================= KPI ================= --}}
<div class="row mb-4">

@foreach([
    ['Visitors', $visits],
    ['Cart Users', $cartAbandon],
    ['Orders', $orders],
    ['Conversion', number_format($conversionRate, 2) . '%']
] as $k)
<div class="col-md-3">
<div class="card p-3 text-center shadow-sm border-0">
<small>{{ $k[0] }}</small>
<h4 class="fw-bold">{{ $k[1] }}</h4>
</div>
</div>
@endforeach

</div>

{{-- ================= 4 GRAPH GRID ================= --}}
<div class="row">

{{-- 1. LINE CHART --}}
<div class="col-md-6 mb-4">
<div class="card p-3 shadow-sm border-0">
<h6 class="fw-bold mb-3">Visitor to Buyer Trend</h6>
<div id="lineChart" style="height:280px;"></div>
</div>
</div>

{{-- 2. FUNNEL --}}
<div class="col-md-6 mb-4">
<div class="card p-3 shadow-sm border-0">
<h6 class="fw-bold mb-3">Drop-Off Funnel</h6>
<div id="funnelChart" style="height:280px;"></div>
</div>
</div>

{{-- 3. RECOVERY --}}
<div class="col-md-6 mb-4">
<div class="card p-3 shadow-sm border-0">
<h6 class="fw-bold mb-3">Cart Recovery Strategy</h6>
<div id="recoveryChart" style="height:280px;"></div>
</div>
</div>

{{-- 4. CHECKOUT BAR --}}
<div class="col-md-6 mb-4">
<div class="card p-3 shadow-sm border-0">
<h6 class="fw-bold mb-3">Checkout Friction</h6>
<div id="barChart" style="height:280px;"></div>
</div>
</div>

</div>

</div>

{{-- ================= ECHART ================= --}}
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

let visits = {{ $visits }};
let cart = {{ $cartAbandon }};
let orders = {{ $orders }};

/* ================= LINE ================= */
let lineChart = echarts.init(document.getElementById('lineChart'));

lineChart.setOption({
    tooltip: { trigger: 'axis' },
    xAxis: { type: 'category', data: ['Jan','Feb','Mar','Apr','May','Jun'] },
    yAxis: { type: 'value' },
    series: [
        {
            name: 'Visitors',
            type: 'line',
            smooth: true,
            data: [120,200,150,280,300,350],
            areaStyle: {}
        },
        {
            name: 'Orders',
            type: 'line',
            smooth: true,
            data: [20,40,30,60,80,90],
            areaStyle: {}
        }
    ]
});

/* ================= FUNNEL ================= */
let funnelChart = echarts.init(document.getElementById('funnelChart'));

funnelChart.setOption({
    tooltip: { trigger: 'item' },
    series: [{
        type: 'funnel',
        left: '10%',
        width: '80%',
        label: { show: true, position: 'inside' },
        data: [
            { value: visits, name: 'Visitors' },
            { value: cart, name: 'Cart' },
            { value: orders, name: 'Orders' }
        ]
    }]
});

/* ================= RECOVERY ================= */
let recoveryChart = echarts.init(document.getElementById('recoveryChart'));

recoveryChart.setOption({
    tooltip: { trigger: 'axis' },
    xAxis: { type: 'category', data: ['Potential','Abandoned','Email','WhatsApp','Recovered'] },
    yAxis: { type: 'value' },
    series: [{
        type: 'bar',
        data: [100,70,10,15,55]
    }]
});

/* ================= BAR ================= */
let barChart = echarts.init(document.getElementById('barChart'));

barChart.setOption({
    tooltip: { trigger: 'axis' },
    xAxis: { type: 'category', data: ['Login','Address','Payment','Review'] },
    yAxis: { type: 'value' },
    series: [{
        type: 'bar',
        data: [15,50,25,10],
        itemStyle: {
            borderRadius: [6,6,0,0]
        }
    }]
});

/* ================= RESPONSIVE ================= */
window.addEventListener('resize', function(){
    lineChart.resize();
    funnelChart.resize();
    recoveryChart.resize();
    barChart.resize();
});

});
</script>

@endsection