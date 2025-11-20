<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash Flow Statement</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .amount { text-align: right; font-family: monospace; }
        .section-title { background-color: #e8e8e8; font-weight: bold; padding: 10px; margin-top: 15px; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .net-cash { background-color: #e8f5e9; font-weight: bold; font-size: 14px; }
        .summary-box { border: 2px solid #333; padding: 15px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tenant->name }}</h1>
        <h2>Cash Flow Statement</h2>
        <p>For the period from {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}</p>
    </div>

    <div class="section-title">CASH FLOWS FROM OPERATING ACTIVITIES</div>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Type</th>
                <th class="amount">Amount (₦)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($operatingActivities as $activity)
            <tr>
                <td>{{ $activity['description'] }}</td>
                <td>{{ ucfirst($activity['type']) }}</td>
                <td class="amount">{{ number_format($activity['amount'], 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; color: #999;">No operating activities</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="2">Net Cash from Operating Activities</td>
                <td class="amount">{{ number_format($operatingTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">CASH FLOWS FROM INVESTING ACTIVITIES</div>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Type</th>
                <th class="amount">Amount (₦)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($investingActivities as $activity)
            <tr>
                <td>{{ $activity['description'] }}</td>
                <td>Investing</td>
                <td class="amount">{{ number_format($activity['amount'], 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; color: #999;">No investing activities</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="2">Net Cash from Investing Activities</td>
                <td class="amount">{{ number_format($investingTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">CASH FLOWS FROM FINANCING ACTIVITIES</div>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Type</th>
                <th class="amount">Amount (₦)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($financingActivities as $activity)
            <tr>
                <td>{{ $activity['description'] }}</td>
                <td>{{ ucfirst($activity['type']) }}</td>
                <td class="amount">{{ number_format($activity['amount'], 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; color: #999;">No financing activities</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="2">Net Cash from Financing Activities</td>
                <td class="amount">{{ number_format($financingTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="summary-box">
        <table>
            <tr>
                <td><strong>Cash at Beginning of Period</strong></td>
                <td class="amount"><strong>{{ number_format($openingCash, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Net Cash from Operating Activities</td>
                <td class="amount">{{ number_format($operatingTotal, 2) }}</td>
            </tr>
            <tr>
                <td>Net Cash from Investing Activities</td>
                <td class="amount">{{ number_format($investingTotal, 2) }}</td>
            </tr>
            <tr>
                <td>Net Cash from Financing Activities</td>
                <td class="amount">{{ number_format($financingTotal, 2) }}</td>
            </tr>
            <tr class="net-cash">
                <td><strong>Net Increase (Decrease) in Cash</strong></td>
                <td class="amount"><strong>{{ number_format($netCashFlow, 2) }}</strong></td>
            </tr>
            <tr class="total-row">
                <td><strong>Cash at End of Period</strong></td>
                <td class="amount"><strong>{{ number_format($closingCash, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 30px; text-align: center; color: #666; font-size: 10px;">
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>
