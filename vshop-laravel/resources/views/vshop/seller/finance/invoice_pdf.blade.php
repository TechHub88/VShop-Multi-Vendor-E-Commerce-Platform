<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        body { font-family: DejaVu Sans; }
        .box { border:1px solid #ddd; padding:15px; }
    </style>
</head>
<body>

<h2>Invoice</h2>

<div class="box">

<p><strong>Reference:</strong> {{ $txn->reference_no }}</p>
<p><strong>Date:</strong> {{ date('d M Y',strtotime($txn->created_at)) }}</p>
<p><strong>Type:</strong> {{ ucfirst($txn->transaction_type) }}</p>

<hr>

<p>
<strong>Credit:</strong>
{{ $symbol }} {{ number_format($txn->credit * $rate, 2) }}
</p>

<p>
<strong>Debit:</strong>
{{ $symbol }} {{ number_format($txn->debit * $rate, 2) }}
</p>

</div>

</body>
</html>