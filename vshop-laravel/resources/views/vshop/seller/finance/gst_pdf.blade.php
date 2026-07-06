<!DOCTYPE html>
<html>
<head>
    <title>GST Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
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

        .box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }

        .row {
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
        }

        .amount {
            font-size: 20px;
            color: #28a745;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }

    </style>
</head>

<body>

    <div class="header">
        <h2>GST Report</h2>
        <p>Seller Finance Summary</p>
    </div>

    <div class="box">

        <div class="row">
            <span class="label">Report Date:</span>
            {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
        </div>

        <div class="row">
            <span class="label">Generated At:</span>
            {{ now()->format('d M Y H:i') }}
        </div>

        <hr>

        <div class="row">
            <span class="label">Total GST Amount:</span>
        </div>

        <div class="row amount">
            ₹ {{ number_format($gst_total,2) }}
        </div>

    </div>

    <div class="footer">
        This is a system generated GST report.
    </div>

</body>
</html>
