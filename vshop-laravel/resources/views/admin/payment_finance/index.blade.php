@extends('layouts.vertical', ['title' => 'Payment & Finance'])

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center py-3">
        <h4 class="fw-semibold">Payment & Finance</h4>
    </div>


    <!-- ===================== REFUND REQUESTS ===================== -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="fw-semibold mb-0">Refund Requests</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Refund Type</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($refund_requests as $key => $refund)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>#ORD-{{ $refund->id }}</td>
                            <td>{{ $refund->customer->name ?? 'N/A' }}</td>
                            <td>₹{{ number_format($refund->total_amount, 2) }}</td>
                            <td>{{ $refund->refund_type }}</td>

                            <td>
                                @if($refund->status == 'Refund')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($refund->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($refund->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">Unknown</span>
                                @endif
                            </td>

                            <td>
                                @if($refund->status == 'pending')
                                    <a href="{{ route('admin.refund.process', $refund->id) }}"
                                       class="btn btn-sm btn-success">Process</a>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                        Done
                                    </button>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>



</div>

@endsection
