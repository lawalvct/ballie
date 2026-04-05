@php
    $vendor = $purchaseOrder->vendor;
    $vendorName = $vendor ? $vendor->getFullNameAttribute() : 'N/A';

    if (!function_exists('numberToWordsPdf')) {
        function numberToWordsPdf($number) {
            if ($number == 0) return 'Zero';
            $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
                     'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
                     'Seventeen', 'Eighteen', 'Nineteen'];
            $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            $number = (int) $number;
            if ($number < 20) return $ones[$number];
            if ($number < 100) return $tens[intval($number / 10)] . ($number % 10 ? ' ' . $ones[$number % 10] : '');
            if ($number < 1000) return $ones[intval($number / 100)] . ' Hundred' . ($number % 100 ? ' ' . numberToWordsPdf($number % 100) : '');
            if ($number < 1000000) return numberToWordsPdf(intval($number / 1000)) . ' Thousand' . ($number % 1000 ? ' ' . numberToWordsPdf($number % 1000) : '');
            if ($number < 1000000000) return numberToWordsPdf(intval($number / 1000000)) . ' Million' . ($number % 1000000 ? ' ' . numberToWordsPdf($number % 1000000) : '');
            return 'Amount too large';
        }
    }

    $invoiceBank = null;
    if (!empty($tenant->settings['invoice_bank_account_id'])) {
        $invoiceBank = \App\Models\Bank::find($tenant->settings['invoice_bank_account_id']);
    }

    $logoPath = null;
    if ($tenant->logo && file_exists(storage_path('app/public/' . $tenant->logo))) {
        $logoPath = storage_path('app/public/' . $tenant->logo);
    }
    $signaturePath = null;
    if ($tenant->signature && file_exists(storage_path('app/public/' . $tenant->signature))) {
        $signaturePath = storage_path('app/public/' . $tenant->signature);
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order {{ $purchaseOrder->lpo_number }} - {{ $tenant->name }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 10px;
            color: #333;
            font-size: 11px;
            line-height: 1.3;
        }

        p { margin: 0 0 3px 0; }

        /* ── Header ── */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 2px solid #e67e22;
            padding-bottom: 8px;
        }
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }
        .logo {
            width: 45px;
            height: 45px;
            border-radius: 6px;
            margin-bottom: 5px;
            float: left;
            margin-right: 10px;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #e67e22;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .company-details {
            font-size: 10px;
            color: #666;
            line-height: 1.3;
            clear: both;
        }
        .doc-title {
            font-size: 18px;
            font-weight: bold;
            color: #e67e22;
            margin-bottom: 4px;
        }
        .doc-number {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin-bottom: 2px;
        }
        .doc-meta {
            font-size: 10px;
            color: #666;
        }

        /* ── Billing ── */
        .billing-info {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .vendor-to, .doc-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 8px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        .vendor-to {
            border-right: none;
            border-radius: 6px 0 0 6px;
        }
        .doc-info {
            border-radius: 0 6px 6px 0;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #e67e22;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .vendor-name {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
        }
        .detail-line {
            margin-bottom: 1px;
            font-size: 10px;
        }

        /* ── Items Table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 10px;
        }
        .items-table th {
            background: #e67e22;
            color: white;
            padding: 5px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 5px 6px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
        }
        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .sn-col {
            width: 5%;
            text-align: center;
            font-weight: bold;
            background: #f1f3f4;
        }
        .product-name { font-weight: bold; color: #333; }
        .product-desc { color: #666; font-size: 9px; }

        /* ── Summary ── */
        .summary-section {
            float: right;
            width: 260px;
            margin-bottom: 8px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            overflow: hidden;
        }
        .summary-table td {
            padding: 4px 8px;
            border-bottom: 1px solid #e9ecef;
        }
        .summary-label {
            background: #f8f9fa;
            font-weight: 500;
            color: #555;
        }
        .summary-amount {
            text-align: right;
            font-weight: bold;
        }
        .total-row {
            background: #e67e22;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        .total-row td {
            border-bottom: none;
            padding: 6px 8px;
        }

        /* ── Amount in Words ── */
        .amount-words {
            clear: both;
            background: #e8f5e8;
            padding: 6px 10px;
            border-radius: 4px;
            border-left: 3px solid #28a745;
            margin-bottom: 8px;
        }
        .amount-words-label {
            font-size: 9px;
            font-weight: bold;
            color: #155724;
            margin-bottom: 1px;
        }
        .amount-words-text {
            font-size: 10px;
            font-weight: bold;
            color: #333;
            font-style: italic;
        }

        /* ── Notes & Terms ── */
        .notes-section { margin-bottom: 6px; }
        .notes-title {
            font-size: 10px;
            font-weight: bold;
            color: #e67e22;
            margin-bottom: 3px;
        }
        .notes-content {
            background: #fff3cd;
            padding: 5px 8px;
            border-radius: 3px;
            border-left: 3px solid #ffc107;
            font-size: 10px;
        }
        .terms-section {
            font-size: 9px;
            color: #666;
            border-top: 1px solid #e9ecef;
            padding-top: 5px;
            margin-bottom: 6px;
        }

        /* ── Bank Details ── */
        .bank-box {
            background: #fef5ec;
            padding: 6px 10px;
            border-radius: 4px;
            border-left: 3px solid #e67e22;
            margin-bottom: 6px;
        }
        .bank-title {
            font-size: 10px;
            font-weight: bold;
            color: #e67e22;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .bank-line {
            font-size: 10px;
            line-height: 1.4;
        }

        /* ── Signatures ── */
        .signatures {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 5px 10px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 25px;
            padding-top: 4px;
            font-size: 10px;
            font-weight: bold;
            color: #666;
        }

        /* ── Footer ── */
        .footer {
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #e9ecef;
            padding-top: 5px;
        }

        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                @if($logoPath)
                    <img src="{{ $logoPath }}" alt="" class="logo">
                @endif
                <div class="company-name">{{ $tenant->name }}</div>
                <div class="company-details">
                    @if($tenant->address){{ $tenant->address }}<br>@endif
                    @if($tenant->phone)Tel: {{ $tenant->phone }}@endif
                    @if($tenant->email) | Email: {{ $tenant->email }}@endif
                    @if($tenant->website)<br>{{ $tenant->website }}@endif
                    @if($tenant->tax_identification_number)<br>TIN: {{ $tenant->tax_identification_number }}@endif
                </div>
            </div>
            <div class="header-right">
                <div class="doc-title">LOCAL PURCHASE ORDER</div>
                <div class="doc-number"># {{ $purchaseOrder->lpo_number }}</div>
                <div class="doc-meta">
                    <strong>Date:</strong> {{ $purchaseOrder->lpo_date->format('d M, Y') }}<br>
                    @if($purchaseOrder->expected_delivery_date)
                        <strong>Delivery By:</strong> {{ $purchaseOrder->expected_delivery_date->format('d M, Y') }}<br>
                    @endif
                    <strong>Status:</strong> {{ ucfirst($purchaseOrder->status) }}
                </div>
            </div>
        </div>

        <!-- Vendor / Order Info -->
        <div class="billing-info">
            <div class="vendor-to">
                <div class="section-title">Vendor</div>
                <div class="vendor-name">{{ $vendorName }}</div>
                @if($vendor && $vendor->getFullAddressAttribute())
                    <div class="detail-line">{{ $vendor->getFullAddressAttribute() }}</div>
                @endif
                @if($vendor && $vendor->phone)
                    <div class="detail-line">Phone: {{ $vendor->phone }}</div>
                @endif
                @if($vendor && $vendor->email)
                    <div class="detail-line">Email: {{ $vendor->email }}</div>
                @endif
            </div>
            <div class="doc-info">
                <div class="section-title">Order Details</div>
                <div class="detail-line"><strong>LPO No:</strong> {{ $purchaseOrder->lpo_number }}</div>
                <div class="detail-line"><strong>Order Date:</strong> {{ $purchaseOrder->lpo_date->format('d M, Y') }}</div>
                @if($purchaseOrder->expected_delivery_date)
                    <div class="detail-line"><strong>Delivery Date:</strong> {{ $purchaseOrder->expected_delivery_date->format('d M, Y') }}</div>
                @endif
                @if($purchaseOrder->creator)
                    <div class="detail-line"><strong>Prepared By:</strong> {{ $purchaseOrder->creator->name }}</div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="no-break">
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="sn-col">S/N</th>
                        <th style="width: 35%;">Description</th>
                        <th class="text-center" style="width: 10%;">Qty</th>
                        <th class="text-right" style="width: 14%;">Unit Price</th>
                        <th class="text-right" style="width: 11%;">Discount</th>
                        <th class="text-right" style="width: 8%;">Tax</th>
                        <th class="text-right" style="width: 17%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $index => $item)
                        <tr>
                            <td class="sn-col">{{ $index + 1 }}</td>
                            <td>
                                <div class="product-name">{{ $item->product->name ?? $item->description }}</div>
                                @if($item->description && $item->description !== ($item->product->name ?? ''))
                                    <div class="product-desc">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td class="text-center">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }} {{ $item->unit }}</td>
                            <td class="text-right">&#8358;{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right">&#8358;{{ number_format($item->discount, 2) }}</td>
                            <td class="text-right">{{ rtrim(rtrim(number_format($item->tax_rate, 2), '0'), '.') }}%</td>
                            <td class="text-right">&#8358;{{ number_format($item->getTotal(), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="summary-section no-break">
            <table class="summary-table">
                <tr>
                    <td class="summary-label">Subtotal:</td>
                    <td class="summary-amount">&#8358;{{ number_format($purchaseOrder->subtotal, 2) }}</td>
                </tr>
                @if($purchaseOrder->discount_amount > 0)
                <tr>
                    <td class="summary-label">Discount:</td>
                    <td class="summary-amount" style="color: #dc3545;">-&#8358;{{ number_format($purchaseOrder->discount_amount, 2) }}</td>
                </tr>
                @endif
                @if($purchaseOrder->tax_amount > 0)
                <tr>
                    <td class="summary-label">Tax:</td>
                    <td class="summary-amount">&#8358;{{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>TOTAL:</td>
                    <td style="text-align: right;">&#8358;{{ number_format($purchaseOrder->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words no-break">
            <div class="amount-words-label">Amount in Words:</div>
            <div class="amount-words-text">{{ ucfirst(numberToWordsPdf($purchaseOrder->total_amount)) }} Naira Only</div>
        </div>

        <!-- Notes -->
        @if($purchaseOrder->notes)
        <div class="notes-section">
            <div class="notes-title">Notes:</div>
            <div class="notes-content">{{ $purchaseOrder->notes }}</div>
        </div>
        @endif

        <!-- Bank Details & Terms -->
        <div style="display: table; width: 100%; margin-bottom: 8px;">
            @if($invoiceBank)
            <div style="display: table-cell; width: 50%; vertical-align: top; padding-right: 10px;">
                <div class="bank-box">
                    <div class="bank-title">Bank Details</div>
                    <div class="bank-line">
                        <strong>Bank:</strong> {{ $invoiceBank->bank_name }}<br>
                        <strong>Account Name:</strong> {{ $invoiceBank->account_name }}<br>
                        <strong>Account No:</strong> {{ $invoiceBank->account_number }}
                        @if($invoiceBank->sort_code)<br><strong>Sort Code:</strong> {{ $invoiceBank->sort_code }}@endif
                        @if($invoiceBank->swift_code)<br><strong>Swift Code:</strong> {{ $invoiceBank->swift_code }}@endif
                        @if($invoiceBank->branch_name)<br><strong>Branch:</strong> {{ $invoiceBank->branch_name }}@endif
                    </div>
                </div>
            </div>
            @endif
            @if($purchaseOrder->terms_conditions)
            <div style="display: table-cell; vertical-align: top;">
                <div class="terms-section" style="border-top: none; padding-top: 0;">
                    <strong style="color: #e67e22;">Terms &amp; Conditions:</strong>
                    <p style="margin-top: 3px; line-height: 1.5;">{{ $purchaseOrder->terms_conditions }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                @if($signaturePath)
                    <img src="{{ $signaturePath }}" alt="Authorized Signature" style="max-width: 150px; max-height: 60px; margin-bottom: 5px;">
                @endif
                <div class="signature-line">Authorized By ({{ $tenant->name }})</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Received By (Vendor)</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ $tenant->name }}</strong></p>
            <p>Generated on {{ now()->format('d M, Y') }} | This is a computer-generated document.</p>
        </div>
    </div>
</body>
</html>
