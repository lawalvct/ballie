{{-- Sage Invoice Template - Professional green-themed with sidebar accent --}}
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
            color: #2d3436;
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
            margin-bottom: 5px;
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
            max-height: 55px;
            margin-bottom: 8px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #00733B;
            margin-bottom: 3px;
        }
        .company-details {
            font-size: 11px;
            color: #777;
            line-height: 1.4;
        }
        .invoice-badge {
            display: inline-block;
            background: #00733B;
            color: #fff;
            font-size: 20px;
            font-weight: bold;
            padding: 8px 20px;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }
        .meta-line {
            font-size: 11px;
            color: #555;
            margin-bottom: 2px;
        }
        .meta-line strong {
            color: #333;
        }
        /* Green divider */
        .divider {
            height: 3px;
            background: #00733B;
            margin: 15px 0;
        }
        .divider-thin {
            height: 1px;
            background: #ddd;
            margin: 15px 0;
        }
        /* Billing */
        .billing-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .billing-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .billing-label {
            font-size: 11px;
            font-weight: bold;
            color: #00733B;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 2px solid #00733B;
            display: inline-block;
        }
        .billing-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .billing-detail {
            font-size: 11px;
            color: #666;
            line-height: 1.5;
        }
        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background: #f4f9f6;
            color: #00733B;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #00733B;
            border-top: 2px solid #00733B;
        }
        .items-table th.text-right { text-align: right; }
        .items-table th.text-center { text-align: center; }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        .product-name { font-weight: bold; }
        .product-desc { font-size: 10px; color: #999; }
        /* Summary */
        .summary-wrapper {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-notes {
            display: table-cell;
            width: 55%;
            vertical-align: top;
            padding-right: 20px;
        }
        .summary-box {
            display: table-cell;
            width: 45%;
            vertical-align: top;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 6px 10px;
            font-size: 11px;
        }
        .summary-label {
            text-align: right;
            padding-right: 15px;
            color: #777;
        }
        .summary-value {
            text-align: right;
            font-weight: bold;
        }
        .summary-total {
            background: #00733B;
            color: #fff;
        }
        .summary-total td {
            padding: 10px;
            font-size: 14px;
            font-weight: bold;
        }
        /* Amount Words */
        .amount-words {
            background: #f4f9f6;
            border-left: 4px solid #00733B;
            padding: 10px 15px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .amount-words-label {
            font-weight: bold;
            color: #00733B;
            font-size: 10px;
            text-transform: uppercase;
        }
        .amount-words-text {
            font-weight: bold;
            font-style: italic;
        }
        /* Notes */
        .notes-title {
            font-size: 11px;
            font-weight: bold;
            color: #00733B;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .notes-content {
            font-size: 11px;
            color: #666;
            background: #fafafa;
            padding: 8px;
            border-radius: 3px;
        }
        /* Terms */
        .terms {
            font-size: 10px;
            color: #999;
            padding-top: 10px;
            border-top: 1px solid #eee;
            margin-bottom: 25px;
        }
        .terms ul {
            margin: 4px 0 0;
            padding-left: 15px;
        }
        .terms li { margin-bottom: 2px; }
        /* Signatures */
        .signatures {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .sign-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }
        .sign-line {
            border-top: 1px solid #666;
            margin-top: 45px;
            padding-top: 6px;
            font-size: 11px;
            color: #777;
        }
        /* Footer */
        .footer {
            text-align: center;
            font-size: 10px;
            color: #aaa;
            padding-top: 10px;
            border-top: 2px solid #00733B;
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
                <div class="company-details">
                    @if($tenant->address){{ $tenant->address }}<br>@endif
                    @if($tenant->phone)Tel: {{ $tenant->phone }}@endif
                    @if($tenant->email) | {{ $tenant->email }}@endif
                    @if($tenant->website)<br>{{ $tenant->website }}@endif
                    @if($tenant->tax_number)<br>Tax ID: {{ $tenant->tax_number }}@endif
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-badge">INVOICE</div>
                <div class="meta-line"><strong>Invoice #:</strong> {{ $invoiceNumber }}</div>
                <div class="meta-line"><strong>Date:</strong> {{ $invoice->voucher_date->format('d/m/Y') }}</div>
                <div class="meta-line"><strong>Due:</strong> {{ $invoice->voucher_date->addDays((int)($customer->payment_terms ?? 30))->format('d/m/Y') }}</div>
                @if($invoice->reference_number)
                    <div class="meta-line"><strong>Ref:</strong> {{ $invoice->reference_number }}</div>
                @endif
            </div>
        </div>

        <div class="divider"></div>

        <!-- Billing -->
        <div class="billing-row">
            <div class="billing-col" style="padding-right: 20px;">
                <div class="billing-label">Invoice To</div>
                @if($customer)
                    <div class="billing-name">{{ $displayName }}</div>
                    <div class="billing-detail">
                        @if($customer->address ?? ($customer->address_line1 ?? null)){{ $customer->address ?? $customer->address_line1 }}<br>@endif
                        @if(($customer->city ?? null) || ($customer->state ?? null)){{ $customer->city ?? '' }} {{ $customer->state ?? '' }}<br>@endif
                        @if($customer->phone ?? null){{ $customer->phone }}<br>@endif
                        @if($customer->email ?? null){{ $customer->email }}@endif
                    </div>
                @else
                    <div class="billing-name">Walk-in {{ $term->label('customer') }}</div>
                @endif
            </div>
            <div class="billing-col">
                <div class="billing-label">Payment Details</div>
                <div class="billing-detail">
                    <strong>Terms:</strong> {{ $customer->payment_terms ?? 'Net 30' }}<br>
                    <strong>Currency:</strong> {{ $tenant->settings['currency'] ?? 'NGN' }}<br>
                    @if($invoice->createdBy)
                        <strong>Prepared:</strong> {{ $invoice->createdBy->name ?? 'System' }}<br>
                    @endif
                    <strong>Status:</strong> {{ ucfirst($invoice->status) }}
                </div>
            </div>
        </div>

        <!-- Items -->
        @if($lineItems->count() > 0)
        <div class="no-break">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;" class="text-center">#</th>
                        <th style="width: 35%;">Description</th>
                        <th style="width: 12%;" class="text-center">Quantity</th>
                        <th style="width: 18%;" class="text-right">Unit Price</th>
                        <th style="width: 15%;" class="text-right">Discount</th>
                        <th style="width: 15%;" class="text-right">Amount</th>
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
                            <td>
                                <span class="product-name">{{ $item->product_name ?? $item->description ?? 'Item' }}</span>
                                @if(($item->description ?? null) && ($item->description ?? null) !== ($item->product_name ?? null))
                                    <br><span class="product-desc">{{ $item->description }}</span>
                                @endif
                            </td>
                            <td class="text-center">{{ number_format((float)($item->quantity ?? 0), 2) }}</td>
                            <td class="text-right">{{ $currencySymbol }}{{ number_format($itemRate, 2) }}</td>
                            <td class="text-right">-</td>
                            <td class="text-right" style="font-weight: bold;">{{ $currencySymbol }}{{ number_format($itemAmount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Summary + Notes -->
        <div class="summary-wrapper no-break">
            <div class="summary-notes">
                @if($invoice->narration)
                    <div class="notes-title">Notes</div>
                    <div class="notes-content">{{ $invoice->narration }}</div>
                @endif
            </div>
            <div class="summary-box">
                <table class="summary-table">
                    <tr>
                        <td class="summary-label">Subtotal:</td>
                        <td class="summary-value">{{ $currencySymbol }}{{ number_format($subtotal, 2) }}</td>
                    </tr>
                    @foreach($additionalCharges as $charge)
                        @php $chargeAmt = $charge->credit_amount > 0 ? $charge->credit_amount : $charge->debit_amount; @endphp
                        <tr>
                            <td class="summary-label">{{ $charge->ledgerAccount->name }}:</td>
                            <td class="summary-value">{{ $currencySymbol }}{{ number_format($chargeAmt, 2) }}</td>
                        </tr>
                    @endforeach
                    @foreach($vatEntries as $vatEntry)
                        @php $vatAmt = $vatEntry->credit_amount > 0 ? $vatEntry->credit_amount : $vatEntry->debit_amount; @endphp
                        <tr>
                            <td class="summary-label">{{ $vatEntry->ledgerAccount->name }}:</td>
                            <td class="summary-value">{{ $currencySymbol }}{{ number_format($vatAmt, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="summary-total">
                        <td style="text-align: right; padding-right: 15px;">Total Due:</td>
                        <td style="text-align: right;">{{ $currencySymbol }}{{ number_format($totalAmount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <div class="amount-words-label">Amount in Words</div>
            <div class="amount-words-text">{{ ucfirst(numberToWords((int)$totalAmount)) }} {{ $tenant->settings['currency'] ?? 'Naira' }} Only</div>
        </div>

        <!-- Bank Details & Terms -->
        <div style="display: table; width: 100%; margin-bottom: 15px;">
            @if(isset($invoiceBank) && $invoiceBank)
            <div style="display: table-cell; width: 50%; vertical-align: top; padding-right: 15px;">
                <div style="background: #f0faf0; padding: 12px 15px; border-radius: 6px; border-left: 4px solid #2e7d32;">
                    <div style="font-size: 13px; font-weight: bold; color: #2e7d32; margin-bottom: 8px; text-transform: uppercase;">Bank Details</div>
                    <div style="font-size: 12px; line-height: 1.6;">
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
            <div style="display: table-cell; vertical-align: top;">
                <div class="terms">
                    <strong>Terms & Conditions</strong>
                    <ul>
                        @if(!empty($invoiceTerms))
                            @foreach(explode("\n", $invoiceTerms) as $termLine)
                                @if(trim($termLine))
                                <li>{{ trim($termLine) }}</li>
                                @endif
                            @endforeach
                        @else
                            <li>Payment due within {{ $customer->payment_terms ?? '30 days' }}</li>
                            <li>Late payments subject to service charges</li>
                            <li>Disputes must be reported within 7 days</li>
                            <li>Goods sold are not returnable unless defective</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sign-box">
                <div class="sign-line">{{ $term->label('customer') }} Signature</div>
            </div>
            <div class="sign-box">
                @if($tenant->signature)
                    <img src="{{ storage_path('app/public/' . $tenant->signature) }}" alt="Authorized Signature" style="max-width: 150px; max-height: 60px; margin-bottom: 5px;">
                @endif
                <div class="sign-line">For {{ $tenant->name }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            {{ $tenant->name }} | Generated: {{ now()->format('d M Y, g:i A') }} | Thank you for your business!
        </div>
    </div>
</body>
</html>
