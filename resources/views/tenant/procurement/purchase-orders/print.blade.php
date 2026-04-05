@php
    $vendor = $purchaseOrder->vendor;
    $vendorName = $vendor ? $vendor->getFullNameAttribute() : 'N/A';

    if (!function_exists('numberToWords')) {
        function numberToWords($number) {
            $ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
            $tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
            $scales = ['', 'thousand', 'million', 'billion'];
            if ($number == 0) return 'Zero Naira Only';
            $number = number_format($number, 2, '.', '');
            list($integer, $fraction) = explode('.', $number);
            $words = '';
            if ($integer > 0) $words .= convertIntegerToWords($integer, $ones, $tens, $scales);
            if ($fraction > 0) $words .= ' and ' . convertIntegerToWords($fraction, $ones, $tens, $scales) . ' kobo';
            return ucfirst(trim($words)) . ' Naira Only';
        }
    }
    if (!function_exists('convertIntegerToWords')) {
        function convertIntegerToWords($integer, $ones, $tens, $scales) {
            $words = ''; $scaleIndex = 0;
            while ($integer > 0) {
                $chunk = $integer % 1000;
                if ($chunk > 0) { $chunkWords = convertChunkToWords($chunk, $ones, $tens); if ($scaleIndex > 0) $chunkWords .= ' ' . $scales[$scaleIndex]; $words = $chunkWords . ' ' . $words; }
                $integer = intval($integer / 1000); $scaleIndex++;
            }
            return trim($words);
        }
    }
    if (!function_exists('convertChunkToWords')) {
        function convertChunkToWords($chunk, $ones, $tens) {
            $words = ''; $hundreds = intval($chunk / 100); $remainder = $chunk % 100;
            if ($hundreds > 0) { $words .= $ones[$hundreds] . ' hundred'; if ($remainder > 0) $words .= ' '; }
            if ($remainder >= 20) { $tensDigit = intval($remainder / 10); $onesDigit = $remainder % 10; $words .= $tens[$tensDigit]; if ($onesDigit > 0) $words .= '-' . $ones[$onesDigit]; }
            elseif ($remainder > 0) { $words .= $ones[$remainder]; }
            return $words;
        }
    }

    $invoiceBank = null;
    if (!empty($tenant->settings['invoice_bank_account_id'])) {
        $invoiceBank = \App\Models\Bank::find($tenant->settings['invoice_bank_account_id']);
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order {{ $purchaseOrder->lpo_number }} - {{ $tenant->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; line-height: 1.3; color: #333; background: #f0f0f0; }
        .page-container { max-width: 210mm; margin: 0 auto; padding: 15px 20px; background: white; box-shadow: 0 0 20px rgba(0,0,0,0.1); min-height: 100vh; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 12px; border-bottom: 3px solid #e67e22; margin-bottom: 15px; }
        .header-left { flex: 1; }
        .header-right { text-align: right; }
        .company-logo { max-width: 80px; max-height: 50px; margin-bottom: 6px; }
        .company-name { font-size: 20px; font-weight: bold; color: #e67e22; text-transform: uppercase; letter-spacing: 0.5px; }
        .company-details { font-size: 10px; color: #666; line-height: 1.4; margin-top: 3px; }
        .doc-title { font-size: 22px; font-weight: bold; color: #e67e22; letter-spacing: 2px; text-transform: uppercase; }
        .doc-number { font-size: 13px; font-weight: bold; color: #555; margin-top: 4px; }
        .doc-status { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; }
        .status-draft { background: #fff3cd; color: #856404; }
        .status-sent { background: #d1ecf1; color: #0c5460; }
        .status-confirmed { background: #d4edda; color: #155724; }
        .status-received { background: #e8daef; color: #6c3483; }

        /* Billing Section */
        .billing-section { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .billing-left, .billing-right { width: 48%; }
        .billing-right { text-align: right; }
        .section-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #999; font-weight: bold; margin-bottom: 4px; }
        .party-name { font-size: 14px; font-weight: bold; color: #e67e22; margin-bottom: 2px; }
        .party-details { font-size: 11px; color: #555; line-height: 1.4; }
        .info-line { font-size: 11px; color: #555; margin-bottom: 2px; }
        .info-line strong { color: #333; }

        /* Items Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .items-table th { background: #e67e22; color: white; padding: 7px 8px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .items-table th.text-right { text-align: right; }
        .items-table td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        .items-table td.text-right { text-align: right; }
        .items-table tbody tr:nth-child(even) { background: #fafafa; }
        .items-table .item-name { font-weight: 600; color: #333; }
        .items-table .item-desc { font-size: 10px; color: #888; margin-top: 1px; }

        /* Totals */
        .totals-section { display: flex; justify-content: flex-end; margin-bottom: 12px; }
        .totals-table { width: 280px; }
        .totals-table tr td { padding: 4px 8px; font-size: 11px; }
        .totals-table .label { text-align: right; color: #666; font-weight: 600; }
        .totals-table .value { text-align: right; color: #333; min-width: 100px; }
        .totals-table .grand-total td { font-size: 14px; font-weight: bold; background: #e67e22; color: white; padding: 8px; }

        /* Amount in Words */
        .amount-words { background: #f0f7f0; padding: 6px 10px; border-left: 3px solid #27ae60; margin-bottom: 12px; font-size: 10px; }
        .amount-words strong { color: #27ae60; text-transform: uppercase; font-size: 9px; }
        .amount-words .words { font-style: italic; font-weight: 600; color: #333; margin-top: 1px; }

        /* Notes & Terms */
        .notes-terms { display: flex; gap: 15px; margin-bottom: 12px; }
        .notes-box, .terms-box { flex: 1; }
        .notes-box { background: #fff8e1; padding: 8px 10px; border-radius: 4px; border-left: 3px solid #f39c12; }
        .terms-box { background: #fef5ec; padding: 8px 10px; border-radius: 4px; border-left: 3px solid #e67e22; }
        .box-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #999; font-weight: bold; margin-bottom: 3px; }
        .box-text { font-size: 10px; color: #555; line-height: 1.4; }

        /* Bank Details */
        .bank-section { background: #f8f9fa; padding: 8px 10px; border-radius: 4px; border: 1px solid #e9ecef; margin-bottom: 12px; }
        .bank-section .section-label { margin-bottom: 4px; }
        .bank-grid { display: flex; gap: 15px; flex-wrap: wrap; }
        .bank-item { font-size: 10px; }
        .bank-item strong { color: #333; }

        /* Signatures */
        .signatures { display: flex; justify-content: space-between; margin-top: 20px; }
        .sig-box { text-align: center; min-width: 180px; }
        .sig-line { border-top: 2px solid #333; margin-top: 30px; padding-top: 4px; font-size: 10px; font-weight: bold; color: #555; }

        /* Footer */
        .footer { text-align: center; font-size: 9px; color: #999; padding-top: 10px; border-top: 1px solid #eee; margin-top: 15px; }

        /* Print Controls */
        .print-controls { position: fixed; top: 15px; right: 15px; z-index: 1000; display: flex; gap: 8px; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: white; text-decoration: none; display: inline-block; transition: all 0.3s ease; }
        .btn-primary { background: linear-gradient(135deg, #e67e22 0%, #d35400 100%); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(230,126,34,0.3); }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }

        /* Contenteditable */
        [contenteditable="true"] { outline: none; }
        [contenteditable="true"]:hover { background: rgba(230,126,34,0.04); }
        [contenteditable="true"]:focus { background: rgba(230,126,34,0.08); border-radius: 2px; }

        @media print {
            body { background: white; margin: 0; }
            .page-container { box-shadow: none; max-width: none; margin: 0; padding: 10px 15px; }
            .no-print { display: none !important; }
            [contenteditable="true"] { background: none !important; }
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Print</button>
        <a href="{{ route('tenant.procurement.purchase-orders.pdf', [$tenant->slug, $purchaseOrder->id]) }}" class="btn btn-secondary">📄 PDF</a>
        <a href="{{ route('tenant.procurement.purchase-orders.show', [$tenant->slug, $purchaseOrder->id]) }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="page-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                @if($tenant->logo)
                    <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }}" class="company-logo"><br>
                @endif
                <div class="company-name" contenteditable="true">{{ $tenant->name }}</div>
                <div class="company-details" contenteditable="true">
                    @if($tenant->address){{ $tenant->address }}<br>@endif
                    @if($tenant->phone)Phone: {{ $tenant->phone }}@endif
                    @if($tenant->email) | {{ $tenant->email }}@endif
                    @if($tenant->website)<br>{{ $tenant->website }}@endif
                </div>
            </div>
            <div class="header-right">
                <div class="doc-title" contenteditable="true">PURCHASE ORDER</div>
                <div class="doc-number" contenteditable="true">#{{ $purchaseOrder->lpo_number }}</div>
                <div>
                    <span class="doc-status status-{{ $purchaseOrder->status }}">{{ ucfirst($purchaseOrder->status) }}</span>
                </div>
            </div>
        </div>

        <!-- Billing Section -->
        <div class="billing-section">
            <div class="billing-left">
                <div class="section-label">Vendor</div>
                <div class="party-name" contenteditable="true">{{ $vendorName }}</div>
                <div class="party-details" contenteditable="true">
                    @if($vendor && $vendor->getFullAddressAttribute()){{ $vendor->getFullAddressAttribute() }}<br>@endif
                    @if($vendor && $vendor->email){{ $vendor->email }}<br>@endif
                    @if($vendor && $vendor->phone){{ $vendor->phone }}@endif
                </div>
            </div>
            <div class="billing-right">
                <div class="info-line"><strong>LPO Date:</strong> <span contenteditable="true">{{ $purchaseOrder->lpo_date->format('M d, Y') }}</span></div>
                @if($purchaseOrder->expected_delivery_date)
                    <div class="info-line"><strong>Delivery By:</strong> <span contenteditable="true">{{ $purchaseOrder->expected_delivery_date->format('M d, Y') }}</span></div>
                @endif
                @if($purchaseOrder->creator)
                    <div class="info-line" style="margin-top: 4px;"><strong>Prepared By:</strong> <span contenteditable="true">{{ $purchaseOrder->creator->name }}</span></div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Item & Description</th>
                    <th class="text-right" style="width: 10%;">Qty</th>
                    <th class="text-right" style="width: 15%;">Unit Price (₦)</th>
                    <th class="text-right" style="width: 12%;">Discount</th>
                    <th class="text-right" style="width: 8%;">Tax</th>
                    <th class="text-right" style="width: 15%;">Amount (₦)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td contenteditable="true">{{ $index + 1 }}</td>
                    <td>
                        <div class="item-name" contenteditable="true">{{ $item->product->name ?? $item->description }}</div>
                        @if($item->description && $item->description !== ($item->product->name ?? ''))
                            <div class="item-desc" contenteditable="true">{{ $item->description }}</div>
                        @endif
                    </td>
                    <td class="text-right" contenteditable="true">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }} {{ $item->unit }}</td>
                    <td class="text-right" contenteditable="true">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right" contenteditable="true">{{ number_format($item->discount, 2) }}</td>
                    <td class="text-right" contenteditable="true">{{ rtrim(rtrim(number_format($item->tax_rate, 2), '0'), '.') }}%</td>
                    <td class="text-right" contenteditable="true">{{ number_format($item->getTotal(), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="value" contenteditable="true">₦{{ number_format($purchaseOrder->subtotal, 2) }}</td>
                </tr>
                @if($purchaseOrder->discount_amount > 0)
                <tr>
                    <td class="label">Discount:</td>
                    <td class="value" style="color: #e74c3c;" contenteditable="true">-₦{{ number_format($purchaseOrder->discount_amount, 2) }}</td>
                </tr>
                @endif
                @if($purchaseOrder->tax_amount > 0)
                <tr>
                    <td class="label">Tax:</td>
                    <td class="value" contenteditable="true">₦{{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td style="text-align: right;">TOTAL:</td>
                    <td style="text-align: right;" contenteditable="true">₦{{ number_format($purchaseOrder->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <strong>Amount in Words:</strong>
            <div class="words" contenteditable="true">{{ numberToWords($purchaseOrder->total_amount) }}</div>
        </div>

        <!-- Notes & Terms -->
        @if($purchaseOrder->notes || $purchaseOrder->terms_conditions)
        <div class="notes-terms">
            @if($purchaseOrder->notes)
            <div class="notes-box">
                <div class="box-label">Notes</div>
                <div class="box-text" contenteditable="true">{{ $purchaseOrder->notes }}</div>
            </div>
            @endif
            @if($purchaseOrder->terms_conditions)
            <div class="terms-box">
                <div class="box-label">Terms & Conditions</div>
                <div class="box-text" contenteditable="true">{{ $purchaseOrder->terms_conditions }}</div>
            </div>
            @endif
        </div>
        @endif

        <!-- Bank Details -->
        @if($invoiceBank)
        <div class="bank-section">
            <div class="section-label">Bank Details</div>
            <div class="bank-grid">
                <div class="bank-item"><strong>Bank:</strong> <span contenteditable="true">{{ $invoiceBank->bank_name }}</span></div>
                <div class="bank-item"><strong>Account:</strong> <span contenteditable="true">{{ $invoiceBank->account_name }}</span></div>
                <div class="bank-item"><strong>Account No:</strong> <span contenteditable="true">{{ $invoiceBank->account_number }}</span></div>
                @if($invoiceBank->sort_code)
                    <div class="bank-item"><strong>Sort Code:</strong> <span contenteditable="true">{{ $invoiceBank->sort_code }}</span></div>
                @endif
            </div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="sig-box">
                @if($tenant->signature)
                    <img src="{{ asset('storage/' . $tenant->signature) }}" alt="Signature" style="max-width: 150px; max-height: 50px;">
                @endif
                <div class="sig-line">
                    Authorized By<br>
                    <span style="font-weight: normal; font-size: 9px;" contenteditable="true">{{ $tenant->name }}</span>
                </div>
            </div>
            <div class="sig-box">
                <div class="sig-line">
                    Received By (Vendor)<br>
                    <span style="font-weight: normal; font-size: 9px;">Date: ___________________</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer" contenteditable="true">
            <p><strong>{{ $tenant->name }}</strong> | Generated on {{ now()->format('l, M d, Y') }}</p>
            <p>This is a computer-generated document.</p>
        </div>
    </div>
</body>
</html>
