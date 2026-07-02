<!DOCTYPE html>
<html>
<head>
    <title>Monthly Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            color: #2c3e50;
        }

        .summary {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 10px;
        }

        table td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        .amount {
            color: #28a745;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #888;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Monthly Earnings Report</h2>
        <p>Seller Finance Summary</p>
    </div>

    <div class="summary">
        <p><strong>Generated Date:</strong> {{ now()->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Total Earnings</th>
            </tr>
        </thead>

        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ \Carbon\Carbon::create()->month($row->month)->format('F') }}</td>
                <td class="amount">₹ {{ number_format($row->total,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        This is a system-generated report. No signature required.
    </div>

</body>
</html>