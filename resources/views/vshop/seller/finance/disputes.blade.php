@extends('vshop.seller.main')

@section('content')

<div class="container-fluid">

    <h4 class="mb-4">Dispute Management</h4>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ================= RAISE DISPUTE ================= --}}
    <div class="card mb-4 p-3">
        <h6>Raise Dispute</h6>

        <form action="{{ route('seller.finance.raise.dispute') }}" method="POST">
            @csrf

            <div class="row">

                <div class="col-md-4">
                    <input type="text" name="order_id" class="form-control" placeholder="Order ID" required>
                </div>

                <div class="col-md-6">
                    <input type="text" name="message" class="form-control" placeholder="Enter issue" required>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-danger w-100">Submit</button>
                </div>

            </div>
        </form>
    </div>

    {{-- ================= DISPUTE LIST ================= --}}
    <div class="card p-3">
        <h6>All Disputes</h6>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order ID</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Admin Reply</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                @forelse($disputes as $d)
                <tr>
                    <td>{{ $d->id }}</td>
                    <td>{{ $d->order_id }}</td>
                    <td>{{ $d->message }}</td>

                    <td>
                        @if($d->status == 'open')
                            <span class="badge bg-danger">Open</span>
                        @elseif($d->status == 'in_progress')
                            <span class="badge bg-warning">In Progress</span>
                        @elseif($d->status == 'resolved')
                            <span class="badge bg-success">Resolved</span>
                        @else
                            <span class="badge bg-secondary">Rejected</span>
                        @endif
                    </td>

                    <td>{{ $d->admin_reply ?? '-' }}</td>
                    <td>{{ $d->created_at }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No disputes found</td>
                </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</div>

@endsection