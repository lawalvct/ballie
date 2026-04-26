@php
    $isProduction = $stockJournal->entry_type === 'production';
    $companyName = $tenant->company_name ?? $tenant->name ?? 'Company Name';
    $companyEmail = $tenant->email ?? '';
    $companyPhone = $tenant->phone ?? '';
    $companyAddress = $tenant->address ?? '';
    $logoPath = $tenant->logo ?? null;
    $logoFullPath = $logoPath ? public_path('storage/' . ltrim($logoPath, '/')) : null;
    $hasLogo = $logoFullPath && file_exists($logoFullPath);

    $items = $stockJournal->items;
    $inItems = $items->where('movement_type', 'in');
    $outItems = $items->where('movement_type', 'out');
    $totalIn = $inItems->sum('amount');
    $totalOut = $outItems->sum('amount');
    $totalRejected = $inItems->sum('rejected_quantity');
    $totalWaste = $outItems->sum('waste_quantity');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentLabel }} - {{ $stockJournal->journal_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 24px 28px;
        }

        .header {
            border-bottom: 2px solid #1d4ed8;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header table { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: middle; }
        .company-logo { max-height: 64px; max-width: 180px; }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #1d4ed8;
            margin: 0 0 2px 0;
        }
        .company-meta {
            font-size: 10px;
            color: #4b5563;
            line-height: 1.5;
        }
        .doc-title {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .doc-sub {
            text-align: right;
            font-size: 10px;
            color: #6b7280;
            margin-top: 2px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-draft     { background: #fef3c7; color: #92400e; }
        .status-posted    { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        .meta-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .meta-grid td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
            vertical-align: top;
        }
        .meta-grid .label {
            color: #6b7280;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .meta-grid .value {
            color: #111827;
            font-weight: 600;
            font-size: 11px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1d4ed8;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 4px;
            margin: 18px 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table.items th {
            background: #1d4ed8;
            color: #fff;
            text-align: left;
            padding: 6px 7px;
            font-weight: 600;
            font-size: 10px;
        }
        table.items td {
            padding: 6px 7px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        table.items tr:nth-child(even) td { background: #f9fafb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .pill {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .pill-in  { background: #d1fae5; color: #065f46; }
        .pill-out { background: #fee2e2; color: #991b1b; }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .summary td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }
        .summary .summary-label { color: #6b7280; }
        .summary .summary-value { font-weight: bold; color: #111827; text-align: right; }

        .notes {
            margin-top: 12px;
            padding: 10px 12px;
            background: #f3f4f6;
            border-left: 3px solid #1d4ed8;
            font-size: 10px;
            color: #374151;
        }

        .footer {
            position: fixed;
            bottom: 16px;
            left: 28px;
            right: 28px;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
        }
        .footer a { color: #1d4ed8; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td style="width: 60%;">
                    @if($hasLogo)
                        <img src="{{ $logoFullPath }}" alt="Logo" class="company-logo">
                    @endif
                    <div class="company-name">{{ $companyName }}</div>
                    <div class="company-meta">
                        @if($companyAddress) {{ $companyAddress }}<br> @endif
                        @if($companyPhone) Tel: {{ $companyPhone }} @endif
                        @if($companyEmail) &nbsp;&middot;&nbsp; {{ $companyEmail }} @endif
                    </div>
                </td>
                <td style="width: 40%;">
                    <div class="doc-title">{{ $documentLabel }}</div>
                    <div class="doc-sub">
                        # {{ $stockJournal->journal_number }} &middot;
                        {{ $stockJournal->journal_date->format('d M Y') }}
                    </div>
                    <div class="doc-sub" style="margin-top: 6px;">
                        <span class="status-badge status-{{ $stockJournal->status }}">{{ ucfirst($stockJournal->status) }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="meta-grid">
        <tr>
            <td style="width: 25%;">
                <div class="label">Journal Number</div>
                <div class="value">{{ $stockJournal->journal_number }}</div>
            </td>
            <td style="width: 25%;">
                <div class="label">Entry Type</div>
                <div class="value">{{ ucfirst(str_replace(['_', '-'], ' ', $stockJournal->entry_type)) }}</div>
            </td>
            <td style="width: 25%;">
                <div class="label">Journal Date</div>
                <div class="value">{{ $stockJournal->journal_date->format('d M Y') }}</div>
            </td>
            <td style="width: 25%;">
                <div class="label">Reference</div>
                <div class="value">{{ $stockJournal->reference_number ?: '—' }}</div>
            </td>
        </tr>
    </table>

    @if($isProduction)
        <div class="section-title">Production Report Details</div>
        <table class="meta-grid">
            <tr>
                <td style="width: 25%;">
                    <div class="label">Operator</div>
                    <div class="value">{{ $stockJournal->operator->full_name ?? '—' }}</div>
                </td>
                <td style="width: 25%;">
                    <div class="label">Assistant Operator</div>
                    <div class="value">{{ $stockJournal->assistantOperator->full_name ?? '—' }}</div>
                </td>
                <td style="width: 25%;">
                    <div class="label">Production Batch</div>
                    <div class="value">{{ $stockJournal->production_batch_number ?: '—' }}</div>
                </td>
                <td style="width: 25%;">
                    <div class="label">Work Order</div>
                    <div class="value">{{ $stockJournal->work_order_number ?: '—' }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="label">Shift</div>
                    <div class="value">{{ $stockJournal->production_shift ?: '—' }}</div>
                </td>
                <td>
                    <div class="label">Machine / Line</div>
                    <div class="value">{{ $stockJournal->machine_name ?: '—' }}</div>
                </td>
                <td>
                    <div class="label">Start Time</div>
                    <div class="value">{{ $stockJournal->production_started_at ?: '—' }}</div>
                </td>
                <td>
                    <div class="label">End Time</div>
                    <div class="value">{{ $stockJournal->production_ended_at ?: '—' }}</div>
                </td>
            </tr>
        </table>
    @endif

    @if($isProduction && $outItems->count())
        <div class="section-title">Source / Material Consumption (OUT)</div>
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 28%;">Material</th>
                    <th class="text-right">Qty Used</th>
                    <th class="text-right">Waste</th>
                    <th class="text-right">Rate</th>
                    <th class="text-right">Amount</th>
                    <th>Batch</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($outItems as $item)
                    @php $unit = $item->unit_snapshot ?: ($item->product->primaryUnit->symbol ?? $item->product->primaryUnit->name ?? ''); @endphp
                    <tr>
                        <td>
                            <strong>{{ $item->product->name }}</strong><br>
                            <span style="color:#6b7280; font-size:9px;">SKU: {{ $item->product->sku ?? '—' }}</span>
                        </td>
                        <td class="text-right">{{ number_format($item->quantity, 4) }} {{ $unit }}</td>
                        <td class="text-right">{{ number_format($item->waste_quantity ?? 0, 4) }} {{ $unit }}</td>
                        <td class="text-right">₦{{ number_format($item->rate, 2) }}</td>
                        <td class="text-right">₦{{ number_format($item->amount, 2) }}</td>
                        <td>{{ $item->batch_number ?: '—' }}</td>
                        <td>{{ $item->remarks ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="section-title">Finished Goods Output (IN)</div>
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 28%;">Product</th>
                    <th class="text-right">Good Qty</th>
                    <th class="text-right">Rejected</th>
                    <th class="text-right">Rate</th>
                    <th class="text-right">Amount</th>
                    <th>Batch</th>
                    <th>Expiry</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inItems as $item)
                    @php $unit = $item->unit_snapshot ?: ($item->product->primaryUnit->symbol ?? $item->product->primaryUnit->name ?? ''); @endphp
                    <tr>
                        <td>
                            <strong>{{ $item->product->name }}</strong><br>
                            <span style="color:#6b7280; font-size:9px;">SKU: {{ $item->product->sku ?? '—' }}</span>
                        </td>
                        <td class="text-right">{{ number_format($item->quantity, 4) }} {{ $unit }}</td>
                        <td class="text-right">{{ number_format($item->rejected_quantity ?? 0, 4) }} {{ $unit }}</td>
                        <td class="text-right">₦{{ number_format($item->rate, 2) }}</td>
                        <td class="text-right">₦{{ number_format($item->amount, 2) }}</td>
                        <td>{{ $item->batch_number ?: '—' }}</td>
                        <td>{{ $item->expiry_date ? $item->expiry_date->format('d M Y') : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="section-title">Journal Entry Items</div>
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 28%;">Product</th>
                    <th class="text-center">Movement</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Rate</th>
                    <th class="text-right">Amount</th>
                    <th>Batch</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    @php $unit = $item->unit_snapshot ?: ($item->product->primaryUnit->symbol ?? $item->product->primaryUnit->name ?? ''); @endphp
                    <tr>
                        <td>
                            <strong>{{ $item->product->name }}</strong><br>
                            <span style="color:#6b7280; font-size:9px;">SKU: {{ $item->product->sku ?? '—' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="pill {{ $item->movement_type === 'in' ? 'pill-in' : 'pill-out' }}">
                                {{ strtoupper($item->movement_type) }}
                            </span>
                        </td>
                        <td class="text-right">{{ number_format($item->quantity, 4) }} {{ $unit }}</td>
                        <td class="text-right">₦{{ number_format($item->rate, 2) }}</td>
                        <td class="text-right">₦{{ number_format($item->amount, 2) }}</td>
                        <td>{{ $item->batch_number ?: '—' }}</td>
                        <td>{{ $item->remarks ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <table class="summary">
        @if($isProduction)
            <tr>
                <td class="summary-label" style="width: 70%;">Total Material Consumption</td>
                <td class="summary-value">₦{{ number_format($totalOut, 2) }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total Finished Goods Output</td>
                <td class="summary-value">₦{{ number_format($totalIn, 2) }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total Wastage Quantity</td>
                <td class="summary-value">{{ number_format($totalWaste, 4) }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total Rejected Quantity</td>
                <td class="summary-value">{{ number_format($totalRejected, 4) }}</td>
            </tr>
            <tr>
                <td class="summary-label" style="background:#eff6ff;"><strong>Variance (Output − Consumption)</strong></td>
                <td class="summary-value" style="background:#eff6ff;">₦{{ number_format($totalIn - $totalOut, 2) }}</td>
            </tr>
        @else
            <tr>
                <td class="summary-label" style="width: 70%;"><strong>Total Amount</strong></td>
                <td class="summary-value">₦{{ number_format($stockJournal->total_amount, 2) }}</td>
            </tr>
        @endif
    </table>

    @if($stockJournal->narration)
        <div class="notes">
            <strong>Narration:</strong> {{ $stockJournal->narration }}
        </div>
    @endif

    @if($isProduction && $stockJournal->production_notes)
        <div class="notes">
            <strong>Production Notes:</strong> {{ $stockJournal->production_notes }}
        </div>
    @endif

    <div style="margin-top: 24px; font-size: 9px; color: #6b7280;">
        Prepared by: {{ $stockJournal->creator->name ?? 'System' }}
        @if($stockJournal->posted_at)
            &nbsp;&middot;&nbsp; Posted by: {{ $stockJournal->poster->name ?? 'System' }} on {{ $stockJournal->posted_at->format('d M Y H:i') }}
        @endif
    </div>

    <div class="footer">
        Generated on {{ now()->format('d M Y H:i') }} &middot; {{ $companyName }} &middot;
        Powered by <a href="https://ballie.co">Ballie</a>
    </div>
</body>
</html>
