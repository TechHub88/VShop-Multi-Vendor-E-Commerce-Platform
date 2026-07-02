@extends('layouts.vertical', ['title' => 'Footer Management'])

@section('content')
<h3>Footer List</h3>
<a href="{{ route('admin.footers.create') }}" class="btn btn-primary mb-3">Add New Footer</a>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Quick Home</th>
        <th>Quick About</th>
        <th>Company Privacy Policy</th>
        <th>Address</th>
        <th>Actions</th>
    </tr>
    @foreach($footers as $footer)
    <tr>
        <td>{{ $footer->id }}</td>
        <td>{{ $footer->quick_home }}</td>
        <td>{{ $footer->quick_about }}</td>
        <td>{{ $footer->company_privacy_policy }}</td>
        <td>{{ $footer->address }}</td>
        <td>
            <a href="{{ route('admin.footers.edit', $footer->id) }}" class="btn btn-warning btn-sm">Edit</a>
            <form action="{{ route('admin.footers.destroy', $footer->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this footer?')">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@endsection
