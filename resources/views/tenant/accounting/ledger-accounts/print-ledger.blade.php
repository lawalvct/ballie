<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $ledgerAccount->name }} - Ledger Account Statement</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
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
            color: #2563eb;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .account-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .account-details h3 {
            font-size: 16px;
            margin-bottom: 5px;
            color: #1f2937;
        }

        .account-details p {
            margin: 2px 0;
            font-size: 13px;
        }

        .balance-summary {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            background: #e5e7eb;
            padding: 15px;
            border-radius: 5px;
        }

        .balance-item {
            text-align: center;
        }

        .balance-item .label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
        }

        .balance-item .amount {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }

        .transactions-table th {
            background: #374151;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }

        .transactions-table td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
        }

        .transactions-table tr:nth-child(even) {
            background: #f9fafb;
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

        .font-bold {
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }

        .print-date {
            margin-top: 10px;
            font-size: 10px;
            color: #9ca3af;
        }

        .no-transactions {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            font-style: italic;
        }

        @media print {
            body {
                margin: 0;
                background: white;
            }

            .container {
                margin: 0;
                padding: 15px;
                max-width: none;
            }

            .header {
                margin-bottom: 20px;
                padding-bottom: 15px;
            }

            .transactions-table {
                font-size: 10px;
            }

            .transactions-table th,
            .transactions-table td {
                padding: 4px;
            }
        }

        @page {
            margin: 1cm;
            size: A4;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">{{ $tenant->name ?? 'Ballie Business Management' }}</div>
            <div class="company-details">
                @if($tenant->address)
                    {{ $tenant->address }}<br>
                @endif
                @if($tenant->phone)
                    Phone: {{ $tenant->phone }} |
                @endif
                @if($tenant->email)
                    Email: {{ $tenant->email }}
                @endif
            </div>
            <div class="report-title">Ledger Account Statement</div>
        </div>

        <!-- Account Information -->
        <div class="account-info">
            <div class="account-details">
                <h3>{{ $ledgerAccount->name }}</h3>
                <p><strong>Account Code:</strong> {{ $ledgerAccount->code }}</p>
                <p><strong>Account Type:</strong> {{ ucfirst($ledgerAccount->account_type) }}</p>
                <p><strong>Account Group:</strong> {{ $ledgerAccount->accountGroup->name ?? 'N/A' }}</p>
                @if($ledgerAccount->parent)
                    <p><strong>Parent Account:</strong> {{ $ledgerAccount->parent->name }}</p>
                @endif
            </div>
            <div class="account-details">
                <p><strong>Opening Balance:</strong> ₦{{ number_format($ledgerAccount->opening_balance, 2) }}</p>
                <p><strong>Current Balance:</strong>
                    <span class="{{ $currentBalance >= 0 ? 'text-green' : 'text-red' }}">
                        ₦{{ number_format(abs($currentBalance), 2) }} {{ $currentBalance >= 0 ? 'Dr' : 'Cr' }}
                    </span>
                </p>
                <p><strong>Total Transactions:</strong> {{ $transactions->count() }}</p>
                <p><strong>Status:</strong>
                    <span class="{{ $ledgerAccount->is_active ? 'text-green' : 'text-red' }}">
                        {{ $ledgerAccount->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Balance Summary -->
        <div class="balance-summary">
            <div class="balance-item">
                <div class="label">Total Debits</div>
                <div class="amount text-green">₦{{ number_format($totalDebits, 2) }}</div>
            </div>
            <div class="balance-item">
                <div class="label">Total Credits</div>
                <div class="amount text-red">₦{{ number_format($totalCredits, 2) }}</div>
            </div>
            <div class="balance-item">
                <div class="label">Net Movement</div>
                @php $netMovement = $totalDebits - $totalCredits; @endphp
                <div class="amount {{ $netMovement >= 0 ? 'text-green' : 'text-red' }}">
                    ₦{{ number_format(abs($netMovement), 2) }} {{ $netMovement >= 0 ? 'Dr' : 'Cr' }}
                </div>
            </div>
            <div class="balance-item">
                <div class="label">Closing Balance</div>
                <div class="amount text-blue">
                    ₦{{ number_format(abs($currentBalance), 2) }} {{ $currentBalance >= 0 ? 'Dr' : 'Cr' }}
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        @if($transactions->count() > 0)
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Date</th>
                        <th style="width: 12%;">Voucher #</th>
                        <th style="width: 30%;">Description</th>
                        <th style="width: 12%;" class="text-right">Debit (₦)</th>
                        <th style="width: 12%;" class="text-right">Credit (₦)</th>
                        <th style="width: 12%;" class="text-right">Balance (₦)</th>
                        <th style="width: 12%;" class="text-center">Dr/Cr</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Opening Balance Row -->
                    <tr style="background: #f3f4f6; font-weight: bold;">
                        <td>-</td>
                        <td>-</td>
                        <td>Opening Balance</td>
                        <td class="text-right">-</td>
                        <td class="text-right">-</td>
                        <td class="text-right">{{ number_format(abs($ledgerAccount->opening_balance), 2) }}</td>
                        <td class="text-center">{{ $ledgerAccount->opening_balance >= 0 ? 'Dr' : 'Cr' }}</td>
                    </tr>

                    <!-- Transaction Rows -->
                    @foreach($transactionsWithBalance as $item)
                        @php
                            $transaction = $item['transaction'];
                            $runningBalance = $item['running_balance'];
                        @endphp
                        <tr>
                            <td>{{ $transaction->voucher->voucher_date->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ $transaction->voucher->voucher_number }}</strong>
                            </td>
                            <td>{{ $transaction->particulars ?? 'Transaction' }}</td>
                            <td class="text-right">
                                @if($transaction->debit_amount > 0)
                                    <span class="text-green font-bold">{{ number_format($transaction->debit_amount, 2) }}</span>
                                @else
                                    <span style="color: #9ca3af;">-</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($transaction->credit_amount > 0)
                                    <span class="text-red font-bold">{{ number_format($transaction->credit_amount, 2) }}</span>
                                @else
                                    <span style="color: #9ca3af;">-</span>
                                @endif
                            </td>
                            <td class="text-right font-bold">{{ number_format(abs($runningBalance), 2) }}</td>
                            <td class="text-center font-bold">{{ $runningBalance >= 0 ? 'Dr' : 'Cr' }}</td>
                        </tr>
                    @endforeach

                    <!-- Closing Balance Summary Row -->
                    <tr style="background: #e5e7eb; font-weight: bold; border-top: 2px solid #374151;">
                        <td colspan="3" class="text-right"><strong>TOTALS:</strong></td>
                        <td class="text-right text-green">{{ number_format($totalDebits, 2) }}</td>
                        <td class="text-right text-red">{{ number_format($totalCredits, 2) }}</td>
                        <td class="text-right text-blue">{{ number_format(abs($currentBalance), 2) }}</td>
                        <td class="text-center text-blue">{{ $currentBalance >= 0 ? 'Dr' : 'Cr' }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-transactions">
                <h3>No Transactions Found</h3>
                <p>This account has no transaction history to display.</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div>
                <strong>{{ $tenant->name ?? 'Ballie Business Management' }}</strong> - Accounting System
            </div>
            <div class="print-date">
                Generated on: {{ now()->format('l, F j, Y \a\t g:i A') }}
            </div>
            <div style="margin-top: 8px; font-size: 9px; color: #9ca3af;">
                Powered by <strong>Ballie</strong> - Business Management Software
            </div>
        </div>
    </div>

    <script>
        // Auto-print when page loads (optional)
        window.onload = function() {
            // Uncomment the line below to auto-print
            // window.print();
        };

        // Print function for manual printing
        function printPage() {
            window.print();
        }
    </script>
</body>
</html>
