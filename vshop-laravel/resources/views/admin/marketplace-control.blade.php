@extends('layouts.vertical', ['title' => 'Marketplace Control Center'])

@section('content')
<style>
    .btn-outline-secondary {
        border-color: #61b3e5;
        color: #61b3e5;
    }
    .btn-outline-secondary:hover {
        background: #61b3e5;
        border-color: #61b3e5;
        color: #fff;
    }
</style>
<div class="container-fluid pt-3">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div>
            <h1 class="h3 mb-0 text-slate-800 fw-bold">Marketplace Control Center</h1>
            <p class="text-muted mb-0">Overview and moderation of all marketplace sellers and their resources.</p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <button class="btn btn-light border d-flex align-items-center gap-1" onclick="refreshData()">
                <iconify-icon icon="solar:refresh-outline" width="18"></iconify-icon> Refresh
            </button>
        </div>
    </div>

    <!-- Stats Overview Section -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 4px solid #6366f1 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Total Sellers</span>
                            <h3 class="mb-0 mt-1 fw-bold text-slate-900">{{ $sellers->count() }}</h3>
                        </div>
                        <div class="p-3 bg-indigo-light rounded-circle text-indigo">
                            <iconify-icon icon="solar:users-group-two-round-bold" width="24"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 4px solid #f59e0b !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Pending Review</span>
                            <h3 class="mb-0 mt-1 fw-bold text-slate-900">
                                {{ $sellers->where('status', 'pending')->count() }}
                            </h3>
                        </div>
                        <div class="p-3 bg-warning-light rounded-circle text-warning">
                            <iconify-icon icon="solar:clock-circle-bold" width="24"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 4px solid #10b981 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Approved Sellers</span>
                            <h3 class="mb-0 mt-1 fw-bold text-slate-900">
                                {{ $sellers->where('status', 'approved')->count() }}
                            </h3>
                        </div>
                        <div class="p-3 bg-success-light rounded-circle text-success">
                            <iconify-icon icon="solar:check-circle-bold" width="24"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 4px solid #ef4444 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Blocked/Rejected</span>
                            <h3 class="mb-0 mt-1 fw-bold text-slate-900">
                                {{ $sellers->whereIn('status', ['blocked', 'rejected'])->count() }}
                            </h3>
                        </div>
                        <div class="p-3 bg-danger-light rounded-circle text-danger">
                            <iconify-icon icon="solar:shield-warning-bold" width="24"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Filter Bar with Dropdowns -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="text-muted fw-semibold"><iconify-icon icon="solar:filter-bold-duotone" class="align-middle me-1 text-primary"></iconify-icon> Filter Sellers:</span>
                
                <!-- Seller Dropdown Filter -->
                <select id="sellerFilter" class="form-select form-select-sm" style="width: auto; min-width: 180px;">
                    <option value="all">All Sellers</option>
                    @foreach($sellers as $seller)
                        <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                    @endforeach
                </select>

                <!-- Status Dropdown Filter -->
                <select id="statusFilter" class="form-select form-select-sm" style="width: auto; min-width: 160px;">
                    <option value="all">All Status</option>
                    <option value="pending">Pending Review</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="blocked">Blocked</option>
                </select>

                <!-- Business Type Dropdown Filter -->
                <select id="businessTypeFilter" class="form-select form-select-sm" style="width: auto; min-width: 160px;">
                    <option value="all">All Business Types</option>
                    <option value="dropshipping">Dropshipping</option>
                    <option value="direct">Direct</option>
                    <option value="service">Service</option>
                </select>

                <!-- Reset Filters Button -->
                <button class="btn btn-outline-secondary btn-sm" id="resetFilters">
                    <iconify-icon icon="solar:refresh-outline" width="16"></iconify-icon> Reset
                </button>
            </div>
            <div class="search-box">
                <input type="text" id="sellerSearchInput" class="form-control form-control-sm" placeholder="Search sellers...">
            </div>
        </div>
    </div>

    <!-- Sellers List Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="mb-0 fw-bold text-slate-800">All Registered Sellers</h5>
            <span class="badge bg-primary px-2 py-1" id="visibleSellersCount">{{ $sellers->count() }} Sellers</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="sellers-list-table">
                    <thead class="bg-light text-uppercase font-size-11 letter-spacing-05">
                        <tr>
                            <th class="px-4 py-3 text-muted">Seller ID</th>
                            <th class="py-3 text-muted">Seller Info</th>
                            <th class="py-3 text-muted">Contact Info</th>
                            <th class="py-3 text-muted text-center">Status</th>
                            <th class="py-3 text-muted text-center">Products</th>
                            <th class="py-3 text-muted text-center">SEO Config</th>
                            <th class="py-3 text-muted text-center">Operation Areas</th>
                            <th class="py-3 text-muted text-center">Banners</th>
                            <th class="py-3 text-muted">Joined Date</th>
                            <th class="px-4 py-3 text-end text-muted">Action</th>
                        </tr>
                    </thead>
                    <tbody id="sellers-table-body">
                        @forelse($sellers as $seller)
                        <tr id="seller-row-{{ $seller->id }}" 
                            data-status="{{ $seller->status ?? 'pending' }}"
                            data-business-type="{{ $seller->business_type ?? 'generic' }}"
                            data-seller-id="{{ $seller->id }}"
                            data-seller-name="{{ strtolower($seller->name) }}">
                            <td class="px-4 font-semibold text-slate-600">#{{ $seller->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm bg-primary-light text-primary rounded d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                        {{ strtoupper(substr($seller->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-slate-800">{{ $seller->name }}</h6>
                                        <small class="text-muted">{{ $seller->business_type ?? 'Generic Business' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <div><i class="bi bi-envelope text-muted me-1"></i>{{ $seller->email ?? 'N/A' }}</div>
                                    <div><i class="bi bi-telephone text-muted me-1"></i>{{ $seller->phone ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusClass = 'secondary';
                                    if ($seller->status === 'approved') $statusClass = 'success';
                                    elseif ($seller->status === 'pending') $statusClass = 'warning';
                                    elseif (in_array($seller->status, ['rejected', 'blocked'])) $statusClass = 'danger';
                                @endphp
                                <span class="badge bg-{{ $statusClass }}-light text-{{ $statusClass }} px-2 py-1 rounded text-uppercase font-size-10">
                                    {{ $seller->status ?? 'pending' }}
                                </span>
                            </td>
                            <td class="text-center text-slate-700 font-semibold">{{ $seller->products_count ?? 0 }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2 py-1 rounded">
                                    {{ $seller->seo_data_count ?? 0 }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2 py-1 rounded">
                                    {{ $seller->operation_areas_count ?? 0 }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2 py-1 rounded">
                                    {{ $seller->banners_count ?? 0 }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    {{ $seller->created_at ? $seller->created_at->format('Y-m-d') : 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 text-end">
                                <a href="{{ route('admin.marketplace.verify', $seller->id) }}" class="btn btn-sm btn-primary px-3 shadow-xs d-inline-flex align-items-center gap-1" id="btn-view-seller-{{ $seller->id }}">
                                    <iconify-icon icon="solar:eye-outline" width="16"></iconify-icon> View & Verify
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr class="no-sellers-row">
                            <td colspan="10" class="text-center py-5">
                                <iconify-icon icon="solar:document-text-outline" class="text-muted mb-2" width="48"></iconify-icon>
                                <p class="text-muted mb-0">No sellers registered in the marketplace yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Custom Premium CSS Styling -->
<style>
    .bg-indigo-light { background-color: rgba(99, 102, 241, 0.1); }
    .text-indigo { color: #6366f1; }
    .bg-warning-light { background-color: rgba(245, 158, 11, 0.1); }
    .bg-success-light { background-color: rgba(16, 185, 129, 0.1); }
    .bg-danger-light { background-color: rgba(239, 68, 68, 0.1); }
    .bg-primary-light { background-color: rgba(13, 110, 253, 0.1); }
    
    .bg-success-light.text-success { color: #10b981 !important; }
    .bg-warning-light.text-warning { color: #f59e0b !important; }
    .bg-danger-light.text-danger { color: #ef4444 !important; }
    
    .avatar-sm {
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    .letter-spacing-05 {
        letter-spacing: 0.5px;
    }
    .font-size-11 {
        font-size: 11px;
    }
    .font-size-10 {
        font-size: 10px;
    }
    .text-slate-800 {
        color: #1e293b;
    }
    .text-slate-900 {
        color: #0f172a;
    }
    .text-slate-600 {
        color: #475569;
    }
    .font-semibold {
        font-weight: 600;
    }
    
    /* Table hover micro-animation */
    .table tbody tr {
        transition: background-color 0.15s ease-in-out;
    }
    .table tbody tr:hover {
        background-color: rgba(248, 250, 252, 0.8) !important;
    }
    
    .search-box input {
        border-radius: 20px;
        padding-left: 15px;
        min-width: 200px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }
    .search-box input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Dropdown styling */
    .form-select-sm {
        font-size: 0.875rem;
        border-radius: 8px;
        border-color: #e2e8f0;
        background-color: #f8fafc;
    }
    .form-select-sm:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Filter function for all dropdowns
        function filterSellers() {
            const selectedSeller = $('#sellerFilter').val();
            const selectedStatus = $('#statusFilter').val();
            const selectedBusinessType = $('#businessTypeFilter').val();
            const searchQuery = $('#sellerSearchInput').val().toLowerCase().trim();
            let visibleCount = 0;

            $("#sellers-table-body tr").not('.no-sellers-row').each(function() {
                const row = $(this);
                const rowStatus = row.data('status');
                const rowBusinessType = (row.data('business-type') || 'generic').toLowerCase();
                const rowSellerId = row.data('seller-id').toString();
                const rowText = row.text().toLowerCase();

                // Apply filters
                let matchesSeller = (selectedSeller === 'all' || rowSellerId === selectedSeller);
                let matchesStatus = (selectedStatus === 'all' || rowStatus === selectedStatus);
                let matchesBusinessType = (selectedBusinessType === 'all' || rowBusinessType === selectedBusinessType);
                let matchesSearch = (searchQuery === '' || rowText.indexOf(searchQuery) > -1);

                if (matchesSeller && matchesStatus && matchesBusinessType && matchesSearch) {
                    row.show();
                    visibleCount++;
                } else {
                    row.hide();
                }
            });

            // Update visible count text
            $('#visibleSellersCount').text(visibleCount + ' Sellers');

            // Show empty row if count is 0
            if (visibleCount === 0) {
                if ($('.no-sellers-row').length === 0) {
                    $("#sellers-table-body").append('<tr class="no-sellers-row"><td colspan="10" class="text-center py-5 text-muted">No sellers matching the criteria.</td></tr>');
                } else {
                    $('.no-sellers-row').show();
                }
            } else {
                $('.no-sellers-row').hide();
            }
        }

        // Event listeners for dropdown changes
        $('#sellerFilter, #statusFilter, #businessTypeFilter').on('change', function() {
            filterSellers();
        });

        // Search input listener
        $("#sellerSearchInput").on("keyup", function() {
            filterSellers();
        });

        // Reset all filters
        $('#resetFilters').on('click', function() {
            $('#sellerFilter').val('all');
            $('#statusFilter').val('all');
            $('#businessTypeFilter').val('all');
            $('#sellerSearchInput').val('');
            filterSellers();
        });
    });

    function refreshData() {
        location.reload();
    }
</script>
@endsection