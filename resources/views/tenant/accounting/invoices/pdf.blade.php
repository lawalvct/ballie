<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->voucherType->prefix ?? '' }}{{ $invoice->voucher_number }} - {{ $tenant->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 15px;
            color: #333;
            font-size: 13px;
            line-height: 1.4;
        }
        
        .invoice-container {
            max-width: 100%;
            margin: 0 auto;
        }
        
        /* Compact Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 2px solid #2c5aa0;
            padding-bottom: 15px;
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
        
        .logo {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            margin-bottom: 10px;
            float: left;
            margin-right: 15px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .company-details {
            font-size: 12px;
            color: #666;
            line-height: 1.3;
            clear: both;
        }
        
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 8px;
        }
        
        .invoice-number {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .invoice-date {
            font-size: 12px;
            color: #666;
        }
        
        /* Compact Billing Section */
        .billing-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .bill-to, .invoice-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        
        .bill-to {
            border-right: none;
            border-radius: 8px 0 0 8px;
        }
        
        .invoice-info {
            border-radius: 0 8px 8px 0;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .customer-name {
            font-size: 15px;
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
        }
        
        .detail-line {
            margin-bottom: 3px;
            font-size: 12px;
        }
        
        /* Compact Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 12px;
        }
        
        .items-table th {
            background: #2c5aa0;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
        }
        
        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .sn-col {
            width: 5%;
            text-align: center;
            font-weight: bold;
            background: #f1f3f4;
        }
        
        .desc-col {
            width: 35%;
        }
        
        .qty-col {
            width: 15%;
            text-align: center;
        }
        
        .rate-col {
            width: 20%;
            text-align: right;
        }
        
        .amount-col {
            width: 25%;
            text-align: right;
            font-weight: bold;
        }
        
        .product-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 2px;
        }
        
        .product-desc {
            color: #666;
            font-size: 11px;
        }
        
        /* Compact Summary */
        .summary-section {
            float: right;
            width: 300px;
            margin-bottom: 20px;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .summary-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .summary-label {
            background: #f8f9fa;
            font-weight: 500;
            color: #555;
        }
        
        .summary-amount {
            text-align: right;
            font-weight: bold;
        }
        
        .total-row {
            background: #2c5aa0;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        
        .total-row td {
            border-bottom: none;
            padding: 12px;
        }
        
        /* Amount in Words - Compact */
        .amount-words {
            clear: both;
            background: #e8f5e8;
            padding: 12px 15px;
            border-radius: 6px;
            border-left: 4px solid #28a745;
            margin-bottom: 20px;
        }
        
        .amount-words-label {
            font-size: 12px;
            font-weight: bold;
            color: #155724;
            margin-bottom: 4px;
        }
        
        .amount-words-text {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            font-style: italic;
        }
        
        /* Compact Notes */
        .notes {
            margin-bottom: 15px;
        }
        
        .notes-title {
            font-size: 13px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 6px;
        }
        
        .notes-content {
            background: #fff3cd;
            padding: 10px;
            border-radius: 4px;
            border-left: 3px solid #ffc107;
            font-size: 12px;
        }
        
        /* Compact Terms */
        .terms {
            font-size: 11px;
            color: #666;
            border-top: 1px solid #e9ecef;
            padding-top: 10px;
            margin-bottom: 15px;
        }
        
        .terms ul {
            margin: 0;
            padding-left: 15px;
            columns: 2;
            column-gap: 20px;
        }
        
        .terms li {
            margin-bottom: 4px;
            break-inside: avoid;
        }
        
        /* Signature Section - Compact */
        .signatures {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px 10px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 11px;
            font-weight: bold;
            color: #666;
        }
        
        /* Footer - Compact */
        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #e9ecef;
            padding-top: 10px;
        }
        
        /* Utility Classes */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-muted { color: #666; }
        
        /* Page optimization */
        @page {
            margin: 20mm;
            size: A4;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        /* No page breaks in critical sections */
        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Compact Header -->
        <div class="header">
            <div class="header-left">
                @if($tenant->logo)
                    <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }}" class="logo">
                @endif
                <div class="company-name">{{ $tenant->name }}</div>
                <div class="company-details">
                    @if($tenant->address){{ $tenant->address }}<br>@endif
                    @if($tenant->phone)Tel: {{ $tenant->phone }}@endif
                    @if($tenant->email) | Email: {{ $tenant->email }}@endif
                    @if($tenant->website)<br>{{ $tenant->website }}@endif
                    @if($tenant->tax_number) | Tax ID: {{ $tenant->tax_number }}@endif
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number"># {{ $invoice->voucherType->prefix ?? '' }}{{ str_pad($invoice->voucher_number, 4, '0', STR_PAD_LEFT) }}</div>
                <div class="invoice-date">
                    <strong>Date:</strong> {{ $invoice->voucher_date->format('d M, Y') }}<br>
                    @if($invoice->reference_number)
                        <strong>Ref:</strong> {{ $invoice->reference_number }}<br>
                    @endif
                    <strong>Status:</strong> {{ ucfirst($invoice->status) }}
                </div>
            </div>
        </div>

        <!-- Compact Billing Information -->
        <div class="billing-info">
            <div class="bill-to">
                <div class="section-title">Bill To</div>
                @if($customer)
                    <div class="customer-name">
                        @if($customer->customer_type === 'business' || !empty($customer->company_name))
                            {{ $customer->company_name ?? $customer->name }}
                        @else
                            {{ trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) }}
                        @endif
                    </div>
                    @if($customer->address || ($customer->address_line1 ?? false))
                        <div class="detail-line">{{ $customer->address ?? $customer->address_line1 }}</div>
                        @if($customer->address_line2 ?? false)
                            <div class="detail-line">{{ $customer->address_line2 }}</div>
                        @endif
                        @if(($customer->city ?? false) || ($customer->state ?? false))
                            <div class="detail-line">{{ $customer->city ?? '' }} {{ $customer->state ?? '' }} {{ $customer->postal_code ?? '' }}</div>
                        @endif
                    @endif
                    @if($customer->phone)
                        <div class="detail-line">Phone: {{ $customer->phone }}</div>
                    @endif
                    @if($customer->email)
                        <div class="detail-line">Email: {{ $customer->email }}</div>
                    @endif
                    @if($customer->tax_id ?? false)
                        <div class="detail-line">Tax ID: {{ $customer->tax_id }}</div>
                    @endif
                @else
                    <div class="customer-name">Walk-in Customer</div>
                    <div class="detail-line">Cash Sale</div>
                @endif
            </div>
            
            <div class="invoice-info">
                <div class="section-title">Invoice Details</div>
                <div class="detail-line"><strong>Payment Terms:</strong> {{ $customer->payment_terms ?? '30 Days' }}</div>
                <div class="detail-line"><strong>Due Date:</strong> {{ $invoice->voucher_date->addDays(30)->format('d M, Y') }}</div>
                <div class="detail-line"><strong>Currency:</strong> Nigerian Naira (₦)</div>
                @if($invoice->created_by)
                    <div class="detail-line"><strong>Prepared By:</strong> {{ $invoice->createdBy->name ?? 'System' }}</div>
                @endif
                @if($invoice->posted_at)
                    <div class="detail-line"><strong>Posted:</strong> {{ $invoice->posted_at->format('d M, Y') }}</div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        @if($invoice->items && $invoice->items->count() > 0)
        <div class="no-break">
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="sn-col">S/N</th>
                        <th class="desc-col">Description</th>
                        <th class="qty-col">Qty</th>
                        <th class="rate-col">Unit Price</th>
                        <th class="amount-col">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotal = 0; @endphp
                    @foreach($invoice->items as $index => $item)
                        @php $subtotal += $item->amount; @endphp
                        <tr>
                            <td class="sn-col">{{ $index + 1 }}</td>
                            <td class="desc-col">
                                <div class="product-name">{{ $item->product_name }}</div>
                                @if($item->description && $item->description !== $item->product_name)
                                    <div class="product-desc">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td class="qty-col">{{ number_format($item->quantity, 2) }}</td>
                            <td class="rate-col">₦{{ number_format($item->rate, 2) }}</td>
                            <td class="amount-col">₦{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Summary -->
        <div class="summary-section no-break">
            <table class="summary-table">
                @php 
                    $taxAmount = $invoice->items->sum('tax') ?? 0;
                    $discountAmount = $invoice->items->sum('discount') ?? 0;
                    $totalAmount = $subtotal - $discountAmount + $taxAmount;
                @endphp
                <tr>
                    <td class="summary-label">Subtotal:</td>
                    <td class="summary-amount">₦{{ number_format($subtotal, 2) }}</td>
                </tr>
                @if($discountAmount > 0)
                <tr>
                    <td class="summary-label">Discount:</td>
                    <td class="summary-amount">-₦{{ number_format($discountAmount, 2) }}</td>
                </tr>
                @endif
                @if($taxAmount > 0)
                <tr>
                    <td class="summary-label">Tax:</td>
                    <td class="summary-amount">₦{{ number_format($taxAmount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>TOTAL:</td>
                    <td>₦{{ number_format($totalAmount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words no-break">
            <div class="amount-words-label">Amount in Words:</div>
            <div class="amount-words-text">{{ ucfirst(numberToWords($totalAmount)) }} Naira Only</div>
        </div>

        <!-- Notes -->
        @if($invoice->narration)
        <div class="notes">
            <div class="notes-title">Notes:</div>
            <div class="notes-content">{{ $invoice->narration }}</div>
        </div>
        @endif

        <!-- Terms -->
        <div class="terms">
            <strong>Terms & Conditions:</strong>
            <ul>
                <li>Payment due within {{ $customer->payment_terms ?? '30 days' }}</li>
                <li>Late payments subject to 2% monthly charge</li>
                <li>Disputes reported within 7 days</li>
                <li>Goods sold not returnable unless defective</li>
            </ul>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">Customer Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">For {{ $tenant->name }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <strong>{{ $tenant->name }}</strong> | Generated: {{ now()->format('d M Y, g:i A') }} | Thank you for your business!
        </div>
    </div>

@php
function numberToWords($number) {
    if ($number == 0) return 'Zero';
    
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
             'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
             'Seventeen', 'Eighteen', 'Nineteen'];
    
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    
    $number = (int) $number;
    
    if ($number < 20) {
        return $ones[$number];
    } elseif ($number < 100) {
        return $tens[intval($number / 10)] . ($number % 10 ? ' ' . $ones[$number % 10] : '');
    } elseif ($number < 1000) {
        return $ones[intval($number / 100)] . ' Hundred' . ($number % 100 ? ' ' . numberToWords($number % 100) : '');
    } elseif ($number < 1000000) {
        return numberToWords(intval($number / 1000)) . ' Thousand' . ($number % 1000 ? ' ' . numberToWords($number % 1000) : '');
    } elseif ($number < 1000000000) {
        return numberToWords(intval($number / 1000000)) . ' Million' . ($number % 1000000 ? ' ' . numberToWords($number % 1000000) : '');
    }
    
    return 'Amount too large';
}
@endphp
</body>
</html>
