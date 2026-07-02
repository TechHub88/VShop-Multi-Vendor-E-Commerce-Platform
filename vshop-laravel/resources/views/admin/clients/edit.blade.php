@extends('layouts.vertical', ['title' => auth()->user()->type == 'travel' ? 'Edit Location' : 'Edit Brand'])

@section('content')
@php
    $userType = auth()->user()->type;
    $isTravel = $userType == 'travel';
    $isProduct = $userType == 'product';
    
    $title = $isTravel ? 'Location' : 'Brand';
    $updateRoute = $isTravel ? 'admin.location.update' : 'admin.clients.update';
    $indexRoute = $isTravel ? 'admin.location.index' : 'admin.clients.index';
    
    // Determine which model data to use
    if($isTravel) {
        $item = $location ?? null;
    } else {
        $item = $client ?? null;
    }
@endphp

<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit {{ $title }}: {{ $item->name ?? '' }}</h5>
        </div>

        <div class="card-body">

            @if($isTravel)
            <!-- ========== TRAVEL ADMIN EDIT FORM ========== -->
            <form action="{{ route($updateRoute, $item->id ?? '') }}" method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label class="form-label">Location Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $item->name ?? '') }}" required placeholder="Enter location name">
                    @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Location Logo</label>
                    @if($item->logo_url ?? false)
                        <div class="mb-2">
                            <img src="{{ asset($item->logo_url) }}" alt="{{ $item->name }}" style="max-width: 100px; max-height: 100px;" class="img-thumbnail">
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="remove_logo" name="remove_logo">
                                <label class="form-check-label" for="remove_logo">Remove current logo</label>
                            </div>
                        </div>
                    @endif
                    <input type="file" class="form-control" name="file" accept="image/*">
                    <small class="text-muted">Leave empty to keep current logo. Recommended: PNG, JPG, SVG (Max 2MB)</small>
                    @error('file')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Additional travel fields if needed -->
                @if($item->country ?? false)
                <div class="col-md-4">
                    <label class="form-label">Country</label>
                    <input type="text" class="form-control" name="country" value="{{ old('country', $item->country ?? '') }}" placeholder="Country">
                </div>
                @endif

                @if($item->state ?? false)
                <div class="col-md-4">
                    <label class="form-label">State/Region</label>
                    <input type="text" class="form-control" name="state" value="{{ old('state', $item->state ?? '') }}" placeholder="State">
                </div>
                @endif

                @if($item->city ?? false)
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" name="city" value="{{ old('city', $item->city ?? '') }}" placeholder="City">
                </div>
                @endif

                @if($item->description ?? false)
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3" placeholder="Location description">{{ old('description', $item->description ?? '') }}</textarea>
                </div>
                @endif

                <!-- Status Field -->
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="active" {{ (old('status', $item->status ?? '') == 'active') ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ (old('status', $item->status ?? '') == 'inactive') ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12 text-end">
                    <a href="{{ route($indexRoute) }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Update Location</button>
                </div>

            </form>
            @else
            <!-- ========== PRODUCT ADMIN EDIT FORM ========== -->
            <form action="{{ route($updateRoute, $item->id ?? '') }}" method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Brand Name -->
                <div class="col-md-6">
                    <label class="form-label">Brand Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $item->name ?? '') }}" required placeholder="Enter brand name">
                    @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Brand Logo -->
                <div class="col-md-6">
                    <label class="form-label">Brand Logo</label>
                    @if($item->logo_url ?? false)
                        <div class="mb-2">
                            <img src="{{ asset($item->logo_url) }}" alt="{{ $item->name }}" style="max-width: 100px; max-height: 100px;" class="img-thumbnail">
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="remove_logo" name="remove_logo">
                                <label class="form-check-label" for="remove_logo">Remove current logo</label>
                            </div>
                        </div>
                    @endif
                    <input type="file" class="form-control" name="file" accept="image/*">
                    <small class="text-muted">Leave empty to keep current logo. Recommended: PNG, JPG, SVG (Max 2MB)</small>
                    @error('file')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Contact Information -->
                <div class="col-md-6">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $item->email ?? '') }}" placeholder="brand@example.com">
                    @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input type="text" class="form-control" name="phone" value="{{ old('phone', $item->phone ?? '') }}" placeholder="+1 234 567 8900">
                    @error('phone')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Address Information -->
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" rows="2" placeholder="Full address">{{ old('address', $item->address ?? '') }}</textarea>
                    @error('address')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" name="city" value="{{ old('city', $item->city ?? '') }}" placeholder="City">
                    @error('city')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">State</label>
                    <input type="text" class="form-control" name="state" value="{{ old('state', $item->state ?? '') }}" placeholder="State">
                    @error('state')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Pincode/ZIP</label>
                    <input type="text" class="form-control" name="pincode" value="{{ old('pincode', $item->pincode ?? '') }}" placeholder="123456">
                    @error('pincode')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Additional Fields (Optional) -->
                <div class="col-md-6">
                    <label class="form-label">Website</label>
                    <input type="url" class="form-control" name="website" value="{{ old('website', $item->website ?? '') }}" placeholder="https://example.com">
                    @error('website')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Contact Person</label>
                    <input type="text" class="form-control" name="contact_person" value="{{ old('contact_person', $item->contact_person ?? '') }}" placeholder="John Doe">
                    @error('contact_person')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label class="form-label">Description (Optional)</label>
                    <textarea class="form-control" name="description" rows="3" placeholder="Brief description about the brand">{{ old('description', $item->description ?? '') }}</textarea>
                    @error('description')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status Field -->
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="active" {{ (old('status', $item->status ?? '') == 'active') ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ (old('status', $item->status ?? '') == 'inactive') ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="col-12 text-end">
                    <a href="{{ route($indexRoute) }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Update Brand</button>
                </div>

            </form>
            @endif

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const removeLogoCheckbox = document.getElementById('remove_logo');
    const fileInput = document.querySelector('input[name="file"]');
    
    // Handle remove logo functionality
    if(removeLogoCheckbox && fileInput) {
        removeLogoCheckbox.addEventListener('change', function() {
            if(this.checked) {
                fileInput.disabled = true;
                fileInput.value = '';
            } else {
                fileInput.disabled = false;
            }
        });
    }
    
    // Form validation
    form.addEventListener('submit', function(e) {
        const nameInput = document.querySelector('input[name="name"]');
        
        if (!nameInput.value.trim()) {
            e.preventDefault();
            alert('Please enter {{ $title }} name');
            nameInput.focus();
        }
    });
});
</script>

@endsection