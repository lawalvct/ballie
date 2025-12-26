<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $storeSettings->meta_description ?? $storeSettings->store_description }}">
    <title>@yield('title', $storeSettings->store_name ?? $tenant->name . ' Store')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .hero-gradient {
            background: linear-gradient(135deg, {{ $storeSettings->theme_primary_color ?? '#3B82F6' }} 0%, {{ $storeSettings->theme_secondary_color ?? '#8B5CF6' }} 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Top Header -->
    <div class="bg-gray-900 text-white py-2">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center text-sm">
                <div>
                    @if($storeSettings->social_facebook || $storeSettings->social_instagram || $storeSettings->social_twitter)
                        <span class="mr-4">Follow us:</span>
                        @if($storeSettings->social_facebook)
                            <a href="{{ $storeSettings->social_facebook }}" target="_blank" class="hover:text-blue-400 mr-3">Facebook</a>
                        @endif
                        @if($storeSettings->social_instagram)
                            <a href="{{ $storeSettings->social_instagram }}" target="_blank" class="hover:text-pink-400 mr-3">Instagram</a>
                        @endif
                        @if($storeSettings->social_twitter)
                            <a href="{{ $storeSettings->social_twitter }}" target="_blank" class="hover:text-blue-300">Twitter</a>
                        @endif
                    @endif
                </div>
                <div>
                    @auth('customer')
                        <span class="mr-4">Welcome, {{ auth('customer')->user()->customer->name }}!</span>
                        <form method="POST" action="{{ route('storefront.logout', ['tenant' => $tenant->slug]) }}" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-gray-300">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('storefront.login', ['tenant' => $tenant->slug]) }}" class="hover:text-gray-300 mr-3">Login</a>
                        @if($storeSettings->allow_email_registration)
                            <a href="{{ route('storefront.register', ['tenant' => $tenant->slug]) }}" class="hover:text-gray-300">Register</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="{{ route('storefront.index', ['tenant' => $tenant->slug]) }}" class="flex items-center space-x-3">
                    @if($storeSettings->store_logo)
                        <img src="{{ Storage::disk('public')->url($storeSettings->store_logo) }}" alt="{{ $storeSettings->store_name }}" class="h-12 w-auto">
                    @else
                        <div class="h-12 w-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">{{ substr($storeSettings->store_name ?? $tenant->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <span class="text-2xl font-bold text-gray-800">{{ $storeSettings->store_name ?? $tenant->name }}</span>
                </a>

                <!-- Search Bar -->
                <div class="hidden md:block flex-1 max-w-xl mx-8">
                    <form method="GET" action="{{ route('storefront.products', ['tenant' => $tenant->slug]) }}" class="relative">
                        <input type="text" name="search" placeholder="Search products..."
                               value="{{ request('search') }}"
                               class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-gray-500 hover:text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Cart -->
                <a href="{{ route('storefront.cart', ['tenant' => $tenant->slug]) }}" class="relative inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span>Cart</span>
                    <span id="cart-count" class="ml-2 bg-white text-blue-600 rounded-full px-2 py-0.5 text-xs font-bold">0</span>
                </a>
            </div>

            <!-- Mobile Search -->
            <div class="md:hidden mt-4">
                <form method="GET" action="{{ route('storefront.products', ['tenant' => $tenant->slug]) }}" class="relative">
                    <input type="text" name="search" placeholder="Search products..."
                           value="{{ request('search') }}"
                           class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-gray-500 hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">{{ $storeSettings->store_name ?? $tenant->name }}</h3>
                    <p class="text-gray-400">{{ $storeSettings->store_description }}</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('storefront.index', ['tenant' => $tenant->slug]) }}" class="text-gray-400 hover:text-white">Home</a></li>
                        <li><a href="{{ route('storefront.products', ['tenant' => $tenant->slug]) }}" class="text-gray-400 hover:text-white">Shop</a></li>
                        <li><a href="{{ route('storefront.cart', ['tenant' => $tenant->slug]) }}" class="text-gray-400 hover:text-white">Cart</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Connect With Us</h3>
                    <div class="space-y-2">
                        @if($storeSettings->social_facebook)
                            <a href="{{ $storeSettings->social_facebook }}" target="_blank" class="block text-gray-400 hover:text-white">Facebook</a>
                        @endif
                        @if($storeSettings->social_instagram)
                            <a href="{{ $storeSettings->social_instagram }}" target="_blank" class="block text-gray-400 hover:text-white">Instagram</a>
                        @endif
                        @if($storeSettings->social_twitter)
                            <a href="{{ $storeSettings->social_twitter }}" target="_blank" class="block text-gray-400 hover:text-white">Twitter</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $storeSettings->store_name ?? $tenant->name }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
