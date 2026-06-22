@extends('layouts.vertical', ['title' => 'Top Selling Products'])

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
    .reset-btn {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
    .reset-btn:hover {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
</style>

<div class="container-fluid pt-2 pb-2">

<div class="d-flex justify-content-between align-items-center mb-4">

<h4 class="fw-bold">Top Selling Products Report</h4>

</div>

<div class="card shadow-sm">

<div class="card-header">
<h6 class="mb-0">Seller Wise Highest Selling Products</h6>
</div>

<div class="card-body">

<!-- SEARCH -->

<div class="mb-3">
<div class="row">
    <div class="col-lg-10">
        <input type="text"
        id="keywordSearch"
        class="form-control form-control-sm"
        placeholder="Search Seller / Product / Category">
    </div>
    <div class="col-lg-2">
        <button class="btn reset-btn btn-sm">Reset</button>
    </div>
</div>

</div>


<div class="table-responsive">

<table id="topSellingTable"
class="table table-bordered table-hover">

<thead class="table-light">

<tr>

<th>SL</th>
<th>Seller</th>
<th>Category</th>
<th>Product</th>
<th>Price</th>
<th>Quantity Sold</th>
<th>Total Sales</th>

</tr>

</thead>

<tbody>

@foreach($reports as $index => $row)

<tr>

<td>{{ $index+1 }}</td>

<td>{{ $row->seller_name }}</td>

<td>{{ $row->category_name }}</td>

<td>{{ $row->product_name }}</td>

<td>₹{{ number_format($row->price,2) }}</td>

<td>{{ $row->total_quantity }}</td>

<td class="fw-bold text-success">
₹{{ number_format($row->total_sales,2) }}
</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>

</div>

<script>

$(document).ready(function(){

$('#topSellingTable').DataTable();

});

</script>

@endsection