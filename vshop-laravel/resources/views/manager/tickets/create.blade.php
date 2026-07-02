@extends('manager.layout')

@section('content')

<div class="container-fluid px-4 py-4">

    <div class="mb-4">
        <h2 class="fw-bold">Create a New Ticket</h2>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <form action="{{ route('manager.tickets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- FULL NAME -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control py-2" required>
                </div>

                <!-- EMAIL -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control py-2" required>
                </div>

                <!-- PHONE -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Phone Number (optional)</label>
                    <input type="text" name="phone_number" id="phone_number" class="form-control py-2">
                </div>

                <!-- TICKET TITLE -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ticket Title</label>
                    <input type="text" name="ticket_title" class="form-control py-2" required>
                </div>

                <!-- CATEGORY -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ticket Category</label>
                    <select name="category" id="category" class="form-select py-2" required>
                        @foreach ($categories as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- PRIORITY -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Priority</label>
                    <select name="priority" class="form-select py-2" required>
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority }}">{{ ucfirst($priority) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- ISSUE DESCRIPTION -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Describe Your Issue</label>
                    <textarea name="issue_description" class="form-control py-2" rows="4" required></textarea>
                </div>

                <!-- ATTACHMENT -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Upload Attachment (optional)</label>
                    <input type="file" name="attachment" class="form-control py-2">
                </div>

                <!-- ORDER SECTION -->
                <div id="order_info_section" class="p-3 border rounded" style="display:none; background:#fafafa;">
                    <h5 class="fw-bold mb-3">Order Information</h5>

                    <!-- ORDER ID DROPDOWN -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Order (Transaction ID)</label>
                        <select name="order_id" id="order_id" class="form-select py-2">
                            <option value="">Select Order</option>
                            @foreach ($orders as $order)
                                <option value="{{ $order->id }}"
                                    data-date="{{ $order->created_at->format('Y-m-d') }}"
                                    data-payment="{{ $order->payment_method }}">
                                    {{ $order->transaction_id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- PRODUCT LIST -->
                    <div id="product_list_box" style="display:none; margin-top: 15px;">
                        <h6>Select Products</h6>
                        <div id="product_list"></div>
                    </div>

                    <!-- PURCHASE DATE -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Date of Purchase</label>
                        <input type="date" name="purchase_date" id="purchase_date" class="form-control py-2" readonly>
                    </div>

                    <!-- PAYMENT METHOD -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Method</label>
                        <input type="text" name="payment_method" id="payment_method" class="form-control py-2" readonly>
                    </div>
                </div>

                <!-- CONTACT METHOD -->
                <div class="mb-3 mt-4">
                    <label class="form-label fw-semibold">Preferred Contact Method</label>
                    <select name="preferred_contact_method" class="form-select py-2" required>
                        <option value="email">Email</option>
                        <option value="phone">Phone</option>
                        <option value="whatsapp">WhatsApp</option>
                    </select>
                </div>

                <!-- CONTACT TIME -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Preferred Time to Contact (optional)</label>
                    <input type="text" name="preferred_contact_time" class="form-control py-2">
                </div>

                <button class="btn btn-primary px-4 py-2 fw-semibold">
                    Create Ticket
                </button>

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
    dataType: "json", // Use json instead of text
    success: function (data) {
        console.log("Products data:", data);
        
        if (!data.length) {
            $productList.html("<p class='text-danger'>No products found.</p>");
            $productListBox.show();
            return;
        }

        const html = data.map(item => `
            <div class="form-check my-1">
                <input class="form-check-input" type="checkbox" name="product_ids[]" value="${item.id}">
                <label class="form-check-label">${item.name} (Qty: ${item.qty})</label>
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
});
</script>


@endsection
