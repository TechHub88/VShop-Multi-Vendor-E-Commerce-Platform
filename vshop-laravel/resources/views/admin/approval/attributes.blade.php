@extends('layouts.vertical', ['title' => 'Attribute Approvals'])

@section('content')

@php
    $title = 'Attribute Approvals';
@endphp

<div class="container-fluid">

    <div class="pt-3 pb-1 d-flex align-items-center justify-content-between">
        <h4 class="fs-18 fw-semibold">{{ $title }}</h4>
    </div>

    <div class="card">
        <div class="card-body" style="padding:15px 15px;">

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Attribute Name</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th width="220">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($items as $attribute)
                        <tr id="row-{{ $attribute->id }}">
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $attribute->name ?? '-' }}</td>

                            <td>{{ $attribute->category->name ?? '-' }}</td>

                            {{-- STRING BASED STATUS --}}
                            <td>
                                @if($attribute->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($attribute->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($attribute->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($attribute->status) }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $attribute->created_at ? $attribute->created_at->format('d M, Y') : '-' }}
                            </td>

                            {{-- ACTION COLUMN --}}
                            <td>
                                @if($attribute->status == 'pending')
                                    <button class="btn btn-sm btn-success approveBtn"
                                            data-id="{{ $attribute->id }}"
                                            data-type="attribute"
                                            data-status="approved">
                                        <i class="fas fa-check"></i> Approve
                                    </button>

                                    <button class="btn btn-sm btn-danger rejectBtn"
                                            data-id="{{ $attribute->id }}"
                                            data-type="attribute"
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
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-sliders-h fa-2x mb-2"></i>
                                <p>No pending attributes found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- AJAX APPROVE / REJECT --}}
<script>
$(document).on('click', '.approveBtn, .rejectBtn', function () {

    let button = $(this);
    let id     = button.data('id');
    let type   = button.data('type');
    let status = button.data('status');

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

            if(response.success){

                let row = $('#row-' + id);

                // 🔥 Update Status Badge (4th column in attribute table)
                if(status === 'approved'){
                    row.find('td:nth-child(4)').html(
                        '<span class="badge bg-success">Approved</span>'
                    );
                } else {
                    row.find('td:nth-child(4)').html(
                        '<span class="badge bg-danger">Rejected</span>'
                    );
                }

                // 🔥 Remove approve/reject buttons
                row.find('.approveBtn, .rejectBtn').remove();

                // 🔥 Replace action column text
                row.find('td:last').html('<span class="text-muted">Action Completed</span>');
            }
        }
    });

});
</script>
@endsection
