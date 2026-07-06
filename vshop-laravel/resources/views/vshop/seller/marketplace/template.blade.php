@extends('vshop.seller.main')
<style>
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
    .add-template-btn {
        background: #094f9c !important;
        color: #fff !important;
    }
    .save-template-btn,
    .preview-btn {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%) !important;
        border: 0 !important;
    }
</style>

@section('content')

<div class="container-fluid py-3">

@include('vshop.seller.partials.marketplace-header',[
'title'=>'Website Template'
])

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

@php
$selectedTemplates = $selectedTemplates ?? [];

if(is_string($selectedTemplates)){
    $selectedTemplates = json_decode($selectedTemplates,true);
}
@endphp

<form method="POST"
      action="{{ route('seller.marketplace.template.save') }}">

@csrf

<div class="marketplace-wrapper">

    {{-- ================= LEFT ================= --}}
    <div class="marketplace-left">

        {{-- HEADER --}}
        <div class="template-header">
            <div class="d-flex top-back-heading-sec">
              <button type="button"
        onclick="window.location.href='{{ route('seller.marketplace.banner') }}'"
        class="nav-btn">

    <i class="fa-solid fa-arrow-left"></i>

</button>
                <h5>Website Template</h5>
            </div>

            <div class="header-actions">

                <a href="{{ route('seller.marketplace.template.create') }}"
                   class="add-template-btn">

                    <i class="fa fa-plus"></i>
                    Add Template

                </a>

                <button type="submit"
                        class="save-template-btn">

                    <i class="fa fa-save"></i>
                    Save

                </button>

            </div>

        </div>

        {{-- SUCCESS --}}
        @if(session('success'))

        <div class="alert alert-success successMessage">

            {{ session('success') }}

        </div>

        @endif

        <input type="hidden"
               name="category_id"
               value="{{ request('category_id') }}">

        {{-- GRID --}}
        <div class="template-grid">

            @foreach($templates as $template)

            @php
            $isSelected = in_array($template->id, $selectedTemplates);
            @endphp

            <div class="template-card-wrapper">

                {{-- SELECT --}}
                <label class="select-box">

                    <input type="checkbox"
                           name="templates[]"
                           value="{{ $template->id }}"
                           {{ $isSelected ? 'checked' : '' }}>

                    <span class="circle"></span>

                </label>

                {{-- CARD --}}
                <div class="template-card {{ $isSelected ? 'active-card' : '' }}">

                    {{-- IMAGE --}}
                    <div class="template-image">

                        <img src="{{ asset($template->preview_image) }}"
                             alt="{{ $template->name }}">

                    </div>

                    {{-- CONTENT --}}
                    <div class="template-content">

                        {{-- LEFT --}}
                        <div class="left-content">

                            <h6 class="template-title">
                                {{ $template->name }}
                            </h6>

                            <small class="template-type">
                                {{ $template->application_type }}
                            </small>

                            @if($template->description)

                            <p class="template-desc">
                                {{ $template->description }}
                            </p>

                            @endif

                        </div>

                        {{-- RIGHT --}}
                        <div class="right-content">

                            {{-- PREVIEW --}}
                            <a href="{{ asset($template->preview_image) }}"
                               target="_blank"
                               class="preview-btn">

                                Preview

                            </a>

                            {{-- EDIT --}}
                            <a href="{{ route('seller.marketplace.template.edit',$template->id) }}"
                               class="icon-btn edit-btn">

                                <i class="fa fa-pencil"></i>

                            </a>

                            {{-- DELETE --}}
                            <button type="button"
                                    class="icon-btn delete-btn"
                                    onclick="deleteTemplate({{ $template->id }})">

                                <i class="fa fa-trash"></i>

                            </button>

                        </div>

                    </div>

                </div>

            </div>

            @endforeach

        </div>

    </div>


    {{-- ================= RIGHT ================= --}}
    <div class="marketplace-right">

        @include('vshop.seller.partials.marketplace-progress')

    </div>

</div>

</form>


</div>
{{-- DELETE --}}
<script>

function deleteTemplate(id){

    if(confirm('Delete template?')){

        let form = document.createElement('form');

        form.method = 'POST';

        form.action =
        '/seller/marketplace/template/delete/' + id;

        form.innerHTML = `
            <input type="hidden"
                   name="_token"
                   value="{{ csrf_token() }}">

            <input type="hidden"
                   name="_method"
                   value="DELETE">
        `;

        document.body.appendChild(form);

        form.submit();
    }
}

</script>


<style>

/* ================= LAYOUT ================= */

.marketplace-wrapper{
display:flex;
gap:24px;
align-items:flex-start;
}

.marketplace-left{
flex:1;
}

.marketplace-right{
width:300px;
position:sticky;
top:20px;
}


/* ================= HEADER ================= */

