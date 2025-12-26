@extends('layouts.storefront')

@section('title', $storeSettings->store_name ?? $tenant->name . ' - Online Store')

@section('content')
<!-- Hero Section - Enhanced with better mobile support -->
<div class="relative min-h-[60vh] md:min-h-[70vh] flex items-center overflow-hidden">
    <!-- Background -->
    @if($storeSettings->store_banner)
        <div class="absolute inset-0">
            <img src="{{ Storage::disk('public')->url($storeSettings->store_banner) }}"
                 alt="{{ $storeSettings->store_name }}"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-transparent"></div>
        </div>
    @else
        <div class="absolute inset-0 hero-gradient"></div>
        <!-- Decorative Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        </div>
    @endif

    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-2xl text-white">
            <span class="inline-block px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium mb-4 md:mb-6">
                🎉 Welcome to {{ $storeSettings->store_name ?? $tenant->name }}
            </span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight">
                {{ $storeSettings->store_description ?? 'Discover Amazing Products at Great Prices' }}
            </h1>
            <p class="text-base sm:text-lg md:text-xl mb-6 md:mb-8 text-white/80 leading-relaxed">
                Shop the latest trends with fast delivery and secure payment options.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                <a href="{{ route('storefront.products', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center justify-center px-6 sm:px-8 py-3 sm:py-4 bg-white text-blue-600 font-semibold rounded-xl hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Shop Now
                </a>
                @if($categories->count() > 0)
                <a href="#categories"
                   class="inline-flex items-center justify-center px-6 sm:px-8 py-3 sm:py-4 bg-white/20 backdrop-blur-sm text-white font-semibold rounded-xl border-2 border-white/30 hover:bg-white hover:text-blue-600 transition-all duration-300">
                    Browse Categories
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 animate-bounce hidden md:block">
        <svg class="w-6 h-6 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
    </div>
</div>

<!-- Success Message -->
@if(session('success'))
    <div class="container mx-auto px-4 mt-6">
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg flex items-center" role="alert">
            <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    </div>
@endif

<!-- Trust Badges Section -->
<section class="py-8 md:py-12 bg-white border-b border-gray-100">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
            <div class="flex flex-col md:flex-row items-center text-center md:text-left gap-2 md:gap-4 p-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 text-sm md:text-base">Fast Delivery</h4>
                    <p class="text-xs md:text-sm text-gray-500">Quick & reliable shipping</p>
                </div>
            </div>
            <div class="flex flex-col md:flex-row items-center text-center md:text-left gap-2 md:gap-4 p-4">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 text-sm md:text-base">Secure Payment</h4>
                    <p class="text-xs md:text-sm text-gray-500">100% secure checkout</p>
                </div>
            </div>
            <div class="flex flex-col md:flex-row items-center text-center md:text-left gap-2 md:gap-4 p-4">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 text-sm md:text-base">Easy Returns</h4>
                    <p class="text-xs md:text-sm text-gray-500">Hassle-free returns</p>
                </div>
            </div>
            <div class="flex flex-col md:flex-row items-center text-center md:text-left gap-2 md:gap-4 p-4">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 text-sm md:text-base">24/7 Support</h4>
                    <p class="text-xs md:text-sm text-gray-500">Always here to help</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
