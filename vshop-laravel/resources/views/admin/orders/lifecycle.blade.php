@extends('layouts.vertical', ['title' => 'Order Lifecycle Control'])

@section('content')

<style>
    .lifecycle-card {
        border-radius: 16px;
        transition: all 0.2s ease;
        background: #fff;
    }
    .lifecycle-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .stage-completed {
        color: #10b981;
    }
    .stage-pending {
        color: #f59e0b;
    }
    .stage-missing {
        color: #ef4444;
    }
    .timeline-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }
    .timeline-dot.completed { background: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.2); }
    .timeline-dot.pending { background: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.2); }
    .timeline-dot.missing { background: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.2); }
    .order-row {
        cursor: pointer;
        transition: background 0.2s;
    }
    .order-row:hover {
        background: #f8fafc;
    }
    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
    }
    .status-delivered { background: #d1fae5; color: #065f46; }
    .status-shipped { background: #dbeafe; color: #1e40af; }
    .status-pending { background: #fed7aa; color: #92400e; }
    .status-processing { background: #fef3c7; color: #b45309; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
    .filter-card {
        border-radius: 12px;
        background: #fff;
    }
    .table-container {
        overflow-x: auto;
        border-radius: 12px;
    }
    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .data-table thead th {
        background: #0f172a;
        color: white;
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .data-table tbody td {
        padding: 12px 16px;
        font-size: 13px;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .data-table tbody tr:hover {
        background: #f1f5f9;
    }
    .kpi-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 16px;
        color: white;
    }
    .kpi-box.blue { background: linear-gradient(135deg, #3b82f6, #1e40af); }
    .kpi-box.green { background: linear-gradient(135deg, #10b981, #065f46); }
    .kpi-box.orange { background: linear-gradient(135deg, #f59e0b, #b45309); }
    .kpi-box.purple { background: linear-gradient(135deg, #8b5cf6, #5b21b6); }
    
    /* Loading Spinner */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.9);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #e2e8f0;
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Detail Modal */
    .detail-modal .modal-content {
        border-radius: 16px;
    }
    .timeline-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 16px;
        padding: 12px;
        background: #f8fafc;
        border-radius: 12px;
    }
    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        flex-shrink: 0;
    }
    .timeline-icon.completed { background: #d1fae5; color: #065f46; }
    .timeline-icon.pending { background: #fed7aa; color: #92400e; }
    .timeline-icon.missing { background: #fee2e2; color: #991b1b; }
    .timeline-content { flex: 1; }
    .timeline-title { font-weight: 600; font-size: 14px; margin-bottom: 4px; }
    .timeline-date { font-size: 11px; color: #64748b; }
    .timeline-detail { font-size: 12px; color: #475569; margin-top: 4px; }
    
    .btn-outline-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff;
    }
    .page-link.active, .active>.page-link {
        background-color: hsl(222.27deg 55.7% 30.98%);
        border-color: hsl(222.27deg 55.7% 30.98%);
    }
    .data-table thead th {
        background-color: #004392 !important;
        color: #fff !important;
        border-color: #fff !important;
    }
    .reset-btn {
        width: 100%;
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
        padding: 10px 7px;
        border-radius: 5px;
    }
    .kpi-box {
        background: #fff;
    }
    .kpi-box small {
        color: rgb(74 90 107 / 75%);
        font-size: 14px;
        text-transform: uppercase;
    }
    .kpi-box h3 {
        color: rgb(6 7 7);
    }
    .kpi-box:first-child i {
        background: #05966926;
        color: #059669;
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px !important;
    }
    .kpi-box i {
        background: #05966926;
        color: #004392;
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px !important;
    }
    .kpi-box i.yellow {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        color: #d97706;
    }
    .kpi-box i.blue {
        background: #00439233;
        color: #004392;
    }
     .kpi-box i.purple {
        background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
        color: #9333ea;
    }
    .filter-card select,
    .filter-card input {
        height: 40px;
    }
</style>

<div class="container-fluid py-3">
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Order Lifecycle Control</h4>
            <p class="text-muted small mb-0">Track complete order journey from receipt to closure</p>
        </div>
        <button class="btn btn-sm btn-outline-primary" onclick="refreshData()">
            <i class="fa-solid fa-rotate-right me-1"></i> Refresh
        </button>
    </div>

    <!-- KPI Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="kpi-box ">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="opacity-75">Total Orders</small>
                        <h3 class="fw-bold mb-0" id="totalOrders">0</h3>
                    </div>
                    <i class="fa-solid fa-shopping-cart fs-1"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-box">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="opacity-75">Pending</small>
                        <h3 class="fw-bold mb-0" id="pendingOrders">0</h3>
                    </div>
                    <i class="fa-solid fa-clock fs-1 yellow"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-box">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="opacity-75">Shipped</small>
                        <h3 class="fw-bold mb-0" id="shippedOrders">0</h3>
                    </div>
                    <i class="fa-solid fa-truck fs-1 blue"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-box">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="opacity-75">Delivered</small>
                        <h3 class="fw-bold mb-0" id="deliveredOrders">0</h3>
                    </div>
                    <i class="fa-solid fa-circle-check fs-1 purple"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card filter-card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Seller</label>
                    <select id="filterSeller" class="form-select form-select-sm">
                        <option value="">All Sellers</option>
                        @foreach($sellers ?? [] as $seller)
                            <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Product</label>
                    <select id="filterProduct" class="form-select form-select-sm">
                        <option value="">All Products</option>
                        @foreach($products ?? [] as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Search</label>
                    <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Order ID, Customer, Tracking...">
                </div>
                <div class="col-md-2">
                    <button class="reset-btn">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-container">
                <table class="data-table" id="ordersTable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Order Received</th>
                            <th>Payment</th>
                            <th>Dispatch</th>
                            <th>Delivery</th>
                            <th>Closure</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        @forelse($orders ?? [] as $order)
                        <tr class="order-row" data-order-id="{{ $order->id }}" onclick="showOrderDetails({{ $order->id }})">
                            <td>
                                <span class="fw-semibold text-primary">#{{ $order->id }}</span>
                                <small class="d-block text-muted">{{ $order->order_number ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <div>{{ $order->customer->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $order->customer->phone ?? '' }}</small>
                            </td>
                            <td id="orderReceived-{{ $order->id }}">
                                @if($order->created_at)
                                    <span class="stage-completed">
                                        <i class="fa-regular fa-calendar-check me-1"></i>
                                        {{ $order->created_at->format('d M Y') }}
                                    </span>
                                    <small class="d-block text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                @else
                                    <span class="stage-missing">
                                        <i class="fa-regular fa-clock me-1"></i> Not recorded
                                    </span>
                                @endif
                            </td>
                            <td id="payment-{{ $order->id }}">
                                @if(in_array(strtolower($order->payment_status), ['paid', 'completed', 'success']))
                                    <span class="stage-completed">
                                        <i class="fa-regular fa-credit-card me-1"></i>
                                        {{ $order->updated_at->format('d M Y') }}
                                    </span>
                                    <small class="d-block text-muted">
                                        {{ $order->payment_method ?? $order->payment_type ?? 'N/A' }}
                                        @if($order->transaction_id)
                                            <br>ID: {{ $order->transaction_id }}
                                        @endif
                                    </small>
                                @else
                                    <span class="stage-missing">
                                        <i class="fa-regular fa-hourglass-half me-1"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td id="dispatch-{{ $order->id }}">
                                @if($order->shipped_at || in_array(strtolower($order->status), ['shipped', 'out_for_delivery', 'delivered', 'completed']))
                                    <span class="stage-completed">
                                        <i class="fa-solid fa-truck-fast me-1"></i>
                                        {{ $order->shipped_at ? \Carbon\Carbon::parse($order->shipped_at)->format('d M Y') : $order->updated_at->format('d M Y') }}
                                    </span>
                                    <small class="d-block text-muted">
                                        {{ $order->courier_name ?? $order->shipping_method ?? 'Standard' }}
                                        @if($order->tracking_id)
                                            <br>Tracking: {{ $order->tracking_id }}
                                        @endif
                                    </small>
                                @else
                                    <span class="stage-missing">
                                        <i class="fa-regular fa-hourglass-half me-1"></i> Not dispatched
                                    </span>
                                @endif
                            </td>
                            <td id="delivery-{{ $order->id }}">
                                @if($order->delivered_at || in_array(strtolower($order->status), ['delivered', 'completed']))
                                    <span class="stage-completed">
                                        <i class="fa-regular fa-circle-check me-1"></i>
                                        {{ $order->delivered_at ? \Carbon\Carbon::parse($order->delivered_at)->format('d M Y') : $order->updated_at->format('d M Y') }}
                                    </span>
                                    <small class="d-block text-muted">
                                        Received by: Customer
                                    </small>
                                @else
                                    <span class="stage-missing">
                                        <i class="fa-regular fa-hourglass-half me-1"></i> Not delivered
                                    </span>
                                @endif
                            </td>
                            <td id="closure-{{ $order->id }}">
                                @if(in_array(strtolower($order->status), ['delivered', 'completed']))
                                    <span class="stage-completed">
                                        <i class="fa-regular fa-circle-check me-1"></i>
                                        Closed
                                    </span>
                                @elseif($order->status == 'cancelled')
                                    <span class="stage-missing">
                                        <i class="fa-regular fa-circle-xmark me-1"></i> Cancelled
                                    </span>
                                @else
                                    <span class="stage-pending">
                                        <i class="fa-regular fa-hourglass-half me-1"></i> Open
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $status = $order->status ?? 'pending';
                                    $statusClass = match($status) {
                                        'delivered', 'completed' => 'status-delivered',
                                        'shipped', 'out_for_delivery' => 'status-shipped',
                                        'processing' => 'status-processing',
                                        'cancelled' => 'status-cancelled',
                                        default => 'status-pending'
                                    };
                                    $statusText = match($status) {
                                        'delivered', 'completed' => 'Delivered',
                                        'shipped' => 'Shipped',
                                        'out_for_delivery' => 'Out for Delivery',
                                        'processing' => 'Processing',
                                        'cancelled' => 'Cancelled',
                                        default => 'Pending'
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                                No orders found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
           <div class="d-flex justify-content-center mt-3">
    {{ $orders->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade detail-modal" id="orderDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white border-0 pt-4 px-4">
                <div>
                    <h5 class="fw-bold mb-1" id="modalOrderTitle">Order Details</h5>
                    <p class="text-muted small mb-0" id="modalOrderSubtitle">Order #</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0" id="modalOrderContent">
                <!-- Content loaded via AJAX -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Loading order details...</p>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Store orders data
let ordersData = @json($orders->items() ?? []);

// Update KPI counters
function updateKPIs() {
    let total = ordersData.length;
    let pending = ordersData.filter(o => o.status === 'pending' || o.status === 'processing').length;
    let shipped = ordersData.filter(o => o.status === 'shipped' || o.status === 'out_for_delivery').length;
    let delivered = ordersData.filter(o => o.status === 'delivered' || o.status === 'completed').length;
    
    $('#totalOrders').text(total);
    $('#pendingOrders').text(pending);
    $('#shippedOrders').text(shipped);
    $('#deliveredOrders').text(delivered);
}

// Filter and Search function
function applyFilters() {
    let sellerId = $('#filterSeller').val();
    let productId = $('#filterProduct').val();
    let status = $('#filterStatus').val();
    let searchTerm = $('#searchInput').val().toLowerCase();
    
    $('#ordersTableBody tr').each(function() {
        let row = $(this);
        let orderId = row.data('order-id');
        let order = ordersData.find(o => o.id == orderId);
        
        if(!order) {
            row.show();
            return;
        }
        
        let match = true;
        
        if(sellerId && order.seller_id != sellerId) match = false;
        if(productId && order.product_id != productId) match = false;
        if(status && order.status != status) match = false;
        
        if(searchTerm) {
            let orderIdStr = '#' + order.id;
            let customerName = (order.customer?.name || '').toLowerCase();
            let trackingId = (order.tracking_id || '').toLowerCase();
            if(!orderIdStr.includes(searchTerm) && !customerName.includes(searchTerm) && !trackingId.includes(searchTerm)) {
                match = false;
            }
        }
        
        match ? row.show() : row.hide();
    });
}

// Refresh data without page reload
function refreshData() {
    $('#ordersTableBody').html('<tr><td colspan="8" class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2">Loading...</p></td></tr>');
    
    $.ajax({
        url: window.location.href,
        method: "GET",
        data: {
            ajax: 1,
            seller: $('#filterSeller').val(),
            product: $('#filterProduct').val(),
            status: $('#filterStatus').val(),
            search: $('#searchInput').val()
        },
        success: function(response) {
            if(response.orders) {
                ordersData = response.orders;
                updateKPIs();
                renderTable(ordersData);
            }
        },
        error: function() {
            location.reload();
        }
    });
}

// Render table from data
function renderTable(orders) {
    let html = '';
    if(!orders || orders.length === 0) {
        html = '<tr><td colspan="8" class="text-center py-5 text-muted"><i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>No orders found</td></tr>';
    } else {
        orders.forEach(order => {
            let statusClass = 'status-pending';
            let statusText = 'Pending';
            
            if(order.status === 'delivered' || order.status === 'completed') {
                statusClass = 'status-delivered';
                statusText = 'Delivered';
            } else if(order.status === 'shipped' || order.status === 'out_for_delivery') {
                statusClass = 'status-shipped';
                statusText = 'Shipped';
            } else if(order.status === 'processing') {
                statusClass = 'status-processing';
                statusText = 'Processing';
            } else if(order.status === 'cancelled') {
                statusClass = 'status-cancelled';
                statusText = 'Cancelled';
            }
            
            let paymentIsDone = ['paid', 'completed', 'success'].includes((order.payment_status || '').toLowerCase());
            let dispatchIsDone = order.shipped_at || ['shipped', 'out_for_delivery', 'delivered', 'completed'].includes((order.status || '').toLowerCase());
            let deliveryIsDone = order.delivered_at || ['delivered', 'completed'].includes((order.status || '').toLowerCase());
            let isClosed = ['delivered', 'completed'].includes((order.status || '').toLowerCase());
            let isCancelled = order.status == 'cancelled';
            
            html += `
                <tr class="order-row" data-order-id="${order.id}" onclick="showOrderDetails(${order.id})">
                    <td><span class="fw-semibold text-primary">#${order.id}</span><small class="d-block text-muted">${order.order_number || 'N/A'}</small></td>
                    <td><div>${order.customer?.name || 'N/A'}</div><small class="text-muted">${order.customer?.phone || ''}</small></td>
                    <td>${order.created_at ? `<span class="stage-completed"><i class="fa-regular fa-calendar-check me-1"></i>${new Date(order.created_at).toLocaleDateString()}</span><small class="d-block text-muted">${new Date(order.created_at).toLocaleTimeString()}</small>` : '<span class="stage-missing"><i class="fa-regular fa-clock me-1"></i>Not recorded</span>'}</td>
                    <td>${paymentIsDone ? `<span class="stage-completed"><i class="fa-regular fa-credit-card me-1"></i>${new Date(order.updated_at).toLocaleDateString()}</span><small class="d-block text-muted">${order.payment_method || order.payment_type || 'N/A'}${order.transaction_id ? '<br>ID: '+order.transaction_id : ''}</small>` : '<span class="stage-missing"><i class="fa-regular fa-hourglass-half me-1"></i>Pending</span>'}</td>
                    <td>${dispatchIsDone ? `<span class="stage-completed"><i class="fa-solid fa-truck-fast me-1"></i>${order.shipped_at ? new Date(order.shipped_at).toLocaleDateString() : new Date(order.updated_at).toLocaleDateString()}</span><small class="d-block text-muted">${order.courier_name || order.shipping_method || 'Standard'}${order.tracking_id ? '<br>Tracking: '+order.tracking_id : ''}</small>` : '<span class="stage-missing"><i class="fa-regular fa-hourglass-half me-1"></i>Not dispatched</span>'}</td>
                    <td>${deliveryIsDone ? `<span class="stage-completed"><i class="fa-regular fa-circle-check me-1"></i>${order.delivered_at ? new Date(order.delivered_at).toLocaleDateString() : new Date(order.updated_at).toLocaleDateString()}</span><small class="d-block text-muted">Received by: Customer</small>` : '<span class="stage-missing"><i class="fa-regular fa-hourglass-half me-1"></i>Not delivered</span>'}</td>
                    <td>${isClosed ? `<span class="stage-completed"><i class="fa-regular fa-circle-check me-1"></i>Closed</span>` : (isCancelled ? '<span class="stage-missing"><i class="fa-regular fa-circle-xmark me-1"></i>Cancelled</span>' : '<span class="stage-pending"><i class="fa-regular fa-hourglass-half me-1"></i>Open</span>')}</td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                </tr>
            `;
        });
    }
    $('#ordersTableBody').html(html);
    updateKPIs();
}

// Show order details in modal
function showOrderDetails(orderId) {
    let order = ordersData.find(o => o.id == orderId);
    if(!order) {
        Swal.fire('Error', 'Order details not found', 'error');
        return;
    }
    
    $('#modalOrderTitle').text(`Order #${order.id}`);
    $('#modalOrderSubtitle').text(order.order_number || `Placed on ${new Date(order.created_at).toLocaleDateString()}`);
    
    let paymentIsDone = ['paid', 'completed', 'success'].includes((order.payment_status || '').toLowerCase());
    let dispatchIsDone = order.shipped_at || ['shipped', 'out_for_delivery', 'delivered', 'completed'].includes((order.status || '').toLowerCase());
    let deliveryIsDone = order.delivered_at || ['delivered', 'completed'].includes((order.status || '').toLowerCase());
    let isClosed = ['delivered', 'completed'].includes((order.status || '').toLowerCase());
    
    let paymentDate = paymentIsDone ? new Date(order.updated_at).toLocaleString() : 'Awaiting payment';
    let dispatchDate = dispatchIsDone ? (order.shipped_at ? new Date(order.shipped_at).toLocaleString() : new Date(order.updated_at).toLocaleString()) : (paymentIsDone ? 'Ready to dispatch' : 'Waiting for payment');
    let deliveryDate = deliveryIsDone ? (order.delivered_at ? new Date(order.delivered_at).toLocaleString() : new Date(order.updated_at).toLocaleString()) : (dispatchIsDone ? 'In transit' : 'Not dispatched yet');
    let closureDate = isClosed ? (order.delivered_at ? new Date(order.delivered_at).toLocaleString() : new Date(order.updated_at).toLocaleString()) : (deliveryIsDone ? 'Ready to close' : (order.status == 'cancelled' ? 'Order cancelled' : 'Order in progress'));
    
    let timelineHtml = `
        <div class="mb-4">
            <h6 class="fw-bold mb-3"><i class="fa-regular fa-timeline me-2"></i>Lifecycle Timeline</h6>
            
            <!-- Order Received -->
            <div class="timeline-item">
                <div class="timeline-icon ${order.created_at ? 'completed' : 'missing'}">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div class="timeline-content">
                    <div class="timeline-title">Order Received</div>
                    <div class="timeline-date">${order.created_at ? new Date(order.created_at).toLocaleString() : 'Not recorded'}</div>
                    ${order.created_at ? `<div class="timeline-detail">Order placed by customer</div>` : ''}
                </div>
            </div>
            
            <!-- Payment -->
            <div class="timeline-item">
                <div class="timeline-icon ${paymentIsDone ? 'completed' : 'pending'}">
                    <i class="fa-regular fa-credit-card"></i>
                </div>
                <div class="timeline-content">
                    <div class="timeline-title">Payment ${paymentIsDone ? 'Completed' : 'Pending'}</div>
                    <div class="timeline-date">${paymentDate}</div>
                    ${order.payment_method ? `<div class="timeline-detail">Method: ${order.payment_method} | ${order.payment_type || 'N/A'}</div>` : ''}
                    ${order.transaction_id ? `<div class="timeline-detail">Transaction ID: ${order.transaction_id}</div>` : ''}
                </div>
            </div>
            
            <!-- Dispatch -->
            <div class="timeline-item">
                <div class="timeline-icon ${dispatchIsDone ? 'completed' : (paymentIsDone ? 'pending' : 'missing')}">
                    <i class="fa-solid fa-truck"></i>
                </div>
                <div class="timeline-content">
                    <div class="timeline-title">Dispatch</div>
                    <div class="timeline-date">${dispatchDate}</div>
                    ${order.courier_name || order.shipping_method ? `<div class="timeline-detail">Courier: ${order.courier_name || order.shipping_method || 'Standard'}</div>` : ''}
                    ${order.tracking_id ? `<div class="timeline-detail">Tracking ID: ${order.tracking_id}</div>` : ''}
                </div>
            </div>
            
            <!-- Delivery -->
            <div class="timeline-item">
                <div class="timeline-icon ${deliveryIsDone ? 'completed' : (dispatchIsDone ? 'pending' : 'missing')}">
                    <i class="fa-regular fa-circle-check"></i>
                </div>
                <div class="timeline-content">
                    <div class="timeline-title">Delivery</div>
                    <div class="timeline-date">${deliveryDate}</div>
                    <div class="timeline-detail">Received by: Customer</div>
                    ${order.delivery_notes ? `<div class="timeline-detail">Notes: ${order.delivery_notes}</div>` : ''}
                </div>
            </div>
            
            <!-- Closure -->
            <div class="timeline-item">
                <div class="timeline-icon ${isClosed ? 'completed' : (deliveryIsDone ? 'pending' : 'missing')}">
                    <i class="fa-regular fa-file-lines"></i>
                </div>
                <div class="timeline-content">
                    <div class="timeline-title">Order Closure</div>
                    <div class="timeline-date">${closureDate}</div>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="border-top pt-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="bg-light rounded-3 p-3">
                        <div class="small text-muted mb-2">Customer Information</div>
                        <p class="mb-1 fw-semibold">${order.customer?.name || 'N/A'}</p>
                        <p class="mb-1 small">${order.customer?.email || 'No email'}</p>
                        <p class="mb-0 small">${order.customer?.phone || 'No phone'}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-light rounded-3 p-3">
                        <div class="small text-muted mb-2">Order Summary</div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Total Amount:</span>
                            <span class="fw-semibold">₹${(order.total_amount || order.total || 0).toLocaleString()}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Payment Type:</span>
                            <span>${order.payment_type || 'N/A'}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Items Count:</span>
                            <span>${order.items_count || 1}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#modalOrderContent').html(timelineHtml);
    $('#orderDetailModal').modal('show');
}

// Event Listeners
$(document).ready(function() {
    updateKPIs();
    
    $('#filterSeller, #filterProduct, #filterStatus').on('change', function() {
        applyFilters();
    });
    
    $('#searchInput').on('keyup', function() {
        applyFilters();
    });
});

// View courier tracking (opens in new tab)
function viewTracking(trackingId, courier) {
    if(!trackingId) {
        Swal.fire('Info', 'No tracking ID available', 'info');
        return;
    }
    
    let trackingUrl = '';
    let courierLower = (courier || '').toLowerCase();
    
    if(courierLower.includes('dtdc')) {
        trackingUrl = `https://www.dtdc.in/tracking.asp?trackid=${trackingId}`;
    } else if(courierLower.includes('bluedart')) {
        trackingUrl = `https://www.bluedart.com/servlet/RoutingServlet?handler=tnt&action=awbQuery&awbno=${trackingId}`;
    } else if(courierLower.includes('delhivery')) {
        trackingUrl = `https://www.delhivery.com/track/package/${trackingId}`;
    } else {
        trackingUrl = `https://www.google.com/search?q=track+${courier}+${trackingId}`;
    }
    
    window.open(trackingUrl, '_blank');
}
</script>
@endsection