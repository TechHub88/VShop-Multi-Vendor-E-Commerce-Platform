@extends('admin.main')

@section('content')

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="page-title m-0">Global Referral History</h2>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-bottom p-3">
        <h5 class="m-0 fw-bold">All Referral Transactions</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Seller</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Referred Buyer</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Referrer (Code)</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Products Purchased</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Commission</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $ref)
                    <tr>
                        <td class="ps-3">{{ $ref->created_at->format('d M, Y H:i') }}</td>
                        <td>
                            <div class="fw-bold">{{ $ref->seller->business_name ?? $ref->seller->name ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $ref->buyer->name ?? 'N/A' }}</div>
                            <div class="small text-muted">{{ $ref->buyer->email ?? '' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $ref->referrer->name ?? 'N/A' }}</div>
                            <div class="small text-muted mb-1">{{ $ref->referrer->email ?? '' }}</div>
                            <span class="badge bg-secondary" style="font-size: 11px;">Code: {{ $ref->referrer->referral_code ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @if($ref->order && $ref->order->items)
                                <ul class="list-unstyled mb-0" style="font-size: 13px;">
                                    @foreach($ref->order->items as $item)
                                        @if($item->seller_id == $ref->seller_id)
                                            <li>
                                                - {{ $item->product_name ?? ($item->product ? $item->product->name : 'Product') }} 
                                                <span class="text-muted">(Qty: {{ $item->quantity }})</span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted small">No details</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-success">₹{{ number_format($ref->amount, 2) }}</div>
                            <div class="small text-muted">({{ $ref->commission_percent }}%)</div>
                        </td>
                        <td>
                            @if($ref->status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ ucfirst($ref->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No referral commissions paid yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($referrals->hasPages())
    <div class="card-footer bg-white border-top border-light">
        {{ $referrals->links() }}
    </div>
    @endif
</div>

@endsection
