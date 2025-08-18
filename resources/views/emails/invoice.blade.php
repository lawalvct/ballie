<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice from {{ $tenant->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-left: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
        }
        .footer {
            background-color: #374151;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
        }
        .invoice-details {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border: 1px solid #e5e7eb;
        }
        .invoice-details h3 {
            margin-top: 0;
            color: #2563eb;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px dotted #e5e7eb;
        }
        .detail-label {
            font-weight: bold;
            color: #6b7280;
        }
        .detail-value {
            color: #374151;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
        }
        .message {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #2563eb;
            margin: 20px 0;
            white-space: pre-line;
        }
        .cta-button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }
        .contact-info {
            margin-top: 20px;
            font-size: 14px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tenant->name }}</h1>
        <p>Invoice {{ $invoice->voucherType->prefix ?? '' }}{{ $invoice->voucher_number }}</p>
    </div>

    <div class="content">
        <div class="message">
            {{ $message }}
        </div>

        <div class="invoice-details">
            <h3>Invoice Details</h3>
            <div class="detail-row">
                <span class="detail-label">Invoice Number:</span>
                <span class="detail-value">{{ $invoice->voucherType->prefix ?? '' }}{{ $invoice->voucher_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Invoice Date:</span>
                <span class="detail-value">{{ $invoice->voucher_date->format('M d, Y') }}</span>
            </div>
            @if($invoice->reference_number)
            <div class="detail-row">
                <span class="detail-label">Reference:</span>
                <span class="detail-value">{{ $invoice->reference_number }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">{{ ucfirst($invoice->status) }}</span>
            </div>
            <div class="detail-row" style="border-bottom: 2px solid #2563eb; padding-top: 15px; margin-top: 15px;">
                <span class="detail-label">Total Amount:</span>
                <span class="detail-value amount">₦{{ number_format($invoice->total_amount, 2) }}</span>
            </div>
        </div>

        @if($invoice->narration)
        <div class="invoice-details">
            <h3>Description</h3>
            <p>{{ $invoice->narration }}</p>
        </div>
        @endif

        <div class="contact-info">
            <p><strong>Questions about this invoice?</strong></p>
            <p>Contact us at:</p>
            @if($tenant->email)
            <p>Email: <a href="mailto:{{ $tenant->email }}">{{ $tenant->email }}</a></p>
            @endif
            @if($tenant->phone)
            <p>Phone: {{ $tenant->phone }}</p>
            @endif
        </div>
    </div>

    <div class="footer">
        <p><strong>{{ $tenant->name }}</strong></p>
        @if($tenant->address)
        <p>{{ $tenant->address }}</p>
        @endif
        <p>Powered by Ballie Business Management System</p>
    </div>
</body>
</html>
