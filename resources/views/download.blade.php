@extends('layouts.app')

@section('title', 'Download Ballie Mobile App - Manage Your Business On The Go')
@section('description', 'Download the Ballie mobile app for Android and iOS. Manage invoices, track inventory, monitor sales, and run your business from anywhere.')

@section('content')

<!-- Hero Section -->
<section class="gradient-bg text-white py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>

    <!-- Floating background elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-brand-gold opacity-20 rounded-full floating-animation"></div>
    <div class="absolute top-32 right-20 w-16 h-16 bg-brand-teal opacity-30 rounded-full floating-animation" style="animation-delay: -2s;"></div>
    <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-brand-lavender opacity-25 rounded-full floating-animation" style="animation-delay: -4s;"></div>
    <div class="absolute bottom-10 right-1/3 w-14 h-14 bg-brand-blue opacity-20 rounded-full floating-animation" style="animation-delay: -3s;"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Left: Text Content -->
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 mb-6">
                    <svg class="w-5 h-5 text-brand-gold mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-medium text-brand-gold">Now Available on Mobile</span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight slide-in-left">
                    Your Business<br>
                    <span class="text-brand-gold">In Your Pocket</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-200 mb-8 max-w-lg slide-in-right">
                    Run your entire business from anywhere. Create invoices, track inventory, manage customers, and monitor your finances — all from the Ballie mobile app.
                </p>

                <!-- Download Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start slide-in-left">
                    <a href="{{ asset('mobile/ballie-android-app.apk') }}" download
                       class="group inline-flex items-center bg-white text-gray-900 px-6 py-4 rounded-xl hover:bg-gray-100 font-semibold transition-all transform hover:scale-105 shadow-lg">
                        <svg class="w-8 h-8 mr-3 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.523 2.237l1.386-2.4a.29.29 0 00-.104-.393.287.287 0 00-.39.106l-1.404 2.43A8.861 8.861 0 0012.012.5c-1.8 0-3.476.53-4.998 1.48L5.609-.45a.289.289 0 00-.393.106.289.289 0 00.106.393l1.386 2.4C3.2 4.74 1.25 8.57 1.25 12h21.5c0-3.43-1.95-7.26-5.227-9.763zM7.5 9a1 1 0 110-2 1 1 0 010 2zm9 0a1 1 0 110-2 1 1 0 010 2zM1.25 13v8a2 2 0 002 2h1.5V13h-3.5zm4.5 10h12.5V13H5.75v10zM22.75 13h-3.5v10h1.5a2 2 0 002-2v-8z"/>
                        </svg>
                        <div class="text-left">
                            <div class="text-xs text-gray-500 group-hover:text-gray-600">Download for</div>
                            <div class="text-lg font-bold -mt-1">Android</div>
                        </div>
                    </a>

                    <a href="{{ asset('mobile/balle-apple-app.ipa') }}" download
                       class="group inline-flex items-center bg-white text-gray-900 px-6 py-4 rounded-xl hover:bg-gray-100 font-semibold transition-all transform hover:scale-105 shadow-lg">
                        <svg class="w-8 h-8 mr-3 text-gray-800" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                        </svg>
                        <div class="text-left">
                            <div class="text-xs text-gray-500 group-hover:text-gray-600">Download for</div>
                            <div class="text-lg font-bold -mt-1">iPhone</div>
                        </div>
                    </a>
                </div>

                <p class="text-sm text-gray-300 mt-4">
                    <svg class="w-4 h-4 inline-block mr-1 text-brand-gold" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                    Secure & trusted by 8,500+ Nigerian businesses
                </p>
            </div>

            <!-- Right: Phone Mockups -->
            <div class="relative flex justify-center lg:justify-end slide-in-right">
                <div class="relative">
                    <!-- Phone Frame 1 (Front) -->
                    <div class="relative z-10 w-64 h-[520px] bg-gray-900 rounded-[3rem] p-3 shadow-2xl transform rotate-2">
                        <div class="w-full h-full bg-gray-100 rounded-[2.4rem] overflow-hidden relative">
                            <!-- Status bar -->
                            <div class="bg-brand-blue h-8 flex items-center justify-between px-6">
                                <span class="text-white text-xs font-medium">9:41</span>
                                <div class="flex items-center space-x-1">
                                    <div class="w-4 h-2 bg-white rounded-sm opacity-80"></div>
                                    <div class="w-1.5 h-1.5 bg-white rounded-full opacity-80"></div>
                                </div>
                            </div>
                            <!-- App screenshot placeholder -->
                            <div class="bg-gradient-to-b from-brand-blue to-brand-dark-purple p-4 h-full">
                                <div class="text-white text-center mt-4">
                                    <div class="w-12 h-12 bg-white/20 rounded-xl mx-auto mb-3 flex items-center justify-center">
                                        <img src="{{ $brandService->logo() }}" alt="Ballie" class="w-8 h-8 object-contain">
                                    </div>
                                    <p class="text-sm font-bold mb-4">{{ $brand['name'] ?? 'Ballie' }}</p>
                                </div>
                                <!-- Mock dashboard cards -->
                                <div class="space-y-3">
                                    <div class="bg-white/15 backdrop-blur rounded-xl p-3">
                                        <div class="text-white/70 text-xs mb-1">Today's Sales</div>
                                        <div class="text-white font-bold text-lg">₦1,240,500</div>
                                        <div class="flex items-center mt-1">
                                            <svg class="w-3 h-3 text-green-400 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                                            <span class="text-green-400 text-xs">+12.5%</span>
                                        </div>
                                    </div>
                                    <div class="bg-white/15 backdrop-blur rounded-xl p-3">
                                        <div class="text-white/70 text-xs mb-1">Pending Invoices</div>
                                        <div class="text-white font-bold text-lg">23</div>
                                    </div>
                                    <div class="bg-white/15 backdrop-blur rounded-xl p-3">
                                        <div class="text-white/70 text-xs mb-1">Low Stock Items</div>
                                        <div class="text-brand-gold font-bold text-lg">7 items</div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="bg-white/15 backdrop-blur rounded-xl p-3 text-center">
                                            <svg class="w-5 h-5 text-brand-gold mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span class="text-white/80 text-xs">Invoice</span>
                                        </div>
                                        <div class="bg-white/15 backdrop-blur rounded-xl p-3 text-center">
                                            <svg class="w-5 h-5 text-brand-teal mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span class="text-white/80 text-xs">Customers</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Notch -->
                        <div class="absolute top-3 left-1/2 transform -translate-x-1/2 w-24 h-6 bg-gray-900 rounded-b-2xl"></div>
                    </div>

                    <!-- Phone Frame 2 (Behind) -->
                    <div class="absolute -left-12 top-8 z-0 w-56 h-[460px] bg-gray-800 rounded-[2.5rem] p-3 shadow-xl transform -rotate-6 opacity-60">
                        <div class="w-full h-full bg-gray-200 rounded-[2rem] overflow-hidden">
                            <div class="bg-brand-dark-purple h-full p-4">
                                <div class="space-y-3 mt-10">
                                    <div class="bg-white/10 rounded-lg h-12"></div>
                                    <div class="bg-white/10 rounded-lg h-20"></div>
                                    <div class="bg-white/10 rounded-lg h-16"></div>
                                    <div class="bg-white/10 rounded-lg h-12"></div>
                                    <div class="bg-white/10 rounded-lg h-24"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Everything You Need, <span class="text-brand-blue">Right on Your Phone</span>
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                The full power of Ballie, optimized for mobile. Access all the tools you need to manage your business on the go.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-8 border border-blue-100 hover:shadow-xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-brand-blue/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-brand-blue/20 transition-colors">
                    <svg class="w-7 h-7 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Invoicing & Receipts</h3>
                <p class="text-gray-600">Create and send professional invoices instantly. Generate receipts and share them via WhatsApp or email directly from your phone.</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-gradient-to-br from-green-50 to-white rounded-2xl p-8 border border-green-100 hover:shadow-xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-brand-green/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-brand-green/20 transition-colors">
                    <svg class="w-7 h-7 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Inventory Management</h3>
                <p class="text-gray-600">Track stock levels in real-time. Get low stock alerts and manage products with barcode scanning from your camera.</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-gradient-to-br from-purple-50 to-white rounded-2xl p-8 border border-purple-100 hover:shadow-xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-brand-purple/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-brand-purple/20 transition-colors">
                    <svg class="w-7 h-7 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Accounting & Finance</h3>
                <p class="text-gray-600">Monitor cash flow, track expenses, and view financial reports. Full double-entry accounting right in your pocket.</p>
            </div>

            <!-- Feature 4 -->
            <div class="bg-gradient-to-br from-yellow-50 to-white rounded-2xl p-8 border border-yellow-100 hover:shadow-xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-brand-gold/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-brand-gold/20 transition-colors">
                    <svg class="w-7 h-7 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Customer Management</h3>
                <p class="text-gray-600">Keep all customer data at your fingertips. View purchase history, manage balances, and send follow-up reminders.</p>
            </div>

            <!-- Feature 5 -->
            <div class="bg-gradient-to-br from-teal-50 to-white rounded-2xl p-8 border border-teal-100 hover:shadow-xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-brand-teal/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-brand-teal/20 transition-colors">
                    <svg class="w-7 h-7 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Reports & Analytics</h3>
                <p class="text-gray-600">View sales reports, profit & loss, and business analytics with beautiful charts optimized for mobile screens.</p>
            </div>

            <!-- Feature 6 -->
            <div class="bg-gradient-to-br from-red-50 to-white rounded-2xl p-8 border border-red-100 hover:shadow-xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-red-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-red-500/20 transition-colors">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Smart Notifications</h3>
                <p class="text-gray-600">Never miss a payment or low stock alert. Get push notifications for everything important happening in your business.</p>
            </div>
        </div>
    </div>
