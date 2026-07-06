@extends('vshop.seller.main')
<style>
    .top-back-heading-sec h5 {
        color: #0f172a !important;
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
</style>

@section('content')

@php
$baseAmount = $txn->debit > 0 ? $txn->debit : $txn->credit;
@endphp

<div class="container-fluid py-4">

{{-- ================= HEADER ================= --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    
    <div class="d-flex top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <h5 class="fw-bold mb-0">
            Dispute Details
        </h5>
    </div>

    {{-- Currency Switch --}}
    <div class="d-flex justify-content-between align-items-center gap-2">
        <label class="small text-muted">Currency</label>
        <select id="currencySwitcher" class="form-select form-select-sm">
            <option value="INR">₹ INR</option>
            <option value="USD">$ USD</option>
            <option value="EUR">€ EUR</option>
        </select>
    </div>

</div>

{{-- ================= CARD ================= --}}
<div class="card shadow-lg border-0">

<div class="card-body">

<div class="row g-4">

{{-- LEFT --}}
<div class="col-md-6">

<div class="p-3 border rounded bg-light h-100">

<h6 class="fw-bold mb-3 text-primary">Transaction Info</h6>

<p><strong>ID:</strong> #{{ $txn->id }}</p>

<p>
<strong>Status:</strong> 
<span class="badge bg-{{ $txn->status=='disputed'?'danger':'success' }}">
{{ ucfirst($txn->status) }}
</span>
</p>

<p>
<strong>Dispute Amount:</strong> 
<span class="money fw-bold text-success" data-amount="{{ $baseAmount }}"></span>
</p>

<p><strong>Date:</strong> {{ date('d M Y',strtotime($txn->created_at)) }}</p>

</div>

</div>

{{-- RIGHT --}}
<div class="col-md-6">

<div class="p-3 border rounded bg-light h-100">

<h6 class="fw-bold mb-3 text-primary">User Message</h6>

<div class="p-3 bg-white rounded border">
{{ $txn->note ?? 'No note provided' }}
</div>

@if(!empty($txn->admin_reply))
<hr>
<h6 class="fw-bold text-success">Admin Reply</h6>

<div class="p-3 bg-white rounded border">
{{ $txn->admin_reply }}
</div>
@endif

</div>

</div>

</div>

</div>
</div>

</div>

{{-- ================= JS (SAME AS LEDGER) ================= --}}
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

    function updateCurrency(){
        document.querySelectorAll('.money').forEach(el=>{
            let amount = parseFloat(el.dataset.amount || 0);
            let converted = amount * rates[currency];
            el.innerHTML = symbols[currency] + ' ' + formatMoney(converted);
        });
    }

    updateCurrency();

    if(switcher){
        switcher.addEventListener('change',function(){
            currency = this.value;
            localStorage.setItem('currency', currency);
            updateCurrency();
        });
    }

});
</script>

@endsection
