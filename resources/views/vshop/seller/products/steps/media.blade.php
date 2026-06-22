@extends('vshop.seller.main')

@section('content')

<style>

.btn-primary {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%) !important;
    border: 0;
}

.row{
    margin-left:0 !important;
    margin-right:0 !important;
}

/* ================= MAIN CONTENT ================= */
.main-content{
    padding-left:15px;
    padding-right:15px;
}

/* ================= CARD ================= */
.card{
    width:100%;
}

/* ================= IMAGE BOX ================= */
.img-box{
    position:relative;
    display:inline-block;
    margin:8px;
}

.img-box img,
.img-box video{
    width:100px;
    height:90px;
    object-fit:cover;
    border-radius:8px;
    border:1px solid #ddd;
}

/* ================= DELETE CROSS ================= */
.remove-img,
.remove-thumb{
    position:absolute;
    top:-6px;
    right:-6px;
    width:22px;
    height:22px;
    background:#ff0000;
    color:#fff;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    z-index:999;
    font-size:14px;
    font-weight:700;
    border:2px solid #fff;
    box-shadow:0 2px 6px rgba(0,0,0,0.25);
    line-height:1;
}

/* ================= VIDEO ================= */
.img-box video{
    width:100px;
    height:90px;
    object-fit:cover;
    border-radius:8px;
}

/* ================= STATUS ================= */
.status-wrapper{
    position:relative;
    margin:25px 0;
}

.status-line{
    position:absolute;
    top:18px;
    left:0;
    width:100%;
    height:3px;
    background:#ddd;
    z-index:1;
}

.status-line-active{
    position:absolute;
    top:18px;
    left:0;
    height:3px;
    background:#0d6efd;
    z-index:2;
}

.status-step{
    position:relative;
    z-index:3;
    flex:1;
}

.status-circle{
    width:38px;
    height:38px;
    border-radius:50%;
    background:#e5e7eb;
    color:#555;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:auto;
    font-weight:700;
}

.status-circle.active{
    background:#0d6efd;
    color:#fff;
}

.status-circle.danger{
    background:#dc3545;
    color:#fff;
}

.status-label{
    margin-top:8px;
    font-size:13px;
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
.top-back-heading-sec h5 {
    color: #0f172a !important;
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

</style>

<div class="container-fluid py-4">
<div class="marketplace-wrapper">

{{-- ================= MAIN ================= --}}
<div class="marketplace-left">



<form id="mediaForm"
action="{{ $product->id
? route('seller.products.media.update',$product->id)
: route('seller.products.media.save') }}"
enctype="multipart/form-data">

@csrf

<div class="card shadow-sm p-4">
<div class="d-flex justify-content-between align-items-center mb-4">
    
    <div class="d-flex top-back-heading-sec">
      <button type="button"
        onclick="window.location.href='{{ request()->route('product') 
            ? route('seller.products.basic.edit', request()->route('product'))
            : route('seller.products.basic') }}'"
        class="nav-btn">

    <i class="fa-solid fa-arrow-left"></i>

</button>
        <h5 class="text-primary mb-0">Images / Videos</h5>
    </div>

    @php
        $backUrl = (isset($product) && $product->id)
            ? route('seller.products.basic.edit', $product->id)
            : route('seller.products.basic');
    @endphp

   

</div>
@if(isset($product))
    @include('vshop.seller.partials.draft-status', ['product' => $product])
@endif


{{-- ================= THUMB ================= --}}
<div class="mb-4">

<label class="fw-bold">
Thumbnail Image
</label>

<input type="file"
name="thumbnail"
class="form-control"
id="thumbInput">
<small id="thumbError" class="text-danger d-block"></small>
<div id="thumbPreview" class="mt-3">

@if(!empty($product->thumbnail))

<div class="img-box">

<img src="{{ asset($product->thumbnail) }}">

<span class="remove-thumb">
×
</span>

</div>

@endif

</div>

</div>


{{-- ================= GALLERY ================= --}}
<div class="mb-4">

<label class="fw-bold">
Gallery Images
</label>

<input type="file"
name="images[]"
id="imageInput"
multiple
class="form-control">

<small class="text-muted">
Max 4 media (images + videos combined)
</small>

<small id="mediaError"
class="text-danger d-block">
</small>

