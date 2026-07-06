@extends('layouts.vertical', ['title' => 'Payment Monitoring'])

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1">Payment Monitoring</h4>
            <p class="text-muted mb-0">Track payment success/failure, validate invoices, and detect fraud</p>
        </div>
    </div>

    <!-- Statistics Cards with Proper Icons -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold tracking-wider" style="font-size: 0.7rem; letter-spacing: 0.8px;">Successful Payments</span>
                            <h3 class="mb-0 fw-bold text-dark mt-1">{{ number_format($successfulPayments ?? 0) }}</h3>
                            <div class="mt-2">
                                <span class="badge bg-success bg-opacity-10 text-success fs-11">+8% from last month</span>
                            </div>
                        </div>
                        <div class="kpi-icon-wrapper green-theme">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: {{ min(100, ($successfulPayments ?? 0) / max(1, ($successfulPayments ?? 0) + ($failedPayments ?? 0)) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold tracking-wider" style="font-size: 0.7rem; letter-spacing: 0.8px;">Failed Payments</span>
                            <h3 class="mb-0 fw-bold text-dark mt-1">{{ number_format($failedPayments ?? 0) }}</h3>
                            <div class="mt-2">
                                <span class="badge bg-danger bg-opacity-10 text-danger fs-11">-3% from average</span>
                            </div>
                        </div>
                        <div class="kpi-icon-wrapper red-theme">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-danger" style="width: {{ min(100, ($failedPayments ?? 0) / max(1, ($successfulPayments ?? 0) + ($failedPayments ?? 0)) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold tracking-wider" style="font-size: 0.7rem; letter-spacing: 0.8px;">Pending Verification</span>
                            <h3 class="mb-0 fw-bold text-dark mt-1">{{ number_format($pendingVerification ?? 0) }}</h3>
                            <div class="mt-2">
                                <span class="badge bg-warning bg-opacity-10 text-warning fs-11">Needs review</span>
                            </div>
                        </div>
                        <div class="kpi-icon-wrapper yellow-theme">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 45%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold tracking-wider" style="font-size: 0.7rem; letter-spacing: 0.8px;">Fraud Alerts</span>
                            <h3 class="mb-0 fw-bold text-dark mt-1">{{ number_format($fraudAlerts ?? 0) }}</h3>
                            <div class="mt-2">
                                <span class="badge bg-danger bg-opacity-10 text-danger fs-11">Critical!</span>
                            </div>
                        </div>
                        <div class="kpi-icon-wrapper purple-theme">
                            <i class="fa-solid fa-shield"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div class="progress-bar bg-danger" style="width: {{ min(100, ($fraudAlerts ?? 0) / max(1, ($successfulPayments ?? 0)) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card - Manual Filtering Only -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bx bx-filter-alt me-1"></i> Filter Payments
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Seller</label>
                    <select id="filterSeller" class="form-select">
                        <option value="">All Sellers</option>
                        @foreach($sellers ?? [] as $seller)
                            <option value="{{ $seller->id }}" {{ request('seller') == $seller->id ? 'selected' : '' }}>{{ $seller->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Product</label>
                    <select id="filterProduct" class="form-select">
                        <option value="">All Products</option>
                        @foreach($products ?? [] as $product)
                            <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Location (City)</label>
                    <select id="filterLocation" class="form-select">
                        <option value="">All Locations</option>
                        @foreach($cities ?? [] as $city)
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
                        <option value="">All Status</option>
                        <option value="success" {{ request('payment_status') == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Fraud Status</label>
                    <select id="filterFraudStatus" class="form-select">
                        <option value="">All</option>
                        <option value="clean" {{ request('fraud_status') == 'clean' ? 'selected' : '' }}>Clean</option>
                        <option value="suspicious" {{ request('fraud_status') == 'suspicious' ? 'selected' : '' }}>Suspicious</option>
                        <option value="flagged" {{ request('fraud_status') == 'flagged' ? 'selected' : '' }}>Flagged</option>
                        <option value="blocked" {{ request('fraud_status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Date Range</label>
                    <select id="filterDate" class="form-select">
                        <option value="">All Time</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Search</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by order ID, invoice..." value="{{ request('search') }}">
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

    <!-- Payment Monitoring Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bx bx-credit-card me-1"></i> Payment Transactions
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Invoice No.</th>
                            <th>Customer</th>
                            <th>Amount (₹)</th>
                            <th>Payment Type</th>
                            <th>Transaction ID</th>
                            <th>Payment Status</th>
                            <th>Fraud Score</th>
                            <th>Fraud Status</th>
                            <th>Payment Date</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->invoice_no ?? ('INV-' . str_pad($order->id, 8, '0', STR_PAD_LEFT)) }}</td>
                            <td>{{ $order->customer?->name ?? ($order->recipient_name ?? 'N/A') }}</td>
                            <td class="fw-bold">₹{{ number_format($order->total_amount ?? 0, 2) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $order->payment_type ?? 'N/A')) }}</td>
                            <td><code>{{ $order->transaction_id ?? 'N/A' }}</code></td>
                            <td>
                                @if(($order->payment_status ?? '') == 'success')
                                    <span class="badge bg-success">Success</span>
                                @elseif(($order->payment_status ?? '') == 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @elseif(($order->payment_status ?? '') == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($order->payment_status ?? 'Unknown') }}</span>
                                @endif
                            </td>
                            <td>
                                @php $fraudScore = $order->fraud_score ?? 0; @endphp
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $fraudScore < 30 ? 'success' : ($fraudScore < 70 ? 'warning' : 'danger') }}" style="width: {{ $fraudScore }}%"></div>
                                </div>
                                <small>{{ $fraudScore }}%</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ ($order->fraud_status ?? 'clean') == 'clean' ? 'success' : (($order->fraud_status ?? '') == 'suspicious' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($order->fraud_status ?? 'Clean') }}
                                </span>
                            </td>
                            <td>{{ $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewPaymentDetails({{ $order->id }})">View Details</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="validateInvoice({{ $order->id }})">Validate Invoice</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="markAsSuspicious({{ $order->id }})">Mark Suspicious</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="flagFraud({{ $order->id }})">Flag as Fraud</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="clearFraudFlag({{ $order->id }})">Clear Fraud Flag</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="refundPayment({{ $order->id }})">Refund Payment</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <i class="bx bx-credit-card bx-lg text-muted"></i>
                                <p class="text-muted mt-2">No payment transactions found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Simple Bootstrap Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() ?? 0 }} payments
                </div>
                <div>
                    {{ $orders->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="paymentDetailsContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $(document).on('click', '.dropdown-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var dropdownEl = this;
        var dropdown = bootstrap.Dropdown.getOrCreateInstance(dropdownEl);
        dropdown.toggle();
    });
});

// Apply filters - Reload page with filter parameters
function applyFilters() {
    const params = new URLSearchParams();
    
    const seller = $('#filterSeller').val();
    const product = $('#filterProduct').val();
    const location = $('#filterLocation').val();
    const paymentType = $('#filterPaymentType').val();
    const paymentStatus = $('#filterPaymentStatus').val();
    const fraudStatus = $('#filterFraudStatus').val();
    const dateRange = $('#filterDate').val();
    const search = $('#searchInput').val();
    
    if (seller) params.append('seller', seller);
    if (product) params.append('product', product);
    if (location) params.append('location', location);
    if (paymentType) params.append('payment_type', paymentType);
    if (paymentStatus) params.append('payment_status', paymentStatus);
    if (fraudStatus) params.append('fraud_status', fraudStatus);
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

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text);
    Swal.fire({
        icon: 'success',
        title: 'Copied!',
        text: 'Transaction ID copied to clipboard',
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

// View payment details
function viewPaymentDetails(orderId) {
    Swal.fire({
        title: 'Loading...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    $.ajax({
        url: '/admin/orders/payments/' + orderId,
        method: 'GET',
        success: function(response) {
            Swal.close();
            if (response.success) {
                $('#paymentDetailsContent').html(response.html);
                $('#paymentDetailsModal').modal('show');
            } else {
                Swal.fire('Error', response.message || 'Failed to load payment details', 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Failed to load payment details', 'error');
        }
    });
}

// Validate invoice
function validateInvoice(orderId) {
    Swal.fire({
        title: 'Validating...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    $.ajax({
        url: '/admin/orders/invoice/validate/' + orderId,
        method: 'GET',
        success: function(response) {
            Swal.fire({
                icon: response.valid ? 'success' : 'error',
                title: response.valid ? 'Valid Invoice' : 'Invalid Invoice',
                html: response.message
            });
        },
        error: function() {
            Swal.fire('Error', 'Failed to validate invoice', 'error');
        }
    });
}

// Mark as suspicious
function markAsSuspicious(orderId) {
    Swal.fire({
        title: 'Mark as Suspicious?',
        text: 'This payment will be marked for review',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, mark',
        confirmButtonColor: '#ffc107'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/admin/orders/payments/' + orderId + '/suspicious',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    Swal.fire('Updated!', 'Payment marked as suspicious', 'success');
                    location.reload();
                },
                error: function() {
                    Swal.fire('Error', 'Failed to update', 'error');
                }
            });
        }
    });
}

// Flag as fraud
function flagFraud(orderId) {
    Swal.fire({
        title: 'Flag as Fraud?',
        input: 'textarea',
        inputPlaceholder: 'Enter reason...',
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes, flag',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            $.ajax({
                url: '/admin/orders/payments/' + orderId + '/fraud',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', reason: result.value },
                success: function() {
                    Swal.fire('Flagged!', 'Payment flagged as fraudulent', 'success');
                    location.reload();
                },
                error: function() {
                    Swal.fire('Error', 'Failed to flag payment', 'error');
                }
            });
        }
    });
}

// Clear fraud flag
function clearFraudFlag(orderId) {
    Swal.fire({
        title: 'Clear Fraud Flag?',
        text: 'Remove fraud flag from this payment',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, clear',
        confirmButtonColor: '#28a745'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/admin/orders/payments/' + orderId + '/clear-fraud',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function() {
                    Swal.fire('Cleared!', 'Fraud flag removed', 'success');
                    location.reload();
                },
                error: function() {
                    Swal.fire('Error', 'Failed to clear flag', 'error');
                }
            });
        }
    });
}

