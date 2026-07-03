@extends('layouts.vertical', ['title' => 'Tax Configuration'])

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center py-3">
        <h4 class="fw-semibold">Tax Configuration (GST / Region Wise Tax)</h4>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTaxModal">
            + Add Tax Rule
        </button>
    </div>

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Region / State</th>
                        <th>GST %</th>
                        <th>CGST %</th>
                        <th>SGST %</th>
                        <th>IGST %</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($taxes as $index => $tax)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $tax->state->name ?? 'Unknown State' }}</td>
                            <td>{{ $tax->gst }}%</td>
                            <td>{{ $tax->cgst ?? '-' }}%</td>
                            <td>{{ $tax->sgst ?? '-' }}%</td>
                            <td>{{ $tax->igst ?? '-' }}%</td>

                            <td>
                                <button class="btn btn-sm btn-outline-secondary">Edit</button>
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>


    <!-- Add Tax Modal -->
    <div class="modal fade" id="addTaxModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('admin.taxes.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Tax Rule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Region / State</label>
                        <select name="state_id" class="form-control" required>
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">GST %</label>
                        <input type="number" name="gst" class="form-control" placeholder="18" required>
                    </div>

                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label">CGST %</label>
                            <input type="number" name="cgst" class="form-control" placeholder="9">
                        </div>
                        <div class="col mb-3">
                            <label class="form-label">SGST %</label>
                            <input type="number" name="sgst" class="form-control" placeholder="9">
                        </div>
                        <div class="col mb-3">
                            <label class="form-label">IGST %</label>
                            <input type="number" name="igst" class="form-control" placeholder="18">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
