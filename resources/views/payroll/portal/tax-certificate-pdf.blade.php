<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Certificate {{ $year }}</title>
    <style>
        @page {
            margin: 5mm;
            size: A4;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #333;
            background-color: #fff;
        }

        .container {
            border: 2px solid #4F46E5;
            padding: 15px;
            position: relative;
        }        .watermark {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
            font-weight: bold;
            white-space: nowrap;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 24px;
            color: #111827;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header p {
            font-size: 14px;
            color: #6B7280;
            margin: 5px 0 0;
        }

        .row-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: separate;
            border-spacing: 10px 0; /* Horizontal spacing between cells */
        }

        .col-half-td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .section-box {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 4px;
            padding: 10px;
            height: 140px; /* Fixed height to align columns */
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 8px;
            text-transform: uppercase;
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 3px 0;
            vertical-align: top;
        }

        .label {
            color: #6B7280;
            font-weight: 500;
            width: 35%;
        }

        .value {
            color: #111827;
            font-weight: 600;
        }

        .summary-table {
            width: 100%;
            margin-top: 5px;
            border: 1px solid #E5E7EB;
        }

        .summary-table th, .summary-table td {
            padding: 8px 12px;
            border: 1px solid #E5E7EB;
            text-align: left;
        }

        .summary-table th {
            background-color: #F3F4F6;
            font-weight: bold;
            color: #374151;
        }

        .amount {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }

        .statement {
            margin-top: 20px;
            padding: 15px;
            background: #FEF3C7;
            border: 1px solid #FCD34D;
            border-radius: 4px;
            font-size: 11px;
            text-align: justify;
            line-height: 1.5;
        }

        .signatures {
            margin-top: 30px;
            width: 100%;
        }

        .sig-box {
            width: 45%;
            display: inline-block;
            text-align: center;
        }

        .sig-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
            font-weight: bold;
        }

        .footer {
            position: absolute;
            bottom: 10px;
            left: 20px;
            right: 20px;
            font-size: 9px;
            color: #9CA3AF;
            border-top: 1px solid #E5E7EB;
            padding-top: 10px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-green { color: #059669; }
        .text-red { color: #DC2626; }
        .text-blue { color: #2563EB; }
    </style>
</head>
<body>
    <div class="container">
        <div class="watermark">TAX CERTIFICATE</div>

        <div class="header">
            <h1>Annual Tax Certificate</h1>
            <p>Tax Year: <strong>{{ $year }}</strong></p>
        </div>

        <table class="row-table">
            <tr>
                <td class="col-half-td">
                    <div class="section-box">
                        <div class="section-title">Employer Details</div>
                        <table>
                            <tr>
                                <td class="label">Name:</td>
                                <td class="value">{{ $employee->tenant->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Address:</td>
                                <td class="value">{{ $employee->tenant->address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Date:</td>
                                <td class="value">{{ now()->format('d M, Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td class="col-half-td">
                    <div class="section-box">
                        <div class="section-title">Employee Details</div>
                        <table>
                            <tr>
                                <td class="label">Name:</td>
                                <td class="value">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                            </tr>
                            <tr>
                                <td class="label">ID Number:</td>
                                <td class="value">{{ $employee->employee_number }}</td>
                            </tr>
                            <tr>
                                <td class="label">TIN:</td>
                                <td class="value">{{ $employee->tin ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Designation:</td>
                                <td class="value">{{ $employee->job_title }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <div style="width: 100%; margin-bottom: 15px;">
            <div class="section-title" style="margin-top: 10px;">Financial Summary</div>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th style="text-align: right;">Amount (₦)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Gross Income</td>
                        <td class="amount text-green">{{ number_format($taxData->total_gross ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Consolidated Relief Allowance & Exemptions</td>
                        <td class="amount">{{ number_format($employee->annual_relief ?? 200000, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Taxable Income</td>
                        <td class="amount">{{ number_format(($taxData->total_gross ?? 0) - ($employee->annual_relief ?? 200000), 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Tax Paid (PAYE)</td>
                        <td class="amount text-red">{{ number_format($taxData->total_tax ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Pension Contribution</td>
                        <td class="amount">{{ number_format($taxData->total_pension ?? 0, 2) }}</td>
                    </tr>
                    <tr style="background-color: #EFF6FF;">
                        <td class="font-bold">Net Income Received</td>
                        <td class="amount text-blue">{{ number_format($taxData->total_net ?? 0, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="statement">
            <strong>CERTIFICATION:</strong><br>
            This is to certify that the above-named employee was employed by <strong>{{ $employee->tenant->name ?? 'the company' }}</strong> during the tax year <strong>{{ $year }}</strong>.
            The total emoluments earned and tax deducted as Pay-As-You-Earn (PAYE) are correctly stated above in accordance with the Personal Income Tax Act (PITA) and relevant tax regulations of Nigeria.
            This certificate is issued for tax filing purposes with the Federal Inland Revenue Service (FIRS) or State Internal Revenue Service.
        </div>

        <div class="signatures">
            <div class="sig-box">
                <div class="sig-line">Authorized Signatory</div>
                <div style="font-size: 10px; color: #666;">For: {{ $employee->tenant->name ?? 'Employer' }}</div>
            </div>
            <div class="sig-box" style="float: right;">
                <div class="sig-line">Employee Signature</div>
                <div style="font-size: 10px; color: #666;">Date: _________________</div>
            </div>
        </div>

        <div class="footer">
            <table style="width: 100%">
                <tr>
                    <td>
                            Certificate ID: <strong>TAX-{{ $employee->employee_number }}-{{ $year }}</strong><br>
                            Generated via Ballie Payroll System<br>
                            &copy; 2025 All Rights Reserved. Ballie Tech Solution<br>
                            Version 1.0.0
                        </td>
                    <td class="text-right">
                        Page 1 of 1<br>
                        Valid without seal if verified online
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
