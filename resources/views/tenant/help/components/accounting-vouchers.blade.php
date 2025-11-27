'accounting-vouchers': {
    template: `
        <div>
            <h1 class="text-3xl font-bold mb-6">📝 Vouchers & Journal Entries</h1>

            <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-r-lg mb-8">
                <p class="text-gray-700 leading-relaxed">
                    Vouchers are the foundation of double-entry bookkeeping. Every financial transaction is recorded through vouchers, ensuring your accounts are always balanced and accurate.
                </p>
            </div>

            <h2 class="text-2xl font-bold mb-4">Accessing Vouchers</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <p class="text-gray-700 mb-3">Click the <strong>"VIEW VOUCHERS"</strong> button on the accounting dashboard:</p>
                <img src="{{ asset('images/help/accounting_voucher_button.png') }}" alt="View Vouchers Button" class="w-full rounded-lg shadow-md mb-3">
            </div>

            <h2 class="text-2xl font-bold mb-4">Voucher List Page</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_voucher_list.png') }}" alt="Voucher List" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-3">
                    <div class="bg-gray-50 p-3 rounded">
                        <h4 class="font-semibold text-gray-900 mb-2">Summary Statistics</h4>
                        <ul class="text-sm text-gray-700 space-y-1 ml-4">
                            <li>• <strong>Total Vouchers:</strong> Count of all vouchers</li>
                            <li>• <strong>Draft Vouchers:</strong> Saved but not posted</li>
                            <li>• <strong>Posted Vouchers:</strong> Finalized and affecting accounts</li>
                            <li>• <strong>Total Amount:</strong> Sum of all voucher amounts</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <h4 class="font-semibold text-gray-900 mb-2">Search & Filters</h4>
                        <p class="text-sm text-gray-700">Search by voucher number, reference, or narration. Filter by type, date range, status, and amount.</p>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">6 Types of Ballie Common Vouchers</h2>
            <div class="grid md:grid-cols-2 gap-4 mb-8">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                    <h3 class="font-bold text-blue-900 mb-2">📘 Journal (JV)</h3>
                    <p class="text-sm text-blue-800">General accounting entries for adjustments, depreciation, and accruals</p>
                </div>
                <div class="bg-gradient-to-br from-red-50 to-red-100 p-4 rounded-lg border border-red-200">
                    <h3 class="font-bold text-red-900 mb-2">💸 Payment (PV)</h3>
                    <p class="text-sm text-red-800">Record money going out - paying suppliers, expenses, bills</p>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                    <h3 class="font-bold text-green-900 mb-2">💰 Receipt (RV)</h3>
                    <p class="text-sm text-green-800">Record money coming in - customer payments, sales revenue</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                    <h3 class="font-bold text-purple-900 mb-2">🔄 Contra (CV)</h3>
                    <p class="text-sm text-purple-800">Transfer between bank and cash accounts</p>
                </div>
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 rounded-lg border border-yellow-200">
                    <h3 class="font-bold text-yellow-900 mb-2">📈 Debit Note (DN)</h3>
                    <p class="text-sm text-yellow-800">Increase customer balance - additional charges, interest</p>
                </div>
                <div class="bg-gradient-to-br from-pink-50 to-pink-100 p-4 rounded-lg border border-pink-200">
                    <h3 class="font-bold text-pink-900 mb-2">📉 Credit Note (CN)</h3>
                    <p class="text-sm text-pink-800">Reduce customer balance - returns, discounts, corrections</p>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Creating a Journal Voucher</h2>
            <div class="space-y-6 mb-8">
                <div class="border-l-4 border-blue-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                        <h3 class="text-xl font-semibold">Click Journal Button</h3>
                    </div>
                    <img src="{{ asset('images/help/accounting_voucher_create-journal.png') }}" alt="Journal Entry" class="w-full rounded-lg shadow-md mb-3">
                    <p class="text-gray-700">Select <strong>Journal (JV)</strong> from the voucher type buttons at the top.</p>
                </div>

                <div class="border-l-4 border-blue-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
                        <h3 class="text-xl font-semibold">Fill Voucher Information</h3>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <div><strong>Voucher Date:</strong> Transaction date (defaults to today)</div>
                        <div><strong>Reference Number:</strong> Optional external reference</div>
                        <div><strong>Narration:</strong> Brief description of the transaction</div>
                        <div><strong>Voucher Number:</strong> Auto-generated on save</div>
                    </div>
                </div>

                <div class="border-l-4 border-blue-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
                        <h3 class="text-xl font-semibold">Add Voucher Entries</h3>
                    </div>
                    <p class="text-gray-700 mb-3">Each entry requires:</p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 mb-3">
                        <li><strong>Ledger Account:</strong> Select the account being affected</li>
                        <li><strong>Particulars:</strong> Description of this specific entry</li>
                        <li><strong>Debit Amount OR Credit Amount:</strong> Enter amount in one column only</li>
                    </ul>
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <p class="text-sm text-yellow-800">⚠️ <strong>Important:</strong> Total Debits must equal Total Credits. The system will show a warning if not balanced.</p>
                    </div>
                </div>

                <div class="border-l-4 border-blue-500 pl-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">4</div>
                        <h3 class="text-xl font-semibold">Save the Voucher</h3>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="bg-gray-100 p-4 rounded-lg border border-gray-300">
                            <h4 class="font-semibold mb-2">Save as Draft</h4>
                            <p class="text-sm text-gray-700">Saves without posting. Can edit later. Doesn't affect account balances.</p>
                        </div>
                        <div class="bg-green-100 p-4 rounded-lg border border-green-300">
                            <h4 class="font-semibold mb-2">Save & Post</h4>
                            <p class="text-sm text-gray-700">Posts to ledger immediately. Updates all account balances. Cannot edit after posting.</p>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Payment Voucher (PV)</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_voucher_create-payment.png') }}" alt="Payment Entry" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-4">
                    <div class="bg-red-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-red-900 mb-2">Bank Account (Credit - Top Section)</h4>
                        <p class="text-sm text-red-800">Select the bank/cash account money is leaving. Amount auto-calculates from payment entries.</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-green-900 mb-2">Payment Entries (Debit - Middle Section)</h4>
                        <p class="text-sm text-green-800">Add what you're paying for. Can add multiple entries. Attach documents (receipts, invoices).</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-900 mb-2">💡 Bulk Upload Feature</h4>
                        <p class="text-sm text-blue-800">Upload multiple payments from Excel/CSV. Download template, fill it, and upload. Perfect for salary payments or multiple supplier payments.</p>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Receipt Voucher (RV)</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_voucher_create-receipt.png') }}" alt="Receipt Entry" class="w-full rounded-lg shadow-md mb-4">
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-900 mb-2">Customer/Vendor Selection (Optional)</h4>
                        <p class="text-sm text-blue-800">Quick select customer or vendor. Auto-fills their ledger account in receipt entries.</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-red-900 mb-2">Receipt Entries (Credit - Top Section)</h4>
                        <p class="text-sm text-red-800">Where money is coming from. Can receive from multiple sources in one voucher.</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-green-900 mb-2">Bank Account (Debit - Bottom Section)</h4>
                        <p class="text-sm text-green-800">Select bank/cash account receiving money. Amount auto-calculates from receipt entries.</p>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Contra Voucher (CV)</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_voucher_create-contra.png') }}" alt="Contra Entry" class="w-full rounded-lg shadow-md mb-4">
                <p class="text-gray-700 mb-4">Simplest voucher type - just transfer money between your own accounts.</p>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-red-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-red-900 mb-2">From Account (Credit)</h4>
                        <p class="text-sm text-red-800">Account money is leaving. Shows balance before and after transfer.</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-green-900 mb-2">To Account (Debit)</h4>
                        <p class="text-sm text-green-800">Account money is entering. Shows balance before and after transfer.</p>
                    </div>
                </div>
                <div class="mt-4 bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <p class="text-sm text-purple-800"><strong>Example:</strong> Withdrawing ₦100,000 cash from bank</p>
                    <div class="mt-2 text-sm text-purple-700">
                        <div>From: Bank Account (Credit) ₦100,000</div>
                        <div>To: Cash in Hand (Debit) ₦100,000</div>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Debit Note (DN)</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_voucher_create-debit-note.png') }}" alt="Debit Note" class="w-full rounded-lg shadow-md mb-4">
                <p class="text-gray-700 mb-4">Increases the amount a customer owes you.</p>
                <div class="space-y-3">
                    <div class="bg-yellow-50 p-3 rounded">
                        <strong>Common Uses:</strong> Late payment fees, additional charges, interest on overdue payments, billing corrections (undercharged)
                    </div>
                    <div class="bg-blue-50 p-3 rounded">
                        <strong>Structure:</strong> Customer Account (Debit) + Additional Charge Accounts (Credit)
                    </div>
                    <div class="bg-green-50 p-3 rounded">
                        <strong>Example:</strong> Charging ₦5,000 late fee<br>
                        <span class="text-sm">Customer Account (Dr) ₦5,000 | Late Fee Income (Cr) ₦5,000</span>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Credit Note (CN)</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <img src="{{ asset('images/help/accounting_voucher_create-credit-note.png') }}" alt="Credit Note" class="w-full rounded-lg shadow-md mb-4">
                <p class="text-gray-700 mb-4">Reduces the amount a customer owes you.</p>
                <div class="space-y-3">
                    <div class="bg-yellow-50 p-3 rounded">
                        <strong>Common Uses:</strong> Product returns, discounts given, billing corrections (overcharged), goodwill adjustments
                    </div>
                    <div class="bg-blue-50 p-3 rounded">
                        <strong>Structure:</strong> Sales/Revenue Accounts (Debit) + Customer Account (Credit)
                    </div>
                    <div class="bg-green-50 p-3 rounded">
                        <strong>Example:</strong> Processing ₦20,000 product return<br>
                        <span class="text-sm">Sales Revenue (Dr) ₦20,000 | Customer Account (Cr) ₦20,000</span>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Understanding Debit & Credit</h2>
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                    <h3 class="text-lg font-bold text-green-800 mb-3">DEBIT (Dr.) - Increases ⬆️</h3>
                    <ul class="space-y-2 text-sm text-green-700">
                        <li>✓ <strong>Assets:</strong> Cash, Bank, Equipment (Money IN)</li>
                        <li>✓ <strong>Expenses:</strong> Rent, Salaries, Utilities (Spending UP)</li>
                    </ul>
                    <div class="mt-3 p-2 bg-green-100 rounded text-xs text-green-800">
                        💡 Think: When you GET something or SPEND money = DEBIT
                    </div>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                    <h3 class="text-lg font-bold text-blue-800 mb-3">CREDIT (Cr.) - Sources 💰</h3>
                    <ul class="space-y-2 text-sm text-blue-700">
                        <li>✓ <strong>Assets:</strong> Cash, Bank going OUT (Money OUT)</li>
                        <li>✓ <strong>Income:</strong> Sales, Revenue (Money SOURCE)</li>
                        <li>✓ <strong>Liabilities:</strong> Loans, Creditors (Money OWED)</li>
                    </ul>
                    <div class="mt-3 p-2 bg-blue-100 rounded text-xs text-blue-800">
                        💡 Think: Where money COMES FROM or GOES OUT = CREDIT
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-4">Quick Examples</h2>
            <div class="grid md:grid-cols-3 gap-4 mb-8">
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <h4 class="font-semibold text-yellow-900 mb-2">Pay Electricity ₦6,000</h4>
                    <div class="text-sm text-yellow-800 space-y-1">
                        <div>Dr: Electricity Expense ₦6,000</div>
                        <div>Cr: Bank Account ₦6,000</div>
                    </div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <h4 class="font-semibold text-yellow-900 mb-2">Receive Sales ₦10,000</h4>
                    <div class="text-sm text-yellow-800 space-y-1">
                        <div>Dr: Bank Account ₦10,000</div>
                        <div>Cr: Sales Revenue ₦10,000</div>
                    </div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <h4 class="font-semibold text-yellow-900 mb-2">Buy Equipment ₦5,000</h4>
                    <div class="text-sm text-yellow-800 space-y-1">
                        <div>Dr: Equipment ₦5,000</div>
                        <div>Cr: Cash Account ₦5,000</div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-6 rounded-lg border border-purple-200 mb-8">
                <h3 class="text-xl font-semibold text-purple-900 mb-4">💡 Helper Features</h3>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-white p-3 rounded">
                        <h4 class="font-semibold text-purple-800 mb-1">AI Assistant</h4>
                        <p class="text-sm text-gray-700">Get smart suggestions for account selection</p>
                    </div>
                    <div class="bg-white p-3 rounded">
                        <h4 class="font-semibold text-green-800 mb-1">Add Account</h4>
                        <p class="text-sm text-gray-700">Create new ledger account without leaving page</p>
                    </div>
                    <div class="bg-white p-3 rounded">
                        <h4 class="font-semibold text-blue-800 mb-1">Help Panel</h4>
                        <p class="text-sm text-gray-700">Shows debit/credit rules and examples</p>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-r-lg mb-8">
                <h3 class="text-lg font-semibold text-red-900 mb-3">❌ Common Mistakes to Avoid</h3>
                <ul class="space-y-2 text-sm text-red-800">
                    <li>• Not ensuring total debits equal total credits</li>
                    <li>• Using expense account for asset purchases</li>
                    <li>• Not describing what the entry is for</li>
                    <li>• Using today's date for past transactions</li>
                    <li>• Posting too quickly without reviewing</li>
                </ul>
            </div>

            <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-r-lg">
                <h3 class="text-lg font-semibold text-green-900 mb-3">✅ Best Practices</h3>
                <ul class="space-y-2 text-sm text-green-800">
                    <li>• Write clear particulars for each entry</li>
                    <li>• Always attach supporting documents for payments</li>
                    <li>• Use "Save as Draft" to review complex entries</li>
                    <li>• Use actual transaction dates, not entry dates</li>
                    <li>• Include external references (invoice #, check #)</li>
                    <li>• Enter transactions daily to avoid backlog</li>
                </ul>
            </div>
        </div>
    `
},
