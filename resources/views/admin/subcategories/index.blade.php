@extends('layouts.vertical', ['title' => 'Sub Categories'])

@section('content')
<div class="container-fluid">

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Sub Category List</h5>
            <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">+ Add Sub Category</a>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Sub Category</th>
                        <th>Category</th>
                        <th>Slug</th>
                        <th width="130">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($subcategories as $key => $sub)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $sub->name }}</td>
                        <td>{{ $sub->category->name ?? '-' }}</td>
                        <td>{{ $sub->slug }}</td>

                        <td>
                            <a href="{{ route('admin.subcategories.edit', $sub->id) }}" class="btn btn-sm btn-info">Edit</a>

                            <form action="{{ route('admin.subcategories.destroy', $sub->id) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Delete this subcategory?')"
                                        class="btn btn-sm btn-danger">
                                    Delete
                                </button>
                            </form>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $subcategories->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>
@endsection
