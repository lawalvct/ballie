@php
// Number to words function for amount in words - must be defined before use
if (!function_exists('numberToWords')) {
    function numberToWords($number) {
        $ones = array(
            '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
            'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
            'seventeen', 'eighteen', 'nineteen'
        );

        $tens = array(
            '', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'
        );

        $scales = array('', 'thousand', 'million', 'billion', 'trillion');

        if ($number == 0) return 'zero';

        $number = number_format($number, 2, '.', '');
        list($integer, $fraction) = explode('.', $number);

        $words = '';

        if ($integer > 0) {
            $words .= convertIntegerToWords($integer, $ones, $tens, $scales);
        }

        if ($fraction > 0) {
            $words .= ' and ' . convertIntegerToWords($fraction, $ones, $tens, $scales) . ' kobo';
        }

        return $words;
    }
}

if (!function_exists('convertIntegerToWords')) {
    function convertIntegerToWords($integer, $ones, $tens, $scales) {
        $words = '';
        $scaleIndex = 0;

        while ($integer > 0) {
            $chunk = $integer % 1000;
            if ($chunk > 0) {
                $chunkWords = convertChunkToWords($chunk, $ones, $tens);
                if ($scaleIndex > 0) {
                    $chunkWords .= ' ' . $scales[$scaleIndex];
                }
                $words = $chunkWords . ' ' . $words;
            }
            $integer = intval($integer / 1000);
            $scaleIndex++;
        }

        return trim($words);
    }
}

