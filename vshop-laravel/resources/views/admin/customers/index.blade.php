@extends('layouts.vertical', ['title' => 'Registered Users'])

@section('content')

<style>
.salesReportSearchArea{padding:15px 15px 15px;}
.container-fluid {
    padding-left: 15px;
    padding-right: 15px;
}
@media(max-width:575px){
.container-fluid {
    padding-left: 5px;
    padding-right: 5px;
}
}

/* ── KPI Cards ── */
.kpi-card {
    border: none;
    border-radius: 14px;
    overflow: hidden;
    transition: transform 0.22s ease, box-shadow 0.22s ease;
    cursor: default;
    position: relative;
}
.kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0,0,0,.13) !important;
}
.kpi-icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.35rem;
}
.kpi-label {
    font-size: .78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #8a93a2;
    margin-bottom: 2px;
}
.kpi-value {
    font-size: 1.7rem;
    font-weight: 700;
    line-height: 1.1;
    color: #1a2035;
}
.kpi-sub {
    font-size: .75rem;
    color: #8a93a2;
    margin-top: 2px;
}
/* accent stripe at card top */
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
}
.kpi-card.kpi-total::before   { background: #3762ea; }
.kpi-card.kpi-active::before  { background: #2dce89; }
.kpi-card.kpi-inactive::before{ background: #f5365c; }
.kpi-card.kpi-blocked::before { background: #f5365c; }
.kpi-card.kpi-new::before     { background: #9b59b6; }
.kpi-card.kpi-pending::before { background: #ffc107; }

.kpi-icon-wrap.bg-blue    { background: rgba(55,98,234,.12);  color: #3762ea; }
.kpi-icon-wrap.bg-green   { background: rgba(45,206,137,.12); color: #2dce89; }
.kpi-icon-wrap.bg-red     { background: rgba(245,54,92,.12);  color: #f5365c; }
.kpi-icon-wrap.bg-purple  { background: rgba(155,89,182,.12); color: #9b59b6; }
.kpi-icon-wrap.bg-warning  { background: rgba(255,193,7,.12); color: #ffc107; }

.table th {
    background: #004392 !important;
    color: #fff !important;
}
.btn-primary,
.btn-warning,
.btn-danger,
.btn-info {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
}
.btn-success {
    background: #2dce89 !important;
    border: 0 !important;
}
.btn-secondary {
    background: #6c757d !important;
    color: #fff;
    border: 0 !important;
}

/* Professional Filter Bar */
.filter-bar {
    background: #fff;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
}
.filter-grid {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 1rem;
}
.filter-group {
    flex: 1;
    min-width: 160px;
}
.filter-group label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    margin-bottom: 0.35rem;
}
.filter-group .input-group {
    width: 100%;
}
.filter-group .form-control,
.filter-group .form-select {
    height: 42px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    font-size: 0.875rem;
    transition: all 0.2s;
}
.filter-group .form-control:focus,
.filter-group .form-select:focus {
    border-color: #3762ea;
    box-shadow: 0 0 0 3px rgba(55,98,234,0.1);
}
.filter-group .input-group-text {
    background: #f8f9fa;
    border: 1px solid #e2e8f0;
    border-right: none;
    color: #6c757d;
}
.filter-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}
.filter-actions .btn {
    height: 42px;
    padding: 0 1.25rem;
    font-weight: 500;
    border-radius: 8px;
}
.btn-search {
    background: #3762ea;
    color: white;
}
.btn-search:hover {
    background: #2c52c9;
    color: white;
}
.btn-export {
    background: #28a745;
    color: white;
    border: none;
}
.btn-export:hover {
    background: #218838;
    color: white;
}
.btn-fields {
    background: #6f42c1;
    color: white;
    border: none;
}
.btn-fields:hover {
    background: #5e35b1;
    color: white;
}
.divider-vertical {
    width: 1px;
    height: 30px;
    background: #dee2e6;
    margin: 0 0.5rem;
}

/* Status Badges */
.badge-status {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: capitalize;
    display: inline-block;
}
.badge-active, .badge-approved {
    background: #d4edda;
    color: #155724;
}
.badge-inactive {
    background: #f8d7da;
    color: #721c24;
}
.badge-pending {
    background: #fff3cd;
    color: #856404;
}
.badge-blocked {
    background: #f8d7da;
    color: #721c24;
}

/* Toggle Switch Styles */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}
.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #dc3545;
    transition: 0.3s;
    border-radius: 24px;
}
.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
}
input:checked + .toggle-slider {
    background-color: #2dce89;
}
input:checked + .toggle-slider:before {
    transform: translateX(26px);
}
input:disabled + .toggle-slider {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Action Buttons */
.action-btns {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
    align-items: center;
}
.action-btns .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    border-radius: 6px;
}

/* Dropdown Styles */
.dropdown-container { 
    position: relative; 
    display: inline-block; 
}
#fieldDropdown {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    width: 280px;
    background: white;
    border: 1px solid #e9ecef;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    border-radius: 12px;
    z-index: 1050;
    margin-top: 8px;
    overflow: hidden;
}
#fieldDropdown.show {
    display: block !important;
}
.dropdown-title {
    font-weight: 600;
    padding: 12px 16px;
    border-bottom: 1px solid #e9ecef;
    font-size: 0.8rem;
    background: #f8f9fa;
    color: #1a2035;
}
.dropdown-list {
    padding: 8px 0;
    max-height: 300px;
    overflow-y: auto;
}
.dropdown-list .dropdown-item {
    display: flex;
    align-items: center;
    padding: 8px 16px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background 0.2s;
}
.dropdown-list .dropdown-item:hover {
    background: #f8f9fa;
}
.dropdown-list .dropdown-item input {
    margin-right: 10px;
}
.dropdown-footer {
    padding: 12px 16px;
    border-top: 1px solid #e9ecef;
    background: #f8f9fa;
    display: flex;
    gap: 12px;
}

/* Responsive */
@media (max-width: 992px) {
    .filter-grid {
        flex-direction: column;
    }
    .filter-group {
        width: 100%;
    }
    .divider-vertical {
        display: none;
    }
    .filter-actions {
        flex-wrap: wrap;
    }
}

/* Table Styles */
.table th {
    background: #004392 !important;
    color: #fff !important;
    font-weight: 600;
    font-size: 0.8rem;
    padding: 12px 8px;
}
.table td {
    padding: 12px 8px;
    vertical-align: middle;
    font-size: 0.85rem;
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}
.loading-spinner {
    background: white;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
}
.btn-fields {
    background: #004392;
}
.btn-fields:hover {
    background: #004392;
}
.btn-export {
    background: #61b3e5 !important;
}
.btn-export:hover {
    background: #61b3e5 !important;
}
.reset-btn {
    background: #61b3e5 !important;
    color: #fff;
    border: 0 !important;
    padding: 7px 7px;
    border-radius: 5px;
}
.reset-btn:hover {
    color: #fff;
}
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-3">
        <h4 class="fw-semibold mb-0">All Buyers</h4>
    </div>

    <!-- KPI CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-4 col-xl-2">
            <div class="card kpi-card kpi-total shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="kpi-icon-wrap bg-blue">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Total</div>
                        <div class="kpi-value" id="totalCustomers">{{ number_format($totalCustomers) }}</div>
                        <div class="kpi-sub">All buyers</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-xl-2">
            <div class="card kpi-card kpi-active shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="kpi-icon-wrap bg-green">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Active</div>
                        <div class="kpi-value" id="activeCustomers">{{ number_format($activeCustomers) }}</div>
                        <div class="kpi-sub">Active users</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-xl-2">
            <div class="card kpi-card kpi-inactive shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="kpi-icon-wrap bg-red">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Inactive</div>
                        <div class="kpi-value" id="inactiveCustomers">{{ number_format($inactiveCustomers) }}</div>
                        <div class="kpi-sub">Inactive users</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-xl-2">
            <div class="card kpi-card kpi-blocked shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="kpi-icon-wrap bg-red">
                        <i class="fa-solid fa-x"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Blocked</div>
                        <div class="kpi-value" id="blockedCustomers">{{ number_format($blockedCustomers) }}</div>
                        <div class="kpi-sub">Blocked users</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-xl-2">
            <div class="card kpi-card kpi-new shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="kpi-icon-wrap bg-purple">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div>
                        <div class="kpi-label">New</div>
                        <div class="kpi-value">{{ number_format($newThisMonth) }}</div>
                        <div class="kpi-sub">This month</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-xl-2">
            <div class="card kpi-card kpi-pending shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="kpi-icon-wrap bg-warning">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <div class="kpi-label">Pending</div>
                        <div class="kpi-value" id="pendingCustomers">{{ number_format($pendingCustomers) }}</div>
                        <div class="kpi-sub">Awaiting approval</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PROFESSIONAL FILTER BAR -->
    <div class="filter-bar mb-4">
        <div class="filter-grid">
            <div class="filter-group">
                <label><i class="fas fa-search me-1"></i> Search</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="keywordSearch" class="form-control" placeholder="Name, email, ID...">
                </div>
            </div>
            
            <div class="filter-group">
                <label><i class="fas fa-tag me-1"></i> Buyer Type</label>
                <select id="customerTypeFilter" class="form-select">
                    <option value="">All Types</option>
                    <option value="direct_selling">Direct Buyers</option>
                    <option value="dropshipping">Vshop-Buyers</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label><i class="fas fa-filter me-1"></i> Status</label>
                <select id="statusFilter" class="form-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                    <option value="blocked">Blocked</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label><i class="fas fa-sort me-1"></i> Sort By</label>
                <select id="sortFilter" class="form-select">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="name_asc">Name (A-Z)</option>
                    <option value="name_desc">Name (Z-A)</option>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="button" id="applySearch" class="btn btn-search">
                    <i class="fas fa-search me-1"></i> Search
                </button>
                
                <button type="button" id="applySearch" class="btn reset-btn">
                     Reset
                </button>
                
                <div class="divider-vertical"></div>
                
                <div class="dropdown-container">
                    <button type="button" id="fieldDropdownBtn" class="btn btn-fields">
                        <i class="fas fa-columns me-1"></i> Fields
                        <span id="selectedCount" class="badge bg-light text-dark ms-1">7</span>
                    </button>
                    <div id="fieldDropdown" class="dropdown-menu">
                        <div class="dropdown-title">Select Fields to Export</div>
                        <div class="dropdown-list">
                            <label class="dropdown-item"><input type="checkbox" class="field-checkbox" value="sl" data-label="SL" checked> SL</label>
                            <label class="dropdown-item"><input type="checkbox" class="field-checkbox" value="customer_id_name" data-label="Buyer ID/Name" checked> Buyer ID/Name</label>
                            <label class="dropdown-item"><input type="checkbox" class="field-checkbox" value="seller_id_name" data-label="Seller ID/Name" checked> Seller ID/Name</label>
                            <label class="dropdown-item"><input type="checkbox" class="field-checkbox" value="email" data-label="Email" checked> Email</label>
                            <label class="dropdown-item"><input type="checkbox" class="field-checkbox" value="customer_type" data-label="Buyer Type" checked> Buyer Type</label>
                            <label class="dropdown-item"><input type="checkbox" class="field-checkbox" value="status" data-label="Status" checked> Status</label>
                            <label class="dropdown-item"><input type="checkbox" class="field-checkbox" value="created_at" data-label="Created At" checked> Created At</label>
                        </div>
                        <div class="dropdown-footer">
                            <button type="button" id="selectAllFields" class="btn btn-sm btn-link p-0">Select All</button>
                            <button type="button" id="deselectAllFields" class="btn btn-sm btn-link p-0">Deselect All</button>
                        </div>
                    </div>
                </div>
                
                <button type="button" id="exportCsvBtn" class="btn btn-export">
                    <i class="fas fa-file-csv me-1"></i> Export
                </button>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="usersTable" class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="selectAllCheckbox"></th>
                            <th>SL</th>
                            <th>Buyer ID/Name</th>
                            <th>Seller ID/Name</th>
                            <th>Email</th>
                            <th>Buyer Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th width="120" class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        @forelse($customers as $customer)
                        <tr data-customer-id="{{ $customer->id }}" data-customer-status="{{ $customer->status }}" data-customer-type="{{ empty($customer->seller_id) ? 'direct_selling' : 'dropshipping' }}">
                            <td><input type="checkbox" class="row-checkbox" value="{{ $customer->id }}"></td>
                            <td><span class="sl-number">{{ $loop->iteration }}</span></td>
                            <td data-customer-id="{{ $customer->id }}" data-customer-name="{{ $customer->name }}">
                                <strong>#{{ $customer->id }}</strong><br>
                                <small class="text-muted">{{ $customer->name }}</small>
                            </td>
                            <td data-seller-id="{{ optional($customer->seller)->id ?? '' }}" data-seller-name="{{ optional($customer->seller)->name ?? '' }}">
                                @if($customer->seller)
                                    <strong>#{{ $customer->seller->id }}</strong><br>
                                    <small class="text-muted">{{ $customer->seller->name }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $customer->email }}</td>
                            <td>
                                @if(empty($customer->seller_id))
                                    <span class="badge-status badge-active">Direct Buyers</span>
                                @else
                                    <span class="badge-status badge-approved">Vshop-Buyers</span>
                                @endif
                            </td>
                            <td class="status-cell">
                                <span class="badge-status 
                                    @if(in_array($customer->status, ['active', 'approved'])) badge-active
                                    @elseif($customer->status == 'inactive') badge-inactive
                                    @elseif($customer->status == 'pending') badge-pending
                                    @elseif($customer->status == 'blocked') badge-blocked
                                    @endif">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>
                            <td>{{ $customer->created_at->format('d M, Y') }}</td>
                            <td class="text-end">
                                <div class="action-btns">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-sm btn-info me-1" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <label class="toggle-switch me-1">
                                        <input type="checkbox" class="status-toggle" data-customer-id="{{ $customer->id }}" 
                                               {{ $customer->status == 'active' ? 'checked' : '' }}
                                               {{ $customer->status == 'blocked' ? 'disabled' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <form method="POST" action="{{ route('admin.customers.destroy', $customer->id) }}" onsubmit="return confirm('Delete this user?')" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No customers found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 mb-0">Updating...</p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    // Field selection dropdown
    $('#fieldDropdownBtn').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#fieldDropdown').toggleClass('show');
    });
    
    $(document).click(function(e) {
        if (!$(e.target).closest('.dropdown-container').length) {
            $('#fieldDropdown').removeClass('show');
        }
    });
    
    $('#fieldDropdown').click(function(e) { e.stopPropagation(); });
    
    function updateSelectedCount() {
        var count = $('.field-checkbox:checked').length;
        $('#selectedCount').text(count);
    }
    
    $('.field-checkbox').on('change', function() { updateSelectedCount(); });
    $('#selectAllFields').click(function() { $('.field-checkbox').prop('checked', true); updateSelectedCount(); });
    $('#deselectAllFields').click(function() { $('.field-checkbox').prop('checked', false); updateSelectedCount(); });
    
    // Select all checkbox
    $('#selectAllCheckbox').change(function() {
        $('.row-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    $('.row-checkbox').change(function() {
        if($('.row-checkbox:checked').length === $('.row-checkbox').length) {
            $('#selectAllCheckbox').prop('checked', true);
        } else {
            $('#selectAllCheckbox').prop('checked', false);
        }
    });
    
    // Dynamic Status Toggle with AJAX - Updates data and KPI
    $('.status-toggle').change(function() {
        var $toggle = $(this);
        var customerId = $toggle.data('customer-id');
        var newStatus = $toggle.is(':checked') ? 'active' : 'inactive';
        var $row = $toggle.closest('tr');
        var $statusSpan = $row.find('.status-cell .badge-status');
        var oldStatus = $row.data('customer-status');
        
        // Show loading
        $('#loadingOverlay').show();
        
        // Get old status for rollback
        var oldStatusText = $statusSpan.text().toLowerCase();
        
        // Optimistic UI update
        updateStatusBadge($statusSpan, newStatus);
        $row.data('customer-status', newStatus);
        
        // Send AJAX request
        $.ajax({
            url: '{{ route("admin.customers.update-status") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                customer_id: customerId,
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    // Update KPI counters dynamically
                    updateKPICounters(oldStatus, newStatus);
                    console.log('Status updated successfully for customer:', customerId);
                } else {
                    // Revert on failure
                    $toggle.prop('checked', !$toggle.is(':checked'));
                    updateStatusBadge($statusSpan, oldStatus);
                    $row.data('customer-status', oldStatus);
                    alert('Failed to update status: ' + (response.message || 'Unknown error'));
                }
                $('#loadingOverlay').hide();
            },
            error: function(xhr) {
                // Revert on error
                $toggle.prop('checked', !$toggle.is(':checked'));
                updateStatusBadge($statusSpan, oldStatus);
                $row.data('customer-status', oldStatus);
                $('#loadingOverlay').hide();
                
                let errorMsg = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            }
        });
    });
    
    // Function to update KPI counters dynamically
    function updateKPICounters(oldStatus, newStatus) {
        // Decrement old status count
        if (oldStatus === 'active') {
            let activeCount = parseInt($('#activeCustomers').text().replace(/,/g, '')) - 1;
            $('#activeCustomers').text(activeCount.toLocaleString());
        } else if (oldStatus === 'inactive') {
            let inactiveCount = parseInt($('#inactiveCustomers').text().replace(/,/g, '')) - 1;
            $('#inactiveCustomers').text(inactiveCount.toLocaleString());
        } else if (oldStatus === 'pending') {
            let pendingCount = parseInt($('#pendingCustomers').text().replace(/,/g, '')) - 1;
            $('#pendingCustomers').text(pendingCount.toLocaleString());
        } else if (oldStatus === 'blocked') {
            let blockedCount = parseInt($('#blockedCustomers').text().replace(/,/g, '')) - 1;
            $('#blockedCustomers').text(blockedCount.toLocaleString());
        }
        
        // Increment new status count
        if (newStatus === 'active') {
            let activeCount = parseInt($('#activeCustomers').text().replace(/,/g, '')) + 1;
            $('#activeCustomers').text(activeCount.toLocaleString());
        } else if (newStatus === 'inactive') {
            let inactiveCount = parseInt($('#inactiveCustomers').text().replace(/,/g, '')) + 1;
            $('#inactiveCustomers').text(inactiveCount.toLocaleString());
        } else if (newStatus === 'pending') {
            let pendingCount = parseInt($('#pendingCustomers').text().replace(/,/g, '')) + 1;
            $('#pendingCustomers').text(pendingCount.toLocaleString());
        } else if (newStatus === 'blocked') {
            let blockedCount = parseInt($('#blockedCustomers').text().replace(/,/g, '')) + 1;
            $('#blockedCustomers').text(blockedCount.toLocaleString());
        }
        
        // Total remains the same
    }
    
    function updateStatusBadge($badge, status) {
        $badge.removeClass('badge-active badge-inactive badge-pending badge-blocked').text(status.charAt(0).toUpperCase() + status.slice(1));
        if (status === 'active' || status === 'approved') {
            $badge.addClass('badge-active');
        } else if (status === 'inactive') {
            $badge.addClass('badge-inactive');
        } else if (status === 'pending') {
            $badge.addClass('badge-pending');
        } else if (status === 'blocked') {
            $badge.addClass('badge-blocked');
        }
    }
    
    // Search functionality with filters
    $('#applySearch').click(function() { performSearch(); });
    $('#keywordSearch').on('keypress', function(e) { if (e.which === 13) { e.preventDefault(); performSearch(); } });
    
    function performSearch() {
        var keyword = $('#keywordSearch').val().toLowerCase();
        var selectedCustomerType = $('#customerTypeFilter').val();
        var selectedStatus = $('#statusFilter').val();
        var selectedSort = $('#sortFilter').val();
        var rows = $('#usersTableBody tr');
        var visibleRows = [];
        var slCounter = 1;
        
        rows.each(function() {
            var $row = $(this);
            if ($row.find('td').length === 1) return;
            
            var customerText = $row.find('td:eq(1)').text().toLowerCase();
            var sellerText = $row.find('td:eq(2)').text().toLowerCase();
            var email = $row.find('td:eq(3)').text().toLowerCase();
            var customerType = $row.data('customer-type') || 'direct_selling';
            var status = $row.data('customer-status') || '';
            
            var show = true;
            if (keyword && !customerText.includes(keyword) && !sellerText.includes(keyword) && !email.includes(keyword)) show = false;
            if (selectedCustomerType && customerType !== selectedCustomerType) show = false;
            if (selectedStatus && status !== selectedStatus) show = false;
            
            if (show) {
                $row.show();
                $row.find('.sl-number').text(slCounter++);
                visibleRows.push({ 
                    row: $row, 
                    name: $row.find('td:eq(1) strong').text(), 
                    created_at: $row.find('td:eq(6)').text(),
                    status: status
                });
            } else {
                $row.hide();
            }
        });
        
        if (selectedSort && visibleRows.length > 0) {
            visibleRows.sort(function(a, b) {
                if (selectedSort === 'name_asc') return a.name.localeCompare(b.name);
                if (selectedSort === 'name_desc') return b.name.localeCompare(a.name);
                if (selectedSort === 'newest') return new Date(b.created_at) - new Date(a.created_at);
                if (selectedSort === 'oldest') return new Date(a.created_at) - new Date(b.created_at);
                return 0;
            });
            var tbody = $('#usersTableBody');
            tbody.empty();
            visibleRows.forEach(function(item) { tbody.append(item.row); });
            // Renumber SL after sorting
            $('#usersTableBody tr:visible').each(function(index) {
                $(this).find('.sl-number').text(index + 1);
            });
        }
        
        // Show no results message
        if (visibleRows.length === 0 && rows.length > 0) {
            if ($('#usersTableBody tr.no-results').length === 0) {
                $('#usersTableBody').append('<tr class="no-results"><td colspan="9" class="text-center py-5"><i class="fas fa-users fa-3x text-muted mb-3"></i><p class="text-muted mb-0">No customers found matching your criteria</p></td></tr>');
            }
        } else {
            $('.no-results').remove();
        }
    }
    
    // Export CSV
    $('#exportCsvBtn').click(function() {
        var selectedFields = [];
        $('.field-checkbox:checked').each(function() { 
            selectedFields.push({ value: $(this).val(), label: $(this).data('label') }); 
        });
        if (selectedFields.length === 0) { alert('Select at least one field'); return; }
        
        var data = [];
        $('#usersTableBody tr:visible').each(function(index) {
            var row = $(this);
            if (row.find('td').length === 1) return;
            data.push({
                sl: index + 1,
                customer: row.find('td:eq(1)').text().trim(),
                seller: row.find('td:eq(2)').text().trim(),
                email: row.find('td:eq(3)').text().trim(),
                customer_type: row.data('customer-type') === 'direct_selling' ? 'Direct Selling' : 'Dropshipping',
                status: row.data('customer-status') || '',
                created_at: row.find('td:eq(6)').text().trim()
            });
        });
        
        if (data.length === 0) { alert('No data to export'); return; }
        
        var csv = '\ufeff' + selectedFields.map(f => f.label).join(',') + '\n';
        data.forEach(function(row) {
            var values = selectedFields.map(f => {
                var val = '';
                switch(f.value) {
                    case 'sl': val = row.sl; break;
                    case 'customer_id_name': val = row.customer; break;
                    case 'seller_id_name': val = row.seller; break;
                    case 'email': val = row.email; break;
                    case 'customer_type': val = row.customer_type; break;
                    case 'status': val = row.status.charAt(0).toUpperCase() + row.status.slice(1); break;
                    case 'created_at': val = row.created_at; break;
                    default: val = '';
                }
                if (typeof val === 'string' && (val.includes(',') || val.includes('"'))) {
                    val = '"' + val.replace(/"/g, '""') + '"';
                }
                return val;
            });
            csv += values.join(',') + '\n';
        });
        
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.href = url;
        link.download = 'customers_' + new Date().toISOString().split('T')[0] + '.csv';
        link.click();
        URL.revokeObjectURL(url);
    });
    
    updateSelectedCount();
});
</script>
@endsection
