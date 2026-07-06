@extends('layouts.vertical',['title'=>'Seller Negative Reviews'])

@section('content')

<style>
    .table th {
        background: #004392 !important;
        color: #fff !important;
    }
    .btn-primary,
    .btn-warning,
    .btn-danger,
    .btn-info {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-light border {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
    .btn-secondary,
    .btn-outline-secondary {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .nav-tabs .nav-link.active {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
</style>

<div class="container-fluid pt-2">

<h4 class="mb-4 fw-bold">Seller Negative Review Report</h4>

<div class="card shadow-sm">
<div class="card-body">

<!-- FILTER -->
<form method="GET" action="{{ route('admin.reports.seller-negative') }}">
<div class="row mb-3">

<div class="col-md-4">
<input type="text" name="keyword" value="{{ request('keyword') }}"
class="form-control form-control-sm"
placeholder="Search seller, product or comment">
</div>

<div class="col-md-3">
<select name="rating" class="form-control form-control-sm">
<option value="">Filter Rating</option>
<option value="1" {{ request('rating')==1?'selected':'' }}>⭐ Rating 1</option>
<option value="2" {{ request('rating')==2?'selected':'' }}>⭐ Rating 2</option>
<option value="3" {{ request('rating')==3?'selected':'' }}>⭐ Rating 3</option>
<option value="4" {{ request('rating')==4?'selected':'' }}>⭐ Rating 4</option>
<option value="5" {{ request('rating')==5?'selected':'' }}>⭐ Rating 5</option>
</select>
</div>

<div class="col-md-2">
<button class="btn btn-success btn-sm w-100">Search</button>
</div>

<div class="col-md-2">
<a href="{{ route('admin.reports.seller-negative') }}"
class="btn btn-secondary btn-sm w-100">Reset</a>
</div>

</div>
</form>

<!-- TABLE -->
<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">

<thead class="table-light">
<tr>
<th>SL</th>
<th>Seller</th>
<th>Product</th>
<th>Rating</th>
<th>Comment</th>
<th>Date</th>
<th width="180">Action</th>
</tr>
</thead>

<tbody>

@forelse($reviews as $index=>$row)
<tr>

<td>{{ $index+1 }}</td>

<td>
<b>{{ $row->seller_name }}</b>
@if($row->seller_status=='banned')
<span class="badge bg-danger ms-2">BANNED</span>
@endif
</td>

<td>{{ $row->product_name }}</td>

<td>
<span class="badge bg-danger">⭐ {{ $row->rating }}</span>
</td>

<td style="max-width:300px">{{ $row->comment }}</td>

<td>{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</td>

<td>

<div class="d-flex flex-wrap gap-1">

<!-- WARNING -->
<button class="btn btn-info btn-sm warningBtn"
title="Send Warning"
data-seller="{{ $row->seller_id }}"
data-product="{{ $row->product_id }}"
data-productname="{{ $row->product_name }}">
<i class="fa-solid fa-triangle-exclamation"></i>
</button>

<!-- BAN -->
@if($row->seller_status!='banned')
<button class="btn btn-danger btn-sm banSellerBtn"
title="Ban Seller"
data-id="{{ $row->seller_id }}">
<i class="fa-solid fa-user-slash"></i>
</button>
@endif

<!-- REJECT -->
@if($row->product_status!='rejected')
<button class="btn btn-warning btn-sm rejectProductBtn"
title="Reject Product"
data-id="{{ $row->product_id }}">
<i class="fa-solid fa-ban"></i>
</button>
@else
<span class="badge bg-secondary">Rejected</span>
@endif

</div>

</td>

</tr>

@empty
<tr>
<td colspan="7" class="text-center text-muted">
No negative reviews found
</td>
</tr>
@endforelse

</tbody>

</table>
</div>

</div>
</div>
</div>

<!-- WARNING MODAL -->
<div class="modal fade" id="warningModal">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Send Warning</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input type="hidden" id="seller_id">
<input type="hidden" id="product_id">

<div class="mb-2">
<label>Product</label>
<input type="text" id="product_name" class="form-control" readonly>
</div>

<div class="mb-2">
<label>Reason</label>
<textarea id="warning_reason" class="form-control"></textarea>
</div>

</div>

<div class="modal-footer">
<button class="btn btn-primary sendWarningBtn">Send Warning</button>
</div>

</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

/* BAN SELLER */
$(document).on('click','.banSellerBtn',function(){
let sellerId = $(this).data('id');

Swal.fire({
title:'Ban this seller?',
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#d33'
}).then((result)=>{
if(result.isConfirmed){
$.post('{{ route("admin.reports.ban-seller") }}',{
seller_id:sellerId,_token:'{{ csrf_token() }}'
},function(){
Swal.fire('Banned!','','success').then(()=>location.reload());
});
}
});
});

/* REJECT PRODUCT */
$(document).on('click','.rejectProductBtn',function(){
let productId=$(this).data('id');

Swal.fire({
title:'Reject product?',
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#f0ad4e'
}).then((result)=>{
if(result.isConfirmed){
$.post('{{ route("admin.reports.reject-product") }}',{
product_id:productId,_token:'{{ csrf_token() }}'
},function(){
Swal.fire('Rejected!','','success').then(()=>location.reload());
});
}
});
});

/* OPEN MODAL (FIXED) */
let warningModal = new bootstrap.Modal(document.getElementById('warningModal'));

$(document).on('click','.warningBtn',function(){
$('#seller_id').val($(this).data('seller'));
$('#product_id').val($(this).data('product'));
$('#product_name').val($(this).data('productname'));
$('#warning_reason').val('');
warningModal.show();
});

/* SEND WARNING */
$(document).on('click','.sendWarningBtn',function(){

let seller_id=$('#seller_id').val();
let product_id=$('#product_id').val();
let reason=$('#warning_reason').val();

if(!reason){
Swal.fire('Enter reason','','warning');
return;
}

$.post('{{ route("admin.reports.send-warning") }}',{
seller_id:seller_id,
product_id:product_id,
reason:reason,
_token:'{{ csrf_token() }}'
},function(){
warningModal.hide();
Swal.fire('Warning Sent','','success');
});
});

</script>

@endsection
