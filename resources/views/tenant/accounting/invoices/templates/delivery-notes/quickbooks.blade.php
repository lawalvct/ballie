{{-- QuickBooks Delivery Note Template - Simple clean dark-header style --}}
@php include resource_path('views/tenant/accounting/invoices/templates/partials/delivery-note-data.blade.php'); @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Note {{ $invoiceNumber }} - {{ $tenant->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; margin: 0; padding: 15px; color: #333; font-size: 12px; line-height: 1.5; }
        .invoice-container { max-width: 100%; margin: 0 auto; }
        .header { display: table; width: 100%; margin-bottom: 25px; }
        .header-left { display: table-cell; width: 50%; vertical-align: top; }
        .header-right { display: table-cell; width: 50%; vertical-align: top; text-align: right; }
        .logo { max-height: 50px; margin-bottom: 8px; }
        .company-name { font-size: 18px; font-weight: bold; color: #393939; margin-bottom: 4px; }
        .company-info { font-size: 10px; color: #777; line-height: 1.5; }
        .doc-title { font-size: 28px; font-weight: bold; color: #2CA01C; margin-bottom: 10px; }
        .meta-table { margin-left: auto; }
        .meta-table td { font-size: 11px; padding: 2px 0; }
        .meta-table .label { color: #777; text-align: right; padding-right: 10px; }
        .meta-table .value { color: #333; font-weight: bold; }
        .billing-section { display: table; width: 100%; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #e8e8e8; }
        .bill-to { display: table-cell; width: 50%; vertical-align: top; }
        .ship-to { display: table-cell; width: 50%; vertical-align: top; }
        .section-label { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #999; letter-spacing: 1px; margin-bottom: 5px; }
        .customer-name { font-size: 13px; font-weight: bold; margin-bottom: 3px; }
        .customer-detail { font-size: 11px; color: #666; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .items-table th { background: #393939; color: #fff; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: normal; }
        .items-table th.text-right { text-align: right; }
        .items-table th.text-center { text-align: center; }
        .items-table td { padding: 8px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        .items-table tbody tr:nth-child(even) td { background: #fafafa; }
        .summary-box { background: #f8f8f8; border: 1px solid #e8e8e8; padding: 12px 15px; margin-bottom: 20px; font-size: 12px; }
        .summary-box strong { color: #2CA01C; }
        .message-section { margin-bottom: 15px; }
        .message-label { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #999; margin-bottom: 4px; }
        .message-text { font-size: 11px; color: #555; padding: 8px; background: #fafafa; border-left: 3px solid #2CA01C; }
        .signatures { display: table; width: 100%; margin-bottom: 15px; }
        .sign-col { display: table-cell; width: 33.33%; text-align: center; padding: 10px; }
        .sign-line { border-top: 1px solid #999; margin-top: 45px; padding-top: 6px; font-size: 11px; color: #777; }
        .footer { text-align: center; font-size: 10px; color: #ccc; padding-top: 10px; border-top: 2px solid #393939; }
        .footer .brand { color: #2CA01C; font-weight: bold; }
        @page { margin: 18mm; size: A4; }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                @if($tenant->logo)
                    <img src="{{ storage_path('app/public/' . $tenant->logo) }}" alt="" class="logo">
                @endif
                <div class="company-name">{{ $tenant->name }}</div>
                <div class="company-info">
                    @if($tenant->address){{ $tenant->address }}<br>@endif
                    @if($tenant->phone){{ $tenant->phone }}@endif
                    @if($tenant->email) | {{ $tenant->email }}@endif
                </div>
            </div>
            <div class="header-right">
                <div class="doc-title">Delivery Note</div>
                <table class="meta-table">
                    <tr>
                        <td class="label">D/Note No.</td>
                        <td class="value">{{ $invoiceNumber }}</td>
                    </tr>
                    <tr>
                        <td class="label">Date</td>
                        <td class="value">{{ $invoice->voucher_date->format('M d, Y') }}</td>
                    </tr>
                    @if($invoice->reference_number)
                    <tr>
                        <td class="label">Reference</td>
                        <td class="value">{{ $invoice->reference_number }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Invoice No.</td>
                        <td class="value">{{ $invoiceNumber }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Billing -->
        <div class="billing-section">
            <div class="bill-to">
                <div class="section-label">Deliver To</div>
                @if($customer)
                    <div class="customer-name">{{ $displayName }}</div>
                    <div class="customer-detail">
                        @if($customer->address ?? ($customer->address_line1 ?? null)){{ $customer->address ?? $customer->address_line1 }}<br>@endif
                        @if(($customer->city ?? null) || ($customer->state ?? null)){{ $customer->city ?? '' }} {{ $customer->state ?? '' }}<br>@endif
                        @if($customer->phone ?? null){{ $customer->phone }}@endif
                    </div>
                @else
                    <div class="customer-name">Walk-in {{ $term->label('customer') }}</div>
                @endif
            </div>
            <div class="ship-to">
                <div class="section-label">Delivery Details</div>
                <div class="customer-detail">
                    <strong>Total Items:</strong> {{ $deliveryItems->count() }}<br>
                    <strong>Total Quantity:</strong> {{ number_format($totalQuantity, 2) }}<br>
                    @if($unitTotals->count() > 0)
                        <strong>By Unit:</strong> {{ $unitTotalsText }}<br>
                    @endif
                    @if($invoice->createdBy)
                        <strong>Prepared by:</strong> {{ $invoice->createdBy->name ?? 'System' }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        @if($deliveryItems->count() > 0)
        <div class="no-break">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 8%;" class="text-center">#</th>
                        <th style="width: 35%;">Product</th>
                        <th style="width: 27%;">Description</th>
                        <th style="width: 15%;" class="text-center">Qty</th>
                        <th style="width: 15%;" class="text-center">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveryItems as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td style="font-weight: bold;">{{ $item->product_name ?? $item->description ?? 'Item' }}</td>
                            <td style="color: #777;">{{ ($item->description ?? null) !== ($item->product_name ?? null) ? ($item->description ?? '-') : '-' }}</td>
                            <td class="text-center">{{ number_format((float)($item->quantity ?? 0), 2) }}</td>
                            <td class="text-center">{{ $item->unit ?: ($item->unit_name ?? (optional(optional($item->product ?? null)->primaryUnit)->symbol ?? '')) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Summary -->
        <div class="summary-box">
            <strong>Total Items:</strong> {{ $deliveryItems->count() }} &nbsp; | &nbsp;
            <strong>Total Quantity:</strong> {{ number_format($totalQuantity, 2) }}
            @if($unitTotals->count() > 0)
                &nbsp; | &nbsp; <strong>By Unit:</strong> {{ $unitTotalsText }}
            @endif
        </div>

        <!-- Notes -->
        @if($invoice->narration)
        <div class="message-section">
            <div class="message-label">Delivery Notes</div>
            <div class="message-text">{{ $invoice->narration }}</div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="sign-col">
                <div class="sign-line">Prepared By</div>
            </div>
            <div class="sign-col">
                <div class="sign-line">Dispatched By</div>
            </div>
            <div class="sign-col">
                <div class="sign-line">Received By ({{ $term->label('customer') }})</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <span class="brand">{{ $tenant->name }}</span> | Delivery Note for Invoice {{ $invoiceNumber }} | Generated on {{ now()->format('M d, Y \a\t g:i A') }}
        </div>
        @include('tenant.accounting.invoices.templates.partials.powered-by')
    </div>
</body>
</html>
