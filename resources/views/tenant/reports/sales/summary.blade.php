@extends('layouts.tenant')

@section('title', 'Sales Summary Report')
@section('page-title', 'Sales Summary Report')
@section('page-description')
    <span class="hidden md:inline">Overview of sales performance and key metrics.</span>
@endsection
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>


            <a href="{{ route('tenant.reports.customer-sales', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">

            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
               Customer Sales Report
            </a>
                  <a href="{{ route('tenant.reports.product-sales', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                </svg>
               Product Sales Report
            </a>

            <a href="{{ route('tenant.reports.sales-by-period', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
               Sales by Period
            </a>
        </div>
        <div class="flex space-x-3">

            <a href="{{ route('tenant.reports.index', $tenant->slug) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Reports
            </a>
        </div>
    </div>





    <!-- Sales Register -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden sales-register-container">
        <!-- Report Header -->
        <div class="bg-teal-700 text-white px-6 py-3 flex items-center justify-between sales-register-header">
            <h3 class="text-lg font-bold">Sales Register</h3>
            <div class="text-sm">
                <span class="font-semibold">{{ $tenant->company_name ?? $tenant->name }}</span>
            </div>
            <div class="text-sm">
                {{ date('d-M-Y', strtotime($fromDate)) }} to {{ date('d-M-Y', strtotime($toDate)) }}
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="printSalesRegister(event); return false;"
                        type="button"
                        class="inline-flex items-center px-3 py-2 bg-teal-600 hover:bg-teal-500 border border-teal-500 rounded-md text-sm font-medium text-white transition-colors duration-150"
                        title="Print Sales Register">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Header Section -->
            <div class="mb-6 text-center">
                <h4 class="text-xl font-bold text-gray-900">Sales</h4>
                <p class="text-sm text-gray-600">{{ $tenant->company_name ?? $tenant->name }}</p>
                <p class="text-sm text-gray-600">{{ date('d-M-Y', strtotime($fromDate)) }} to {{ date('d-M-Y', strtotime($toDate)) }}</p>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="border-b-2 border-gray-400">
                            <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 bg-gray-100 border-r border-gray-300">Particulars</th>
                            <th class="px-4 py-2 text-center text-sm font-bold text-gray-700 bg-gray-100 border-r border-gray-300">Transactions</th>
                            <th class="px-4 py-2 text-right text-sm font-bold text-gray-700 bg-gray-100 border-r border-gray-300">Debit</th>
                            <th class="px-4 py-2 text-right text-sm font-bold text-gray-700 bg-gray-100 border-r border-gray-300">Credit</th>
                            <th class="px-4 py-2 text-right text-sm font-bold text-gray-700 bg-gray-100">Closing Balance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-cream">
                        @php
                            $runningBalance = 0;
                        @endphp
                        @forelse($salesTrend as $trend)
                            @php
                                $runningBalance += $trend->total_sales;
                            @endphp
                            <tr class="border-b border-gray-200 hover:bg-yellow-50">
                                <td class="px-4 py-3 text-sm border-r border-gray-200">
                                    <a href="{{ route('tenant.accounting.invoices.index', ['tenant' => $tenant->slug, 'from_date' => $trend->period_key . '-01', 'to_date' => date('Y-m-t', strtotime($trend->period_key . '-01'))]) }}" 
                                       class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                        {{ $trend->period }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-center border-r border-gray-200">{{ number_format($trend->invoice_count) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right border-r border-gray-200">--</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right border-r border-gray-200">{{ number_format($trend->total_sales, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">{{ number_format($runningBalance, 2) }} Cr</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No sales data available for this period</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($salesTrend->count() > 0)
                    <tfoot>
                        <tr class="border-t-2 border-gray-400 bg-gray-100 font-bold">
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-300">Grand Total</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-center border-r border-gray-300">{{ number_format($salesTrend->sum('invoice_count')) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right border-r border-gray-300">--</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right border-r border-gray-300">{{ number_format($salesTrend->sum('total_sales'), 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($salesTrend->sum('total_sales'), 2) }} Cr</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            <!-- Bar Chart Section -->
            @if($salesTrend->count() > 0)
            <div class="mt-8">


                <div class="relative" style="height: 300px;">
                    @php
                        $maxSales = $salesTrend->max('total_sales');
                        // Dynamic scale based on max sales value with some headroom
                        if ($maxSales > 0) {
                            $maxWithHeadroom = $maxSales * 1.2;
                            // Find appropriate scale (round up to nearest nice number)
                            if ($maxWithHeadroom >= 1000000) {
                                $scale = ceil($maxWithHeadroom / 1000000) * 1000000;
                            } elseif ($maxWithHeadroom >= 100000) {
                                $scale = ceil($maxWithHeadroom / 100000) * 100000;
                            } elseif ($maxWithHeadroom >= 10000) {
                                $scale = ceil($maxWithHeadroom / 10000) * 10000;
                            } else {
                                $scale = ceil($maxWithHeadroom / 1000) * 1000;
                            }
                        } else {
                            $scale = 1000;
                        }
                        $gridLines = 5;
                    @endphp

                    <!-- Debug Scale Info -->
                    <div class="absolute top-0 right-0 bg-blue-100 p-2 text-xs">
                        Max: ₦{{ number_format($maxSales, 2) }}<br>
                        Scale: ₦{{ number_format($scale, 0) }}
                    </div>

                    <!-- Y-axis labels and grid lines -->
                    <div class="absolute left-0 top-0 bottom-8 w-20 flex flex-col justify-between text-xs text-gray-600 text-right pr-2">
                        @for($i = $gridLines; $i >= 0; $i--)
                            @php
                                $labelValue = ($scale / $gridLines) * $i;
                                // Format based on scale
                                if ($scale >= 1000000) {
                                    $label = number_format($labelValue / 1000000, 1) . 'M';
                                } elseif ($scale >= 1000) {
                                    $label = number_format($labelValue / 1000, 0) . 'K';
                                } else {
                                    $label = number_format($labelValue, 0);
                                }
                            @endphp
                            <div class="relative">
                                <span>{{ $label }}</span>
                                @if($i > 0)
                                    <div class="absolute left-full top-1/2 w-screen h-px bg-gray-200" style="margin-left: 8px;"></div>
                                @endif
                            </div>
                        @endfor
                    </div>

                    <!-- Chart area -->
                    <div class="absolute left-24 right-0 top-0 bottom-8 border-l-2 border-b-2 border-gray-400 pl-4 pb-4">
                        @php $barCount = $salesTrend->count(); @endphp
                        @foreach($salesTrend as $index => $trend)
                            @php
                                $height = $maxSales > 0 ? ($trend->total_sales / $scale * 100) : 0;
                                $height = max(min($height, 100), 0); // Ensure between 0 and 100
                                // Minimum visible height for bars with data
                                $displayHeight = $trend->total_sales > 0 ? max($height, 5) : 0;

                                // Calculate bar position and width
                                $barWidth = (100 / $barCount) * 0.8; // 80% of available space for bar
                                $barLeft = ($index / $barCount) * 100 + (100 / $barCount - $barWidth) / 2;
                            @endphp

                            <!-- Bar -->
                            <div class="absolute bottom-0"
                                 style="left: {{ $barLeft }}%; width: {{ $barWidth }}%; height: {{ $displayHeight }}%;">
                                @if($trend->total_sales > 0)
                                    <div class="w-full h-full bg-red-500 hover:bg-red-600 transition-colors cursor-pointer relative group border border-red-600"
                                         title="₦{{ number_format($trend->total_sales, 2) }}">
                                        <!-- Debug percentage on bar -->
                                        <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 text-xs text-gray-600">
                                            {{ number_format($displayHeight, 1) }}%
                                        </div>
                                        <!-- Tooltip -->
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                                            ₦{{ number_format($trend->total_sales, 2) }}
                                        </div>
                                    </div>
                                @else
                                    <div class="w-full bg-gray-200 border border-gray-300" style="height: 3px;" title="No sales"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- X-axis labels -->
                    <div class="absolute left-24 right-0 bottom-0 flex items-start justify-between gap-2 pl-4 pt-2">
                        @foreach($salesTrend as $trend)
                            <div class="flex-1 text-center text-xs text-gray-600 font-medium">
                                {{ $trend->period }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    // Print Sales Register function
    function printSalesRegister(event) {
        if (event) {
            event.preventDefault();
        }

        // Get the sales register container
        const salesRegister = document.querySelector('.sales-register-container');

        if (!salesRegister) {
            alert('Sales register not found');
            return;
        }

        // Get the chart element
        const chartElement = salesRegister.querySelector('.mt-8 .relative');

        if (chartElement) {
            // Convert chart to canvas/image
            html2canvas(chartElement, {
                backgroundColor: '#ffffff',
                scale: 2, // Higher quality
                logging: false
            }).then(canvas => {
                // Convert canvas to image
                const chartImage = canvas.toDataURL('image/png');

                // Now create the print content with the chart image
                createPrintWindow(salesRegister, chartImage);
            }).catch(error => {
                console.error('Error converting chart to image:', error);
                // Fallback: print without chart image
                createPrintWindow(salesRegister, null);
            });
        } else {
            // No chart, print normally
            createPrintWindow(salesRegister, null);
        }

        return false;
    }

    function createPrintWindow(salesRegister, chartImageData) {
        // Clone the element to avoid modifying the original
        const printContent = salesRegister.cloneNode(true);

        // Remove the print button from the cloned content
        const printButton = printContent.querySelector('button');
        if (printButton) {
            printButton.remove();
        }

        // Remove debug info if exists
        const debugInfo = printContent.querySelectorAll('.bg-yellow-100, .bg-blue-100');
        debugInfo.forEach(el => el.remove());

        // If we have chart image, replace the chart section
        if (chartImageData) {
            const chartSection = printContent.querySelector('.mt-8');
            if (chartSection) {
                chartSection.innerHTML = `
                    <div style="margin-top: 2rem; page-break-inside: avoid;">
                        <img src="${chartImageData}" style="width: 100%; max-width: 800px; height: auto; display: block; margin: 0 auto;" alt="Sales Chart" />
                    </div>
                `;
            }
        }

        // Create print HTML
        const printHTML = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Sales Register - {{ $tenant->company_name ?? $tenant->name }}</title>
                <meta charset="utf-8">
                <style>
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    body {
                        font-family: Arial, sans-serif;
                        padding: 20px;
                        color: #000;
                    }
                    .sales-register-header {
                        background-color: #0f766e !important;
                        color: white !important;
                        padding: 15px 20px;
                        margin-bottom: 20px;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        print-color-adjust: exact;
                        -webkit-print-color-adjust: exact;
                    }
                    .sales-register-header h3 {
                        font-size: 18px;
                        font-weight: bold;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin: 20px 0;
                    }
                    th, td {
                        border: 1px solid #333;
                        padding: 8px 12px;
                        text-align: left;
                        font-size: 12px;
                    }
                    th {
                        background-color: #e5e7eb;
                        font-weight: bold;
                    }
                    .text-center { text-align: center !important; }
                    .text-right { text-align: right !important; }
                    .text-left { text-align: left !important; }
                    .font-bold { font-weight: bold; }
                    .bg-gray-100 { background-color: #f3f4f6; }
                    .mb-6 { margin-bottom: 1.5rem; }
                    .text-center h4 {
                        font-size: 20px;
                        margin-bottom: 5px;
                    }
                    .text-center p {
                        font-size: 13px;
                        margin: 2px 0;
                    }
                    .overflow-x-auto {
                        overflow: visible !important;
                    }
                    img {
                        page-break-inside: avoid;
                        max-width: 100%;
                        height: auto;
                    }
                    @media print {
                        body {
                            padding: 10px;
                        }
                        @page {
                            margin: 0.75in;
                            size: A4 portrait;
                        }
                        .sales-register-header {
                            print-color-adjust: exact;
                            -webkit-print-color-adjust: exact;
                        }
                    }
                </style>
            </head>
            <body>
                ${printContent.innerHTML}
            </body>
            </html>
        `;

        // Open new window for printing
        const printWindow = window.open('', '_blank', 'width=800,height=600');

        if (!printWindow) {
            alert('Please allow popups for this website to print');
            return;
        }

        printWindow.document.open();
        printWindow.document.write(printHTML);
        printWindow.document.close();

        // Wait for content to load before printing
        printWindow.onload = function() {
            printWindow.focus();
            setTimeout(function() {
                printWindow.print();
            }, 500); // Increased delay to ensure images load
        };
    }

    // Original print event handlers
    window.addEventListener('beforeprint', function() {
        document.body.classList.add('printing');
    });

    window.addEventListener('afterprint', function() {
        document.body.classList.remove('printing');
    });
</script>
@endpush
@endsection
