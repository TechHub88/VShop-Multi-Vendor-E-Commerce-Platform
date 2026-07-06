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
.btn-save {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%) !important;
}
.btn-cancel {
    background: #61b3e5 !important;
    color: #fff !important;
    border: 0 !important;
}
</style>

@section('content')

<div class="container-fluid py-4">

@include('vshop.seller.partials.marketplace-header',[
'title'=>'Category'
])

<div class="marketplace-wrapper">
    
{{-- LEFT FORM --}}
<div class="marketplace-left">
    
<div class="d-flex justify-content-between align-items-center mb-4">
<div class="d-flex top-back-heading-sec mb-4">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <h5 class="">
    Select Category <span class="text-danger">*</span>
    </h5>
</div>


<div class="header-actions d-flex gap-2">

<a href="{{ route('seller.marketplace.category.list') }}"
class="icon-btn"
title="Category List">
<i class="bi bi-list-ul"></i>
</a>

<a href="{{ route('seller.marketplace.category.create') }}"
class="icon-btn"
title="Add Category">
<i class="bi bi-plus-circle"></i>
</a>

</div>

</div>

@if($categories->isEmpty())
    <div class="alert alert-info text-center py-4" style="border-radius: 12px; font-weight: 500;">
        No products are available
    </div>
@else
<form method="POST"
action="{{ route('seller.marketplace.category.save') }}">
@csrf


@foreach($categories as $cat)

<div class="category-box">

<div class="category-row">

<label class="category-title d-flex align-items-center gap-2">

<input type="checkbox"
class="category-checkbox"
data-id="{{ $cat->id }}"
style="width:16px;height:16px;">

<span>{{ $cat->name }}</span>

</label>

<div class="category-actions">

<div class="pill-display"
data-id="{{ $cat->id }}">
Select Sub Category ▼
</div>

<a href="{{ route('seller.marketplace.category.edit',$cat->id) }}"
class="edit-icon"
title="Edit Category">
<i class="bi bi-pencil"></i>
</a>

<!-- 🔥 DELETE ICON ADDED -->
<a href="javascript:void(0)"
onclick="deleteCategory({{ $cat->id }})"
class="delete-icon"
title="Delete Category">
<i class="bi bi-trash"></i>
</a>

</div>

</div>

{{-- SUBCATEGORY --}}
<div class="subcategory-area"
id="sub_{{ $cat->id }}"
style="display:none">

<label class="select-all">
<input type="checkbox"
class="select-all-checkbox"
data-id="{{ $cat->id }}">
Select All
</label>

<div class="pill-wrapper">

@foreach($cat->subCategories as $sub)

<label class="pill">

<input type="checkbox"
name="sub_categories[]"
value="{{ $sub->id }}"
class="sub-checkbox cat_{{ $cat->id }}"
{{ in_array($sub->id,$selected) ? 'checked' : '' }}>

<span>{{ $sub->name }}</span>

</label>

@endforeach

</div>

</div>

{{-- PREVIEW --}}
<div class="preview-box"
id="preview_{{ $cat->id }}"></div>

</div>

@endforeach

<div class="mt-4">
<button class="btn-save">Save</button>
<button type="button" class="btn-cancel">Cancel</button>
</div>

</form>
@endif

</div>

{{-- RIGHT --}}
<div class="marketplace-right">
@include('vshop.seller.partials.marketplace-progress')
</div>

</div>

</div>

@endsection

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

/* CATEGORY ACTIONS */
.category-actions{
display:flex;
align-items:center;
gap:10px;
}

/* EDIT ICON */
.edit-icon{
width:32px;
height:32px;
display:flex;
align-items:center;
justify-content:center;
background:#eef3fb;
border-radius:6px;
color:#0b4da1;
text-decoration:none;
font-size:14px;
}

.edit-icon:hover{
background:#dbe6ff;
}

/* 🔥 DELETE ICON */
.delete-icon{
width:32px;
height:32px;
display:flex;
align-items:center;
justify-content:center;
background:#ffecec;
border-radius:6px;
color:#d11a2a;
text-decoration:none;
font-size:14px;
}

.delete-icon:hover{
background:#ffd6d6;
}

