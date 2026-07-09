@extends('layouts.vertical', ['title' => 'Campaign ROI Analytics'])

@section('content')
<div class="container-fluid">
    <!-- Page Title & Filter Row -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 class="page-title">Campaign ROI Analytics</h4>
       
        </div>
        
    </div>

    <!-- Metrics Cards (White background, subtle shadow) -->
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <h6 class="text-muted mb-1 fs-12 text-uppercase">Total Spend</h6>
                    <h3 class="mb-0 fw-bold" id="totalSpend">₹{{ number_format($totalSpend ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <h6 class="text-muted mb-1 fs-12 text-uppercase">Leads Generated</h6>
                    <h3 class="mb-0 fw-bold" id="totalLeads">{{ number_format($totalLeads ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <h6 class="text-muted mb-1 fs-12 text-uppercase">Cost Per Lead (CPL)</h6>
                    <h3 class="mb-0 fw-bold" id="avgCPA">₹{{ number_format($avgCPA ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <h6 class="text-muted mb-1 fs-12 text-uppercase">Conversions</h6>
                    <h3 class="mb-0 fw-bold" id="totalConversions">{{ number_format($totalConversions ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <h6 class="text-muted mb-1 fs-12 text-uppercase">Avg ROI</h6>
                    <h3 class="mb-0 fw-bold {{ ($avgROI ?? 0) >= 0 ? 'text-success' : 'text-danger' }}" id="avgROI">{{ number_format($avgROI ?? 0, 1) }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Performance Table -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="fw-bold text-dark mb-0">Campaign Performance</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-12 text-muted fw-semibold">
                        <tr>
                            <th class="ps-4">Campaign</th>
                            <th>Spend (₹)</th>
                            <th>Leads</th>
                            <th>Conversions</th>
                            <th>CPA (₹)</th>
                            <th class="pe-4">ROI (%)</th>
                        </tr>
                    </thead>
                    <tbody id="campaignTableBody">
                        @forelse($campaigns as $campaign)
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">{{ $campaign->name }}</td>
                            <td>₹{{ number_format($campaign->boost_amount ?? 0) }}</td>
                            <td>{{ number_format($campaign->total_enquiries ?? 0) }}</td>
                            <td>{{ number_format($campaign->total_orders ?? 0) }}</td>
                            <td>₹{{ number_format($campaign->cpa ?? 0, 2) }}</td>
                            <td class="pe-4">
                                <span class="badge {{ ($campaign->roi ?? 0) >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-2 py-1">
                                    {{ number_format($campaign->roi ?? 0, 1) }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No Campaign ROI Data Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Charts Row (3 charts) -->
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3">CPA per Campaign</h6>
                    <canvas id="cpaTrendChart" height="180"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Spend vs Leads</h6>
                    <canvas id="spendLeadsChart" height="180"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3">ROI per Campaign</h6>
                    <canvas id="roiTrendChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal (for future real data) -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET" action="#" id="filterForm">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Campaigns</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" id="startDate" value="2024-05-01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" id="endDate" value="2024-05-31">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Type</label>
                        <select name="type" class="form-select" id="productType">
                            <option value="">All Types</option>
                            <option value="digital">Digital</option>
                            <option value="physical">Physical</option>
                            <option value="service">Service</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Convert dynamic PHP data to JavaScript
    const campaignNames = {!! json_encode($campaigns->pluck('name')) !!};
    const cpaData = {!! json_encode($campaigns->pluck('cpa')) !!};
    const spendData = {!! json_encode($campaigns->pluck('boost_amount')) !!};
    const leadsData = {!! json_encode($campaigns->pluck('total_enquiries')) !!};
    const roiData = {!! json_encode($campaigns->pluck('roi')) !!};

    // Render CPA Chart (Bar Chart)
    new Chart(document.getElementById('cpaTrendChart'), {
        type: 'bar',
        data: { labels: campaignNames, datasets: [{ label: 'CPA (₹)', data: cpaData, backgroundColor: 'rgba(220, 53, 69, 0.7)', borderColor: '#dc3545', borderWidth: 1 }] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
    });

    // Render Spend vs Leads Chart (Dual Axis: Bar & Line)
    new Chart(document.getElementById('spendLeadsChart'), {
        type: 'bar',
        data: {
            labels: campaignNames,
            datasets: [
                { label: 'Spend (₹)', data: spendData, backgroundColor: 'rgba(13, 110, 253, 0.7)', borderColor: '#0d6efd', borderWidth: 1, yAxisID: 'y' },
                { label: 'Leads', data: leadsData, type: 'line', borderColor: '#6c757d', backgroundColor: '#6c757d', tension: 0.3, fill: false, yAxisID: 'y1' }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { title: { display: true, text: 'Spend (₹)' } }, y1: { position: 'right', title: { display: true, text: 'Leads' }, grid: { drawOnChartArea: false } } }
        }
    });

    // Render ROI Chart (Bar Chart)
    new Chart(document.getElementById('roiTrendChart'), {
        type: 'bar',
        data: { labels: campaignNames, datasets: [{ label: 'ROI (%)', data: roiData, backgroundColor: 'rgba(25, 135, 84, 0.7)', borderColor: '#198754', borderWidth: 1 }] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } }, scales: { y: { ticks: { callback: v => v + '%' } } } }
    });

    // Optional: Update metrics and table when filter is applied (simulate for now)
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const start = document.getElementById('startDate').value;
        const end = document.getElementById('endDate').value;
        const type = document.getElementById('productType').value;
        // Format date range display
        const formatDate = (dateStr) => {
            const d = new Date(dateStr);
            return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/ /g, ' ');
        };
        document.getElementById('dateRangeDisplay').innerText = `${formatDate(start)} - ${formatDate(end)}`;
        // Here you would normally fetch new data via AJAX and update charts & table
        // For demo, just show a toast or alert
        alert(`Filter applied: ${start} to ${end}, Type: ${type || 'All'}\n(Real data integration pending)`);
        bootstrap.Modal.getInstance(document.getElementById('filterModal')).hide();
    });
</script>
@endsection
