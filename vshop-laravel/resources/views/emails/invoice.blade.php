<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 14px; background-color: #f6f6f6; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; background: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, .15);">
        <table cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse; line-height: 1.5; text-align: left;">
            <!-- Header Row -->
            <tr>
                <td colspan="2" style="padding-bottom: 20px; border-bottom: 2px solid #eee;">
                    <table cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 50%; vertical-align: top;">
                                <h2 style="color: #2c3e50; margin: 0 0 5px 0; font-size: 28px; font-weight: bold;">INVOICE</h2>
                                <span style="font-size: 13px; color: #777; display: block; margin-bottom: 3px;">#{{ $invoiceTemplate['invoicePrefix'] ?? 'INV' }}-{{ $seller->id }}-{{ date('Ymd') }}</span>
                                <span style="font-size: 13px; color: #777; display: block;">Date: {{ date('d M Y') }}</span>
                            </td>
                            <td style="width: 50%; text-align: right; vertical-align: top; font-size: 12px; color: #555;">
                                <h3 style="color: #1a5276; margin: 0 0 5px 0; font-size: 18px; font-weight: bold;">{{ $invoiceTemplate['companyName'] ?? 'Bridhi Invation' }}</h3>
                                <span style="display: block; margin-bottom: 2px;">{{ $invoiceTemplate['companyAddress'] ?? '123 Bridhi Tower, Tech Park' }}</span>
                                @if(!empty($invoiceTemplate['companyPhone'])) <span style="display: block; margin-bottom: 2px;">Phone: {{ $invoiceTemplate['companyPhone'] }}</span> @endif
                                @if(!empty($invoiceTemplate['companyEmail'])) <span style="display: block; margin-bottom: 2px;">Email: {{ $invoiceTemplate['companyEmail'] }}</span> @endif
                                @if(!empty($invoiceTemplate['companyGst'])) <span style="display: block;">GSTIN: {{ $invoiceTemplate['companyGst'] }}</span> @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <!-- Bill To & Billing Period -->
            <tr>
                <td colspan="2" style="padding: 20px 0; border-bottom: 1px solid #eee;">
                    <table cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 55%; vertical-align: top; font-size: 13px;">
                                <strong style="text-transform: uppercase; color: #777; font-size: 11px; letter-spacing: 1px; display: block; margin-bottom: 5px;">BILLED TO:</strong>
                                <span style="font-size: 15px; font-weight: bold; color: #333; display: block; margin-bottom: 3px;">{{ $seller->name }}</span>
                                <span style="color: #666; display: block; margin-bottom: 2px;">Seller ID: S-{{ $seller->id }}</span>
                                <span style="color: #666; display: block; margin-bottom: 2px;">Email: {{ $seller->email }}</span>
                                @if($seller->phone) <span style="color: #666; display: block; margin-bottom: 2px;">Phone: {{ $seller->phone }}</span> @endif
                                @if($seller->gst_number) <span style="color: #666; display: block;">GSTIN: {{ $seller->gst_number }}</span> @endif
                            </td>
                            <td style="width: 45%; text-align: right; vertical-align: top; font-size: 13px;">
                                <strong style="text-transform: uppercase; color: #777; font-size: 11px; letter-spacing: 1px; display: block; margin-bottom: 5px;">BILLING PERIOD:</strong>
                                <span style="color: #333; display: block; margin-bottom: 10px;">
                                    @if($startDate && $endDate)
                                        {{ date('d M Y', strtotime($startDate)) }} to {{ date('d M Y', strtotime($endDate)) }}
                                    @else
                                        All Time up to {{ date('d M Y') }}
                                    @endif
                                </span>
                                <strong style="text-transform: uppercase; color: #777; font-size: 11px; letter-spacing: 1px; display: block; margin-bottom: 5px;">INVOICE DATE:</strong>
                                <span style="color: #333; display: block;">{{ date('d M Y') }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Line Items Table -->
            <tr>
                <td colspan="2" style="padding-top: 20px;">
                    <table cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="border: 1px solid #dee2e6; text-align: left; color: #555; text-transform: uppercase; font-size: 11px;">Description</th>
                                <th style="border: 1px solid #dee2e6; text-align: center; color: #555; text-transform: uppercase; font-size: 11px; width: 15%;">Quantity</th>
                                <th style="border: 1px solid #dee2e6; text-align: right; color: #555; text-transform: uppercase; font-size: 11px; width: 25%;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="border: 1px solid #dee2e6; vertical-align: top;">
                                    <strong style="font-size: 14px; color: #333;">Platform Commission</strong><br>
                                    <span style="font-size: 12px; color: #777;">Commission on {{ $salesData->count() }} successful transactions.</span>
                                </td>
                                <td style="border: 1px solid #dee2e6; text-align: center; vertical-align: middle;">{{ $salesData->count() }}</td>
                                <td style="border: 1px solid #dee2e6; text-align: right; vertical-align: middle;">₹{{ number_format($commission, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #dee2e6; vertical-align: top;">
                                    <strong style="font-size: 14px; color: #333;">GST ({{ $gstRate }}%)</strong><br>
                                    <span style="font-size: 12px; color: #777;">Tax applied on platform commission.</span>
                                </td>
                                <td style="border: 1px solid #dee2e6; text-align: center; vertical-align: middle;">-</td>
                                <td style="border: 1px solid #dee2e6; text-align: right; vertical-align: middle;">₹{{ number_format($gstAmount, 2) }}</td>
                            </tr>
                            <!-- Totals -->
                            <tr>
                                <td colspan="2" style="border: 1px solid #dee2e6; text-align: right; font-weight: bold;">Subtotal:</td>
                                <td style="border: 1px solid #dee2e6; text-align: right;">₹{{ number_format($commission, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="border: 1px solid #dee2e6; text-align: right; font-weight: bold;">Tax (GST {{ $gstRate }}%):</td>
                                <td style="border: 1px solid #dee2e6; text-align: right;">₹{{ number_format($gstAmount, 2) }}</td>
                            </tr>
                            <tr style="background-color: #f8f9fa;">
                                <td colspan="2" style="border: 1px solid #dee2e6; text-align: right; font-weight: bold; font-size: 15px; color: #1a5276;">Total Due:</td>
                                <td style="border: 1px solid #dee2e6; text-align: right; font-weight: bold; font-size: 15px; color: #1a5276;">₹{{ number_format($commission + $gstAmount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>

            <!-- Footer Text -->
            <tr>
                <td colspan="2" style="padding-top: 40px; text-align: center; color: #777; font-size: 12px; line-height: 1.6;">
                    <p style="margin: 0 0 5px 0; border-top: 1px solid #eee; padding-top: 15px;">This is a computer-generated invoice and does not require a physical signature.</p>
                    <p style="margin: 0; font-weight: bold; color: #555;">{{ $invoiceTemplate['invoiceFooter'] ?? 'Thank you for your business!' }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
