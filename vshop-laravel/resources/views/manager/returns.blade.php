<!-- resources/views/manager/orders/refund.blade.php -->
<!-- resources/views/manager/orders/cancelled.blade.php -->


<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <style>
        .dashboard-container { display: flex; min-height: 100vh; background: #f5f6fa; }
        .sidebar { width: 220px; background: #2f3640; color: #fff; padding: 20px; }
        .sidebar h2 { font-size: 20px; margin-bottom: 30px; }
        .sidebar a { display: block; color: #fff; text-decoration: none; padding: 10px 0; transition: 0.3s; }
        .sidebar a:hover { color: #00a8ff; }
        .main-content { flex: 1; padding: 20px; }
        .top-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .top-nav h1 { font-size: 24px; }
        .table th, .table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .table th { background: #2f3640; color: #fff; }
        .product-image { width: 50px; height: 50px; object-fit: cover; }
        .btn-space { margin-right: 10px; }
    </style>
</head>
<body>
<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar" style="width: 220px; background: #2f3640; color: white; padding: 20px;">
        <h2>Manager Panel</h2>
        <a href="{{ route('manager.dashboard') }}">Dashboard</a>
        <a href="{{ route('manager.orders') }}">All Orders</a>
        <a href="{{ route('manager.orders.completed') }}">Completed Orders</a>
        <a href="{{ route('manager.orders.cancelled') }}">Cancelled Orders</a>
        <a href="{{ route('manager.orders.declined') }}">Declined Orders</a>
        <a href="{{ route('manager.orders.failed') }}">Failed Orders</a>
        <a href="{{ route('manager.orders.outfordelivery') }}">Out for Delivery</a>
        <a href="{{ route('manager.orders.processing') }}">Processing Orders</a>
        <a href="{{ route('manager.orders.refund') }}" class="active">Refund Orders</a>
        <a href="{{ route('manager.customers') }}">Customers</a>

        <!-- Wallet Balance -->
        <div style="margin-top: 20px; padding: 10px; background: #00a8ff; border-radius: 5px; color: #fff;">
            <strong>Wallet Balance:</strong> ₹{{ number_format($walletBalance, 2) }}
        </div>

        <!-- Logout Form -->
        <form action="{{ route('manager.logout') }}" method="POST" style="margin-top:20px;">
            @csrf
            <button type="submit" style="background:none; border:none; color:#fff; padding:10px 0; width:100%; text-align:left; cursor:pointer;">Logout</button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="main-content" >
        <div class="top-nav" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4 class="fw-semibold">Returns / Refund Requests</h4>
            <input type="text" class="form-control w-auto" placeholder="Search Order / Customer">
        </div>

        <!-- Refund Orders Table -->
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Refund Amount</th>
                            <th>Date</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($refundRequests as $request)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $request->order_id }}</td>
                            <td>{{ $request->customer->name }}</td>
                            <td>{{ $request->product->name }}</td>
                            <td>{{ $request->reason }}</td>
                            <td>
                                @if($request->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge bg-success">Refunded</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>₹{{ number_format($request->refund_amount, 2) }}</td>
                            <td>{{ $request->created_at->format('d M Y') }}</td>
                            <td>
                                @if($request->status == 'pending')
                                    <a href="{{ route('manager.refund.approve', $request->id) }}" class="btn btn-success btn-sm">Approve</a>
                                    <a href="{{ route('manager.refund.reject', $request->id) }}" class="btn btn-danger btn-sm">Reject</a>
                                @else
                                    <a href="{{ route('manager.refund.view', $request->id) }}" class="btn btn-outline-secondary btn-sm">View</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        @if($refundRequests->isEmpty())
                        <tr>
                            <td colspan="9" style="text-align:center;">No refund requests found.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Load JS in correct order -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Example JavaScript for any dynamic interactions, such as status change actions
</script>

<!-- Styles (Integrated in the same file for simplicity) -->
<style>
/* Basic dashboard styling */
.dashboard-container {
    display: flex;
    min-height: 100vh;
    background: #f5f6fa;
}

/* Sidebar */
.sidebar {
    width: 220px;
    background: #2f3640;
    color: #fff;
    padding: 20px;
}

.sidebar h2 {
    font-size: 20px;
    margin-bottom: 30px;
}

.sidebar a {
    display: block;
    color: #fff;
    text-decoration: none;
    padding: 10px 0;
    transition: 0.3s;
}

.sidebar a:hover {
    color: #00a8ff;
}

/* Main content */
.main-content {
    flex: 1;
    padding: 20px;
}

/* Table styling */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #fff;
}

.table th, .table td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

.table th {
    background: #2f3640;
    color: #fff;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-container {
        flex-direction: column;
    }
    .sidebar {
        width: 100%;
    }
}
</style>
</body>
</html>
