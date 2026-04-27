<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $ledgerAccount->name }} - Ledger Account Statement</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/ballie_logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/ballie_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/ballie_logo.png') }}">
    <style>
        @page {
            margin: 10mm 9mm 9mm;
            size: A4 portrait;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10.5px;
            line-height: 1.35;
            color: #1f2937;
            background: #ffffff;
        }

        .page {
            width: 100%;
        }

        .header {
            border-bottom: 2px solid #1d4ed8;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header-table,
        .info-table,
        .transactions-table,
        .footer-table,
        .kv-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td,
        .info-table td,
        .footer-table td {
            vertical-align: top;
        }

        .company-name {
            font-size: 20px;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 2px;
        }

        .company-meta {
            color: #6b7280;
            font-size: 10px;
            line-height: 1.45;
        }

        .report-title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 2px;
        }

        .report-subtitle {
            color: #6b7280;
            font-size: 10px;
            line-height: 1.4;
        }

        .info-card {
            width: 50%;
        }

        .info-card-left {
            padding-right: 8px;
        }

        .info-card-right {
            padding-left: 8px;
        }

        .box {
            border: 1px solid #d1d5db;
            background: #f8fafc;
            padding: 8px 10px;
        }

        .box-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .account-name {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 5px;
        }

        .kv-table td {
            padding: 1px 0;
            font-size: 10.5px;
        }

        .kv-table tr.kv-divider td {
            border-top: 1px dashed #d1d5db;
            padding-top: 4px;
        }

        .kv-label {
            width: 48%;
            color: #6b7280;
        }

        .kv-value {
            font-weight: 600;
            color: #111827;
        }

        .transactions-table {
            margin-top: 10px;
        }

        .transactions-table thead {
            display: table-header-group;
        }

        .transactions-table tr {
            page-break-inside: avoid;
        }

        .transactions-table th {
            background: #1f2937;
            color: #ffffff;
            border: 1px solid #374151;
            padding: 5px 6px;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: left;
        }

        .transactions-table td {
            border: 1px solid #e5e7eb;
            padding: 4px 6px;
            font-size: 10px;
            vertical-align: top;
        }

        .row-opening td {
            background: #eff6ff;
            font-weight: 700;
        }

        .row-alt td {
            background: #fafafa;
        }

        .row-total td {
            background: #e0f2fe;
            font-weight: 700;
            border-top: 2px solid #0284c7;
        }

        .description {
            word-break: break-word;
        }

        .nowrap {
            white-space: nowrap;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-green {
            color: #059669;
        }

        .text-red {
            color: #dc2626;
        }

        .text-blue {
            color: #2563eb;
        }

        .muted {
            color: #9ca3af;
        }

        .footer {
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px solid #d1d5db;
            color: #6b7280;
            font-size: 9.5px;
        }

        .small {
            font-size: 9px;
        }

        .no-transactions {
            margin-top: 10px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            padding: 18px;
            text-align: center;
            color: #6b7280;
        }
    </style>
</head>
<body>
    @php
        $netMovement = $totalDebits - $totalCredits;
    @endphp

    <div class="page">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td style="width: 58%;">
                        <div class="company-name">{{ $tenant->name ?? 'Ballie Business Management' }}</div>
                        <div class="company-meta">
                            @if($tenant->address)
                                {{ $tenant->address }}<br>
                            @endif
                            @if($tenant->phone)
                                Phone: {{ $tenant->phone }}
                            @endif
                            @if($tenant->phone && $tenant->email)
                                |
                            @endif
                            @if($tenant->email)
                                Email: {{ $tenant->email }}
                            @endif
                        </div>
                    </td>
                    <td style="width: 42%;" class="text-right">
                        <div class="report-title">Ledger Account Statement</div>
                        <div class="report-subtitle">Generated on {{ now()->format('d M Y, g:i A') }}</div>
                        <div class="report-subtitle">Currency: NGN</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="info-table">
            <tr>
                <td class="info-card info-card-left">
                    <div class="box">
                        <div class="box-title">Account Details</div>
                        <div class="account-name">{{ $ledgerAccount->name }}</div>
                        <table class="kv-table">
                            <tr>
                                <td class="kv-label">Account Code</td>
                                <td class="kv-value">{{ $ledgerAccount->code }}</td>
                            </tr>
                            <tr>
                                <td class="kv-label">Account Type</td>
                                <td class="kv-value">{{ ucfirst($ledgerAccount->account_type) }}</td>
                            </tr>
                            <tr>
                                <td class="kv-label">Account Group</td>
                                <td class="kv-value">{{ $ledgerAccount->accountGroup->name ?? 'N/A' }}</td>
                            </tr>
                            @if($ledgerAccount->parent)
                                <tr>
                                    <td class="kv-label">Parent Account</td>
                                    <td class="kv-value">{{ $ledgerAccount->parent->name }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="kv-label">Status</td>
                                <td class="kv-value {{ $ledgerAccount->is_active ? 'text-green' : 'text-red' }}">
                                    {{ $ledgerAccount->is_active ? 'Active' : 'Inactive' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td class="info-card info-card-right">
                    <div class="box">
                        <div class="box-title">Statement Snapshot</div>
                        <table class="kv-table">
                            <tr>
                                <td class="kv-label">Opening Balance</td>
                                <td class="kv-value text-right">
                                    NGN {{ number_format(abs($ledgerAccount->opening_balance), 2) }}
                                    {{ $ledgerAccount->opening_balance >= 0 ? 'Dr' : 'Cr' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="kv-label">Total Debits</td>
                                <td class="kv-value text-right text-green">
                                    NGN {{ number_format($totalDebits, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="kv-label">Total Credits</td>
                                <td class="kv-value text-right text-red">
                                    NGN {{ number_format($totalCredits, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="kv-label">Net Movement</td>
                                <td class="kv-value text-right {{ $netMovement >= 0 ? 'text-green' : 'text-red' }}">
                                    NGN {{ number_format(abs($netMovement), 2) }} {{ $netMovement >= 0 ? 'Dr' : 'Cr' }}
                                </td>
                            </tr>
                            <tr class="kv-divider">
                                <td class="kv-label">Closing Balance</td>
                                <td class="kv-value text-right {{ $currentBalance >= 0 ? 'text-green' : 'text-red' }}">
                                    NGN {{ number_format(abs($currentBalance), 2) }}
                                    {{ $currentBalance >= 0 ? 'Dr' : 'Cr' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="kv-label">Transactions</td>
                                <td class="kv-value text-right">{{ $transactions->count() }}</td>
                            </tr>
                            <tr>
                                <td class="kv-label">Statement Date</td>
                                <td class="kv-value text-right">{{ now()->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        @if($transactions->count() > 0)
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th style="width: 11%;">Date</th>
                        <th style="width: 14%;">Voucher #</th>
                        <th style="width: 31%;">Description</th>
                        <th style="width: 14%;" class="text-right">Debit (NGN)</th>
                        <th style="width: 14%;" class="text-right">Credit (NGN)</th>
                        <th style="width: 10%;" class="text-right">Balance (NGN)</th>
                        <th style="width: 6%;" class="text-center">Dr/Cr</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="row-opening">
                        <td class="nowrap">-</td>
                        <td class="nowrap">-</td>
                        <td>Opening Balance</td>
                        <td class="text-right">-</td>
                        <td class="text-right">-</td>
                        <td class="text-right">{{ number_format(abs($ledgerAccount->opening_balance), 2) }}</td>
                        <td class="text-center">{{ $ledgerAccount->opening_balance >= 0 ? 'Dr' : 'Cr' }}</td>
                    </tr>

                    @foreach($transactionsWithBalance as $item)
                        @php
                            $transaction = $item['transaction'];
                            $runningBalance = $item['running_balance'];
                        @endphp
                        <tr class="{{ $loop->even ? 'row-alt' : '' }}">
                            <td class="nowrap">{{ $transaction->voucher->voucher_date->format('d/m/Y') }}</td>
                            <td class="nowrap"><strong>{{ $transaction->voucher->voucher_number }}</strong></td>
                            <td class="description">{{ $transaction->particulars ?? $transaction->voucher->narration ?? 'Transaction' }}</td>
                            <td class="text-right">
                                @if($transaction->debit_amount > 0)
                                    <span class="text-green"><strong>{{ number_format($transaction->debit_amount, 2) }}</strong></span>
                                @else
                                    <span class="muted">-</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($transaction->credit_amount > 0)
                                    <span class="text-red"><strong>{{ number_format($transaction->credit_amount, 2) }}</strong></span>
                                @else
                                    <span class="muted">-</span>
                                @endif
                            </td>
                            <td class="text-right"><strong>{{ number_format(abs($runningBalance), 2) }}</strong></td>
                            <td class="text-center {{ $runningBalance >= 0 ? 'text-green' : 'text-red' }}"><strong>{{ $runningBalance >= 0 ? 'Dr' : 'Cr' }}</strong></td>
                        </tr>
                    @endforeach

                    <tr class="row-total">
                        <td colspan="3" class="text-right"><strong>TOTALS:</strong></td>
                        <td class="text-right text-green"><strong>{{ number_format($totalDebits, 2) }}</strong></td>
                        <td class="text-right text-red"><strong>{{ number_format($totalCredits, 2) }}</strong></td>
                        <td class="text-right text-blue"><strong>{{ number_format(abs($currentBalance), 2) }}</strong></td>
                        <td class="text-center text-blue"><strong>{{ $currentBalance >= 0 ? 'Dr' : 'Cr' }}</strong></td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-transactions">
                <strong>No Transactions Found</strong><br>
                This account has no transaction history to display.
            </div>
        @endif

        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td>
                        <strong>{{ $tenant->name ?? 'Ballie Business Management' }}</strong> - Accounting System
                    </td>
                    <td class="text-right">
                        Generated on {{ now()->format('l, F j, Y \a\t g:i A') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="small" style="padding-top: 6px; text-align: center;">
                        Powered by <strong>Ballie</strong> - Business Management Software
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
