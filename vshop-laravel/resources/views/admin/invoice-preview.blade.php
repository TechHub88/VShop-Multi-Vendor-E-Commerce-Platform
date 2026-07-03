<div class="invoice-container p-4" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; background: #fff;">
    <!-- Header -->
    <div class="row mb-4 border-bottom pb-3 align-items-center">
        <div class="col-sm-6">
            <h2 class="mb-0 fw-bold" style="color: #2c3e50;">INVOICE</h2>
            <p class="text-muted mb-0">Invoice #{{ $invoiceTemplate['invoicePrefix'] ?? 'INV' }}-{{ $seller->id }}-{{ date('Ymd') }}</p>
            <p class="text-muted fs-8">Date: {{ date('d M Y') }}</p>
        </div>
        <div class="col-sm-6 text-end">
            <!-- Professional Branding -->
            <h4 class="mb-1 fw-bold" style="color: #1a5276;">{{ $invoiceTemplate['companyName'] ?? 'Bridhi Invation' }}</h4>
            <p class="text-muted mb-0 fs-8">{{ $invoiceTemplate['companyAddress'] ?? '123 Bridhi Tower, Tech Park' }}</p>
            @if(!empty($invoiceTemplate['companyPhone'])) <p class="text-muted mb-0 fs-8">Phone: {{ $invoiceTemplate['companyPhone'] }}</p> @endif
            @if(!empty($invoiceTemplate['companyEmail'])) <p class="text-muted mb-0 fs-8">Email: {{ $invoiceTemplate['companyEmail'] }}</p> @endif
            @if(!empty($invoiceTemplate['companyGst'])) <p class="text-muted mb-0 fs-8">GSTIN: {{ $invoiceTemplate['companyGst'] }}</p> @endif
        </div>
    </div>

    <!-- Bill To -->
    <div class="row mb-4">
        <div class="col-sm-6">
            <h6 class="text-uppercase text-muted fw-bold mb-2" style="letter-spacing: 1px; font-size: 12px;">Billed To:</h6>
            <h5 class="fw-bold mb-1">{{ $seller->name }}</h5>
            <p class="text-muted mb-0 fs-8">Seller ID: S-{{ $seller->id }}</p>
            <p class="text-muted mb-0 fs-8">{{ $seller->email }}</p>
            @if($seller->phone) <p class="text-muted mb-0 fs-8">{{ $seller->phone }}</p> @endif
            @if($seller->gst_number) <p class="text-muted mb-0 fs-8">GSTIN: {{ $seller->gst_number }}</p> @endif
        </div>
        <div class="col-sm-6 text-end">
            <h6 class="text-uppercase text-muted fw-bold mb-2" style="letter-spacing: 1px; font-size: 12px;">Billing Period:</h6>
            <p class="mb-0 fs-8">
                @if($startDate && $endDate)
                    {{ date('d M Y', strtotime($startDate)) }} to {{ date('d M Y', strtotime($endDate)) }}
                @else
                    All Time up to {{ date('d M Y') }}
                @endif
            </p>
        </div>
    </div>

    <!-- Items Table -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered border-primary" style="border-color: #dee2e6 !important;">
            <thead style="background-color: #f8f9fa;">
                <tr>
                    <th class="text-uppercase text-muted fs-8">Description</th>
                    <th class="text-uppercase text-muted fs-8 text-center" style="width: 15%">Quantity</th>
                    <th class="text-uppercase text-muted fs-8 text-end" style="width: 20%">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <h6 class="mb-0 fs-7">Platform Commission</h6>
                        <small class="text-muted">Commission on {{ $salesData->count() }} successful transactions.</small>
                    </td>
                    <td class="text-center align-middle">{{ $salesData->count() }}</td>
                    <td class="text-end align-middle">₹{{ number_format($commission, 2) }}</td>
                </tr>
                <tr>
                    <td>
                        <h6 class="mb-0 fs-7">GST ({{ $gstRate }}%)</h6>
                        <small class="text-muted">Tax applied on platform commission.</small>
                    </td>
                    <td class="text-center align-middle">-</td>
                    <td class="text-end align-middle">₹{{ number_format($gstAmount, 2) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-end">Subtotal:</th>
                    <th class="text-end">₹{{ number_format($commission, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="2" class="text-end">Tax (GST {{ $gstRate }}%):</th>
                    <th class="text-end">₹{{ number_format($gstAmount, 2) }}</th>
                </tr>
                <tr style="background-color: #f8f9fa;">
                    <th colspan="2" class="text-end fs-5 text-primary">Total Due:</th>
                    <th class="text-end fs-5 text-primary">₹{{ number_format($commission + $gstAmount, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Footer -->
    <div class="text-center mt-5 pt-3 border-top text-muted">
        <p class="mb-1 fs-8">This is a computer-generated invoice and does not require a physical signature.</p>
        <p class="fw-bold fs-7 mb-0">{{ $invoiceTemplate['invoiceFooter'] ?? 'Thank you for your business!' }}</p>
    </div>
</div>
