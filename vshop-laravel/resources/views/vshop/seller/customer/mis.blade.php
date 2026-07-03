@extends('vshop.seller.main')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    .customer-mis-wrapper {
        font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background-color: #f8fafc;
    }

    /* Cohesive card styling */
    .dashboard-card {
        background: #ffffff;
        border: 1px solid #e2e8f0 !important;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        overflow: hidden;
    }
    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1 !important;
    }
    
    .dashboard-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
    }
    .dashboard-card-title::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 40px;
        height: 2.5px;
        background: #004392;
        border-radius: 2px;
    }

    /* Back button styling */
    .btn-back {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        color: #475569;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
    }
    .btn-back:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    /* KPI Icon badge styling */
    .kpi-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .chart-container {
        height: 260px;
        width: 100%;
    }
</style>

<div class="customer-mis-wrapper container-fluid py-4">
    
    {{-- ================= HEADER ================= --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <button onclick="goBack()" class="btn-back" title="Go Back">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <div>
            <h3 class="fw-bold mb-0 text-dark" style="font-size: 24px; letter-spacing: -0.5px;">Customer MIS Dashboard</h3>
            <p class="text-muted fw-medium mb-0 fs-13 mt-0.5">Analyze customer behavior, feedback ratios, and distribution metrics</p>
        </div>
    </div>

    {{-- ================= TOP KPI CARDS ================= --}}
    <div class="row g-3 mb-4">
        @php
            $kpis = [
                ['Total', $totalCustomers, '#004392', 'fa-solid fa-users', 'Cumulative consumers'],
                ['Returning', $returningCustomers, '#10b981', 'fa-solid fa-user-check', 'Active repeat rate'],
                ['New', $newCustomers, '#3b82f6', 'fa-solid fa-user-plus', 'Acquired this month'],
                ['Bad', $badReviews, '#ef4444', 'fa-solid fa-face-frown', 'Reviews 2-star & below']
            ];
        @endphp

        @foreach($kpis as $k)
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card p-4 position-relative">
                    <div class="position-absolute" style="right: -10px; bottom: -10px; font-size: 80px; color: {{ $k[2] }}05; transform: rotate(-10deg); pointer-events: none;">
                        <i class="{{ $k[3] }}"></i>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted text-uppercase fw-bold fs-11" style="letter-spacing: 0.5px;">{{ $k[0] }} Customers</span>
                        <span class="kpi-icon-wrapper" style="background: {{ $k[2] }}15; color: {{ $k[2] }};">
                            <i class="{{ $k[3] }}"></i>
                        </span>
                    </div>
                    <div class="mt-3">
                        <h2 class="text-dark fw-bold mb-1" style="font-size: 32px; font-weight: 800; letter-spacing: -0.5px;">{{ number_format($k[1]) }}</h2>
                        <span class="text-muted fs-12 fw-semibold d-flex align-items-center gap-1">
                            {{ $k[4] }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ================= CHARTS - ROW 1 ================= --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="dashboard-card p-4">
                <h5 class="dashboard-card-title">Top vs Bottom Customers</h5>
                <div id="lineChart" class="chart-container"></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card p-4">
                <h5 class="dashboard-card-title">Distribution</h5>
                <div id="pieChart" class="chart-container"></div>
            </div>
        </div>
    </div>

    {{-- ================= CHARTS - ROW 2 ================= --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="dashboard-card p-4">
                <h5 class="dashboard-card-title">Review Analysis</h5>
                <div id="reviewChart" class="chart-container"></div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-card p-4">
                <h5 class="dashboard-card-title">Ratings</h5>
                <div id="ratingChart" class="chart-container"></div>
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
            tooltip: { 
                trigger: 'axis',
                backgroundColor: '#0f172a',
                borderWidth: 0,
                textStyle: {
                    color: '#ffffff',
                    fontFamily: 'Outfit'
                },
                borderRadius: 8
            },
            grid: [
                { left: '5%', right: '55%', containLabel: true },
                { left: '55%', right: '5%', containLabel: true }
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
                        fontFamily: 'Outfit',
                        color: '#64748b',
                        fontWeight: 600,
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
                        fontFamily: 'Outfit',
                        color: '#64748b',
                        fontWeight: 600,
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
                    itemStyle: { 
                        color: new echarts.graphic.LinearGradient(1, 0, 0, 0, [
                            { offset: 0, color: '#10b981' },
                            { offset: 1, color: '#059669' }
                        ]),
                        borderRadius: [0, 4, 4, 0]
                    }
                },
                {
                    name: 'Bottom Customers',
                    type: 'bar',
                    data: bottomValues,
                    xAxisIndex: 1,
                    yAxisIndex: 1,
                    itemStyle: { 
                        color: new echarts.graphic.LinearGradient(1, 0, 0, 0, [
                            { offset: 0, color: '#ef4444' },
                            { offset: 1, color: '#b91c1c' }
                        ]),
                        borderRadius: [0, 4, 4, 0]
                    }
                }
            ]
        });
    }

    // ================= PIE =================
    const pieEl = document.getElementById('pieChart');
    if(pieEl){
        const pieChart = echarts.init(pieEl);
        pieChart.setOption({
            color: ['#3b82f6', '#10b981', '#ef4444'],
            tooltip: { 
                trigger: 'item',
                backgroundColor: '#0f172a',
                borderWidth: 0,
                textStyle: {
                    color: '#ffffff',
                    fontFamily: 'Outfit'
                },
                borderRadius: 8
            },
            legend: { 
                bottom: 0,
                textStyle: {
                    fontFamily: 'Outfit',
                    color: '#64748b',
                    fontWeight: 600
                }
            },
            series: [
                {
                    type: 'pie',
                    radius: ['55%', '75%'],
                    avoidLabelOverlap: false,
                    itemStyle: {
                        borderRadius: 8
                    },
                    label: {
                        show: false
                    },
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
    const reviewEl = document.getElementById('reviewChart');
    if(reviewEl){
        const reviewChart = echarts.init(reviewEl);
        reviewChart.setOption({
            color: ['#10b981', '#ef4444'],
            tooltip: {
                trigger: 'item',
                backgroundColor: '#0f172a',
                borderWidth: 0,
                textStyle: {
                    color: '#ffffff',
                    fontFamily: 'Outfit'
                },
                borderRadius: 8
            },
            legend: {
                bottom: 0,
                textStyle: {
                    fontFamily: 'Outfit',
                    color: '#64748b',
                    fontWeight: 600
                }
            },
            series: [
                {
                    type: 'pie',
                    radius: ['55%', '75%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: false
                    },
                    itemStyle: {
                        borderRadius: 8
                    },
                    data: [
                        { value: {{ count($topReviews ?? []) }}, name: 'Positive' },
                        { value: {{ count($badReviewList ?? []) }}, name: 'Negative' }
                    ]
                }
            ]
        });
    }

    // ================= RATINGS CHART =================
    const ratingEl = document.getElementById('ratingChart');
    if(ratingEl){
        const ratingChart = echarts.init(ratingEl);
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
                backgroundColor: '#0f172a',
                borderWidth: 0,
                textStyle: {
                    color: '#ffffff',
                    fontFamily: 'Outfit'
                },
                borderRadius: 8,
                formatter: function(params){
                    let data = params[0];
                    return `
                        <div style="padding:6px; font-family: 'Outfit';">
                            <b>${data.axisValue}</b><br>
                            Rating: <strong style="color: #f59e0b">${data.value} / 5</strong>
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
                boundaryGap: false,
                axisLine: {
                    lineStyle: { color: '#cbd5e1' }
                },
                axisTick: { show: false },
                axisLabel: {
                    color: '#64748b',
                    rotate: 15,
                    fontFamily: 'Outfit',
                    fontWeight: 600,
                    formatter: function(value){
                        if(!value || value === 'null'){ return 'Guest'; }
                        return value.length > 12 ? value.substring(0,12)+'...' : value;
                    }
                }
            },
            yAxis: {
                type: 'value',
                min: 0,
                max: 5,
                interval: 1,
                axisLine: { show: false },
                axisTick: { show: false },
                splitLine: {
                    lineStyle: { type: 'dashed', color: '#cbd5e1' }
                },
                axisLabel: {
                    color: '#64748b',
                    fontFamily: 'Outfit',
                    fontWeight: 600
                }
            },
            series: [
                {
                    name: 'Ratings',
                    data: topRatedValues,
                    type: 'line',
                    smooth: true,
                    symbol: 'circle',
                    symbolSize: 8,
                    lineStyle: {
                        width: 3.5,
                        color: '#004392'
                    },
                    itemStyle: {
                        color: '#ffffff',
                        borderWidth: 3,
                        borderColor: '#004392'
                    },
                    areaStyle: {
                        opacity: 0.1,
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: '#004392' },
                            { offset: 1, color: 'rgba(0, 67, 146, 0.01)' }
                        ])
                    },
                    label: {
                        show: true,
                        position: 'top',
                        color: '#0f172a',
                        fontWeight: 'bold',
                        fontFamily: 'Outfit'
                    }
                }
            ]
        });
    }

    // Responsive ECharts resize logic
    window.addEventListener('resize', function () {
        document.querySelectorAll('.chart-container').forEach(el => {
            let chart = echarts.getInstanceByDom(el);
            if (chart) {
                chart.resize();
            }
        });
    });
});
</script>

@endsection
