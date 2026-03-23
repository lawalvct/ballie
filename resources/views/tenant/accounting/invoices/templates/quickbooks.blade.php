{{-- QuickBooks Generic Invoice Template - Simple clean dark-header style --}}
@php include resource_path('views/tenant/accounting/invoices/templates/partials/data.blade.php'); @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $term->label('sales_invoice') }} {{ $invoiceNumber }} - {{ $tenant->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 15px;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
        }
        .invoice-container {
            max-width: 100%;
            margin: 0 auto;
        }
        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        .logo {
            max-height: 50px;
            margin-bottom: 8px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #393939;
            margin-bottom: 4px;
        }
        .company-info {
            font-size: 10px;
            color: #777;
            line-height: 1.5;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #2CA01C;
            margin-bottom: 10px;
        }
        .meta-table {
            margin-left: auto;
        }
        .meta-table td {
            font-size: 11px;
            padding: 2px 0;
        }
        .meta-table .label {
            color: #777;
            text-align: right;
            padding-right: 10px;
        }
        .meta-table .value {
            color: #333;
            font-weight: bold;
        }
        /* Billing */
        .billing-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e8e8e8;
        }
        .bill-to {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .ship-to {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .section-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #999;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .customer-name {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .customer-detail {
            font-size: 11px;
            color: #666;
        }
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .items-table th {
            background: #393939;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: normal;
        }
        .items-table th.text-right { text-align: right; }
        .items-table th.text-center { text-align: center; }
        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        .items-table tbody tr:nth-child(even) td {
            background: #fafafa;
        }
        /* Summary */
        .summary-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-left {
            display: table-cell;
            width: 55%;
            vertical-align: top;
        }
        .summary-right {
            display: table-cell;
            width: 45%;
            vertical-align: top;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px 10px;
            font-size: 11px;
        }
        .totals-table .label-cell {
            text-align: right;
            color: #777;
            padding-right: 15px;
        }
        .totals-table .value-cell {
            text-align: right;
            font-weight: bold;
        }
        .totals-table .total-row td {
            background: #2CA01C;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            padding: 10px;
        }
        /* Amount Words */
        .amount-words {
            background: #f8f8f8;
            border: 1px solid #e8e8e8;
            padding: 10px 12px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .amount-words strong {
            color: #2CA01C;
        }
        /* Message / Notes */
        .message-section {
            margin-bottom: 15px;
        }
        .message-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 4px;
        }
        .message-text {
            font-size: 11px;
            color: #555;
            padding: 8px;
            background: #fafafa;
            border-left: 3px solid #2CA01C;
        }
        /* Terms */
        .terms-section {
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
            margin-bottom: 25px;
        }
        .terms-section ul {
            margin: 4px 0 0;
            padding-left: 15px;
        }
        .terms-section li { margin-bottom: 2px; }
        /* Signatures */
        .signatures {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .sign-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }
        .sign-line {
            border-top: 1px solid #999;
            margin-top: 45px;
            padding-top: 6px;
            font-size: 11px;
            color: #777;
        }
        /* Footer */
        .footer {
            text-align: center;
            font-size: 10px;
            color: #ccc;
            padding-top: 10px;
            border-top: 2px solid #393939;
        }
        .footer .brand {
            color: #2CA01C;
            font-weight: bold;
        }
        @page { margin: 18mm; size: A4; }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                @if($tenant->logo)
                    <img src="{{ storage_path('app/public/' . $tenant->logo) }}" alt="" class="logo">
                @endif
                <div class="company-name">{{ $tenant->name }}</div>
                <div class="company-info">
                    @if($tenant->address){{ $tenant->address }}<br>@endif
                    @if($tenant->phone){{ $tenant->phone }}@endif
                    @if($tenant->email) | {{ $tenant->email }}@endif
                    @if($tenant->tax_number)<br>Tax ID: {{ $tenant->tax_number }}@endif
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">Invoice</div>
                <table class="meta-table">
                    <tr>
                        <td class="label">Invoice No.</td>
                        <td class="value">{{ $invoiceNumber }}</td>
                    </tr>
                    <tr>
                        <td class="label">Date</td>
                        <td class="value">{{ $invoice->voucher_date->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Due Date</td>
                        <td class="value">{{ $invoice->voucher_date->addDays((int)($customer->payment_terms ?? 30))->format('M d, Y') }}</td>
                    </tr>
                    @if($invoice->reference_number)
                    <tr>
                        <td class="label">Reference</td>
                        <td class="value">{{ $invoice->reference_number }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Billing -->
        <div class="billing-section">
            <div class="bill-to">
                <div class="section-label">Bill To</div>
                @if($customer)
                    <div class="customer-name">{{ $displayName }}</div>
                    <div class="customer-detail">
                        @if($customer->address ?? ($customer->address_line1 ?? null)){{ $customer->address ?? $customer->address_line1 }}<br>@endif
                        @if(($customer->city ?? null) || ($customer->state ?? null)){{ $customer->city ?? '' }} {{ $customer->state ?? '' }}<br>@endif
                        @if($customer->phone ?? null){{ $customer->phone }}@endif
                    </div>
                @else
                    <div class="customer-name">Walk-in {{ $term->label('customer') }}</div>
                @endif
            </div>
            <div class="ship-to">
                <div class="section-label">Invoice Details</div>
                <div class="customer-detail">
                    <strong>Terms:</strong> {{ $customer->payment_terms ?? 'Net 30' }}<br>
                    @if($invoice->createdBy)
                        <strong>Prepared by:</strong> {{ $invoice->createdBy->name ?? 'System' }}<br>
                    @endif
                    <strong>Status:</strong> {{ ucfirst($invoice->status) }}
                </div>
            </div>
        </div>

        <!-- Items Table -->
        @if($lineItems->count() > 0)
        <div class="no-break">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;" class="text-center">#</th>
                        <th style="width: 30%;">Product / Service</th>
                        <th style="width: 30%;">Description</th>
                        <th style="width: 10%;" class="text-center">Qty</th>
                        <th style="width: 12%;" class="text-right">Rate</th>
                        <th style="width: 13%;" class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lineItems as $index => $item)
                        @php
                            $itemAmount = (float) ($item->amount ?? ((float)($item->quantity ?? 0) * (float)($item->rate ?? $item->unit_price ?? 0)));
                            $itemRate = (float) ($item->rate ?? $item->unit_price ?? 0);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td style="font-weight: bold;">{{ $item->product_name ?? $item->description ?? 'Item' }}</td>
                            <td style="color: #777;">{{ ($item->description ?? null) !== ($item->product_name ?? null) ? ($item->description ?? '-') : '-' }}</td>
                            <td class="text-center">{{ number_format((float)($item->quantity ?? 0), 2) }}</td>
                            <td class="text-right">{{ $currencySymbol }}{{ number_format($itemRate, 2) }}</td>
                            <td class="text-right" style="font-weight: bold;">{{ $currencySymbol }}{{ number_format($itemAmount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Summary -->
        <div class="summary-section no-break">
            <div class="summary-left">
                @if($invoice->narration)
                    <div class="message-section">
                        <div class="message-label">Message on Invoice</div>
                        <div class="message-text">{{ $invoice->narration }}</div>
                    </div>
                @endif
            </div>
            <div class="summary-right">
                <table class="totals-table">
                    <tr>
                        <td class="label-cell">Subtotal</td>
                        <td class="value-cell">{{ $currencySymbol }}{{ number_format($subtotal, 2) }}</td>
                    </tr>
                    @foreach($additionalCharges as $charge)
                        @php $chargeAmt = $charge->credit_amount > 0 ? $charge->credit_amount : $charge->debit_amount; @endphp
                        <tr>
                            <td class="label-cell">{{ $charge->ledgerAccount->name }}</td>
                            <td class="value-cell">{{ $currencySymbol }}{{ number_format($chargeAmt, 2) }}</td>
                        </tr>
                    @endforeach
                    @foreach($vatEntries as $vatEntry)
                        @php $vatAmt = $vatEntry->credit_amount > 0 ? $vatEntry->credit_amount : $vatEntry->debit_amount; @endphp
                        <tr>
                            <td class="label-cell">{{ $vatEntry->ledgerAccount->name }}</td>
                            <td class="value-cell">{{ $currencySymbol }}{{ number_format($vatAmt, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td style="text-align: right; padding-right: 15px;">Balance Due</td>
                        <td style="text-align: right;">{{ $currencySymbol }}{{ number_format($totalAmount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <strong>Total (in words):</strong> {{ ucfirst(numberToWords((int)$totalAmount)) }} {{ $tenant->settings['currency'] ?? 'Naira' }} Only
        </div>

        <!-- Terms -->
        <div class="terms-section">
            <strong>Terms and Conditions</strong>
            <ul>
                <li>Payment is due within {{ $customer->payment_terms ?? '30 days' }}</li>
                <li>Please include the invoice number on your payment</li>
                <li>Make checks payable to {{ $tenant->name }}</li>
                <li>Goods sold are not returnable unless defective</li>
            </ul>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sign-col">
                <div class="sign-line">{{ $term->label('customer') }} Signature</div>
            </div>
            <div class="sign-col">
                <div class="sign-line">Authorized Signature</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <span class="brand">{{ $tenant->name }}</span> | Generated on {{ now()->format('M d, Y \a\t g:i A') }} | Thank you for your business!
        </div>
    </div>
</body>
</html>
