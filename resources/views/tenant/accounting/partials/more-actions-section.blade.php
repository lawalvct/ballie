<!-- More Actions Expandable Section -->
<div x-show="moreActionsExpanded"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95"
     class="bg-gradient-to-br from-purple-900 via-gray-800 to-gray-900 rounded-2xl p-8 shadow-2xl border border-gray-700"
     style="display: none;">

    <!-- Section Header -->
    <div class="flex items-center justify-between mb-8">
        <h3 class="text-2xl font-bold text-white flex items-center">
            <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            All Accounting Actions
        </h3>
        <button @click="moreActionsExpanded = false"
                class="text-gray-400 hover:text-white transition-colors duration-200 p-2 rounded-lg hover:bg-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Voucher Management Section -->
    <div class="mb-8">
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            Voucher Management
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Voucher Types Card -->
            <a href="{{ route('tenant.accounting.voucher-types.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-blue-600 to-blue-800 hover:from-blue-500 hover:to-blue-700 border border-blue-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-blue-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-blue-200 transition-colors duration-300">Voucher Types</h5>
                        <p class="text-xs text-blue-200">Manage categories</p>
                    </div>
                </div>
                <p class="text-xs text-blue-200">Configure voucher types for better accounting management.</p>
            </a>

            <!-- Create Voucher Card -->
            <a href="{{ route('tenant.accounting.vouchers.create', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-green-600 to-green-800 hover:from-green-500 hover:to-green-700 border border-green-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-green-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-green-200 transition-colors duration-300">Create Voucher</h5>
                        <p class="text-xs text-green-200">Add new voucher</p>
                    </div>
                </div>
                <p class="text-xs text-green-200">Create new vouchers for recording financial transactions.</p>
            </a>

            <!-- View Vouchers Card -->
            <a href="{{ route('tenant.accounting.vouchers.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-purple-600 to-purple-800 hover:from-purple-500 hover:to-purple-700 border border-purple-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-purple-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-purple-200 transition-colors duration-300">View Vouchers</h5>
                        <p class="text-xs text-purple-200">Browse all vouchers</p>
                    </div>
                </div>
                <p class="text-xs text-purple-200">View and manage all your existing vouchers.</p>
            </a>

            <!-- Journal Entries Card -->
            <a href="{{ route('tenant.accounting.vouchers.create', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-pink-600 to-pink-800 hover:from-pink-500 hover:to-pink-700 border border-pink-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-pink-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-pink-200 transition-colors duration-300">Journal Entries</h5>
                        <p class="text-xs text-pink-200">Manage entries</p>
                    </div>
                </div>
                <p class="text-xs text-pink-200">Create and view journal entries for accurate records.</p>
            </a>
        </div>
    </div>

    <!-- Financial Reports Section -->
    <div class="mb-8">
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            Financial Reports
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Profit & Loss Card -->
            <a href="{{ route('tenant.reports.profit-loss', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-orange-600 to-orange-800 hover:from-orange-500 hover:to-orange-700 border border-orange-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-orange-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-orange-200 transition-colors duration-300">Profit & Loss</h5>
                        <p class="text-xs text-orange-200">Income statement</p>
                    </div>
                </div>
                <p class="text-xs text-orange-200">View comprehensive income statement and profit analysis.</p>
            </a>

            <!-- Balance Sheet Card -->
            <a href="{{ route('tenant.reports.balance-sheet', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-teal-600 to-teal-800 hover:from-teal-500 hover:to-teal-700 border border-teal-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-teal-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-teal-200 transition-colors duration-300">Balance Sheet</h5>
                        <p class="text-xs text-teal-200">Financial position</p>
                    </div>
                </div>
                <p class="text-xs text-teal-200">View assets, liabilities, and equity statements.</p>
            </a>

            <!-- Trial Balance Card -->
            <a href="{{ route('tenant.reports.trial-balance', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-indigo-600 to-indigo-800 hover:from-indigo-500 hover:to-indigo-700 border border-indigo-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-indigo-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0V4a1 1 0 011-1h3M7 3v18"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-indigo-200 transition-colors duration-300">Trial Balance</h5>
                        <p class="text-xs text-indigo-200">Account balances</p>
                    </div>
                </div>
                <p class="text-xs text-indigo-200">View all account balances and verify accuracy.</p>
            </a>

            <!-- Cash Flow Card -->
            <a href="{{ route('tenant.reports.cash-flow', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-cyan-600 to-cyan-800 hover:from-cyan-500 hover:to-cyan-700 border border-cyan-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-cyan-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m-3-6h6M9 10.5h6"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-cyan-200 transition-colors duration-300">Cash Flow</h5>
                        <p class="text-xs text-cyan-200">Money movement</p>
                    </div>
                </div>
                <p class="text-xs text-cyan-200">Track cash inflows and outflows over time.</p>
            </a>
        </div>
    </div>

    <!-- Account Management Section -->
    <div class="mb-8">
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-yellow-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            Account Management
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Chart of Accounts Card -->
            <a href="{{ route('tenant.accounting.ledger-accounts.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-yellow-600 to-yellow-800 hover:from-yellow-500 hover:to-yellow-700 border border-yellow-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-yellow-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-yellow-200 transition-colors duration-300">Ledger Accounts</h5>
                        <p class="text-xs text-yellow-200">Account structure</p>
                    </div>
                </div>
                <p class="text-xs text-yellow-200">Manage your complete chart of accounts structure.</p>
            </a>

            <!-- Account Groups Card -->
            <a href="{{ route('tenant.accounting.account-groups.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-red-600 to-red-800 hover:from-red-500 hover:to-red-700 border border-red-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-red-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-red-200 transition-colors duration-300">Account Groups</h5>
                        <p class="text-xs text-red-200">Group management</p>
                    </div>
                </div>
                <p class="text-xs text-red-200">Organize accounts into logical groups and categories.</p>
            </a>

            <!-- Bank Accounts Card -->
            <a href="{{ route('tenant.banking.banks.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-emerald-600 to-emerald-800 hover:from-emerald-500 hover:to-emerald-700 border border-emerald-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-emerald-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-emerald-200 transition-colors duration-300">Bank Accounts</h5>
                        <p class="text-xs text-emerald-200">Banking setup</p>
                    </div>
                </div>
                <p class="text-xs text-emerald-200">Manage your business bank accounts and reconciliation.</p>
            </a>

            <!-- Reconciliation Card -->
            <a href="{{ route('tenant.banking.reconciliations.index', ['tenant' => $tenant->slug]) }}"
                class="action-card bg-gradient-to-br from-violet-600 to-violet-800 hover:from-violet-500 hover:to-violet-700 border border-violet-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                 <div class="flex items-center mb-3">
                      <div class="w-10 h-10 bg-violet-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                      </div>
                      <div>
                            <h5 class="font-semibold text-white group-hover:text-violet-200 transition-colors duration-300">Reconciliation</h5>
                            <p class="text-xs text-violet-200">Balance matching</p>
                      </div>
                 </div>
                 <p class="text-xs text-violet-200">Reconcile bank statements with your records.</p>
            </a>
        </div>
    </div>

    <!-- Quick Actions Section -->
    {{-- <div>
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-gray-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            Quick Actions
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Backup Data Card -->
            <button onclick="backupData()"
                    class="action-card bg-gradient-to-br from-gray-600 to-gray-800 hover:from-gray-500 hover:to-gray-700 border border-gray-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group text-left">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-gray-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-gray-200 transition-colors duration-300">Backup Data</h5>
                        <p class="text-xs text-gray-200">Export records</p>
                    </div>
                </div>
                <p class="text-xs text-gray-200">Create backup of your accounting data.</p>
            </button>

            <!-- Import Data Card -->
            <button onclick="importData()"
                    class="action-card bg-gradient-to-br from-slate-600 to-slate-800 hover:from-slate-500 hover:to-slate-700 border border-slate-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group text-left">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-slate-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-slate-200 transition-colors duration-300">Import Data</h5>
                        <p class="text-xs text-slate-200">Upload records</p>
                    </div>
                </div>
                <p class="text-xs text-slate-200">Import accounting data from external sources.</p>
            </button>

            <!-- Settings Card -->
            <a href="#"
               class="action-card bg-gradient-to-br from-stone-600 to-stone-800 hover:from-stone-500 hover:to-stone-700 border border-stone-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">

                    <div class="w-10 h-10 bg-stone-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-stone-200 transition-colors duration-300">Settings</h5>
                        <p class="text-xs text-stone-200">Configuration</p>
                    </div>
                </div>
                <p class="text-xs text-stone-200">Configure accounting preferences and settings.</p>
            </a>

            <!-- Help & Support Card -->
            <a href="#"
               class="action-card bg-gradient-to-br from-amber-600 to-amber-800 hover:from-amber-500 hover:to-amber-700 border border-amber-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-amber-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-amber-200 transition-colors duration-300">Help & Support</h5>
                        <p class="text-xs text-amber-200">Get assistance</p>
                    </div>
                </div>
                <p class="text-xs text-amber-200">Access help documentation and support resources.</p>
            </a>
        </div>
    </div> --}}
</div>

<script>
function backupData() {
    // Add your backup functionality here
    alert('Backup functionality will be implemented here');
}

function importData() {
    // Add your import functionality here
    alert('Import functionality will be implemented here');
}
</script>
