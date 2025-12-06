'accounting-reports': {
    template: `
        <div>
            <h1 class="text-3xl font-bold mb-6">📊 Financial Reports</h1>

            <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-r-lg mb-8">
                <p class="text-gray-700 leading-relaxed">
                    Generate comprehensive financial reports including Profit & Loss, Balance Sheet, Trial Balance, and Cash Flow statements. These reports provide crucial insights into your business performance and financial position.
                </p>
            </div>

            <h2 class="text-2xl font-bold mb-4">4 Essential Financial Reports</h2>
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 p-6 rounded-lg border border-emerald-200">
                    <h3 class="font-bold text-emerald-900 mb-2 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Profit & Loss Statement
                    </h3>
                    <p class="text-sm text-emerald-800">Shows your business income, expenses, and net profit over a specific period.</p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg border border-blue-200">
                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Balance Sheet
                    </h3>
                    <p class="text-sm text-blue-800">Displays your assets, liabilities, and equity at a specific point in time.</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg border border-purple-200">
                    <h3 class="font-bold text-purple-900 mb-2 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0V4a1 1 0 011-1h3M7 3v18"></path>
                        </svg>
                        Trial Balance
                    </h3>
                    <p class="text-sm text-purple-800">Lists all ledger accounts with their debit and credit balances to ensure books are balanced.</p>
                </div>
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-6 rounded-lg border border-indigo-200">
                    <h3 class="font-bold text-indigo-900 mb-2 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m-3-6h6M9 10.5h6"></path>
                        </svg>
                        Cash Flow Statement
                    </h3>
                    <p class="text-sm text-indigo-800">Tracks cash movements from operating, investing, and financing activities.</p>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Profit & Loss Statement</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_report_profit_loss.png') }}" alt="Profit & Loss Report" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-4">
                    <div class="bg-emerald-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-emerald-900 mb-2">What it Shows</h4>
                        <ul class="text-sm text-emerald-800 space-y-1 ml-4">
                            <li>• <strong>Total Income:</strong> All revenue from sales and other sources</li>
                            <li>• <strong>Total Expenses:</strong> All business costs and expenditures</li>
                            <li>• <strong>Net Profit/Loss:</strong> Income minus expenses</li>
                            <li>• <strong>Detailed Breakdown:</strong> Income and expenses by category</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Display Options</h4>
                        <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700">
                            <div>
                                <strong>Detailed Mode:</strong> Shows individual accounts within each group
                            </div>
                            <div>
                                <strong>Condensed Mode:</strong> Shows only group totals for cleaner view
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Balance Sheet</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_report_balance_sheet.png') }}" alt="Balance Sheet Report" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-900 mb-2">The Accounting Equation</h4>
                        <div class="text-center text-blue-800 font-mono text-lg mb-3">
                            Assets = Liabilities + Owner's Equity
                        </div>
                        <ul class="text-sm text-blue-800 space-y-1 ml-4">
                            <li>• <strong>Assets:</strong> What your business owns (cash, inventory, equipment)</li>
                            <li>• <strong>Liabilities:</strong> What your business owes (loans, payables)</li>
                            <li>• <strong>Equity:</strong> Owner's investment and retained earnings</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Key Features</h4>
                        <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700">
                            <div>
                                <strong>Balance Verification:</strong> Automatically checks if Assets = Liabilities + Equity
                            </div>
                            <div>
                                <strong>Financial Ratios:</strong> Shows debt-to-equity and other key metrics
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Trial Balance</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_report_trial_balance.png') }}" alt="Trial Balance Report" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-4">
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-purple-900 mb-2">Purpose</h4>
                        <ul class="text-sm text-purple-800 space-y-1 ml-4">
                            <li>• <strong>Balance Verification:</strong> Ensures total debits equal total credits</li>
                            <li>• <strong>Account Summary:</strong> Lists all accounts with non-zero balances</li>
                            <li>• <strong>Error Detection:</strong> Helps identify posting errors</li>
                            <li>• <strong>Report Preparation:</strong> Foundation for other financial statements</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Information Displayed</h4>
                        <div class="grid md:grid-cols-3 gap-4 text-sm text-gray-700">
                            <div><strong>Account Code:</strong> Unique identifier</div>
                            <div><strong>Account Name:</strong> Descriptive name</div>
                            <div><strong>Account Type:</strong> Asset, Liability, etc.</div>
                            <div><strong>Opening Balance:</strong> Starting balance</div>
                            <div><strong>Debit Amount:</strong> Total debits</div>
                            <div><strong>Credit Amount:</strong> Total credits</div>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Cash Flow Statement</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_report_cash-flow.png') }}" alt="Cash Flow Report" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-4">
                    <div class="bg-indigo-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-indigo-900 mb-2">Three Categories of Cash Flow</h4>
                        <div class="grid md:grid-cols-3 gap-4 text-sm text-indigo-800">
                            <div>
                                <strong>Operating Activities:</strong><br>
                                Cash from day-to-day business operations
                            </div>
                            <div>
                                <strong>Investing Activities:</strong><br>
                                Cash from buying/selling assets and investments
                            </div>
                            <div>
                                <strong>Financing Activities:</strong><br>
                                Cash from loans, equity, and dividend payments
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Interactive Features</h4>
                        <ul class="text-sm text-gray-700 space-y-1 ml-4">
                            <li>• <strong>Expandable Sections:</strong> Click to show/hide activity details</li>
                            <li>• <strong>Visual Chart:</strong> Bar chart showing cash flow by category</li>
                            <li>• <strong>Simple View:</strong> Toggle between detailed and summary views</li>
                            <li>• <strong>Cash Reconciliation:</strong> Shows opening + changes = closing cash</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Accessing Financial Reports</h2>
            <div class="space-y-6 mb-8">
                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                        <h3 class="text-xl font-semibold">Navigate to Reports</h3>
                    </div>
                    <p class="text-gray-700 mb-3">From the main menu, click <strong>Reports</strong> or use the quick search (<kbd class="px-2 py-1 bg-gray-100 border border-gray-300 rounded text-sm font-mono">Ctrl+K</kbd>) and type "reports".</p>
                </div>

                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
                        <h3 class="text-xl font-semibold">Select Report Type</h3>
                    </div>
                    <p class="text-gray-700 mb-3">Choose from the four main financial reports. Each report has navigation buttons to quickly switch between reports.</p>
                </div>

                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
                        <h3 class="text-xl font-semibold">Set Date Range</h3>
                    </div>
                    <p class="text-gray-700 mb-3">Use the date filters to specify the reporting period. Quick presets are available for common periods.</p>
                </div>

                <div class="border-l-4 border-green-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">4</div>
                        <h3 class="text-xl font-semibold">Generate & Export</h3>
                    </div>
                    <p class="text-gray-700">Click "Generate Report" to create the report, then use export options (PDF, Excel, Print) as needed.</p>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Report Features & Options</h2>
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Date Range Options
                    </h3>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li>• <strong>Custom Range:</strong> Select any start and end date</li>
                        <li>• <strong>This Month:</strong> Current month to date</li>
                        <li>• <strong>Last Month:</strong> Previous complete month</li>
                        <li>• <strong>This Quarter:</strong> Current quarter to date</li>
                        <li>• <strong>This Year:</strong> Current year to date</li>
                        <li>• <strong>Last Year:</strong> Previous complete year</li>
                    </ul>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Options
                    </h3>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li>• <strong>PDF Download:</strong> Professional formatted report</li>
                        <li>• <strong>Excel Export:</strong> Spreadsheet format for analysis</li>
                        <li>• <strong>CSV Export:</strong> Data format for importing elsewhere</li>
                        <li>• <strong>Print:</strong> Browser print with optimized layout</li>
                        <li>• <strong>Email:</strong> Send report directly to recipients</li>
                    </ul>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Display Modes
                    </h3>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li>• <strong>Detailed View:</strong> Shows all individual accounts</li>
                        <li>• <strong>Condensed View:</strong> Shows only group totals</li>
                        <li>• <strong>Tabular View:</strong> Traditional table format</li>
                        <li>• <strong>Modern View:</strong> Card-based visual layout</li>
                        <li>• <strong>Comparison Mode:</strong> Side-by-side period comparison</li>
                    </ul>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Interactive Features
                    </h3>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li>• <strong>Drill-down:</strong> Click accounts to view details</li>
                        <li>• <strong>Expand/Collapse:</strong> Show/hide sections</li>
                        <li>• <strong>Quick Filters:</strong> Filter by account type</li>
                        <li>• <strong>Search:</strong> Find specific accounts</li>
                        <li>• <strong>Sort Options:</strong> Order by name, amount, etc.</li>
                    </ul>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Understanding Your Reports</h2>
            <div class="space-y-6 mb-8">
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-6 rounded-r-lg">
                    <h3 class="text-lg font-semibold text-emerald-900 mb-3">Profit & Loss Analysis</h3>
                    <div class="grid md:grid-cols-2 gap-4 text-sm text-emerald-800">
                        <div>
                            <strong>Positive Net Profit:</strong> Your business is profitable
                        </div>
                        <div>
                            <strong>Negative Net Profit:</strong> Your business has a loss
                        </div>
                        <div>
                            <strong>Gross Profit Margin:</strong> (Revenue - Cost of Sales) / Revenue
                        </div>
                        <div>
                            <strong>Operating Margin:</strong> Operating Income / Revenue
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-lg">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3">Balance Sheet Health</h3>
                    <div class="grid md:grid-cols-2 gap-4 text-sm text-blue-800">
                        <div>
                            <strong>Current Ratio:</strong> Current Assets / Current Liabilities
                        </div>
                        <div>
                            <strong>Debt-to-Equity:</strong> Total Liabilities / Total Equity
                        </div>
                        <div>
                            <strong>Working Capital:</strong> Current Assets - Current Liabilities
                        </div>
                        <div>
                            <strong>Equity Ratio:</strong> Total Equity / Total Assets
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-6 rounded-r-lg">
                    <h3 class="text-lg font-semibold text-indigo-900 mb-3">Cash Flow Insights</h3>
                    <div class="grid md:grid-cols-2 gap-4 text-sm text-indigo-800">
                        <div>
                            <strong>Positive Operating Cash Flow:</strong> Business generates cash from operations
                        </div>
                        <div>
                            <strong>Negative Operating Cash Flow:</strong> Business uses more cash than it generates
                        </div>
                        <div>
                            <strong>Free Cash Flow:</strong> Operating Cash Flow - Capital Expenditures
                        </div>
                        <div>
                            <strong>Cash Conversion:</strong> How quickly sales convert to cash
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-6 rounded-lg border border-purple-200 mb-8">
                <h3 class="text-xl font-semibold text-purple-900 mb-4">💡 Best Practices</h3>
                <ul class="space-y-2 text-purple-800">
                    <li>✅ Generate reports monthly to track performance trends</li>
                    <li>✅ Compare current period with previous periods</li>
                    <li>✅ Review Trial Balance before generating other reports</li>
                    <li>✅ Export reports for backup and sharing with stakeholders</li>
                    <li>✅ Use detailed view for analysis, condensed for presentations</li>
                    <li>✅ Verify Balance Sheet always balances (Assets = Liabilities + Equity)</li>
                    <li>✅ Monitor cash flow regularly to ensure liquidity</li>
                    <li>✅ Keep reports for tax preparation and auditing</li>
                </ul>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg mb-8">
                <h3 class="text-lg font-semibold text-yellow-900 mb-3">⚠️ Common Issues</h3>
                <div class="space-y-3 text-yellow-800">
                    <div>
                        <strong>Trial Balance Not Balancing:</strong>
                        <ul class="text-sm ml-4 mt-1 space-y-1">
                            <li>• Check for unposted vouchers</li>
                            <li>• Verify all transactions have equal debits and credits</li>
                            <li>• Look for data entry errors</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Balance Sheet Not Balancing:</strong>
                        <ul class="text-sm ml-4 mt-1 space-y-1">
                            <li>• Ensure all transactions are properly categorized</li>
                            <li>• Check opening balances are correct</li>
                            <li>• Verify retained earnings calculation</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Missing Data in Reports:</strong>
                        <ul class="text-sm ml-4 mt-1 space-y-1">
                            <li>• Check date range includes all relevant transactions</li>
                            <li>• Ensure accounts are active and properly configured</li>
                            <li>• Verify transactions are posted, not in draft</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-r-lg">
                <h3 class="text-lg font-semibold text-green-900 mb-3">✅ Quick Tips</h3>
                <ul class="space-y-2 text-sm text-green-800">
                    <li>• Use keyboard shortcuts: <kbd class="px-1 py-0.5 bg-white border border-green-300 rounded text-xs">Ctrl+P</kbd> to print, <kbd class="px-1 py-0.5 bg-white border border-green-300 rounded text-xs">Ctrl+E</kbd> to export</li>
                    <li>• Click on account names to drill down to ledger details</li>
                    <li>• Use the comparison feature to analyze trends over time</li>
                    <li>• Save frequently used date ranges as bookmarks</li>
                    <li>• Schedule regular report generation for consistent monitoring</li>
                    <li>• Share PDF reports with accountants and stakeholders</li>
                    <li>• Keep printed copies for important meetings and presentations</li>
                </ul>
            </div>
        </div>
    `
},