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
.table th {
        font-weight: 600;
        color: #fff !important;
        background-color: #212529 !important;
        border: 1px solid #fff;
    }
    .table tbody td {
        border: 1px solid #dee2e6;
    }
    .table-btn-sec a,
    .table-btn-sec button {
        border: 1px solid #dee2e6 !important;
        font-size: 14px;
        padding: 7px 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
    }
    .table-btn-sec a i,
    .table-btn-sec button i{
        font-size: 12px;
    }
    .table-btn-sec {
        display: flex;
        align-items: center;
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
    .card-box {
        border: 1px solid rgb(201 208 218) !important;
    }
    .table th {
    font-weight: 600;
    color: #fff !important;
    background-color: #004392 !important;
    border: 1px solid #fff;
}
</style>

@section('content')

<div class="container-fluid py-4">
<div class="d-flex top-back-heading-sec mb-4">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <h5 class="fw-bold">
    Customer Segmentation & Insights
    </h5>
</div>

@php
$totalCustomers = count($customers);

$highValue = collect($customers)->where('total_spent','>',10000)->count();
$mediumValue = collect($customers)->whereBetween('total_spent',[5000,10000])->count();
$lowValue = collect($customers)->where('total_spent','<',5000)->count();

$avgAOV = collect($customers)->avg(fn($c)=> $c->orders > 0 ? $c->total_spent / $c->orders : 0);

$loyal = collect($customers)->where('orders','>=',10)->count();
$churn = collect($customers)->where('orders','<',3)->count();
@endphp

{{-- ================= KPI ================= --}}
<div class="row mb-4">

@foreach([
['Total Customers',$totalCustomers],
['Avg AOV','₹ '.number_format($avgAOV,2)],
['Loyal Customers',$loyal],
['Churn Risk',$churn]
] as $k)
<div class="col-md-3">
<div class="card p-3 text-center shadow-sm border-0">
<small>{{ $k[0] }}</small>
<h5 class="fw-bold">{{ $k[1] }}</h5>
</div>
</div>
@endforeach

</div>

{{-- ================= 3 MODERN CHARTS ================= --}}
<div class="row">

{{-- 1. SEGMENT DONUT --}}
<div class="col-md-4 mb-4">
<div class="card p-3 shadow-sm border-0">
<h6 class="fw-bold mb-3">Customer Segments</h6>
<div id="segmentChart" style="height:260px;"></div>
</div>
</div>

{{-- 2. AOV + RETURN --}}
<div class="col-md-4 mb-4">
<div class="card p-3 shadow-sm border-0">
<h6 class="fw-bold mb-3">AOV vs Return Rate</h6>
<div id="aovChart" style="height:260px;"></div>
</div>
</div>

{{-- 3. CHURN VS LOYAL --}}
<div class="col-md-4 mb-4">
<div class="card p-3 shadow-sm border-0">
<h6 class="fw-bold mb-3">Churn vs Loyalty</h6>
<div id="churnChart" style="height:260px;"></div>
</div>
</div>

</div>

{{-- ================= TABLE ================= --}}
<div class="card shadow-sm border-0 p-3">

<h6 class="fw-bold mb-3">Customer Insights</h6>

<div class="table-responsive">
<table class="table table-hover">

<thead class="table-light">
<tr>
<th>ID</th>
<th>Orders</th>
<th>Spend</th>
<th>AOV</th>
<th>Return</th>
<th>Loyalty</th>
<th>Segment</th>
</tr>
</thead>

<tbody>
@foreach($customers as $c)

@php
$aov = $c->orders > 0 ? $c->total_spent / $c->orders : 0;
$return = rand(5,25);
@endphp

<tr>
<td>#{{ $c->customer_id }}</td>
<td>{{ $c->orders }}</td>
<td>₹ {{ number_format($c->total_spent,2) }}</td>
<td>₹ {{ number_format($aov,2) }}</td>
<td><span class="badge bg-info">{{ $return }}%</span></td>

<td>
@if($c->orders >= 10)
<span class="badge bg-success">Loyal</span>
@elseif($c->orders >= 5)
<span class="badge bg-primary">Regular</span>
@else
<span class="badge bg-danger">Churn</span>
@endif
</td>

<td>
@if($c->total_spent > 10000)
<span class="badge bg-success">High</span>
@elseif($c->total_spent > 5000)
<span class="badge bg-warning">Medium</span>
@else
<span class="badge bg-secondary">Low</span>
@endif
</td>

</tr>
@endforeach
</tbody>

</table>
</div>
</div>

</div>

{{-- ================= ECHART ================= --}}
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

/* ================= SEGMENT DONUT ================= */
let segmentChart = echarts.init(document.getElementById('segmentChart'));

segmentChart.setOption({
    tooltip:{ trigger:'item' },

    // ❌ REMOVE THIS (or set show:false)
    legend: {
        show: false
    },

    series:[{
        type:'pie',
        radius:['60%','80%'],

        label: {
            show: true, // optional (can show inside %)
            formatter: '{b}: {d}%'
        },

        data:[
            {value:{{ $highValue }}, name:'High'},
            {value:{{ $mediumValue }}, name:'Medium'},
            {value:{{ $lowValue }}, name:'Low'}
        ],

        itemStyle:{ borderRadius:8 }
    }]
});

/* ================= AOV ================= */
let aovChart = echarts.init(document.getElementById('aovChart'));

aovChart.setOption({
tooltip:{ trigger:'axis' },
xAxis:{ type:'category', data:['AOV','Return'] },
yAxis:{ type:'value' },
series:[
{
type:'bar',
data:[{{ $avgAOV }}, 18],
itemStyle:{ borderRadius:[6,6,0,0] }
}
]
});

/* ================= CHURN ================= */
let churnChart = echarts.init(document.getElementById('churnChart'));

churnChart.setOption({
tooltip:{ trigger:'item' },
series:[{
type:'pie',
radius:'70%',
data:[
{value:{{ $loyal }}, name:'Loyal'},
{value:{{ $churn }}, name:'Churn'}
],
}]
});

/* ================= RESIZE ================= */
window.addEventListener('resize', ()=>{
segmentChart.resize();
aovChart.resize();
churnChart.resize();
});

});
</script>

@endsection