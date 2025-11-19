@php
    $navLinks = [
        [
            'label'   => 'Dashboard',
            'route'   => 'affiliate.dashboard',
            'pattern' => 'affiliate.dashboard',
            'icon'    => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        ],
        [
            'label'   => 'Referrals',
            'route'   => 'affiliate.referrals',
            'pattern' => 'affiliate.referrals',
            'icon'    => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        ],
        [
            'label'   => 'Earnings',
            'route'   => 'affiliate.commissions',
            'pattern' => 'affiliate.commissions',
            'icon'    => 'M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'label'   => 'Payouts',
            'route'   => 'affiliate.payouts',
            'pattern' => 'affiliate.payouts*',
            'icon'    => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        ],
        [
            'label'   => 'Settings',
            'route'   => 'affiliate.settings',
            'pattern' => 'affiliate.settings*',
            'icon'    => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
        ],
    ];
@endphp

<!-- Affiliate Navigation -->
<nav class="bg-white/95 backdrop-blur-md shadow-lg border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left side - Logo and Brand -->
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('affiliate.dashboard') }}" class="flex items-center group">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-2.5 rounded-xl group-hover:from-blue-100 group-hover:to-indigo-100 transition-all duration-300 group-hover:scale-105">
                            <img src="{{ asset('images/ballie.png') }}" alt="Ballie" class="h-6 w-auto">
                        </div>
                        <div class="ml-3 hidden sm:block">
                            <span class="text-gray-900 text-xl font-bold tracking-tight">Ballie</span>
                            <span class="block text-xs text-blue-600 font-semibold">Affiliate Partner</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex lg:items-center lg:space-x-2">
                @foreach ($navLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="nav-link {{ request()->routeIs($link['pattern']) ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"></path>
                        </svg>
                        <span class="font-semibold">{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </div>

            <!-- Primary CTA -->
            <div class="hidden lg:flex items-center ml-6">
                <a href="{{ route('affiliate.referrals') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-sm font-semibold text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Share Referral Link
                </a>
            </div>

            <!-- Profile Dropdown (Desktop) -->
            <div class="hidden lg:flex lg:items-center ml-4">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center bg-gradient-to-br from-gray-50 to-gray-100 hover:from-gray-100 hover:to-gray-200 rounded-xl px-3 py-2.5 transition-all duration-300 group border border-gray-200 hover:border-gray-300 hover:shadow-md">
                        <div class="w-9 h-9 bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-lg group-hover:scale-105 transition-transform duration-300">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="ml-2 text-gray-700 font-semibold text-sm hidden xl:block">{{ Str::limit(Auth::user()->name, 15) }}</span>
                        <svg class="ml-2 w-4 h-4 text-gray-500 group-hover:text-gray-700 transition-all duration-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden z-50">

                        <!-- User Info Header -->
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ Auth::user()->email }}</p>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-2">
                            <a href="{{ route('affiliate.settings') }}" class="dropdown-link">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Account Settings</span>
                            </a>
                            <a href="{{ route('affiliate.index') }}" class="dropdown-link">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Program Info</span>
                            </a>
                        </div>

                        <!-- Sign Out Section -->
                        <div class="border-t border-gray-200 pt-2 pb-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-link w-full text-left text-red-600 hover:bg-red-50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="lg:hidden flex items-center">
                <button @click="mobileOpen = !mobileOpen"
                        class="inline-flex items-center justify-center p-2.5 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 hover:from-gray-100 hover:to-gray-200 text-gray-700 transition-all duration-300 border border-gray-200 hover:border-gray-300 hover:shadow-md"
                        :class="{ 'bg-blue-50 border-blue-200': mobileOpen }">
                    <svg x-show="!mobileOpen" class="h-6 w-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileOpen" class="h-6 w-6 transition-transform duration-300 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="lg:hidden bg-gradient-to-b from-white to-gray-50 border-t border-gray-200 shadow-2xl">
        <div class="px-4 pt-4 pb-6 space-y-2 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <!-- User Profile Section -->
            <div class="flex items-center px-4 py-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl mb-4 border-2 border-blue-100 shadow-sm">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="ml-4 flex-1 min-w-0">
                    <div class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-600 truncate">{{ Auth::user()->email }}</div>
                    <div class="mt-1 inline-flex items-center px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                        Active
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            @foreach ($navLinks as $link)
                <a href="{{ route($link['route']) }}"
                   class="mobile-nav-link {{ request()->routeIs($link['pattern']) ? 'active' : '' }}">
                    <div class="flex items-center flex-1">
                        <div class="p-2 rounded-lg {{ request()->routeIs($link['pattern']) ? 'bg-blue-100' : 'bg-gray-100' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"></path>
                            </svg>
                        </div>
                        <span class="ml-3 font-semibold">{{ $link['label'] }}</span>
                    </div>
                    <svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            @endforeach

            <a href="{{ route('affiliate.referrals') }}" class="block w-full mt-4">
                <span class="inline-flex items-center justify-center w-full gap-3 px-4 py-3 rounded-2xl border-2 border-blue-500 text-blue-600 font-semibold bg-white hover:bg-blue-50 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16h6m2 4a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2h10z"></path>
                    </svg>
                    Share Your Link
                </span>
            </a>

            <div class="border-t-2 border-gray-200 mt-3 pt-3 space-y-2">
                <a href="{{ route('affiliate.index') }}" class="mobile-nav-link">
                    <div class="flex items-center flex-1">
                        <div class="p-2 rounded-lg bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="ml-3 font-semibold">Program Info</span>
                    </div>
                    <svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="mobile-nav-link w-full text-left text-red-600 hover:bg-red-50">
                        <div class="flex items-center flex-1">
                            <div class="p-2 rounded-lg bg-red-50 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </div>
                            <span class="ml-3 font-bold">Sign Out</span>
                        </div>
                        <svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<style>
    /* Modern Navigation Styles */
    .nav-link {
        @apply flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-300;
        @apply text-gray-600 hover:text-blue-700 hover:bg-blue-50;
        @apply border border-transparent hover:border-blue-100;
        position: relative;
    }

    .nav-link:hover {
        @apply transform -translate-y-0.5 shadow-md;
    }

    .nav-link.active {
        @apply text-white bg-gradient-to-r from-blue-600 to-indigo-600;
        @apply border-transparent shadow-lg;
    }

    .nav-link.active::before {
        content: '';
        @apply absolute bottom-0 left-1/2 transform -translate-x-1/2;
        width: 4px;
        height: 4px;
        @apply bg-white rounded-full;
        opacity: 0.8;
    }

    .dropdown-link {
        @apply flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all duration-200;
        @apply hover:text-gray-900;
    }

    .dropdown-link:hover svg {
        @apply text-blue-600 scale-110 transform;
    }

    .mobile-nav-link {
        @apply flex items-center justify-between px-4 py-3.5 rounded-xl text-base font-medium transition-all duration-300;
        @apply text-gray-700 hover:text-blue-700 hover:bg-blue-50;
        @apply border-2 border-transparent hover:border-blue-100;
    }

    .mobile-nav-link.active {
        @apply text-blue-700 bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200;
        @apply shadow-md;
    }

    .mobile-nav-link svg {
        @apply transition-all duration-300;
    }

    .mobile-nav-link:hover svg {
        @apply scale-110 transform;
    }

    .mobile-nav-link:active {
        @apply scale-95 transform;
    }

    /* Sticky navigation offset for content */
    .nav-offset {
        @apply pt-16;
    }

    /* Smooth scrolling for page */
    html {
        scroll-behavior: smooth;
    }

    /* Custom scrollbar for webkit browsers */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        @apply bg-gray-100;
    }

    ::-webkit-scrollbar-thumb {
        @apply bg-gradient-to-b from-blue-500 to-purple-600 rounded-full;
    }

    ::-webkit-scrollbar-thumb:hover {
        @apply from-blue-600 to-purple-700;
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('navigation', () => ({
            mobileOpen: false,

            init() {
                // Close mobile menu when clicking outside
                document.addEventListener('click', (e) => {
                    if (!this.$el.contains(e.target)) {
                        this.mobileOpen = false;
                    }
                });

                // Close mobile menu on route change
                window.addEventListener('beforeunload', () => {
                    this.mobileOpen = false;
                });
            }
        }))
    })
</script>
