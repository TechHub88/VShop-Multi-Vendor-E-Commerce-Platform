@extends('vshop.seller.main')

<style>
    .ai-btn {
        display: flex !important;
        align-items: center !important;
        font-size: 9px !important; 
        font-weight: 700 !important;
        gap: 6px !important;
        white-space: nowrap;
        margin-top: 28px;
        padding: 8px 18px !important;
        border-radius: 60px !important;
        border: 0 !important;
        background: linear-gradient(180deg, #d7f1ff 0%, #9fd0ff 25%, #7bb8ff 55%, #6aa8ff 75%, #8fd4ff 100%);
        color: #fff !important;
        letter-spacing: 2px;
        line-height: 14px;
        text-transform: uppercase;
    }
    .ai-btn:hover {
        box-shadow: inset 0 4px 10px rgba(255, 255, 255, 0.6), inset 0 -6px 12px rgba(0, 0, 0, 0.12), 0 0 20px rgba(120, 190, 255, 0.8);
    }
    .ai-btn svg {
        width: 22px;
        height: 22px;
    }
    .ai-btn i {
        color: orange;
        font-size: 14px;
    }
    .nav-btn {
        border: 1px solid #004392;
        color: #004392;
        background: transparent;
        border-radius: 5px;
        font-size: 4px;
        width: 35px;
        height: 38px;
    }
    .top-back-heading-sec {
        align-items: center;
        gap: 10px;
    }
    .edit-btn {
        width: 32px;
        height: 32px;
    }
    .top-back-heading-sec h5 {
        color: #0f172a !important;
    }
    .btn-save {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%) !important;
}
</style>

@section('content')
<div class="container-fluid py-3">
@include('vshop.seller.partials.marketplace-header',['title'=>'SEO'])
<div class="marketplace-wrapper">

{{-- LEFT --}}
<div class="marketplace-left">

<div class="d-flex justify-content-between align-items-center mb-4">
<div class="d-flex top-back-heading-sec mb-4">
  <button onclick="window.location.href='{{ route('seller.marketplace.template') }}'" 
        class="nav-btn">
    <i class="fa-solid fa-arrow-left"></i>
</button>
    <h5 class="">
    {{ isset($seo) ? 'Edit SEO' : 'SEO Management' }}
    </h5>
</div>


<div class="header-actions">
<a href="{{ route('seller.marketplace.seo.list') }}" class="">
<i class="bi bi-list-ul"></i>
</a>
</div>

</div>


<div class="market-card">

<form method="POST"
action="{{ route('seller.marketplace.seo.save') }}"
onsubmit="return validateSEO()">

@csrf

<input type="hidden" name="id" value="{{ $seo->id ?? '' }}">

{{-- TITLE --}}
<div class="mb-3">
<label class="form-label required">
Home Page Title <span class="text-danger">*</span>
</label>

<input type="text"
name="home_title"
class="form-control"
value="{{ old('home_title', $seo->home_title ?? '') }}">

@error('home_title')
<small class="text-danger">{{ $message }}</small>
@enderror
</div>

{{-- DESCRIPTION --}}
<div class="mb-3">
<label class="form-label required">
Meta Description <span class="text-danger">*</span>
</label>

<textarea name="meta_description"
class="form-control"
rows="3">{{ old('meta_description', $seo->meta_description ?? '') }}</textarea>

@error('meta_description')
<small class="text-danger">{{ $message }}</small>
@enderror
</div>

{{-- KEYWORDS --}}
<div class="mb-3">
<label class="form-label">
Meta Keywords (comma separated)
</label>

<textarea name="meta_keywords"
id="keywords"
class="form-control"
placeholder="e.g. business, marketing, growth">{{ old('meta_keywords', $seo->meta_keywords ?? '') }}</textarea>

<small class="text-danger" id="keywordError"></small>
</div>

{{-- HASHTAGS --}}
<div class="mb-3">

<label class="form-label d-flex justify-content-between align-items-center">

<span>Search Hashtags</span>

<div class="d-flex align-items-center gap-2">
<button type="button"
class="btn btn-sm btn-outline-primary ai-btn"
onclick="generateAI()">
<!--    <i class="fa-solid fa-bolt"></i>-->
<!--V-AI-->
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 64 64" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M4 35c18.621 4.21 20 23 20 23s1.379-18.79 20-23c-18.621-4.21-20-23-20-23S22.621 30.79 4 35zM38 48c10.241 2.2 11 12 11 12s.759-9.8 11-12c-10.241-2.2-11-12-11-12s-.759 9.8-11 12zM31 16c10.241 2.2 11 12 11 12s.759-9.8 11-12C42.759 13.8 42 4 42 4s-.759 9.8-11 12z" fill="#ffffff" opacity="1" data-original="#000000"></path></g></svg>
                                    <span>V-AI<br>Generate</span>
</button>
</div>

</label>

<textarea name="search_hashtags"
id="hashtags"
class="form-control"
placeholder="#business,#growth,#marketing">{{ old('search_hashtags', $seo->search_hashtags ?? '') }}</textarea>

<small class="text-danger" id="hashtagError"></small>
</div>

<button class="btn-save">
{{ isset($seo) ? 'Update SEO' : 'Save SEO' }}
</button>

</form>

</div>

</div>

{{-- RIGHT --}}
<div class="marketplace-right">
@include('vshop.seller.partials.marketplace-progress')
</div>

</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</div>
<style>

.marketplace-wrapper{
display:flex;
gap:25px;
}

.marketplace-left{flex:1;}
.marketplace-right{
width:300px;
position:sticky;
top:20px;
}

.market-card{
background:#fff;
padding:25px;
border-radius:12px;
box-shadow:0 3px 12px rgba(0,0,0,.05);
}

.btn-save{
background:#0b4da1;
color:#fff;
border:none;
padding:10px 28px;
border-radius:6px;
cursor:pointer;
}

.btn-save:hover{
background:#083b7c;
}

.icon-btn{
width:38px;
height:38px;
border-radius:8px;
background:#0b4da1;
color:#fff;
display:flex;
align-items:center;
justify-content:center;
text-decoration:none;
}

/* REQUIRED STAR STYLE */
.form-label.required span{
font-weight:700;
}

.text-danger{
font-size:12px;
}

.form-control:focus{
border-color:#0b4da1;
box-shadow:0 0 0 0.15rem rgba(11,77,161,.15);
}

.header-actions{
display:flex;
align-items:center;
gap:10px;
}

</style>

<script>

function validateSEO(){

let keywords = document.getElementById('keywords').value.trim();
let hashtags = document.getElementById('hashtags').value.trim();

document.getElementById('keywordError').innerText = '';
document.getElementById('hashtagError').innerText = '';

// keyword validation
if(keywords.includes(';')){
    document.getElementById('keywordError').innerText =
    "Only comma separated values allowed";
    return false;
}

// hashtag validation
if(hashtags){
    let tags = hashtags.split(',');

    for(let t of tags){
        t = t.trim();
        if(t.length > 0 && !t.startsWith('#')){
            document.getElementById('hashtagError').innerText =
            "Each tag must start with #";
            return false;
        }
    }
}

return true;
}

// AI generator
function generateAI(){
document.getElementById('hashtags').value =
"#business,#growth,#marketing,#startup,#branding";
}

</script>

<script>
/* AUTO HIDE ALERTS AFTER 5 SECONDS */
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(el => {
        el.style.transition = "0.5s";
        el.style.opacity = "0";
        setTimeout(() => el.remove(), 500);
    });
}, 5000);
</script>

@endsection