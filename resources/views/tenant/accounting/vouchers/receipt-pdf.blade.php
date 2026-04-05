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
    <title>{{ $isReceipt ? 'Receipt' : 'Payment' }} {{ $voucher->voucherType->prefix ?? '' }}{{ $voucher->voucher_number }} - {{ $tenant->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; margin: 0; padding: 10px; color: #333; font-size: 11px; line-height: 1.3; }
        .container { max-width: 100%; }

        /* Header */
        .header { text-align: center; padding-bottom: 10px; border-bottom: 3px solid #2c5aa0; margin-bottom: 15px; }
        .logo { max-height: 40px; margin-bottom: 5px; }
        .company-name { font-size: 18px; font-weight: bold; color: #2c5aa0; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
        .company-details { font-size: 10px; color: #666; line-height: 1.3; }
        .receipt-badge { display: inline-block; background: {{ $isReceipt ? '#27ae60' : '#e74c3c' }}; color: white; font-size: 16px; font-weight: bold; padding: 4px 20px; letter-spacing: 3px; text-transform: uppercase; margin-top: 8px; }

        /* Info Table */
        .info-table { width: 100%; margin-bottom: 12px; border-collapse: collapse; }
        .info-table td { padding: 5px 8px; background: #f8f9fa; border: 1px solid #e9ecef; }
        .info-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #999; font-weight: bold; }
        .info-value { font-size: 12px; font-weight: bold; color: #333; }
        .info-value.number { font-size: 14px; color: #2c5aa0; }

        /* Details Table */
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .details-table td { padding: 8px 10px; border-bottom: 1px dashed #e0e0e0; vertical-align: top; }
        .details-table .label-cell { width: 160px; font-size: 11px; font-weight: 600; color: #555; text-transform: uppercase; letter-spacing: 0.3px; }
        .details-table .value-cell { font-size: 12px; color: #333; }
        .details-table .party-name { font-weight: bold; font-size: 14px; color: #2c5aa0; }

        /* Amount Box */
        .amount-box { background: #2c5aa0; color: white; padding: 12px 15px; margin: 12px 0; }
        .amount-box table { width: 100%; }
        .amount-box .label { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .amount-box .value { font-size: 22px; font-weight: bold; text-align: right; }

        /* Amount Words */
        .amount-words { background: #f0f7f0; padding: 6px 10px; border-left: 3px solid #27ae60; margin-bottom: 10px; font-size: 10px; }
        .amount-words strong { color: #27ae60; text-transform: uppercase; font-size: 9px; }
        .amount-words .words { font-style: italic; font-weight: bold; color: #333; margin-top: 2px; }

        /* Narration */
        .narration-box { background: #fff8e1; padding: 6px 10px; border-left: 3px solid #f39c12; margin-bottom: 10px; }
        .narration-label { font-size: 9px; text-transform: uppercase; color: #999; font-weight: bold; margin-bottom: 2px; }
        .narration-text { font-size: 11px; color: #555; }

        /* Status */
        .status-posted { display: inline-block; padding: 2px 8px; background: #d4edda; color: #155724; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .status-draft { display: inline-block; padding: 2px 8px; background: #fff3cd; color: #856404; font-size: 9px; font-weight: bold; text-transform: uppercase; }

        /* Signatures */
        .signatures { display: table; width: 100%; margin-top: 25px; }
        .signature-box { display: table-cell; width: 50%; text-align: center; padding: 5px 10px; }
        .signature-line { border-top: 1px solid #333; margin-top: 25px; padding-top: 4px; font-size: 10px; font-weight: bold; color: #555; }

        /* Footer */
        .footer { text-align: center; font-size: 9px; color: #999; padding-top: 8px; border-top: 1px solid #eee; margin-top: 15px; }

        /* Watermark */
        .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 72px; color: rgba(0,0,0,0.06); z-index: -1; font-weight: bold; }

        @page { margin: 15mm; size: A4; }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    @if($voucher->status === 'draft')
        <div class="watermark">DRAFT</div>
    @endif

    <div class="container">
        <!-- Header -->
        <div class="header">
            @if($tenant->logo)
                <img src="{{ storage_path('app/public/' . $tenant->logo) }}" alt="" class="logo"><br>
            @endif
            <div class="company-name">{{ $tenant->name }}</div>
            <div class="company-details">
                @if($tenant->address) {{ $tenant->address }}<br> @endif
                @if($tenant->phone) Phone: {{ $tenant->phone }} @endif
                @if($tenant->email) | Email: {{ $tenant->email }} @endif
                @if($tenant->tax_number) <br>Tax ID: {{ $tenant->tax_number }} @endif
            </div>
            <div class="receipt-badge">{{ $isReceipt ? 'RECEIPT' : 'PAYMENT VOUCHER' }}</div>
        </div>

        <!-- Receipt Info -->
        <table class="info-table">
            <tr>
                <td>
                    <div class="info-label">Receipt No.</div>
                    <div class="info-value number">{{ $voucher->voucherType->prefix ?? '' }}{{ $voucher->voucher_number }}</div>
                </td>
                <td>
                    <div class="info-label">Date</div>
                    <div class="info-value">{{ $voucher->voucher_date->format('M d, Y') }}</div>
                </td>
                <td>
                    <div class="info-label">Status</div>
                    <div class="info-value"><span class="status-{{ $voucher->status }}">{{ ucfirst($voucher->status) }}</span></div>
                </td>
                @if($voucher->reference_number)
                <td>
                    <div class="info-label">Reference</div>
                    <div class="info-value">{{ $voucher->reference_number }}</div>
                </td>
                @endif
            </tr>
        </table>

        <!-- Receipt Details -->
        <table class="details-table">
            <tr>
                <td class="label-cell">{{ $isReceipt ? 'Received From' : 'Paid To' }}</td>
                <td class="value-cell party-name">{{ $partyName }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ $isReceipt ? 'Payment Method' : 'Paid Via' }}</td>
                <td class="value-cell">{{ $paymentMethod }}</td>
            </tr>
            @if($relatedInvoice)
            <tr>
                <td class="label-cell">For Invoice</td>
                <td class="value-cell">{{ $relatedInvoice->voucherType->prefix ?? '' }}{{ $relatedInvoice->voucher_number }} (₦{{ number_format($relatedInvoice->total_amount, 2) }})</td>
            </tr>
            @endif
            <tr>
                <td class="label-cell">Prepared By</td>
                <td class="value-cell">{{ $voucher->createdBy->name ?? 'System' }}</td>
            </tr>
        </table>

        <!-- Amount Box -->
        <div class="amount-box">
            <table><tr>
                <td class="label">{{ $isReceipt ? 'Amount Received' : 'Amount Paid' }}</td>
                <td class="value">₦{{ number_format($voucher->total_amount, 2) }}</td>
            </tr></table>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <strong>Amount in Words:</strong>
            <div class="words">{{ ucfirst(trim(numberToWords($voucher->total_amount))) }} Naira Only</div>
        </div>

        <!-- Narration -->
        @if($voucher->narration)
        <div class="narration-box">
            <div class="narration-label">Purpose / Narration</div>
            <div class="narration-text">{{ $voucher->narration }}</div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                @if($tenant->signature)
                    <img src="{{ storage_path('app/public/' . $tenant->signature) }}" alt="Signature" style="max-width: 130px; max-height: 45px;">
                @endif
                <div class="signature-line">Authorized Signatory</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">{{ $isReceipt ? 'Payer Signature' : 'Receiver Signature' }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Generated on {{ now()->format('d M Y \a\t g:i A') }} | {{ $tenant->name }} | Powered by Ballie Accounting System
            @if($voucher->status === 'posted')
                | Posted on {{ $voucher->posted_at?->format('d M Y \a\t g:i A') ?? 'N/A' }}
            @endif
        </div>
    </div>
</body>
</html>
