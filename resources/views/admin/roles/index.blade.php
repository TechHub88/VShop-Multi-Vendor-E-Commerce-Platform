@extends('layouts.vertical', ['title' => 'Roles & Permissions'])

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center py-3">
        <h4>Roles & Permissions</h4>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">Add role permission</a>
    </div>

    <div class="card">
        <div class="card-body p-0">

            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Created</th>
                        <th width="120" class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->created_at->format('h:i a, d M, y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Delete this role?')" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">No roles found</td></tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>

@endsection
