@extends('layouts.vertical', ['title' => 'Edit Sub Category'])

@section('content')
<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h5>Edit Sub Category</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.subcategories.update', $subcategory->id) }}" 
                  method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <!-- Category -->
                <div class="col-md-6">
                    <label class="form-label">Select Category</label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ $subcategory->category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Name -->
                <div class="col-md-6">
                    <label class="form-label">Sub Category Name</label>
                    <input type="text" name="name" value="{{ $subcategory->name }}" class="form-control" required>
                </div>

                <!-- Slug -->
                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" value="{{ $subcategory->slug }}">
                </div>

                <!-- Submit -->
                <div class="col-12 text-end">
                    <a href="{{ route('admin.subcategories.index') }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Update</button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
