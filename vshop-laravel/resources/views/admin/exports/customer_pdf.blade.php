<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            padding: 10px 0;
            margin-bottom: 15px;
            border-bottom: 2px solid #ddd;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th {
            background: #f5f5f5;
            padding: 8px;
            border: 1px solid #ddd;
            font-weight: bold;
            text-align: left;
        }

        table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
            color: #777;
        }

        .amount {
            font-weight: bold;
            color: #000;
        }
    </style>

</head>
<body>

    <div class="header">
        <h2>CUSTOMER ACTIVITY REPORT</h2>
        <small>Generated on: {{ now()->format('d M Y, h:i A') }}</small>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Total Orders</th>
                <th>Total Spend (₹)</th>
                <th>Last Purchase</th>
            </tr>
        </thead>

        <tbody>
            @foreach($customers as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->email }}</td>
                <td>{{ $row->total_orders }}</td>
                <td class="amount">₹{{ number_format($row->total_spend, 2) }}</td>
                <td>{{ $row->last_purchase ? date('d M Y', strtotime($row->last_purchase)) : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} Your Company. This is a system-generated report.
    </div>

</body>
</html>
