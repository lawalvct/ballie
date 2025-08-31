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
                            <div class="relative">
                                <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-all duration-200">
                                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V9a6 6 0 10-12 0v3l-5 5h5a3 3 0 006 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d=" 17v2a2 2 0 01-4 0v-2"></path>
                                    </svg>
                                    <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">3</span>
                                </button>
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
