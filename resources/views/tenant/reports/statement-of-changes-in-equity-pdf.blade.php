<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statement of Changes in Equity</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; margin: 0; padding: 18px; color: #111; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 12px; }
        .company-name { font-size: 18px; font-weight: bold; }
        .report-title { font-size: 14px; font-weight: bold; margin-top: 4px; }
        .report-period { font-size: 11px; margin-top: 4px; }
        .meta { margin: 10px 0 16px 0; font-size: 10px; color: #444; display: flex; justify-content: space-between; }
        .summary { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .summary td { border: 1px solid #bbb; padding: 8px; width: 25%; vertical-align: top; }
        .summary .label { font-size: 9px; text-transform: uppercase; color: #555; letter-spacing: 0.5px; }
        .summary .value { font-size: 13px; font-weight: bold; margin-top: 3px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.data th, table.data td { border: 1px solid #999; padding: 6px 8px; }
        table.data th { background: #f0f0f0; font-size: 10px; text-align: left; }
        table.data td.num, table.data th.num { text-align: right; white-space: nowrap; }
        .section-title { font-size: 12px; font-weight: bold; margin: 14px 0 6px 0; padding-bottom: 3px; border-bottom: 1px solid #ccc; }
        .row-retained { background: #eef4ff; }
        .row-total { background: #111; color: #fff; font-weight: bold; }
        .row-total td { border-color: #111; }
        .recon { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .recon td { border: 1px solid #bbb; padding: 7px 9px; font-size: 11px; }
        .recon .grand { background: #111; color: #fff; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #ddd; padding-top: 8px; }
        .pos { color: #0a7c3a; }
        .neg { color: #b00020; }
        .muted { color: #777; }
    </style>
</head>
@php
    $fmt = function ($amount) {
        $sign = $amount < 0 ? '-' : '';
        return $sign . 'NGN ' . number_format(abs($amount), 2);
    };
@endphp
<body>
    <div class="header">
        <div class="company-name">{{ $tenant->name ?? 'Company' }}</div>
        <div class="report-title">STATEMENT OF CHANGES IN EQUITY</div>
        <div class="report-period">
            For the Period from {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }}
            to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}
        </div>
    </div>

    <div class="meta">
        <div><strong>Report Date:</strong> {{ now()->format('F d, Y') }}</div>
        <div><strong>Opening As Of:</strong> {{ \Carbon\Carbon::parse($openingDate)->format('F d, Y') }}</div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Opening Equity</div>
                <div class="value">{{ $fmt($totalOpeningEquity) }}</div>
            </td>
            <td>
                <div class="label">Direct Equity Movement</div>
                <div class="value {{ $totalDirectEquityMovement >= 0 ? 'pos' : 'neg' }}">{{ $fmt($totalDirectEquityMovement) }}</div>
            </td>
            <td>
                <div class="label">Profit / Loss for Period</div>
                <div class="value {{ $profitForPeriod >= 0 ? 'pos' : 'neg' }}">{{ $fmt($profitForPeriod) }}</div>
            </td>
            <td>
                <div class="label">Closing Equity</div>
                <div class="value">{{ $fmt($totalClosingEquity) }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Statement Details</div>
    <table class="data">
        <thead>
            <tr>
                <th>Description</th>
                <th class="num">Opening</th>
                <th class="num">Additions</th>
                <th class="num">Deductions</th>
                <th class="num">Closing</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equityMovements as $movement)
                <tr>
                    <td>
                        <strong>{{ $movement['account']->name }}</strong>
                        <div class="muted">{{ $movement['account']->code }}{{ $movement['account']->accountGroup ? ' · ' . $movement['account']->accountGroup->name : '' }}</div>
                    </td>
                    <td class="num">{{ $fmt($movement['opening_balance']) }}</td>
                    <td class="num pos">{{ $movement['additions'] > 0 ? $fmt($movement['additions']) : '-' }}</td>
                    <td class="num neg">{{ $movement['deductions'] > 0 ? $fmt($movement['deductions']) : '-' }}</td>
                    <td class="num"><strong>{{ $fmt($movement['closing_balance']) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding: 14px; color:#777;">
                        No direct equity account movement found for this period.
                    </td>
                </tr>
            @endforelse

            <tr class="row-retained">
                <td>
                    <strong>Retained Earnings / Current Period Result</strong>
                    <div class="muted">Opening retained earnings plus profit or loss for this period</div>
                </td>
                <td class="num"><strong>{{ $fmt($openingRetainedEarnings) }}</strong></td>
                <td class="num pos">{{ $profitForPeriod > 0 ? $fmt($profitForPeriod) : '-' }}</td>
                <td class="num neg">{{ $profitForPeriod < 0 ? $fmt(abs($profitForPeriod)) : '-' }}</td>
                <td class="num"><strong>{{ $fmt($closingRetainedEarnings) }}</strong></td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="row-total">
                <td>Total Equity</td>
                <td class="num">{{ $fmt($totalOpeningEquity) }}</td>
                <td class="num">{{ $fmt($totalEquityAdditions + max($profitForPeriod, 0)) }}</td>
                <td class="num">{{ $fmt($totalEquityDeductions + abs(min($profitForPeriod, 0))) }}</td>
                <td class="num">{{ $fmt($totalClosingEquity) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">Equity Reconciliation</div>
    <table class="recon">
        <tr>
            <td>Opening Equity</td>
            <td class="num" style="text-align:right;"><strong>{{ $fmt($totalOpeningEquity) }}</strong></td>
        </tr>
        <tr>
            <td>Direct Equity Movement</td>
            <td class="num {{ $totalDirectEquityMovement >= 0 ? 'pos' : 'neg' }}" style="text-align:right;"><strong>{{ $fmt($totalDirectEquityMovement) }}</strong></td>
        </tr>
        <tr>
            <td>Profit / Loss for Period</td>
            <td class="num {{ $profitForPeriod >= 0 ? 'pos' : 'neg' }}" style="text-align:right;"><strong>{{ $fmt($profitForPeriod) }}</strong></td>
        </tr>
        <tr class="grand">
            <td>Closing Equity</td>
            <td class="num" style="text-align:right;">{{ $fmt($totalClosingEquity) }}</td>
        </tr>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F d, Y H:i') }} · {{ $tenant->name ?? '' }}
    </div>
</body>
</html>
