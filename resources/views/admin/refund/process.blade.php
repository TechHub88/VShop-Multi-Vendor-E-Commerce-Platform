@extends('layouts.vertical', ['title' => 'Refund Process'])

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center py-3">
        <h4 class="fw-semibold fs-20">Refund Process Management</h4>
    </div>

    <div class="row">
        {{-- Create Process Form --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="fw-bold fs-15 mb-0">Add Refund Process</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.refund.process.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="step_name" class="form-label">Process Name</label>
                            <input type="text" name="step_name" id="step_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <select name="is_active" id="is_active" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Create Process</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Process Data Table --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="fw-bold fs-15 mb-0">Existing Refund Processes</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle" id="processTable">
                        <thead class="bg-light">
                            <tr>
                                <th>SL</th>
                                <th>Process</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($processes as $index => $process)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $process->step_name }}</td>
                                <td>{{ $process->description }}</td>
                                <td>{{ $process->is_active ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    <a href="{{ route('admin.refund.process.edit', $process->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('admin.refund.process.destroy', $process->id) }}" method="POST" class="d-inline">
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

{{-- Optional: Add datatable js --}}
@push('scripts')
<script>
    $(document).ready(function () {
        $('#processTable').DataTable();
    });
</script>
@endpush
@endsection
