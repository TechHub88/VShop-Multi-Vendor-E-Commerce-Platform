@extends('vshop.seller.main', ['title' => 'Invoice'])
<style>
    .nav-btn {
        border: 1px solid #004392;
        color: #004392;
        background: transparent;
        border-radius: 5px;
        width: 35px;
        height: 38px;
    }
    .top-back-heading-sec h5 {
        color: #0f172a !important;
    }
    .top-back-heading-sec {
        align-items: center;
        gap: 10px;
    }
</style>

@section('content')

<div class="container-fluid py-4">
    <div class="d-flex align-items-center top-back-heading-sec mb-3">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <h5 class="mb-0 d-flex align-items-center gap-2"
            style="
                color:#0d6efd;
                font-weight:600;
            ">
            Customer Reviews
        </h5>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Invoice #{{ $order->id }}</h4>

            <button onclick="window.print()" class="btn btn-primary">
                Print Invoice
            </button>
        </div>

        <div class="card-body">

            <h5>Customer Information</h5>

            <p>
                <strong>Name:</strong> {{ $order->customer->name ?? '-' }} <br>
                <strong>Email:</strong> {{ $order->customer->email ?? '-' }} <br>

                <strong>Address:</strong>
                {{ $order->customer->firstAddress->address ?? '-' }}
            </p>

            <hr>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Color</th>
                            <th>Size</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>

                        @php $grandTotal = 0; @endphp

                        @foreach($order->items as $item)

                        @php
                            $total = $item->price * $item->quantity;
                            $grandTotal += $total;
                        @endphp

                        <tr>
                            <td>{{ $item->product->name ?? '-' }}</td>
                            <td>{{ $item->product->category->name ?? '-' }}</td>
                            <td>{{ $item->color->name ?? '-' }}</td>
                            <td>{{ $item->size->name ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ number_format($item->price,2) }}</td>
                            <td>₹{{ number_format($total,2) }}</td>
                        </tr>

                        @endforeach

                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-end">Grand Total</th>
                            <th>₹{{ number_format($grandTotal,2) }}</th>
                        </tr>
                    </tfoot>

                </table>
            </div>

        </div>
    </div>

</div>

@endsection