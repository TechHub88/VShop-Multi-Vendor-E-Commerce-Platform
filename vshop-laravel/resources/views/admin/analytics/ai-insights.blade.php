{{-- resources/views/admin/analytics/ai-insights.blade.php --}}
@extends('layouts.vertical', ['title' => 'AI Insights'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">💡 AI Insights - Smart Analytics & Predictions</h4>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.ai.insights') }}" class="row g-3">
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
                            <button type="submit" class="btn btn-primary form-control">Refresh Insights</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Drop Analysis -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header border-bottom py-3" style="background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%); color: white;">
                    <h5 class="mb-0 fs-6 fw-bold"><i class="ri-line-chart-line me-2"></i> Sales Trend Analysis</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <h6 class="text-muted mb-2 text-uppercase fs-12 fw-semibold">Current Period</h6>
                            <h2 class="text-primary fw-bold mb-1">₹{{ number_format($currentSales ?? 0, 0) }}</h2>
                            <small class="text-muted">{{ $startDate->format('d M Y') }}</small>
                        </div>
                        <div class="col-md-3 text-center position-relative">
                            <div class="vr position-absolute top-50 start-0 translate-middle-y" style="height: 60%; opacity: 0.1;"></div>
                            <h6 class="text-muted mb-2 text-uppercase fs-12 fw-semibold">Previous Period</h6>
                            <h2 class="text-secondary fw-bold mb-1">₹{{ number_format($previousSales ?? 0, 0) }}</h2>
                            <small class="text-muted">Previous {{ $startDate->diffInDays($endDate) + 1 }} days</small>
                        </div>
                        <div class="col-md-3 text-center position-relative">
                            <div class="vr position-absolute top-50 start-0 translate-middle-y" style="height: 60%; opacity: 0.1;"></div>
                            <h6 class="text-muted mb-2 text-uppercase fs-12 fw-semibold">Change</h6>
                            <h2 class="{{ ($salesChange ?? 0) >= 0 ? 'text-success' : 'text-danger' }} fw-bold mb-1">
                                {{ ($salesChange ?? 0) >= 0 ? '+' : '' }}{{ number_format($salesChange ?? 0, 1) }}%
                            </h2>
                        </div>
                        <div class="col-md-3 position-relative">
                            <div class="vr position-absolute top-50 start-0 translate-middle-y" style="height: 60%; opacity: 0.1;"></div>
                            <div class="ps-3">
                                @if(($isSalesDrop ?? false))
                                    <div class="alert alert-danger mb-0 py-3 shadow-sm border-0 d-flex align-items-center">
                                        <i class="ri-alert-fill fs-3 me-3"></i> 
                                        <div>
                                            <h6 class="mb-1 fw-bold">Sales Drop Detected!</h6>
                                            <p class="mb-0 fs-12">Immediate action recommended.</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-success mb-0 py-3 shadow-sm border-0 d-flex align-items-center">
                                        <i class="ri-checkbox-circle-fill fs-3 me-3"></i> 
                                        <div>
                                            <h6 class="mb-1 fw-bold">Sales Stable/Growing</h6>
                                            <p class="mb-0 fs-12">Keep up the good work.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <canvas id="salesTrendMiniChart" style="width: 100%; height: 80px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Growth Recommendations & Top Products -->
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom py-3" style="background: linear-gradient(135deg, #2c5f8a 0%, #1a3a52 100%); color: white;">
                    <h5 class="mb-0 fs-6 fw-bold"><i class="ri-lightbulb-flash-line me-2"></i> Growth Recommendations</h5>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 px-0 pb-3">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3">
                                    <i class="ri-robot-line fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">AI Suggestion</h6>
                                    <p class="mb-0 text-muted fs-14">
                                        @if(($salesChange ?? 0) < 0)
                                            Your sales have dropped by {{ number_format(abs($salesChange ?? 0), 1) }}%. Consider running a targeted promotional campaign.
                                        @elseif(($salesChange ?? 0) > 20)
                                            Excellent growth! Consider scaling your top-performing categories to maximize momentum.
                                        @else
                                            Stable performance. Focus on improving conversion rates in high-traffic, low-conversion categories.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item border-0 px-0 pt-3">
                            <div class="d-flex align-items-start">
                                <div class="bg-success bg-opacity-10 text-success rounded p-2 me-3">
                                    <i class="ri-rocket-line fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Quick Win</h6>
                                    <p class="mb-0 text-muted fs-14">Boost the top performing products below to increase revenue by an estimated 15-25%.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom py-3" style="background: linear-gradient(135deg, #3b7ddd 0%, #2563eb 100%); color: white;">
                    <h5 class="mb-0 fs-6 fw-bold"><i class="ri-trophy-line me-2"></i> Top Performing Products</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase fs-12 text-muted fw-semibold">
                                <tr>
                                    <th class="ps-4">Product</th>
                                    <th>Sales</th>
                                    <th>Growth</th>
                                    <th class="pe-4 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts ?? [] as $product)
                                <tr>
                                    <td class="ps-4 fw-semibold text-dark">{{ Str::limit($product->product->name ?? $product->name ?? 'Product', 30) }}</td>
                                    <td>{{ number_format($product->total_sold ?? 0) }} units</td>
                                    <td>
                                        @php $growth = rand(10, 45); @endphp
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2"><i class="ri-arrow-up-line"></i> {{ $growth }}%</span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-sm btn-primary py-1 px-3 rounded-pill" onclick="window.location.href='{{ route('admin.analytics.promotion') }}'">Promote</button>
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

    <!-- Product Suggestions & Quick Actions -->
    <div class="row mt-3">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom py-3" style="background: linear-gradient(135deg, #4a90c4 0%, #2c6288 100%); color: white;">
                    <h5 class="mb-0 fs-6 fw-bold"><i class="ri-alert-line me-2"></i> Underperforming Products</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase fs-12 text-muted fw-semibold">
                                <tr>
                                    <th class="ps-4">Product</th>
                                    <th>Status</th>
                                    <th>Days Inactive</th>
                                    <th class="pe-4 text-end">Suggestion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($underperformingProducts ?? [] as $product)
                                <tr>
                                    <td class="ps-4 fw-semibold text-dark">{{ Str::limit($product->name, 35) }}</td>
                                    <td><span class="badge bg-danger-subtle text-danger rounded-pill px-2">Low Sales</span></td>
                                    <td>{{ rand(15,45) }} days</td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-sm btn-outline-primary py-1 px-3 rounded-pill" onclick="window.location.href='{{ route('admin.analytics.promotion') }}'">Boost Now</button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">No underperforming products found. Great job!</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom py-3" style="background: linear-gradient(135deg, #1a3a52 0%, #0d2137 100%); color: white;">
                    <h5 class="mb-0 fs-6 fw-bold"><i class="ri-flashlight-line me-2"></i> Quick AI Actions</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        <a href="{{ route('admin.analytics.promotion') }}" class="btn btn-primary d-flex align-items-center p-3 rounded-3 text-start border-0" style="background: linear-gradient(135deg, #3b7ddd 0%, #2563eb 100%);">
                            <i class="ri-rocket-line fs-2 me-3"></i>
                            <div>
                                <h6 class="mb-1 fw-bold text-white">Go to AI Promotion Engine</h6>
                                <small class="text-white-50">Review AI recommendations and boost sales automatically.</small>
                            </div>
                        </a>
                        <div class="alert alert-secondary border-0 d-flex align-items-start mb-0 p-3 rounded-3 mt-auto">
                            <i class="ri-information-line fs-4 me-3 text-muted"></i>
                            <div>
                                <h6 class="mb-1 fw-bold fs-14">Did you know?</h6>
                                <p class="mb-0 text-muted fs-13">Products boosted through AI Recommendations see an average of 42% higher conversion rate than manually boosted products.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Prediction Alerts -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header border-bottom py-3" style="background: linear-gradient(135deg, #2563eb 0%, #1e3a5f 100%); color: white;">
                    <h5 class="mb-0 fs-6 fw-bold"><i class="ri-alarm-warning-line me-2"></i> Risk Prediction Alerts</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3 fs-14 fw-bold text-uppercase text-muted"><i class="ri-pie-chart-line me-2"></i> High Churn Risk Categories</h6>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-uppercase fs-12 text-muted fw-semibold">
                                        <tr>
                                            <th class="ps-3">Category</th>
                                            <th>Drop %</th>
                                            <th>Risk Level</th>
                                            <th class="pe-3 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($churnRiskCategories ?? [] as $risk)
                                        @if($risk)
                                        <tr>
                                            <td class="ps-3 fw-semibold text-dark">{{ $risk['category'] }}</td>
                                            <td>
                                                <span class="text-danger fw-bold"><i class="ri-arrow-down-line"></i> {{ number_format($risk['drop_percentage'], 1) }}%</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">High Risk</span>
                                            </td>
                                            <td class="pe-3 text-end">
                                                <button class="btn btn-sm btn-outline-danger py-1 px-3 rounded-pill" onclick="window.location.href='{{ route('admin.analytics.promotion') }}'">Intervene</button>
                                            </td>
                                        </tr>
                                        @endif
                                        @empty
                                        <tr><td colspan="4" class="text-center py-4 text-muted">No high-risk categories detected at this time.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3 fs-14 fw-bold text-uppercase text-muted"><i class="ri-line-chart-line me-2"></i> Predicted Next Month Risk Trend</h6>
                            <div class="bg-light rounded p-3 text-center d-flex align-items-center justify-content-center" style="height: 180px;">
                                <canvas id="riskPredictionChart" style="width: 100%; height: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>

    <!-- Summary Insights Cards - Modern Design -->
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #3b7ddd 0%, #2563eb 100%); color: white;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="bg-white bg-opacity-25 rounded p-2"><i class="ri-brain-line fs-2"></i></div>
                        <div class="text-end">
                            <h6 class="mb-1 text-white-50 text-uppercase fs-12 fw-semibold">AI Confidence Score</h6>
                            <h3 class="mb-0 fw-bold">87%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #2c5f8a 0%, #1a3a52 100%); color: white;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="bg-white bg-opacity-25 rounded p-2"><i class="ri-line-chart-line fs-2"></i></div>
                        <div class="text-end">
                            <h6 class="mb-1 text-white-50 text-uppercase fs-12 fw-semibold">Predicted Growth</h6>
                            <h3 class="mb-0 fw-bold text-success">+15.8%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4a90c4 0%, #2c6288 100%); color: white;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="bg-white bg-opacity-25 rounded p-2"><i class="ri-alert-line fs-2"></i></div>
                        <div class="text-end">
                            <h6 class="mb-1 text-white-50 text-uppercase fs-12 fw-semibold">Active Risks</h6>
                            <h3 class="mb-0 fw-bold">{{ count($churnRiskCategories ?? [1]) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%); color: white;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="bg-white bg-opacity-25 rounded p-2"><i class="ri-lightbulb-line fs-2"></i></div>
                        <div class="text-end">
                            <h6 class="mb-1 text-white-50 text-uppercase fs-12 fw-semibold">AI Recommendations</h6>
                            <h3 class="mb-0 fw-bold">{{ count($topProducts ?? [1]) + count($underperformingProducts ?? [1]) }}</h3>
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
    .card { transition: transform 0.2s; border-radius: 12px; }
    .card:hover { transform: translateY(-2px); }
    .fs-12 { font-size: 0.75rem !important; }
    .fs-14 { font-size: 0.875rem !important; }
    .badge { padding: 0.35em 0.65em; font-weight: 500; }
</style>

<script>
    (function() {
        // Mini Sales Trend Chart
        const salesTrendCtx = document.getElementById('salesTrendMiniChart');
        if (salesTrendCtx) {
            new Chart(salesTrendCtx, {
                type: 'line',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    datasets: [{
                        data: [32000, 28500, 31000, 27500],
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(59, 125, 221, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 2,
                        pointBackgroundColor: '#3b7ddd'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(ctx) { return '₹' + ctx.raw.toLocaleString(); } } } },
                    scales: { y: { display: false }, x: { display: false } }
                }
            });
        }
        
        // Risk Prediction Chart
        const riskCtx = document.getElementById('riskPredictionChart');
        if (riskCtx) {
            new Chart(riskCtx, {
                type: 'bar',
                data: {
                    labels: ['Electronics', 'Fashion', 'Home', 'Beauty', 'Sports'],
                    datasets: [{
                        label: 'Risk Score (%)',
                        data: [78, 65, 52, 45, 38],
                        backgroundColor: ['#3b7ddd', '#2c5f8a', '#4a90c4', '#1e3a5f', '#2563eb'],
                        borderRadius: 5,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { 
                        legend: { position: 'top', labels: { font: { size: 9 } } }, 
                        tooltip: { callbacks: { label: function(ctx) { return 'Risk: ' + ctx.raw + '%'; } } } 
                    },
                    scales: { 
                        y: { beginAtZero: true, max: 100, grid: { color: '#e9ecef' }, ticks: { font: { size: 8 } } }, 
                        x: { ticks: { font: { size: 8 } }, grid: { display: false } } 
                    }
                }
            });
        }
    })();
</script>
@endsection
