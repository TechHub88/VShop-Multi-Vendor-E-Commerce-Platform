@extends('manager.layout')

@section('content')

<div class="container-fluid px-4 py-4">

    <div class="mb-4">
        <h2 class="fw-bold">Edit Ticket</h2>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <form action="{{ route('manager.tickets.update', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- FULL NAME -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control py-2" value="{{ old('full_name', $ticket->full_name) }}" required>
                </div>

                <!-- EMAIL -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control py-2" value="{{ old('email', $ticket->email) }}" required>
                </div>

                <!-- PHONE -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Phone Number (optional)</label>
                    <input type="text" name="phone_number" id="phone_number" class="form-control py-2" value="{{ old('phone_number', $ticket->phone_number) }}">
                </div>

                <!-- TICKET TITLE -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ticket Title</label>
                    <input type="text" name="ticket_title" class="form-control py-2" value="{{ old('ticket_title', $ticket->title) }}" required>
                </div>

                <!-- CATEGORY -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ticket Category</label>
                    <select name="category" id="category" class="form-select py-2" required>
                        @foreach ($categories as $key => $value)
                            <option value="{{ $key }}" {{ old('category', $ticket->category) == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- PRIORITY -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Priority</label>
                    <select name="priority" class="form-select py-2" required>
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority }}" {{ old('priority', $ticket->priority) == $priority ? 'selected' : '' }}>
                                {{ ucfirst($priority) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- ISSUE DESCRIPTION -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Describe Your Issue</label>
                    <textarea name="issue_description" class="form-control py-2" rows="4" required>{{ old('issue_description', $ticket->description) }}</textarea>
                </div>

                <!-- ATTACHMENT -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Upload Attachment (optional)</label>
                    <input type="file" name="attachment" class="form-control py-2">
                    @if($ticket->attachment)
                        <div class="mt-2">
                            <small>Current attachment: 
                                <a href="{{ Storage::url($ticket->attachment) }}" target="_blank">
                                    View Attachment
                                </a>
                            </small>
                        </div>
                    @endif
                </div>

                <!-- ORDER SECTION -->
                <div id="order_info_section" class="p-3 border rounded" style="background:#fafafa;">
                    <h5 class="fw-bold mb-3">Order Information</h5>

                    <!-- ORDER ID DROPDOWN -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Order (Transaction ID)</label>
                        <select name="order_id" id="order_id" class="form-select py-2">
                            <option value="">Select Order</option>
                            @foreach ($orders as $order)
                                <option value="{{ $order->id }}"
                                    data-date="{{ $order->created_at->format('Y-m-d') }}"
                                    data-payment="{{ $order->payment_method }}"
                                    {{ old('order_id', $ticket->order_id) == $order->id ? 'selected' : '' }}>
                                    {{ $order->transaction_id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- PRODUCT LIST -->
                    <div id="product_list_box" style="margin-top: 15px;">
                        <h6>Select Products</h6>
                        <div id="product_list">
                            <!-- Products will be loaded here via AJAX -->
                        </div>
                    </div>

                    <!-- PURCHASE DATE -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Date of Purchase</label>
                        <input type="date" name="purchase_date" id="purchase_date" class="form-control py-2" readonly value="{{ old('purchase_date', $ticket->purchase_date ? \Carbon\Carbon::parse($ticket->purchase_date)->format('Y-m-d') : '') }}">
                    </div>

                    <!-- PAYMENT METHOD -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Method</label>
                        <input type="text" name="payment_method" id="payment_method" class="form-control py-2" readonly value="{{ old('payment_method', $ticket->payment_method) }}">
                    </div>
                </div>

                <!-- CONTACT METHOD -->
                <div class="mb-3 mt-4">
                    <label class="form-label fw-semibold">Preferred Contact Method</label>
                    <select name="preferred_contact_method" class="form-select py-2" required>
                        <option value="email" {{ old('preferred_contact_method', $ticket->preferred_contact_method) == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="phone" {{ old('preferred_contact_method', $ticket->preferred_contact_method) == 'phone' ? 'selected' : '' }}>Phone</option>
                        <option value="whatsapp" {{ old('preferred_contact_method', $ticket->preferred_contact_method) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    </select>
                </div>

                <!-- CONTACT TIME -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Preferred Time to Contact (optional)</label>
                    <input type="text" name="preferred_contact_time" class="form-control py-2" value="{{ old('preferred_contact_time', $ticket->preferred_contact_time) }}">
                </div>

                <!-- Display already selected products for this ticket -->
                @if($ticket->products && $ticket->products->count() > 0)
                    <div class="mb-4 p-3 border rounded bg-light">
                        <h6 class="fw-bold">Currently Selected Products:</h6>
                        @foreach($ticket->products as $product)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="existing_product_ids[]" value="{{ $product->id }}" checked disabled>
                                <label class="form-check-label">
                                    {{ $product->name }} (Qty: {{ $product->pivot->quantity ?? 1 }})
                                </label>
                                <input type="hidden" name="product_ids[]" value="{{ $product->id }}">
                            </div>
                        @endforeach
                    </div>
                @endif

                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                    Update Ticket
                </button>
                <a href="{{ route('manager.tickets.index') }}" class="btn btn-secondary px-4 py-2 fw-semibold">
                    Cancel
                </a>

            </form>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

    const $category = $("#category");
    const $orderSelect = $("#order_id");
    const $orderSection = $("#order_info_section");
    const $productListBox = $("#product_list_box");
    const $productList = $("#product_list");

    // Show/Hide order section based on category
    function toggleOrderSection() {
        if ($category.val() === "order_issue") {
            $orderSection.show();
        } else {
            $orderSection.hide();
            $productList.empty();
            $productListBox.hide();
        }
    }

    $category.on("change", toggleOrderSection);
    toggleOrderSection();

    // When order is selected
    $orderSelect.on("change", function () {
        const orderId = $(this).val();
        const $selectedOption = $(this).find(":selected");

        $("#purchase_date").val($selectedOption.data("date") || "");
        $("#payment_method").val($selectedOption.data("payment") || "");

        if (!orderId) {
            $productList.empty();
            $productListBox.hide();
            return;
        }

        console.log("Fetching products for order ID:", orderId);
        $.ajax({
            url: "{{ url('/brand/orders/') }}/" + orderId + "/products",
            method: "GET",
            dataType: "json",
            success: function (data) {
                console.log("Products data:", data);
                
                if (!data.length) {
                    $productList.html("<p class='text-muted'>No products found for this order.</p>");
                    $productListBox.show();
                    return;
                }

                // Get already selected product IDs from the ticket
                const existingProductIds = [
                    @if($ticket->products)
                        @foreach($ticket->products as $product)
                            {{ $product->id }},
                        @endforeach
                    @endif
                ];

                const html = data.map(item => `
                    <div class="form-check my-1">
                        <input class="form-check-input" type="checkbox" 
                               name="product_ids[]" 
                               value="${item.id}"
                               ${existingProductIds.includes(item.id) ? 'checked' : ''}>
                        <label class="form-check-label">
                            ${item.name} (Qty: ${item.qty || 1})
                        </label>
                    </div>
                `).join("");

                $productList.html(html);
                $productListBox.show();
            },
            error: function (xhr, textStatus, errorThrown) {
                console.error("Error loading products:", textStatus, errorThrown, "Status:", xhr.status);
                $productList.html("<p class='text-danger'>Error loading products. Please try again.</p>");
                $productListBox.show();
            }
        });
    });

    // Trigger change event on page load if order is already selected
    const initialOrderId = $orderSelect.val();
    if (initialOrderId) {
        $orderSelect.trigger('change');
    }

    // Initial toggle based on category
    toggleOrderSection();
});
</script>

@endsection
