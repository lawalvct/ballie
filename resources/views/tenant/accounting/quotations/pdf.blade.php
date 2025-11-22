<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->getQuotationNumber() }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 22px; color: #333; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; }
        .info-label { font-weight: bold; width: 120px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f4f4f4; font-weight: bold; text-align: left; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .footer { margin-top: 30px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>QUOTATION</h1>
        <p style="margin: 5px 0;"><strong>{{ $quotation->getQuotationNumber() }}</strong></p>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Customer:</td>
            <td>{{ $quotation->customer ? ($quotation->customer->company_name ?: trim($quotation->customer->first_name . ' ' . $quotation->customer->last_name)) : 'N/A' }}</td>
            <td class="info-label">Date:</td>
            <td>{{ $quotation->quotation_date->format('M d, Y') }}</td>
        </tr>
        <tr>
            <td class="info-label">Subject:</td>
            <td>{{ $quotation->subject ?: 'N/A' }}</td>
            <td class="info-label">Expiry Date:</td>
            <td>{{ $quotation->expiry_date ? $quotation->expiry_date->format('M d, Y') : 'N/A' }}</td>
        </tr>
        @if($quotation->reference_number)
        <tr>
            <td class="info-label">Reference:</td>
            <td colspan="3">{{ $quotation->reference_number }}</td>
        </tr>
        @endif
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 40%;">Item</th>
                <th class="text-right" style="width: 10%;">Qty</th>
                <th class="text-right" style="width: 12%;">Rate</th>
                <th class="text-right" style="width: 12%;">Discount</th>
                <th class="text-right" style="width: 10%;">Tax</th>
                <th class="text-right" style="width: 16%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->product_name }}</strong>
                    @if($item->description)<br><span style="font-size: 9px; color: #666;">{{ $item->description }}</span>@endif
                </td>
                <td class="text-right">{{ $item->quantity }} {{ $item->unit }}</td>
                <td class="text-right">₦{{ number_format($item->rate, 2) }}</td>
                <td class="text-right">₦{{ number_format($item->discount, 2) }}</td>
                <td class="text-right">{{ $item->tax }}%</td>
                <td class="text-right">₦{{ number_format($item->getTotal(), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right">₦{{ number_format($quotation->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" class="text-right"><strong>Total Discount:</strong></td>
                <td class="text-right">₦{{ number_format($quotation->total_discount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" class="text-right"><strong>Total Tax:</strong></td>
                <td class="text-right">₦{{ number_format($quotation->total_tax, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>₦{{ number_format($quotation->total_amount, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    @if($quotation->terms_and_conditions)
    <div style="margin-top: 20px;">
        <strong>Terms & Conditions:</strong>
        <p style="margin: 5px 0; font-size: 10px;">{{ $quotation->terms_and_conditions }}</p>
    </div>
    @endif

    @if($quotation->notes)
    <div style="margin-top: 15px;">
        <strong>Notes:</strong>
        <p style="margin: 5px 0; font-size: 10px;">{{ $quotation->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y h:i A') }}</p>
    </div>
</body>
</html>
