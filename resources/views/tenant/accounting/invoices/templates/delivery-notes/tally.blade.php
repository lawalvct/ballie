{{-- Tally Delivery Note Template - Classic bordered table layout --}}
@php include resource_path('views/tenant/accounting/invoices/templates/partials/delivery-note-data.blade.php'); @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Note {{ $invoiceNumber }} - {{ $tenant->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; margin: 0; padding: 10px; color: #000; font-size: 12px; line-height: 1.3; }
        .invoice-container { max-width: 100%; margin: 0 auto; border: 2px solid #000; }
        .invoice-title-row { text-align: center; font-size: 16px; font-weight: bold; padding: 8px; border-bottom: 2px solid #000; text-decoration: underline; }
        .top-section { width: 100%; border-collapse: collapse; }
        .top-section td { vertical-align: top; padding: 6px 10px; border-bottom: 1px solid #000; }
        .top-left { width: 55%; border-right: 1px solid #000; }
        .top-right { width: 45%; }
        .company-name { font-size: 15px; font-weight: bold; text-transform: uppercase; margin-bottom: 3px; }
        .company-detail { font-size: 11px; color: #333; line-height: 1.4; }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 3px 4px; font-size: 11px; border: none; }
        .meta-label { width: 45%; color: #555; }
        .meta-value { font-weight: bold; }
        .buyer-section { width: 100%; border-collapse: collapse; }
        .buyer-section td { vertical-align: top; padding: 6px 10px; border-bottom: 1px solid #000; }
        .buyer-left { width: 55%; border-right: 1px solid #000; }
        .buyer-right { width: 45%; }
        .buyer-label { font-size: 11px; color: #555; margin-bottom: 2px; }
        .buyer-name { font-size: 13px; font-weight: bold; margin-bottom: 3px; }
        .buyer-detail { font-size: 11px; color: #333; line-height: 1.4; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th { background: #f5f5f5; border-bottom: 2px solid #000; border-right: 1px solid #000; padding: 6px 4px; text-align: center; font-size: 11px; font-weight: bold; }
        .items-table th:last-child { border-right: none; }
        .items-table td { border-bottom: 1px solid #ccc; border-right: 1px solid #ccc; padding: 5px 4px; font-size: 11px; vertical-align: top; }
        .items-table td:last-child { border-right: none; }
        .col-sn { width: 8%; text-align: center; }
        .col-desc { width: 52%; }
        .col-qty { width: 20%; text-align: right; padding-right: 8px !important; }
        .col-unit { width: 20%; text-align: center; }
        .item-sub { font-size: 10px; color: #666; font-style: italic; }
        .total-row { border-top: 2px solid #000; }
        .total-row td { padding: 8px 4px; font-weight: bold; font-size: 13px; border-bottom: none; border-right: 1px solid #000; }
        .total-row td:last-child { border-right: none; }
        .footer-section { width: 100%; border-collapse: collapse; border-top: 1px solid #000; }
        .footer-section td { padding: 10px; vertical-align: top; }
        .footer-left { width: 55%; border-right: 1px solid #000; font-size: 11px; line-height: 1.5; }
        .footer-right { width: 45%; text-align: right; font-size: 11px; }
        .auth-signatory { margin-top: 40px; font-weight: bold; font-style: italic; }
        .delivery-confirmation { width: 100%; border-collapse: collapse; border-top: 1px solid #000; }
        .delivery-confirmation td { padding: 8px 10px; vertical-align: top; border-right: 1px solid #000; font-size: 11px; }
        .delivery-confirmation td:last-child { border-right: none; }
        .computer-generated { text-align: center; font-size: 10px; color: #999; padding: 5px; border-top: 1px solid #000; }
        @page { margin: 15mm; size: A4; }
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-title-row">DELIVERY NOTE / WAYBILL</div>

        <!-- Company Info + Meta -->
        <table class="top-section">
            <tr>
                <td class="top-left">
                    <div class="company-name">{{ $tenant->name }}</div>
                    <div class="company-detail">
                        @if($tenant->address){{ $tenant->address }}<br>@endif
                        @if($tenant->email)E-mail: {{ $tenant->email }}<br>@endif
                        @if($tenant->phone)Phone: {{ $tenant->phone }}@endif
                    </div>
                </td>
                <td class="top-right">
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">Delivery Note No.</td>
                            <td class="meta-value">{{ $invoiceNumber }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Dated</td>
                            <td class="meta-value">{{ $invoice->voucher_date->format('d-M-Y') }}</td>
                        </tr>
                        @if($invoice->reference_number)
                        <tr>
                            <td class="meta-label">Reference</td>
                            <td class="meta-value">{{ $invoice->reference_number }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="meta-label">Total Items</td>
                            <td class="meta-value">{{ $deliveryItems->count() }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Buyer Section -->
        <table class="buyer-section">
            <tr>
                <td class="buyer-left">
                    <div class="buyer-label">Consignee (Deliver To)</div>
                    <div class="buyer-name">{{ $displayName }}</div>
                    <div class="buyer-detail">
                        @if($customer)
                            @if($customer->address ?? ($customer->address_line1 ?? null)){{ $customer->address ?? $customer->address_line1 }}<br>@endif
                            @if(($customer->city ?? null) || ($customer->state ?? null)){{ $customer->city ?? '' }}{{ ($customer->city ?? null) && ($customer->state ?? null) ? ', ' : '' }}{{ $customer->state ?? '' }}<br>@endif
                            @if($customer->phone ?? null)Phone: {{ $customer->phone }}<br>@endif
                            @if($customer->email ?? null)Email: {{ $customer->email }}@endif
                        @endif
                    </div>
                </td>
                <td class="buyer-right">
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">Invoice No.</td>
                            <td class="meta-value">{{ $invoiceNumber }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Dated</td>
                            <td class="meta-value">{{ $invoice->voucher_date->format('d-M-Y') }}</td>
                        </tr>
                        @if(($customer->city ?? null) || ($customer->state ?? null))
                        <tr>
                            <td class="meta-label">Destination</td>
                            <td class="meta-value">{{ $customer->city ?? $customer->state ?? '' }}</td>
                        </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <div class="no-break">
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-sn">Sl<br>No.</th>
                        <th class="col-desc">Description of Goods</th>
                        <th class="col-qty">Quantity</th>
                        <th class="col-unit">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveryItems as $index => $item)
                        <tr>
                            <td class="col-sn">{{ $index + 1 }}</td>
                            <td class="col-desc">
                                <strong>{{ $item->product_name ?? $item->description ?? 'Item' }}</strong>
                                @if(($item->description ?? null) && ($item->description ?? null) !== ($item->product_name ?? null))
                                    <br><span class="item-sub">{{ $item->description }}</span>
                                @endif
                            </td>
                            <td class="col-qty">{{ number_format((float)($item->quantity ?? 0), 2) }}</td>
                            <td class="col-unit">{{ $item->unit ?? $item->unit_name ?? 'Nos' }}</td>
                        </tr>
                    @endforeach

                    <tr class="total-row">
                        <td class="col-sn"></td>
                        <td class="col-desc" style="text-align: right; padding-right: 10px;"><strong>Total Quantity</strong></td>
                        <td class="col-qty"><strong>{{ number_format($totalQuantity, 2) }}</strong></td>
                        <td class="col-unit"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Delivery Confirmation -->
        <table class="delivery-confirmation">
            <tr>
                <td style="width: 33%;">
                    <strong>Dispatched By:</strong><br><br><br><br>
                    Sign: ___________________<br>
                    Date: ___________________
                </td>
                <td style="width: 33%;">
                    <strong>Transported By:</strong><br><br><br><br>
                    Sign: ___________________<br>
                    Vehicle: ________________
                </td>
                <td style="width: 34%;">
                    <strong>Received By:</strong><br><br><br><br>
                    Sign: ___________________<br>
                    Date: ___________________
                </td>
            </tr>
        </table>

        <!-- Declaration & Signature -->
        <table class="footer-section">
            <tr>
                <td class="footer-left">
                    <strong>Declaration</strong><br>
                    We hereby certify that the goods listed above have been dispatched in good condition.
                    @if($invoice->narration)
                        <br><br><strong>Notes:</strong> {{ $invoice->narration }}
                    @endif
                </td>
                <td class="footer-right">
                    <strong>for {{ strtoupper($tenant->name) }}</strong>
                    <div class="auth-signatory">Authorised Signatory</div>
                </td>
            </tr>
        </table>

        <div class="computer-generated">
            This is a Computer Generated Delivery Note
        </div>
    </div>
</body>
</html>
