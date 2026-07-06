@extends('vshop.seller.main')

<style>

/* HEADER */
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
.nav-btn:hover {
    background:#f5f7fb;
}

/* WRAPPER */
.marketplace-wrapper{
    display:flex;
    gap:30px;
}
.marketplace-left{
    flex:1;
}
.marketplace-right{
    width:300px;
    position:sticky;
    top:20px;
}

/* CARD */
.category-card{
    background:#fff;
    padding:22px;
    border-radius:14px;
    border:1px solid #eef1f6;
    margin-bottom:20px;
}

/* LABEL */
label{
    font-weight:500;
    margin-bottom:6px;
}

/* INPUT */
.form-control{
    height:42px;
    border-radius:8px;
}

/* SUBCATEGORY */
.sub-row{
    display:flex;
    align-items:center;
    gap:10px;
    padding:8px;
    border-radius:8px;
    background:#f9fbff;
    border:1px solid #eef1f6;
    margin-bottom:10px;
}

.sub-row input{
    flex:1;
    border:none;
    background:transparent;
    height:36px;
}

/* REMOVE BTN */
.remove-sub{
    width:30px;
    height:30px;
    border:none;
    background:#ff4d4f;
    color:#fff;
    border-radius:6px;
    font-size:16px;
}

/* ADD BTN */
.add-sub-btn{
    width:34px;
    height:34px;
    border-radius:50%;
    border:none;
    background:#0b4da1;
    color:#fff;
    font-size:18px;
}

/* SAVE */
.save-btn{
    background:#0b4da1;
    color:#fff;
    border:none;
    padding:12px 25px;
    border-radius:8px;
    font-weight:500;
    font-size:15px;
}

/* HEADING */
.section-title{
    font-size:15px;
    font-weight:600;
    margin-bottom:15px;
}
.top-back-heading-sec h5 {
    color: #0f172a !important;
}
.save-btn {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%) !important;
}

</style>

@section('content')

<div class="container-fluid py-4">

@include('vshop.seller.partials.marketplace-header',['title'=>'Category'])

<div class="marketplace-wrapper">

<div class="marketplace-left">

{{-- TOP --}}
<div class="d-flex align-items-center mb-4 top-back-heading-sec" style="gap:12px;">
    <a href="{{ url('seller/marketplace/category') }}" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <h5 class="mb-0">
        {{ !empty($category) ? 'Edit Category' : 'Create Category' }}
    </h5>
</div>

{{-- ALERT --}}
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form method="POST"
action="{{ !empty($category) 
? route('seller.marketplace.category.update',$category->id) 
: route('seller.marketplace.category.store') }}">

@csrf

{{-- CATEGORY --}}
<div class="category-card">

    <div class="section-title">Category Information</div>

    <div class="row">

        <div class="col-md-6">
            <label>Category Type</label>
            <select name="type" id="categoryType" class="form-control" required>
                <option value="">Select Type</option>
                <option value="product"
                    {{ old('type', $category->type ?? '') == 'product' ? 'selected' : '' }}>
                    Product
                </option>
                <option value="service"
                    {{ old('type', $category->type ?? '') == 'service' ? 'selected' : '' }}>
                    Service
                </option>
            </select>
        </div>

        <div class="col-md-6">
            <label>Parent Category</label>
            <select name="category_id" id="parentCategory" class="form-control" required>
                <option value="">Select a Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        data-type="{{ $cat->type }}"
                        {{ old('category_id', $category->id ?? '') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }} 
                    </option>
                @endforeach
            </select>
        </div>

    </div>
</div>

{{-- SUBCATEGORY --}}
<div class="category-card">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-title mb-0">Sub Categories</div>
        <button type="button" id="addSubCategory" class="add-sub-btn">+</button>
    </div>

    <div id="subCategoryWrapper">

        @if(!empty($subcategories))
            @foreach($subcategories as $sub)
            <div class="sub-row">
                <input type="text"
                       name="subcategories[]"
                       value="{{ $sub }}"
                       placeholder="Enter sub category"
                       required>
                <button type="button" class="remove-sub">×</button>
            </div>
            @endforeach
        @else
            <div class="sub-row">
                <input type="text"
                       name="subcategories[]"
                       placeholder="Enter sub category"
                       required>
                <button type="button" class="remove-sub">×</button>
            </div>
        @endif

    </div>

</div>

<div class="mt-3">
    <button type="submit" class="save-btn">
        {{ !empty($category) ? 'Update Category' : 'Save Category' }}
    </button>
</div>

</form>

</div>

{{-- RIGHT --}}
<div class="marketplace-right">
@include('vshop.seller.partials.marketplace-progress')
</div>

</div>

</div>

{{-- SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function(){

    // -------- TYPE-DEPENDENT PARENT CATEGORY DROPDOWN --------
    const typeSelect = document.getElementById('categoryType');
    const parentSelect = document.getElementById('parentCategory');

    function filterParentCategories() {
        const selectedType = typeSelect.value;
        // If no type selected, show all
        const showAll = selectedType === '';

        // Get currently selected option (to keep it visible)
        const selectedOption = parentSelect.options[parentSelect.selectedIndex];
        const selectedValue = selectedOption ? selectedOption.value : '';

        // Loop through all options
        for (let opt of parentSelect.options) {
            // Always show the placeholder (value="") and the currently selected option
            if (opt.value === '' || opt.value === selectedValue) {
                opt.style.display = '';
                continue;
            }
            // Show if type matches or show all
            if (showAll || opt.dataset.type === selectedType) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        }

        // If the selected option is now hidden (shouldn't happen because we kept it visible), but if it's hidden, reset to placeholder
        if (selectedOption && selectedOption.style.display === 'none') {
            // This can happen if we didn't keep it visible, but we did.
        }
    }

    // Initial filter on page load
    filterParentCategories();

    // When type changes, re-filter
    typeSelect.addEventListener('change', filterParentCategories);

    // -------- ADD / REMOVE SUB CATEGORY ROWS --------
    document.getElementById('addSubCategory').addEventListener('click', function(){
        let html = `
            <div class="sub-row">
                <input type="text" name="subcategories[]" placeholder="Enter sub category" required>
                <button type="button" class="remove-sub">×</button>
            </div>
        `;
        document.getElementById('subCategoryWrapper').insertAdjacentHTML('beforeend', html);
    });

    document.addEventListener('click', function(e){
        if(e.target.classList.contains('remove-sub')){
            e.target.closest('.sub-row').remove();
        }
    });

});
</script>

@endsection
