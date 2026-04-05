@php
    $typeColor = $voucherStyle['color'] ?? '#2c5aa0';
    $typeDark = $voucherStyle['colorDark'] ?? '#1e3d72';
    $typeBg = $voucherStyle['bgColor'] ?? '#eef2ff';
    $typeBadge = $voucherStyle['badge'] ?? strtoupper($voucher->voucherType->name);
    $typeCode = $voucherTypeCode ?? $voucher->voucherType->code;

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
            return ucfirst(trim($words)) . ' Naira Only';
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
    <title>{{ $typeBadge }} {{ ($voucher->voucherType->prefix ?? '') . $voucher->voucher_number }} - {{ $tenant->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; margin: 0; padding: 10px; color: #333; font-size: 11px; line-height: 1.3; }

        /* Header */
        .header { text-align: center; padding-bottom: 10px; border-bottom: 3px solid {{ $typeColor }}; margin-bottom: 15px; }
        .logo { max-height: 40px; margin-bottom: 5px; }
        .company-name { font-size: 18px; font-weight: bold; color: {{ $typeColor }}; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
        .company-details { font-size: 10px; color: #666; line-height: 1.3; }
        .type-badge { display: inline-block; background: {{ $typeColor }}; color: white; font-size: 14px; font-weight: bold; padding: 4px 18px; letter-spacing: 2px; text-transform: uppercase; margin-top: 8px; }

        /* Info Table */
        .info-table { width: 100%; margin-bottom: 12px; border-collapse: collapse; }
        .info-table td { padding: 5px 8px; background: #f8f9fa; border: 1px solid #e9ecef; }
        .info-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #999; font-weight: bold; }
        .info-value { font-size: 12px; font-weight: bold; color: #333; }
        .info-value.number { font-size: 14px; color: {{ $typeColor }}; }

        /* Context Section */
        .context-section { margin-bottom: 12px; padding: 8px 10px; background: {{ $typeBg }}; border-left: 4px solid {{ $typeColor }}; }
        .context-title { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: {{ $typeColor }}; font-weight: bold; margin-bottom: 5px; }
        .context-table { width: 100%; border-collapse: collapse; }
        .context-table td { padding: 4px 8px; border-bottom: 1px dashed #e0e0e0; vertical-align: top; }
        .context-table tr:last-child td { border-bottom: none; }
        .context-label-cell { width: 150px; font-size: 10px; font-weight: 600; color: #555; text-transform: uppercase; }
        .context-value-cell { font-size: 12px; color: #333; }
        .context-highlight { font-weight: bold; font-size: 13px; color: {{ $typeColor }}; }
        .transfer-arrow { text-align: center; font-size: 16px; color: {{ $typeColor }}; padding: 2px 0; }
        .account-tag { display: inline-block; padding: 2px 8px; background: white; border: 1px solid {{ $typeColor }}; font-size: 10px; color: #555; margin: 2px; }

        /* Status */
        .status-posted { display: inline-block; padding: 2px 8px; background: #d4edda; color: #155724; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .status-draft { display: inline-block; padding: 2px 8px; background: #fff3cd; color: #856404; font-size: 9px; font-weight: bold; text-transform: uppercase; }

        /* Amount Box */
        .amount-box { background: {{ $typeColor }}; color: white; padding: 10px 15px; margin: 10px 0; }
        .amount-box table { width: 100%; }
        .amount-box .label { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .amount-box .value { font-size: 20px; font-weight: bold; text-align: right; }

        /* Amount Words */
        .amount-words { background: #f0f7f0; padding: 6px 10px; border-left: 3px solid #27ae60; margin-bottom: 10px; font-size: 10px; }
        .amount-words strong { color: #27ae60; text-transform: uppercase; font-size: 9px; }
        .amount-words .words { font-style: italic; font-weight: bold; color: #333; margin-top: 2px; }

        /* Entries Table */
        .entries-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .entries-table th { background: {{ $typeColor }}; color: white; padding: 6px 8px; text-align: center; font-size: 10px; text-transform: uppercase; letter-spacing: 0.3px; }
        .entries-table th:first-child { text-align: left; }
        .entries-table td { border: 1px solid #ddd; padding: 5px 8px; font-size: 11px; }
        .entries-table .amount { text-align: right; font-family: 'DejaVu Sans Mono', monospace; }
        .entries-table .total-row { background: #f0f0f0; font-weight: bold; }

        /* Narration */
        .narration-box { background: #fff8e1; padding: 6px 10px; border-left: 3px solid #f39c12; margin-bottom: 10px; }
        .narration-label { font-size: 9px; text-transform: uppercase; color: #999; font-weight: bold; margin-bottom: 2px; }
        .narration-text { font-size: 11px; color: #555; }

        /* Signatures */
        .signatures { display: table; width: 100%; margin-top: 25px; }
        .signature-box { display: table-cell; width: 50%; text-align: center; padding: 5px 10px; }
        .signature-line { border-top: 1px solid #333; margin-top: 25px; padding-top: 4px; font-size: 10px; font-weight: bold; color: #555; }

        /* Footer */
        .footer { text-align: center; font-size: 9px; color: #999; padding-top: 8px; border-top: 1px solid #eee; margin-top: 15px; }

        /* Watermark */
        .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 72px; color: rgba(0,0,0,0.06); z-index: -1; font-weight: bold; }

        @page { margin: 15mm; size: A4; }
    </style>
</head>
<body>
    @if($voucher->status === 'draft')
        <div class="watermark">DRAFT</div>
    @endif

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
            @if($tenant->tax_number ?? false) <br>Tax ID: {{ $tenant->tax_number }} @endif
        </div>
        <div class="type-badge">{{ $typeBadge }}</div>
    </div>

    <!-- Voucher Info -->
    <table class="info-table">
        <tr>
            <td>
                <div class="info-label">Voucher No.</div>
                <div class="info-value number">{{ ($voucher->voucherType->prefix ?? '') . $voucher->voucher_number }}</div>
            </td>
            <td>
                <div class="info-label">Date</div>
                <div class="info-value">{{ $voucher->voucher_date->format('d M Y') }}</div>
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
            <td>
                <div class="info-label">Amount</div>
                <div class="info-value number">₦{{ number_format($voucher->total_amount, 2) }}</div>
            </td>
        </tr>
    </table>

    <!-- Type-Specific Context -->
    @if(isset($voucherTypeCode))
        <div class="context-section">
            @if($typeCode === 'CV')
                <div class="context-title">Transfer Details</div>
                <table class="context-table">
                    <tr>
                        <td class="context-label-cell">From Account</td>
                        <td class="context-value-cell context-highlight">{{ $fromAccount }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="transfer-arrow">↓ Transfer ↓</td>
                    </tr>
                    <tr>
                        <td class="context-label-cell">To Account</td>
                        <td class="context-value-cell context-highlight">{{ $toAccount }}</td>
                    </tr>
                </table>
            @elseif($typeCode === 'CN')
                <div class="context-title">Credit Note Details</div>
                <table class="context-table">
                    <tr>
                        <td class="context-label-cell">Issued To</td>
                        <td class="context-value-cell context-highlight">{{ $partyName }}</td>
                    </tr>
                    <tr>
                        <td class="context-label-cell">Adjustment Account</td>
                        <td class="context-value-cell">{{ $adjustmentAccount }}</td>
                    </tr>
                </table>
            @elseif($typeCode === 'DN')
                <div class="context-title">Debit Note Details</div>
                <table class="context-table">
                    <tr>
                        <td class="context-label-cell">Issued To</td>
                        <td class="context-value-cell context-highlight">{{ $partyName }}</td>
                    </tr>
                    <tr>
                        <td class="context-label-cell">Adjustment Account</td>
                        <td class="context-value-cell">{{ $adjustmentAccount }}</td>
                    </tr>
                </table>
            @elseif($typeCode === 'JV')
                <div class="context-title">Accounts Affected</div>
                @foreach($affectedAccounts as $account)
                    <span class="account-tag">{{ $account }}</span>
                @endforeach
            @endif
        </div>
    @endif

    <!-- Amount Box -->
    <div class="amount-box">
        <table><tr>
            <td class="label">Total Amount</td>
            <td class="value">₦{{ number_format($voucher->total_amount, 2) }}</td>
        </tr></table>
    </div>

    <!-- Amount in Words -->
    <div class="amount-words">
        <strong>Amount in Words:</strong>
        <div class="words">{{ numberToWords($voucher->total_amount) }}</div>
    </div>

    <!-- Entries Table -->
    <table class="entries-table">
        <thead>
            <tr>
                <th style="width: 38%; text-align: left;">Ledger Account</th>
                <th style="width: 27%;">Particulars</th>
                <th style="width: 17.5%;">Debit (₦)</th>
                <th style="width: 17.5%;">Credit (₦)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($voucher->entries as $entry)
                <tr>
                    <td>
                        <strong>{{ $entry->ledgerAccount->name }}</strong><br>
                        <span style="font-size: 9px; color: #888;">{{ $entry->ledgerAccount->accountGroup->name }}</span>
                    </td>
                    <td style="font-size: 10px;">{{ $entry->particulars ?: '-' }}</td>
                    <td class="amount">
                        @if($entry->debit_amount > 0)
                            {{ number_format($entry->debit_amount, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="amount">
                        @if($entry->credit_amount > 0)
                            {{ number_format($entry->credit_amount, 2) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" style="text-align: center;"><strong>TOTAL</strong></td>
                <td class="amount"><strong>₦{{ number_format($voucher->entries->sum('debit_amount'), 2) }}</strong></td>
                <td class="amount"><strong>₦{{ number_format($voucher->entries->sum('credit_amount'), 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- Narration -->
    @if($voucher->narration)
        <div class="narration-box">
            <div class="narration-label">Narration / Description</div>
            <div class="narration-text">{{ $voucher->narration }}</div>
        </div>
    @endif

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">
                Prepared By<br>
                <span style="font-weight: normal; font-size: 9px;">{{ $voucher->createdBy->name ?? '___________________' }}</span>
            </div>
        </div>
        <div class="signature-box">
            @if($tenant->signature)
                <img src="{{ storage_path('app/public/' . $tenant->signature) }}" alt="Signature" style="max-width: 130px; max-height: 45px;">
            @endif
            <div class="signature-line">Authorized Signatory</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Generated on {{ now()->format('d M Y \a\t g:i A') }} | {{ $tenant->name }} | Powered by Ballie Accounting System
        @if($voucher->status === 'posted')
            | Posted on {{ $voucher->posted_at?->format('d M Y \a\t g:i A') ?? 'N/A' }}
        @endif
    </div>
</body>
</html>
