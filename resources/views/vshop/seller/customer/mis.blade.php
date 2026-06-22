@extends('vshop.seller.main')

@section('content')

<style>

body{
    background:#f4f7fe;
}

/* ===== CARD ===== */
.card{
    border-radius:18px;
    border:none;
    background:#fff;
    box-shadow:0 15px 40px rgba(0,0,0,0.05);
    transition:.3s;
}
.card:hover{
    transform:translateY(-5px);
}

/* ===== KPI ===== */
.kpi{
    padding:20px;
    text-align:left;
}
.kpi h3{
    font-size:26px;
    font-weight:700;
}
.kpi small{
    color:#6b7280;
}

.chart{
    height:360px;
    width:100%;
}

.card{
    overflow:hidden;
}

.echarts-for-react,
canvas{
    border-radius:12px;
}
/* ===== TITLE ===== */
.section-title{
    font-weight:600;
    font-size:15px;
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
.top-back-heading-sec h5 {
    color: #0f172a !important;
}
.card {
    border: 1px solid rgb(201 208 218) !important;
}

</style>

<div class="container-fluid py-4">
    
<div class="d-flex top-back-heading-sec mb-4">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <h5 class="fw-bold">Customer MIS Dashboard</h5>
</div>

{{-- KPI --}}
<div class="row g-3 mb-4">

@php
$cards = [
['Total',$totalCustomers,'#3b82f6'],
['Returning',$returningCustomers,'#10b981'],
['New',$newCustomers,'#f59e0b'],
['Bad',$badReviews,'#ef4444']
];
@endphp

@foreach($cards as $c)
<div class="col-lg-3">
<div class="card kpi">
<h3 style="color:{{$c[2]}}">{{$c[1]}}</h3>
<small>{{$c[0]}}</small>
</div>
</div>
@endforeach

</div>

{{-- CHART ROW --}}
<div class="row g-4">

<div class="col-lg-8">
<div class="card p-3">
<div class="section-title mb-2">Top vs Bottom Customers</div>
<div id="lineChart" class="chart"></div>
</div>
</div>

<div class="col-lg-4">
<div class="card p-3">
<div class="section-title mb-2">Distribution</div>
<div id="pieChart" class="chart"></div>
</div>
</div>

</div>

<div class="row g-4 mt-2">

<div class="col-lg-6">
<div class="card p-3">
<div class="section-title mb-2">Review Analysis</div>
<div id="reviewChart" class="chart"></div>
</div>
</div>

<div class="col-lg-6">
<div class="card p-3">
<div class="section-title mb-2">Ratings</div>
<div id="ratingChart" class="chart"></div>
</div>
</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // ================= TOP vs BOTTOM CLEAN GRAPH =================
    const chartEl = document.getElementById('lineChart');

    if(chartEl){

        const chart = echarts.init(chartEl);

        const topNames = @json(collect($topCustomers ?? [])->pluck('name'));
        const topValues = @json(collect($topCustomers ?? [])->pluck('orders_count'));

        const bottomNames = @json(collect($bottomCustomers ?? [])->pluck('name'));
        const bottomValues = @json(collect($bottomCustomers ?? [])->pluck('orders_count'));

        chart.setOption({

            tooltip: { trigger: 'axis' },

            grid: [
                { left: '5%', right: '55%', containLabel: true }, // LEFT (TOP)
                { left: '55%', right: '5%', containLabel: true }  // RIGHT (BOTTOM)
            ],

            xAxis: [
                { type: 'value', gridIndex: 0 },
                { type: 'value', gridIndex: 1 }
            ],

            yAxis: [
                {
                    type: 'category',
                    data: topNames,
                    gridIndex: 0,
                    inverse: true,
                    axisLabel: {
                        formatter: function(value){
                            return value.length > 10 ? value.substring(0,10)+'...' : value;
                        }
                    }
                },
                {
                    type: 'category',
                    data: bottomNames,
                    gridIndex: 1,
                    inverse: true,
                    axisLabel: {
                        formatter: function(value){
                            return value.length > 10 ? value.substring(0,10)+'...' : value;
                        }
                    }
                }
            ],

            series: [
                {
                    name: 'Top Customers',
                    type: 'bar',
                    data: topValues,
                    xAxisIndex: 0,
                    yAxisIndex: 0,
                    itemStyle: { color: '#22c55e' }
                },
                {
                    name: 'Bottom Customers',
                    type: 'bar',
                    data: bottomValues,
                    xAxisIndex: 1,
                    yAxisIndex: 1,
                    itemStyle: { color: '#ef4444' }
                }
            ]
        });
    }

    // ================= PIE =================
    const pieEl = document.getElementById('pieChart');

    if(pieEl){
        const pieChart = echarts.init(pieEl);

        pieChart.setOption({
            tooltip: { trigger: 'item' },
            legend: { show: false },
            series: [
                {
                    type: 'pie',
                    radius: ['50%', '70%'],
                    data: [
                        { value: {{$newCustomers ?? 0}}, name: 'New' },
                        { value: {{$returningCustomers ?? 0}}, name: 'Returning' },
                        { value: {{$inactiveCustomers ?? 0}}, name: 'Inactive' }
                    ]
                }
            ]
        });
    }

    // ================= REVIEW =================
  // ================= PROFESSIONAL REVIEW DONUT =================
const reviewEl = document.getElementById('reviewChart');

if(reviewEl){

    const reviewChart = echarts.init(reviewEl);

    reviewChart.setOption({

        tooltip:{
            trigger:'item'
        },

        series:[

            {
                type:'pie',

                radius:['55%','75%'],

                avoidLabelOverlap:false,

                label:{
                    show:true,
                    formatter:'{b}\n{c}',
                    fontWeight:'600'
                },

                itemStyle:{
                    borderRadius:10,
                    borderColor:'#fff',
                    borderWidth:4
                },

                data:[
                    {
                        value: {{ count($topReviews ?? []) }},
                        name:'Positive'
                    },
                    {
                        value: {{ count($badReviewList ?? []) }},
                        name:'Negative'
                    }
                ]
            }

        ]

    });

}


// ================= PROFESSIONAL RATING CHART =================
const ratingEl = document.getElementById('ratingChart');

if(ratingEl){

    const ratingChart = echarts.init(ratingEl);

    // ===== FIXED DATA =====
    // user_name use karo name ki jagah

    const topRatedNames = @json(
        collect($topRated ?? [])->map(function($item){
            return $item->user_name ?? 'Guest';
        })
    );

    const topRatedValues = @json(
        collect($topRated ?? [])->map(function($item){
            return (int) ($item->rating ?? 0);
        })
    );

    ratingChart.setOption({

        tooltip: {
            trigger: 'axis',

            formatter: function(params){

                let data = params[0];

                return `
                    <div style="padding:8px">
                        <b>${data.axisValue}</b><br>
                        Rating : ${data.value}
                    </div>
                `;
            }
        },

        grid: {
            top: 40,
            left: 50,
            right: 30,
            bottom: 60
        },

        xAxis: {
            type: 'category',

            data: topRatedNames,

            boundaryGap:false,

            axisLine:{
                lineStyle:{
                    color:'#d1d5db'
                }
            },

            axisTick:{
                show:false
            },

            axisLabel:{
                color:'#6b7280',
                rotate:20,
                fontSize:12,

                formatter:function(value){

                    if(!value || value === 'null'){
                        return 'Guest';
                    }

                    return value.length > 12
                        ? value.substring(0,12)+'...'
                        : value;
                }
            }
        },

        yAxis: {
            type: 'value',

            min:0,
            max:5,
            interval:1,

            axisLine:{
                show:false
            },

            axisTick:{
                show:false
            },

            splitLine:{
                lineStyle:{
                    type:'dashed',
                    color:'#e5e7eb'
                }
            },

            axisLabel:{
                color:'#6b7280'
            }
        },

        series: [

            {
                name:'Ratings',

                data: topRatedValues,

                type: 'line',

                smooth: true,

                symbol: 'circle',

                symbolSize: 10,

                lineStyle:{
                    width:4,
                    color:'#4f46e5'
                },

                itemStyle:{
                    color:'#ffffff',
                    borderWidth:3,
                    borderColor:'#4f46e5'
                },

                areaStyle:{
                    opacity:0.15,
                    color:'#4f46e5'
                },

                label:{
                    show:true,
                    position:'top',
                    color:'#111827',
                    fontWeight:'bold'
                }
            }

        ]

    });

    window.addEventListener('resize', function () {
        ratingChart.resize();
    });

}

});
</script>
<style>
    .section-title{
    font-weight:700 !important;
    font-size:16px;
    color:#111827;
}

.kpi h3{
    font-weight:800;
}

.kpi small{
    font-weight:600;
    color:#374151;
}
</style>
@endsection
