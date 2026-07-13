<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #0d6efd;
        }
        .info-section {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-section td {
            vertical-align: top;
            width: 50%;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .text-end {
            text-align: right !important;
        }
        .totals-table {
            width: 50%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table th, .totals-table td {
            padding: 6px;
            border: 1px solid #ddd;
        }
        .totals-table th {
            background-color: #f8f9fa;
            text-align: right;
        }
        .totals-table td {
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>TAX INVOICE</h2>
        <p>Order #{{ $order->id }} | Date: {{ $order->created_at->format('d M Y') }}</p>
    </div>

    <table class="info-section">
        <tr>
            <td>
                <strong>Seller Details:</strong><br>
                {{ $seller->name ?? '-' }}<br>
                Email: {{ $seller->email ?? '-' }}<br>
                @if(!empty($seller->phone)) Phone: {{ $seller->phone }}<br> @endif
                @if(!empty($seller->gst_number)) GST No: {{ $seller->gst_number }}<br> @endif
            </td>
            <td>
                <strong>Customer Details:</strong><br>
                {{ $order->customer->name ?? '-' }}<br>
                Email: {{ $order->customer->email ?? '-' }}<br>
                Address: {{ $order->customer->firstAddress->address ?? '-' }}<br>
                @if(!empty($order->gst_no)) GST No: {{ $order->gst_no }}<br> @endif
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $itemTotal = 0; @endphp
            @foreach($order->items as $item)
            @php
                $total = $item->price * $item->quantity;
                $itemTotal += $total;
            @endphp
            <tr>
                <td>
                    {{ $item->product->name ?? '-' }}
                    @if(!empty($item->color)) <br><small>Color: {{ $item->color->name }}</small> @endif
                    @if(!empty($item->size)) <br><small>Size: {{ $item->size->name }}</small> @endif
                </td>
                <td>{{ $item->product->category->name ?? '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ number_format($item->price, 2) }}</td>
                <td>₹{{ number_format($total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <th>Sub Total:</th>
            <td>₹{{ number_format($order->sub_total ?? $itemTotal, 2) }}</td>
        </tr>
        @if($order->cgst > 0)
        <tr>
            <th>CGST:</th>
            <td>₹{{ number_format($order->cgst, 2) }}</td>
        </tr>
        @endif
        @if($order->sgst > 0)
        <tr>
            <th>SGST:</th>
            <td>₹{{ number_format($order->sgst, 2) }}</td>
        </tr>
        @endif
        @if($order->igst > 0)
        <tr>
            <th>IGST:</th>
            <td>₹{{ number_format($order->igst, 2) }}</td>
        </tr>
        @endif
        @if($order->gst_amount > 0 && empty($order->cgst) && empty($order->sgst) && empty($order->igst))
        <tr>
            <th>Total GST:</th>
            <td>₹{{ number_format($order->gst_amount, 2) }}</td>
        </tr>
        @endif
        @if($order->courier_charge > 0)
        <tr>
            <th>Shipping / Courier:</th>
            <td>₹{{ number_format($order->courier_charge, 2) }}</td>
        </tr>
        @endif
        <tr>
            <th>Grand Total:</th>
            <td><strong>₹{{ number_format($order->total_amount ?? $itemTotal, 2) }}</strong></td>
        </tr>
    </table>

</body>
</html>
