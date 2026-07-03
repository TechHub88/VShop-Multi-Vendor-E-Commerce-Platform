@extends('layouts.vertical', ['title' => 'News Highlights'])

@section('content')
<h3>News Highlights</h3>
<a href="{{ route('admin.newshighlights.create') }}" class="btn btn-primary mb-3">Add New</a>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Images</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($news as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->title }}</td>
       <td>
    @if($item->image)
        <img src="{{ asset('uploads/news/'.$item->image) }}" width="80" class="me-2 mb-2" alt="News Image">
    @endif
</td>


            <td>{{ $item->description }}</td>
            <td>
                <a href="{{ route('admin.newshighlights.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>

                <form action="{{ route('admin.newshighlights.destroy', $item->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to delete?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
