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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $typeBadge }} {{ ($voucher->voucherType->prefix ?? '') . $voucher->voucher_number }} - {{ $tenant->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; color: #333; background: #f5f5f5; }
        .voucher-container { max-width: 210mm; margin: 0 auto; padding: 25px; background: white; box-shadow: 0 0 20px rgba(0,0,0,0.1); }

        /* Header */
        .header { text-align: center; padding-bottom: 12px; border-bottom: 3px solid {{ $typeColor }}; margin-bottom: 18px; }
        .company-logo { max-width: 80px; max-height: 50px; margin-bottom: 8px; }
        .company-name { font-size: 22px; font-weight: bold; color: {{ $typeColor }}; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        .company-details { font-size: 11px; color: #666; line-height: 1.4; }
        .type-badge { display: inline-block; background: {{ $typeColor }}; color: white; font-size: 16px; font-weight: bold; padding: 5px 22px; letter-spacing: 2px; text-transform: uppercase; margin-top: 10px; border-radius: 4px; }

        /* Info Row */
        .info-row-container { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-bottom: 18px; padding: 10px; background: #f8f9fa; border-radius: 6px; border: 1px solid #e9ecef; }
        .info-group { }
        .info-group.right { text-align: right; }
        .info-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #999; font-weight: bold; margin-bottom: 2px; }
        .info-value { font-size: 13px; font-weight: bold; color: #333; }
        .info-value.number { font-size: 16px; color: {{ $typeColor }}; }

        /* Context Section */
        .context-section { margin-bottom: 18px; padding: 12px 15px; background: {{ $typeBg }}; border-left: 4px solid {{ $typeColor }}; border-radius: 0 6px 6px 0; }
        .context-title { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: {{ $typeColor }}; font-weight: bold; margin-bottom: 8px; }
        .context-row { display: flex; align-items: baseline; padding: 5px 0; border-bottom: 1px dashed rgba(0,0,0,0.08); }
        .context-row:last-child { border-bottom: none; }
        .context-label { flex: 0 0 160px; font-size: 11px; font-weight: 600; color: #555; text-transform: uppercase; letter-spacing: 0.3px; }
        .context-value { flex: 1; font-size: 13px; color: #333; }
        .context-value.highlight { font-weight: bold; font-size: 14px; color: {{ $typeColor }}; }
        .transfer-arrow { text-align: center; font-size: 18px; color: {{ $typeColor }}; padding: 2px 0; }
        .account-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px; }
        .account-tag { display: inline-block; padding: 3px 10px; background: white; border: 1px solid {{ $typeColor }}40; border-radius: 12px; font-size: 11px; color: #555; }

        /* Status */
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .status-posted { background: #d4edda; color: #155724; }
        .status-draft { background: #fff3cd; color: #856404; }

        /* Amount Box */
        .amount-box { background: linear-gradient(135deg, {{ $typeColor }} 0%, {{ $typeDark }} 100%); color: white; padding: 14px 18px; border-radius: 8px; margin: 16px 0; display: flex; justify-content: space-between; align-items: center; }
        .amount-box .label { font-size: 13px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }
        .amount-box .value { font-size: 26px; font-weight: bold; }

        /* Amount Words */
        .amount-words { background: #f0f7f0; padding: 8px 12px; border-radius: 4px; border-left: 4px solid #27ae60; margin-bottom: 16px; font-size: 11px; }
        .amount-words strong { color: #27ae60; text-transform: uppercase; font-size: 9px; }
        .amount-words .words { font-style: italic; font-weight: 600; color: #333; margin-top: 2px; }

        /* Entries Table */
        .entries-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .entries-table th { background: {{ $typeColor }}; color: white; padding: 8px; text-align: center; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .entries-table th:first-child { text-align: left; }
        .entries-table td { border: 1px solid #e0e0e0; padding: 7px 8px; }
        .entries-table .amount { text-align: right; font-family: 'Courier New', monospace; font-size: 12px; }
        .entries-table .total-row { background: #f0f0f0; font-weight: bold; }
        .entries-table tbody tr:nth-child(even) { background: #fafafa; }

        /* Narration */
        .narration-box { background: #fff8e1; padding: 10px 15px; border-radius: 4px; border-left: 4px solid #f39c12; margin-bottom: 16px; }
        .narration-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #999; font-weight: bold; margin-bottom: 3px; }
        .narration-text { font-size: 12px; color: #555; }

        /* Signatures */
        .signature-section { display: flex; justify-content: space-between; margin-top: 30px; }
        .signature-box { text-align: center; min-width: 180px; }
        .signature-line { border-top: 2px solid #333; margin-top: 35px; padding-top: 5px; font-size: 10px; font-weight: bold; color: #555; }

        /* Footer */
        .footer { text-align: center; font-size: 10px; color: #999; padding-top: 12px; border-top: 1px solid #eee; margin-top: 18px; }

        /* Watermark */
        .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 72px; color: rgba(0,0,0,0.06); z-index: -1; font-weight: bold; }

        /* Print Controls */
        .print-controls { position: fixed; top: 15px; right: 15px; z-index: 1000; }
        .btn { padding: 10px 20px; margin-left: 8px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; transition: all 0.3s ease; color: white; text-decoration: none; display: inline-block; }
        .btn-primary { background: linear-gradient(135deg, {{ $typeColor }} 0%, {{ $typeDark }} 100%); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }

        @media print {
            body { background: white; margin: 0; }
            .voucher-container { box-shadow: none; max-width: none; margin: 0; padding: 15px; }
            .no-print { display: none !important; }
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

    <div class="voucher-container">
        <!-- Header -->
        <div class="header">
            @if($tenant->logo)
                <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }}" class="company-logo"><br>
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
        <div class="info-row-container">
            <div class="info-group">
                <div class="info-label">Voucher No.</div>
                <div class="info-value number">{{ ($voucher->voucherType->prefix ?? '') . $voucher->voucher_number }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Date</div>
                <div class="info-value">{{ $voucher->voucher_date->format('d M Y') }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $voucher->status }}">{{ ucfirst($voucher->status) }}</span>
                </div>
            </div>
            @if($voucher->reference_number)
            <div class="info-group">
                <div class="info-label">Reference</div>
                <div class="info-value">{{ $voucher->reference_number }}</div>
            </div>
            @endif
            <div class="info-group right">
                <div class="info-label">Amount</div>
                <div class="info-value number">₦{{ number_format($voucher->total_amount, 2) }}</div>
            </div>
        </div>

        <!-- Type-Specific Context Section -->
        @if(isset($voucherTypeCode))
            <div class="context-section">
                @if($typeCode === 'CV')
                    <div class="context-title">Transfer Details</div>
                    <div class="context-row">
                        <div class="context-label">From Account</div>
                        <div class="context-value highlight">{{ $fromAccount }}</div>
                    </div>
                    <div class="transfer-arrow">↓ Transfer ↓</div>
                    <div class="context-row">
                        <div class="context-label">To Account</div>
                        <div class="context-value highlight">{{ $toAccount }}</div>
                    </div>
                @elseif($typeCode === 'CN')
                    <div class="context-title">Credit Note Details</div>
                    <div class="context-row">
                        <div class="context-label">Issued To</div>
                        <div class="context-value highlight">{{ $partyName }}</div>
                    </div>
                    <div class="context-row">
                        <div class="context-label">Adjustment Account</div>
                        <div class="context-value">{{ $adjustmentAccount }}</div>
                    </div>
                @elseif($typeCode === 'DN')
                    <div class="context-title">Debit Note Details</div>
                    <div class="context-row">
                        <div class="context-label">Issued To</div>
                        <div class="context-value highlight">{{ $partyName }}</div>
                    </div>
                    <div class="context-row">
                        <div class="context-label">Adjustment Account</div>
                        <div class="context-value">{{ $adjustmentAccount }}</div>
                    </div>
                @elseif($typeCode === 'JV')
                    <div class="context-title">Accounts Affected</div>
                    <div class="account-tags">
                        @foreach($affectedAccounts as $account)
                            <span class="account-tag">{{ $account }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- Amount Box -->
        <div class="amount-box">
            <div class="label">Total Amount</div>
            <div class="value">₦{{ number_format($voucher->total_amount, 2) }}</div>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <strong>Amount in Words:</strong>
            <div class="words">{{ numberToWords($voucher->total_amount) }}</div>
        </div>

        <!-- Voucher Entries -->
        <table class="entries-table">
            <thead>
                <tr>
                    <th style="width: 40%; text-align: left;">Ledger Account</th>
                    <th style="width: 30%;">Particulars</th>
                    <th style="width: 15%;">Debit (₦)</th>
                    <th style="width: 15%;">Credit (₦)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($voucher->entries as $entry)
                    <tr>
                        <td>
                            <strong>{{ $entry->ledgerAccount->name }}</strong><br>
                            <small style="color: #888;">{{ $entry->ledgerAccount->accountGroup->name }}</small>
                        </td>
                        <td style="font-size: 11px;">{{ $entry->particulars ?: '-' }}</td>
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
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    Prepared By<br>
                    <span style="font-weight: normal; font-size: 9px;">{{ $voucher->createdBy->name ?? '___________________' }}</span>
                </div>
            </div>
            <div class="signature-box">
                @if($tenant->signature)
                    <img src="{{ asset('storage/' . $tenant->signature) }}" alt="Signature" style="max-width: 150px; max-height: 50px;">
                @endif
                <div class="signature-line">Authorized Signatory</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ $tenant->name }}</strong> | Generated on {{ now()->format('l, M d, Y \a\t g:i A') }}</p>
            <p>Powered by Ballie Business Management System</p>
            @if($voucher->status === 'posted')
                <p>Posted on {{ $voucher->posted_at?->format('d M Y \a\t g:i A') ?? 'N/A' }}</p>
            @endif
        </div>
    </div>

    <script>
        window.onafterprint = function() { /* window.close(); */ }
    </script>
</body>
</html>
