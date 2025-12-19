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
    <title>Invoice {{ $invoice->voucherType->abbreviation }}-{{ $invoice->voucher_number }} - {{ $tenant->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #fff;
        }

        .invoice-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        /* Header Section */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2c5aa0;
        }

        .company-info {
            flex: 1;
        }

        .company-logo {
            max-width: 120px;
            max-height: 80px;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .company-details {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }

        .invoice-meta {
            text-align: right;
            flex: 0 0 300px;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .invoice-number {
            font-size: 20px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }

        .invoice-date {
            font-size: 14px;
            color: #666;
        }

        /* Bill To Section */
        .billing-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .bill-to, .ship-to {
            flex: 1;
            margin-right: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #2c5aa0;
        }

        .bill-to:last-child, .ship-to:last-child {
            margin-right: 0;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .customer-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        .customer-details {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }

        /* Items Table */
        .items-section {
            margin-bottom: 30px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .items-table thead {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3d72 100%);
            color: white;
        }

        .items-table th {
            padding: 15px 12px;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 12px;
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
            margin-bottom: 30px;
        }

        .summary-table {
            min-width: 350px;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .summary-table td {
            padding: 12px 15px;
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
            font-size: 16px;
            font-weight: bold;
        }

        .summary-table .total-row td {
            border-bottom: none;
        }

        /* Amount in Words */
        .amount-words {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #27ae60;
            margin-bottom: 30px;
        }

        .amount-words-title {
            font-size: 14px;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .amount-words-text {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            font-style: italic;
        }

        /* Notes Section */
        .notes-section {
            margin-bottom: 30px;
        }

        .notes-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .notes-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #f39c12;
            font-size: 14px;
            color: #555;
        }

        /* Footer */
        .invoice-footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #eee;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .signature-box {
            text-align: center;
            min-width: 200px;
        }

        .signature-line {
            border-top: 2px solid #333;
            margin-top: 60px;
            padding-top: 10px;
            font-weight: bold;
            color: #555;
        }

        .footer-info {
            text-align: center;
            font-size: 12px;
            color: #999;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
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
            body {
                margin: 0;
                background: white;
            }

            .invoice-container {
                box-shadow: none;
                max-width: none;
                margin: 0;
                padding: 15px;
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
            🖨️ Print Invoice
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
                <div class="company-name">{{ $tenant->name }}</div>
                <div class="company-details">
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
                <div class="invoice-title">Invoice</div>
                <div class="invoice-number"># {{ $invoice->voucherType->prefix }}{{ str_pad($invoice->voucher_number, 4, '0', STR_PAD_LEFT) }}</div>
                <div class="invoice-date">
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
                    <div class="customer-name">
                        @if($customer->customer_type === 'business' || !empty($customer->company_name))
                            {{ $customer->company_name ?? $customer->name }}
                        @else
                            {{ $customer->first_name ?? '' }} {{ $customer->last_name ?? '' }}
                        @endif
                    </div>
                    <div class="customer-details">
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
                    <div class="customer-name">Walk-in Customer</div>
                    <div class="customer-details">Cash Sale / Counter Sale</div>
                @endif
            </div>

            <div class="ship-to">
                <div class="section-title">📦 Invoice Details</div>
                <div class="customer-details">
                    <strong>Payment Terms:</strong> {{ $customer->payment_terms ?? 'Cash on Delivery' }}<br>
                    <strong>Currency:</strong> {{ $customer->currency ?? 'NGN' }}<br>
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
        @if(count($inventoryItems) > 0)
        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;" class="text-center">S/N</th>
                        <th style="width: 35%;">Product/Service</th>
                        <th style="width: 25%;">Description</th>
                        <th style="width: 8%;" class="text-center">Qty</th>
                        <th style="width: 12%;" class="text-right">Unit Price</th>
                        <th style="width: 15%;" class="text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotal = 0; @endphp
                    @foreach($inventoryItems as $index => $item)
                        @php $subtotal += $item['amount']; @endphp
                        <tr>
                            <td class="text-center sn-column">{{ $index + 1 }}</td>
                            <td>
                                <div style="font-weight: bold; color: #333; margin-bottom: 4px;">
                                    {{ $item['product_name'] }}
                                </div>
                                @if(isset($item['sku']) && $item['sku'])
                                    <div style="font-size: 12px; color: #999;">
                                        SKU: {{ $item['sku'] }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="color: #666; font-size: 14px;">
                                    {{ $item['description'] ?: 'N/A' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span style="font-weight: bold; color: #2c5aa0;">
                                    {{ number_format($item['quantity'], 2) }}
                                </span>
                                @if(isset($item['unit']) && $item['unit'])
                                    <div style="font-size: 12px; color: #999;">{{ $item['unit'] }}</div>
                                @endif
                            </td>
                            <td class="text-right">
                                <span style="font-weight: bold;">₦{{ number_format($item['rate'], 2) }}</span>
                            </td>
                            <td class="text-right">
                                <span style="font-weight: bold; color: #27ae60;">₦{{ number_format($item['amount'], 2) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Summary Section -->
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="amount">₦{{ number_format($subtotal ?? $inventoryItems->sum('amount'), 2) }}</td>
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

                    // Get product accounts from inventory items to exclude from additional charges
                    $productAccountIds = collect($inventoryItems)->map(function($item) use ($invoice) {
                        if (isset($item['product_id'])) {
                            $product = \App\Models\Product::find($item['product_id']);
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

                    $totalAmount = ($subtotal ?? $inventoryItems->sum('amount'));
                @endphp

                @if($additionalCharges->count() > 0)
                    @foreach($additionalCharges as $charge)
                        @php
                            $chargeAmount = $charge->credit_amount > 0 ? $charge->credit_amount : $charge->debit_amount;
                        @endphp
                        <tr>
                            <td class="label">
                                {{ $charge->ledgerAccount->name }}:
                                @if($charge->narration && $charge->narration !== $charge->ledgerAccount->name)
                                    <div style="font-size: 11px; color: #999; font-weight: normal;">
                                        {{ $charge->narration }}
                                    </div>
                                @endif
                            </td>
                            <td class="amount">₦{{ number_format($chargeAmount, 2) }}</td>
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
                            <td class="label">
                                {{ $vatEntry->ledgerAccount->name }}:
                                @if($vatEntry->narration && $vatEntry->narration !== $vatEntry->ledgerAccount->name)
                                    <div style="font-size: 11px; color: #999; font-weight: normal;">
                                        {{ $vatEntry->narration }}
                                    </div>
                                @endif
                            </td>
                            <td class="amount">₦{{ number_format($vatAmount, 2) }}</td>
                        </tr>
                        @php $totalAmount += $vatAmount; @endphp
                    @endforeach
                @endif

                <tr class="total-row" style="border-top: 2px solid #2c5aa0;">
                    <td>TOTAL AMOUNT:</td>
                    <td>₦{{ number_format($totalAmount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <div class="amount-words-title">💰 Amount in Words:</div>
            <div class="amount-words-text">
                {{ ucfirst(trim(numberToWords($totalAmount))) }} Naira Only
            </div>
        </div>

        <!-- Notes/Narration -->
        @if($invoice->narration)
        <div class="notes-section">
            <div class="notes-title">📝 Additional Notes</div>
            <div class="notes-content">
                {{ $invoice->narration }}
            </div>
        </div>
        @endif

        <!-- Terms and Conditions -->
        <div class="notes-section">
            <div class="notes-title">📋 Terms & Conditions</div>
            <div class="notes-content">
                <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                    <li>Payment is due within {{ $customer->payment_terms ?? '30 days' }} of invoice date</li>
                    <li>Late payments may be subject to service charges</li>
                    <li>All disputes must be reported within 7 days of invoice date</li>
                    <li>Goods sold are not returnable unless defective</li>
                    <li>This invoice is computer generated and does not require signature</li>
                </ul>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">Customer Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Authorized Signature</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-info">
            <p><strong>{{ $tenant->name }}</strong> | Generated on {{ now()->format('l, M d, Y \a\t g:i A') }}</p>
            <p>Powered by Ballie Business Management System | Thank you for your business!</p>
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
