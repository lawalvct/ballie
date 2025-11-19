<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $employee->first_name }} {{ $employee->last_name }}</title>
    <style>
        @page {
            size: A4;
            margin: 8mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            line-height: 1.4;
            color: #1f2937;
            background: white;
        }
        .container {
            width: 100%;
            max-width: 194mm;
            margin: 0 auto;
        }
        .company-header {
            text-align: center;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 8px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 2px;
        }
        .company-address {
            font-size: 8px;
            color: #6b7280;
        }
        .header {
            padding: 8px 12px;
            margin-bottom: 8px;
            border-bottom: 2px solid #f59e0b;
            background: #1e40af;
            color: white;
        }
        .header-content {
            display: table;
            width: 100%;
        }
        .header-left, .header-right {
            display: table-cell;
            vertical-align: middle;
        }
        .header-right {
            text-align: right;
        }
        .payslip-title {
            font-size: 8px;
            opacity: 0.85;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .period-name {
            font-size: 13px;
            font-weight: bold;
        }
        .section {
            margin-bottom: 8px;
            border: 1px solid #d1d5db;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .section-header {
            background-color: #f3f4f6;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 9px;
            color: #111827;
            border-bottom: 1px solid #d1d5db;
            text-transform: uppercase;
        }
        .section-content {
            padding: 8px;
        }
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            padding: 5px 8px;
            width: 25%;
            vertical-align: top;
            border-right: 1px solid #e5e7eb;
        }
        .info-cell:last-child {
            border-right: none;
        }
        .info-label {
            font-size: 7px;
            color: #6b7280;
            margin-bottom: 1px;
            text-transform: uppercase;
        }
        .info-value {
            font-size: 9px;
            font-weight: 600;
            color: #111827;
        }
        .main-content-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
            border-spacing: 8px;
            margin: 0 -4px;
        }
        .main-content-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .pay-item {
            display: table;
            width: 100%;
            padding: 4px 6px;
            border-bottom: 1px solid #e5e7eb;
        }
        .pay-item:last-child {
            border-bottom: none;
        }
        .pay-label {
            display: table-cell;
            color: #374151;
            width: 70%;
        }
        .pay-amount {
            display: table-cell;
            text-align: right;
            font-weight: 600;
            color: #111827;
            width: 30%;
        }
        .total-row {
            background-color: #e5e7eb;
            padding: 6px 8px;
            margin-top: 5px;
            display: table;
            width: 100%;
            font-weight: bold;
        }
        .total-label {
            display: table-cell;
            color: #111827;
            width: 70%;
            font-size: 10px;
        }
        .total-amount {
            display: table-cell;
            text-align: right;
            color: #111827;
            font-size: 10px;
            width: 30%;
        }
        .deduction-total {
            background-color: #fee2e2;
        }
        .deduction-total .total-label,
        .deduction-total .total-amount {
            color: #991b1b;
        }
        .net-pay-section {
            background: #1e40af;
            color: white;
            padding: 12px;
            margin: 8px 0;
            border-left: 5px solid #f59e0b;
            page-break-inside: avoid;
        }
        .net-pay-content {
            display: table;
            width: 100%;
        }
        .net-pay-left, .net-pay-right {
            display: table-cell;
            vertical-align: middle;
        }
        .net-pay-right {
            text-align: right;
        }
        .net-pay-label {
            font-size: 9px;
            opacity: 0.85;
            text-transform: uppercase;
        }
        .net-pay-amount {
            font-size: 20px;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 9px;
            font-size: 8px;
            font-weight: 600;
        }
        .status-paid { background-color: #d1fae5; color: #065f46; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-failed { background-color: #fee2e2; color: #991b1b; }
        .footer {
            text-align: center;
            padding: 8px;
            border-top: 1px solid #1e40af;
            margin-top: 8px;
            font-size: 7px;
            color: #6b7280;
            page-break-inside: avoid;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72px;
            color: rgba(0, 0, 0, 0.025);
            font-weight: bold;
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="watermark">{{ strtoupper($employee->tenant->name ?? 'PAYSLIP') }}</div>
    <div class="container">
        <!-- Company Header -->
        <div class="company-header">
            <div class="company-name">{{ $employee->tenant->name ?? 'Company Name' }}</div>
            <div class="company-address">
                {{ $employee->tenant->email ?? '' }}
                @if($employee->tenant->phone ?? null) | {{ $employee->tenant->phone }} @endif
                @if($employee->tenant->address ?? null) <br>{{ $employee->tenant->address }} @endif
            </div>
        </div>

        <!-- Payslip Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <div class="payslip-title">Payslip</div>
                    <div class="period-name">{{ $payslip->payrollPeriod->name ?? 'N/A' }}</div>
                </div>
                <div class="header-right">
                    <div class="info-label" style="color: rgba(255,255,255,0.85);">PAYMENT DATE</div>
                    <div class="info-value" style="color: white; font-size: 10px;">
                        {{ $payslip->payment_date ? \Carbon\Carbon::parse($payslip->payment_date)->format('d M Y') : 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee & Pay Period Info -->
        <div class="section">
            <div class="section-header">Employee & Pay Period Details</div>
            <div class="section-content" style="padding: 0;">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-cell">
                            <div class="info-label">Employee Name</div>
                            <div class="info-value">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Employee Number</div>
                            <div class="info-value">{{ $employee->employee_number }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Department</div>
                            <div class="info-value">{{ $employee->department->name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Position</div>
                            <div class="info-value">{{ $employee->job_title }}</div>
                        </div>
                    </div>
                    <div class="info-row" style="background-color: #f9fafb;">
                        <div class="info-cell">
                            <div class="info-label">Period Start</div>
                            <div class="info-value">{{ $payslip->payrollPeriod->start_date->format('d M Y') }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Period End</div>
                            <div class="info-value">{{ $payslip->payrollPeriod->end_date->format('d M Y') }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Pay Date</div>
                            <div class="info-value">{{ $payslip->payrollPeriod->pay_date->format('d M Y') }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                <span class="status-badge status-{{ $payslip->status }}">
                                    {{ ucfirst($payslip->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings & Deductions -->
        <div class="main-content-grid">
            <div class="main-content-cell">
                <div class="section">
                    <div class="section-header">Earnings</div>
                    <div class="section-content">
                        @php
                            $earnings = $payslip->details->where('type', 'earning');
                            $totalEarnings = $earnings->sum('amount');
                        @endphp
                        @forelse($earnings as $earning)
                        <div class="pay-item">
                            <div class="pay-label">{{ $earning->salaryComponent->name ?? $earning->description }}</div>
                            <div class="pay-amount">₦{{ number_format($earning->amount, 2) }}</div>
                        </div>
                        @empty
                        <div class="pay-item">
                            <div class="pay-label">Basic Salary</div>
                            <div class="pay-amount">₦{{ number_format($payslip->gross_salary, 2) }}</div>
                        </div>
                        @endforelse
                        <div class="total-row">
                            <div class="total-label">Gross Salary</div>
                            <div class="total-amount">₦{{ number_format($payslip->gross_salary, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main-content-cell">
                <div class="section">
                    <div class="section-header">Deductions</div>
                    <div class="section-content">
                        @php
                            $deductions = $payslip->details->where('type', 'deduction');
                            $totalDeductions = $deductions->sum('amount');
                        @endphp
                        @if($payslip->monthly_tax > 0)
                        <div class="pay-item">
                            <div class="pay-label">PAYE Tax</div>
                            <div class="pay-amount">₦{{ number_format($payslip->monthly_tax, 2) }}</div>
                        </div>
                        @endif
                        @foreach($deductions as $deduction)
                        <div class="pay-item">
                            <div class="pay-label">{{ $deduction->salaryComponent->name ?? $deduction->description }}</div>
                            <div class="pay-amount">₦{{ number_format($deduction->amount, 2) }}</div>
                        </div>
                        @endforeach
                        <div class="total-row deduction-total">
                            <div class="total-label">Total Deductions</div>
                            <div class="total-amount">₦{{ number_format($totalDeductions + $payslip->monthly_tax, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Pay -->
        <div class="net-pay-section">
            <div class="net-pay-content">
                <div class="net-pay-left">
                    <div class="net-pay-label">Net Pay</div>
                    <div class="net-pay-amount">₦{{ number_format($payslip->net_salary, 2) }}</div>
                </div>
                <div class="net-pay-right">
                    <div style="font-size: 9px; opacity: 0.9;">
                        Employee Portal Generated
                    </div>
                </div>
            </div>
        </div>

        <!-- Tax & Bank Info -->
        <div class="main-content-grid">
            @if($payslip->monthly_tax > 0)
            <div class="main-content-cell">
                <div class="section">
                    <div class="section-header">Tax Information</div>
                    <div class="section-content" style="padding:0;">
                        <div class="info-grid">
                            <div class="info-row">
                                <div class="info-cell" style="width: 50%;">
                                    <div class="info-label">Monthly Tax (PAYE)</div>
                                    <div class="info-value">₦{{ number_format($payslip->monthly_tax, 2) }}</div>
                                </div>
                                <div class="info-cell" style="width: 50%;">
                                    <div class="info-label">Tax Relief</div>
                                    <div class="info-value">₦{{ number_format($employee->annual_relief ?? 200000, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if($employee->bank_name)
            <div class="main-content-cell">
                <div class="section">
                    <div class="section-header">Bank Details</div>
                    <div class="section-content" style="padding: 5px 8px;">
                        <div class="info-grid">
                            <div class="info-row">
                                <div class="info-cell" style="width: 50%; border: none; padding: 4px;">
                                    <div class="info-label">Bank Name</div>
                                    <div class="info-value">{{ $employee->bank_name }}</div>
                                </div>
                                <div class="info-cell" style="width: 50%; border: none; padding: 4px;">
                                    <div class="info-label">Account Number</div>
                                    <div class="info-value">{{ $employee->account_number }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <strong>{{ $employee->tenant->name ?? 'Company Name' }}</strong> | This is a computer-generated payslip and does not require a signature.<br>
            Generated on {{ now()->format('d M Y H:i:s') }} | Document ID: PAY-{{ $employee->employee_number }}-{{ $payslip->id }}<br>
            &copy; 2025 All Rights Reserved. Ballie Tech Solution | Version 1.0.0
        </div>
    </div>
</body>
</html>
