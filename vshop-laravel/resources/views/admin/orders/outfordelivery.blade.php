
























@extends('layouts.vertical', ['title' => 'Out For Delivery Orders'])

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Out For Delivery Orders</h4>
    <div class="card">
        <div class="card-body">
            @include('layouts.partials.order-table', ['orders' => $orders])
        </div>
    </div>
</div>
@endsection

