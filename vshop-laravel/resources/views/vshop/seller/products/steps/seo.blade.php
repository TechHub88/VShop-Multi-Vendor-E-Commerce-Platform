@extends('vshop.seller.main')
<style>
    .nav-btn {
        border: 1px solid #004392;
        color: #004392;
        background: transparent;
        border-radius: 5px;
        width: 35px;
        height: 38px;
    }
    .bulk-btn {
        background-color : #004392 !important;
        border-color: #004392 !important;
        color: #fff !important;
        padding: 8px 8px !important;
        height: 36px !important;
    }
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
        animation: glowing 1500ms infinite;
        background: #fff !important;
        padding: 0 !important;
        border: 1px solid #004392 !important;
    }
    .ai-btn img {
        width: 60px;
        height: 60px;
    }
    
    @-webkit-keyframes glowing {
        0% { background: linear-gradient(180deg, #d7f1ff 0%, #9fd0ff 25%, #7bb8ff 55%, #6aa8ff 75%, #8fd4ff 100%); -webkit-box-shadow: 0 0 3px rgb(120 190 255 / 80%); }
        50% { background: linear-gradient(180deg, #d7f1ff 0%, #9fd0ff 25%, #7bb8ff 55%, #6aa8ff 75%, #8fd4ff 100%); -webkit-box-shadow: 0 0 40px rgb(120 190 255 / 80%); }
        100% { background: linear-gradient(180deg, #d7f1ff 0%, #9fd0ff 25%, #7bb8ff 55%, #6aa8ff 75%, #8fd4ff 100%); -webkit-box-shadow: 0 0 3px rgb(120 190 255 / 80%); }
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
    .top-back-heading-sec {
        align-items: center;
        gap: 10px;
    }
    .marketplace-wrapper {
        display: flex;
        gap: 25px;
        align-items: flex-start;
    }
    .marketplace-left {
        flex: 1;
    }
    .marketplace-right {
        width: 300px;
    }
    .top-back-heading-sec h5 {
        color: #0f172a !important;
    }
    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%) !important;
        border: 0 !important;
    }
    .btn-outline-primary {
        border-color: #233d78 !important;
        color: #233d78;
    }
    .btn-outline-primary:hover {
        color: #fff !important;
    }
</style>

@section('content')

<div class="container-fluid py-4">
<div class="marketplace-wrapper">

{{-- LEFT --}}
<div class="marketplace-left">
    
    

<form method="POST"
action="{{ $product->id 
    ? route('seller.products.seo.update',$product->id) 
    : route('seller.products.seo.save') }}">

@csrf

{{-- HIDDEN PRODUCT ID FOR NEW PRODUCTS --}}
@if(!isset($product->id) || !$product->id)
    <input type="hidden" name="temp_product_id" value="new">
@endif

<div class="card shadow-sm p-4">

<div class="d-flex justify-content-between align-items-center mb-4">

    @php
        $backUrl = (isset($product) && !empty($product->id))
            ? route('seller.products.specifications.edit', $product->id)
            : route('seller.products.specifications');
    @endphp

    {{-- LEFT: HEADING --}}
    <div class="d-flex top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <h5 class="text-primary mb-0">SEO</h5>
    </div>

    {{-- RIGHT ACTIONS --}}
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary btn-sm ai-btn mt-0" onclick="generateSeoAIContent(this)">
            <img src="https://vshop.zelofx.com/public/images/vAi-button-removebg-preview.png" alt="Boost Now" >
            <!--<span>V-AI<br>Generate</span>-->
        </button>
    </div>

</div>

@if(isset($product) && $product->id)
    @include('vshop.seller.partials.draft-status', ['product' => $product])
@endif

{{-- SEO TAGS --}}
<div class="mb-3">
<div class="label-row">
<label>SEO Tags <span class="text-danger">*</span></label>
</div>

<input type="text"
name="seo_tags"
value="{{ old('seo_tags', $product->seo_tags ?? '') }}"
class="form-control mandatory-field @error('seo_tags') is-invalid @enderror"
placeholder="Tag1, Tag2">

@error('seo_tags')
<small class="text-danger validation-error">{{ $message }}</small>
@enderror
</div>


{{-- META TITLE --}}
<div class="mb-3">
<div class="label-row">
<label>Meta Title <span class="text-danger">*</span></label>
</div>

<input type="text"
name="meta_title"
value="{{ old('meta_title', $product->meta_title ?? '') }}"
class="form-control mandatory-field @error('meta_title') is-invalid @enderror"
placeholder="Meta Title">

@error('meta_title')
<small class="text-danger validation-error">{{ $message }}</small>
@enderror
</div>


{{-- META KEYWORDS --}}
<div class="mb-3">
<div class="label-row">
<label>Meta Keywords <span class="text-danger">*</span></label>
</div>

<input type="text"
name="meta_keywords"
value="{{ old('meta_keywords', $product->meta_keywords ?? '') }}"
class="form-control mandatory-field @error('meta_keywords') is-invalid @enderror"
placeholder="Keywords">

@error('meta_keywords')
<small class="text-danger validation-error">{{ $message }}</small>
@enderror
</div>


{{-- META DESCRIPTION --}}
<div class="mb-4">
<div class="label-row">
<label>Meta Description <span class="text-danger">*</span></label>
</div>

<textarea name="meta_description"
class="form-control editor mandatory-field @error('meta_description') is-invalid @enderror"
rows="4">{!! old('meta_description', $product->meta_description ?? '') !!}</textarea>

@error('meta_description')
<small class="text-danger validation-error">{{ $message }}</small>
@enderror
</div>


{{-- BULLET POINTS --}}
<div class="mb-4">
<div class="label-row">
<label>Product Bullet Points <span class="text-danger">*</span></label>
</div>

<textarea name="bullet_points"
class="form-control editor mandatory-field @error('bullet_points') is-invalid @enderror"
rows="5">{!! old('bullet_points', $product->bullet_points ?? '') !!}</textarea>

@error('bullet_points')
<small class="text-danger validation-error">{{ $message }}</small>
@enderror
</div>


{{-- TARGET AUDIENCE --}}
<div class="mb-4">
<div class="label-row">
<label>Target Audience <span class="text-danger">*</span></label>
</div>

<select name="target_audience"
        class="form-control mandatory-field @error('target_audience') is-invalid @enderror">

    <option value="">Select Target Audience</option>

    <option value="kids"
        {{ old('target_audience', $product->target_audience ?? '') == 'kids' ? 'selected' : '' }}>
        Kids
    </option>

    <option value="students"
        {{ old('target_audience', $product->target_audience ?? '') == 'students' ? 'selected' : '' }}>
        Students
    </option>

    <option value="professionals"
        {{ old('target_audience', $product->target_audience ?? '') == 'professionals' ? 'selected' : '' }}>
        Professionals
    </option>

    <option value="seniors"
        {{ old('target_audience', $product->target_audience ?? '') == 'seniors' ? 'selected' : '' }}>
        Seniors
    </option>

    <option value="all"
        {{ old('target_audience', $product->target_audience ?? '') == 'all' ? 'selected' : '' }}>
        All
    </option>

</select>

@error('target_audience')
<small class="text-danger validation-error">
    {{ $message }}
</small>
@enderror
</div>


<div class="text-center mt-4">
<button type="submit" class="btn btn-primary px-5">Save</button>

<a href="{{ $backUrl }}" class="btn btn-outline-primary ms-2" style="color:#233d78;">
Cancel
</a>
</div>

</div>

</form>

</div>


{{-- RIGHT --}}
<div class="marketplace-right">
@include('vshop.seller.partials.product-progress', ['product' => $product])
</div>

</div>
</div>

{{-- CKEDITOR --}}
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script>

let editors = [];

/* =========================================
   DOM READY
========================================= */
document.addEventListener("DOMContentLoaded", function () {

    // =====================================
    // NORMAL INPUT FIELDS
    // =====================================
    document.querySelectorAll('.mandatory-field').forEach(function(field){

        // INPUT
        field.addEventListener('input', function(){

            if(this.value.trim() !== ''){

                this.classList.remove('is-invalid');

                let error =
                    this.parentElement.querySelector('.validation-error');

                if(error){
                    error.style.display = 'none';
                }

            }

        });

        // SELECT
        field.addEventListener('change', function(){

            if(this.value.trim() !== ''){

                this.classList.remove('is-invalid');

                let error =
                    this.parentElement.querySelector('.validation-error');

                if(error){
                    error.style.display = 'none';
                }

            }

        });

    });

});


/* =========================================
   CKEDITOR INIT
========================================= */
document.querySelectorAll('.editor').forEach(el => {

    ClassicEditor.create(el)

    .then(editor => {

        // STORE
        editors.push(editor);

        // INITIAL ERROR STATE
        if(el.classList.contains('is-invalid')){

            editor.ui.view.element
                .classList.add('invalid-editor');
        }

        // LIVE CHANGE
        editor.model.document.on('change:data', () => {

            // IMPORTANT
            el.value = editor.getData();

            let data = editor.getData()
                .replace(/<[^>]*>/g, '')
                .replace(/&nbsp;/g, '')
                .replace(/\s+/g, '')
                .trim();

            if(data !== ''){

                el.classList.remove('is-invalid');

                editor.ui.view.element
                    .classList.remove('invalid-editor');

                let error =
                    el.parentElement.querySelector('.validation-error');

                if(error){
                    error.style.display = 'none';
                }

            }else{

                el.classList.add('is-invalid');

                editor.ui.view.element
                    .classList.add('invalid-editor');
            }

        });

    })

    .catch(err => console.error(err));

});


/* =========================================
   FORM SUBMIT
========================================= */
document.querySelector('form').addEventListener('submit', function(e){
    
    // SYNC CKEDITOR DATA
    editors.forEach(editor => {

        editor.updateSourceElement();

    });
    
    // DEBUG: Log form data before submission
    console.log('Submitting form data:');
    console.log({
        seo_tags: document.querySelector('[name="seo_tags"]')?.value,
        meta_title: document.querySelector('[name="meta_title"]')?.value,
        meta_keywords: document.querySelector('[name="meta_keywords"]')?.value,
        meta_description: document.querySelector('[name="meta_description"]')?.value,
        bullet_points: document.querySelector('[name="bullet_points"]')?.value,
        target_audience: document.querySelector('[name="target_audience"]')?.value
    });

});

function generateSeoAIContent(btn) {
    let productName = {!! json_encode($product->name ?? '') !!};
    
    if (!productName) {
        alert('Product name is missing. Please save basic details first.');
        return;
    }

    let originalText = $(btn).html();
    $(btn).html('<span>Loading...</span>').prop('disabled', true);

    $.ajax({
        url: "{{ route('seller.products.seo.ai.generate') }}",
        type: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            name: productName
        },
        success: function(response) {
            $(btn).html(originalText).prop('disabled', false);
            if (response.success) {
                if (response.seo_tags) $('input[name="seo_tags"]').val(response.seo_tags);
                if (response.meta_title) $('input[name="meta_title"]').val(response.meta_title);
                if (response.meta_keywords) $('input[name="meta_keywords"]').val(response.meta_keywords);
                if (response.target_audience) $('select[name="target_audience"]').val(response.target_audience);
                
                // For CKEditor fields
                if (response.meta_description) {
                    let mdEditor = editors.find(e => e.sourceElement.name === 'meta_description');
                    if (mdEditor) mdEditor.setData(response.meta_description);
                    else $('textarea[name="meta_description"]').val(response.meta_description);
                }
                
                if (response.bullet_points) {
                    let bpEditor = editors.find(e => e.sourceElement.name === 'bullet_points');
                    if (bpEditor) bpEditor.setData(response.bullet_points);
                    else $('textarea[name="bullet_points"]').val(response.bullet_points);
                }
                
                // trigger change to remove errors if any
                $('input[name="seo_tags"], input[name="meta_title"], input[name="meta_keywords"]').trigger('input');
            } else {
                alert(response.message || 'Error generating content.');
            }
        },
        error: function(xhr) {
            $(btn).html(originalText).prop('disabled', false);
            let errMsg = 'Failed to connect to the server. Please check the API key or try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errMsg = xhr.responseJSON.message;
            }
            alert(errMsg);
        }
    });
}


</script>

<style>
.label-row{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:6px;
font-weight:600;
}

.v-ai-btn{
font-size:11px;
background:#ff4d4f;
color:#fff;
border:none;
padding:2px 8px;
border-radius:4px;
cursor:pointer;
}

.v-ai-btn:hover{
background:#e43d3f;
}

.editor{
min-height:180px;
}

.is-invalid{
    border:1px solid red !important;
}

/* CKEDITOR RED BORDER */
.ck-editor.invalid-editor .ck-editor__editable{
    border:1px solid red !important;
}
/* ERROR MESSAGE */
.validation-error{
display:block;
margin-top:4px;
font-size:13px;
}
</style>

@endsection