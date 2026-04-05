@php
if (!function_exists('numberToWords')) {
    function numberToWords($number) {
        $ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
        $tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
        $scales = ['', 'thousand', 'million', 'billion'];
        if ($number == 0) return 'zero';
        $number = number_format($number, 2, '.', '');
        list($integer, $fraction) = explode('.', $number);
        $words = '';
        if ($integer > 0) $words .= convertIntegerToWords($integer, $ones, $tens, $scales);
        if ($fraction > 0) $words .= ' and ' . convertIntegerToWords($fraction, $ones, $tens, $scales) . ' kobo';
        return $words;
    }
}
if (!function_exists('convertIntegerToWords')) {
    function convertIntegerToWords($integer, $ones, $tens, $scales) {
        $words = ''; $scaleIndex = 0;
        while ($integer > 0) {
            $chunk = $integer % 1000;
            if ($chunk > 0) {
                $chunkWords = convertChunkToWords($chunk, $ones, $tens);
                if ($scaleIndex > 0) $chunkWords .= ' ' . $scales[$scaleIndex];
                $words = $chunkWords . ' ' . $words;
            }
            $integer = intval($integer / 1000); $scaleIndex++;
        }
        return trim($words);
    }
}
if (!function_exists('convertChunkToWords')) {
    function convertChunkToWords($chunk, $ones, $tens) {
        $words = '';
        $hundreds = intval($chunk / 100); $remainder = $chunk % 100;
        if ($hundreds > 0) { $words .= $ones[$hundreds] . ' hundred'; if ($remainder > 0) $words .= ' '; }
        if ($remainder >= 20) {
            $tensDigit = intval($remainder / 10); $onesDigit = $remainder % 10;
            $words .= $tens[$tensDigit]; if ($onesDigit > 0) $words .= '-' . $ones[$onesDigit];
        } elseif ($remainder > 0) { $words .= $ones[$remainder]; }
        return $words;
    }
}
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isReceipt ? 'Receipt' : 'Payment' }} {{ $voucher->voucherType->prefix ?? '' }}{{ $voucher->voucher_number }} - {{ $tenant->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; line-height: 1.4; color: #333; background: #f5f5f5; }
        .receipt-container { max-width: 210mm; margin: 0 auto; padding: 25px; background: white; box-shadow: 0 0 20px rgba(0,0,0,0.1); }

        /* Header */
        .receipt-header { text-align: center; padding-bottom: 15px; border-bottom: 3px solid #2c5aa0; margin-bottom: 20px; }
        .company-logo { max-width: 80px; max-height: 50px; margin-bottom: 8px; }
        .company-name { font-size: 22px; font-weight: bold; color: #2c5aa0; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        .company-details { font-size: 12px; color: #666; line-height: 1.4; }
        .receipt-badge { display: inline-block; background: {{ $isReceipt ? '#27ae60' : '#e74c3c' }}; color: white; font-size: 18px; font-weight: bold; padding: 6px 25px; letter-spacing: 3px; text-transform: uppercase; margin-top: 12px; border-radius: 4px; }

        /* Receipt Info */
        .receipt-info { display: flex; justify-content: space-between; margin-bottom: 20px; padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #e9ecef; }
        .receipt-info-group { }
        .receipt-info-group.right { text-align: right; }
        .info-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #999; font-weight: bold; margin-bottom: 2px; }
        .info-value { font-size: 14px; font-weight: bold; color: #333; }
        .info-value.number { font-size: 18px; color: #2c5aa0; }

        /* Main Content */
        .receipt-body { margin-bottom: 20px; }
        .receipt-row { display: flex; align-items: baseline; padding: 10px 0; border-bottom: 1px dashed #e0e0e0; }
        .receipt-row:last-child { border-bottom: none; }
        .receipt-label { flex: 0 0 180px; font-size: 13px; font-weight: 600; color: #555; text-transform: uppercase; letter-spacing: 0.5px; }
        .receipt-value { flex: 1; font-size: 14px; color: #333; }

        /* Amount Highlight */
        .amount-box { background: linear-gradient(135deg, #2c5aa0 0%, #1e3d72 100%); color: white; padding: 18px 20px; border-radius: 8px; margin: 20px 0; display: flex; justify-content: space-between; align-items: center; }
        .amount-box .label { font-size: 14px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }
        .amount-box .value { font-size: 28px; font-weight: bold; }

        /* Amount in Words */
        .amount-words { background: #f0f7f0; padding: 10px 15px; border-radius: 4px; border-left: 4px solid #27ae60; margin-bottom: 20px; font-size: 12px; }
        .amount-words strong { color: #27ae60; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; }
        .amount-words .words { font-style: italic; font-weight: 600; color: #333; margin-top: 3px; }

        /* Narration */
        .narration-box { background: #fff8e1; padding: 10px 15px; border-radius: 4px; border-left: 4px solid #f39c12; margin-bottom: 20px; }
        .narration-box .label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #999; font-weight: bold; margin-bottom: 3px; }
        .narration-box .text { font-size: 13px; color: #555; }

        /* Status */
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-posted { background: #d4edda; color: #155724; }
        .status-draft { background: #fff3cd; color: #856404; }

        /* Signatures */
        .signature-section { display: flex; justify-content: space-between; margin-top: 30px; padding-top: 10px; }
        .signature-box { text-align: center; min-width: 180px; }
        .signature-line { border-top: 2px solid #333; margin-top: 35px; padding-top: 6px; font-size: 11px; font-weight: bold; color: #555; }

        /* Footer */
        .receipt-footer { text-align: center; font-size: 10px; color: #999; padding-top: 15px; border-top: 1px solid #eee; margin-top: 20px; }

        /* Watermark */
        .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 72px; color: rgba(0, 0, 0, 0.06); z-index: -1; font-weight: bold; }

        /* Print Controls */
        .print-controls { position: fixed; top: 15px; right: 15px; z-index: 1000; }
        .btn { padding: 10px 20px; margin-left: 8px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; transition: all 0.3s ease; color: white; text-decoration: none; display: inline-block; }
        .btn-primary { background: linear-gradient(135deg, #2c5aa0 0%, #1e3d72 100%); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(44,90,160,0.3); }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }

        @media print {
            body { background: white; margin: 0; }
            .receipt-container { box-shadow: none; max-width: none; margin: 0; padding: 15px; }
            .no-print { display: none !important; }
            .receipt-header { border-bottom-color: #2c5aa0; }
        }
    </style>
</head>
<body>
    @if($voucher->status === 'draft')
        <div class="watermark">DRAFT</div>
    @endif

    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Print</button>
        <a href="{{ route('tenant.accounting.vouchers.pdf', [$tenant->slug, $voucher->id]) }}" class="btn btn-secondary">📄 PDF</a>
        <a href="{{ route('tenant.accounting.vouchers.show', [$tenant->slug, $voucher->id]) }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            @if($tenant->logo)
                <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }}" class="company-logo"><br>
            @endif
            <div class="company-name">{{ $tenant->name }}</div>
            <div class="company-details">
                @if($tenant->address) {{ $tenant->address }}<br> @endif
                @if($tenant->phone) 📞 {{ $tenant->phone }} @endif
                @if($tenant->email) | ✉️ {{ $tenant->email }} @endif
                @if($tenant->website) | 🌐 {{ $tenant->website }} @endif
                @if($tenant->tax_number) <br>Tax ID: {{ $tenant->tax_number }} @endif
            </div>
            <div class="receipt-badge">{{ $isReceipt ? '✓ RECEIPT' : '✓ PAYMENT VOUCHER' }}</div>
        </div>

        <!-- Receipt Info Row -->
        <div class="receipt-info">
            <div class="receipt-info-group">
                <div class="info-label">Receipt No.</div>
                <div class="info-value number">{{ $voucher->voucherType->prefix ?? '' }}{{ $voucher->voucher_number }}</div>
            </div>
            <div class="receipt-info-group">
                <div class="info-label">Date</div>
                <div class="info-value">{{ $voucher->voucher_date->format('M d, Y') }}</div>
            </div>
            <div class="receipt-info-group">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $voucher->status }}">{{ ucfirst($voucher->status) }}</span>
                </div>
            </div>
            @if($voucher->reference_number)
            <div class="receipt-info-group right">
                <div class="info-label">Reference</div>
                <div class="info-value">{{ $voucher->reference_number }}</div>
            </div>
            @endif
        </div>

        <!-- Receipt Details -->
        <div class="receipt-body">
            <div class="receipt-row">
                <div class="receipt-label">{{ $isReceipt ? 'Received From' : 'Paid To' }}</div>
                <div class="receipt-value" style="font-weight: bold; font-size: 16px; color: #2c5aa0;">{{ $partyName }}</div>
            </div>
            <div class="receipt-row">
                <div class="receipt-label">{{ $isReceipt ? 'Payment Method' : 'Paid Via' }}</div>
                <div class="receipt-value">{{ $paymentMethod }}</div>
            </div>
            @if($relatedInvoice)
            <div class="receipt-row">
                <div class="receipt-label">For Invoice</div>
                <div class="receipt-value">{{ $relatedInvoice->voucherType->prefix ?? '' }}{{ $relatedInvoice->voucher_number }} (₦{{ number_format($relatedInvoice->total_amount, 2) }})</div>
            </div>
            @endif
            <div class="receipt-row">
                <div class="receipt-label">Prepared By</div>
                <div class="receipt-value">{{ $voucher->createdBy->name ?? 'System' }}</div>
            </div>
        </div>

        <!-- Amount Box -->
        <div class="amount-box">
            <div class="label">{{ $isReceipt ? 'Amount Received' : 'Amount Paid' }}</div>
            <div class="value">₦{{ number_format($voucher->total_amount, 2) }}</div>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <strong>Amount in Words:</strong>
            <div class="words">{{ ucfirst(trim(numberToWords($voucher->total_amount))) }} Naira Only</div>
        </div>

        <!-- Narration -->
        @if($voucher->narration)
        <div class="narration-box">
            <div class="label">Purpose / Narration</div>
            <div class="text">{{ $voucher->narration }}</div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                @if($tenant->signature)
                    <img src="{{ asset('storage/' . $tenant->signature) }}" alt="Signature" style="max-width: 150px; max-height: 50px;">
                @endif
                <div class="signature-line">Authorized Signatory</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">{{ $isReceipt ? 'Payer Signature' : 'Receiver Signature' }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <p><strong>{{ $tenant->name }}</strong> | Generated on {{ now()->format('l, M d, Y \a\t g:i A') }}</p>
            <p>Powered by Ballie Business Management System | Thank you!</p>
        </div>
    </div>

    <script>
        window.onafterprint = function() { /* window.close(); */ }
    </script>
</body>
</html>
