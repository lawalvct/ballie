'inventory-reports': {
    template: `
        <div>
            <h1 class="text-3xl font-bold mb-4">📊 Inventory Reports</h1>
            <p class="text-gray-600 mb-6">
                Comprehensive inventory analytics and reporting tools to help you track stock levels, monitor movements,
                identify low stock items, and calculate inventory valuation across your business.
            </p>

            <!-- Navigation Guide -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <h3 class="font-semibold text-blue-900 mb-2">🧭 How to Access Reports</h3>
                <ol class="text-sm text-blue-800 space-y-1 list-decimal list-inside">
                    <li>Navigate to <strong>Inventory</strong> menu in the sidebar</li>
                    <li>Click on <strong>More Actions</strong> dropdown</li>
                    <li>Select <strong>Reports</strong> to view all inventory report cards</li>
                    <li>Choose the report you need from the available options</li>
                </ol>
            </div>

            <!-- Stock Summary Report -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 flex items-center">
                    <span class="bg-yellow-100 text-yellow-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-lg">📋</span>
                    Stock Summary Report
                </h2>
                <p class="text-gray-700 mb-4">
                    Get a comprehensive overview of all your inventory items with detailed stock levels, valuations, and status indicators.
                </p>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-3">📊 What You'll See:</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Summary Cards:</strong> Total products, total stock value, total quantity, low stock alerts, and out-of-stock items</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Detailed Table:</strong> Product name with avatar, category, current stock quantity, unit cost, total value, and reorder level</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Status Indicators:</strong> Color-coded badges showing in-stock (green), low stock (yellow), and out-of-stock (red) items</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Search & Filters:</strong> Search by product name and filter by category</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-900 mb-2">🎯 Purpose & Use Cases:</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Monitor overall inventory health and stock levels</li>
                        <li>• Identify which products need restocking</li>
                        <li>• Calculate total inventory value for financial reporting</li>
                        <li>• Export to Excel for further analysis or presentations</li>
                        <li>• Print for physical inventory audits</li>
                    </ul>
                </div>
            </div>

            <!-- Low Stock Alert Report -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 flex items-center">
                    <span class="bg-red-100 text-red-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-lg">⚠️</span>
                    Low Stock Alert Report
                </h2>
                <p class="text-gray-700 mb-4">
                    Proactively identify products that are running low on stock or are out of stock to prevent stockouts and maintain optimal inventory levels.
                </p>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-3">📊 What You'll See:</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Alert Summary:</strong> Total alerts, critical alerts (out of stock), warning alerts (low stock), and estimated reorder value</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Priority Indicators:</strong> Animated pulsing red icon for critical items, warning icon for low stock items</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Reorder Information:</strong> Current stock, reorder level, shortage quantity, and recommended reorder quantity</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Cost Analysis:</strong> Unit cost and estimated reorder cost for each product</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-red-50 rounded-lg p-4">
                    <h3 class="font-semibold text-red-900 mb-2">🎯 Purpose & Use Cases:</h3>
                    <ul class="text-sm text-red-800 space-y-1">
                        <li>• Prevent stockouts and lost sales opportunities</li>
                        <li>• Plan purchase orders and reorder schedules</li>
                        <li>• Prioritize restocking based on urgency (critical vs. warning)</li>
                        <li>• Calculate budget needed for restocking</li>
                        <li>• Share with purchasing team for procurement planning</li>
                    </ul>
                </div>
            </div>

            <!-- Stock Valuation Report -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 flex items-center">
                    <span class="bg-green-100 text-green-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-lg">💰</span>
                    Stock Valuation Report
                </h2>
                <p class="text-gray-700 mb-4">
                    Calculate the financial value of your inventory using different valuation methods for accurate financial reporting and decision-making.
                </p>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-3">📊 What You'll See:</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Valuation Summary:</strong> Total products valued, total stock value, total quantity, and average value per unit</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Valuation Methods:</strong> Weighted Average, FIFO (First In, First Out), or LIFO (Last In, First Out)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Product View:</strong> Detailed breakdown by product showing quantity, unit cost, and total value</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Category View:</strong> Grouped valuation by product categories</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Date Selection:</strong> Calculate valuation as of a specific date</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-green-50 rounded-lg p-4">
                    <h3 class="font-semibold text-green-900 mb-2">🎯 Purpose & Use Cases:</h3>
                    <ul class="text-sm text-green-800 space-y-1">
                        <li>• Prepare financial statements and balance sheets</li>
                        <li>• Calculate Cost of Goods Sold (COGS) accurately</li>
                        <li>• Compare inventory values using different accounting methods</li>
                        <li>• Analyze which product categories hold the most value</li>
                        <li>• Generate reports for tax filing and audits</li>
                        <li>• Track historical inventory values over time</li>
                    </ul>
                </div>
            </div>

            <!-- Stock Movement Report -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 flex items-center">
                    <span class="bg-blue-100 text-blue-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-lg">↕️</span>
                    Stock Movement Report
                </h2>
                <p class="text-gray-700 mb-4">
                    Track detailed transaction history of all stock in and out movements to maintain complete inventory audit trails and identify trends.
                </p>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-900 mb-3">📊 What You'll See:</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Movement Summary:</strong> Total stock in, total stock out, net movement, in value, out value, and transaction count</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Transaction Details:</strong> Date & time, product name, movement type (IN/OUT), quantity, rate, total value</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Audit Information:</strong> Reference number and user who created the transaction</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Advanced Filters:</strong> Filter by date range, product, category, and movement type (in/out)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            <span><strong>Visual Indicators:</strong> Green badges with up arrows for stock IN, red badges with down arrows for stock OUT</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-900 mb-2">🎯 Purpose & Use Cases:</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Maintain complete audit trail of all inventory transactions</li>
                        <li>• Track when and why stock levels changed</li>
                        <li>• Identify movement patterns and trends over time</li>
                        <li>• Investigate discrepancies or missing stock</li>
                        <li>• Analyze fast-moving vs. slow-moving products</li>
                        <li>• Generate reports for inventory audits and compliance</li>
                        <li>• Review who made specific inventory changes</li>
                    </ul>
                </div>
            </div>

            <!-- Common Features -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-4 flex items-center">
                    <span class="bg-purple-100 text-purple-800 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-lg">⚙️</span>
                    Common Features Across All Reports
                </h2>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-white border-2 border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Excel Export
                        </h3>
                        <p class="text-sm text-gray-600">Download any report as Excel spreadsheet (.xlsx) with formatted data, summary statistics, and proper column headers for offline analysis.</p>
                    </div>

                    <div class="bg-white border-2 border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Print Friendly
                        </h3>
                        <p class="text-sm text-gray-600">All reports are optimized for printing with clean layouts, hidden non-essential elements, and preserved color coding for physical documentation.</p>
                    </div>

                    <div class="bg-white border-2 border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Search & Filter
                        </h3>
                        <p class="text-sm text-gray-600">Powerful search and filtering options to narrow down data by product name, category, date range, status, and more specific criteria.</p>
                    </div>

                    <div class="bg-white border-2 border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                            Visual Design
                        </h3>
                        <p class="text-sm text-gray-600">Modern gradient cards, color-coded badges, product avatars, and intuitive visual indicators make data easy to understand at a glance.</p>
                    </div>
                </div>
            </div>

            <!-- Tips and Best Practices -->
            <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-6 border-2 border-purple-200">
                <h2 class="text-xl font-bold mb-4 text-purple-900">💡 Tips & Best Practices</h2>
                <div class="space-y-3 text-gray-700">
                    <div class="flex items-start">
                        <span class="font-bold text-purple-600 mr-2">1.</span>
                        <span><strong>Regular Monitoring:</strong> Review Low Stock Alert report daily to prevent stockouts and maintain optimal inventory levels.</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-bold text-purple-600 mr-2">2.</span>
                        <span><strong>Month-End Reporting:</strong> Generate Stock Valuation report at month-end for accurate financial statements and inventory valuation.</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-bold text-purple-600 mr-2">3.</span>
                        <span><strong>Audit Trail:</strong> Use Stock Movement report to investigate any discrepancies or unusual patterns in inventory changes.</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-bold text-purple-600 mr-2">4.</span>
                        <span><strong>Export for Analysis:</strong> Download Excel reports for deeper analysis, pivot tables, or integration with other business systems.</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-bold text-purple-600 mr-2">5.</span>
                        <span><strong>Filter Wisely:</strong> Use category and product filters to focus on specific inventory segments for targeted decision-making.</span>
                    </div>
                </div>
            </div>

            <!-- Need Help? -->
            <div class="mt-8 bg-gray-100 rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Need Additional Help?</h3>
                <p class="text-gray-600 mb-4">If you have questions about inventory reports or need assistance, our support team is here to help.</p>
                <a href="#" @click.prevent="$parent.activeMenu = 'support'; $parent.activeSubmenu = null"
                   class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Contact Support
                </a>
            </div>
        </div>
    `
},
