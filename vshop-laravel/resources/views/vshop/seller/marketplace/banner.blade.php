@extends('vshop.seller.main')

<style>
    .ai-btn {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 50%;
        background: #fff;
        border: 1px solid rgba(0, 82, 204, 0.25) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer;
        box-shadow: 0 0 0 0 rgba(0, 82, 204, 0.4);
        animation: pulse-vai 2s infinite;
        transition: all 0.3s ease;
        padding: 0 !important;
        flex-shrink: 0;
        margin-top: 28px;
        overflow: hidden;
    }
    .ai-btn:hover {
        transform: scale(1.08);
        border-color: rgba(0, 82, 204, 0.5) !important;
        box-shadow: 0 4px 15px rgba(0, 82, 204, 0.3);
    }
    .ai-btn img {
        width: 38px;
        height: 38px;
        object-fit: contain;
    }
    .ai-btn svg, .ai-btn span {
        display: none !important;
    }
    @keyframes pulse-vai {
        0% { box-shadow: 0 0 0 0 rgba(0, 82, 204, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(0, 82, 204, 0); }
        100% { box-shadow: 0 0 0 0 rgba(0, 82, 204, 0); }
    }
    .marketplace-wrapper {
        display: flex;
        gap: 25px;
    }
    .marketplace-left {
        flex: 1;
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
    .nav-btn {
        border: 1px solid #004392;
        color: #004392;
        background: transparent;
        border-radius: 5px;
        font-size: 4px;
        width: 35px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
    }
    .top-back-heading-sec h5 {
        color: #0f172a !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
</style>

@section('content')

<div class="container-fluid py-3">
    
    @include('vshop.seller.partials.marketplace-header',['title'=>'Banner'])

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <div class="marketplace-wrapper">

        {{-- LEFT SIDE --}}
        <div class="marketplace-left">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex top-back-heading-sec">
                   <button onclick="window.location.href='{{ route('seller.marketplace.category') }}'" 
                            class="nav-btn">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                    <h5 class="mb-0 fw-semibold text-primary">
                        {{ !empty($banner) ? 'Edit Banner' : 'Banner Management' }}
                    </h5>
                </div>

                {{-- LIST --}}
                <a href="{{ route('seller.marketplace.banner.list') }}"
                   class="">

                    <i class="bi bi-list"></i>

                </a>

            </div>

            {{-- SUCCESS --}}
            @if(session('success'))

                <div class="alert alert-success successMessage shadow-sm">

                    {{ session('success') }}

                </div>

            @endif

            {{-- CARD --}}
            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body p-4">

                    <form method="POST"
                          action="{{ !empty($banner)
                          ? route('seller.marketplace.banner.update',$banner->id)
                          : route('seller.marketplace.banner.store') }}"
                          enctype="multipart/form-data" id="bannerForm">

                        @csrf

                        {{-- IMAGE --}}
                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Upload Banner
                                <small class="text-danger">(Multiple allowed)</small>
                            </label>

                            {{-- OLD IMAGES --}}
                            @if(!empty($banner))

                                @php
                                $images = is_array($banner->banner_image)
                                    ? $banner->banner_image
                                    : json_decode($banner->banner_image, true);
                                @endphp

                                <div id="oldPreview" class="mb-3">

                                    @foreach($images as $index => $img)

                                        <div class="preview-box">

                                            <img src="{{ asset($img) }}">

                                            <span class="remove-btn old-remove"
                                                  data-index="{{ $index }}">

                                                ×

                                            </span>

                                        </div>

                                    @endforeach

                                </div>

                            @endif

                            <input type="file"
                                   name="banner_image[]"
                                   class="form-control"
                                   multiple
                                   accept="image/png,image/jpeg"
                                   id="bannerInput">

                            <input type="hidden"
                                   name="existing_images"
                                   id="existingImagesInput"
                                   value='@json($images ?? [])'>

                            <small class="text-muted">
                                PNG/JPG Recommended Size: 1200x400px
                            </small>

                            @error('banner_image')
                                <div class="text-danger small mt-1 d-block font-weight-bold">{{ $message }}</div>
                            @enderror

                            {{-- NEW PREVIEW --}}
                            <div id="preview" class="mt-3"></div>

                        </div>

                        {{-- TITLE --}}
                        <div class="mb-4">

                            <div class="d-flex gap-2 align-items-start">
                                <div class="w-100">
                                    <label class="form-label d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">
                                            Banner Title
                                            <span class="text-danger">*</span>
                                        </span>
                                    </label>

                                    <input type="text"
                                           name="banner_title"
                                           id="banner_title"
                                           maxlength="100"
                                           class="form-control"
                                           value="{{ old('banner_title',$banner->banner_title ?? '') }}"
                                           required>

                                    <small id="titleCount" class="text-muted">
                                        0/100
                                    </small>
                                </div>
                                
                                <button type="button"
                                        class="btn ai-btn"
                                        onclick="generateAI(this)"
                                        title="V-AI Generate">
                                    <img src="{{ asset('vtravelassets/images/vai-chip-logo.png') }}?v=2" alt="V-AI" class="ai-logo">
                                    <span class="spinner-border text-primary d-none ai-spinner" role="status" aria-hidden="true" style="width: 24px; height: 24px; border-width: 3px;"></span>
                                </button>
                            </div>

                        </div>

                        {{-- SUBTITLE --}}
                        <div class="mb-4">

                            <label class="form-label d-flex justify-content-between align-items-center">

                                <span class="fw-semibold">
                                    Banner Sub Title
                                    <small class="text-muted">(Optional)</small>
                                </span>

                            </label>

                            <textarea name="banner_sub_title"
                                      id="banner_sub_title"
                                      maxlength="200"
                                      class="form-control"
                                      rows="4">{{ old('banner_sub_title',$banner->banner_sub_title ?? '') }}</textarea>

                            <small id="subCount" class="text-muted">
                                0/200
                            </small>

                        </div>

                        {{-- STATUS --}}
                        @if(!empty($banner))

                            <div class="mb-4">

                                <label class="form-label fw-semibold">
                                    Verification Status
                                </label>

                                <div>

                                    @if($banner->status == 'approved')

                                        <span class="badge bg-success px-3 py-2">
                                            Approved
                                        </span>

                                    @elseif($banner->status == 'rejected')

                                        <span class="badge bg-danger px-3 py-2">
                                            Rejected
                                        </span>

                                    @else

                                        <span class="badge bg-warning text-dark px-3 py-2">
                                            Pending
                                        </span>

                                    @endif

                                </div>

                            </div>

                        @endif

                        {{-- SUBMIT --}}
                        <button class="btn btn-success px-4 rounded-3">

                            {{ !empty($banner) ? 'Update Banner' : 'Upload Banner' }}

                        </button>

                    </form>

                </div>

            </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="marketplace-right">

            <div class="marketplace-progress-sticky">

                @include('vshop.seller.partials.marketplace-progress')

            </div>

        </div>

    </div>

</div>


<style>

.marketplace-progress-sticky{
    position: sticky;
    top: 20px;
}

.circle-btn{
    width:40px;
    height:40px;
    border-radius:50%;
    background:#eef2f7;
    display:flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    color:#333;
    transition:.3s;
    font-size:18px;
}

.circle-btn:hover{
    background:#dbe5f1;
    color:#000;
}

#preview,
#oldPreview{
    display:flex;
    flex-wrap:wrap;
    gap:12px;
}

