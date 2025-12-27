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
            <p class="hidden md:block text-sm text-gray-500 mt-1">@yield('page-description', 'Welcome back! Here\'s what\'s happening with your business today.')</p>
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <!-- Action Buttons -->
        @hasSection('action-buttons')
            @yield('action-buttons')
        @endif

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
                 x-cloak
                 style="display: none;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90 transform translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 transform translate-y-4"
                 @click.outside="closeCalculator()"
                 class="calculator-popup absolute right-0 mt-2 w-80 shadow-2xl rounded-2xl overflow-hidden z-50">

                <!-- Calculator Header -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <h3 class="font-semibold text-white text-sm">Smart Calculator</h3>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button @click="clearAll()" class="text-xs text-white/80 hover:text-white bg-white/20 hover:bg-white/30 px-2 py-1 rounded transition-all duration-200">AC</button>
                            <button @click="closeCalculator()" class="text-white/80 hover:text-white transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <!-- Display -->
                    <div class="mb-4">
                        <div class="calculator-display rounded-xl p-4 mb-2">
                            <input x-model="expression"
                                   @keyup.enter="calculate()"
                                   @keydown="handleKeyboard($event)"
                                   class="w-full bg-transparent text-green-400 font-mono text-sm placeholder-gray-500 focus:outline-none border-b border-gray-600 pb-1"
                                   placeholder="Enter calculation..."/>
                            <div class="text-right text-3xl font-bold text-white mt-3 min-h-[40px] transition-all duration-200 border border-gray-600 rounded-lg px-3 py-2 bg-black/20"
                                 :class="{'text-green-400 border-green-500': result && result !== 'Error', 'text-red-400 border-red-500': result === 'Error', 'border-gray-600': !result || result === '0'}"
                                 x-text="result || '0'"></div>
                        </div>
                        <div class="text-xs text-gray-500 text-center" x-show="lastCalculation" x-text="'Last: ' + lastCalculation"></div>
                    </div>

                    <!-- Calculator Buttons -->
                    <div class="grid grid-cols-4 gap-2 mb-4">
                        <!-- Row 1 -->
                        <button @click="clearAll()" class="calc-btn-special bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white shadow-lg">AC</button>
                        <button @click="deleteLast()" class="calc-btn-special bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white shadow-lg">⌫</button>
                        <button @click="addToExpression('%')" class="calc-btn-operator">%</button>
                        <button @click="addToExpression('/')" class="calc-btn-operator">÷</button>

                        <!-- Row 2 -->
                        <button @click="addToExpression('7')" class="calc-btn-number">7</button>
                        <button @click="addToExpression('8')" class="calc-btn-number">8</button>
                        <button @click="addToExpression('9')" class="calc-btn-number">9</button>
                        <button @click="addToExpression('*')" class="calc-btn-operator">×</button>

                        <!-- Row 3 -->
                        <button @click="addToExpression('4')" class="calc-btn-number">4</button>
                        <button @click="addToExpression('5')" class="calc-btn-number">5</button>
                        <button @click="addToExpression('6')" class="calc-btn-number">6</button>
                        <button @click="addToExpression('-')" class="calc-btn-operator">−</button>

                        <!-- Row 4 -->
                        <button @click="addToExpression('1')" class="calc-btn-number">1</button>
                        <button @click="addToExpression('2')" class="calc-btn-number">2</button>
                        <button @click="addToExpression('3')" class="calc-btn-number">3</button>
                        <button @click="addToExpression('+')" class="calc-btn-operator row-span-2">+</button>

                        <!-- Row 5 -->
                        <button @click="addToExpression('0')" class="calc-btn-number col-span-2">0</button>
                        <button @click="addToExpression('.')" class="calc-btn-number">.</button>
                        <button @click="calculate()" class="calc-btn-equals">=</button>
                    </div>

                    <!-- Quick Functions -->
                    <div class="border-t border-gray-200 pt-4 mt-4" >
                        <div class="text-xs font-medium text-gray-600 mb-3 text-center">Quick Actions</div>
                        <div class="grid grid-cols-2 gap-2 mb-2  ">
                            <button @click="addVat()" class="quick-btn quick-btn-vat-add " style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24 ">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                +VAT 7.5%
                            </button>
                            <button @click="removeVat()" class="quick-btn quick-btn-vat-remove" style="background: linear-gradient(135deg, #f56565 0%, #c53030 100%); color: white;">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/>
                                </svg>
                                -VAT 7.5%
                            </button>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <button @click="copyResult()" class="quick-btn quick-btn-copy" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white;">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Copy
                            </button>
                            <button @click="storeMemory()" class="quick-btn quick-btn-memory" style="background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%); color: white;">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                                M+
                            </button>
                            <button @click="recallMemory()" class="quick-btn quick-btn-memory" :disabled="!memoryValue" style="background: linear-gradient(135deg, #ed64a6 0%, #d53f8c 100%); color: white;">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                MR
                            </button>
                        </div>
                    </div>

                    <!-- History Panel -->
                    <div x-show="showHistory" x-transition class="mt-3 pt-3 border-t border-gray-200">
                        <div class="max-h-32 overflow-y-auto">
                            <template x-for="(item, index) in history" :key="index">
                                <div class="flex justify-between items-center py-1 px-2 hover:bg-gray-100 rounded cursor-pointer text-xs" @click="useFromHistory(item)">
                                    <span x-text="item.expression"></span>
                                    <span class="font-mono text-blue-600" x-text="item.result"></span>
                                </div>
                            </template>
                            <div x-show="history.length === 0" class="text-center text-gray-500 text-xs py-2">No history yet</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="relative" x-data="{ open: false, unreadCount: {{ auth()->user()->unreadNotifications->count() }}, notifications: [] }"
             x-init="
                // Fetch notifications on load
                fetch('{{ route('tenant.notifications.index', tenant()->slug) }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => notifications = data.notifications || [])
                .catch(err => console.error('Error loading notifications:', err));

                // Refresh count every 30 seconds
                setInterval(() => {
                    fetch('{{ route('tenant.notifications.unread-count', tenant()->slug) }}')
                        .then(res => res.json())
                        .then(data => unreadCount = data.count)
                        .catch(err => console.error('Error fetching count:', err));
                }, 30000);
             ">
            <button @click="open = !open" @click.away="open = false" class="relative p-2 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center text-xs text-white pulse-animation"></span>
            </button>

            <!-- Notifications Dropdown -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-1 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-1 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50"
                 style="display: none;">

                <!-- Header -->
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                    <button @click="fetch('{{ route('tenant.notifications.mark-all-read', tenant()->slug) }}', {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}}).then(() => {unreadCount = 0; notifications.forEach(n => n.read_at = new Date())})"
                            class="text-xs text-blue-600 hover:text-blue-800">Mark all read</button>
                </div>

                <!-- Notifications List -->
                <div class="max-h-96 overflow-y-auto">
                    <template x-if="notifications.length === 0">
                        <div class="px-4 py-8 text-center text-gray-500 text-sm">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            No notifications yet
                        </div>
                    </template>

                    <template x-for="notification in notifications" :key="notification.id">
                        <div @click="if(!notification.read_at) { fetch(`{{ url(tenant()->slug . '/notifications') }}/${notification.id}/mark-read`, {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}}).then(() => {notification.read_at = new Date(); unreadCount = Math.max(0, unreadCount - 1)}); } if(notification.data.ticket_id) window.location.href = '{{ url(tenant()->slug . '/support/tickets') }}/' + notification.data.ticket_id; else if(notification.data.action_url) window.location.href = notification.data.action_url;"
                             :class="notification.read_at ? 'bg-white' : 'bg-blue-50'"
                             class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <template x-if="notification.data.ticket_number">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                        </template>
                                        <template x-if="!notification.data.ticket_number">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </template>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900" x-text="notification.data.ticket_number ? 'Ticket #' + notification.data.ticket_number : (notification.data.title || 'Notification')"></p>
                                    <p class="text-xs text-gray-600 mt-1" x-text="notification.data.subject || notification.data.message || ''"></p>
                                    <p class="text-xs text-gray-400 mt-1" x-text="new Date(notification.created_at).toLocaleString()"></p>
                                </div>
                                <template x-if="!notification.read_at">
                                    <div class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full"></div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="px-4 py-3 border-t border-gray-200 text-center">
                    <a href="{{ route('tenant.notifications.index', tenant()->slug) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View all notifications</a>
                </div>
            </div>
        </div>

        <!-- User Menu -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false" data-user-menu-button class="flex items-center space-x-3 p-2 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                         alt="{{ auth()->user()->name }}"
                         class="w-8 h-8 rounded-full object-cover border-2 border-purple-500">
                @else
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                @endif
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
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                 alt="{{ auth()->user()->name }}"
                                 class="w-10 h-10 rounded-full object-cover border-2 border-purple-500">
                        @else
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'User' }}</div>
                            <div class="text-xs text-gray-500">{{ auth()->user()->email ?? 'user@example.com' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="py-1">
                    <a href="{{ route('tenant.profile.index', ['tenant' => tenant()->slug]) }}"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        My Profile
                    </a>

                    <a href="{{ route('tenant.settings.company', ['tenant' => tenant()->slug]) }}"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Company Settings
                    </a>                    <div class="border-t border-gray-100 my-1"></div>

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
.calc-btn-number {
    @apply py-3 px-2 rounded-xl font-bold text-lg bg-gradient-to-br from-white to-gray-50 hover:from-gray-50 hover:to-gray-100 border-2 border-gray-300 hover:border-blue-400 text-gray-800 transition-all duration-200 active:scale-95 shadow-lg hover:shadow-xl;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.calc-btn-number:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.calc-btn-number:active {
    transform: translateY(0) scale(0.95);
    box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1), inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.calc-btn-operator {
    @apply py-3 px-2 rounded-xl font-bold text-lg bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 hover:from-blue-600 hover:via-blue-700 hover:to-indigo-700 text-white border-2 border-blue-400 hover:border-blue-300 transition-all duration-200 active:scale-95 shadow-lg;
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3), 0 2px 4px -1px rgba(59, 130, 246, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.calc-btn-operator:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 15px -3px rgba(59, 130, 246, 0.4), 0 4px 6px -2px rgba(59, 130, 246, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.calc-btn-operator:active {
    transform: translateY(0) scale(0.95);
    box-shadow: 0 2px 4px -1px rgba(59, 130, 246, 0.2), inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.calc-btn-special {
    @apply py-3 px-2 rounded-xl font-bold text-sm border-2 transition-all duration-200 active:scale-95 shadow-lg;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.calc-btn-special:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.calc-btn-special:active {
    transform: translateY(0) scale(0.95);
    box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1), inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.calc-btn-equals {
    @apply py-3 px-2 rounded-xl font-bold text-lg bg-gradient-to-br from-emerald-500 via-green-500 to-teal-500 hover:from-emerald-600 hover:via-green-600 hover:to-teal-600 text-white border-2 border-emerald-400 hover:border-emerald-300 transition-all duration-200 active:scale-95 shadow-lg;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3), 0 2px 4px -1px rgba(16, 185, 129, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.calc-btn-equals:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 15px -3px rgba(16, 185, 129, 0.4), 0 4px 6px -2px rgba(16, 185, 129, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.calc-btn-equals:active {
    transform: translateY(0) scale(0.95);
    box-shadow: 0 2px 4px -1px rgba(16, 185, 129, 0.2), inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Calculator Display Enhancement */
.calculator-display {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    border: 3px solid #374151;
    box-shadow:
        inset 0 2px 4px rgba(0, 0, 0, 0.3),
        inset 0 -2px 4px rgba(255, 255, 255, 0.05),
        0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Quick Function Buttons */
.quick-btn {
    @apply px-3 py-2 text-xs font-semibold rounded-lg border-2 transition-all duration-200 active:scale-95 shadow-lg hover:shadow-xl flex items-center justify-center bg-gray-100 text-gray-700 border-gray-300;
}

.quick-btn-vat-add {
    @apply bg-gradient-to-r from-emerald-400 to-green-400 text-white border-emerald-500 hover:from-emerald-500 hover:to-green-500 hover:border-emerald-600 shadow-emerald-200 !important;
}

.quick-btn-vat-remove {
    @apply bg-gradient-to-r from-orange-400 to-red-400 text-white border-orange-500 hover:from-orange-500 hover:to-red-500 hover:border-orange-600 shadow-orange-200 !important;
}

.quick-btn-copy {
    @apply bg-gradient-to-r from-blue-400 to-indigo-400 text-white border-blue-500 hover:from-blue-500 hover:to-indigo-500 hover:border-blue-600 shadow-blue-200 !important;
}

.quick-btn-memory {
    @apply bg-gradient-to-r from-purple-400 to-violet-400 text-white border-purple-500 hover:from-purple-500 hover:to-violet-500 hover:border-purple-600 shadow-purple-200 !important;
}

.quick-btn:disabled {
    @apply opacity-50 cursor-not-allowed;
}

.quick-btn:disabled:hover {
    @apply opacity-50;
}

/* Calculator popup enhancement */
.calculator-popup {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 2px solid #e2e8f0;
    box-shadow:
        0 25px 50px -12px rgba(0, 0, 0, 0.25),
        0 10px 20px -5px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.6);
}

.calc-btn-number:active,
.calc-btn-operator:active,
.calc-btn-special:active,
.calc-btn-equals:active,
.quick-btn:active {
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
        lastCalculation: '',
        memoryValue: null,
        history: [],
        showHistory: false,

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
                this.lastCalculation = `${this.expression} = ${this.result}`;
            } catch (error) {
                this.result = 'Error';
                console.error('Calculation error:', error);
            }
        },

        handleKeyboard(event) {
            const key = event.key;
            if (/[0-9+\-*/.()%]/.test(key)) {
                event.preventDefault();
                this.addToExpression(key === '*' ? '×' : key === '/' ? '÷' : key === '-' ? '−' : key);
            } else if (key === 'Backspace') {
                event.preventDefault();
                this.deleteLast();
            } else if (key === 'Escape') {
                event.preventDefault();
                this.clearAll();
            }
        },

        addToHistory() {
            if (this.result && this.result !== 'Error' && this.expression) {
                this.history.unshift({
                    expression: this.expression,
                    result: this.result,
                    timestamp: new Date().toLocaleTimeString()
                });
                if (this.history.length > 10) this.history.pop();
            }
        },

        toggleHistory() {
            this.showHistory = !this.showHistory;
        },

        useFromHistory(item) {
            this.expression = item.expression;
            this.result = item.result;
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
        },

        storeMemory() {
            if (this.result && this.result !== 'Error') {
                this.memoryValue = parseFloat(this.result);
                // Show brief feedback
                const originalText = this.result;
                this.result = 'Stored!';
                setTimeout(() => {
                    this.result = originalText;
                }, 800);
            }
        },

        recallMemory() {
            if (this.memoryValue !== null) {
                this.expression = this.memoryValue.toString();
                this.result = this.memoryValue.toString();
            }
        },

        clearMemory() {
            this.memoryValue = null;
        },

        handleKeyboard(event) {
            // Handle keyboard shortcuts
            if (event.key === 'Escape') {
                this.closeCalculator();
            } else if (event.key === 'c' && event.ctrlKey) {
                event.preventDefault();
                this.copyResult();
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

    function setActiveHeaderItem(items, index) {
        items.forEach(item => item.classList.remove('active'));
        if (items[index]) {
            items[index].classList.add('active');
            items[index].scrollIntoView({ block: 'nearest' });
        }
    }
});
</script>
