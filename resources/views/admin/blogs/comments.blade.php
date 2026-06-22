@extends('layouts.vertical', ['title' => 'Blog Comments'])

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Comments for: {{ $blog->title }}</h4>
        <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">Back to Blogs</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comments as $key => $comment)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <strong>{{ $comment->name }}</strong>
                                @if($comment->website)
                                    <br><small><a href="{{ $comment->website }}" target="_blank">{{ $comment->website }}</a></small>
                                @endif
                            </td>
                            <td>{{ $comment->email }}</td>
                            <td>
                                <div style="max-width: 400px;">
                                    {{ Str::limit($comment->comment, 150) }}
                                    @if($comment->parent_id)
                                        <br><small class="text-muted">Reply to comment #{{ $comment->parent_id }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <a href="javascript:void(0);" 
                                   class="comment-status-toggle" 
                                   data-id="{{ $comment->id }}" 
                                   data-status="{{ $comment->status }}"
                                   style="text-decoration: none; cursor: pointer;">
                                    @php
                                        $statusColors = [
                                            'active' => 'success',
                                            'inactive' => 'secondary'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$comment->status] ?? 'secondary' }}">
                                        {{ ucfirst($comment->status) }}
                                    </span>
                                </a>
                            </td>
                            <td>{{ $comment->created_at->format('d M, Y H:i') }}</td>
                            <td>
                                <button type="button" 
                                        class="btn btn-sm btn-danger" 
                                        onclick="deleteComment({{ $comment->id }})"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center">No comments found for this blog</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($comments->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $comments->firstItem() }} to {{ $comments->lastItem() }} of {{ $comments->total() }} entries
                </div>
                <nav>
                    {{ $comments->links() }}
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

    // Comment status toggle handler
    $('.comment-status-toggle').click(function(e) {
        e.preventDefault();
        
        var $this = $(this);
        var commentId = $this.data('id');
        var currentStatus = $this.data('status');
        var newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        
        // Disable click during request
        $this.css('pointer-events', 'none');
        
        $.ajax({
            url: '{{ route("admin.blogs.comments.update-status", ["id" => $blog->id, "commentId" => ":commentId"]) }}'.replace(':commentId', commentId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                // Update status colors mapping
                var statusColors = {
                    'active': 'success',
                    'inactive': 'secondary'
                };
                
                // Update the badge
                var $badge = $this.find('.badge');
                $badge.removeClass().addClass('badge bg-' + statusColors[newStatus]).text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                
                // Update data attribute
                $this.data('status', newStatus);
                
                // Show success toast
                toastr.success('Comment status updated to ' + newStatus.charAt(0).toUpperCase() + newStatus.slice(1) + ' successfully!', 'Status Updated');
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
});

function deleteComment(commentId) {
    if (confirm('Are you sure you want to delete this comment?')) {
        $.ajax({
            url: '{{ route("admin.blogs.comments.delete", ["id" => $blog->id, "commentId" => ":commentId"]) }}'.replace(':commentId', commentId),
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                toastr.success('Comment deleted successfully!', 'Success');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                var errorMessage = 'Failed to delete comment. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage, 'Error');
            }
        });
    }
}
</script>

<style>
.comment-status-toggle:hover .badge {
    opacity: 0.8;
}
</style>
@endsection
