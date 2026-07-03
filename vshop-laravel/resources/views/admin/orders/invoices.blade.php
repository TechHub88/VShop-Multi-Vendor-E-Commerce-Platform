@extends('layouts.vertical', ['title' => 'Invoices'])

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center py-3">
        <h4 class="fw-semibold">Invoices</h4>

        <div>
            <a href="#" class="btn btn-sm btn-outline-secondary">Export CSV</a>
            <a href="#" class="btn btn-sm btn-outline-primary">Export PDF</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <form class="row g-2">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Search Invoice # / Name">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option selected>Status Filter</option>
                        <option>Paid</option>
                        <option>Pending</option>
                        <option>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="80">Invoice #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#INV-001</td>
                        <td>John Doe</td>
                        <td>12 Feb 2025</td>
                        <td>₹2,450.00</td>
                        <td><span class="badge bg-success">Paid</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>

                    <tr>
                        <td>#INV-002</td>
                        <td>Amit Sharma</td>
                        <td>14 Feb 2025</td>
                        <td>₹1,200.00</td>
                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>

                    <tr>
                        <td>#INV-003</td>
                        <td>Priya Nair</td>
                        <td>16 Feb 2025</td>
                        <td>₹3,990.00</td>
                        <td><span class="badge bg-danger">Cancelled</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">More invoices will appear here…</td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
