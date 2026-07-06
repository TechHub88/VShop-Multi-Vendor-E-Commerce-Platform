@extends('layouts.vertical',['title'=>'Commission Report'])

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

<h4 class="mb-4 fw-bold">Commission Report</h4>

<div class="card">

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered">

<thead class="table-light">

<tr>

<th>SL</th>
<th>Seller</th>
<th>Product</th>
<th>Price</th>
<th>Commission</th>
<th>Seller Earning</th>

</tr>

</thead>

<tbody>

@foreach($reports as $index=>$row)

<tr>

<td>{{ $index+1 }}</td>

<td>{{ $row->seller_name }}</td>

<td>{{ $row->product_name }}</td>

<td>₹{{ $row->price }}</td>

<td class="text-danger">
₹{{ $row->commission_amount }}
</td>

<td class="text-success">
₹{{ $row->seller_earning }}
</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>

</div>

@endsection