.icon-btn{
width:36px;
height:36px;
background:#0d47a1;
color:#fff;
display:flex;
align-items:center;
justify-content:center;
border-radius:8px;
text-decoration:none;
}

.icon-btn i{
font-size:16px;
}

.marketplace-wrapper{
display:flex;
gap:25px;
align-items:flex-start;
}

.marketplace-left{
flex:1;
}

.marketplace-right{
width:300px;
}

.category-box{
background:#fff;
padding:20px;
margin-bottom:20px;
border-radius:12px;
box-shadow:0 2px 6px rgba(0,0,0,.05);
}

.category-row{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:10px;
}

.category-title{
font-weight:600;
}

.pill-display{
border:1px solid #d0d7e2;
border-radius:25px;
padding:6px 15px;
background:#f6f8fb;
cursor:pointer;
font-size:13px;
}

.subcategory-area{
margin-top:12px;
}

.pill-wrapper{
display:flex;
flex-wrap:wrap;
gap:8px;
margin-top:10px;
}

.pill{
background:#eef2f7;
padding:6px 14px;
border-radius:20px;
cursor:pointer;
}

.pill input{
display:none;
}

.pill input:checked + span{
background:#0b4da1;
color:#fff;
padding:6px 14px;
border-radius:20px;
}

.preview-box{
border:1px solid #cfd6e0;
border-radius:8px;
padding:10px;
margin-top:12px;
background:#f8fafc;
font-size:13px;
}

.btn-save{
background:#0b4da1;
color:#fff;
padding:8px 30px;
border:none;
border-radius:6px;
}

.btn-cancel{
border:1px solid #0b4da1;
color:#0b4da1;
padding:8px 30px;
border-radius:6px;
margin-left:10px;
background:#fff;
}

.category-title input[type="checkbox"]{
display:inline-block !important;
opacity:1 !important;
position:relative !important;
margin-right:8px;
cursor:pointer;
}

</style>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>

function deleteCategory(id){

    if(!confirm('Are you sure you want to delete this category?')) return;

    $.ajax({
        url: "{{ route('seller.marketplace.category.destroy') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            category_id: id
        },
        success: function(res){

            if(res.status){
                alert('Deleted Successfully');
                location.reload();
            }else{
                alert(res.msg || 'Delete failed');
            }

        },
        error:function(xhr){
            console.log(xhr.responseText);
            alert('Server Error');
        }
    });
}

/* EXISTING JS SAME */
document.addEventListener('DOMContentLoaded',function(){

document.querySelectorAll('.category-checkbox')
.forEach(cb=>{

cb.addEventListener('change',function(){

let id=this.dataset.id;
let box=document.getElementById('sub_'+id);

if(this.checked){

if(box) box.style.display='block';

document.querySelectorAll('.cat_'+id)
.forEach(el=>el.checked=true);

}else{

if(box) box.style.display='none';

document.querySelectorAll('.cat_'+id)
.forEach(el=>el.checked=false);
}

updatePreview(id);

});

});

document.querySelectorAll('.pill-display')
.forEach(pill=>{

pill.addEventListener('click',function(){

let id=this.dataset.id;
let box=document.getElementById('sub_'+id);

box.style.display =
box.style.display=='block'?'none':'block';

});

});

document.querySelectorAll('.select-all-checkbox')
.forEach(selectAll=>{

selectAll.addEventListener('change',function(){

let id=this.dataset.id;

document.querySelectorAll('.cat_'+id)
.forEach(cb=>cb.checked=this.checked);

updatePreview(id);

});

});

document.querySelectorAll('.sub-checkbox')
.forEach(cb=>{

cb.addEventListener('change',function(){

let id=this.className.match(/cat_(\d+)/)[1];

let checked=document.querySelectorAll('.cat_'+id+':checked');

let parent=document.querySelector('.category-checkbox[data-id="'+id+'"]');

parent.checked = checked.length > 0;

updatePreview(id);

});

});

function updatePreview(id){

let selected=[];

document.querySelectorAll('.cat_'+id+':checked')
.forEach(el=>{
selected.push(el.nextElementSibling.innerText);
});

document.getElementById('preview_'+id)
.innerHTML =
selected.length
? "<b>Selected :</b> "+selected.join(', ')
: "";

}

});

</script>
