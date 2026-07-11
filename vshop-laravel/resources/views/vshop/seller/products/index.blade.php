@extends('vshop.seller.main')

<style>
.remove-btn {
    background: #61b3e5 !important;
    color: #fff !important;
    margin-left: 5px;
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

/*.sort-dropdown{*/
/*    width: 220px;*/
/*    height: auto;*/
/*    max-height: 200px;*/
/*    overflow-y: auto;*/
/*    border: 1px solid #ddd;*/
/*    border-radius: 6px;*/
/*    padding: 5px;*/
/*    font-size: 14px;*/
/*}*/

/* Option spacing */
/*.sort-dropdown option{*/
/*    padding: 10px;*/
/*}*/

/* Custom Scrollbar */
/*.sort-dropdown::-webkit-scrollbar{*/
/*    width: 6px;*/
/*}*/

/*.sort-dropdown::-webkit-scrollbar-thumb{*/
/*    background: #2563eb;*/
/*    border-radius: 10px;*/
/*}*/

.card {
    border: 1px solid rgb(201 208 218) !important;
}
.product-card {
    border: 1px solid rgb(201 208 218) !important;
}
.top-back-heading-sec h5 {
    color: #0f172a !important;
}
.btn-success {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);  
    border: 0 !important;
}
.btn-primary {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);  
    border: 0 !important;
}

</style>

@section('content')

<div class="container-fluid py-4">

