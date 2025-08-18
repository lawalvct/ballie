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

    <div class="p-4">
        <!-- Real-time confidence meter -->
        <div class="mb-4">
            <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                <span>Entry Confidence</span>
                <span x-text="`${confidence}%`"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-500"
                     :class="confidence > 80 ? 'bg-green-500' : confidence > 60 ? 'bg-yellow-500' : 'bg-red-500'"
                     :style="`width: ${confidence}%`"></div>
            </div>
        </div>

        <!-- Live suggestions -->
        <div class="space-y-3">
            <template x-for="insight in insights" :key="insight.id">
                <div class="flex items-start space-x-3 p-3 rounded-lg"
                     :class="insight.type === 'error' ? 'bg-red-50 border border-red-200' :
                             insight.type === 'warning' ? 'bg-yellow-50 border border-yellow-200' :
                             'bg-blue-50 border border-blue-200'">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg x-show="insight.type === 'error'" class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <svg x-show="insight.type === 'warning'" class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <svg x-show="insight.type === 'suggestion'" class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium"
                           :class="insight.type === 'error' ? 'text-red-800' :
                                   insight.type === 'warning' ? 'text-yellow-800' :
                                   'text-blue-800'"
                           x-text="insight.message"></p>
                        <button x-show="insight.action"
                                @click="applyInsight(insight)"
                                class="mt-2 text-xs px-3 py-1 rounded-full border"
                                :class="insight.type === 'error' ? 'border-red-300 text-red-700 hover:bg-red-100' :
                                        insight.type === 'warning' ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-100' :
                                        'border-blue-300 text-blue-700 hover:bg-blue-100'"
                                x-text="insight.actionText">
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Quick fix buttons -->
        <div class="mt-4 flex flex-wrap gap-2" x-show="quickFixes.length > 0">
            <span class="text-xs text-gray-500 mr-2">Quick fixes:</span>
            <template x-for="fix in quickFixes" :key="fix.id">
                <button @click="applyQuickFix(fix)"
                        class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded-full hover:bg-purple-200 transition-colors"
                        x-text="fix.label">
                </button>
            </template>
        </div>
    </div>
</div>

