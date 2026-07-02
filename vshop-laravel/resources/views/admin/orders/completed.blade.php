@extends('layouts.vertical', ['title' => 'Completed Orders'])

@section('content')
<div class="container-fluid">

    <h4 class="mb-3">Completed Orders</h4>

    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @include('layouts.partials.order-table', ['orders' => $orders])
        </div>
    </div>

</div>
@endsection