@php

    /*
    |--------------------------------------------------------------------------
    | GET MODULE TYPE FROM SESSION
    |--------------------------------------------------------------------------
    */

    $selectedPanels = session('selected_panels', []);

    $moduleType = 'product';


    /*
    |--------------------------------------------------------------------------
    | PRODUCT + SERVICE
    |--------------------------------------------------------------------------
    */

    if(
        in_array('product', $selectedPanels)
        &&
        in_array('travel', $selectedPanels)
    ){

        $moduleType = 'both';
    }


    /*
    |--------------------------------------------------------------------------
    | SERVICE ONLY
    |--------------------------------------------------------------------------
    */

    elseif(
        !in_array('product', $selectedPanels)
        &&
        in_array('travel', $selectedPanels)
    ){

        $moduleType = 'service';
    }


    /*
    |--------------------------------------------------------------------------
    | COURSE ONLY
    |--------------------------------------------------------------------------
    */

    elseif(
        in_array('vcampus', $selectedPanels)
    ){

        $moduleType = 'course';
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT ONLY
    |--------------------------------------------------------------------------
    */

    else{

        $moduleType = 'product';
    }


    /*
    |--------------------------------------------------------------------------
    | LABELS
    |--------------------------------------------------------------------------
    */

    $headingLabel = 'Products';

    $singleLabel = 'Product';

    $pageHeading = 'Product / List of Products';

    $listHeading = 'Product List';


    /*
    |--------------------------------------------------------------------------
    | PRODUCT
    |--------------------------------------------------------------------------
    */

    if($moduleType == 'product'){

        $headingLabel = 'Products';

        $singleLabel = 'Product';

        $pageHeading = 'Product / List of Products';

        $listHeading = 'Product List';
    }


    /*
    |--------------------------------------------------------------------------
    | SERVICE
    |--------------------------------------------------------------------------
    */

    elseif($moduleType == 'service'){

        $headingLabel = 'Services';

        $singleLabel = 'Service';

        $pageHeading = 'Service / List of Services';

        $listHeading = 'Service List';
    }


    /*
    |--------------------------------------------------------------------------
    | COURSE
    |--------------------------------------------------------------------------
    */

    elseif($moduleType == 'course'){

        $headingLabel = 'Courses';

        $singleLabel = 'Course';

        $pageHeading = 'Course / List of Courses';

        $listHeading = 'Course List';
    }


    /*
    |--------------------------------------------------------------------------
    | BOTH
    |--------------------------------------------------------------------------
    */

    elseif($moduleType == 'both'){

        $headingLabel = 'Products / Services';

        $singleLabel = 'Product / Service';

        $pageHeading = 'Product / Service / List';

        $listHeading = 'Product / Service List';
    }

@endphp



<div class="d-flex justify-content-between align-items-center mb-3">
    
    <div class="d-flex top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
      <h5 class="fw-bold text-primary mb-0">Filters</h5>
    </div>

    <div>

        <a href="{{ route('seller.products.mis.dashboard') }}"
           class="btn btn-success me-2">

            ✔ {{ $singleLabel }} MIS

        </a>

        <a class="btn btn-success">

            ✔ {{ $singleLabel }} List

        </a>

        <a href="{{ route('seller.products.basic.save') }}"
           class="btn btn-success">

            ✔ Create {{ $singleLabel }}

        </a>

    </div>

</div>



{{-- ================= PRODUCT LIST ================= --}}

<!--<h6 class="fw-bold mb-3">-->

<!--    {{ $listHeading }}-->

<!--</h6>-->



<div class="card p-3 mb-4 shadow-sm">

<form method="GET">

<div class="row g-2">

{{-- CATEGORY --}}
<div class="col-md-2">
<label class="form-label">Category</label>

<select class="form-select" name="category" id="category">

<option value="">Category</option>

@foreach($categories as $cat)

<option value="{{$cat->id}}"
{{ request('category')==$cat->id ? 'selected' : '' }}>

{{$cat->name}}

</option>

@endforeach

</select>
</div>

{{-- SUB CATEGORY --}}
<div class="col-md-2">
<label class="form-label">Sub Category</label>

<select class="form-select" name="subcategory" id="subcategory">

<option value="">Sub Category</option>

</select>
</div>

{{-- STATUS --}}
<div class="col-md-2">
<label class="form-label">Status</label>

<select class="form-select" name="status">

<option value="">Status</option>

<option value="approved"
{{ request('status')=='approved' ? 'selected' : '' }}>
Verified
</option>

<option value="pending"
{{ request('status')=='pending' ? 'selected' : '' }}>
In Progress
</option>

<option value="rejected"
{{ request('status')=='rejected' ? 'selected' : '' }}>
Out of Stock
</option>

</select>
</div>





<div class="col-md-2">
<label class="form-label">Payment Type</label>

<select class="form-select" name="payment_type">

<option value="">Payment Type</option>

<option value="online"
{{ request('payment_type')=='online' ? 'selected' : '' }}>
Online
</option>

<option value="bank"
{{ request('payment_type')=='bank' ? 'selected' : '' }}>
Bank Transfer
</option>

<option value="invoice"
{{ request('payment_type')=='invoice' ? 'selected' : '' }}>
Invoice
</option>

</select>
</div>

{{-- CITY --}}
<div class="col-md-2">
<label class="form-label">Destination City</label>

<input type="text"
name="city"
value="{{request('city')}}"
class="form-control"
placeholder="Destination City">
</div>

{{-- DURATION --}}
<div class="col-md-2">
<label class="form-label">Duration</label>

<input type="text"
name="duration"
value="{{request('duration')}}"
class="form-control"
placeholder="Duration">
</div>

{{-- RATING --}}
<div class="col-md-2">
<label class="form-label">Rating</label>

<select class="form-select" name="rating">

<option value="">Rating</option>

@for($i=5;$i>=1;$i--)

<option value="{{$i}}"
{{ request('rating')==$i ? 'selected' : '' }}>

{{$i}}

</option>

@endfor

</select>
</div>

{{-- DISCOUNT --}}
<div class="col-md-2">
<label class="form-label">Discount</label>

<select class="form-select" name="discount">

<option value="">Discount</option>

<option value="yes"
{{ request('discount')=='yes' ? 'selected' : '' }}>
Yes
</option>

<option value="no"
{{ request('discount')=='no' ? 'selected' : '' }}>
No
</option>

</select>
</div>

<div class="col-md-3">
    <label class="form-label">Sort By</label>

    <select class="form-select sort-dropdown" name="sort" style="max-height:200px; overflow-y:auto;">
        <option value="">Sort By</option>

        <option value="high_low" {{ request('sort')=='high_low' ? 'selected' : '' }}>
            High → Low
        </option>

        <option value="low_high" {{ request('sort')=='low_high' ? 'selected' : '' }}>
            Low → High
        </option>

        <option value="popular" {{ request('sort')=='popular' ? 'selected' : '' }}>
            Most Popular
        </option>

        <option value="high_review" {{ request('sort')=='high_review' ? 'selected' : '' }}>
            Highest Review
        </option>

        <option value="low_review" {{ request('sort')=='low_review' ? 'selected' : '' }}>
            Lowest Review
        </option>

        <option value="high_sold" {{ request('sort')=='high_sold' ? 'selected' : '' }}>
            Highest Sold
        </option>

        <option value="low_sold" {{ request('sort')=='low_sold' ? 'selected' : '' }}>
            Lowest Sold
        </option>

        <option value="highest_price" {{ request('sort')=='highest_price' ? 'selected' : '' }}>
            Highest Price
        </option>

        <option value="lowest_price" {{ request('sort')=='lowest_price' ? 'selected' : '' }}>
            Lowest Price
        </option>

        {{-- NEW OPTIONS ADDED FROM SCREENSHOT --}}
        <option value="most_viewed" {{ request('sort')=='most_viewed' ? 'selected' : '' }}>
            Most Viewed
        </option>

        <option value="most_enquired" {{ request('sort')=='most_enquired' ? 'selected' : '' }}>
            Most Enquired
        </option>

        <option value="newest" {{ request('sort')=='newest' ? 'selected' : '' }}>
            Newest
        </option>

        <option value="oldest" {{ request('sort')=='oldest' ? 'selected' : '' }}>
            Oldest
        </option>

        <option value="most_boosted" {{ request('sort')=='most_boosted' ? 'selected' : '' }}>
            Most Boosted
        </option>

        <option value="highest_conversion" {{ request('sort')=='highest_conversion' ? 'selected' : '' }}>
            Highest Conversion
        </option>
    </select>
</div>
{{-- SEARCH --}}
<div class="col-md-3">
<label class="form-label">Search</label>

<input type="text"
name="search"
value="{{request('search')}}"
class="form-control"
placeholder="Product Name / Code">
</div>

{{-- BUTTON --}}
<div class="col-md-2 d-flex align-items-end">

<button class="btn btn-primary w-100">
Search
</button>

<button class="btn remove-btn w-100">Remove</button>

</div>

</div>

</form>

</div>
{{-- ================= PRODUCT LIST ================= --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="fw-bold mb-0">Product List</h6>
    
    @if(isset($hasActiveCampaign) && $hasActiveCampaign)
        <button type="button" class="btn btn-sm btn-success fw-bold open-global-campaign-btn shadow-sm" style="border-radius: 6px;">
            <i class="fa fa-bullhorn me-1"></i> Join Campaign
        </button>
    @endif
</div>

@forelse($products as $product)

<div class="product-card">
<div class="row align-items-center">

{{-- IMAGE --}}
<div class="col-md-3">
<div class="position-relative">
<img class="product-img"
src="{{ $product->thumbnail ? asset($product->thumbnail) : asset('images/no-image.png') }}">
</div>
</div>

{{-- DETAILS --}}
<div class="col-md-6">

<h6 class="fw-bold mb-1 text-primary product-title">
{{ $product->name }}
</h6>

<p class="text-muted small mb-2">
{{ $product->category->name ?? '' }}
→
{{ $product->subcategory->name ?? '' }}
</p>

<div class="mb-2 d-flex align-items-center gap-2 flex-wrap">

<span class="rating">
⭐ {{ number_format($product->reviews_avg_rating ?? 0,1) }}
</span>

<span class="text-muted">
{{ $product->reviews_count ?? 0 }} reviews
</span>

<span class="badge bg-light text-dark">
{{ ucfirst($product->type) }}
</span>

@if($product->recommendations->isNotEmpty())
<span class="badge recommended-badge-pill">
    <i class="fa fa-star text-warning me-1"></i> Recommended
</span>
@endif

@if(isset($hasActiveCampaign) && $hasActiveCampaign && $product->campaignParticipations->isNotEmpty())
@php
    $participation = $product->campaignParticipations->first();
    $campaignText = '';
    $comboHtml = '';

    if ($participation->campaign_type === 'percentage_20') {
        $campaignText = '20% Offer';
    } elseif ($participation->campaign_type === 'combo') {
        $combo = json_decode($participation->combo_details);
        if ($combo) {
            $freeProduct = \App\Models\Product::find($combo->free_product_id);
            if ($freeProduct) {
                $imgSrc = $freeProduct->thumbnail ? asset($freeProduct->thumbnail) : asset('images/no-image.png');
                $comboHtml = '
                <div class="d-inline-flex align-items-center mt-1 border rounded p-1 shadow-sm bg-white" style="border-color: #e5e7eb !important;">
                    <div class="d-flex align-items-center me-2 p-1 bg-light rounded border" style="border-color: #f3f4f6 !important;">
                        <img src="' . $imgSrc . '" alt="Free item" style="width: 28px; height: 28px; object-fit: cover; border-radius: 4px;">
                    </div>
                    <div class="d-flex flex-column justify-content-center pe-2">
                        <span class="text-success fw-bold" style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.3px;"><i class="fa fa-gift me-1"></i>Buy ' . $combo->buy_qty . ' Get ' . $combo->get_qty . ' Free</span>
                        <span class="text-muted text-truncate" style="font-size: 10px; max-width: 130px;" title="' . htmlspecialchars($freeProduct->name) . '">' . htmlspecialchars($freeProduct->name) . '</span>
                    </div>
                </div>';
            } else {
                $campaignText = 'Combo Offer';
            }
        } else {
            $campaignText = 'Combo Offer';
        }
    } else {
        $campaignText = ucfirst(str_replace('_', ' ', $participation->campaign_type));
    }
@endphp

@if($comboHtml)
    {!! $comboHtml !!}
@else
    <span class="badge ms-1" style="background: linear-gradient(135deg, #10b981, #059669) !important;">
        <i class="fa fa-star me-1"></i> {{ $campaignText }}
    </span>
@endif
@endif

</div>

@if($product->price)
<div class="text-success fw-bold">
₹ {{ number_format($product->price,2) }}
</div>
@endif

<!--@if($product->type=='service' && !$product->is_available)-->
<!--<div class="text-danger small mt-1">-->
<!--Service Unavailable-->
<!--</div>-->
<!--@endif-->

</div>

{{-- ACTION --}}
<div class="col-md-3 text-end">

<span class="badge status-badge {{ $product->status=='approved'?'bg-success':'bg-warning text-dark' }}">
{{ ucfirst($product->status) }}
</span>

<div class="mt-2 position-relative">

<button class="btn btn-sm btn-light border action-btn">
Action ▾
</button>

<div class="action-dropdown">

<a href="{{ route('seller.products.basic.edit',$product->id) }}" class="dropdown-item">
<i class="fa fa-edit me-2 text-warning"></i> Edit
</a>

<button type="button" class="dropdown-item text-primary open-recommendations-btn" data-product-id="{{ $product->id }}">
<i class="fa fa-star me-2 text-primary"></i> Recommendations
</button>


<form method="POST"
action="{{ route('seller.products.destroy',$product->id) }}"
class="deleteForm">

@csrf
@method('DELETE')

<button type="button"
class="dropdown-item text-danger deleteBtn">

<i class="fa fa-trash me-2"></i>
Delete

</button>

</form>

</div>

</div>

</div>

</div>
</div>

@empty

<div class="text-center py-4">
No Products Found
</div>

@endforelse


{{-- ================= PAGINATION ================= --}}
@if($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
<div class="d-flex justify-content-between align-items-center mt-4">

<div class="text-muted small">
Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }}
of {{ $products->total() }} entries
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $products->links('pagination::bootstrap-5') }}
</div>

