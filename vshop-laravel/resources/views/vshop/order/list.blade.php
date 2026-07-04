@extends('vshop.main')

@section('title', 'My Orders')

@section('content')
<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
    <div class="dashboardPosPagesLeftContentonner">
        <div class="dashboardDtlsArea">
            <div class="dashboardDtlsAreaBody">
                <div class="bed-cum-sec justify-content-between">
                    <div class="bedcum-left">
                        <button class="back-btn" onclick="window.history.back()">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>
                        <ul>
                            <li><a href="{{ route('home') }}">Home</a> /</li>
                            <li>Orders</li>
                        </ul>
                    </div>
                    <div class="date-short-sec d-flex align-items-center gap-3">
                        <!-- ===== RAISE COMPLAINT BUTTON ===== -->
                        <button class="btn btn-danger " style="font-size: 10px;" id="openComplaintPopupFromOrders">
                           Raise Complaint
                        </button>

                        <div class="date-sec">
                            <div class="date-input-box">
                                <input type="date" id="startDate" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                            </div>
                            <div class="date-input-box">
                                <input type="date" id="endDate" value="{{ date('Y-m-d') }}">
                            </div>
                            <button id="filterBtn"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </div>
                        <div class="order-dropdown-sec">
                            <div class="dropdown high-low-dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Delivered Orders
                                    <i class="fa-solid fa-filter" style="font-size: 15px; color: #5F6368;"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item filter-order" data-status="all" href="#">All Orders</a></li>
                                    <li><a class="dropdown-item filter-order" data-status="pending" href="#">Pending Orders</a></li>
                                    <li><a class="dropdown-item filter-order" data-status="dispatched" href="#">Dispatched Orders</a></li>
                                    <li><a class="dropdown-item filter-order" data-status="delivered" href="#">Delivered Orders</a></li>
                                    <li><a class="dropdown-item filter-order" data-status="cancelled" href="#">Cancelled Orders</a></li>
                                    <li><a class="dropdown-item filter-order" data-status="return" href="#">Return Orders</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="order-list-sec">
                    <ul>
                        @forelse($orders as $order)
                        <li class="order-item" data-status="{{ $order->status }}">
                          

                                {{-- LEFT --}}
                                <div class="order-list-box">
                                    <h3>
                                        <a href="{{ route('vshop.order.details', $order->id) }}">
                                            Order no : {{ $order->id }}
                                        </a>
                                    </h3>

                                    <div class="total-order-sec">
                                        <p>Total Items : {{ $order->items->count() }}</p>
                                        <p>Total Quantity : {{ $order->items->sum('quantity') }}</p>
                                    </div>

                                    {{-- 🆕 PRODUCT IMAGES --}}
                                    <div class="product-thumbnails mt-2 d-flex gap-2 flex-wrap">
                                        @php
                                            $items = $order->items->take(4);
                                        @endphp
                                        @foreach($items as $item)
                                            @php
                                                $product = $item->product;
                                                $image = $product && $product->thumbnail 
                                                    ? asset($product->thumbnail) 
                                                    : ($product && $product->images 
                                                        ? asset(json_decode($product->images, true)[0] ?? '') 
                                                        : asset('images/no-image.png'));
                                            @endphp
                                            <div class="product-thumb">
                                                <img src="{{ $image }}" alt="{{ $product->name ?? 'Product' }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 4)
                                            <div class="product-thumb more-thumb">
                                                <span class="badge bg-secondary" style="width:50px; height:50px; display:flex; align-items:center; justify-content:center; border-radius:4px;">+{{ $order->items->count() - 4 }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- MIDDLE --}}
                                <div class="order-list-box">
                                    <div class="total-time-sec">
                                        <p>{{ $order->created_at->format('h:i A') }}</p>
                                        <p>{{ $order->created_at->format('d-m-Y') }}</p>
                                    </div>

                                    <p class="order-status">
                                        Order Status :
                                        <span class="order-badge {{ $order->status }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </p>
                                </div>

                                {{-- RIGHT --}}
                                <div class="order-list-box">
                                    <div class="total-time-sec justify-content-end">
                                        <p class="blackColor">Estimated Delivery Date :</p>
                                        <p class="grenColor">26-10-2025</p>
                                    </div>
                                    <p class="grandtotal">
                                        Grand Total :
                                        <span class="{{ in_array(strtolower($order->status), ['cancelled','return','returned']) ? 'redColor' : 'blueColor' }}">
                                            ₹ {{ number_format($order->total_amount, 2) }}
                                        </span>
                                    </p>

                                    @if(in_array(strtolower($order->status), ['return', 'returned']))
                                        @php
                                            $orderReturn = DB::table('order_returns')->where('order_id', $order->id)->first();
                                            $rStatus = $orderReturn ? strtolower($orderReturn->status) : 'requested';
                                            $dispStatus = 'Processing';
                                            $statusColor = 'warning';
                                            if($rStatus == 'approved') { $dispStatus = 'Pending Refund'; $statusColor = 'info'; }
                                            if($rStatus == 'rejected') { $dispStatus = 'Rejected'; $statusColor = 'danger'; }
                                            if($rStatus == 'refunded') { $dispStatus = 'Refunded'; $statusColor = 'success'; }
                                            
                                            $rAmount = 0;
                                            if($rStatus == 'refunded' && $orderReturn) {
                                                $txn = DB::table('wallet_transactions')
                                                    ->where('customer_id', auth()->id())
                                                    ->where('transaction_type', 'refund')
                                                    ->where('description', 'LIKE', '%Order #' . $order->id . '%')
                                                    ->first();
                                                if ($txn) $rAmount = $txn->credit;
                                            }
                                        @endphp
                                        <div class="mt-2 text-end">
                                            <p class="mb-1" style="font-size:13px; color:#555;"><strong>Refund Status:</strong> 
                                                <span class="badge bg-{{ $statusColor }} px-2 py-1" style="font-size:11px; font-weight:600;">
                                                    {{ $dispStatus }}
                                                </span>
                                            </p>
                                            @if($rStatus == 'refunded' && $rAmount > 0)
                                            <p class="mb-0" style="font-size:13px; color:#555;"><strong>Refunded:</strong> <span class="text-success fw-bold">₹{{ number_format($rAmount, 2) }}</span></p>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- DETAILS BUTTON --}}
                                    <a href="{{ route('vshop.order.details', $order->id) }}" class="btn btn-sm btn-outline-primary mt-2 rounded-pill px-4" style="font-weight: 500;">
                                        View Details
                                    </a>
                                </div>

                            
                        </li>
                        @empty
                            <p>No orders found.</p>
                        @endforelse
                    </ul>
                </div>

                <div class="pagination-sec">
                    @if($orders->hasPages())
                    <div class="pagination-sec mt-4">
                        {{ $orders->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.orangeColor { color: #ff9800 !important; }
.grenColor { color: #4caf50 !important; }
.redColor { color: #dc3545 !important; }
.blueColor { color: #0d6efd !important; }

.order-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.order-item {
    list-style: none;
    margin-bottom: 20px;
}

.order-list-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: box-shadow 0.3s ease;
}

.order-list-wrapper:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.order-list-box {
    flex: 1;
}

.order-list-box:nth-child(2) {
    text-align: center;
}

.order-list-box:nth-child(3) {
    text-align: right;
}

.order-list-box h3 a {
    color: #333;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
}

.order-list-box h3 a:hover {
    color: #0d6efd;
}

.total-order-sec p, .total-time-sec p {
    margin: 0;
    font-size: 14px;
    color: #555;
}

.grandtotal {
    font-size: 16px;
    font-weight: 700;
    margin-top: 8px;
}

.order-badge.delivered { background: #d4edda; color: #155724; }
.order-badge.dispatched { background: #cce5ff; color: #004085; }
.order-badge.pending { background: #fff3cd; color: #856404; }
.order-badge.cancelled { background: #f8d7da; color: #721c24; }
.order-badge.return { background: #f8d7da; color: #721c24; }

/* Complaint button style */
#openComplaintPopupFromOrders {
    background: #dc3545;
    color: #fff;
    border: none;
    padding: 8px 18px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 500;
    transition: 0.3s;
}
#openComplaintPopupFromOrders:hover {
    background: #c82333;
    color: #fff;
}

/* 🆕 Product thumbnails */
.product-thumbnails {
    margin-top: 8px;
}
.product-thumb img {
    border: 1px solid #ddd;
    transition: transform 0.2s;
}
.product-thumb img:hover {
    transform: scale(1.1);
}
.more-thumb .badge {
    font-size: 12px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {

    // ========== RAISE COMPLAINT POPUP ==========
    function openComplaintPopup() {
        let orderOptions = '';
        @if(isset($orders) && $orders->count())
            @foreach($orders as $order)
                orderOptions += `<option value="{{ $order->id }}">
                    {{ $order->id }} - Delivered on {{ $order->delivery_date ? date('d M Y', strtotime($order->delivery_date)) : 'N/A' }}
                </option>`;
            @endforeach
        @else
            orderOptions = `<option value="">No orders available</option>`;
        @endif

        const formHtml = `
            <div class="complaint-form" style="text-align: left;">
                <div class="mb-2">
                    <label for="swal_order_id" class="form-label">Select Order *</label>
                    <select id="swal_order_id" class="form-select" required>
                        <option value="">Select an order</option>
                        ${orderOptions}
                    </select>
                </div>
                <div class="mb-2">
                    <label for="swal_issue_type" class="form-label">Select Issue Type *</label>
                    <select id="swal_issue_type" class="form-select" required>
                        <option value="">Select issue type</option>
                        <option value="Damaged Product">Damaged Product</option>
                        <option value="Wrong Item">Wrong Item</option>
                        <option value="Late Delivery">Late Delivery</option>
                        <option value="Missing Parts">Missing Parts</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label for="swal_description" class="form-label">Issue Description *</label>
                    <textarea id="swal_description" class="form-control" rows="4" placeholder="Please describe your issue in detail..." required></textarea>
                </div>
                <div class="mb-2">
                    <label for="swal_proof" class="form-label">Upload Proof (Images / Videos / Documents)</label>
                    <input type="file" id="swal_proof" class="form-control" multiple accept="image/*,video/*,.pdf,.doc,.docx">
                    <small class="text-muted">You can upload multiple files (Max 5MB each)</small>
                    <div id="swal_filePreview" class="mt-2"></div>
                </div>
                <div class="mb-2">
                    <label class="form-label">Priority</label>
                    <div class="priority-group d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="swal_priority" id="swal_priorityLow" value="Low">
                            <label class="form-check-label" for="swal_priorityLow">Low</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="swal_priority" id="swal_priorityMedium" value="Medium" checked>
                            <label class="form-check-label" for="swal_priorityMedium">Medium</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="swal_priority" id="swal_priorityHigh" value="High">
                            <label class="form-check-label" for="swal_priorityHigh">High</label>
                        </div>
                    </div>
                </div>
            </div>
        `;

        Swal.fire({
            title: 'Raise a Complaint',
            html: formHtml,
            width: '600px',
            showCancelButton: true,
            confirmButtonText: 'Submit Complaint',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            preConfirm: () => {
                const orderId = document.getElementById('swal_order_id').value;
                const issueType = document.getElementById('swal_issue_type').value;
                const description = document.getElementById('swal_description').value;
                const priority = document.querySelector('input[name="swal_priority"]:checked')?.value || 'Medium';

                if (!orderId) {
                    Swal.showValidationMessage('Please select an order');
                    return false;
                }
                if (!issueType) {
                    Swal.showValidationMessage('Please select an issue type');
                    return false;
                }
                if (!description || description.trim().length < 10) {
                    Swal.showValidationMessage('Please describe your issue in detail (min 10 characters)');
                    return false;
                }

                return {
                    order_id: orderId,
                    issue_type: issueType,
                    description: description.trim(),
                    priority: priority,
                    proof: document.getElementById('swal_proof').files
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                const data = result.value;
                formData.append('order_id', data.order_id);
                formData.append('issue_type', data.issue_type);
                formData.append('description', data.description);
                formData.append('priority', data.priority);
                for (let i = 0; i < data.proof.length; i++) {
                    formData.append('proof[]', data.proof[i]);
                }

                Swal.fire({
                    title: 'Submitting...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: "{{ route('vshop.complaint.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({ 
                                icon: 'success', 
                                title: 'Complaint Raised!', 
                                text: res.message || 'Your complaint has been submitted.', 
                                confirmButtonColor: '#28a745' 
                            });
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            Swal.fire({ 
                                icon: 'error', 
                                title: 'Error', 
                                text: res.message || 'Something went wrong.', 
                                confirmButtonColor: '#dc3545' 
                            });
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Server error. Please try again.';
                        Swal.fire({ 
                            icon: 'error', 
                            title: 'Failed', 
                            text: msg, 
                            confirmButtonColor: '#dc3545' 
                        });
                    }
                });
            }
        });

        // File preview
        setTimeout(() => {
            document.getElementById('swal_proof')?.addEventListener('change', function(e) {
                const container = document.getElementById('swal_filePreview');
                if (!container) return;
                container.innerHTML = '';
                const files = this.files;
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const span = document.createElement('span');
                    span.className = 'file-preview-thumb';
                    span.innerHTML = `<i class="bi bi-file-earmark"></i> ${file.name} (${(file.size/1024).toFixed(1)} KB)`;
                    container.appendChild(span);
                }
            });
        }, 100);
    }

    $('#openComplaintPopupFromOrders').on('click', function(e) {
        e.preventDefault();
        openComplaintPopup();
    });

    // ===== BACK BUTTON =====
    $('.back-btn').click(function() {
        window.history.back();
    });

    // ===== DATE FILTER =====
    $('#filterBtn').click(function() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        if(startDate && endDate) {
            if(new Date(startDate) > new Date(endDate)) {
                alert('Start date cannot be greater than end date');
                return;
            }
            alert(`Filtering orders from ${startDate} to ${endDate}`);
        }
    });

    // ===== ORDER STATUS FILTER =====
    $('.filter-order').click(function(e) {
        e.preventDefault();
        const status = $(this).data('status');
        const dropdownText = $(this).text();
        $('.high-low-dropdown .dropdown-toggle').html(dropdownText + 
            ' <i class="fa-regular fa-filter" style="font-size: 15px; color: #5F6368;"></i>');
        if(status === 'all') {
            $('.order-item').show();
        } else {
            $('.order-item').hide();
            $('.order-item[data-status="' + status + '"]').show();
        }
    });

    // ===== DATE INPUT LIMITS =====
    const today = new Date().toISOString().split('T')[0];
    $('#startDate').attr('max', today);
    $('#endDate').attr('max', today);
    $('#startDate').change(function() {
        $('#endDate').attr('min', $(this).val());
    });
});
</script>

<style>
    .file-preview-thumb {
        display: inline-block;
        margin: 5px 8px 5px 0;
        padding: 4px 10px;
        background: #f0f0f0;
        border-radius: 20px;
        font-size: 13px;
        border: 1px solid #ddd;
    }
    .priority-group .form-check {
        display: inline-block;
        margin-right: 15px;
    }
    .complaint-form .form-control,
    .complaint-form .form-select {
        border-radius: 6px;
        border: 1px solid #ddd;
        padding: 10px 12px;
        font-size: 14px;
        width: 100%;
        margin-bottom: 12px;
    }
    .complaint-form .form-control:focus,
    .complaint-form .form-select:focus {
        border-color: #004392;
        box-shadow: 0 0 0 0.2rem rgba(0,67,146,0.25);
    }
    .swal2-popup .form-select,
    .swal2-popup .form-control {
        border: 1px solid #ddd !important;
        border-radius: 6px !important;
        padding: 10px 12px !important;
        font-size: 14px !important;
        width: 100% !important;
    }
    .swal2-popup .form-select:focus,
    .swal2-popup .form-control:focus {
        border-color: #004392 !important;
        box-shadow: 0 0 0 0.2rem rgba(0,67,146,0.25) !important;
    }
    .swal2-popup .form-label {
        font-weight: 500;
        margin-bottom: 4px;
        display: block;
        text-align: left;
    }
    .swal2-popup .mb-2 {
        margin-bottom: 12px;
        text-align: left;
    }
    .swal2-popup .priority-group {
        display: flex;
        gap: 15px;
        margin-top: 5px;
    }
    .swal2-popup .priority-group .form-check {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .swal2-popup .priority-group .form-check-input {
        margin: 0;
    }
    .swal2-popup .text-muted {
        font-size: 12px;
        color: #6c757d;
        display: block;
        margin-top: 4px;
    }
</style>
@endpush