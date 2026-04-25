{{-- Tally Invoice Template - Classic bordered table layout --}}
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
            padding: 8px;
            color: #000;
            font-size: 11px;
            line-height: 1.2;
        }
        .invoice-container {
            max-width: 100%;
            margin: 0 auto;
            border: 2px solid #000;
        }
        /* Title */
        .invoice-title-row {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            padding: 5px;
            border-bottom: 2px solid #000;
            text-decoration: underline;
        }
        /* Top section: company + meta in table */
        .top-section {
            width: 100%;
            border-collapse: collapse;
        }
        .top-section td {
            vertical-align: top;
            padding: 4px 8px;
            border-bottom: 1px solid #000;
        }
        .top-left {
            width: 55%;
            border-right: 1px solid #000;
        }
        .top-right {
            width: 45%;
        }
        .company-name {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 1px;
        }
        .company-detail {
            font-size: 10px;
            color: #333;
            line-height: 1.3;
        }
        /* Meta info rows */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 2px 3px;
            font-size: 10px;
            border: none;
        }
        .meta-label {
            width: 45%;
            color: #555;
        }
        .meta-value {
            font-weight: bold;
        }
        /* Buyer section */
        .buyer-section {
            width: 100%;
            border-collapse: collapse;
        }
        .buyer-section td {
            vertical-align: top;
            padding: 4px 8px;
            border-bottom: 1px solid #000;
        }
        .buyer-left {
            width: 55%;
            border-right: 1px solid #000;
        }
        .buyer-right {
            width: 45%;
        }
        .buyer-label {
            font-size: 10px;
            color: #555;
            margin-bottom: 1px;
        }
        .buyer-name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 1px;
        }
        .buyer-detail {
            font-size: 10px;
            color: #333;
            line-height: 1.3;
        }
        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            background: #f5f5f5;
            border-bottom: 2px solid #000;
            border-right: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
        }
        .items-table th:last-child {
            border-right: none;
        }
        .items-table td {
            border-bottom: 1px solid #ccc;
            border-right: 1px solid #ccc;
            padding: 3px 3px;
            font-size: 10px;
            vertical-align: top;
        }
        .items-table td:last-child {
            border-right: none;
        }
        .col-sn { width: 5%; text-align: center; }
        .col-desc { width: 40%; }
        .col-qty { width: 15%; text-align: right; padding-right: 8px !important; }
        .col-rate { width: 15%; text-align: right; padding-right: 8px !important; }
        .col-per { width: 8%; text-align: center; }
        .col-amount { width: 17%; text-align: right; padding-right: 8px !important; font-weight: bold; }
        .item-sub {
            font-size: 9px;
            color: #666;
            font-style: italic;
        }
        /* Charges rows */
        .charge-row td {
            border-bottom: 1px solid #ccc;
            font-size: 10px;
        }
        /* Total row */
        .total-row {
            border-top: 2px solid #000;
        }
        .total-row td {
            padding: 5px 3px;
            font-weight: bold;
            font-size: 12px;
            border-bottom: none;
            border-right: 1px solid #000;
        }
        .total-row td:last-child {
            border-right: none;
        }
        /* Amount in words */
        .amount-words {
            padding: 5px 8px;
            border-top: 2px solid #000;
            font-size: 10px;
        }
        .amount-words-label {
            font-weight: bold;
            color: #555;
        }
        .amount-words-text {
            font-weight: bold;
            font-style: italic;
        }
        /* Declaration / footer */
        .footer-section {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #000;
        }
        .footer-section td {
            padding: 6px;
            vertical-align: top;
        }
        .footer-left {
            width: 55%;
            border-right: 1px solid #000;
            font-size: 10px;
            line-height: 1.3;
        }
        .footer-right {
            width: 45%;
            text-align: right;
            font-size: 10px;
        }
        .auth-signatory {
            margin-top: 25px;
            font-weight: bold;
            font-style: italic;
        }
        /* Bank details */
        .bank-details {
            font-size: 9px;
            padding: 3px 8px;
            border-top: 1px solid #ccc;
            color: #555;
        }
        .computer-generated {
            text-align: center;
            font-size: 9px;
            color: #999;
            padding: 3px;
            border-top: 1px solid #000;
        }
        @page {
            margin: 12mm;
            size: A4;
        }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Title -->
        <div class="invoice-title-row">{{ strtoupper($term->label('sales_invoice')) }}</div>

        <!-- Company Info + Invoice Meta -->
        <table class="top-section">
            <tr>
                <td class="top-left">
                    <div class="company-name">{{ $tenant->name }}</div>
                    <div class="company-detail">
                        @if($tenant->address){{ $tenant->address }}<br>@endif
                        @if($tenant->email)E-mail: {{ $tenant->email }}<br>@endif
                        @if($tenant->phone)Phone: {{ $tenant->phone }}@endif
                    </div>
                </td>
                <td class="top-right">
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">{{ $term->label('sales_invoice') }} No.</td>
                            <td class="meta-value">{{ $invoiceNumber }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Dated</td>
                            <td class="meta-value">{{ $invoice->voucher_date->format('d-M-Y') }}</td>
                        </tr>
                        @if($invoice->reference_number)
                        <tr>
                            <td class="meta-label">Delivery Note</td>
                            <td class="meta-value">{{ $invoice->reference_number }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="meta-label">Mode/Terms of Payment</td>
                            <td class="meta-value">{{ $customer->payment_terms ?? 'on delivery' }}</td>
                        </tr>
                        @if($tenant->tax_number)
                        <tr>
                            <td class="meta-label">Supplier's Ref.</td>
                            <td class="meta-value">{{ $tenant->tax_number }}</td>
                        </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>

        <!-- Buyer Section -->
        <table class="buyer-section">
            <tr>
                <td class="buyer-left">
                    <div class="buyer-label">Buyer</div>
                    <div class="buyer-name">{{ $displayName }}</div>
                    <div class="buyer-detail">
                        @if($customer)
                            @if($customer->address ?? ($customer->address_line1 ?? null)){{ $customer->address ?? $customer->address_line1 }}<br>@endif
                            @if(($customer->city ?? null) || ($customer->state ?? null)){{ $customer->city ?? '' }}{{ ($customer->city ?? null) && ($customer->state ?? null) ? ', ' : '' }}{{ $customer->state ?? '' }}<br>@endif
                            @if($customer->phone ?? null)Phone: {{ $customer->phone }}<br>@endif
                            @if($customer->email ?? null)Email: {{ $customer->email }}@endif
                        @endif
                    </div>
                </td>
                <td class="buyer-right">
                    <table class="meta-table">
                        @if($invoice->reference_number)
                        <tr>
                            <td class="meta-label">Buyer's Order No.</td>
                            <td class="meta-value">{{ $invoice->reference_number }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="meta-label">Dated</td>
                            <td class="meta-value">{{ $invoice->voucher_date->format('d-M-Y') }}</td>
                        </tr>
                        @if(($customer->city ?? null) || ($customer->state ?? null))
                        <tr>
                            <td class="meta-label">Destination</td>
                            <td class="meta-value">{{ $customer->city ?? $customer->state ?? '' }}</td>
                        </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <div class="no-break">
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-sn">Sl<br>No.</th>
                        <th class="col-desc">Description of Goods</th>
                        <th class="col-qty">Quantity</th>
                        <th class="col-rate">Rate</th>
                        <th class="col-per">per</th>
                        <th class="col-amount">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lineItems as $index => $item)
                        @php
                            $itemAmount = (float) ($item->amount ?? ((float)($item->quantity ?? 0) * (float)($item->rate ?? $item->unit_price ?? 0)));
                            $itemRate = (float) ($item->rate ?? $item->unit_price ?? 0);
                            $itemQty = (float) ($item->quantity ?? 0);
                            $unit = $item->unit ?? $item->unit_name ?? '';
                            if (trim((string)$unit) === '') {
                                $unit = trim((string)(optional(optional($item->product ?? null)->primaryUnit)->symbol ?? ''));
                            }
                        @endphp
                        <tr>
                            <td class="col-sn">{{ $index + 1 }}</td>
                            <td class="col-desc">
                                <strong>{{ $item->product_name ?? $item->description ?? 'Item' }}</strong>
                                @if(($item->description ?? null) && ($item->description ?? null) !== ($item->product_name ?? null))
                                    <br><span class="item-sub">{{ $item->description }}</span>
                                @endif
                            </td>
                            <td class="col-qty">{{ number_format($itemQty, 2) }}{{ $unit ? ' ' . $unit : '' }}</td>
                            <td class="col-rate">{{ number_format($itemRate, 2) }}</td>
                            <td class="col-per">{{ $unit ?: 'Nos' }}</td>
                            <td class="col-amount">{{ $currencySymbol }}{{ number_format($itemAmount, 2) }}</td>
                        </tr>
                    @endforeach

                    {{-- Additional charges --}}
                    @if($additionalCharges->count() > 0)
                        @foreach($additionalCharges as $charge)
                            @php
                                $chargeAmount = $charge->credit_amount > 0 ? $charge->credit_amount : $charge->debit_amount;
                            @endphp
                            <tr class="charge-row">
                                <td class="col-sn"></td>
                                <td class="col-desc" colspan="4">
                                    <em>{{ $charge->ledgerAccount->name }}</em>
                                    @if($charge->narration && $charge->narration !== $charge->ledgerAccount->name)
                                        <br><span class="item-sub">{{ $charge->narration }}</span>
                                    @endif
                                </td>
                                <td class="col-amount">{{ $currencySymbol }}{{ number_format($chargeAmount, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif

                    {{-- VAT --}}
                    @if($vatEntries->count() > 0)
                        @foreach($vatEntries as $vatEntry)
                            @php
                                $vatAmount = $vatEntry->credit_amount > 0 ? $vatEntry->credit_amount : $vatEntry->debit_amount;
                            @endphp
                            <tr class="charge-row">
                                <td class="col-sn"></td>
                                <td class="col-desc" colspan="4">
                                    <em>{{ $vatEntry->ledgerAccount->name }}</em>
                                    @if($vatEntry->narration && $vatEntry->narration !== $vatEntry->ledgerAccount->name)
                                        <br><span class="item-sub">{{ $vatEntry->narration }}</span>
                                    @endif
                                </td>
                                <td class="col-amount">{{ $currencySymbol }}{{ number_format($vatAmount, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif

                    {{-- Total row --}}
                    <tr class="total-row">
                        <td class="col-sn"></td>
                        <td class="col-desc" style="text-align: right; padding-right: 10px;" colspan="4"><strong>Total</strong></td>
                        <td class="col-amount">{{ $currencySymbol }}{{ number_format($totalAmount, 2) }}</td>
                    </tr>
                    @foreach($unitTotals as $ut)
                        <tr style="background:#f6f8fb;">
                            <td class="col-sn"></td>
                            <td class="col-desc" style="text-align: right; padding-right: 10px;">Total {{ $ut['unit'] }}</td>
                            <td class="col-qty"><strong>{{ $ut['qty_formatted'] }}</strong></td>
                            <td class="col-rate"></td>
                            <td class="col-per">{{ $ut['unit'] }}</td>
                            <td class="col-amount"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <span class="amount-words-label">Amount Chargeable (in words):</span><br>
            <span class="amount-words-text">{{ ucfirst(numberToWords((int)$totalAmount)) }} {{ $tenant->settings['currency'] ?? 'Naira' }} Only</span>
        </div>

        @if($tenant->tax_number)
        <div class="bank-details">
            VAT Regn. No. : {{ $tenant->tax_number }}
        </div>
        @endif

        <!-- Bank Details -->
        @if(isset($invoiceBank) && $invoiceBank)
        <table style="width: 100%; margin-bottom: 5px; border: 1px solid #000;">
            <tr>
                <td style="padding: 4px 6px; font-weight: bold; background: #f5f5f5; border-bottom: 1px solid #000; font-size: 10px;" colspan="2">Bank Details for Payment</td>
            </tr>
            <tr>
                <td style="padding: 3px 6px; width: 50%; border-right: 1px solid #ccc; font-size: 10px;">
                    <strong>Bank:</strong> {{ $invoiceBank->bank_name }}<br>
                    <strong>Account Name:</strong> {{ $invoiceBank->account_name }}
                </td>
                <td style="padding: 3px 6px; font-size: 10px;">
                    <strong>Account No:</strong> {{ $invoiceBank->account_number }}
                    @if($invoiceBank->branch_name)<br><strong>Branch:</strong> {{ $invoiceBank->branch_name }}@endif
                    @if($invoiceBank->sort_code)<br><strong>Sort Code:</strong> {{ $invoiceBank->sort_code }}@endif
                </td>
            </tr>
        </table>
        @endif

        @include('tenant.accounting.invoices.partials.pdf-payment-links', ['linkColor' => '#333333'])

        <!-- Declaration & Signature -->
        <table class="footer-section">
            <tr>
                <td class="footer-left">
                    <strong>Declaration</strong><br>
                    @if(!empty($invoiceTerms))
                        @foreach(explode("\n", $invoiceTerms) as $termLine)
                            @if(trim($termLine))
                            {{ trim($termLine) }}<br>
                            @endif
                        @endforeach
                    @else
                    We hereby declare that the information on this {{ strtolower($term->label('sales_invoice')) }} is
                    true and correct.<br>
                    Thanks for your regular patronage.
                    @endif

                    @if($invoice->narration)
                        <br><br><strong>Notes:</strong> {{ $invoice->narration }}
                    @endif
                </td>
                <td class="footer-right">
                    <strong>for {{ strtoupper($tenant->name) }}</strong>
                    @if($tenant->signature)
                        <br><img src="{{ storage_path('app/public/' . $tenant->signature) }}" alt="Authorized Signature" style="max-width: 120px; max-height: 50px; margin: 5px 0;">
                    @endif
                    <div class="auth-signatory">Authorised Signatory</div>
                </td>
            </tr>
        </table>

        <!-- Computer Generated -->
        <div class="computer-generated">
            This is a Computer Generated {{ $term->label('sales_invoice') }}
        </div>
        @include('tenant.accounting.invoices.templates.partials.powered-by')
    </div>
</body>
</html>
