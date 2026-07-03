@extends('layouts.vertical', ['title' => 'Edit Ticket'])

@section('content')

<div class="container-fluid px-4 py-4">

    <h2 class="fw-bold mb-4">Edit Ticket: #{{ $ticket->id }}</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Ticket Type Display -->
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Ticket Type</label>
                    @if($userType == 'admin')
                        <select name="type" class="form-control" required>
                            <option value="">Select Ticket Type</option>
                            <option value="product" {{ (old('type') ?? $ticket->type) == 'product' ? 'selected' : '' }}>Product Ticket</option>
                            <option value="travel" {{ (old('type') ?? $ticket->type) == 'travel' ? 'selected' : '' }}>Travel Ticket</option>
                        </select>
                        @error('type')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    @else
                        <input type="text" class="form-control bg-light" value="{{ ucfirst($ticket->type) }} Ticket" readonly>
                        <small class="text-muted">This is a {{ $ticket->type }} ticket</small>
                        <input type="hidden" name="type" value="{{ $ticket->type }}">
                    @endif
                </div>

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
                               value="{{ $ticket->phone_number }}"
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
                        <input type="text" name="full_name" id="fullName" class="form-control" 
                               value="{{ old('full_name', $ticket->full_name) }}" required>
                        @error('full_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Auto-filled Email --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" 
                               value="{{ old('email', $ticket->email) }}" required>
                        @error('email')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Phone Number Field --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" id="phoneInput" class="form-control" 
                               value="{{ old('phone_number', $ticket->phone_number) }}">
                        @error('phone_number')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Ticket Title --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ticket Title</label>
                        <input type="text" name="ticket_title" class="form-control" 
                               value="{{ old('ticket_title', $ticket->ticket_title) }}" required>
                        @error('ticket_title')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ticket Category</label>
                        <select name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $key => $cat)
                                <option value="{{ $key }}" {{ (old('category') ?? $ticket->category) == $key ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
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
                                <option value="{{ $p }}" {{ (old('priority') ?? $ticket->priority) == $p ? 'selected' : '' }}>
                                    {{ ucfirst($p) }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">Describe Your Issue</label>
                        <textarea name="issue_description" rows="4" class="form-control" required>{{ old('issue_description', $ticket->issue_description) }}</textarea>
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
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}" 
                                        {{ (old('order_id') ?? $ticket->order_id) == $order->id ? 'selected' : '' }}
                                        data-transaction="{{ $order->transaction_id }}"
                                        data-date="{{ $order->created_at->format('Y-m-d') }}"
                                        data-payment="{{ $order->payment_method }}">
                                    {{ $order->transaction_id }} - {{ $order->created_at->format('d M Y') }}
                                </option>
                            @endforeach
                        </select>
                        @error('order_id')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Date --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Purchase Date</label>
                        <input type="text" id="purchaseDate" class="form-control" 
                               value="{{ old('purchase_date', $ticket->purchase_date) }}" readonly>
                    </div>

                    {{-- Payment --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment Method</label>
                        <input type="text" id="paymentMethod" class="form-control" 
                               value="{{ old('payment_method', $ticket->payment_method) }}" readonly>
                    </div>

                    {{-- Products --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">Products</label>
                        <div id="productList">
                            @if($selectedProducts && count($selectedProducts) > 0)
                                <div class="alert alert-info">Products are associated with this ticket</div>
                                <input type="hidden" name="product_ids[]" value="{{ implode(',', $selectedProducts) }}">
                            @else
                                <div class="alert alert-info">No products selected</div>
                            @endif
                        </div>
                    </div>

                    {{-- Attachment --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">Upload Attachment</label>
                        @if($ticket->attachment_path)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $ticket->attachment_path) }}" target="_blank" class="btn btn-sm btn-info">
                                    View Current Attachment
                                </a>
                            </div>
                        @endif
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
                            <option value="email" {{ (old('preferred_contact_method') ?? $ticket->preferred_contact_method) == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="phone" {{ (old('preferred_contact_method') ?? $ticket->preferred_contact_method) == 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="whatsapp" {{ (old('preferred_contact_method') ?? $ticket->preferred_contact_method) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        </select>
                        @error('preferred_contact_method')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Preferred Contact Time</label>
                        <input type="text" name="preferred_contact_time" class="form-control" 
                               value="{{ old('preferred_contact_time', $ticket->preferred_contact_time) }}">
                        @error('preferred_contact_time')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Ticket</button>
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
            return;
        }

        $.ajax({
            url: BASE_URL + "/admin/tickets/search-phone/" + phone,
            method: "GET",
            dataType: "json",
            success: function (data) {
                if (!data || !data.user) {
                    return;
                }

                let user = data.user;
                let orders = data.orders;

                // Auto-fill user details
                $("#fullName").val(user.name || user.full_name || "");
                $("#email").val(user.email || "");
                $("#phoneInput").val(user.phone || phone);

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
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    });

    // Load order details when order is selected
    $("#orderSelect").on("change", function() {
        let selectedOption = $(this).find(':selected');
        let orderId = $(this).val();
        
        if (orderId) {
            // Update purchase date and payment method from data attributes
            $("#purchaseDate").val(selectedOption.data('date') || '');
            $("#paymentMethod").val(selectedOption.data('payment') || 'N/A');
            
            // Load products for this order
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
                // Display products
                let productList = $("#productList");
                productList.html("");

                if (data.length > 0) {
                    let html = '<div class="list-group">';
                    data.forEach(item => {
                        html += `
                            <div class="list-group-item">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="product_ids[]" value="${item.id}" id="product_${item.id}">
                                    <label class="form-check-label" for="product_${item.id}">
                                        <strong>${item.name}</strong>
                                        <div class="small text-muted">
                                            Quantity: ${item.qty || 1} | 
                                            Price: ₹${item.price || 'N/A'}
                                        </div>
                                    </label>
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

    // Clear order details function
    function clearOrderDetails() {
        $("#purchaseDate").val("");
        $("#paymentMethod").val("");
        $("#productList").html('<div class="alert alert-info">No order selected</div>');
    }

    // Auto-fill order details if order is already selected
    @if($ticket->order_id)
        $(document).ready(function() {
            let orderSelect = $("#orderSelect");
            let selectedOption = orderSelect.find(':selected');
            
            if (selectedOption.val()) {
                $("#purchaseDate").val(selectedOption.data('date') || '{{ $ticket->purchase_date }}');
                $("#paymentMethod").val(selectedOption.data('payment') || '{{ $ticket->payment_method }}');
                
                // Load products for selected order
                loadOrderDetails('{{ $ticket->order_id }}');
            }
        });
    @endif
});
</script>
@endsection