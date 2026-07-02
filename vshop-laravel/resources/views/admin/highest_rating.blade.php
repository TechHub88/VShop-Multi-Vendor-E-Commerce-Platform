@extends('layouts.vertical',['title'=>'Highest Rating Report'])

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

<h4 class="mb-4 fw-bold">⭐ Highest Rating Report</h4>

<div class="card shadow-sm">
<div class="card-body">

<!-- FILTER -->
<form method="GET" action="{{ route('admin.reports.highest.rating') }}">

<div class="row mb-3">

<div class="col-md-4">
<input type="text" name="keyword"
value="{{ request('keyword') }}"
class="form-control form-control-sm"
placeholder="Search seller, product or comment">
</div>

<div class="col-md-3">
<select name="rating" class="form-control form-control-sm">
<option value="">Filter Rating</option>
<option value="5" {{ request('rating')==5?'selected':'' }}>⭐ 5 Star Only</option>
<option value="4" {{ request('rating')==4?'selected':'' }}>⭐ 4 Star+</option>
</select>
</div>

<div class="col-md-2">
<button type="submit" class="btn btn-success btn-sm w-100">Search</button>
</div>

<div class="col-md-2">
<a href="{{ route('admin.reports.highest.rating') }}"
class="btn btn-secondary btn-sm w-100">Reset</a>
</div>

</div>
</form>

<!-- TABLE -->
<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-light">
<tr>
<th>SL</th>
<th>Seller</th>
<th>Product</th>
<th>Rating</th>
<th>Comment</th>
<th>Date</th>
</tr>
</thead>

<tbody>

@forelse($reviews as $index=>$row)

<tr>
<td>{{ $index+1 }}</td>

<td>
<b>{{ $row->seller_name }}</b>
</td>

<td>{{ $row->product_name }}</td>

<td>
<span class="badge bg-success">⭐ {{ $row->rating }}</span>
</td>

<td style="max-width:300px">
{{ $row->comment }}
</td>

<td>
{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}
</td>

</tr>

@empty
<tr>
<td colspan="6" class="text-center text-muted">
No high rating reviews found
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