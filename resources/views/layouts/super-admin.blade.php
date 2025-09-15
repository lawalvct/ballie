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

        @media (min-width: 769px) {
            .sidebar-mobile-hidden,
            .sidebar-mobile-visible {
                transform: translateX(0) !important;
            }
        }

        /* Prevent body scroll when mobile menu is open */
        body.mobile-menu-open {
            overflow: hidden;
        }

        /* Improve mobile menu overlay */
        #mobile-menu-overlay {
            backdrop-filter: blur(2px);
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

        /* Sidebar collapse/expand functionality */
        .sidebar-collapsed {
            width: 5rem !important;
        }

        .sidebar-expanded {
            width: 16rem !important;
        }

        .content-area-collapsed {
            margin-left: 5rem !important;
        }

        .content-area-expanded {
            margin-left: 16rem !important;
        }

        .sidebar-title {
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-collapsed .sidebar-title {
            opacity: 0;
            transform: translateX(-10px);
        }

        .menu-title {
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-collapsed .menu-title {
            opacity: 0;
            transform: translateX(-10px);
        }

        .sidebar-section-title {
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-collapsed .sidebar-section-title {
            opacity: 0;
            transform: translateX(-10px);
        }

        .sidebar-collapse-btn {
            position: absolute;
            right: -12px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 10;
        }

        .sidebar-collapsed .sidebar-collapse-btn svg {
            transform: scaleX(-1);
        }

        /* Adjust main content area */
        .main-content-area {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-left: 16rem;
        }

        @media (max-width: 1024px) {
            .main-content-area {
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex bg-gray-100">
        <!-- Mobile menu overlay -->
        <div class="fixed inset-0 z-20 md:hidden" id="mobile-menu-overlay" style="display: none;">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <div class="fixed top-0 left-64 right-0 bottom-0" onclick="closeMobileMenu()"></div>
        </div>

        <!-- Sidebar -->
        <aside class="w-64 sidebar-gradient shadow-2xl flex flex-col fixed h-full z-50 sidebar-mobile-hidden md:sidebar-mobile-visible" id="sidebar">
            <!-- Close button for mobile (top right of sidebar) -->
            <div class="md:hidden absolute top-4 right-4 z-10">
                <button onclick="closeMobileMenu()" class="p-2 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Logo Section -->
            <div class="px-6 py-6 border-b border-white border-opacity-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white border-opacity-20">
                            <img src="{{ asset('images/ballie_logo.png') }}" alt="Ballie Logo" class="w-10 h-10 object-contain">
                        </div>
                        <div class="ml-3 sidebar-title overflow-hidden whitespace-nowrap transition-opacity">
                            <h1 class="text-xl font-bold text-white">Ballie Admin</h1>
                            <p class="text-xs text-gray-300 opacity-75">Super Administrator</p>
                        </div>
                    </div>
                    <!-- Sidebar Collapse Button -->
                    <button id="sidebarCollapseBtn" style="color:wheat" class="p-2 rounded-lg hover:bg-white hover:bg-opacity-10 hidden lg:block transition-all duration-200 sidebar-collapse-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation Menu -->
            @include('layouts.superadmin.sidebar')

            <!-- User Profile Section -->
            <div class="px-6 py-6 border-t border-white border-opacity-20 bg-black bg-opacity-20">
                
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
        <div class="flex-1 flex flex-col main-content-area" id="main-content">
            <!-- Top Header -->
          @include('layouts.superadmin.header')

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
                // Show menu
                sidebar.classList.remove('sidebar-mobile-hidden');
                sidebar.classList.add('sidebar-mobile-visible');
                overlay.style.display = 'block';
                document.body.style.overflow = 'hidden'; // Prevent body scroll
            } else {
                // Hide menu
                sidebar.classList.add('sidebar-mobile-hidden');
                sidebar.classList.remove('sidebar-mobile-visible');
                overlay.style.display = 'none';
                document.body.style.overflow = ''; // Restore body scroll
            }
        }

        function closeMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-menu-overlay');

            sidebar.classList.add('sidebar-mobile-hidden');
            sidebar.classList.remove('sidebar-mobile-visible');
            overlay.style.display = 'none';
            document.body.style.overflow = ''; // Restore body scroll
        }

        // Initialize mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('mobile-menu-overlay');
            const sidebar = document.getElementById('sidebar');
            const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
            const mainContent = document.getElementById('main-content');
            const sidebarTitles = document.querySelectorAll('.sidebar-title');
            const menuTitles = document.querySelectorAll('.menu-title');
            const sectionTitles = document.querySelectorAll('.sidebar-section-title');

            // Desktop sidebar collapse/expand
            if (sidebarCollapseBtn) {
                sidebarCollapseBtn.addEventListener('click', function() {
                    if (sidebar.classList.contains('sidebar-collapsed')) {
                        // Expand sidebar
                        sidebar.classList.remove('sidebar-collapsed');
                        sidebar.classList.add('sidebar-expanded');
                        mainContent.classList.remove('content-area-collapsed');
                        mainContent.classList.add('content-area-expanded');
                        localStorage.setItem('superAdminSidebarCollapsed', 'false');
                    } else {
                        // Collapse sidebar
                        sidebar.classList.add('sidebar-collapsed');
                        sidebar.classList.remove('sidebar-expanded');
                        mainContent.classList.add('content-area-collapsed');
                        mainContent.classList.remove('content-area-expanded');
                        localStorage.setItem('superAdminSidebarCollapsed', 'true');
                    }
                });
            }

            // Restore sidebar state from localStorage
            const sidebarCollapsed = localStorage.getItem('superAdminSidebarCollapsed');
            if (sidebarCollapsed === 'true' && window.innerWidth >= 1024) {
                sidebar.classList.add('sidebar-collapsed');
                sidebar.classList.remove('sidebar-expanded');
                mainContent.classList.add('content-area-collapsed');
                mainContent.classList.remove('content-area-expanded');
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                mainContent.classList.remove('content-area-collapsed');
                mainContent.classList.add('content-area-expanded');
            }

            // Close menu when clicking on overlay
            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    // Only close if clicking on the overlay itself or the background, not on child elements
                    if (e.target === overlay || e.target.classList.contains('bg-black')) {
                        closeMobileMenu();
                    }
                });
            }

            // Close menu when clicking on navigation links (for better UX on mobile)
            if (sidebar) {
                const navLinks = sidebar.querySelectorAll('a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        // Only close on mobile
                        if (window.innerWidth < 768) {
                            closeMobileMenu();
                        }
                    });
                });
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    // Desktop view - ensure menu is visible and overlay is hidden
                    const sidebar = document.getElementById('sidebar');
                    const overlay = document.getElementById('mobile-menu-overlay');
                    if (sidebar && overlay) {
                        sidebar.classList.remove('sidebar-mobile-hidden');
                        sidebar.classList.add('sidebar-mobile-visible');
                        overlay.style.display = 'none';
                        document.body.style.overflow = '';
                    }
                }

                // Reset sidebar state on mobile
                if (window.innerWidth < 1024) {
                    sidebar.classList.remove('sidebar-collapsed', 'sidebar-expanded');
                    mainContent.classList.remove('content-area-collapsed', 'content-area-expanded');
                }
            });

            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && window.innerWidth < 768) {
                    closeMobileMenu();
                }
            });
        });
    </script>
</body>
</html>
