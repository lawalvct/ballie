{{-- Zoho Invoice Template - Clean, modern with accent color strip --}}
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
            padding: 10px;
            color: #333;
            font-size: 11px;
            line-height: 1.3;
        }
        .invoice-container {
            max-width: 100%;
            margin: 0 auto;
        }
        /* Top accent bar */
        .accent-bar {
            height: 4px;
            background: #0077B5;
            margin-bottom: 10px;
        }
        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
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
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #0077B5;
            margin-bottom: 2px;
        }
        .company-details {
            font-size: 10px;
            color: #777;
            line-height: 1.3;
        }
        .invoice-label {
            font-size: 20px;
            font-weight: bold;
            color: #0077B5;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }
        .invoice-meta {
            font-size: 10px;
            color: #555;
        }
        .invoice-meta strong {
            display: inline-block;
            width: 80px;
            color: #777;
        }
        /* Bill To / Ship To */
        .billing-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .billing-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .billing-title {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0077B5;
            font-weight: bold;
            margin-bottom: 4px;
            padding-bottom: 2px;
            border-bottom: 1px solid #e0e0e0;
        }
        .billing-name {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-bottom: 2px;
        }
        .billing-detail {
            font-size: 10px;
            color: #666;
        }
        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .items-table th {
            background: #0077B5;
            color: #fff;
            padding: 5px 6px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .items-table th.text-right { text-align: right; }
        .items-table th.text-center { text-align: center; }
        .items-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
        }
        .items-table tbody tr:nth-child(even) {
            background: #f9fbfd;
        }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        .product-name { font-weight: bold; color: #333; }
        .product-desc { font-size: 9px; color: #999; }
        /* Summary */
        .summary-wrapper {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .summary-spacer {
            display: table-cell;
            width: 55%;
        }
        .summary-box {
            display: table-cell;
            width: 45%;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 4px 8px;
            font-size: 10px;
        }
        .summary-table .label {
            color: #777;
            text-align: right;
            padding-right: 10px;
        }
        .summary-table .value {
            text-align: right;
            font-weight: bold;
        }
        .summary-table .total-row {
            background: #0077B5;
            color: #fff;
        }
        .summary-table .total-row td {
            padding: 6px;
            font-size: 12px;
            font-weight: bold;
        }
        /* Amount in words */
        .amount-words {
            background: #f0f7ff;
            border-left: 3px solid #0077B5;
            padding: 5px 10px;
            margin-bottom: 8px;
            font-size: 10px;
        }
        .amount-words-label {
            color: #0077B5;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .amount-words-text {
            font-weight: bold;
            font-style: italic;
            color: #333;
        }
        /* Notes */
        .notes {
            margin-bottom: 6px;
        }
        .notes-title {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0077B5;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .notes-content {
            font-size: 10px;
            color: #666;
            padding: 5px;
            background: #fafafa;
            border-radius: 3px;
        }
        /* Terms */
        .terms {
            font-size: 9px;
            color: #999;
            margin-bottom: 8px;
            padding-top: 5px;
            border-top: 1px solid #eee;
        }
        .terms ul {
            margin: 3px 0 0;
            padding-left: 12px;
        }
        .terms li { margin-bottom: 1px; }
        /* Signatures */
        .signatures {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .sign-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 5px;
        }
        .sign-line {
            border-top: 1px solid #333;
            margin-top: 25px;
            padding-top: 4px;
            font-size: 10px;
            color: #777;
        }
        /* Footer */
        .footer {
            text-align: center;
            font-size: 9px;
            color: #aaa;
            padding-top: 5px;
            border-top: 1px solid #eee;
        }
        @page { margin: 15mm; size: A4; }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="accent-bar"></div>

        <!-- Header -->
        <div class="header">
            <div class="header-left">
                @if($tenant->logo)
                    <img src="{{ storage_path('app/public/' . $tenant->logo) }}" alt="" style="max-height: 40px; margin-bottom: 4px;">
                @endif
                <div class="company-name">{{ $tenant->name }}</div>
                <div class="company-details">
                    @if($tenant->address){{ $tenant->address }}<br>@endif
                    @if($tenant->phone){{ $tenant->phone }}@endif
                    @if($tenant->email) | {{ $tenant->email }}@endif
                    @if($tenant->website)<br>{{ $tenant->website }}@endif
                    @if($tenant->tax_number)<br>Tax ID: {{ $tenant->tax_number }}@endif
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-label">INVOICE</div>
                <div class="invoice-meta">
                    <strong># </strong>{{ $invoiceNumber }}<br>
                    <strong>Date:</strong> {{ $invoice->voucher_date->format('d M, Y') }}<br>
                    <strong>Due Date:</strong> {{ $invoice->voucher_date->addDays((int)($customer->payment_terms ?? 30))->format('d M, Y') }}<br>
                    @if($invoice->reference_number)
                        <strong>Reference:</strong> {{ $invoice->reference_number }}<br>
                    @endif
                    <strong>Status:</strong> {{ ucfirst($invoice->status) }}
                </div>
            </div>
        </div>

        <!-- Billing -->
        <div class="billing-row">
            <div class="billing-col" style="padding-right: 20px;">
                <div class="billing-title">Bill To</div>
                @if($customer)
                    <div class="billing-name">{{ $displayName }}</div>
                    <div class="billing-detail">
                        @if($customer->address ?? ($customer->address_line1 ?? null)){{ $customer->address ?? $customer->address_line1 }}<br>@endif
                        @if(($customer->city ?? null) || ($customer->state ?? null)){{ $customer->city ?? '' }} {{ $customer->state ?? '' }} {{ $customer->postal_code ?? '' }}<br>@endif
                        @if($customer->phone ?? null){{ $customer->phone }}<br>@endif
                        @if($customer->email ?? null){{ $customer->email }}@endif
                    </div>
                @else
                    <div class="billing-name">Walk-in {{ $term->label('customer') }}</div>
                @endif
            </div>
            <div class="billing-col">
                <div class="billing-title">{{ $term->label('sales_invoice') }} Details</div>
                <div class="billing-detail">
                    <strong>Payment Terms:</strong> {{ $customer->payment_terms ?? '30 Days' }}<br>
                    @if($invoice->createdBy)
                        <strong>Prepared By:</strong> {{ $invoice->createdBy->name ?? 'System' }}
                    @endif
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
                        <th style="width: 35%;">Item & Description</th>
                        <th style="width: 12%;" class="text-center">Qty</th>
                        <th style="width: 18%;" class="text-right">Rate</th>
                        <th style="width: 15%;" class="text-right">Tax</th>
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
                            <td class="text-center">
                                @php $__rowUnit = trim((string)($item->unit ?? '')) ?: trim((string)(optional(optional($item->product ?? null)->primaryUnit)->symbol ?? '')); @endphp
                                {{ number_format((float)($item->quantity ?? 0), 2) }}@if($__rowUnit !== '') <span style="color:#666; font-size:9px;">{{ $__rowUnit }}</span>@endif
                            </td>
                            <td class="text-right">{{ $currencySymbol }}{{ number_format($itemRate, 2) }}</td>
                            <td class="text-right">-</td>
                            <td class="text-right" style="font-weight: bold;">{{ $currencySymbol }}{{ number_format($itemAmount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($unitTotals->count() > 0)
            <div class="no-break" style="text-align:right; margin:4px 0 8px;">
                @foreach($unitTotals as $ut)
                    <span style="display:inline-block; background:#eef4ff; border:1px solid #c9d9f2; color:#1e3d72; padding:2px 8px; border-radius:10px; font-size:9px; font-weight:bold; margin-left:4px;">
                        Total {{ $ut['unit'] }}: {{ $ut['label'] }}
                    </span>
                @endforeach
            </div>
        @endif

        <!-- Summary -->
        <div class="summary-wrapper no-break">
            <div class="summary-spacer"></div>
            <div class="summary-box">
                <table class="summary-table">
                    <tr>
                        <td class="label">Sub Total:</td>
                        <td class="value">{{ $currencySymbol }}{{ number_format($subtotal, 2) }}</td>
                    </tr>
                    @foreach($additionalCharges as $charge)
                        @php $chargeAmt = $charge->credit_amount > 0 ? $charge->credit_amount : $charge->debit_amount; @endphp
                        <tr>
                            <td class="label">{{ $charge->ledgerAccount->name }}:</td>
                            <td class="value">{{ $currencySymbol }}{{ number_format($chargeAmt, 2) }}</td>
                        </tr>
                    @endforeach
                    @foreach($vatEntries as $vatEntry)
                        @php $vatAmt = $vatEntry->credit_amount > 0 ? $vatEntry->credit_amount : $vatEntry->debit_amount; @endphp
                        <tr>
                            <td class="label">{{ $vatEntry->ledgerAccount->name }}:</td>
                            <td class="value">{{ $currencySymbol }}{{ number_format($vatAmt, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td style="text-align: right; padding-right: 15px;">Total:</td>
                        <td style="text-align: right;">{{ $currencySymbol }}{{ number_format($totalAmount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <div class="amount-words-label">Total In Words</div>
            <div class="amount-words-text">{{ ucfirst(numberToWords((int)$totalAmount)) }} {{ $tenant->settings['currency'] ?? 'Naira' }} Only</div>
        </div>

        <!-- Notes -->
        @if($invoice->narration)
        <div class="notes">
            <div class="notes-title">Notes</div>
            <div class="notes-content">{{ $invoice->narration }}</div>
        </div>
        @endif

        <!-- Bank Details & Terms -->
        <div style="display: table; width: 100%; margin-bottom: 8px;">
            @if(isset($invoiceBank) && $invoiceBank)
            <div style="display: table-cell; width: 50%; vertical-align: top; padding-right: 10px;">
                <div style="background: #f0f7ff; padding: 6px 10px; border-radius: 4px; border-left: 3px solid #0078d4;">
                    <div style="font-size: 10px; font-weight: bold; color: #0078d4; margin-bottom: 4px; text-transform: uppercase;">Bank Details</div>
                    <div style="font-size: 10px; line-height: 1.4;">
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
                            <li>Disputes reported within 7 days</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        @include('tenant.accounting.invoices.partials.pdf-payment-links', ['linkColor' => '#0078d4'])

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
        @include('tenant.accounting.invoices.templates.partials.powered-by')
    </div>
</body>
</html>
