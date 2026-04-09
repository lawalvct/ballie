@php
    $docTitle = strtoupper($quotation->document_title ?: $term->label('quotation'));
    $customer = $quotation->customer;
    $customerName = $customer
        ? ($customer->company_name ?: trim($customer->first_name . ' ' . $customer->last_name))
        : 'N/A';

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
    $invoiceTerms = $tenant->settings['invoice_terms'] ?? null;

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
    <title>{{ $docTitle }} {{ $quotation->getQuotationNumber() }} - {{ $tenant->name }}</title>
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
            border-bottom: 2px solid #2c5aa0;
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
            color: #2c5aa0;
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
            color: #2c5aa0;
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
        .bill-to, .doc-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 8px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        .bill-to {
            border-right: none;
            border-radius: 6px 0 0 6px;
        }
        .doc-info {
            border-radius: 0 6px 6px 0;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .customer-name {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
        }
        .detail-line {
            margin-bottom: 1px;
            font-size: 10px;
        }

        /* ── Subject ── */
        .subject-bar {
            background: #f0f4ff;
            padding: 6px 10px;
            border-radius: 4px;
            border-left: 3px solid #2c5aa0;
            margin-bottom: 10px;
        }
        .subject-bar strong {
            color: #2c5aa0;
            font-size: 10px;
            text-transform: uppercase;
        }

        /* ── Items Table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 10px;
        }
        .items-table th {
            background: #2c5aa0;
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
            background: #2c5aa0;
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
            color: #2c5aa0;
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
            background: #f0f4ff;
            padding: 6px 10px;
            border-radius: 4px;
            border-left: 3px solid #2c5aa0;
            margin-bottom: 6px;
        }
        .bank-title {
            font-size: 10px;
            font-weight: bold;
            color: #2c5aa0;
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
                <div class="doc-title">{{ $docTitle }}</div>
                <div class="doc-number"># {{ $quotation->getQuotationNumber() }}</div>
                <div class="doc-meta">
                    <strong>Date:</strong> {{ $quotation->quotation_date->format('d M, Y') }}<br>
                    @if($quotation->expiry_date)
                        <strong>Valid Until:</strong> {{ $quotation->expiry_date->format('d M, Y') }}<br>
                    @endif
                    <strong>Status:</strong> {{ ucfirst($quotation->status) }}
                </div>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="billing-info">
            <div class="bill-to">
                <div class="section-title">{{ $docTitle === 'PROFORMA INVOICE' ? 'Bill To' : 'Prepared For' }}</div>
                <div class="customer-name">{{ $customerName }}</div>
                @if($customer && ($customer->address ?? false))
                    <div class="detail-line">{{ $customer->address }}</div>
                @elseif($customer && ($customer->address_line1 ?? false))
                    <div class="detail-line">{{ $customer->address_line1 }}</div>
                    @if($customer->address_line2 ?? false)
                        <div class="detail-line">{{ $customer->address_line2 }}</div>
                    @endif
                    @if(($customer->city ?? false) || ($customer->state ?? false))
                        <div class="detail-line">{{ $customer->city ?? '' }} {{ $customer->state ?? '' }} {{ $customer->postal_code ?? '' }}</div>
                    @endif
                @endif
                @if($customer && $customer->phone)
                    <div class="detail-line">Phone: {{ $customer->phone }}</div>
                @endif
                @if($customer && $customer->email)
                    <div class="detail-line">Email: {{ $customer->email }}</div>
                @endif
            </div>
            <div class="doc-info">
                <div class="section-title">{{ $docTitle }} Details</div>
                @if($quotation->reference_number)
                    <div class="detail-line"><strong>Reference:</strong> {{ $quotation->reference_number }}</div>
                @endif
                @if($quotation->createdBy)
                    <div class="detail-line"><strong>Prepared By:</strong> {{ $quotation->createdBy->name }}</div>
                @endif
                @if($quotation->expiry_date)
                    <div class="detail-line">
                        <strong>Validity:</strong>
                        @if($quotation->isExpired())
                            <span style="color: #dc3545; font-weight: bold;">Expired</span>
                        @else
                            <span style="color: #28a745; font-weight: bold;">Valid</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Subject -->
        @if($quotation->subject)
            <div class="subject-bar">
                <strong>Subject:</strong> {{ $quotation->subject }}
            </div>
        @endif

        <!-- Items Table -->
        <div class="no-break">
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="sn-col">S/N</th>
                        <th style="width: 8%;">Type</th>
                        <th style="width: 37%;">Description</th>
                        <th class="text-center" style="width: 12%;">Qty</th>
                        <th class="text-right" style="width: 18%;">Unit Price</th>
                        <th class="text-right" style="width: 20%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotation->items as $index => $item)
                        <tr>
                            <td class="sn-col">{{ $index + 1 }}</td>
                            <td style="text-align: center;">
                                @if(($item->item_type ?? 'product') === 'product')
                                    <span style="background: #dbeafe; color: #1e40af; padding: 1px 4px; border-radius: 3px; font-size: 8px;">Product</span>
                                @else
                                    <span style="background: #dcfce7; color: #166534; padding: 1px 4px; border-radius: 3px; font-size: 8px;">Service</span>
                                @endif
                            </td>
                            <td>
                                <div class="product-name">{{ $item->product_name }}</div>
                                @if($item->description)
                                    <div class="product-desc">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td class="text-center">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }} {{ $item->unit }}</td>
                            <td class="text-right">&#8358;{{ number_format($item->rate, 2) }}</td>
                            <td class="text-right">&#8358;{{ number_format($item->quantity * $item->rate, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="summary-section no-break">
            <table class="summary-table">
                <tr>
                    <td class="summary-label">Items Subtotal:</td>
                    <td class="summary-amount">&#8358;{{ number_format($quotation->subtotal, 2) }}</td>
                </tr>
                @if(!empty($quotation->additional_charges) && is_array($quotation->additional_charges))
                    @foreach($quotation->additional_charges as $charge)
                    <tr>
                        <td class="summary-label">{{ $charge['name'] ?? 'Additional Charge' }}:</td>
                        <td class="summary-amount">&#8358;{{ number_format($charge['amount'] ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                @endif
                @if($quotation->vat_enabled && $quotation->vat_amount > 0)
                <tr>
                    <td class="summary-label">VAT (7.5%):</td>
                    <td class="summary-amount">&#8358;{{ number_format($quotation->vat_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>TOTAL:</td>
                    <td style="text-align: right;">&#8358;{{ number_format($quotation->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words no-break">
            <div class="amount-words-label">Amount in Words:</div>
            <div class="amount-words-text">{{ ucfirst(numberToWordsPdf($quotation->total_amount)) }} Naira Only</div>
        </div>

        <!-- Notes -->
        @if($quotation->notes)
        <div class="notes-section">
            <div class="notes-title">Notes:</div>
            <div class="notes-content">{{ $quotation->notes }}</div>
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
            @if($quotation->terms_and_conditions || $invoiceTerms)
            <div style="display: table-cell; vertical-align: top;">
                <div class="terms-section" style="border-top: none; padding-top: 0;">
                    <strong style="color: #2c5aa0;">Terms &amp; Conditions:</strong>
                    <p style="margin-top: 3px; line-height: 1.5;">{{ $quotation->terms_and_conditions ?: $invoiceTerms }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">{{ $docTitle === 'PROFORMA INVOICE' ? 'Client' : 'Customer' }} Signature</div>
            </div>
            <div class="signature-box">
                @if($signaturePath)
                    <img src="{{ $signaturePath }}" alt="Authorized Signature" style="max-width: 150px; max-height: 60px; margin-bottom: 5px;">
                @endif
                <div class="signature-line">For {{ $tenant->name }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ $tenant->name }}</strong></p>
            <p>Generated on {{ now()->format('d M, Y') }} | Thank you for your business!</p>
        </div>
    </div>
</body>
</html>
