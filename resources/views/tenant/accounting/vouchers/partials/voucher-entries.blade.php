<div class="bg-white shadow-sm rounded-lg border border-gray-200" x-data="voucherEntries()">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Voucher Entries</h3>
            <div class="flex items-center space-x-3">
                <!-- AI Accounting Assistant Button -->
                <button type="button"
                        onclick="toggleAISuggestions()"
                        class="inline-flex items-center px-3 py-2 border border-blue-300 text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    AI Assistant
                </button>
                <!-- Quick Add Ledger Account Button -->
                <button type="button"
                        onclick="event.preventDefault(); openAddLedgerModal();"
                        class="inline-flex items-center px-3 py-2 border border-green-300 text-sm leading-4 font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Account
                </button>
                <!-- Accounting Help Button -->
                <button type="button"
                        onclick="toggleAccountingHelp()"
                        class="inline-flex items-center px-3 py-2 border border-blue-300 text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Help
                </button>
                <button type="button"
                        @click="addEntry()"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Entry
                </button>
            </div>
        </div>
    </div>

    <!-- Accounting Help Panel -->
    <div id="accounting-help-panel" class="hidden bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-200">
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Debit Rules -->
                <div class="bg-white rounded-lg p-4 border border-green-200">
                    <h4 class="text-lg font-semibold text-green-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        DEBIT (Left Side) - INCREASES ⬆️
                    </h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center p-2 bg-green-50 rounded">
                            <span class="font-medium text-green-700">Assets:</span>
                            <span class="ml-2 text-green-600">Cash, Bank, Equipment (Money IN)</span>
                        </div>
                        <div class="flex items-center p-2 bg-green-50 rounded">
                            <span class="font-medium text-green-700">Expenses:</span>
                            <span class="ml-2 text-green-600">Rent, Electricity, Food (Spending UP)</span>
                        </div>
                        <div class="mt-3 p-2 bg-green-100 rounded text-green-800 text-xs">
                            💡 <strong>Think:</strong> When you GET something or SPEND money = DEBIT
                        </div>
                    </div>
                </div>

                <!-- Credit Rules -->
                <div class="bg-white rounded-lg p-4 border border-blue-200">
                    <h4 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                        CREDIT (Right Side) - DECREASES ⬇️ or SOURCES 💰
                    </h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center p-2 bg-blue-50 rounded">
                            <span class="font-medium text-blue-700">Assets:</span>
                            <span class="ml-2 text-blue-600">Cash, Bank going OUT (Money OUT)</span>
                        </div>
                        <div class="flex items-center p-2 bg-blue-50 rounded">
                            <span class="font-medium text-blue-700">Income:</span>
                            <span class="ml-2 text-blue-600">Sales, Service Revenue (Money SOURCE)</span>
                        </div>
                        <div class="flex items-center p-2 bg-blue-50 rounded">
                            <span class="font-medium text-blue-700">Liabilities:</span>
                            <span class="ml-2 text-blue-600">Loans, Creditors (Money OWED)</span>
                        </div>
                        <div class="mt-3 p-2 bg-blue-100 rounded text-blue-800 text-xs">
                            💡 <strong>Think:</strong> Where money COMES FROM or GOES OUT = CREDIT
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Examples -->
            <div class="mt-4 bg-white rounded-lg p-4 border border-yellow-200">
                <h4 class="text-lg font-semibold text-yellow-800 mb-3">📝 Quick Examples:</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="p-3 bg-yellow-50 rounded">
                        <div class="font-medium text-yellow-800">Pay Electricity ₦6,000</div>
                        <div class="mt-1 text-yellow-700">
                            <div>DEBIT: Electricity Expense ₦6,000</div>
                            <div>CREDIT: Bank Account ₦6,000</div>
                        </div>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded">
                        <div class="font-medium text-yellow-800">Receive Sales ₦10,000</div>
                        <div class="mt-1 text-yellow-700">
                            <div>DEBIT: Bank Account ₦10,000</div>
                            <div>CREDIT: Sales Revenue ₦10,000</div>
                        </div>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded">
                        <div class="font-medium text-yellow-800">Buy Equipment ₦5,000</div>
                        <div class="mt-1 text-yellow-700">
                            <div>DEBIT: Equipment ₦5,000</div>
                            <div>CREDIT: Cash Account ₦5,000</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Suggestions Panel -->
    <div id="ai-suggestions-panel" class="hidden bg-gradient-to-r from-purple-50 to-blue-50 border-b border-purple-200">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold text-purple-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    🤖 AI Suggestions
                </h4>
                <div class="flex items-center space-x-2">
                    <button onclick="getAISuggestions()" class="text-purple-600 hover:text-purple-800 text-sm">
                        🔄 Refresh
                    </button>
                    <button onclick="toggleAISuggestions()" class="text-purple-600 hover:text-purple-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="ai-suggestions-content">
                <div class="text-center py-4">
                    <div class="text-purple-600">Click refresh to get AI suggestions for your entries</div>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6">
        @error('entries')
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600">{{ $message }}</p>
            </div>
        @enderror

        <!-- Entries Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ledger Account <span class="text-red-500">*</span>
                        </th>
                        <th class="text-left py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Particulars
                        </th>
                        <th class="text-right py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Debit Amount
                        </th>
                        <th class="text-right py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Credit Amount
                        </th>
                        <th class="text-center py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(entry, index) in entries" :key="index">
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-2">
                                <select :name="`entries[${index}][ledger_account_id]`"
                                        x-model="entry.ledger_account_id"
                                        @change="updateEntryAccount(index); showAccountHint($event.target, index)"
                                        class="block w-full pl-3 pr-10 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-md"
                                        required>
                                    <option value="">Select Account</option>
                                    @foreach($ledgerAccounts as $account)
                                        <option value="{{ $account->id }}"
                                                data-type="{{ strtolower($account->accountGroup->name) }}"
                                                data-hint="{{ $account->accountGroup->name }}">
                                            {{ $account->name }} ({{ $account->accountGroup->name }})
                                        </option>
                                    @endforeach
                                </select>
                                <!-- Account Type Hint -->
                                <div :id="`account-hint-${index}`" class="account-hint hidden mt-1 p-2 text-xs rounded-lg"></div>
                            </td>
                            <td class="py-3 px-2">
                                <input type="text"
                                       :name="`entries[${index}][particulars]`"
                                       x-model="entry.particulars"
                                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="e.g., Being electricity bill payment">
                            </td>
                            <td class="py-3 px-2">
                                <input type="number"
                                       :name="`entries[${index}][debit_amount]`"
                                       x-model="entry.debit_amount"
                                       @input="clearCredit(index)"
                                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 text-right"
                                       placeholder="0.00"
                                       step="0.01"
                                       min="0">
                            </td>
                            <td class="py-3 px-2">
                                <input type="number"
                                       :name="`entries[${index}][credit_amount]`"
                                       x-model="entry.credit_amount"
                                       @input="clearDebit(index)"
                                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right"
                                       placeholder="0.00"
                                       step="0.01"
                                       min="0">
                            </td>
                            <td class="py-3 px-2 text-center">
                                <button type="button"
                                        @click="removeEntry(index)"
                                        x-show="entries.length > 2"
                                        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-300 bg-gray-50">
                        <td colspan="2" class="py-3 px-2 text-sm font-medium text-gray-900">
                            Total
                        </td>
                        <td class="py-3 px-2 text-right text-sm font-medium text-gray-900">
                            ₦<span x-text="formatNumber(totalDebits)"></span>
                        </td>
                        <td class="py-3 px-2 text-right text-sm font-medium text-gray-900">
                            ₦<span x-text="formatNumber(totalCredits)"></span>
                        </td>
                        <td class="py-3 px-2"></td>
                    </tr>
                    <tr x-show="!isBalanced && totalDebits > 0" class="bg-red-50 border-t border-red-200">
                        <td colspan="5" class="py-3 px-2 text-center text-sm text-red-600">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span class="font-medium">Voucher is not balanced!</span>
                                <span class="ml-2">Difference: ₦<span x-text="formatNumber(Math.abs(totalDebits - totalCredits))"></span></span>
                            </div>
                            <div class="mt-1 text-xs text-red-500">
                                💡 Tip: Total Debits must equal Total Credits. Check your entries above.
                            </div>
                        </td>
                    </tr>
                    <tr x-show="isBalanced && totalDebits > 0" class="bg-green-50 border-t border-green-200">
                        <td colspan="5" class="py-2 px-2 text-center text-sm text-green-600">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium">✅ Voucher is balanced!</span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Submit Buttons Section -->
        <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-sm">
                        <span x-show="!isBalanced && totalDebits > 0" class="inline-flex items-center text-red-600 font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            ⚠️ Voucher must be balanced before saving
                        </span>
                        <span x-show="isBalanced && totalDebits > 0" class="inline-flex items-center text-green-600 font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            ✅ Voucher is balanced and ready to save
                        </span>
                        <span x-show="totalDebits === 0" class="text-gray-500">
                            💡 Add entries to create your voucher
                        </span>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="{{ route('tenant.accounting.vouchers.index', ['tenant' => $tenant->slug]) }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </a>

                        <button type="submit"
                                x-bind:disabled="!isBalanced || entries.length < 2"
                                x-bind:class="{
                                    'opacity-50 cursor-not-allowed': !isBalanced || entries.length < 2,
                                    'hover:bg-primary-700': isBalanced && entries.length >= 2
                                }"
                                class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span x-text="isBalanced && entries.length >= 2 ? 'Create Voucher' : 'Complete Entries'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global functions
