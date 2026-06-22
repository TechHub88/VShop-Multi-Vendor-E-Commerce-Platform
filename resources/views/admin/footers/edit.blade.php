@extends('layouts.vertical', ['title' => 'Edit Footer'])

@section('content')
<h3>Edit Footer</h3>

<form action="{{ route('admin.footers.update', $footer->id) }}" method="POST">
    @csrf
    @method('PUT')

    <h5>Quick Links</h5>
    <div class="mb-3">
        <label>Home</label>
        <input type="text" name="quick_home" class="form-control" value="{{ $footer->quick_home }}">
    </div>
    <div class="mb-3">
        <label>About</label>
        <input type="text" name="quick_about" class="form-control" value="{{ $footer->quick_about }}">
    </div>
    <div class="mb-3">
        <label>FAQ</label>
        <input type="text" name="quick_faq" class="form-control" value="{{ $footer->quick_faq }}">
    </div>
    <div class="mb-3">
        <label>Testimonials</label>
        <input type="text" name="quick_testimonials" class="form-control" value="{{ $footer->quick_testimonials }}">
    </div>
    <div class="mb-3">
        <label>Blogs</label>
        <input type="text" name="quick_blogs" class="form-control" value="{{ $footer->quick_blogs }}">
    </div>

    <h5>Company</h5>
    <div class="mb-3">
        <label>Privacy Policy</label>
        <input type="text" name="company_privacy_policy" class="form-control" value="{{ $footer->company_privacy_policy }}">
    </div>
    <div class="mb-3">
        <label>Return Policy</label>
        <input type="text" name="company_return_policy" class="form-control" value="{{ $footer->company_return_policy }}">
    </div>
    <div class="mb-3">
        <label>Terms & Conditions</label>
        <input type="text" name="company_terms" class="form-control" value="{{ $footer->company_terms }}">
    </div>

    <h5>Category</h5>
    <div class="mb-3">
        <label>Corporate</label>
        <input type="text" name="category_corporate" class="form-control" value="{{ $footer->category_corporate }}">
    </div>
    <div class="mb-3">
        <label>Work</label>
        <input type="text" name="category_work" class="form-control" value="{{ $footer->category_work }}">
    </div>
    <div class="mb-3">
        <label>Healthcare</label>
        <input type="text" name="category_healthcare" class="form-control" value="{{ $footer->category_healthcare }}">
    </div>
    <div class="mb-3">
        <label>School</label>
        <input type="text" name="category_school" class="form-control" value="{{ $footer->category_school }}">
    </div>

    <h5>Contact Us</h5>
    <div class="mb-3">
        <label>Address</label>
        <input type="text" name="address" class="form-control" value="{{ $footer->address }}">
    </div>
    <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ $footer->phone }}">
    </div>
    <div class="mb-3">
        <label>Fax</label>
        <input type="text" name="fax" class="form-control" value="{{ $footer->fax }}">
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ $footer->email }}">
    </div>

    <h5>Social Media</h5>
    <div class="mb-3">
        <label>Facebook</label>
        <input type="url" name="facebook" class="form-control" value="{{ $footer->facebook }}">
    </div>
    <div class="mb-3">
        <label>LinkedIn</label>
        <input type="url" name="linkedin" class="form-control" value="{{ $footer->linkedin }}">
    </div>
    <div class="mb-3">
        <label>Youtube</label>
        <input type="url" name="youtube" class="form-control" value="{{ $footer->youtube }}">
    </div>
    <div class="mb-3">
        <label>Instagram</label>
        <input type="url" name="instagram" class="form-control" value="{{ $footer->instagram }}">
    </div>

    <button class="btn btn-success">Update Footer</button>
</form>
@endsection
