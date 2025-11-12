<header class="bg-white dark:bg-gray-800/50 backdrop-blur-sm border-b border-gray-200/80 dark:border-gray-700/50 px-4 md:px-6 py-3 md:py-4 flex items-center justify-between sticky top-0 z-20 transition-colors duration-300">
    <div class="flex items-center gap-3 md:gap-6">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="w-9 h-9 md:w-10 md:h-10 bg-gradient-to-r from-[var(--color-dark-purple)] to-[var(--color-dark-purple-2)] rounded-lg flex items-center justify-center shadow-lg">
                <i class="fas fa-cash-register text-white text-base md:text-lg"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    {{-- <span class="text-xs px-2 py-0.5 bg-green-500 text-white rounded-full font-medium">Enhanced</span> --}}
                </div>
                @if(isset($activeSession))
                    <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400">{{ $activeSession->cashRegister->name }}</p>
                @endif
            </div>
        </div>
        @if(isset($activeSession))
            <div class="hidden md:flex items-center gap-3 text-sm">
                <div class="bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200 px-3 py-1 rounded-full flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Session Active
                </div>
                <span class="text-gray-600 dark:text-gray-400">
                    Started: {{ $activeSession->opened_at->format('H:i') }}
                </span>
            </div>
        @endif
    </div>

    <div class="flex items-center gap-2 md:gap-3">
        <div class="hidden sm:flex items-center gap-2">
            <button @click="toggleViewMode()" class="px-3 py-2 rounded-lg text-sm flex items-center gap-2 bg-gray-100/50 hover:bg-gray-200/50 dark:bg-gray-700/50 dark:hover:bg-gray-600/50 text-gray-700 dark:text-gray-300 transition-colors duration-200">
                <i class="fas" :class="(viewMode || 'grid') === 'grid' ? 'fa-th-list' : 'fa-th'"></i>
                <span class="hidden lg:inline" x-text="(viewMode || 'grid') === 'grid' ? 'List View' : 'Grid View'"></span>
            </button>

            <button @click="toggleDarkMode()" class="px-3 py-2 rounded-lg text-sm flex items-center gap-2 bg-gray-100/50 hover:bg-gray-200/50 dark:bg-gray-700/50 dark:hover:bg-gray-600/50 text-gray-700 dark:text-gray-300 transition-colors duration-200">
                <i class="fas" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
                <span class="hidden lg:inline" x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
                <span class="shortcut-label hidden md:inline">Ctrl+D</span>
            </button>

            <button @click="toggleTouchMode()" :class="touchMode ? 'bg-primary text-white' : 'bg-gray-100/50 hover:bg-gray-200/50 dark:bg-gray-700/50 dark:hover:bg-gray-600/50 text-gray-700 dark:text-gray-300'" class="px-3 py-2 rounded-lg text-sm flex items-center gap-2 transition-colors duration-200">
                <i class="fas fa-hand-pointer"></i>
                <span class="hidden lg:inline">Touch Mode</span>
            </button>

            <button @click="toggleFullscreen()" class="px-3 py-2 rounded-lg text-sm flex items-center gap-2 bg-gray-100/50 hover:bg-gray-200/50 dark:bg-gray-700/50 dark:hover:bg-gray-600/50 text-gray-700 dark:text-gray-300 transition-colors duration-200">
                <i class="fas" :class="isFullscreen ? 'fa-compress' : 'fa-expand'"></i>
                <span class="hidden lg:inline" x-text="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'"></span>
                <span class="shortcut-label hidden md:inline">Ctrl+F</span>
            </button>

            @if(isset($recentSales) && $recentSales->count() > 0)
                <button @click="showRecentSales = !showRecentSales" :class="showRecentSales ? 'bg-primary text-white' : 'bg-gray-100/50 hover:bg-gray-200/50 dark:bg-gray-700/50 dark:hover:bg-gray-600/50 text-gray-700 dark:text-gray-300'" class="px-3 py-2 rounded-lg text-sm flex items-center gap-2 transition-colors duration-200">
                    <i class="fas fa-history"></i>
                    <span class="hidden lg:inline">Recent Sales</span>
                    @if($recentSales->count() > 0)
                        <span class="bg-red-500 text-white text-xs rounded-full px-2 py-0.5 min-w-[1.25rem] flex items-center justify-center">{{ $recentSales->count() }}</span>
                    @endif
                </button>
            @endif

            <button @click="toggleKeyboardShortcuts()" class="px-3 py-2 rounded-lg text-sm flex items-center gap-2 bg-gray-100/50 hover:bg-gray-200/50 dark:bg-gray-700/50 dark:hover:bg-gray-600/50 text-gray-700 dark:text-gray-300 transition-colors duration-200">
                <i class="fas fa-keyboard"></i>
                <span class="hidden lg:inline">Shortcuts</span>
                <span class="shortcut-label hidden md:inline">Ctrl+K</span>
            </button>

            <button @click="openCustomerDisplay()" class="px-3 py-2 rounded-lg text-sm flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-200 shadow-md">
                <i class="fas fa-tv"></i>
                <span class="hidden lg:inline">Customer Display</span>
            </button>
        </div>

        <div class="flex sm:hidden">
            <button @click="showMenuDropdown = !showMenuDropdown" class="px-3 py-2 rounded-lg text-sm flex items-center gap-2 bg-gray-100/50 hover:bg-gray-200/50 dark:bg-gray-700/50 dark:hover:bg-gray-600/50 text-gray-700 dark:text-gray-300">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>

        @if(isset($activeSession))
            <a href="{{ route('tenant.pos.close-session', ['tenant' => $tenant->slug]) }}"
               class="px-3 md:px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 btn-primary">
                <i class="fas fa-sign-out-alt"></i>
                <span class="hidden md:inline">Close Session</span>
            </a>
        @else
            <a href="{{ route('tenant.pos.register-session', ['tenant' => $tenant->slug]) }}"
               class="px-3 md:px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2 btn-primary">
                <i class="fas fa-cash-register"></i>
                <span class="hidden md:inline">Open Session</span>
            </a>
        @endif
    </div>