window.formatNumber = function(num) {
    if (!num || isNaN(num)) return '0.00';
    return parseFloat(num).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

window.toggleAccountingHelp = function() {
    const panel = document.getElementById('accounting-help-panel');
    panel.classList.toggle('hidden');
};

window.getAISuggestions = function() {
    const aiContent = document.getElementById('ai-suggestions-content');
    if (!aiContent) return;
    
    // Show loading state
    aiContent.innerHTML = `
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
            <div class="flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-purple-600 mr-2"></div>
                <span class="text-purple-700 text-sm">AI analyzing your voucher entries...</span>
            </div>
        </div>
    `;
    
    // Collect current voucher data
    const context = {
        voucherType: document.querySelector('select[name="voucher_type"]')?.value || 'General',
        narration: document.querySelector('textarea[name="narration"]')?.value || '',
        entries: collectCurrentEntries(),
        totalDebits: calculateTotalDebits(),
        totalCredits: calculateTotalCredits(),
        isBalanced: Math.abs(calculateTotalDebits() - calculateTotalCredits()) < 0.01
    };
    
    // Make API call to get real AI suggestions
    fetch('/api/ai/accounting-suggestions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ context })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.suggestions) {
            displayAISuggestions(data.suggestions);
        } else {
            showAIError(data.message || 'AI suggestions temporarily unavailable');
        }
    })
    .catch(error => {
        console.error('AI API Error:', error);
        showAIError('Failed to get AI suggestions. Please try again.');
    });
};

