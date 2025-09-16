<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Journal Entry - {{ $stockJournal->journal_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-box {
            width: 48%;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 140px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th,
        .table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .movement-in {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
        }
        .movement-out {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-draft {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-posted {
            background-color: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #333;
            font-size: 11px;
            color: #666;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature-box {
            width: 30%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .totals-section {
            float: right;
            width: 300px;
            margin-top: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .grand-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #333;
            margin-top: 5px;
            padding-top: 5px;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print Button (hidden when printing) -->
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Print Document
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            Close
        </button>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $tenant->name ?? 'Company Name' }}</div>
        <div>{{ $tenant->address ?? 'Company Address' }}</div>
        <div class="document-title">STOCK JOURNAL ENTRY</div>
    </div>

    <!-- Document Information -->
    <div class="info-section">
        <div class="info-box">
            <div><span class="info-label">Journal Number:</span> {{ $stockJournal->journal_number }}</div>
            <div><span class="info-label">Entry Type:</span> {{ $stockJournal->entry_type_display }}</div>
            <div><span class="info-label">Journal Date:</span> {{ $stockJournal->journal_date->format('d M Y') }}</div>
            @if($stockJournal->reference_number)
            <div><span class="info-label">Reference Number:</span> {{ $stockJournal->reference_number }}</div>
            @endif
        </div>
        <div class="info-box">
            <div><span class="info-label">Status:</span>
                <span class="status-badge status-{{ $stockJournal->status }}">
                    {{ $stockJournal->status_display }}
                </span>
            </div>
            <div><span class="info-label">Created By:</span> {{ $stockJournal->creator->name ?? 'System' }}</div>
            <div><span class="info-label">Created On:</span> {{ $stockJournal->created_at->format('d M Y H:i') }}</div>
            @if($stockJournal->posted_at)
            <div><span class="info-label">Posted On:</span> {{ $stockJournal->posted_at->format('d M Y H:i') }}</div>
            <div><span class="info-label">Posted By:</span> {{ $stockJournal->poster->name ?? 'System' }}</div>
            @endif
        </div>
    </div>

    @if($stockJournal->narration)
    <!-- Narration -->
    <div style="margin-bottom: 20px;">
        <div style="font-weight: bold; margin-bottom: 5px;">Narration:</div>
        <div style="border: 1px solid #ddd; padding: 8px; background-color: #f9f9f9;">
            {{ $stockJournal->narration }}
        </div>
    </div>
    @endif

    <!-- Items Table -->
    <table class="table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Product Details</th>
                <th style="width: 10%;" class="text-center">Movement</th>
                <th style="width: 12%;" class="text-right">Stock Before</th>
                <th style="width: 12%;" class="text-right">Quantity</th>
                <th style="width: 12%;" class="text-right">Rate (₦)</th>
                <th style="width: 12%;" class="text-right">Amount (₦)</th>
                <th style="width: 12%;" class="text-right">Stock After</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stockJournal->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->product->name }}</strong><br>
                    <small>SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                    @if($item->product->productCategory)
                    <br><small>Category: {{ $item->product->productCategory->name }}</small>
                    @endif
                    @if($item->batch_number || $item->expiry_date)
                    <br><small>
                        @if($item->batch_number)Batch: {{ $item->batch_number }}@endif
                        @if($item->batch_number && $item->expiry_date) | @endif
                        @if($item->expiry_date)Expiry: {{ $item->expiry_date->format('d M Y') }}@endif
                    </small>
                    @endif
                    @if($item->remarks)
                    <br><small style="font-style: italic;">{{ $item->remarks }}</small>
                    @endif
                </td>
                <td class="text-center">
                    @if($item->movement_type === 'in')
                        <span class="movement-in">IN</span>
                    @else
                        <span class="movement-out">OUT</span>
                    @endif
                </td>
                <td class="text-right">
                    {{ number_format($item->stock_before, 4) }}
                    <br><small>{{ $item->product->primaryUnit->name ?? '' }}</small>
                </td>
                <td class="text-right">
                    <strong>{{ number_format($item->quantity, 4) }}</strong>
                    <br><small>{{ $item->product->primaryUnit->name ?? '' }}</small>
                </td>
                <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                <td class="text-right"><strong>{{ number_format($item->amount, 2) }}</strong></td>
                <td class="text-right">
                    {{ number_format($item->stock_after, 4) }}
                    <br><small>{{ $item->product->primaryUnit->name ?? '' }}</small>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px; color: #666;">
                    No items found in this journal entry.
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($stockJournal->items->count() > 0)
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="6" class="text-right">TOTAL:</td>
                <td class="text-right">₦{{ number_format($stockJournal->items->sum('amount'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Summary -->
    @if($stockJournal->items->count() > 0)
    <div class="totals-section">
        <div class="total-row">
            <span>Total Items:</span>
            <span>{{ $stockJournal->items->count() }}</span>
        </div>
        <div class="total-row">
            <span>Total Quantity:</span>
            <span>{{ number_format($stockJournal->items->sum('quantity'), 4) }}</span>
        </div>
        <div class="total-row grand-total">
            <span>Grand Total:</span>
            <span>₦{{ number_format($stockJournal->items->sum('amount'), 2) }}</span>
        </div>
    </div>
    <div style="clear: both;"></div>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div>Prepared By</div>
            <div style="margin-top: 30px;">{{ $stockJournal->creator->name ?? '' }}</div>
        </div>
        @if($stockJournal->status === 'posted')
        <div class="signature-box">
            <div>Approved By</div>
            <div style="margin-top: 30px;">{{ $stockJournal->poster->name ?? '' }}</div>
        </div>
        @endif
        <div class="signature-box">
            <div>Authorized Signature</div>
            <div style="margin-top: 30px;">_________________</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div style="display: flex; justify-content: space-between;">
            <div>
                Generated on: {{ now()->format('d M Y H:i:s') }}
            </div>
            <div>
                System: {{ config('app.name', 'Ballie') }}
            </div>
        </div>
    </div>
</body>
</html>
