@extends('vshop.seller.main')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    .mis-dashboard-wrapper {
        font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background-color: #f8fafc;
    }

    /* Cohesive card styling */
    .dashboard-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        overflow: hidden;
    }
    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
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

    /* Filter block styling */
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 0.5rem 0.85rem;
        font-size: 0.875rem;
        color: #1e293b;
        background-color: #ffffff;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #004392;
        box-shadow: 0 0 0 3px rgba(0, 67, 146, 0.12);
        outline: none;
    }

    .btn-primary-theme {
        background: #004392 !important;
        border-color: #004392 !important;
        color: #ffffff !important;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .btn-primary-theme:hover {
        background: #00316b !important;
        border-color: #00316b !important;
    }

    .btn-outline-theme {
        border: 1px solid #cbd5e1 !important;
        color: #475569 !important;
        background: #ffffff;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .btn-outline-theme:hover, .btn-outline-theme.active {
        background-color: #004392 !important;
        border-color: #004392 !important;
        color: #ffffff !important;
    }

    /* KPI styling */
    .kpi-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    /* Overdue Alert Alert styling */
    .overdue-banner {
        background: #fff5f5;
        border: 1px solid #fee2e2;
        color: #e11d48;
        padding: 10px 16px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 5px rgba(225, 29, 72, 0.03);
    }
</style>

