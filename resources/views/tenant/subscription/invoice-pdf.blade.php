<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $payment->payment_reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-successful {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .invoice-meta {
            margin-top: 10px;
        }

        .invoice-meta .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .invoice-meta .value {
            font-weight: bold;
            font-size: 12px;
            color: #1f2937;
        }

        .billing-section {
            display: table;
            width: 100%;
            margin: 30px 0;
        }

        .bill-to {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .payment-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .billing-details {
            font-size: 12px;
            line-height: 1.5;
        }

        .billing-details .company {
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            border: 1px solid #d1d5db;
        }

        .items-table th {
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            color: #374151;
        }

        .items-table td {
            border: 1px solid #d1d5db;
            padding: 12px 8px;
            vertical-align: top;
        }

        .items-table .description {
            font-weight: bold;
            color: #1f2937;
        }

        .items-table .sub-description {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
        }

        .items-table .amount {
            text-align: right;
            font-weight: bold;
            font-size: 14px;
        }

        .items-table .period {
            text-align: center;
            font-size: 11px;
        }

        .amount-words {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 15px;
            margin: 20px 0;
        }

        .amount-words .label {
            font-weight: bold;
            color: #374151;
        }

        .amount-words .value {
            text-transform: capitalize;
            color: #1f2937;
        }

        .summary {
            float: right;
            width: 300px;
            margin: 20px 0;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .summary-label {
            display: table-cell;
            text-align: left;
            color: #6b7280;
            font-size: 12px;
        }

        .summary-value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
            font-size: 12px;
        }

        .summary-total {
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
            margin-top: 8px;
        }

        .summary-total .summary-label {
            font-weight: bold;
            color: #1f2937;
            font-size: 14px;
        }

        .summary-total .summary-value {
            font-size: 16px;
            color: #2563eb;
        }

        .payment-details {
            margin: 30px 0;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 15px;
        }

        .payment-details .details-grid {
            display: table;
            width: 100%;
        }

        .payment-details .detail-item {
            display: table-cell;
            width: 33.33%;
            vertical-align: top;
            padding-right: 15px;
        }

        .payment-details .detail-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .payment-details .detail-value {
            font-weight: bold;
            font-size: 12px;
            color: #1f2937;
        }

        .footer {
            margin-top: 40px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 11px;
        }

        .clear {
            clear: both;
        }

        @page {
            margin: 0.5in;
            size: A4;
        }
    </style>
</head>
<body>
    @php
    use App\Helpers\NumberToWords;
    $amountInWords = NumberToWords::convert(floatval(str_replace(['$', ','], '', $payment->amount ?? 0)));
    @endphp

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="invoice-title">INVOICE</div>
                <div class="company-name">{{ config('app.name') }}</div>
                <span class="status-badge
                    @if($payment->status === 'successful') status-successful
                    @elseif($payment->status === 'pending') status-pending
                    @elseif($payment->status === 'failed') status-failed
                    @endif">
                    {{ ucfirst($payment->status) }}
                </span>
            </div>
            <div class="header-right">
                <div class="invoice-meta">
                    <div class="label">Invoice #</div>
                    <div class="value">{{ $payment->payment_reference }}</div>
                </div>
                <div class="invoice-meta">
                    <div class="label">Date</div>
                    <div class="value">{{ $payment->created_at->format('M j, Y') }}</div>
                </div>
                @if($payment->paid_at)
                <div class="invoice-meta">
                    <div class="label">Paid</div>
                    <div class="value">{{ $payment->paid_at->format('M j, Y') }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Billing Information -->
        <div class="billing-section">
            <div class="bill-to">
                <div class="section-title">Bill To:</div>
                <div class="billing-details">
                    <div class="company">{{ $tenant->company_name ?? $tenant->name }}</div>
                    @if($tenant->email)
                    <div>{{ $tenant->email }}</div>
                    @endif
                    @if($tenant->phone)
                    <div>{{ $tenant->phone }}</div>
                    @endif
                    @if($tenant->address)
                    <div>{{ $tenant->address }}</div>
                    @if($tenant->city || $tenant->state)
                    <div>{{ implode(', ', array_filter([$tenant->city, $tenant->state])) }}</div>
                    @endif
                    @endif
                </div>
            </div>

            <div class="payment-info">
                <div class="section-title">Payment Info:</div>
                <div class="billing-details">
                    @if($payment->payment_method)
                    <div>Method: {{ ucfirst($payment->payment_method) }}</div>
                    @endif
                    @if($payment->gateway_reference)
                    <div>Reference: {{ $payment->gateway_reference }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="width: 25%; text-align: center;">Period</th>
                    <th style="width: 25%; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if($payment->subscription)
                    <tr>
                        <td>
                            <div class="description">
                                @if($payment->subscription && $payment->subscription->plan && is_object($payment->subscription->plan))
                                    {{ $payment->subscription->plan->name }}
                                @else
                                    Subscription Plan
                                @endif
                            </div>
                            <div class="sub-description">
                                {{ ucfirst($payment->subscription->billing_cycle ?? 'monthly') }} subscription
                            </div>
                        </td>
                        <td class="period">
                            @if($payment->subscription->starts_at && $payment->subscription->ends_at)
                                {{ $payment->subscription->starts_at->format('M j') }} - {{ $payment->subscription->ends_at->format('M j, Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="amount">{{ $payment->formatted_amount }}</td>
                    </tr>
                @else
                    <tr>
                        <td>
                            <div class="description">Subscription Payment</div>
                            <div class="sub-description">Payment for subscription service</div>
                        </td>
                        <td class="period">{{ $payment->created_at->format('M j, Y') }}</td>
                        <td class="amount">{{ $payment->formatted_amount }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Amount in Words -->
        <div class="amount-words">
            <span class="label">Amount in words:</span>
            <span class="value">{{ $amountInWords }}</span>
        </div>

        <!-- Payment Summary -->
        <div class="summary">
            <div class="summary-row">
                <div class="summary-label">Subtotal:</div>
                <div class="summary-value">{{ $payment->formatted_amount }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Tax:</div>
                <div class="summary-value">$0.00</div>
            </div>
            <div class="summary-row summary-total">
                <div class="summary-label">Total:</div>
                <div class="summary-value">{{ $payment->formatted_amount }}</div>
            </div>
        </div>

        <div class="clear"></div>

        <!-- Payment Details (for successful payments) -->
        @if($payment->status === 'successful' && ($payment->gateway_reference || $payment->paid_at))
        <div class="payment-details">
            <div class="details-grid">
                @if($payment->gateway_reference)
                <div class="detail-item">
                    <div class="detail-label">Gateway Reference:</div>
                    <div class="detail-value">{{ $payment->gateway_reference }}</div>
                </div>
                @endif
                @if($payment->paid_at)
                <div class="detail-item">
                    <div class="detail-label">Payment Date:</div>
                    <div class="detail-value">{{ $payment->paid_at->format('M j, Y g:i A') }}</div>
                </div>
                @endif
                <div class="detail-item">
                    <div class="detail-label">Payment Status:</div>
                    <div class="detail-value">{{ ucfirst($payment->status) }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
