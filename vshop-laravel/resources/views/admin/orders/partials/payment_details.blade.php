@php
    $order = $order ?? null;
@endphp

@if(!$order)
    <div class="p-4 text-center text-muted">
        <i class="bx bx-error-circle fs-1 text-warning"></i>
        <p class="mt-2 mb-0">Order details not available or order not found.</p>
    </div>
@else
<div class="p-2">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h6 class="mb-0 fw-bold text-primary">Transaction ID: {{ $order?->transaction_id ?? 'N/A' }}</h6>
            <small class="text-muted">Order ID: #{{ $order?->id }} | Invoice: {{ $order?->invoice_no ?? ('INV-' . str_pad($order?->id ?? 0, 8, '0', STR_PAD_LEFT)) }}</small>
        </div>
        <div>
            @if(strtolower($order?->payment_status ?? '') == 'success' || strtolower($order?->payment_status ?? '') == 'paid')
                <span class="badge bg-success fs-12 px-3 py-2">Success</span>
            @elseif(strtolower($order?->payment_status ?? '') == 'failed')
                <span class="badge bg-danger fs-12 px-3 py-2">Failed</span>
            @else
                <span class="badge bg-warning text-dark fs-12 px-3 py-2">{{ ucfirst($order?->payment_status ?? 'Pending') }}</span>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="p-3 bg-light rounded border">
                <h6 class="fw-bold text-dark mb-2"><i class="bx bx-user me-1"></i> Customer Information</h6>
                <p class="mb-1"><strong>Name:</strong> {{ $order?->customer?->name ?? ($order?->recipient_name ?? 'N/A') }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $order?->customer?->email ?? 'N/A' }}</p>
                <p class="mb-0"><strong>Phone:</strong> {{ $order?->customer?->mobile ?? ($order?->customer?->phone ?? ($order?->phone ?? 'N/A')) }}</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 bg-light rounded border">
                <h6 class="fw-bold text-dark mb-2"><i class="bx bx-credit-card me-1"></i> Payment Summary</h6>
                <p class="mb-1"><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $order?->payment_type ?? 'COD')) }}</p>
                <p class="mb-1"><strong>Amount Paid:</strong> <span class="fw-bold text-success">₹{{ number_format($order?->total_amount ?? 0, 2) }}</span></p>
                <p class="mb-0"><strong>Date:</strong> {{ $order?->created_at ? $order->created_at->format('d M Y, h:i A') : 'N/A' }}</p>
            </div>
        </div>
    </div>

    <div class="p-3 bg-light rounded border mb-3">
        <h6 class="fw-bold text-dark mb-2"><i class="bx bx-shield-quarter me-1"></i> Security & Risk Assessment</h6>
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-1"><strong>Fraud Score:</strong> {{ $order?->fraud_score ?? 0 }}%</p>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-{{ ($order?->fraud_score ?? 0) < 30 ? 'success' : (($order?->fraud_score ?? 0) < 70 ? 'warning' : 'danger') }}" style="width: {{ $order?->fraud_score ?? 0 }}%"></div>
                </div>
            </div>
            <div class="col-md-6">
                <p class="mb-0"><strong>Fraud Status:</strong> 
                    <span class="badge bg-{{ ($order?->fraud_status ?? 'clean') == 'clean' ? 'success' : (($order?->fraud_status ?? '') == 'suspicious' ? 'warning' : 'danger') }}">
                        {{ ucfirst($order?->fraud_status ?? 'Clean') }}
                    </span>
                </p>
                @if(!empty($order?->fraud_reason))
                    <small class="text-danger d-block mt-1">Reason: {{ $order->fraud_reason }}</small>
                @endif
            </div>
        </div>
    </div>

    <h6 class="fw-bold text-dark mb-2"><i class="bx bx-shopping-bag me-1"></i> Ordered Items</h6>
    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order?->items ?? [] as $item)
                <tr>
                    <td>{{ $item->product?->name ?? 'Product Item' }}</td>
                    <td class="text-center">{{ $item->quantity ?? 1 }}</td>
                    <td class="text-end">₹{{ number_format($item->price ?? 0, 2) }}</td>
                    <td class="text-end fw-bold">₹{{ number_format(($item->price ?? 0) * ($item->quantity ?? 1), 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No item details available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
