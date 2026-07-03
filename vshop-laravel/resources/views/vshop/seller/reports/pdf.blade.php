<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Seller Sales Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
            color: #777;
        }

        .summary {
            margin-bottom: 15px;
            padding: 10px;
            background: #f2f2f2;
            border-radius: 5px;
        }

        .summary strong {
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #1f4e79;
            color: white;
        }

        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            font-size: 12px;
        }

        td {
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Seller Sales Report</h2>
        <p>Generated on {{ now()->format('d M Y h:i A') }}</p>
    </div>

    <div class="summary">
        <strong>Total Orders:</strong> {{ $items->pluck('order_id')->unique()->count() }} |
        <strong>Total Sales:</strong> ₹ {{ number_format($items->sum('total'), 2) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Order ID</th>
                <th>Product</th>
                <th>Customer</th>
                <th>Quantity</th>
                <th class="text-right">Amount (₹)</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>#{{ $item->order_id }}</td>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->order->customer->name ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="text-right">
                        {{ number_format($item->total, 2) }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} Your Company Name | Seller Report
    </div>

</body>
</html>