<table class="table table-hover align-middle">

<thead class="table-light">
<tr>
    <th>Order No</th>
    <th>Customer</th>
    <th>Product</th>
    <th>Payment</th>
    <th>Total</th>
    <th>Status Flow</th>
    <th>Date</th>
    <th width="180">Action</th>
</tr>
</thead>

<tbody>

@forelse($orders as $order)

<tr>

<td>
    <strong>#{{ $order->order_no ?? 'ORD'.str_pad($order->id,6,'0',STR_PAD_LEFT) }}</strong>
</td>

<td>{{ $order->customer->name ?? 'N/A' }}</td>

<td>{{ $order->product->name ?? 'Service' }}</td>

<td>
    <span class="badge bg-light text-dark">
        {{ ucfirst($order->payment_type ?? 'Online') }}
    </span>
</td>

<td>₹ {{ number_format($order->total_amount,2) }}</td>

{{-- STATUS FLOW --}}
<td>
    <div class="status-flow">

        @php $paymentStat = strtolower($order->payment_status ?? 'pending'); @endphp
        <span class="step {{ in_array($order->status, ['processing','completed']) || in_array($paymentStat, ['paid', 'success']) ? 'active' : '' }}">
            Payment
        </span>

        <span class="step {{ in_array($order->status, ['out for delivery','completed']) ? 'active' : '' }}">
            Dispatch
        </span>

        <span class="step {{ $order->status == 'out for delivery' ? 'active' : '' }}">
            Delivery
        </span>

        <span class="step {{ $order->status == 'completed' ? 'active' : '' }}">
            Closure
        </span>

    </div>
</td>

<td>{{ $order->created_at->format('d M Y') }}</td>

<td>

    <a href="{{ route('seller.orders.manage',$order->id) }}"
       class="btn btn-sm btn-primary">View</a>

    {{-- CANCEL --}}
    @if(!in_array($order->status, ['cancelled','completed']))
        <button class="btn btn-sm btn-danger">Cancel</button>
    @endif

    {{-- RETURN --}}
    @if($order->status == 'completed')
        <button class="btn btn-sm btn-warning">Return</button>
    @endif

</td>

</tr>

@empty

<tr>
<td colspan="8" class="text-center py-4">No orders found</td>
</tr>

@endforelse

</tbody>
</table>

