@extends('layouts.vertical', ['title' => 'Add Footer'])

@section('content')
<h3>Add New Footer</h3>

<form action="{{ route('admin.footers.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <h5>Footer Logo</h5>
    <div class="mb-3">
        <label>Logo</label>
        <input type="file" name="footer_logo" class="form-control" id="footerLogoInput" required>
        @error('footer_logo')
            <span class="text-danger">{{ $message }}</span>
        @enderror
        <img id="logoPreview" class="mt-2" style="width:120px;height:80px;object-fit:cover;display:none;">
    </div>

    <h5>Quick Links</h5>
    <div class="mb-3">
        <label>Home</label>
        <input type="text" name="quick_home" class="form-control">
    </div>
    <div class="mb-3">
        <label>About</label>
        <input type="text" name="quick_about" class="form-control">
    </div>
    <div class="mb-3">
        <label>FAQ</label>
        <input type="text" name="quick_faq" class="form-control">
    </div>
    <div class="mb-3">
        <label>Testimonials</label>
        <input type="text" name="quick_testimonials" class="form-control">
    </div>
    <div class="mb-3">
        <label>Blogs</label>
        <input type="text" name="quick_blogs" class="form-control">
    </div>

    <h5>Company</h5>
    <div class="mb-3">
        <label>Privacy Policy</label>
        <input type="text" name="company_privacy_policy" class="form-control">
    </div>
    <div class="mb-3">
        <label>Return Policy</label>
        <input type="text" name="company_return_policy" class="form-control">
    </div>
    <div class="mb-3">
        <label>Terms & Conditions</label>
        <input type="text" name="company_terms" class="form-control">
    </div>

    <h5>Category</h5>
    <div class="mb-3">
        <label>Corporate</label>
        <input type="text" name="category_corporate" class="form-control">
    </div>
    <div class="mb-3">
        <label>Work</label>
        <input type="text" name="category_work" class="form-control">
    </div>
    <div class="mb-3">
        <label>Healthcare</label>
        <input type="text" name="category_healthcare" class="form-control">
    </div>
    <div class="mb-3">
        <label>School</label>
        <input type="text" name="category_school" class="form-control">
    </div>

    <h5>Contact Us</h5>
    <div class="mb-3">
        <label>Address</label>
        <input type="text" name="address" class="form-control">
    </div>
    <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control">
    </div>
    <div class="mb-3">
        <label>Fax</label>
        <input type="text" name="fax" class="form-control">
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control">
    </div>

    <h5>Social Media Icons</h5>
    <div class="mb-3">
        <label>Upload Icons (Facebook, LinkedIn, Youtube, Instagram etc.)</label>
        <input type="file" name="social_icons[]" class="form-control" id="socialIconsInput" multiple>
        @error('social_icons.*')
            <span class="text-danger">{{ $message }}</span>
        @enderror
        <div id="iconsPreview" class="mt-2" style="display:flex; gap:10px; flex-wrap:wrap;"></div>
    </div>

    <button class="btn btn-primary">Save Footer</button>
</form>
@endsection

@section('scripts')
<script>
    const footerLogoInput = document.getElementById('footerLogoInput');
    const logoPreview = document.getElementById('logoPreview');

    footerLogoInput.addEventListener('change', function() {
        const file = this.files[0];
        if(file){
            const reader = new FileReader();
            reader.onload = function(e){
                logoPreview.src = e.target.result;
                logoPreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    const socialIconsInput = document.getElementById('socialIconsInput');
    const iconsPreview = document.getElementById('iconsPreview');

    socialIconsInput.addEventListener('change', function() {
        iconsPreview.innerHTML = '';
        const files = this.files;
        if(files){
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e){
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '60px';
                    img.style.height = '60px';
                    img.style.objectFit = 'cover';
                    img.style.border = '1px solid #ccc';
                    img.style.borderRadius = '5px';
                    iconsPreview.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        }
    });
</script>
@endsection
