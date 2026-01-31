<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoices Export</title>
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
    <h1>Invoices</h1>
    <div class="meta">
        {{ $tenant->name }} &nbsp;|&nbsp; Exported {{ now()->format('M d, Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Type</th>
                <th>Date</th>
                <th>Customer/Vendor</th>
                <th>Reference</th>
                <th>Description</th>
                <th class="right">Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                @php
                    $displayNumber = ($invoice->voucherType?->prefix ?? $invoice->voucherType?->abbreviation ?? '') . $invoice->voucher_number;
                    $type = ($invoice->voucherType?->inventory_effect ?? '') === 'increase' ? 'Purchase' : 'Sales';
                    $partyEntry = $invoice->entries->where('debit_amount', '>', 0)->first();
                    $partyName = $partyEntry?->ledgerAccount?->name ?? 'Cash Sale';
                @endphp
                <tr>
                    <td>{{ $displayNumber }}</td>
                    <td>{{ $type }}</td>
                    <td>{{ optional($invoice->voucher_date)->format('Y-m-d') }}</td>
                    <td>{{ $partyName }}</td>
                    <td>{{ $invoice->reference_number ?? '' }}</td>
                    <td>{{ $invoice->narration ?? '' }}</td>
                    <td class="right">₦{{ number_format($invoice->total_amount, 2) }}</td>
                    <td class="status">{{ ucfirst($invoice->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
