@extends('layouts.vertical', ['title' => 'Funnel Analytics'])

@section('css')
<style>
    :root {
        --primary-color: #004392;
        --primary-hover: #002d62;
        --border-color: #e2e8f0;
        
        /* Funnel Colors */
        --color-visitors: #3b82f6;   /* Blue */
        --color-views: #14b8a6;      /* Teal/Cyan */
        --color-enquiries: #eab308;  /* Yellow */
        --color-checkout: #f97316;   /* Orange */
        --color-orders: #ef4444;     /* Red */
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

    .funnel-table th {
        font-weight: 600;
        color: #475569;
        font-size: 0.825rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .funnel-table td {
        font-size: 0.9rem;
        color: #1e293b;
        vertical-align: middle;
    }

    .funnel-badge {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }

    .badge-visitors { background-color: var(--color-visitors); }
    .badge-views { background-color: var(--color-views); }
    .badge-enquiries { background-color: var(--color-enquiries); }
    .badge-checkout { background-color: var(--color-checkout); }
    .badge-orders { background-color: var(--color-orders); }

    .fs-11 { font-size: 0.725rem !important; }
    .fs-10 { font-size: 0.65rem !important; }
    .fs-13 { font-size: 0.8125rem !important; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    <!-- Top Header Title and Filter Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h3 class="text-dark fw-bold mb-0"> FUNNEL ANALYTICS</h3>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('admin.analytics.funnel') }}" class="d-flex align-items-center gap-2" id="filterForm">
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
                            <li><a class="dropdown-item" href="#">All Traffic</a></li>
                            <li><a class="dropdown-item" href="#">Organic Search</a></li>
                            <li><a class="dropdown-item" href="#">Paid Ads</a></li>
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
                    <a href="{{ route('admin.analytics.funnel') }}" class="btn btn-sm btn-white border"><i class="bx bx-refresh"></i></a>
                </div>
            </div>
        </div>
    </div>

    @php
        // Ratios and Calculations based on funnelData
        $visitorsVal = $funnelData['views'];
        $viewsVal = $funnelData['clicks'];
        $enquiriesVal = $funnelData['enquiries'];
        $checkoutVal = $funnelData['orders'];
        $ordersVal = $funnelData['completed'];

        // Drops and Conversions relative to previous stage
        // Views drop relative to visitors
        $viewsDropRate = $visitorsVal > 0 ? (($visitorsVal - $viewsVal) / $visitorsVal) * 100 : 0;
        $viewsConvRate = $visitorsVal > 0 ? ($viewsVal / $visitorsVal) * 100 : 0;

        // Enquiries drop relative to views
        $enquiriesDropRate = $viewsVal > 0 ? (($viewsVal - $enquiriesVal) / $viewsVal) * 100 : 0;
        $enquiriesConvRate = $viewsVal > 0 ? ($enquiriesVal / $viewsVal) * 100 : 0;

        // Checkout drop relative to enquiries
        $checkoutDropRate = $enquiriesVal > 0 ? (($enquiriesVal - $checkoutVal) / $enquiriesVal) * 100 : 0;
        $checkoutConvRate = $enquiriesVal > 0 ? ($checkoutVal / $enquiriesVal) * 100 : 0;

        // Orders drop relative to checkout
        $ordersDropRate = $checkoutVal > 0 ? (($checkoutVal - $ordersVal) / $checkoutVal) * 100 : 0;
        $ordersConvRate = $checkoutVal > 0 ? ($ordersVal / $checkoutVal) * 100 : 0;

        // Determine Highest Drop-off Stage by percentage
        $drops = [
            'Service Views' => $viewsDropRate,
            'Enquiries' => $enquiriesDropRate,
            'Checkout' => $checkoutDropRate,
            'Orders' => $ordersDropRate
        ];
        arsort($drops);
        $highestDropStage = key($drops);
    @endphp

    <!-- Main Funnel Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border shadow-sm rounded-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <!-- Funnel Chart Left Column -->
                        <div class="col-lg-5 text-center">
                            <div id="funnelChartContainer" style="height: 380px; width: 100%;"></div>
                        </div>

                        <!-- Funnel Detailed Table Right Column -->
                        <div class="col-lg-7">
                            <div class="table-responsive">
                                <table class="table table-centered funnel-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Stage</th>
                                            <th class="text-end">Users</th>
                                            <th class="text-end">Drop-off %</th>
                                            <th class="text-end">Conversion %</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <span class="funnel-badge badge-visitors"></span>
                                                <strong>Visitors</strong>
                                            </td>
                                            <td class="text-end fw-semibold text-primary">{{ number_format($visitorsVal) }}</td>
                                            <td class="text-end text-muted">-</td>
                                            <td class="text-end fw-semibold">100%</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="funnel-badge badge-views"></span>
                                                <strong>Service Views</strong>
                                            </td>
                                            <td class="text-end fw-semibold text-primary">{{ number_format($viewsVal) }}</td>
                                            <td class="text-end text-danger fw-semibold">{{ number_format($viewsDropRate, 2) }}%</td>
                                            <td class="text-end fw-semibold">{{ number_format($viewsConvRate, 2) }}%</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="funnel-badge badge-enquiries"></span>
                                                <strong>Enquiries</strong>
                                            </td>
                                            <td class="text-end fw-semibold text-primary">{{ number_format($enquiriesVal) }}</td>
                                            <td class="text-end text-danger fw-semibold">{{ number_format($enquiriesDropRate, 2) }}%</td>
                                            <td class="text-end fw-semibold">{{ number_format($enquiriesConvRate, 2) }}%</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="funnel-badge badge-checkout"></span>
                                                <strong>Checkout</strong>
                                            </td>
                                            <td class="text-end fw-semibold text-primary">{{ number_format($checkoutVal) }}</td>
                                            <td class="text-end text-danger fw-semibold">{{ number_format($checkoutDropRate, 2) }}%</td>
                                            <td class="text-end fw-semibold">{{ number_format($checkoutConvRate, 2) }}%</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="funnel-badge badge-orders"></span>
                                                <strong>Orders</strong>
                                            </td>
                                            <td class="text-end fw-semibold text-primary">{{ number_format($ordersVal) }}</td>
                                            <td class="text-end text-danger fw-semibold">{{ number_format($ordersDropRate, 2) }}%</td>
                                            <td class="text-end fw-semibold">{{ number_format($ordersConvRate, 2) }}%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Heat Scale Bar -->
                            <div class="d-flex align-items-center mt-4 pt-3 border-top">
                                <span class="fs-11 text-muted fw-semibold me-2">Low</span>
                                <div class="flex-grow-1 rounded-pill" style="height: 8px; background: linear-gradient(to right, #89f7fe, #66a6ff, #004392);"></div>
                                <span class="fs-11 text-muted fw-semibold ms-2">High</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- Overall Conversion Rate -->
        <div class="col-md-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase fs-11 fw-semibold mb-2">Overall Conversion Rate</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($overallConversionRate, 2) }}%</h3>
                </div>
            </div>
        </div>

        <!-- Highest Drop-off Stage -->
        <div class="col-md-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase fs-11 fw-semibold mb-2">Highest Drop-off Stage</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $highestDropStage }}</h3>
                </div>
            </div>
        </div>

        <!-- Total Enquiries -->
        <div class="col-md-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase fs-11 fw-semibold mb-2">Total Enquiries</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($enquiriesVal) }}</h3>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="col-md-3">
            <div class="card kpi-card">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase fs-11 fw-semibold mb-2">Total Orders</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($ordersVal) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row (Drop-off by Category & Comparative Funnel) -->
    <div class="row g-3">
        <!-- Drop-off by Category -->
        <div class="col-lg-6">
            <div class="card border shadow-sm rounded-3">
                <div class="card-body">
                    <h4 class="header-title mb-3">Drop-off by Category</h4>
                    <div style="position: relative; height: 320px;">
                        <canvas id="categoryDropoffChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Funnel Comparison -->
        <div class="col-lg-6">
            <div class="card border shadow-sm rounded-3">
                <div class="card-body">
                    <h4 class="header-title mb-3">Category Funnel Comparison</h4>
                    <div style="position: relative; height: 320px;">
                        <canvas id="categoryComparisonChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ECharts for Funnel -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