.template-header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:22px;
gap:15px;
flex-wrap:wrap;
}

.page-title{
font-size:24px;
font-weight:700;
margin:0;
color:#0f172a;
}

.header-actions{
display:flex;
align-items:center;
gap:10px;
}


/* ================= BUTTONS ================= */

.add-template-btn,
.save-template-btn{
height:40px;
padding:0 16px;
border:none;
border-radius:10px;

display:flex;
align-items:center;
gap:8px;

font-size:14px;
font-weight:600;

text-decoration:none;
cursor:pointer;

transition:.2s ease;
}

.add-template-btn{
background:#0d6efd;
color:#fff;
}

.add-template-btn:hover{
background:#0b5ed7;
color:#fff;
}

.save-template-btn{
background:#16a34a;
color:#fff;
}

.save-template-btn:hover{
background:#15803d;
}


/* ================= ALERT ================= */

.successMessage{
border-radius:12px;
margin-bottom:20px;
}


/* ================= GRID ================= */

.template-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(340px,1fr));
grid-template-columns: repeat(3, 1fr);
gap:24px;
}


/* ================= CARD ================= */

.template-card{
background:#fff;
border-radius:18px;
padding:12px;
padding-top: 40px;
border:1px solid #edf1f7;

box-shadow:
0 3px 14px rgba(0,0,0,.05);

transition:.25s ease;
}

.template-card:hover{
transform:translateY(-3px);

box-shadow:
0 8px 24px rgba(0,0,0,.08);
}

.active-card{
border:2px solid #22c55e;
}


/* ================= IMAGE ================= */

.template-image{
position:relative;
}

.template-image img{
width:100%;
height:120px;
object-fit:cover;
border-radius:12px;
display:block;
}


/* ================= SELECT ================= */

.template-card-wrapper{
position:relative;
}

.select-box{
position:absolute;
top:12px;
left:12px;
z-index:20;
}

.select-box input{
display:none;
}

.circle{
width:20px;
height:20px;
border-radius:50%;
background:#fff;
border:2px solid #d1d5db;

display:flex;
align-items:center;
justify-content:center;

cursor:pointer;
transition:.2s ease;
}

.select-box input:checked + .circle{
background:#22c55e;
border-color:#22c55e;
}

.select-box input:checked + .circle::after{
content:"✓";
color:#fff;
font-size:11px;
font-weight:700;
}


/* ================= CONTENT ================= */

.template-content{
display:flex;
justify-content:space-between;
align-items:center;
gap:14px;

padding-top:14px;
}

.left-content{
flex:1;
}


/* ================= TEXT ================= */

.template-title{
font-size:16px;
font-weight:700;
margin-bottom:3px;
color:#0f172a;
}

.template-type{
font-size:12px;
color:#64748b;
display:block;
margin-bottom:6px;
}

.template-desc{
font-size:12px;
line-height:1.5;
color:#6b7280;
margin:0;
}


/* ================= RIGHT ================= */

.right-content{
display:flex;
align-items:center;
gap:8px;
}


/* ================= PREVIEW ================= */

.preview-btn{
height:32px;
padding:0 12px;

background:#0d6efd;
color:#fff;

border-radius:8px;

display:flex;
align-items:center;
justify-content:center;

font-size:12px;
font-weight:600;

text-decoration:none;

transition:.2s ease;
}

.preview-btn:hover{
background:#0b5ed7;
color:#fff;
}


/* ================= ICON BUTTON ================= */

.icon-btn{
width:32px;
height:32px;

border:none;
border-radius:8px;

background:#f8fafc;

display:flex;
align-items:center;
justify-content:center;

text-decoration:none;
cursor:pointer;

transition:.2s ease;

font-size:13px;
}

.icon-btn:hover{
transform:translateY(-2px);
}

.edit-btn{
color:#f59e0b;
}

.delete-btn{
color:#ef4444;
}


/* ================= RESPONSIVE ================= */

@media(max-width:991px){

.marketplace-wrapper{
flex-direction:column;
}

.marketplace-right{
width:100%;
position:relative;
top:0;
}

}


@media(max-width:768px){

.template-grid{
grid-template-columns:1fr;
}

.template-content{
flex-direction:column;
align-items:flex-start;
}

.right-content{
width:100%;
justify-content:flex-end;
}

.template-image img{
height:100px;
}

}

</style>


<script>

/* ================= AUTO HIDE SUCCESS ================= */

document.addEventListener('DOMContentLoaded', function () {

    let successBox =
    document.querySelector('.successMessage');

    if(successBox){

        setTimeout(function(){

            successBox.style.transition =
            "all 0.5s ease";

            successBox.style.opacity = "0";

            successBox.style.transform =
            "translateY(-10px)";

            setTimeout(function(){

                successBox.remove();

            },500);

        },2500);

    }

});

</script>

@endsection
