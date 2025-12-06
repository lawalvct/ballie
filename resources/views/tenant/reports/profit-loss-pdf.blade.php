<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss Statement</title>
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
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .section-header {
            background-color: #e8f5e9;
            font-weight: bold;
        }
        .section-header-expense {
            background-color: #ffebee;
            font-weight: bold;
        }
        .totals-row {
            background-color: #f9f9f9;
            font-weight: bold;
            border-top: 2px solid #000;
        }
        .net-profit {
            background-color: #e3f2fd;
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .summary-box {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $tenant->name ?? 'Company Name' }}</div>
        <div class="report-title">PROFIT & LOSS STATEMENT</div>
        <div class="report-date">
            From {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}
        </div>
    </div>

    <!-- Summary Box -->
    <div class="summary-box">
        <table style="border: none; width: 100%;">
            <tr>
                <td style="border: none;"><strong>Total Income:</strong></td>
                <td style="border: none;" class="text-right">₦{{ number_format($totalIncome, 2) }}</td>
                <td style="border: none;"><strong>Total Expenses:</strong></td>
                <td style="border: none;" class="text-right">₦{{ number_format($totalExpenses, 2) }}</td>
            </tr>
            <tr>
                <td style="border: none;"><strong>Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}:</strong></td>
                <td style="border: none;" class="text-right">₦{{ number_format(abs($netProfit), 2) }}</td>
                <td style="border: none;"><strong>Profit Margin:</strong></td>
                <td style="border: none;" class="text-right">
                    @if($totalIncome > 0)
                        {{ number_format(($netProfit / $totalIncome) * 100, 2) }}%
                    @else
                        0.00%
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Income Section -->
    <table>
        <thead>
            <tr class="section-header">
                <th colspan="2">INCOME</th>
            </tr>
            <tr>
                <th>Account Name</th>
                <th class="text-right">Amount (₦)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incomeData as $item)
                <tr>
                    <td>{{ $item['account']->name }}</td>
                    <td class="text-right">{{ number_format($item['amount'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center; font-style: italic;">No income recorded</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td class="font-bold">Total Income</td>
                <td class="text-right font-bold">{{ number_format($totalIncome, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Expenses Section -->
    <table style="margin-top: 30px;">
        <thead>
            <tr class="section-header-expense">
                <th colspan="2">EXPENSES</th>
            </tr>
            <tr>
                <th>Account Name</th>
                <th class="text-right">Amount (₦)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenseData as $item)
                <tr>
                    <td>{{ $item['account']->name }}</td>
                    <td class="text-right">{{ number_format($item['amount'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center; font-style: italic;">No expenses recorded</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td class="font-bold">Total Expenses</td>
                <td class="text-right font-bold">{{ number_format($totalExpenses, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Net Profit/Loss -->
    <table style="margin-top: 30px;">
        <tr class="net-profit">
            <td class="font-bold">NET {{ $netProfit >= 0 ? 'PROFIT' : 'LOSS' }}</td>
            <td class="text-right font-bold">₦{{ number_format(abs($netProfit), 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F d, Y \a\t g:i A') }} | {{ $tenant->name ?? 'Company Name' }}
    </div>
</body>
</html>