@if($categories->count() > 0)
<section id="categories" class="py-10 md:py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-8 md:mb-12">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-3">Shop by Category</h2>
            <p class="text-gray-600 max-w-2xl mx-auto text-sm md:text-base">Browse our curated collection of products organized just for you</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
            @foreach($categories as $category)
                <a href="{{ route('storefront.products', ['tenant' => $tenant->slug, 'category' => $category->id]) }}"
                   class="group bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 text-center shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl sm:rounded-2xl mx-auto mb-3 sm:mb-4 flex items-center justify-center text-white text-xl sm:text-2xl md:text-3xl font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">
                        {{ substr($category->name, 0, 1) }}
                    </div>
                    <h3 class="font-semibold text-gray-800 group-hover:text-blue-600 transition-colors text-sm sm:text-base md:text-lg">{{ $category->name }}</h3>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<section class="py-10 md:py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8 md:mb-10">
            <div>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800">Featured Products</h2>
                <p class="text-gray-600 mt-1 text-sm md:text-base">Handpicked favorites just for you</p>
            </div>
            <a href="{{ route('storefront.products', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold group">
                View All
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
            @foreach($featuredProducts as $product)
                @include('storefront.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Promotional Banner -->
<section class="py-10 md:py-16 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-500 relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-20 -right-20 w-60 h-60 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-60 h-60 bg-white/10 rounded-full blur-3xl"></div>
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 md:gap-8">
            <div class="text-center md:text-left">
                <span class="inline-block px-4 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium mb-4">
                    Limited Time Offer
                </span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-3">Get Special Discounts!</h2>
                <p class="text-white/80 text-sm md:text-base max-w-md">Sign up for our newsletter and be the first to know about exclusive deals and new arrivals.</p>
            </div>
            <div class="w-full md:w-auto">
                <div class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto md:mx-0">
                    <input type="email" placeholder="Enter your email"
                           class="flex-1 px-5 py-3 rounded-xl border-0 focus:ring-2 focus:ring-white/50 text-gray-800 placeholder-gray-500">
                    <button class="px-6 py-3 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-800 transition-colors whitespace-nowrap">
                        Subscribe
                    </button>
                </div>
                <p class="text-white/60 text-xs mt-3 text-center md:text-left">We respect your privacy. Unsubscribe anytime.</p>
            </div>
        </div>
    </div>
</section>

<!-- New Arrivals -->
@if($newProducts->count() > 0)
<section class="py-10 md:py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8 md:mb-10">
            <div>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800">New Arrivals</h2>
                <p class="text-gray-600 mt-1 text-sm md:text-base">Fresh products just added to our collection</p>
            </div>
            <a href="{{ route('storefront.products', ['tenant' => $tenant->slug, 'sort' => 'newest']) }}"
               class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold group">
                View All
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
            @foreach($newProducts as $product)
                @include('storefront.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Why Choose Us Section -->
<section class="py-10 md:py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-8 md:mb-12">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-3">Why Choose Us?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto text-sm md:text-base">We're committed to providing the best shopping experience</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
            <div class="text-center p-6 md:p-8 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100">
                <div class="w-16 h-16 mx-auto mb-4 bg-blue-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2">Quality Products</h3>
                <p class="text-gray-600 text-sm md:text-base">We carefully select and verify all products to ensure top quality for our customers.</p>
            </div>
            <div class="text-center p-6 md:p-8 rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2">Best Prices</h3>
                <p class="text-gray-600 text-sm md:text-base">Competitive pricing and regular discounts to give you the best value for your money.</p>
            </div>
            <div class="text-center p-6 md:p-8 rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-100">
                <div class="w-16 h-16 mx-auto mb-4 bg-purple-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2">Happy Customers</h3>
                <p class="text-gray-600 text-sm md:text-base">Join thousands of satisfied customers who trust us for their shopping needs.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-12 md:py-20 bg-gray-900 text-white relative overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-blue-900/50 to-purple-900/50"></div>
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-500/20 rounded-full blur-3xl"></div>
    </div>
    <div class="container mx-auto px-4 text-center relative z-10">
        <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-4 md:mb-6">Ready to Start Shopping?</h2>
        <p class="text-lg md:text-xl mb-8 text-white/80 max-w-2xl mx-auto">Explore our amazing collection of products and find exactly what you're looking for.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('storefront.products', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center justify-center px-8 py-4 bg-white text-gray-900 font-bold rounded-xl hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Browse Products
            </a>
            @if(!auth('customer')->check() && $storeSettings->allow_email_registration)
                <a href="{{ route('storefront.register', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-xl hover:bg-white hover:text-gray-900 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Create Account
                </a>
            @endif
        </div>
    </div>
</section>
@endsection
