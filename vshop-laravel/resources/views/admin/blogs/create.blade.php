@extends('layouts.vertical', ['title' => isset($blog) ? 'Edit Blog' : 'Create Blog'])

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>{{ isset($blog) ? 'Edit Blog Post' : 'Create New Blog Post' }}</h4>
        <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">Back to Blogs</a>
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

    <div class="card">
        <div class="card-body p-4">
            <form action="{{ isset($blog) ? route('admin.blogs.update', $blog->id) : route('admin.blogs.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                @if(isset($blog)) @method('PUT') @endif

                <div class="row">
                    <!-- Left Column - Main Content -->
                    <div class="col-lg-8">
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Blog Title *</label>
                            <input type="text" 
                                   name="title" 
                                   class="form-control py-2" 
                                   value="{{ old('title', $blog->title ?? '') }}" 
                                   required
                                   placeholder="Enter blog title...">
                        </div>

                        <!-- Excerpt -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Excerpt (Short Description)</label>
                            <textarea name="excerpt" 
                                      class="form-control py-2" 
                                      rows="3"
                                      placeholder="Brief summary of the blog post...">{{ old('excerpt', $blog->excerpt ?? '') }}</textarea>
                            <div class="form-text">Maximum 500 characters. This will appear in blog listings.</div>
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Content *</label>
                            <textarea name="content" 
                                      id="blogContent" 
                                      class="form-control py-2" 
                                      rows="15"
                                      required
                                      placeholder="Write your blog content here...">{{ old('content', $blog->content ?? '') }}</textarea>
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
                                       value="{{ old('meta_title', $blog->meta_title ?? '') }}"
                                       placeholder="Meta title for search engines...">
                                <div class="form-text">Recommended: 50-60 characters</div>
                            </div>

                            <!-- Meta Description -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Meta Description</label>
                                <textarea name="meta_description" 
                                          class="form-control py-2" 
                                          rows="3"
                                          placeholder="Meta description for search engines...">{{ old('meta_description', $blog->meta_description ?? '') }}</textarea>
                                <div class="form-text">Recommended: 150-160 characters</div>
                            </div>

                            <!-- Meta Keywords -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Meta Keywords</label>
                                <input type="text" 
                                       name="meta_keywords" 
                                       class="form-control py-2" 
                                       value="{{ old('meta_keywords', $blog->meta_keywords ?? '') }}"
                                       placeholder="Keywords separated by commas...">
                            </div>

                            <!-- Tags -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tags</label>
                                <input type="text" 
                                       name="tags" 
                                       class="form-control py-2" 
                                       value="{{ old('tags', isset($blog) && $blog->tags ? implode(',', $blog->tags) : '') }}"
                                       placeholder="Add tags separated by commas...">
                                <div class="form-text">e.g., school uniforms, fashion tips, corporate wear</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Settings -->
                    <div class="col-lg-4">
                        <!-- Status & Featured -->
                        <div class="border rounded p-3 mb-3">
                            <h5 class="fw-bold mb-3">Publish Settings</h5>
                            
                            <!-- Status -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status *</label>
                                <select name="status" class="form-select py-2" required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" 
                                            {{ old('status', $blog->status ?? 'draft') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Featured -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" 
                                       name="is_featured" 
                                       id="is_featured" 
                                       class="form-check-input" 
                                       value="1"
                                       {{ old('is_featured', $blog->is_featured ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_featured">
                                    Mark as Featured
                                </label>
                                <div class="form-text">Featured blogs appear in special sections</div>
                            </div>

                            <!-- Published Date -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Publish Date & Time</label>
                                <input type="datetime-local" 
                                       name="published_at" 
                                       class="form-control py-2" 
                                       value="{{ old('published_at', isset($blog) && $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : '') }}">
                                <div class="form-text">Leave empty to publish immediately or schedule for later</div>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="border rounded p-3 mb-3">
                            <h5 class="fw-bold mb-3">Category</h5>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Category *</label>
                                <select name="category_id" class="form-select py-2" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('category_id', $blog->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($categories->isEmpty())
                                    <div class="alert alert-warning mt-2">
                                        No categories found. <a href="{{ route('admin.blog-categories.create') }}">Create a category first</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div class="border rounded p-3 mb-3">
                            <h5 class="fw-bold mb-3">Featured Image</h5>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Upload Image</label>
                                <input type="file" 
                                       name="featured_image" 
                                       class="form-control py-2" 
                                       accept="image/*">
                                
                                @if(isset($blog) && $blog->featured_image)
                                    <div class="mt-3">
                                        <label class="form-label fw-semibold">Current Image</label>
                                        <div class="border rounded p-2 text-center">
                                            <img src="{{ asset($blog->featured_image) }}" 
                                                 alt="{{ $blog->title }}" 
                                                 class="img-fluid rounded"
                                                 style="max-height: 200px;">
                                            <div class="mt-2">
                                                <a href="{{ asset($blog->featured_image) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    View Full Image
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="form-text">
                                    Recommended size: 1200x630 pixels<br>
                                    Supported formats: JPG, PNG, GIF
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                        {{ isset($blog) ? 'Update Blog Post' : 'Create Blog Post' }}
                    </button>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary px-4 py-2 fw-semibold">
                        Cancel
                    </a>
                    @if(isset($blog))
                    <a href="{{ route('blog.show', $blog->slug) }}" 
                       target="_blank" 
                       class="btn btn-info px-4 py-2 fw-semibold">
                        Preview
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Simple Text Editor Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('blogContent');
    
    // Add basic formatting buttons
    const toolbar = document.createElement('div');
    toolbar.className = 'mb-2';
    toolbar.innerHTML = `
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('bold')">
            <i class="fas fa-bold"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('italic')">
            <i class="fas fa-italic"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('underline')">
            <i class="fas fa-underline"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('ul')">
            <i class="fas fa-list-ul"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('ol')">
            <i class="fas fa-list-ol"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('link')">
            <i class="fas fa-link"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('image')">
            <i class="fas fa-image"></i>
        </button>
    `;
    
    textarea.parentNode.insertBefore(toolbar, textarea);
});

function formatText(command) {
    const textarea = document.getElementById('blogContent');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    
    let formattedText = '';
    
    switch(command) {
        case 'bold':
            formattedText = `**${selectedText}**`;
            break;
        case 'italic':
            formattedText = `*${selectedText}*`;
            break;
        case 'underline':
            formattedText = `<u>${selectedText}</u>`;
            break;
        case 'ul':
            formattedText = selectedText.split('\n').map(line => `* ${line}`).join('\n');
            break;
        case 'ol':
            formattedText = selectedText.split('\n').map((line, i) => `${i + 1}. ${line}`).join('\n');
            break;
        case 'link':
            const url = prompt('Enter URL:', 'https://');
            if (url) {
                formattedText = `[${selectedText}](${url})`;
            } else {
                return;
            }
            break;
        case 'image':
            const imgUrl = prompt('Enter image URL:', 'https://');
            if (imgUrl) {
                const altText = prompt('Enter alt text:', '');
                formattedText = `![${altText}](${imgUrl})`;
            } else {
                return;
            }
            break;
    }
    
    textarea.value = textarea.value.substring(0, start) + 
                     formattedText + 
                     textarea.value.substring(end);
    
    // Restore focus and selection
    textarea.focus();
    textarea.selectionStart = start + formattedText.length;
    textarea.selectionEnd = start + formattedText.length;
}
</script>

<style>
.form-text {
    font-size: 0.85rem;
    color: #6c757d;
}
.btn-group-vertical .btn {
    margin-bottom: 2px;
}
</style>
@endsection