if (!function_exists('convertChunkToWords')) {
    function convertChunkToWords($chunk, $ones, $tens) {
        $words = '';

        $hundreds = intval($chunk / 100);
        $remainder = $chunk % 100;

        if ($hundreds > 0) {
            $words .= $ones[$hundreds] . ' hundred';
            if ($remainder > 0) {
                $words .= ' ';
            }
        }

        if ($remainder >= 20) {
            $tensDigit = intval($remainder / 10);
            $onesDigit = $remainder % 10;
            $words .= $tens[$tensDigit];
            if ($onesDigit > 0) {
                $words .= '-' . $ones[$onesDigit];
            }
        } elseif ($remainder > 0) {
            $words .= $ones[$remainder];
        }

        return $words;
    }
}
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $term->label('sales_invoice') }} {{ $invoice->voucherType->abbreviation }}-{{ $invoice->voucher_number }} - {{ $tenant->name }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/ballie_logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/ballie_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/ballie_logo.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.3;
            color: #333;
            background-color: #fff;
        }

        .invoice-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 10px;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        /* Header Section */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px solid #2c5aa0;
        }

        .company-info {
            flex: 1;
        }

        .company-logo {
            max-width: 80px;
            max-height: 50px;
            margin-bottom: 5px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .company-details {
            font-size: 12px;
            color: #666;
            line-height: 1.3;
        }

        .invoice-meta {
            text-align: right;
            flex: 0 0 250px;
        }

        .invoice-title {
            font-size: 22px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .invoice-number {
            font-size: 15px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 3px;
        }

        .invoice-date {
            font-size: 12px;
            color: #666;
        }

        /* Bill To Section */
        .billing-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .bill-to, .ship-to {
            flex: 1;
            margin-right: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #2c5aa0;
        }

        .bill-to:last-child, .ship-to:last-child {
            margin-right: 0;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .customer-name {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
        }

        .customer-details {
            font-size: 12px;
            color: #666;
            line-height: 1.3;
        }

        /* Items Table */
        .items-section {
            margin-bottom: 10px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .items-table thead {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3d72 100%);
            color: white;
        }

        .items-table th {
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        .items-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .sn-column {
            font-weight: bold;
            background: #f8f9fa;
            color: #2c5aa0;
        }

        /* Summary Section */
        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }

        .summary-table {
            min-width: 300px;
            border-collapse: collapse;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .summary-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #eee;
        }

        .summary-table .label {
            font-weight: 600;
            color: #555;
            background: #f8f9fa;
        }

        .summary-table .amount {
            text-align: right;
            font-weight: bold;
            color: #333;
        }

        .summary-table .total-row {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3d72 100%);
            color: white;
            font-size: 13px;
            font-weight: bold;
        }

        .summary-table .total-row td {
            border-bottom: none;
        }

        /* Amount in Words */
        .amount-words {
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 4px;
            border-left: 4px solid #27ae60;
            margin-bottom: 10px;
        }

        .amount-words-title {
            font-size: 11px;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .amount-words-text {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            font-style: italic;
        }

        /* Notes Section */
        .notes-section {
            margin-bottom: 10px;
        }

        .notes-title {
            font-size: 13px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .notes-content {
            background: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            border-left: 4px solid #f39c12;
            font-size: 12px;
            color: #555;
        }

        /* Footer */
        .invoice-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #eee;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .signature-box {
            text-align: center;
            min-width: 200px;
        }

        .signature-line {
            border-top: 2px solid #333;
            margin-top: 30px;
            padding-top: 5px;
            font-weight: bold;
            color: #555;
        }

        .footer-info {
            text-align: center;
            font-size: 10px;
            color: #999;
            padding-top: 8px;
            border-top: 1px solid #eee;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-posted {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-draft {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        /* Print Styles */
        @media print {
            [contenteditable] {
                outline: none !important;
                border: none !important;
                box-shadow: none !important;
            }

            body {
                margin: 0;
                background: white;
            }

            .invoice-container {
                box-shadow: none;
                max-width: none;
                margin: 0;
                padding: 10px;
            }

            .no-print {
                display: none !important;
            }

            .invoice-header {
                border-bottom: 3px solid #2c5aa0;
            }

            /* Ensure proper page breaks */
            .items-table {
                page-break-inside: avoid;
            }

            .summary-section {
                page-break-inside: avoid;
            }
        }

        /* Print Button */
        .print-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .btn {
            padding: 12px 24px;
            margin-left: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3d72 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 90, 160, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Print Buttons -->
    <div class="print-buttons no-print">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Print {{ $term->label('sales_invoice') }}
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            ✕ Close
        </button>
    </div>

    <div class="invoice-container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="company-info">
                @if($tenant->logo)
                    <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }}" class="company-logo">
                @endif
                <div class="company-name" contenteditable="true">{{ $tenant->name }}</div>
                <div class="company-details" contenteditable="true">
                    @if($tenant->address)
                        📍 {{ $tenant->address }}<br>
                    @endif
                    @if($tenant->phone)
                        📞 {{ $tenant->phone }}<br>
                    @endif
                    @if($tenant->email)
                        ✉️ {{ $tenant->email }}<br>
                    @endif
                    @if($tenant->website)
                        🌐 {{ $tenant->website }}<br>
                    @endif
                    @if($tenant->tax_number)
                        🆔 Tax ID: {{ $tenant->tax_number }}
                    @endif
                </div>
            </div>

            <div class="invoice-meta">
                <div class="invoice-title" contenteditable="true">{{ $term->label('sales_invoice') }}</div>
                <div class="invoice-number" contenteditable="true"># {{ $invoice->voucherType->prefix }}{{ str_pad($invoice->voucher_number, 4, '0', STR_PAD_LEFT) }}</div>
                <div class="invoice-date" contenteditable="true">
                    <strong>Date:</strong> {{ $invoice->voucher_date->format('M d, Y') }}<br>
                    @if($invoice->reference_number)
                        <strong>Ref:</strong> {{ $invoice->reference_number }}<br>
                    @endif
                    <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                </div>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="billing-section">
            <div class="bill-to">
                <div class="section-title">📋 Bill To</div>
                @if($customer)
                    <div class="customer-name" contenteditable="true">
                        @if($customer->customer_type === 'business' || !empty($customer->company_name))
                            {{ $customer->company_name ?? $customer->name }}
                        @else
                            {{ $customer->first_name ?? '' }} {{ $customer->last_name ?? '' }}
                        @endif
                    </div>
                    <div class="customer-details" contenteditable="true">
                        @if($customer->address || ($customer->address_line1 ?? false))
                            📍 {{ $customer->address ?? $customer->address_line1 }}<br>
                            @if($customer->address_line2 ?? false)
                                &nbsp;&nbsp;&nbsp;&nbsp;{{ $customer->address_line2 }}<br>
                            @endif
                            @if(($customer->city ?? false) || ($customer->state ?? false) || ($customer->postal_code ?? false))
                                &nbsp;&nbsp;&nbsp;&nbsp;{{ $customer->city ?? '' }} {{ $customer->state ?? '' }} {{ $customer->postal_code ?? '' }}<br>
                            @endif
                            @if($customer->country ?? false)
                                &nbsp;&nbsp;&nbsp;&nbsp;{{ $customer->country }}<br>
                            @endif
                        @endif
                        @if($customer->phone)
                            📞 {{ $customer->phone }}<br>
                        @endif
                        @if($customer->mobile ?? false)
                            📱 {{ $customer->mobile }}<br>
                        @endif
                        @if($customer->email)
                            ✉️ {{ $customer->email }}<br>
                        @endif
                        @if($customer->tax_id ?? false)
                            🆔 Tax ID: {{ $customer->tax_id }}
                        @endif
                    </div>
                @else
                    <div class="customer-name" contenteditable="true">Walk-in {{ $term->label('customer') }}</div>
                    <div class="customer-details" contenteditable="true">Cash {{ $term->label('sales') }} / Counter {{ $term->label('sales') }}</div>
                @endif
            </div>

            <div class="ship-to">
                <div class="section-title">📦 {{ $term->label('sales_invoice') }} Details</div>
                <div class="customer-details" contenteditable="true">
                    <strong>Payment Terms:</strong> {{ $customer->payment_terms ?? 'Cash on Delivery' }}<br>
                    @if($invoice->created_by)
                        <strong>Prepared By:</strong> {{ $invoice->createdBy->name ?? 'System' }}<br>
                    @endif
                    @if($invoice->posted_at)
                        <strong>Posted:</strong> {{ $invoice->posted_at->format('M d, Y g:i A') }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        @php
            // Support both inventory items (from meta_data) and invoice items (from database relationship)
            $items = [];
            if (isset($inventoryItems) && count($inventoryItems) > 0) {
                $items = $inventoryItems;
            } elseif ($invoice->items && $invoice->items->count() > 0) {
                $items = $invoice->items;
            }
        @endphp
        @if(count($items) > 0)
        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;" class="text-center">S/N</th>
                        <th style="width: 35%;">{{ $term->label('line_item') }}</th>
                        <th style="width: 25%;">Description</th>
                        <th style="width: 8%;" class="text-center">Qty</th>
                        <th style="width: 12%;" class="text-right">Unit Price</th>
                        <th style="width: 15%;" class="text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotal = 0; $unitTotalsMap = []; @endphp
                    @foreach($items as $index => $item)
                        @php
                            // Support both array and object formats
                            $productName = is_array($item) ? $item['product_name'] : $item->product_name;
                            $description = is_array($item) ? ($item['description'] ?? '') : ($item->description ?? '');
                            $quantity = is_array($item) ? $item['quantity'] : $item->quantity;
                            $rate = is_array($item) ? $item['rate'] : $item->rate;
                            $amount = is_array($item) ? $item['amount'] : $item->amount;
                            $sku = is_array($item) ? ($item['sku'] ?? '') : ($item->product->sku ?? '');
                            $unit = is_array($item)
                                ? ($item['unit'] ?? '')
                                : (trim((string)($item->unit ?? '')) !== ''
                                    ? $item->unit
                                    : (optional(optional($item->product ?? null)->primaryUnit)->symbol ?? ''));
                            $itemType = is_array($item) ? ($item['item_type'] ?? 'product') : ($item->item_type ?? 'product');

                            $subtotal += $amount;

                            $unitKey = trim((string) $unit);
                            $qtyVal = (float) $quantity;
                            if (strtolower((string) $itemType) !== 'service' && $unitKey !== '' && $qtyVal > 0) {
                                $unitTotalsMap[$unitKey] = ($unitTotalsMap[$unitKey] ?? 0) + $qtyVal;
                            }
                        @endphp
                        <tr>
                            <td class="text-center sn-column">{{ $index + 1 }}</td>
                            <td>
                                <div style="font-weight: bold; color: #333; margin-bottom: 2px;" contenteditable="true">
                                    {{ $productName }}
                                </div>
                                @if($sku)
                                    <div style="font-size: 10px; color: #999;">
                                        SKU: {{ $sku }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="color: #666; font-size: 12px;" contenteditable="true">
                                    {{ $description ?: 'N/A' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span style="font-weight: bold; color: #2c5aa0;" contenteditable="true">
                                    {{ number_format($quantity, 2) }}
                                </span>
                                @if($unit)
                                    <div style="font-size: 10px; color: #999;">{{ $unit }}</div>
                                @endif
                            </td>
                            <td class="text-right">
                                <span style="font-weight: bold;" contenteditable="true">₦{{ number_format($rate, 2) }}</span>
                            </td>
                            <td class="text-right">
                                <span style="font-weight: bold; color: #27ae60;" contenteditable="true">₦{{ number_format($amount, 2) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @php
            $unitTotalsList = [];
            foreach ($unitTotalsMap as $__u => $__q) {
                $__fmt = (floor($__q) == $__q) ? number_format($__q, 0) : rtrim(rtrim(number_format($__q, 3, '.', ''), '0'), '.');
                $unitTotalsList[] = ['unit' => $__u, 'label' => $__fmt . ' ' . $__u];
            }
        @endphp
        @if(count($unitTotalsList) > 0)
        <div style="display:flex; flex-wrap:wrap; gap:8px; justify-content:flex-end; margin:6px 0 10px;">
            @foreach($unitTotalsList as $ut)
                <div style="background:#eef4ff; border:1px solid #c9d9f2; color:#1e3d72; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:600;">
                    Total <span style="text-transform:lowercase;">{{ $ut['unit'] }}</span>: {{ $ut['label'] }}
                </div>
            @endforeach
        </div>
        @endif
        @endif

        <!-- Summary Section -->
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td class="label" contenteditable="true">Subtotal:</td>
                    <td class="amount" contenteditable="true">₦{{ number_format($subtotal ?? 0, 2) }}</td>
                </tr>

                @php
                    // Get all voucher entries for this invoice
                    $voucherEntries = $invoice->entries;

                    // Filter VAT entries (checking account name contains 'vat' OR specific VAT account codes)
                    $vatEntries = $voucherEntries->filter(function($entry) {
                        $accountName = strtolower($entry->ledgerAccount->name ?? '');
                        $accountCode = $entry->ledgerAccount->code ?? '';
                        return str_contains($accountName, 'vat') ||
                               in_array($accountCode, ['VAT-OUT-001', 'VAT-IN-001']);
                    });

                    // Get product accounts from items to exclude from additional charges
                    $productAccountIds = collect($items)->map(function($item) use ($invoice) {
                        $productId = is_array($item) ? ($item['product_id'] ?? null) : ($item->product_id ?? null);
                        if ($productId) {
                            $product = \App\Models\Product::find($productId);
                            if ($product) {
                                // Check if it's a sales or purchase invoice to get the right account
                                if ($invoice->voucherType && str_contains(strtolower($invoice->voucherType->name ?? ''), 'purchase')) {
                                    return $product->purchase_account_id;
                                } else {
                                    return $product->sales_account_id;
                                }
                            }
                        }
                        return null;
                    })->filter()->toArray();

                    // Filter additional charges (exclude customer/vendor accounts, VAT accounts, product accounts, COGS and Inventory)
                    $additionalCharges = $voucherEntries->filter(function($entry) use ($vatEntries, $invoice, $productAccountIds) {
                        // Skip if it's a VAT entry
                        if ($vatEntries->contains('id', $entry->id)) {
                            return false;
                        }

                        $account = $entry->ledgerAccount;
                        if (!$account) return false;

                        // Skip customer/vendor accounts (Receivables/Payables)
                        if ($account->accountGroup && in_array($account->accountGroup->code, ['AR', 'AP'])) {
                            return false;
                        }

                        // Skip product-specific accounts
                        if (in_array($account->id, $productAccountIds)) {
                            return false;
                        }

                        // Skip COGS and Inventory accounts (internal accounting entries that shouldn't show on customer invoice)
                        $accountName = strtolower($account->name ?? '');
                        $accountCode = strtoupper($account->code ?? '');
                        if (in_array($accountName, ['cost of goods sold', 'inventory', 'stock']) ||
                            in_array($accountCode, ['COGS', 'INV', 'STOCK'])) {
                            return false;
                        }

                        return true;
                    });

                    $totalAmount = $subtotal;
                @endphp

                @if($additionalCharges->count() > 0)
                    @foreach($additionalCharges as $charge)
                        @php
                            $chargeAmount = $charge->credit_amount > 0 ? $charge->credit_amount : $charge->debit_amount;
                        @endphp
                        <tr>
                            <td class="label" contenteditable="true">
                                {{ $charge->ledgerAccount->name }}:
                                @if($charge->narration && $charge->narration !== $charge->ledgerAccount->name)
                                    <div style="font-size: 11px; color: #999; font-weight: normal;">
                                        {{ $charge->narration }}
                                    </div>
                                @endif
                            </td>
                            <td class="amount" contenteditable="true">₦{{ number_format($chargeAmount, 2) }}</td>
                        </tr>
                        @php $totalAmount += $chargeAmount; @endphp
                    @endforeach
                @endif

                @if($vatEntries->count() > 0)
                    @foreach($vatEntries as $vatEntry)
                        @php
                            $vatAmount = $vatEntry->credit_amount > 0 ? $vatEntry->credit_amount : $vatEntry->debit_amount;
                        @endphp
                        <tr>
                            <td class="label" contenteditable="true">
                                {{ $vatEntry->ledgerAccount->name }}:
                                @if($vatEntry->narration && $vatEntry->narration !== $vatEntry->ledgerAccount->name)
                                    <div style="font-size: 11px; color: #999; font-weight: normal;">
                                        {{ $vatEntry->narration }}
                                    </div>
                                @endif
                            </td>
                            <td class="amount" contenteditable="true">₦{{ number_format($vatAmount, 2) }}</td>
                        </tr>
                        @php $totalAmount += $vatAmount; @endphp
                    @endforeach
                @endif

                <tr class="total-row" style="border-top: 2px solid #2c5aa0;">
                    <td contenteditable="true">TOTAL AMOUNT:</td>
                    <td contenteditable="true">₦{{ number_format($totalAmount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <div class="amount-words-title">💰 Amount in Words:</div>
            <div class="amount-words-text" contenteditable="true">
                {{ ucfirst(trim(numberToWords($totalAmount))) }} Naira Only
            </div>
        </div>

        <!-- Notes/Narration -->
        @if($invoice->narration)
        <div class="notes-section">
            <div class="notes-title">📝 Additional Notes</div>
            <div class="notes-content" contenteditable="true">
                {{ $invoice->narration }}
            </div>
        </div>
        @endif

        <!-- Bank Details & Terms -->
        <div style="display: flex; gap: 10px; margin-bottom: 10px;">
            @if(isset($invoiceBank) && $invoiceBank)
            <div style="flex: 1;">
                <div class="notes-section">
                    <div class="notes-title">🏦 Bank Details</div>
                    <div class="notes-content">
                        <table style="width: 100%; font-size: 12px;">
                            <tr><td style="padding: 2px 0; font-weight: 600; width: 110px;">Bank:</td><td contenteditable="true">{{ $invoiceBank->bank_name }}</td></tr>
                            <tr><td style="padding: 2px 0; font-weight: 600;">Account Name:</td><td contenteditable="true">{{ $invoiceBank->account_name }}</td></tr>
                            <tr><td style="padding: 2px 0; font-weight: 600;">Account No:</td><td contenteditable="true">{{ $invoiceBank->account_number }}</td></tr>
                            @if($invoiceBank->sort_code)<tr><td style="padding: 2px 0; font-weight: 600;">Sort Code:</td><td contenteditable="true">{{ $invoiceBank->sort_code }}</td></tr>@endif
                            @if($invoiceBank->swift_code)<tr><td style="padding: 2px 0; font-weight: 600;">Swift Code:</td><td contenteditable="true">{{ $invoiceBank->swift_code }}</td></tr>@endif
                            @if($invoiceBank->branch_name)<tr><td style="padding: 2px 0; font-weight: 600;">Branch:</td><td contenteditable="true">{{ $invoiceBank->branch_name }}</td></tr>@endif
                        </table>
                    </div>
                </div>
            </div>
            @endif
            <div style="flex: 1;">
                <div class="notes-section">
                    <div class="notes-title">📋 Terms & Conditions</div>
                    <div class="notes-content">
                        <ul style="margin: 0; padding-left: 15px; line-height: 1.4;" contenteditable="true">
                            @if(!empty($invoiceTerms))
                                @foreach(explode("\n", $invoiceTerms) as $termLine)
                                    @if(trim($termLine))
                                    <li>{{ trim($termLine) }}</li>
                                    @endif
                                @endforeach
                            @else
                                <li>Payment is due within {{ $customer->payment_terms ?? '30 days' }} of invoice date</li>
                                <li>Late payments may be subject to service charges</li>
                                <li>All disputes must be reported within 7 days of invoice date</li>
                                <li>Goods sold are not returnable unless defective</li>
                                <li>This invoice is computer generated and does not require signature</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line" contenteditable="true">{{ $term->label('customer') }} Signature</div>
            </div>
            <div class="signature-box">
                @if($tenant->signature)
                    <img src="{{ asset('storage/' . $tenant->signature) }}" alt="Authorized Signature" style="max-width: 180px; max-height: 60px; margin-bottom: 5px;">
                @endif
                <div class="signature-line" contenteditable="true">Authorized Signature</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-info" contenteditable="true">
            <p><strong>{{ $tenant->name }}</strong> | Generated on {{ now()->format('l, M d, Y \a\t g:i A') }}</p>
            <p>Powered by <a href="https://ballie.co" target="_blank" style="color:inherit; text-decoration:none; font-weight:600;">Ballie</a> - Business Management Software | Thank you for your business!</p>
        </div>
    </div>

    <!-- JavaScript for Print Functions -->
    <script>
        // Auto-focus print dialog (optional)
        window.onload = function() {
            // Uncomment the line below to auto-print when page loads
            // window.print();
        }

        // Handle post-print actions
        window.onafterprint = function() {
            // Uncomment to auto-close after printing
            // window.close();
        }

        // Enhanced print function with options
        function printInvoice() {
            // Hide print buttons
            document.querySelector('.print-buttons').style.display = 'none';

            // Print the document
            window.print();

            // Show print buttons again after print dialog closes
            setTimeout(() => {
                document.querySelector('.print-buttons').style.display = 'block';
            }, 1000);
        }
    </script>
</body>
</html>
