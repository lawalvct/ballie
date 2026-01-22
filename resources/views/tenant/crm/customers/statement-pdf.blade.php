<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Statement</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        .header { margin-bottom: 16px; }
        .header h1 { margin: 0 0 6px 0; font-size: 18px; }
        .meta { font-size: 11px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px; }
        th { background: #f3f4f6; text-align: left; }
        .right { text-align: right; }
        .summary { margin-top: 10px; }
        .summary td { border: none; padding: 3px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Customer Statement</h1>
        <div class="meta">
            <div>Company: {{ $tenant->name }}</div>
            <div>Customer: {{ $customer->display_name ?? $customer->company_name ?? $customer->email }}</div>
            <div>Period: {{ $period['start_date'] }} to {{ $period['end_date'] }}</div>
        </div>
    </div>

    <table class="summary">
        <tr>
            <td>Opening Balance:</td>
            <td class="right">₦{{ number_format($openingBalance, 2) }}</td>
        </tr>
        <tr>
            <td>Total Debits:</td>
            <td class="right">₦{{ number_format($totalDebits, 2) }}</td>
        </tr>
        <tr>
            <td>Total Credits:</td>
            <td class="right">₦{{ number_format($totalCredits, 2) }}</td>
        </tr>
        <tr>
            <td>Closing Balance:</td>
            <td class="right">₦{{ number_format($closingBalance, 2) }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Particulars</th>
                <th>Voucher Type</th>
                <th>Voucher No.</th>
                <th class="right">Debit (₦)</th>
                <th class="right">Credit (₦)</th>
                <th class="right">Running Balance (₦)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['particulars'] }}</td>
                    <td>{{ $row['voucher_type'] }}</td>
                    <td>{{ $row['voucher_number'] }}</td>
                    <td class="right">{{ number_format($row['debit'], 2) }}</td>
                    <td class="right">{{ number_format($row['credit'], 2) }}</td>
                    <td class="right">{{ number_format($row['running_balance'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No transactions found for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
