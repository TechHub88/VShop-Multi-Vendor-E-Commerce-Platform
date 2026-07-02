@extends('vshop.seller.main', ['title' => 'Cancelled Orders'])

@section('content')

{{-- ================= AMAZ HEADER STYLE ================= --}}
<style>
    .amaz-header-btn {
        border: 1px solid #a66bff;
        color: #a66bff;
        background: #f8f2ff;
        padding: 7px 18px;
        border-radius: 25px;
        font-weight: 500;
        font-size: 13px;
    }
    .amaz-header-btn.active {
        background: #a66bff;
        color: #fff;
    }
    .amaz-search { max-width: 240px; }
    .amaz-search input {
        border-radius: 10px;
        border: 1px solid #d1c2ff;
    }
    .amaz-export-btn {
        background: #f1eaff;
        border: none;
        padding: 8px 10px;
        border-radius: 8px;
        margin-left: 6px;
        font-size: 14px;
        color: #7533ff;
    }
</style>

<div class="container-fluid py-4">

<div class="card shadow-sm">
<div class="card-body">

{{-- TABS --}}
<div class="d-flex flex-wrap gap-2 mb-3">

    <a href="{{ route('seller.orders.index') }}">
        <button class="amaz-header-btn">All Orders</button>
    </a>

    <a href="{{ route('seller.orders.processing') }}">
        <button class="amaz-header-btn">Processing</button>
    </a>

    <a href="{{ route('seller.orders.completed') }}">
        <button class="amaz-header-btn">Completed</button>
    </a>

    <a href="{{ route('seller.orders.cancelled') }}">
        <button class="amaz-header-btn active">Cancelled</button>
    </a>
      <a href="{{ route('seller.orders.refund') }}">
        <button class="amaz-header-btn ">Returned</button>
    </a>

</div>

<h4 class="fw-semibold mb-3">Cancelled Orders</h4>

@include('vshop.seller.orders.partials.table', ['orders' => $orders])

</div>
</div>
</div>
@endsection