function collectCurrentEntries() {
    const entries = [];
    const rows = document.querySelectorAll('#voucher-entries-table tbody tr');
    
    rows.forEach((row, index) => {
        const particulars = row.querySelector('.particulars-input')?.value || '';
        const debitAmount = parseFloat(row.querySelector('.debit-input')?.value || 0);
        const creditAmount = parseFloat(row.querySelector('.credit-input')?.value || 0);
        const ledgerAccountId = row.querySelector('.ledger-account-select')?.value || null;
        
        if (particulars || debitAmount > 0 || creditAmount > 0) {
            entries.push({
                particulars,
                debit_amount: debitAmount,
                credit_amount: creditAmount,
                ledger_account_id: ledgerAccountId
            });
        }
    });
    
    return entries;
}

function calculateTotalDebits() {
    let total = 0;
    document.querySelectorAll('.debit-input').forEach(input => {
        total += parseFloat(input.value || 0);
    });
    return total;
}

function calculateTotalCredits() {
    let total = 0;
    document.querySelectorAll('.credit-input').forEach(input => {
        total += parseFloat(input.value || 0);
    });
    return total;
}

function displayAISuggestions(suggestions) {
    const aiContent = document.getElementById('ai-suggestions-content');
    
    let html = '';
    
    // Display corrections
    if (suggestions.corrections && suggestions.corrections.length > 0) {
        html += `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <h5 class="font-semibold text-red-800 mb-2">🚨 Corrections Needed</h5>
                <ul class="text-red-700 text-sm space-y-1">
                    ${suggestions.corrections.map(correction => `<li>• ${correction}</li>`).join('')}
                </ul>
            </div>
        `;
    }
    
    // Display suggestions
    if (suggestions.suggestions && suggestions.suggestions.length > 0) {
        html += `
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h5 class="font-semibold text-blue-800 mb-2">💡 AI Suggestions</h5>
                <ul class="text-blue-700 text-sm space-y-1">
                    ${suggestions.suggestions.map(suggestion => `<li>• ${suggestion}</li>`).join('')}
                </ul>
            </div>
        `;
    }
    
    // Display tips
    if (suggestions.tips && suggestions.tips.length > 0) {
        html += `
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <h5 class="font-semibold text-green-800 mb-2">📚 Accounting Tips</h5>
                <ul class="text-green-700 text-sm space-y-1">
                    ${suggestions.tips.map(tip => `<li>• ${tip}</li>`).join('')}
                </ul>
            </div>
        `;
    }
    
    // Add action buttons
    html += `
        <div class="mt-4 flex flex-wrap gap-2">
            <button type="button" onclick="validateWithAI(); event.stopPropagation();" class="bg-purple-600 text-white px-3 py-1 text-xs rounded hover:bg-purple-700">
                🔍 Validate Transaction
            </button>
            <button type="button" onclick="getSmartTemplates(); event.stopPropagation();" class="bg-indigo-600 text-white px-3 py-1 text-xs rounded hover:bg-indigo-700">
                📋 Smart Templates
            </button>
            <button type="button" onclick="showAccountingChat(); event.stopPropagation();" class="bg-green-600 text-white px-3 py-1 text-xs rounded hover:bg-green-700">
                💬 Ask Question
            </button>
        </div>
    `;
    
    aiContent.innerHTML = html;
}

