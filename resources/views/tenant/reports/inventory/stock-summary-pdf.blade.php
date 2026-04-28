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

    $showValues = $showValues ?? true;
    $valueModeLabel = $showValues ? 'With Values' : 'Quantity Only';
    $asOfLabel = \Carbon\Carbon::parse($asOfDate)->format('d M Y');
    $rows = $allProducts ?? collect();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Summary - {{ $asOfLabel }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9.5px;
            color: #111827;
            margin: 0;
            padding: 16px 20px;
        }
        .header {
            border-bottom: 2px solid #0f766e;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: top; }
        .logo { max-height: 50px; max-width: 150px; margin-bottom: 4px; }
        .company-name { font-size: 17px; font-weight: bold; color: #0f766e; margin-bottom: 2px; }
        .company-meta { color: #4b5563; line-height: 1.4; font-size: 9px; }
        .report-title { text-align: right; font-size: 17px; font-weight: bold; text-transform: uppercase; color: #111827; }
        .report-subtitle { text-align: right; color: #4b5563; margin-top: 3px; font-size: 9.5px; }
        .summary-grid { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .summary-grid td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            vertical-align: top;
            width: 20%;
        }
        .label { color: #6b7280; font-size: 8px; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 2px; }
        .value { color: #111827; font-size: 11px; font-weight: bold; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.data th, table.data td {
            border: 1px solid #d1d5db;
            padding: 5px 6px;
            font-size: 9px;
        }
        table.data thead th {
            background: #f3f4f6;
            color: #111827;
            text-transform: uppercase;
            font-size: 8.5px;
            letter-spacing: .3px;
            text-align: left;
        }
        table.data td.num, table.data th.num { text-align: right; }
        table.data td.center, table.data th.center { text-align: center; }
        table.data tfoot td { background: #f9fafb; font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
            border: 1px solid transparent;
        }
        .badge-in { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
        .badge-low { background: #fef3c7; color: #92400e; border-color: #fde68a; }
        .badge-out { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
        .footer {
            position: fixed;
            bottom: 8px;
            left: 20px;
            right: 20px;
            font-size: 8px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 4px;
            text-align: center;
        }
        .muted { color: #6b7280; font-size: 8.5px; }
    </style>
</head>
<body>

<div class="header">
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                @if($hasLogo)
                    <img src="{{ $logoFullPath }}" alt="Logo" class="logo">
                @endif
                <div class="company-name">{{ $companyName }}</div>
                <div class="company-meta">
                    @if($companyAddress){{ $companyAddress }}<br>@endif
                    @if($companyPhone)Tel: {{ $companyPhone }}@endif
                    @if($companyPhone && $companyEmail) | @endif
                    @if($companyEmail){{ $companyEmail }}@endif
                </div>
            </td>
            <td style="width: 40%;">
                <div class="report-title">Stock Summary</div>
                <div class="report-subtitle">As of {{ $asOfLabel }}</div>
                <div class="report-subtitle">{{ $valueModeLabel }}</div>
                @if(!empty($selectedLocation))
                    <div class="report-subtitle">Location: {{ $selectedLocation->name }}</div>
                @endif
                <div class="report-subtitle muted">Generated {{ now()->format('d M Y, H:i') }}</div>
            </td>
        </tr>
    </table>
</div>

<table class="summary-grid">
    <tr>
        <td>
            <div class="label">Total Products</div>
            <div class="value">{{ number_format($totalProducts) }}</div>
        </td>
        <td>
            <div class="label">Total Quantity</div>
            <div class="value">{{ number_format($totalStockQuantity, 2) }}</div>
        </td>
        @if($showValues)
        <td>
            <div class="label">Stock Value (NGN)</div>
            <div class="value">{{ number_format($totalStockValue, 2) }}</div>
        </td>
        <td>
            <div class="label">Sales Value (NGN)</div>
            <div class="value">{{ number_format($totalSalesValue, 2) }}</div>
        </td>
        @endif
        <td>
            <div class="label">Low / Out of Stock</div>
            <div class="value">{{ number_format($lowStockCount) }} / {{ number_format($outOfStockCount) }}</div>
        </td>
    </tr>
</table>

<table class="data">
    <thead>
        <tr>
            <th style="width: 4%;" class="center">#</th>
            <th style="width: {{ $showValues ? '14%' : '18%' }};">Category</th>
            <th style="width: {{ $showValues ? '20%' : '32%' }};">Product</th>
            <th style="width: 8%;" class="num">Stock</th>
            <th style="width: 6%;" class="center">Unit</th>
            @if($showValues)
                <th style="width: 10%;" class="num">Purch. Rate (NGN)</th>
                <th style="width: 10%;" class="num">Sales Rate (NGN)</th>
                <th style="width: 11%;" class="num">Purch. Value (NGN)</th>
                <th style="width: 11%;" class="num">Sales Value (NGN)</th>
            @endif
            <th style="width: 8%;" class="num">Reorder</th>
            <th style="width: {{ $showValues ? '8%' : '14%' }};" class="center">Status</th>
        </tr>
    </thead>
    <tbody>
    @forelse($rows as $i => $product)
        <tr>
            <td class="center">{{ $i + 1 }}</td>
            <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
            <td>
                <strong>{{ $product->name }}</strong>
                @if($product->sku)<br><span class="muted">SKU: {{ $product->sku }}</span>@endif
            </td>
            <td class="num">{{ number_format($product->calculated_stock, 2) }}</td>
            <td class="center">{{ $product->primaryUnit?->symbol ?? $product->primaryUnit?->name ?? '-' }}</td>
            @if($showValues)
                <td class="num">{{ number_format($product->purchase_rate ?? 0, 2) }}</td>
                <td class="num">{{ number_format($product->sales_rate ?? 0, 2) }}</td>
                <td class="num">{{ number_format($product->calculated_stock * ($product->purchase_rate ?? 0), 2) }}</td>
                <td class="num">{{ number_format($product->calculated_stock * ($product->sales_rate ?? 0), 2) }}</td>
            @endif
            <td class="num">{{ $product->reorder_level ? number_format($product->reorder_level, 2) : '-' }}</td>
            <td class="center">
                @if($product->status_flag === 'out_of_stock')
                    <span class="badge badge-out">Out</span>
                @elseif($product->status_flag === 'low_stock')
                    <span class="badge badge-low">Low</span>
                @else
                    <span class="badge badge-in">In Stock</span>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="{{ $showValues ? 11 : 7 }}" class="center muted" style="padding: 20px;">
                No stock data available for the selected filters.
            </td>
        </tr>
    @endforelse
    </tbody>
    @if($rows->count() > 0)
    <tfoot>
        <tr>
            <td colspan="4" class="num">Totals</td>
            <td class="num">{{ number_format($totalStockQuantity, 2) }}</td>
            <td></td>
            @if($showValues)
                <td></td>
                <td></td>
                <td class="num">{{ number_format($totalStockValue, 2) }}</td>
                <td class="num">{{ number_format($totalSalesValue, 2) }}</td>
            @endif
            <td></td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="footer">
    {{ $companyName }} | Stock Summary as of {{ $asOfLabel }} | {{ $valueModeLabel }} | Page <span class="pagenum"></span>
</div>

</body>
</html>
