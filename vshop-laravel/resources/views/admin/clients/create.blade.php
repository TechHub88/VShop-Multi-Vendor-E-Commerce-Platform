@extends('layouts.vertical', ['title' => auth()->user()->type == 'travel' ? 'Add Location' : 'Add Brand'])

@section('content')
@php
    $userType = auth()->user()->type;
    $isTravel = $userType == 'travel';
    $isProduct = $userType == 'product';
    
    $title = $isTravel ? 'Location' : 'Brand';
    $storeRoute = $isTravel ? 'admin.location.store' : 'admin.clients.store';
    $indexRoute = $isTravel ? 'admin.location.index' : 'admin.clients.index';
@endphp

<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Add {{ $title }}</h5>
        </div>

        <div class="card-body">

            @if($isTravel)
            <!-- ========== TRAVEL ADMIN FORM (Simple) ========== -->
            <form action="{{ route($storeRoute) }}" method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Location Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required placeholder="Enter location name">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Location Logo <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="file" accept="image/*" required>
                    <small class="text-muted">Recommended: PNG, JPG, SVG (Max 2MB)</small>
                </div>

                <div class="col-12 text-end">
                    <a href="{{ route($indexRoute) }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Save Location</button>
                </div>

            </form>
            @else
            <!-- ========== PRODUCT ADMIN FORM (Detailed) ========== -->
            <form action="{{ route($storeRoute) }}" method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf

                <!-- Brand Name -->
                <div class="col-md-6">
                    <label class="form-label">Brand Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required placeholder="Enter brand name">
                </div>

                <!-- Brand Logo -->
                <div class="col-md-6">
                    <label class="form-label">Brand Logo <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="file" accept="image/*" required>
                    <small class="text-muted">Recommended: PNG, JPG, SVG (Max 2MB)</small>
                </div>

                <!-- Contact Information -->
                <div class="col-md-6">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" placeholder="brand@example.com">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input type="text" class="form-control" name="phone" placeholder="+1 234 567 8900">
                </div>

                <!-- Address Information -->
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" rows="2" placeholder="Full address"></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" name="city" placeholder="City">
                </div>

                <div class="col-md-4">
                    <label class="form-label">State</label>
                    <input type="text" class="form-control" name="state" placeholder="State">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Pincode/ZIP</label>
                    <input type="text" class="form-control" name="pincode" placeholder="123456">
                </div>

                <!-- Additional Fields (Optional) -->
                <div class="col-md-6">
                    <label class="form-label">Website</label>
                    <input type="url" class="form-control" name="website" placeholder="https://example.com">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Contact Person</label>
                    <input type="text" class="form-control" name="contact_person" placeholder="John Doe">
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label class="form-label">Description (Optional)</label>
                    <textarea class="form-control" name="description" rows="3" placeholder="Brief description about the brand"></textarea>
                </div>

                <!-- Submit Button -->
                <div class="col-12 text-end">
                    <a href="{{ route($indexRoute) }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Save Brand</button>
                </div>

            </form>
            @endif

        </div>
    </div>

</div>

@if($isProduct)
<script>
    // Optional: Add form validation or other JavaScript for Product Admin form
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const nameInput = document.querySelector('input[name="name"]');
            const fileInput = document.querySelector('input[name="file"]');
            
            if (!nameInput.value.trim()) {
                e.preventDefault();
                alert('Please enter brand name');
                nameInput.focus();
            }
            
            if (!fileInput.value) {
                e.preventDefault();
                alert('Please select a logo');
                fileInput.focus();
            }
        });
    });
</script>
@endif

@endsection
