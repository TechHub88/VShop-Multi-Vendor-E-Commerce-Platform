@extends('layouts.vertical', ['title' => 'Website Templates'])

@section('content')

<style>
    .table th {
        background: #004392 !important;
        color: #fff !important;
    }
    .btn-primary,
    .btn-warning,
    .btn-danger,
    .btn-info {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-light border {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
    .btn-secondary,
    .btn-outline-secondary {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .nav-tabs .nav-link.active {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
</style>

<div class="container-fluid">

    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">Website Templates</h4>

                <a href="{{ route('admin.website.templates.create') }}"
                   class="btn btn-primary">
                    <i class="mdi mdi-plus"></i> Add Template
                </a>
            </div>
        </div>
    </div>

    <!-- Template Table -->
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Preview</th>
                                    <th>Name</th>
                                    <th>Application</th>
                                    <th>Status</th>
                                    <th width="160">Action</th>
                                </tr>
                            </thead>

                            <tbody>

                            @forelse($templates as $key => $template)

                                <tr>
                                    <td>{{ $key + 1 }}</td>

                                    <!-- Preview Image -->
                                   <td>
@if($template->preview_image)
    <img src="{{ asset($template->preview_image) }}"
         width="90"
         style="border-radius:6px;">
@else
    -
@endif
</td>
                                    <td>
                                        <strong>{{ $template->name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $template->description }}
                                        </small>
                                    </td>

                                    <td>
                                        <span class="badge bg-info">
                                            {{ ucfirst($template->application_type) }}
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td>
                                        @if($template->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td>

                                        <a href="{{ route('admin.website.templates.edit',$template->id) }}"
                                           class="btn btn-sm btn-warning">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>

                                        <form action="{{ route('admin.website.templates.destroy',$template->id) }}"
                                              method="POST"
                                              style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete Template?')">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>

                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No Templates Found
                                    </td>
                                </tr>
                            @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection