@extends('layouts.vertical', ['title' => 'Advertisement Management'])

@section('content')

<style>
    .table th {
        background: #004392 !important;
        color: #fff !important;
        border-color: #fff;
    }
</style>

<div class="container-fluid pt-2">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Advertisement Management</h4>

        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Create
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Banner</th>
                            <th>Type</th>
                            <th>Platform</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($banners as $key => $banner)
                            <tr>
                                <td>{{ $key + 1 }}</td>

                                <td>{{ $banner->title }}</td>

                                <td>{{ $banner->category->name ?? 'N/A' }}</td>

                                <td>
                                    <img src="{{ asset($banner->banner) }}"
                                         style="height:70px;border-radius:6px;">
                                </td>

                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ ucfirst($banner->type) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ strtoupper($banner->platform) }}
                                    </span>
                                </td>

                                <td>{{ $banner->start_date }}</td>
                                <td>{{ $banner->end_date }}</td>

                                <td>
                                    @if($banner->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('admin.banners.edit', $banner->id) }}"
                                       class="btn btn-sm btn-warning">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <a href="{{ route('admin.banners.delete', $banner->id) }}"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this banner?')">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    No banners found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

@endsection