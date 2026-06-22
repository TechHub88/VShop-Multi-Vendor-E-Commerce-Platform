@extends('vshop.seller.main')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    .container-fluid {
        font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    body {
        background: #f8fafc !important;
    }

    .chart-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 24px;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
    }
    .chart-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }
    .chart-card h5 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1.25rem;
        position: relative;
        padding-bottom: 8px;
    }
    .chart-card h5::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 32px;
        height: 3px;
        background: #3b82f6;
        border-radius: 2px;
    }

    .kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }

    .top-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .top-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }
    .top-kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #e2e8f0;
        transition: background 0.3s ease;
    }
    .top-kpi-card:hover::before {
        background: #3b82f6;
    }
    .top-kpi-card.has-trend::before {
        background: #22c55e;
    }

    .nav-btn {
        border: 1px solid #004392;
        color: #004392;
        background: transparent;
        border-radius: 5px;
        font-size: 4px;
        width: 35px;
        height: 38px;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
        transition: all 0.2s ease !important;
    }

    .top-back-heading-sec {
        align-items: center;
        gap: 12px;
    }

    .list-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 600;
        color: #334155;
        transition: all 0.2s ease;
    }
    .list-item-row:hover {
        padding-left: 6px;
        color: #0f172a;
    }
    .list-item-row:last-child {
        border-bottom: none;
    }
    .badge-count {
        background: #ecfdf5;
        color: #059669;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.775rem;
        font-weight: 700;
    }
    .top-alert-box {
        width: auto !important;
    }
    .top-kpi-card,
    .kpi-card,
    .chart-card {
        border: 1px solid rgb(201 208 218) !important;
    }
    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-outline-primary {
        border-color: #233d79 !important;
        color: #233d79 !important;
    }
    .btn-outline-primary:hover {
        background-color: #233d79 !important;
        color: #fff !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
</style>

@section('content')

<div class="container-fluid py-4">

{{-- ================= HEADER ================= --}}
<div class=" mb-4 flex-wrap gap-3">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="d-flex top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <div>
            <h3 class="fw-bold mb-1 text-dark">Enquiry MIS Dashboard</h3>
            <small class="text-muted fw-semibold">Track and analyze your enquiry performance</small>
        </div>
    </div>
    
    {{-- ================= 🔥 OVERDUE ALERT ================= --}}
    @php
    $overdueCount = \App\Models\Enquiry::where('seller_id', auth()->guard('seller')->id())
        ->where('status','Follow-up')
        ->whereDate('next_followup_date','<', now())
        ->count();
    @endphp
    
    @if($overdueCount > 0)
    
    <div class="top-alert-box">
        <div class="overdue-alert">
            <i class="fa-solid fa-triangle-exclamation me-1"></i>
            {{ $overdueCount }} Overdue Follow-ups pending
        </div>
    </div>
    
    @endif
    </div>

  <form method="GET" id="misFilterForm" class="d-flex gap-2 flex-wrap">

    {{-- HIDDEN TYPE --}}
    <input type="hidden"
           name="type"
           id="filterType"
           value="{{ request('type','month') }}">

    {{-- DATE TYPE --}}
    <div class="filter-box p-3">

        <small class="text-muted fw-bold">
            Date Range
        </small>

        <div class="mt-2">

            <div class="btn-group btn-group-sm">

                <button type="button"
                        class="btn date-btn {{ request('type','month')=='day'?'btn-primary':'btn-outline-primary' }}"
                        data-type="day">

                    Day

                </button>

                <button type="button"
                        class="btn date-btn {{ request('type','month')=='month'?'btn-primary':'btn-outline-primary' }}"
                        data-type="month">

                    Month

                </button>

                <button type="button"
                        class="btn date-btn {{ request('type','month')=='year'?'btn-primary':'btn-outline-primary' }}"
                        data-type="year">

                    Year

                </button>

            </div>

        </div>

    </div>


    {{-- START DATE --}}
    <div class="filter-box">

        <small class="fw-bold">
            Start Date
        </small>

        <input type="date"
               name="start"
               value="{{ request('start') }}"
               class="form-control">

    </div>


    {{-- END DATE --}}
    <div class="filter-box">

        <small class="fw-bold">
            End Date
        </small>

        <input type="date"
               name="end"
               value="{{ request('end') }}"
               class="form-control">

    </div>


    {{-- SORT --}}
    <div class="filter-box">

        <small class="fw-bold">
            Sort
        </small>

        <select name="sort" class="form-select">

            <option value="">Default</option>

            <option value="user"
            {{ request('sort')=='user'?'selected':'' }}>

                By User

            </option>

            <option value="product"
            {{ request('sort')=='product'?'selected':'' }}>

                By Product

            </option>

        </select>

    </div>


    {{-- APPLY --}}
    <div class="align-self-end">

        <button class="btn btn-primary">

            Apply

        </button>

    </div>

</form>
</div>

{{-- ================= TOP KPI CARDS ================= --}}
<div class="row g-3 mb-4">
    <!-- Total Enquiries -->
    <div class="col-lg-3 col-md-6">
        <div class="top-kpi-card has-trend">
            <div class="d-flex align-items-center gap-1 text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                Total Enquiries 
                <i class="fa-regular fa-circle-question" style="cursor: pointer; font-size: 12px; color: #94a3b8;" title="Total enquiries received all time"></i>
            </div>
            <div class="d-flex align-items-baseline gap-2 mt-2">
                <h2 class="mb-0 fw-extrabold" style="font-size: 32px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">{{ number_format($totalEnquiries) }}</h2>
                <span class="text-success d-inline-flex align-items-center" style="font-size: 18px; font-weight: 700; color: #22c55e !important;"><i class="fa-solid fa-arrow-trend-up"></i></span>
            </div>
        </div>
    </div>

    <!-- Today's Enquiries -->
    <div class="col-lg-3 col-md-6">
        <div class="top-kpi-card">
            <div class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                Today's Enquiries
            </div>
            <div class="d-flex align-items-baseline gap-2 mt-2">
                <h2 class="mb-0 fw-extrabold" style="font-size: 32px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">{{ number_format($todaysEnquiries) }}</h2>
            </div>
        </div>
    </div>

    <!-- Pending Follow-ups -->
    <div class="col-lg-3 col-md-6">
        <div class="top-kpi-card">
            <div class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                Pending Follow-ups
            </div>
            <div class="d-flex align-items-baseline gap-2 mt-2">
                <h2 class="mb-0 fw-extrabold" style="font-size: 32px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">{{ number_format($pendingFollowups) }}</h2>
            </div>
        </div>
    </div>

    <!-- Converted Leads -->
    <div class="col-lg-3 col-md-6">
        <div class="top-kpi-card has-trend">
            <div class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                Converted Leads
            </div>
            <div class="d-flex align-items-center gap-2 mt-2">
                <h2 class="mb-0 fw-extrabold" style="font-size: 32px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">{{ number_format($convertedLeads) }}</h2>
                <div class="d-flex flex-column align-items-start" style="margin-left: 2px;">
                    <span class="text-success d-inline-flex align-items-center" style="font-size: 14px; font-weight: 700; color: #22c55e !important; line-height: 1;"><i class="fa-solid fa-arrow-trend-up"></i></span>
                    <span style="font-size: 9px; color: #64748b; font-weight: 600; line-height: 1; margin-top: 2px;">(Conversion rate)</span>
                </div>
            </div>
        </div>
    </div>
</div>

@php
$new = $statusWise->where('status','New')->sum('total');
$follow = $statusWise->where('status','Follow-up')->sum('total');
$converted = $statusWise->where('status','Converted')->sum('total');
$lost = $statusWise->where('status','Lost')->sum('total');
@endphp

{{-- ================= KPI ================= --}}
<div class="row g-3 mb-4">

@foreach([
['New',$new,'#4f6bdc'],
['Follow-up',$follow,'#22c55e'],
['Converted',$converted,'#6366f1'],
['Lost',$lost,'#ef4444']
] as $k)

<div class="col-md-3">
<div class="kpi-card d-flex align-items-center gap-3">
    <div class="icon-circle" style="background: {{ $k[2] }}20; color: {{ $k[2] }}">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M249.03 10.865 28.063 117.537a8.017 8.017 0 0 0-4.531 7.219v262.485a8.018 8.018 0 0 0 4.531 7.219L249.03 501.135a16.031 16.031 0 0 0 13.94 0l220.967-106.674a8.017 8.017 0 0 0 4.531-7.219V124.757a8.018 8.018 0 0 0-4.531-7.219L262.97 10.865a16.031 16.031 0 0 0-13.94 0z" style="" fill="#ffe17d" data-original="#ffe17d"></path><path d="M24.865 120.368a7.98 7.98 0 0 0-1.333 4.388v262.485a8.017 8.017 0 0 0 4.531 7.219L249.03 501.135a16.027 16.027 0 0 0 6.97 1.595V231.951L24.865 120.368z" style="" fill="#ffc350" data-original="#ffc350"></path><path d="M487.135 120.368a7.98 7.98 0 0 1 1.333 4.388v262.485a8.017 8.017 0 0 1-4.531 7.219L262.97 501.135a16.027 16.027 0 0 1-6.97 1.595V231.951l231.135-111.583z" style="" fill="#ffd164" data-original="#ffd164"></path><path d="M408.807 158.052 176.86 46l-61.142 29.636 232.467 112.227z" style="" fill="#ff8087" data-original="#ff8087"></path><path d="M348.185 187.863v86.94c0 2.955 3.089 4.894 5.751 3.609l50.34-24.302a8.015 8.015 0 0 0 4.531-7.219v-88.839l-60.622 29.811z" style="" fill="#e6646e" data-original="#e6646e"></path><path d="M491.729 112.97 259.261.746a7.503 7.503 0 0 0-6.521 0L20.271 112.97a7.5 7.5 0 0 0-4.239 6.754v272.549a7.5 7.5 0 0 0 4.239 6.754l232.468 112.226a7.504 7.504 0 0 0 6.522 0l232.468-112.226a7.5 7.5 0 0 0 4.239-6.754V119.725a7.503 7.503 0 0 0-4.239-6.755zM256 15.828l215.217 103.897-62.387 30.118a7.434 7.434 0 0 0-1.27-.8L193.805 45.853 256 15.828zm-79.133 38.505 214.904 103.746-44.015 21.249L132.941 75.623l43.926-21.29zm178.819 137.823 41.113-19.848v78.546l-41.113 19.848v-78.546zM263.5 492.551V236.658l51.873-25.042a7.5 7.5 0 1 0-6.522-13.508L256 223.623l-20.796-10.04c-3.731-1.803-8.214-.237-10.015 3.493s-.237 8.214 3.493 10.015l19.818 9.567V492.55L31.032 387.566V131.673l165.6 79.945a7.463 7.463 0 0 0 3.255.748 7.5 7.5 0 0 0 3.266-14.256l-162.37-78.386 74.505-35.968L340.582 192.52c.033.046.07.087.104.132v89.999a7.502 7.502 0 0 0 10.761 6.754l56.113-27.089a7.499 7.499 0 0 0 4.239-6.754v-90.495l69.169-33.392v255.893L263.5 492.551z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="m92.926 358.48-34.115-16.469a7.5 7.5 0 0 0-6.522 13.508l34.115 16.469a7.463 7.463 0 0 0 3.255.748 7.5 7.5 0 0 0 6.759-4.241 7.499 7.499 0 0 0-3.492-10.015zM124.323 338.042l-65.465-31.604a7.501 7.501 0 0 0-6.521 13.509l65.465 31.604a7.477 7.477 0 0 0 3.255.748 7.5 7.5 0 0 0 3.266-14.257z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
    </div>
    <div>
        <h2 class="mb-0 fw-bold">{{ $k[1] }}</h2>
        <small class="text-muted fw-bold">{{ $k[0] }}</small>
    </div>
</div>
</div>

@endforeach

</div>

{{-- ================= CHARTS ================= --}}
<div class="row g-3 mb-3">

<div class="col-lg-4">
<div class="chart-card">
<h5 class="fw-bold">Enquiry Funnel</h5>
<div id="funnelChart"></div>
</div>
</div>

<div class="col-lg-4">
<div class="chart-card text-center">
<h5 class="fw-bold">Status Distribution</h5>
<div id="pieChart"></div>
</div>
</div>

<div class="col-lg-4">
<div class="chart-card">
<h5 class="fw-bold">Status Trend</h5>
<div id="lineChart"></div>
</div>
</div>

</div>

<div class="row g-3">

<div class="col-md-6">
<div class="chart-card">
<h5 class="fw-bold">Source Analysis</h5>
<div id="sourceChart"></div>
</div>
</div>

<div class="col-md-6">
<div class="chart-card">
<h5 class="fw-bold">Top Product / Service</h5>

@foreach($topProducts as $p)
<div class="list-item-row">
    <span>{{ $p->product_service }}</span>
    <span class="badge-count">{{ $p->total }}</span>
</div>
@endforeach

</div>
</div>

<div class="col-md-6">
<div class="chart-card">
<h5 class="fw-bold">Top 5 Locations</h5>
<div id="locationBarChart"></div>
</div>
</div>

<div class="col-md-6">
<div class="chart-card">
<h5 class="fw-bold">Location Distribution</h5>
<div id="locationPieChart"></div>
</div>
</div>

</div>

</div>

{{-- ================= STYLE ================= --}}
<style>
    body {
        background: #f8fafc !important;
    }

    .filter-box {
        background: #ffffff;
        padding: 12px 18px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 0.45rem 0.85rem;
        font-size: 0.875rem;
        color: #1e293b;
        background-color: #ffffff;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        outline: none;
    }

    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }

    #funnelChart, #pieChart, #lineChart, #sourceChart {
        height: 280px;
    }

    #locationBarChart, #locationPieChart {
        height: 280px;
    }

    /* ================= ALERT TOP RIGHT ================= */

    .top-alert-box {
        width: 100%;
        display: flex;
        justify-content: flex-end;
        margin-bottom: 15px;
    }

    .overdue-alert {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fee2e2;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(239,68,68,0.05);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        max-width: fit-content;
    }
</style>

{{-- ================= JS ================= --}}
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

<script>

document.addEventListener("DOMContentLoaded", function () {

    /*
    |--------------------------------------------------------------------------
    | DATE FILTER BUTTONS
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll(".date-btn").forEach(btn => {

        btn.addEventListener("click", function () {

            let type = this.dataset.type;

            // SET HIDDEN TYPE INPUT
            document.getElementById('filterType').value = type;

            // SUBMIT FORM
            document.getElementById('misFilterForm').submit();

        });

    });



    /*
    |--------------------------------------------------------------------------
    | KPI DATA
    |--------------------------------------------------------------------------
    */

    const dataVals = [
        {{ $newCount ?? 0 }},
        {{ $followupCount ?? 0 }},
        {{ $convertedCount ?? 0 }},
        {{ $lostCount ?? 0 }}
    ];



    /*
    |--------------------------------------------------------------------------
    | FUNNEL CHART
    |--------------------------------------------------------------------------
    */

    let funnelEl = document.getElementById('funnelChart');

    if (funnelEl) {

        echarts.init(funnelEl).setOption({

            color: ['#ef4444', '#6366f1', '#10b981', '#4f6bdc'],

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

            series: [{

                type: 'funnel',

                width: '80%',

                label: {
                    show: true,
                    fontFamily: 'Outfit',
                    fontWeight: 600,
                    color: '#475569'
                },

                data: [

                    {
                        value: {{ $lostCount ?? 0 }},
                        name: 'Lost'
                    },

                    {
                        value: {{ $convertedCount ?? 0 }},
                        name: 'Converted'
                    },

                    {
                        value: {{ $followupCount ?? 0 }},
                        name: 'Follow-up'
                    },

                    {
                        value: {{ $newCount ?? 0 }},
                        name: 'New'
                    }

                ]

            }]

        });
    }



    /*
    |--------------------------------------------------------------------------
    | PIE CHART
    |--------------------------------------------------------------------------
    */

    let pieEl = document.getElementById('pieChart');

    if (pieEl) {

        echarts.init(pieEl).setOption({

            color: ['#4f6bdc', '#22c55e', '#6366f1', '#ef4444'],

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

            series: [{

                type: 'pie',

                radius: ['60%', '80%'],

                avoidLabelOverlap: false,

                itemStyle: {
                    borderRadius: 8
                },

                data: [

                    {
                        value: dataVals[0],
                        name: 'New'
                    },

                    {
                        value: dataVals[1],
                        name: 'Follow-up'
                    },

                    {
                        value: dataVals[2],
                        name: 'Converted'
                    },

                    {
                        value: dataVals[3],
                        name: 'Lost'
                    }

                ]

            }]

        });
    }



    /*
    |--------------------------------------------------------------------------
    | TREND CHART
    |--------------------------------------------------------------------------
    */

    let lineEl = document.getElementById('lineChart');

    if (lineEl) {

        echarts.init(lineEl).setOption({

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

            grid: {
                top: '15%',
                left: '5%',
                right: '5%',
                bottom: '10%',
                containLabel: true
            },

            xAxis: {

                type: 'category',

                data: @json($trend->pluck('label')),
                axisLabel: {
                    fontFamily: 'Outfit',
                    color: '#64748b',
                    fontWeight: 600
                },
                axisLine: {
                    lineStyle: {
                        color: '#cbd5e1'
                    }
                }

            },

            yAxis: {

                type: 'value',
                axisLabel: {
                    fontFamily: 'Outfit',
                    color: '#64748b',
                    fontWeight: 600
                },
                splitLine: {
                    lineStyle: {
                        color: '#f1f5f9'
                    }
                }

            },

            series: [{

                type: 'line',

                smooth: true,
                
                lineStyle: {
                    color: '#3b82f6',
                    width: 3
                },
                
                itemStyle: {
                    color: '#3b82f6'
                },

                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: 'rgba(59, 130, 246, 0.25)' },
                        { offset: 1, color: 'rgba(59, 130, 246, 0.01)' }
                    ])
                },

                data: @json($trend->pluck('total'))

            }]

        });
    }



    /*
    |--------------------------------------------------------------------------
    | SOURCE CHART
    |--------------------------------------------------------------------------
    */

    let sourceEl = document.getElementById('sourceChart');

    if (sourceEl) {

        echarts.init(sourceEl).setOption({

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

            grid: {
                top: '15%',
                left: '5%',
                right: '5%',
                bottom: '10%',
                containLabel: true
            },

            xAxis: {

                type: 'category',

                data: @json($sourceWise->pluck('source')),
                axisLabel: {
                    fontFamily: 'Outfit',
                    color: '#64748b',
                    fontWeight: 600
                },
                axisLine: {
                    lineStyle: {
                        color: '#cbd5e1'
                    }
                }

            },

            yAxis: {

                type: 'value',
                axisLabel: {
                    fontFamily: 'Outfit',
                    color: '#64748b',
                    fontWeight: 600
                },
                splitLine: {
                    lineStyle: {
                        color: '#f1f5f9'
                    }
                }

            },

            series: [{

                type: 'bar',

                barWidth: '35%',

                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#6366f1' },
                        { offset: 1, color: '#4f46e5' }
                    ]),
                    borderRadius: [6, 6, 0, 0]
                },

                data: @json($sourceWise->pluck('total')),

                label: {
                    show: true,
                    position: 'top',
                    fontFamily: 'Outfit',
                    fontWeight: 700,
                    color: '#475569'
                }

            }]

        });
    }



    /*
    |--------------------------------------------------------------------------
    | LOCATION CHARTS
    |--------------------------------------------------------------------------
    */

    let locations = @json($topLocations);



    /*
    |--------------------------------------------------------------------------
    | LOCATION BAR CHART
    |--------------------------------------------------------------------------
    */

    let locationBarEl =
    document.getElementById('locationBarChart');

    if (
        locationBarEl &&
        locations &&
        locations.length
    ) {

        let names =
        locations.map(i => i.location);

        let values =
        locations.map(i => i.total);

        echarts.init(locationBarEl).setOption({

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

            grid: {
                top: '15%',
                left: '5%',
                right: '5%',
                bottom: '10%',
                containLabel: true
            },

            xAxis: {

                type: 'category',

                data: names,
                axisLabel: {
                    fontFamily: 'Outfit',
                    color: '#64748b',
                    fontWeight: 600
                },
                axisLine: {
                    lineStyle: {
                        color: '#cbd5e1'
                    }
                }

            },

            yAxis: {

                type: 'value',
                axisLabel: {
                    fontFamily: 'Outfit',
                    color: '#64748b',
                    fontWeight: 600
                },
                splitLine: {
                    lineStyle: {
                        color: '#f1f5f9'
                    }
                }

            },

            series: [{

                type: 'bar',
                
                barWidth: '35%',

                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#10b981' },
                        { offset: 1, color: '#059669' }
                    ]),
                    borderRadius: [6, 6, 0, 0]
                },

                data: values,

                label: {

                    show: true,

                    position: 'top',
                    fontFamily: 'Outfit',
                    fontWeight: 700,
                    color: '#475569'

                }

            }]

        });
    }



    /*
    |--------------------------------------------------------------------------
    | LOCATION PIE CHART
    |--------------------------------------------------------------------------
    */

    let locationPieEl =
    document.getElementById('locationPieChart');

    if (
        locationPieEl &&
        locations &&
        locations.length
    ) {

        echarts.init(locationPieEl).setOption({

            color: ['#3b82f6', '#10b981', '#6366f1', '#f59e0b', '#ec4899'],

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

            series: [{

                type: 'pie',

                radius: ['50%', '75%'],
                
                itemStyle: {
                    borderRadius: 8
                },

                data: locations.map(i => ({

                    name: i.location,

                    value: i.total

                }))

            }]

        });
    }



    /*
    |--------------------------------------------------------------------------
    | RESPONSIVE CHART RESIZE
    |--------------------------------------------------------------------------
    */

    window.addEventListener('resize', function () {

        document.querySelectorAll('[id$="Chart"]').forEach(el => {

            let chart = echarts.getInstanceByDom(el);

            if (chart) {

                chart.resize();
            }

        });

    });

});

</script>

@endsection