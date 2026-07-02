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

                        {{-- ===== WHATSAPP SHARE INVOICE ===== --}}
                        @php $waLink = session('order_whatsapp_link'); @endphp
                        @if($waLink)
                        <div class="whatsapp-share-sec" style="margin-top: 20px;">
                            <a href="{{ $waLink }}" target="_blank" class="btn-whatsapp" id="waShareBtn">
                                <i class="fa-brands fa-whatsapp me-2"></i> Share Invoice on WhatsApp
                            </a>
                            <p style="font-size: 12px; color: #888; margin-top: 8px;">
                                Click above to send the invoice link to your WhatsApp number.
                            </p>
                        </div>
                        @endif

                        {{-- ===== DOWNLOAD INVOICE ===== --}}
                        <div style="margin-top: 15px;">
                            <a href="{{ route('vshop.order.invoice', $order->id) }}" class="btn-invoice">
                                <i class="fa-solid fa-file-invoice me-2"></i> Download Invoice PDF
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

.btn-whatsapp{
    display:inline-flex;
    align-items:center;
    background:#25D366;
    color:#fff;
    padding:12px 24px;
    border-radius:30px;
    text-decoration:none;
    font-weight:600;
    font-size:15px;
    transition: all 0.3s;
}
.btn-whatsapp:hover{
    background:#1ebe5d;
    color:#fff;
    transform:scale(1.03);
}

.btn-invoice{
    display:inline-flex;
    align-items:center;
    background:#f1f5f9;
    color:#334155;
    padding:10px 20px;
    border-radius:30px;
    text-decoration:none;
    font-weight:500;
    font-size:14px;
    border:1px solid #e2e8f0;
    transition: all 0.3s;
}
.btn-invoice:hover{
    background:#e2e8f0;
    color:#1e293b;
}
</style>
@endpush

@push('scripts')
<script>
    // Auto-open WhatsApp share link after 2 seconds
    @if(session('order_whatsapp_link'))
    setTimeout(function() {
        window.open("{{ session('order_whatsapp_link') }}", '_blank');
    }, 2000);
    @endif
</script>
@endpush