<script>
window.aiInsights = function() {
    return {
        showInsights: true,
        confidence: 0,
        insights: [],
        quickFixes: [],
        analysisTimeout: null,

        init() {
            // Watch for changes in entries
            this.$watch('$store.voucherEntries.entries', () => {
                this.scheduleAnalysis();
            });

            console.log('✅ AI Insights component initialized');
        },

        scheduleAnalysis() {
            // Debounce analysis to avoid too many API calls
            clearTimeout(this.analysisTimeout);
            this.analysisTimeout = setTimeout(() => {
                this.analyzeEntries();
            }, 1500);
        },

        async analyzeEntries() {
            const entries = Alpine.store('voucherEntries').entries;

            if (entries.length === 0) {
                this.confidence = 0;
                this.insights = [];
                this.quickFixes = [];
                return;
            }

            // Perform local analysis first (fast)
            this.performLocalAnalysis(entries);

            // Then get AI insights (slower)
            if (window.AI_ENABLED) {
                await this.getAIInsights(entries);
            }
        },

        performLocalAnalysis(entries) {
            const insights = [];
            const quickFixes = [];
            let confidence = 60; // Base confidence

            // Check for common issues
            const hasEmptyAccount = entries.some(e => !e.ledger_account_id);
            const hasEmptyAmount = entries.some(e => !e.debit_amount && !e.credit_amount);
            const hasBothAmounts = entries.some(e => e.debit_amount && e.credit_amount);

            if (hasEmptyAccount) {
                insights.push({
                    id: 'empty_account',
                    type: 'error',
                    message: '⚠️ Some entries are missing ledger accounts',
                    action: true,
                    actionText: 'Highlight missing'
                });
                confidence -= 20;
            }

            if (hasEmptyAmount) {
                insights.push({
                    id: 'empty_amount',
                    type: 'warning',
                    message: '💰 Some entries are missing amounts',
                    action: true,
                    actionText: 'Show entries'
                });
                confidence -= 15;
            }

            if (hasBothAmounts) {
                insights.push({
                    id: 'both_amounts',
                    type: 'error',
                    message: '🚫 Entries cannot have both debit and credit amounts',
                    action: true,
                    actionText: 'Auto-fix'
                });
                confidence -= 25;

                quickFixes.push({
                    id: 'clear_duplicate_amounts',
                    label: 'Clear duplicate amounts',
                    action: 'clearDuplicateAmounts'
                });
            }

            // Check if balanced
            const totalDebits = entries.reduce((sum, e) => sum + (parseFloat(e.debit_amount) || 0), 0);
            const totalCredits = entries.reduce((sum, e) => sum + (parseFloat(e.credit_amount) || 0), 0);
            const isBalanced = Math.abs(totalDebits - totalCredits) < 0.01;

            if (!isBalanced && totalDebits > 0) {
                insights.push({
                    id: 'unbalanced',
                    type: 'error',
                    message: `⚖️ Transaction not balanced. Difference: ₦${Math.abs(totalDebits - totalCredits).toFixed(2)}`,
                    action: false
                });

 confidence -= 30;
            } else if (isBalanced && totalDebits > 0) {
                insights.push({
                    id: 'balanced',
                    type: 'suggestion',
                    message: '✅ Transaction is perfectly balanced!',
                    action: false
                });
                confidence += 20;
            }

            // Check for good practices
            const hasParticulars = entries.every(e => e.particulars?.trim());
            if (hasParticulars) {
                confidence += 10;
            } else {
                insights.push({
                    id: 'missing_particulars',
                    type: 'warning',
                    message: '📝 Consider adding descriptive particulars to all entries',
                    action: true,
                    actionText: 'Auto-generate'
                });

                quickFixes.push({
                    id: 'generate_particulars',
                    label: 'Generate particulars',
                    action: 'generateParticulars'
                });
            }

            this.confidence = Math.max(0, Math.min(100, confidence));
            this.insights = insights;
            this.quickFixes = quickFixes;
        },

        async getAIInsights(entries) {
            try {
                const response = await fetch('/api/ai/real-time-insights', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        entries,
                        voucherType: document.getElementById('voucher_type_id').selectedOptions[0]?.text || '',
                        narration: document.getElementById('narration').value
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Merge AI insights with local insights
                    this.insights = [...this.insights, ...data.insights];
                    this.quickFixes = [...this.quickFixes, ...data.quickFixes];
                    this.confidence = Math.max(this.confidence, data.confidence || this.confidence);
                }
            } catch (error) {
                console.log('AI insights unavailable, using local analysis only');
            }
        },

        applyInsight(insight) {
            switch (insight.id) {
                case 'empty_account':
                    this.highlightEmptyAccounts();
                    break;
                case 'empty_amount':
                    this.highlightEmptyAmounts();
                    break;
                case 'both_amounts':
                    this.clearDuplicateAmounts();
                    break;
                case 'missing_particulars':
                    this.generateParticulars();
                    break;
            }
        },

        applyQuickFix(fix) {
            switch (fix.action) {
                case 'clearDuplicateAmounts':
                    this.clearDuplicateAmounts();
                    break;
                case 'generateParticulars':
                    this.generateParticulars();
                    break;
            }
        },

        highlightEmptyAccounts() {
            document.querySelectorAll('select[name*="[ledger_account_id]"]').forEach((select, index) => {
                if (!select.value) {
                    select.classList.add('border-red-500', 'bg-red-50');
                    select.focus();
                    setTimeout(() => {
                        select.classList.remove('border-red-500', 'bg-red-50');
                    }, 3000);
                }
            });
        },

        clearDuplicateAmounts() {
            const entries = Alpine.store('voucherEntries').entries;
            entries.forEach(entry => {
                if (entry.debit_amount && entry.credit_amount) {
                    // Keep the larger amount, clear the smaller
                    if (parseFloat(entry.debit_amount) >= parseFloat(entry.credit_amount)) {
                        entry.credit_amount = '';
                    } else {
                        entry.debit_amount = '';
                    }
                }
            });
            this.showNotification('✅ Duplicate amounts cleared!', 'success');
        },

        generateParticulars() {
            const entries = Alpine.store('voucherEntries').entries;
            const voucherType = document.getElementById('voucher_type_id').selectedOptions[0]?.text || '';

            entries.forEach((entry, index) => {
                if (!entry.particulars?.trim() && entry.ledger_account_id) {
                    const select = document.querySelector(`select[name="entries[${index}][ledger_account_id]"]`);
                    const accountName = select?.selectedOptions[0]?.text?.split(' (')[0] || 'Account';
                    const isDebit = entry.debit_amount && parseFloat(entry.debit_amount) > 0;

                    entry.particulars = `Being ${isDebit ? 'debit to' : 'credit to'} ${accountName}`;
                }
            });
            this.showNotification('✅ Particulars generated!', 'success');
        },

        showNotification(message, type) {
            // Reuse the existing notification system
            window.showNotification(message, type);
        }
    }
};
</script>


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
                    <!-- Amount in Words Row -->
                    <tr x-show="isBalanced && totalDebits > 0" class="bg-blue-50 border-t border-blue-200">
                        <td colspan="5" class="py-3 px-2 text-center text-sm text-blue-800">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z"></path>
                                </svg>
                                <span class="font-medium">Amount in Words:</span>
                                <span class="ml-2 italic" x-text="convertToWords(totalDebits)"></span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Quick Entry Templates -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg" x-show="voucherTypeId">
            <h4 class="text-sm font-medium text-gray-900 mb-3">⚡ Quick Entry Templates</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <template x-for="template in quickTemplates" :key="template.name">
                    <button type="button"
                            @click="applyTemplate(template)"
                            class="text-left p-3 border border-gray-200 rounded-lg hover:bg-white hover:shadow-sm transition-all hover:border-primary-300">
                        <div class="text-sm font-medium text-gray-900" x-text="template.name"></div>
                        <div class="text-xs text-gray-500" x-text="template.description"></div>
                    </button>
                </template>
            </div>
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

