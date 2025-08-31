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
                        <img src="{{ asset('images/ballie_logo.png') }}" alt="Ballie Logo" class="w-10 h-10 object-contain">
                    </div>
                    <div class="ml-3">
                        <h1 class="text-xl font-bold text-white">Ballie Admin</h1>
                        <p class="text-xs text-gray-300 opacity-75">Super Administrator</p>
                    </div>
                </div>
            </div>
