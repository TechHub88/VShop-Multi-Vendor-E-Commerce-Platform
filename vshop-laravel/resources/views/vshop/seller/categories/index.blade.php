@extends('vshop.seller.main', ['title' => 'My Categories'])

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>My Categories</h4>

        <a href="{{ route('seller.categories.create') }}" class="btn btn-primary">
            + Add Category
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($categories as $key => $cat)
                            <tr>
                                <td>{{ $key + 1 }}</td>

                                <td>
                                    <strong>{{ $cat->name }}</strong>
                                </td>

                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ ucfirst($cat->type) }}
                                    </span>
                                </td>

                                <td>
                                    @if($cat->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $cat->created_at->format('d M Y') }}
                                </td>

                                <td>
                                    <div class="d-flex gap-1">

                                        {{-- Edit --}}
                                        <a href="{{ route('seller.categories.edit', $cat->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('seller.categories.destroy', $cat->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete this category?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-outline-danger">
                                                Delete
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    No categories found.
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
