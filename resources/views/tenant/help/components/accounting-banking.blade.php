'accounting-banking': {
    template: `
        <div>
            <h1 class="text-3xl font-bold mb-6">🏦 Banking & Reconciliation</h1>

            <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-r-lg mb-8">
                <p class="text-gray-700 leading-relaxed">
                    Manage your bank accounts and reconcile them with your accounting records to ensure accuracy. Ballie creates a primary bank account during onboarding that you can customize.
                </p>
            </div>

            <h2 class="text-2xl font-bold mb-4">Accessing Banking Module</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <p class="text-gray-700 mb-3">From the Accounting Dashboard, click <strong>"MORE ACTIONS"</strong> button and select <strong>"Bank Accounts"</strong>:</p>
                <img src="{{ asset('images/help/accounting_banking_more-action.png') }}" alt="Access Banking" class="w-full rounded-lg shadow-md">
            </div>

            <h2 class="text-2xl font-bold mb-4">Bank Accounts List</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_banking_list.png') }}" alt="Bank Accounts List" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-3">
                    <div class="bg-green-50 p-3 rounded border border-green-200">
                        <h4 class="font-semibold text-green-900 mb-2">Primary Bank Account</h4>
                        <p class="text-sm text-green-800">Ballie creates a primary bank account during company onboarding. You can edit the name and account number to match your actual bank details.</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <h4 class="font-semibold text-gray-900 mb-2">Available Actions</h4>
                        <ul class="text-sm text-gray-700 space-y-1 ml-4">
                            <li>• <strong>Add Bank Account:</strong> Create additional bank accounts</li>
                            <li>• <strong>PDF:</strong> Export bank accounts list as PDF</li>
                            <li>• <strong>Export:</strong> Download bank accounts as Excel file</li>
                            <li>• <strong>View Statement:</strong> See transaction history for each account</li>
                            <li>• <strong>Reconcile:</strong> Start bank reconciliation process</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Creating a New Bank Account</h2>
            <div class="space-y-6 mb-8">
                <div class="border-l-4 border-emerald-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                        <h3 class="text-xl font-semibold">Click "Add Bank Account"</h3>
                    </div>
                    <p class="text-gray-700">From the bank accounts list, click the green <strong>"ADD BANK ACCOUNT"</strong> button.</p>
                </div>

                <div class="border-l-4 border-emerald-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
                        <h3 class="text-xl font-semibold">Fill Basic Information</h3>
                    </div>
                    <img src="{{ asset('images/help/accounting_banking_create.png') }}" alt="Create Bank Account" class="w-full rounded-lg shadow-md mb-3">
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <div><strong>Bank Name:</strong> Name of the bank (e.g., First Bank of Nigeria)</div>
                        <div><strong>Account Name/Holder:</strong> Name on the account</div>
                        <div><strong>Account Number:</strong> Bank account number (must be unique)</div>
                        <div><strong>Account Type:</strong> Savings, Current, Fixed Deposit, Credit Card, Loan, Investment, or Other</div>
                        <div><strong>Currency:</strong> NGN (Naira), USD, EUR, or GBP</div>
                        <div><strong>Opening Balance:</strong> Starting balance for the account</div>
                        <div><strong>Status:</strong> Active, Inactive, Closed, or Suspended</div>
                        <div><strong>Description:</strong> Additional notes about the account</div>
                    </div>
                </div>

                <div class="border-l-4 border-emerald-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
                        <h3 class="text-xl font-semibold">Add Branch Details (Optional)</h3>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <div><strong>Branch Name:</strong> Name of the bank branch</div>
                        <div><strong>Branch Code:</strong> Branch identification code</div>
                        <div><strong>Branch Address:</strong> Physical address of the branch</div>
                        <div><strong>City, State, Phone:</strong> Contact information</div>
                    </div>
                </div>

                <div class="border-l-4 border-emerald-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center font-bold">4</div>
                        <h3 class="text-xl font-semibold">Configure Account Settings</h3>
                    </div>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h4 class="font-semibold text-blue-900 mb-2">Primary Account</h4>
                            <p class="text-sm text-blue-800">Set as default bank account for transactions</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <h4 class="font-semibold text-purple-900 mb-2">Payroll Account</h4>
                            <p class="text-sm text-purple-800">Use this account for employee salary payments</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <h4 class="font-semibold text-green-900 mb-2">Enable Reconciliation</h4>
                            <p class="text-sm text-green-800">Track and reconcile bank statements</p>
                        </div>
                    </div>
                </div>

                <div class="border-l-4 border-emerald-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center font-bold">5</div>
                        <h3 class="text-xl font-semibold">Set Account Limits (Optional)</h3>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <div><strong>Minimum Balance:</strong> Minimum balance to maintain</div>
                        <div><strong>Overdraft Limit:</strong> Maximum overdraft allowed</div>
                    </div>
                </div>

                <div class="border-l-4 border-emerald-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center font-bold">6</div>
                        <h3 class="text-xl font-semibold">Add International Codes (Optional)</h3>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <div><strong>SWIFT/BIC Code:</strong> For international transfers</div>
                        <div><strong>IBAN:</strong> International Bank Account Number</div>
                        <div><strong>Routing Number:</strong> For US banks</div>
                        <div><strong>Sort Code:</strong> For UK banks</div>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Bank Statement</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_banking_statement.png') }}" alt="Bank Statement" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-3">
                    <p class="text-gray-700">View detailed transaction history for any bank account. The statement shows:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-1 ml-4">
                        <li>Opening and closing balances</li>
                        <li>All debits (money out) and credits (money in)</li>
                        <li>Running balance after each transaction</li>
                        <li>Transaction dates, descriptions, and references</li>
                    </ul>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mt-4">
                        <p class="text-sm text-blue-800"><strong>💡 Tip:</strong> Click the <strong>"Print"</strong> button to generate a professional bank statement for physical record keeping.</p>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Bank Reconciliation</h2>
            <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-6 rounded-lg border border-purple-200 mb-6">
                <h3 class="text-xl font-semibold text-purple-900 mb-3">What is Bank Reconciliation?</h3>
                <p class="text-purple-800 mb-3">
                    Bank reconciliation is the process of matching your Ballie accounting records with your actual bank statement to ensure accuracy and identify any discrepancies.
                </p>
                <p class="text-purple-800">
                    This helps you catch errors, detect fraud, and maintain accurate financial records.
                </p>
            </div>

            <h3 class="text-xl font-bold mb-4">Creating a Reconciliation</h3>
            <div class="space-y-6 mb-8">
                <div class="border-l-4 border-blue-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                        <h3 class="text-xl font-semibold">Start Reconciliation</h3>
                    </div>
                    <img src="{{ asset('images/help/accounting_reconcile_create.png') }}" alt="Create Reconciliation" class="w-full rounded-lg shadow-md mb-3">
                    <p class="text-gray-700 mb-3">From the bank accounts list, click <strong>"Reconcile"</strong> next to the account you want to reconcile.</p>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <div><strong>Bank Account:</strong> Select the account to reconcile</div>
                        <div><strong>Reconciliation Date:</strong> Date you're performing the reconciliation</div>
                        <div><strong>Statement Period:</strong> Start and end dates from your bank statement</div>
                        <div><strong>Closing Balance (Per Bank):</strong> Ending balance shown on your bank statement</div>
                        <div><strong>Bank Charges:</strong> Any fees charged by the bank</div>
                        <div><strong>Interest Earned:</strong> Interest credited to your account</div>
                        <div><strong>Notes:</strong> Any additional comments</div>
                    </div>
                </div>

                <div class="border-l-4 border-blue-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
                        <h3 class="text-xl font-semibold">Match Transactions</h3>
                    </div>
                    <img src="{{ asset('images/help/accounting_reconcile_process.png') }}" alt="Reconciliation Process" class="w-full rounded-lg shadow-md mb-3">
                    <p class="text-gray-700 mb-3">After creating the reconciliation, you'll see two tabs:</p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <h4 class="font-semibold text-yellow-900 mb-2">Uncleared Transactions</h4>
                            <p class="text-sm text-yellow-800">Transactions in Ballie that haven't been matched with your bank statement yet. Click <strong>"Clear"</strong> to mark them as matched.</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <h4 class="font-semibold text-green-900 mb-2">Cleared Transactions</h4>
                            <p class="text-sm text-green-800">Transactions that have been matched with your bank statement. Click <strong>"Unclear"</strong> if you need to unmatch them.</p>
                        </div>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mt-4">
                        <p class="text-sm text-blue-800"><strong>💡 Quick Actions:</strong></p>
                        <ul class="text-sm text-blue-700 ml-4 mt-2 space-y-1">
                            <li>• <strong>Mark All:</strong> Clear all uncleared transactions at once</li>
                            <li>• <strong>Unmark All:</strong> Unclear all cleared transactions at once</li>
                        </ul>
                    </div>
                </div>

                <div class="border-l-4 border-blue-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
                        <h3 class="text-xl font-semibold">Balance the Reconciliation</h3>
                    </div>
                    <p class="text-gray-700 mb-3">Continue matching transactions until the <strong>Difference</strong> becomes zero. The system shows:</p>
                    <div class="grid md:grid-cols-4 gap-4 mb-4">
                        <div class="bg-blue-50 p-3 rounded border-l-4 border-blue-500">
                            <div class="text-xs text-gray-600 mb-1">Bank Statement Balance</div>
                            <div class="font-bold text-gray-900">From your bank</div>
                        </div>
                        <div class="bg-cyan-50 p-3 rounded border-l-4 border-cyan-500">
                            <div class="text-xs text-gray-600 mb-1">Book Balance</div>
                            <div class="font-bold text-gray-900">From Ballie records</div>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded border-l-4 border-yellow-500">
                            <div class="text-xs text-gray-600 mb-1">Difference</div>
                            <div class="font-bold text-yellow-600">Must be ₦0.00</div>
                        </div>
                        <div class="bg-emerald-50 p-3 rounded border-l-4 border-emerald-500">
                            <div class="text-xs text-gray-600 mb-1">Progress</div>
                            <div class="font-bold text-emerald-600">% Completed</div>
                        </div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="font-semibold text-green-800">Reconciliation Balanced!</h4>
                                <p class="text-sm text-green-700">When the difference is zero, you can complete the reconciliation.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-l-4 border-blue-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">4</div>
                        <h3 class="text-xl font-semibold">Complete Reconciliation</h3>
                    </div>
                    <img src="{{ asset('images/help/accounting_reconcile_after_process.png') }}" alt="After Reconciliation" class="w-full rounded-lg shadow-md mb-3">
                    <p class="text-gray-700 mb-3">Once balanced, click the <strong>"Complete"</strong> button. The reconciliation will be marked as completed and cannot be modified.</p>
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <p class="text-sm text-yellow-800"><strong>⚠️ Note:</strong> Completed reconciliations are locked and cannot be edited. Make sure everything is correct before completing.</p>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Reconciliation Summary</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-8">
                <p class="text-gray-700 mb-3">The sidebar shows a complete summary of your reconciliation:</p>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div class="bg-gray-50 p-3 rounded">
                            <div class="text-sm text-gray-600">Opening Balance</div>
                            <div class="font-semibold text-gray-900">Starting balance</div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded">
                            <div class="text-sm text-gray-600">Closing Balance (Statement)</div>
                            <div class="font-semibold text-gray-900">Ending balance from bank</div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded">
                            <div class="text-sm text-gray-600">Bank Charges</div>
                            <div class="font-semibold text-red-600">Fees deducted</div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded">
                            <div class="text-sm text-gray-600">Interest Earned</div>
                            <div class="font-semibold text-green-600">Interest credited</div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="bg-gray-50 p-3 rounded">
                            <div class="text-sm text-gray-600">Total Items</div>
                            <div class="font-semibold text-gray-900">All transactions</div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded">
                            <div class="text-sm text-gray-600">Cleared Items</div>
                            <div class="font-semibold text-green-600">Matched transactions</div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded">
                            <div class="text-sm text-gray-600">Uncleared Items</div>
                            <div class="font-semibold text-yellow-600">Pending transactions</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-6 rounded-lg border border-purple-200 mb-8">
                <h3 class="text-xl font-semibold text-purple-900 mb-4">💡 Best Practices</h3>
                <ul class="space-y-2 text-purple-800">
                    <li>✅ Reconcile your bank accounts monthly</li>
                    <li>✅ Keep your bank statements organized</li>
                    <li>✅ Record all transactions in Ballie promptly</li>
                    <li>✅ Investigate any discrepancies immediately</li>
                    <li>✅ Document bank charges and interest in the reconciliation</li>
                    <li>✅ Review completed reconciliations regularly</li>
                    <li>✅ Use the primary account for most transactions</li>
                    <li>✅ Set up separate accounts for payroll if needed</li>
                </ul>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg mb-8">
                <h3 class="text-lg font-semibold text-yellow-900 mb-3">⚠️ Common Issues</h3>
                <div class="space-y-3 text-yellow-800">
                    <div>
                        <strong>Difference Won't Balance:</strong>
                        <ul class="text-sm ml-4 mt-1 space-y-1">
                            <li>• Check for duplicate transactions</li>
                            <li>• Verify all amounts match your bank statement</li>
                            <li>• Look for missing transactions in Ballie</li>
                            <li>• Ensure bank charges and interest are recorded</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Missing Transactions:</strong>
                        <ul class="text-sm ml-4 mt-1 space-y-1">
                            <li>• Create vouchers for transactions not in Ballie</li>
                            <li>• Check the date range of your reconciliation</li>
                            <li>• Verify transactions are posted (not draft)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-r-lg">
                <h3 class="text-lg font-semibold text-green-900 mb-3">✅ Tips for Success</h3>
                <ul class="space-y-2 text-sm text-green-800">
                    <li>• Start with the most recent bank statement</li>
                    <li>• Work in a quiet environment to avoid errors</li>
                    <li>• Use the "Mark All" feature for bulk matching</li>
                    <li>• Save your work frequently (auto-saved)</li>
                    <li>• Print bank statements for reference</li>
                    <li>• Keep notes of any unusual transactions</li>
                    <li>• Complete reconciliations before month-end closing</li>
                </ul>
            </div>
        </div>
    `
},
