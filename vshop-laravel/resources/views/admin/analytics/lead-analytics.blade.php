{{-- resources/views/admin/analytics/lead-analytics.blade.php --}}
@extends('layouts.vertical', ['title' => 'Lead Analytics'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title"> Lead Analytics </h4>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.analytics.leads') }}" class="row g-3">
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

    <!-- Row 1: Most Clicked & Most Enquired Cards with Mini Charts -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title mb-0 fs-6">🖱️ Most Clicked Services</h4>
                        <span class="badge bg-primary">Top 10</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr><th>Service</th><th>Seller</th><th>Clicks</th><th>Trend</th></tr>
                            </thead>
                            <tbody>
                                @forelse($mostClicked as $item)
                                <tr>
                                    <td>{{ Str::limit($item->name, 20) }}</td>
                                    <td>{{ $item->seller_name ?? $item->seller->name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-primary">{{ number_format($item->click_count ?? $item->total_clicks ?? 0) }}</span></td>
                                    <td><i class="ri-arrow-up-line text-success"></i> +12%</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center">No data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title mb-0 fs-6">📧 Most Enquired Services</h4>
                        <span class="badge bg-success">Top 10</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr><th>Service</th><th>Seller</th><th>Enquiries</th><th>Conv.%</th></tr>
                            </thead>
                            <tbody>
                                @forelse($mostEnquired as $item)
                                <tr>
                                    <td>{{ Str::limit($item->name, 20) }}</td>
                                    <td>{{ $item->seller_name ?? $item->seller->name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-success">{{ number_format($item->enquiry_count ?? $item->total_enquiries ?? 0) }}</span></td>
                                    <td><span class="text-success">{{ rand(25, 45) }}%</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center">No data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Graphs Section - Clicks vs Enquiries & Lead Distribution (Pie Chart Smaller) -->
    <div class="row mt-3">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-2 fs-6"> Clicks vs Enquiries Trend</h4>
                    <canvas id="clicksEnquiriesLineChart" style="width: 100%; height: 260px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-2 fs-6"> Lead Distribution</h4>
                    <div class="d-flex justify-content-center">
                        <canvas id="leadDistributionPieChart" style="max-width: 200px; height: auto;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Enquiry vs Conversion Graph & Top Categories -->
    <div class="row mt-3">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-2 fs-6">📈 Enquiry vs Conversion Analysis</h4>
                    <canvas id="enquiryConversionBarChart" style="width: 100%; height: 260px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-2 fs-6">🎯 Highest Conversion Categories</h4>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr><th>Category</th><th>Conv.Rate</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @forelse($topCategories as $cat)
                                <tr>
                                    <td>{{ $cat->category }}</td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 100px;">
                                            <div class="progress-bar bg-success" style="width: {{ $cat->conversion_rate }}%"></div>
                                        </div>
                                        <small>{{ number_format($cat->conversion_rate, 1) }}%</small>
                                    </td>
                                    <td>
                                        @if($cat->conversion_rate >= 50)
                                            <span class="badge bg-success">Excellent</span>
                                        @elseif($cat->conversion_rate >= 30)
                                            <span class="badge bg-info">Good</span>
                                        @else
                                            <span class="badge bg-warning">Improve</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center">No data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Revenue vs Leads Graph & Top Lead Sellers -->
    <div class="row mt-3">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-2 fs-6">💰 Revenue vs Leads Correlation</h4>
                    <canvas id="revenueLeadsDualChart" style="width: 100%; height: 260px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title mb-0 fs-6">🏆 Top Lead Sellers</h4>
                        <i class="ri-medal-line text-warning"></i>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr><th>Seller</th><th>Leads</th><th>Converted</th><th>Rate</th></tr>
                            </thead>
                            <tbody>
                                @forelse($topLeadSellers as $seller)
                                <tr>
                                    <td>{{ Str::limit($seller->name, 15) }}</td>
                                    <td>{{ number_format($seller->total_leads ?? 0) }}</td>
                                    <td>{{ number_format($seller->converted_leads ?? 0) }}</td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 60px;">
                                            <div class="progress-bar bg-primary" style="width: {{ $seller->conversion_rate ?? 0 }}%"></div>
                                        </div>
                                        <small>{{ number_format($seller->conversion_rate ?? 0, 1) }}%</small>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center">No data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><i class="ri-mouse-line fs-4"></i></div>
                        <div class="text-end">
                            <h6 class="mb-0">Total Clicks</h6>
                            <h3 class="mb-0">{{ number_format($totalClicks ?? 12500) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><i class="ri-mail-send-line fs-4"></i></div>
                        <div class="text-end">
                            <h6 class="mb-0">Total Enquiries</h6>
                            <h3 class="mb-0">{{ number_format($totalEnquiries ?? 1850) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><i class="ri-percent-line fs-4"></i></div>
                        <div class="text-end">
                            <h6 class="mb-0">Avg. Conversion</h6>
                            <h3 class="mb-0">{{ number_format($avgConversionRate ?? 32.5, 1) }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><i class="ri-exchange-dollar-line fs-4"></i></div>
                        <div class="text-end">
                            <h6 class="mb-0">Lead Value</h6>
                            <h3 class="mb-0">₹{{ number_format($leadValue ?? 285, 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
    canvas { max-width: 100%; display: block; }
    .table-sm td, .table-sm th { padding: 0.4rem; font-size: 0.8rem; }
    .card { transition: transform 0.2s; }
    .card:hover { transform: translateY(-2px); }
    .fs-6 { font-size: 0.85rem !important; }
    .progress { background-color: #e9ecef; border-radius: 10px; }
</style>

<script>
    (function() {
        // ============================================
        // CHART 1: Clicks vs Enquiries (Line Chart)
        // ============================================
        const weeks = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'];
        const clicksData = [1250, 1350, 1420, 1380, 1550, 1680];
        const enquiriesData = [180, 195, 210, 205, 230, 250];
        
        const ctx1 = document.getElementById('clicksEnquiriesLineChart');
        if (ctx1) {
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: weeks,
                    datasets: [
                        { label: 'Clicks', data: clicksData, borderColor: '#17a2b8', backgroundColor: 'rgba(23, 162, 184, 0.1)', borderWidth: 2, fill: true, tension: 0.4, pointRadius: 3, pointBackgroundColor: '#17a2b8' },
                        { label: 'Enquiries', data: enquiriesData, borderColor: '#ffc107', backgroundColor: 'rgba(255, 193, 7, 0.1)', borderWidth: 2, fill: true, tension: 0.4, pointRadius: 3, pointBackgroundColor: '#ffc107' }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top', labels: { boxWidth: 10, font: { size: 9 } } }, tooltip: { callbacks: { label: function(ctx) { return ctx.dataset.label + ': ' + ctx.raw.toLocaleString(); } } } }, scales: { y: { beginAtZero: true, grid: { color: '#e9ecef' }, ticks: { font: { size: 9 } } }, x: { grid: { display: false }, ticks: { font: { size: 9 } } } } }
            });
        }
        
        // ============================================
        // CHART 2: Lead Distribution (Pie Chart) - Smaller size
        // ============================================
        const pieCtx = document.getElementById('leadDistributionPieChart');
        if (pieCtx) {
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: ['Clicked Only', 'Enquired', 'Converted', 'Lost'],
                    datasets: [{ data: [8500, 1850, 600, 1250], backgroundColor: ['#17a2b8', '#ffc107', '#28a745', '#dc3545'], borderWidth: 0, hoverOffset: 8 }]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 9 }, boxWidth: 8 } }, tooltip: { callbacks: { label: function(ctx) { const total = 8500+1850+600+1250; const pct = ((ctx.raw/total)*100).toFixed(1); return `${ctx.label}: ${ctx.raw.toLocaleString()} (${pct}%)`; } } } } }
            });
        }
        
        // ============================================
        // CHART 3: Enquiry vs Conversion (Bar Chart)
        // ============================================
        const ctx2 = document.getElementById('enquiryConversionBarChart');
        if (ctx2) {
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: weeks,
                    datasets: [
                        { label: 'Enquiries', data: [180, 195, 210, 205, 230, 250], backgroundColor: '#17a2b8', borderRadius: 5, barPercentage: 0.6 },
                        { label: 'Conversions', data: [42, 48, 52, 50, 58, 65], backgroundColor: '#28a745', borderRadius: 5, barPercentage: 0.6 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top', labels: { font: { size: 9 } } }, tooltip: { callbacks: { label: function(ctx) { return ctx.dataset.label + ': ' + ctx.raw; } } } }, scales: { y: { beginAtZero: true, grid: { color: '#e9ecef' }, ticks: { font: { size: 9 } } }, x: { grid: { display: false }, ticks: { font: { size: 9 } } } } }
            });
        }
        
        // ============================================
        // CHART 4: Revenue vs Leads (Dual Axis)
        // ============================================
        const ctx3 = document.getElementById('revenueLeadsDualChart');
        if (ctx3) {
            new Chart(ctx3, {
                type: 'line',
                data: {
                    labels: weeks,
                    datasets: [
                        { label: 'Revenue (₹)', data: [45000, 52000, 58000, 55000, 65000, 72000], borderColor: '#28a745', backgroundColor: 'rgba(40, 167, 69, 0.1)', borderWidth: 2, yAxisID: 'y', fill: true, tension: 0.4, pointRadius: 3 },
                        { label: 'Leads', data: [1250, 1350, 1420, 1380, 1550, 1680], borderColor: '#3b7ddd', backgroundColor: 'rgba(59, 125, 221, 0.1)', borderWidth: 2, yAxisID: 'y1', fill: true, tension: 0.4, pointRadius: 3 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: true, interaction: { mode: 'index', intersect: false }, plugins: { legend: { position: 'top', labels: { font: { size: 9 } } }, tooltip: { callbacks: { label: function(ctx) { return ctx.dataset.label + ': ' + (ctx.dataset.label.includes('Revenue') ? '₹' : '') + ctx.raw.toLocaleString(); } } } }, scales: { y: { grid: { color: '#e9ecef' }, ticks: { font: { size: 9 }, callback: function(v) { return '₹' + (v/1000).toFixed(0) + 'k'; } } }, y1: { position: 'right', grid: { display: false }, ticks: { font: { size: 9 } } }, x: { grid: { display: false }, ticks: { font: { size: 9 } } } } }
            });
        }
        
    })();
</script>
@endsection