@section('content')
<div class="mis-dashboard-wrapper container-fluid py-4">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <button onclick="goBack()" class="btn-back" title="Go Back">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <div>
                <h3 class="fw-bold mb-0 text-dark" style="font-size: 24px; letter-spacing: -0.5px;">Enquiry MIS Dashboard</h3>
                <p class="text-muted fw-medium mb-0 fs-13 mt-0.5">Track and analyze your enquiry performance metrics</p>
            </div>
        </div>
        
        {{-- ================= 🔥 OVERDUE ALERT ================= --}}
        <div id="overdueBannerContainer">
            @php
                $overdueCount = \App\Models\Enquiry::where('seller_id', auth()->guard('seller')->id())
                    ->where('status','Follow-up')
                    ->whereDate('next_followup_date','<', now())
                    ->count();
            @endphp
            @if($overdueCount > 0)
                <div class="overdue-banner">
                    <i class="fa-solid fa-triangle-exclamation animate__animated animate__pulse animate__infinite"></i>
                    <span>{{ $overdueCount }} Overdue Follow-ups pending action</span>
                </div>
            @endif
        </div>
    </div>

    {{-- ================= FILTERS CARD ================= --}}
    <div class="filter-card card border-0 p-3 mb-4">
        <form method="GET" id="misFilterForm" class="row g-3 align-items-end">
            {{-- HIDDEN TYPE --}}
            <input type="hidden" name="type" id="filterType" value="{{ request('type','month') }}">

            {{-- DATE TYPE --}}
            <div class="col-lg-3 col-md-4 col-sm-6">
                <label class="form-label text-muted fw-bold mb-2 fs-12 text-uppercase" style="letter-spacing: 0.5px;">Date Range Type</label>
                <div class="btn-group btn-group-sm w-100 p-1 bg-light rounded-3 border" style="height: 38px;">
                    <button type="button" class="btn border-0 rounded-2 date-btn py-1 fs-12 {{ request('type','month')=='day'?'btn-primary-theme':'btn-light text-muted' }}" data-type="day">
                        Day
                    </button>
                    <button type="button" class="btn border-0 rounded-2 date-btn py-1 fs-12 {{ request('type','month')=='month'?'btn-primary-theme':'btn-light text-muted' }}" data-type="month">
                        Month
                    </button>
                    <button type="button" class="btn border-0 rounded-2 date-btn py-1 fs-12 {{ request('type','month')=='year'?'btn-primary-theme':'btn-light text-muted' }}" data-type="year">
                        Year
                    </button>
                </div>
            </div>

            {{-- START DATE --}}
            <div class="col-lg-3 col-md-4 col-sm-6">
                <label class="form-label text-muted fw-bold mb-2 fs-12 text-uppercase" style="letter-spacing: 0.5px;">Start Date</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-regular fa-calendar"></i></span>
                    <input type="date" name="start" value="{{ request('start') }}" class="form-control border-start-0 ps-0" style="height: 38px;">
                </div>
            </div>

            {{-- END DATE --}}
            <div class="col-lg-3 col-md-4 col-sm-6">
                <label class="form-label text-muted fw-bold mb-2 fs-12 text-uppercase" style="letter-spacing: 0.5px;">End Date</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-regular fa-calendar"></i></span>
                    <input type="date" name="end" value="{{ request('end') }}" class="form-control border-start-0 ps-0" style="height: 38px;">
                </div>
            </div>

            {{-- SORT --}}
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label class="form-label text-muted fw-bold mb-2 fs-12 text-uppercase" style="letter-spacing: 0.5px;">Sort By</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-arrow-down-wide-short"></i></span>
                    <select name="sort" class="form-select border-start-0 ps-0" style="height: 38px;">
                        <option value="">Default</option>
                        <option value="user" {{ request('sort')=='user'?'selected':'' }}>By User</option>
                        <option value="product" {{ request('sort')=='product'?'selected':'' }}>By Product</option>
                    </select>
                </div>
            </div>

            {{-- APPLY --}}
            <div class="col-lg-1 col-md-4 col-sm-6">
                <button type="submit" class="btn btn-primary-theme w-100" style="height: 38px;">
                    Apply
                </button>
            </div>
        </form>
    </div>

    {{-- ================= TOP KPI CARDS ================= --}}
    <div class="row g-3 mb-4" id="kpiCardsContainer">
        <!-- Total Enquiries -->
        <div class="col-lg-3 col-md-6">
            <div class="dashboard-card p-4 position-relative">
                <div class="position-absolute" style="right: -10px; bottom: -10px; font-size: 80px; color: rgba(0, 67, 146, 0.02); transform: rotate(-10deg); pointer-events: none;">
                    <i class="fa-solid fa-envelope-open-text"></i>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted text-uppercase fw-bold fs-11" style="letter-spacing: 0.5px;">Total Enquiries</span>
                    <span class="kpi-icon-wrapper" style="background: rgba(0, 67, 146, 0.1); color: #004392;">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <h2 class="text-dark fw-bold mb-1" style="font-size: 32px; font-weight: 800; letter-spacing: -0.5px;">{{ number_format($totalEnquiries) }}</h2>
                    <span class="text-primary fs-12 fw-semibold d-flex align-items-center gap-1">
                        <i class="fa-solid fa-arrow-up-right-dots"></i> Cumulative lifetime leads
                    </span>
                </div>
            </div>
        </div>

        <!-- Today's Enquiries -->
        <div class="col-lg-3 col-md-6">
            <div class="dashboard-card p-4 position-relative">
                <div class="position-absolute" style="right: -10px; bottom: -10px; font-size: 80px; color: rgba(59,130,246,0.02); transform: rotate(-10deg); pointer-events: none;">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted text-uppercase fw-bold fs-11" style="letter-spacing: 0.5px;">Today's Enquiries</span>
                    <span class="kpi-icon-wrapper" style="background: rgba(59,130,246,0.1); color: #3b82f6;">
                        <i class="fa-solid fa-calendar-day"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <h2 class="text-dark fw-bold mb-1" style="font-size: 32px; font-weight: 800; letter-spacing: -0.5px;">{{ number_format($todaysEnquiries) }}</h2>
                    <span class="text-primary fs-12 fw-semibold d-flex align-items-center gap-1">
                        <i class="fa-solid fa-clock"></i> Incoming today
                    </span>
                </div>
            </div>
        </div>

        <!-- Pending Follow-ups -->
        <div class="col-lg-3 col-md-6">
            <div class="dashboard-card p-4 position-relative">
                <div class="position-absolute" style="right: -10px; bottom: -10px; font-size: 80px; color: rgba(245,158,11,0.02); transform: rotate(-10deg); pointer-events: none;">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted text-uppercase fw-bold fs-11" style="letter-spacing: 0.5px;">Pending Follow-ups</span>
                    <span class="kpi-icon-wrapper" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <h2 class="text-dark fw-bold mb-1" style="font-size: 32px; font-weight: 800; letter-spacing: -0.5px;">{{ number_format($pendingFollowups) }}</h2>
                    <span class="text-warning fs-12 fw-semibold d-flex align-items-center gap-1">
                        <i class="fa-solid fa-clock-rotate-left"></i> Scheduled callbacks
                    </span>
                </div>
            </div>
        </div>

        <!-- Converted Leads -->
        <div class="col-lg-3 col-md-6">
            <div class="dashboard-card p-4 position-relative">
                <div class="position-absolute" style="right: -10px; bottom: -10px; font-size: 80px; color: rgba(16,185,129,0.02); transform: rotate(-10deg); pointer-events: none;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted text-uppercase fw-bold fs-11" style="letter-spacing: 0.5px;">Converted Leads</span>
                    <span class="kpi-icon-wrapper" style="background: rgba(16,185,129,0.1); color: #10b981;">
                        <i class="fa-solid fa-circle-check"></i>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="d-flex align-items-baseline gap-2">
                        <h2 class="text-dark fw-bold mb-0" style="font-size: 32px; font-weight: 800; letter-spacing: -0.5px;">{{ number_format($convertedLeads) }}</h2>
                        @php
                            $rate = $totalEnquiries > 0 ? ($convertedLeads / $totalEnquiries) * 100 : 0;
                        @endphp
                        <span class="badge bg-success text-white px-2 py-0.5 fs-10 fw-bold">{{ number_format($rate, 1) }}% Rate</span>
                    </div>
                    <span class="text-success fs-12 fw-semibold d-flex align-items-center gap-1 mt-1">
                        <i class="fa-solid fa-chart-line"></i> Success state
                    </span>
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

    {{-- ================= STATUS BREAKDOWN CARDS ================= --}}
    <div class="row g-3 mb-4" id="statusBreakdownContainer">
        @foreach([
            ['New', $new, '#3b82f6', 'fa-regular fa-envelope', 'bg-primary-subtle', 'text-primary'],
            ['Follow-up', $follow, '#f59e0b', 'fa-solid fa-arrows-spin', 'bg-warning-subtle', 'text-warning'],
            ['Converted', $converted, '#10b981', 'fa-regular fa-handshake', 'bg-success-subtle', 'text-success'],
            ['Lost', $lost, '#ef4444', 'fa-solid fa-user-xmark', 'bg-danger-subtle', 'text-danger']
        ] as $k)
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="dashboard-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 42px; height: 42px; background: {{ $k[2] }}12; color: {{ $k[2] }}; font-size: 1.1rem;">
                                <i class="{{ $k[3] }}"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold text-dark" style="font-size: 22px; letter-spacing: -0.5px;">{{ $k[1] }}</h3>
                                <small class="text-muted fw-bold text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">{{ $k[0] }} Status</small>
                            </div>
                        </div>
                        <span class="badge {{ $k[4] }} {{ $k[5] }} px-2 py-1 fs-10 fw-bold text-uppercase">{{ $k[0] }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ================= CHARTS - SECTION 1 ================= --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="dashboard-card p-4">
                <h5 class="dashboard-card-title">Enquiry Funnel</h5>
                <div id="funnelChart" style="height: 280px;"></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card p-4 text-center">
                <h5 class="dashboard-card-title text-start">Status Distribution</h5>
                <div id="pieChart" style="height: 280px;"></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card p-4">
                <h5 class="dashboard-card-title">Status Trend</h5>
                <div id="lineChart" style="height: 280px;"></div>
            </div>
        </div>
    </div>

    {{-- ================= CHARTS - SECTION 2 ================= --}}
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="dashboard-card p-4">
                <h5 class="dashboard-card-title">Source Analysis</h5>
                <div id="sourceChart" style="height: 280px;"></div>
            </div>
        </div>

        {{-- Top Products / Services --}}
        <div class="col-lg-6" id="topProductsContainer">
            <div class="dashboard-card p-4">
                <h5 class="dashboard-card-title d-flex align-items-center gap-2">
                    <i class="fa-solid fa-trophy text-warning"></i> Top Products / Services
                </h5>
                <div class="d-flex flex-column gap-2 mt-2" style="max-height: 280px; overflow-y: auto;">
                    @forelse($topProducts as $index => $p)
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                            <div class="d-flex align-items-center gap-3">
                                <span class="d-flex align-items-center justify-content-center fw-bold rounded-circle" style="width: 24px; height: 24px; background: {{ $index == 0 ? '#fef3c7' : '#f1f5f9' }}; color: {{ $index == 0 ? '#d97706' : '#64748b' }}; font-size: 11px;">
                                    {{ $index + 1 }}
                                </span>
                                <span class="fw-semibold text-dark fs-13.5">{{ $p->product_service }}</span>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fw-bold fs-11">{{ $p->total }} Enquiries</span>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted fs-14">
                            <i class="fa-solid fa-circle-info d-block fs-24 mb-2 text-muted"></i>
                            No products tracked in this filter.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-card p-4">
                <h5 class="dashboard-card-title">Top 5 Locations</h5>
                <div id="locationBarChart" style="height: 280px;"></div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-card p-4">
                <h5 class="dashboard-card-title">Location Distribution</h5>
                <div id="locationPieChart" style="height: 280px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- ================= HIDDEN AJAX DATA CONTAINER ================= --}}
<div id="chartDataContainer" 
     data-new-count="{{ $newCount ?? 0 }}"
     data-followup-count="{{ $followupCount ?? 0 }}"
     data-converted-count="{{ $convertedCount ?? 0 }}"
     data-lost-count="{{ $lostCount ?? 0 }}"
     data-trend='@json($trend)'
     data-source-wise='@json($sourceWise)'
     data-top-locations='@json($topLocations)'
     style="display: none;">
</div>

{{-- ================= JS ================= --}}
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

<script>
// Declare chart variables globally
let funnelChart = null;
let pieChart = null;
let lineChart = null;
let sourceChart = null;
let locationBarChart = null;
let locationPieChart = null;

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
            // SUBMIT FORM via AJAX
            submitFilterFormAJAX(type);
        });
    });

    // Intercept standard form submit button
    const filterForm = document.getElementById('misFilterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            submitFilterFormAJAX();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX SUBMISSION FUNCTION
    |--------------------------------------------------------------------------
    */
    function submitFilterFormAJAX(clickType = null) {
        const form = document.getElementById('misFilterForm');
        if (!form) return;

        // Visual feedback - lower opacity of updateable content cards
        const containers = [
            document.getElementById('kpiCardsContainer'),
            document.getElementById('statusBreakdownContainer'),
            document.getElementById('topProductsContainer'),
            document.getElementById('overdueBannerContainer')
        ];
        
        containers.forEach(c => {
            if (c) c.style.opacity = '0.55';
        });

        const formData = new FormData(form);
        const searchParams = new URLSearchParams(formData);
        const url = window.location.pathname + '?' + searchParams.toString();

        // Push URL state smoothly so browser back/forward still works
        window.history.pushState({}, '', url);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Replace standard DOM chunks
            const idsToReplace = [
                'kpiCardsContainer',
                'statusBreakdownContainer',
                'topProductsContainer',
                'overdueBannerContainer'
            ];

            idsToReplace.forEach(id => {
                const target = document.getElementById(id);
                const source = doc.getElementById(id);
                if (target && source) {
                    target.innerHTML = source.innerHTML;
                    target.style.opacity = '1';
                }
            });

            // Parse and reload new ECharts data values
            const dataContainer = doc.getElementById('chartDataContainer');
            if (dataContainer) {
                const newCount = parseInt(dataContainer.dataset.newCount || 0);
                const followupCount = parseInt(dataContainer.dataset.followupCount || 0);
                const convertedCount = parseInt(dataContainer.dataset.convertedCount || 0);
                const lostCount = parseInt(dataContainer.dataset.lostCount || 0);
                
                const trend = JSON.parse(dataContainer.dataset.trend || '[]');
                const sourceWise = JSON.parse(dataContainer.dataset.sourceWise || '[]');
                const topLocations = JSON.parse(dataContainer.dataset.topLocations || '[]');

                // ECharts update transitions
                if (funnelChart) {
                    funnelChart.setOption({
                        series: [{
                            data: [
                                { value: newCount, name: 'New' },
                                { value: followupCount, name: 'Follow-up' },
                                { value: convertedCount, name: 'Converted' },
                                { value: lostCount, name: 'Lost' }
                            ]
                        }]
                    });
                }

                if (pieChart) {
                    pieChart.setOption({
                        series: [{
                            data: [
                                { value: newCount, name: 'New' },
                                { value: followupCount, name: 'Follow-up' },
                                { value: convertedCount, name: 'Converted' },
                                { value: lostCount, name: 'Lost' }
                            ]
                        }]
                    });
                }

                if (lineChart) {
                    lineChart.setOption({
                        xAxis: {
                            data: trend.map(i => i.label)
                        },
                        series: [{
                            data: trend.map(i => i.total)
                        }]
                    });
                }

                if (sourceChart) {
                    sourceChart.setOption({
                        xAxis: {
                            data: sourceWise.map(i => i.source)
                        },
                        series: [{
                            data: sourceWise.map(i => i.total)
                        }]
                    });
                }

                if (locationBarChart) {
                    locationBarChart.setOption({
                        xAxis: {
                            data: topLocations.map(i => i.location)
                        },
                        series: [{
                            data: topLocations.map(i => i.total)
                        }]
                    });
                }

                if (locationPieChart) {
                    locationPieChart.setOption({
                        series: [{
                            data: topLocations.map(i => ({ name: i.location, value: i.total }))
                        }]
                    });
                }
            }

            // Sync Date button designs to active state
            const activeType = clickType || document.getElementById('filterType').value;
            document.querySelectorAll(".date-btn").forEach(btn => {
                btn.classList.remove("btn-primary-theme");
                btn.classList.add("btn-light", "text-muted");
                if (btn.dataset.type === activeType) {
                    btn.classList.add("btn-primary-theme");
                    btn.classList.remove("btn-light", "text-muted");
                }
            });
        })
        .catch(err => {
            console.error('AJAX Load Error:', err);
            // Fallback reload if AJAX request fails
            form.submit();
        });
    }

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
        funnelChart = echarts.init(funnelEl);
        funnelChart.setOption({
            color: ['#3b82f6', '#f59e0b', '#10b981', '#ef4444'],
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
                left: '10%',
                label: {
                    show: true,
                    fontFamily: 'Outfit',
                    fontWeight: 600,
                    color: '#475569'
                },
                data: [
                    { value: {{ $newCount ?? 0 }}, name: 'New' },
                    { value: {{ $followupCount ?? 0 }}, name: 'Follow-up' },
                    { value: {{ $convertedCount ?? 0 }}, name: 'Converted' },
                    { value: {{ $lostCount ?? 0 }}, name: 'Lost' }
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
        pieChart = echarts.init(pieEl);
        pieChart.setOption({
            color: ['#3b82f6', '#f59e0b', '#10b981', '#ef4444'],
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
                radius: ['55%', '75%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 8
                },
                label: {
                    show: false
                },
                data: [
                    { value: dataVals[0], name: 'New' },
                    { value: dataVals[1], name: 'Follow-up' },
                    { value: dataVals[2], name: 'Converted' },
                    { value: dataVals[3], name: 'Lost' }
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
        lineChart = echarts.init(lineEl);
        lineChart.setOption({
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
        sourceChart = echarts.init(sourceEl);
        sourceChart.setOption({
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
                        { offset: 0, color: '#3b82f6' },
                        { offset: 1, color: '#004392' }
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
    let locationBarEl = document.getElementById('locationBarChart');
    if (locationBarEl && locations && locations.length) {
        let names = locations.map(i => i.location);
        let values = locations.map(i => i.total);

        locationBarChart = echarts.init(locationBarEl);
        locationBarChart.setOption({
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
    let locationPieEl = document.getElementById('locationPieChart');
    if (locationPieEl && locations && locations.length) {
        locationPieChart = echarts.init(locationPieEl);
        locationPieChart.setOption({
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
                label: {
                    show: false
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