// Refund payment
function refundPayment(orderId) {
    Swal.fire({
        title: 'Process Refund',
        html: `
            <input type="number" id="refundAmount" class="swal2-input" placeholder="Refund Amount">
            <textarea id="refundReason" class="swal2-textarea" placeholder="Reason for refund..."></textarea>
        `,
        showCancelButton: true,
        confirmButtonText: 'Process',
        confirmButtonColor: '#dc3545',
        preConfirm: () => {
            const amount = document.getElementById('refundAmount').value;
            const reason = document.getElementById('refundReason').value;
            if (!amount || !reason) {
                Swal.showValidationMessage('Please fill all fields');
            }
            return { amount, reason };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/admin/orders/payments/' + orderId + '/refund',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    amount: result.value.amount,
                    reason: result.value.reason
                },
                success: function(response) {
                    Swal.fire('Refunded!', response.message, 'success');
                    location.reload();
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.error || 'Failed to process refund', 'error');
                }
            });
        }
    });
}
</script>

<style>
.table-responsive {
    overflow-x: auto;
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
.purple-theme {
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    color: #9333ea;
}
.fs-11 {
    font-size: 0.7rem;
}

/* Table Header & Compact Rows */
.table thead th {
    background: #004392 !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: none !important;
    padding: 8px 10px;
}
.table {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
}
.table td {
    padding: 8px 10px;
    vertical-align: middle;
    border-color: #eef2f6;
    font-size: 0.82rem;
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