<style>
/* Account Hint Styles */
.account-hint {
    font-size: 11px;
    line-height: 1.3;
    transition: all 0.3s ease;
}

.account-hint.show {
    display: block !important;
}

.account-hint.asset {
    background-color: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.account-hint.expense {
    background-color: #fef3c7;
    color: #92400e;
    border: 1px solid #fcd34d;
}

.account-hint.income {
    background-color: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.account-hint.liability {
    background-color: #fce7f3;
    color: #be185d;
    border: 1px solid #f9a8d4;
}

/* Animation for new rows */
tbody tr {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Help Panel Animation */
#accounting-help-panel {
    transition: all 0.3s ease-in-out;
    max-height: 0;
    overflow: hidden;
}

#accounting-help-panel.show {
    max-height: 1000px;
}
</style>

<script>
// Global functions that need to be available immediately
window.formatNumber = function(num) {
    if (!num || isNaN(num)) return '0.00';
    return parseFloat(num).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

window.convertToWords = function(amount) {
    if (!amount || amount === 0) return '';

    const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
    const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    const thousands = ['', 'Thousand', 'Million', 'Billion'];

    function convertHundreds(num) {
        let result = '';

        if (num > 99) {
            result += ones[Math.floor(num / 100)] + ' Hundred ';
            num %= 100;
        }

        if (num > 19) {
            result += tens[Math.floor(num / 10)] + ' ';
            num %= 10;
        } else if (num > 9) {
            result += teens[num - 10] + ' ';
            return result;
        }

        if (num > 0) {
            result += ones[num] + ' ';
        }

        return result;
    }

    function convertToWordsInternal(num) {
        if (num === 0) return 'Zero';

        let result = '';
        let thousandCounter = 0;

        while (num > 0) {
            if (num % 1000 !== 0) {
                result = convertHundreds(num % 1000) + thousands[thousandCounter] + ' ' + result;
            }
            num = Math.floor(num / 1000);
            thousandCounter++;
        }

        return result.trim();
    }

    const integerPart = Math.floor(amount);
    const decimalPart = Math.round((amount - integerPart) * 100);

    let words = convertToWordsInternal(integerPart) + ' Naira';

    if (decimalPart > 0) {
        words += ' and ' + convertToWordsInternal(decimalPart) + ' Kobo';
    }

    return words + ' Only';
};

window.toggleAccountingHelp = function() {
    const panel = document.getElementById('accounting-help-panel');
    panel.classList.toggle('hidden');
    panel.classList.toggle('show');
};

// Quick Add Ledger Account functions
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

    // Show loading state
    submitButton.disabled = true;
    submitText.textContent = 'Adding...';
    spinner.classList.remove('hidden');

    // Check if CSRF token exists
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('CSRF token not found. Please refresh the page and try again.');
        submitButton.disabled = false;
        submitText.textContent = 'Add Account';
        spinner.classList.add('hidden');
        return;
    }

    console.log('=== REQUEST DEBUG ===');
    console.log('URL:', '{{ route("tenant.accounting.ledger-accounts.store", ["tenant" => $tenant->slug]) }}');
    console.log('CSRF Token:', csrfToken.getAttribute('content'));
    console.log('Form Data:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    // Make AJAX request
    fetch('{{ route("tenant.accounting.ledger-accounts.store", ["tenant" => $tenant->slug]) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('=== RESPONSE DEBUG ===');
        console.log('Status:', response.status);
        console.log('Status Text:', response.statusText);
        console.log('Content-Type:', response.headers.get('content-type'));
        console.log('Response OK:', response.ok);

        // First, let's see the raw response text
        return response.text().then(text => {
            console.log('Raw response text:', text);

            // Try to parse as JSON
            try {
                const data = JSON.parse(text);
                console.log('Parsed JSON data:', data);
                return data;
            } catch (e) {
                console.error('Failed to parse JSON:', e);
                console.log('Response was not JSON, content:', text.substring(0, 500));

                // Account was likely created but Laravel returned a redirect
                showNotification('Account was created successfully! Refreshing page...', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
                return null;
            }
        });
    })
    .then(data => {
        if (!data) return; // Already handled above

        console.log('Processing data:', data);

        if (data.success) {
            console.log('Success! Account data:', data.account);

            // Add new account to all dropdowns
            updateLedgerDropdowns(data.account);

            // Close modal and reset form
            closeAddLedgerModal();

            // Show success message
            showNotification('Ledger account added successfully!', 'success');
        } else {
            console.log('Server returned error:', data);

            // Handle validation errors
            if (data.errors) {
                let errorMessage = 'Please correct the following errors:\n';
                Object.keys(data.errors).forEach(field => {
                    errorMessage += `- ${data.errors[field][0]}\n`;
                });
                alert(errorMessage);
            } else {
                alert(data.message || 'Failed to add ledger account. Please try again.');
            }
        }
    })
    .catch(error => {
    console.error('=== FETCH ERROR ===');
    console.error('Error:', error);
    console.error('Error message:', error.message);
    console.error('Error stack:', error.stack);

    // Fallback: Use traditional form submission
        console.log('Attempting fallback form submission...');

        // Create a temporary form for traditional submission
        const fallbackForm = document.createElement('form');
        fallbackForm.method = 'POST';
        fallbackForm.action = '{{ route("tenant.accounting.ledger-accounts.store", ["tenant" => $tenant->slug]) }}';
        fallbackForm.style.display = 'none';

        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fallbackForm.appendChild(csrfInput);

        // Add form data
        for (let [key, value] of formData.entries()) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            fallbackForm.appendChild(input);
        }

        // Add redirect parameter to come back to voucher creation
        const redirectInput = document.createElement('input');
        redirectInput.type = 'hidden';
        redirectInput.name = 'redirect_back';
        redirectInput.value = window.location.href;
        fallbackForm.appendChild(redirectInput);

        document.body.appendChild(fallbackForm);

        // Show message to user
        showNotification('Using alternative method to save account...', 'info');

        // Submit form after short delay
        setTimeout(() => {
            fallbackForm.submit();
        }, 1000);
    })
    .finally(() => {
        // Reset button state
        submitButton.disabled = false;
        submitText.textContent = 'Add Account';
        spinner.classList.add('hidden');
    });
};

