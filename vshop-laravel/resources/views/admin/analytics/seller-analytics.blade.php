@extends('layouts.vertical', ['title' => 'Seller Analytics'])

@section('content')
<style>
    .card-analytics {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease;
    }
    .card-analytics:hover {
        transform: translateY(-5px);
    }
    .kpi-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .bg-light-blue { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .bg-light-green { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
    .bg-light-orange { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .nav-tabs-custom .nav-link {
        border: none;
        color: #4b5563;
        font-weight: 500;
        padding: 10px 16px;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }
    .nav-tabs-custom .nav-link.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
        background: transparent;
    }
    .nav-tabs-custom .nav-link:hover:not(.active) {
        border-bottom-color: #e5e7eb;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-3">
        <h4 class="fw-semibold mb-0">Seller Analytics & Reports</h4>
        <form method="GET" class="d-flex gap-2">
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate->toDateString() }}">
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate->toDateString() }}">
            <button type="submit" class="btn btn-primary btn-sm">Filter Date</button>
        </form>
    </div>

    <!-- FILTER BAR -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted fs-11 mb-1 uppercase"><i class="fas fa-search me-1"></i> Search Seller</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="sellerSearchInput" class="form-control" placeholder="Search by shop name, email...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted fs-11 mb-1 uppercase"><i class="fas fa-filter me-1"></i> Seller Type</label>
                    <select id="sellerTypeFilter" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="direct">Direct Seller</option>
                        <option value="dropshipping">Dropshipping</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted fs-11 mb-1 uppercase"><i class="fas fa-sort me-1"></i> Sort By</label>
                    <select id="sellerSortFilter" class="form-select form-select-sm">
                        <option value="name_asc">Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" id="resetSellerFilters" class="btn btn-outline-secondary btn-sm w-100">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-analytics shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="kpi-icon bg-light-blue">
                        <i class="fas fa-store"></i>
                    </div>
                    <div>
                        <div class="text-muted fs-12 uppercase fw-semibold">Total Sellers</div>
                        <div class="fw-bold fs-4">{{ number_format($totalSellers) }}</div>
                        <small class="text-muted">Registered in platform</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-analytics shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="kpi-icon bg-light-green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted fs-12 uppercase fw-semibold">Active Sellers</div>
                        <div class="fw-bold fs-4">{{ number_format($activeSellersCount) }}</div>
                        <small class="text-success">{{ $totalSellers > 0 ? round(($activeSellersCount/$totalSellers)*100, 1) : 0 }}% active rate</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-analytics shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="kpi-icon bg-light-orange">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <div class="text-muted fs-12 uppercase fw-semibold">Avg. Revenue / Seller</div>
                        <div class="fw-bold fs-4">₹{{ number_format($averageSellerRevenue, 2) }}</div>
                        <small class="text-muted">Based on top 15 sellers</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Top Revenue Chart -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="fw-semibold mb-0">Top Sellers Revenue Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="sellerRevenueChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <!-- Return Rates -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="fw-semibold mb-0">Sellers Return Rate Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="sellerReturnRateChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 9 Points Tabs Panel -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-transparent border-0 pt-3">
            <h5 class="fw-semibold mb-3">Seller Insights Dashboard </h5>
            <ul class="nav nav-tabs nav-tabs-custom border-bottom" id="myTab" role="tablist">
                <li class="nav-item"><button class="nav-link active" id="high-value-tab" data-bs-toggle="tab" data-bs-target="#high-value" type="button" role="tab">1. High Value</button></li>
                <li class="nav-item"><button class="nav-link" id="returns-tab" data-bs-toggle="tab" data-bs-target="#returns" type="button" role="tab">2. Frequent Returns</button></li>
                <li class="nav-item"><button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">3. Bad Reviews</button></li>
                <li class="nav-item"><button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">4. Top Orders</button></li>
                <li class="nav-item"><button class="nav-link" id="slow-moving-tab" data-bs-toggle="tab" data-bs-target="#slow-moving" type="button" role="tab">5. Slow Moving</button></li>
                <li class="nav-item"><button class="nav-link" id="boosters-tab" data-bs-toggle="tab" data-bs-target="#boosters" type="button" role="tab">6. Active Boosters</button></li>
                <li class="nav-item"><button class="nav-link" id="new-reg-tab" data-bs-toggle="tab" data-bs-target="#new-reg" type="button" role="tab">7. New Register</button></li>
                <li class="nav-item"><button class="nav-link" id="banned-tab" data-bs-toggle="tab" data-bs-target="#banned" type="button" role="tab">8. Blocked/Inactive</button></li>
                <li class="nav-item"><button class="nav-link" id="conversion-tab" data-bs-toggle="tab" data-bs-target="#conversion" type="button" role="tab">9. Conversion Rate</button></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <!-- Tab 1: High Value -->
                <div class="tab-pane fade show active" id="high-value" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle">
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
                                <tr><td colspan="4" class="text-center text-muted">No high value sellers found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Tab 2: Frequent Returns -->
                <div class="tab-pane fade" id="returns" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle">
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
                                <tr><td colspan="5" class="text-center text-muted">No data found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Tab 3: Bad Reviews -->
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle">
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
                                <tr><td colspan="4" class="text-center text-muted">No sellers with bad reviews (< 3★) found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Tab 4: Top Orders -->
                <div class="tab-pane fade" id="orders" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>ID</th><th>Shop Name</th><th>Total Orders</th></tr></thead>
                            <tbody>
                                @forelse($topSellersByOrders as $seller)
                                <tr data-name="{{ $seller->name }}" data-email="{{ $seller->email ?? '' }}" data-type="{{ ($seller->type === 'supplier') ? 'dropshipping' : 'direct' }}">
                                    <td>#{{ $seller->id }}</td>
                                    <td><strong>{{ $seller->name }}</strong></td>
                                    <td class="fw-bold">{{ $seller->total_orders }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">No data found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Tab 5: Slow Moving -->
                <div class="tab-pane fade" id="slow-moving" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>ID</th><th>Shop Name</th><th>Revenue</th></tr></thead>
                            <tbody>
                                @forelse($slowMovingSellers as $seller)
                                <tr data-name="{{ $seller->name }}" data-email="{{ $seller->email ?? '' }}" data-type="{{ ($seller->type === 'supplier') ? 'dropshipping' : 'direct' }}">
                                    <td>#{{ $seller->id }}</td>
                                    <td><strong>{{ $seller->name }}</strong></td>
                                    <td>₹{{ number_format($seller->total_revenue, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">No slow moving sellers found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Tab 6: Active Boosters -->
                <div class="tab-pane fade" id="boosters" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>ID</th><th>Shop Name</th><th>Boost Spend</th></tr></thead>
                            <tbody>
                                @forelse($activeBoosters as $seller)
                                <tr data-name="{{ $seller->name }}" data-email="{{ $seller->email ?? '' }}" data-type="{{ ($seller->type === 'supplier') ? 'dropshipping' : 'direct' }}">
                                    <td>#{{ $seller->id }}</td>
                                    <td><strong>{{ $seller->name }}</strong></td>
                                    <td class="text-primary fw-bold">₹{{ number_format($seller->total_boost_spend, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">No active boosters found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Tab 7: New Registrations -->
                <div class="tab-pane fade" id="new-reg" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle">
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
                                <tr><td colspan="4" class="text-center text-muted">No new sellers registered in last 30 days</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Tab 8: Banned/Inactive -->
                <div class="tab-pane fade" id="banned" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>ID</th><th>Shop Name</th><th>Status</th><th>Updated</th></tr></thead>
                            <tbody>
                                @forelse($bannedSellers as $seller)
                                <tr data-name="{{ $seller->name }}" data-email="{{ $seller->email ?? '' }}" data-type="{{ ($seller->type === 'supplier') ? 'dropshipping' : 'direct' }}">
                                    <td>#{{ $seller->id }}</td>
                                    <td><strong>{{ $seller->name }}</strong></td>
                                    <td><span class="badge bg-danger bg-opacity-10 text-danger">{{ ucfirst($seller->status) }}</span></td>
                                    <td>{{ $seller->updated_at->format('d M Y, h:i A') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">No blocked/inactive sellers found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Tab 9: Conversion Rate -->
                <div class="tab-pane fade" id="conversion" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle">
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
                                <tr><td colspan="5" class="text-center text-muted">No data found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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
    // 1. Seller Revenue Trend Chart
    const revCtx = document.getElementById('sellerRevenueChart');
    if (revCtx) {
        new Chart(revCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($highValueSellers->pluck('name')->toArray()) !!},
                datasets: [{
                    label: 'Total Revenue (INR)',
                    data: {!! json_encode($highValueSellers->pluck('total_revenue')->toArray()) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // 2. Seller Return Rate Chart
    const retCtx = document.getElementById('sellerReturnRateChart');
    if (retCtx) {
        new Chart(retCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($frequentReturns->pluck('name')->toArray()) !!},
                datasets: [{
                    label: 'Return Rate (%)',
                    data: {!! json_encode($frequentReturns->pluck('return_rate')->toArray()) !!},
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }

    // 3. Search, Filter and Sort Logic for Sellers
    function filterAndSortSellers() {
        const keyword = $('#sellerSearchInput').val().toLowerCase();
        const selectedType = $('#sellerTypeFilter').val();
        const selectedSort = $('#sellerSortFilter').val();

        // Loop through all tables in the tab panes
        $('.tab-pane table tbody').each(function() {
            const tbody = $(this);
            const rows = tbody.find('tr').not('.no-results-row');
            let visibleRows = [];

            rows.each(function() {
                const row = $(this);
                const name = row.data('name') ? row.data('name').toLowerCase() : '';
                const email = row.data('email') ? row.data('email').toLowerCase() : '';
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

            // Sort visible rows
            if (selectedSort && visibleRows.length > 0) {
                visibleRows.sort((a, b) => {
                    const nameA = a.data('name') || '';
                    const nameB = b.data('name') || '';
                    if (selectedSort === 'name_asc') return nameA.localeCompare(nameB);
                    if (selectedSort === 'name_desc') return nameB.localeCompare(nameA);
                    return 0;
                });
                
                // Re-append in sorted order
                visibleRows.forEach(row => tbody.append(row));
            }

            // Show no results inside this table if empty
            tbody.find('.no-results-row').remove();
            if (visibleRows.length === 0 && rows.length > 0) {
                tbody.append('<tr class="no-results-row"><td colspan="5" class="text-center py-4 text-muted">No matching sellers found</td></tr>');
            }
        });
    }

    // jQuery triggers for filter
    if (typeof $ !== 'undefined') {
        $('#sellerSearchInput').on('input', filterAndSortSellers);
        $('#sellerTypeFilter, #sellerSortFilter').on('change', filterAndSortSellers);
        $('#resetSellerFilters').click(function() {
            $('#sellerSearchInput').val('');
            $('#sellerTypeFilter').val('');
            $('#sellerSortFilter').val('name_asc');
            filterAndSortSellers();
        });
        
        // Initial sorting call
        filterAndSortSellers();
    }
});
</script>
@endsection
