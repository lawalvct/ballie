@php
    $grandTotal   = $invoices->sum('total_amount');
    $totalCount   = $invoices->count();
    $filters      = $filters ?? [];

    // Resolve display labels for active filters
    $typeLabel = match($filters['type'] ?? 'all') {
        'sales'    => 'Sales Only',
        'purchase' => 'Purchase Only',
        default    => 'All Types',
    };
    $statusLabel    = ucfirst($filters['status'] ?? '');
    $dateFromLabel  = $filters['date_from'] ? \Carbon\Carbon::parse($filters['date_from'])->format('d M Y') : null;
    $dateToLabel    = $filters['date_to']   ? \Carbon\Carbon::parse($filters['date_to'])->format('d M Y')   : null;
    $searchLabel    = $filters['search'] ?? null;

    $activeFilters = [];
    $activeFilters['Company']     = $tenant->name;
    $activeFilters['Document Type'] = $typeLabel;
    if ($dateFromLabel || $dateToLabel)
        $activeFilters['Date Range'] = ($dateFromLabel ?? 'Start') . ' — ' . ($dateToLabel ?? 'End');
    if ($statusLabel)
        $activeFilters['Status'] = $statusLabel;
    if ($searchLabel)
        $activeFilters['Search'] = $searchLabel;
    $activeFilters['Exported On']  = now()->format('d M Y, g:i A');
    $activeFilters['Total Records'] = $totalCount . ' invoice' . ($totalCount !== 1 ? 's' : '');
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $term->label('sales_invoices') }} Export — {{ $tenant->name }}</title>
    <style>
        @page { margin: 10mm 8mm; size: A4 landscape; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #111827;
            margin: 0; padding: 0;
        }

        /* Header */
        .header-table { width: 100%; border-collapse: collapse; border-bottom: 3px solid #1e40af; padding-bottom: 8px; margin-bottom: 8px; }
        .header-table td { vertical-align: top; }

        .company-logo { width: 44px; height: 44px; margin-right: 8px; vertical-align: middle; }
        .company-name { font-size: 17px; font-weight: 700; color: #1e40af; text-transform: uppercase; }
        .company-meta { font-size: 9px; color: #6b7280; margin-top: 2px; line-height: 1.5; }

        .report-title { font-size: 15px; font-weight: 700; color: #ffffff; }
        .report-sub   { font-size: 9.5px; color: #bfdbfe; margin-top: 2px; }
        .title-box    { background: #1e40af; padding: 8px 12px; text-align: right; }

        /* Filters bar */
        .filters-bar {
            width: 100%;
            border-collapse: collapse;
            background: #f0f4ff;
            border: 1px solid #c7d2fe;
            margin-bottom: 8px;
        }
        .filters-bar td { padding: 4px 8px; font-size: 9px; vertical-align: top; }
        .filter-heading {
            font-size: 8.5px; font-weight: 700; color: #3730a3;
            text-transform: uppercase; letter-spacing: 0.5px;
            border-bottom: 1px solid #c7d2fe; padding: 3px 8px;
        }
        .fk { color: #6b7280; padding-right: 4px; white-space: nowrap; }
        .fv { font-weight: 700; color: #111827; }

        /* Data table */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 2px; }
        .data-table thead tr th {
            background: #1e40af;
            color: #fff;
            padding: 5px 5px;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: none;
        }
        .data-table tbody tr td {
            padding: 4px 5px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9.5px;
            vertical-align: top;
        }
        .data-table tbody tr.alt td { background: #f9fafb; }
        .data-table tbody tr.total-row td {
            background: #1e3a8a;
            color: #fff;
            font-weight: 700;
            font-size: 10px;
            border: none;
        }

        .right  { text-align: right; }
        .center { text-align: center; }

        /* Status badges */
        .badge { padding: 1px 5px; font-size: 8.5px; font-weight: 700; }
        .badge-paid     { color: #166534; background: #dcfce7; }
        .badge-draft    { color: #374151; background: #f3f4f6; }
        .badge-posted   { color: #1e40af; background: #dbeafe; }
        .badge-overdue  { color: #991b1b; background: #fee2e2; }
        .badge-partial  { color: #92400e; background: #fef3c7; }
        .badge-cancelled{ color: #6b7280; background: #f3f4f6; }

        /* Footer */
        .footer { text-align: center; font-size: 8.5px; color: #9ca3af; margin-top: 8px; border-top: 1px solid #e5e7eb; padding-top: 4px; }
    </style>
</head>
<body>

    {{-- ══ HEADER ══ --}}
    <table class="header-table">
        <tr>
            <td style="width:55%;">
                @if($tenant->logo)
                    <img src="{{ storage_path('app/public/' . $tenant->logo) }}" alt="" class="company-logo">
                @endif
                <div class="company-name">{{ $tenant->name }}</div>
                <div class="company-meta">
                    @if($tenant->address){{ $tenant->address }}<br>@endif
                    @if($tenant->phone)Tel: {{ $tenant->phone }}@endif
                    @if($tenant->email) &nbsp;|&nbsp; {{ $tenant->email }}@endif
                    @if($tenant->tax_number)<br>Tax ID: {{ $tenant->tax_number }}@endif
                </div>
            </td>
            <td style="width:45%; vertical-align:top;">
                <div class="title-box">
                    <div class="report-title">{{ strtoupper($term->label('sales_invoices')) }} REPORT</div>
                    <div class="report-sub">
                        {{ $totalCount }} record{{ $totalCount !== 1 ? 's' : '' }}
                        &nbsp;|&nbsp; Total: &#8358;{{ number_format($grandTotal, 2) }}
                        &nbsp;|&nbsp; {{ now()->format('d M Y') }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ══ ACTIVE FILTERS BAR ══ --}}
    <table class="filters-bar" style="width:100%;">
        <tr>
            <td colspan="{{ ceil(count($activeFilters) / 2) * 2 }}" class="filter-heading">Applied Filters</td>
        </tr>
        <tr>
            @php $col = 0; @endphp
            @foreach($activeFilters as $fk => $fv)
                <td style="width:{{ 100 / min(count($activeFilters), 6) }}%;">
                    <span class="fk">{{ $fk }}:</span>
                    <span class="fv">{{ $fv }}</span>
                </td>
                @php $col++; if ($col % 6 === 0 && !$loop->last) echo '</tr><tr>'; @endphp
            @endforeach
            {{-- pad remaining cells --}}
            @php $remainder = $col % 6; @endphp
            @if($remainder > 0)
                @for($i = $remainder; $i < 6; $i++)
                    <td></td>
                @endfor
            @endif
        </tr>
    </table>

    {{-- ══ INVOICE TABLE ══ --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:4%;" class="center">#</th>
                <th style="width:9%;">Invoice No.</th>
                <th style="width:8%;">Type</th>
                <th style="width:8%;">Date</th>
                <th style="width:18%;">{{ $term->label('customer') }} / Vendor</th>
                <th style="width:9%;">Reference</th>
                <th style="width:18%;">Description</th>
                <th style="width:11%;" class="right">Amount (NGN)</th>
                <th style="width:8%;" class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $index => $invoice)
                @php
                    $displayNumber = ($invoice->voucherType?->prefix ?? $invoice->voucherType?->abbreviation ?? '') . str_pad($invoice->voucher_number, 4, '0', STR_PAD_LEFT);
                    $type = ($invoice->voucherType?->inventory_effect ?? '') === 'increase' ? $term->label('purchase') : $term->label('sales');
                    $partyEntry = $invoice->entries->where('debit_amount', '>', 0)->first();
                    $partyName  = $partyEntry?->ledgerAccount?->name ?? 'Cash Sale';
                    $statusClass = 'badge badge-' . strtolower($invoice->status ?? 'draft');
                @endphp
                <tr class="{{ $loop->even ? 'alt' : '' }}">
                    <td class="center" style="color:#9ca3af;">{{ $index + 1 }}</td>
                    <td style="font-weight:700;">{{ $displayNumber }}</td>
                    <td>{{ $type }}</td>
                    <td>{{ optional($invoice->voucher_date)->format('d M Y') }}</td>
                    <td>{{ $partyName }}</td>
                    <td style="color:#6b7280;">{{ $invoice->reference_number ?? '—' }}</td>
                    <td style="color:#374151;">{{ \Str::limit($invoice->narration ?? '', 50) }}</td>
                    <td class="right">&#8358;{{ number_format($invoice->total_amount, 2) }}</td>
                    <td class="center"><span class="{{ $statusClass }}">{{ ucfirst($invoice->status) }}</span></td>
                </tr>
            @endforeach

            {{-- Grand total row --}}
            <tr class="total-row">
                <td colspan="7" style="text-align:right; padding-right:8px;">TOTAL ({{ $totalCount }} records)</td>
                <td class="right">&#8358;{{ number_format($grandTotal, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <strong>{{ $tenant->name }}</strong> &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, g:i A') }} &nbsp;|&nbsp; Confidential
    </div>

</body>
</html>
