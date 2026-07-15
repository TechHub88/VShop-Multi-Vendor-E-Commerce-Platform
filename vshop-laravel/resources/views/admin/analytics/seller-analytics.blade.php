@extends('layouts.vertical', ['title' => 'Seller Analytics'])

@section('content')
<style>
    .sa-page { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }

    /* Header */
    .sa-header {
        background: linear-gradient(135deg, #0b4da1 0%, #1e3a8a 100%);
        border-radius: 12px;
        padding: 20px 28px;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .sa-header h4 { margin: 0; font-weight: 700; font-size: 20px; }
    .sa-header .sa-date-group { display: flex; gap: 8px; align-items: center; }
    .sa-header .sa-date-group input[type="date"] {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        color: #fff;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 13px;
    }
    .sa-header .sa-date-group input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); }
    .sa-header .btn-filter {
        background: #fff;
        color: #0b4da1;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        padding: 7px 18px;
        font-size: 13px;
        transition: all 0.2s;
    }
    .sa-header .btn-filter:hover { background: #e0e7ff; }

    /* Filter Bar — single row */
    .sa-filter-bar {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 14px 20px;
        display: flex;
        gap: 14px;
        align-items: center;
        margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .sa-filter-bar .filter-item { flex: 1; min-width: 0; }
    .sa-filter-bar .filter-item label {
        font-size: 10px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
        display: block;
    }
    .sa-filter-bar input,
    .sa-filter-bar select {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 7px 12px;
        font-size: 13px;
        background: #f9fafb;
        transition: border-color 0.2s;
    }
    .sa-filter-bar input:focus,
    .sa-filter-bar select:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
    .sa-filter-bar .btn-reset {
        background: transparent;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 7px 18px;
        font-size: 13px;
        font-weight: 500;
        color: #6b7280;
        cursor: pointer;
        white-space: nowrap;
        margin-top: 18px;
        transition: all 0.2s;
    }
    .sa-filter-bar .btn-reset:hover { border-color: #3b82f6; color: #3b82f6; }

    /* KPI Cards */
    .sa-kpi-row { display: flex; gap: 16px; margin-bottom: 20px; }
    .sa-kpi-card {
        flex: 1;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .sa-kpi-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
    .sa-kpi-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
    }
    .sa-kpi-icon.blue { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .sa-kpi-icon.green { background: rgba(16,185,129,0.1); color: #10b981; }
    .sa-kpi-icon.orange { background: rgba(245,158,11,0.1); color: #f59e0b; }
    .sa-kpi-value { font-size: 22px; font-weight: 700; color: #111827; line-height: 1.2; }
    .sa-kpi-label { font-size: 11px; color: #6b7280; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }
    .sa-kpi-sub { font-size: 11px; color: #9ca3af; margin-top: 2px; }

    /* Charts Row */
    .sa-charts-row { display: flex; gap: 16px; margin-bottom: 20px; }
    .sa-chart-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 0;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .sa-chart-card.large { flex: 3; }
    .sa-chart-card.small { flex: 2; }
    .sa-chart-header {
        padding: 16px 20px 0;
        font-size: 15px;
        font-weight: 700;
        color: #111827;
    }
    .sa-chart-body { padding: 12px 20px 20px; }

    /* Tabs Panel */
    .sa-tabs-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        margin-bottom: 20px;
    }
    .sa-tabs-header {
        padding: 16px 20px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    .sa-tabs-header h5 { font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 12px; }
    .sa-tabs-nav {
        display: flex;
        gap: 0;
        list-style: none;
        margin: 0;
        padding: 0;
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: none;
    }
    .sa-tabs-nav::-webkit-scrollbar { display: none; }
    .sa-tabs-nav li { flex-shrink: 0; }
    .sa-tabs-nav li button {
        background: none;
        border: none;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 500;
        color: #6b7280;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .sa-tabs-nav li button:hover { color: #3b82f6; background: rgba(59,130,246,0.04); }
    .sa-tabs-nav li button.active {
        color: #fff;
        background: #0b4da1;
        border-bottom: 3px solid #0b4da1;
        border-radius: 6px 6px 0 0;
        font-weight: 600;
    }
    .sa-tabs-body { padding: 20px; }

    /* Tables */
    .sa-table { width: 100%; border-collapse: collapse; }
    .sa-table thead th {
        background: #f8fafc;
        border-bottom: 2px solid #e5e7eb;
        padding: 10px 14px;
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: left;
    }
    .sa-table tbody td {
        padding: 10px 14px;
        font-size: 13px;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }
    .sa-table tbody tr:hover { background: #f9fafb; }
    .sa-table .text-success { color: #059669 !important; }
    .sa-table .text-danger { color: #dc2626 !important; }
    .sa-table .text-warning { color: #d97706 !important; }
    .sa-table .text-primary { color: #2563eb !important; }
    .sa-table .badge-danger {
        background: rgba(220,38,38,0.1);
        color: #dc2626;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .sa-table .empty-msg { text-align: center; color: #9ca3af; padding: 30px 14px; font-style: italic; }

    @media (max-width: 768px) {
        .sa-header { flex-direction: column; gap: 12px; text-align: center; }
        .sa-filter-bar { flex-wrap: wrap; }
        .sa-kpi-row { flex-direction: column; }
        .sa-charts-row { flex-direction: column; }
    }
</style>

<div class="sa-page">
    {{-- ===== HEADER ===== --}}
    <form method="GET" class="sa-header">
        <h4><i class="fas fa-chart-line me-2"></i> Seller Analytics & Reports</h4>
        <div class="sa-date-group">
            <input type="date" name="start_date" value="{{ $startDate->toDateString() }}">
            <input type="date" name="end_date" value="{{ $endDate->toDateString() }}">
            <button type="submit" class="btn-filter">Filter Date</button>
        </div>
    </form>

    {{-- ===== FILTER BAR (single row) ===== --}}
    <div class="sa-filter-bar">
        <div class="filter-item" style="flex:2;">
            <label><i class="fas fa-search me-1"></i> Search Seller</label>
            <input type="text" id="sellerSearchInput" placeholder="Search by shop name, email...">
        </div>
        <div class="filter-item">
            <label><i class="fas fa-filter me-1"></i> Seller Type</label>
            <select id="sellerTypeFilter">
                <option value="">All Types</option>
                <option value="direct">Direct Seller</option>
                <option value="dropshipping">Dropshipping</option>
            </select>
        </div>
        <div class="filter-item">
            <label><i class="fas fa-sort me-1"></i> Sort By</label>
            <select id="sellerSortFilter">
                <option value="name_asc">Name (A-Z)</option>
                <option value="name_desc">Name (Z-A)</option>
            </select>
        </div>
        <button type="button" id="resetSellerFilters" class="btn-reset">Reset</button>
    </div>

    {{-- ===== KPI CARDS ===== --}}
    <div class="sa-kpi-row">
        <div class="sa-kpi-card">
            <div class="sa-kpi-icon blue"><i class="fas fa-store"></i></div>
            <div>
                <div class="sa-kpi-label">Total Sellers</div>
                <div class="sa-kpi-value">{{ number_format($totalSellers) }}</div>
                <div class="sa-kpi-sub">Registered in platform</div>
            </div>
        </div>
        <div class="sa-kpi-card">
            <div class="sa-kpi-icon green"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="sa-kpi-label">Active Sellers</div>
                <div class="sa-kpi-value">{{ number_format($activeSellersCount) }}</div>
                <div class="sa-kpi-sub">{{ $totalSellers > 0 ? round(($activeSellersCount/$totalSellers)*100, 1) : 0 }}% active rate</div>
            </div>
        </div>
        <div class="sa-kpi-card">
            <div class="sa-kpi-icon orange"><i class="fas fa-wallet"></i></div>
            <div>
                <div class="sa-kpi-label">Avg. Revenue / Seller</div>
                <div class="sa-kpi-value">₹{{ number_format($averageSellerRevenue, 2) }}</div>
                <div class="sa-kpi-sub">Based on top 15 sellers</div>
            </div>
        </div>
    </div>

    {{-- ===== CHARTS ROW ===== --}}
    <div class="sa-charts-row">
        <div class="sa-chart-card large">
            <div class="sa-chart-header"><i class="fas fa-chart-bar me-2 text-primary"></i>Top Sellers Revenue Trend</div>
            <div class="sa-chart-body"><canvas id="sellerRevenueChart" style="max-height: 280px;"></canvas></div>
        </div>
        <div class="sa-chart-card small">
            <div class="sa-chart-header"><i class="fas fa-chart-line me-2 text-danger"></i>Return Rate Distribution</div>
            <div class="sa-chart-body"><canvas id="sellerReturnRateChart" style="max-height: 280px;"></canvas></div>
        </div>
    </div>

    {{-- ===== INSIGHTS TABS (all in one line) ===== --}}
    <div class="sa-tabs-panel">
        <div class="sa-tabs-header">
            <h5><i class="fas fa-lightbulb me-2" style="color:#f59e0b;"></i>Seller Insights Dashboard</h5>
            <ul class="sa-tabs-nav" id="saTabNav" role="tablist">
                <li><button class="active" data-target="tab-high-value" type="button">1. High Value</button></li>
                <li><button data-target="tab-returns" type="button">2. Frequent Returns</button></li>
                <li><button data-target="tab-reviews" type="button">3. Bad Reviews</button></li>
                <li><button data-target="tab-orders" type="button">4. Top Orders</button></li>
                <li><button data-target="tab-slow" type="button">5. Slow Moving</button></li>
                <li><button data-target="tab-boosters" type="button">6. Active Boosters</button></li>
                <li><button data-target="tab-newreg" type="button">7. New Register</button></li>
                <li><button data-target="tab-blocked" type="button">8. Blocked/Inactive</button></li>
                <li><button data-target="tab-conversion" type="button">9. Conversion Rate</button></li>
            </ul>
        </div>
        <div class="sa-tabs-body">
            {{-- Tab 1: High Value --}}
            <div class="sa-tab-pane" id="tab-high-value">
                <table class="sa-table">
                    <thead><tr><th>ID</th><th>Shop Name</th><th>Email</th><th>Total Revenue</th></tr></thead>
                    <tbody>
                        @forelse($highValueSellers as $seller)
                        <tr data-name="{{ $seller->name }}" data-email="{{ $seller->email }}" data-type="{{ ($seller->type === 'supplier') ? 'dropshipping' : 'direct' }}">
                            <td>#{{ $seller->id }}</td>
                            <td><strong>{{ $seller->name }}</strong></td>
                            <td>{{ $seller->email }}</td>
                            <td class="text-success fw-bold">₹{{ number_format($seller->total_revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="empty-msg">No high value sellers found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tab 2: Frequent Returns --}}
            <div class="sa-tab-pane" id="tab-returns" style="display:none;">
                <table class="sa-table">
                    <thead><tr><th>ID</th><th>Shop Name</th><th>Returned Items</th><th>Total Items Sold</th><th>Return Rate</th></tr></thead>
                    <tbody>
                        @forelse($frequentReturns as $seller)
                        <tr data-name="{{ $seller->name }}" data-email="{{ $seller->email ?? '' }}" data-type="{{ ($seller->type === 'supplier') ? 'dropshipping' : 'direct' }}">
                            <td>#{{ $seller->id }}</td>
                            <td><strong>{{ $seller->name }}</strong></td>
                            <td>{{ $seller->returns_count }}</td>
                            <td>{{ $seller->total_items_sold }}</td>
                            <td class="text-danger fw-bold">{{ $seller->return_rate }}%</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="empty-msg">No data found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tab 3: Bad Reviews --}}
            <div class="sa-tab-pane" id="tab-reviews" style="display:none;">
                <table class="sa-table">
                    <thead><tr><th>ID</th><th>Shop Name</th><th>Reviews Count</th><th>Average Rating</th></tr></thead>
                    <tbody>
                        @forelse($badReviewSellers as $seller)
                        <tr data-name="{{ $seller->name }}" data-email="{{ $seller->email ?? '' }}" data-type="{{ ($seller->type === 'supplier') ? 'dropshipping' : 'direct' }}">
                            <td>#{{ $seller->id }}</td>
                            <td><strong>{{ $seller->name }}</strong></td>
                            <td>{{ $seller->reviews_count }}</td>
                            <td class="text-warning fw-bold"><i class="fas fa-star me-1"></i>{{ round($seller->avg_rating, 2) }} / 5.00</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="empty-msg">No sellers with bad reviews (< 3★) found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tab 4: Top Orders --}}
            <div class="sa-tab-pane" id="tab-orders" style="display:none;">
                <table class="sa-table">
                    <thead><tr><th>ID</th><th>Shop Name</th><th>Total Orders</th></tr></thead>
                    <tbody>
                        @forelse($topSellersByOrders as $seller)
                        <tr data-name="{{ $seller->name }}" data-email="{{ $seller->email ?? '' }}" data-type="{{ ($seller->type === 'supplier') ? 'dropshipping' : 'direct' }}">
                            <td>#{{ $seller->id }}</td>
                            <td><strong>{{ $seller->name }}</strong></td>
                            <td class="fw-bold">{{ $seller->total_orders }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="empty-msg">No data found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tab 5: Slow Moving --}}
            <div class="sa-tab-pane" id="tab-slow" style="display:none;">
                <table class="sa-table">
                    <thead><tr><th>ID</th><th>Shop Name</th><th>Revenue</th></tr></thead>
                    <tbody>
                        @forelse($slowMovingSellers as $seller)
                        <tr data-name="{{ $seller->name }}" data-email="{{ $seller->email ?? '' }}" data-type="{{ ($seller->type === 'supplier') ? 'dropshipping' : 'direct' }}">
                            <td>#{{ $seller->id }}</td>
                            <td><strong>{{ $seller->name }}</strong></td>
                            <td>₹{{ number_format($seller->total_revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="empty-msg">No slow moving sellers found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tab 6: Active Boosters --}}
            <div class="sa-tab-pane" id="tab-boosters" style="display:none;">
                <table class="sa-table">
                    <thead><tr><th>ID</th><th>Shop Name</th><th>Boost Spend</th></tr></thead>
                    <tbody>
                        @forelse($activeBoosters as $seller)
                        <tr data-name="{{ $seller->name }}" data-email="{{ $seller->email ?? '' }}" data-type="{{ ($seller->type === 'supplier') ? 'dropshipping' : 'direct' }}">
                            <td>#{{ $seller->id }}</td>
                            <td><strong>{{ $seller->name }}</strong></td>
                            <td class="text-primary fw-bold">₹{{ number_format($seller->total_boost_spend, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="empty-msg">No active boosters found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tab 7: New Registrations --}}
            <div class="sa-tab-pane" id="tab-newreg" style="display:none;">
                <table class="sa-table">
                    <thead><tr><th>ID</th><th>Shop Name</th><th>Email</th><th>Registered At</th></tr></thead>
                    <tbody>
                        @forelse($newRegistrations as $seller)
                        <tr data-name="{{ $seller->name }}" data-email="{{ $seller->email }}" data-type="{{ ($seller->type === 'supplier') ? 'dropshipping' : 'direct' }}">
                            <td>#{{ $seller->id }}</td>
                            <td><strong>{{ $seller->name }}</strong></td>
                            <td>{{ $seller->email }}</td>
                            <td>{{ $seller->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="empty-msg">No new sellers registered in last 30 days</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tab 8: Blocked/Inactive --}}
            <div class="sa-tab-pane" id="tab-blocked" style="display:none;">
                <table class="sa-table">
                    <thead><tr><th>ID</th><th>Shop Name</th><th>Status</th><th>Updated</th></tr></thead>
                    <tbody>
                        @forelse($bannedSellers as $seller)
                        <tr data-name="{{ $seller->name }}" data-email="{{ $seller->email ?? '' }}" data-type="{{ ($seller->type === 'supplier') ? 'dropshipping' : 'direct' }}">
                            <td>#{{ $seller->id }}</td>
                            <td><strong>{{ $seller->name }}</strong></td>
                            <td><span class="badge-danger">{{ ucfirst($seller->status) }}</span></td>
                            <td>{{ $seller->updated_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="empty-msg">No blocked/inactive sellers found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tab 9: Conversion Rate --}}
            <div class="sa-tab-pane" id="tab-conversion" style="display:none;">
                <table class="sa-table">
                    <thead><tr><th>ID</th><th>Shop Name</th><th>Page Views</th><th>Orders</th><th>Conversion Rate</th></tr></thead>
                    <tbody>
                        @forelse($conversionRateSellers as $seller)
                        <tr data-name="{{ $seller->name }}" data-email="{{ $seller->email ?? '' }}" data-type="{{ ($seller->type === 'supplier') ? 'dropshipping' : 'direct' }}">
                            <td>#{{ $seller->id }}</td>
                            <td><strong>{{ $seller->name }}</strong></td>
                            <td>{{ $seller->page_views }}</td>
                            <td>{{ $seller->orders_count }}</td>
                            <td class="text-success fw-bold">{{ $seller->conversion_rate }}%</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="empty-msg">No data found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ===== Custom Tabs Logic =====
    const tabButtons = document.querySelectorAll('.sa-tabs-nav button');
    const tabPanes = document.querySelectorAll('.sa-tab-pane');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active from all buttons
            tabButtons.forEach(b => b.classList.remove('active'));
            // Hide all panes
            tabPanes.forEach(p => p.style.display = 'none');
            // Activate clicked
            this.classList.add('active');
            const target = this.getAttribute('data-target');
            document.getElementById(target).style.display = 'block';
        });
    });

    // ===== Revenue Chart (Professional) =====
    const revCtx = document.getElementById('sellerRevenueChart');
    if (revCtx) {
        const revLabels = {!! json_encode($highValueSellers->pluck('name')->toArray()) !!};
        const revData = {!! json_encode($highValueSellers->pluck('total_revenue')->toArray()) !!};

        // Generate gradient colors
        const gradientColors = revLabels.map((_, i) => {
            const opacity = 1 - (i * 0.06);
            return `rgba(11, 77, 161, ${Math.max(opacity, 0.3)})`;
        });

        new Chart(revCtx, {
            type: 'bar',
            data: {
                labels: revLabels,
                datasets: [{
                    label: 'Total Revenue (₹)',
                    data: revData,
                    backgroundColor: gradientColors,
                    borderColor: 'rgba(11, 77, 161, 1)',
                    borderWidth: 0,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e3a8a',
                        titleFont: { size: 12, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: ctx => '₹' + ctx.parsed.y.toLocaleString('en-IN')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                        ticks: {
                            font: { size: 11 },
                            color: '#9ca3af',
                            callback: val => '₹' + (val >= 1000 ? (val/1000).toFixed(0) + 'K' : val)
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10 },
                            color: '#6b7280',
                            maxRotation: 45,
                            minRotation: 30
                        }
                    }
                }
            }
        });
    }

    // ===== Return Rate Chart (Professional) =====
    const retCtx = document.getElementById('sellerReturnRateChart');
    if (retCtx) {
        const retLabels = {!! json_encode($frequentReturns->pluck('name')->toArray()) !!};
        const retData = {!! json_encode($frequentReturns->pluck('return_rate')->toArray()) !!};

        new Chart(retCtx, {
            type: 'line',
            data: {
                labels: retLabels,
                datasets: [{
                    label: 'Return Rate (%)',
                    data: retData,
                    borderColor: '#ef4444',
                    backgroundColor: (context) => {
                        const chart = context.chart;
                        const {ctx: c, chartArea} = chart;
                        if (!chartArea) return 'rgba(239,68,68,0.1)';
                        const gradient = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        gradient.addColorStop(0, 'rgba(239,68,68,0.2)');
                        gradient.addColorStop(1, 'rgba(239,68,68,0.01)');
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#dc2626',
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: { label: ctx => ctx.parsed.y + '%' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                        ticks: { font: { size: 11 }, color: '#9ca3af', callback: val => val + '%' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#6b7280', maxRotation: 45, minRotation: 30 }
                    }
                }
            }
        });
    }

    // ===== Search, Filter and Sort Logic =====
    function filterAndSortSellers() {
        const keyword = $('#sellerSearchInput').val().toLowerCase();
        const selectedType = $('#sellerTypeFilter').val();
        const selectedSort = $('#sellerSortFilter').val();

        $('.sa-tab-pane .sa-table tbody').each(function() {
            const tbody = $(this);
            const rows = tbody.find('tr').not('.no-results-row');
            let visibleRows = [];

            rows.each(function() {
                const row = $(this);
                const name = row.data('name') ? row.data('name').toString().toLowerCase() : '';
                const email = row.data('email') ? row.data('email').toString().toLowerCase() : '';
                const type = row.data('type') || 'direct';

                let show = true;
                if (keyword && !name.includes(keyword) && !email.includes(keyword)) show = false;
                if (selectedType && type !== selectedType) show = false;

                if (show) {
                    row.show();
                    visibleRows.push(row);
                } else {
                    row.hide();
                }
            });

            if (selectedSort && visibleRows.length > 0) {
                visibleRows.sort((a, b) => {
                    const nameA = a.data('name') || '';
                    const nameB = b.data('name') || '';
                    if (selectedSort === 'name_asc') return nameA.localeCompare(nameB);
                    if (selectedSort === 'name_desc') return nameB.localeCompare(nameA);
                    return 0;
                });
                visibleRows.forEach(row => tbody.append(row));
            }

            tbody.find('.no-results-row').remove();
            if (visibleRows.length === 0 && rows.length > 0) {
                const colCount = tbody.closest('table').find('thead th').length;
                tbody.append('<tr class="no-results-row"><td colspan="' + colCount + '" class="empty-msg">No matching sellers found</td></tr>');
            }
        });
    }

    if (typeof $ !== 'undefined') {
        $('#sellerSearchInput').on('input', filterAndSortSellers);
        $('#sellerTypeFilter, #sellerSortFilter').on('change', filterAndSortSellers);
        $('#resetSellerFilters').click(function() {
            $('#sellerSearchInput').val('');
            $('#sellerTypeFilter').val('');
            $('#sellerSortFilter').val('name_asc');
            filterAndSortSellers();
        });
        filterAndSortSellers();
    }
});
</script>
@endsection