<div id="preview"
class="d-flex flex-wrap mt-3">

@php
$images = is_array($product->images)
? $product->images
: json_decode($product->images,true);
@endphp

@if(!empty($images))

@foreach($images as $img)

<div class="img-box existing">

<img src="{{ asset($img) }}">

<span class="remove-img"
data-path="{{ $img }}">
×
</span>

<input type="hidden"
name="existing_images[]"
value="{{ $img }}">

</div>

@endforeach

@endif

</div>

</div>


{{-- ================= VIDEOS ================= --}}
<div class="mb-4">

<div class="d-flex justify-content-between align-items-center">

<label class="fw-bold">
YouTube Videos
</label>

<div>

<button type="button"
id="addVideo"
class="btn btn-success btn-sm">
+
</button>

<button type="button"
id="removeVideo"
class="btn btn-danger btn-sm">
−
</button>

</div>

</div>

<div id="videoWrapper">

@php
$videos = is_array($product->videos)
? $product->videos
: json_decode($product->videos, true);
@endphp

@if(!empty($videos))

@foreach($videos as $vid)

<div class="video-box mb-2 d-flex gap-2">

<input type="text"
name="video_urls[]"
value="{{ $vid }}"
class="form-control video-input">


</div>

@endforeach

@else

<div class="video-box mb-2 d-flex gap-2">

<input type="text"
name="video_urls[]"
class="form-control video-input">

</div>

@endif

</div>

</div>


<div class="text-end">

<button class="btn btn-primary px-5">
Save & Continue →
</button>

</div>

</div>

</form>

</div>


{{-- ================= RIGHT SIDEBAR ================= --}}
<div class="marketplace-right">

@include(
'vshop.seller.partials.product-progress',
['product'=>$product]
)

</div>

</div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script>

let selectedFiles = [];


/* =========================================================
| COMMON VALIDATION
========================================================= */

function validateMediaSection(){

    let existingThumb = $('#thumbPreview .img-box').length;
    let newThumb = $('#thumbInput')[0].files.length;

    let thumbTotal = existingThumb + newThumb;

    let existingImages = $('#preview .img-box.existing').length;
    let newImages = $('#preview .img-box.new').length;

    let totalImages = existingImages + newImages;

    let totalVideos = $('.video-input').filter(function(){
        return $(this).val().trim() !== '';
    }).length;

    $('#mediaError').hide().text('');

    // ✅ THUMB REQUIRED
    if(thumbTotal <= 0){
        $('#mediaError').text('Thumbnail is required').show();
        return false;
    }

    // ✅ AT LEAST 1 MEDIA
    if(totalImages === 0 && totalVideos === 0){
        $('#mediaError').text('Add at least 1 image or video').show();
        return false;
    }

    // ✅ IMAGE LIMIT ONLY
    if(totalImages > 4){
        $('#mediaError').text('Maximum 4 images allowed').show();
        return false;
    }

    return true;
}


/* =========================================================
| THUMB CHANGE
========================================================= */

$('#thumbInput').on('change', function(){

    let file = this.files[0];

    if(!file) return;

    if(
        ![
            'image/jpeg',
            'image/png',
            'image/jpg'
        ].includes(file.type)
    ){
        $('#mediaError').text('Only JPG / PNG allowed').show();
        this.value='';
        return;
    }

    let reader = new FileReader();

    reader.onload = e => {

        $('#thumbPreview').html(`
            <div class="img-box">
                <img src="${e.target.result}">
                <span class="remove-thumb">×</span>
            </div>
        `);

        validateMediaSection();

    };

    reader.readAsDataURL(file);

});


/* =========================================================
| REMOVE THUMB
========================================================= */

$(document).on('click','.remove-thumb',function(){
    $('#thumbPreview').html('');
    $('#thumbInput').val('');
    validateMediaSection();
});


/* =========================================================
| GALLERY ADD (🔥 FIXED)
========================================================= */

