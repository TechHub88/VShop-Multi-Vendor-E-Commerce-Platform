@extends('frontend.layout')

@section('content')
<main style="min-height: calc(100vh - 350px); display: block;">
   <section class="bedcum-sec" style="padding-top: 120px;">
        <div class="container">
            <div class="bedcum-inner">    
                <div class="bed-cum-list">
                    <ul>
                        <li>
                            <a href="#">Shop</a>
                        </li>
                        <li>
                            <i class="fa-regular fa-angle-right"></i>
                        </li>
                        <li>
                            <span>Order List</span>
                        </li>
                    </ul>
                </div>
                <div class="date-short-sec">
                  
                     <form method="GET" action="{{ route('frontend.orders') }}" class="date-sec d-flex align-items-center mb-0">
    <div class="date-input-box me-0">
        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
    </div>
    <div class="date-input-box me-0">
        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
    </div>
    <div class="me-0">
        <select name="status" class="form-select">
            <option value="">Sort by : Delivered Orders</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">
        <i class="fa-light fa-magnifying-glass"></i> Filter
    </button>
</form>

                 
                  
                </div>
            </div>
        </div>
    </section>

    <section class="order-list-sec">
        <div class="container">
            <div class="order-list-inner">
                <ul>
                 @if($orders->count() > 0)

  @foreach($orders as $order)
    <li>
        <div class="order-list-wrapper">
            <div class="order-list-box">
                <h3>
                    <a href="#">Order No : #{{ $order->id }}</a>
                </h3>
                <div class="total-order-sec">
                    <p>Total Items : {{ $order->items->count() }}</p>
                    <p>Total Quantity : {{ $order->items->sum('quantity') }}</p>
                </div>
            </div>

            <div class="order-list-box">
                <div class="total-time-sec">
                    <p>{{ $order->created_at->format('h:i A') }}</p>
                    <p>{{ $order->created_at->format('d-m-Y') }}</p>
                </div>
                <p class="order-status">
                    Order Status :
                    <span class="badge rounded-pill {{ $order->status == 'Delivered' ? 'grenColor' : 'yellowColor' }}">
                        {{ $order->status }}
                    </span>
                </p>
            </div>

            <div class="order-list-box text-end">
                <p class="grandtotal">
                    Grand Total :
                    <span class="blueColor">₹{{ number_format($order->total_amount, 2) }}</span>
                </p>

                {{-- Coupon Section --}}
                @if($order->appliedCoupon)
                <div class="coupon-details text-end mt-2">
                    <p>Coupon Code: <strong>{{ $order->appliedCoupon->coupon_code }}</strong></p>
                    <p>Discount Amount: <strong class="text-success">- ₹{{ number_format($order->appliedCoupon->discount_amount, 2) }}</strong></p>
                    <p>Total After Discount: <strong>₹{{ number_format($order->total_amount - $order->appliedCoupon->discount_amount, 2) }}</strong></p>
                    <p>You Save: <strong class="text-success">₹{{ number_format($order->appliedCoupon->discount_amount, 2) }}</strong></p>
                </div>
                @endif

                <div class="total-time-sec justify-content-end">
                    <div class="viewOrderBtnArea">
                        <a href="{{ route('frontend.order.details', $order->id) }}" 
                           class="btn btn-primary mt-2 order-dtl-btn">
                           View Order
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </li>
@endforeach


@else
    <div class="d-flex justify-content-center align-items-center" 
         style="min-height: 20vh; width: 100%; text-align: center;">

        <div>
            <h5 class="text-muted mb-3">You have no orders yet</h5>

            <a href="{{ route('frontend.product.details', ['slug' => '']) }}" 
               class="btn"
               style="background: #000; color: #fff; padding: 10px 22px; border-radius: 6px;">
                Start Shopping
            </a>
        </div>

    </div>
@endif


                </ul>
            </div>

            <div class="pagination-sec">
                <div class="pagination-inner">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
