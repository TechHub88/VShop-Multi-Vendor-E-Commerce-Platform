@extends('layouts.vertical', ['title' => 'All Orders'])

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="#" class="text-muted">Apps</a></li>
                    <li class="breadcrumb-item active text-primary" aria-current="page">Order Management</li>
                </ol>
            </nav>
            <h3 class="mb-0 fw-bold">Order Management</h3>
            <p class="text-muted mb-0">Monitor, track & control all orders</p>
        </div>
        <div class="col-md-6 text-end">
           
        </div>
    </div>

    </div>

    <!-- KPI Cards Grid - Premium Design -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold tracking-wider" style="font-size: 0.7rem; letter-spacing: 0.8px;">Direct Orders</span>
                            <h3 class="mb-0 fw-bold text-dark mt-1">{{ number_format($directOrdersCount ?? 0) }}</h3>
                            <div class="mt-2">
                                <span class="badge bg-light text-muted fs-11">+12% from last month</span>
                            </div>
                        </div>
                        <div class="kpi-icon-wrapper blue-theme">
                            <i class="bx bx-shopping-bag fs-4"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-primary" style="width: 70%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold tracking-wider" style="font-size: 0.7rem; letter-spacing: 0.8px;">Completed</span>
                            <h3 class="mb-0 fw-bold text-dark mt-1">{{ number_format($completedOrdersCount ?? 0) }}</h3>
                            <div class="mt-2">
                                <span class="badge bg-success bg-opacity-10 text-success fs-11">+5% this week</span>
                            </div>
                        </div>
                        <div class="kpi-icon-wrapper green-theme">
                            <i class="bx bx-check-circle fs-4"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: 85%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold tracking-wider" style="font-size: 0.7rem; letter-spacing: 0.8px;">Cancelled</span>
                            <h3 class="mb-0 fw-bold text-dark mt-1">{{ number_format($cancelledOrdersCount ?? 0) }}</h3>
                            <div class="mt-2">
                                <span class="badge bg-danger bg-opacity-10 text-danger fs-11">-2% from average</span>
                            </div>
                        </div>
                        <div class="kpi-icon-wrapper red-theme">
                            <i class="bx bx-x-circle fs-4"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-danger" style="width: 28%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold tracking-wider" style="font-size: 0.7rem; letter-spacing: 0.8px;">Pending</span>
                            <h3 class="mb-0 fw-bold text-dark mt-1">{{ number_format($pendingOrdersCount ?? 0) }}</h3>
                            <div class="mt-2">
                                <span class="badge bg-warning bg-opacity-10 text-warning fs-11">Needs attention</span>
                            </div>
                        </div>
                        <div class="kpi-icon-wrapper yellow-theme">
                            <i class="bx bx-time-five fs-4"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 45%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card - Manual Filtering Only -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bx bx-filter-alt me-1"></i> Filter Orders
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Seller</label>
                    <select id="filterSeller" class="form-select">
                        <option value="">All Sellers</option>
                        @foreach($sellers as $seller)
                            <option value="{{ $seller->id }}" {{ request('seller') == $seller->id ? 'selected' : '' }}>{{ $seller->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Product</label>
                    <select id="filterProduct" class="form-select">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Location (City)</label>
                    <select id="filterLocation" class="form-select">
                        <option value="">All Locations</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->name }}" {{ request('location') == $city->name ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Payment Type</label>
                    <select id="filterPaymentType" class="form-select">
                        <option value="">All Payment Types</option>
                        <option value="credit_card" {{ request('payment_type') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                        <option value="debit_card" {{ request('payment_type') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                        <option value="paypal" {{ request('payment_type') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                        <option value="bank_transfer" {{ request('payment_type') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cash_on_delivery" {{ request('payment_type') == 'cash_on_delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                        <option value="upi" {{ request('payment_type') == 'upi' ? 'selected' : '' }}>UPI</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Payment Status</label>
                    <select id="filterPaymentStatus" class="form-select">
                        <option value="">All</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Order Status</label>
                    <select id="filterOrderStatus" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('order_status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="approved" {{ request('order_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="out for delivery" {{ request('order_status') == 'out for delivery' ? 'selected' : '' }}>Out For Delivery</option>
                        <option value="completed" {{ request('order_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="refund" {{ request('order_status') == 'refund' ? 'selected' : '' }}>Refund</option>
                        <option value="declined" {{ request('order_status') == 'declined' ? 'selected' : '' }}>Declined</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Date Range</label>
                    <select id="filterDate" class="form-select">
                        <option value="">All Time</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="last_week" {{ request('date_range') == 'last_week' ? 'selected' : '' }}>Last Week</option>
                        <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ request('date_range') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                        <option value="this_year" {{ request('date_range') == 'this_year' ? 'selected' : '' }}>This Year</option>
                        <option value="last_year" {{ request('date_range') == 'last_year' ? 'selected' : '' }}>Last Year</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Search</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search orders..." value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary" onclick="applyFilters()">
                        <i class="bx bx-search-alt me-1"></i> Apply Filters
                    </button>
                    <button class="btn btn-secondary" onclick="resetFilters()">
                        <i class="bx bx-refresh me-1"></i> Reset Filters
                    </button>
                    <button class="btn btn-success ms-auto" onclick="exportToCSV()">
                        <i class="bx bx-file me-1"></i> Export CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ORDER NO.</th>
                            <th>TOTAL AMOUNT</th>
                            <th style="width: 120px;">SELLER SHARE (₹)</th>
                            <th style="width: 120px;">REFERRER SHARE (₹)</th>
                            <th style="width: 120px;">PLATFORM SHARE (₹)</th>
                            <th>REFERRER</th>
                            <th>PAYMENT TYPE</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr data-order-id="{{ $order->id }}">
                            <td>
                                {{ $order->order_no ?? 'ORD' . str_pad($order->id, 8, '0', STR_PAD_LEFT) }}
                                <br>
                                <small class="text-muted">{{ $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : 'N/A' }}</small>
                                <br>
                                <small class="text-muted">{{ $order->location ?? $order->city ?? 'N/A' }}</small>
                            </td>
                            <td class="fw-bold">₹{{ number_format($order->total_amount ?? 0, 2) }}</td>
                            <td class="text-primary fw-bold">
                                ₹{{ number_format($order->seller_share, 2) }}
                                <br><small class="text-muted fw-normal" style="font-size:0.7rem;">({{ $order->commission_split->seller_pct ?? 0 }}%)</small>
                                <br><small class="text-dark fw-semibold" style="font-size:0.75rem;">Seller: {{ $order->seller_name ?? 'N/A' }}</small>
                            </td>
                            <td class="text-info fw-bold">
                                ₹{{ number_format($order->referrer_share, 2) }}
                                <br><small class="text-muted fw-normal" style="font-size:0.7rem;">({{ $order->commission_split->referrer_pct ?? 0 }}%)</small>
                            </td>
                            <td class="text-warning fw-bold">
                                ₹{{ number_format($order->platform_share, 2) }}
                                <br><small class="text-muted fw-normal" style="font-size:0.7rem;">({{ $order->commission_split->platform_pct ?? 0 }}%)</small>
                            </td>
                            <td>{{ $order->referrer_name ?? 'N/A' }}</td>
                            <td>
                                @if(($order->payment_status ?? '') == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-warning text-dark">Unpaid</span>
                                @endif
                            </td>
                            <td>
                                <form class="statusForm" data-id="{{ $order->id }}" action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                    @csrf
                                    <select name="prev_status" class="form-select order-status">
                                        <option value="Pending" {{ ($order->status ?? '') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Processing" {{ ($order->status ?? '') == 'Processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="Approved" {{ ($order->status ?? '') == 'Approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="Out For Delivery" {{ ($order->status ?? '') == 'Out For Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                        <option value="Completed" {{ ($order->status ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="Refund" {{ ($order->status ?? '') == 'Refund' ? 'selected' : '' }}>Refund</option>
                                        <option value="Declined" {{ ($order->status ?? '') == 'Declined' ? 'selected' : '' }}>Declined</option>
                                        <option value="Cancelled" {{ ($order->status ?? '') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('admin.orders.edit', $order->id) }}">Manage Order</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.orders.invoice', $order->id) }}">Invoice</a></li>
                                       
                                       
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteOrder({{ $order->id }})">Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="bx bx-shopping-bag bx-lg text-muted"></i>
                                <p class="text-muted mt-2">No orders found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Simple Bootstrap Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() ?? 0 }} orders
                </div>
                <div>
                    {{ $orders->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ORDER STATUS UPDATE MODAL -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="statusModalForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="prev_status" id="prev_status">
                    <div class="mb-3">
                        <label class="form-label">Update Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Select Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Processing">Processing</option>
                            <option value="Approved">Approved</option>
                            <option value="Out For Delivery">Out for Delivery</option>
                            <option value="Completed">Completed</option>
                            <option value="Refund">Refund</option>
                            <option value="Declined">Declined</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="datetime-local" name="start_date" id="start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">End Date</label>
                                <input type="datetime-local" name="end_date" id="end_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Privacy</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="privacy" id="public" value="public" checked>
                            <label class="form-check-label" for="public">Public</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="privacy" id="private" value="private">
                            <label class="form-check-label" for="private">Private</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ORDER DETAILS MODAL -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- Dynamic content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ✅ JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function getLocalDateTime() {
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    return now.toISOString().slice(0, 16);
}

// Apply filters - Reload page with filter parameters
function applyFilters() {
    const params = new URLSearchParams();
    
    const seller = $('#filterSeller').val();
    const product = $('#filterProduct').val();
    const location = $('#filterLocation').val();
    const paymentType = $('#filterPaymentType').val();
    const paymentStatus = $('#filterPaymentStatus').val();
    const orderStatus = $('#filterOrderStatus').val();
    const dateRange = $('#filterDate').val();
    const search = $('#searchInput').val();
    
    if (seller) params.append('seller', seller);
    if (product) params.append('product', product);
    if (location) params.append('location', location);
    if (paymentType) params.append('payment_type', paymentType);
    if (paymentStatus) params.append('payment_status', paymentStatus);
    if (orderStatus) params.append('order_status', orderStatus);
    if (dateRange) params.append('date_range', dateRange);
    if (search) params.append('search', search);
    
    window.location.href = window.location.pathname + '?' + params.toString();
}

function resetFilters() {
    window.location.href = window.location.pathname;
}

// Export to CSV
function exportToCSV() {
    const params = new URLSearchParams(window.location.search);
    params.append('export', 'csv');
    window.location.href = window.location.pathname + '?' + params.toString();
}

// View order details
function viewOrderDetails(orderId) {
    $.ajax({
        url: '/admin/orders/' + orderId,
        method: 'GET',
        success: function(data) {
            $('#orderDetailsContent').html(data);
            $('#orderDetailsModal').modal('show');
        },
        error: function() {
            Swal.fire('Error', 'Failed to load order details', 'error');
        }
    });
}

// Delete order
function deleteOrder(orderId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/admin/orders/' + orderId,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    Swal.fire('Deleted!', 'Order has been deleted.', 'success');
                    location.reload();
                },
                error: function() {
                    Swal.fire('Error', 'Failed to delete order', 'error');
                }
            });
        }
    });
}

$(function () {
    // Fix dropdowns position
    $('body').on('show.bs.dropdown', '.dropdown', function () {
        const $dropdown = $(this).find('.dropdown-menu');
        $('body').append($dropdown.detach());
        const eOffset = $(this).offset();
        $dropdown.css({
            'display': 'block',
            'top': eOffset.top + $(this).outerHeight(),
            'left': eOffset.left
        });
    }).on('hide.bs.dropdown', '.dropdown', function () {
        $(this).append($('.dropdown-menu', this).detach());
    });
    
    // Open modal on status change
    $(document).on('change', '.order-status', function() {
        const status = $(this).val();
        const form = $(this).closest('form');
        const action = form.attr('action');
        
        $('#prev_status').val(status);
        $('#status').val(status);
        $('#statusModalForm').attr('action', action);
        
        const formattedStatus = status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        $('#statusModalLabel').text('Update Order Status: ' + formattedStatus);
        
        const now = getLocalDateTime();
        $('#start_date').val('');
        $('#end_date').val('');
        
        if (['Processing', 'Approved', 'Shipped', 'Out For Delivery'].includes(status)) {
            $('#start_date').val(now);
        }
        
        if (['Completed', 'Cancelled', 'Declined', 'Refund'].includes(status)) {
            $('#end_date').val(now);
        }
        
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('statusModal'));
        modal.show();
    });
    
    // Submit modal via AJAX
    $('#statusModalForm').on('submit', function(e) {
        e.preventDefault();
        
        if ($('#status').val() === "" || $('#status').val() === null) {
            $('#status').val($('#prev_status').val());
        }
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function() {
                bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Order status updated successfully!',
                    confirmButtonText: 'OK'
                }).then(() => location.reload());
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Something went wrong.';
                Swal.fire({ icon: 'error', title: 'Failed to update', text: msg });
            }
        });
    });
});
</script>

<style>
.table-responsive {
    overflow-x: auto;
}
.table th, .table td {
    white-space: nowrap;
}
.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}
.form-select:focus {
    box-shadow: none;
}

/* Premium KPI Card Styling */
.kpi-card {
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    border: 1px solid rgba(0, 0, 0, 0.04);
    overflow: hidden;
}
.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
}
.kpi-icon-wrapper {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.kpi-icon-wrapper i {
    font-size: 28px;
}
/* Theme Colors */
.blue-theme {
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    color: #2563eb;
}
.green-theme {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    color: #059669;
}
.red-theme {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    color: #dc2626;
}
.yellow-theme {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    color: #d97706;
}
.fs-11 {
    font-size: 0.7rem;
}

/* Table Header */
.table thead th {
    background: #004392 !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: none !important;
    padding: 14px 12px;
    white-space: nowrap;
}
.table {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 12px;
    overflow: hidden;
}
.table td {
    padding: 12px;
    vertical-align: middle;
    border-color: #eef2f6;
}
.table tr:hover td {
    background-color: #f8fafc;
}

.btn-primary {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
}
.btn-light border {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
    color: #fff !important;
}
.btn-secondary {
    background: #61b3e5 !important;
    color: #fff;
    border: 0 !important;
}
.btn-success {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
}

/* Pagination styling */
.pagination {
    margin-bottom: 0;
}
.pagination .page-link {
    color: #004392;
    border-radius: 8px;
    margin: 0 2px;
}
.pagination .page-item.active .page-link {
    background: #004392;
    border-color: #004392;
    color: white;
}
.pagination .page-link:hover {
    background: #e0e7ff;
    color: #004392;
}
</style>
@endsection