<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') - {{ config('app.name', 'Ballie') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        :root {
            --color-gold: #d1b05e;
            --color-blue: #2b6399;
            --color-dark-purple: #3c2c64;
            --color-teal: #69a2a4;
            --color-purple: #85729d;
            --color-light-blue: #7b87b8;
            --color-deep-purple: #4a3570;
            --color-lavender: #a48cb4;
            --color-violet: #614c80;
            --color-green: #249484;
        }

        .sidebar-gradient {
            background: linear-gradient(180deg, var(--color-dark-purple) 0%, var(--color-deep-purple) 100%);
        }

        .nav-item-active {
            background: linear-gradient(90deg, var(--color-gold), var(--color-violet));
            border-radius: 8px;
        }

        .nav-item-hover:hover {
            background: rgba(209, 176, 94, 0.1);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .gold-accent {
            color: var(--color-gold);
        }

        .admin-header {
            background: linear-gradient(90deg, var(--color-blue) 0%, var(--color-dark-purple) 100%);
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .sidebar-mobile-hidden {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .sidebar-mobile-visible {
                transform: translateX(0);
                transition: transform 0.3s ease-in-out;
            }
        }

        /* Scrollbar styling */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex bg-gray-100">
        <!-- Mobile menu overlay -->
        <div class="fixed inset-0 z-40 md:hidden" id="mobile-menu-overlay" style="display: none;">
            <div class="fixed inset-0 bg-black opacity-50" onclick="toggleMobileMenu()"></div>
        </div>

        <!-- Sidebar -->
        <aside class="w-64 sidebar-gradient shadow-2xl flex flex-col fixed h-full z-30 sidebar-mobile-hidden md:sidebar-mobile-visible" id="sidebar">
            <!-- Logo Section -->
            <div class="px-6 py-6 border-b border-white border-opacity-10">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white border-opacity-20">
                        <img src="{{ asset('images/ballie.png') }}" alt="Ballie Logo" class="w-8 h-8 object-contain">
                    </div>
                    <div class="ml-3">
                        <h1 class="text-xl font-bold text-white">Ballie Admin</h1>
                        <p class="text-xs text-gray-300 opacity-75">Super Administrator</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 px-6 py-6 space-y-2 overflow-y-auto custom-scrollbar">
                <!-- Main Navigation -->
                <div class="space-y-1">
                    <a href="{{ route('super-admin.dashboard') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-300 {{ request()->routeIs('super-admin.dashboard') ? 'nav-item-active text-white shadow-lg' : 'text-gray-300 nav-item-hover hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('super-admin.tenants.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-300 {{ request()->routeIs('super-admin.tenants.*') ? 'nav-item-active text-white shadow-lg' : 'text-gray-300 nav-item-hover hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Tenants
                    </a>

                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-gray-300 rounded-lg nav-item-hover hover:text-white transition-all duration-300">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Support Center
                    </a>

                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-gray-300 rounded-lg nav-item-hover hover:text-white transition-all duration-300">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Revenue & Billing
                    </a>

                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-gray-300 rounded-lg nav-item-hover hover:text-white transition-all duration-300">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Analytics & Reports
                    </a>
                </div>

                <!-- System Section -->
                <div class="pt-6 mt-6 border-t border-white border-opacity-20">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">System Management</p>

                    <div class="space-y-1">
                        <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-gray-300 rounded-lg nav-item-hover hover:text-white transition-all duration-300">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            System Settings
                        </a>

                        <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-gray-300 rounded-lg nav-item-hover hover:text-white transition-all duration-300">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Security & Logs
                        </a>
                    </div>
                </div>
            </nav>

            <!-- User Profile Section -->
            <div class="px-6 py-6 border-t border-white border-opacity-20 bg-black bg-opacity-20">
                <div class="flex items-center mb-4">
                    <img class="w-12 h-12 rounded-full border-2 shadow-lg"
                         style="border-color: var(--color-gold);"
                         src="{{ auth('super_admin')->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth('super_admin')->user()->name).'&color=ffffff&background=d1b05e' }}"
                         alt="Profile">
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-semibold text-white truncate">{{ auth('super_admin')->user()->name }}</p>
                        <p class="text-xs text-gray-300 opacity-75">Super Admin</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('super-admin.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-300 rounded-lg nav-item-hover hover:text-white transition-all duration-300 border border-white border-opacity-20">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col md:ml-64">
            <!-- Top Header -->
            <header class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-20" style="border-image: linear-gradient(90deg, var(--color-gold), var(--color-blue)) 1;">
                <div class="px-4 md:px-8 py-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <!-- Mobile menu button -->
                            <button class="md:hidden mr-4 p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors" onclick="toggleMobileMenu()">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>

                            <div>
                                <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-gray-800 via-blue-600 to-purple-600 bg-clip-text text-transparent">
                                    @yield('page-title', 'Dashboard')
                                </h1>
                                <p class="text-xs md:text-sm text-gray-500 mt-1 hidden sm:block">
                                    Welcome back, manage your system with ease
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5V12H9v5l5-5z"></path>
                                    </svg>
                                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
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

            <!-- Main Content -->
            <main class="flex-1 overflow-auto bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100">
                <div class="p-4 md:p-8">
                    <!-- Success/Error Messages -->
                    @if (session('success'))
                        <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-4 md:px-6 py-4 rounded-xl shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-800 px-4 md:px-6 py-4 rounded-xl shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Page Content -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-menu-overlay');

            if (sidebar.classList.contains('sidebar-mobile-hidden')) {
                sidebar.classList.remove('sidebar-mobile-hidden');
                sidebar.classList.add('sidebar-mobile-visible');
                overlay.style.display = 'block';
            } else {
                sidebar.classList.add('sidebar-mobile-hidden');
                sidebar.classList.remove('sidebar-mobile-visible');
                overlay.style.display = 'none';
            }
        }

        // Close mobile menu when clicking on overlay
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('mobile-menu-overlay');
            if (overlay) {
                overlay.addEventListener('click', toggleMobileMenu);
            }
        });
    </script>
</body>
</html>
