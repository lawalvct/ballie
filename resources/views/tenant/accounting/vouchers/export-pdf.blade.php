<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vouchers Export</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #111827; }
        h1 { text-align: center; font-size: 18px; margin: 0 0 4px; }
        .meta { text-align: center; color: #6b7280; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; font-weight: bold; }
        .right { text-align: right; }
        .status { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Vouchers</h1>
    <div class="meta">
        {{ $tenant->name }} &nbsp;|&nbsp; Exported {{ now()->format('M d, Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Voucher #</th>
                <th>Type</th>
                <th>Date</th>
                <th>Reference</th>
                <th>First Particular</th>
                <th>Ledger Account</th>
                <th class="right">Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vouchers as $voucher)
                @php
                    $firstEntry = $voucher->entries->first();
                    $firstParticular = $firstEntry?->particulars ?: ($firstEntry?->ledgerAccount?->name ?? '');
                @endphp
                <tr>
                    <td>{{ $voucher->voucher_number }}</td>
                    <td>{{ $voucher->voucherType?->name ?? 'N/A' }}</td>
                    <td>{{ optional($voucher->voucher_date)->format('Y-m-d') }}</td>
                    <td>{{ $voucher->reference_number ?? '' }}</td>
                    <td>{{ $firstParticular }}</td>
                    <td>{{ $firstEntry?->ledgerAccount?->name ?? '' }}</td>
                    <td class="right">₦{{ number_format($voucher->total_amount, 2) }}</td>
                    <td class="status">{{ ucfirst($voucher->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
