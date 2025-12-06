<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balance Sheet - {{ $tenant->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .company-name { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
        .report-title { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .report-date { font-size: 12px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f3f4f6; padding: 10px; text-align: left; font-weight: bold; border-bottom: 2px solid #333; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        .section-header { background-color: #e5e7eb; font-weight: bold; padding: 10px; margin-top: 20px; }
        .total-row { font-weight: bold; background-color: #f9fafb; border-top: 2px solid #333; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #ccc; font-size: 10px; text-align: center; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $tenant->name }}</div>
        <div class="report-title">Balance Sheet</div>
        <div class="report-date">As of {{ \Carbon\Carbon::parse($asOfDate)->format('F d, Y') }}</div>
    </div>

    <div class="section-header">ASSETS</div>
    <table>
        <thead>
            <tr>
                <th>Account</th>
                <th class="text-right">Amount (₦)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $item)
            <tr>
                <td>{{ $item['account']->name }}</td>
                <td class="text-right">{{ number_format($item['balance'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>Total Assets</td>
                <td class="text-right">{{ number_format($totalAssets, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-header">LIABILITIES</div>
    <table>
        <thead>
            <tr>
                <th>Account</th>
                <th class="text-right">Amount (₦)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($liabilities as $item)
            <tr>
                <td>{{ $item['account']->name }}</td>
                <td class="text-right">{{ number_format($item['balance'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>Total Liabilities</td>
                <td class="text-right">{{ number_format($totalLiabilities, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-header">OWNER'S EQUITY</div>
    <table>
        <thead>
            <tr>
                <th>Account</th>
                <th class="text-right">Amount (₦)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equity as $item)
            <tr>
                <td>{{ $item['account']->name }}</td>
                <td class="text-right">{{ number_format($item['balance'], 2) }}</td>
            </tr>
            @endforeach
            @if(isset($retainedEarnings) && abs($retainedEarnings) >= 0.01)
            <tr>
                <td>Retained Earnings</td>
                <td class="text-right">{{ number_format($retainedEarnings, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total Equity</td>
                <td class="text-right">{{ number_format($totalEquity, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table style="margin-top: 30px;">
        <tr class="total-row">
            <td>Total Liabilities + Equity</td>
            <td class="text-right">{{ number_format($totalLiabilities + $totalEquity, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F d, Y h:i A') }} | {{ $tenant->name }}
    </div>
</body>
</html>
