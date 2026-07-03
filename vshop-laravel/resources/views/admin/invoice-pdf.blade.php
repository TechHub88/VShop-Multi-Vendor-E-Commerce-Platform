<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 14px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2), .invoice-box table tr th:nth-child(2) { text-align: center; }
        .invoice-box table tr td:nth-child(3), .invoice-box table tr th:nth-child(3) { text-align: right; }
        .invoice-box table tr.top table td { padding-bottom: 20px; }
        .invoice-box table tr.top table td.title { font-size: 45px; line-height: 45px; color: #333; }
        .invoice-box table tr.information table td { padding-bottom: 40px; }
        .invoice-box table tr.heading th { background: #f8f9fa; border-bottom: 1px solid #ddd; font-weight: bold; padding: 10px; }
        .invoice-box table tr.item td { border-bottom: 1px solid #eee; padding: 10px; }
        .invoice-box table tr.item.last td { border-bottom: none; }
        .invoice-box table tr.total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; }
        .text-primary { color: #1a5276; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="3">
                    <table>
                        <tr>
                            <td class="title">
                                <h2 style="color: #2c3e50; margin:0;">INVOICE</h2>
                                <span style="font-size: 14px; color:#777;">#{{ $invoiceTemplate['invoicePrefix'] ?? 'INV' }}-{{ $seller->id }}-{{ date('Ymd') }}</span>
                            </td>
                            <td class="text-right">
                                <h3 class="text-primary" style="margin:0;">{{ $invoiceTemplate['companyName'] ?? 'Bridhi Invation' }}</h3>
                                <div style="font-size: 12px; color: #555;">
                                    {{ $invoiceTemplate['companyAddress'] ?? '123 Bridhi Tower, Tech Park' }}<br>
                                    @if(!empty($invoiceTemplate['companyPhone'])) Phone: {{ $invoiceTemplate['companyPhone'] }}<br> @endif
                                    @if(!empty($invoiceTemplate['companyEmail'])) Email: {{ $invoiceTemplate['companyEmail'] }}<br> @endif
                                    @if(!empty($invoiceTemplate['companyGst'])) GSTIN: {{ $invoiceTemplate['companyGst'] }} @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="3">
                    <table>
                        <tr>
                            <td>
                                <strong>Billed To:</strong><br>
                                {{ $seller->name }}<br>
                                Seller ID: S-{{ $seller->id }}<br>
                                {{ $seller->email }}<br>
                                @if($seller->phone) {{ $seller->phone }}<br> @endif
                                @if($seller->gst_number) GSTIN: {{ $seller->gst_number }} @endif
                            </td>
                            <td class="text-right">
                                <strong>Billing Period:</strong><br>
                                @if($startDate && $endDate)
                                    {{ date('d M Y', strtotime($startDate)) }} to {{ date('d M Y', strtotime($endDate)) }}
                                @else
                                    All Time up to {{ date('d M Y') }}
                                @endif
                                <br><br>
                                <strong>Invoice Date:</strong><br>
                                {{ date('d M Y') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <th>Description</th>
                <th>Quantity</th>
                <th>Amount</th>
            </tr>
            <tr class="item">
                <td>
                    <strong>Platform Commission</strong><br>
                    <span style="font-size: 12px; color: #777;">Commission on {{ $salesData->count() }} successful transactions.</span>
                </td>
                <td>{{ $salesData->count() }}</td>
                <td>₹{{ number_format($commission, 2) }}</td>
            </tr>
            <tr class="item last">
                <td>
                    <strong>GST ({{ $gstRate }}%)</strong><br>
                    <span style="font-size: 12px; color: #777;">Tax applied on platform commission.</span>
                </td>
                <td>-</td>
                <td>₹{{ number_format($gstAmount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right">₹{{ number_format($commission, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-right"><strong>Tax (GST {{ $gstRate }}%):</strong></td>
                <td class="text-right">₹{{ number_format($gstAmount, 2) }}</td>
            </tr>
            <tr class="total">
                <td></td>
                <td class="text-right" style="font-size: 18px; color:#1a5276;">Total Due:</td>
                <td class="text-right" style="font-size: 18px; color:#1a5276;">₹{{ number_format($commission + $gstAmount, 2) }}</td>
            </tr>
        </table>
        <div style="margin-top: 50px; text-align: center; color: #777; border-top: 1px solid #eee; padding-top: 10px; font-size: 12px;">
            This is a computer-generated invoice and does not require a physical signature.<br>
            {{ $invoiceTemplate['invoiceFooter'] ?? 'Thank you for your business!' }}
        </div>
    </div>
</body>
</html>
