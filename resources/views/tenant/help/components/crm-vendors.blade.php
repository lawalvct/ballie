'crm-vendors': {
    template: `
        <div class="max-w-5xl">
            <h1 class="text-3xl font-bold mb-4 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                🏢 Vendor Management
            </h1>
            <p class="text-gray-600 mb-8 text-lg">
                Complete guide to managing suppliers, purchase orders, and vendor transactions in your CRM system.
            </p>

            <!-- Navigation Guide -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-l-4 border-purple-500 p-6 mb-8 rounded-r-lg">
                <h2 class="text-xl font-bold text-gray-800 mb-3 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    Where to Find It
                </h2>
                <p class="text-gray-700 mb-3"><strong>Navigation Path:</strong></p>
                <div class="bg-white rounded-lg p-4 font-mono text-sm border border-purple-200">
                    CRM Menu → Vendors → Vendor Management
                </div>
            </div>

            <!-- Section 1: Adding New Vendor -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        1. Adding a New Vendor
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-gray-700">Follow these steps to add a new supplier or vendor to your system:</p>

                    <div class="space-y-3">
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-sm font-bold mr-3">1</span>
                            <div>
                                <p class="font-semibold text-gray-800">Select Vendor Type</p>
                                <p class="text-gray-600 text-sm">Choose between <strong>Individual</strong> (freelancer/person) or <strong>Business</strong> (company/organization)</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-sm font-bold mr-3">2</span>
                            <div>
                                <p class="font-semibold text-gray-800">Enter Vendor Details</p>
                                <ul class="text-gray-600 text-sm mt-1 ml-4 list-disc">
                                    <li><strong>Individual:</strong> First name, last name</li>
                                    <li><strong>Business:</strong> Company name, business type, RC number</li>
                                </ul>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-sm font-bold mr-3">3</span>
                            <div>
                                <p class="font-semibold text-gray-800">Add Contact Information</p>
                                <p class="text-gray-600 text-sm">Email, phone, tax ID (TIN), and website (optional)</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-sm font-bold mr-3">4</span>
                            <div>
                                <p class="font-semibold text-gray-800">Enter Address Details</p>
                                <p class="text-gray-600 text-sm">Street address, city, state, postal code, and country</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-sm font-bold mr-3">5</span>
                            <div>
                                <p class="font-semibold text-gray-800">Set Payment Terms (Optional)</p>
                                <p class="text-gray-600 text-sm">Credit limit, payment terms (e.g., Net 30), and currency preferences</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-sm font-bold mr-3">6</span>
                            <div>
                                <p class="font-semibold text-gray-800">Add Banking Information (Optional)</p>
                                <p class="text-gray-600 text-sm">Bank name, account number, and account name for payments</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-sm font-bold mr-3">7</span>
                            <div>
                                <p class="font-semibold text-gray-800">Save Vendor</p>
                                <p class="text-gray-600 text-sm">Click "Create Vendor" button - a ledger account is automatically created</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mt-4 rounded-r">
                        <p class="text-sm text-blue-800">
                            <strong>💡 Pro Tip:</strong> The system automatically creates a corresponding ledger account in your Chart of Accounts for seamless accounting integration.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 2: Viewing & Searching Vendors -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        2. Viewing & Searching Vendors
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <h3 class="font-semibold text-gray-800 text-lg">Search & Filter Options</h3>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                Filter by Status
                            </h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Active vendors</li>
                                <li>• Inactive vendors</li>
                                <li>• All vendors</li>
                            </ul>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Filter by Type
                            </h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Individual vendors</li>
                                <li>• Business vendors</li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="font-semibold text-gray-800 mb-2">Search Functionality</h4>
                        <p class="text-sm text-gray-600">Use the search bar to find vendors by:</p>
                        <ul class="text-sm text-gray-600 mt-2 ml-4 list-disc">
                            <li>Company name or individual name</li>
                            <li>Email address</li>
                            <li>Phone number</li>
                            <li>Tax ID (TIN)</li>
                        </ul>
                    </div>

                    <div class="bg-purple-50 border-l-4 border-purple-400 p-4 rounded-r">
                        <p class="text-sm text-purple-800">
                            <strong>⚡ Quick Sort:</strong> Click column headers to sort vendors by name, status, purchase total, or outstanding balance.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 3: Viewing Vendor Details -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-emerald-500 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        3. Viewing Vendor Details
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-gray-700">Click any vendor name to view their complete profile including:</p>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                            <h4 class="font-semibold text-gray-800 mb-2">📋 Contact Information</h4>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Full name/company details</li>
                                <li>• Email and phone numbers</li>
                                <li>• Physical address</li>
                                <li>• Tax ID and business type</li>
                            </ul>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                            <h4 class="font-semibold text-gray-800 mb-2">💰 Financial Summary</h4>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Total purchases amount</li>
                                <li>• Outstanding balance (payables)</li>
                                <li>• Total number of orders</li>
                                <li>• Last purchase date</li>
                            </ul>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                            <h4 class="font-semibold text-gray-800 mb-2">📝 Transaction History</h4>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Purchase orders</li>
                                <li>• Payment records</li>
                                <li>• Invoice history</li>
                                <li>• Credit notes</li>
                            </ul>
                        </div>

                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                            <h4 class="font-semibold text-gray-800 mb-2">🏦 Banking Details</h4>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li>• Bank name</li>
                                <li>• Account number</li>
                                <li>• Account name</li>
                                <li>• Payment preferences</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Vendor Statements & Reports -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        4. Vendor Statements & Reports
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <h3 class="font-semibold text-gray-800 text-lg">Generate Vendor Statements</h3>
                    <p class="text-gray-700">Track purchase history and payment obligations with detailed vendor statements:</p>

                    <div class="space-y-3">
                        <div class="border-l-4 border-orange-500 bg-orange-50 p-4 rounded-r">
                            <h4 class="font-semibold text-gray-800 mb-1">Statement Period Selection</h4>
                            <p class="text-sm text-gray-700">Choose custom date ranges to view transactions for specific periods</p>
                        </div>

                        <div class="border-l-4 border-red-500 bg-red-50 p-4 rounded-r">
                            <h4 class="font-semibold text-gray-800 mb-1">Transaction Details</h4>
                            <p class="text-sm text-gray-700">View all purchases, payments, debits, and credits within the selected period</p>
                        </div>

                        <div class="border-l-4 border-pink-500 bg-pink-50 p-4 rounded-r">
                            <h4 class="font-semibold text-gray-800 mb-1">Outstanding Balance</h4>
                            <p class="text-sm text-gray-700">See current payables and aging analysis for better cash flow management</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-300">
                        <h4 class="font-semibold text-gray-800 mb-3">Export Options</h4>
                        <div class="flex flex-wrap gap-3">
                            <span class="px-3 py-1 bg-white rounded-full text-sm border border-gray-300 shadow-sm">
                                📄 PDF Download
                            </span>
                            <span class="px-3 py-1 bg-white rounded-full text-sm border border-gray-300 shadow-sm">
                                📊 Excel Export
                            </span>
                            <span class="px-3 py-1 bg-white rounded-full text-sm border border-gray-300 shadow-sm">
                                🖨️ Print Statement
                            </span>
                            <span class="px-3 py-1 bg-white rounded-full text-sm border border-gray-300 shadow-sm">
                                📧 Email to Vendor
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 5: Bulk Upload Vendors -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        5. Bulk Upload Vendors
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-gray-700">Import multiple vendors at once using Excel/CSV files:</p>

                    <div class="space-y-3">
                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold mr-3">1</span>
                            <div>
                                <p class="font-semibold text-gray-800">Download Template</p>
                                <p class="text-gray-600 text-sm">Click "Bulk Upload Vendors" button and download the Excel template</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold mr-3">2</span>
                            <div>
                                <p class="font-semibold text-gray-800">Fill Vendor Data</p>
                                <p class="text-gray-600 text-sm">Complete all required fields: name, email, phone, address, and vendor type</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold mr-3">3</span>
                            <div>
                                <p class="font-semibold text-gray-800">Upload File</p>
                                <p class="text-gray-600 text-sm">Select your completed Excel/CSV file from your computer</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold mr-3">4</span>
                            <div>
                                <p class="font-semibold text-gray-800">Validate & Import</p>
                                <p class="text-gray-600 text-sm">System validates data and shows any errors before final import</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold mr-3">5</span>
                            <div>
                                <p class="font-semibold text-gray-800">Confirm Import</p>
                                <p class="text-gray-600 text-sm">Review summary and confirm to add all vendors to your database</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r">
                        <p class="text-sm text-yellow-800">
                            <strong>⚠️ Important:</strong> Ensure email addresses are unique. Duplicate emails will be rejected during import to maintain data integrity.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 6: Editing & Managing Vendors -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6 overflow-hidden">
                <div class="bg-gradient-to-r from-pink-500 to-rose-500 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        6. Editing & Managing Vendors
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <h3 class="font-semibold text-gray-800 text-lg">Update Vendor Information</h3>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">✏️ Edit Details</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Update contact information</li>
                                <li>• Modify address details</li>
                                <li>• Change payment terms</li>
                                <li>• Update banking information</li>
                            </ul>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">🔄 Status Changes</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Activate/deactivate vendor</li>
                                <li>• Adjust credit limits</li>
                                <li>• Modify payment preferences</li>
                                <li>• Update vendor classification</li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r">
                        <p class="text-sm text-red-800">
                            <strong>🚫 Note:</strong> You cannot delete vendors with existing transactions. Instead, mark them as "Inactive" to hide from active lists while preserving transaction history.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 7: Quick Actions -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-6 overflow-hidden">
                <div class="bg-gradient-to-r from-teal-500 to-cyan-500 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        7. Quick Actions
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">Access common vendor operations with one click:</p>

                    <div class="grid md:grid-cols-2 gap-3">
                        <div class="flex items-center p-3 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Create Purchase Order</p>
                                <p class="text-xs text-gray-600">Order from vendor</p>
                            </div>
                        </div>

                        <div class="flex items-center p-3 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200">
                            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Record Payment</p>
                                <p class="text-xs text-gray-600">Pay vendor balance</p>
                            </div>
                        </div>

                        <div class="flex items-center p-3 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">View Statement</p>
                                <p class="text-xs text-gray-600">Transaction history</p>
                            </div>
                        </div>

                        <div class="flex items-center p-3 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg border border-orange-200">
                            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Export Data</p>
                                <p class="text-xs text-gray-600">Download vendor info</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 8: Best Practices -->
            <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50 border-2 border-purple-200 rounded-xl p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Best Practices for Vendor Management
                </h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">✅</span>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-1">Keep Information Current</h4>
                                <p class="text-sm text-gray-600">Regularly update vendor contact details, banking information, and payment terms to avoid payment delays.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">🏷️</span>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-1">Categorize Vendors</h4>
                                <p class="text-sm text-gray-600">Use vendor types and tags to organize suppliers by category, priority, or service type for better management.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">📊</span>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-1">Monitor Outstanding Balances</h4>
                                <p class="text-sm text-gray-600">Review vendor statements regularly to track payables, manage cash flow, and maintain good supplier relationships.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">💳</span>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-1">Set Payment Terms</h4>
                                <p class="text-sm text-gray-600">Define clear payment terms (Net 30, Net 60, etc.) for each vendor to automate reminders and track due dates.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">🔍</span>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-1">Verify Tax Information</h4>
                                <p class="text-sm text-gray-600">Ensure Tax ID (TIN) is accurate for all vendors to facilitate proper tax reporting and compliance.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">📁</span>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-1">Use Bulk Upload Wisely</h4>
                                <p class="text-sm text-gray-600">When importing multiple vendors, validate your spreadsheet thoroughly to avoid data errors and duplicates.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Need Help Section -->
            <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl p-6 text-white">
                <h2 class="text-xl font-bold mb-3">Need More Help?</h2>
                <p class="text-gray-300 mb-4">If you have questions about vendor management or encounter any issues:</p>
                <div class="flex flex-wrap gap-3">
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-white text-gray-800 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Contact Support
                    </a>
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        View Documentation
                    </a>
                </div>
            </div>
        </div>
    `
},
