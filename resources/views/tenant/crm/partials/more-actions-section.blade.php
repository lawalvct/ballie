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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            All CRM Actions
        </h3>
        <button @click="moreActionsExpanded = false"
                class="text-gray-400 hover:text-white transition-colors duration-200 p-2 rounded-lg hover:bg-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Customer Management Section -->
    <div class="mb-8">
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            Customer Management
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Add Customer Card -->
            <a href="{{ route('tenant.crm.customers.create', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-blue-600 to-blue-800 hover:from-blue-500 hover:to-blue-700 border border-blue-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-blue-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-blue-200 transition-colors duration-300">Add Customer</h5>
                        <p class="text-xs text-blue-200">Create new record</p>
                    </div>
                </div>
                <p class="text-xs text-blue-200">Create new customer records for your business.</p>
            </a>

            <!-- Customer List Card -->
            <a href="{{ route('tenant.crm.customers.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-green-600 to-green-800 hover:from-green-500 hover:to-green-700 border border-green-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-green-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-green-200 transition-colors duration-300">Customer List</h5>
                        <p class="text-xs text-green-200">View all customers</p>
                    </div>
                </div>
                <p class="text-xs text-green-200">Browse and manage all your existing customers.</p>
            </a>

            <!-- Customer Statements Card -->
            <a href="{{ route('tenant.crm.customers.statements', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-purple-600 to-purple-800 hover:from-purple-500 hover:to-purple-700 border border-purple-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-purple-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-purple-200 transition-colors duration-300">Customer Statements</h5>
                        <p class="text-xs text-purple-200">Generate statements</p>
                    </div>
                </div>
                <p class="text-xs text-purple-200">Generate and send customer account statements.</p>
            </a>

            <!-- Customer Reports Card -->
            <a href="{{ route('tenant.reports.customer-sales', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-indigo-600 to-indigo-800 hover:from-indigo-500 hover:to-indigo-700 border border-indigo-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-indigo-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-indigo-200 transition-colors duration-300">Customer Reports</h5>
                        <p class="text-xs text-indigo-200">Analytics & insights</p>
                    </div>
                </div>
                <p class="text-xs text-indigo-200">View comprehensive customer analytics and reports.</p>
            </a>
        </div>
    </div>

    <!-- Invoices & Quotes Section -->
    <div class="mb-8">
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            Invoices & Quotes
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Create Invoice Card -->
            <a href="{{ route('tenant.accounting.invoices.create',  ['tenant' => $tenant->slug] ) }}"
               class="action-card bg-gradient-to-br from-emerald-600 to-emerald-800 hover:from-emerald-500 hover:to-emerald-700 border border-emerald-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-emerald-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-emerald-200 transition-colors duration-300">Create Invoice</h5>
                        <p class="text-xs text-emerald-200">New customer invoice</p>
                    </div>
                </div>
                <p class="text-xs text-emerald-200">Create new invoices for customer transactions.</p>
            </a>
 <!-- Invoice List Card -->
                       <a href="{{ route('tenant.accounting.invoices.index',  ['tenant' => $tenant->slug] ) }}"

               class="action-card bg-gradient-to-br from-sky-600 to-sky-800 hover:from-sky-500 hover:to-sky-700 border border-sky-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-sky-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-sky-200 transition-colors duration-300">Customer Invoices</h5>
                        <p class="text-xs text-sky-200">List all invoices</p>
                    </div>
                </div>
                <p class="text-xs text-sky-200">View and manage all customer invoices.</p>
            </a>
            <!-- New Quote Card -->
                <a href="{{ route('tenant.accounting.quotes.create', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-teal-600 to-teal-800 hover:from-teal-500 hover:to-teal-700 border border-teal-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-teal-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-teal-200 transition-colors duration-300">New Quote</h5>
                        <p class="text-xs text-teal-200">Create customer quote</p>
                    </div>
                </div>
                <p class="text-xs text-teal-200">Generate professional quotes for customers.</p>
            </a>

            <!-- Quote List Card -->
                <a href="{{ route('tenant.accounting.quotes.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-cyan-600 to-cyan-800 hover:from-cyan-500 hover:to-cyan-700 border border-cyan-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-cyan-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-cyan-200 transition-colors duration-300">Quote List</h5>
                        <p class="text-xs text-cyan-200">View all quotes</p>
                    </div>
                </div>
                <p class="text-xs text-cyan-200">Browse and manage all customer quotes.</p>
            </a>


        </div>
    </div>

    <!-- Payments & Collections Section -->
    <div class="mb-8">
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-yellow-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            Payments & Collections
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Record Payment Card -->
            <a href="{{ route('tenant.crm.record-payment', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-amber-600 to-amber-800 hover:from-amber-500 hover:to-amber-700 border border-amber-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-amber-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-amber-200 transition-colors duration-300">Record Payment</h5>
                        <p class="text-xs text-amber-200">Log customer payment</p>
                    </div>
                </div>
                <p class="text-xs text-amber-200">Record and track customer payment transactions.</p>
            </a>

            <!-- Payment Reminder Card -->
            <a href="{{ route('tenant.crm.payment-reminders', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-yellow-600 to-yellow-800 hover:from-yellow-500 hover:to-yellow-700 border border-yellow-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-yellow-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 00-15 0v5h5l-5 5-5-5h5V7a7.5 7.5 0 0115 0v10z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-yellow-200 transition-colors duration-300">Payment Reminder</h5>
                        <p class="text-xs text-yellow-200">Send reminder email</p>
                    </div>
                </div>
                <p class="text-xs text-yellow-200">Send payment reminders to customers automatically.</p>
            </a>

            <!-- Payment Reports Card -->
            <a href="{{ route('tenant.crm.payment-reports', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-lime-600 to-lime-800 hover:from-lime-500 hover:to-lime-700 border border-lime-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-lime-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-lime-200 transition-colors duration-300">Payment Reports</h5>
                        <p class="text-xs text-lime-200">View payment analytics</p>
                    </div>
                </div>
                <p class="text-xs text-lime-200">Analyze payment trends and collection reports.</p>
            </a>

            <!-- Customer Activities Card -->
            <a href="{{ route('tenant.crm.activities.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-orange-600 to-orange-800 hover:from-orange-500 hover:to-orange-700 border border-orange-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-orange-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-orange-200 transition-colors duration-300">Customer Activities</h5>
                        <p class="text-xs text-orange-200">Track interactions</p>
                    </div>
                </div>
                <p class="text-xs text-orange-200">Log and track customer interactions and follow-ups.</p>
            </a>
        </div>
    </div>

    <!-- Vendor Management Section -->
    <div>
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            Vendor Management
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Add Vendor Card -->
            <a href="{{ route('tenant.crm.vendors.create', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-fuchsia-600 to-fuchsia-800 hover:from-fuchsia-500 hover:to-fuchsia-700 border border-fuchsia-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-fuchsia-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-fuchsia-200 transition-colors duration-300">Add Vendor</h5>
                        <p class="text-xs text-fuchsia-200">Create new vendor</p>
                    </div>
                </div>
                <p class="text-xs text-fuchsia-200">Create new vendor records for your suppliers.</p>
            </a>

            <!-- Vendor List Card -->
            <a href="{{ route('tenant.crm.vendors.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-pink-600 to-pink-800 hover:from-pink-500 hover:to-pink-700 border border-pink-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-pink-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-pink-200 transition-colors duration-300">Vendor List</h5>
                        <p class="text-xs text-pink-200">View all vendors</p>
                    </div>
                </div>
                <p class="text-xs text-pink-200">Browse and manage all your vendor records.</p>
            </a>

            <!-- Vendor Statements Card -->
            <a href="#"
               class="action-card bg-gradient-to-br from-rose-600 to-rose-800 hover:from-rose-500 hover:to-rose-700 border border-rose-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-rose-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-rose-200 transition-colors duration-300">Vendor Statements</h5>
                        <p class="text-xs text-rose-200">Generate statements</p>
                    </div>
                </div>
                <p class="text-xs text-rose-200">Generate and review vendor account statements.</p>
            </a>

            <!-- Vendor Reports Card -->
            <a href="#"
               class="action-card bg-gradient-to-br from-violet-600 to-violet-800 hover:from-violet-500 hover:to-violet-700 border border-violet-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-violet-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-violet-200 transition-colors duration-300">Vendor Reports</h5>
                        <p class="text-xs text-violet-200">Analytics & insights</p>
                    </div>
                </div>
                <p class="text-xs text-violet-200">View comprehensive vendor analytics and reports.</p>
            </a>
        </div>
    </div>

    <!-- Purchase Orders (LPO) Section -->
    <div class="mb-8">
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            Purchase Orders (LPO)
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Create LPO Card -->
            <a href="{{ route('tenant.procurement.purchase-orders.create', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-orange-600 to-orange-800 hover:from-orange-500 hover:to-orange-700 border border-orange-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-orange-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-orange-200 transition-colors duration-300">Create LPO</h5>
                        <p class="text-xs text-orange-200">New purchase order</p>
                    </div>
                </div>
                <p class="text-xs text-orange-200">Create local purchase orders for vendors.</p>
            </a>

            <!-- LPO List Card -->
            <a href="{{ route('tenant.procurement.purchase-orders.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-amber-600 to-amber-800 hover:from-amber-500 hover:to-amber-700 border border-amber-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-amber-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-amber-200 transition-colors duration-300">LPO List</h5>
                        <p class="text-xs text-amber-200">View all orders</p>
                    </div>
                </div>
                <p class="text-xs text-amber-200">Browse and manage all purchase orders.</p>
            </a>
        </div>
    </div>
</div>
