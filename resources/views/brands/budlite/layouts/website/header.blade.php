<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Budlite - Nigerian Business Management Software')</title>
    <meta name="description" content="@yield('description', 'Comprehensive business management software built specifically for Nigerian businesses. Manage accounting, inventory, sales, and more in one platform.')">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'Budlite - Nigerian Business Management Software')">
    <meta property="og:description" content="@yield('og_description', 'Comprehensive business management software built specifically for Nigerian businesses. Manage accounting, inventory, sales, and more in one platform.')">
    <meta property="og:image" content="{{ asset('images/budlite-logo.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Budlite - Business Management Software">
    <meta property="og:site_name" content="Budlite">
    <meta property="og:locale" content="en_US">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('twitter_title', 'Budlite - Nigerian Business Management Software')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Comprehensive business management software built specifically for Nigerian businesses. Manage accounting, inventory, sales, and more in one platform.')">
    <meta name="twitter:image" content="{{ asset('images/budlite-logo.png') }}">
    <meta name="twitter:image:alt" content="Budlite - Business Management Software">

    <!-- WhatsApp specific (uses Open Graph) -->
    <meta property="og:image:type" content="image/png">

    <!-- Additional meta tags -->
    <meta name="robots" content="index, follow">
    <meta name="author" content="Budlite">
    <meta name="keywords" content="@yield('keywords', 'business management software, accounting software Nigeria, inventory management, invoicing, Nigerian business, ERP software')">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/budlite-logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/budlite-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/budlite-logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900  : '#1e3a8a',
                        },
                        brand: {
                            gold: '#d1b05e',
                            blue: '#2b6399',
                            'dark-purple': '#3c2c64',
                            teal: '#69a2a4',
                            purple: '#85729d',
                            'light-blue': '#7b87b8',
                            'deep-purple': '#4a3570',
                            lavender: '#a48cb4',
                            violet: '#614c80',
                            green: '#249484',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<!-- Google tag (gtag.js) -->
{{-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-YSBPVPWC3Q"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-YSBPVPWC3Q');
</script> --}}
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center">
                            <div class="w-24 h-8 rounded-lg flex items-center justify-center mr-3">
                                {{-- <img src="{{ asset('images/budlite-logo.png') }}" alt="Budlite Logo" class="w-36 h-12"> --}}
                            </div>
                            <span class="text-xl font-bold text-brand-blue">Budlite Group of Company</span>
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        @if(!request()->is('*/dashboard*') && !request()->is('*/invoices*') && !request()->is('*/customers*'))
                            <!-- Public navigation links -->
                            <a href="{{ route('home') }}" class="text-gray-600 hover:text-brand-blue font-medium transition-colors">Home</a>
                            <a href="#about" class="text-gray-600 hover:text-brand-blue font-medium transition-colors">About</a>
                            <a href="#services" class="text-gray-600 hover:text-brand-blue font-medium transition-colors">Services</a>
                            <a href="#contact" class="text-gray-600 hover:text-brand-blue font-medium transition-colors">Contact</a>
                        @endif

                        @auth
                            <div class="relative">
                                <!-- Dropdown -->
                                <div class="relative inline-block text-left">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-brand-blue focus:outline-none transition ease-in-out duration-150" id="user-menu-button" aria-expanded="false" aria-haspopup="true" onclick="toggleDropdown()">
                                        <div>{{ Auth::user()->name }}</div>
                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>

                                    <div id="user-dropdown" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">
                                        <div class="py-1" role="none">
                                            @if(Auth::user()->tenant)
                                                <a href="{{ route('tenant.dashboard', ['tenant' => Auth::user()->tenant->slug]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-gold hover:bg-opacity-10 hover:text-brand-blue" role="menuitem">
                                                    Dashboard
                                                </a>
                                            @else
                                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-gold hover:bg-opacity-10 hover:text-brand-blue" role="menuitem">
                                                    Dashboard
                                                </a>
                                            @endif

                                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-gold hover:bg-opacity-10 hover:text-brand-blue" role="menuitem">
                                                Profile
                                            </a>

                                            <!-- Authentication -->
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <a href="{{ route('logout') }}"
                                                   onclick="event.preventDefault(); this.closest('form').submit();"
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-gold hover:bg-opacity-10 hover:text-brand-blue" role="menuitem">
                                                    Log Out
                                                </a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-brand-blue font-medium transition-colors">Login</a>
                            {{-- <a href="{{ route('register') }}" class="bg-brand-blue text-white px-4 py-2 rounded-lg hover:bg-brand-dark-purple font-medium transition-colors">Get Started</a> --}}
                        @endauth
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button class="text-gray-600 hover:text-brand-blue" onclick="toggleMobileMenu()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation -->
                <div id="mobileMenu" class="hidden md:hidden pb-4">
                    <div class="space-y-2">
                        @if(!request()->is('*/dashboard*') && !request()->is('*/invoices*') && !request()->is('*/customers*'))
                            <a href="{{ route('home') }}" class="block text-gray-600 hover:text-brand-blue font-medium py-2">Home</a>
                            <a href="#about" class="block text-gray-600 hover:text-brand-blue font-medium py-2">About</a>
                            <a href="#services" class="block text-gray-600 hover:text-brand-blue font-medium py-2">Services</a>
                            <a href="#contact" class="block text-gray-600 hover:text-brand-blue font-medium py-2">Contact</a>
                        @endif

                        @auth
                            @if(Auth::user()->tenant)
                                <a href="{{ route('tenant.dashboard', ['tenant' => Auth::user()->tenant->slug]) }}" class="block text-gray-600 hover:text-brand-blue font-medium py-2">Dashboard</a>
                            @else
                                <a href="{{ route('dashboard') }}" class="block text-gray-600 hover:text-brand-blue font-medium py-2">Dashboard</a>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="block text-gray-600 hover:text-brand-blue font-medium py-2">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); this.closest('form').submit();"
                                   class="block text-gray-600 hover:text-brand-blue font-medium py-2">Logout</a>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="block text-gray-600 hover:text-brand-blue font-medium py-2">Login</a>
                            {{-- <a href="{{ route('register') }}" class="block bg-brand-blue text-white px-4 py-2 rounded-lg hover:bg-brand-dark-purple font-medium text-center">Get Started</a> --}}
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
