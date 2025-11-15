<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overtime Payment Slip - {{ $overtime->employee->full_name }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #1f2937;
            background: white;
        }
        .container {
            width: 100%;
            max-width: 190mm;
            margin: 0 auto;
        }
        .company-header {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 3px;
        }
        .company-address {
            font-size: 9px;
            color: #6b7280;
            line-height: 1.4;
        }
        .doc-header {
            text-align: center;
            padding: 12px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            border-radius: 4px;
        }
        .doc-title {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .doc-subtitle {
            font-size: 9px;
            opacity: 0.9;
        }
        .section {
            margin-bottom: 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .section-header {
            background-color: #f3f4f6;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 10px;
            color: #111827;
            border-bottom: 1px solid #d1d5db;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-content {
            padding: 10px 12px;
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
            padding: 6px 8px;
            width: 25%;
            vertical-align: top;
            border-right: 1px solid #f3f4f6;
        }
        .info-cell:last-child {
            border-right: none;
        }
        .info-cell-wide {
            width: 50%;
        }
        .info-label {
            font-size: 8px;
            color: #6b7280;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-value {
            font-size: 10px;
            font-weight: 600;
            color: #111827;
        }
        .detail-item {
            display: table;
            width: 100%;
            padding: 6px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            display: table-cell;
            color: #4b5563;
            width: 40%;
            font-weight: 500;
        }
        .detail-value {
            display: table-cell;
            text-align: right;
            font-weight: 600;
            color: #111827;
            width: 60%;
        }
        .amount-section {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            border-left: 5px solid #f59e0b;
            page-break-inside: avoid;
        }
        .amount-section.pending {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
        }
        .amount-content {
            display: table;
            width: 100%;
        }
        .amount-left, .amount-right {
            display: table-cell;
            vertical-align: middle;
        }
        .amount-right {
            text-align: right;
        }
        .amount-label {
            font-size: 10px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .amount-value {
            font-size: 24px;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-paid { background-color: #d1fae5; color: #065f46; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        .payment-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .payment-pending { background-color: #fef3c7; color: #92400e; }
        .payment-paid { background-color: #d1fae5; color: #065f46; }
        .calculation-box {
            background-color: #f9fafb;
            padding: 10px;
            border-radius: 4px;
            margin-top: 8px;
            border-left: 3px solid #3b82f6;
        }
        .calculation-row {
            display: table;
            width: 100%;
            padding: 4px 0;
        }
        .calculation-label {
            display: table-cell;
            color: #4b5563;
            width: 60%;
        }
        .calculation-value {
            display: table-cell;
            text-align: right;
            font-weight: 600;
            color: #111827;
            width: 40%;
        }
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-grid {
            display: table;
            width: 100%;
            margin-top: 40px;
        }
        .signature-cell {
            display: table-cell;
            width: 50%;
            padding: 0 10px;
            vertical-align: top;
        }
        .signature-line {
            border-top: 2px solid #111827;
            margin-top: 30px;
            padding-top: 5px;
            text-align: center;
            font-size: 9px;
            font-weight: 600;
        }
        .signature-label {
            text-align: center;
            color: #6b7280;
            font-size: 8px;
            margin-top: 3px;
        }
        .footer {
            text-align: center;
            padding: 12px;
            border-top: 2px solid #1e40af;
            margin-top: 20px;
            font-size: 8px;
            color: #6b7280;
            page-break-inside: avoid;
        }
        .footer-strong {
            font-weight: bold;
            color: #1e40af;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(0, 0, 0, 0.02);
            font-weight: bold;
            z-index: -1;
            pointer-events: none;
        }
        .notes-box {
            background-color: #fffbeb;
            padding: 10px;
            border-radius: 4px;
            border-left: 3px solid #f59e0b;
            margin-top: 10px;
        }
        .notes-label {
            font-size: 9px;
            color: #92400e;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .notes-text {
            font-size: 9px;
            color: #78350f;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="watermark">{{ strtoupper($tenant->name) }}</div>
    <div class="container">
        <!-- Company Header -->
        <div class="company-header">
            <div class="company-name">{{ $tenant->name }}</div>
            <div class="company-address">
                {{ $tenant->email }}
                @if($tenant->phone) | {{ $tenant->phone }} @endif
                @if($tenant->address)
                    <br>{{ $tenant->address }}
                @endif
            </div>
        </div>

        <!-- Document Header -->
        <div class="doc-header">
            <div class="doc-title">Overtime Payment Slip</div>
            <div class="doc-subtitle">Official Overtime Compensation Document</div>
        </div>

        <!-- Employee Information -->
        <div class="section">
            <div class="section-header">Employee Information</div>
            <div class="section-content" style="padding: 0;">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-cell info-cell-wide">
                            <div class="info-label">Employee Name</div>
                            <div class="info-value">{{ $overtime->employee->full_name }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Employee Number</div>
                            <div class="info-value">{{ $overtime->employee->employee_number ?? 'N/A' }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Department</div>
                            <div class="info-value">{{ $overtime->employee->department->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="info-row" style="background-color: #f9fafb;">
                        <div class="info-cell">
                            <div class="info-label">Position</div>
                            <div class="info-value">{{ $overtime->employee->job_title ?? 'N/A' }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Overtime Number</div>
                            <div class="info-value">{{ $overtime->overtime_number }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Date</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('d M Y') }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                <span class="status-badge status-{{ $overtime->status }}">
                                    {{ ucfirst($overtime->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overtime Details -->
        <div class="section">
            <div class="section-header">Overtime Details</div>
            <div class="section-content">
                <div class="detail-item">
                    <div class="detail-label">Calculation Method</div>
                    <div class="detail-value">{{ ucfirst($overtime->calculation_method) }}</div>
                </div>

                @if($overtime->calculation_method === 'hourly')
                    <div class="detail-item">
                        <div class="detail-label">Overtime Type</div>
                        <div class="detail-value">{{ ucfirst(str_replace('_', ' ', $overtime->overtime_type)) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Time Period</div>
                        <div class="detail-value">
                            {{ \Carbon\Carbon::parse($overtime->start_time)->format('h:i A') }} -
                            {{ \Carbon\Carbon::parse($overtime->end_time)->format('h:i A') }}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Total Hours</div>
                        <div class="detail-value">{{ number_format($overtime->total_hours, 2) }} hours</div>
                    </div>
                @endif

                <div class="detail-item">
                    <div class="detail-label">Reason</div>
                    <div class="detail-value">{{ $overtime->reason }}</div>
                </div>

                @if($overtime->work_description)
                    <div class="detail-item">
                        <div class="detail-label">Work Description</div>
                        <div class="detail-value">{{ $overtime->work_description }}</div>
                    </div>
                @endif

                @if($overtime->calculation_method === 'hourly')
                    <!-- Calculation Breakdown -->
                    <div class="calculation-box">
                        <div class="calculation-row">
                            <div class="calculation-label">Hourly Rate</div>
                            <div class="calculation-value">₦{{ number_format($overtime->hourly_rate, 2) }}</div>
                        </div>
                        <div class="calculation-row">
                            <div class="calculation-label">Total Hours Worked</div>
                            <div class="calculation-value">{{ number_format($overtime->total_hours, 2) }} hrs</div>
                        </div>
                        <div class="calculation-row">
                            <div class="calculation-label">Rate Multiplier ({{ ucfirst($overtime->overtime_type) }})</div>
                            <div class="calculation-value">× {{ $overtime->multiplier }}</div>
                        </div>
                        <div class="calculation-row" style="border-top: 2px solid #d1d5db; margin-top: 5px; padding-top: 8px; font-weight: bold;">
                            <div class="calculation-label" style="color: #111827;">Total Amount</div>
                            <div class="calculation-value" style="color: #059669; font-size: 12px;">
                                ₦{{ number_format($overtime->total_amount, 2) }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Approval Information -->
        @if($overtime->status !== 'pending')
        <div class="section">
            <div class="section-header">
                @if($overtime->status === 'approved' || $overtime->status === 'paid')
                    Approval Information
                @else
                    Rejection Information
                @endif
            </div>
            <div class="section-content">
                @if($overtime->status === 'approved' || $overtime->status === 'paid')
                    <div class="detail-item">
                        <div class="detail-label">Approved By</div>
                        <div class="detail-value">{{ $overtime->approver->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Approved Date</div>
                        <div class="detail-value">{{ $overtime->approved_at ? \Carbon\Carbon::parse($overtime->approved_at)->format('d M Y H:i A') : 'N/A' }}</div>
                    </div>
                    @if($overtime->approved_hours)
                        <div class="detail-item">
                            <div class="detail-label">Approved Hours</div>
                            <div class="detail-value">{{ number_format($overtime->approved_hours, 2) }} hours</div>
                        </div>
                    @endif
                    @if($overtime->approval_remarks)
                        <div class="notes-box">
                            <div class="notes-label">Approval Remarks</div>
                            <div class="notes-text">{{ $overtime->approval_remarks }}</div>
                        </div>
                    @endif
                @elseif($overtime->status === 'rejected')
                    <div class="detail-item">
                        <div class="detail-label">Rejected By</div>
                        <div class="detail-value">{{ $overtime->rejector->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Rejected Date</div>
                        <div class="detail-value">{{ $overtime->rejected_at ? \Carbon\Carbon::parse($overtime->rejected_at)->format('d M Y H:i A') : 'N/A' }}</div>
                    </div>
                    @if($overtime->rejection_reason)
                        <div class="notes-box" style="background-color: #fef2f2; border-left-color: #ef4444;">
                            <div class="notes-label" style="color: #991b1b;">Rejection Reason</div>
                            <div class="notes-text" style="color: #7f1d1d;">{{ $overtime->rejection_reason }}</div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
        @endif

        <!-- Payment Information -->
        @if($overtime->status === 'approved' || $overtime->status === 'paid')
        <div class="section">
            <div class="section-header">Payment Information</div>
            <div class="section-content">
                <div class="detail-item">
                    <div class="detail-label">Payment Status</div>
                    <div class="detail-value">
                        <span class="payment-badge payment-{{ $overtime->is_paid ? 'paid' : 'pending' }}">
                            {{ $overtime->is_paid ? 'Paid' : 'Pending' }}
                        </span>
                    </div>
                </div>
                @if($overtime->is_paid && $overtime->paid_date)
                    <div class="detail-item">
                        <div class="detail-label">Payment Date</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($overtime->paid_date)->format('d M Y') }}</div>
                    </div>
                @endif
                @if($overtime->employee->bank_name)
                    <div class="detail-item">
                        <div class="detail-label">Bank Name</div>
                        <div class="detail-value">{{ $overtime->employee->bank_name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Account Number</div>
                        <div class="detail-value">{{ $overtime->employee->account_number }}</div>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Total Amount Section -->
        <div class="amount-section {{ $overtime->is_paid ? '' : 'pending' }}">
            <div class="amount-content">
                <div class="amount-left">
                    <div class="amount-label">
                        @if($overtime->is_paid)
                            Total Amount Paid
                        @else
                            Total Amount Payable
                        @endif
                    </div>
                    <div class="amount-value">₦{{ number_format($overtime->total_amount, 2) }}</div>
                </div>
                <div class="amount-right">
                    @if($overtime->is_paid && $overtime->paid_date)
                        <div style="font-size: 10px; opacity: 0.95;">
                            Paid on: {{ \Carbon\Carbon::parse($overtime->paid_date)->format('d M Y') }}
                        </div>
                    @elseif($overtime->status === 'approved')
                        <div style="font-size: 10px; opacity: 0.95;">
                            Awaiting Payment
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Signature Section -->
        @if($overtime->status === 'approved' || $overtime->status === 'paid')
        <div class="signature-section">
            <div class="signature-grid">
                <div class="signature-cell">
                    <div class="signature-line">
                        {{ $overtime->employee->full_name }}
                    </div>
                    <div class="signature-label">Employee Signature & Date</div>
                </div>
                <div class="signature-cell">
                    <div class="signature-line">
                        {{ $overtime->approver->name ?? 'Authorized Signatory' }}
                    </div>
                    <div class="signature-label">Approved By (Signature & Date)</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <span class="footer-strong">{{ $tenant->name }}</span> | This is a computer-generated overtime payment slip and serves as an official record.<br>
            Generated on {{ now()->format('d M Y H:i:s') }} | Document ID: OT-{{ $overtime->id }}-{{ date('Ymd') }}
            @if($overtime->is_paid)
            <br><strong>PAID</strong> - This document confirms payment has been processed.
            @endif
        </div>
    </div>
</body>
</html>
