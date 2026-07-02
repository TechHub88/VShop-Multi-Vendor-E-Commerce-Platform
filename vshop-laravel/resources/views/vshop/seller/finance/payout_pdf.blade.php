<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payout Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            background: #fff;
            color: #333;
        }

        .wrapper {
            padding: 25px;
        }

        /* HEADER */
        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 1px;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 11px;
            color: #777;
        }

        /* SUMMARY BOX */
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .summary div {
            width: 32%;
            text-align: center;
        }

        .summary strong {
            display: block;
            font-size: 14px;
            margin-top: 5px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .amount {
            font-weight: bold;
        }

        .success { color: green; font-weight: bold; }
        .failed { color: red; font-weight: bold; }
        .pending { color: orange; font-weight: bold; }

        /* FOOTER */
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>

<body>

<div class="wrapper">

    {{-- HEADER --}}
    <div class="header">
        <h1>PAYOUT REPORT</h1>
        <p>Generated on {{ date('d M Y H:i') }}</p>
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
        @forelse($data as $row)
            <tr>
                <td>{{ $row['date'] }}</td>

                <td class="amount">
                    ₹ {{ number_format($row['amount'], 2) }}
                </td>

                <td>{{ $row['type'] }}</td>

                <td>
                    @if($row['status'] == 'Success')
                        <span class="success">Success</span>
                    @elseif($row['status'] == 'Failed')
                        <span class="failed">Failed</span>
                    @else
                        <span class="pending">Pending</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center;">No data found</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="footer">
        This report is system generated.
    </div>

</div>

</body>
</html>