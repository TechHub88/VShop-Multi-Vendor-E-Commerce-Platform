@extends('layouts.vertical', ['title' => 'General Settings'])

@section('content')
<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h5>General Settings</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.save') }}" method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf

                <!-- System Logo Section -->
                <div class="col-md-12">
                    <label class="form-label">System Logo</label>
                    <div class="d-flex align-items-center">
                        <input type="file" name="system_logo" class="form-control" value="{{ old('system_logo', $settings->system_logo ?? '') }}">
                        @if ($settings->system_logo)
                            <img src="{{ asset($settings->system_logo) }}" alt="System Logo" class="img-fluid ms-3" style="max-width: 100px;">
                        @else
                            <span>No logo uploaded</span>
                        @endif
                    </div>
                </div>

                <!-- Favicon Section -->
                <div class="col-md-12">
                    <label class="form-label">Favicon Icon</label>
                    <div class="d-flex align-items-center">
                        <input type="file" name="favicon_icon" class="form-control" value="{{ old('favicon_icon', $settings->favicon_icon ?? '') }}">
                        @if ($settings->favicon_icon)
                            <img src="{{ asset($settings->favicon_icon) }}" alt="Favicon" class="img-fluid ms-3" style="max-width: 50px;">
                        @else
                            <span>No favicon uploaded</span>
                        @endif
                    </div>
                </div>

                <!-- LazyLoad Image Section -->
                <div class="col-md-12">
                    <label class="form-label">LazyLoad Image</label>
                    <div class="d-flex align-items-center">
                        <input type="file" name="lazyload_image" class="form-control" value="{{ old('lazyload_image', $settings->lazyload_image ?? '') }}">
                        @if ($settings->lazyload_image)
                            <img src="{{ asset($settings->lazyload_image) }}" alt="LazyLoad Image" class="img-fluid ms-3" style="max-width: 100px;">
                        @else
                            <span>No lazyload image uploaded</span>
                        @endif
                    </div>
                </div>

                <!-- Shop Link Banner Section -->
                <div class="col-md-12">
                    <label class="form-label">Shop Link Banner</label>
                    <div class="d-flex align-items-center">
                        <input type="file" name="shop_link_banner" class="form-control" value="{{ old('shop_link_banner', $settings->shop_link_banner ?? '') }}">
                        @if ($settings->shop_link_banner)
                            <img src="{{ asset($settings->shop_link_banner) }}" alt="Shop Banner" class="img-fluid ms-3" style="max-width: 100px;">
                        @else
                            <span>No banner uploaded</span>
                        @endif
                    </div>
                </div>

                <!-- Currency Format Section -->
                <div class="col-md-6">
                    <label class="form-label">Currency Format</label>
                    <select name="currency_format" class="form-select">
                        <option value="USD" {{ $settings->currency_format == 'USD' ? 'selected' : '' }}>$ - US Dollar</option>
                        <option value="INR" {{ $settings->currency_format == 'INR' ? 'selected' : '' }}>₹ - Indian Rupee</option>
                        <option value="EUR" {{ $settings->currency_format == 'EUR' ? 'selected' : '' }}>€ - Euro</option>
                    </select>
                </div>

                <!-- Site Name Section -->
                <div class="col-md-6">
                    <label class="form-label">Site Name</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings->site_name ?? '') }}" class="form-control">
                </div>

                <!-- Contact Email Section -->
                <div class="col-md-6">
                    <label class="form-label">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $settings->contact_email ?? '') }}" class="form-control">
                </div>

                <!-- Contact Phone Section -->
                <div class="col-md-6">
                    <label class="form-label">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings->contact_phone ?? '') }}" class="form-control">
                </div>

                <!-- Save Button -->
                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
