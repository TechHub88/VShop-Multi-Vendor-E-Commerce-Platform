@extends('layouts.vertical', ['title' => 'Sales Analytics'])

@section('css')
<style>
    :root {
        --primary-color: #004392;
        --primary-hover: #002d62;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --border-color: #e2e8f0;
    }

    .kpi-card {
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
        border-radius: 10px;
        background: #fff;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08);
    }

    .trend-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 2px;
    }
    .trend-badge.trend-up {
        color: var(--success-color);
        background-color: rgba(16, 185, 129, 0.1);
    }
    .trend-badge.trend-down {
        color: var(--danger-color);
        background-color: rgba(239, 68, 68, 0.1);
    }

    .header-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
    }

    .btn-white {
        background: #fff;
        color: #64748b;
        border: 1px solid var(--border-color);
    }
    .btn-white:hover {
        background: #f8fafc;
        color: #1e293b;
    }

    /* Heatmap styling */
    .heatmap-cell {
        cursor: pointer;
        transition: transform 0.1s;
    }
    .heatmap-cell:hover {
        transform: scale(1.15);
        z-index: 10;
        outline: 1px solid #6f42c1;
    }

    .fs-11 { font-size: 0.725rem !important; }
    .fs-10 { font-size: 0.65rem !important; }
    .fs-13 { font-size: 0.8125rem !important; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    <!-- Top Filter Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="text-primary fw-bold mb-0">Sales Analytics</h4>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('admin.analytics.sales') }}" class="d-flex align-items-center gap-2" id="filterForm">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bx bx-calendar"></i></span>
                            <input type="date" name="start_date" class="form-control form-control-sm border-start-0" value="{{ $startDate->format('Y-m-d') }}" onchange="this.form.submit()">
                            <span class="input-group-text bg-white border-start-0 border-end-0">to</span>
                            <input type="date" name="end_date" class="form-control form-control-sm border-end-0" value="{{ $endDate->format('Y-m-d') }}" onchange="this.form.submit()">
                        </div>
                    </form>
                    
                    <!-- Filter Button -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-white dropdown-toggle border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">All Sales</a></li>
                            <li><a class="dropdown-item" href="#">Boosted Ads Sales</a></li>
                        </ul>
                    </div>
                    
                    <!-- Export Button -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-white dropdown-toggle border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-export me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bx bx-file me-1"></i> Export CSV</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bx bx-spreadsheet me-1"></i> Export Excel</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bx bx-file-blank me-1"></i> Export PDF</a></li>
                        </ul>
                    </div>

                    <!-- Reload -->
                    <a href="{{ route('admin.analytics.sales') }}" class="btn btn-sm btn-white border"><i class="bx bx-refresh"></i></a>
                </div>
            </div>
        </div>
    </div>

    @php
        $grossRevenue = $totalRevenue ?? 0;
        $refundsVal = $totalRefunds ?? 0;
        $netRevenue = $grossRevenue - $refundsVal;
        $totalOrdersVal = $totalOrders ?? 0;
        $avgOrderValueVal = $avgOrderValue ?? 0;
    @endphp

    <!-- Summary KPI Cards -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
        <!-- Gross Revenue -->
        <div class="col">
            <div class="card kpi-card h-100">
                <div class="card-body py-3">
                    <h6 class="text-muted text-uppercase fs-11 fw-semibold mb-2">Gross Revenue</h6>
                    <h3 class="fw-bold mb-1 text-dark">
                        ₹{{ $grossRevenue >= 100000 ? round($grossRevenue / 100000, 1) . 'L' : number_format($grossRevenue) }}
                    </h3>
                    <span class="trend-badge trend-up"><i class="bx bx-up-arrow-alt"></i> +15.8%</span>
                </div>
            </div>
        </div>

        <!-- Net Revenue -->
        <div class="col">
            <div class="card kpi-card h-100">
                <div class="card-body py-3">
                    <h6 class="text-muted text-uppercase fs-11 fw-semibold mb-2">Net Revenue</h6>
                    <h3 class="fw-bold mb-1 text-dark">
                        ₹{{ $netRevenue >= 100000 ? round($netRevenue / 100000, 1) . 'L' : number_format($netRevenue) }}
                    </h3>
                    <span class="trend-badge trend-up"><i class="bx bx-up-arrow-alt"></i> +14.3%</span>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="col">
            <div class="card kpi-card h-100">
                <div class="card-body py-3">
                    <h6 class="text-muted text-uppercase fs-11 fw-semibold mb-2">Total Orders</h6>
                    <h3 class="fw-bold mb-1 text-dark">{{ number_format($totalOrdersVal) }}</h3>
                    <span class="trend-badge trend-up"><i class="bx bx-up-arrow-alt"></i> +12.3%</span>
                </div>
            </div>
        </div>

        <!-- Avg. Order Value -->
        <div class="col">
            <div class="card kpi-card h-100">
                <div class="card-body py-3">
                    <h6 class="text-muted text-uppercase fs-11 fw-semibold mb-2">Avg. Order Value</h6>
                    <h3 class="fw-bold mb-1 text-dark">₹{{ number_format($avgOrderValueVal) }}</h3>
                    <span class="trend-badge trend-up"><i class="bx bx-up-arrow-alt"></i> +9.8%</span>
                </div>
            </div>
        </div>

        <!-- Refunds -->
        <div class="col">
            <div class="card kpi-card h-100">
                <div class="card-body py-3">
                    <h6 class="text-muted text-uppercase fs-11 fw-semibold mb-2">Refunds</h6>
                    <h3 class="fw-bold mb-1 text-dark">
                        ₹{{ $refundsVal >= 100000 ? round($refundsVal / 100000, 1) . 'L' : ($refundsVal >= 1000 ? round($refundsVal / 1000, 1) . 'K' : number_format($refundsVal)) }}
                    </h3>
                    <span class="trend-badge trend-down"><i class="bx bx-down-arrow-alt"></i> -4.3%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- First Row of Charts (Revenue Trend & Heatmap) -->
    <div class="row g-3">
        <!-- Revenue Trend Line Chart -->
        <div class="col-lg-7">
            <div class="card h-100 border shadow-sm rounded-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title mb-0">Revenue Trend</h4>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-white dropdown-toggle border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Daily
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Daily</a></li>
                                <li><a class="dropdown-item" href="#">Weekly</a></li>
                                <li><a class="dropdown-item" href="#">Monthly</a></li>
                            </ul>
                        </div>
                    </div>
                    <div style="position: relative; height: 320px;">
                        <canvas id="revenueTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peak Sales Hours Heatmap -->
        <div class="col-lg-5">
            <div class="card h-100 border shadow-sm rounded-3">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-3">
                        <h4 class="header-title mb-1">Peak Sales Hours</h4>
                        <p class="text-muted fs-11 mb-3">Hourly purchase density analysis across the week.</p>
                    </div>

                    @php
                        // Initialize a 24-hour map
                        $hourlyMap = [];
                        for ($i = 0; $i < 24; $i++) {
                            $hourlyMap[$i] = 0;
                        }
                        foreach ($peakPeriods as $p) {
                            $hourlyMap[$p->hour] = $p->orders;
                        }

                        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                    @endphp

                    <!-- Heatmap Grid Container -->
                    <div class="heatmap-container">
                        @foreach ($days as $dayIdx => $day)
                            <div class="d-flex align-items-center mb-1">
                                <span class="text-muted fw-semibold fs-11 me-2" style="width: 30px; text-align: left;">{{ $day }}</span>
                                <div class="d-flex flex-grow-1 gap-1">
                                    @for ($h = 0; $h < 24; $h++)
                                        @php
                                            // Cell calculation based purely on database map
                                            $cellVal = $hourlyMap[$h];
                                            
                                            // Determine purple scale opacity based on volume
                                            $maxDensity = max(1, max($hourlyMap));
                                            $opacity = $cellVal > 0 ? min(0.1 + ($cellVal / $maxDensity) * 0.9, 1.0) : 0.05;
                                        @endphp
                                        <div class="heatmap-cell flex-fill rounded-1" 
                                             style="height: 18px; background-color: rgba(106, 57, 198, {{ $opacity }});" 
                                             data-bs-toggle="tooltip" 
                                             title="{{ $day }} {{ $h == 0 ? '12 AM' : ($h < 12 ? $h.' AM' : ($h == 12 ? '12 PM' : ($h-12).' PM')) }}: {{ $cellVal }} Orders">
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @endforeach

                        <!-- X-axis Timeline Labels -->
                        <div class="d-flex justify-content-between text-muted fs-10 mt-2 px-1" style="margin-left: 38px;">
                            <span>12 AM</span>
                            <span>6 AM</span>
                            <span>12 PM</span>
                            <span>6 PM</span>
                            <span>11 PM</span>
                        </div>
                    </div>

                    <!-- Heatmap Legend -->
                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-3 text-muted fs-11">
                        <span>Interactive Tooltips Active</span>
                        <div class="d-flex align-items-center gap-2">
                            <span>Low</span>
                            <div class="d-flex gap-1" style="width: 80px; height: 10px;">
                                <div class="rounded-1" style="background-color: rgba(106, 57, 198, 0.1); width: 20%; height: 100%;"></div>
                                <div class="rounded-1" style="background-color: rgba(106, 57, 198, 0.35); width: 20%; height: 100%;"></div>
                                <div class="rounded-1" style="background-color: rgba(106, 57, 198, 0.6); width: 20%; height: 100%;"></div>
                                <div class="rounded-1" style="background-color: rgba(106, 57, 198, 0.8); width: 20%; height: 100%;"></div>
                                <div class="rounded-1" style="background-color: rgba(106, 57, 198, 1.0); width: 20%; height: 100%;"></div>
                            </div>
                            <span>High</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row of Charts (Category Sales & Top Sellers) -->
    <div class="row g-3 mt-1">
        <!-- Category-wise Sales Bar Chart -->
        <div class="col-lg-7">
            <div class="card h-100 border shadow-sm rounded-3">
                <div class="card-body">
                    <h4 class="header-title mb-3">Category Wise Sales</h4>
                    <div style="position: relative; height: 280px;">
                        <canvas id="categorySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Sellers Revenue list (Progress Bars) -->
        <div class="col-lg-5">
            <div class="card h-100 border shadow-sm rounded-3">
                <div class="card-body">
                    <h4 class="header-title fw-bold text-dark mb-3">Top Sellers Revenue</h4>
                    <p class="text-muted fs-11 mb-3">Leading seller portfolios by generated revenue.</p>
                    <div class="d-flex flex-column gap-3 mt-2">
                        @php
                            $maxSellerRevenue = $topSellers->count() > 0 ? $topSellers->max('revenue') : 1;
                        @endphp
                        
                        @forelse($topSellers as $seller)
                            @php
                                $percent = min(100, round(($seller->revenue / $maxSellerRevenue) * 100));
                            @endphp
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold text-dark fs-13">{{ $seller->seller_name }}</span>
                                    <span class="text-muted fw-bold fs-13">₹{{ $seller->revenue >= 100000 ? round($seller->revenue / 100000, 2) . 'L' : number_format($seller->revenue) }}</span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 4px; background-color: #f1f5f9;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%; background-color: #3b7ddd; border-radius: 4px;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <span class="text-muted fs-13">No seller revenue data found for this period.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    window.addEventListener('load', function() {
        console.log('Sales Analytics - Initializing charts...');

        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        }

        let revenueLabels = @json($revenueTrends->pluck('date') ?? []);
        let revenueValues = @json($revenueTrends->pluck('revenue') ?? []);
        let orderValues = @json($revenueTrends->pluck('orders') ?? []);

        // Format labels from dates to 'DD MMM'
        revenueLabels = revenueLabels.map(d => {
            const dateObj = new Date(d);
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            return dateObj.getDate() + ' ' + months[dateObj.getMonth()];
        });

        // ============================================
        // CHART 1: REVENUE TRENDS (LINE CHART)
        // ============================================
        const revenueCtx = document.getElementById('revenueTrendsChart');
        if (revenueCtx) {
            try {
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: revenueLabels,
                        datasets: [
                            {
                                label: 'Revenue (₹)',
                                data: revenueValues,
                                borderColor: '#3b7ddd',
                                backgroundColor: 'rgba(59, 125, 221, 0.04)',
                                borderWidth: 2.5,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#3b7ddd',
                                pointBorderColor: '#fff',
                                pointRadius: 2.5,
                                pointHoverRadius: 5,
                                yAxisID: 'y'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let value = context.raw;
                                        return 'Revenue: ₹' + value.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                grid: { color: '#f1f5f9' },
                                ticks: { 
                                    callback: function(value) { 
                                        if (value >= 100000) return '₹' + (value / 100000) + 'L';
                                        if (value >= 1000) return '₹' + (value / 1000) + 'K';
                                        return '₹' + value;
                                    },
                                    color: '#94a3b8',
                                    font: { size: 10 }
                                },
                                border: { display: false }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { 
                                    autoSkip: true, 
                                    maxTicksLimit: 7, 
                                    color: '#94a3b8',
                                    font: { size: 10 }
                                },
                                border: { display: false }
                            }
                        }
                    }
                });
            } catch(e) {
                console.error('Revenue chart creation error:', e);
            }
        }

        // ============================================
        // DB DATA FOR CATEGORY CHART
        // ============================================
        let catLabels = @json($categorySales->pluck('category') ?? []);
        let catValues = @json($categorySales->pluck('revenue') ?? []);

        // ============================================
        // CHART 2: CATEGORY WISE SALES (BAR CHART)
        // ============================================
        const categoryCtx = document.getElementById('categorySalesChart');
        if (categoryCtx) {
            try {
                new Chart(categoryCtx, {
                    type: 'bar',
                    data: {
                        labels: catLabels,
                        datasets: [{
                            data: catValues,
                            backgroundColor: '#3b7ddd',
                            borderRadius: 6,
                            borderSkipped: false,
                            barThickness: 28,
                            maxBarThickness: 35
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        return 'Revenue: ₹' + ctx.raw.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 100000) return '₹' + (value / 100000) + 'L';
                                        if (value >= 1000) return '₹' + (value / 1000) + 'K';
                                        return '₹' + value;
                                    },
                                    color: '#94a3b8',
                                    font: { size: 10 }
                                },
                                border: { display: false }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { 
                                    color: '#475569',
                                    font: { size: 10 }
                                },
                                border: { display: false }
                            }
                        }
                    }
                });
            } catch(e) {
                console.error('Category chart creation error:', e);
            }
        }
    });
</script>
@endsection
