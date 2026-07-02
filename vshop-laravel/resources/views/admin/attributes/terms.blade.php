@extends('layouts.vertical', ['title' => 'Configure Terms'])

@section('content')
<div class="container-fluid">

    <h4>Attribute: {{ $attribute->name }}</h4>

    <!-- Add New Term Form -->
    <form action="{{ route('admin.attributes.terms.store', $attribute->id) }}" method="POST" class="d-flex mb-3">
        @csrf
        <input type="text" name="term" class="form-control me-2" placeholder="Enter term (Ex: XL, Blue)" required>
        <button class="btn btn-primary">Add</button>
    </form>

    <!-- List of Terms -->
    @if($attribute->terms->isNotEmpty())
        <ul class="list-group">
            @foreach($attribute->terms as $term)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $term->term }}
                    <div class="d-flex gap-2">
                        <!-- Edit Button -->
                        <a href="{{ route('admin.attributes.terms.edit', [$attribute->id, $term->id]) }}" class="btn btn-warning btn-sm">Edit</a>

                        <!-- Delete Button -->
                        <form action="{{ route('admin.attributes.terms.destroy', [$attribute->id, $term->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this term?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p>No terms added yet.</p>
    @endif

</div>
@endsection
