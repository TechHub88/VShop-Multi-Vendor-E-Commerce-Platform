@extends('layouts.vertical', ['title' => 'Blog Categories'])

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Blog Categories</h4>
        <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">+ Add New Category</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
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
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search categories...">
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
                            <th width="250">Category Name</th>
                            <th>Slug</th>
                            <th>Blog Posts</th>
                            <th>Status</th>
                            <th>Sort Order</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($categories as $key => $category)
                        <tr class="category-row" 
                            data-status="{{ $category->is_active ? 'active' : 'inactive' }}"
                            data-search="{{ strtolower($category->name . ' ' . $category->description) }}">
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <div>
                                    <strong>{{ $category->name }}</strong>
                                    @if($category->description)
                                        <div class="text-muted small mt-1">
                                            {{ Str::limit($category->description, 60) }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <code>{{ $category->slug }}</code>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $category->blogs->count() }} posts
                                </span>
                            </td>
                            <td>
                                <a href="javascript:void(0);" 
                                   class="status-toggle" 
                                   data-id="{{ $category->id }}" 
                                   data-status="{{ $category->is_active ? 'active' : 'inactive' }}"
                                   style="text-decoration: none; cursor: pointer;">
                                    @if($category->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $category->sort_order }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.blog-categories.edit', $category->id) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.blog-categories.destroy', $category->id) }}" 
                                          method="POST" 
                                          style="display:inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this category?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center">No categories found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($categories->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} entries
                </div>
                <nav>
                    {{ $categories->links() }}
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
        var categoryId = $this.data('id');
        var currentStatus = $this.data('status');
        var newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        
        // Disable click during request
        $this.css('pointer-events', 'none');
        
        $.ajax({
            url: '{{ route("admin.blog-categories.toggle-status", ":id") }}'.replace(':id', categoryId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                // Update the badge
                var $badge = $this.find('.badge');
                if (newStatus === 'active') {
                    $badge.removeClass('bg-secondary').addClass('bg-success').text('Active');
                } else {
                    $badge.removeClass('bg-success').addClass('bg-secondary').text('Inactive');
                }
                
                // Update data attribute
                $this.data('status', newStatus);
                
                // Show success toast
                toastr.success('Category status updated to ' + newStatus.charAt(0).toUpperCase() + newStatus.slice(1) + ' successfully!', 'Status Updated');
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

    // Filter functionality
    function filterCategories() {
        var statusFilter = $('#statusFilter').val();
        var searchTerm = $('#searchInput').val().toLowerCase();
        
        $('.category-row').each(function() {
            var $row = $(this);
            var status = $row.data('status');
            var search = $row.data('search');
            
            var statusMatch = !statusFilter || status === statusFilter;
            var searchMatch = !searchTerm || search.includes(searchTerm);
            
            if (statusMatch && searchMatch) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    }

    // Event listeners for filters
    $('#statusFilter').on('change', filterCategories);
    $('#searchInput').on('keyup', filterCategories);
    
    // Reset filters
    $('#resetFilters').click(function() {
        $('#statusFilter').val('');
        $('#searchInput').val('');
        filterCategories();
    });
});
</script>

<style>
.category-row:hover {
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
