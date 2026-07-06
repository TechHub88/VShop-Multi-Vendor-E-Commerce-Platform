@extends('vshop.seller.main')

<style>
    .top-back-heading-sec h5 {
        color: #0f172a !important;
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
    }
    .top-back-heading-sec {
        align-items: center;
        gap: 10px;
    }
    .marketplace-right {
        width: 300px;
        position: sticky;
        top: 20px;
    }
    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        padding: 9px !important;
        text-align: center;
    }
</style>

@section('content')

<div class="container-fluid py-3">

@include('vshop.seller.partials.marketplace-header',[
'title'=>'Edit Website Template'
])

<form method="POST"
action="{{ route('seller.marketplace.template.update',$template->id) }}"
enctype="multipart/form-data">

@csrf

<div class="marketplace-wrapper">

    {{-- LEFT --}}
    <div class="marketplace-left">

        <div class="card template-wrapper p-4">

            {{-- HEADER --}}
            
            <div class="d-flex top-back-heading-sec mb-4">
                <a href="{{ url('seller/marketplace/template') }}"
                class="nav-btn">
                
                <i class="fa-solid fa-arrow-left"></i>
                
                </a>
               <h5 class="mb-0">Edit Template</h5>
            </div>

            {{-- SUCCESS --}}
            @if(session('success'))

            <div class="alert alert-success" id="successMessage">
                {{ session('success') }}
            </div>

            @endif

            {{-- ERROR --}}
            @if(session('error'))

            <div class="alert alert-danger">
                {{ session('error') }}
            </div>

            @endif

            {{-- NAME --}}
            <div class="mb-3">

                <label>Name</label>

                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name',$template->name) }}"
                       required>

            </div>

            {{-- APPLICATION TYPE --}}
            <div class="mb-3">

                <label>Application Type</label>

                <input type="text"
                       name="application_type"
                       class="form-control"
                       value="{{ old('application_type',$template->application_type) }}"
                       required>

            </div>

            {{-- IMAGE --}}
            <div class="mb-3">

                <label>Preview Image</label>

                <input type="file"
                       name="preview_image"
                       class="form-control">

                @if($template->preview_image)

                <div class="mt-3">

                    <img src="{{ asset($template->preview_image) }}"
                         width="140"
                         class="border rounded">

                    <div class="mt-2">

                        <a href="{{ asset($template->preview_image) }}"
                           target="_blank"
                           class="btn btn-sm btn-dark">

                            <i class="fa fa-eye me-1"></i>
                            View File

                        </a>

                    </div>

                </div>

                @endif

            </div>

            {{-- DESCRIPTION --}}
            <div class="mb-3">

                <label>Description</label>

                <textarea name="description"
                          class="form-control"
                          rows="4">{{ old('description',$template->description) }}</textarea>

            </div>

            {{-- BUTTON --}}
            <button class="btn btn-primary px-4">

                <i class="fa fa-save me-2"></i>
                Update

            </button>

        </div>

    </div>

    {{-- RIGHT --}}
    <div class="marketplace-right">

        @include('vshop.seller.partials.marketplace-progress')

    </div>

</div>

</form>
</div>
<style>

.marketplace-wrapper{
    display:flex;
    gap:25px;
    align-items:flex-start;
}

.marketplace-left{
    flex: 1;
}

.marketplace-right {
    width: 300px;
    position: sticky;
    top: 20px;
}

.back-btn{
color:#000;
padding:0;
text-decoration:none;
font-size:14px;
font-weight:600;
display:inline-flex;
align-items:center;
gap:6px;
transition:.3s;
background:transparent;
border:none;
box-shadow:none;
}

.back-btn:hover{
color:#333;
text-decoration:none;
background:transparent;
}
/* IMAGE */
.template-wrapper img{
object-fit:cover;
}

</style>

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
