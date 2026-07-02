@extends('layouts.vertical', ['title' => 'Add Brand Location'])

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb‑0">Add Brand Location</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.brand-location.store') }}" method="POST" class="row g‑3">
                @csrf

                <!-- Brand Dropdown -->
                <div class="col-md-6">
                    <label class="form-label">Select Brand <span class="text-danger">*</span></label>
                    <select name="brand_id" class="form-control" required>
                        <option value="">-- Select Brand --</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}"
                                {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- User Fields -->
                <div class="col-md-6">
                    <label class="form-label">Contact Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <!-- Location Fields -->
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" name="city" value="{{ old('city') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Pincode</label>
                    <input type="text" class="form-control" name="pincode" value="{{ old('pincode') }}">
                </div>

                <!-- Submit Button -->
                <div class="col-12 text-end">
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Location</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
