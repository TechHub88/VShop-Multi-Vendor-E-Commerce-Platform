@extends('vshop.seller.main')

@section('content')

<h4 class="mb-4">Finance Dashboard</h4>

{{-- ================= SUMMARY ================= --}}
<div class="row mb-4">

<div class="col-md-4">
<div class="card p-3 shadow-sm">
<h6>Total Revenue</h6>
<h3 class="text-success">
₹ {{ number_format($totalRevenue,2) }}
</h3>
</div>
</div>

<div class="col-md-4">
<div class="card p-3 shadow-sm">
<h6>Paid Orders</h6>
<h4>{{ $stats->paid }}</h4>
</div>
</div>

<div class="col-md-4">
<div class="card p-3 shadow-sm">
<h6>Pending Orders</h6>
<h4>{{ $stats->pending }}</h4>
</div>
</div>

</div>







{{-- ================= FILTER ================= --}}
<form method="GET" class="row mb-4">

<div class="col-md-3">
<input type="date"
name="start_date"
value="{{ request('start_date') }}"
class="form-control">
</div>

<div class="col-md-3">
<input type="date"
name="end_date"
value="{{ request('end_date') }}"
class="form-control">
</div>

<div class="col-md-3">
<select name="payment_status" class="form-control">

<option value="">All Status</option>

<option value="paid"
{{ request('payment_status')=='paid'?'selected':'' }}>
Paid
</option>

<option value="pending"
{{ request('payment_status')=='pending'?'selected':'' }}>
Pending
</option>

<option value="failed"
{{ request('payment_status')=='failed'?'selected':'' }}>
Failed
</option>

</select>
</div>

<div class="col-md-3">
<button class="btn btn-primary w-100">
Filter Report
</button>
</div>

</form>



{{-- ================= TRANSACTION REPORT ================= --}}
<div class="card p-3 shadow-sm">

<h5 class="mb-3">Transaction Report</h5>

<table class="table table-bordered table-sm">

<thead class="table-light">
<tr>
<th>Order ID</th>
<th>Transaction</th>
<th>Payment</th>
<th>Status</th>
<th>Amount</th>
<th>Date</th>
</tr>
</thead>

<tbody>

@forelse($transactions as $t)

<tr>

<td>#{{ $t->order_id }}</td>

<td>{{ $t->transaction_id ?? '-' }}</td>

<td>{{ ucfirst($t->payment_method) }}</td>

<td>
<span class="badge
@if($t->payment_status=='paid') bg-success
@elseif($t->payment_status=='pending') bg-warning
@else bg-danger
@endif">
{{ ucfirst($t->payment_status) }}
</span>
</td>

<td class="fw-bold">
₹ {{ number_format($t->total_amount,2) }}
</td>

<td>
{{ date('d M Y',strtotime($t->order_date ?? $t->created_at)) }}
</td>

</tr>

@empty

<tr>
<td colspan="6" class="text-center">
No Transactions Found
</td>
</tr>

@endforelse

</tbody>

</table>

{{ $transactions->links() }}

</div>

@endsection



{{-- ================= CHART JS ================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

/* ================= PIE CHART ================= */

new Chart(document.getElementById('financePie'),{
type:'pie',
data:{
labels:['Paid','Pending','Failed'],
datasets:[{
data:[
{{ $stats->paid }},
{{ $stats->pending }},
{{ $stats->failed }}
],
backgroundColor:[
'#28a745',
'#ffc107',
'#dc3545'
]
}]
},
options:{
plugins:{legend:{position:'bottom'}},
maintainAspectRatio:false
}
});


/* ================= LINE CHART ================= */

new Chart(document.getElementById('financeLine'),{
type:'line',
data:{
labels:[
'Jan','Feb','Mar','Apr','May','Jun',
'Jul','Aug','Sep','Oct','Nov','Dec'
],
datasets:[{
label:'Revenue',
data:[
@for($i=1;$i<=12;$i++)
{{ $monthlyRevenue[$i] ?? 0 }},
@endfor
],
borderColor:'#0d6efd',
backgroundColor:'rgba(13,110,253,0.1)',
tension:0.4,
fill:true
}]
},
options:{
plugins:{legend:{display:false}},
scales:{
y:{beginAtZero:true}
}
}
});

</script>