$('#imageInput').on('change', function(){

    let files = Array.from(this.files);

    let existingCount = $('#preview .img-box.existing').length;
    let currentNew = selectedFiles.length;

    /* ✅ VIDEO REMOVED FROM COUNT */
    if(existingCount + currentNew + files.length > 4){

        $('#mediaError')
        .text("Maximum 4 images allowed")
        .show();

        this.value='';
        return;
    }

    files.forEach(file => {

        if(
            ![
                'image/jpeg',
                'image/png',
                'image/jpg'
            ].includes(file.type)
        ){
            $('#mediaError').text('Only image files allowed').show();
            return;
        }

        selectedFiles.push(file);

        let reader = new FileReader();

        reader.onload = e => {

            let html = `
                <div class="img-box new">
                    <img src="${e.target.result}">
                    <span class="remove-img" data-type="new">×</span>
                </div>
            `;

            $('#preview').append(html);

            validateMediaSection();

        };

        reader.readAsDataURL(file);

    });

    this.value='';

});


/* =========================================================
| REMOVE IMAGE
========================================================= */

$(document).on('click','.remove-img',function(){

    let box = $(this).closest('.img-box');

    if(box.hasClass('existing')){
        box.remove();
    } else {
        let allNewBoxes = $('#preview .img-box.new');
        let index = allNewBoxes.index(box);

        if(index > -1){
            selectedFiles.splice(index,1);
        }

        box.remove();
    }

    validateMediaSection();

});


/* =========================================================
| ADD VIDEO URL (🔥 FIXED - NO LIMIT)
========================================================= */

$('#addVideo').click(function(){

    $('#videoWrapper').append(`
        <div class="video-box mb-2">
            <input type="text"
            name="video_urls[]"
            class="form-control video-input">
        </div>
    `);

});


/* =========================================================
| REMOVE VIDEO URL
========================================================= */

$('#removeVideo').click(function(){
    $('#videoWrapper .video-box').last().remove();
    validateMediaSection();
});


/* =========================================================
| VIDEO INPUT VALIDATION
========================================================= */

$(document).on('input','.video-input',function(){
    validateMediaSection();
});


/* =========================================================
| YOUTUBE PREVIEW
========================================================= */

$(document).on('blur','.video-input',function(){

    let url = $(this).val();

    $(this).parent().find('iframe').remove();

    if(!url) return;

    let id = '';

    if(url.includes('v=')){
        id = url.split('v=')[1];
        if(id.includes('&')){
            id = id.split('&')[0];
        }
    }
    else if(url.includes('youtu.be/')){
        id = url.split('youtu.be/')[1];
    }

    if(id){
        $(this).parent().append(`
            <iframe
            src="https://www.youtube.com/embed/${id}"
            width="200"
            height="120"
            frameborder="0"
            allowfullscreen>
            </iframe>
        `);
    }

});


/* =========================================================
| FINAL SUBMIT
========================================================= */

$('#mediaForm').submit(function(e){

    e.preventDefault();

    if(!validateMediaSection()){
        return;
    }

    let formData = new FormData();

    formData.append('_token',$('input[name="_token"]').val());

    if($('#thumbInput')[0].files[0]){
        formData.append('thumbnail',$('#thumbInput')[0].files[0]);
    }

    selectedFiles.forEach(file=>{
        formData.append('images[]',file);
    });

    $('#preview .img-box.existing input').each(function(){
        formData.append('existing_images[]',$(this).val());
    });

    $('.video-input').each(function(){
        formData.append('video_urls[]',$(this).val());
    });

    $.ajax({
        url: $('#mediaForm').attr('action'),
        method:'POST',
        data:formData,
        processData:false,
        contentType:false,

        beforeSend:function(){
            $('#mediaError').hide().text('');
            $('button[type="submit"]').prop('disabled', true).html('Saving...');
        },

        success:function(res){

            $('button[type="submit"]').prop('disabled', false).html('Save & Continue →');

            if(res.status){
                window.location.href = res.redirect;
            }else{
                $('#mediaError').text(res.error || 'Something went wrong').show();
            }

        },

        error:function(xhr){

            $('button[type="submit"]').prop('disabled', false).html('Save & Continue →');

            let errorMsg = 'Please fill all required fields';

            if(xhr.responseJSON && xhr.responseJSON.errors){
                let errors = xhr.responseJSON.errors;
                errorMsg = Object.values(errors)[0][0];
            }

            $('#mediaError').text(errorMsg).show();

        }

    });

});


$(document).ready(function(){
    validateMediaSection();
});

</script>

@endsection