<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->getQuotationNumber() }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; }
        .info-section { margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .info-label { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 5px; cursor: pointer;">Print</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer;">Close</button>
    </div>

    <div class="header">
        <h1>QUOTATION</h1>
        <p><strong>{{ $quotation->getQuotationNumber() }}</strong></p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div>
                <div class="info-label">Customer:</div>
                <div>{{ $quotation->customer ? ($quotation->customer->company_name ?: trim($quotation->customer->first_name . ' ' . $quotation->customer->last_name)) : 'N/A' }}</div>
            </div>
            <div>
                <div class="info-label">Date:</div>
                <div>{{ $quotation->quotation_date->format('M d, Y') }}</div>
            </div>
        </div>
        <div class="info-row">
            <div>
                <div class="info-label">Subject:</div>
                <div>{{ $quotation->subject ?: 'N/A' }}</div>
            </div>
            <div>
                <div class="info-label">Expiry Date:</div>
                <div>{{ $quotation->expiry_date ? $quotation->expiry_date->format('M d, Y') : 'N/A' }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Rate</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Tax</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->product_name }}</strong>
                    @if($item->description)<br><small>{{ $item->description }}</small>@endif
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
    <div style="margin-top: 30px;">
        <strong>Terms & Conditions:</strong>
        <p>{{ $quotation->terms_and_conditions }}</p>
    </div>
    @endif

    @if($quotation->notes)
    <div style="margin-top: 20px;">
        <strong>Notes:</strong>
        <p>{{ $quotation->notes }}</p>
    </div>
    @endif
</body>
</html>
