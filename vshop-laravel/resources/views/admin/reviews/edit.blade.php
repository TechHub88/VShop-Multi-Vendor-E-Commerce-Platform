@extends('layouts.vertical', ['title' => 'Edit Review'])

@section('content')
<div class="container-fluid">

    <div class="card">
        <div class="card-header"><h5>Edit Review</h5></div>

        <div class="card-body">
            <form action="{{ route('admin.update', $review) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <!-- Product Info -->
                <div class="col-md-6">
                    <label class="form-label">Product</label>
                    <input type="text" class="form-control" value="{{ $review->product->name }}" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label">User</label>
                    <input type="text" class="form-control" value="{{ $review->user_name }} ({{ $review->user_email }})" readonly>
                </div>

                <!-- Rating -->
                <div class="col-md-6">
                    <label class="form-label">Rating <span class="text-danger">*</span></label>
                    <select name="rating" class="form-select" required>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ $review->rating == $i ? 'selected' : '' }}>
                                {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="is_approved" class="form-select">
                        <option value="1" {{ $review->is_approved ? 'selected' : '' }}>Approved</option>
                        <option value="0" {{ !$review->is_approved ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <!-- Review Title -->
                <div class="col-md-12">
                    <label class="form-label">Review Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ $review->title }}" required>
                </div>

                <!-- Review Comment -->
                <div class="col-md-12">
                    <label class="form-label">Review Comment <span class="text-danger">*</span></label>
                    <textarea name="comment" class="form-control" rows="5" required>{{ $review->comment }}</textarea>
                </div>

                <!-- Additional Options -->
                <div class="col-md-4">
                    <label class="form-label">Verified Purchase</label>
                    <select name="is_verified_purchase" class="form-select">
                        <option value="1" {{ $review->is_verified_purchase ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$review->is_verified_purchase ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Would Recommend</label>
                    <select name="would_recommend" class="form-select">
                        <option value="">Not Specified</option>
                        <option value="1" {{ $review->would_recommend === true ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $review->would_recommend === false ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <!-- Admin Reply -->
                <div class="col-md-12">
                    <label class="form-label">Admin Reply</label>
                    <textarea class="form-control" readonly rows="3">{{ $review->admin_reply ?? 'No reply yet' }}</textarea>
                    @if($review->replied_at)
                        <small class="text-muted">
                            Replied by: {{ $review->adminReplier->name ?? 'Admin' }} on {{ $review->replied_at->format('M j, Y g:i A') }}
                        </small>
                    @endif
                </div>

                <!-- Submit -->
                <div class="col-12 text-end mt-3">
                    <a href="{{ route('admin.index') }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Update Review</button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
