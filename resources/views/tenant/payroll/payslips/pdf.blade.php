<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payrollRun->employee->full_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
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
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-email {
            font-size: 11px;
            opacity: 0.9;
        }
        .payslip-title {
            font-size: 10px;
            opacity: 0.9;
            margin-bottom: 3px;
        }
        .period-name {
            font-size: 16px;
            font-weight: bold;
        }
        .section {
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        .section-header {
            background-color: #f9fafb;
            padding: 12px 15px;
            font-weight: bold;
            font-size: 13px;
            color: #1f2937;
            border-bottom: 1px solid #e5e7eb;
        }
        .section-content {
            padding: 15px;
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
            padding: 8px 10px;
            width: 25%;
            vertical-align: top;
        }
        .info-label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 3px;
        }
        .info-value {
            font-size: 12px;
            font-weight: 600;
            color: #111827;
        }
        .pay-item {
            display: table;
            width: 100%;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .pay-item:last-child {
            border-bottom: none;
        }
        .pay-label {
            display: table-cell;
            color: #4b5563;
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
            background-color: #f0fdf4;
            padding: 12px 15px;
            margin-top: 10px;
            border-radius: 6px;
            display: table;
            width: 100%;
        }
        .total-label {
            display: table-cell;
            font-weight: bold;
            color: #166534;
            width: 70%;
        }
        .total-amount {
            display: table-cell;
            text-align: right;
            font-weight: bold;
            color: #166534;
            font-size: 14px;
            width: 30%;
        }
        .deduction-total {
            background-color: #fef2f2;
        }
        .deduction-total .total-label,
        .deduction-total .total-amount {
            color: #991b1b;
        }
        .net-pay-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
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
            font-size: 11px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .net-pay-amount {
            font-size: 28px;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .footer {
            text-align: center;
            padding: 15px;
            background-color: #f9fafb;
            border-top: 2px solid #e5e7eb;
            margin-top: 30px;
            font-size: 9px;
            color: #6b7280;
            border-radius: 6px;
        }
        .tax-grid {
            display: table;
            width: 100%;
        }
        .tax-item {
            display: table-cell;
            width: 25%;
            padding: 8px;
        }
        .bank-grid {
            display: table;
            width: 100%;
        }
        .bank-item {
            display: table-cell;
            width: 33.33%;
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <div class="company-name">{{ $tenant->name }}</div>
                    <div class="company-email">{{ $tenant->email }}</div>
                </div>
                <div class="header-right">
                    <div class="payslip-title">PAYSLIP</div>
                    <div class="period-name">{{ $payrollRun->payrollPeriod->name }}</div>
                </div>
            </div>
        </div>

        <!-- Employee Information -->
        <div class="section">
            <div class="section-header">Employee Information</div>
            <div class="section-content">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-cell">
                            <div class="info-label">Employee Name</div>
                            <div class="info-value">{{ $payrollRun->employee->full_name }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Employee Number</div>
                            <div class="info-value">{{ $payrollRun->employee->employee_number ?? 'N/A' }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Department</div>
                            <div class="info-value">{{ $payrollRun->employee->department->name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Position</div>
                            <div class="info-value">{{ $payrollRun->employee->job_title ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pay Period -->
        <div class="section">
            <div class="section-header">Pay Period Details</div>
            <div class="section-content">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-cell">
                            <div class="info-label">Period Start</div>
                            <div class="info-value">{{ $payrollRun->payrollPeriod->start_date->format('d M Y') }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Period End</div>
                            <div class="info-value">{{ $payrollRun->payrollPeriod->end_date->format('d M Y') }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Pay Date</div>
                            <div class="info-value">{{ $payrollRun->payrollPeriod->pay_date->format('d M Y') }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                <span class="status-badge status-{{ $payrollRun->payment_status }}">
                                    {{ ucfirst($payrollRun->payment_status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings -->
        <div class="section">
            <div class="section-header">Earnings</div>
            <div class="section-content">
                <div class="pay-item">
                    <div class="pay-label">Basic Salary</div>
                    <div class="pay-amount">₦{{ number_format($payrollRun->basic_salary, 2) }}</div>
                </div>
                @foreach($payrollRun->details->where('component_type', 'earning') as $detail)
                <div class="pay-item">
                    <div class="pay-label">{{ $detail->component_name }}</div>
                    <div class="pay-amount">₦{{ number_format($detail->amount, 2) }}</div>
                </div>
                @endforeach
                <div class="total-row">
                    <div class="total-label">Gross Salary</div>
                    <div class="total-amount">₦{{ number_format($payrollRun->gross_salary, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Deductions -->
        <div class="section">
            <div class="section-header">Deductions</div>
            <div class="section-content">
                @if($payrollRun->monthly_tax > 0)
                <div class="pay-item">
                    <div class="pay-label">PAYE Tax</div>
                    <div class="pay-amount">₦{{ number_format($payrollRun->monthly_tax, 2) }}</div>
                </div>
                @endif
                @if($payrollRun->nsitf_contribution > 0)
                <div class="pay-item">
                    <div class="pay-label">NSITF</div>
                    <div class="pay-amount">₦{{ number_format($payrollRun->nsitf_contribution, 2) }}</div>
                </div>
                @endif
                @foreach($payrollRun->details->where('component_type', 'deduction') as $detail)
                <div class="pay-item">
                    <div class="pay-label">{{ $detail->component_name }}</div>
                    <div class="pay-amount">₦{{ number_format($detail->amount, 2) }}</div>
                </div>
                @endforeach
                <div class="total-row deduction-total">
                    <div class="total-label">Total Deductions</div>
                    <div class="total-amount">₦{{ number_format($payrollRun->total_deductions, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Net Pay -->
        <div class="net-pay-section">
            <div class="net-pay-content">
                <div class="net-pay-left">
                    <div class="net-pay-label">Net Pay</div>
                    <div class="net-pay-amount">₦{{ number_format($payrollRun->net_salary, 2) }}</div>
                </div>
                <div class="net-pay-right">
                    @if($payrollRun->paid_at)
                    <div style="font-size: 10px; opacity: 0.9;">
                        Paid: {{ $payrollRun->paid_at->format('d M Y H:i') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tax Information -->
        @if($payrollRun->monthly_tax > 0)
        <div class="section">
            <div class="section-header">Tax Information</div>
            <div class="section-content">
                <div class="tax-grid">
                    <div class="tax-item">
                        <div class="info-label">Annual Gross</div>
                        <div class="info-value">₦{{ number_format($payrollRun->annual_gross, 2) }}</div>
                    </div>
                    <div class="tax-item">
                        <div class="info-label">Consolidated Relief</div>
                        <div class="info-value">₦{{ number_format($payrollRun->consolidated_relief, 2) }}</div>
                    </div>
                    <div class="tax-item">
                        <div class="info-label">Taxable Income</div>
                        <div class="info-value">₦{{ number_format($payrollRun->taxable_income, 2) }}</div>
                    </div>
                    <div class="tax-item">
                        <div class="info-label">Annual Tax</div>
                        <div class="info-value">₦{{ number_format($payrollRun->annual_tax, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Bank Details -->
        @if($payrollRun->employee->bank_name)
        <div class="section">
            <div class="section-header">Bank Details</div>
            <div class="section-content">
                <div class="bank-grid">
                    <div class="bank-item">
                        <div class="info-label">Bank Name</div>
                        <div class="info-value">{{ $payrollRun->employee->bank_name }}</div>
                    </div>
                    <div class="bank-item">
                        <div class="info-label">Account Number</div>
                        <div class="info-value">{{ $payrollRun->employee->account_number }}</div>
                    </div>
                    <div class="bank-item">
                        <div class="info-label">Account Name</div>
                        <div class="info-value">{{ $payrollRun->employee->account_name }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            This is a computer-generated payslip and does not require a signature.
            <br>
            Generated on {{ now()->format('d M Y H:i:s') }}
        </div>
    </div>
</body>
</html>
