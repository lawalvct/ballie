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
    <div id="searchModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 pt-[10vh] px-4" style="align-items: flex-start; justify-content: center;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[75vh] overflow-hidden transform transition-all duration-300 ring-1 ring-gray-200">
            <!-- Search Header -->
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-purple-100">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text"
                           id="globalSearchInput"
                           class="flex-1 text-base border-none focus:ring-0 bg-transparent placeholder-gray-400 py-1"
                           placeholder="Search pages, records, actions..."
                           autocomplete="off"
                           spellcheck="false">
                    <div id="searchSpinner" class="hidden">
                        <div class="w-5 h-5 border-2 border-purple-200 border-t-purple-600 rounded-full animate-spin"></div>
                    </div>
                    <button id="closeSearchModal" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-md hover:bg-gray-100">
                        <kbd class="text-xs font-sans px-1.5 py-0.5 bg-gray-100 border border-gray-200 rounded text-gray-500">ESC</kbd>
                    </button>
                </div>

                <!-- Category Filters -->
                <div class="mt-3 flex flex-wrap gap-1.5" id="categoryFilters">
                    <button data-category="all" class="search-filter-btn active text-xs px-2.5 py-1 rounded-full font-medium transition-all">All</button>
                    <button data-category="Accounting" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">Accounting</button>
                    <button data-category="CRM" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">CRM</button>
                    <button data-category="Inventory" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">Inventory</button>
                    <button data-category="Payroll" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">Payroll</button>
                    <button data-category="Reports" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">Reports</button>
                    <button data-category="more" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">More...</button>
                </div>
                <!-- Extended filters (hidden by default) -->
                <div class="mt-1.5 flex flex-wrap gap-1.5 hidden" id="extendedFilters">
                    <button data-category="Banking" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">Banking</button>
                    <button data-category="POS" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">POS</button>
                    <button data-category="Projects" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">Projects</button>
                    <button data-category="E-Commerce" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">E-Commerce</button>
                    <button data-category="Procurement" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">Procurement</button>
                    <button data-category="Statutory" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">Statutory</button>
                    <button data-category="Admin" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">Admin</button>
                    <button data-category="Settings" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">Settings</button>
                    <button data-category="Support" class="search-filter-btn text-xs px-2.5 py-1 rounded-full font-medium transition-all">Support</button>
                </div>
            </div>

            <!-- Search Results -->
            <div id="searchResults" class="overflow-y-auto max-h-[52vh] overscroll-contain">
                <!-- Quick Actions -->
                <div id="quickActionsSection" class="hidden">
                    <div class="px-4 py-2 bg-gray-50/80 border-b border-gray-100 sticky top-0">
                        <h3 class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Quick Actions
                        </h3>
                    </div>
                    <div id="quickActionsList" class="p-2 space-y-0.5"></div>
                </div>

                <!-- Routes Section -->
                <div id="routesSection" class="hidden">
                    <div class="px-4 py-2 bg-gray-50/80 border-b border-gray-100 sticky top-0">
                        <h3 class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Pages & Features
                        </h3>
                    </div>
                    <div id="routesList" class="p-2 space-y-0.5"></div>
                </div>

                <!-- Records Section -->
                <div id="recordsSection" class="hidden">
                    <div class="px-4 py-2 bg-gray-50/80 border-b border-gray-100 sticky top-0">
                        <h3 class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                            Records
                        </h3>
                    </div>
                    <div id="recordsList" class="p-2 space-y-0.5"></div>
                </div>

                <!-- Recent Searches -->
                <div id="recentSection" class="hidden">
                    <div class="px-4 py-2 bg-gray-50/80 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Recent Searches
                        </h3>
                        <button id="clearRecentBtn" class="text-[11px] text-gray-400 hover:text-red-500 transition-colors">Clear</button>
                    </div>
                    <div id="recentList" class="p-2 space-y-0.5"></div>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="py-10 px-6 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-purple-50 mb-4">
                        <svg class="w-7 h-7 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 mb-1">Search Anything</h3>
                    <p class="text-xs text-gray-500 mb-4">Find invoices, customers, products, vouchers, and more</p>
                    <div class="flex flex-wrap justify-center gap-2">
                        <button class="search-suggestion text-xs bg-gray-100 hover:bg-purple-100 hover:text-purple-700 px-3 py-1.5 rounded-full text-gray-600 transition-colors" data-query="sales invoice">sales invoice</button>
                        <button class="search-suggestion text-xs bg-gray-100 hover:bg-purple-100 hover:text-purple-700 px-3 py-1.5 rounded-full text-gray-600 transition-colors" data-query="purchase invoice">purchase invoice</button>
                        <button class="search-suggestion text-xs bg-gray-100 hover:bg-purple-100 hover:text-purple-700 px-3 py-1.5 rounded-full text-gray-600 transition-colors" data-query="customer">customer</button>
                        <button class="search-suggestion text-xs bg-gray-100 hover:bg-purple-100 hover:text-purple-700 px-3 py-1.5 rounded-full text-gray-600 transition-colors" data-query="products">products</button>
                        <button class="search-suggestion text-xs bg-gray-100 hover:bg-purple-100 hover:text-purple-700 px-3 py-1.5 rounded-full text-gray-600 transition-colors" data-query="reports">reports</button>
                        <button class="search-suggestion text-xs bg-gray-100 hover:bg-purple-100 hover:text-purple-700 px-3 py-1.5 rounded-full text-gray-600 transition-colors" data-query="payroll">payroll</button>
                    </div>
                </div>

                <!-- No Results State -->
                <div id="noResults" class="hidden py-10 px-6 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gray-50 mb-4">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 mb-1">No Results Found</h3>
                    <p class="text-xs text-gray-500">Try different keywords or browse by category</p>
                </div>
            </div>

            <!-- Search Footer -->
            <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 bg-white border border-gray-200 rounded text-[10px] font-mono">↑↓</kbd>
                        navigate
                    </span>
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 bg-white border border-gray-200 rounded text-[10px] font-mono">↵</kbd>
                        open
                    </span>
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 bg-white border border-gray-200 rounded text-[10px] font-mono">Ctrl+K</kbd>
                        search
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span id="resultCount" class="hidden text-purple-500 font-medium"></span>
                    <span id="cacheIndicator" style="display: none;" class="text-green-500 font-medium flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Cached
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #searchModal > div {
        animation: searchModalIn 0.15s ease-out;
    }
    @keyframes searchModalIn {
        from { opacity: 0; transform: scale(0.98) translateY(-8px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .search-result-item {
        transition: background-color 0.1s ease;
    }
    .search-result-item:hover,
    .search-result-item.active {
        background-color: #F5F3FF;
    }
    .search-result-item.active {
        outline: 2px solid #8B5CF6;
        outline-offset: -2px;
    }

    .search-filter-btn {
        color: #6B7280;
        background: #F3F4F6;
        border: 1px solid transparent;
    }
    .search-filter-btn:hover {
        background: #EDE9FE;
        color: #7C3AED;
    }
    .search-filter-btn.active {
        background: #7C3AED;
        color: white;
        border-color: #7C3AED;
    }

    #searchResults::-webkit-scrollbar { width: 4px; }
    #searchResults::-webkit-scrollbar-track { background: transparent; }
    #searchResults::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 4px; }
    #searchResults::-webkit-scrollbar-thumb:hover { background: #9CA3AF; }
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
    const searchSpinner = document.getElementById('searchSpinner');
    const emptyState = document.getElementById('emptyState');
    const noResults = document.getElementById('noResults');

    const quickActionsSection = document.getElementById('quickActionsSection');
    const quickActionsList = document.getElementById('quickActionsList');
    const routesSection = document.getElementById('routesSection');
    const routesList = document.getElementById('routesList');
    const recordsSection = document.getElementById('recordsSection');
    const recordsList = document.getElementById('recordsList');
    const recentSection = document.getElementById('recentSection');
    const recentList = document.getElementById('recentList');
    const resultCount = document.getElementById('resultCount');
    const cacheIndicator = document.getElementById('cacheIndicator');

    let searchTimeout;
    let activeIndex = -1;
    let activeCategory = 'all';
    let allResults = null;

    const CACHE_KEY = 'globalSearchCache';
    const CACHE_EXPIRY = 5 * 60 * 1000;
    const WIDGET_HIDDEN_KEY = 'globalSearchWidgetHidden';
    const RECENT_KEY = 'globalSearchRecent';
    const MAX_RECENT = 8;

    // Recent searches
    function getRecentSearches() {
        try {
            return JSON.parse(localStorage.getItem(RECENT_KEY) || '[]');
        } catch { return []; }
    }

    function addRecentSearch(query) {
        if (!query || query.length < 2) return;
        let recent = getRecentSearches().filter(r => r !== query);
        recent.unshift(query);
        if (recent.length > MAX_RECENT) recent = recent.slice(0, MAX_RECENT);
        localStorage.setItem(RECENT_KEY, JSON.stringify(recent));
    }

    function showRecentSearches() {
        const recent = getRecentSearches();
        if (recent.length === 0) {
            recentSection.classList.add('hidden');
            return;
        }
        recentSection.classList.remove('hidden');
        emptyState.classList.add('hidden');
        recentList.innerHTML = recent.map(q => `
            <button class="search-result-item w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left group" data-recent-query="${escapeAttr(q)}">
                <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm text-gray-700 flex-1 truncate">${escapeHtml(q)}</span>
                <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        `).join('');

        recentList.querySelectorAll('[data-recent-query]').forEach(btn => {
            btn.addEventListener('click', () => {
                searchInput.value = btn.dataset.recentQuery;
                searchInput.dispatchEvent(new Event('input'));
            });
        });
    }

    document.getElementById('clearRecentBtn').addEventListener('click', () => {
        localStorage.removeItem(RECENT_KEY);
        recentSection.classList.add('hidden');
        emptyState.classList.remove('hidden');
    });

    // Widget visibility
    function checkWidgetVisibility() {
        if (localStorage.getItem(WIDGET_HIDDEN_KEY) === 'true') {
            globalSearchWidget.style.display = 'none';
        }
    }

    function restoreWidget(showMessage) {
        globalSearchWidget.style.display = 'block';
        localStorage.setItem(WIDGET_HIDDEN_KEY, 'false');

        if (showMessage) {
            showNotification('Search widget restored.');
        }
    }

    function hideWidget() {
        globalSearchWidget.style.display = 'none';
        localStorage.setItem(WIDGET_HIDDEN_KEY, 'true');
        showNotification('Search widget hidden. Press Ctrl+K to search or restore it.');
    }

    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed bottom-6 right-6 bg-gray-800 text-white px-5 py-3 rounded-xl shadow-xl z-50 text-sm max-w-xs';
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.style.transition = 'opacity 0.3s ease';
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Cache functions
    function getCachedResults(query) {
        try {
            const cache = localStorage.getItem(CACHE_KEY);
            if (!cache) return null;
            const cacheData = JSON.parse(cache);
            if (Date.now() - cacheData.timestamp > CACHE_EXPIRY) {
                localStorage.removeItem(CACHE_KEY);
                return null;
            }
            return cacheData.queries[query.toLowerCase()] || null;
        } catch { return null; }
    }

    function cacheResults(query, results) {
        try {
            let cacheData = { timestamp: Date.now(), queries: {} };
            const existing = localStorage.getItem(CACHE_KEY);
            if (existing) {
                const parsed = JSON.parse(existing);
                if (Date.now() - parsed.timestamp <= CACHE_EXPIRY) cacheData = parsed;
            }
            cacheData.queries[query.toLowerCase()] = results;
            cacheData.timestamp = Date.now();
            const keys = Object.keys(cacheData.queries);
            if (keys.length > 30) {
                keys.slice(0, keys.length - 30).forEach(k => delete cacheData.queries[k]);
            }
            localStorage.setItem(CACHE_KEY, JSON.stringify(cacheData));
        } catch (e) {
            if (e.name === 'QuotaExceededError') localStorage.removeItem(CACHE_KEY);
        }
    }

    // Initialize
    checkWidgetVisibility();

    // Category filter logic
    document.querySelectorAll('.search-filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const cat = btn.dataset.category;

            if (cat === 'more') {
                const ext = document.getElementById('extendedFilters');
                ext.classList.toggle('hidden');
                btn.textContent = ext.classList.contains('hidden') ? 'More...' : 'Less';
                return;
            }

            document.querySelectorAll('.search-filter-btn').forEach(b => {
                if (b.dataset.category !== 'more') b.classList.remove('active');
            });
            btn.classList.add('active');
            activeCategory = cat;

            if (allResults) {
                displayResults(allResults.searchData, activeCategory);
            }
        });
    });

    // Suggestion clicks
    document.querySelectorAll('.search-suggestion').forEach(btn => {
        btn.addEventListener('click', () => {
            searchInput.value = btn.dataset.query;
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
        });
    });

    // Open/Close search
    function openSearch() {
        if (localStorage.getItem(WIDGET_HIDDEN_KEY) === 'true') {
            restoreWidget(true);
        }

        searchModal.classList.remove('hidden');
        searchModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        globalSearchWidget.style.display = 'block';
        setTimeout(() => {
            searchInput.focus();
            if (!searchInput.value.trim()) showRecentSearches();
        }, 50);
    }

    function closeSearch() {
        searchModal.classList.add('hidden');
        searchModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        searchInput.value = '';
        activeIndex = -1;
        allResults = null;
        resetSearchResults();
    }

    function resetSearchResults() {
        emptyState.classList.remove('hidden');
        noResults.classList.add('hidden');
        quickActionsSection.classList.add('hidden');
        routesSection.classList.add('hidden');
        recordsSection.classList.add('hidden');
        recentSection.classList.add('hidden');
        resultCount.classList.add('hidden');
    }

    // Events
    searchWidgetBtn.addEventListener('click', openSearch);
    hideWidgetBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        hideWidget();
    });
    closeModalBtn.addEventListener('click', closeSearch);
    searchModal.addEventListener('click', (e) => {
        if (e.target === searchModal) closeSearch();
    });

    // Global keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            if (searchModal.classList.contains('hidden')) openSearch();
            else closeSearch();
        }
        if (e.key === 'Escape' && !searchModal.classList.contains('hidden')) {
            closeSearch();
        }
    });

    // Keyboard navigation within results
    searchInput.addEventListener('keydown', (e) => {
        const items = searchResults.querySelectorAll('.search-result-item');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = Math.min(activeIndex + 1, items.length - 1);
            updateActiveItem(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = Math.max(activeIndex - 1, -1);
            updateActiveItem(items);
        } else if (e.key === 'Enter' && activeIndex >= 0 && items[activeIndex]) {
            e.preventDefault();
            const item = items[activeIndex];
            const href = item.getAttribute('href');
            if (item.dataset.recentQuery) {
                searchInput.value = item.dataset.recentQuery;
                searchInput.dispatchEvent(new Event('input'));
            } else if (href) {
                addRecentSearch(searchInput.value.trim());
                window.location.href = href;
            }
        }
    });

    function updateActiveItem(items) {
        items.forEach((item, i) => {
            item.classList.toggle('active', i === activeIndex);
        });
        if (activeIndex >= 0 && items[activeIndex]) {
            items[activeIndex].scrollIntoView({ block: 'nearest' });
        }
    }

    // Search input handler
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();
        activeIndex = -1;

        if (query.length < 2) {
            resetSearchResults();
            if (!query) showRecentSearches();
            return;
        }

        recentSection.classList.add('hidden');
        emptyState.classList.add('hidden');
        searchSpinner.classList.remove('hidden');

        searchTimeout = setTimeout(() => performSearch(query), 250);
    });

    // Perform search
    async function performSearch(query) {
        const cached = getCachedResults(query);
        if (cached) {
            searchSpinner.classList.add('hidden');
            allResults = cached;
            displayResults(cached.searchData, activeCategory);
            if (cached.quickActions && cached.quickActions.length > 0) {
                displayQuickActions(cached.quickActions);
            }
            if (cacheIndicator) {
                cacheIndicator.style.display = 'flex';
                setTimeout(() => { cacheIndicator.style.display = 'none'; }, 2000);
            }
            return;
        }

        if (cacheIndicator) cacheIndicator.style.display = 'none';

        try {
            const response = await fetch(`{{ route('tenant.api.global-search', ['tenant' => tenant()->slug]) }}?query=${encodeURIComponent(query)}`);
            const data = await response.json();
            searchSpinner.classList.add('hidden');

            allResults = { searchData: data, quickActions: [] };
            displayResults(data, activeCategory);

            fetchQuickActions(query).then(actions => {
                allResults.quickActions = actions || [];
                cacheResults(query, allResults);
            });

            addRecentSearch(query);
        } catch (error) {
            console.error('Search error:', error);
            searchSpinner.classList.add('hidden');
            noResults.classList.remove('hidden');
        }
    }

    async function fetchQuickActions(query) {
        try {
            const response = await fetch(`{{ route('tenant.api.quick-actions', ['tenant' => tenant()->slug]) }}?query=${encodeURIComponent(query)}`);
            const actions = await response.json();
            if (actions.length > 0) displayQuickActions(actions);
            return actions;
        } catch { return []; }
    }

    // Display results with category filtering
    function displayResults(data, categoryFilter) {
        categoryFilter = categoryFilter || 'all';
        let routes = data.routes || [];
        let records = data.records || [];

        if (categoryFilter !== 'all') {
            routes = routes.filter(r => r.category === categoryFilter);
            records = records.filter(r => r.category === categoryFilter);
        }

        const hasRoutes = routes.length > 0;
        const hasRecords = records.length > 0;
        const totalCount = routes.length + records.length;

        if (!hasRoutes && !hasRecords) {
            noResults.classList.remove('hidden');
            routesSection.classList.add('hidden');
            recordsSection.classList.add('hidden');
            resultCount.classList.add('hidden');
            return;
        }

        noResults.classList.add('hidden');
        emptyState.classList.add('hidden');

        resultCount.textContent = totalCount + ' result' + (totalCount !== 1 ? 's' : '');
        resultCount.classList.remove('hidden');

        if (hasRoutes) {
            routesSection.classList.remove('hidden');
            routesList.innerHTML = routes.map(route => createRouteItem(route)).join('');
        } else {
            routesSection.classList.add('hidden');
        }

        if (hasRecords) {
            recordsSection.classList.remove('hidden');
            recordsList.innerHTML = records.map(record => createRecordItem(record)).join('');
        } else {
            recordsSection.classList.add('hidden');
        }
    }

    function displayQuickActions(actions) {
        if (actions.length > 0) {
            quickActionsSection.classList.remove('hidden');
            quickActionsList.innerHTML = actions.map(action => createQuickAction(action)).join('');
        } else {
            quickActionsSection.classList.add('hidden');
        }
    }

    // Item templates
    function createRouteItem(route) {
        const colorClass = getCategoryColor(route.category);
        return '<a href="' + escapeAttr(route.url) + '" class="search-result-item flex items-center gap-3 px-3 py-2.5 rounded-lg">' +
            '<div class="flex-shrink-0 w-8 h-8 ' + colorClass + ' rounded-lg flex items-center justify-center">' +
                '<i class="' + escapeAttr(route.icon) + ' text-white text-xs"></i>' +
            '</div>' +
            '<div class="flex-1 min-w-0">' +
                '<p class="text-sm font-medium text-gray-900 truncate">' + escapeHtml(route.title) + '</p>' +
                '<p class="text-xs text-gray-500 truncate">' + escapeHtml(route.description) + '</p>' +
            '</div>' +
            '<span class="flex-shrink-0 text-[10px] px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full font-medium">' + escapeHtml(route.category) + '</span>' +
        '</a>';
    }

    function createRecordItem(record) {
        const colorClass = getRecordColor(record.type);
        return '<a href="' + escapeAttr(record.url) + '" class="search-result-item flex items-center gap-3 px-3 py-2.5 rounded-lg">' +
            '<div class="flex-shrink-0 w-8 h-8 ' + colorClass + ' rounded-lg flex items-center justify-center">' +
                '<i class="' + escapeAttr(record.icon) + ' text-white text-xs"></i>' +
            '</div>' +
            '<div class="flex-1 min-w-0">' +
                '<p class="text-sm font-medium text-gray-900 truncate">' + escapeHtml(record.title) + '</p>' +
                '<p class="text-xs text-gray-500 truncate">' + escapeHtml(record.description || '') + '</p>' +
            '</div>' +
            '<span class="flex-shrink-0 text-[10px] px-2 py-0.5 bg-blue-50 text-blue-500 rounded-full font-medium">' + escapeHtml(record.category) + '</span>' +
        '</a>';
    }

    function createQuickAction(action) {
        var colors = {
            'blue': 'bg-blue-600 hover:bg-blue-700',
            'green': 'bg-green-600 hover:bg-green-700',
            'purple': 'bg-purple-600 hover:bg-purple-700',
            'orange': 'bg-orange-500 hover:bg-orange-600'
        };
        var colorClass = colors[action.color] || 'bg-gray-600 hover:bg-gray-700';
        return '<a href="' + escapeAttr(action.url) + '" class="search-result-item flex items-center gap-3 px-3 py-2.5 ' + colorClass + ' text-white rounded-lg transition-colors">' +
            '<i class="' + escapeAttr(action.icon) + ' text-sm"></i>' +
            '<span class="text-sm font-medium flex-1">' + escapeHtml(action.title) + '</span>' +
            '<svg class="w-4 h-4 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>' +
            '</svg>' +
        '</a>';
    }

    // Color helpers
    function getCategoryColor(category) {
        var colors = {
            'Accounting': 'bg-blue-500', 'CRM': 'bg-green-500', 'Inventory': 'bg-purple-500',
            'POS': 'bg-orange-500', 'Reports': 'bg-indigo-500', 'Settings': 'bg-gray-500',
            'Dashboard': 'bg-pink-500', 'Payroll': 'bg-teal-500', 'Admin': 'bg-red-500',
            'Banking': 'bg-cyan-500', 'Projects': 'bg-amber-500', 'E-Commerce': 'bg-rose-500',
            'Procurement': 'bg-lime-600', 'Statutory': 'bg-sky-500', 'Support': 'bg-violet-500'
        };
        return colors[category] || 'bg-gray-500';
    }

    function getRecordColor(type) {
        var colors = {
            'customer': 'bg-green-500', 'vendor': 'bg-amber-500', 'product': 'bg-purple-500',
            'voucher': 'bg-blue-500', 'ledger_account': 'bg-indigo-500',
            'employee': 'bg-teal-500', 'payroll_period': 'bg-cyan-500'
        };
        return colors[type] || 'bg-gray-500';
    }

    // Security: escape HTML and attributes
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function escapeAttr(text) {
        if (!text) return '';
        return text.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
});
</script>