</div>
@endif

</div>

{{-- ================= STYLE ================= --}}
<style>

.product-title{
display:-webkit-box;
-webkit-line-clamp:2;
-webkit-box-orient:vertical;
overflow:hidden;
text-overflow:ellipsis;
}

.product-card{
background:#fff;
padding:18px;
border-radius:14px;
margin-bottom:16px;
box-shadow:0 4px 15px rgba(0,0,0,.05);
transition:.2s;
}

.product-card:hover{
transform:translateY(-2px);
}

.product-img{
width:100%;
height:130px;
object-fit:cover;
border-radius:10px;
}

.recommended-badge-pill {
    background-color: #e0f2fe !important;
    color: #0369a1 !important;
    border: 1px solid #bae6fd !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    padding: 5px 10px !important;
    border-radius: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
}

.rating{
color:#0b4ea2;
font-weight:600;
}

.status-badge{
font-size:12px;
padding:6px 10px;
border-radius:20px;
}

.action-btn{
background:#f8fafc;
}

.action-dropdown{
display:none;
position:absolute;
right:0;
top:40px;
background:#fff;
border-radius:10px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
padding:8px 0;
min-width:150px;
z-index:100;
}

.dropdown-item{
display:block;
padding:10px 14px;
font-size:14px;
border:none;
background:none;
width:100%;
text-align:left;
}

