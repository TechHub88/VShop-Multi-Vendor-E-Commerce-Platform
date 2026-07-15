@extends('layouts.vertical', ['title' => 'Customer Status & Blocking Control'])

@section('content')
<style>
/* Spacing and Margin Fixes */
.content {
    padding-top: 5px !important;
}
.container-fluid {
    padding-top: 0 !important;
    padding-left: 15px;
    padding-right: 15px;
}
@media(max-width:575px){
    .container-fluid {
        padding-left: 5px;
        padding-right: 5px;
    }
}

/* Premium Contrast Stats Cards with forced gradients */
.card.card-stat-total {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%) !important;
}
.card.card-stat-active {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
}
.card.card-stat-inactive {
    background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%) !important;
}
.card.card-stat-blocked {
    background: linear-gradient(135deg, #cb2d3e 0%, #ef473a 100%) !important;
}

.card-stat-label {
    color: rgba(255, 255, 255, 0.9) !important;
    font-weight: 600 !important;
    text-transform: uppercase;
    text-shadow: 0 1px 2px rgba(0,0,0,0.15);
}
.card-stat-value {
    color: #ffffff !important;
    font-weight: 700 !important;
    text-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.card-stat-icon {
    background-color: rgba(255, 255, 255, 0.22) !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
}

.fs-12 { font-size: 12px; }
.fs-11 { font-size: 11px; }
.rounded-4 { border-radius: 12px !important; }
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
    .btn-light border {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
    .btn-outline-secondary {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-2 pb-2 mb-3 border-bottom">
        <h4 class="fw-bold text-dark mb-0">Block / Unblock</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                <li class="breadcrumb-item active" aria-current="page">Block Control</li>
            </ol>
        </nav>
    </div>

    <!-- Stats Cards Row -->
    <div class="row mb-4">
        <div class="col-md-3 mb-2 mb-md-0">
            <div class="card border-0 shadow-sm rounded-4 card-stat-total" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%) !important;">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-stat-label text-uppercase fs-12 mb-1">Total Users</h6>
                            <h2 class="mb-0 fw-bold card-stat-value">{{ $totalCustomers }}</h2>
                        </div>
                        <div class="card-stat-icon rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(255, 255, 255, 0.22) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.15) !important;">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <div class="card border-0 shadow-sm rounded-4 card-stat-active" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-stat-label text-uppercase fs-12 mb-1">Active Users</h6>
                            <h2 class="mb-0 fw-bold card-stat-value">{{ $activeCustomers }}</h2>
                        </div>
                        <div class="card-stat-icon rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(255, 255, 255, 0.22) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.15) !important;">
                            <i class="fas fa-user-check fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <div class="card border-0 shadow-sm rounded-4 card-stat-inactive" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%) !important;">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-stat-label text-uppercase fs-12 mb-1">Inactive Users</h6>
                            <h2 class="mb-0 fw-bold card-stat-value">{{ $inactiveCustomers }}</h2>
                        </div>
                        <div class="card-stat-icon rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(255, 255, 255, 0.22) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.15) !important;">
                            <i class="fas fa-user-clock fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <div class="card border-0 shadow-sm rounded-4 card-stat-blocked" style="background: linear-gradient(135deg, #cb2d3e 0%, #ef473a 100%) !important;">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-stat-label text-uppercase fs-12 mb-1">Blocked Users</h6>
                            <h2 class="mb-0 fw-bold card-stat-value">{{ $blockedCustomers }}</h2>
                        </div>
                        <div class="card-stat-icon rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(255, 255, 255, 0.22) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.15) !important;">
                            <i class="fas fa-user-slash fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Control Card -->
    <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.customers.block-control') }}" class="row g-2">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status Filter</option>
                        <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                        <option value="blocked" {{ $status == 'blocked' ? 'selected' : '' }}>Blocked Only</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> Apply Filters</button>
                </div>
                <div class="col-md-2 d-grid">
                    <a href="{{ route('admin.customers.block-control') }}" class="btn btn-outline-secondary"><i class="fas fa-sync me-1"></i> Clear Filters</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Customer Table Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light py-3">
                        <tr>
                            <th class="ps-4">Customer Details</th>
                            <th>Register Date</th>
                            <th>Status Badge</th>
                            <th>Current State</th>
                            <th class="pe-4 text-end">Quick Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light p-2 border me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                            <span class="fw-bold text-primary">{{ strtoupper(substr($customer->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold text-dark">{{ $customer->name }}</h6>
                                            <small class="text-muted"><i class="far fa-envelope me-1"></i> {{ $customer->email }}</small>
                                            @if($customer->phone)
                                                <br><small class="text-muted"><i class="fas fa-phone-alt me-1"></i> {{ $customer->phone }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted d-block"><i class="far fa-calendar-alt me-1"></i> {{ $customer->created_at->format('M d, Y') }}</small>
                                    <small class="text-muted fs-11"><i class="far fa-clock me-1"></i> {{ $customer->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <span class="status-badge-{{ $customer->id }} badge bg-{{ $customer->status == 'active' ? 'success' : ($customer->status == 'inactive' ? 'secondary' : 'warning') }}">
                                        {{ ucfirst($customer->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="block-badge-{{ $customer->id }} badge px-3 py-1.5 {{ $customer->is_blocked ? 'bg-danger text-white' : 'bg-success bg-opacity-10 text-success border border-success' }}">
                                        <i class="fas {{ $customer->is_blocked ? 'fa-user-slash me-1' : 'fa-check-circle me-1' }}"></i>
                                        {{ $customer->is_blocked ? 'Blocked' : 'Allowed' }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-inline-flex gap-2">
                                        <!-- Block/Unblock toggle button -->
                                        <button type="button" 
                                                class="btn btn-sm btn-action-toggle {{ $customer->is_blocked ? 'btn-success' : 'btn-danger' }}"
                                                data-id="{{ $customer->id }}" 
                                                data-blocked="{{ $customer->is_blocked }}">
                                            <i class="fas {{ $customer->is_blocked ? 'fa-user-check me-1' : 'fa-user-slash me-1' }}"></i>
                                            {{ $customer->is_blocked ? 'Unblock Customer' : 'Block Customer' }}
                                        </button>
                                        
                                        
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-user-shield fa-3x mb-3 text-muted"></i>
                                    <h5 class="text-dark">No customers found</h5>
                                    <p class="text-muted">Try updating your filters or search keywords.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light bg-opacity-50">
                <small class="text-muted">Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} results</small>
                <div>
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AJAX JavaScript Code -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    // 1. Handle Block/Unblock toggle
    $('.btn-action-toggle').click(function() {
        var button = $(this);
        var customerId = button.data('id');
        var isBlocked = button.data('blocked');
        var newBlockState = isBlocked ? 0 : 1;
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...');

        $.ajax({
            url: `/admin/customers/${customerId}/toggle-status`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                block: newBlockState
            },
            success: function(response) {
                if (response.success) {
                    // Update button details
                    button.data('blocked', response.is_blocked);
                    if (response.is_blocked) {
                        button.removeClass('btn-success').addClass('btn-danger')
                              .html('<i class="fas fa-user-check me-1"></i> Unblock Customer');
                        
                        // Update current state badge
                        $(`.block-badge-${customerId}`).removeClass('bg-success bg-opacity-10 text-success border border-success')
                                                        .addClass('bg-danger text-white')
                                                        .html('<i class="fas fa-user-slash me-1"></i> Blocked');
                    } else {
                        button.removeClass('btn-danger').addClass('btn-success')
                              .html('<i class="fas fa-user-slash me-1"></i> Block Customer');
                        
                        // Update current state badge
                        $(`.block-badge-${customerId}`).removeClass('bg-danger text-white')
                                                        .addClass('bg-success bg-opacity-10 text-success border border-success')
                                                        .html('<i class="fas fa-check-circle me-1"></i> Allowed');
                    }
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    // 2. Handle Status switcher
    $('.status-switcher').change(function() {
        var select = $(this);
        var customerId = select.data('id');
        var newStatus = select.val();
        
        select.prop('disabled', true);

        $.ajax({
            url: `/admin/customers/${customerId}/toggle-status`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    // Update status badge
                    var badge = $(`.status-badge-${customerId}`);
                    badge.removeClass('bg-success bg-secondary bg-warning bg-danger')
                         .text(response.status.charAt(0).toUpperCase() + response.status.slice(1));
                    
                    if (response.status === 'active') {
                        badge.addClass('bg-success');
                    } else if (response.status === 'inactive') {
                        badge.addClass('bg-secondary');
                    } else {
                        badge.addClass('bg-warning');
                    }
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('An error occurred while switching status.');
            },
            complete: function() {
                select.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection
