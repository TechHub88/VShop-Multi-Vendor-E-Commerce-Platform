<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Order #{{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 30px;
            color: #333;
        }

        p {
            font-size: 13px;
            margin: 0;
            margin-bottom: 6px;
        }

        .invoice-wrapper {
            max-width: 900px;
            margin: auto;
            background: #ffffff;
            padding: 35px;
            border: 1px solid #ddd;
        }

        .header-center h1 {
            font-size: 24px;
            text-align: center;
            margin: 0;
            margin-left: 34px;
        }

        .office-dtl-sec {
            display: flex;
        }

        .office-dtl-left-inner {
            border: 1px solid #000;
            padding: 10px;
        }

        .invoice-name-sec {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }

        .invoice-name-sec h2 {
            margin: 0;
            margin-bottom: 12px;
            font-size: 20px;
        }

        .invoice-name-sec p {
            margin: 0;
        }

        .invoice-box-wrapper table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-box-wrapper table tbody tr td {
            border: 1px solid #000;
            padding: 10px;
            font-size: 14px;
            font-weight: 600;
        }
        .invoice-box-wrapper table tbody tr td p {
            font-size: 14px;
            font-weight: 600;
        }
        .bill-dtl-top:first-child {
            margin-bottom: 20px;
        }
        .bill-dtl-top p:first-child {
            display: flex;
        }
        .bill-dtl-top p:first-child span {
            display: inline-block;
            width: 50%;
        }
        .po-sec {
            display: flex;
        }
        .po-sec span {
            display: inline-block;
            width: 50%;
        }
        .invoice-details-sec table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details-sec table thead tr th {
            border: 1px solid #000;
            padding: 10px;
            font-size: 14px;
            font-weight: 600;
        }
        .invoice-details-sec table tbody tr td {
            border: 1px solid #000;
            padding: 10px;
            font-size: 14px;
        }
        .invoice-details-sec table tfoot tr td {
            border: 1px solid #000;
            padding: 10px;
            font-size: 14px;
            font-weight: 600;
        }
        .invoice-bottom-sec table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-bottom-sec table tbody tr td {
            border: 1px solid #000;
            padding: 10px;
            font-size: 14px;
            font-weight: 600;
        }
        .invoice-bottom-sec table tbody tr td p {
            font-size: 14px;
            font-weight: 600;
        }
        .one-line {
            white-space: nowrap;
        }
        .tearms-sec {
            display: flex;
            align-items: self-start;
            border: 1px solid #000;
            padding: 10px;
        }
        .tearms-box:first-child {
            width: 70%;
        }
        .tearms-box:last-child {
            width: 30%;
        }
        .tearms-top p {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .tearms-bottom p {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .tearms-box:last-child .tearms-top p {
            text-align: right;
        }
        .signature-bottom p {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 0;
            margin-top: 60px;
            text-align: right;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px;">Print Invoice</button>
        <a href="{{ route('admin.orders.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; display: inline-block; margin-left: 10px;">Back to Orders</a>
    </div>

    <div class="invoice-wrapper">
        <div class="header" style="display: flex;">
            <div style="width: 70%;">
                <div class="header-top" style="display: flex;align-items: center;justify-content: space-between;">
                    <div class="header-left">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="max-height: 80px;" onerror="this.style.display='none'">
                    </div>
                    <div class="header-center">
                        <h1>MAFATLAL INDUSTRIES LIMITED</h1>
                    </div>
                </div>
                <div class="header-bottom" style="width: 100%;">
                    <div class="office-dtl-left">
                        <div class="office-dtl-left-inner">
                            <p><strong>REGD.OFFICE</strong>: 301-302, <strong>HERITAGE HORIZONE, 3RD FLOOR, OFF.C.G.ROAD, NAVRANGPURA</strong>,</p>
                            <p><strong>AHMEDABAD</strong>-380009, INDIA <strong>TEL</strong>: 079-26444404, <strong>FAX</strong>: 079-26444403</p>
                            <p><strong>MKTG. OFFICE</strong>: MARKETING OFFICE, 5TH FLOOR, H. T. PAREKH MARG, BACKBAY RECLAMATION,CHURCHGATE, <strong>MUMBAI</strong>-400020, <strong>CIN</strong> - L17110GJ1913PLC000035.</p>
                            <p><strong>FACTORY/WAREHOUSE</strong>: UNIT- II (MSD),,KAPADVANJ ROAD,,NADIAD-387001</p>
                            <p><strong>GSTIN.</strong>: 24AAACM2813L1ZO</p>
                            <p><strong>WHETHER RCM APPLICABLE OR NOT AND ANSWER TO BE NO</strong></p>
                        </div>
                        <div class="invoice-name-sec">
                            <h2>TAX INVOICE</h2>
                            <p>INVOICE ISSUED AS PER RULE 46 OF GST RULES</p>
                        </div>
                    </div>
                </div>
            </div>
            <div style="width: 30%;">
                <div class="office-dtl-qr" style="width: 100%;height: 100%;display: flex;align-items: self-start;justify-content: right;padding-top: 33px;">
                    <img src="{{ asset('images/qr-code.png') }}" alt="QR Code" style="width: 92%;height: 83%;" onerror="this.style.display='none'">
                </div>
            </div>
        </div>

        <div class="invoice-box-wrapper">
            <table>
                <tbody>
                    <tr>
                        <td>Original for Receipient</td>
                        <td>Duplicate for Transporter </td>
                        <td>Triplicate for Supplier</td>
                    </tr>
                </tbody>
            </table>
            <table>
                <tbody>
                    <tr>
                        <td>Invoice No : {{ $order->id ?? 'N/A' }}</td>
                        <td>Invoice Date : {{ $order->created_at ? $order->created_at->format('d.m.Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="bill-dtl">
                                <div class="bill-dtl-top">
                                    <p>Bill to Party : {{ $order->customer->id ?? 'N/A' }}</p>
                                    <p>{{ $order->customer->name ?? 'N/A' }}</p>
                                    @php
                                        $billingAddress = $order->customer->firstAddress ?? null;
                                        $billingAddressText = '';
                                        if ($billingAddress) {
                                            $parts = array_filter([
                                                $billingAddress->address_line1,
                                                $billingAddress->city,
                                                $billingAddress->state,
                                                $billingAddress->pincode
                                            ]);
                                            $billingAddressText = implode(', ', $parts);
                                        }
                                    @endphp
                                    <p>{{ $billingAddressText ?: ($order->customer->address ?? 'N/A') }}</p>
                                </div>
                                <div class="bill-dtl-top">
                                    <p>
                                        <span>State Code : N/A</span>
                                        <span>State: {{ $billingAddress->state ?? ($order->customer->state ?? 'N/A') }}</span>
                                    </p>
                                    <p>GSTIN/UI : {{ $billingAddress->gstin ?? ($order->customer->gstin ?? 'N/A') }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="bill-dtl">
                                <div class="bill-dtl-top">
                                    <p>Ship to Party : {{ $order->customer->id ?? 'N/A' }}</p>
                                    <p>{{ $order->customer->name ?? 'N/A' }}</p>
                                    <p>{{ $billingAddressText ?: ($order->customer->address ?? 'N/A') }}</p>
                                </div>
                                <div class="bill-dtl-top">
                                    <p> 
                                        <span>State Code : N/A</span>
                                        <span>State: {{ $billingAddress->state ?? ($order->customer->state ?? 'N/A') }}</span>
                                    </p>
                                    <p>GSTIN/UI : {{ $billingAddress->gstin ?? ($order->customer->gstin ?? 'N/A') }}</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Guaranter Code : N/A</td>
                        <td>Guaranter Name : {{ $order->customer->company_name ?? 'DIRECT SALE' }}</td>
                    </tr>
                    <tr>
                        <td>
                            <p class="po-sec">
                                <span>PO No : N/A</span>
                                <span>PO Date : N/A</span>
                            </p>
                        </td>
                        <td>Profit Center Code : N/A</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="transpoart-sec">
                                <p>Transporter Name : {{ $order->shipping_method ?? 'DIRECT DISPATCH' }}</p>
                                <p>LR No.: N/A</p>
                                <p>LR Date.: N/A</p>
                            </div>
                        </td>
                        <td>
                            <div class="transpoart-sec">
                                <p>Place of Supply: {{ $billingAddress->city ?? ($order->customer->city ?? 'N/A') }}</p>
                                <p>LR No.: N/A</p>
                                <p>Delivery No: {{ $order->id ?? 'N/A' }}</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">IRN No.: N/A</td>
                    </tr>
                    <tr>
                        <td>From : {{ $billingAddress->city ?? ($order->customer->city ?? 'N/A') }}</td>
                        <td>To : {{ $billingAddress->city ?? ($order->customer->city ?? 'N/A') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="invoice-details-sec">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2"> Sr no</th>
                        <th rowspan="2">Description of Goods </th>
                        <th rowspan="2">HSN/SAC </th>
                        <th rowspan="2">Qty </th>
                        <th rowspan="2">UoM</th>
                        <th rowspan="2">Rate</th>
                        <th rowspan="2">Disc.%</th>
                        <th rowspan="2">Taxable Amount INR</th>
                        <th colspan="2">IGST </th>
                        <th rowspan="2">Total INR</th>
                    </tr>
                    <tr>
                        <th>Rate %</th>
                        <th>Amount INR</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $srNo = 1;
                        $totalQty = 0;
                        $totalTaxable = 0;
                        $totalIGST = 0;
                        $grandTotal = 0;
                    @endphp
                    @foreach($order->items as $item)
                    @php
                        $mainProductAttributes = $item->main_product_attributes ? json_decode($item->main_product_attributes, true) : [];
                        $comboProductAttributes = $item->combo_product_attributes ? json_decode($item->combo_product_attributes, true) : [];
                        
                        $comboProduct = null;
                        if ($item->combo_id) {
                            $comboProduct = \App\Models\Product::find($item->combo_id);
                        }
                        
                        if($item->combo_id && $comboProduct) {
                            $mainProductPrice = $item->main_product_price ?? 0;
                            $mainProductQuantity = $item->main_product_quantity ?? 1;
                            $comboProductPrice = $item->combo_product_price ?? 0;
                            $comboProductQuantity = $item->combo_product_quantity ?? 1;
                            
                            $mainProductTotal = $mainProductPrice * $mainProductQuantity;
                            $comboProductTotal = $comboProductPrice * $comboProductQuantity;
                            $totalUnitPrice = $mainProductTotal + $comboProductTotal;
                            $quantity = $mainProductQuantity + $comboProductQuantity;
                        } else {
                            $unitPrice = $item->price ?? 0;
                            $quantity = $item->quantity ?? 1;
                            $totalUnitPrice = $unitPrice * $quantity;
                        }
                        
                        $gstPercent = $item->gst_percent ?? 5;
                        $gstAmount = $totalUnitPrice * ($gstPercent / 100);
                        $itemTotal = $totalUnitPrice + $gstAmount;
                        
                        $totalQty += $quantity;
                        $totalTaxable += $totalUnitPrice;
                        $totalIGST += $gstAmount;
                        $grandTotal += $itemTotal;
                        
                        $productName = $item->product->name ?? 'N/A';
                        if($item->combo_id && $comboProduct) {
                            $productName .= ' (Combo: ' . ($comboProduct->name ?? 'N/A') . ')';
                        }
                    @endphp
                    <tr>
                        <td>{{ $srNo++ }}</td>
                        <td><strong>{{ strtoupper($productName) }}</strong></td>
                        <td>N/A</td>
                        <td>{{ number_format($quantity, 3) }}</td>
                        <td>PCS</td>
                        <td>{{ number_format($item->price ?? 0, 2) }}</td>
                        <td>0.00</td>
                        <td>{{ number_format($totalUnitPrice, 2) }}</td>
                        <td>{{ number_format($gstPercent, 2) }}</td>
                        <td>{{ number_format($gstAmount, 2) }}</td>
                        <td>{{ number_format($itemTotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">Total</td>
                        <td>{{ number_format($totalQty, 3) }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($totalTaxable, 2) }}</td>
                        <td></td>
                        <td>{{ number_format($totalIGST, 2) }}</td>
                        <td>{{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="invoice-bottom-sec">
            <table>
                <tbody>
                    <tr>
                        <td colspan="3">No. of Bales : N/A</td>
                    </tr>
                    <tr>
                        <td colspan="3">Bale No : N/A</td>
                    </tr>
                    <tr>
                        <td colspan="3">Total Invoice Value (In Words) : 
                            @php
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
                                $amount = intval($order->total_amount ?? 0);
                                $words = $amount > 0 ? numberToWords($amount) . ' Rupees Only' : 'N/A';
                            @endphp
                            {{ $words }}
                        </td>
                    </tr>
                    <tr>
                        <td>Bank Details: ICICI BANK A/C NO.: 642751000003</td>
                        <td class="one-line">Amount Before Tax </td>
                        <td>{{ number_format($order->sub_total ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td rowspan="7" style="vertical-align: baseline;">
                            <p>IFSC Code: ICIC0006427</p>
                            <p>TATA AIG Insurance No.: 0830013326</p>
                            <p>Declaration : We hereby certify that the particulars given are true and correct and amount indicated represents the price actually charged and that there is flow additional consideration directly or indirectly from the buyer.</p>
                        </td>
                        <td>Discount </td>
                        <td>0.00</td>
                    </tr>
                    <tr>
                        <td>Packing </td>
                        <td>0.00</td>
                    </tr>
                    <tr>
                        <td>Freight </td>
                        <td>{{ number_format($order->courier_charge ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total GST </td>
                        <td>{{ number_format($order->gst_amount ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>TCS </td>
                        <td>0.00</td>
                    </tr>
                    <tr>
                        <td>Rounding Off</td>
                        <td>0.00</td>
                    </tr>
                    <tr>
                        <td class="one-line">Total Invoice Value INR</td>
                        <td>{{ number_format($order->total_amount ?? 0, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="tearms-sec">
            <div class="tearms-box">
                <div class="tearms-top">
                    <p>Terms & Conditions :-</p>
                </div>
                <div class="tearms-bottom">
                    <p>Contract: The terms and conditions hereof shall constitute a binding contract between Seller and Buyer concerning the goods sold hereunder.</p>
                    <p>Payments: All Payment terms shall be as per the terms and conditions mentioned in our payment circular/purchase order. Any delay in payments shall accrue interest at the rate of 24% p.a. till the date of actual realization.</p>
                    <p>Claims: All claims relating to quantity or quality shall be waived by Buyer unless made in writing within fifteen (15) days from the date of receipt of goods, to the address stated.</p>
                    <p>Return: Goods once dispatched shall not be taken back or exchanged.</p>
                    <p>Dispute Resolution: Any or all disputes arising out of or in respect to the invoice issued against GST No. 24AAACM2813L1ZO shall be resolved by means of Arbitration in accordance with the Arbitration and Conciliation Act of 1996. The seat of Arbitration shall be Nadiad or Ahmedabad in Gujarat.</p>
                </div>
            </div>
            <div class="tearms-box">
                <div class="tearms-top">
                    <p>For : Mafatlal Industries Limited</p>
                </div>
                <div class="signature-bottom">
                    <p>Authorised Signatory</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
