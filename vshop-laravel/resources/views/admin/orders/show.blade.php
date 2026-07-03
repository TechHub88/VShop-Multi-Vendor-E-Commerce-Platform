@extends('layouts.vertical', ['title' => 'Order Details'])

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Order Details (ID: {{ $order->id }})</h4>

    <div class="card mb-3">
        <div class="card-body">
            <h5>Customer Details</h5>
            <p><strong>Name:</strong> {{ $order->customer->name ?? '-' }}</p>
            <p><strong>Email:</strong> {{ $order->customer->email ?? '-' }}</p>
            <p><strong>Phone:</strong> {{ $order->customer->phone ?? '-' }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5>Order Summary</h5>
            <p><strong>Status:</strong> <span class="badge bg-success">{{ ucfirst($order->status) }}</span></p>
            <p><strong>Total Amount:</strong> ₹{{ number_format($order->total_amount, 2) }}</p>
            <p><strong>Created At:</strong> {{ $order->created_at->format('d-m-Y H:i') }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Order Items</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product->name ?? '-' }}</td>
                             <td>
    @if($item->product && $item->product->thumbnail)
        <img src="{{ asset($item->product->thumbnail) }}" width="60" height="60"
             style="object-fit:cover; border-radius:4px;" alt="{{ $item->product->name }}">
    @elseif($item->product && $item->product->images)
        @php
            $images = is_array($item->product->images) ? $item->product->images : json_decode($item->product->images, true);
            $firstImage = $images[0] ?? null;
        @endphp
        @if($firstImage)
            <img src="{{ asset($firstImage) }}" width="60" height="60"
                 style="object-fit:cover; border-radius:4px;" alt="{{ $item->product->name }}">
        @else
            <img src="https://via.placeholder.com/60x60?text=No+Image" width="60" height="60"
                 style="object-fit:cover; border-radius:4px;">
        @endif
    @else
        <img src="https://via.placeholder.com/60x60?text=No+Image" width="60" height="60"
             style="object-fit:cover; border-radius:4px;">
    @endif
</td>
                            <td>{{ $item->color->name ?? '-' }}</td>
                            <td>{{ $item->size->name ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ number_format($item->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
