<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_no ?? $order->id }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            padding: 30px 15px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print {
            max-width: 820px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-success {
            background-color: #10b981;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        .btn-success:hover {
            background-color: #059669;
        }

        .btn-secondary {
            background-color: #64748b;
            color: #ffffff;
        }
        .btn-secondary:hover {
            background-color: #475569;
        }

        .invoice-card {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            padding: 40px;
            border: 1px solid #e2e8f0;
        }

        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 24px;
            border-bottom: 2px solid #f1f5f9;
            margin-bottom: 24px;
        }

        .seller-brand {
            max-width: 60%;
        }

        .seller-name {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .seller-details {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: 800;
            color: #2563eb;
            letter-spacing: -0.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .meta-line {
            font-size: 13px;
            color: #475569;
            margin-bottom: 4px;
        }

        .badge-status {
            display: inline-block;
            padding: 4px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }

        .badge-paid {
            background-color: #dcfce7;
            color: #15803d;
        }

        .badge-unpaid {
            background-color: #fef3c7;
            color: #b45309;
        }

        /* Billing / Shipping grid */
        .address-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .address-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 18px;
        }

        .address-box-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.8px;
            margin-bottom: 10px;
        }

        .party-name {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .party-details {
            font-size: 13px;
            color: #475569;
            line-height: 1.5;
        }

        /* Table styling */
        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 30px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .items-table th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .items-table td {
            padding: 14px 16px;
            font-size: 13.5px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .product-title {
            font-weight: 600;
            color: #0f172a;
        }

        .product-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Summary Footer Grid */
        .summary-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 30px;
            align-items: flex-start;
        }

        .notes-box {
            font-size: 12.5px;
            color: #64748b;
            line-height: 1.6;
        }

        .notes-box h4 {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 8px 0;
            font-size: 13.5px;
            color: #475569;
        }

        .summary-table tr.grand-total td {
            padding-top: 12px;
            border-top: 2px solid #e2e8f0;
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
        }

        .words-total {
            background-color: #eff6ff;
            border: 1px dashed #bfdbfe;
            border-radius: 6px;
            padding: 10px 14px;
            margin-top: 20px;
            font-size: 12.5px;
            color: #1e40af;
            font-weight: 500;
        }

        .signature-section {
            margin-top: 40px;
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .signature-box {
            text-align: right;
        }

        .signature-line {
            width: 180px;
            border-bottom: 1px solid #cbd5e1;
            margin-left: auto;
            margin-bottom: 8px;
        }

        .signature-title {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        /* Print Override */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .invoice-card {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            @page {
                margin: 15mm;
            }
        }
    </style>
</head>
@php
    $seller = $order->seller ?? null;
    $sellerName = $seller->legal_name ?? ($seller->org_name ?? ($seller->name ?? 'VRIDHI INNOVATION'));
    $sellerGstin = $seller->gst_number ?? '24AAACM2813L1ZO';
    $sellerAddressParts = array_filter([
        $seller->billing_address ?? null,
        $seller->billing_city ?? null,
        $seller->billing_state ?? null,
        $seller->billing_zip ?? null
    ]);
    $sellerAddress = !empty($sellerAddressParts) ? implode(', ', $sellerAddressParts) : 'Surat, Gujarat, India';
    $sellerEmail = $seller->org_email ?? ($seller->email ?? 'info@vridhiinnovation.com');
    $sellerPhone = $seller->org_mobile ?? ($seller->mobile ?? ($seller->phone ?? 'N/A'));

    $customer = $order->customer ?? null;
    $customerAddressObj = $customer->firstAddress ?? null;
    $customerAddressParts = array_filter([
        $customerAddressObj->address_line1 ?? ($customerAddressObj->address ?? null),
        $customerAddressObj->city ?? ($customer->city ?? null),
        $customerAddressObj->state ?? ($customer->state ?? null),
        $customerAddressObj->pincode ?? ($customerAddressObj->zip ?? null)
    ]);
    $customerAddress = !empty($customerAddressParts) ? implode(', ', $customerAddressParts) : ($customer->address ?? 'N/A');

    if (!function_exists('numberToWords')) {
        function numberToWords($number) {
            $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
            $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            
            if ($number == 0) return 'Zero';
            if ($number < 20) return $ones[$number];
            if ($number < 100) return $tens[intval($number/10)] . ($number % 10 ? ' ' . $ones[$number % 10] : '');
            if ($number < 1000) return $ones[intval($number/100)] . ' Hundred' . ($number % 100 ? ' ' . numberToWords($number % 100) : '');
            if ($number < 100000) return numberToWords(intval($number/1000)) . ' Thousand' . ($number % 1000 ? ' ' . numberToWords($number % 1000) : '');
            if ($number < 10000000) return numberToWords(intval($number/100000)) . ' Lakh' . ($number % 100000 ? ' ' . numberToWords($number % 100000) : '');
            return numberToWords(intval($number/10000000)) . ' Crore' . ($number % 10000000 ? ' ' . numberToWords($number % 10000000) : '');
        }
    }
    $amountInWords = intval($order->total_amount ?? 0) > 0 ? numberToWords(intval($order->total_amount)) . ' Rupees Only' : 'N/A';
@endphp
<body>

    <!-- Action Buttons -->
    <div class="no-print">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            &larr; Back to Orders
        </a>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Print Invoice
            </button>
            <a href="{{ route('admin.orders.invoice', ['id' => $order->id, 'download' => 'pdf']) }}" class="btn btn-success">
                ⬇️ Direct Download PDF
            </a>
        </div>
    </div>

    <!-- Main Flat Invoice Container -->
    <div class="invoice-card">
        
        <!-- Top Header -->
        <div class="invoice-header">
            <div class="seller-brand">
                <div class="seller-name">{{ $sellerName }}</div>
                <div class="seller-details">
                    <p><strong>Office:</strong> {{ $sellerAddress }}</p>
                    <p><strong>GSTIN:</strong> {{ $sellerGstin }}</p>
                    <p><strong>Contact:</strong> {{ $sellerPhone }} | {{ $sellerEmail }}</p>
                </div>
            </div>
            <div class="invoice-meta">
                <div class="invoice-title">Tax Invoice</div>
                <div class="meta-line"><strong>Invoice No:</strong> #{{ $order->order_no ?? $order->id }}</div>
                <div class="meta-line"><strong>Date:</strong> {{ $order->created_at ? $order->created_at->format('d M, Y') : 'N/A' }}</div>
                <div class="meta-line"><strong>Payment:</strong> {{ strtoupper($order->payment_type ?? 'COD') }}</div>
                <div>
                    @if(strtolower($order->payment_status ?? '') === 'paid')
                        <span class="badge-status badge-paid">Paid</span>
                    @else
                        <span class="badge-status badge-unpaid">Unpaid</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Address Grid -->
        <div class="address-grid">
            <div class="address-box">
                <div class="address-box-title">Billed To</div>
                <div class="party-name">{{ $customer->name ?? 'Customer' }}</div>
                <div class="party-details">
                    <p>{{ $customerAddress }}</p>
                    <p style="margin-top: 4px;"><strong>Email:</strong> {{ $customer->email ?? 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ $customer->mobile ?? ($customer->phone ?? 'N/A') }}</p>
                </div>
            </div>
            <div class="address-box">
                <div class="address-box-title">Shipped To</div>
                <div class="party-name">{{ $customer->name ?? 'Customer' }}</div>
                <div class="party-details">
                    <p>{{ $customerAddress }}</p>
                    <p style="margin-top: 4px;"><strong>Shipping Method:</strong> {{ $order->shipping_method ?? 'Standard Delivery' }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">Item Description</th>
                    <th class="text-center" style="width: 12%;">Qty</th>
                    <th class="text-right" style="width: 18%;">Unit Price</th>
                    <th class="text-right" style="width: 20%;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $sr = 1; $calcSubtotal = 0; @endphp
                @forelse($order->items as $item)
                @php
                    $qty = $item->quantity ?? 1;
                    $price = $item->price ?? 0;
                    $itemTotal = $price * $qty;
                    $calcSubtotal += $itemTotal;
                @endphp
                <tr>
                    <td class="text-center">{{ $sr++ }}</td>
                    <td>
                        <div class="product-title">{{ $item->product->name ?? 'Product Item' }}</div>
                        @if($item->color || $item->size)
                        <div class="product-sub">
                            @if($item->color) Color: {{ $item->color->name ?? '-' }} @endif
                            @if($item->size) | Size: {{ $item->size->name ?? '-' }} @endif
                        </div>
                        @endif
                    </td>
                    <td class="text-center">{{ $qty }}</td>
                    <td class="text-right">₹{{ number_format($price, 2) }}</td>
                    <td class="text-right" style="font-weight: 600;">₹{{ number_format($itemTotal, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px; color: #94a3b8;">No items in this order</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary & Totals -->
        <div class="summary-grid">
            <div class="notes-box">
                <h4>Terms & Declaration</h4>
                <p>We certify that this invoice shows the actual price of the goods described and that all particulars are true and correct.</p>
                
                <div class="words-total">
                    <strong>Amount in Words:</strong><br>
                    {{ $amountInWords }}
                </div>
            </div>
            <div>
                <table class="summary-table">
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-right">₹{{ number_format($order->sub_total ?? $calcSubtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Shipping / Courier</td>
                        <td class="text-right">₹{{ number_format($order->courier_charge ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>GST / Tax</td>
                        <td class="text-right">₹{{ number_format($order->gst_amount ?? 0, 2) }}</td>
                    </tr>
                    @if(($order->discount ?? 0) > 0)
                    <tr>
                        <td>Discount</td>
                        <td class="text-right">-₹{{ number_format($order->discount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="grand-total">
                        <td>Grand Total</td>
                        <td class="text-right" style="color: #2563eb;">₹{{ number_format($order->total_amount ?? $calcSubtotal, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer Signature -->
        <div class="signature-section">
            <div style="font-size: 12px; color: #94a3b8;">
                Thank you for your business!
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-title">For {{ $sellerName }}</div>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Authorised Signatory</div>
            </div>
        </div>

    </div>

</body>
</html>
