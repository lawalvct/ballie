<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Voucher {{ $voucher->voucher_number }} - {{ $tenant->name }}</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 11px;
            color: #666;
            margin-bottom: 15px;
        }

        .voucher-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .voucher-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .voucher-info-left,
        .voucher-info-right {
            width: 48%;
        }

        .voucher-info-right {
            text-align: right;
        }

        .info-row {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .entries-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .entries-table th,
        .entries-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .entries-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        .entries-table .amount {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .entries-table .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .narration {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #333;
        }

        .narration-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 30%;
            text-align: center;
            padding: 20px 10px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 10px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-draft {
            background-color: #fef3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-posted {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .print-controls {
            margin-bottom: 20px;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 5px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72px;
            color: rgba(0, 0, 0, 0.1);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @if($voucher->status === 'draft')
        <div class="watermark">DRAFT</div>
    @endif

    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn">
            🖨️ Print Voucher
        </button>
        <a href="{{ route('tenant.accounting.vouchers.pdf', [$tenant->slug, $voucher->id]) }}" class="btn btn-secondary">
            📄 Download PDF
        </a>
        <a href="{{ route('tenant.accounting.vouchers.show', [$tenant->slug, $voucher->id]) }}" class="btn btn-secondary">
            ← Back to Voucher
        </a>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $tenant->name }}</div>
        <div class="company-details">
            @if($tenant->address)
                {{ $tenant->address }}<br>
            @endif
            @if($tenant->phone)
                Phone: {{ $tenant->phone }}
            @endif
            @if($tenant->email)
                | Email: {{ $tenant->email }}
            @endif
        </div>
        <div class="voucher-title">{{ $voucher->voucherType->name }}</div>
    </div>

    <!-- Voucher Information -->
    <div class="voucher-info">
        <div class="voucher-info-left">
            <div class="info-row">
                <span class="info-label">Voucher No:</span>
                <strong>{{ $voucher->voucher_number }}</strong>
            </div>
            <div class="info-row">
                <span class="info-label">Voucher Type:</span>
                {{ $voucher->voucherType->name }} ({{ $voucher->voucherType->code }})
            </div>
            <div class="info-row">
                <span class="info-label">Reference:</span>
                {{ $voucher->reference_number ?: 'N/A' }}
            </div>
        </div>
        <div class="voucher-info-right">
            <div class="info-row">
                <span class="info-label">Date:</span>
                <strong>{{ $voucher->voucher_date->format('d M Y') }}</strong>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="status-badge status-{{ $voucher->status }}">
                    {{ ucfirst($voucher->status) }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Amount:</span>
                <strong>₦{{ number_format($voucher->total_amount, 2) }}</strong>
            </div>
        </div>
    </div>

    <!-- Voucher Entries -->
    <table class="entries-table">
        <thead>
            <tr>
                <th style="width: 40%;">Ledger Account</th>
                <th style="width: 35%;">Particulars</th>
                <th style="width: 12.5%;">Debit Amount</th>
                <th style="width: 12.5%;">Credit Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($voucher->entries as $entry)
                <tr>
                    <td>
                        <strong>{{ $entry->ledgerAccount->name }}</strong><br>
                        <small style="color: #666;">{{ $entry->ledgerAccount->accountGroup->name }}</small>
                    </td>
                    <td>{{ $entry->particulars ?: 'N/A' }}</td>
                    <td class="amount">
                        @if($entry->debit_amount > 0)
                            ₦{{ number_format($entry->debit_amount, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="amount">
                        @if($entry->credit_amount > 0)
                            ₦{{ number_format($entry->credit_amount, 2) }}
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
        <div class="narration">
            <div class="narration-label">Narration:</div>
            <div>{{ $voucher->narration }}</div>
        </div>
    @endif

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line">
                Prepared By<br>
                <strong>{{ $voucher->createdBy->name }}</strong><br>
                {{ $voucher->created_at->format('d M Y') }}
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                Reviewed By<br>
                <br>
                Date: ___________
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                Approved By<br>
                <br>
                Date: ___________
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Generated on {{ now()->format('d M Y \a\t g:i A') }} |
        {{ $tenant->name }} |
        Powered by Ballie Accounting System
        @if($voucher->status === 'posted')
            | Posted on {{ $voucher->posted_at?->format('d M Y \a\t g:i A') ?? 'N/A' }}
        @endif
    </div>

    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() { window.print(); }

        // Close window after printing
        window.onafterprint = function() {
            // Uncomment if you want to close the window after printing
            // window.close();
        }
    </script>
</body>
</html>