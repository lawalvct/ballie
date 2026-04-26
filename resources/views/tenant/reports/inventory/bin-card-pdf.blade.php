@php
    $companyName = $tenant->company_name ?? $tenant->name ?? 'Company Name';
    $companyEmail = $tenant->email ?? '';
    $companyPhone = $tenant->phone ?? '';
    $companyAddress = $tenant->address ?? '';
    $logoPath = $tenant->logo ?? null;
    $logoFullPath = null;

    if ($logoPath) {
        $normalizedLogoPath = ltrim($logoPath, '/');
        $logoFullPath = strpos($normalizedLogoPath, 'storage/') === 0
            ? public_path($normalizedLogoPath)
            : public_path('storage/' . $normalizedLogoPath);
    }

    $hasLogo = $logoFullPath && file_exists($logoFullPath);
    $productName = $product?->name ?? 'No product selected';
    $productSku = $product?->sku ?? 'N/A';
    $productCategory = $product?->category?->name ?? 'Uncategorized';
    $productUnit = $product?->primaryUnit?->symbol ?? $product?->primaryUnit?->name ?? 'Unit';
    $periodLabel = \Carbon\Carbon::parse($fromDate)->format('d M Y') . ' to ' . \Carbon\Carbon::parse($toDate)->format('d M Y');
    $showValues = $showValues ?? true;
    $valueModeLabel = $showValues ? 'With Values' : 'Quantity Only';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bin Card - {{ $productName }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #111827;
            margin: 0;
            padding: 18px 22px;
        }
        .header {
            border-bottom: 2px solid #0f766e;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: top; }
        .logo { max-height: 54px; max-width: 160px; margin-bottom: 6px; }
        .company-name { font-size: 18px; font-weight: bold; color: #0f766e; margin-bottom: 3px; }
        .company-meta { color: #4b5563; line-height: 1.45; }
        .report-title { text-align: right; font-size: 18px; font-weight: bold; text-transform: uppercase; color: #111827; }
        .report-subtitle { text-align: right; color: #4b5563; margin-top: 4px; }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin: 12px 0 6px;
        }
        .info-grid, .summary-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-grid td, .summary-grid td {
            border: 1px solid #d1d5db;
            padding: 7px 8px;
            vertical-align: top;
        }
        .label {
            color: #6b7280;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 2px;
        }
        .value {
            color: #111827;
            font-size: 10px;
            font-weight: bold;
        }
        .value-green { color: #047857; }
        .value-red { color: #b91c1c; }
        table.ledger {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.ledger th {
            background: #0f766e;
            color: #fff;
            padding: 6px 5px;
            font-size: 8px;
            text-align: left;
            text-transform: uppercase;
        }
        table.ledger td {
            border-bottom: 1px solid #e5e7eb;
            padding: 5px;
            vertical-align: top;
            font-size: 8.5px;
        }
        table.ledger tr:nth-child(even) td { background: #f9fafb; }
        table.ledger .opening td { background: #ecfeff; font-weight: bold; }
        table.ledger .totals td { background: #f3f4f6; font-weight: bold; border-top: 1px solid #9ca3af; }
        table.ledger .remark-cell { border-left: 1px solid #e5e7eb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .muted { color: #6b7280; }
        .inward { color: #047857; }
        .outward { color: #b91c1c; }
        .footer {
            position: fixed;
            left: 22px;
            right: 22px;
            bottom: 12px;
            border-top: 1px solid #d1d5db;
            padding-top: 5px;
            text-align: center;
            color: #6b7280;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 58%;">
                    @if($hasLogo)
                        <img src="{{ $logoFullPath }}" alt="Logo" class="logo">
                    @endif
                    <div class="company-name">{{ $companyName }}</div>
                    <div class="company-meta">
                        @if($companyAddress) {{ $companyAddress }}<br> @endif
                        @if($companyPhone) Tel: {{ $companyPhone }} @endif
                        @if($companyEmail) @if($companyPhone) &nbsp;|&nbsp; @endif {{ $companyEmail }} @endif
                    </div>
                </td>
                <td style="width: 42%;">
                    <div class="report-title">Bin Card</div>
                    <div class="report-subtitle">Inventory Ledger | {{ $periodLabel }} | {{ $valueModeLabel }}</div>
                    <div class="report-subtitle">Generated {{ now()->format('d M Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Product Information</div>
    <table class="info-grid">
        <tr>
            <td style="width: 32%;">
                <div class="label">Product</div>
                <div class="value">{{ $productName }}</div>
            </td>
            <td style="width: 17%;">
                <div class="label">SKU</div>
                <div class="value">{{ $productSku }}</div>
            </td>
            <td style="width: 21%;">
                <div class="label">Category</div>
                <div class="value">{{ $productCategory }}</div>
            </td>
            <td style="width: 13%;">
                <div class="label">Unit</div>
                <div class="value">{{ $productUnit }}</div>
            </td>
            <td style="width: 17%;">
                <div class="label">Movements</div>
                <div class="value">{{ number_format($transactionCount) }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Stock Flow Summary</div>
    <table class="summary-grid">
        <tr>
            <td style="width: 20%;">
                <div class="label">Opening</div>
                <div class="value">{{ number_format($openingQty, 2) }} {{ $productUnit }}</div>
                @if($showValues)
                    <div class="muted">Value (NGN): {{ number_format($openingValue, 2) }}</div>
                @endif
            </td>
            <td style="width: 20%;">
                <div class="label">Inwards</div>
                <div class="value value-green">{{ number_format($totalInQty, 2) }} {{ $productUnit }}</div>
                @if($showValues)
                    <div class="muted">Value (NGN): {{ number_format($totalInValue, 2) }}</div>
                @endif
            </td>
            <td style="width: 20%;">
                <div class="label">Outwards</div>
                <div class="value value-red">{{ number_format($totalOutQty, 2) }} {{ $productUnit }}</div>
                @if($showValues)
                    <div class="muted">Value (NGN): {{ number_format($totalOutValue, 2) }}</div>
                @endif
            </td>
            <td style="width: 20%;">
                <div class="label">Net Movement</div>
                <div class="value {{ $netMovementQty < 0 ? 'value-red' : 'value-green' }}">{{ number_format($netMovementQty, 2) }} {{ $productUnit }}</div>
                @if($showValues)
                    <div class="muted">Value (NGN): {{ number_format($netMovementValue, 2) }}</div>
                @endif
            </td>
            <td style="width: 20%;">
                <div class="label">Closing</div>
                <div class="value">{{ number_format($closingQty, 2) }} {{ $productUnit }}</div>
                @if($showValues)
                    <div class="muted">Value (NGN): {{ number_format($closingValue, 2) }}</div>
                @endif
            </td>
        </tr>
    </table>

    <div class="section-title">Movement Ledger</div>
    <table class="ledger">
        <thead>
            <tr>
                <th style="width: {{ $showValues ? '7%' : '9%' }};">Date</th>
                <th style="width: {{ $showValues ? '19%' : '28%' }};">Particulars</th>
                <th style="width: {{ $showValues ? '8%' : '10%' }};">Vch Type</th>
                <th style="width: {{ $showValues ? '8%' : '10%' }};">Vch No.</th>
                <th style="width: {{ $showValues ? '7%' : '10%' }};" class="text-right">In Qty</th>
                @if($showValues)
                    <th style="width: 8%;" class="text-right">In Value (NGN)</th>
                @endif
                <th style="width: {{ $showValues ? '7%' : '10%' }};" class="text-right">Out Qty</th>
                @if($showValues)
                    <th style="width: 8%;" class="text-right">Out Value (NGN)</th>
                @endif
                <th style="width: {{ $showValues ? '8%' : '10%' }};" class="text-right">Closing Qty</th>
                @if($showValues)
                    <th style="width: 8%;" class="text-right">Closing Value (NGN)</th>
                @endif
                <th style="width: {{ $showValues ? '12%' : '13%' }};">Remark</th>
            </tr>
        </thead>
        <tbody>
            <tr class="opening">
                <td>{{ \Carbon\Carbon::parse($fromDate)->subDay()->format('d-M-Y') }}</td>
                <td>Opening Balance</td>
                <td>-</td>
                <td>-</td>
                <td class="text-right">{{ number_format($openingQty, 2) }}</td>
                @if($showValues)
                    <td class="text-right">{{ number_format($openingValue, 2) }}</td>
                @endif
                <td class="text-right">-</td>
                @if($showValues)
                    <td class="text-right">-</td>
                @endif
                <td class="text-right">{{ number_format($openingQty, 2) }}</td>
                @if($showValues)
                    <td class="text-right">{{ number_format($openingValue, 2) }}</td>
                @endif
                <td class="remark-cell">&nbsp;</td>
            </tr>

            @forelse($rows as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->date)->format('d-M-Y') }}</td>
                    <td>{{ $row->particulars }}</td>
                    <td>{{ $row->vch_type }}</td>
                    <td>{{ $row->vch_no }}</td>
                    <td class="text-right inward">{{ $row->in_qty ? number_format($row->in_qty, 2) : '-' }}</td>
                    @if($showValues)
                        <td class="text-right inward">{{ $row->in_value ? number_format($row->in_value, 2) : '-' }}</td>
                    @endif
                    <td class="text-right outward">{{ $row->out_qty ? number_format($row->out_qty, 2) : '-' }}</td>
                    @if($showValues)
                        <td class="text-right outward">{{ $row->out_value ? number_format($row->out_value, 2) : '-' }}</td>
                    @endif
                    <td class="text-right">{{ number_format($row->closing_qty, 2) }}</td>
                    @if($showValues)
                        <td class="text-right">{{ number_format($row->closing_value, 2) }}</td>
                    @endif
                    <td class="remark-cell">&nbsp;</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showValues ? 11 : 8 }}" class="text-center muted" style="padding: 18px;">No movements found for the selected period.</td>
                </tr>
            @endforelse

            <tr class="totals">
                <td colspan="4" class="text-right">Totals</td>
                <td class="text-right inward">{{ number_format($totalInQty, 2) }}</td>
                @if($showValues)
                    <td class="text-right inward">{{ number_format($totalInValue, 2) }}</td>
                @endif
                <td class="text-right outward">{{ number_format($totalOutQty, 2) }}</td>
                @if($showValues)
                    <td class="text-right outward">{{ number_format($totalOutValue, 2) }}</td>
                @endif
                <td class="text-right">{{ number_format($closingQty, 2) }}</td>
                @if($showValues)
                    <td class="text-right">{{ number_format($closingValue, 2) }}</td>
                @endif
                <td class="remark-cell">&nbsp;</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        {{ $companyName }} | Bin Card for {{ $productName }} | Powered by Ballie
    </div>
</body>
</html>
