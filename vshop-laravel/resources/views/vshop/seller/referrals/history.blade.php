@extends('vshop.seller.main', ['title' => 'Referral History'])

@section('content')

<style>
.page-title { font-weight:600; font-size:18px; }
.card { border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.05); border:none; }
.card-header { background:transparent; border-bottom:1px solid #f1f5f9; padding:15px 20px; }
.card-body { padding:20px; }
.table th { background:#f8fafc; font-weight:600; color:#475569; font-size:13px; text-transform:uppercase; border-bottom:2px solid #e2e8f0; }
.table td { vertical-align:middle; font-size:14px; color:#334155; border-bottom:1px solid #f1f5f9; }
.badge-success { background-color: #dcfce7; color: #166534; padding: 5px 10px; border-radius: 20px; font-weight: 600; font-size: 11px; }
</style>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="page-title m-0">Product Referral History</h2>
        <div class="stat-card" style="background:#fff; padding:10px 20px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.05);">
            <span style="font-size:12px; color:#64748b; font-weight:600; text-transform:uppercase;">Total Rewards Distributed</span>
            <div style="font-size:20px; font-weight:700; color:#16a34a;">₹{{ number_format($totalCommission, 2) }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="m-0" style="font-size: 15px; font-weight: 600;">Referral Transactions</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Referred Buyer</th>
                        <th>Referrer (Code)</th>
                      
                        <th>Commission %</th>
                        <th>Amount Earned</th>
                        <th>Reward Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $ref)
                    @php
                        $referrerSeller = null;
                        if ($ref->referrer) {
                            $referrerSeller = \App\Models\Seller::where('email', $ref->referrer->email)
                                ->orWhere('mobile', $ref->referrer->phone)
                                ->first();
                        }
                        $isPoints = !$referrerSeller;
                    @endphp
                    <tr>
                        <td>{{ $ref->created_at->format('d M, Y H:i') }}</td>
                        <td>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#orderModal{{ $ref->order_id }}" class="text-primary fw-bold">#{{ $ref->order_id }}</a>
                        </td>
                        <td>
                            @if($ref->order && $ref->order->items->first() && $ref->order->items->first()->product)
                                {{ \Illuminate\Support\Str::limit($ref->order->items->first()->product->name, 25) }}
                            @else
                                -
                            @endif
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
                       
                        <td>{{ $ref->commission_percent }}%</td>
                        <td class="{{ $isPoints ? 'text-warning' : 'text-success' }} fw-bold">
                            {{ $isPoints ? '+' . intval($ref->amount) . ' Points' : '+₹' . number_format($ref->amount, 2) }}
                        </td>
                        <td>
                            @if($isPoints)
                                <span class="badge bg-warning" style="font-size: 11px;">Reward Points</span>
                            @else
                                <span class="badge bg-success" style="font-size: 11px;">Real Money</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-success">{{ ucfirst($ref->status) }}</span>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No referral earnings yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($referrals->hasPages())
    <div class="card-footer bg-white">
        {{ $referrals->links() }}
    </div>
    @endif
</div>

<!-- Order Details Modals (Outside the table to prevent HTML breaking) -->
@foreach($referrals as $ref)
    @if($ref->order)
    <div class="modal fade" id="orderModal{{ $ref->order_id }}" tabindex="-1" aria-labelledby="orderModalLabel{{ $ref->order_id }}" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-light border-0">
            <h5 class="modal-title fw-bold text-dark" id="orderModalLabel{{ $ref->order_id }}">Order Details #{{ $ref->order_id }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">Customer Info</h6>
                    <p class="mb-1 fw-bold">{{ optional($ref->order->customer)->name ?? ($ref->buyer->name ?? 'N/A') }}</p>
                    <p class="mb-1 text-muted small"><i class="fas fa-envelope me-2"></i>{{ optional($ref->order->customer)->email ?? ($ref->buyer->email ?? 'N/A') }}</p>
                    <p class="mb-0 text-muted small"><i class="fas fa-phone me-2"></i>{{ optional($ref->order->customer)->phone ?? ($ref->buyer->phone ?? 'N/A') }}</p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">Order Info</h6>
                    <p class="mb-1 text-muted small">Date: <span class="fw-bold text-dark">{{ $ref->order->created_at->format('d M, Y H:i A') }}</span></p>
                    <p class="mb-1 text-muted small">Status: <span class="badge bg-{{ $ref->order->status == 'pending' ? 'warning' : ($ref->order->status == 'delivered' ? 'success' : 'info') }}">{{ ucfirst($ref->order->status) }}</span></p>
                    <p class="mb-0 text-muted small">Payment: <span class="fw-bold text-dark">{{ strtoupper(str_replace('_', ' ', $ref->order->payment_method ?? 'N/A')) }}</span></p>
                </div>
            </div>
            
            <h6 class="text-muted text-uppercase small fw-bold mb-3">Products</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ref->order->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->product && $item->product->thumbnail)
                                            <img src="{{ asset($item->product->thumbnail) }}" alt="" style="width:40px; height:40px; object-fit:cover; border-radius:4px; margin-right:10px;">
                                        @endif
                                        <div>
                                            <div class="fw-bold" style="font-size:13px;">{{ $item->product_name ?? ($item->product ? $item->product->name : 'N/A') }}</div>
                                            @if(($item->product->created_by_id ?? null) != Auth::guard('seller')->id())
                                                <span class="badge bg-secondary" style="font-size:10px;">Other Seller</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">₹{{ number_format($item->price, 2) }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end fw-bold">₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end text-muted small">Subtotal:</td>
                            <td class="text-end fw-bold">₹{{ number_format($ref->order->sub_total, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end text-muted small">Order Total (incl. Taxes & Shipping):</td>
                            <td class="text-end fw-bold text-primary">₹{{ number_format($ref->order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
          </div>
          <div class="modal-footer border-0 bg-light">
            <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    @endif
@endforeach

@endsection