.preview-box{
    position:relative;
    width:130px;
    height:80px;
}

.preview-box img{
    width:100%;
    height:100%;
    object-fit:cover;
    border-radius:10px;
    border:1px solid #ddd;
}

.remove-btn{
    position:absolute;
    top:-7px;
    right:-7px;
    width:22px;
    height:22px;
    border-radius:50%;
    background:#dc3545;
    color:#fff;
    font-size:13px;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
}

.successMessage{
    border-radius:12px;
}

</style>

<script src="https://cdn.jsdelivr.net/npm/nsfwjs@2.4.2/dist/nsfwjs.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

let selectedFiles = [];
let nsfwModel = null;
let isScanning = false;

$('#bannerForm').submit(function(e){
    if(isScanning) {
        e.preventDefault();
        alert("Please wait while image safety scan is in progress...");
        return false;
    }
});

// Preload the NSFWJS model
async function loadNSFWModel() {
    try {
        if (!nsfwModel) {
            // Load the model locally to guarantee availability and prevent CORS/network errors
            nsfwModel = await nsfwjs.load('/models/mobilenet_v2/');
            console.log("NSFW model loaded successfully.");
        }
    } catch (e) {
        console.error("Failed to load NSFW model:", e);
    }
}

// Automatically start loading model when page is ready
$(document).ready(function() {
    loadNSFWModel();
});

// Helper function to scan image pixels using NSFW.js
function checkImageSafety(file) {
    return new Promise((resolve) => {
        let reader = new FileReader();
        reader.onload = function(e) {
            let img = new Image();
            img.onload = async function() {
                try {
                    let predictions = await nsfwModel.classify(img);
                    console.log("NSFWJS Predictions:", predictions);
                    
                    let isUnsafe = false;
                    let nsfwScore = 0;

                    predictions.forEach(p => {
                        let category = p.className.toLowerCase();
                        let prob = p.probability;
                        
                        if (category === 'porn' || category === 'hentai' || category === 'sexy') {
                            nsfwScore += prob;
                        }
                    });

                    // Extremely strict check: if combined vulgarity score is > 4%
                    if (nsfwScore > 0.04) {
                        isUnsafe = true;
                    }

                    if (isUnsafe) {
                        resolve(false); // Flagged as Unsafe
                    } else {
                        resolve(true); // Flagged as Safe
                    }
                } catch (err) {
                    console.error(err);
                    resolve(true); // Fallback to safe if classification fails
                }
            };
            img.onerror = function() {
                resolve(true);
            };
            img.src = e.target.result;
        };
        reader.onerror = function() {
            resolve(true);
        };
        reader.readAsDataURL(file);
    });
}

