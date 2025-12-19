<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->getQuotationNumber() }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            padding: 20px 0;
            border-bottom: 2px solid #eee;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header .company-details {
            text-align: left;
        }
        .header .company-details h1 {
            margin: 0 0 5px 0;
            font-size: 24px;
            color: #222;
        }
        .header .quotation-details {
            text-align: right;
        }
        .header .quotation-details h2 {
            margin: 0;
            font-size: 28px;
            color: #555;
        }
        .details-section {
            margin-top: 25px;
            margin-bottom: 25px;
        }
        .details-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .details-section .bill-to {
            text-align: left;
        }
        .details-section .quotation-info {
            text-align: right;
        }
        .details-section h3 {
            margin-bottom: 5px;
            font-size: 13px;
            color: #555;
            text-transform: uppercase;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .items-table th, .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .items-table th {
            background-color: #f8f8f8;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
        .items-table .text-right {
            text-align: right;
        }
        .totals-section {
            margin-top: 20px;
            width: 100%;
        }
        .totals-section table {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-section td {
            padding: 8px 10px;
        }
        .totals-section .total-label {
            font-weight: bold;
            text-align: right;
        }
        .totals-section .grand-total {
            font-size: 16px;
            font-weight: bold;
            background-color: #f8f8f8;
        }
        .footer-notes {
            margin-top: 30px;
            font-size: 11px;
            color: #666;
        }
        .footer-notes h4 {
            margin-bottom: 5px;
            font-size: 12px;
            color: #444;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        p {
            margin: 0 0 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table>
                <tr>
                    <td class="company-details">
                        <h1>{{ $tenant->name ?? ($tenant->company_name ?? 'Ballie') }}</h1>
                        <p>{{ $tenant->address ?? '' }}</p>
                        <p>{{ $tenant->email ?? '' }}</p>
                        <p>{{ $tenant->phone ?? '' }}</p>
                    </td>
                    <td class="quotation-details">
                        <h2>QUOTATION</h2>
                        <p><strong>#{{ $quotation->getQuotationNumber() }}</strong></p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="details-section">
            <table>
                <tr>
                    <td class="bill-to">
                        <h3>Bill To:</h3>
                        <p>
                            <strong>{{ $quotation->customer ? ($quotation->customer->company_name ?: trim($quotation->customer->first_name . ' ' . $quotation->customer->last_name)) : 'N/A' }}</strong>
                        </p>
                        @if($quotation->customer && $quotation->customer->address)
                            <p>{{ $quotation->customer->address }}</p>
                        @endif
                         @if($quotation->customer && $quotation->customer->email)
                            <p>Email: {{ $quotation->customer->email }}</p>
                        @endif
                        @if($quotation->customer && $quotation->customer->phone)
                            <p>Phone: {{ $quotation->customer->phone }}</p>
                        @endif
                    </td>
                    <td class="quotation-info">
                        <p><strong>Quotation Date:</strong> {{ $quotation->quotation_date->format('M d, Y') }}</p>
                        <p><strong>Expiry Date:</strong> {{ $quotation->expiry_date ? $quotation->expiry_date->format('M d, Y') : 'N/A' }}</p>
                        @if($quotation->reference_number)
                            <p><strong>Reference #:</strong> {{ $quotation->reference_number }}</p>
                        @endif
                         @if($quotation->subject)
                            <p><strong>Subject:</strong> {{ $quotation->subject }}</p>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item & Description</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Rate</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Tax (%)</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if($item->description)<br><span style="color: #666;">{{ $item->description }}</span>@endif
                    </td>
                    <td class="text-right">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }} {{ $item->unit }}</td>
                    <td class="text-right">₦{{ number_format($item->rate, 2) }}</td>
                    <td class="text-right">₦{{ number_format($item->discount, 2) }}</td>
                    <td class="text-right">{{ rtrim(rtrim(number_format($item->tax, 2), '0'), '.') }}%</td>
                    <td class="text-right">₦{{ number_format($item->getTotal(), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <table>
                <tr>
                    <td class="total-label">Subtotal:</td>
                    <td class="text-right">₦{{ number_format($quotation->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="total-label">Total Discount:</td>
                    <td class="text-right">₦{{ number_format($quotation->total_discount, 2) }}</td>
                </tr>
                <tr>
                    <td class="total-label">Total Tax:</td>
                    <td class="text-right">₦{{ number_format($quotation->total_tax, 2) }}</td>
                </tr>
                <tr class="grand-total">
                    <td class="total-label">TOTAL:</td>
                    <td class="text-right">₦{{ number_format($quotation->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        @if($quotation->terms_and_conditions || $quotation->notes)
        <div class="footer-notes">
            @if($quotation->notes)
                <h4>Notes</h4>
                <p>{{ $quotation->notes }}</p>
            @endif
            @if($quotation->terms_and_conditions)
                <h4 style="margin-top: 15px;">Terms & Conditions</h4>
                <p>{{ $quotation->terms_and_conditions }}</p>
            @endif
        </div>
        @endif

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Generated on {{ now()->format('M d, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>
