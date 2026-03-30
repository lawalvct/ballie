{{-- Ballie Delivery Note Template --}}
@php include resource_path('views/tenant/accounting/invoices/templates/partials/delivery-note-data.blade.php'); @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Note {{ $invoiceNumber }} - {{ $tenant->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; margin: 0; padding: 15px; color: #333; font-size: 13px; line-height: 1.4; }
        .container { max-width: 100%; margin: 0 auto; }
        .header { display: table; width: 100%; margin-bottom: 25px; border-bottom: 2px solid #2c5aa0; padding-bottom: 15px; }
        .header-left { display: table-cell; width: 60%; vertical-align: top; }
        .header-right { display: table-cell; width: 40%; vertical-align: top; text-align: right; }
        .logo { width: 60px; height: 60px; border-radius: 8px; margin-bottom: 10px; float: left; margin-right: 15px; }
        .company-name { font-size: 20px; font-weight: bold; color: #2c5aa0; margin-bottom: 5px; text-transform: uppercase; }
        .company-details { font-size: 12px; color: #666; line-height: 1.3; clear: both; }
        .doc-title { font-size: 24px; font-weight: bold; color: #2c5aa0; margin-bottom: 8px; }
        .doc-number { font-size: 16px; font-weight: bold; color: #333; margin-bottom: 5px; }
        .doc-date { font-size: 12px; color: #666; }
        .billing-info { display: table; width: 100%; margin-bottom: 20px; }
        .bill-to, .doc-info { display: table-cell; width: 50%; vertical-align: top; padding: 15px; background: #f8f9fa; border: 1px solid #e9ecef; }
        .bill-to { border-right: none; border-radius: 8px 0 0 8px; }
        .doc-info { border-radius: 0 8px 8px 0; }
        .section-title { font-size: 14px; font-weight: bold; color: #2c5aa0; margin-bottom: 8px; text-transform: uppercase; }
        .customer-name { font-size: 15px; font-weight: bold; color: #333; margin-bottom: 6px; }
        .detail-line { margin-bottom: 3px; font-size: 12px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; }
        .items-table th { background: #2c5aa0; color: white; padding: 10px 8px; text-align: left; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        .items-table td { padding: 8px; border-bottom: 1px solid #e9ecef; vertical-align: top; }
        .items-table tbody tr:nth-child(even) { background: #f8f9fa; }
        .sn-col { width: 8%; text-align: center; font-weight: bold; background: #f1f3f4; }
        .desc-col { width: 47%; }
        .qty-col { width: 20%; text-align: center; }
        .unit-col { width: 25%; text-align: center; }
        .product-name { font-weight: bold; color: #333; margin-bottom: 2px; }
        .product-desc { color: #666; font-size: 11px; }
        .summary-box { background: #e8f4fd; padding: 12px 15px; border-radius: 6px; border-left: 4px solid #2c5aa0; margin-bottom: 20px; font-size: 13px; }
        .notes { margin-bottom: 15px; }
        .notes-title { font-size: 13px; font-weight: bold; color: #2c5aa0; margin-bottom: 6px; }
        .notes-content { background: #fff3cd; padding: 10px; border-radius: 4px; border-left: 3px solid #ffc107; font-size: 12px; }
        .signatures { display: table; width: 100%; margin-bottom: 15px; }
        .signature-box { display: table-cell; width: 33.33%; text-align: center; padding: 10px; }
        .signature-line { border-top: 1px solid #333; margin-top: 50px; padding-top: 5px; font-size: 11px; font-weight: bold; color: #666; }
        .footer { text-align: center; font-size: 10px; color: #999; border-top: 1px solid #e9ecef; padding-top: 10px; }
        .watermark { text-align: center; font-size: 10px; color: #aaa; font-style: italic; margin-bottom: 10px; }
        @page { margin: 20mm; size: A4; }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="container">
        <div class="watermark">DELIVERY NOTE / WAYBILL</div>

        <div class="header">
            <div class="header-left">
                @if($tenant->logo)
                    <img src="{{ storage_path('app/public/' . $tenant->logo) }}" alt="" class="logo">
                @endif
                <div class="company-name">{{ $tenant->name }}</div>
                <div class="company-details">
                    @if($tenant->address){{ $tenant->address }}<br>@endif
                    @if($tenant->phone)Tel: {{ $tenant->phone }}@endif
                    @if($tenant->email) | Email: {{ $tenant->email }}@endif
                    @if($tenant->website)<br>{{ $tenant->website }}@endif
                </div>
            </div>
            <div class="header-right">
                <div class="doc-title">DELIVERY NOTE</div>
                <div class="doc-number"># {{ $invoiceNumber }}</div>
                <div class="doc-date">
                    <strong>Date:</strong> {{ $invoice->voucher_date->format('d M, Y') }}<br>
                    @if($invoice->reference_number)
                        <strong>Ref:</strong> {{ $invoice->reference_number }}<br>
                    @endif
                </div>
            </div>
        </div>

        <div class="billing-info">
            <div class="bill-to">
                <div class="section-title">Deliver To</div>
                @if($customer)
                    <div class="customer-name">{{ $displayName }}</div>
                    @if($customer->address ?? ($customer->address_line1 ?? false))
                        <div class="detail-line">{{ $customer->address ?? $customer->address_line1 }}</div>
                    @endif
                    @if(($customer->city ?? false) || ($customer->state ?? false))
                        <div class="detail-line">{{ $customer->city ?? '' }} {{ $customer->state ?? '' }}</div>
                    @endif
                    @if($customer->phone ?? null)
                        <div class="detail-line">Phone: {{ $customer->phone }}</div>
                    @endif
                @else
                    <div class="customer-name">Walk-in {{ $term->label('customer') }}</div>
                @endif
            </div>
            <div class="doc-info">
                <div class="section-title">Delivery Details</div>
                <div class="detail-line"><strong>Invoice #:</strong> {{ $invoiceNumber }}</div>
                <div class="detail-line"><strong>Date:</strong> {{ $invoice->voucher_date->format('d M, Y') }}</div>
                <div class="detail-line"><strong>Total Items:</strong> {{ $deliveryItems->count() }}</div>
                <div class="detail-line"><strong>Total Qty:</strong> {{ number_format($totalQuantity, 2) }}</div>
            </div>
        </div>

        @if($deliveryItems->count() > 0)
        <div class="no-break">
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="sn-col">S/N</th>
                        <th class="desc-col">Description</th>
                        <th class="qty-col">Quantity</th>
                        <th class="unit-col">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveryItems as $index => $item)
                        <tr>
                            <td class="sn-col">{{ $index + 1 }}</td>
                            <td class="desc-col">
                                <div class="product-name">{{ $item->product_name ?? $item->description ?? 'Item' }}</div>
                                @if(($item->description ?? null) && ($item->description ?? null) !== ($item->product_name ?? null))
                                    <div class="product-desc">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td class="qty-col">{{ number_format((float)($item->quantity ?? 0), 2) }}</td>
                            <td class="unit-col">{{ $item->unit ?? $item->unit_name ?? 'Pcs' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="summary-box">
            <strong>Total Items:</strong> {{ $deliveryItems->count() }} |
            <strong>Total Quantity:</strong> {{ number_format($totalQuantity, 2) }}
        </div>

        @if($invoice->narration)
        <div class="notes">
            <div class="notes-title">Delivery Notes:</div>
            <div class="notes-content">{{ $invoice->narration }}</div>
        </div>
        @endif

        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">Prepared By</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Dispatched By</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Received By ({{ $term->label('customer') }})</div>
            </div>
        </div>

        <div class="footer">
            <strong>{{ $tenant->name }}</strong> | Delivery Note for Invoice {{ $invoiceNumber }} | Generated: {{ now()->format('d M Y, g:i A') }}
        </div>
    </div>
</body>
</html>
