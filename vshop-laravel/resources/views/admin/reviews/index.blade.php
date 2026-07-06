@extends('layouts.vertical', ['title' => 'Review Management'])

@section('content')

<style>
    .table th {
        background: #004392 !important;
        color: #fff !important;
    }
    .btn-primary,
    .btn-warning,
    .btn-danger,
    .btn-info {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-light border {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
    .btn-secondary {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .reset-btn {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
        margin-left: 12px;
    }
    .reset-btn:hover {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
</style>
<div class="container-fluid pt-3">

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_reviews'] ?? 0 }}</h4>
                            <p class="mb-0">Total Reviews</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa-solid fa-comment"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['approved_reviews'] ?? 0 }}</h4>
                            <p class="mb-0">Approved</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['pending_reviews'] ?? 0 }}</h4>
                            <p class="mb-0">Pending</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['average_rating'] ?? 0 }}/5</h4>
                            <p class="mb-0">Avg Rating</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa-solid fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Manage Reviews</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" id="refreshStats">
                        <i class="fa-solid fa-arrow-rotate-right"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" placeholder="Search reviews..." id="searchInput">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rating</label>
                    <select class="form-select" id="ratingFilter">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary" id="applyFilters" style="margin-top:28px;">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                    
                    <button class="btn reset-btn" style="margin-top:28px;">Reset</button>
                </div>
            </div>

            <!-- Reviews Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>#{{ $review->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($review->product->thumbnail)
                                            <img src="{{ asset($review->product->thumbnail) }}" 
                                                 class="rounded me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <strong>{{ Str::limit($review->product->name, 30) }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $review->user_name }}</strong>
                                    @if($review->user_email)
                                        <br><small class="text-muted">{{ $review->user_email }}</small>
                                    @endif
                                    @if($review->is_verified_purchase)
                                        <br><span class="badge bg-success small">Verified Purchase</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fa-solid fa-star text-warning"></i>
                                            @else
                                                <i class="fa-regular fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <small class="text-muted">{{ $review->rating }}/5</small>
                                </td>
                                <td>
                                    <strong>{{ $review->title }}</strong>
                                    <p class="mb-0 text-muted small">{{ Str::limit($review->comment, 50) }}</p>
                                    @if($review->would_recommend !== null)
                                        <small class="{{ $review->would_recommend ? 'text-success' : 'text-danger' }}">
                                            {{ $review->would_recommend ? '✓ Recommends' : '✗ Doesn\'t recommend' }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $review->is_approved ? 'bg-success' : 'bg-warning' }}">
                                        {{ $review->is_approved ? 'Approved' : 'Pending' }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $review->created_at->format('M j, Y') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $review->created_at->format('g:i A') }}</small>
                                </td>
                                <td>

<div class="btn-group">

{{-- APPROVE --}}
<button class="btn btn-sm btn-success approve-btn"
data-id="{{ $review->id }}">
<i class="bi bi-check-lg"></i>
</button>


{{-- REJECT --}}
<button class="btn btn-sm btn-warning reject-btn"
data-id="{{ $review->id }}" title="Reject Review">
<i class="bi bi-x-lg"></i>
</button>

{{-- BLOCK SELLER --}}
<button class="btn btn-sm btn-danger block-seller-btn"
data-id="{{ $review->id }}" title="Block Seller">
<i class="fa-solid fa-ban"></i>
</button>

{{-- INACTIVE SELLER --}}
<button class="btn btn-sm btn-secondary inactive-seller-btn"
data-id="{{ $review->id }}" title="Inactive Seller">
<i class="fa-solid fa-user-slash"></i>
</button>

</div>

</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fa-light fa-comments fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No reviews found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
         <div class="d-flex justify-content-between align-items-center mt-3">


<div>
{{ $reviews->links('pagination::bootstrap-5') }}
</div>

</div>
        </div>
    </div>

</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replyModalLabel">Add Admin Reply</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="replyForm">
                    @csrf
                    <input type="hidden" name="review_id" id="replyReviewId">
                    <div class="mb-3">
                        <label class="form-label">Admin Reply</label>
                        <textarea class="form-control" name="admin_reply" rows="4" placeholder="Enter your reply..." required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Reply</button>
                    </div>
                </form>
            </div>
        </div>
