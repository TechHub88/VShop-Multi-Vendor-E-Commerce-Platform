@extends('vshop.seller.main')

<style>
    .invisible {
        display: none;
    }
    .transaction-btn-sec .btn {
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .transaction-btn-sec .btn i {
        font-size: 15px;
    }
    .transaction-btn-sec select {
        height: 29px;
    }
    .transaction-filter-btn {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .transaction-filter-btn .btn {
        height: 45px;
    }
    .remove-btn {
        background: #61b3e5 !important;
        color: #fff !important;
        font-size: 16px !important;
    }
    .table th {
    font-weight: 600;
    color: #fff !important;
    background-color: #004392 !important;
    border: 1px solid #fff;
}
.table tbody td {
    border: 1px solid #dee2e6;
}
.table-btn-sec a,
.table-btn-sec button {
    border: 1px solid #dee2e6 !important;
    font-size: 14px;
    padding: 7px 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
}
.table-btn-sec a i,
.table-btn-sec button i{
    font-size: 12px;
}
.table-btn-sec {
    display: flex;
    align-items: center;
}
.nav-btn {
    border: 1px solid #004392;
    color: #004392;
    background: transparent;
    border-radius: 5px;
    font-size: 4px;
    width: 35px;
    height: 38px;
}
.top-back-heading-sec {
    align-items: center;
    gap: 10px;
}
.table-box {
    overflow: auto;
}
.chart-box div {
    width: 100% !important;
    height: 100% !important;
}
.chart-box div canvas {
    width: 100% !important;
    height: 100% !important;
}
.top-back-heading-sec h5 {
    color: #0f172a !important;
}
.btn-primary {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
    font-size: 16px !important;
}
.btn-outline-primary {
    border-color: #004392 !important;
    color: #004392 !important;
}
.btn-danger {
    background-color: #233d78 !important;
    border: 0 !important;
}
.btn-success {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
    font-size: 16px !important;
}
.btn-warning {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
    color: #fff !important;
    font-size: 16px !important;
}
.btn-dark {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
    font-size: 16px !important;
}
#currencySwitcher {
    border: 1px solid #233d78;
    font-size: 14px !important;
    height: 32px;
}
</style>

@section('content')

<div class="container-fluid py-4">

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <div class="d-flex top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <h5 class="fw-bold mb-0">
            Transaction History
        </h5>
    </div>

    <div class="d-flex gap-2 align-items-end flex-wrap transaction-btn-sec">

        {{-- ✅ Currency Switcher --}}
        <div>
            <label class="small text-muted invisible">Currency</label>
            <select id="currencySwitcher" class="form-select form-select-sm">
                <option value="INR">₹ INR</option>
                <option value="USD">$ USD</option>
                <option value="EUR">€ EUR</option>
            </select>
        </div>

        {{-- ✅ Wallet --}}
        <div>
            <label class="small text-muted invisible">Wallet</label>
            <a href="{{ route('seller.finance.wallet') }}" class="btn btn-success btn-sm">
                <i class="fa fa-wallet"></i> Wallet
            </a>
        </div>

        {{-- ✅ Support --}}
        <div>
            <label class="small text-muted invisible">Support</label>
            <a href="{{ route('seller.grievance.list') }}" class="btn btn-warning btn-sm">
                <i class="fa fa-headset"></i> Support
            </a>
        </div>

        {{-- ✅ Export --}}
        <div>
            <label class="small text-muted invisible">Export</label>
            <div class="dropdown">
                <button class="btn btn-dark btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                    Export
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('seller.transactions.export', request()->all()) }}"
class="dropdown-item">CSV</a>
<a href="{{ route('seller.transactions.export', request()->all()) }}"
class="dropdown-item">Excel</a>
                    </li>
                   
                </ul>
            </div>
        </div>

    </div>
</div>

{{-- ================= FILTER BAR ================= --}}
<form method="GET" id="filterForm">

<div class="card p-3 mb-3 shadow-sm">

<div class="row g-2 align-items-end">

<div class="col-md-2">
<label>Date From</label>
<input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
</div>

<div class="col-md-2">
<label>Date To</label>
<input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
</div>

<div class="col-md-2">
<label>Type</label>
<select name="type" class="form-control form-control-sm">
<option value="">All</option>
<option value="earning" {{ request('type')=='earning'?'selected':'' }}>Orders</option>
<option value="refund" {{ request('type')=='refund'?'selected':'' }}>Refunds</option>
<option value="fee" {{ request('type')=='fee'?'selected':'' }}>Fees</option>
<option value="payout_success" {{ request('type')=='payout_success'?'selected':'' }}>Payouts</option>
</select>
</div>

