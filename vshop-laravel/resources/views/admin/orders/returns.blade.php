@extends('layouts.vertical', ['title' => 'Returns & Refunds'])

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center py-3">
        <h4 class="fw-semibold">Returns / Refund Requests</h4>
        <input type="text" class="form-control w-auto" placeholder="Search Order / Customer">
    </div>

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
                    {{-- Dummy Data --}}
                    <tr>
                        <td>1</td>
                        <td>#ORD-2025</td>
                        <td>Rahul Sharma</td>
                        <td>Blue Denim Jacket</td>
                        <td>Size Issue</td>
                        <td><span class="badge bg-warning">Pending</span></td>
                        <td>₹799.00</td>
                        <td>02 Nov 2025</td>
                        <td>
                            <a href="#" class="btn btn-success btn-sm">Approve</a>
                            <a href="#" class="btn btn-danger btn-sm">Reject</a>
                        </td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td>#ORD-1987</td>
                        <td>Priya Verma</td>
                        <td>Sports Shoes</td>
                        <td>Defective Product</td>
                        <td><span class="badge bg-success">Refunded</span></td>
                        <td>₹1,299.00</td>
                        <td>30 Oct 2025</td>
                        <td>
                            <a href="#" class="btn btn-outline-secondary btn-sm">View</a>
                        </td>
                    </tr>

                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection
