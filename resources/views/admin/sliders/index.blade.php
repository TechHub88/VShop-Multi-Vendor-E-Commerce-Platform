@extends('layouts.vertical', ['title' => 'Sliders'])

@section('content')
<h3>Slider List</h3>
<a href="{{ route('admin.sliders.create') }}" class="btn btn-primary mb-3">Add New Slider</a>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Banner Image</th>
        <th>Second Image</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>
    @foreach($sliders as $slider)
    <tr>
        <td>{{ $slider->id }}</td>
        <td>
            @if($slider->banner_image)
                <img src="{{ asset('uploads/slider/'.$slider->banner_image) }}" width="80" alt="Banner Image">
            @endif
        </td>
        <td>
            @if($slider->second_image)
                <img src="{{ asset('uploads/slider/'.$slider->second_image) }}" width="80" alt="Second Image">
            @endif
        </td>
        <td>{{ $slider->created_at->format('d-m-Y') }}</td>
        <td>
            <a href="{{ route('admin.sliders.edit', $slider->id) }}" class="btn btn-warning btn-sm">Edit</a>
            
            <form action="{{ route('admin.sliders.destroy', $slider->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to delete this slider?')">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>

@endsection
