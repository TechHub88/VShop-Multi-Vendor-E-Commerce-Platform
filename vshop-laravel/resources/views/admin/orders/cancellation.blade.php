@extends('layouts.vertical', ['title' => 'Cancellation & Return'])

@section('content')

<style>
    /* Professional Action Button Styles */
    .btn-action {
        width: 34px;
        height: 34px;
        padding: 0;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
    }
    
    .btn-action i {
        font-size: 18px;
    }
    
    /* View Button - Blue */
    .btn-view {
        background: linear-gradient(135deg, #004392, #0066cc);
        color: white;
    }
    
    .btn-view:hover {
        background: linear-gradient(135deg, #00317a, #0052a3);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 67, 146, 0.3);
    }
    
    /* Approve Button - Green */
    .btn-approve {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .btn-approve:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    /* Reject Button - Red */
    .btn-reject {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    
    .btn-reject:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    
    /* Refund Button - Orange */
    .btn-refund {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }
    
    .btn-refund:hover {
        background: linear-gradient(135deg, #d97706, #b45309);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    
    .btn-action:active {
        transform: translateY(0);
    }
    
    /* Badge Styles */
    .badge-pending {
        background: #fef3c7;
        color: #d97706;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .badge-approved {
        background: #d1fae5;
        color: #059669;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .badge-rejected {
        background: #fee2e2;
        color: #dc2626;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .badge-cancellation {
        background: #fee2e2;
        color: #dc2626;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .badge-return {
        background: #fef3c7;
        color: #d97706;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    /* Toast Notification */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        font-size: 14px;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .toast-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    
    .toast-error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Loading Spinner */
    .spinner-sm {
        width: 14px;
        height: 14px;
        border: 2px solid white;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        display: inline-block;
        margin-right: 5px;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .reset-btn {
        width: 100%;
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
        padding: 7px 7px;
        border-radius: 5px;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-semibold">Cancellation & Return Management</h4>
            <p class="text-muted small mb-0">Manage customer cancellations, returns, and refunds</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-info btn-sm text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#monitorReturnModal" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); border: none;">
                <i class="ri-flow-chart"></i> Monitor return flow
            </button>
            <button class="btn btn-success btn-sm text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#verifyRefundModal" style="background: linear-gradient(135deg, #10b981, #059669); border: none;">
                <i class="ri-checkbox-circle-line"></i> Verify refund completion
            </button>
            <button class="btn btn-primary btn-sm shadow-sm" id="exportReport" style="background: linear-gradient(135deg, #004392, #0066cc); border: none;">
                <i class="ri-file-excel-line"></i> Export Report
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total Requests</span>
                            <h3 class="mb-0" id="totalOrders">0</h3>
                        </div>
                        <i class="ri-file-list-line fs-4" style="color: #004392;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Pending Requests</span>
                            <h3 class="mb-0" id="pendingOrders">0</h3>
                        </div>
                        <i class="ri-time-line fs-4" style="color: #f59e0b;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Approved Requests</span>
                            <h3 class="mb-0" id="approvedOrders">0</h3>
                        </div>
                        <i class="ri-checkbox-circle-line fs-4" style="color: #10b981;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Rejected Requests</span>
                            <h3 class="mb-0" id="rejectedOrders">0</h3>
                        </div>
                        <i class="ri-close-circle-line fs-4" style="color: #ef4444;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.orders.cancellation') }}" class="row g-2 align-items-center">
                <div class="col-auto flex-grow-1">
                    <select name="request_type" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Requests</option>
                        <option value="cancellation" {{ request('request_type') == 'cancellation' ? 'selected' : '' }}>Cancellation</option>
                        <option value="return" {{ request('request_type') == 'return' ? 'selected' : '' }}>Return</option>
                    </select>
                </div>
                <div class="col-auto flex-grow-1">
                    <select name="status" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-auto flex-grow-1">
                    <select name="seller" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Sellers</option>
                        @foreach($sellers ?? [] as $seller)
                            <option value="{{ $seller->id }}" {{ request('seller') == $seller->id ? 'selected' : '' }}>{{ $seller->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto flex-grow-1">
                    <select name="product" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Products</option>
                        @foreach($products ?? [] as $product)
                            <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-3" style="background: linear-gradient(135deg, #004392, #0066cc); border: none;padding: 6px 8px;">
                        <i class="ri-filter-3-line"></i> Filter
                    </button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.orders.cancellation') }}" class="btn btn-outline-secondary btn-sm px-3 reset-btn">
                        <i class="ri-refresh-line"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0 fw-semibold">Cancellation & Return Requests</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3" style="background: #004392; color: white;">Order ID</th>
                            <th class="border-0 py-3" style="background: #004392; color: white;">Request Type</th>
                            <th class="border-0 py-3" style="background: #004392; color: white;">Customer</th>
                            <th class="border-0 py-3" style="background: #004392; color: white;">Product</th>
                            <th class="border-0 py-3" style="background: #004392; color: white;">Amount</th>
                            <th class="border-0 py-3" style="background: #004392; color: white;">Request Date</th>
                            <th class="border-0 py-3" style="background: #004392; color: white;">Status</th>
                            <th class="border-0 py-3" style="background: #004392; color: white;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns ?? [] as $request)
                        @php
                            $requestType = $request->return_type ?? 'return';
                            $requestStatus = $request->status ?? 'pending';
                        @endphp
                        <tr>
                            <td class="align-middle">
                                <a href="javascript:void(0)" class="text-primary text-decoration-none fw-semibold" onclick="showOrderDetails({{ $request->order_id }})">
                                    #{{ $request->order_id }}
                                </a>
                            </td>
                            <td class="align-middle">
                                <span class="{{ $requestType == 'cancellation' ? 'badge-cancellation' : 'badge-return' }}">
                                    <i class="{{ $requestType == 'cancellation' ? 'ri-close-circle-line' : 'ri-refund-2-line' }}"></i>
                                    {{ ucfirst($requestType) }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">{{ $request->customer->name ?? 'N/A' }}</span>
                                    <small class="text-muted">{{ $request->customer->email ?? '' }}</small>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="small">{{ $request->order->items->first()->product->name ?? 'N/A' }}</span>
                                <small class="d-block text-muted">Qty: {{ $request->order->items->first()->quantity ?? 1 }}</small>
                            </td>
                            <td class="align-middle">
                                <span class="fw-semibold">₹{{ number_format($request->order->total_amount ?? $request->order->total ?? 0, 2) }}</span>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex flex-column">
                                    <span>{{ $request->created_at ? $request->created_at->format('d M Y') : '-' }}</span>
                                    <small class="text-muted">{{ $request->created_at ? $request->created_at->format('h:i A') : '-' }}</small>
                                </div>
                            </td>
                            <td class="align-middle">
                                @if($requestStatus == 'pending' || $requestStatus == 'requested')
                                    <span class="badge-pending"><i class="ri-time-line"></i> Pending</span>
                                @elseif($requestStatus == 'approved' || $requestStatus == 'refunded' || $requestStatus == 'completed')
                                    <span class="badge-approved"><i class="ri-checkbox-circle-line"></i> {{ ucfirst($requestStatus) }}</span>
                                @else
                                    <span class="badge-rejected"><i class="ri-close-circle-line"></i> {{ ucfirst($requestStatus) }}</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="d-flex gap-1">
                                    <!-- View Button -->
                                    <button type="button" class="btn-action btn-view" onclick="showOrderDetails({{ $request->order_id }})" title="View Details">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    
                                    <!-- Approve Button (only for pending requests) -->
                                    @if($requestStatus == 'pending' || $requestStatus == 'requested')
                                        <button type="button" class="btn-action btn-approve" onclick="showApproveModal({{ $request->order_id }}, '{{ $requestType }}', {{ $request->order->total_amount ?? $request->order->total ?? 0 }})" title="Approve Request">
                                            <i class="ri-check-line"></i>
                                        </button>
                                        <button type="button" class="btn-action btn-reject" onclick="showRejectModal({{ $request->order_id }}, '{{ $requestType }}')" title="Reject Request">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    @endif
                                    
                                    <!-- Process Refund Button -->
                                    @if($requestStatus == 'approved' && ($request->order->refund_status ?? 'pending') != 'completed')
                                        <button type="button" class="btn-action btn-refund" onclick="processRefund({{ $request->order_id }})" title="Process Refund">
                                            <i class="ri-bank-card-line"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="ri-inbox-line fs-1 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">No cancellation or return requests found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($returns ?? [], 'links'))
            <div class="p-3 bg-light border-top">
                {{ $returns->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Request Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 12px 20px;">
                <h6 class="modal-title fw-semibold" id="approveModalLabel">
                    <i class="ri-check-line me-2"></i> Approve Request
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="approveOrderId">
                <input type="hidden" id="approveRequestTypeHidden">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">
                        <i class="ri-file-text-line me-1"></i> Approval Notes
                    </label>
                    <textarea id="approvalNotes" class="form-control form-control-sm" rows="3" placeholder="Enter approval notes..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">
                        <i class="ri-bank-card-line me-1"></i> Refund Amount
                    </label>
                    <input type="number" id="refundAmount" class="form-control form-control-sm" step="0.01" required>
                    <small class="text-muted" id="approveMaxAmount"></small>
                </div>
                <div class="mb-0" id="qualityCheckDiv" style="display: none;">
                    <div class="form-check">
                        <input type="checkbox" id="qualityCheck" class="form-check-input" value="1">
                        <label class="form-check-label small" for="qualityCheck">
                            <i class="ri-shield-check-line me-1"></i> Require Quality Check before refund
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-sm" id="confirmApproveBtn" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none;">
                    <i class="ri-check-line me-1"></i> Approve Request
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Request Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 12px 20px;">
                <h6 class="modal-title fw-semibold" id="rejectModalLabel">
                    <i class="ri-close-line me-2"></i> Reject Request
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="rejectOrderId">
                <input type="hidden" id="rejectRequestTypeHidden">
                <div class="mb-0">
                    <label class="form-label small fw-semibold">
                        <i class="ri-chat-quote-line me-1"></i> Rejection Reason
                    </label>
                    <textarea id="rejectionReason" class="form-control form-control-sm" rows="4" placeholder="Enter rejection reason..." required></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-sm" id="confirmRejectBtn" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border: none;">
                    <i class="ri-close-line me-1"></i> Reject Request
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-0 py-3 px-4">
                <div>
                    <h6 class="modal-title fw-semibold mb-1" id="orderDetailsTitle">
                        <i class="ri-shopping-bag-line me-2"></i> Order Details
                    </h6>
                    <small class="text-muted" id="orderDetailsDate"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0" id="orderDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Loading...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Monitor Return Flow -->
<div class="modal fade" id="monitorReturnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white border-0" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-20 p-2 me-2">
                        <i class="ri-flow-chart fs-4 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">Monitor Return Flow</h5>
                        <p class="mb-0 small text-white opacity-85">Track items returning to the warehouse/seller</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Return Status</th>
                                <th>Courier Tracking</th>
                                <th>Expected Arrival</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returnFlows ?? [] as $rf)
                            <tr>
                                <td>#{{ $rf->order_id }}</td>
                                <td>
                                    <span class="small">{{ $rf->order->items->first()->product->name ?? 'N/A' }}</span>
                                    @if(($rf->order->items->count() ?? 0) > 1)
                                        <small class="text-muted d-block">+{{ $rf->order->items->count() - 1 }} more items</small>
                                    @endif
                                </td>
                                <td>{{ $rf->customer->name ?? 'N/A' }}</td>
                                <td>
                                    @if($rf->status == 'refunded' || $rf->status == 'completed')
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold"><i class="ri-checkbox-circle-line me-1"></i>Returned</span>
                                    @elseif($rf->status == 'approved')
                                        <span class="badge bg-info bg-opacity-10 text-info fw-semibold"><i class="ri-truck-line me-1"></i>In Transit</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning fw-semibold"><i class="ri-time-line me-1"></i>{{ ucfirst($rf->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rf->pickup_status)
                                        <span class="text-secondary small"><i class="ri-barcode-box-line me-1"></i>{{ ucfirst($rf->pickup_status) }}</span>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $rf->pickup_date ? \Carbon\Carbon::parse($rf->pickup_date)->format('d M Y') : 'Pending Schedule' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No active return flows to monitor.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Verify Refund Completion -->
<div class="modal fade" id="verifyRefundModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white border-0" style="background: linear-gradient(135deg, #10b981, #059669);">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-20 p-2 me-2">
                        <i class="ri-checkbox-circle-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">Verify Refund Completion</h5>
                        <p class="mb-0 small text-white opacity-85">Monitor and verify processed refunds with payment gateways</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Amount</th>
                                <th>Gateway</th>
                                <th>Transaction ID</th>
                                <th>Refund Date</th>
                                <th>Verification Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($refundFlows ?? [] as $rf)
                            <tr>
                                <td>#{{ $rf->order_id }}</td>
                                <td><strong>₹{{ number_format($rf->order->refund_amount ?? $rf->order->total_amount ?? $rf->order->total ?? 0, 2) }}</strong></td>
                                <td><span class="badge bg-light text-dark text-uppercase">{{ $rf->refund_mode ?? 'wallet' }}</span></td>
                                <td><span class="text-muted font-monospace small">{{ $rf->otp ? 'TXN-'.$rf->otp : 'TXN-REF-'.$rf->id }}</span></td>
                                <td><span class="small text-muted">{{ $rf->updated_at ? $rf->updated_at->format('d M Y, h:i A') : '-' }}</span></td>
                                <td>
                                    @if($rf->status == 'refunded')
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold"><i class="ri-checkbox-circle-line me-1"></i>Verified</span>
                                    @elseif($rf->admin_refund_status == 'pending')
                                        <span class="badge bg-warning bg-opacity-10 text-warning fw-semibold"><i class="ri-time-line me-1"></i>Pending</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold">{{ ucfirst($rf->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No pending refunds to verify.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Include Remix Icon CSS -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

<script>
// Store orders data
let ordersData = @json($returns instanceof \Illuminate\Pagination\LengthAwarePaginator ? $returns->items() : $returns);

// Show Toast Notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `<i class="${type === 'success' ? 'ri-checkbox-circle-line' : 'ri-error-warning-line'} me-2"></i> ${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Update stats
function updateStats() {
    let total = ordersData?.length || 0;
    let pending = 0;
    let approved = 0;
    let rejected = 0;
    
    if(ordersData && ordersData.length > 0) {
        ordersData.forEach(request => {
            let status = request.status || 'pending';
            if(status == 'pending' || status == 'requested') pending++;
            else if(status == 'approved' || status == 'refunded' || status == 'completed') approved++;
            else if(status == 'rejected' || status == 'cancelled') rejected++;
        });
    }
    
    document.getElementById('totalOrders').innerText = total;
    document.getElementById('pendingOrders').innerText = pending;
    document.getElementById('approvedOrders').innerText = approved;
    document.getElementById('rejectedOrders').innerText = rejected;
}

// Show Order Details
function showOrderDetails(orderId) {
    let request = ordersData.find(o => o.order_id == orderId);
    if(!request) {
        console.log('Order Return Request not found:', orderId);
        return;
    }
    
    let order = request.order;
    if(!order) {
        console.log('Order details not attached:', orderId);
        return;
    }
    
    let itemsHtml = '';
    if(order.items && order.items.length > 0) {
        order.items.forEach(item => {
            itemsHtml += `
                <div class="d-flex justify-content-between mb-1">
                    <span class="small">${item.product?.name || 'N/A'}</span>
                    <span class="small">x${item.quantity || 1}</span>
                </div>
            `;
        });
    } else {
        itemsHtml = '<p class="small text-muted">No items found</p>';
    }
    
    let html = `
        <div class="mb-3 pb-2 border-bottom">
            <div class="d-flex align-items-center mb-2">
                <i class="ri-user-line text-muted me-2"></i>
                <span class="small fw-semibold">Customer Information</span>
            </div>
            <p class="mb-1 small"><strong>Name:</strong> ${request.customer?.name || order.customer?.name || 'N/A'}</p>
            <p class="mb-1 small"><strong>Email:</strong> ${request.customer?.email || order.customer?.email || 'N/A'}</p>
            <p class="mb-0 small"><strong>Phone:</strong> ${request.customer?.phone || order.customer?.phone || 'N/A'}</p>
        </div>
        <div class="mb-3 pb-2 border-bottom">
            <div class="d-flex align-items-center mb-2">
                <i class="ri-shopping-bag-line text-muted me-2"></i>
                <span class="small fw-semibold">Order Items</span>
            </div>
            ${itemsHtml}
        </div>
        <div class="mb-3 pb-2 border-bottom">
            <div class="d-flex align-items-center mb-2">
                <i class="ri-file-text-line text-muted me-2"></i>
                <span class="small fw-semibold">Request Details</span>
            </div>
            <p class="mb-1 small"><strong>Reason:</strong> ${request.reason || 'N/A'}</p>
            <p class="mb-1 small"><strong>Note:</strong> ${request.note || 'N/A'}</p>
        </div>
        <div>
            <div class="d-flex justify-content-between mb-1">
                <span class="small text-muted">Total Amount:</span>
                <span class="small fw-semibold">₹${(order.total_amount || order.total || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span class="small text-muted">Payment Type:</span>
                <span class="small">${order.payment_type || 'N/A'}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="small text-muted">Order Status:</span>
                <span class="small">${order.status || 'pending'}</span>
            </div>
        </div>
    `;
    
    document.getElementById('orderDetailsTitle').innerHTML = `<i class="ri-shopping-bag-line me-2"></i> Order #${orderId}`;
    document.getElementById('orderDetailsDate').innerHTML = order.created_at ? new Date(order.created_at).toLocaleDateString() : 'N/A';
    document.getElementById('orderDetailsContent').innerHTML = html;
    
    let modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    modal.show();
}

// Show Approve Modal
function showApproveModal(orderId, requestType, totalAmount) {
    document.getElementById('approveOrderId').value = orderId;
    document.getElementById('approveRequestTypeHidden').value = requestType;
    document.getElementById('refundAmount').value = totalAmount;
    document.getElementById('approveMaxAmount').innerHTML = `Maximum refund amount: ₹${totalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
    document.getElementById('approvalNotes').value = '';
    
    const modalLabel = document.getElementById('approveModalLabel');
    if(modalLabel) {
        modalLabel.innerHTML = `<i class="ri-check-line me-2"></i> Approve ${requestType} Request for Order #${orderId}`;
    }
    
    const qualityCheckDiv = document.getElementById('qualityCheckDiv');
    if(qualityCheckDiv) {
        qualityCheckDiv.style.display = requestType == 'return' ? 'block' : 'none';
    }
    
    let modal = new bootstrap.Modal(document.getElementById('approveModal'));
    modal.show();
}

// Show Reject Modal
function showRejectModal(orderId, requestType) {
    document.getElementById('rejectOrderId').value = orderId;
    document.getElementById('rejectRequestTypeHidden').value = requestType;
    document.getElementById('rejectionReason').value = '';
    
    const modalLabel = document.getElementById('rejectModalLabel');
    if(modalLabel) {
        modalLabel.innerHTML = `<i class="ri-close-line me-2"></i> Reject ${requestType} Request for Order #${orderId}`;
    }
    
    let modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

// Process Refund
function processRefund(orderId) {
    if(confirm('Are you sure you want to process refund for this order?')) {
        showLoading(true);
        
        fetch(`/admin/orders/${orderId}/process-refund`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            showLoading(false);
            if(data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message || 'Failed to process refund', 'error');
            }
        })
        .catch(error => {
            showLoading(false);
            console.error('Error:', error);
            showToast('An error occurred while processing refund', 'error');
        });
    }
}

// Confirm Approve Button Click
document.getElementById('confirmApproveBtn')?.addEventListener('click', function() {
    const orderId = document.getElementById('approveOrderId').value;
    const requestType = document.getElementById('approveRequestTypeHidden').value;
    const approvalNotes = document.getElementById('approvalNotes').value;
    const refundAmount = document.getElementById('refundAmount').value;
    const qualityCheck = document.getElementById('qualityCheck')?.checked ? 1 : 0;
    
    if (!approvalNotes) {
        showToast('Please enter approval notes', 'error');
        return;
    }
    
    if (!refundAmount || refundAmount <= 0) {
        showToast('Please enter valid refund amount', 'error');
        return;
    }
    
    // Show loading state
    const btn = this;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-sm"></span> Processing...';
    
    fetch(`/admin/orders/${orderId}/approve-request`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            request_type: requestType,
            approval_notes: approvalNotes,
            refund_amount: refundAmount,
            quality_check: qualityCheck
        })
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        if(data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'Failed to approve request', 'error');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        console.error('Error:', error);
        showToast('An error occurred', 'error');
    });
});

// Confirm Reject Button Click
document.getElementById('confirmRejectBtn')?.addEventListener('click', function() {
    const orderId = document.getElementById('rejectOrderId').value;
    const requestType = document.getElementById('rejectRequestTypeHidden').value;
    const rejectionReason = document.getElementById('rejectionReason').value;
    
    if (!rejectionReason) {
        showToast('Please enter rejection reason', 'error');
        return;
    }
    
    // Show loading state
    const btn = this;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-sm"></span> Processing...';
    
    fetch(`/admin/orders/${orderId}/reject-request`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            rejection_reason: rejectionReason
        })
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        if(data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'Failed to reject request', 'error');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        console.error('Error:', error);
        showToast('An error occurred', 'error');
    });
});

// Show/Hide Loading
function showLoading(show) {
    if(show) {
        document.body.style.cursor = 'wait';
    } else {
        document.body.style.cursor = 'default';
    }
}

// Export Report Function
document.getElementById('exportReport')?.addEventListener('click', function() {
    let requestType = document.querySelector('select[name="request_type"]')?.value || '';
    let status = document.querySelector('select[name="status"]')?.value || '';
    let seller = document.querySelector('select[name="seller"]')?.value || '';
    let product = document.querySelector('select[name="product"]')?.value || '';
    
    let exportUrl = "{{ route('admin.orders.export-cancellation') }}?";
    if(requestType) exportUrl += `request_type=${requestType}&`;
    if(status) exportUrl += `status=${status}&`;
    if(seller) exportUrl += `seller=${seller}&`;
    if(product) exportUrl += `product=${product}&`;
    
    window.location.href = exportUrl;
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
});

// Make functions global
window.showOrderDetails = showOrderDetails;
window.showApproveModal = showApproveModal;
window.showRejectModal = showRejectModal;
window.processRefund = processRefund;
</script>

@endsection
