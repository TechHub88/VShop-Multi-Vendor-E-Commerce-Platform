@extends('layouts.vertical', ['title' => 'Category Approvals'])

@section('content')
<style>
    .btn-approve, .btn-reject {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0;
        color: #fff;
    }
    .btn-reject {
        background: linear-gradient(180deg, #dc3545 0%, #a71d2a 100%);
    }
</style>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category Name</th>
                            <th>Type</th>
                            <th>Seller</th>
                            <th>Status</th>
                            <th width="220">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $cat)
                        <tr id="row-{{ $cat->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $cat->name ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $cat->type === 'travel' ? 'bg-info' : 'bg-warning' }}">
                                    {{ ucfirst($cat->type ?? '-') }}
                                </span>
                            </td>
                            <td>
                                @if($cat->seller)
                                    {{ $cat->seller->name ?? $cat->seller->email ?? 'N/A' }}
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td class="status-col">
                                @php $status = $cat->approval_status; @endphp
                                @if($status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($status) }}</span>
                                @endif
                            </td>
                            <td class="action-col">
                                @if($status == 'pending')
                                    <button class="btn btn-sm btn-approve approveBtn"
                                            data-id="{{ $cat->id }}"
                                            data-type="category"
                                            data-status="approved">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-sm btn-reject rejectBtn"
                                            data-id="{{ $cat->id }}"
                                            data-type="category"
                                            data-status="rejected">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                @else
                                    <span class="text-muted">Action Completed</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No categories found for this panel.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).on('click', '.approveBtn, .rejectBtn', function () {
    let btn = $(this);
    let id = btn.data('id');
    let type = btn.data('type');
    let status = btn.data('status');

    $.ajax({
        url: "{{ route('admin.approval.update') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: id,
            type: type,
            status: status
        },
        success: function (response) {
            if (response.success) {
                let row = $('#row-' + id);
                let newBadge = status === 'approved'
                    ? '<span class="badge bg-success">Approved</span>'
                    : '<span class="badge bg-danger">Rejected</span>';
                row.find('.status-col').html(newBadge);
                row.find('.action-col').html('<span class="text-muted">Action Completed</span>');
            } else {
                alert('Error: ' + (response.message || 'Something went wrong'));
            }
        },
        error: function () {
            alert('Server error. Please try again.');
        }
    });
});
</script>
@endsection