.dropdown-item:hover{
background:#f1f5f9;
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


.sort-dropdown {
    max-height: 180px;      /* limit height */
    overflow-y: auto;       /* enable vertical scroll */
}

/* Optional: add custom scrollbar for modern browsers */
.sort-dropdown::-webkit-scrollbar {
    width: 6px;
}

.sort-dropdown::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.sort-dropdown::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.sort-dropdown::-webkit-scrollbar-thumb:hover {
    background: #a0a0a0;
}

</style>

{{-- ================= SCRIPT ================= --}}
<script>

document.querySelectorAll('.action-btn').forEach(btn=>{
btn.addEventListener('click',function(e){

e.stopPropagation();

let dropdown=this.nextElementSibling;

document.querySelectorAll('.action-dropdown').forEach(d=>{
if(d!==dropdown) d.style.display='none';
});

dropdown.style.display =
dropdown.style.display === 'block' ? 'none' : 'block';

});
});

document.addEventListener('click',function(){
document.querySelectorAll('.action-dropdown').forEach(d=>{
d.style.display='none';
});
});

</script>


<script>

document.addEventListener('DOMContentLoaded', function () {

    let category = document.getElementById('category');
    let subcategory = document.getElementById('subcategory');

    let selectedSubcategory = "{{ request('subcategory') }}";

    function loadSubcategories(categoryId, selected = '') {

        if (!categoryId) {

            subcategory.innerHTML =
                '<option value="">Sub Category</option>';

            return;
        }

        fetch('/seller/products/get-subcategories/' + categoryId)

        .then(response => response.json())

        .then(data => {

            let options =
                '<option value="">Sub Category</option>';

            data.forEach(function (item) {

                let isSelected =
                    selected == item.id ? 'selected' : '';

                options += `
                    <option value="${item.id}" ${isSelected}>
                        ${item.name}
                    </option>
                `;
            });

            subcategory.innerHTML = options;
        });
    }

    category.addEventListener('change', function () {

        loadSubcategories(this.value);

    });

    // ===== PAGE LOAD AUTO SELECT =====
    if(category.value){

        loadSubcategories(category.value, selectedSubcategory);

    }

    // Campaign Participation Modal Logic
    $('#campaign_type').change(function() {
        if ($(this).val() === 'bogo' || $(this).val() === 'buy2_get1') {
            $('#combo_details_container').slideDown();
        } else {
            $('#combo_details_container').slideUp();
        }
    });

    $(document).on('click', '.open-campaign-btn', function() {
        let productId = $(this).data('product-id');
        let btn = $(this);
        
        btn.html('<i class="fa fa-spinner fa-spin me-2 text-success"></i> Loading...');
        
        $.ajax({
            url: `/seller/products/${productId}/campaign-participation`,
            type: 'GET',
            success: function(response) {
                btn.html('<i class="fa fa-bullhorn me-2 text-success"></i> Campaign Participation');
                
                $('#campaign_product_id').val(response.product.id);
                $('#campaign_product_name').text(response.product.name);
                $('#campaign_product_sku').text(response.product.sku || 'N/A');
                $('#campaign_product_image').attr('src', response.product.thumbnail_url);
                
                if (response.participation) {
                    $('#campaign_type').val(response.participation.campaign_type).trigger('change');
                    $('#combo_details').val(response.participation.combo_details);
                } else {
                    $('#campaign_type').val('none').trigger('change');
                    $('#combo_details').val('');
                }
                
                $('#campaignModal').modal('show');
            },
            error: function() {
                btn.html('<i class="fa fa-bullhorn me-2 text-success"></i> Campaign Participation');
                alert('Error loading campaign data');
            }
        });
    });

    $('#saveCampaignBtn').click(function() {
        let productId = $('#campaign_product_id').val();
        let btn = $(this);
        let originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Saving...');
        
        $.ajax({
            url: `/seller/products/${productId}/campaign-participation`,
            type: 'POST',
            data: $('#campaignForm').serialize(),
            success: function(response) {
                btn.prop('disabled', false).html(originalText);
                $('#campaignModal').modal('hide');
                alert(response.message || 'Saved successfully');
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalText);
                alert(xhr.responseJSON?.message || 'Error saving campaign participation');
            }
        });
    });

});

</script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

