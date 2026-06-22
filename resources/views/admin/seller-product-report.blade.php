@extends('layouts.vertical', ['title' => 'Seller Product Report'])

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
    
    .salesReportSearchBox {
        width: 25%;
    }
    .filter-area {
        padding: 12px;
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

<div class="container-fluid py-4a px-4a pt-2 pb-2">

<div class="d-flex justify-content-between align-items-center mb-4">
<h4 class="fw-bold">Seller Product Report</h4>
</div>


<div class="card shadow-sm">

<div class="card-header" style="padding:15px;background:#fff;">
<h6 class="mb-0">Seller Wise Product Upload Report</h6>
</div>


<div class="card-body">

<!-- SEARCH AREA -->

<div class="filter-area mb-3 bg-light rounded salesReportSearchArea">

<div class="d-flex flex-wrap gap-2 align-items-end">

<div class="salesReportSearchBox">
<label class="form-label small text-muted">Search</label>
<input type="text"
id="keywordSearch"
class="form-control form-control-sm"
placeholder="Seller / Product / Category">
</div>


<div class="salesReportSearchBox">

<label class="form-label small text-muted">Seller</label>

<select id="sellerFilter" class="form-select form-select-sm">

<option value="">All Sellers</option>

@foreach($sellers as $seller)

<option value="{{ $seller->id }}">
{{ $seller->name }}
</option>

@endforeach

</select>

</div>


<div class="salesReportSearchBox">

<label class="form-label small text-muted">Category</label>

<select id="categoryFilter" class="form-select form-select-sm">

<option value="">All Categories</option>

@foreach($categories as $cat)

<option value="{{ $cat->id }}">
{{ $cat->name }}
</option>

@endforeach

</select>

</div>


<div>
<button class="btn btn-success btn-sm" id="applySearch">
<i class="fas fa-search"></i> Search
</button>
<button class="btn reset-btn btn-sm">Reset</button>
</div>

</div>

</div>



<!-- TABLE -->

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-light">

<tr>

<th>SL</th>
<th>Seller Name</th>
<th>Category Name</th>
<th>Total Products</th>
<th>Total Revenue</th>

</tr>

</thead>


<tbody id="reportTableBody">

@forelse($reports as $index => $row)

<tr>

<td>{{ $index+1 }}</td>

<td>{{ $row->seller_name }}</td>

<td>{{ $row->category_name }}</td>

<td>{{ $row->total_products }}</td>

<td>₹ {{ number_format($row->total_revenue ?? 0,2) }}</td>

</tr>

@empty

<tr>
<td colspan="5" class="text-center">
No records found
</td>
</tr>

@endforelse

</tbody>

</table>

</div>

</div>

</div>

</div>

@endsection