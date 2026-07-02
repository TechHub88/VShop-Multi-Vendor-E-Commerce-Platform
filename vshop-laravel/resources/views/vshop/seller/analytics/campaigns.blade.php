@extends('vshop.seller.main')

@section('content')

<style>
body{background:#f4f6f9;}

.dash-card{
    background:#fff;
    border-radius:12px;
    padding:14px;
    border:1px solid #e5e7eb;
    box-shadow:0 2px 6px rgba(0,0,0,0.05);
}

.dash-title{
    font-size:13px;
    font-weight:700;
    color:#111;
    margin-bottom:8px;
}

.kpi{
    font-size:22px;
    font-weight:700;
}

.chart{height:240px;}
.small-chart{height:190px;}

.dash-card{
    transition: 0.3s;
}
.dash-card:hover {
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
.dash-card div {
    width: 100% !important;
}
.dash-card div canvas {
    width: 100% !important;
}
.top-back-heading-sec h5 {
        color: #0f172a !important;
    }
</style>

<div class="container-fluid py-4">
<div class="d-flex top-back-heading-sec mb-4">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <h5 class="fw-bold">Campaign Analytics Dashboard</h5>
</div>


@php
$totalRevenue = $campaigns->sum('revenue');
$totalBudget = $campaigns->sum('budget');
$totalProfit = $campaigns->sum('profit');
$dsRevenue = $campaigns->sum('ds_revenue');
$dsProfit = $campaigns->sum('ds_profit');
@endphp

{{-- ================= TOP ================= --}}
<div class="row g-3">

<div class="col-md-8">
<div class="dash-card">
<div class="dash-title">Revenue Trend</div>
<div id="trendChart" class="chart"></div>
</div>
</div>

<div class="col-md-4">

<div class="dash-card mb-2">
<div class="dash-title">Total Revenue</div>
<div class="kpi text-success">₹ {{ number_format($totalRevenue) }}</div>
</div>

<div class="dash-card mb-2">
<div class="dash-title">Total Budget</div>
<div class="kpi text-primary">₹ {{ number_format($totalBudget) }}</div>
</div>

<div class="dash-card">
<div class="dash-title">Total Profit</div>
<div class="kpi text-info">₹ {{ number_format($totalProfit) }}</div>
</div>

</div>
</div>

{{-- ================= DISTRIBUTION ================= --}}
<div class="row g-3 mt-1">

<div class="col-md-4"><div class="dash-card"><div class="dash-title">Revenue Distribution</div><div id="pieChart" class="small-chart"></div></div></div>

<div class="col-md-4"><div class="dash-card"><div class="dash-title">Profit Distribution</div><div id="profitChart" class="small-chart"></div></div></div>

<div class="col-md-4"><div class="dash-card"><div class="dash-title">Campaign Status</div><div id="statusChart" class="small-chart"></div></div></div>

</div>

{{-- ================= PERFORMANCE ================= --}}
<div class="row g-3 mt-1">

<div class="col-md-6"><div class="dash-card"><div class="dash-title">Campaign ROI</div><div id="roiChart" class="chart"></div></div></div>

<div class="col-md-6"><div class="dash-card"><div class="dash-title">Campaign Comparison</div><div id="barChart" class="chart"></div></div></div>

</div>

{{-- ================= CUSTOMER INSIGHTS ================= --}}
<div class="row g-3 mt-1">

<div class="col-md-4"><div class="dash-card"><div class="dash-title">Customer Segmentation</div><div id="segmentChart" class="small-chart"></div></div></div>

<div class="col-md-4"><div class="dash-card"><div class="dash-title">Repeat vs New</div><div id="repeatChart" class="small-chart"></div></div></div>

<div class="col-md-4"><div class="dash-card"><div class="dash-title">Churn vs Loyalty</div><div id="churnChart" class="small-chart"></div></div></div>

</div>

{{-- ================= AOV + RATES ================= --}}
<div class="row g-3 mt-1">

<div class="col-md-4"><div class="dash-card"><div class="dash-title">Average Order Value</div><div id="aovChart" class="small-chart"></div></div></div>

<div class="col-md-4"><div class="dash-card"><div class="dash-title">Refund Rate</div><div id="refundChart" class="small-chart"></div></div></div>

<div class="col-md-4"><div class="dash-card"><div class="dash-title">Cancellation Rate</div><div id="cancelChart" class="small-chart"></div></div></div>

</div>

{{-- ================= PROMO + SEASON ================= --}}
<div class="row g-3 mt-1">

<div class="col-md-6"><div class="dash-card"><div class="dash-title">Promo Code Usage</div><div id="promoChart" class="chart"></div></div></div>

<div class="col-md-6"><div class="dash-card"><div class="dash-title">Seasonal Trends</div><div id="seasonChart" class="chart"></div></div></div>

</div>

{{-- ================= GEO ================= --}}
<div class="row g-3 mt-1">

<div class="col-md-12"><div class="dash-card"><div class="dash-title">Top Cities Performance</div><div id="cityChart" class="chart"></div></div></div>

</div>

{{-- ================= DROPSHIPPING ================= --}}
<div class="row g-3 mt-1">

<div class="col-md-5">
    <div class="dash-card mb-3"><div class="dash-title">Dropshipping Revenue</div><div class="kpi text-success">₹ {{ number_format($dsRevenue) }}</div></div>
    <div class="dash-card"><div class="dash-title">Dropshipping Profit</div><div class="kpi text-primary">₹ {{ number_format($dsProfit) }}</div></div>
</div>

<!--<div class="col-md-3"></div>-->

<div class="col-md-7"><div class="dash-card"><div class="dash-title">Top Dropshipping Products</div><div id="dsProductChart" class="chart"></div></div></div>

</div>

{{-- ================= SUPPLIER ================= --}}
<div class="row g-3 mt-1">

<div class="col-md-12"><div class="dash-card"><div class="dash-title">Supplier Performance</div><div id="supplierChart" class="chart"></div></div></div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

<script>
let campaigns = @json($campaigns);

/* TREND */
echarts.init(document.getElementById('trendChart')).setOption({
xAxis:{type:'category',data:campaigns.map(x=>x.name)},
yAxis:{type:'value'},
tooltip:{},
series:[{type:'line',smooth:true,data:campaigns.map(x=>x.revenue),areaStyle:{}}]
});

/* PIE */
echarts.init(document.getElementById('pieChart')).setOption({
series:[{type:'pie',radius:['50%','70%'],data:campaigns.map(x=>({value:x.revenue,name:x.name}))}]
});

/* PROFIT */
echarts.init(document.getElementById('profitChart')).setOption({
xAxis:{type:'category',data:campaigns.map(x=>x.name)},
yAxis:{type:'value'},
series:[{type:'bar',data:campaigns.map(x=>x.profit)}]
});

/* STATUS */
echarts.init(document.getElementById('statusChart')).setOption({
series:[{type:'pie',radius:'70%',data:[
{value:campaigns.filter(x=>x.profit>0).length,name:'Profit'},
{value:campaigns.filter(x=>x.profit<=0).length,name:'Loss'}
]}]
});

/* ROI */
echarts.init(document.getElementById('roiChart')).setOption({
xAxis:{type:'category',data:campaigns.map(x=>x.name)},
yAxis:{type:'value'},
series:[{type:'bar',data:campaigns.map(x=>((x.profit/x.budget)*100).toFixed(2))}]
});

/* BAR */
echarts.init(document.getElementById('barChart')).setOption({
xAxis:{type:'category',data:campaigns.map(x=>x.name)},
yAxis:{type:'value'},
series:[{type:'bar',data:campaigns.map(x=>x.revenue)}]
});

/* SEGMENT */
echarts.init(document.getElementById('segmentChart')).setOption({
series:[{type:'pie',radius:['45%','70%'],data:[
{value:40,name:'High'},
{value:35,name:'Mid'},
{value:25,name:'Low'}
]}]
});

/* REPEAT */
echarts.init(document.getElementById('repeatChart')).setOption({
series:[{type:'pie',radius:['40%','70%'],data:[
{value:70,name:'New'},
{value:30,name:'Repeat'}
]}]
});

/* CHURN */
echarts.init(document.getElementById('churnChart')).setOption({
series:[{type:'pie',radius:'70%',data:[
{value:20,name:'Churn'},
{value:80,name:'Loyal'}
]}]
});

/* AOV */
echarts.init(document.getElementById('aovChart')).setOption({
    series: [{
        type: 'gauge',
        startAngle: 180, endAngle: 0,
        min: 0, max: 10000,
        radius: '100%',
        center: ['50%', '75%'],
        pointer: { show: true, length: '60%', width: 4 },
        progress: { show: true, width: 12, itemStyle: {color: '#4e73df'} },
        axisLine: { lineStyle: { width: 12, color: [[1, '#eaecf4']] } },
        axisTick: { show: false },
        splitLine: { show: false },
        axisLabel: { show: false },
        detail: { fontSize: 22, offsetCenter: [0, '20%'], formatter: '₹ {value}', color: '#333', fontWeight: '800' },
        data: [{value: 2500}]
    }]
});

/* REFUND */
echarts.init(document.getElementById('refundChart')).setOption({
    series: [{
        type: 'gauge',
        startAngle: 180, endAngle: 0,
        min: 0, max: 10,
        radius: '100%',
        center: ['50%', '75%'],
        pointer: { show: true, length: '60%', width: 4 },
        progress: { show: true, width: 12, itemStyle: {color: '#e74a3b'} },
        axisLine: { lineStyle: { width: 12, color: [[1, '#eaecf4']] } },
        axisTick: { show: false },
        splitLine: { show: false },
        axisLabel: { show: false },
        detail: { fontSize: 22, offsetCenter: [0, '20%'], formatter: '{value}%', color: '#333', fontWeight: '800' },
        data: [{value: 3.1}]
    }]
});

/* CANCEL */
echarts.init(document.getElementById('cancelChart')).setOption({
    series: [{
        type: 'gauge',
        startAngle: 180, endAngle: 0,
        min: 0, max: 10,
        radius: '100%',
        center: ['50%', '75%'],
        pointer: { show: true, length: '60%', width: 4 },
        progress: { show: true, width: 12, itemStyle: {color: '#f6c23e'} },
        axisLine: { lineStyle: { width: 12, color: [[1, '#eaecf4']] } },
        axisTick: { show: false },
        splitLine: { show: false },
        axisLabel: { show: false },
        detail: { fontSize: 22, offsetCenter: [0, '20%'], formatter: '{value}%', color: '#333', fontWeight: '800' },
        data: [{value: 2.4}]
    }]
});

/* PROMO */
echarts.init(document.getElementById('promoChart')).setOption({
xAxis:{type:'category',data:campaigns.map(x=>x.name)},
yAxis:{type:'value'},
series:[{type:'bar',data:campaigns.map(x=>x.promo_usage || 0)}]
});

/* SEASON */
echarts.init(document.getElementById('seasonChart')).setOption({
xAxis:{type:'category',data:['Jan','Feb','Mar','Apr','May','Jun']},
yAxis:{type:'value'},
series:[{type:'line',smooth:true,data:[12000,15000,18000,22000,26000,30000]}]
});

/* CITY */
echarts.init(document.getElementById('cityChart')).setOption({
xAxis:{type:'category',data:['Delhi','Mumbai','Bangalore','Hyderabad']},
yAxis:{type:'value'},
series:[{type:'bar',data:[32000,28000,25000,21000]}]
});

/* DS PRODUCT */
echarts.init(document.getElementById('dsProductChart')).setOption({
xAxis:{type:'category',data:campaigns.map(x=>x.name)},
yAxis:{type:'value'},
series:[{type:'bar',data:campaigns.map(x=>x.ds_profit || 0)}]
});

/* SUPPLIER */
echarts.init(document.getElementById('supplierChart')).setOption({
xAxis:{type:'category',data:['Supplier A','Supplier B','Supplier C']},
yAxis:{type:'value'},
series:[{type:'bar',data:[15000,22000,18000]}]
});
</script>

@endsection