
@extends('manager.layout')
@section('content')

<div class="adminPagesBodySec">
		<div class="container-fluid">		
		<div class="adminPagesBodySecinner">
        <!-- Dashboard Cards -->
        <div class="dashboardStaticArea">
			<div class="row rowBox">			
			 <div class="col-lg-3 col-md-3 col-md-6 col-sm-12 columnBox">
           <a href="{{ route('manager.customers') }}" class="text-decoration-none">
    <div class="dashboard_card">
        <div class="dashboard_card_body">
            <div class="d-flex align-items-center justify-content-between dashboardStaticinner">
                <div class="content">
                    <h4 class="card-title">Total Member</h4>
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $totalClients ?? 0 }}</p>
                </div>
                <div class="icon">
                    <i class="fa-regular fa-user"></i>
                </div>
            </div>                
        </div>
    </div>
</a>

			</div>
			 <div class="col-lg-3 col-md-3 col-md-6 col-sm-12 columnBox">
            <a href="{{ route('manager.orders') }}" class="text-decoration-none">
    <div class="dashboard_card">
        <div class="dashboard_card_body">
            <div class="d-flex align-items-center justify-content-between dashboardStaticinner">
                <div class="content">
                    <h4 class="card-title">Active Order</h4>
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $activeOrders ?? 0 }}</p>
                </div>
                <div class="icon">
                    <i class="fa-regular fa-cart-circle-check"></i>
                </div>
            </div>                
        </div>
    </div>
</a>

			</div>
		<div class="col-lg-3 col-md-3 col-sm-6 col-12 columnBox">
    <a href="{{ route('manager.orders') }}" class="text-decoration-none">
        <div class="dashboard_card">
            <div class="dashboard_card_body">
                <div class="d-flex align-items-center justify-content-between dashboardStaticinner">
                    <div class="content">
                        <h4 class="card-title">Order in Progress</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ $pendingRequests ?? 0 }}</p>
                    </div>
                    <div class="icon">
                        <i class="fa-regular fa-person-dolly"></i>
                    </div>
                </div>                
            </div>
        </div>
    </a>
</div>

			<div class="col-lg-3 col-md-3 col-md-6 col-sm-12 columnBox">
           <a href="{{ route('manager.orders.outfordelivery') }}" class="text-decoration-none">
    <div class="dashboard_card">
        <div class="dashboard_card_body">
            <div class="d-flex align-items-center justify-content-between dashboardStaticinner">
                <div class="content">
                    <h4 class="card-title">Deliver Order</h4>
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $revenue ?? 0 }}</p>
                </div>
                <div class="icon">
                    <i class="fa-regular fa-cart-flatbed-suitcase"></i>
                </div>
            </div>                
        </div>
    </div>
</a>

			</div>
			
			</div>
			
        </div>
		
		<div class="row rowBox">
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-12 columnBox">
		<div class="dashboard_card">
		<div class="d-flex justify-content-between dashboard_card_header">
		<div class="leftPart">
		<h4 class="mb-0 dashboard_card_title ">All Order List</h4>
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
  <table class="table align-middle mb-0 table-hover table-centered">
    <thead class="bg-light-subtle">
        <tr>
            <th>Order ID</th>
            <th>Created At</th>
            <th>Customer</th>
            <th>Priority</th>
            <th>Total</th>
            <th>Payment Status</th>
            <!--<th>Items</th>-->
            <th>Delivery Number</th>
            <th>Order Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>
                #{{ $order->order_no ?? 'FAD' . str_pad($order->id, 8, '0', STR_PAD_LEFT) }}
            </td>

            <td>{{ $order->created_at->format('M d, Y') }}</td>

            <td>
                <a href="{{ route('manager.order.details', $order->id) }}" class="link-primary fw-medium">
                    {{ $order->customer->name ?? 'N/A' }}
                </a>
            </td>

            <td>
                {{ ucfirst($order->priority ?? 'Normal') }}
            </td>

            <td>₹{{ number_format($order->total_amount, 2) }}</td>

            <td>
                @if($order->payment_status == 'paid')
                    <span class="badge bg-success text-light px-2 py-1 fs-13">Paid</span>
                @elseif($order->payment_status == 'refund')
                    <span class="badge bg-light text-dark px-2 py-1 fs-13">Refund</span>
                @else
                    <span class="badge bg-light text-dark px-2 py-1 fs-13">Unpaid</span>
                @endif
            </td>

          

            <td>{{ $order->delivery_no ?? '-' }}</td>

            <td>
                <span class="badge border 
                    @if($order->status == 'completed') border-success text-success
                    @elseif($order->status == 'packaging') border-warning text-warning
                    @elseif($order->status == 'canceled') border-danger text-danger
                    @else border-secondary text-secondary
                    @endif
                    px-2 py-1 fs-13">
                    {{ ucfirst($order->status) }}
                </span>
            </td>

            <td>
                <div class="d-flex gap-2">
                    <a href="{{ route('manager.order.details', $order->id) }}" class="btn btn-light btn-sm">
                        <i class="fa-light fa-eye"></i>
                    </a>
                    <a href="{{ url('manager.order.edit', $order->id) }}" class="btn btn-soft-primary btn-sm">
                        <i class="fa-light fa-pen-line"></i>
                    </a>
                    <a href="{{ url('manager.order.delete', $order->id) }}" class="btn btn-soft-danger btn-sm">
                        <i class="fa-light fa-trash-can"></i>
                    </a>
                </div>
            </td>
        </tr>
        @endforeach

        @if($orders->isEmpty())
        <tr>
            <td colspan="10" class="text-center">No orders found.</td>
        </tr>
        @endif

    </tbody>
</table>

</div>
<div class="mt-3">
    {{ $orders->links('pagination::bootstrap-5') }}
</div>

		</div>
		</div>
		</div>
		
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-12 columnBox">
		<div class="dashboard_card">
		<div class="dashboard_card_header"></div>
		<div class="dashboard_card_body"></div>
		</div>
		</div>
		</div>
		
		
		
		
		
	</div>
	
	
    </div>  
    </div>
	
@endsection	
