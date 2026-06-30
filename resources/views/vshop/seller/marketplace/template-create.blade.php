@extends('vshop.seller.main')
<style>

    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
</style>

@section('content')

<div class="container-fluid py-3">

@include('vshop.seller.partials.marketplace-header',[
'title'=>'Add Website Template'
])

<form method="POST"
action="{{ route('seller.marketplace.template.store') }}"
enctype="multipart/form-data"
id="templateForm">
@csrf

<div class="marketplace-wrapper">

{{-- LEFT --}}
<div class="marketplace-left">

<div class="premium-form-container mb-4">
<div class="header-action-row">
    <div class="top-back-heading-sec">
        <a href="{{ url('seller/marketplace/template') }}" class="premium-nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h5>Add Template</h5>
    </div>

    <div>
        <a href="{{ route('seller.marketplace.template') }}" class="premium-list-btn" title="Template List">
            <i class="bi bi-list"></i>
        </a>
    </div>
</div>

@if(session('success'))

<div class="alert alert-success" id="successMessage">
{{ session('success') }}
</div>

@endif

{{-- NAME --}}
<div class="mb-3">
<label>Name <span class="text-danger">*</span></label>

<input type="text" name="name"
class="form-control @error('name') is-invalid @enderror"
value="{{ old('name') }}" id="name">

@error('name')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

{{-- APPLICATION TYPE --}}
<div class="mb-3">
<label>Application Type <span class="text-danger">*</span></label>

<input type="text" name="application_type"
class="form-control @error('application_type') is-invalid @enderror"
value="{{ old('application_type') }}" id="application_type">

@error('application_type')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

{{-- IMAGE --}}
<div class="mb-3">
<label>Preview Image <span class="text-danger">*</span></label>

<input type="file" name="preview_image"
class="form-control @error('preview_image') is-invalid @enderror"
id="preview_image"
accept=".jpg,.jpeg,.png">

<small class="text-muted">Only JPG/PNG allowed</small>

<div id="imageError" class="text-danger small mt-1 d-none"></div>

@error('preview_image')
<div class="invalid-feedback">{{ $message }}</div>
@enderror

{{-- 🔥 IMAGE PREVIEW --}}
<div id="previewBox" class="mt-3 d-none">
    <div class="img-preview-wrapper">
        <img id="previewImg">
        <span id="removeImg">×</span>
    </div>
</div>

</div>

{{-- DESCRIPTION --}}
<div class="mb-3">
<label>Description</label>

<textarea name="description"
class="form-control @error('description') is-invalid @enderror"
id="description">{{ old('description') }}</textarea>

@error('description')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

<div class="text-end mt-4">
    <button class="premium-btn-save">
        <i class="fa fa-save me-2"></i> Save Template
    </button>
</div>

</div>

</div>

{{-- RIGHT --}}
<div class="marketplace-right">
@include('vshop.seller.partials.marketplace-progress')
</div>

</div>

</form>


</div>
{{-- ================= CSS ================= --}}
<style>

.marketplace-wrapper{
display:flex;
gap:25px;
align-items:flex-start;
}

.marketplace-left{
flex: 1;
}

.marketplace-right{
    width: 300px;
}

/* 🔥 IMAGE PREVIEW */
.img-preview-wrapper{
position:relative;
width:160px;
height:110px;
}

.img-preview-wrapper img{
width:100%;
height:100%;
object-fit:cover;
border-radius:8px;
border:1px solid #ddd;
}

/* ❌ CROSS BUTTON */
#removeImg{
position:absolute;
top:-6px;
right:-6px;
background:red;
color:#fff;
width:22px;
height:22px;
font-size:13px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
cursor:pointer;
box-shadow:0 2px 6px rgba(0,0,0,0.2);
}

</style>

{{-- ================= JS ================= --}}
<script>
document.addEventListener("DOMContentLoaded", function(){

const form = document.getElementById('templateForm');

const name = document.getElementById('name');
const appType = document.getElementById('application_type');
const image = document.getElementById('preview_image');

const previewImg = document.getElementById('previewImg');
const previewBox = document.getElementById('previewBox');
const removeBtn = document.getElementById('removeImg');

const imageError = document.getElementById('imageError');

// ================= TEXT VALIDATION =================
function showError(input){
input.classList.add('is-invalid');
}

function clearError(input){
input.classList.remove('is-invalid');
}

[name, appType].forEach(el => {
el.addEventListener('input', function(){
if(this.value.trim() !== ''){
clearError(this);
}
});
});

// ================= IMAGE PREVIEW =================
image.addEventListener('change', function(){

let file = this.files[0];

if(!file) return;

let allowed = ['image/jpeg','image/png'];

if(!allowed.includes(file.type)){
this.value = '';
imageError.classList.remove('d-none');
imageError.innerText = "Only JPG & PNG allowed";
this.classList.add('is-invalid');
previewBox.classList.add('d-none');
return;
}

// preview
let reader = new FileReader();

reader.onload = function(e){
previewImg.src = e.target.result;
previewBox.classList.remove('d-none');
};

reader.readAsDataURL(file);

// clear error
imageError.classList.add('d-none');
image.classList.remove('is-invalid');

});

// ================= REMOVE IMAGE =================
removeBtn.addEventListener('click', function(){

image.value = "";
previewImg.src = "";
previewBox.classList.add('d-none');

});

// ================= FORM VALIDATION =================
form.addEventListener('submit', function(e){

let valid = true;

if(name.value.trim() === ''){
showError(name);
valid = false;
}

if(appType.value.trim() === ''){
showError(appType);
valid = false;
}

if(image.files.length === 0){
imageError.classList.remove('d-none');
imageError.innerText = "Image is required";
image.classList.add('is-invalid');
valid = false;
}

if(!valid){
e.preventDefault();
}

});

});
</script>

<script>
    // ================= AUTO HIDE SUCCESS MESSAGE =================
setTimeout(function(){

    let successBox = document.getElementById('successMessage');

    if(successBox){

        successBox.style.transition = "0.5s";
        successBox.style.opacity = "0";

        setTimeout(() => {

            successBox.remove();

        }, 500);
    }

}, 5000);
</script>

@endsection