</div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle review status
    document.querySelectorAll('.toggle-status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reviewId = this.dataset.reviewId;
            const currentStatus = this.dataset.currentStatus === '1';
            
            fetch(`/admin/reviews/${reviewId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Delete review
    document.querySelectorAll('.delete-review-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reviewId = this.dataset.reviewId;
            const reviewTitle = this.dataset.reviewTitle;
            
            if (confirm(`Are you sure you want to delete review: "${reviewTitle}"?`)) {
                fetch(`/admin/reviews/${reviewId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });

    // Reply modal
    document.querySelectorAll('.reply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reviewId = this.dataset.reviewId;
            document.getElementById('replyReviewId').value = reviewId;
        });
    });

    // Submit reply
    document.getElementById('replyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const reviewId = document.getElementById('replyReviewId').value;
        const formData = new FormData(this);

        fetch(`/admin/reviews/${reviewId}/reply`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#replyModal').modal('hide');
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Apply filters
    document.getElementById('applyFilters').addEventListener('click', function() {
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        const rating = document.getElementById('ratingFilter').value;
        
        let url = new URL(window.location.href);
        let params = new URLSearchParams();
        
        if (search) params.append('search', search);
        if (status) params.append('status', status);
        if (rating) params.append('rating', rating);
        
        window.location.href = url.pathname + '?' + params.toString();
    });

    // Refresh stats
    document.getElementById('refreshStats').addEventListener('click', function() {
        location.reload();
    });
});



</script>
<script>
    // APPROVE REVIEW
document.querySelectorAll('.approve-btn').forEach(btn => {

btn.addEventListener('click', function(){

let id = this.dataset.id;

fetch(`/admin/reviews/${id}/approve`, {

method: "POST",

headers:{
"X-CSRF-TOKEN":"{{ csrf_token() }}",
"Accept":"application/json"
}

})
.then(res => res.json())
.then(data => {

if(data.success){
location.reload();
}

});

});

});



// REJECT REVIEW
document.querySelectorAll('.reject-btn').forEach(btn => {

btn.addEventListener('click', function(){

let id = this.dataset.id;

fetch(`/admin/reviews/${id}/reject`, {

method:"POST",

headers:{
"X-CSRF-TOKEN":"{{ csrf_token() }}",
"Accept":"application/json"
}

})
.then(res => res.json())
.then(data => {

if(data.success){
location.reload();
}

});

});

});

// BLOCK SELLER
document.querySelectorAll('.block-seller-btn').forEach(btn => {
    btn.addEventListener('click', function(){
        let id = this.dataset.id;
        
        Swal.fire({
            title: 'Block Seller',
            text: 'Please enter the reason for blocking this seller:',
            input: 'textarea',
            inputPlaceholder: 'Enter reason here...',
            inputAttributes: {
                'aria-label': 'Reason for blocking'
            },
            showCancelButton: true,
            confirmButtonText: 'Block Seller',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to write something!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append('reason', result.value);
                
                fetch(`/admin/reviews/${id}/block-seller`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success){
                        Swal.fire('Blocked!', data.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                });
            }
        });
    });
});

// INACTIVE SELLER
document.querySelectorAll('.inactive-seller-btn').forEach(btn => {
    btn.addEventListener('click', function(){
        let id = this.dataset.id;
        
        Swal.fire({
            title: 'Inactivate Seller',
            text: 'Please enter the reason for inactivating this seller:',
            input: 'textarea',
            inputPlaceholder: 'Enter reason here...',
            inputAttributes: {
                'aria-label': 'Reason for inactivating'
            },
            showCancelButton: true,
            confirmButtonText: 'Inactivate Seller',
            confirmButtonColor: '#f8bb86',
            cancelButtonColor: '#3085d6',
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to write something!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append('reason', result.value);
                
                fetch(`/admin/reviews/${id}/inactive-seller`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success){
                        Swal.fire('Inactivated!', data.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                });
            }
        });
    });
});
</script>

@endsection