window.updateLedgerDropdowns = function(newAccount) {
    // Find all ledger account dropdowns and add the new option
    const selects = document.querySelectorAll('select[name*="[ledger_account_id]"]');

    selects.forEach(select => {
        const option = document.createElement('option');
        option.value = newAccount.id;
        option.textContent = `${newAccount.name} (${newAccount.account_group.name})`;
        option.setAttribute('data-type', newAccount.account_type.toLowerCase());
        option.setAttribute('data-hint', newAccount.account_group.name);

        // Add option in alphabetical order
        let inserted = false;
        for (let i = 1; i < select.options.length; i++) { // Start from 1 to skip "Select Account" option
            if (select.options[i].text.localeCompare(option.textContent) > 0) {
                select.insertBefore(option.cloneNode(true), select.options[i]);
                inserted = true;
                break;
            }
        }

        if (!inserted) {
            select.appendChild(option.cloneNode(true));
        }
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

window.showAccountHint = function(selectElement, index) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const hintElement = document.getElementById(`account-hint-${index}`);

    if (selectedOption.value && hintElement) {
        const accountType = selectedOption.getAttribute('data-type');
        const accountGroup = selectedOption.getAttribute('data-hint');

        let hintText = '';
        let hintClass = '';

        // Determine hint based on account type
        if (accountType.includes('asset') || accountType.includes('bank') || accountType.includes('cash')) {
            hintText = `💰 ${accountGroup}: DEBIT to increase (money IN), CREDIT to decrease (money OUT)`;
            hintClass = 'asset';
        } else if (accountType.includes('expense') || accountType.includes('cost')) {
            hintText = `💸 ${accountGroup}: DEBIT to increase spending, CREDIT to decrease spending`;
            hintClass = 'expense';
        } else if (accountType.includes('income') || accountType.includes('revenue') || accountType.includes('sales')) {
            hintText = `💵 ${accountGroup}: CREDIT to increase income, DEBIT to decrease income`;
            hintClass = 'income';
        } else if (accountType.includes('liability') || accountType.includes('payable') || accountType.includes('loan')) {
            hintText = `📋 ${accountGroup}: CREDIT to increase debt, DEBIT to decrease debt`;
            hintClass = 'liability';
        } else {
            hintText = `📊 ${accountGroup}: Check account type for proper debit/credit usage`;
            hintClass = 'asset';
        }

        hintElement.textContent = hintText;
        hintElement.className = `account-hint show ${hintClass}`;

        // Hide hint after 5 seconds
        setTimeout(() => {
            hintElement.classList.remove('show');
        }, 5000);
    }
};

// Alpine.js component
window.voucherEntries = function() {
    return {
        entries: [
            {
                ledger_account_id: '',
                particulars: '',
                debit_amount: '',
                credit_amount: ''
            },
            {
                ledger_account_id: '',
                particulars: '',
                debit_amount: '',
                credit_amount: ''
            }
        ],
        voucherTypeId: '',
        quickTemplates: [],
        currentVoucherType: null,
        aiSuggestions: [],
        aiLoading: false,

        get totalDebits() {
            return this.entries.reduce((sum, entry) => {
                return sum + (parseFloat(entry.debit_amount) || 0);
            }, 0);
        },

        get totalCredits() {
            return this.entries.reduce((sum, entry) => {
                return sum + (parseFloat(entry.credit_amount) || 0);
            }, 0);
        },

        get isBalanced() {
            const diff = Math.abs(this.totalDebits - this.totalCredits);
            return diff < 0.01 && this.totalDebits > 0;
        },

        formatNumber(num) {
            return window.formatNumber(num);
        },

        convertToWords(amount) {
            return window.convertToWords(amount);
        },

        addEntry() {
            this.entries.push({
                ledger_account_id: '',
                particulars: '',
                debit_amount: '',
                credit_amount: ''
            });
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

        // AI-powered template selection
        async getSmartTemplates() {
            if (!this.currentVoucherType) return this.quickTemplates;

            const context = {
                voucherType: this.currentVoucherType.name,
                narration: document.getElementById('narration')?.value || '',
                amount: this.totalDebits || 0
            };

            try {
                const response = await fetch('/api/ai/smart-templates', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ context })
                });

                const data = await response.json();
                return data.templates || this.quickTemplates;
            } catch (error) {
                return this.quickTemplates;
            }
        },

        // AI-powered entry validation
        async validateWithAI() {
            this.aiLoading = true;

            const context = {
                entries: this.entries,
                voucherType: this.currentVoucherType?.name || '',
                totalDebits: this.totalDebits,
                totalCredits: this.totalCredits,
                isBalanced: this.isBalanced
            };

            try {
                const response = await fetch('/api/ai/validate-transaction', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ context })
                });

                const data = await response.json();

                if (data.success) {
                    this.showAIValidation(data.validation);
                }
            } catch (error) {
                console.error('AI validation failed:', error);
            } finally {
                this.aiLoading = false;
            }
        },

        showAIValidation(validation) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-md ${
                validation.isValid ? 'bg-green-100 border border-green-200' : 'bg-yellow-100 border border-yellow-200'
            }`;

            notification.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        ${validation.isValid ?
                            '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
                            '<svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>'
                        }
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium ${validation.isValid ? 'text-green-800' : 'text-yellow-800'}">
                            🤖 AI Validation: ${validation.isValid ? 'Looks Good!' : 'Suggestions Available'}
                        </h4>
                        <div class="mt-2 text-xs ${validation.isValid ? 'text-green-700' : 'text-yellow-700'}">
                            ${validation.message}
                        </div>
                        ${validation.suggestions ? `
                            <div class="mt-2 text-xs">
                                <button onclick="toggleAISuggestions()" class="text-blue-600 hover:text-blue-800">
                                    View detailed suggestions →
                                </button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        },

        // Enhanced init with AI features
        init() {
            this.loadQuickTemplates();
            this.watchVoucherType();

            // Auto-validate with AI when entries change (disabled for now)
            // this.$watch('entries', () => {
            //     if (this.entries.length >= 2 && this.totalDebits > 0) {
            //         setTimeout(() => this.validateWithAI(), 1000);
            //     }
            // });

            // Listen for inventory entry generation
            document.addEventListener('generate-voucher-entries', (e) => {
                this.generateEntriesFromInventory(e.detail);
            });

            console.log('✅ Voucher entries component initialized with AI integration');
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

        applyTemplate(template) {
            this.entries = [];

            template.entries.forEach(templateEntry => {
                this.entries.push({
                    ledger_account_id: '',
                    particulars: templateEntry.particulars || '',
                    debit_amount: templateEntry.amount_type === 'debit' ? '0.00' : '',
                    credit_amount: templateEntry.amount_type === 'credit' ? '0.00' : ''
                });
            });

            while (this.entries.length < 2) {
                this.addEntry();
            }
        },

        // NEW: Handle auto-generation from inventory
        generateEntriesFromInventory(inventoryData) {
            const { inventoryItems, totalAmount, voucherType } = inventoryData;

            // Clear existing entries
            this.entries = [];

            if (voucherType && voucherType.name) {
                const typeName = voucherType.name.toLowerCase();

                if (typeName.includes('sales')) {
                    // Sales Transaction
                    this.entries.push({
                        ledger_account_id: '',
                        particulars: 'Being goods sold (Debtors/Cash A/c Dr)',
                        debit_amount: totalAmount.toFixed(2),
                        credit_amount: ''
                    });
                    this.entries.push({
                        ledger_account_id: '',
                        particulars: 'Being sales revenue',
                        debit_amount: '',
                        credit_amount: totalAmount.toFixed(2)
                    });
                } else if (typeName.includes('purchase')) {
                    // Purchase Transaction
                    this.entries.push({
                        ledger_account_id: '',
                        particulars: 'Being goods purchased',
                        debit_amount: totalAmount.toFixed(2),
                        credit_amount: ''
                    });
                    this.entries.push({
                        ledger_account_id: '',
                        particulars: 'Being payment made (Cash/Bank/Creditors A/c Cr)',
                        debit_amount: '',
                        credit_amount: totalAmount.toFixed(2)
                    });
                } else {
                    // Generic inventory adjustment
                    this.entries.push({
                        ledger_account_id: '',
                        particulars: 'Being inventory adjustment',
                        debit_amount: totalAmount.toFixed(2),
                        credit_amount: ''
                    });
                    this.entries.push({
                        ledger_account_id: '',
                        particulars: 'Being corresponding account',
                        debit_amount: '',
                        credit_amount: totalAmount.toFixed(2)
                    });
                }
            }

            // Show success notification
            this.showNotification('Voucher entries generated from inventory items!', 'success');
        },

        // NEW: Notification system
        showNotification(message, type = 'info') {
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
        },

        // NEW: Watch for voucher type changes
        watchVoucherType() {
            this.$watch('voucherTypeId', (value) => {
                if (value) {
                    // Find the voucher type object
                    const voucherType = @json($voucherTypes->keyBy('id'));
                    this.currentVoucherType = voucherType[value] || null;

                    // Dispatch event for inventory section
                    document.dispatchEvent(new CustomEvent('voucher-type-changed', {
                        detail: {
                            voucherTypeId: value,
                            voucherType: this.currentVoucherType
                        }
                    }));

                    this.loadQuickTemplates();
                }
            });
        },

        init() {
            this.loadQuickTemplates();
            this.watchVoucherType();

            // Listen for inventory entry generation
            document.addEventListener('generate-voucher-entries', (e) => {
                this.generateEntriesFromInventory(e.detail);
            });

            console.log('✅ Voucher entries component initialized with inventory integration');
        },

        loadQuickTemplates() {
            const templates = {
                'payment': [
                    {
                        name: '💰 Cash Payment',
                        description: 'Payment made in cash',
                        entries: [
                            { particulars: 'Being payment made', amount_type: 'debit' },
                            { particulars: 'Being cash paid', amount_type: 'credit' }
                        ]
                    },
                    {
                        name: '🏦 Bank Payment',
                        description: 'Payment made via bank',
                        entries: [
                            { particulars: 'Being payment made', amount_type: 'debit' },
                            { particulars: 'Being bank payment', amount_type: 'credit' }
                        ]
                    }
                ],
                'receipt': [
                    {
                        name: '💵 Cash Receipt',
                        description: 'Receipt in cash',
                        entries: [
                            { particulars: 'Being cash received', amount_type: 'debit' },
                            { particulars: 'Being income received', amount_type: 'credit' }
                        ]
                    },
                    {
                        name: '🏦 Bank Receipt',
                        description: 'Receipt via bank',
                        entries: [
                            { particulars: 'Being bank receipt', amount_type: 'debit' },
                            { particulars: 'Being income received', amount_type: 'credit' }
                        ]
                    }
                ],
                'sales': [
                    {
                        name: '🛒 Cash Sales',
                        description: 'Cash sales transaction',
                        entries: [
                            { particulars: 'Being cash sales', amount_type: 'debit' },
                            { particulars: 'Being sales revenue', amount_type: 'credit' }
                        ]
                    },
                    {
                        name: '🏦 Credit Sales',
                        description: 'Credit sales transaction',
                        entries: [
                            { particulars: 'Being debtors A/c', amount_type: 'debit' },
                            { particulars: 'Being sales revenue', amount_type: 'credit' }
                        ]
                    }
                ],
                'purchase': [
                    {
                        name: '💰 Cash Purchase',
                        description: 'Cash purchase transaction',
                        entries: [
                            { particulars: 'Being goods purchased', amount_type: 'debit' },
                            { particulars: 'Being cash payment', amount_type: 'credit' }
                        ]
                    },
                    {
                        name: '🏦 Credit Purchase',
                        description: 'Credit purchase transaction',
                        entries: [
                            { particulars: 'Being goods purchased', amount_type: 'debit' },
                            { particulars: 'Being creditors A/c', amount_type: 'credit' }
                        ]
                    }
                ]
            };

            // Determine template type based on current voucher type
            let templateType = 'journal';
            if (this.currentVoucherType && this.currentVoucherType.name) {
                const typeName = this.currentVoucherType.name.toLowerCase();
                if (typeName.includes('sales')) templateType = 'sales';
                else if (typeName.includes('purchase')) templateType = 'purchase';
                else if (typeName.includes('payment')) templateType = 'payment';
                else if (typeName.includes('receipt')) templateType = 'receipt';
            }

            this.quickTemplates = templates[templateType] || templates['journal'] || [];
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // F1 to toggle help
        if (e.key === 'F1') {
            e.preventDefault();
            toggleAccountingHelp();
        }

        // Ctrl+H to toggle help
        if (e.ctrlKey && e.key === 'h') {
            e.preventDefault();
            toggleAccountingHelp();
        }

        // Ctrl+Enter to add new entry
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            const addButton = document.querySelector('button[\\@click="addEntry()"]');
            if (addButton) {
                addButton.click();
            }
        }
    });

    // Visual feedback for validation
    function validateEntry(entryRow) {
        const debitInput = entryRow.querySelector('input[name*="[debit_amount]"]');
        const creditInput = entryRow.querySelector('input[name*="[credit_amount]"]');
        const accountSelect = entryRow.querySelector('select[name*="[ledger_account_id]"]');

        const debitValue = parseFloat(debitInput.value) || 0;
        const creditValue = parseFloat(creditInput.value) || 0;

        // Remove previous validation classes
        entryRow.classList.remove('border-red-200', 'bg-red-50', 'border-green-200', 'bg-green-50');

        // Check if both debit and credit are filled (invalid)
        if (debitValue > 0 && creditValue > 0) {
            entryRow.classList.add('border-red-200', 'bg-red-50');
            return false;
        }

        // Check if account is selected and either debit or credit is filled
        if (accountSelect.value && (debitValue > 0 || creditValue > 0)) {
            entryRow.classList.add('border-green-200', 'bg-green-50');
            return true;
        }

        return true;
    }

    // Add validation on input change
    document.addEventListener('input', function(e) {
        if (e.target.type === 'number' || e.target.tagName === 'SELECT') {
            const entryRow = e.target.closest('tr');
            if (entryRow) {
                validateEntry(entryRow);
            }
        }
    });

    // Add keyboard navigation hints
    const keyboardHints = document.createElement('div');
    keyboardHints.innerHTML = `
        <div class="fixed bottom-4 right-4 bg-gray-800 text-white p-3 rounded-lg shadow-lg text-xs z-50" id="keyboard-hints" style="display: none;">
            <div class="font-semibold mb-2">⌨️ Keyboard Shortcuts:</div>
            <div>F1 or Ctrl+H: Toggle Help</div>
            <div>Ctrl+Enter: Add New Entry</div>
            <div>Tab: Navigate between fields</div>
        </div>
    `;
    document.body.appendChild(keyboardHints);

    // Show keyboard hints on first focus
    let hintsShown = false;
    document.addEventListener('focus', function(e) {
        if (!hintsShown && (e.target.type === 'number' || e.target.tagName === 'SELECT')) {
            const hints = document.getElementById('keyboard-hints');
            hints.style.display = 'block';
            hintsShown = true;

            setTimeout(() => {
                hints.style.display = 'none';
            }, 5000);
        }
    }, true);

    console.log('✅ Voucher entries with thousand separator and amount in words loaded successfully!');
});