</header>

<!-- Mobile Menu Dropdown -->
<div x-show="showMenuDropdown" @click.away="showMenuDropdown = false" class="fixed top-16 right-4 z-50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-xl rounded-lg py-2 w-48 border border-gray-200 dark:border-gray-700 animate-fade-in" style="display: none;">
    @if(isset($recentSales) && $recentSales->count() > 0)
        <button @click="showRecentSales = !showRecentSales; showMenuDropdown = false" class="flex items-center justify-between px-4 py-2 w-full text-left hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
            <div class="flex items-center gap-2">
                <i class="fas fa-fw fa-history"></i>
                <span>Recent Sales</span>
            </div>
            @if($recentSales->count() > 0)
                <span class="bg-red-500 text-white text-xs rounded-full px-2 py-0.5 min-w-[1.25rem] flex items-center justify-center">{{ $recentSales->count() }}</span>
            @endif
        </button>
        <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
    @endif
    <button @click="toggleViewMode(); showMenuDropdown = false" class="flex items-center gap-2 px-4 py-2 w-full text-left hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
        <i class="fas fa-fw" :class="(viewMode || 'grid') === 'grid' ? 'fa-th-list' : 'fa-th'"></i>
        <span x-text="(viewMode || 'grid') === 'grid' ? 'List View' : 'Grid View'"></span>
    </button>
    <button @click="toggleDarkMode(); showMenuDropdown = false" class="flex items-center gap-2 px-4 py-2 w-full text-left hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
        <i class="fas fa-fw" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
        <span x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
    </button>
    <button @click="toggleTouchMode(); showMenuDropdown = false" class="flex items-center gap-2 px-4 py-2 w-full text-left hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
        <i class="fas fa-fw fa-hand-pointer"></i>
        <span>Touch Mode</span>
    </button>
    <button @click="toggleFullscreen(); showMenuDropdown = false" class="flex items-center gap-2 px-4 py-2 w-full text-left hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
        <i class="fas fa-fw" :class="isFullscreen ? 'fa-compress' : 'fa-expand'"></i>
        <span x-text="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'"></span>
    </button>
    <button @click="toggleKeyboardShortcuts(); showMenuDropdown = false" class="flex items-center gap-2 px-4 py-2 w-full text-left hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
        <i class="fas fa-fw fa-keyboard"></i>
        <span>Keyboard Shortcuts</span>
    </button>
    <button @click="openCustomerDisplay(); showMenuDropdown = false" class="flex items-center gap-2 px-4 py-2 w-full text-left hover:bg-gray-100 dark:hover:bg-gray-700 text-blue-600 dark:text-blue-400">
        <i class="fas fa-fw fa-tv"></i>
        <span>Customer Display</span>
    </button>
</div>
