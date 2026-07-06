@extends('layouts.vertical', ['title' => 'Create Ticket'])

@section('content')

<style>
    .btn-primary,
    .btn-warning,
    .btn-danger,
    .btn-info {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-secondary {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
</style>

<div class="container-fluid pt-3">

    <h2 class="fw-bold mb-4">Create Ticket</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <form action="{{ route('admin.tickets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Ticket Type (Only for Admin) -->
                @if($userType == 'admin')
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Ticket Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="">Select Ticket Type</option>
                        <option value="product" {{ old('type') == 'product' ? 'selected' : '' }}>Product Ticket</option>
                        <option value="travel" {{ old('type') == 'travel' ? 'selected' : '' }}>Travel Ticket</option>
                    </select>
                    @error('type')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                @else
                <!-- Hidden field for product/travel admin -->
                <input type="hidden" name="type" value="{{ $userType }}">
                <!-- Show user type as read-only -->
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Ticket Type <span class="text-danger">*</span></label>
                    <input type="text" class="form-control bg-light" value="{{ ucfirst($userType) }} Ticket" readonly>
                    <small class="text-muted">You can only create {{ $userType }} tickets</small>
                </div>
                @endif

                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Search Phone Number</label>
                    <div class="search-wrapper" style="position: relative;">
                        <span style="
                            position: absolute;
                            top: 50%;
                            left: 14px;
                            transform: translateY(-50%);
                            font-size: 18px;
                            color: #6c757d;
                        ">
                            🔍
                        </span>
                        <input type="text" id="phoneSearch"
                               class="form-control"
                               placeholder="Search by phone number..."
                               required
                               style="
                                    padding-left: 45px;
                                    height: 48px;
                                    border-radius: 8px;
                                    font-size: 16px;
                               ">
                    </div>
                </div>

                <div class="row">
                    {{-- Auto-filled Name --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" id="fullName" class="form-control" readonly required>
                    </div>

                    {{-- Auto-filled Email --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" readonly required>
                    </div>

                    {{-- Final Phone Number Field --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="hidden" id="phoneInput" name="phone_number">
                        <input type="text" id="phoneDisplay" class="form-control" readonly>
                    </div>

                    {{-- Ticket Title --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ticket Title</label>
                        <input type="text" name="ticket_title" class="form-control" required>
                        @error('ticket_title')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ticket Category</label>
                        @if($userType == 'admin')
                            <select name="category" class="form-control" required id="categorySelect">
                                <option value="">Select Category</option>
                                <optgroup label="Product Categories">
                                    @foreach($categories['product'] as $key => $cat)
                                        <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Travel Categories">
                                    @foreach($categories['travel'] as $key => $cat)
                                        <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        @else
                            <select name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $cat)
                                    <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        @endif
                        @error('category')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Priority --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-control" required>
                            <option value="">Select Priority</option>
                            @foreach($priorities as $p)
                                <option value="{{ $p }}" {{ old('priority') == $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                        @error('priority')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">Describe Your Issue</label>
                        <textarea name="issue_description" rows="4" class="form-control" required>{{ old('issue_description') }}</textarea>
                        @error('issue_description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <h5 class="fw-bold mt-3">Order Information</h5>

                    {{-- Orders --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Select Order</label>
                        <select name="order_id" id="orderSelect" class="form-control">
                            <option value="">Select Order</option>
                        </select>
                        @error('order_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Date --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Purchase Date</label>
                        <input type="text" id="purchaseDate" class="form-control" readonly>
                    </div>

                    {{-- Payment --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment Method</label>
                        <input type="text" id="paymentMethod" class="form-control" readonly>
                    </div>

                    {{-- Products --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">Products</label>
                        <div id="productList">
                            <div class="alert alert-info">No order selected</div>
                        </div>
                    </div>

                    {{-- Attachment --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">Upload Attachment</label>
                        <input type="file" name="attachment" class="form-control">
                        @error('attachment')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Contact --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Preferred Contact Method</label>
                        <select name="preferred_contact_method" class="form-control" required>
                            <option value="">Select Method</option>
                            <option value="email" {{ old('preferred_contact_method') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="phone" {{ old('preferred_contact_method') == 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="whatsapp" {{ old('preferred_contact_method') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        </select>
                        @error('preferred_contact_method')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Preferred Contact Time</label>
                        <input type="text" name="preferred_contact_time" class="form-control" value="{{ old('preferred_contact_time') }}">
                        @error('preferred_contact_time')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Ticket</button>
                </div>
            </form>

        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const BASE_URL = "{{ url('/') }}";

    // Phone search functionality
    $("#phoneSearch").on("keyup", function () {
        let phone = $(this).val();

        if (phone.length < 4) {
            clearFields();
            return;
        }

        $.ajax({
            url: BASE_URL + "/admin/tickets/search-phone/" + phone,
            method: "GET",
            dataType: "json",
            success: function (data) {
                if (!data || !data.user) {
                    clearFields();
                    return;
                }

                let user = data.user;
                let orders = data.orders;

                // Auto-fill user details
                $("#fullName").val(user.name || user.full_name || "");
                $("#email").val(user.email || "");
                $("#phoneInput").val(user.phone || phone);
                $("#phoneDisplay").val(user.phone || phone);

                // Populate orders dropdown
                let orderSelect = $("#orderSelect");
                orderSelect.html('<option value="">Select Order</option>');

                if (orders.length > 0) {
                    orders.forEach(order => {
                        orderSelect.append(`
                            <option value="${order.id}">
                                ${order.transaction_id || 'Order #' + order.id} - ${new Date(order.created_at).toLocaleDateString()}
                            </option>
                        `);
                    });

                    // Load first order details
                    loadOrderDetails(orders[0].id);
                } else {
                    clearOrderDetails();
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
                clearFields();
            }
        });
    });

    // Load order details when order is selected
    $("#orderSelect").on("change", function() {
        let orderId = $(this).val();
        if (orderId) {
            loadOrderDetails(orderId);
        } else {
            clearOrderDetails();
        }
    });

    // Load order details function
    function loadOrderDetails(orderId) {
        $.ajax({
            url: BASE_URL + "/admin/tickets/get-products/" + orderId,
            method: "GET",
            dataType: "json",
            success: function (data) {
                // Display order details
                $.ajax({
                    url: BASE_URL + "/api/orders/" + orderId, // You need to create this API endpoint
                    method: "GET",
                    success: function(order) {
                        $("#purchaseDate").val(new Date(order.created_at).toLocaleDateString());
                        $("#paymentMethod").val(order.payment_method || 'N/A');
                    }
                });

                // Display products
                let productList = $("#productList");
                productList.html("");

                if (data.length > 0) {
                    let html = '<div class="list-group">';
                    data.forEach(item => {
                        html += `
                            <div class="list-group-item">
                                <strong>${item.name}</strong>
                                <div class="small text-muted">
                                    Quantity: ${item.qty || 1} | 
                                    Price: ₹${item.price || 'N/A'}
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    productList.html(html);
                } else {
                    productList.html('<div class="alert alert-info">No products found for this order</div>');
                }
            },
            error: function () {
                $("#productList").html('<div class="alert alert-danger">Failed to load products</div>');
            }
        });
    }

    // Clear fields function
    function clearFields() {
        $("#fullName").val("");
        $("#email").val("");
        $("#phoneInput").val("");
        $("#phoneDisplay").val("");
        clearOrderDetails();
    }

    // Clear order details function
    function clearOrderDetails() {
        $("#purchaseDate").val("");
        $("#paymentMethod").val("");
        $("#productList").html('<div class="alert alert-info">No order selected</div>');
    }

    // For admin: Update categories when type changes
    @if($userType == 'admin')
    $('select[name="type"]').on('change', function() {
        let type = $(this).val();
        let categorySelect = $('#categorySelect');
        
        categorySelect.html('<option value="">Select Category</option>');
        
        if (type === 'product') {
            categorySelect.append('<optgroup label="Product Categories">');
            @foreach($categories['product'] as $key => $cat)
                categorySelect.append('<option value="{{ $key }}">{{ $cat }}</option>');
            @endforeach
        } else if (type === 'travel') {
            categorySelect.append('<optgroup label="Travel Categories">');
            @foreach($categories['travel'] as $key => $cat)
                categorySelect.append('<option value="{{ $key }}">{{ $cat }}</option>');
            @endforeach
        }
    });
    @endif
});
</script>
@endsection