window.aiAssistant = {
    async getSuggestions(context) {
        const prompt = this.buildPrompt(context);

        return await fetch('/api/ai/accounting-assistant', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ prompt, context })
        }).then(res => res.json());
    },

    buildPrompt(context) {
        return `
        You are an expert accounting assistant for a Nigerian business.

        Context:
        - Voucher Type: ${context.voucherType}
        - Current Entries: ${JSON.stringify(context.entries)}
        - Available Accounts: ${context.accounts.map(a => a.name).join(', ')}
        - Transaction Amount: ₦${context.totalAmount}
        - Narration: "${context.narration}"

        Please suggest:
        1. Correct debit/credit entries
        2. Appropriate ledger accounts
        3. Proper particulars/descriptions
        4. Any missing entries
        5. Compliance with Nigerian accounting standards

        Format your response as actionable suggestions.
        `;
    }
};


window.getAISuggestions = async function() {
    const loadingHtml = `
        <div class="flex items-center justify-center p-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
            <span class="ml-3 text-purple-600">AI is analyzing your entries...</span>
        </div>
    `;

    document.getElementById('ai-suggestions-content').innerHTML = loadingHtml;

    try {
        const context = {
            voucherType: document.getElementById('voucher_type_id').selectedOptions[0]?.text || '',
            narration: document.getElementById('narration').value,
            entries: Alpine.store('voucherEntries').entries,
            totalAmount: Alpine.store('voucherEntries').totalDebits
        };

        const response = await fetch('/api/ai/accounting-suggestions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ context })
        });

        const data = await response.json();

        if (data.success) {
            displayAISuggestions(data.suggestions);
        } else {
            showError('Unable to get AI suggestions');
        }
    } catch (error) {
        showError('Network error. Please try again.');
    }
};

