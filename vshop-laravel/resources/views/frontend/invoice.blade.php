<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .invoice-box {
            max-width: 400px;
            margin: auto;
            padding: 15px;
            border: 1px solid #eee;
            font-size: 12px;
            line-height: 20px;
        }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 5px; vertical-align: top; }
        .title { font-size: 24px; font-weight: bold; }
        .heading { background: #f0f0f0; border-bottom: 1px solid #ddd; font-weight: bold; }
        .item { border-bottom: 1px solid #eee; }
        .total { font-weight: bold; border-top: 2px solid #000; }
    </style>
</head>
<body>

<div class="invoice-box">

    <table>
        <tr>
            <td class="title">INVOICE</td>
            <td style="text-align:right;">
                <strong>Order ID:</strong> {{ $order->id }}<br>
                <strong>Date:</strong> {{ $order->created_at->format('d M Y') }}<br>
                <strong>Time:</strong> {{ $order->created_at->format('h:i A') }}<br>
                @if($order->payment_method)
                    <strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}<br>
                @endif
            </td>
        </tr>
    </table>

    <br>

    <table>
        <tr>
            <td width="50%">
                <strong>Sold By:</strong><br>
                VRIDHEE INNOVATIONS PRIVATE LIMITED<br>
                123, Industrial Estate,<br>
                Mumbai, India 400001
            </td>

            <td width="50%">
                <strong>Billing To:</strong><br>
                {{ $order->customer->name ?? 'N/A' }}<br>
                @if($order->customer->firstAddress)
                    {{ $order->customer->firstAddress->address_line1 ?? '' }}<br>
                    @if($order->customer->firstAddress->city)
                        {{ $order->customer->firstAddress->city }}, 
                    @endif
                    @if($order->customer->firstAddress->state)
                        {{ $order->customer->firstAddress->state }}
                    @endif
                    @if($order->customer->firstAddress->pincode)
                        - {{ $order->customer->firstAddress->pincode }}
                    @endif
                    <br>
                @elseif($order->customer->address_line1)
                    {{ $order->customer->address_line1 }}<br>
                    @if($order->customer->city)
                        {{ $order->customer->city }}, 
                    @endif
                    @if($order->customer->state)
                        {{ $order->customer->state }}
                    @endif
                    @if($order->customer->pincode)
                        - {{ $order->customer->pincode }}
                    @endif
                    <br>
                @endif
                @if($order->customer->phone)
                    Phone: {{ $order->customer->phone }}<br>
                @endif
                @if($order->customer->email)
                    Email: {{ $order->customer->email }}
                @endif
            </td>
        </tr>
    </table>

    <br><br>

    <table>
        <tr class="heading">
            <td>Item</td>
            <td style="text-align:center;">Qty</td>
            <td style="text-align:right;">Price</td>
            <td style="text-align:right;">GST</td>
            <td style="text-align:right;">Total</td>
        </tr>

        @php
            $totalSubtotal = 0;
            $totalGST = 0;
            $totalCourierCharge = 0;
        @endphp

        @foreach ($order->items as $item)
            @php
                // Parse JSON attributes
                $mainProductAttributes = $item->main_product_attributes ? json_decode($item->main_product_attributes, true) : [];
                $comboProductAttributes = $item->combo_product_attributes ? json_decode($item->combo_product_attributes, true) : [];
                
                // Get combo product if combo_id exists
                $comboProduct = null;
                if ($item->combo_id) {
                    $comboProduct = \App\Models\Product::find($item->combo_id);
                }
                
                // Get attributes from JSON
                $mainColor = null;
                $mainSize = null;
                $mainFabric = null;
                if (!empty($mainProductAttributes)) {
                    foreach ($mainProductAttributes as $attr) {
                        $slug = $attr['attribute_slug'] ?? '';
                        if (in_array($slug, ['colour', 'color'])) {
                            $mainColor = $attr['term_name'] ?? null;
                        } elseif ($slug === 'size') {
                            $mainSize = $attr['term_name'] ?? null;
                        } elseif (in_array($slug, ['febric', 'fabric'])) {
                            $mainFabric = $attr['term_name'] ?? null;
                        }
                    }
                }
                
                // Fallback to legacy color_id/size_id
                if (!$mainColor && $item->color) {
                    $mainColor = $item->color->term;
                }
                if (!$mainSize && $item->size) {
                    $mainSize = $item->size->term;
                }
                
                // Calculate prices
                if($item->combo_id && $comboProduct) {
                    // Combo product
                    $mainProductPrice = $item->main_product_price ?? 0;
                    $mainProductQuantity = $item->main_product_quantity ?? 1;
                    $comboProductPrice = $item->combo_product_price ?? 0;
                    $comboProductQuantity = $item->combo_product_quantity ?? 1;
                    
                    $mainProductTotal = $mainProductPrice * $mainProductQuantity;
                    $comboProductTotal = $comboProductPrice * $comboProductQuantity;
                    $itemSubtotal = $mainProductTotal + $comboProductTotal;
                    $gstPercent = $item->gst_percent ?? 5;
                    $itemGST = $itemSubtotal * ($gstPercent / 100);
                    $itemCourierCharge = $item->courier_charge ?? 0;
                    $itemTotal = $itemSubtotal + $itemGST + $itemCourierCharge;
                    
                    $totalQuantity = $mainProductQuantity + $comboProductQuantity;
                } else {
                    // Individual product
                    $itemSubtotal = ($item->price ?? 0) * ($item->quantity ?? 1);
                    $gstPercent = $item->gst_percent ?? 5;
                    $itemGST = $itemSubtotal * ($gstPercent / 100);
                    $itemCourierCharge = $item->courier_charge ?? 0;
                    $itemTotal = $itemSubtotal + $itemGST + $itemCourierCharge;
                    $totalQuantity = $item->quantity ?? 1;
                }
                
                $totalSubtotal += $itemSubtotal;
                $totalGST += $itemGST;
                $totalCourierCharge += $itemCourierCharge;
            @endphp

            {{-- Main Product Row --}}
            <tr class="item">
                <td>
                    <strong>{{ $item->product->name ?? 'Product not found' }}</strong><br>
                    <small>SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                    @if($item->product->category)
                        <br><small>Category: {{ $item->product->category->name }}</small>
                    @endif
                    @if($mainColor || $mainSize || $mainFabric || isset($mainProductAttributes['Booking Slots']))
                        <br><small>
                            @if($mainColor) Color: {{ $mainColor }} @endif
                            @if($mainSize) | Size: {{ $mainSize }} @endif
                            @if($mainFabric) | Fabric: {{ $mainFabric }} @endif
                            @if(isset($mainProductAttributes['Booking Slots'])) | Booking Slots: {{ $mainProductAttributes['Booking Slots']['name'] ?? '' }} @endif
                        </small>
                    @endif
                    @if($item->combo_id && $comboProduct)
                        <br><small style="color: #dc3545; font-weight: bold;">[COMBO PACKAGE]</small>
                    @endif
                </td>
                <td style="text-align:center;">
                    @if($item->combo_id && $comboProduct)
                        {{ $mainProductQuantity }}
                    @else
                        {{ $totalQuantity }}
                    @endif
                </td>
                <td style="text-align:right;">
                    @if($item->combo_id && $comboProduct)
                        ₹{{ number_format($mainProductPrice, 2) }}
                    @else
                        ₹{{ number_format($item->price ?? 0, 2) }}
                    @endif
                </td>
                <td style="text-align:right;">
                    @if($item->combo_id && $comboProduct)
                        {{ $gstPercent }}%
                    @else
                        {{ $gstPercent }}%
                    @endif
                </td>
                <td style="text-align:right;">
                    @if($item->combo_id && $comboProduct)
                        ₹{{ number_format($mainProductTotal, 2) }}
                    @else
                        ₹{{ number_format($itemSubtotal, 2) }}
                    @endif
                </td>
            </tr>

            {{-- Combo Product Row (if exists) --}}
            @if($item->combo_id && $comboProduct)
                @php
                    // Get combo attributes
                    $comboColor = null;
                    $comboSize = null;
                    $comboFabric = null;
                    if (!empty($comboProductAttributes)) {
                        foreach ($comboProductAttributes as $attr) {
                            $slug = $attr['attribute_slug'] ?? '';
                            if (in_array($slug, ['colour', 'color'])) {
                                $comboColor = $attr['term_name'] ?? null;
                            } elseif ($slug === 'size') {
                                $comboSize = $attr['term_name'] ?? null;
                            } elseif (in_array($slug, ['febric', 'fabric'])) {
                                $comboFabric = $attr['term_name'] ?? null;
                            }
                        }
                    }
                @endphp
                <tr class="item" style="background-color: #f9f9f9;">
                    <td>
                        <strong>{{ $comboProduct->name }}</strong><br>
                        <small>SKU: {{ $comboProduct->sku ?? 'N/A' }}</small>
                        @if($comboColor || $comboSize || $comboFabric)
                            <br><small>
                                @if($comboColor) Color: {{ $comboColor }} @endif
                                @if($comboSize) | Size: {{ $comboSize }} @endif
                                @if($comboFabric) | Fabric: {{ $comboFabric }} @endif
                            </small>
                        @endif
                        <br><small style="color: #dc3545; font-weight: bold;">[COMBO ITEM]</small>
                    </td>
                    <td style="text-align:center;">{{ $comboProductQuantity }}</td>
                    <td style="text-align:right;">₹{{ number_format($comboProductPrice, 2) }}</td>
                    <td style="text-align:right;">{{ $gstPercent }}%</td>
                    <td style="text-align:right;">₹{{ number_format($comboProductTotal, 2) }}</td>
                </tr>
                
                {{-- Combo Total Row --}}
                <tr class="item" style="border-top: 1px dashed #ccc;">
                    <td colspan="2" style="text-align:right; font-weight: bold;">
                        Subtotal (Main + Combo):
                    </td>
                    <td style="text-align:right;"></td>
                    <td style="text-align:right;">GST ({{ $gstPercent }}%):</td>
                    <td style="text-align:right;">
                        ₹{{ number_format($itemSubtotal, 2) }}<br>
                        <small>+ ₹{{ number_format($itemGST, 2) }}</small>
                        @if($itemCourierCharge > 0)
                            <br><small>+ Courier: ₹{{ number_format($itemCourierCharge, 2) }}</small>
                        @endif
                    </td>
                </tr>
                <tr class="item" style="border-bottom: 2px solid #ddd;">
                    <td colspan="4" style="text-align:right; font-weight: bold;">
                        Item Total:
                    </td>
                    <td style="text-align:right; font-weight: bold;">
                        ₹{{ number_format($itemTotal, 2) }}
                    </td>
                </tr>
            @else
                {{-- Individual Product Total Row --}}
                <tr class="item" style="border-top: 1px dashed #ccc;">
                    <td colspan="2" style="text-align:right; font-weight: bold;">
                        Subtotal:
                    </td>
                    <td style="text-align:right;"></td>
                    <td style="text-align:right;">GST ({{ $gstPercent }}%):</td>
                    <td style="text-align:right;">
                        ₹{{ number_format($itemSubtotal, 2) }}<br>
                        <small>+ ₹{{ number_format($itemGST, 2) }}</small>
                        @if($itemCourierCharge > 0)
                            <br><small>+ Courier: ₹{{ number_format($itemCourierCharge, 2) }}</small>
                        @endif
                    </td>
                </tr>
                <tr class="item" style="border-bottom: 2px solid #ddd;">
                    <td colspan="4" style="text-align:right; font-weight: bold;">
                        Item Total:
                    </td>
                    <td style="text-align:right; font-weight: bold;">
                        ₹{{ number_format($itemTotal, 2) }}
                    </td>
                </tr>
            @endif
        @endforeach

        {{-- Order Totals --}}
        <tr class="total">
            <td colspan="4" style="text-align:right; font-weight: bold;">Subtotal:</td>
            <td style="text-align:right; font-weight: bold;">₹{{ number_format($totalSubtotal, 2) }}</td>
        </tr>

        <tr class="total">
            <td colspan="4" style="text-align:right; font-weight: bold;">GST:</td>
            <td style="text-align:right; font-weight: bold;">₹{{ number_format($totalGST, 2) }}</td>
        </tr>

        @if($totalCourierCharge > 0)
        <tr class="total">
            <td colspan="4" style="text-align:right; font-weight: bold;">Courier Charge:</td>
            <td style="text-align:right; font-weight: bold;">₹{{ number_format($totalCourierCharge, 2) }}</td>
        </tr>
        @endif

        <tr class="total" style="border-top: 3px solid #000;">
            <td colspan="4" style="text-align:right; font-size:18px; font-weight: bold;">Grand Total:</td>
            <td style="text-align:right; font-size:18px; font-weight: bold;">₹{{ number_format($order->total_amount, 2) }}</td>
        </tr>
    </table>

    <br><br>

    <p style="text-align:center; font-size:12px;">
        Thank you for shopping with Vridhee!
    </p>
</div>

</body>
</html>
