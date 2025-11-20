<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Trial Balance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-date {
            font-size: 12px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .totals-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .balance-check {
            background-color: #f0f0f0;
            font-style: italic;
        }
        .account-type {
            font-size: 10px;
            padding: 2px 6px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $tenant->name ?? 'Company Name' }}</div>
        <div class="report-title">TRIAL BALANCE</div>
        <div class="report-date">
            @if(isset($fromDate) && isset($toDate))
                From {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}
            @else
                As of {{ \Carbon\Carbon::parse($asOfDate ?? now())->format('F d, Y') }}
            @endif
        </div>
    </div>

    @if(count($trialBalanceData) > 0)
        <table>
            <thead>
                <tr>
                    <th>Account Code</th>
                    <th>Account Name</th>
                    <th>Account Type</th>
                    <th>Opening Balance</th>
                    <th>Debit Amount</th>
                    <th>Credit Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trialBalanceData as $data)
                    <tr>
                        <td>{{ $data['account']->code }}</td>
                        <td>
                            {{ $data['account']->name }}
                            @if($data['account']->accountGroup)
                                <br><small style="color: #666;">{{ $data['account']->accountGroup->name }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="account-type">{{ ucfirst($data['account']->account_type) }}</span>
                        </td>
                        <td class="text-right">{{ number_format($data['opening_balance'], 2) }}</td>
                        <td class="text-right">
                            @if($data['debit_amount'] > 0)
                                {{ number_format($data['debit_amount'], 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if($data['credit_amount'] > 0)
                                {{ number_format($data['credit_amount'], 2) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <td colspan="4" class="font-bold">TOTAL</td>
                    <td class="text-right font-bold">{{ number_format($totalDebits, 2) }}</td>
                    <td class="text-right font-bold">{{ number_format($totalCredits, 2) }}</td>
                </tr>
                <tr class="balance-check">
                    <td colspan="4">Balance Check:</td>
                    <td colspan="2" class="text-right">
                        @if(abs($totalDebits - $totalCredits) < 0.01)
                            ✓ Balanced
                        @else
                            ✗ Out of Balance ({{ number_format(abs($totalDebits - $totalCredits), 2) }})
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Summary Section -->
        <div style="margin-top: 30px;">
            <h3>Summary</h3>
            <table style="width: 50%;">
                <tr>
                    <td><strong>Total Accounts:</strong></td>
                    <td class="text-right">{{ count($trialBalanceData) }}</td>
                </tr>
                <tr>
                    <td><strong>Total Debits:</strong></td>
                    <td class="text-right">₦{{ number_format($totalDebits, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Total Credits:</strong></td>
                    <td class="text-right">₦{{ number_format($totalCredits, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Difference:</strong></td>
                    <td class="text-right">₦{{ number_format(abs($totalDebits - $totalCredits), 2) }}</td>
                </tr>
            </table>
        </div>
    @else
        <div style="text-align: center; margin-top: 50px;">
            <h3>No Account Balances</h3>
            <p>There are no accounts with balances as of the selected date.</p>
        </div>
    @endif

    <div class="footer">
        Generated on {{ now()->format('F d, Y \a\t g:i A') }} | {{ $tenant->name ?? 'Company Name' }}
    </div>
</body>
</html>