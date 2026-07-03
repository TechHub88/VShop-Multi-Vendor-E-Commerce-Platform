@extends('layouts.vertical', ['title' => 'Transaction History'])

@section('content')
<div class="container-fluid">

    <!-- ===================== TRANSACTION HISTORY ===================== -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="fw-semibold mb-0">Transaction History</h5>
        </div>

        <div class="card-body">

            {{-- Filter Form --}}
            <form action="{{ route('admin.transactions.filter') }}" method="GET" class="row g-3 mb-3">

                <div class="col-md-3">
                    <label class="form-label">Payment Method</label>
                    <select name="method" class="form-select">
                        <option value="">All</option>
                        @foreach($payment_methods as $pm)
                            <option value="{{ $pm }}">{{ strtoupper($pm) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to" class="form-control">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>

            </form>

            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($transactions as $key => $txn)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>#ORD-{{ $txn->id }}</td>
                            <td>{{ strtoupper($txn->payment_method) }}</td>
                            <td>₹{{ number_format($txn->total_amount, 2) }}</td>

                            <td>
                                @if($txn->payment_status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($txn->payment_status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>

                            <td>{{ $txn->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection
