@extends('vshop.main')

@section('title', 'Order Confirmation')

@section('content')
<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
    <div class="dashboardPosPagesLeftContentonner">
        <div class="dashboardDtlsArea">
            <div class="dashboardDtlsAreaBody">

                {{-- ===== Breadcrumb ===== --}}
                <div class="bed-cum-sec justify-content-between">
                    <div class="bedcum-left">
                        <ul>
                            <li><a href="{{ route('vtravelhome') }}">Home</a> /</li>
                            <li>Order Success</li>
                        </ul>
                    </div>
                </div>

                {{-- ===== SUCCESS CARD ===== --}}
                <div class="order-success-wrapper">

                    <div class="order-success-card">

                        <div class="success-icon">
                            ✔
                        </div>

                        <h2>Order Placed Successfully!</h2>

                        <p class="order-id">
                            Order ID: <strong>#{{ $order->id }}</strong>
                        </p>

                        <p class="thank-text">
                            Thank you for your purchase.  
                            Your order has been received and is being processed.
                        </p>

                        {{-- ===== ORDER SUMMARY ===== --}}
                        <div class="success-summary">

                            @foreach($order->items as $item)
                                <div class="summary-row">
                                    <span>{{ $item->product->name ?? 'Product' }} × {{ $item->quantity }}</span>
                                    <span>₹ {{ number_format($item->total, 2) }}</span>
                                </div>
                            @endforeach

                            <div class="summary-row border-top">
                                <span>Subtotal</span>
                                <span>₹ {{ number_format($order->sub_total, 2) }}</span>
                            </div>

                            <div class="summary-row">
                                <span>GST</span>
                                <span>₹ {{ number_format($order->gst_amount, 2) }}</span>
                            </div>

                            <div class="summary-row total">
                                <span>Total Paid</span>
                                <span>₹ {{ number_format($order->total_amount, 2) }}</span>
                            </div>

                        </div>

                        {{-- ===== ACTION BUTTONS ===== --}}
                        <div class="success-actions">

                            <a href="{{route('vshop.order.list')}}" class="btn-primary">
                                View My Orders
                            </a>

                            <a href="{{ route('productmanage.index') }}" class="btn-outline">
                                Continue Shopping
                            </a>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection


{{-- ===== CSS ===== --}}
@push('styles')
<style>
.order-success-wrapper{
    display:flex;
    justify-content:center;
    padding:40px 0;
}

.order-success-card{
    background:#fff;
    padding:40px;
    border-radius:14px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    max-width:520px;
    width:100%;
    text-align:center;
}

.success-icon{
    width:70px;
    height:70px;
    border-radius:50%;
    background:#16a34a;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:32px;
    margin:0 auto 20px;
}

.order-success-card h2{
    font-size:26px;
    margin-bottom:10px;
    font-weight:600;
}

.order-id{
    color:#1d4ed8;
    margin-bottom:10px;
}

.thank-text{
    color:#666;
    font-size:14px;
    margin-bottom:25px;
}

.success-summary{
    text-align:left;
    margin-top:20px;
}

.summary-row{
    display:flex;
    justify-content:space-between;
    padding:8px 0;
    font-size:14px;
}

.summary-row.border-top{
    border-top:1px solid #eee;
    margin-top:10px;
    padding-top:12px;
}

.summary-row.total{
    font-weight:600;
    font-size:16px;
    color:#1d4ed8;
}

.success-actions{
    margin-top:30px;
    display:flex;
    gap:12px;
    justify-content:center;
}

.btn-primary{
    background:#1d4ed8;
    color:#fff;
    padding:10px 20px;
    border-radius:30px;
    text-decoration:none;
}

.btn-outline{
    border:1px solid #1d4ed8;
    color:#1d4ed8;
    padding:10px 20px;
    border-radius:30px;
    text-decoration:none;
}
</style>
@endpush
