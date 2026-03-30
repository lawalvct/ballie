{{-- Sage Delivery Note Template - Professional green-themed --}}
@php include resource_path('views/tenant/accounting/invoices/templates/partials/delivery-note-data.blade.php'); @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Note {{ $invoiceNumber }} - {{ $tenant->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; margin: 0; padding: 15px; color: #2d3436; font-size: 12px; line-height: 1.5; }
        .invoice-container { max-width: 100%; margin: 0 auto; }
        .header { display: table; width: 100%; margin-bottom: 5px; }
        .header-left { display: table-cell; width: 60%; vertical-align: top; }
        .header-right { display: table-cell; width: 40%; vertical-align: top; text-align: right; }
        .logo { max-height: 55px; margin-bottom: 8px; }
        .company-name { font-size: 20px; font-weight: bold; color: #00733B; margin-bottom: 3px; }
        .company-details { font-size: 11px; color: #777; line-height: 1.4; }
        .doc-badge { display: inline-block; background: #00733B; color: #fff; font-size: 20px; font-weight: bold; padding: 8px 20px; letter-spacing: 3px; margin-bottom: 10px; }
        .meta-line { font-size: 11px; color: #555; margin-bottom: 2px; }
        .meta-line strong { color: #333; }
        .divider { height: 3px; background: #00733B; margin: 15px 0; }
        .billing-row { display: table; width: 100%; margin-bottom: 20px; }
        .billing-col { display: table-cell; width: 50%; vertical-align: top; }
        .billing-label { font-size: 11px; font-weight: bold; color: #00733B; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; padding-bottom: 3px; border-bottom: 2px solid #00733B; display: inline-block; }
        .billing-name { font-size: 14px; font-weight: bold; margin-bottom: 4px; }
        .billing-detail { font-size: 11px; color: #666; line-height: 1.5; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #f4f9f6; color: #00733B; padding: 10px 8px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #00733B; border-top: 2px solid #00733B; }
        .items-table th.text-right { text-align: right; }
        .items-table th.text-center { text-align: center; }
        .items-table td { padding: 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        .product-name { font-weight: bold; }
        .product-desc { font-size: 10px; color: #999; }
        .summary-box { background: #f4f9f6; border-left: 4px solid #00733B; padding: 12px 15px; margin-bottom: 20px; font-size: 12px; }
        .notes-title { font-size: 11px; font-weight: bold; color: #00733B; text-transform: uppercase; margin-bottom: 4px; }
        .notes-content { font-size: 11px; color: #666; background: #fafafa; padding: 8px; border-radius: 3px; }
        .signatures { display: table; width: 100%; margin-bottom: 15px; }
        .sign-box { display: table-cell; width: 33.33%; text-align: center; padding: 10px; }
        .sign-line { border-top: 1px solid #666; margin-top: 45px; padding-top: 6px; font-size: 11px; color: #777; }
        .footer { text-align: center; font-size: 10px; color: #aaa; padding-top: 10px; border-top: 2px solid #00733B; }
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
                <div class="company-details">
                    @if($tenant->address){{ $tenant->address }}<br>@endif
                    @if($tenant->phone)Tel: {{ $tenant->phone }}@endif
                    @if($tenant->email) | {{ $tenant->email }}@endif
                    @if($tenant->website)<br>{{ $tenant->website }}@endif
                </div>
            </div>
            <div class="header-right">
                <div class="doc-badge">DELIVERY NOTE</div>
                <div class="meta-line"><strong>D/Note #:</strong> {{ $invoiceNumber }}</div>
                <div class="meta-line"><strong>Date:</strong> {{ $invoice->voucher_date->format('d/m/Y') }}</div>
                @if($invoice->reference_number)
                    <div class="meta-line"><strong>Ref:</strong> {{ $invoice->reference_number }}</div>
                @endif
            </div>
        </div>

        <div class="divider"></div>

        <!-- Billing -->
        <div class="billing-row">
            <div class="billing-col" style="padding-right: 20px;">
                <div class="billing-label">Deliver To</div>
                @if($customer)
                    <div class="billing-name">{{ $displayName }}</div>
                    <div class="billing-detail">
                        @if($customer->address ?? ($customer->address_line1 ?? null)){{ $customer->address ?? $customer->address_line1 }}<br>@endif
                        @if(($customer->city ?? null) || ($customer->state ?? null)){{ $customer->city ?? '' }} {{ $customer->state ?? '' }}<br>@endif
                        @if($customer->phone ?? null){{ $customer->phone }}<br>@endif
                        @if($customer->email ?? null){{ $customer->email }}@endif
                    </div>
                @else
                    <div class="billing-name">Walk-in {{ $term->label('customer') }}</div>
                @endif
            </div>
            <div class="billing-col">
                <div class="billing-label">Delivery Details</div>
                <div class="billing-detail">
                    <strong>Invoice #:</strong> {{ $invoiceNumber }}<br>
                    <strong>Total Items:</strong> {{ $deliveryItems->count() }}<br>
                    <strong>Total Qty:</strong> {{ number_format($totalQuantity, 2) }}<br>
                    @if($invoice->createdBy)
                        <strong>Prepared:</strong> {{ $invoice->createdBy->name ?? 'System' }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Items -->
        @if($deliveryItems->count() > 0)
        <div class="no-break">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 8%;" class="text-center">#</th>
                        <th style="width: 52%;">Description</th>
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
                            <td class="text-center">{{ $item->unit ?? $item->unit_name ?? 'Pcs' }}</td>
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
        <div style="margin-bottom: 15px;">
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
    </div>
</body>
</html>