</section>

<!-- App Screenshots Section -->
<section class="py-20 bg-gradient-to-br from-gray-50 to-blue-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                See <span class="text-brand-blue">Ballie</span> in Action
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                A clean, intuitive interface designed for speed and simplicity. Here's a peek at what you'll get.
            </p>
        </div>

        <!-- Screenshot carousel / grid -->
        <div class="flex overflow-x-auto gap-6 pb-6 snap-x snap-mandatory scrollbar-hide justify-center">
            @foreach (['Dashboard', 'Invoicing', 'Inventory', 'Customers', 'Reports'] as $screen)
            <div class="flex-shrink-0 snap-center">
                <div class="w-56 h-[400px] bg-gray-900 rounded-[2.5rem] p-2.5 shadow-xl hover:shadow-2xl transition-shadow duration-300">
                    <div class="w-full h-full bg-white rounded-[2rem] overflow-hidden relative">
                        <!-- Placeholder for actual screenshot -->
                        <div class="absolute inset-0 bg-gradient-to-b from-brand-blue/80 to-brand-dark-purple/90 flex flex-col items-center justify-center p-6">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                                @if($screen === 'Dashboard')
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                                @elseif($screen === 'Invoicing')
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @elseif($screen === 'Inventory')
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                @elseif($screen === 'Customers')
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                @else
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                @endif
                            </div>
                            <span class="text-white font-bold text-lg">{{ $screen }}</span>
                            <span class="text-white/60 text-sm mt-1">Screenshot coming soon</span>
                            <!-- Placeholder content lines -->
                            <div class="w-full mt-6 space-y-2">
                                <div class="h-2 bg-white/20 rounded-full w-full"></div>
                                <div class="h-2 bg-white/15 rounded-full w-4/5"></div>
                                <div class="h-2 bg-white/10 rounded-full w-3/5"></div>
                                <div class="h-8 bg-white/10 rounded-lg w-full mt-4"></div>
                                <div class="h-8 bg-white/10 rounded-lg w-full"></div>
                                <div class="h-8 bg-white/10 rounded-lg w-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center text-sm font-medium text-gray-600 mt-3">{{ $screen }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- How to Install Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Get Started in <span class="text-brand-gold">3 Easy Steps</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            <!-- Step 1 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-brand-blue to-brand-dark-purple rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg transform hover:scale-110 transition-transform">
                    <span class="text-white text-3xl font-bold">1</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Download the App</h3>
                <p class="text-gray-600">Tap the download button above for your device — Android or iPhone.</p>
            </div>

            <!-- Step 2 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-brand-gold to-yellow-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg transform hover:scale-110 transition-transform">
                    <span class="text-white text-3xl font-bold">2</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Log In or Sign Up</h3>
                <p class="text-gray-600">Use your existing Ballie account or create a new one right from the app.</p>
            </div>

            <!-- Step 3 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-brand-green to-brand-teal rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg transform hover:scale-110 transition-transform">
                    <span class="text-white text-3xl font-bold">3</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Start Managing</h3>
                <p class="text-gray-600">You're all set! Begin invoicing, tracking sales, and managing your business on the go.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="gradient-bg text-white py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="absolute top-10 right-10 w-24 h-24 bg-brand-gold opacity-15 rounded-full floating-animation"></div>
    <div class="absolute bottom-10 left-10 w-20 h-20 bg-brand-teal opacity-20 rounded-full floating-animation" style="animation-delay: -3s;"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
            Ready to Take Your Business <span class="text-brand-gold">Mobile</span>?
        </h2>
        <p class="text-xl text-gray-200 mb-10 max-w-2xl mx-auto">
            Join thousands of Nigerian business owners who manage their businesses on the go with Ballie.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ asset('mobile/ballie-android-app.apk') }}" download
               class="group inline-flex items-center bg-white text-gray-900 px-8 py-4 rounded-xl hover:bg-gray-100 font-semibold transition-all transform hover:scale-105 shadow-lg">
                <svg class="w-8 h-8 mr-3 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.523 2.237l1.386-2.4a.29.29 0 00-.104-.393.287.287 0 00-.39.106l-1.404 2.43A8.861 8.861 0 0012.012.5c-1.8 0-3.476.53-4.998 1.48L5.609-.45a.289.289 0 00-.393.106.289.289 0 00.106.393l1.386 2.4C3.2 4.74 1.25 8.57 1.25 12h21.5c0-3.43-1.95-7.26-5.227-9.763zM7.5 9a1 1 0 110-2 1 1 0 010 2zm9 0a1 1 0 110-2 1 1 0 010 2zM1.25 13v8a2 2 0 002 2h1.5V13h-3.5zm4.5 10h12.5V13H5.75v10zM22.75 13h-3.5v10h1.5a2 2 0 002-2v-8z"/>
                </svg>
                <div class="text-left">
                    <div class="text-xs text-gray-500">Download for</div>
                    <div class="text-lg font-bold -mt-1">Android</div>
                </div>
            </a>

            <a href="{{ asset('mobile/balle-apple-app.ipa') }}" download
               class="group inline-flex items-center bg-white text-gray-900 px-8 py-4 rounded-xl hover:bg-gray-100 font-semibold transition-all transform hover:scale-105 shadow-lg">
                <svg class="w-8 h-8 mr-3 text-gray-800" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                </svg>
                <div class="text-left">
                    <div class="text-xs text-gray-500">Download for</div>
                    <div class="text-lg font-bold -mt-1">iPhone</div>
                </div>
            </a>
        </div>

        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-6 text-sm text-gray-300">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Free to download
            </div>
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Works offline
            </div>
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Syncs with your web account
            </div>
        </div>
    </div>
</section>

<style>
    .gradient-bg {
        background: linear-gradient(135deg, #3c2c64 0%, #2b6399 50%, #3c2c64 100%);
    }
    .floating-animation {
        animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    .slide-in-left {
        animation: slideInLeft 0.8s ease-out forwards;
    }
    .slide-in-right {
        animation: slideInRight 0.8s ease-out forwards;
    }
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-40px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(40px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

@endsection
