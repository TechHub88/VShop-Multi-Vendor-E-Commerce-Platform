@extends('layouts.vertical', ['title' => 'Attributes'])

@section('content')
<h3>About Section List</h3>
<a href="{{ route('admin.about.create') }}" class="btn btn-primary mb-3">Add New</a>

<table class="table table-bordered">
    <tr>
        <th>ID</th><th>Image</th><th>Title</th><th>Description</th><th>Action</th>
    </tr>
    @foreach($aboutSections as $item)
    <tr>
        <td>{{ $item->id }}</td>
        <td><img src="{{ asset('uploads/about/'.$item->bgimage) }}" width="80"></td>
        <td>{{ $item->title }}</td>
        <td>{{ Str::limit($item->description, 50) }}</td>
        <td>
            <a href="{{ route('admin.about.edit',$item->id) }}" class="btn btn-warning btn-sm">Edit</a>
            <form action="{{ route('admin.about.destroy',$item->id) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>

@endsection
