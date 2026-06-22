@extends('layouts.vertical', ['title' => 'Add Sub Category'])

@section('content')
<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h5>Add New Sub Category</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.subcategories.store') }}" method="POST" class="row g-3">
                @csrf

                <!-- Category -->
                <div class="col-md-6">
                    <label class="form-label">Select Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Subcategory Name -->
                <div class="col-md-6">
                    <label class="form-label">Sub Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <!-- Slug -->
                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" placeholder="Auto-generated if empty">
                </div>

                <!-- Submit -->
                <div class="col-12 text-end">
                    <a href="{{ route('admin.subcategories.index') }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Save Subcategory</button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
