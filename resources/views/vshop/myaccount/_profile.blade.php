<?php
/* resources/views/vshop/myaccount/_profile.blade.php */
?>
<div class="card">
  <div class="card-body">
    <h5 class="card-title">Personal Information</h5>
    <form method="POST" action="{{ route('customer.profile.update') }}">
      @csrf
      @method('PUT')
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Name</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="name" value="{{ old('name', auth()->user()->name) }}" required>
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
          <input type="email" class="form-control" name="email" value="{{ old('email', auth()->user()->email) }}" required>
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Mobile Number</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="mobile" value="{{ old('mobile', auth()->user()->mobile ?? '') }}">
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Gender</label>
        <div class="col-sm-10">
          <select class="form-select" name="gender">
            <option value="" {{ old('gender') == '' ? 'selected' : '' }}>Select</option>
            <option value="male" {{ old('gender', auth()->user()->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender', auth()->user()->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old('gender', auth()->user()->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
          </select>
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Language</label>
        <div class="col-sm-10">
          <select class="form-select" name="language">
            <option value="en" {{ old('language', auth()->user()->language ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
            <option value="hi" {{ old('language', auth()->user()->language ?? '') == 'hi' ? 'selected' : '' }}>Hindi</option>
            <!-- Add more languages as needed -->
          </select>
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Occupation</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="occupation" value="{{ old('occupation', auth()->user()->occupation ?? '') }}">
        </div>
      </div>
      <div class="row mb-3">
        <label class="col-sm-2 col-form-label">Profile Photo</label>
        <div class="col-sm-10">
          <input type="file" class="form-control" name="photo">
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
  </div>
</div>