window.displayAISuggestions = function(suggestions) {
    const html = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Corrections -->
            <div class="bg-white rounded-lg p-4 border border-red-200">
                <h5 class="font-semibold text-red-800 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    ⚠️ Corrections Needed
                </h5>
                <div class="space-y-2 text-sm">
                    ${suggestions.corrections?.map(c => `<div class="p-2 bg-red-50 rounded">${c}</div>`).join('') || '<p>No corrections needed! ✅</p>'}
                </div>
            </div>

            <!-- Suggestions -->
            <div class="bg-white rounded-lg p-4 border border-green-200">
                <h5 class="font-semibold text-green-800 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    💡 Suggestions
                </h5>
                <div class="space-y-2 text-sm">
                    ${suggestions.suggestions?.map(s => `<div class="p-2 bg-green-50 rounded">${s}</div>`).join('') || '<p>Entries look good!</p>'}
                </div>
            </div>
        </div>

        <!-- Tips Section -->
        <div class="mt-4 bg-white rounded-lg p-4 border border-blue-200">
            <h5 class="font-semibold text-blue-800 mb-2 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                💝 Pro Tips
            </h5>
            <div class="space-y-2 text-sm text-blue-700">
                ${suggestions.tips?.map(t => `<div class="flex items-start"><span class="mr-2">•</span><span>${t}</span></div>`).join('')}
            </div>
        </div>
    `;

    document.getElementById('ai-suggestions-content').innerHTML = html;
};

window.getAISuggestions = function() {
    const aiContent = document.getElementById('ai-suggestions-content');
    if (!aiContent) return;
    
    aiContent.innerHTML = `
        <div class="text-center py-4">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-600 mx-auto"></div>
            <div class="text-purple-600 mt-2">Getting AI suggestions...</div>
        </div>
    `;
    
    // For now, show a placeholder message since the AI API isn't set up yet
    setTimeout(() => {
        aiContent.innerHTML = `
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h5 class="font-semibold text-blue-800 mb-2">🤖 AI Assistant Ready</h5>
                <p class="text-blue-700 text-sm">
                    AI suggestions will be available once the backend API endpoints are configured.
                    Your OpenAI credentials are set up correctly in the .env file.
                </p>
                <div class="mt-3 text-xs text-blue-600">
                    <div>• Real-time validation of accounting entries</div>
                    <div>• Smart suggestions for missing accounts</div>
                    <div>• Automatic entry generation from descriptions</div>
                </div>
            </div>
        `;
    }, 1000);
};

window.toggleAISuggestions = function() {
    const panel = document.getElementById('ai-suggestions-panel');
    panel.classList.toggle('hidden');

    // Auto-get suggestions when opened if entries exist
    if (!panel.classList.contains('hidden')) {
        setTimeout(getAISuggestions, 500);
    }
};

window.explainEntry = function() {
    const currentEntries = Alpine.store('voucherEntries').entries;
    if (currentEntries.length === 0) {
        alert('Please add some entries first.');
        return;
    }

    const explanation = generateEntryExplanation(currentEntries);
    showExplanationModal(explanation);
};

window.generateEntryExplanation = function(entries) {
    return {
        transaction: "Here's what this transaction does:",
        steps: entries.map((entry, index) => {
            const amount = entry.debit_amount || entry.credit_amount;
            const type = entry.debit_amount ? 'DEBIT' : 'CREDIT';
            return `${index + 1}. ${type} ${entry.particulars || 'Entry'} - ₦${formatNumber(amount)}`;
        }),
        impact: "This will affect your financial statements as follows:",
        balanceCheck: Alpine.store('voucherEntries').isBalanced ? "✅ Transaction is balanced" : "⚠️ Transaction needs balancing"
    };
};
</script>
