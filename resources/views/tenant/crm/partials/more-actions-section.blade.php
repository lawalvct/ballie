<div x-show="moreActionsExpanded"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95"
     class="bg-gradient-to-br from-purple-900 via-gray-800 to-gray-900 rounded-2xl p-8 shadow-2xl border border-gray-700"
     style="display: none;">

    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-white text-white-900">Quick CRM Actions</h3>
        <button @click="moreActionsExpanded = false"
                class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Customer Actions -->
    <div class="mb-8">
        <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Customer Management
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <a href="{{ route('tenant.crm.customers.create', ['tenant' => $tenant->slug]) }}"
               class="modal-action-card bg-gradient-to-br from-blue-500 to-blue-600 border border-blue-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Add Customer</h5>
                    <p class="text-xs opacity-90">Create new customer record</p>
                </div>
            </a>

            <a href="{{ route('tenant.crm.customers.index', ['tenant' => $tenant->slug]) }}"
               class="modal-action-card bg-gradient-to-br from-green-500 to-green-600 border border-green-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Customer List</h5>
                    <p class="text-xs opacity-90">View all customers</p>
                </div>
            </a>

            <a href="{{ route('tenant.crm.customers.statements', ['tenant' => $tenant->slug]) }}"
               class="modal-action-card bg-gradient-to-br from-purple-500 to-purple-600 border border-purple-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Customer Statement</h5>
                    <p class="text-xs opacity-90">Generate statements</p>
                </div>
            </a>

            <a href="#"
               class="modal-action-card bg-gradient-to-br from-indigo-500 to-indigo-600 border border-indigo-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Customer Reports</h5>
                    <p class="text-xs opacity-90">View customer analytics</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Invoice & Quote Actions -->
    <div class="mb-8">
        <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Invoices & Quotes
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <a href="#"
               class="modal-action-card bg-gradient-to-br from-emerald-500 to-emerald-600 border border-emerald-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Create Invoice</h5>
                    <p class="text-xs opacity-90">New customer invoice</p>
                </div>
            </a>

            <a href="#"
               class="modal-action-card bg-gradient-to-br from-teal-500 to-teal-600 border border-teal-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">New Quote</h5>
                    <p class="text-xs opacity-90">Create customer quote</p>
                </div>
            </a>

             <a href="#"
               class="modal-action-card bg-gradient-to-br from-cyan-500 to-cyan-600 border border-cyan-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Convert to Invoice</h5>
                    <p class="text-xs opacity-90">Quote to invoice</p>
                </div>
            </a>

            <a href="#"
               class="modal-action-card bg-gradient-to-br from-sky-500 to-sky-600 border border-sky-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Quote List</h5>
                    <p class="text-xs opacity-90">View all quotes</p>
                </div>
            </a>

            <a href="#"
               class="modal-action-card bg-gradient-to-br from-violet-500 to-violet-600 border border-violet-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Email Quote</h5>
                    <p class="text-xs opacity-90">Send to customer</p>
                </div>
            </a>

            <a href="#"
               class="modal-action-card bg-gradient-to-br from-rose-500 to-rose-600 border border-rose-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Customer Invoices</h5>
                    <p class="text-xs opacity-90">List all invoices</p>
                </div>
            </a>

            <a href="#"
               class="modal-action-card bg-gradient-to-br from-orange-500 to-orange-600 border border-orange-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Invoice Aging</h5>
                    <p class="text-xs opacity-90">View aging report</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Payment Actions -->
    <div class="mb-8">
        <h4 class="text-lg font-semibold  text-white mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Payments & Collections
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <a href="#"
               class="modal-action-card bg-gradient-to-br from-amber-500 to-amber-600 border border-amber-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Record Payment</h5>
                    <p class="text-xs opacity-90">Log customer payment</p>
                </div>
            </a>

            <a href="#"
               class="modal-action-card bg-gradient-to-br from-yellow-500 to-yellow-600 border border-yellow-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 00-15 0v5h5l-5 5-5-5h5V7a7.5 7.5 0 0115 0v10z"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Payment Reminder</h5>
                    <p class="text-xs opacity-90">Send reminder email</p>
                </div>
            </a>

            <a href="#"
               class="modal-action-card bg-gradient-to-br from-lime-500 to-lime-600 border border-lime-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Payment Reports</h5>
                    <p class="text-xs opacity-90">View payment analytics</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Vendor Actions -->
    <div>
        <h4 class="text-lg font-semibold text-white text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            Vendor Management
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <a href="{{ route('tenant.crm.vendors.create', ['tenant' => $tenant->slug]) }}"
               class="modal-action-card bg-gradient-to-br from-fuchsia-500 to-fuchsia-600 border border-fuchsia-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Add Vendor</h5>
                    <p class="text-xs opacity-90">Create new vendor</p>
                </div>
            </a>
            <a href="{{ route('tenant.crm.vendors.index', ['tenant' => $tenant->slug]) }}"
               class="modal-action-card bg-gradient-to-br from-pink-500 to-pink-600 border border-pink-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Vendor List</h5>
                    <p class="text-xs opacity-90">View all vendors</p>
                </div>
            </a>

            <a href="#"
               class="modal-action-card bg-gradient-to-br from-red-500 to-red-600 border border-red-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Vendor Statement</h5>
                    <p class="text-xs opacity-90">Generate statements</p>
                </div>
            </a>

            <a href="#"
               class="modal-action-card bg-gradient-to-br from-gray-500 to-gray-600 border border-gray-400 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h5 class="font-semibold text-sm mb-1">Vendor Reports</h5>
                    <p class="text-xs opacity-90">View vendor analytics</p>
                </div>
            </a>
        </div>
    </div>
</div>