function showAIError(message) {
    const aiContent = document.getElementById('ai-suggestions-content');
    aiContent.innerHTML = `
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <h5 class="font-semibold text-yellow-800 mb-2">⚠️ AI Assistant</h5>
            <p class="text-yellow-700 text-sm">${message}</p>
            <button onclick="getAISuggestions()" class="mt-2 text-yellow-800 underline text-xs">Try Again</button>
        </div>
    `;
}

window.validateWithAI = function(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const context = {
        voucherType: document.querySelector('select[name="voucher_type"]')?.value || 'General',
        narration: document.querySelector('textarea[name="narration"]')?.value || '',
        entries: collectCurrentEntries(),
        totalDebits: calculateTotalDebits(),
        totalCredits: calculateTotalCredits(),
        isBalanced: Math.abs(calculateTotalDebits() - calculateTotalCredits()) < 0.01
    };
    
    fetch('/api/ai/validate-transaction', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ context })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.validation) {
            showValidationResults(data.validation);
        }
    })
    .catch(error => {
        console.error('Validation Error:', error);
        showAIError('Validation failed. Please try again.');
    });
};

window.getSmartTemplates = function(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const context = {
        voucherType: document.querySelector('select[name="voucher_type"]')?.value || 'General',
        narration: document.querySelector('textarea[name="narration"]')?.value || '',
        amount: Math.max(calculateTotalDebits(), calculateTotalCredits())
    };
    
    fetch('/api/ai/smart-templates', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ context })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.templates) {
            showSmartTemplates(data.templates);
        }
    })
    .catch(error => {
        console.error('Templates Error:', error);
        showAIError('Failed to load templates. Please try again.');
    });
};

function showValidationResults(validation) {
    const aiContent = document.getElementById('ai-suggestions-content');
    
    let html = '';
    const isValid = validation.isValid;
    const bgColor = isValid ? 'green' : 'red';
    const textColor = isValid ? 'green' : 'red';
    const icon = isValid ? '✅' : '❌';
    
    html += `
        <div class="bg-${bgColor}-50 border border-${bgColor}-200 rounded-lg p-4 mb-4">
            <h5 class="font-semibold text-${textColor}-800 mb-2">${icon} Validation Results</h5>
            <p class="text-${textColor}-700 text-sm">${validation.message}</p>
            
            ${validation.insights ? `
                <div class="mt-3">
                    <h6 class="font-medium text-${textColor}-800 text-sm">AI Insights:</h6>
                    <ul class="text-${textColor}-700 text-sm space-y-1 mt-1">
                        ${validation.insights.map(insight => `<li>• ${insight}</li>`).join('')}
                    </ul>
                </div>
            ` : ''}
            
            ${validation.warnings && validation.warnings.length > 0 ? `
                <div class="mt-3">
                    <h6 class="font-medium text-yellow-800 text-sm">⚠️ Warnings:</h6>
                    <ul class="text-yellow-700 text-sm space-y-1 mt-1">
                        ${validation.warnings.map(warning => `<li>• ${warning}</li>`).join('')}
                    </ul>
                </div>
            ` : ''}
        </div>
    `;
    
    aiContent.innerHTML = html;
}

