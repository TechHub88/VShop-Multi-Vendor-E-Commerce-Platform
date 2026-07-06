@extends('vshop.seller.main')

@section('content')

<style>
body{background:#f5f7fb;}
.container-fluid{padding:25px;}

.card-box{
    height: 100%;
    background:#fff;
    border-radius:12px;
    padding:16px;
    border:1px solid #e5e7eb;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
    transition: 0.3s;
}
.card-box:hover {
    transform: translateY(-4px);
}

.section-head{
    font-size:13px;
    font-weight:700;
    margin-bottom:10px;
    color:#000;
}

.kpi-box{
    display:flex;
    justify-content:space-between;
    text-align:center;
}

.kpi-item{
    flex:1;
    border-right:1px solid #e5e7eb;
}

.kpi-item:last-child{border:none;}

.kpi-title{font-size:12px;color:#000;}
.kpi-value{font-size:22px;font-weight:700;}

.green{color:#16a34a;}
.blue{color:#2563eb;}

.chart{height:260px;}
.small-chart{height:120px;}
.card-box div canvas {
    width: 100% !important;
    object-fit: contain;
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
</style>

<div class="container-fluid">

<h4 class="fw-bold mb-3"></h4>

<div class="d-flex top-back-heading-sec mb-4">

    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    
    <h5 class="fw-bold mb-0">
        Sales Overview Dashboard
    </h5>
    
</div>

@php
$totalRevenue = $sales->sum('revenue');
$totalOrders = $sales->sum('orders');
$totalSales = $totalRevenue;

$cost = $totalRevenue * 0.4;
$shipping = $totalRevenue * 0.1;
$fees = $totalRevenue * 0.05;
$net = $totalRevenue - ($cost+$shipping+$fees);
@endphp

{{-- ================= KPI ================= --}}
<div class="card-box mb-3">
<div class="section-head">Total Sales, Orders & Revenue (Segmented by Time)</div>

<div class="kpi-box">
    <div class="kpi-item">
        <div class="kpi-title">Revenue</div>
        <div class="kpi-value green">₹{{ number_format($totalRevenue) }}</div>
    </div>

    <div class="kpi-item">
        <div class="kpi-title">Sales</div>
        <div class="kpi-value blue">₹{{ number_format($totalSales) }}</div>
    </div>

    <div class="kpi-item">
        <div class="kpi-title">Orders</div>
        <div class="kpi-value">{{ number_format($totalOrders) }}</div>
    </div>
</div>
</div>

<div class="row g-3">

{{-- TREND --}}
<div class="col-md-8">
<div class="card-box">
<div class="section-head">Trend Graph (Performance Over Time)</div>
<div id="trendChart" class="chart"></div>
</div>
</div>

{{-- CATEGORY --}}
<div class="col-md-4">
<div class="card-box">
<div class="section-head">Sales Segmentation</div>
<div id="pieChart" class="chart"></div>
</div>
</div>

{{-- COMPARATIVE --}}
<div class="col-md-6">
<div class="card-box">
<div class="section-head">Comparative Analysis Over Time</div>
<div id="compareChart" class="chart"></div>
</div>
</div>

{{-- NET PROFIT --}}
<div class="col-md-6">
<div class="card-box">
<div class="section-head">Net Profit Dashboard</div>

<h3 class="green mb-3">₹{{ number_format($net) }}</h3>

<div id="profitFlowChart" class="chart"></div>

</div>
</div>

{{-- PRODUCT --}}
<div class="col-md-4">
<div class="card-box">
<div class="section-head">Product-wise Profitability (Top 10)</div>
<div id="productChart" class="chart"></div>
</div>
</div>

{{-- SIDE --}}
<div class="col-md-4">

<div class="card-box mb-3">
<div class="section-head">Profit Trend</div>
<div id="profitMini" class="small-chart"></div>
</div>

</div>
<div class="col-md-4">
<div class="card-box">
<div class="section-head">Deduction Breakdown</div>
<div id="deductionChart" class="small-chart"></div>
</div>
</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

<script>

/* ================= SAFE DATA (REAL ONLY) ================= */
let sales = @json($sales ?? []);
let categoryData = @json($categoryData ?? []);
let productData = @json($productData ?? []);
let profitTrend = @json($profitTrend ?? []);
let deduction = @json($deduction ?? []);

/* SAFE NUMBERS */
let totalRevenue = @json($totalRevenue ?? 0);
let cost = @json($cost ?? 0);
let shipping = @json($shipping ?? 0);
let fees = @json($fees ?? 0);
let net = @json($net ?? 0);

/* ================= PREP DATA ================= */
let dates = sales.map(x => x.date);
let revenue = sales.map(x => x.revenue);
let orders = sales.map(x => x.orders);

/* ================= TREND ================= */
if(document.getElementById('trendChart')){
    echarts.init(document.getElementById('trendChart')).setOption({
        tooltip:{trigger:'axis'},
        legend:{data:['Revenue','Orders']},
        xAxis:{type:'category',data:dates},
        yAxis:[
            {type:'value'},
            {type:'value'}
        ],
        series:[
            {
                name:'Revenue',
                type:'line',
                data:revenue,
                smooth:true,
                areaStyle:{}
            },
            {
                name:'Orders',
                type:'line',
                yAxisIndex:1,
                data:orders,
                smooth:true
            }
        ]
    });
}

/* ================= CATEGORY PIE ================= */
if(document.getElementById('pieChart')){
    echarts.init(document.getElementById('pieChart')).setOption({
        tooltip:{trigger:'item'},
        series:[{
            type:'pie',
            radius:['50%','70%'],
            data: categoryData.map(x=>({
                value:x.total,
                name:x.label
            }))
        }]
    });
}

/* ================= COMPARATIVE ================= */
if(document.getElementById('compareChart')){
    echarts.init(document.getElementById('compareChart')).setOption({
        tooltip:{},
        legend:{data:['Revenue','Growth']},
        xAxis:{type:'category',data:dates},
        yAxis:{type:'value'},
        series:[
            {
                name:'Revenue',
                type:'bar',
                data:revenue
            },
            {
                name:'Growth',
                type:'line',
                data:revenue
            }
        ]
    });
}

/* ================= PROFIT FLOW ================= */
if(document.getElementById('profitFlowChart')){
    let profitFlowData = [
        totalRevenue,
        -cost,
        -shipping,
        -fees,
        net
    ];

    echarts.init(document.getElementById('profitFlowChart')).setOption({
        tooltip:{},
        xAxis:{
            type:'category',
            data:['Revenue','Cost','Shipping','Fees','Profit']
        },
        yAxis:{type:'value'},
        series:[{
            type:'bar',
            data:profitFlowData,
            itemStyle:{
                color:(params)=>{
                    return params.value >= 0 ? '#16a34a' : '#ef4444';
                }
            },
            label:{
                show:true,
                position:'top',
                formatter:'₹{c}'
            }
        }]
    });
}

/* ================= PRODUCT ================= */
if(document.getElementById('productChart')){
    productData.sort((a,b)=>b.total-a.total);

    echarts.init(document.getElementById('productChart')).setOption({
        tooltip:{},
        xAxis:{type:'value'},
        yAxis:{
            type:'category',
            data:productData.map(x=>x.label)
        },
        series:[{
            type:'bar',
            data:productData.map(x=>x.total),
            label:{
                show:true,
                position:'right',
                formatter:'₹{c}'
            }
        }]
    });
}

/* ================= PROFIT TREND ================= */
if(document.getElementById('profitMini')){
    echarts.init(document.getElementById('profitMini')).setOption({
        xAxis:{
            type:'category',
            show:false,
            data:profitTrend.map(x=>x.date)
        },
        yAxis:{show:false},
        series:[{
            type:'line',
            data:profitTrend.map(x=>x.profit),
            smooth:true,
            areaStyle:{}
        }]
    });
}

/* ================= DEDUCTION ================= */
if(document.getElementById('deductionChart')){
    echarts.init(document.getElementById('deductionChart')).setOption({
        tooltip:{trigger:'item'},
        series:[{
            type:'pie',
            radius:['40%','70%'],
            data:[
                {value: deduction.total_deduction ?? 0, name:'Deduction'},
                {value: deduction.total_credit ?? 0, name:'Revenue'}
            ]
        }]
    });
}

/* ================= RESIZE FIX ================= */
window.addEventListener('resize', function(){
    document.querySelectorAll('[id$="Chart"], #profitMini').forEach(el=>{
        let chart = echarts.getInstanceByDom(el);
        if(chart){
            chart.resize();
        }
    });
});

</script>

@endsection
