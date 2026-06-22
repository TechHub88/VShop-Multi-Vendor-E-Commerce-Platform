@extends('layouts.vertical', ['title' => 'Edit Term'])

@section('content')
<div class="container-fluid">

    <h4>Edit Term: {{ $term->term }}</h4>

    <!-- Edit Term Form -->
    <form action="{{ route('admin.attributes.terms.update', [$attribute->id, $term->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="term" class="form-label">Term</label>
            <input type="text" name="term" id="term" class="form-control" value="{{ old('term', $term->term) }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>

</div>
@endsection