function showSmartTemplates(templates) {
    const aiContent = document.getElementById('ai-suggestions-content');
    
    let html = `
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-4">
            <h5 class="font-semibold text-indigo-800 mb-2">📋 Smart Templates</h5>
            <p class="text-indigo-600 text-sm mb-3">
                AI-generated voucher templates based on your voucher type and description. 
                Click a template to automatically fill your voucher entries.
            </p>
            <div class="space-y-2">
    `;
    
    if (templates.length === 0) {
        html += `
            <div class="text-center py-4 text-indigo-600">
                <p class="text-sm">No templates found for your current voucher type.</p>
                <p class="text-xs mt-1">Try changing the voucher type or adding a description.</p>
            </div>
        `;
    } else {
        templates.forEach((template, index) => {
            html += `
                <div class="bg-white border border-indigo-200 rounded-lg p-3 cursor-pointer hover:bg-indigo-50 transition-colors" onclick="applyTemplate(${index}); event.stopPropagation();">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h6 class="font-medium text-indigo-800 text-sm">${template.name}</h6>
                            <p class="text-indigo-600 text-xs mb-2">${template.description}</p>
                            <div class="text-xs text-gray-600">
                                ${template.entries ? template.entries.map(entry => `
                                    <div>• ${entry.particulars} (${entry.amount_type})</div>
                                `).join('') : ''}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded block mb-1">${template.confidence}% match</span>
                            <button type="button" class="text-xs text-indigo-600 hover:text-indigo-800">Click to apply</button>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    html += `
            </div>
            <div class="mt-3 flex gap-2">
                <button type="button" onclick="getAISuggestions(); event.stopPropagation();" 
                        class="bg-gray-500 text-white px-3 py-1 text-xs rounded hover:bg-gray-600">
                    Back to Suggestions
                </button>
            </div>
        </div>
    `;
    
    aiContent.innerHTML = html;
    
    // Store templates globally for template application
    window.currentTemplates = templates;
}

window.applyTemplate = function(templateIndex) {
    if (!window.currentTemplates || !window.currentTemplates[templateIndex]) return;
    
    const template = window.currentTemplates[templateIndex];
    const tbody = document.querySelector('#voucher-entries-table tbody');
    
    // Clear existing entries
    tbody.innerHTML = '';
    
    // Add template entries
    template.entries.forEach((entry, index) => {
        addVoucherEntryRow();
        const row = tbody.children[index];
        
        // Fill particulars
        const particularsInput = row.querySelector('.particulars-input');
        if (particularsInput) {
            particularsInput.value = entry.particulars;
        }
        
        // Note: Amount filling would depend on your actual template structure
        // You might want to set a default amount or let user fill it
    });
    
    showAIError(`Template "${template.name}" applied successfully! Please fill in the amounts.`);
};

window.showAccountingChat = function(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const aiContent = document.getElementById('ai-suggestions-content');
    aiContent.innerHTML = `
        <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-4 mb-4">
            <h5 class="font-semibold text-green-800 mb-3">💬 Ask Your Accounting Question</h5>
            
            <div class="space-y-3">
                <div>
                    <textarea id="accounting-question" 
                              placeholder="Ask me anything about accounting, vouchers, Nigerian GAAP, or bookkeeping practices..."
                              class="w-full p-3 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                              rows="3"></textarea>
                </div>
                
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="askAccountingQuestion(); event.stopPropagation();" 
                            class="bg-green-600 text-white px-4 py-2 text-sm rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        🤔 Get Answer
                    </button>
                    <button type="button" onclick="showQuickQuestions(); event.stopPropagation();" 
                            class="bg-blue-500 text-white px-4 py-2 text-sm rounded hover:bg-blue-600">
                        ⚡ Quick Questions
                    </button>
                </div>
                
                <div id="chat-response" class="hidden mt-4 p-3 bg-white border border-gray-200 rounded-lg">
                    <!-- AI response will appear here -->
                </div>
            </div>
        </div>
    `;
    
    // Focus on the textarea
    setTimeout(() => {
        document.getElementById('accounting-question')?.focus();
    }, 100);
};

window.askAccountingQuestion = function(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const questionTextarea = document.getElementById('accounting-question');
    const question = questionTextarea?.value?.trim();
    
    if (!question) {
        alert('Please enter your accounting question first.');
        return;
    }
    
    const responseDiv = document.getElementById('chat-response');
    responseDiv.classList.remove('hidden');
    responseDiv.innerHTML = `
        <div class="flex items-center text-green-600">
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-green-600 mr-2"></div>
            <span class="text-sm">AI is thinking about your question...</span>
        </div>
    `;
    
    // Make API call for accounting Q&A
    fetch('/api/ai/ask-question', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            question: question,
            context: {
                voucherType: document.querySelector('select[name="voucher_type"]')?.value || 'General',
                narration: document.querySelector('textarea[name="narration"]')?.value || ''
            }
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.answer) {
            showAccountingAnswer(data.answer, question);
        } else {
            showAnswerError(data.message || 'Failed to get answer');
        }
    })
    .catch(error => {
        console.error('Q&A Error:', error);
        showAnswerError('Failed to get answer. Please try again.');
    });
};

window.showQuickQuestions = function(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const quickQuestions = [
        "What is the difference between debit and credit?",
        "How do I record a cash payment voucher?",
        "What are the Nigerian GAAP standards for voucher entries?",
        "How should I categorize office rent expense?",
        "What's the proper way to record bank transfers?",
        "How do I handle VAT in voucher entries?",
        "What accounts should I use for salary payments?",
        "How to record equipment purchases?"
    ];
    
    const questionTextarea = document.getElementById('accounting-question');
    const responseDiv = document.getElementById('chat-response');
    
    responseDiv.classList.remove('hidden');
    responseDiv.innerHTML = `
        <div class="space-y-2">
            <h6 class="font-medium text-gray-800 text-sm">💡 Quick Questions - Click to ask:</h6>
            <div class="grid grid-cols-1 gap-2">
                ${quickQuestions.map(q => `
                    <button type="button" 
                            onclick="selectQuickQuestion('${q.replace(/'/g, "\\'")}'); event.stopPropagation();"
                            class="text-left p-2 text-sm bg-gray-50 hover:bg-blue-50 border border-gray-200 hover:border-blue-300 rounded transition-colors">
                        ${q}
                    </button>
                `).join('')}
            </div>
        </div>
    `;
};

window.selectQuickQuestion = function(question) {
    const questionTextarea = document.getElementById('accounting-question');
    if (questionTextarea) {
        questionTextarea.value = question;
        askAccountingQuestion();
    }
};

function showAccountingAnswer(answer, question) {
    const responseDiv = document.getElementById('chat-response');
    responseDiv.innerHTML = `
        <div class="space-y-3">
            <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded">
                <h6 class="font-medium text-blue-800 text-sm mb-1">❓ Your Question:</h6>
                <p class="text-blue-700 text-sm">${question}</p>
            </div>
            
            <div class="bg-green-50 border-l-4 border-green-400 p-3 rounded">
                <h6 class="font-medium text-green-800 text-sm mb-2">🤖 AI Expert Answer:</h6>
                <div class="text-green-700 text-sm whitespace-pre-line">${answer}</div>
            </div>
            
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="askAnotherQuestion(); event.stopPropagation();" 
                        class="bg-green-500 text-white px-3 py-1 text-xs rounded hover:bg-green-600">
                    Ask Another Question
                </button>
                <button type="button" onclick="getAISuggestions(); event.stopPropagation();" 
                        class="bg-gray-500 text-white px-3 py-1 text-xs rounded hover:bg-gray-600">
                    Back to Suggestions
                </button>
            </div>
        </div>
    `;
}

function showAnswerError(message) {
    const responseDiv = document.getElementById('chat-response');
    responseDiv.innerHTML = `
        <div class="bg-red-50 border border-red-200 p-3 rounded">
            <h6 class="font-medium text-red-800 text-sm">❌ Error</h6>
            <p class="text-red-700 text-sm">${message}</p>
            <button type="button" onclick="askAccountingQuestion(); event.stopPropagation();" 
                    class="mt-2 text-red-800 underline text-xs">Try Again</button>
        </div>
    `;
}

window.askAnotherQuestion = function() {
    const questionTextarea = document.getElementById('accounting-question');
    if (questionTextarea) {
        questionTextarea.value = '';
        questionTextarea.focus();
    }
    const responseDiv = document.getElementById('chat-response');
    responseDiv.classList.add('hidden');
};

window.toggleAISuggestions = function() {
    const panel = document.getElementById('ai-suggestions-panel');
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) {
        setTimeout(getAISuggestions, 500);
    }
};

window.openAddLedgerModal = function() {
    document.getElementById('addLedgerModal').classList.remove('hidden');
    document.getElementById('ledger_name').focus();
};

window.closeAddLedgerModal = function() {
    document.getElementById('addLedgerModal').classList.add('hidden');
    document.getElementById('addLedgerForm').reset();
};

window.addNewLedgerAccount = function(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const submitText = document.getElementById('addLedgerSubmitText');
    const spinner = document.getElementById('addLedgerSpinner');
    
    submitButton.disabled = true;
    submitText.textContent = 'Adding...';
    spinner.classList.remove('hidden');
    
    fetch('{{ route("tenant.accounting.ledger-accounts.store", ["tenant" => $tenant->slug]) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text().then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                updateLedgerDropdowns(data.account);
                closeAddLedgerModal();
                showNotification('Ledger account added successfully!', 'success');
            } else {
                if (data.errors) {
                    let errorMessage = 'Please correct the following errors:\n';
                    Object.keys(data.errors).forEach(field => {
                        errorMessage += `- ${data.errors[field][0]}\n`;
                    });
                    alert(errorMessage);
                } else {
                    alert(data.message || 'Failed to add ledger account.');
                }
            }
        } catch (e) {
            showNotification('Account created successfully! Refreshing page...', 'success');
            setTimeout(() => window.location.reload(), 1500);
        }
    }))
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitText.textContent = 'Add Account';
        spinner.classList.add('hidden');
    });
};

window.updateLedgerDropdowns = function(newAccount) {
    const selects = document.querySelectorAll('select[name*="[ledger_account_id]"]');
    selects.forEach(select => {
        const option = document.createElement('option');
        option.value = newAccount.id;
        option.textContent = `${newAccount.name} (${newAccount.account_group.name})`;
        option.setAttribute('data-type', newAccount.account_type.toLowerCase());
        option.setAttribute('data-hint', newAccount.account_group.name);
        select.appendChild(option);
    });
};

window.showNotification = function(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
        type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
        'bg-blue-100 text-blue-800 border border-blue-200'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            ${message}
        </div>
    `;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
};

// Alpine.js component
window.voucherEntries = function() {
    return {
        entries: [
            { ledger_account_id: '', particulars: '', debit_amount: '', credit_amount: '' },
            { ledger_account_id: '', particulars: '', debit_amount: '', credit_amount: '' }
        ],
        voucherTypeId: '',
        quickTemplates: [],
        currentVoucherType: null,

        get totalDebits() {
            return this.entries.reduce((sum, entry) => sum + (parseFloat(entry.debit_amount) || 0), 0);
        },

        get totalCredits() {
            return this.entries.reduce((sum, entry) => sum + (parseFloat(entry.credit_amount) || 0), 0);
        },

        get isBalanced() {
            const diff = Math.abs(this.totalDebits - this.totalCredits);
            return diff < 0.01 && this.totalDebits > 0;
        },

        formatNumber(num) {
            return window.formatNumber(num);
        },

        addEntry() {
            this.entries.push({ ledger_account_id: '', particulars: '', debit_amount: '', credit_amount: '' });
        },

        removeEntry(index) {
            if (this.entries.length > 2) {
                this.entries.splice(index, 1);
            }
        },

        clearCredit(index) {
            if (this.entries[index].debit_amount && parseFloat(this.entries[index].debit_amount) > 0) {
                this.entries[index].credit_amount = '';
            }
        },

        clearDebit(index) {
            if (this.entries[index].credit_amount && parseFloat(this.entries[index].credit_amount) > 0) {
                this.entries[index].debit_amount = '';
            }
        },

        updateEntryAccount(index) {
            const entry = this.entries[index];
            if (!entry.particulars && entry.ledger_account_id) {
                const selectElement = document.querySelector(`select[name="entries[${index}][ledger_account_id]"]`);
                if (selectElement) {
                    const selectedOption = selectElement.options[selectElement.selectedIndex];
                    const accountName = selectedOption.text.split(' (')[0];
                    entry.particulars = `Being entry for ${accountName}`;
                }
            }
        },

        init() {
            console.log('✅ Voucher entries component initialized');
        }
    }
};
</script>