<div class="col-md-2">
<label>Order ID</label>
<input type="text" name="order_id" value="{{ request('order_id') }}"
class="form-control form-control-sm">
</div>

<div class="col-md-2">
<label>Amount</label>
<input type="number" name="amount" value="{{ request('amount') }}"
class="form-control form-control-sm">
</div>

<div class="col-md-2">
<div class="transaction-filter-btn">
    <button class="btn btn-primary btn-sm w-100">Apply</button>
    <button class="btn remove-btn btn-sm w-100">Reset</button>
</div>
</div>

</div>

</div>

</form>

{{-- ================= MAIN ================= --}}
<div class="row">

{{-- ================= LEFT ================= --}}
<div class="col-md-8">

{{-- ================= LEDGER ================= --}}
<div class="card shadow-sm mb-3">

    <div class="card-header fw-bold">Transaction Ledger</div>
    
    <div class="table-box">
    <table class="table table-bordered mb-0">

        <thead>
            <tr>
                <th>Date</th>
                <th>ID</th>
                <th>Type</th>
                <th>Reference</th>
                <th>Product</th>
                <th>Lead Charge</th>
                <th>Amount</th>
                <th>Boost Campaign Charge</th>
                <th>Status</th>
                <th>Invoice</th>
                <th>View</th>
            </tr>
        </thead>

        <tbody>

            @forelse($transactions as $t)

                @php
                    $typeMap = [
                        'earning' => 'Order',
                        'refund' => 'Refund',
                        'fee' => 'Fee',
                        'payout_success' => 'Payout'
                    ];

                    $label = $typeMap[$t->transaction_type] ?? $t->transaction_type;
                @endphp

                <tr>

                    <td>{{ date('d/m/Y', strtotime($t->created_at)) }}</td>

                    <td>{{ $t->id }}</td>

                    <td>
                        <span class="badge bg-{{
                            $label == 'Order' ? 'success' :
                            ($label == 'Refund' ? 'warning' :
                            ($label == 'Fee' ? 'secondary' : 'info'))
                        }}">
                            {{ $label }}
                        </span>
                    </td>

                    <td>{{ $t->reference_no }}</td>

                    <td>
                        @if($t->order && $t->order->items->first() && $t->order->items->first()->product)
                            {{ \Illuminate\Support\Str::limit($t->order->items->first()->product->name, 30) }}
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        <span class="money text-muted" data-amount="0"></span>
                    </td>

                    <td>
                        <span class="money" data-amount="{{ $t->credit ?: $t->debit }}"></span>
                    </td>

                    <td>
                        <span class="money text-muted" data-amount="0"></span>
                    </td>

                    <td>
                        <span class="badge bg-{{ $t->status == 'success' ? 'success' : 'warning' }}">
                            {{ ucfirst($t->status) }}
                        </span>
                    </td>

                    <td>
                        <a href="{{ route('seller.invoice.download', ['id' => $t->id, 'currency' => 'INR']) }}"
                           class="btn btn-sm btn-outline-primary invoice-link"
                           target="_blank">
                            <i class="fa fa-download"></i>
                        </a>
                    </td>

                    <td>
                        <a href="{{ route('seller.transactions.dispute.view', $t->id) }}"
                           class="btn btn-sm btn-primary">
                            View
                        </a>
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="10" class="text-center">No data</td>
                </tr>

            @endforelse

        </tbody>

    </table>
    </div>

    {{-- ================= PAGINATION ================= --}}
    <div class="card-footer d-flex justify-content-between align-items-center">
        <div class="text-muted small">
            Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} results
        </div>
        <div>
            {{ $transactions->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>


{{-- ================= DISPUTE ================= --}}
<div class="card shadow-sm">
<div class="card-header fw-bold">Dispute Management</div>

<table class="table table-bordered mb-0">

<tr>
<th>ID</th>
<th>Transaction</th>
<th>Status</th>
<th>Action</th>
</tr>

@foreach($transactions as $t)
<tr>
<td>D{{ $t->id }}</td>
<td>{{ $t->id }}</td>

<td>
<span class="badge bg-warning">Pending</span>
</td>

<td>

<div class="dropdown">
<button class="btn btn-danger btn-sm dropdown-toggle" data-bs-toggle="dropdown">
Action
</button>

<ul class="dropdown-menu">

<li>
<button class="dropdown-item openDisputeModal" data-id="{{ $t->id }}">
Raise Dispute
</button>
</li>

<li>
<a class="dropdown-item"
href="{{ route('seller.transactions.dispute.view',$t->id) }}">
Track
</a>
</li>

<li>
<form method="POST" action="{{ route('seller.transactions.undispute') }}">
@csrf
<input type="hidden" name="id" value="{{ $t->id }}">
<button class="dropdown-item text-success">Undispute</button>
</form>
</li>

</ul>

</div>

</td>

</tr>
@endforeach

</table>

    {{-- ================= PAGINATION ================= --}}
    <div class="card-footer d-flex justify-content-between align-items-center">
        <div class="text-muted small">
            Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} results
        </div>
        <div>
            {{ $transactions->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>

</div>

{{-- ================= RIGHT ================= --}}
<div class="col-md-4">

<div class="card p-3 shadow-sm">

<h6>Monthly Summary</h6>

<canvas id="chart"></canvas>

<hr>

<p>
Total: 
<span class="money" data-amount="{{ $transactions->sum('credit') }}"></span>
</p>

<p>
Payout: 
<span class="money" data-amount="{{ $transactions->sum('debit') }}"></span>
</p>

</div>

</div>

</div>

</div>

{{-- ================= MODAL ================= --}}
<div class="modal fade" id="disputeModal">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST" action="{{ route('seller.transactions.dispute') }}">
@csrf

<div class="modal-header">
<h5>Raise Dispute</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="hidden" name="transaction_id" id="disputeId">
<textarea name="message" class="form-control" required></textarea>
</div>

<div class="modal-footer">
<button class="btn btn-danger">Submit</button>
</div>

</form>

</div>
</div>
</div>

{{-- ================= JS ================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>



// DISPUTE MODAL
document.querySelectorAll('.openDisputeModal').forEach(btn=>{
btn.addEventListener('click',function(){
document.getElementById('disputeId').value=this.dataset.id;
new bootstrap.Modal(document.getElementById('disputeModal')).show();
});
});


/* ================= MONTHLY SUMMARY CHART ================= */

new Chart(document.getElementById('chart'),{

    type:'doughnut',

    data:{

        labels:[
            'Order',
            'Refund',
            'Fee',
            'Payout',
            'Dispute'
        ],

        datasets:[{

            data:[

                {{ $transactions->where('transaction_type','earning')->count() }},

                {{ $transactions->where('transaction_type','refund')->count() }},

                {{ $transactions->where('transaction_type','fee')->count() }},

                {{ $transactions->where('transaction_type','payout_success')->count() }},

                {{-- 🔥 DISPUTE COUNT --}}
                {{ $transactions->where('status','disputed')->count() }}

            ],

            backgroundColor:[

                '#2196f3', // Order
                '#4caf50', // Refund
                '#9c27b0', // Fee
                '#ff9800', // Payout
                '#f44336'  // Dispute

            ],

            borderWidth:0

        }]
    },

    options:{

        cutout:'58%',

        plugins:{

            legend:{
                display:false
            },

            tooltip:{

                callbacks:{

                    label:function(context){

                        return context.label + ' : ' + context.raw;

                    }

                }

            }

        }

    }

});

</script>

<script>
document.addEventListener("DOMContentLoaded", function(){

    let currency = localStorage.getItem('currency') || 'INR';

    const rates = { INR:1, USD:0.012, EUR:0.011 };
    const symbols = { INR:'₹', USD:'$', EUR:'€' };

    const switcher = document.getElementById('currencySwitcher');
    if(switcher) switcher.value = currency;

    function formatMoney(amount){
        return amount.toLocaleString(undefined,{
            minimumFractionDigits:2,
            maximumFractionDigits:2
        });
    }

    /* ================= UPDATE AMOUNTS ================= */
    function updateCurrency(){
        document.querySelectorAll('.money').forEach(el=>{
            let amount = parseFloat(el.dataset.amount || 0);
            let converted = amount * rates[currency];
            el.innerHTML = symbols[currency]+' '+formatMoney(converted);
        });
    }

    /* ================= UPDATE INVOICE LINKS ================= */
   function updateInvoiceLinks(){
    document.querySelectorAll('.invoice-link').forEach(el=>{
        let url = new URL(el.href);
        url.searchParams.set('currency', currency);
        el.href = url.toString();
    });
}

    /* INITIAL LOAD */
    updateCurrency();
    updateInvoiceLinks(); // 🔥 IMPORTANT FIX

    /* ON CHANGE */
    if(switcher){
        switcher.addEventListener('change',function(){
            currency = this.value;
            localStorage.setItem('currency', currency);

            updateCurrency();
            updateInvoiceLinks(); // 🔥 FIXED HERE TOO
        });
    }

});
</script>

@endsection
