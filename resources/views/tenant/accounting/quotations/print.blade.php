@php
    $docTitle = strtoupper($quotation->document_title ?: $term->label('quotation'));

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
    $invoiceTerms = $tenant->settings['invoice_terms'] ?? null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $docTitle }} {{ $quotation->getQuotationNumber() }} - {{ $tenant->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; line-height: 1.3; color: #333; background: #f0f0f0; }
        .page-container { max-width: 210mm; margin: 0 auto; padding: 15px 20px; background: white; box-shadow: 0 0 20px rgba(0,0,0,0.1); min-height: 100vh; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 12px; border-bottom: 3px solid #2c5aa0; margin-bottom: 15px; }
        .header-left { flex: 1; }
        .header-right { text-align: right; }
        .company-logo { max-width: 80px; max-height: 50px; margin-bottom: 6px; }
        .company-name { font-size: 20px; font-weight: bold; color: #2c5aa0; text-transform: uppercase; letter-spacing: 0.5px; }
        .company-details { font-size: 10px; color: #666; line-height: 1.4; margin-top: 3px; }
        .doc-title { font-size: 22px; font-weight: bold; color: #2c5aa0; letter-spacing: 2px; text-transform: uppercase; }
        .doc-number { font-size: 13px; font-weight: bold; color: #555; margin-top: 4px; }
        .doc-status { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; }
        .status-draft { background: #fff3cd; color: #856404; }
        .status-sent { background: #d1ecf1; color: #0c5460; }
        .status-accepted { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .status-expired { background: #ffeeba; color: #856404; }
        .status-converted { background: #e8daef; color: #6c3483; }

        /* Billing Section */
        .billing-section { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .billing-left, .billing-right { width: 48%; }
        .billing-right { text-align: right; }
        .section-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #999; font-weight: bold; margin-bottom: 4px; }
        .party-name { font-size: 14px; font-weight: bold; color: #2c5aa0; margin-bottom: 2px; }
        .party-details { font-size: 11px; color: #555; line-height: 1.4; }
        .info-line { font-size: 11px; color: #555; margin-bottom: 2px; }
        .info-line strong { color: #333; }

        /* Items Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .items-table th { background: #2c5aa0; color: white; padding: 7px 8px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
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
        .totals-table .grand-total td { font-size: 14px; font-weight: bold; background: #2c5aa0; color: white; padding: 8px; }

        /* Amount in Words */
        .amount-words { background: #f0f7f0; padding: 6px 10px; border-left: 3px solid #27ae60; margin-bottom: 12px; font-size: 10px; }
        .amount-words strong { color: #27ae60; text-transform: uppercase; font-size: 9px; }
        .amount-words .words { font-style: italic; font-weight: 600; color: #333; margin-top: 1px; }

        /* Notes & Terms */
        .notes-terms { display: flex; gap: 15px; margin-bottom: 12px; }
        .notes-box, .terms-box { flex: 1; }
        .notes-box { background: #fff8e1; padding: 8px 10px; border-radius: 4px; border-left: 3px solid #f39c12; }
        .terms-box { background: #f0f4ff; padding: 8px 10px; border-radius: 4px; border-left: 3px solid #2c5aa0; }
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

        /* Validity */
        .validity-badge { display: inline-block; padding: 4px 12px; background: #e8f5e9; border: 1px solid #a5d6a7; border-radius: 4px; font-size: 10px; color: #2e7d32; font-weight: 600; margin-top: 2px; }
        .validity-expired { background: #fce4ec; border-color: #ef9a9a; color: #c62828; }

        /* Print Controls */
        .print-controls { position: fixed; top: 15px; right: 15px; z-index: 1000; display: flex; gap: 8px; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; color: white; text-decoration: none; display: inline-block; transition: all 0.3s ease; }
        .btn-primary { background: linear-gradient(135deg, #2c5aa0 0%, #1e3d72 100%); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(44,90,160,0.3); }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }

        /* Contenteditable */
        [contenteditable="true"] { outline: none; }
        [contenteditable="true"]:hover { background: rgba(44,90,160,0.04); }
        [contenteditable="true"]:focus { background: rgba(44,90,160,0.08); border-radius: 2px; }

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
        <a href="{{ route('tenant.accounting.quotations.pdf', [$tenant->slug, $quotation->id]) }}" class="btn btn-secondary">📄 PDF</a>
        <a href="{{ route('tenant.accounting.quotations.show', [$tenant->slug, $quotation->id]) }}" class="btn btn-secondary">← Back</a>
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
                <div class="doc-title" contenteditable="true">{{ $docTitle }}</div>
                <div class="doc-number" contenteditable="true">#{{ $quotation->getQuotationNumber() }}</div>
                <div>
                    <span class="doc-status status-{{ $quotation->status }}">{{ ucfirst($quotation->status) }}</span>
                </div>
            </div>
        </div>

        <!-- Billing Section -->
        <div class="billing-section">
            <div class="billing-left">
                <div class="section-label">{{ $docTitle === 'PROFORMA INVOICE' ? 'Bill To' : 'Prepared For' }}</div>
                <div class="party-name" contenteditable="true">{{ $quotation->customer ? ($quotation->customer->company_name ?: trim($quotation->customer->first_name . ' ' . $quotation->customer->last_name)) : 'N/A' }}</div>
                <div class="party-details" contenteditable="true">
                    @if($quotation->customer && $quotation->customer->address){{ $quotation->customer->address }}<br>@endif
                    @if($quotation->customer && $quotation->customer->email){{ $quotation->customer->email }}<br>@endif
                    @if($quotation->customer && $quotation->customer->phone){{ $quotation->customer->phone }}@endif
                </div>
            </div>
            <div class="billing-right">
                <div class="info-line"><strong>Date:</strong> <span contenteditable="true">{{ $quotation->quotation_date->format('M d, Y') }}</span></div>
                @if($quotation->expiry_date)
                    <div class="info-line"><strong>Valid Until:</strong> <span contenteditable="true">{{ $quotation->expiry_date->format('M d, Y') }}</span></div>
                    @if($quotation->isExpired())
                        <span class="validity-badge validity-expired">Expired</span>
                    @else
                        <span class="validity-badge">Valid</span>
                    @endif
                @endif
                @if($quotation->reference_number)
                    <div class="info-line" style="margin-top: 4px;"><strong>Ref:</strong> <span contenteditable="true">{{ $quotation->reference_number }}</span></div>
                @endif
            </div>
        </div>

        <!-- Subject -->
        @if($quotation->subject)
            <div style="margin-bottom: 10px; padding: 6px 10px; background: #f0f4ff; border-radius: 4px; border-left: 3px solid #2c5aa0;">
                <span class="box-label">Subject</span>
                <div style="font-size: 12px; font-weight: 600; color: #333;" contenteditable="true">{{ $quotation->subject }}</div>
            </div>
        @endif

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 8%;">Type</th>
                    <th style="width: 37%;">Item & Description</th>
                    <th class="text-right" style="width: 12%;">Qty</th>
                    <th class="text-right" style="width: 18%;">Rate (₦)</th>
                    <th class="text-right" style="width: 20%;">Amount (₦)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $index => $item)
                <tr>
                    <td contenteditable="true">{{ $index + 1 }}</td>
                    <td style="text-align: center;">
                        @if(($item->item_type ?? 'product') === 'product')
                            <span style="background: #dbeafe; color: #1e40af; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 600;">Product</span>
                        @else
                            <span style="background: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 600;">Service</span>
                        @endif
                    </td>
                    <td>
                        <div class="item-name" contenteditable="true">{{ $item->product_name }}</div>
                        @if($item->description)
                            <div class="item-desc" contenteditable="true">{{ $item->description }}</div>
                        @endif
                    </td>
                    <td class="text-right" contenteditable="true">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }} {{ $item->unit }}</td>
                    <td class="text-right" contenteditable="true">{{ number_format($item->rate, 2) }}</td>
                    <td class="text-right" contenteditable="true">{{ number_format($item->quantity * $item->rate, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Items Subtotal:</td>
                    <td class="value" contenteditable="true">₦{{ number_format($quotation->subtotal, 2) }}</td>
                </tr>
                @if(!empty($quotation->additional_charges) && is_array($quotation->additional_charges))
                    @foreach($quotation->additional_charges as $charge)
                    <tr>
                        <td class="label">{{ $charge['name'] ?? 'Additional Charge' }}:</td>
                        <td class="value" contenteditable="true">₦{{ number_format($charge['amount'] ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                @endif
                @if($quotation->vat_enabled && $quotation->vat_amount > 0)
                <tr>
                    <td class="label">VAT (7.5%):</td>
                    <td class="value" contenteditable="true">₦{{ number_format($quotation->vat_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td style="text-align: right;">TOTAL:</td>
                    <td style="text-align: right;" contenteditable="true">₦{{ number_format($quotation->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <strong>Amount in Words:</strong>
            <div class="words" contenteditable="true">{{ numberToWords($quotation->total_amount) }}</div>
        </div>

        <!-- Notes & Terms -->
        @if($quotation->notes || $quotation->terms_and_conditions || $invoiceTerms)
        <div class="notes-terms">
            @if($quotation->notes)
            <div class="notes-box">
                <div class="box-label">Notes</div>
                <div class="box-text" contenteditable="true">{{ $quotation->notes }}</div>
            </div>
            @endif
            @if($quotation->terms_and_conditions || $invoiceTerms)
            <div class="terms-box">
                <div class="box-label">Terms & Conditions</div>
                <div class="box-text" contenteditable="true">{{ $quotation->terms_and_conditions ?: $invoiceTerms }}</div>
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
                    Authorized Signatory<br>
                    <span style="font-weight: normal; font-size: 9px;" contenteditable="true">{{ $tenant->name }}</span>
                </div>
            </div>
            <div class="sig-box">
                <div class="sig-line">
                    {{ $docTitle === 'PROFORMA INVOICE' ? 'Client' : 'Customer' }} Acceptance<br>
                    <span style="font-weight: normal; font-size: 9px;">Date: ___________________</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer" contenteditable="true">
            <p><strong>{{ $tenant->name }}</strong> | Generated on {{ now()->format('l, M d, Y') }}</p>
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>
