<header class="glass-effect shadow-sm border-b border-gray-200 h-20 flex items-center justify-between px-6 sticky top-0 z-20">
    <div class="flex items-center space-x-4">
        <button id="mobileSidebarToggle" class="p-2 rounded-lg hover:bg-gray-100 lg:hidden transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                @yield('page-title', 'Dashboard')
            </h1>
            <p class="text-sm text-gray-500 mt-1">@yield('page-description', 'Welcome back! Here\'s what\'s happening with your business today.')</p>
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <!-- Search -->
        <div class="relative hidden md:block search-container">
            <input type="text"
                   id="header-ledger-search"
                   placeholder="Search for Ledger..."
                   class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                   autocomplete="off">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <!-- Search Results Dropdown -->
            <div id="header-search-results" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-64 overflow-y-auto z-50 hidden fade-in">
                <!-- Results will be populated here -->
            </div>
        </div>

        <!-- Calculator Widget -->
        <div class="relative" x-data="calculatorWidget()">
            <button @click="toggleCalculator()"
                    class="p-2 rounded-xl hover:bg-gray-100 transition-colors duration-200 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span x-show="isOpen" class="absolute -top-1 -right-1 h-3 w-3 bg-blue-500 rounded-full"></span>
            </button>

            <!-- Calculator Popup -->
            <div x-show="isOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 transform translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 transform translate-y-2"
                 @click.outside="closeCalculator()"
                 class="absolute right-0 mt-2 w-72 bg-white shadow-2xl rounded-xl border border-gray-200 p-4 z-50">

                <!-- Calculator Header -->
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-800 text-sm">Calculator</h3>
                    <div class="flex items-center space-x-2">
                        <button @click="clearAll()" class="text-xs text-red-500 hover:text-red-700 transition-colors">Clear</button>
                        <button @click="closeCalculator()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Display -->
                <div class="mb-3">
                    <input x-model="expression"
                           @keyup.enter="calculate()"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-right font-mono text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                           placeholder="Enter calculation..."/>
                    <div class="text-right text-xl font-bold text-gray-800 mt-2 min-h-[28px]" x-text="result || '0'"></div>
                </div>

                <!-- Calculator Buttons -->
                <div class="grid grid-cols-4 gap-2">
                    <!-- Row 1 -->
                    <button @click="clearAll()" class="calc-btn bg-red-500 hover:bg-red-600 text-white text-xs">C</button>
                    <button @click="deleteLast()" class="calc-btn bg-orange-500 hover:bg-orange-600 text-white text-xs">⌫</button>
                    <button @click="addToExpression('%')" class="calc-btn bg-gray-500 hover:bg-gray-600 text-white text-xs">%</button>
                    <button @click="addToExpression('/')" class="calc-btn bg-blue-500 hover:bg-blue-600 text-white text-xs">÷</button>

                    <!-- Row 2 -->
                    <button @click="addToExpression('7')" class="calc-btn">7</button>
                    <button @click="addToExpression('8')" class="calc-btn">8</button>
                    <button @click="addToExpression('9')" class="calc-btn">9</button>
                    <button @click="addToExpression('*')" class="calc-btn bg-blue-500 hover:bg-blue-600 text-white text-xs">×</button>

                    <!-- Row 3 -->
                    <button @click="addToExpression('4')" class="calc-btn">4</button>
                    <button @click="addToExpression('5')" class="calc-btn">5</button>
                    <button @click="addToExpression('6')" class="calc-btn">6</button>
                    <button @click="addToExpression('-')" class="calc-btn bg-blue-500 hover:bg-blue-600 text-white text-xs">−</button>

                    <!-- Row 4 -->
                    <button @click="addToExpression('1')" class="calc-btn">1</button>
                    <button @click="addToExpression('2')" class="calc-btn">2</button>
                    <button @click="addToExpression('3')" class="calc-btn">3</button>
                    <button @click="addToExpression('+')" class="calc-btn bg-blue-500 hover:bg-blue-600 text-white text-xs row-span-2">+</button>

                    <!-- Row 5 -->
                    <button @click="addToExpression('0')" class="calc-btn col-span-2">0</button>
                    <button @click="addToExpression('.')" class="calc-btn">.</button>
                    <button @click="calculate()" class="calc-btn bg-green-500 hover:bg-green-600 text-white text-xs">=</button>
                </div>

                <!-- Quick Functions -->
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex flex-wrap gap-1">
                        <button @click="addVat()" class="px-2 py-1 text-xs bg-emerald-100 text-emerald-700 rounded hover:bg-emerald-200 transition-colors">+VAT 7.5%</button>
                        <button @click="removeVat()" class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded hover:bg-orange-200 transition-colors">-VAT 7.5%</button>
                        <button @click="copyResult()" class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors">Copy</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <button class="relative p-2 rounded-xl hover:bg-gray-100 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center text-xs text-white pulse-animation">3</span>
        </button>

        <!-- User Menu -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false" data-user-menu-button class="flex items-center space-x-3 p-2 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="hidden md:block text-left">
                    <div class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'User' }}</div>
                    <div class="text-xs text-gray-500">{{ auth()->user()->role ?? 'Admin' }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-1 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-1 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 data-user-menu-dropdown
                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50"
                 style="display: none;">

                <!-- User Info Header -->
                <div class="px-4 py-3 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'User' }}</div>
                            <div class="text-xs text-gray-500">{{ auth()->user()->email ?? 'user@example.com' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="py-1">
                    <a href="{{ route('tenant.settings.index', ['tenant' => tenant()->slug]) }}"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profile Settings
                    </a>

                    <a href="{{ route('tenant.settings.index', ['tenant' => tenant()->slug]) }}"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Account Settings
                    </a>

                    <div class="border-t border-gray-100 my-1"></div>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150">
                            <svg class="w-4 h-4 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
/* Header Search Styles */
.search-result-item.active {
    background-color: #f3f4f6;
}

.search-result-item:hover {
    background-color: #f9fafb;
}

#header-search-results {
    max-height: 400px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

#header-search-results::-webkit-scrollbar {
    width: 6px;
}

#header-search-results::-webkit-scrollbar-track {
    background: #f7fafc;
}

#header-search-results::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}

#header-search-results::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

.search-container {
    position: relative;
}

#header-ledger-search:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.fade-in {
    animation: fadeIn 0.2s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Calculator Styles */
.calc-btn {
    @apply py-2 px-1 rounded-lg font-medium text-sm bg-gray-100 hover:bg-gray-200 transition-colors duration-150 active:scale-95;
}

.calc-btn:active {
    transform: scale(0.95);
}
</style>

<script>
// Calculator Widget Component - Must be global for Alpine.js
function calculatorWidget() {
    return {
        isOpen: false,
        expression: '',
        result: '',
        lastResult: '',

        toggleCalculator() {
            this.isOpen = !this.isOpen;
        },

        closeCalculator() {
            this.isOpen = false;
        },

        addToExpression(value) {
            if (this.result && !this.expression) {
                // If we just calculated and user enters an operator, use the result
                if (['+', '-', '*', '/', '%'].includes(value)) {
                    this.expression = this.result + value;
                    this.result = '';
                    return;
                }
                // If user enters a number after calculation, start fresh
                if (!isNaN(value)) {
                    this.expression = value;
                    this.result = '';
                    return;
                }
            }
            this.expression += value;
        },

        deleteLast() {
            this.expression = this.expression.slice(0, -1);
            if (!this.expression) {
                this.result = '';
            }
        },

        clearAll() {
            this.expression = '';
            this.result = '';
        },

        calculate() {
            if (!this.expression) return;

            try {
                // Replace display operators with JS operators
                let expr = this.expression
                    .replace(/×/g, '*')
                    .replace(/÷/g, '/')
                    .replace(/−/g, '-');

                // Handle percentage calculations
                expr = expr.replace(/(\d+(?:\.\d+)?)%/g, '($1/100)');
                
                // Security: only allow numbers, operators, parentheses, and decimal points
                if (!/^[0-9+\-*\/().%\s]+$/.test(expr)) {
                    throw new Error('Invalid characters in expression');
                }

                // Use Function constructor for safe evaluation (better than eval)
                const result = new Function('return ' + expr)();
                
                if (!isFinite(result)) {
                    throw new Error('Invalid calculation');
                }

                this.result = this.formatNumber(result);
                this.lastResult = result;
            } catch (error) {
                this.result = 'Error';
                console.error('Calculation error:', error);
            }
        },

        formatNumber(num) {
            // Format numbers with appropriate decimal places
            if (num % 1 === 0) {
                return num.toString();
            } else {
                return parseFloat(num.toFixed(8)).toString();
            }
        },

        addVat() {
            if (this.result) {
                const currentValue = parseFloat(this.result);
                const withVat = currentValue * 1.075;
                this.result = this.formatNumber(withVat);
                this.expression = `${currentValue} * 1.075`;
            } else if (this.expression) {
                this.expression = `(${this.expression}) * 1.075`;
                this.calculate();
            }
        },

        removeVat() {
            if (this.result) {
                const currentValue = parseFloat(this.result);
                const withoutVat = currentValue / 1.075;
                this.result = this.formatNumber(withoutVat);
                this.expression = `${currentValue} / 1.075`;
            } else if (this.expression) {
                this.expression = `(${this.expression}) / 1.075`;
                this.calculate();
            }
        },

        async copyResult() {
            if (this.result && this.result !== 'Error') {
                try {
                    await navigator.clipboard.writeText(this.result);
                    // Show brief success feedback
                    const originalText = this.result;
                    this.result = 'Copied!';
                    setTimeout(() => {
                        this.result = originalText;
                    }, 1000);
                } catch (err) {
                    console.error('Failed to copy:', err);
                }
            }
        }
    }
}

// Header Ledger Search Autocomplete
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('header-ledger-search');
    const searchResults = document.getElementById('header-search-results');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Hide results if query is too short
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            // Debounce search requests
            searchTimeout = setTimeout(() => {
                performHeaderSearch(query);
            }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(event) {
            if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                searchResults.classList.add('hidden');
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(event) {
            const items = searchResults.querySelectorAll('.search-result-item');
            const currentActive = searchResults.querySelector('.search-result-item.active');
            let currentIndex = -1;

            if (currentActive) {
                currentIndex = Array.from(items).indexOf(currentActive);
            }

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                const nextIndex = Math.min(currentIndex + 1, items.length - 1);
                setActiveHeaderItem(items, nextIndex);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                const prevIndex = Math.max(currentIndex - 1, 0);
                setActiveHeaderItem(items, prevIndex);
            } else if (event.key === 'Enter') {
                event.preventDefault();
                if (currentActive) {
                    currentActive.click();
                }
            } else if (event.key === 'Escape') {
                searchResults.classList.add('hidden');
                searchInput.blur();
            }
        });
    }

    // Fallback dropdown functionality if Alpine.js fails
    const userMenuButton = document.querySelector('[data-user-menu-button]');
    const userMenuDropdown = document.querySelector('[data-user-menu-dropdown]');

    if (userMenuButton && userMenuDropdown && !window.Alpine) {
        let isDropdownOpen = false;

        userMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            isDropdownOpen = !isDropdownOpen;

            if (isDropdownOpen) {
                userMenuDropdown.style.display = 'block';
                userMenuDropdown.style.opacity = '0';
                userMenuDropdown.style.transform = 'scale(0.95)';

                requestAnimationFrame(() => {
                    userMenuDropdown.style.transition = 'opacity 200ms ease-out, transform 200ms ease-out';
                    userMenuDropdown.style.opacity = '1';
                    userMenuDropdown.style.transform = 'scale(1)';
                });

                // Rotate chevron
                const chevron = userMenuButton.querySelector('svg:last-child');
                if (chevron) {
                    chevron.style.transform = 'rotate(180deg)';
                }
            } else {
                userMenuDropdown.style.opacity = '0';
                userMenuDropdown.style.transform = 'scale(0.95)';

                setTimeout(() => {
                    userMenuDropdown.style.display = 'none';
                }, 200);

                // Reset chevron
                const chevron = userMenuButton.querySelector('svg:last-child');
                if (chevron) {
                    chevron.style.transform = 'rotate(0deg)';
                }
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenuButton.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                if (isDropdownOpen) {
                    isDropdownOpen = false;
                    userMenuDropdown.style.opacity = '0';
                    userMenuDropdown.style.transform = 'scale(0.95)';

                    setTimeout(() => {
                        userMenuDropdown.style.display = 'none';
                    }, 200);

                    // Reset chevron
                    const chevron = userMenuButton.querySelector('svg:last-child');
                    if (chevron) {
                        chevron.style.transform = 'rotate(0deg)';
                    }
                }
            }
        });
    }

    function performHeaderSearch(query) {
        // Show loading state
        searchResults.innerHTML = '<div class="p-4 text-center text-gray-500">Searching...</div>';
        searchResults.classList.remove('hidden');

        // Get current tenant from URL or use a global variable
        const pathParts = window.location.pathname.split('/');
        const tenant = pathParts[1]; // Assuming tenant is the first part of the path

        const searchUrl = `/${tenant}/accounting/ledger-accounts/search?q=${encodeURIComponent(query)}`;

        // Make API request - using the correct route
        fetch(searchUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                displayHeaderResults(data);
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResults.innerHTML = '<div class="p-4 text-center text-red-500">Search failed. Please try again.</div>';
            });
    }    function displayHeaderResults(accounts) {
        console.log('Search results:', accounts); // Debug log

        if (!Array.isArray(accounts) || accounts.length === 0) {
            searchResults.innerHTML = '<div class="p-4 text-center text-gray-500">No accounts found</div>';
            return;
        }

        const resultsHtml = accounts.map(account => {
            const balanceClass = account.current_balance >= 0 ? 'text-green-600' : 'text-red-600';
            const balanceType = account.current_balance >= 0 ? 'Dr' : 'Cr';

            return `
                <div class="search-result-item p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" data-url="${account.url}">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-medium text-gray-900">${account.name}</span>

                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                             ${account.account_group}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium ${balanceClass}">
                                ₦${new Intl.NumberFormat().format(Math.abs(account.current_balance))} ${balanceType}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        searchResults.innerHTML = resultsHtml;

        // Add click handlers
        searchResults.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', function() {
                const url = this.dataset.url;
                window.location.href = url;
            });
        });
    }

    function setActiveHeaderItem(items, index) {/* Lines 470-475 omitted */}
});

    function setActiveHeaderItem(items, index) {
        items.forEach(item => item.classList.remove('active'));
        if (items[index]) {
            items[index].classList.add('active');
            items[index].scrollIntoView({ block: 'nearest' });
        }
    }
});
</script>