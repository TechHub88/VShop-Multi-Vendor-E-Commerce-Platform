@extends('layouts.vertical', ['title' => 'Refund Reasons'])

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center py-3">
        <h4 class="fw-semibold fs-20">Refund Reasons Management</h4>
    </div>

    <div class="row">
        {{-- Create Reason Form --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="fw-bold fs-15 mb-0">Add Refund Reason</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.refund.reasons.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Reason Title</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <select name="is_active" id="is_active" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Add Reason</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Reasons Data Table --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="fw-bold fs-15 mb-0">Existing Refund Reasons</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle" id="reasonsTable">
                        <thead class="bg-light">
                            <tr>
                                <th>SL</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reasons as $index => $reason)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $reason->title }}</td>
                                <td>{{ $reason->is_active ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    <a href="{{ route('admin.refund.reasons.edit', $reason->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('admin.refund.reasons.destroy', $reason->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Optional: DataTable JS --}}
@push('scripts')
<script>
    $(document).ready(function () {
        $('#reasonsTable').DataTable();
    });
</script>
@endpush
@endsection
