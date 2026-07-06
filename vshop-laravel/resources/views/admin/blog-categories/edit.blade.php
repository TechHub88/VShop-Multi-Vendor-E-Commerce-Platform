@extends('layouts.vertical', ['title' => 'Edit Blog Category'])

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Edit Blog Category</h4>
        <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-secondary">Back to Categories</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-4">
            <form action="{{ route('admin.blog-categories.update', $category->id) }}" 
                  method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Left Column - Main Form -->
                    <div class="col-lg-8">
                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category Name *</label>
                            <input type="text" 
                                   name="name" 
                                   class="form-control py-2" 
                                   value="{{ old('name', $category->name) }}" 
                                   required
                                   placeholder="Enter category name...">
                            <div class="form-text">Unique category name. Slug will be generated automatically.</div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" 
                                      class="form-control py-2" 
                                      rows="4"
                                      placeholder="Describe this category...">{{ old('description', $category->description) }}</textarea>
                            <div class="form-text">Brief description of what this category is about.</div>
                        </div>

                        <!-- SEO Section -->
                        <div class="border rounded p-3 mb-3">
                            <h5 class="fw-bold mb-3">SEO Settings</h5>
                            
                            <!-- Meta Title -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Meta Title</label>
                                <input type="text" 
                                       name="meta_title" 
                                       class="form-control py-2" 
                                       value="{{ old('meta_title', $category->meta_title) }}"
                                       placeholder="Meta title for search engines...">
                                <div class="form-text">Recommended: 50-60 characters</div>
                            </div>

                            <!-- Meta Description -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Meta Description</label>
                                <textarea name="meta_description" 
                                          class="form-control py-2" 
                                          rows="3"
                                          placeholder="Meta description for search engines...">{{ old('meta_description', $category->meta_description) }}</textarea>
                                <div class="form-text">Recommended: 150-160 characters</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Settings -->
                    <div class="col-lg-4">
                        <!-- Status -->
                        <div class="border rounded p-3 mb-3">
                            <h5 class="fw-bold mb-3">Settings</h5>
                            
                            <!-- Status -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <div class="form-check">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           id="is_active" 
                                           class="form-check-input" 
                                           value="1"
                                           {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                                <div class="form-text">Inactive categories won't appear in dropdowns.</div>
                            </div>

                            <!-- Sort Order -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Sort Order</label>
                                <input type="number" 
                                       name="sort_order" 
                                       class="form-control py-2" 
                                       value="{{ old('sort_order', $category->sort_order) }}"
                                       placeholder="0">
                                <div class="form-text">Lower numbers appear first. Leave as 0 for auto.</div>
                            </div>
                        </div>

                        <!-- Information Box -->
                        <div class="border rounded p-3 mb-3 bg-light">
                            <h5 class="fw-bold mb-3">Information</h5>
                            <div class="mb-2">
                                <strong>Slug:</strong>
                                <code class="ms-2">{{ $category->slug }}</code>
                            </div>
                            <div class="mb-2">
                                <strong>Created:</strong>
                                <span class="ms-2">{{ $category->created_at->format('d M, Y') }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Last Updated:</strong>
                                <span class="ms-2">{{ $category->updated_at->format('d M, Y') }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Blog Posts:</strong>
                                <span class="badge bg-info ms-2">{{ $category->blogs()->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                        Update Category
                    </button>
                    <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-secondary px-4 py-2 fw-semibold">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
