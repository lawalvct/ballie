{{-- Expenses Tab --}}
<div x-show="activeTab === 'expenses'" x-transition>
    <!-- Add Expense Form -->
    <div class="mb-6 bg-gray-50 rounded-lg p-4">
        <h4 class="text-sm font-medium text-gray-700 mb-3">Record Project Expense</h4>
            <form @submit.prevent="addExpense()" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-4">
                <input type="text" x-model="newExpense.title" placeholder="Expense title..." required
                       class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                    </div>
                    <div class="md:col-span-2">
                    <input type="text" x-ref="expenseAmountInput" :value="expenseAmountDisplay" @input="setExpenseAmount($event.target.value)" inputmode="decimal" placeholder="Amount (₦)" required
                       class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                    </div>
                    <div class="md:col-span-3">
                <input type="date" x-model="newExpense.expense_date" required
                       class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                    </div>
                    <div class="md:col-span-3">
                <select x-model="newExpense.category"
                        class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500">
                    <option value="general">General</option>
                    <option value="materials">Materials</option>
                    <option value="labor">Labor</option>
                    <option value="travel">Travel</option>
                    <option value="equipment">Equipment</option>
                    <option value="software">Software</option>
                    <option value="subcontractor">Subcontractor</option>
                    <option value="other">Other</option>
                </select>
            </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                <div class="md:col-span-9">
                    <textarea x-model="newExpense.description" placeholder="Description (optional)" rows="3"
                              class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-violet-500 focus:border-violet-500"></textarea>
                </div>
                <div class="md:col-span-3 md:flex md:justify-end">
                    <button type="submit" :disabled="expenseLoading"
                            class="inline-flex items-center justify-center w-full md:w-auto px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 disabled:opacity-50 transition whitespace-nowrap">
                        <svg x-show="expenseLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Add Expense
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Expenses List -->
    <div id="expenses-list" class="space-y-3">
        @forelse($project->expenses as $expense)
            <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg" id="expense-{{ $expense->id }}">
                <div class="flex items-center space-x-4 flex-1 min-w-0">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ $expense->title }}</p>
                        <div class="flex items-center space-x-3 mt-1">
                            <span class="text-xs font-medium text-red-600">₦{{ number_format($expense->amount, 2) }}</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">{{ ucfirst($expense->category) }}</span>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}</span>
                            @if($expense->creator)
                                <span class="text-xs text-gray-400">by {{ $expense->creator->name }}</span>
                            @endif
                            @if($expense->voucher)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">{{ $expense->voucher->voucher_number }}</span>
                            @endif
                        </div>
                        @if($expense->description)
                            <p class="text-xs text-gray-500 mt-1">{{ $expense->description }}</p>
                        @endif
                    </div>
                </div>
                <button onclick="deleteExpense({{ $expense->id }})" class="ml-3 p-1 text-gray-400 hover:text-red-500 transition-colors duration-200" title="Delete expense">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        @empty
            <div id="expenses-empty-state" class="text-center py-8 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p>No expenses recorded yet. Add project costs above — they'll be posted to accounting automatically.</p>
            </div>
        @endforelse
    </div>
</div>