<!-- Chart.js for comparative charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    window.addEventListener('load', function() {
        console.log('Funnel Analytics - Initializing charts...');

        // ============================================
        // 1. FUNNEL CHART USING ECHARTS
        // ============================================
        const funnelContainer = document.getElementById('funnelChartContainer');
        if (funnelContainer) {
            const funnelChart = echarts.init(funnelContainer);
            
            const option = {
                tooltip: {
                    trigger: 'item',
                    formatter: function(params) {
                        return `<strong>${params.name}</strong><br/>Users: ${params.value.toLocaleString()}<br/>Percentage: ${params.percent}%`;
                    }
                },
                legend: {
                    show: false
                },
                series: [
                    {
                        name: 'Conversion Funnel',
                        type: 'funnel',
                        left: '10%',
                        top: 20,
                        bottom: 20,
                        width: '80%',
                        min: 0,
                        max: {{ $visitorsVal }},
                        minSize: '15%',
                        maxSize: '100%',
                        sort: 'descending',
                        gap: 6,
                        label: {
                            show: true,
                            position: 'inside',
                            formatter: function(params) {
                                return params.name + '\n' + params.value.toLocaleString();
                            },
                            color: '#fff',
                            fontSize: 12,
                            fontWeight: 'bold',
                            lineHeight: 16
                        },
                        labelLine: {
                            show: false
                        },
                        itemStyle: {
                            borderColor: '#fff',
                            borderWidth: 2,
                            borderRadius: 8,
                            shadowBlur: 8,
                            shadowOffsetX: 0,
                            shadowOffsetY: 3,
                            shadowColor: 'rgba(0,0,0,0.1)'
                        },
                        data: [
                            { value: {{ $visitorsVal }}, name: 'Visitors', itemStyle: { color: '#3b82f6' } },
                            { value: {{ $viewsVal }}, name: 'Service Views', itemStyle: { color: '#14b8a6' } },
                            { value: {{ $enquiriesVal }}, name: 'Enquiries', itemStyle: { color: '#eab308' } },
                            { value: {{ $checkoutVal }}, name: 'Checkout', itemStyle: { color: '#f97316' } },
                            { value: {{ $ordersVal }}, name: 'Orders', itemStyle: { color: '#ef4444' } }
                        ]
                    }
                ]
            };
            
            funnelChart.setOption(option);
            window.addEventListener('resize', function() { funnelChart.resize(); });
        }

        let categoryData = @json($categoryFunnel);
        let catLabels = categoryData.map(c => c.category);
        
        let dropOffRates = categoryData.map(c => {
            return c.views > 0 ? Math.round(((c.views - c.completed) / c.views) * 100) : 0;
        });

        let catVisitors = categoryData.map(c => c.views);
        let catViews = categoryData.map(c => c.views);
        let catEnquiries = categoryData.map(c => c.enquiries);
        let catOrders = categoryData.map(c => c.orders);

        // ============================================
        // 2. CATEGORY DROP-OFF BAR CHART
        // ============================================
        const dropoffCtx = document.getElementById('categoryDropoffChart');
        if (dropoffCtx) {
            new Chart(dropoffCtx, {
                type: 'bar',
                data: {
                    labels: catLabels,
                    datasets: [{
                        label: 'Drop-off Rate (%)',
                        data: dropOffRates,
                        backgroundColor: '#3b82f6',
                        hoverBackgroundColor: '#2563eb',
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 32
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
                                    return 'Drop-off: ' + ctx.raw + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                callback: function(value) { return value + '%'; },
                                color: '#94a3b8',
                                font: { size: 10 }
                            },
                            border: { display: false },
                            min: 0,
                            max: 100
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#475569', font: { size: 11 } },
                            border: { display: false }
                        }
                    }
                }
            });
        }

        // ============================================
        // 3. CATEGORY FUNNEL COMPARISON STACKED CHART
        // ============================================
        const comparisonCtx = document.getElementById('categoryComparisonChart');
        if (comparisonCtx) {
            new Chart(comparisonCtx, {
                type: 'bar',
                data: {
                    labels: catLabels,
                    datasets: [
                        {
                            label: 'Visitors',
                            data: catVisitors,
                            backgroundColor: '#3b82f6'
                        },
                        {
                            label: 'Views',
                            data: catViews,
                            backgroundColor: '#14b8a6'
                        },
                        {
                            label: 'Enquiries',
                            data: catEnquiries,
                            backgroundColor: '#eab308'
                        },
                        {
                            label: 'Orders',
                            data: catOrders,
                            backgroundColor: '#ef4444'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                boxHeight: 12,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 15,
                                font: { size: 11 }
                            }
                        }
                    },
                    scales: {
                        y: {
                            stacked: true,
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                callback: function(value) { return value + '%'; },
                                color: '#94a3b8',
                                font: { size: 10 }
                            },
                            border: { display: false },
                            min: 0,
                            max: 100
                        },
                        x: {
                            stacked: true,
                            grid: { display: false },
                            ticks: { color: '#475569', font: { size: 11 } },
                            border: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
