@extends('manager.layout')
@section('content')
<div class="dashboard_card">
    <div class="dashboard_card_body">
        <h3 class="mb-4">Manager Profile</h3>
        <form action="{{ route('manager.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
    <label class="form-label">Contact Name *</label>
    <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name', $manager->name) }}" required>
</div>
<div class="col-md-6">
    <label class="form-label">Contact Number *</label>
    <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $branch->contact_number ?? '') }}" required>
</div>
<div class="col-md-6">
    <label class="form-label">Email *</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $manager->email) }}" required>
</div>
<div class="col-md-6">
    <label class="form-label">Address</label>
    <input type="text" name="address" class="form-control" value="{{ old('address', $branch->address ?? '') }}">
</div>
<div class="col-md-6">
    <label class="form-label">City</label>
    <input type="text" name="city" class="form-control" value="{{ old('city', $branch->city ?? '') }}">
</div>
<div class="col-md-6">
    <label class="form-label">Pincode</label>
    <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $branch->pincode ?? '') }}">
</div>

            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </div>
        </form>

        <!-- Change Password Section -->
        <h4 class="mt-5 mb-3">Change Password</h4>
        <form action="{{ route('manager.profile.password') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Current Password *</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">New Password *</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm New Password *</label>
                    <input type="password" name="new_password_confirmation" class="form-control" required>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-warning">Update Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
