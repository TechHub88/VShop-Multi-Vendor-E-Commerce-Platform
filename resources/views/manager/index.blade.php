
@extends('manager.layout')
@section('content')
<div class="adminPagesBodySec">
<div class="container-fluid">		
<div class="adminPagesBodySecinner">
		<div class="row rowBox">		
		<div class="col-lg-12 col-md-12 col-sm-12 col-12 columnBox">
		<div class="dashboard_card">
		<div class="d-flex justify-content-between dashboard_card_header">
		<div class="leftPart">
		<h4 class="mb-0 dashboard_card_title ">All Orders</h4>
		</div>
		<div class="rightPart">
		<div class="dropdown">
		  <button type="button" class="btn btn-defalt dropdown-toggle" data-bs-toggle="dropdown">
			This Month
		  </button>
		  <ul class="dropdown-menu">
			<li><a class="dropdown-item" href="#">Download</a></li>
			<li><a class="dropdown-item" href="#">Export</a></li>
			<li><a class="dropdown-item" href="#">Import</a></li>
		  </ul>
		</div>
		</div>
		</div>
		<div class="dashboard_card_body p-0">
		<div class="table-responsive">
		<table class="table" style="width: 100%; border-collapse: collapse; background: #fff;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order No.</th>
                            <th>Customer Name</th>
                            <th>Phone</th>
                            <th>Total Amount</th>
                            <th>Date</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $index => $order)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $order->order_no ?? 'FAD' . str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</td>
                                <td>
								<a href="{{ route('manager.order.details', $order->id) }}" class="link-primary fw-medium">
								{{ $order->customer->name ?? 'N/A' }}
								</a>
								</td>
                                <td>{{ $order->customer->phone ?? 'N/A' }}</td>
                                <td>₹{{ number_format($order->total_amount, 2) }}</td>
                                <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    @if($order->payment_status == 'paid')
                                        <span class="badge bg-success text-light  px-2 py-1 fs-13">Paid</span>
                                    @else
                                        <span class="badge bg-danger text-light  px-2 py-1 fs-13">Unpaid</span>
                                    @endif
                                </td>
                                <td><span class="badge border border-warning text-warning  px-2 py-1 fs-13">{{ ucfirst($order->status) }}</span></td>
                                <td>
                                    <!-- View Button to order details -->
                                    <a href="{{ route('manager.order.details', $order->id) }}" class="btn btn-light btn-sm"><i class="fa-light fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach

                        @if($orders->isEmpty())
                            <tr>
                                <td colspan="9" style="text-align:center;">No orders found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
		</div>
		</div>
		
		
		</div>
		</div>
		</div>

</div>
</div>
</div>

<script>
$(document).ready(function() {
    $('#ordersTable').DataTable({
        "order": [[1, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": 6 }
        ]
    });
});
</script>

@endsection	




