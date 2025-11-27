'accounting-ledger': {
    template: `
        <div>
            <h1 class="text-3xl font-bold mb-6">📖 Chart of Accounts (COA)</h1>

            <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-r-lg mb-8">
                <p class="text-gray-700 leading-relaxed">
                    The Chart of Accounts is the foundation of your accounting system. It's a complete list of all accounts used to record transactions in your business. Ballie creates 95 commonly used ledger accounts during company onboarding to get you started quickly.
                </p>
            </div>

            <h2 class="text-2xl font-bold mb-4">Accessing Chart of Accounts</h2>

            <div class="space-y-4 mb-8">
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Method 1: Quick Search (Ctrl+K)</h3>
                    <p class="text-gray-700 mb-3">Press <kbd class="px-2 py-1 bg-gray-100 border border-gray-300 rounded text-sm font-mono">Ctrl+K</kbd> anywhere in Ballie and type:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-1 ml-4">
                        <li><strong>"chart"</strong> - Shows Chart of Accounts link</li>
                        <li><strong>"ledger"</strong> - Shows Ledger Accounts link</li>
                        <li><strong>"coa"</strong> - Direct access to Chart of Accounts</li>
                    </ul>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Method 2: From Accounting Dashboard</h3>
                    <p class="text-gray-700 mb-3">Navigate to <strong>Accounting Dashboard</strong> → Click <strong>"MORE ACTIONS"</strong> button → Select <strong>"Ledger Accounts"</strong></p>
                    <img src="{{ asset('images/help/accounting_voucher_more-action-coa.png') }}" alt="Access COA from More Actions" class="w-full rounded-lg shadow-md mt-3">
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Chart of Accounts Overview</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_manage_chat-of-account.png') }}" alt="Chart of Accounts Page" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-3">
                    <div class="bg-gray-50 p-3 rounded">
                        <h4 class="font-semibold text-gray-900 mb-2">📊 Summary Statistics</h4>
                        <ul class="text-sm text-gray-700 space-y-1 ml-4">
                            <li>• <strong>Total Accounts:</strong> All ledger accounts in your system</li>
                            <li>• <strong>Active Accounts:</strong> Accounts available for transactions</li>
                            <li>• <strong>With Balance:</strong> Accounts that have non-zero balances</li>
                            <li>• <strong>Parent Accounts:</strong> Main accounts with sub-accounts</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">View Options</h2>
            <div class="grid md:grid-cols-2 gap-4 mb-8">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                    <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        List View
                    </h3>
                    <p class="text-sm text-blue-800">View all accounts in a detailed table format with columns for account code, name, type, group, parent, and balance.</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                    <h3 class="font-bold text-purple-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Tree View
                    </h3>
                    <p class="text-sm text-purple-800">View accounts in a hierarchical structure showing parent-child relationships and account organization.</p>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Available Actions</h2>
            <div class="grid md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <div class="w-8 h-8 bg-green-100 rounded flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900">Import Accounts</h4>
                    </div>
                    <p class="text-sm text-gray-700">Upload multiple accounts from Excel/CSV file using the provided template.</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900">Create Account</h4>
                    </div>
                    <p class="text-sm text-gray-700">Add new ledger accounts to your chart of accounts with AI assistance.</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900">Export</h4>
                    </div>
                    <p class="text-sm text-gray-700">Download your chart of accounts as Excel file for backup or analysis.</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <div class="w-8 h-8 bg-red-100 rounded flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900">PDF</h4>
                    </div>
                    <p class="text-sm text-gray-700">Generate and download a PDF report of your chart of accounts.</p>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Creating a New Ledger Account</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_COA_create.png') }}" alt="Create Ledger Account" class="w-full rounded-lg shadow-md mb-4">

                <div class="space-y-6">
                    <div class="border-l-4 border-blue-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                            <h3 class="text-xl font-semibold">Click "Create Account" Button</h3>
                        </div>
                        <p class="text-gray-700">From the Chart of Accounts page, click the blue <strong>"CREATE ACCOUNT"</strong> button.</p>
                    </div>

                    <div class="border-l-4 border-blue-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
                            <h3 class="text-xl font-semibold">Fill Basic Information</h3>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                            <div><strong>Account Code:</strong> Unique identifier (e.g., 1000, ACC001)</div>
                            <div><strong>Account Name:</strong> Descriptive name for the account</div>
                            <div><strong>Account Type:</strong> Asset, Liability, Equity, Income, or Expense</div>
                            <div><strong>Account Group:</strong> Categorize under appropriate group</div>
                            <div><strong>Parent Account:</strong> Optional - create sub-accounts</div>
                            <div><strong>Balance Type:</strong> Debit (Dr) or Credit (Cr)</div>
                            <div><strong>Opening Balance:</strong> Starting balance for the account</div>
                        </div>
                    </div>

                    <div class="border-l-4 border-blue-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
                            <h3 class="text-xl font-semibold">Use AI Account Assistant</h3>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <p class="text-purple-800 mb-3">
                                <strong>🤖 AI Assistant Features:</strong>
                            </p>
                            <ul class="text-sm text-purple-700 space-y-2 ml-4">
                                <li>• <strong>Smart Code Generator:</strong> Automatically suggests account codes based on type</li>
                                <li>• <strong>Account Setup Guidance:</strong> Ask Ballie AI for help with account structure</li>
                                <li>• <strong>Code Examples:</strong> View examples for each account type (Asset: 1XXX, Liability: 2XXX, etc.)</li>
                                <li>• <strong>Balance Type Helper:</strong> Automatic suggestion based on account type</li>
                            </ul>
                        </div>
                    </div>

                    <div class="border-l-4 border-blue-500 pl-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">4</div>
                            <h3 class="text-xl font-semibold">Save the Account</h3>
                        </div>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="bg-blue-100 p-4 rounded-lg border border-blue-300">
                                <h4 class="font-semibold mb-2">Create Account</h4>
                                <p class="text-sm text-gray-700">Save and return to list</p>
                            </div>
                            <div class="bg-green-100 p-4 rounded-lg border border-green-300">
                                <h4 class="font-semibold mb-2">Save & Create Another</h4>
                                <p class="text-sm text-gray-700">Save and create another account immediately</p>
                            </div>
                            <div class="bg-purple-100 p-4 rounded-lg border border-purple-300">
                                <h4 class="font-semibold mb-2">Save & View</h4>
                                <p class="text-sm text-gray-700">Save and view account details</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Account Code Structure</h2>
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 rounded-lg border border-indigo-200 mb-8">
                <h3 class="text-lg font-semibold text-indigo-900 mb-4">Standard Account Code Ranges</h3>
                <div class="grid md:grid-cols-5 gap-3">
                    <div class="bg-white p-3 rounded border-l-4 border-green-500">
                        <div class="font-bold text-green-700">1XXX</div>
                        <div class="text-sm text-gray-600">Assets</div>
                        <div class="text-xs text-gray-500 mt-1">e.g., 1001 - Cash</div>
                    </div>
                    <div class="bg-white p-3 rounded border-l-4 border-red-500">
                        <div class="font-bold text-red-700">2XXX</div>
                        <div class="text-sm text-gray-600">Liabilities</div>
                        <div class="text-xs text-gray-500 mt-1">e.g., 2001 - Payable</div>
                    </div>
                    <div class="bg-white p-3 rounded border-l-4 border-yellow-500">
                        <div class="font-bold text-yellow-700">3XXX</div>
                        <div class="text-sm text-gray-600">Equity</div>
                        <div class="text-xs text-gray-500 mt-1">e.g., 3001 - Capital</div>
                    </div>
                    <div class="bg-white p-3 rounded border-l-4 border-blue-500">
                        <div class="font-bold text-blue-700">4XXX</div>
                        <div class="text-sm text-gray-600">Income</div>
                        <div class="text-xs text-gray-500 mt-1">e.g., 4001 - Sales</div>
                    </div>
                    <div class="bg-white p-3 rounded border-l-4 border-purple-500">
                        <div class="font-bold text-purple-700">5XXX</div>
                        <div class="text-sm text-gray-600">Expenses</div>
                        <div class="text-xs text-gray-500 mt-1">e.g., 5001 - Salary</div>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Viewing Account Details</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_COA_show.png') }}" alt="Account Details Page" class="w-full rounded-lg shadow-md mb-4">

                <h3 class="text-lg font-semibold text-gray-900 mb-3">Account Details Page Features</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <div class="bg-gray-50 p-3 rounded">
                            <h4 class="font-semibold text-gray-900 mb-1">Account Information</h4>
                            <p class="text-sm text-gray-700">View complete account details including code, name, type, group, parent account, and description.</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded">
                            <h4 class="font-semibold text-gray-900 mb-1">Balance Information</h4>
                            <p class="text-sm text-gray-700">See opening balance, current balance, total debits, total credits, and transaction count.</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded">
                            <h4 class="font-semibold text-gray-900 mb-1">Recent Transactions</h4>
                            <p class="text-sm text-gray-700">View the latest transactions affecting this account.</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="bg-blue-50 p-3 rounded border border-blue-200">
                            <h4 class="font-semibold text-blue-900 mb-2">Quick Actions Available:</h4>
                            <ul class="text-sm text-blue-800 space-y-1">
                                <li>✓ <strong>Edit Account:</strong> Modify account details</li>
                                <li>✓ <strong>Add Transaction:</strong> Record new transaction</li>
                                <li>✓ <strong>View Statement:</strong> Generate account statement</li>
                                <li>✓ <strong>Export Ledger:</strong> Download account ledger</li>
                                <li>✓ <strong>Print Ledger:</strong> Print account statement</li>
                                <li>✓ <strong>Deactivate Account:</strong> Disable account</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Pre-Created Accounts</h2>
            <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-r-lg mb-8">
                <h3 class="text-lg font-semibold text-green-900 mb-3">95 Default Ledger Accounts</h3>
                <p class="text-green-800 mb-4">
                    Ballie automatically creates 95 commonly used ledger accounts during company onboarding to help you get started immediately. These include:
                </p>
                <div class="grid md:grid-cols-2 gap-3 text-sm text-green-700">
                    <div>
                        <strong>Assets:</strong> Cash, Bank Accounts, Accounts Receivable, Inventory, Fixed Assets, etc.
                    </div>
                    <div>
                        <strong>Liabilities:</strong> Accounts Payable, Loans, Accrued Expenses, Tax Payable, etc.
                    </div>
                    <div>
                        <strong>Equity:</strong> Owner's Capital, Retained Earnings, Drawings, etc.
                    </div>
                    <div>
                        <strong>Income:</strong> Sales Revenue, Service Income, Interest Income, etc.
                    </div>
                    <div>
                        <strong>Expenses:</strong> Salaries, Rent, Utilities, Office Supplies, Marketing, etc.
                    </div>
                </div>
                <p class="text-green-800 mt-4">
                    You can customize, add, or deactivate any of these accounts based on your business needs.
                </p>
            </div>

            <h2 class="text-2xl font-bold mb-4">Search & Filters</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-8">
                <h3 class="font-semibold text-gray-900 mb-3">Filter Your Accounts</h3>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <div>
                            <strong>Search:</strong> Search by account name, code, or description
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 7V4z"></path>
                        </svg>
                        <div>
                            <strong>Account Type:</strong> Filter by Asset, Liability, Equity, Income, or Expense
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <div>
                            <strong>Account Group:</strong> Filter by specific account groups
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-orange-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <strong>Status:</strong> Filter by Active or Inactive accounts
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-6 rounded-lg border border-purple-200 mb-8">
                <h3 class="text-xl font-semibold text-purple-900 mb-4">💡 Best Practices</h3>
                <ul class="space-y-2 text-purple-800">
                    <li>✅ Use consistent account code numbering system</li>
                    <li>✅ Create descriptive account names that are easy to understand</li>
                    <li>✅ Organize accounts using parent-child relationships for better structure</li>
                    <li>✅ Regularly review and deactivate unused accounts</li>
                    <li>✅ Use account groups to categorize similar accounts</li>
                    <li>✅ Set appropriate opening balances when creating accounts</li>
                    <li>✅ Document account purposes in the description field</li>
                </ul>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg">
                <h3 class="text-lg font-semibold text-yellow-900 mb-3">⚠️ Important Notes</h3>
                <ul class="space-y-2 text-yellow-800">
                    <li>• Account codes must be unique across your chart of accounts</li>
                    <li>• Deactivating an account doesn't delete it - historical data is preserved</li>
                    <li>• You cannot delete accounts that have transactions</li>
                    <li>• Parent accounts cannot be deleted if they have sub-accounts</li>
                    <li>• Changes to account type or balance type may affect financial reports</li>
                </ul>
            </div>
        </div>
    `
},
