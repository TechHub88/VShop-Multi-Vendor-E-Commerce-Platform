@extends('vshop.seller.main')

@section('content')

{{-- HEADER --}}
@include('vshop.seller.partials.marketplace-header',[
'title'=>'Products'
])


<div class="marketplace-wrapper">

{{-- LEFT CONTENT --}}
<div class="marketplace-left">

<div class="d-flex justify-content-between align-items-center mb-4">

<h4>My Products</h4>

<a href="{{ route('seller.products.basic.save') }}"
class="add-btn">+</a>

</div>

@if(isset($product))
    @include('vshop.seller.partials.draft-status', ['product' => $product])
@endif


@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif


{{-- PRODUCT LIST --}}
@forelse($products as $product)

<div class="banner-card">

<div class="banner-info">

<h6>{{ $product->name }}</h6>

<p class="small text-muted mb-1">
{{ \Illuminate\Support\Str::limit($product->short_summary,80) }}
</p>

<p class="small mb-1">
Category :
<strong>{{ $product->category->name ?? '-' }}</strong>
</p>

<p class="small mb-1">
Type :
<strong>{{ ucfirst($product->type) }}</strong>
</p>


<span class="status
@if($product->status=='active') approved
@elseif($product->status=='pending') pending
@else rejected
@endif">

{{ ucfirst($product->status) }}

</span>

</div>



<div class="banner-actions">

@if($product->thumbnail)
<img src="{{ asset($product->thumbnail) }}"
class="banner-thumb">
@endif


<div class="action-icons">

<a href="{{ route('seller.products.basic.edit',$product->id) }}"
class="edit-icon">✎</a>


<form action="{{ route('seller.products.delete',$product->id) }}"
method="POST"
style="display:inline-block">

@csrf
@method('DELETE')

<button class="delete-icon"
onclick="return confirm('Delete this product?')"
style="border:none;background:none">

🗑

</button>

</form>

</div>

</div>

</div>

@empty

<div class="alert alert-info">
No Products Added
</div>

@endforelse


<div class="mt-3">
{{ $products->links() }}
</div>


</div>



{{-- RIGHT SIDEBAR --}}
<div class="marketplace-right">

@include('vshop.seller.partials.product-progress',[
'product'=>$products->first() ?? null
])

</div>


</div>



<style>

/* MASTER WRAPPER */
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
position:sticky;
top:20px;
}

/* ADD BUTTON */
.add-btn{
width:38px;
height:38px;
border-radius:50%;
background:#f1f3f6;
display:flex;
align-items:center;
justify-content:center;
font-size:22px;
text-decoration:none;
color:#333;
}

/* PRODUCT CARD */
.banner-card{
background:#fff;
padding:18px;
border-radius:12px;
margin-bottom:18px;
box-shadow:0 3px 12px rgba(0,0,0,.05);
display:flex;
justify-content:space-between;
align-items:center;
}

/* INFO */
.banner-info h6{
font-weight:600;
margin-bottom:6px;
}

/* STATUS */
.status{
font-size:12px;
padding:4px 10px;
border-radius:20px;
}

.status.approved{
background:#d4edda;
color:#2e7d32;
}

.status.pending{
background:#fff3cd;
color:#856404;
}

.status.rejected{
background:#f8d7da;
color:#c62828;
}

/* RIGHT SIDE */
.banner-actions{
display:flex;
align-items:center;
gap:15px;
}

.banner-thumb{
width:70px;
height:50px;
object-fit:cover;
border-radius:6px;
}

/* ICONS */
.action-icons a{
margin-left:8px;
font-size:16px;
text-decoration:none;
}

.edit-icon{
color:#0b4da1;
}

.delete-icon{
color:#dc3545;
}

</style>

@endsection
