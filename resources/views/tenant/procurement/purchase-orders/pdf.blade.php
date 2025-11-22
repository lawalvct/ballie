<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order {{ $purchaseOrder->lpo_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { text-align: right; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PURCHASE ORDER</h1>
        <h2>{{ $purchaseOrder->lpo_number }}</h2>
    </div>

    <div class="info">
        <p><strong>Date:</strong> {{ $purchaseOrder->lpo_date->format('M d, Y') }}</p>
        <p><strong>Vendor:</strong> {{ $purchaseOrder->vendor->getFullNameAttribute() }}</p>
        @if($purchaseOrder->expected_delivery_date)
            <p><strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date->format('M d, Y') }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                    <td>₦{{ number_format($item->unit_price, 2) }}</td>
                    <td>₦{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="total">Total:</td>
                <td class="total">₦{{ number_format($purchaseOrder->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($purchaseOrder->notes)
        <div style="margin-top: 20px;">
            <strong>Notes:</strong>
            <p>{{ $purchaseOrder->notes }}</p>
        </div>
    @endif
</body>
</html>
