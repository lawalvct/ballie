<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | {{ $tenant->name ?? 'Ballie' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .sidebar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(180deg, var(--color-dark-purple) 0%, var(--color-deep-purple) 100%);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 18rem;
        }

        .sidebar-collapsed {
            width: 5rem;
        }

        .sidebar-expanded {
            width: 18rem;
        }

        .content-area {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-left: 18rem;
            width: calc(100% - 18rem);
        }

        .content-area.collapsed {
            margin-left: 5rem;
            width: calc(100% - 5rem);
        }

        .menu-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            color: rgba(255, 255, 255, 0.8);
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .menu-item:hover::before {
            left: 100%;
        }

        .menu-item:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.05) 100%);
            transform: translateX(4px);
            color: white;
        }

        .menu-item.active {
            background: linear-gradient(135deg, rgba(209, 176, 94, 0.3) 0%, rgba(209, 176, 94, 0.15) 100%);
            border-left: 4px solid var(--color-gold);
            box-shadow: 0 4px 12px rgba(209, 176, 94, 0.3);
            color: white;
        }

        .menu-item.active::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 20px;
            background: var(--color-gold);
            border-radius: 2px 0 0 2px;
        }

        .menu-title {
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .8;
            }
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
                height: 100vh;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .content-area {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        /* Custom scrollbar */
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

        /* Footer styles */
        .main-footer {
            background: linear-gradient(135deg, var(--color-dark-purple) 0%, var(--color-deep-purple) 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        /* Mobile responsive improvements */
        @media (max-width: 768px) {
            .content-area {
                padding: 1rem;
            }
        }

        /* Gradient backgrounds for better visual appeal */
        .gradient-bg-primary {
            background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-deep-purple) 100%);
        }

        .gradient-bg-secondary {
            background: linear-gradient(135deg, var(--color-gold) 0%, var(--color-teal) 100%);
        }

        .gradient-text-primary {
            background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-deep-purple) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Brand color utilities */
        .bg-brand-blue { background-color: var(--color-blue); }
        .bg-brand-gold { background-color: var(--color-gold); }
        .bg-brand-purple { background-color: var(--color-dark-purple); }
        .bg-brand-teal { background-color: var(--color-teal); }
        .bg-brand-green { background-color: var(--color-green); }
        .bg-brand-light-blue { background-color: var(--color-light-blue); }
        .bg-brand-deep-purple { background-color: var(--color-deep-purple); }
        .bg-brand-lavender { background-color: var(--color-lavender); }

        .text-brand-gold { color: var(--color-gold); }
        .text-brand-blue { color: var(--color-blue); }
        .text-brand-purple { color: var(--color-dark-purple); }
        .text-brand-teal { color: var(--color-teal); }
        .text-brand-green { color: var(--color-green); }

        .border-brand-gold { border-color: var(--color-gold); }
        .border-brand-blue { border-color: var(--color-blue); }

        .hover\:bg-brand-gold:hover { background-color: var(--color-gold); }
        .hover\:text-brand-blue:hover { color: var(--color-blue); }
        .hover\:border-brand-gold:hover { border-color: var(--color-gold); }

             /* Loading spinner */
             .loading-spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 3px solid var(--color-gold);
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Notification styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 400px;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            background: linear-gradient(135deg, var(--color-green) 0%, #059669 100%);
            color: white;
        }

        .notification.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .notification.warning {
            background: linear-gradient(135deg, var(--color-gold) 0%, #d97706 100%);
            color: white;
        }

        .notification.info {
            background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-deep-purple) 100%);
            color: white;
        }

        /* Sidebar specific styles */
        .sidebar-title {
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-collapsed .sidebar-title {
            opacity: 0;
            transform: translateX(-10px);
        }

        .sidebar-collapsed .menu-title {
            opacity: 0;
            transform: translateX(-10px);
        }

        /* Icon hover effects */
        .menu-item svg {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .menu-item:hover svg {
            transform: scale(1.1);
        }

        .menu-item.active svg {
            filter: drop-shadow(0 0 8px rgba(209, 176, 94, 0.5));
        }

        /* Responsive sidebar adjustments */
        @media (min-width: 1024px) {
            .content-area {
                margin-left: 18rem;
            }
        }

                /* Sidebar collapse button positioning */
                .sidebar-collapse-btn {
            position: absolute;
            right: -12px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 10;
        }

        .sidebar-collapsed .sidebar-collapse-btn {
            right: -12px;
        }

        .sidebar-expanded .sidebar-collapse-btn {
            right: -12px;
        }

    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex flex-col min-h-screen">
        <!-- Sidebar -->
        @include('layouts.tenant.sidebar')

        <!-- Main Content -->
        <div class="content-area flex-1 flex flex-col overflow-hidden ml-0 lg:ml-72 transition-all duration-300">
            <!-- Header -->
            @include('layouts.tenant.header')

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                <!-- Breadcrumbs -->
                @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
                    <nav class="flex mb-6" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            @foreach($breadcrumbs as $index => $breadcrumb)
                                <li class="inline-flex items-center">
                                    @if($index > 0)
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                    @if(isset($breadcrumb['url']) && $index < count($breadcrumbs) - 1)
                                        <a href="{{ $breadcrumb['url'] }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-brand-blue md:ml-2">
                                            {{ $breadcrumb['title'] }}
                                        </a>
                                    @else
                                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                                            {{ $breadcrumb['title'] }}
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                @endif

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <svg class="fill-current h-6 w-6 text-green-500" role="button" onclick="this.parentElement.parentElement.style.display='none'">
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <svg class="fill-current h-6 w-6 text-red-500" role="button" onclick="this.parentElement.parentElement.style.display='none'">
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </span>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Warning!</strong>
                        <span class="block sm:inline">{{ session('warning') }}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <svg class="fill-current h-6 w-6 text-yellow-500" role="button" onclick="this.parentElement.parentElement.style.display='none'">
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Info!</strong>
                        <span class="block sm:inline">{{ session('info') }}</span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <svg class="fill-current h-6 w-6 text-blue-500" role="button" onclick="this.parentElement.parentElement.style.display='none'">
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </span>
                    </div>
                @endif

                <!-- Main Content Area -->
                @yield('content')
            </main>

            <!-- Footer -->
            @include('layouts.tenant.footer')
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden hidden"></div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <div class="loading-spinner"></div>
            <span class="text-gray-700">Loading...</span>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notificationContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
            const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
            const mobileSidebarClose = document.getElementById('mobileSidebarClose');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const contentArea = document.querySelector('.content-area');
            const sidebarTitles = document.querySelectorAll('.sidebar-title');
            const menuTitles = document.querySelectorAll('.menu-title');

            // Desktop sidebar collapse/expand
            if (sidebarCollapseBtn) {
                sidebarCollapseBtn.addEventListener('click', function() {
                    const isExpanded = sidebar.classList.contains('sidebar-expanded');

                    if (isExpanded) {
                        // Collapse sidebar
                        sidebar.classList.remove('sidebar-expanded');
                        sidebar.classList.add('sidebar-collapsed');
                        contentArea.classList.add('collapsed');

                        // Hide titles with animation
                        sidebarTitles.forEach(title => {
                            title.style.opacity = '0';
                            title.style.transform = 'translateX(-10px)';
                        });
                        menuTitles.forEach(title => {
                            title.style.opacity = '0';
                            title.style.transform = 'translateX(-10px)';
                        });

                        // Rotate collapse button
                        sidebarCollapseBtn.querySelector('svg').style.transform = 'rotate(180deg)';

                        // Store state in localStorage
                        localStorage.setItem('sidebarCollapsed', 'true');
                    } else {
                        // Expand sidebar
                        sidebar.classList.remove('sidebar-collapsed');
                        sidebar.classList.add('sidebar-expanded');
                        contentArea.classList.remove('collapsed');

                        // Show titles with animation
                        setTimeout(() => {
                            sidebarTitles.forEach(title => {
                                title.style.opacity = '1';
                                title.style.transform = 'translateX(0)';
                            });
                            menuTitles.forEach(title => {
                                title.style.opacity = '1';
                                title.style.transform = 'translateX(0)';
                            });
                        }, 150);

                        // Reset collapse button rotation
                        sidebarCollapseBtn.querySelector('svg').style.transform = 'rotate(0deg)';

                        // Store state in localStorage
                        localStorage.setItem('sidebarCollapsed', 'false');
                    }
                });
            }

            // Restore sidebar state from localStorage
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed');
            if (sidebarCollapsed === 'true' && window.innerWidth >= 1024) {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                contentArea.classList.add('collapsed');

                sidebarTitles.forEach(title => {
                    title.style.opacity = '0';
                    title.style.transform = 'translateX(-10px)';
                });
                menuTitles.forEach(title => {
                    title.style.opacity = '0';
                    title.style.transform = 'translateX(-10px)';
                });

                if (sidebarCollapseBtn) {
                    sidebarCollapseBtn.querySelector('svg').style.transform = 'rotate(180deg)';
                }
            } else {
                sidebar.classList.add('sidebar-expanded');
                sidebar.classList.remove('sidebar-collapsed');
            }

            // Mobile sidebar toggle
            if (mobileSidebarToggle) {
                mobileSidebarToggle.addEventListener('click', function() {
                    sidebar.classList.add('open');
                    sidebarOverlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            }

            // Mobile sidebar close
            if (mobileSidebarClose) {
                mobileSidebarClose.addEventListener('click', function() {
                    sidebar.classList.remove('open');
                    sidebarOverlay.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                });
            }

            // Overlay click to close sidebar
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('open');
                    sidebarOverlay.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                });
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('open');
                    sidebarOverlay.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            });

            // Add smooth transitions to menu items
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('active')) {
                        this.style.transform = 'translateX(4px) scale(1.02)';
                    }
                });

                item.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('active')) {
                        this.style.transform = 'translateX(0) scale(1)';
                    }
                });
            });

            // Auto-hide flash messages after 5 seconds
            const flashMessages = document.querySelectorAll('[role="alert"]');
            flashMessages.forEach(message => {
                setTimeout(() => {
                    message.style.opacity = '0';
                    message.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        message.style.display = 'none';
                    }, 300);
                }, 5000);
            });

            // Global loading functions
            window.showLoading = function() {
                document.getElementById('loadingOverlay').classList.remove('hidden');
                document.getElementById('loadingOverlay').classList.add('flex');
            };

            window.hideLoading = function() {
                document.getElementById('loadingOverlay').classList.add('hidden');
                document.getElementById('loadingOverlay').classList.remove('flex');
            };

            // Global notification function
            window.showNotification = function(message, type = 'info', duration = 5000) {
                const container = document.getElementById('notificationContainer');
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                `;

                container.appendChild(notification);

                // Show notification
                setTimeout(() => {
                    notification.classList.add('show');
                }, 100);

                // Auto-hide notification
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, duration);
            };

            // Form validation helper
            window.validateForm = function(formId) {
                const form = document.getElementById(formId);
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('border-red-500');
                        isValid = false;
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });

                return isValid;
            };

            // Confirm dialog helper
            window.confirmAction = function(message, callback) {
                if (confirm(message)) {
                    callback();
                }
            };

            // Format currency helper
            window.formatCurrency = function(amount, currency = 'USD') {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: currency
                }).format(amount);
            };

            // Format date helper
            window.formatDate = function(date, options = {}) {
                const defaultOptions = {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                };
                return new Date(date).toLocaleDateString('en-US', { ...defaultOptions, ...options });
            };

            // Debounce helper for search inputs
            window.debounce = function(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            };

            // Initialize tooltips if any
            const tooltips = document.querySelectorAll('[data-tooltip]');
            tooltips.forEach(tooltip => {
                tooltip.addEventListener('mouseenter', function() {
                    const tooltipText = this.getAttribute('data-tooltip');
                    const tooltipElement = document.createElement('div');
                    tooltipElement.className = 'absolute z-50 px-2 py-1 text-sm text-white bg-gray-900 rounded shadow-lg';
                    tooltipElement.textContent = tooltipText;
                    tooltipElement.id = 'tooltip';

                    document.body.appendChild(tooltipElement);

                    const rect = this.getBoundingClientRect();
                    tooltipElement.style.left = rect.left + (rect.width / 2) - (tooltipElement.offsetWidth / 2) + 'px';
                    tooltipElement.style.top = rect.top - tooltipElement.offsetHeight - 5 + 'px';
                });

                tooltip.addEventListener('mouseleave', function() {
                    const tooltipElement = document.getElementById('tooltip');
                    if (tooltipElement) {
                        tooltipElement.remove();
                    }
                });
            });
        });

        // Handle AJAX form submissions
        document.addEventListener('submit', function(e) {
            if (e.target.classList.contains('ajax-form')) {
                e.preventDefault();

                const form = e.target;
                const formData = new FormData(form);
                const url = form.action;
                const method = form.method;

                showLoading();

                fetch(url, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();

                    if (data.success) {
                        showNotification(data.message || 'Operation completed successfully!', 'success');
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1000);
                        }
                    } else {
                        showNotification(data.message || 'An error occurred!', 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    showNotification('An unexpected error occurred!', 'error');
                    console.error('Error:', error);
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
