{{-- resources/views/admin/analytics/analytics-graphs.blade.php --}}
@extends('layouts.vertical', ['title' => 'Analytics Graphs'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">📈 Analytics Graphs - Visual Insights</h4>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.analytics.graphs') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') ?? date('Y-m-d', strtotime('-30 days')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') ?? date('Y-m-d') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary form-control">Apply Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 1: Line Chart + Pie Chart -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-2 fs-6">📊 Clicks vs Enquiries (Area Chart)</h4>
                    <canvas id="clicksEnquiriesChart" style="width: 100%; height: 280px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-2 fs-6">🥧 Conversion Distribution</h4>
                    <canvas id="conversionPieChart" style="width: 100%; height: 230px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Bar Chart + Doughnut Chart -->
    <div class="row mt-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-2 fs-6">📊 Enquiry vs Conversion (Bar Chart)</h4>
                    <canvas id="enquiryConversionChart" style="width: 100%; height: 280px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-2 fs-6">🍩 Enquiry to Conversion Funnel</h4>
                    <canvas id="funnelDoughnutChart" style="width: 100%; height: 230px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Dual Axis Line Chart + Radar Chart -->
    <div class="row mt-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-2 fs-6">💰 Revenue vs Leads (Dual Axis)</h4>
                    <canvas id="revenueLeadsChart" style="width: 100%; height: 280px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-2 fs-6">📈 Performance Radar</h4>
                    <canvas id="performanceRadarChart" style="width: 100%; height: 230px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Horizontal Bar Chart -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-2 fs-6">🏆 Top Performing Categories</h4>
                    <canvas id="topCategoriesChart" style="width: 100%; height: 260px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics Cards - Small -->
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 text-center">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="ri-mouse-line fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Total Clicks</h6>
                            <h3 class="mb-0">{{ number_format($totalClicks ?? 12500) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-2 text-center">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="ri-mail-send-line fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Total Enquiries</h6>
                            <h3 class="mb-0">{{ number_format($totalEnquiries ?? 1850) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-2 text-center">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="ri-percent-line fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Click to Enquiry</h6>
                            <h3 class="mb-0">{{ number_format($clickToEnquiryRate ?? 14.8, 1) }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body py-2 text-center">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="ri-exchange-dollar-line fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Conversion Rate</h6>
                            <h3 class="mb-0">{{ number_format($conversionRate ?? 32.5, 1) }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>


<script>
    (function() {
        // ============================================
        // GRAPH 1 DATA: Clicks vs Enquiries (Area Chart)
        // ============================================
        let graphLabels = [];
        let clicksData = [];
        let enquiriesData = [];
        
        @if(isset($graphData) && count($graphData) > 0)
            graphLabels = @json(array_column($graphData, 'date'));
            clicksData = @json(array_column($graphData, 'clicks'));
            enquiriesData = @json(array_column($graphData, 'enquiries'));
        @else
            graphLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'];
            clicksData = [1250, 1350, 1420, 1380, 1550, 1680];
            enquiriesData = [180, 195, 210, 205, 230, 250];
        @endif
        
        const ctx1 = document.getElementById('clicksEnquiriesChart');
        if (ctx1) {
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: graphLabels,
                    datasets: [
                        { label: 'Clicks', data: clicksData, borderColor: '#17a2b8', backgroundColor: 'rgba(23, 162, 184, 0.15)', borderWidth: 2, fill: true, tension: 0.4, pointRadius: 3, pointHoverRadius: 5 },
                        { label: 'Enquiries', data: enquiriesData, borderColor: '#ffc107', backgroundColor: 'rgba(255, 193, 7, 0.15)', borderWidth: 2, fill: true, tension: 0.4, pointRadius: 3, pointHoverRadius: 5 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: true, interaction: { mode: 'index', intersect: false }, plugins: { tooltip: { callbacks: { label: function(ctx) { return ctx.dataset.label + ': ' + ctx.raw.toLocaleString(); } } }, legend: { position: 'top', labels: { boxWidth: 10, font: { size: 10 } } } }, scales: { y: { grid: { color: '#e9ecef' }, ticks: { font: { size: 9 } } }, x: { grid: { display: false }, ticks: { font: { size: 9 } } } } }
            });
        }
        
        // ============================================
        // CHART 2: Conversion Distribution (Pie Chart - Small)
        // ============================================
        const pieCtx = document.getElementById('conversionPieChart');
        if (pieCtx) {
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: ['Clicked', 'Enquired', 'Converted', 'Completed'],
                    datasets: [{
                        data: [{{ $totalClicks ?? 12500 }}, {{ $totalEnquiries ?? 1850 }}, {{ $totalConversions ?? 600 }}, {{ $totalCompleted ?? 450 }}],
                        backgroundColor: ['#17a2b8', '#ffc107', '#28a745', '#3b7ddd'],
                        borderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 9 }, boxWidth: 8 } }, tooltip: { callbacks: { label: function(ctx) { const total = {{ $totalClicks ?? 12500 }} + {{ $totalEnquiries ?? 1850 }} + {{ $totalConversions ?? 600 }} + {{ $totalCompleted ?? 450 }}; const percentage = ((ctx.raw / total) * 100).toFixed(1); return `${ctx.label}: ${ctx.raw.toLocaleString()} (${percentage}%)`; } } } } }
            });
        }
        
        // ============================================
        // GRAPH 2 DATA: Enquiry vs Conversion (Bar Chart)
        // ============================================
        let enquiryLabels = [];
        let enquiriesData2 = [];
        let conversionsData = [];
        
        @if(isset($enquiryVsConversion) && count($enquiryVsConversion) > 0)
            enquiryLabels = @json(array_column($enquiryVsConversion, 'date'));
            enquiriesData2 = @json(array_column($enquiryVsConversion, 'enquiries'));
            conversionsData = @json(array_column($enquiryVsConversion, 'conversions'));
        @else
            enquiryLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'];
            enquiriesData2 = [180, 195, 210, 205, 230, 250];
            conversionsData = [42, 48, 52, 50, 58, 65];
        @endif
        
        const ctx2 = document.getElementById('enquiryConversionChart');
        if (ctx2) {
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: enquiryLabels,
                    datasets: [
                        { label: 'Enquiries', data: enquiriesData2, backgroundColor: '#17a2b8', borderRadius: 5, barPercentage: 0.6, categoryPercentage: 0.8 },
                        { label: 'Conversions', data: conversionsData, backgroundColor: '#28a745', borderRadius: 5, barPercentage: 0.6, categoryPercentage: 0.8 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top', labels: { font: { size: 10 } } }, tooltip: { callbacks: { label: function(ctx) { return ctx.dataset.label + ': ' + ctx.raw.toLocaleString(); } } } }, scales: { y: { beginAtZero: true, grid: { color: '#e9ecef' }, ticks: { font: { size: 9 } } }, x: { grid: { display: false }, ticks: { font: { size: 9 } } } } }
            });
        }
        
        // ============================================
        // CHART 3: Funnel Doughnut Chart (Small)
        // ============================================
        const doughnutCtx = document.getElementById('funnelDoughnutChart');
        if (doughnutCtx) {
            new Chart(doughnutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Enquiries', 'Converted', 'Completed'],
                    datasets: [{
                        data: [{{ $totalEnquiries ?? 1850 }}, {{ $totalConversions ?? 600 }}, {{ $totalCompleted ?? 450 }}],
                        backgroundColor: ['#ffc107', '#28a745', '#3b7ddd'],
                        borderWidth: 0,
                        cutout: '55%',
                        hoverOffset: 8
                    }]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 9 }, boxWidth: 8 } }, tooltip: { callbacks: { label: function(ctx) { const rate = ((ctx.raw / {{ $totalEnquiries ?? 1850 }}) * 100).toFixed(1); return `${ctx.label}: ${ctx.raw.toLocaleString()} (${rate}%)`; } } } } }
            });
        }
        
        // ============================================
        // GRAPH 3 DATA: Revenue vs Leads (Dual Axis)
        // ============================================
        let revenueLabels = [];
        let revenueData = [];
        let leadsData = [];
        
        @if(isset($revenueVsLeads) && count($revenueVsLeads) > 0)
            revenueLabels = @json(array_column($revenueVsLeads, 'date'));
            revenueData = @json(array_column($revenueVsLeads, 'revenue'));
            leadsData = @json(array_column($revenueVsLeads, 'leads'));
        @else
            revenueLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'];
            revenueData = [45000, 52000, 58000, 55000, 65000, 72000];
            leadsData = [1250, 1350, 1420, 1380, 1550, 1680];
        @endif
        
        const ctx3 = document.getElementById('revenueLeadsChart');
        if (ctx3) {
            new Chart(ctx3, {
                type: 'line',
                data: {
                    labels: revenueLabels,
                    datasets: [
                        { label: 'Revenue (₹)', data: revenueData, borderColor: '#28a745', backgroundColor: 'rgba(40, 167, 69, 0.1)', borderWidth: 2, yAxisID: 'y', fill: true, tension: 0.4, pointRadius: 3 },
                        { label: 'Leads', data: leadsData, borderColor: '#3b7ddd', backgroundColor: 'rgba(59, 125, 221, 0.1)', borderWidth: 2, yAxisID: 'y1', fill: true, tension: 0.4, pointRadius: 3 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: true, interaction: { mode: 'index', intersect: false }, plugins: { legend: { position: 'top', labels: { font: { size: 10 } } }, tooltip: { callbacks: { label: function(ctx) { return ctx.dataset.label + ': ' + (ctx.dataset.label.includes('Revenue') ? '₹' : '') + ctx.raw.toLocaleString(); } } } }, scales: { y: { grid: { color: '#e9ecef' }, ticks: { font: { size: 9 }, callback: function(v) { return '₹' + (v/1000).toFixed(0) + 'k'; } } }, y1: { position: 'right', grid: { display: false }, ticks: { font: { size: 9 } } }, x: { grid: { display: false }, ticks: { font: { size: 9 } } } } }
            });
        }
        
        // ============================================
        // CHART 4: Performance Radar Chart (Small)
        // ============================================
        const radarCtx = document.getElementById('performanceRadarChart');
        if (radarCtx) {
            new Chart(radarCtx, {
                type: 'radar',
                data: {
                    labels: ['Clicks', 'Enquiries', 'Conversions', 'Revenue', 'Retention', 'Satisfaction'],
                    datasets: [{
                        label: 'Score (%)',
                        data: [85, 65, 48, 72, 58, 76],
                        backgroundColor: 'rgba(59, 125, 221, 0.15)',
                        borderColor: '#3b7ddd',
                        borderWidth: 1.5,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 9 } } }, tooltip: { callbacks: { label: function(ctx) { return ctx.dataset.label + ': ' + ctx.raw + '%'; } } } }, scales: { r: { min: 0, max: 100, ticks: { stepSize: 25, font: { size: 8 } }, grid: { color: '#e9ecef' } } } }
            });
        }
        
        // ============================================
        // CHART 5: Horizontal Bar Chart
        // ============================================
        const horizontalCtx = document.getElementById('topCategoriesChart');
        if (horizontalCtx) {
            new Chart(horizontalCtx, {
                type: 'bar',
                data: {
                    labels: ['Electronics', 'Fashion', 'Home & Living', 'Beauty', 'Sports', 'Books'],
                    datasets: [{
                        label: 'Revenue (₹ Lakhs)',
                        data: [125, 98, 76, 54, 42, 28],
                        backgroundColor: '#3b7ddd',
                        borderRadius: 5,
                        barPercentage: 0.7
                    }]
                },
                options: { indexAxis: 'y', responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top', labels: { font: { size: 10 } } }, tooltip: { callbacks: { label: function(ctx) { return 'Revenue: ₹' + ctx.raw + ' Lakhs'; } } } }, scales: { x: { grid: { color: '#e9ecef' }, ticks: { font: { size: 9 } } }, y: { grid: { display: false }, ticks: { font: { size: 9 } } } } }
            });
        }
        
    })();
</script>
@endsection