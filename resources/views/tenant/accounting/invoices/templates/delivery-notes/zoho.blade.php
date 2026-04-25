{{-- Zoho Delivery Note Template - Clean, modern with accent color strip --}}
@php include resource_path('views/tenant/accounting/invoices/templates/partials/delivery-note-data.blade.php'); @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Note {{ $invoiceNumber }} - {{ $tenant->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; margin: 0; padding: 20px; color: #333; font-size: 12px; line-height: 1.5; }
        .invoice-container { max-width: 100%; margin: 0 auto; }
        .accent-bar { height: 6px; background: #0077B5; margin-bottom: 30px; }
        .header { display: table; width: 100%; margin-bottom: 30px; }
        .header-left { display: table-cell; width: 50%; vertical-align: top; }
        .header-right { display: table-cell; width: 50%; vertical-align: top; text-align: right; }
        .company-name { font-size: 22px; font-weight: bold; color: #0077B5; margin-bottom: 4px; }
        .company-details { font-size: 11px; color: #777; line-height: 1.5; }
        .doc-label { font-size: 28px; font-weight: bold; color: #0077B5; letter-spacing: 2px; margin-bottom: 10px; }
        .doc-meta { font-size: 12px; color: #555; }
        .doc-meta strong { display: inline-block; width: 100px; color: #777; }
        .billing-row { display: table; width: 100%; margin-bottom: 25px; }
        .billing-col { display: table-cell; width: 50%; vertical-align: top; }
        .billing-title { font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: #0077B5; font-weight: bold; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 1px solid #e0e0e0; }
        .billing-name { font-size: 14px; font-weight: bold; color: #333; margin-bottom: 4px; }
        .billing-detail { font-size: 11px; color: #666; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #0077B5; color: #fff; padding: 10px 8px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .items-table th.text-right { text-align: right; }
        .items-table th.text-center { text-align: center; }
        .items-table td { padding: 10px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        .items-table tbody tr:nth-child(even) { background: #f9fbfd; }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        .product-name { font-weight: bold; color: #333; }
        .product-desc { font-size: 10px; color: #999; }
        .summary-box { background: #f0f7ff; border-left: 3px solid #0077B5; padding: 12px 15px; margin-bottom: 25px; font-size: 12px; }
        .notes { margin-bottom: 20px; }
        .notes-title { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #0077B5; font-weight: bold; margin-bottom: 5px; }
        .notes-content { font-size: 11px; color: #666; padding: 8px; background: #fafafa; border-radius: 4px; }
        .signatures { display: table; width: 100%; margin-bottom: 20px; }
        .sign-box { display: table-cell; width: 33.33%; text-align: center; padding: 10px; }
        .sign-line { border-top: 1px solid #333; margin-top: 50px; padding-top: 5px; font-size: 11px; color: #777; }
        .footer { text-align: center; font-size: 10px; color: #aaa; padding-top: 10px; border-top: 1px solid #eee; }
        @page { margin: 18mm; size: A4; }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="accent-bar"></div>

        <!-- Header -->
        <div class="header">
            <div class="header-left">
                @if($tenant->logo)
                    <img src="{{ storage_path('app/public/' . $tenant->logo) }}" alt="" style="max-height: 50px; margin-bottom: 8px;">
                @endif
                <div class="company-name">{{ $tenant->name }}</div>
                <div class="company-details">
                    @if($tenant->address){{ $tenant->address }}<br>@endif
                    @if($tenant->phone){{ $tenant->phone }}@endif
                    @if($tenant->email) | {{ $tenant->email }}@endif
                    @if($tenant->website)<br>{{ $tenant->website }}@endif
                </div>
            </div>
            <div class="header-right">
                <div class="doc-label">DELIVERY NOTE</div>
                <div class="doc-meta">
                    <strong># </strong>{{ $invoiceNumber }}<br>
                    <strong>Date:</strong> {{ $invoice->voucher_date->format('d M, Y') }}<br>
                    @if($invoice->reference_number)
                        <strong>Reference:</strong> {{ $invoice->reference_number }}<br>
                    @endif
                    <strong>Items:</strong> {{ $deliveryItems->count() }}
                </div>
            </div>
        </div>

        <!-- Billing -->
        <div class="billing-row">
            <div class="billing-col" style="padding-right: 20px;">
                <div class="billing-title">Deliver To</div>
                @if($customer)
                    <div class="billing-name">{{ $displayName }}</div>
                    <div class="billing-detail">
                        @if($customer->address ?? ($customer->address_line1 ?? null)){{ $customer->address ?? $customer->address_line1 }}<br>@endif
                        @if(($customer->city ?? null) || ($customer->state ?? null)){{ $customer->city ?? '' }} {{ $customer->state ?? '' }} {{ $customer->postal_code ?? '' }}<br>@endif
                        @if($customer->phone ?? null){{ $customer->phone }}<br>@endif
                        @if($customer->email ?? null){{ $customer->email }}@endif
                    </div>
                @else
                    <div class="billing-name">Walk-in {{ $term->label('customer') }}</div>
                @endif
            </div>
            <div class="billing-col">
                <div class="billing-title">Delivery Details</div>
                <div class="billing-detail">
                    <strong>Invoice #:</strong> {{ $invoiceNumber }}<br>
                    <strong>Total Items:</strong> {{ $deliveryItems->count() }}<br>
                    <strong>Total Quantity:</strong> {{ number_format($totalQuantity, 2) }}<br>
                    @if($unitTotals->count() > 0)
                        <strong>By Unit:</strong> {{ $unitTotalsText }}<br>
                    @endif
                    @if($invoice->createdBy)
                        <strong>Prepared By:</strong> {{ $invoice->createdBy->name ?? 'System' }}
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
                        <th style="width: 52%;">Item & Description</th>
                        <th style="width: 20%;" class="text-center">Quantity</th>
                        <th style="width: 20%;" class="text-center">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveryItems as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <span class="product-name">{{ $item->product_name ?? $item->description ?? 'Item' }}</span>
                                @if(($item->description ?? null) && ($item->description ?? null) !== ($item->product_name ?? null))
                                    <br><span class="product-desc">{{ $item->description }}</span>
                                @endif
                            </td>
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
        </div>

        <!-- Notes -->
        @if($invoice->narration)
        <div class="notes">
            <div class="notes-title">Delivery Notes</div>
            <div class="notes-content">{{ $invoice->narration }}</div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="sign-box">
                <div class="sign-line">Prepared By</div>
            </div>
            <div class="sign-box">
                <div class="sign-line">Dispatched By</div>
            </div>
            <div class="sign-box">
                <div class="sign-line">Received By ({{ $term->label('customer') }})</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            {{ $tenant->name }} | Delivery Note for Invoice {{ $invoiceNumber }} | Generated: {{ now()->format('d M Y, g:i A') }}
        </div>
        @include('tenant.accounting.invoices.templates.partials.powered-by')
    </div>
</body>
</html>