document.querySelectorAll('.deleteBtn').forEach(button=>{

    button.addEventListener('click',function(){

        let form = this.closest('.deleteForm');

        Swal.fire({

            title: 'Delete Product ?',

            html: `
                <div style="font-size:14px;color:#6b7280">
                    This product will be permanently deleted.<br><br>

                    <b style="color:#dc2626">
                        This action cannot be undone!
                    </b>
                </div>
            `,

            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#dc2626',

            cancelButtonColor: '#6b7280',

            confirmButtonText: 'Yes Delete',

            cancelButtonText: 'Cancel',

            reverseButtons:true

        }).then((result)=>{

            if(result.isConfirmed){

                Swal.fire({

                    title:'Deleting...',

                    text:'Please wait',

                    allowOutsideClick:false,

                    didOpen:()=>{
                        Swal.showLoading();
                    }

                });

                form.submit();
            }

    });

});

</script>

<!-- Recommendations Modal -->
<div class="modal fade" id="recommendationsModal" tabindex="-1" aria-labelledby="recommendationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header text-white" style="background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%); border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold d-flex align-items-center text-white" id="recommendationsModalLabel">
                    <i class="fa fa-star me-2 text-warning"></i> Configure Product Recommendations
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light-subtle p-4" style="background-color: #f8fafc;">
                
                <!-- Source Product Info -->
                <div class="card p-3 mb-4 border-0 shadow-sm bg-white" style="border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <img id="source_product_img" src="" class="rounded" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #cbd5e1;">
                        <div>
                            <h5 id="source_product_name" class="fw-bold mb-1 text-slate-800" style="color: #0f172a;"></h5>
                            <div class="text-muted small mb-1">
                                <span>SKU: <strong id="source_product_sku"></strong></span> | 
                                <span class="badge bg-success border border-success ms-1" style="font-size: 10px;">Active</span>
                            </div>
                            <div id="source_product_price" class="text-primary fw-semibold fs-5" style="color: #004392 !important;"></div>
                        </div>
                    </div>
                </div>

                <p class="text-muted small mb-4">
                    Add related products/services that will be shown to buyers on product pages, cart, checkout, etc.
                </p>

                <!-- Form sections for 4 types of recommendations -->
                <form id="recommendationsForm">
                    @csrf
                    <input type="hidden" id="rec_product_id" name="product_id">

                    <!-- Section 1: Related Products -->
                    <div class="recommendation-section mb-4 p-3 bg-white rounded-4 shadow-sm border border-slate-100" style="border-radius: 14px;">
                        <h6 class="fw-bold text-dark d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2" style="background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">1</span> Related Products <span class="text-muted fw-normal ms-1" style="font-size: 12px;">(Complementary items)</span>
                        </h6>
                        <div class="position-relative mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control autocomplete-input" data-category="related" placeholder="Search products..." style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                                <button class="btn btn-primary add-selected-btn" data-category="related" type="button" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px; background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">Add Selected</button>
                            </div>
                            <div class="autocomplete-suggestions shadow-sm rounded-3 border bg-white d-none" style="max-height: 200px; overflow-y: auto;"></div>
                        </div>

                        <!-- Selected items list -->
                        <div class="selected-items-container d-flex flex-wrap gap-2 mb-3" id="selected_related"></div>

                        <!-- Popular Suggestions -->
                        <div class="suggestions-section">
                            <span class="text-muted small fw-semibold d-block mb-2">Popular Suggestions</span>
                            <div class="suggestions-container row g-2" id="suggestions_related"></div>
                        </div>
                    </div>

                    <!-- Section 2: Upsell Products -->
                    <div class="recommendation-section mb-4 p-3 bg-white rounded-4 shadow-sm border border-slate-100" style="border-radius: 14px;">
                        <h6 class="fw-bold text-dark d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2" style="background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">2</span> Upsell Products <span class="text-muted fw-normal ms-1" style="font-size: 12px;">(High value upgrade options)</span>
                        </h6>
                        <div class="position-relative mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control autocomplete-input" data-category="upsell" placeholder="Search products..." style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                                <button class="btn btn-primary add-selected-btn" data-category="upsell" type="button" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px; background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">Add Selected</button>
                            </div>
                            <div class="autocomplete-suggestions shadow-sm rounded-3 border bg-white d-none" style="max-height: 200px; overflow-y: auto;"></div>
                        </div>

                        <div class="selected-items-container d-flex flex-wrap gap-2 mb-3" id="selected_upsell"></div>

                        <div class="suggestions-section">
                            <span class="text-muted small fw-semibold d-block mb-2">Popular Suggestions</span>
                            <div class="suggestions-container row g-2" id="suggestions_upsell"></div>
                        </div>
                    </div>

                    <!-- Section 3: Cross-Sell Products -->
                    <div class="recommendation-section mb-4 p-3 bg-white rounded-4 shadow-sm border border-slate-100" style="border-radius: 14px;">
                        <h6 class="fw-bold text-dark d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2" style="background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">3</span> Cross-Sell Products <span class="text-muted fw-normal ms-1" style="font-size: 12px;">(Other useful items)</span>
                        </h6>
                        <div class="position-relative mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control autocomplete-input" data-category="cross_sell" placeholder="Search products..." style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                                <button class="btn btn-primary add-selected-btn" data-category="cross_sell" type="button" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px; background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">Add Selected</button>
                            </div>
                            <div class="autocomplete-suggestions shadow-sm rounded-3 border bg-white d-none" style="max-height: 200px; overflow-y: auto;"></div>
                        </div>

                        <div class="selected-items-container d-flex flex-wrap gap-2 mb-3" id="selected_cross_sell"></div>

                        <div class="suggestions-section">
                            <span class="text-muted small fw-semibold d-block mb-2">Popular Suggestions</span>
                            <div class="suggestions-container row g-2" id="suggestions_cross_sell"></div>
                        </div>
                    </div>

                    <!-- Section 4: Frequently Bought Together -->
                    <div class="recommendation-section mb-4 p-3 bg-white rounded-4 shadow-sm border border-slate-100" style="border-radius: 14px;">
                        <h6 class="fw-bold text-dark d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2" style="background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">4</span> Frequently Bought Together <span class="text-muted fw-normal ms-1" style="font-size: 12px;">(Bundles)</span>
                        </h6>
                        <div class="position-relative mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control autocomplete-input" data-category="bought_together" placeholder="Search products..." style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                                <button class="btn btn-primary add-selected-btn" data-category="bought_together" type="button" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px; background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">Add Selected</button>
                            </div>
                            <div class="autocomplete-suggestions shadow-sm rounded-3 border bg-white d-none" style="max-height: 200px; overflow-y: auto;"></div>
                        </div>

                        <div class="selected-items-container d-flex flex-wrap gap-2 mb-3" id="selected_bought_together"></div>

                        <div class="suggestions-section">
                            <span class="text-muted small fw-semibold d-block mb-2">Popular Suggestions</span>
                            <div class="suggestions-container row g-2" id="suggestions_bought_together"></div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer bg-light border-0" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Close</button>
                <button type="button" class="btn btn-success px-4" id="saveRecommendationsBtn" style="border-radius: 8px; background: linear-gradient(180deg, #10b981 0%, #059669 100%) !important; border: 0 !important; color: white !important;">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Recommendations Modal Custom CSS Styles */
.selected-product-chip {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 8px 12px;
    position: relative;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    min-width: 160px;
    max-width: 220px;
}
.selected-product-chip img {
    width: 36px;
    height: 36px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #f1f5f9;
}
.selected-product-chip-info {
    flex: 1;
    min-width: 0;
    line-height: 1.3;
}
.selected-product-chip-info .name {
    font-size: 11.5px;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}
.selected-product-chip-info .price {
    font-size: 10px;
    color: #64748b;
    font-weight: 600;
}
.selected-product-chip .remove-chip-btn {
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    font-size: 14px;
    padding: 2px;
    line-height: 1;
    transition: color 0.15s ease;
}
.selected-product-chip .remove-chip-btn:hover {
    color: #ef4444;
}

.suggestion-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    padding: 12px 10px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
}
.suggestion-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.suggestion-card img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 8px;
    border: 1px solid #f1f5f9;
}
.suggestion-card .name {
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    margin-bottom: 2px;
}
.suggestion-card .price {
    font-size: 10px;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
}
.suggestion-card .btn-add-suggestion {
    font-size: 10px;
    font-weight: 600;
    padding: 3px 12px;
    border-radius: 6px;
    border-width: 1px;
    transition: all 0.15s ease;
}

.autocomplete-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    max-height: 200px;
    overflow-y: auto;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    z-index: 1050;
    margin-top: 4px;
}
.autocomplete-suggestion-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}
.autocomplete-suggestion-item:last-child {
    border-bottom: none;
}
.autocomplete-suggestion-item:hover {
    background: #f8fafc;
}
.autocomplete-suggestion-item img {
    width: 32px;
    height: 32px;
    object-fit: cover;
    border-radius: 4px;
}

.recommendation-section {
    border: 1px solid #cbd5e1 !important;
    background-color: #ffffff;
    transition: border-color 0.2s ease;
}
.recommendation-section:hover {
    border-color: #004392 !important;
}
</style>

