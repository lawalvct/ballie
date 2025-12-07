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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            All Inventory Actions
        </h3>
        <button @click="moreActionsExpanded = false"
                class="text-gray-400 hover:text-white transition-colors duration-200 p-2 rounded-lg hover:bg-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Product Management Section -->
    <div class="mb-8">
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            Product Management
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Add Product Card -->
            <a href="{{ route('tenant.inventory.products.create', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-blue-600 to-blue-800 hover:from-blue-500 hover:to-blue-700 border border-blue-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-blue-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-blue-200 transition-colors duration-300">Add Product</h5>
                        <p class="text-xs text-blue-200">Create new product</p>
                    </div>
                </div>
                <p class="text-xs text-blue-200">Add new products to your inventory catalog.</p>
            </a>

            <!-- View Products Card -->
            <a href="{{ route('tenant.inventory.products.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-green-600 to-green-800 hover:from-green-500 hover:to-green-700 border border-green-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-green-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-green-200 transition-colors duration-300">View Products</h5>
                        <p class="text-xs text-green-200">Browse all products</p>
                    </div>
                </div>
                <p class="text-xs text-green-200">View and manage all your inventory products.</p>
            </a>

            <!-- Bulk Import Card -->
            <a href="{{ route('tenant.inventory.products.import', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-purple-600 to-purple-800 hover:from-purple-500 hover:to-purple-700 border border-purple-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-purple-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-purple-200 transition-colors duration-300">Bulk Import</h5>
                        <p class="text-xs text-purple-200">Import products</p>
                    </div>
                </div>
                <p class="text-xs text-purple-200">Import multiple products from CSV or Excel files.</p>
            </a>

            <!-- Product Export Card -->
            <a href="{{ route('tenant.inventory.products.export', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-pink-600 to-pink-800 hover:from-pink-500 hover:to-pink-700 border border-pink-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-pink-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-pink-200 transition-colors duration-300">Export Products</h5>
                        <p class="text-xs text-pink-200">Download data</p>
                    </div>
                </div>
                <p class="text-xs text-pink-200">Export product data to CSV or Excel format.</p>
            </a>
        </div>
    </div>

    <!-- Stock Management Section -->
    <div class="mb-8">
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            Stock Management
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Stock Adjustment Card -->
            <a href="{{ route('tenant.inventory.physical-stock.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-orange-600 to-orange-800 hover:from-orange-500 hover:to-orange-700 border border-orange-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-orange-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-orange-200 transition-colors duration-300">Stock Adjustment</h5>
                        <p class="text-xs text-orange-200">Physical Stock</p>
                    </div>
                </div>
                <p class="text-xs text-orange-200">Adjust quantities to match physical stock.</p>
            </a>

            <!-- Stock Movement Card -->
            <a href="{{ route('tenant.inventory.stock-journal.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-teal-600 to-teal-800 hover:from-teal-500 hover:to-teal-700 border border-teal-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-teal-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-teal-200 transition-colors duration-300">Stock Movement</h5>
                        <p class="text-xs text-teal-200">Track movements</p>
                    </div>
                </div>
                <p class="text-xs text-teal-200">View all stock movements and transactions.</p>
            </a>

            <!-- Low Stock Alert Card -->
            <a href="{{ route('tenant.inventory.low-stock', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-red-600 to-red-800 hover:from-red-500 hover:to-red-700 border border-red-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-red-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-red-200 transition-colors duration-300">Low Stock Alert</h5>
                        <p class="text-xs text-red-200">Monitor alerts</p>
                    </div>
                </div>
                <p class="text-xs text-red-200">View products with low stock levels.</p>
            </a>

            <!-- Stock Valuation Card -->
            <a href="#"
               class="action-card bg-gradient-to-br from-indigo-600 to-indigo-800 hover:from-indigo-500 hover:to-indigo-700 border border-indigo-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-indigo-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-indigo-200 transition-colors duration-300">Stock Valuation</h5>
                        <p class="text-xs text-indigo-200">Calculate value</p>
                    </div>
                </div>
                <p class="text-xs text-indigo-200">Calculate total inventory valuation.</p>
            </a>
        </div>
    </div>

    <!-- Categories & Units Section -->
    <div class="mb-8">
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-yellow-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            Categories & Units
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Manage Categories Card -->
            <a href="{{ route('tenant.inventory.categories.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-yellow-600 to-yellow-800 hover:from-yellow-500 hover:to-yellow-700 border border-yellow-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-yellow-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-yellow-200 transition-colors duration-300">Categories</h5>
                        <p class="text-xs text-yellow-200">Manage categories</p>
                    </div>
                </div>
                <p class="text-xs text-yellow-200">Organize products into categories.</p>
            </a>

            <!-- Manage Units Card -->
            <a href="{{ route('tenant.inventory.units.index', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-cyan-600 to-cyan-800 hover:from-cyan-500 hover:to-cyan-700 border border-cyan-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-cyan-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-cyan-200 transition-colors duration-300">Units</h5>
                        <p class="text-xs text-cyan-200">Manage units</p>
                    </div>
                </div>
                <p class="text-xs text-cyan-200">Define measurement units for products.</p>
            </a>

            <!-- Add Category Card -->
            <a href="{{ route('tenant.inventory.categories.create', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-emerald-600 to-emerald-800 hover:from-emerald-500 hover:to-emerald-700 border border-emerald-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-emerald-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-emerald-200 transition-colors duration-300">Add Category</h5>
                        <p class="text-xs text-emerald-200">Create new category</p>
                    </div>
                </div>
                <p class="text-xs text-emerald-200">Create new product categories.</p>
            </a>

            <!-- Add Unit Card -->
            <a href="{{ route('tenant.inventory.units.create', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-violet-600 to-violet-800 hover:from-violet-500 hover:to-violet-700 border border-violet-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-violet-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-violet-200 transition-colors duration-300">Add Unit</h5>
                        <p class="text-xs text-violet-200">Create new unit</p>
                    </div>
                </div>
                <p class="text-xs text-violet-200">Create new measurement units.</p>
            </a>
        </div>
    </div>

     <!-- Reports & Analytics Section -->
    <div>
        <h4 class="text-xl font-semibold text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-gray-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            Reports & Analytics
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Inventory Report Card -->
            <a href="{{ route('tenant.inventory.reports', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-gray-600 to-gray-800 hover:from-gray-500 hover:to-gray-700 border border-gray-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-gray-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-gray-200 transition-colors duration-300">Inventory Report</h5>
                        <p class="text-xs text-gray-200">Stock reports</p>
                    </div>
                </div>
                <p class="text-xs text-gray-200">Generate comprehensive inventory reports.</p>
            </a>

            <!-- Stock Movement Report Card -->
            <a href="{{ route('tenant.inventory.movements', ['tenant' => $tenant->slug]) }}"
               class="action-card bg-gradient-to-br from-slate-600 to-slate-800 hover:from-slate-500 hover:to-slate-700 border border-slate-500 rounded-xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-105 group">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-slate-500 bg-opacity-30 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white group-hover:text-slate-200 transition-colors duration-300">Movement Report</h5>
                        <p class="text-xs text-slate-200">Track movements</p>
                    </div>
                </div>
                <p class="text-xs text-slate-200">Analyze stock movement patterns.</p>
            </a>

          


        </div>
    </div>
</div>
