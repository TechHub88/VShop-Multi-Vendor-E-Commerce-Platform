@extends('layouts.vertical', ['title' => 'Blogs'])

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Blog Posts</h4>
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">+ Add New Blog</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                 
                    
                    <select id="categoryFilter" class="form-control">
    <option value="">All Categories</option>
    @foreach($categories as $category)
        <option value="{{ $category->id }}" 
            {{ request('category_id') == $category->id ? 'selected' : '' }}>
            {{ $category->name }}
        </option>
    @endforeach
</select>

                </div>
                <div class="col-md-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search blogs...">
                </div>
                <div class="col-md-3">
                    <button id="resetFilters" class="btn btn-secondary w-100">Reset Filters</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th width="250">Blog Post</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Comments</th>
                            <th>Views</th>
                            <th>Published Date</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($blogs as $key => $blog)
                        <tr class="blog-row" 
                            data-status="{{ $blog->status }}" 
                            data-category="{{ $blog->category_id }}"
                            data-search="{{ strtolower($blog->title . ' ' . $blog->excerpt) }}">
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($blog->featured_image)
                                        <img src="{{ asset($blog->featured_image) }}" 
                                             alt="{{ $blog->title }}" 
                                             class="rounded me-2" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @endif
                                    <div>
                                        <strong>{{ Str::limit($blog->title, 40) }}</strong>
                                        @if($blog->excerpt)
                                            <div class="text-muted small mt-1">
                                                {{ Str::limit($blog->excerpt, 60) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $blog->category_name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td>{{ $blog->author->name ?? 'Admin' }}</td>
                            <td>
                                <a href="javascript:void(0);" 
                                   class="status-toggle" 
                                   data-id="{{ $blog->id }}" 
                                   data-status="{{ $blog->status }}"
                                   style="text-decoration: none; cursor: pointer;">
                                    @php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'published' => 'success',
                                            'archived' => 'dark'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$blog->status] ?? 'secondary' }}">
                                        {{ ucfirst($blog->status) }}
                                    </span>
                                </a>
                            </td>
                            <td>
                                <a href="javascript:void(0);" 
                                   class="featured-toggle" 
                                   data-id="{{ $blog->id }}" 
                                   data-featured="{{ $blog->is_featured }}"
                                   style="text-decoration: none; cursor: pointer;">
                                    @if($blog->is_featured)
                                        <span class="badge bg-warning">Featured</span>
                                    @else
                                        <span class="badge bg-light text-dark">Normal</span>
                                    @endif
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.blogs.comments', $blog->id) }}" 
                                   class="badge bg-info text-white" 
                                   style="text-decoration: none; cursor: pointer;"
                                   title="View Comments">
                                    {{ $blog->comments_count ?? 0 }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ number_format($blog->view_count) }}
                                </span>
                            </td>
                            <td>
                                @if($blog->published_at)
                                    {{ $blog->published_at->format('d M, Y') }}
                                @else
                                    <span class="text-muted">Not published</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.blogs.edit', $blog->id) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                   
                                    <form action="{{ route('admin.blogs.destroy', $blog->id) }}" 
                                          method="POST" 
                                          style="display:inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this blog post?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center">No blog posts found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($blogs->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $blogs->firstItem() }} to {{ $blogs->lastItem() }} of {{ $blogs->total() }} entries
                </div>
                <nav>
                    {{ $blogs->links() }}
                </nav>
            </div>
            @endif

        </div>
    </div>

</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
$(document).ready(function() {
    // Configure Toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        preventDuplicates: true,
        newestOnTop: true,
        positionClass: "toast-top-right",
        timeOut: 3000
    };

    // Status toggle handler
    $('.status-toggle').click(function(e) {
        e.preventDefault();
        
        var $this = $(this);
        var blogId = $this.data('id');
        var currentStatus = $this.data('status');
        
        // Determine next status
        var statuses = ['draft', 'published', 'archived'];
        var currentIndex = statuses.indexOf(currentStatus);
        var newStatus = currentIndex < statuses.length - 1 ? statuses[currentIndex + 1] : statuses[0];
        
        // Disable click during request
        $this.css('pointer-events', 'none');
        
        $.ajax({
            url: '{{ route("admin.blogs.update-status", ":id") }}'.replace(':id', blogId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                // Update status colors mapping
                var statusColors = {
                    'draft': 'secondary',
                    'published': 'success',
                    'archived': 'dark'
                };
                
                // Update the badge
                var $badge = $this.find('.badge');
                $badge.removeClass().addClass('badge bg-' + statusColors[newStatus]).text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                
                // Update data attribute
                $this.data('status', newStatus);
                
                // Show success toast
                toastr.success('Blog status updated to ' + newStatus.charAt(0).toUpperCase() + newStatus.slice(1) + ' successfully!', 'Status Updated');
            },
            error: function(xhr) {
                var errorMessage = 'Failed to update status. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage, 'Error');
            },
            complete: function() {
                // Re-enable click
                $this.css('pointer-events', 'auto');
            }
        });
    });

    // Featured toggle handler
    $('.featured-toggle').click(function(e) {
        e.preventDefault();
        
        var $this = $(this);
        var blogId = $this.data('id');
        var isFeatured = $this.data('featured');
        var newFeaturedStatus = !isFeatured;
        
        // Disable click during request
        $this.css('pointer-events', 'none');
        
        $.ajax({
            url: '{{ route("admin.blogs.toggle-featured", ":id") }}'.replace(':id', blogId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Update the badge
                var $badge = $this.find('.badge');
                if (response.is_featured) {
                    $badge.removeClass().addClass('badge bg-warning').text('Featured');
                } else {
                    $badge.removeClass().addClass('badge bg-light text-dark').text('Normal');
                }
                
                // Update data attribute
                $this.data('featured', response.is_featured);
                
                // Show success toast
                toastr.success('Featured status updated successfully!', 'Success');
            },
            error: function(xhr) {
                var errorMessage = 'Failed to update featured status. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage, 'Error');
            },
            complete: function() {
                // Re-enable click
                $this.css('pointer-events', 'auto');
            }
        });
    });

    // Filter functionality
    function filterBlogs() {
        var statusFilter = $('#statusFilter').val();
        var categoryFilter = $('#categoryFilter').val();
        var searchTerm = $('#searchInput').val().toLowerCase();
        
        $('.blog-row').each(function() {
            var $row = $(this);
            var status = $row.data('status');
            var category = $row.data('category');
            var search = $row.data('search');
            
            var statusMatch = !statusFilter || status === statusFilter;
            var categoryMatch = !categoryFilter || category === categoryFilter;
            var searchMatch = !searchTerm || search.includes(searchTerm);
            
            if (statusMatch && categoryMatch && searchMatch) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    }

    // Event listeners for filters
    $('#statusFilter, #categoryFilter').on('change', filterBlogs);
    $('#searchInput').on('keyup', filterBlogs);
    
    // Reset filters
    $('#resetFilters').click(function() {
        $('#statusFilter').val('');
        $('#categoryFilter').val('');
        $('#searchInput').val('');
        filterBlogs();
    });
});
</script>

<style>
.blog-row:hover {
    background-color: #f8f9fa;
}
.btn-group .btn {
    padding: 0.25rem 0.5rem;
    margin-right: 2px;
}
.badge {
    font-size: 0.85em;
}
</style>
@endsection