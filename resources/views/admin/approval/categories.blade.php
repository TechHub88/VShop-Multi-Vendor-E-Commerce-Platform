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
                            <td>
                                <div class="fw-bold text-dark fs-14">{{ $cat->name ?? '-' }}</div>
                                <span class="badge bg-primary-subtle text-primary mt-1 px-2 py-0.5" style="font-size: 11px; font-weight: 600;">
                                    <i class="fa-solid fa-users me-1"></i> {{ $cat->seller_count ?? 1 }} {{ Str::plural('Seller', $cat->seller_count ?? 1) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $cat->type === 'travel' ? 'bg-info' : 'bg-warning' }}">
                                    {{ ucfirst($cat->type ?? '-') }}
                                </span>
                            </td>
                            <td>
                                @if(isset($cat->sellers) && count($cat->sellers) > 0)
                                    @foreach($cat->sellers as $s)
                                        <div class="{{ !$loop->last ? 'mb-2 pb-2 border-bottom border-light' : '' }}">
                                            <div class="fw-semibold text-dark fs-13">
                                                {{ $s->name ?? $s->email ?? 'N/A' }}
                                            </div>
                                            @php
                                                $approved = is_string($s->approved_platforms ?? null) ? json_decode($s->approved_platforms, true) : ($s->approved_platforms ?? []);
                                                if(!is_array($approved)) $approved = [];
                                            @endphp
                                            <div class="mt-1 d-flex gap-1 flex-wrap">
                                                @if(count($approved) > 0)
                                                    @foreach($approved as $p)
                                                        @if ($p == 'product')
                                                            <span class="badge bg-info-subtle text-info px-1 py-0.5" style="font-size: 9px; font-weight: 600;">V-Shop</span>
                                                        @elseif ($p == 'travel')
                                                            <span class="badge bg-success-subtle text-success px-1 py-0.5" style="font-size: 9px; font-weight: 600;">V-Travel</span>
                                                        @elseif (($s->app_v_campus ?? false) || $p == 'vcampus')
                                                            <span class="badge bg-warning-subtle text-warning px-1 py-0.5" style="font-size: 9px; font-weight: 600;">V-Campus</span>
                                                        @else
                                                            <span class="badge bg-secondary-subtle text-secondary px-1 py-0.5" style="font-size: 9px; font-weight: 600;">{{ ucfirst($p) }}</span>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary px-1 py-0.5" style="font-size: 9px; font-weight: 600;">No Platform</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @elseif($cat->seller)
                                    <div>
                                        {{ $cat->seller->name ?? $cat->seller->email ?? 'N/A' }}
                                    </div>
                                    @php
                                        $approved = is_string($cat->seller->approved_platforms ?? null) ? json_decode($cat->seller->approved_platforms, true) : ($cat->seller->approved_platforms ?? []);
                                        if(!is_array($approved)) $approved = [];
                                    @endphp
                                    <div class="mt-1 d-flex gap-1 flex-wrap">
                                        @if(count($approved) > 0)
                                            @foreach($approved as $p)
                                                @if ($p == 'product')
                                                    <span class="badge bg-info-subtle text-info px-1 py-0.5" style="font-size: 9px; font-weight: 600;">V-Shop</span>
                                                @elseif ($p == 'travel')
                                                    <span class="badge bg-success-subtle text-success px-1 py-0.5" style="font-size: 9px; font-weight: 600;">V-Travel</span>
                                                @elseif (($cat->seller->app_v_campus ?? false) || $p == 'vcampus')
                                                    <span class="badge bg-warning-subtle text-warning px-1 py-0.5" style="font-size: 9px; font-weight: 600;">V-Campus</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary px-1 py-0.5" style="font-size: 9px; font-weight: 600;">{{ ucfirst($p) }}</span>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary px-1 py-0.5" style="font-size: 9px; font-weight: 600;">No Platform</span>
                                        @endif
                                    </div>
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