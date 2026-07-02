@extends('vtravel.main')

@section('title', 'Orders')

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
                    <div class="date-short-sec">
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

                    @php
                        $statusClass = match($order->status) {
                            'delivered' => 'greenColor',
                            'dispatched' => 'blueColor',
                            'pending' => 'orangeColor',
                            'cancelled' => 'redColor',
                            'return' => 'redColor',
                            default => ''
                        };
                    @endphp

                    <p class="order-status">
                        Order Status :
                        <span class="{{ $statusClass }}">
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
                    <a href="{{ route('vtravel.order.details', $order->id) }}" class="btn-view mt-2">
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
                   {{-- PAGINATION --}}
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
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
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
            // For demo, just show an alert
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
            ' <i class="fa-solid fa-filter" style="font-size: 15px; color: #5F6368;"></i>');
        
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

    // Initialize DataTable if needed
    @if(config('app.env') === 'local')
    // Example of DataTable initialization for order list
    // Uncomment if you want to use DataTables
    /*
    $('.order-list-sec table').DataTable({
        "pageLength": 10,
        "ordering": true,
        "order": [[0, 'desc']],
        "language": {
            "search": "Search orders:",
            "lengthMenu": "Show _MENU_ orders per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ orders"
        }
    });
    */
    @endif

    // Order item click - redirect to order details
    $('.order-list-wrapper h3 a').click(function(e) {
        e.preventDefault();
        const orderId = $(this).text().replace('Order no : ', '').trim();
        // You can redirect to order details page
        window.location.href = $(this).attr('href');
    });

    // Today/Last 7 days/Last 30 days quick filters
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

    // Add quick filter buttons (optional)
    function addQuickFilters() {
        const filterHtml = `
            <div class="quick-filters mb-3">
                <button class="btn btn-outline-secondary btn-sm me-2" data-filter="today">Today</button>
                <button class="btn btn-outline-secondary btn-sm me-2" data-filter="last7days">Last 7 Days</button>
                <button class="btn btn-outline-secondary btn-sm" data-filter="last30days">Last 30 Days</button>
            </div>
        `;
        $('.date-short-sec').before(filterHtml);
        
        $('.quick-filters button').click(function() {
            const filter = $(this).data('filter');
            if(quickFilters[filter]) {
                quickFilters[filter]();
                $('#filterBtn').click();
            }
        });
    }
    
    // Uncomment to add quick filters
    // addQuickFilters();
});
</script>
@endpush