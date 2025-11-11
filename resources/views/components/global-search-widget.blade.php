<!-- Global Search Widget -->
<div id="globalSearchWidget" class="fixed bottom-6 right-6 z-50">
    <!-- Floating Search Button with Close Icon -->
    <div class="relative group">
        <!-- Close Button (Shows on Hover) -->
        <button id="hideWidgetBtn"
                class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 transform scale-0 group-hover:scale-100 z-10"
                aria-label="Hide Search Widget"
                title="Hide search widget">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Search Button -->
        <button id="searchWidgetBtn"
                class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white rounded-full p-4 shadow-2xl hover:shadow-purple-500/50 transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-4 focus:ring-purple-300"
                aria-label="Open Global Search">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </button>
    </div>

    <!-- Search Modal/Panel -->
    <div id="searchModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 pt-20 px-4" style="align-items: flex-start; justify-content: center;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[80vh] overflow-hidden transform transition-all duration-300">
            <!-- Search Header -->
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-blue-50">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text"
                           id="globalSearchInput"
                           class="flex-1 text-lg border-none focus:ring-0 bg-transparent placeholder-gray-400"
                           placeholder="Search for invoices, customers, products, vouchers..."
                           autofocus>
                    <button id="closeSearchModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Search Hints -->
                <div class="mt-3 flex flex-wrap gap-2" id="searchHints">
                    <span class="text-xs bg-white px-3 py-1 rounded-full text-gray-600 border border-gray-200">Try: "sales invoice"</span>
                    <span class="text-xs bg-white px-3 py-1 rounded-full text-gray-600 border border-gray-200">Try: "customer"</span>
                    <span class="text-xs bg-white px-3 py-1 rounded-full text-gray-600 border border-gray-200">Try: "products"</span>
                </div>
            </div>

            <!-- Loading State -->
            <div id="searchLoading" class="hidden p-8 text-center">
                <div class="inline-block w-8 h-8 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin"></div>
                <p class="mt-3 text-gray-600">Searching...</p>
            </div>

            <!-- Search Results -->
            <div id="searchResults" class="overflow-y-auto max-h-[60vh] custom-scrollbar">
                <!-- Quick Actions -->
                <div id="quickActionsSection" class="hidden">
                    <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Quick Actions
                        </h3>
                    </div>
                    <div id="quickActionsList" class="p-4 space-y-2"></div>
                </div>

                <!-- Routes Section -->
                <div id="routesSection" class="hidden">
                    <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Pages & Features
                        </h3>
                    </div>
                    <div id="routesList" class="p-4 space-y-2"></div>
                </div>

                <!-- Records Section -->
                <div id="recordsSection" class="hidden">
                    <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                            Records
                        </h3>
                    </div>
                    <div id="recordsList" class="p-4 space-y-2"></div>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Search Anything</h3>
                    <p class="text-gray-500">Type to search for invoices, customers, products, and more...</p>
                </div>

                <!-- No Results State -->
                <div id="noResults" class="hidden p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Results Found</h3>
                    <p class="text-gray-500">Try searching with different keywords</p>
                </div>
            </div>

            <!-- Search Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between text-xs text-gray-500">
                <div class="flex items-center space-x-4">
                    <span class="flex items-center">
                        <kbd class="px-2 py-1 bg-white border border-gray-300 rounded">Ctrl</kbd>
                        <span class="mx-1">+</span>
                        <kbd class="px-2 py-1 bg-white border border-gray-300 rounded">K</kbd>
                        <span class="ml-2">to open</span>
                    </span>
                    <span class="flex items-center">
                        <kbd class="px-2 py-1 bg-white border border-gray-300 rounded">Esc</kbd>
                        <span class="ml-2">to close</span>
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <span id="cacheIndicator" style="display: none;" class="text-green-600 font-medium flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                        </svg>
                        Cached
                    </span>
                    <span class="text-purple-600 font-medium">Smart Search</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }

    #searchModal > div {
        animation: fadeIn 0.2s ease-out;
    }

    .search-result-item {
        transition: all 0.2s ease;
    }

    .search-result-item:hover {
        transform: translateX(4px);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchWidgetBtn = document.getElementById('searchWidgetBtn');
    const hideWidgetBtn = document.getElementById('hideWidgetBtn');
    const globalSearchWidget = document.getElementById('globalSearchWidget');
    const searchModal = document.getElementById('searchModal');
    const searchInput = document.getElementById('globalSearchInput');
    const closeModalBtn = document.getElementById('closeSearchModal');
    const searchResults = document.getElementById('searchResults');
    const searchLoading = document.getElementById('searchLoading');
    const emptyState = document.getElementById('emptyState');
    const noResults = document.getElementById('noResults');

    const quickActionsSection = document.getElementById('quickActionsSection');
    const quickActionsList = document.getElementById('quickActionsList');
    const routesSection = document.getElementById('routesSection');
    const routesList = document.getElementById('routesList');
    const recordsSection = document.getElementById('recordsSection');
    const recordsList = document.getElementById('recordsList');

    let searchTimeout;
    const CACHE_KEY = 'globalSearchCache';
    const CACHE_EXPIRY = 5 * 60 * 1000; // 5 minutes
    const WIDGET_HIDDEN_KEY = 'globalSearchWidgetHidden';

    // Check if widget should be hidden
    function checkWidgetVisibility() {
        const isHidden = localStorage.getItem(WIDGET_HIDDEN_KEY) === 'true';
        if (isHidden) {
            globalSearchWidget.style.display = 'none';
        }
    }

    // Hide widget
    function hideWidget() {
        globalSearchWidget.style.display = 'none';
        localStorage.setItem(WIDGET_HIDDEN_KEY, 'true');

        // Show notification
        showNotification('Search widget hidden. Press Ctrl+K to search or refresh page to show widget again.', 'info');
    }

    // Show notification helper
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const bgColor = type === 'info' ? 'bg-blue-500' : 'bg-green-500';
        notification.className = `fixed bottom-20 right-6 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-bounce`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }

    // Get cached search results
    function getCachedResults(query) {
        try {
            const cache = localStorage.getItem(CACHE_KEY);
            if (!cache) return null;

            const cacheData = JSON.parse(cache);
            const now = Date.now();

            // Check if cache is expired
            if (now - cacheData.timestamp > CACHE_EXPIRY) {
                localStorage.removeItem(CACHE_KEY);
                return null;
            }

            // Return cached result for this query
            return cacheData.queries[query.toLowerCase()] || null;
        } catch (error) {
            console.error('Cache read error:', error);
            return null;
        }
    }

    // Save search results to cache
    function cacheResults(query, results) {
        try {
            let cacheData = {
                timestamp: Date.now(),
                queries: {}
            };

            // Try to load existing cache
            const existingCache = localStorage.getItem(CACHE_KEY);
            if (existingCache) {
                const parsed = JSON.parse(existingCache);
                const now = Date.now();

                // Keep cache if not expired
                if (now - parsed.timestamp <= CACHE_EXPIRY) {
                    cacheData = parsed;
                }
            }

            // Add new query result
            cacheData.queries[query.toLowerCase()] = results;
            cacheData.timestamp = Date.now();

            // Limit cache size (keep last 20 queries)
            const queryKeys = Object.keys(cacheData.queries);
            if (queryKeys.length > 20) {
                const keysToRemove = queryKeys.slice(0, queryKeys.length - 20);
                keysToRemove.forEach(key => delete cacheData.queries[key]);
            }

            localStorage.setItem(CACHE_KEY, JSON.stringify(cacheData));
        } catch (error) {
            console.error('Cache write error:', error);
            // Clear cache if storage is full
            if (error.name === 'QuotaExceededError') {
                localStorage.removeItem(CACHE_KEY);
            }
        }
    }

    // Clear expired cache on load
    function clearExpiredCache() {
        try {
            const cache = localStorage.getItem(CACHE_KEY);
            if (!cache) return;

            const cacheData = JSON.parse(cache);
            const now = Date.now();

            if (now - cacheData.timestamp > CACHE_EXPIRY) {
                localStorage.removeItem(CACHE_KEY);
            }
        } catch (error) {
            localStorage.removeItem(CACHE_KEY);
        }
    }

    // Initialize
    checkWidgetVisibility();
    clearExpiredCache();

    // Open search modal
    function openSearch() {
        searchModal.classList.remove('hidden');
        searchModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Show widget if hidden (user used Ctrl+K)
        globalSearchWidget.style.display = 'block';

        setTimeout(() => searchInput.focus(), 100);
    }

    // Close search modal
    function closeSearch() {
        searchModal.classList.add('hidden');
        searchModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        searchInput.value = '';
        resetSearchResults();
    }

    // Reset search results
    function resetSearchResults() {
        emptyState.classList.remove('hidden');
        noResults.classList.add('hidden');
        quickActionsSection.classList.add('hidden');
        routesSection.classList.add('hidden');
        recordsSection.classList.add('hidden');
    }

    // Event Listeners
    searchWidgetBtn.addEventListener('click', openSearch);
    hideWidgetBtn.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevent opening search
        hideWidget();
    });
    closeModalBtn.addEventListener('click', closeSearch);
    searchModal.addEventListener('click', (e) => {
        if (e.target === searchModal) closeSearch();
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        // Ctrl+K or Cmd+K to open
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            openSearch();
        }
        // Escape to close
        if (e.key === 'Escape' && !searchModal.classList.contains('hidden')) {
            closeSearch();
        }
    });

    // Search input handler
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();

        if (query.length < 2) {
            resetSearchResults();
            return;
        }

        searchLoading.classList.remove('hidden');
        emptyState.classList.add('hidden');

        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });

    // Perform search
    async function performSearch(query) {
        const cacheIndicator = document.getElementById('cacheIndicator');

        // Check cache first
        const cachedResults = getCachedResults(query);
        if (cachedResults) {
            console.log('Using cached results for:', query);
            searchLoading.classList.add('hidden');
            displayResults(cachedResults.searchData);

            if (cachedResults.quickActions && cachedResults.quickActions.length > 0) {
                displayQuickActions(cachedResults.quickActions);
            }

            // Show cache indicator
            if (cacheIndicator) {
                cacheIndicator.style.display = 'flex';
                setTimeout(() => {
                    cacheIndicator.style.display = 'none';
                }, 3000);
            }

            return;
        }

        // Hide cache indicator for fresh results
        if (cacheIndicator) {
            cacheIndicator.style.display = 'none';
        }

        // Fetch from API
        try {
            const response = await fetch(`{{ route('tenant.api.global-search', ['tenant' => tenant()->slug]) }}?query=${encodeURIComponent(query)}`);
            const data = await response.json();

            searchLoading.classList.add('hidden');
            displayResults(data);

            // Fetch quick actions
            const quickActionsData = await fetchQuickActions(query);

            // Cache the results
            cacheResults(query, {
                searchData: data,
                quickActions: quickActionsData || []
            });
        } catch (error) {
            console.error('Search error:', error);
            searchLoading.classList.add('hidden');
            showError();
        }
    }

    // Fetch quick actions
    async function fetchQuickActions(query) {
        try {
            const response = await fetch(`{{ route('tenant.api.quick-actions', ['tenant' => tenant()->slug]) }}?query=${encodeURIComponent(query)}`);
            const actions = await response.json();

            if (actions.length > 0) {
                displayQuickActions(actions);
            }

            return actions;
        } catch (error) {
            console.error('Quick actions error:', error);
            return [];
        }
    }

    // Display search results
    function displayResults(data) {
        const hasRoutes = data.routes && data.routes.length > 0;
        const hasRecords = data.records && data.records.length > 0;

        if (!hasRoutes && !hasRecords) {
            noResults.classList.remove('hidden');
            return;
        }

        noResults.classList.add('hidden');

        // Display routes
        if (hasRoutes) {
            routesSection.classList.remove('hidden');
            routesList.innerHTML = data.routes.map(route => createRouteItem(route)).join('');
        } else {
            routesSection.classList.add('hidden');
        }

        // Display records
        if (hasRecords) {
            recordsSection.classList.remove('hidden');
            recordsList.innerHTML = data.records.map(record => createRecordItem(record)).join('');
        } else {
            recordsSection.classList.add('hidden');
        }
    }

    // Display quick actions
    function displayQuickActions(actions) {
        if (actions.length > 0) {
            quickActionsSection.classList.remove('hidden');
            quickActionsList.innerHTML = actions.map(action => createQuickAction(action)).join('');
        }
    }

    // Create route item HTML
    function createRouteItem(route) {
        const colorClass = getCategoryColor(route.category);
        return `
            <a href="${route.url}" class="search-result-item flex items-center p-3 hover:bg-gray-50 rounded-lg border border-transparent hover:border-purple-200">
                <div class="flex-shrink-0 w-10 h-10 ${colorClass} rounded-lg flex items-center justify-center mr-3">
                    <i class="${route.icon} text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">${route.title}</p>
                    <p class="text-xs text-gray-500 truncate">${route.description}</p>
                </div>
                <span class="ml-3 text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">${route.category}</span>
            </a>
        `;
    }

    // Create record item HTML
    function createRecordItem(record) {
        const colorClass = getRecordColor(record.type);
        return `
            <a href="${record.url}" class="search-result-item flex items-center p-3 hover:bg-gray-50 rounded-lg border border-transparent hover:border-blue-200">
                <div class="flex-shrink-0 w-10 h-10 ${colorClass} rounded-lg flex items-center justify-center mr-3">
                    <i class="${record.icon} text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">${record.title}</p>
                    <p class="text-xs text-gray-500 truncate">${record.description}</p>
                </div>
                <span class="ml-3 text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded-full">${record.category}</span>
            </a>
        `;
    }

    // Create quick action HTML
    function createQuickAction(action) {
        const colors = {
            'blue': 'bg-blue-500 hover:bg-blue-600',
            'green': 'bg-green-500 hover:bg-green-600',
            'purple': 'bg-purple-500 hover:bg-purple-600',
            'orange': 'bg-orange-500 hover:bg-orange-600',
        };
        const colorClass = colors[action.color] || 'bg-gray-500 hover:bg-gray-600';

        return `
            <a href="${action.url}" class="flex items-center p-3 ${colorClass} text-white rounded-lg hover:shadow-lg transition-all">
                <i class="${action.icon} mr-3"></i>
                <span class="font-medium">${action.title}</span>
                <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        `;
    }

    // Helper functions for colors
    function getCategoryColor(category) {
        const colors = {
            'Accounting': 'bg-blue-500',
            'CRM': 'bg-green-500',
            'Inventory': 'bg-purple-500',
            'POS': 'bg-orange-500',
            'Reports': 'bg-indigo-500',
            'Settings': 'bg-gray-500',
            'Dashboard': 'bg-pink-500',
            'Payroll': 'bg-teal-500',
            'Admin': 'bg-red-500',
            'Banking': 'bg-cyan-500',
        };
        return colors[category] || 'bg-gray-500';
    }

    function getRecordColor(type) {
        const colors = {
            'customer': 'bg-green-500',
            'product': 'bg-purple-500',
            'voucher': 'bg-blue-500',
            'ledger_account': 'bg-indigo-500',
            'employee': 'bg-teal-500',
            'payroll_period': 'bg-cyan-500',
        };
        return colors[type] || 'bg-gray-500';
    }

    function showError() {
        routesList.innerHTML = '<div class="p-4 text-center text-red-600">An error occurred. Please try again.</div>';
    }
});
</script>
