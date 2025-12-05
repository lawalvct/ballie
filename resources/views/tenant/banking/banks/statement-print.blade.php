<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Statement - {{ $bank->bank_name }} - {{ $tenant->name }}</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
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
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .statement-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0;
        }

        .period {
            font-size: 12px;
            color: #666;
        }

        .bank-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }

        .bank-details-left, .bank-details-right {
            width: 48%;
        }

        .detail-row {
            margin-bottom: 5px;
        }

        .detail-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .summary-card {
            flex: 1;
            padding: 10px;
            margin: 0 5px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .summary-card:first-child { margin-left: 0; }
        .summary-card:last-child { margin-right: 0; }

        .summary-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }

        .summary-amount {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .transactions-table th,
        .transactions-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .transactions-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .transactions-table .amount {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .transactions-table .opening-row {
            background-color: #e3f2fd;
            font-weight: bold;
        }

        .transactions-table .closing-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
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
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn">
            🖨️ Print Statement
        </button>
        <a href="{{ route('tenant.banking.banks.statement', [$tenant->slug, $bank->id, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-secondary">
            ← Back to Statement
        </a>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $tenant->name }}</div>
        <div class="statement-title">Bank Account Statement</div>
        <div class="period">{{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</div>
    </div>

    <!-- Bank Details -->
    <div class="bank-details">
        <div class="bank-details-left">
            <div class="detail-row">
                <span class="detail-label">Bank Name:</span>
                <strong>{{ $bank->bank_name }}</strong>
            </div>
            <div class="detail-row">
                <span class="detail-label">Account Name:</span>
                {{ $bank->account_name }}
            </div>
            <div class="detail-row">
                <span class="detail-label">Account Number:</span>
                <strong>{{ $bank->account_number }}</strong>
            </div>
            @if($bank->branch_name)
            <div class="detail-row">
                <span class="detail-label">Branch:</span>
                {{ $bank->branch_name }}
            </div>
            @endif
        </div>
        <div class="bank-details-right">
            <div class="detail-row">
                <span class="detail-label">Currency:</span>
                {{ $bank->currency }}
            </div>
            <div class="detail-row">
                <span class="detail-label">Account Type:</span>
                {{ ucfirst($bank->account_type) }}
            </div>
            <div class="detail-row">
                <span class="detail-label">Generated:</span>
                {{ now()->format('M d, Y h:i A') }}
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-label">Opening Balance</div>
            <div class="summary-amount">₦{{ number_format($openingBalanceAmount ?? 0, 2) }}</div>
            <div class="summary-label">{{ ($openingBalanceAmount ?? 0) >= 0 ? 'DR' : 'CR' }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Debits</div>
            <div class="summary-amount" style="color: #28a745;">₦{{ number_format($totalDebits ?? 0, 2) }}</div>
            <div class="summary-label">Money In</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Credits</div>
            <div class="summary-amount" style="color: #dc3545;">₦{{ number_format($totalCredits ?? 0, 2) }}</div>
            <div class="summary-label">Money Out</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Closing Balance</div>
            <div class="summary-amount">₦{{ number_format($closingBalance ?? 0, 2) }}</div>
            <div class="summary-label">{{ ($closingBalance ?? 0) >= 0 ? 'DR' : 'CR' }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Transactions</div>
            <div class="summary-amount">{{ count($transactionsWithBalance ?? []) }}</div>
            <div class="summary-label">Total</div>
        </div>
    </div>

    <!-- Transactions Table -->
    @if(isset($transactionsWithBalance) && count($transactionsWithBalance) > 0)
    <table class="transactions-table">
        <thead>
            <tr>
                <th style="width: 10%;">Date</th>
                <th style="width: 30%;">Particulars</th>
                <th style="width: 10%;">Vch Type</th>
                <th style="width: 12%;">Vch No.</th>
                <th style="width: 12%;" class="amount">Debit (₦)</th>
                <th style="width: 12%;" class="amount">Credit (₦)</th>
                <th style="width: 14%;" class="amount">Balance (₦)</th>
            </tr>
        </thead>
        <tbody>
            <!-- Opening Balance Row -->
            <tr class="opening-row">
                <td>{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}</td>
                <td colspan="3">Opening Balance</td>
                <td class="amount">-</td>
                <td class="amount">-</td>
                <td class="amount">{{ number_format(abs($openingBalanceAmount ?? 0), 2) }} {{ ($openingBalanceAmount ?? 0) >= 0 ? 'DR' : 'CR' }}</td>
            </tr>

            <!-- Transaction Rows -->
            @foreach($transactionsWithBalance as $transaction)
            <tr>
                <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('M d, Y') }}</td>
                <td>{{ $transaction['particulars'] }}</td>
                <td>{{ $transaction['voucher_type'] }}</td>
                <td style="font-family: 'Courier New', monospace;">{{ $transaction['voucher_number'] }}</td>
                <td class="amount">{{ $transaction['debit'] > 0 ? number_format($transaction['debit'], 2) : '-' }}</td>
                <td class="amount">{{ $transaction['credit'] > 0 ? number_format($transaction['credit'], 2) : '-' }}</td>
                <td class="amount"><strong>{{ number_format(abs($transaction['running_balance']), 2) }} {{ $transaction['running_balance'] >= 0 ? 'DR' : 'CR' }}</strong></td>
            </tr>
            @endforeach

            <!-- Closing Balance Row -->
            <tr class="closing-row">
                <td colspan="4">Closing Balance</td>
                <td class="amount">{{ number_format($totalDebits ?? 0, 2) }}</td>
                <td class="amount">{{ number_format($totalCredits ?? 0, 2) }}</td>
                <td class="amount">{{ number_format(abs($closingBalance ?? 0), 2) }} {{ ($closingBalance ?? 0) >= 0 ? 'DR' : 'CR' }}</td>
            </tr>
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 40px; color: #999;">
        <p>No transactions found for the selected period.</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This is a system-generated statement. For official bank statements, please contact your bank directly.</p>
        <p style="margin-top: 10px;">Generated on {{ now()->format('F d, Y \a\t h:i A') }} | {{ $tenant->name }}</p>
    </div>

    <script>
        window.onafterprint = function() {
            // Uncomment to auto-close after printing
            // window.close();
        }
    </script>
</body>
</html>
