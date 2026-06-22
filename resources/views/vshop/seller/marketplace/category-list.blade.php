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
.edit-btn {
    width: 32px;
    height: 32px;
}
.top-back-heading-sec h5 {
    color: #0f172a !important;
}
</style>

@section('content')

<div class="container-fluid py-4">

<div class="marketplace-wrapper">
    
  

    {{-- LEFT CONTENT --}}
    <div class="marketplace-left">
          @include('vshop.seller.partials.marketplace-header',[
'title'=>'Category'
])

        <div class="category-card">

            <div class="card-header">
            <div class="d-flex top-back-heading-sec">
                <button onclick="goBack()" class="nav-btn">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <h5>Selected Category</h5>
            </div>

    <div class="d-flex gap-2">

        {{-- EDIT --}}
        <a href="{{ route('seller.marketplace.category') }}"
           class="edit-btn"
           title="Edit">
            <i class="bi bi-pencil"></i>
        </a>

        {{-- DELETE ALL --}}
        <form method="POST"
              action="{{ route('seller.marketplace.category.delete') }}"
              onsubmit="return confirm('Delete all categories?')">
            @csrf

            <input type="hidden" name="delete_all" value="1">

            <button type="submit" class="delete-btn" title="Delete All">
                <i class="bi bi-trash"></i>
            </button>
        </form>

    </div>
</div>

            @foreach($selectedCategories as $cat)
<div class="category-item d-flex justify-content-between align-items-start">

    <div>
        <b>{{ $cat->name }}</b>

        <div class="subcategory-text">
            Sub Category : {{ $cat->subs }}
        </div>
    </div>

    <!-- DELETE BUTTON -->
    <form method="POST"
          action="{{ route('seller.marketplace.category.delete') }}"
          onsubmit="return confirm('Are you sure to delete this category?')">
        @csrf
        <input type="hidden" name="category_id" value="{{ $cat->id }}">

        <button type="submit" class="delete-btn" title="Delete">
            <i class="bi bi-trash"></i>
        </button>
    </form>

</div>

            @endforeach

         
        </div>

    </div>


    {{-- RIGHT SIDEBAR --}}
    <div class="marketplace-right">
        @include('vshop.seller.partials.marketplace-progress')
    </div>

</div>

</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>

.delete-btn{
    border:none;
    background:#ffe5e5;
    color:#d11a2a;
    width:32px;
    height:32px;
    border-radius:6px;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
}

.delete-btn:hover{
    background:#ffcccc;
}

/* MAIN WRAPPER */
.marketplace-wrapper{
display:flex;
gap:25px;
align-items:flex-start;
}

/* LEFT AREA */
.marketplace-left{
flex:1;
}

/* RIGHT SIDEBAR */
.marketplace-right{
width:300px;
position:sticky;
top:20px;
}

/* CARD */
.category-card{
background:#fff;
padding:25px;
border-radius:12px;
box-shadow:0 2px 8px rgba(0,0,0,.05);
}

/* HEADER */
.card-header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:15px;
}

.edit-btn{
background:#eef3fb;
padding:6px 10px;
border-radius:50%;
text-decoration:none;
}

/* CATEGORY TEXT */
.category-item{
margin-bottom:15px;
}

.subcategory-text{
color:#6c757d;
font-size:13px;
margin-top:4px;
}

/* NOT SELECTED */
.not-selected{
color:#0b4da1;
}

.not-selected-list{
color:#9aa5b1;
font-size:13px;
}

</style>

@endsection