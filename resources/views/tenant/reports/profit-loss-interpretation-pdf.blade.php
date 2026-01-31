<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss AI Interpretation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #8b5cf6;
            padding-bottom: 15px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #8b5cf6;
        }
        .report-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .report-date {
            font-size: 12px;
            margin-bottom: 10px;
            color: #666;
        }
        .summary-box {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
            border-radius: 5px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .summary-label {
            font-weight: bold;
            color: #374151;
        }
        .summary-value {
            color: #111827;
        }
        .interpretation-content {
            margin-top: 20px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #e5e7eb;
        }
        h1, h2, h3, h4 {
            color: #8b5cf6;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        h1 { font-size: 18px; }
        h2 { font-size: 16px; }
        h3 { font-size: 14px; }
        h4 { font-size: 12px; }
        p {
            margin-bottom: 10px;
            color: #374151;
        }
        ul, ol {
            margin-left: 20px;
            margin-bottom: 15px;
        }
        li {
            margin-bottom: 5px;
            color: #374151;
        }
        strong {
            color: #111827;
        }
        .profit-positive {
            color: #059669;
            font-weight: bold;
        }
        .profit-negative {
            color: #dc2626;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
        .ai-badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: #f3e8ff;
            color: #8b5cf6;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        blockquote {
            border-left: 4px solid #8b5cf6;
            padding-left: 15px;
            margin: 15px 0;
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $tenant->name ?? 'Company Name' }}</div>
        <div class="report-title">
            <span class="ai-badge">🤖 BallieAI</span> Profit & Loss Interpretation
        </div>
        <div class="report-subtitle">AI-Powered Financial Analysis & Insights</div>
        <div class="report-date">
            Period: {{ $fromDate !== 'N/A' && $toDate !== 'N/A' ? \Carbon\Carbon::parse($fromDate)->format('F d, Y') . ' to ' . \Carbon\Carbon::parse($toDate)->format('F d, Y') : $fromDate . ' to ' . $toDate }}
        </div>
        <div class="report-date">
            Generated on: {{ \Carbon\Carbon::now()->format('F d, Y \a\t h:i A') }}
        </div>
    </div>

    <!-- Financial Summary Box -->
    <div class="summary-box">
        <h3 style="margin-top: 0; color: #111827;">Financial Summary</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 5px 0;"><span class="summary-label">Total Income:</span></td>
                <td style="padding: 5px 0; text-align: right;"><span class="summary-value">₦{{ number_format($totalIncome, 2) }}</span></td>
            </tr>
            <tr>
                <td style="padding: 5px 0;"><span class="summary-label">Total Expenses:</span></td>
                <td style="padding: 5px 0; text-align: right;"><span class="summary-value">₦{{ number_format($totalExpenses, 2) }}</span></td>
            </tr>
            <tr style="border-top: 2px solid #e5e7eb;">
                <td style="padding: 8px 0;"><span class="summary-label">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}:</span></td>
                <td style="padding: 8px 0; text-align: right;">
                    <span class="{{ $netProfit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        ₦{{ number_format(abs($netProfit), 2) }}
                    </span>
                </td>
            </tr>
            @if($totalIncome > 0)
            <tr>
                <td style="padding: 5px 0;"><span class="summary-label">Profit Margin:</span></td>
                <td style="padding: 5px 0; text-align: right;">
                    <span class="summary-value">{{ number_format(($netProfit / $totalIncome) * 100, 2) }}%</span>
                </td>
            </tr>
            @endif
        </table>
    </div>

    <!-- AI Interpretation Content -->
    <div class="interpretation-content">
        @php
            // Convert markdown to HTML safely
            $html = $interpretation;

            // Headers
            $html = preg_replace('/^### (.*)$/m', '<h3>$1</h3>', $html);
            $html = preg_replace('/^## (.*)$/m', '<h2>$1</h2>', $html);
            $html = preg_replace('/^# (.*)$/m', '<h1>$1</h1>', $html);

            // Bold
            $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);

            // Lists
            $html = preg_replace('/^\d+\. (.*)$/m', '<li>$1</li>', $html);
            $html = preg_replace('/^[•\-] (.*)$/m', '<li>$1</li>', $html);

            // Paragraphs
            $html = nl2br($html);

            // Wrap lists
            $html = preg_replace('/(<li>.*?<\/li>)/s', '<ul>$1</ul>', $html);
        @endphp

        {!! $html !!}
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This interpretation was generated by BallieAI, an AI-powered accounting assistant.</p>
        <p>While we strive for accuracy, please consult with a qualified accountant for critical financial decisions.</p>
        <p>© {{ date('Y') }} {{ $tenant->name ?? 'Ballie' }} - All rights reserved</p>
    </div>
</body>
</html>
