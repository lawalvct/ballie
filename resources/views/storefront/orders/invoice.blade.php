<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; }
        .header { border-bottom: 3px solid #3B82F6; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #3B82F6; font-size: 32px; }
        .header .company-info { margin-top: 10px; }
        .invoice-info { display: flex; justify-between; margin-bottom: 30px; }
        .invoice-info div { flex: 1; }
        .invoice-info h3 { color: #3B82F6; margin-bottom: 10px; font-size: 14px; text-transform: uppercase; }
        .order-details { background: #F3F4F6; padding: 15px; border-radius: 8px; margin-bottom: 30px; }
        .order-details table { width: 100%; }
        .order-details td { padding: 5px 0; }
        .order-details td:first-child { font-weight: bold; width: 150px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background: #3B82F6; color: white; padding: 12px; text-align: left; }
        .items-table td { padding: 12px; border-bottom: 1px solid #E5E7EB; }
        .items-table tr:last-child td { border-bottom: none; }
        .totals { margin-left: auto; width: 300px; }
        .totals table { width: 100%; }
        .totals td { padding: 8px 0; }
        .totals .total-row { font-size: 18px; font-weight: bold; border-top: 2px solid #3B82F6; }
        .footer { margin-top: 50px; padding-top: 20px; border-top: 2px solid #E5E7EB; text-align: center; color: #6B7280; font-size: 12px; }
        .print-button { background: #3B82F6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px; }
        .print-button:hover { background: #2563EB; }
        @media print {
            .print-button { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="print-button" onclick="window.print()">Print Invoice</button>

        <!-- Header -->
        <div class="header">
            <h1>INVOICE</h1>
            <div class="company-info">
                <h2>{{ $storeSettings->store_name ?? $tenant->name }}</h2>
                @if($storeSettings->store_description)
                    <p>{{ $storeSettings->store_description }}</p>
                @endif
            </div>
        </div>

        <!-- Invoice & Customer Info -->
        <div class="invoice-info">
            <div>
                <h3>Bill To:</h3>
                <p><strong>{{ $order->customer_name }}</strong></p>
                <p>{{ $order->customer_email }}</p>
                @if($order->customer_phone)
                    <p>{{ $order->customer_phone }}</p>
                @endif
                @if($order->shippingAddress)
                    <p style="margin-top: 10px;">
                        {{ $order->shippingAddress->address_line1 }}<br>
                        @if($order->shippingAddress->address_line2)
                            {{ $order->shippingAddress->address_line2 }}<br>
                        @endif
                        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->zip_code }}
                    </p>
                @endif
            </div>
            <div style="text-align: right;">
                <h3>Invoice Details:</h3>
                <p><strong>Invoice #:</strong> {{ $order->order_number }}</p>
                <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                <p><strong>Status:</strong> <span style="color: {{ $order->payment_status === 'paid' ? '#10B981' : '#F59E0B' }};">{{ ucfirst($order->payment_status) }}</span></p>
                <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
            </div>
        </div>

        <!-- Order Details -->
        <div class="order-details">
            <table>
                <tr>
                    <td>Order Number:</td>
                    <td>{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td>Order Date:</td>
                    <td>{{ $order->created_at->format('F d, Y g:i A') }}</td>
                </tr>
                <tr>
                    <td>Order Status:</td>
                    <td>{{ ucfirst($order->status) }}</td>
                </tr>
                @if($order->coupon_code)
                    <tr>
                        <td>Coupon Applied:</td>
                        <td>{{ $order->coupon_code }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product ? $item->product->name : 'Product' }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($item->unit_price, 2) }}</td>
                        <td style="text-align: right;">{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td style="text-align: right;">{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->tax_amount > 0)
                    <tr>
                        <td>Tax:</td>
                        <td style="text-align: right;">{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($order->tax_amount, 2) }}</td>
                    </tr>
                @endif
                @if($order->shipping_amount > 0)
                    <tr>
                        <td>Shipping:</td>
                        <td style="text-align: right;">{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($order->shipping_amount, 2) }}</td>
                    </tr>
                @endif
                @if($order->discount_amount > 0)
                    <tr>
                        <td>Discount:</td>
                        <td style="text-align: right; color: #10B981;">-{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($order->discount_amount, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>Total:</td>
                    <td style="text-align: right;">{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Notes -->
        @if($order->notes)
            <div style="margin-top: 30px; padding: 15px; background: #FEF3C7; border-left: 4px solid #F59E0B; border-radius: 4px;">
                <h3 style="color: #92400E; margin-bottom: 5px;">Customer Notes:</h3>
                <p style="color: #78350F;">{{ $order->notes }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p style="margin-top: 10px;">
                &copy; {{ date('Y') }} {{ $storeSettings->store_name ?? $tenant->name }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
