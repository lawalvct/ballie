  <header class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-10" style="border-image: linear-gradient(90deg, var(--color-gold), var(--color-blue)) 1;">
                <div class="px-4 md:px-8 py-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <!-- Mobile menu button -->
                            <button class="md:hidden mr-4 p-3 rounded-lg text-gray-500 hover:bg-gray-100 active:bg-gray-200 transition-colors touch-manipulation"
                                    onclick="toggleMobileMenu()"
                                    aria-label="Toggle mobile menu">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>

                            <div>
                                <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-gray-800 via-blue-600 to-purple-600 bg-clip-text text-transparent">
                                    @yield('page-title', 'Dashboard')
                                </h1>
                                <p class="text-xs md:text-sm text-gray-500 mt-1 hidden sm:block">
                                    @yield('page-description', 'Welcome back, manage your system with ease')
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 md:space-x-6">
                            <!-- Search Bar -->
                            <div class="relative hidden lg:block">
                                <input type="text"
                                       placeholder="Search..."
                                       class="w-48 xl:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm">
                                <svg class="w-4 h-4 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>

                            <!-- Mobile search button -->
                            <button class="lg:hidden p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>

                            <!-- Notifications -->
                            <div class="relative" x-data="{ open: false, unreadCount: 0, notifications: [] }" @click.away="open = false" x-init="
                                // Fetch unread count on load
                                fetch('{{ route('super-admin.notifications.unread-count') }}')
                                    .then(res => res.json())
                                    .then(data => unreadCount = data.count)
                                    .catch(err => console.error('Error fetching notifications:', err));

                                // Refresh count every 30 seconds
                                setInterval(() => {
                                    fetch('{{ route('super-admin.notifications.unread-count') }}')
                                        .then(res => res.json())
                                        .then(data => unreadCount = data.count)
                                        .catch(err => console.error('Error fetching notifications:', err));
                                }, 30000);
                            ">
                                <button @click="open = !open; if(open && notifications.length === 0) {
                                    fetch('{{ route('super-admin.notifications.index') }}', {
                                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                    })
                                    .then(res => res.json())
                                    .then(data => notifications = data.notifications)
                                    .catch(err => console.error('Error loading notifications:', err));
                                }" class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-all duration-200">
                                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute -top-1 -right-1 flex items-center justify-center min-w-[20px] h-5 px-1 text-xs font-bold text-white bg-red-500 rounded-full"></span>
                                </button>

                                <!-- Dropdown -->
                                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-80 md:w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-hidden">
                                    <div class="px-4 py-3 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                                            <button @click="fetch('{{ route('super-admin.notifications.mark-all-read') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => { unreadCount = 0; notifications.forEach(n => n.read_at = new Date()); })" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Mark all read</button>
                                        </div>
                                    </div>
                                    <div class="overflow-y-auto max-h-80">
                                        <template x-if="notifications.length === 0">
                                            <div class="px-4 py-8 text-center text-gray-500">
                                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                </svg>
                                                <p class="text-sm">No notifications</p>
                                            </div>
                                        </template>
                                        <template x-for="notification in notifications" :key="notification.id">
                                            <a :href="notification.data.url || '#'" @click="if(!notification.read_at) { fetch('{{ url('super-admin/notifications') }}/' + notification.id + '/mark-read', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => { notification.read_at = new Date(); unreadCount = Math.max(0, unreadCount - 1); }); }" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors" :class="{ 'bg-blue-50': !notification.read_at }">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0 mt-1">
                                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full" :class="notification.read_at ? 'bg-gray-200' : 'bg-blue-500'">
                                                            <svg class="w-4 h-4" :class="notification.read_at ? 'text-gray-600' : 'text-white'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="ml-3 flex-1">
                                                        <p class="text-sm font-medium text-gray-900" x-text="notification.data.ticket_number || 'Notification'"></p>
                                                        <p class="text-xs text-gray-600 mt-1" x-text="notification.data.subject || notification.data.message || 'New notification'"></p>
                                                        <p class="text-xs text-gray-500 mt-1" x-text="new Date(notification.created_at).toLocaleString()"></p>
                                                    </div>
                                                </div>
                                            </a>
                                        </template>
                                    </div>
                                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                                        <a href="{{ route('super-admin.notifications.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center">
                                            View all notifications
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- User Profile Menu -->
                            <div class="flex items-center space-x-3 bg-gray-50 rounded-lg px-2 md:px-3 py-2">
                                <div class="text-right hidden sm:block">
                                    <p class="text-sm font-semibold text-gray-900 truncate max-w-24 md:max-w-none">{{ auth('super_admin')->user()->name }}</p>
                                    <p class="text-xs text-gray-500 hidden md:block">Super Administrator</p>
                                </div>
                                <img class="w-8 h-8 md:w-10 md:h-10 rounded-full border-2 shadow-md flex-shrink-0"
                                     style="border-color: var(--color-gold);"
                                     src="{{ auth('super_admin')->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth('super_admin')->user()->name).'&color=ffffff&background=d1b05e' }}"
                                     alt="Profile">
                            </div>
                        </div>
                    </div>
                </div>
            </header>