<script>
$(document).ready(function() {
    let currentProductId = null;
    let currentRecommendations = {
        related: [],
        upsell: [],
        cross_sell: [],
        bought_together: []
    };
    let popularProductsList = [];
    let autocompleteResultsCache = {};

    function resetModal() {
        currentRecommendations = {
            related: [],
            upsell: [],
            cross_sell: [],
            bought_together: []
        };
        autocompleteResultsCache = {};
        $('.autocomplete-input').val('');
        $('.autocomplete-suggestions').addClass('d-none').empty();
        $('.selected-items-container').empty();
        $('.suggestions-container').empty();
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function renderSelectedItems(cat) {
        const container = $(`#selected_${cat}`);
        container.empty();
        
        const items = currentRecommendations[cat];
        if (items.length === 0) {
            container.html('<span class="text-muted small italic">No products selected</span>');
            return;
        }
        
        items.forEach(item => {
            const chip = `
                <div class="selected-product-chip" data-id="${item.id}">
                    <img src="${item.thumbnail_url}">
                    <div class="selected-product-chip-info">
                        <span class="name" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</span>
                        <span class="price">₹${parseFloat(item.price || 0).toLocaleString('en-IN')}</span>
                    </div>
                    <button type="button" class="remove-chip-btn" data-category="${cat}" data-id="${item.id}">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            `;
            container.append(chip);
        });
    }

    function renderSuggestions(cat) {
        const container = $(`#suggestions_${cat}`);
        container.empty();
        
        const selectedIds = currentRecommendations[cat].map(item => parseInt(item.id));
        const availableSuggestions = popularProductsList.filter(item => !selectedIds.includes(parseInt(item.id))).slice(0, 4);
        
        if (availableSuggestions.length === 0) {
            container.html('<div class="col-12"><span class="text-muted small">No suggestions available</span></div>');
            return;
        }
        
        availableSuggestions.forEach(item => {
            const card = `
                <div class="col-3 col-md-3">
                    <div class="suggestion-card">
                        <img src="${item.thumbnail_url}">
                        <span class="name" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</span>
                        <span class="price">₹${parseFloat(item.price || 0).toLocaleString('en-IN')}</span>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-add-suggestion w-100" data-category="${cat}" data-id="${item.id}">
                            Add
                        </button>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function addProductToCategory(cat, item) {
        const selectedIds = currentRecommendations[cat].map(p => parseInt(p.id));
        if (!selectedIds.includes(parseInt(item.id))) {
            currentRecommendations[cat].push(item);
            renderSelectedItems(cat);
            renderSuggestions(cat);
        }
    }

    function removeProductFromCategory(cat, id) {
        currentRecommendations[cat] = currentRecommendations[cat].filter(p => parseInt(p.id) !== parseInt(id));
        renderSelectedItems(cat);
        renderSuggestions(cat);
    }

    function loadDefaultSuggestions() {
        $.ajax({
            url: '/seller/products/search-ajax',
            method: 'GET',
            data: { exclude_id: currentProductId },
            success: function(products) {
                popularProductsList = products;
                const categories = ['related', 'upsell', 'cross_sell', 'bought_together'];
                categories.forEach(cat => {
                    renderSuggestions(cat);
                });
            }
        });
    }

    // Click Recommendations action button
    $(document).on('click', '.open-recommendations-btn', function() {
        currentProductId = $(this).data('product-id');
        $('#rec_product_id').val(currentProductId);
        
        resetModal();
        
        Swal.fire({
            title: 'Loading...',
            text: 'Fetching product details',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: `/seller/products/${currentProductId}/recommendations`,
            method: 'GET',
            success: function(response) {
                Swal.close();
                $('#source_product_name').text(response.product.name);
                $('#source_product_sku').text(response.product.sku || 'N/A');
                $('#source_product_price').text('₹ ' + parseFloat(response.product.price || 0).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                $('#source_product_img').attr('src', response.product.thumbnail_url);
                
                const recs = response.recommendations;
                for (const cat in recs) {
                    currentRecommendations[cat] = recs[cat];
                    renderSelectedItems(cat);
                }
                
                loadDefaultSuggestions();
                
                $('#recommendationsModal').modal('show');
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load product recommendations.'
                });
            }
        });
    });

    // Autocomplete Input Search
    $(document).on('input', '.autocomplete-input', function() {
        const input = $(this);
        const cat = input.data('category');
        const query = input.val().trim();
        const suggestionsBox = input.closest('.position-relative').find('.autocomplete-suggestions');
        
        if (query.length < 2) {
            suggestionsBox.addClass('d-none').empty();
            return;
        }
        
        $.ajax({
            url: '/seller/products/search-ajax',
            method: 'GET',
            data: { q: query, exclude_id: currentProductId },
            success: function(products) {
                suggestionsBox.empty();
                if (products.length === 0) {
                    suggestionsBox.html('<div class="p-2 text-muted small text-center">No products found</div>').removeClass('d-none');
                    return;
                }
                
                const selectedIds = currentRecommendations[cat].map(item => parseInt(item.id));
                const filteredProducts = products.filter(p => !selectedIds.includes(parseInt(p.id)));
                
                if (filteredProducts.length === 0) {
                    suggestionsBox.html('<div class="p-2 text-muted small text-center">All matching products already added</div>').removeClass('d-none');
                    return;
                }
                
                filteredProducts.forEach(item => {
                    autocompleteResultsCache[item.id] = item;
                    const row = `
                        <div class="autocomplete-suggestion-item" data-category="${cat}" data-id="${item.id}">
                            <img src="${item.thumbnail_url}">
                            <div style="flex: 1; min-width: 0;">
                                <div class="fw-semibold text-slate-800 text-truncate" style="font-size: 12px; color: #1e293b;">${escapeHtml(item.name)}</div>
                                <div class="text-muted" style="font-size: 10px;">SKU: ${item.sku || 'N/A'} | ₹${parseFloat(item.price || 0).toLocaleString('en-IN')}</div>
                            </div>
                        </div>
                    `;
                    suggestionsBox.append(row);
                });
                suggestionsBox.removeClass('d-none');
            }
        });
    });

    // Close suggestions box when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $('.autocomplete-suggestions').addClass('d-none').empty();
        }
    });

    // Click suggestion item in autocomplete box
    $(document).on('click', '.autocomplete-suggestion-item', function() {
        const cat = $(this).data('category');
        const id = parseInt($(this).data('id'));
        const item = autocompleteResultsCache[id];
        
        if (item) {
            addProductToCategory(cat, item);
        }
        
        const relativeDiv = $(this).closest('.position-relative');
        relativeDiv.find('.autocomplete-input').val('');
        relativeDiv.find('.autocomplete-suggestions').addClass('d-none').empty();
    });

    // Click Popular Suggestion "Add"
    $(document).on('click', '.btn-add-suggestion', function() {
        const cat = $(this).data('category');
        const id = parseInt($(this).data('id'));
        const item = popularProductsList.find(p => parseInt(p.id) === id);
        if (item) {
            addProductToCategory(cat, item);
        }
    });

    // Remove selected product chip
    $(document).on('click', '.remove-chip-btn', function() {
        const cat = $(this).data('category');
        const id = $(this).data('id');
        removeProductFromCategory(cat, id);
    });

    // Add Selected Button
    $(document).on('click', '.add-selected-btn', function() {
        const cat = $(this).data('category');
        const relativeDiv = $(this).closest('.position-relative');
        const firstItem = relativeDiv.find('.autocomplete-suggestion-item').first();
        if (firstItem.length) {
            firstItem.click();
        } else {
            Swal.fire({
                icon: 'info',
                title: 'No Selection',
                text: 'Please type to search and select a product from suggestions.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });

    // Save recommendations
    $(document).on('click', '#saveRecommendationsBtn', function() {
        let postData = {
            _token: '{{ csrf_token() }}',
            related: currentRecommendations.related.map(p => p.id),
            upsell: currentRecommendations.upsell.map(p => p.id),
            cross_sell: currentRecommendations.cross_sell.map(p => p.id),
            bought_together: currentRecommendations.bought_together.map(p => p.id)
        };
        
        Swal.fire({
            title: 'Saving...',
            text: 'Updating product recommendations',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: `/seller/products/${currentProductId}/recommendations`,
            method: 'POST',
            data: postData,
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Product recommendations updated successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to save recommendations.'
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Server error occurred while saving.'
                });
            }
        });
    });
});
</script>
{{-- GLOBAL CAMPAIGN PARTICIPATION MODAL --}}
<div class="modal fade" id="globalCampaignModal" tabindex="-1" aria-labelledby="globalCampaignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header border-0 pb-3" style="background: linear-gradient(135deg, #28a745, #218838);">
                <h5 class="modal-title fw-bold d-flex align-items-center text-white" id="globalCampaignModalLabel">
                    <i class="fa fa-bullhorn me-2 text-white bg-white bg-opacity-25 p-2 rounded-circle"></i>
                    Join Campaign
                </h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 bg-light" style="max-height: 65vh; overflow-y: auto;">
                <p class="text-muted small mb-3">Select a campaign type for each product to participate. Leave as "None" to opt out.</p>

                <form id="globalCampaignForm">
                    @csrf

                    <div class="mb-3 bg-white p-3 border rounded shadow-sm">
                        <label class="form-label fw-bold text-primary"><i class="fa fa-calendar-alt me-1"></i> Select Global Campaign Event (Optional)</label>
                        <select class="form-select shadow-sm border-0 bg-light" name="global_advertisement_id" id="globalCampaignSelect">
                            <option value="">-- Select an active campaign event --</option>
                        </select>
                        <div id="campaignDatesDisplay" class="mt-2 text-muted small" style="display: none;">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fa fa-play-circle me-1"></i> Starts: <span id="campaignStartDate"></span></span>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger ms-2"><i class="fa fa-stop-circle me-1"></i> Ends: <span id="campaignEndDate"></span></span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover bg-white shadow-sm rounded-3 border" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 80px;">Select</th>
                                    <th>Product</th>
                                    <th>Offer / Campaign Details</th>
                                </tr>
                            </thead>
                            <tbody id="globalCampaignProductList">
                                <tr>
                                    <td colspan="3" class="text-center py-4"><i class="fa fa-spinner fa-spin text-success"></i> Loading products...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer border-0 pt-0 bg-light pb-4 px-4 d-flex justify-content-between">
                <button type="button" class="btn btn-light border px-4 shadow-sm" data-bs-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                <button type="button" class="btn btn-success px-4 shadow-sm fw-bold d-flex align-items-center" id="saveGlobalCampaignBtn" style="border-radius: 6px;">
                    <i class="fa fa-save me-2"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $(document).on('click', '.open-global-campaign-btn', function() {
        let btn = $(this);
        let originalHtml = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin me-1"></i> Loading...').prop('disabled', true);
        
        $.ajax({
            url: `/seller/products/campaign-participation/all`,
            type: 'GET',
            success: function(response) {
                btn.html(originalHtml).prop('disabled', false);
                
                let tbody = $('#globalCampaignProductList');
                tbody.empty();

                // Populate Global Campaigns dropdown
                let adSelect = $('#globalCampaignSelect');
                adSelect.find('option:not(:first)').remove();
                if (response.advertisements && response.advertisements.length > 0) {
                    let firstAdId = null;
                    if (response.products && response.products.length > 0) {
                        let p1 = response.products.find(p => p.participation && p.participation.advertisement_id);
                        if (p1) firstAdId = p1.participation.advertisement_id;
                    }

                    response.advertisements.forEach(function(ad) {
                        let sel = (firstAdId == ad.id) ? 'selected' : '';
                        let sDate = ad.start_date ? new Date(ad.start_date).toLocaleDateString() : 'Immediate';
                        let eDate = ad.end_date ? new Date(ad.end_date).toLocaleDateString() : 'Ongoing';
                        adSelect.append(`<option value="${ad.id}" data-start="${sDate}" data-end="${eDate}" ${sel}>${ad.title}</option>`);
                    });
                    adSelect.trigger('change');
                }

                if (response.products.length === 0) {

                    tbody.append('<tr><td colspan="3" class="text-center py-4 text-muted">No products found.</td></tr>');
                } else {
                    // Store products globally for combo dropdown
                    window._campaignProducts = response.products;

                    response.products.forEach(function(product) {
                        let p = product.participation || {};
                        let pType = p.campaign_type || 'none';
                        let comboData = { buy_qty: 1, get_qty: 1, free_product_id: product.id };
                        if (p.combo_details) {
                            try {
                                comboData = JSON.parse(p.combo_details);
                            } catch (e) {}
                        }

                        // Generate product options for free product dropdown
                        let productOptions = '';
                        response.products.forEach(function(optProd) {
                            let selected = (comboData.free_product_id == optProd.id) ? 'selected' : '';
                            productOptions += `<option value="${optProd.id}" ${selected}>${optProd.name}</option>`;
                        });

                        // Extract existing discount percent if any
                        let discountPercent = 10;
                        if (pType && pType.indexOf('%') !== -1) {
                            let match = pType.match(/^(\d+)\%/);
                            if (match) {
                                discountPercent = parseInt(match[1]);
                            }
                        }

                        let tr = `
                            <tr>
                                <td class="text-center align-middle">
                                    <input class="product-campaign-checkbox" type="checkbox" name="products[${product.id}][selected]" value="1" ${pType !== 'none' ? 'checked' : ''} style="width: 20px; height: 20px; cursor: pointer; display: inline-block !important; opacity: 1 !important; visibility: visible !important;">
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <img src="${product.thumbnail_url}" style="width:40px; height:40px; object-fit:cover; border-radius:4px;" class="me-2 border">
                                        <div>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 250px;" title="${product.name}">${product.name}</div>
                                            <small class="text-muted">₹${parseFloat(product.price).toFixed(2)}</small>
                                        </div>
                                    </div>
                                    <input type="hidden" name="products[${product.id}][id]" value="${product.id}">
                                    <input type="hidden" name="products[${product.id}][campaign_type]" value="${pType}" class="product-campaign-type-hidden">
                                </td>
                                <td class="align-middle">
                                    <!-- Combo Inputs -->
                                    <div class="combo-inputs" style="display:none;">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="small fw-bold">Buy</span>
                                            <input type="number" class="form-control form-control-sm border-0 shadow-sm" name="products[${product.id}][buy_qty]" value="${comboData.buy_qty}" min="1" style="width:60px;">
                                            <span class="small fw-bold text-nowrap">Get</span>
                                            <input type="number" class="form-control form-control-sm border-0 shadow-sm" name="products[${product.id}][get_qty]" value="${comboData.get_qty}" min="1" style="width:60px;">
                                            <span class="small fw-bold text-nowrap">Free</span>
                                        </div>
                                        <select class="form-select form-select-sm border-0 shadow-sm" name="products[${product.id}][free_product_id]" style="max-width: 200px;">
                                            ${productOptions}
                                        </select>
                                    </div>

                                    <!-- Discount Inputs (Preset by Admin) -->
                                    <div class="discount-preset" style="display:none;">
                                        <span class="badge bg-primary text-white p-2" style="font-size:0.85rem;">
                                            <i class="fa fa-percentage me-1"></i> <span class="discount-preset-val">20</span>% Offer (Preset)
                                        </span>
                                    </div>

                                    <!-- Discount Inputs (Seller Editable) -->
                                    <div class="discount-custom" style="display:none;">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="small fw-bold text-nowrap">Discount %:</span>
                                            <input type="number" class="form-control form-control-sm border-0 shadow-sm discount-custom-input" value="${discountPercent}" min="1" max="100" style="width:80px;">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tbody.append(tr);
                    });

                    // Update visibilities based on initial ad selection
                    updateComboVisibility();
                }
                
                $('#globalCampaignModal').modal('show');
            },
            error: function() {
                btn.html(originalHtml).prop('disabled', false);
                alert('Error loading products for campaign');
            }
        });
    });

    function updateComboVisibility() {
        let adTitle = $('#globalCampaignSelect option:selected').text().toLowerCase();
        let isCombo = (adTitle.includes('buy') && adTitle.includes('get')) || adTitle.includes('combo') || adTitle.includes('bogo');
        
        if (isCombo) {
            $('.combo-inputs').show();
            $('.discount-preset').hide();
            $('.discount-custom').hide();
            $('.product-campaign-type-hidden').val('combo');
        } else {
            $('.combo-inputs').hide();
            
            // Check if there is a percentage preset in the campaign title (e.g. "Diwali 20% Sale")
            let match = adTitle.match(/\b(\d+)\s*%/);
            if (match) {
                let presetVal = match[1];
                $('.discount-preset-val').text(presetVal);
                $('.discount-preset').show();
                $('.discount-custom').hide();
                $('.product-campaign-type-hidden').val(presetVal + '%_Offer');
            } else {
                $('.discount-preset').hide();
                $('.discount-custom').show();
                
                // For custom inputs, set hidden fields per product based on their input value
                $('.discount-custom-input').each(function() {
                    let val = $(this).val() || 10;
                    $(this).closest('tr').find('.product-campaign-type-hidden').val(val + '%_Offer');
                });
            }

            // Reset combo fields to defaults
            $('.combo-inputs input[name$="[buy_qty]"]').val(1);
            $('.combo-inputs input[name$="[get_qty]"]').val(1);
            $('.combo-inputs select[name$="[free_product_id]"] option').each(function(){
                let $opt = $(this);
                let prodId = $opt.val();
                let rowProdId = $opt.closest('tr').find('input[name$="[id]"]').val();
                $opt.prop('selected', prodId == rowProdId);
            });
        }
    }

    $(document).on('input change', '.discount-custom-input', function() {
        let val = $(this).val() || 10;
        $(this).closest('tr').find('.product-campaign-type-hidden').val(val + '%_Offer');
    });

    $(document).on('change', '#globalCampaignSelect', function() {
        let opt = $(this).find(':selected');
        if (opt.val()) {
            let start = opt.data('start');
            let end = opt.data('end');
            $('#campaignStartDate').text(start);
            $('#campaignEndDate').text(end);
            $('#campaignDatesDisplay').show();
        } else {
            $('#campaignDatesDisplay').hide();
        }
        updateComboVisibility();
    });

    $('#saveGlobalCampaignBtn').click(function() {
        let btn = $(this);
        let originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Saving...');
        
        $.ajax({
            url: `/seller/products/campaign-participation/all`,
            type: 'POST',
            data: $('#globalCampaignForm').serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                btn.prop('disabled', false).html(originalText);
                $('#globalCampaignModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: response.message || 'Saved successfully',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalText);
                let msg = 'Error saving campaign participations';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                else if (xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
                alert(msg);
            }
        });
    });
    $('#category').change(function() {
        let catId = $(this).val();
        let subcat = $('#subcategory');
        subcat.empty().append('<option value="">Sub Category</option>');
        
        if (catId) {
            $.get('/seller/get-subcategories/' + catId, function(data) {
                if (data.success && data.data) {
                    let oldSub = '{{ request("subcategory") }}';
                    $.each(data.data, function(index, item) {
                        let selected = (item.id == oldSub) ? 'selected' : '';
                        subcat.append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
                    });
                }
            });
        }
    });

    if ($('#category').val()) {
        $('#category').trigger('change');
    }
});
</script>

@endsection
