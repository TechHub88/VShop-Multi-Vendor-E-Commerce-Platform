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
                            <li><a href="{{ route('vtravelhome') }}">Home</a> / </li>
                            <li>Orders</li>
                        </ul>
                    </div>
                    <div class="date-short-sec d-flex align-items-center gap-3">
                        <!-- ===== RAISE COMPLAINT BUTTON ===== -->
                        <button class="btn btn-danger btn-sm rounded-pill px-3" id="openComplaintPopupFromOrders">
                            <i class="bi bi-flag-fill"></i> Raise Complaint
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
                            <div class="order-list-wrapper">

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
                                        <span class="{{ in_array($order->status, ['cancelled','return']) ? 'redColor' : 'blueColor' }}">
                                            ₹ {{ number_format($order->total_amount, 2) }}
                                        </span>
                                    </p>

                                    {{-- DETAILS BUTTON --}}
                                    <a href="{{ route('vshop.order.details', $order->id) }}" class="btn-view mt-2">
                                        View Details
                                    </a>
                                </div>

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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {

    // ========== RAISE COMPLAINT POPUP (Same as My Account) ==========
    function openComplaintPopup() {
        // Build the order dropdown options dynamically from the page
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

        // File preview inside SweetAlert
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

    // Bind complaint popup to the button
    $('#openComplaintPopupFromOrders').on('click', function(e) {
        e.preventDefault();
        openComplaintPopup();
    });

    // ===== EXISTING SCRIPTS (filters, back, etc.) =====

    // Back button functionality
    $('.back-btn').click(function() {
        window.history.back();
    });

    // Date filter functionality
    $('#filterBtn').click(function() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        
        if(startDate && endDate) {
            if(new Date(startDate) > new Date(endDate)) {
                alert('Start date cannot be greater than end date');
                return;
            }
            // Here you would typically make an AJAX call to filter orders
            console.log('Filtering orders from', startDate, 'to', endDate);
            alert(`Filtering orders from ${startDate} to ${endDate}`);
        }
    });

    // Order status filter
    $('.filter-order').click(function(e) {
        e.preventDefault();
        const status = $(this).data('status');
        const dropdownText = $(this).text();
        
        // Update dropdown button text
        $('.high-low-dropdown .dropdown-toggle').html(dropdownText + 
            ' <i class="fa-regular fa-filter" style="font-size: 15px; color: #5F6368;"></i>');
        
        // Filter orders
        if(status === 'all') {
            $('.order-item').show();
        } else {
            $('.order-item').hide();
            $('.order-item[data-status="' + status + '"]').show();
        }
    });

    // Set today's date as max for date inputs
    const today = new Date().toISOString().split('T')[0];
    $('#startDate').attr('max', today);
    $('#endDate').attr('max', today);

    // Set min date for end date based on start date
    $('#startDate').change(function() {
        $('#endDate').attr('min', $(this).val());
    });

    // Quick filter buttons (optional)
    const quickFilters = {
        'today': function() {
            const today = new Date().toISOString().split('T')[0];
            $('#startDate').val(today);
            $('#endDate').val(today);
        },
        'last7days': function() {
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(startDate.getDate() - 6);
            
            $('#startDate').val(startDate.toISOString().split('T')[0]);
            $('#endDate').val(endDate.toISOString().split('T')[0]);
        },
        'last30days': function() {
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(startDate.getDate() - 29);
            
            $('#startDate').val(startDate.toISOString().split('T')[0]);
            $('#endDate').val(endDate.toISOString().split('T')[0]);
        }
    };

    // Uncomment below to add quick filter buttons
    // function addQuickFilters() {
    //     const filterHtml = `
    //         <div class="quick-filters mb-3">
    //             <button class="btn btn-outline-secondary btn-sm me-2" data-filter="today">Today</button>
    //             <button class="btn btn-outline-secondary btn-sm me-2" data-filter="last7days">Last 7 Days</button>
    //             <button class="btn btn-outline-secondary btn-sm" data-filter="last30days">Last 30 Days</button>
    //         </div>
    //     `;
    //     $('.date-short-sec').before(filterHtml);
    //     
    //     $('.quick-filters button').click(function() {
    //         const filter = $(this).data('filter');
    //         if(quickFilters[filter]) {
    //             quickFilters[filter]();
    //             $('#filterBtn').click();
    //         }
    //     });
    // }
    // addQuickFilters();

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