document.getElementById('bannerInput').addEventListener('change', async function(e){

    let files = Array.from(e.target.files);

    let validFiles = [];
    let error = "";

    // Clear old validation error message if any
    $('#banner_image_error_label').remove();

    // Show scanning status overlay
    let statusEl = $('<div id="nsfwScanStatus" class="text-warning small mt-1 font-weight-bold"><i class="fa fa-spinner fa-spin me-1"></i> Scanning image pixels for safety check... Please wait.</div>');
    $('#bannerInput').after(statusEl);

    isScanning = true;
    $('button[type="submit"]').prop('disabled', true);

    // Make sure model is loaded
    if (!nsfwModel) {
        await loadNSFWModel();
    }

    for (let file of files) {
        let type = file.type;

        if(type === "image/jpeg" || type === "image/png"){
            if (nsfwModel) {
                let isSafe = await checkImageSafety(file);
                if (!isSafe) {
                    error = "The uploaded image does not meet our safety and content guidelines and has been automatically removed.";
                } else {
                    validFiles.push(file);
                }
            } else {
                validFiles.push(file); // fallback if model fails
            }
        } else {
            error = "Only JPG & PNG images are allowed!";
        }
    }

    statusEl.remove();
    isScanning = false;
    $('button[type="submit"]').prop('disabled', false);

    if(error){
        alert(error);
        let errEl = $('<div id="banner_image_error_label" class="text-danger small mt-1 d-block font-weight-bold">' + error + '</div>');
        $('#bannerInput').after(errEl);
    }

    selectedFiles = selectedFiles.concat(validFiles);

    updateInputFiles();
    renderPreview();
    updateCount();

});


function renderPreview(){

    let preview = document.getElementById('preview');
    preview.innerHTML = "";

    selectedFiles.forEach((file, index) => {

        let reader = new FileReader();

        reader.onload = function(e){

            let div = document.createElement('div');
            div.className = "preview-box";

            div.innerHTML = `
                <img src="${e.target.result}">
                <span class="remove-btn">×</span>
            `;

            div.querySelector('.remove-btn').onclick = function(){

                selectedFiles.splice(index,1);

                updateInputFiles();
                renderPreview();
                updateCount();
            };

            preview.appendChild(div);
        };

        reader.readAsDataURL(file);

    });

}


function updateInputFiles(){

    let dataTransfer = new DataTransfer();

    selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
    });

    document.getElementById('bannerInput').files = dataTransfer.files;
}


function updateCount(){

    let count = selectedFiles.length;

    let counter = document.getElementById('bannerCount');

    if(!counter){

        let el = document.createElement('div');

        el.id = 'bannerCount';
        el.className = 'text-muted mt-2';

        document.getElementById('preview').after(el);

        counter = el;
    }

    counter.innerHTML = count + " Banner(s) Selected";
}


let oldImages = @json($images ?? []);

document.querySelectorAll('.old-remove').forEach(btn => {

    btn.addEventListener('click', function(){

        let index = parseInt(this.dataset.index);

        oldImages.splice(index, 1);

        this.parentElement.remove();

        document.getElementById('existingImagesInput').value = JSON.stringify(oldImages);

    });

});

function generateAI(btnElement) {
    let base64Image = null;
    let oldImage = null;

    if (selectedFiles.length > 0) {
        let file = selectedFiles[0];
        let reader = new FileReader();
        reader.onload = function(e) {
            base64Image = e.target.result;
            sendAIAjax(btnElement, base64Image, null);
        };
        reader.readAsDataURL(file);
    } else if (oldImages.length > 0) {
        oldImage = oldImages[0];
        sendAIAjax(btnElement, null, oldImage);
    } else {
        alert("Please select or upload an image first to generate AI content.");
    }
}

function sendAIAjax(btnElement, base64Image, oldImage) {
    let originalHtml = btnElement.innerHTML;
    btnElement.innerHTML = '<span>Wait...</span>';
    btnElement.disabled = true;

    $.ajax({
        url: "{{ route('seller.marketplace.banner.ai') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            base64Image: base64Image,
            oldImage: oldImage
        },
        success: function(response) {
            if (response.success) {
                if (response.title) {
                    $('#banner_title').val(response.title);
                }
                if (response.subtitle) {
                    $('#banner_sub_title').val(response.subtitle);
                }
            } else {
                alert(response.message || "Failed to generate.");
            }
            btnElement.innerHTML = originalHtml;
            btnElement.disabled = false;
        },
        error: function(xhr) {
            alert("Error in AI generation. Please check console.");
            console.error(xhr.responseText);
            btnElement.innerHTML = originalHtml;
            btnElement.disabled = false;
        }
    });
}


// SUCCESS AUTO HIDE
document.addEventListener('DOMContentLoaded', function () {

    let successBox = document.querySelector('.successMessage');

    if(successBox){

        setTimeout(function(){

            successBox.style.transition = "all .5s ease";
            successBox.style.opacity = "0";
            successBox.style.transform = "translateY(-10px)";

            setTimeout(function(){

                successBox.remove();

            }, 500);

        }, 3500);

    }

});

</script>

@endsection
