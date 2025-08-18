@extends('layouts.app')

@section('title', 'About Ballie - Nigerian Business Management Software | Ballie')
@section('description', 'Learn about Ballie\'s mission to empower Nigerian businesses with comprehensive, affordable business management software built specifically for the Nigerian market.')

@section('content')

<!-- Hero Section -->
<section class="gradient-bg text-white py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>

    <!-- Floating background elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-brand-gold opacity-20 rounded-full floating-animation"></div>
    <div class="absolute top-32 right-20 w-16 h-16 bg-brand-teal opacity-30 rounded-full floating-animation" style="animation-delay: -2s;"></div>
    <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-brand-lavender opacity-25 rounded-full floating-animation" style="animation-delay: -4s;"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 slide-in-left">
                About <span class="text-brand-gold">Ballie</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-200 max-w-4xl mx-auto mb-8 slide-in-right">
                We're on a mission to empower Nigerian businesses with world-class business management software that's built for the local market, emphasizing <strong class="text-brand-gold">Availability & Affordability</strong>.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center slide-in-left">
                <a href="{{ route('register') }}" class="bg-brand-gold text-gray-900 px-8 py-4 rounded-lg hover:bg-yellow-400 font-semibold text-lg transition-all transform hover:scale-105">
                    Start Your Journey
                </a>
                <a href="{{ route('contact') }}" class="border-2 border-brand-gold text-brand-gold px-8 py-4 rounded-lg hover:bg-brand-gold hover:text-gray-900 font-semibold text-lg transition-all">
                    Get in Touch
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="bg-gradient-to-r from-purple-400 via-blue-800 to-blue-200 text-white py-16 relative overflow-hidden">
    <!-- Background overlay with subtle pattern -->
    <div class="absolute inset-0 bg-black opacity-10 pattern-grid"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <!-- Business Trust Stats -->
            <div class="stats-card bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center hover:bg-white/20 transition-all duration-300 shadow-xl">
                <div class="text-3xl md:text-4xl font-bold mb-2 counter text-brand-gold" data-target="8500">0</div>
                <div class="text-blue-100 text-sm md:text-base">Nigerian Businesses Trust Ballie</div>
            </div>

            <!-- Uptime Stats -->
            <div class="stats-card bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center hover:bg-white/20 transition-all duration-300 shadow-xl">
                <div class="text-3xl md:text-4xl font-bold mb-2 counter text-green-400" data-target="99.9">0</div>
                <div class="text-blue-100 text-sm md:text-base">% Uptime Guarantee</div>
            </div>

            <!-- Support Stats -->
            <div class="stats-card bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center hover:bg-white/20 transition-all duration-300 shadow-xl">
                <div class="text-3xl md:text-4xl font-bold mb-2 text-purple-400">24/7</div>
                <div class="text-blue-100 text-sm md:text-base">Customer Support</div>
            </div>

            <!-- Rating Stats -->
            <div class="stats-card bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center hover:bg-white/20 transition-all duration-300 shadow-xl">
                <div class="text-3xl md:text-4xl font-bold mb-2 counter text-yellow-400" data-target="4.9">0</div>
                <div class="text-blue-100 text-sm md:text-base">/5 Customer Rating</div>
            </div>
        </div>
    </div>

    <!-- Floating background elements -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-32 left-1/2 w-64 h-64 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-4000"></div>
</section>

<!-- Mission Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="slide-in-left">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    Our <span class="text-brand-blue">Mission</span>
                </h2>
                <p class="text-lg text-gray-600 mb-6">
                    At Ballie, we believe every Nigerian business deserves access to powerful, affordable business management tools. We're building software that understands the unique challenges and opportunities of doing business in Nigeria.
                </p>
                <p class="text-lg text-gray-600 mb-8">
                    From compliance with local tax regulations to integration with Nigerian payment systems, Ballie is designed from the ground up for Nigerian businesses with a focus on <strong class="text-brand-teal">maximum availability and affordability</strong>.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start group">
                        <div class="w-12 h-12 bg-brand-teal rounded-full flex items-center justify-center mr-4 mt-1 transition-all group-hover:scale-110">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Local Compliance</h4>
                            <p class="text-gray-600 text-sm">Built for Nigerian tax and regulatory requirements with FIRS integration</p>
                        </div>
                    </div>

                    <div class="flex items-start group">
                        <div class="w-12 h-12 bg-brand-green rounded-full flex items-center justify-center mr-4 mt-1 transition-all group-hover:scale-110">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Affordable Pricing</h4>
                            <p class="text-gray-600 text-sm">Enterprise features at SME-friendly prices starting from ₦5,000/month</p>
                        </div>
                    </div>

                    <div class="flex items-start group">
                        <div class="w-12 h-12 bg-brand-blue rounded-full flex items-center justify-center mr-4 mt-1 transition-all group-hover:scale-110">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Local Support</h4>
                            <p class="text-gray-600 text-sm">Nigerian team that understands your business culture and challenges</p>
                        </div>
                    </div>

                    <div class="flex items-start group">
                        <div class="w-12 h-12 bg-brand-purple rounded-full flex items-center justify-center mr-4 mt-1 transition-all group-hover:scale-110">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Easy Integration</h4>
                            <p class="text-gray-600 text-sm">Works seamlessly with Nigerian banks and payment systems</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative slide-in-right">
                <div class="bg-gradient-to-br from-brand-blue to-brand-purple rounded-3xl p-8 text-white relative overflow-hidden">
                    <!-- Background pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-4 right-4 w-32 h-32 border-2 border-white rounded-full"></div>
                        <div class="absolute bottom-4 left-4 w-24 h-24 border-2 border-white rounded-full"></div>
                    </div>

                    <div class="relative text-center">
                        <div class="mb-8">
                            <div class="text-5xl font-bold mb-2 text-brand-gold counter" data-target="8500">0</div>
                            <div class="text-gray-200 mb-6">Nigerian Businesses Trust Ballie</div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 text-center">
                            <div class="bg-white bg-opacity-10 rounded-xl p-4 backdrop-blur-sm">
                                <div class="text-2xl font-bold text-brand-gold counter" data-target="99.9">0</div>
                                <div class="text-gray-200 text-sm">% Uptime</div>
                            </div>
                            <div class="bg-white bg-opacity-10 rounded-xl p-4 backdrop-blur-sm">
                                <div class="text-2xl font-bold text-brand-gold">24/7</div>
                                <div class="text-gray-200 text-sm">Support</div>
                            </div>
                            <div class="bg-white bg-opacity-10 rounded-xl p-4 backdrop-blur-sm">
                                <div class="text-2xl font-bold text-brand-gold counter" data-target="4.9">0</div>
                                <div class="text-gray-200 text-sm">/5 Rating</div>
                            </div>
                            <div class="bg-white bg-opacity-10 rounded-xl p-4 backdrop-blur-sm">
                                <div class="text-2xl font-bold text-brand-gold">2019</div>
                                <div class="text-gray-200 text-sm">Founded</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Values Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Our <span class="text-brand-teal">Core Values</span>
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                These principles guide everything we do at Ballie and shape how we serve Nigerian businesses.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="value-card text-center group">
                <div class="value-icon w-20 h-20 bg-brand-blue rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Integrity</h3>
                <p class="text-gray-600">We build trust through transparency, honesty, and keeping our promises to customers. Your success is our success.</p>
            </div>

            <div class="value-card text-center group">
                <div class="value-icon w-20 h-20 bg-brand-green rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Innovation</h3>
                <p class="text-gray-600">We continuously improve and innovate to solve real problems for Nigerian businesses with cutting-edge technology.</p>
            </div>

            <div class="value-card text-center group">
                <div class="value-icon w-20 h-20 bg-brand-purple rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Community</h3>
                <p class="text-gray-600">We're committed to supporting and growing the Nigerian business ecosystem through partnerships and mentorship.</p>
            </div>

            <div class="value-card text-center group">
                <div class="value-icon w-20 h-20 bg-brand-teal rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Excellence</h3>
                <p class="text-gray-600">We strive for excellence in everything we do, from product quality to customer service and user experience.</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Meet Our <span class="text-brand-blue">Team</span>
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                We're a passionate team of Nigerian entrepreneurs, developers, and business experts committed to helping businesses succeed.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="team-card bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-100">
                <div class="team-avatar w-32 h-32 rounded-full mx-auto mb-6 flex items-center justify-center text-white text-2xl font-bold">
                    AJ
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Name Name</h3>
                <p class="text-brand-blue mb-4 font-medium">CEO & Co-Founder</p>
                <p class="text-gray-600 text-sm mb-6">Former Phocus consultant with 15+ years experience helping Nigerian businesses scale. Passionate about technology and entrepreneurship.</p>
                <div class="flex justify-center space-x-4">
                    <a href="#" class="text-brand-blue hover:text-brand-teal transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-brand-blue hover:text-brand-teal transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="team-card bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-100">
                <div class="team-avatar w-32 h-32 rounded-full mx-auto mb-6 flex items-center justify-center text-white text-2xl font-bold" style="background: linear-gradient(135deg, var(--color-teal), var(--color-green));">
                    KO
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Name Name</h3>
                <p class="text-brand-teal mb-4 font-medium">CTO & Co-Founder</p>
                <p class="text-gray-600 text-sm mb-6">Software architect with experience at Talosmart Technology and OgiTech, passionate about African tech innovation and scalable systems.</p>
                <div class="flex justify-center space-x-4">
                    <a href="#" class="text-brand-teal hover:text-brand-blue transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-brand-teal hover:text-brand-blue transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.097.118.112.222.083.343-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="team-card bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-100">
                <div class="team-avatar w-32 h-32 rounded-full mx-auto mb-6 flex items-center justify-center text-white text-2xl font-bold" style="background: linear-gradient(135deg, var(--color-purple), var(--color-lavender));">
                    EN
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Name Name</h3>
                <p class="text-brand-purple mb-4 font-medium">Head of Product</p>
                <p class="text-gray-600 text-sm mb-6">Product manager with deep understanding of Nigerian business processes and pain points. Expert in user experience design.</p>
                <div class="flex justify-center space-x-4">
                    <a href="#" class="text-brand-purple hover:text-brand-blue transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-brand-purple hover:text-brand-blue transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="team-card bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-100">
                <div class="team-avatar w-32 h-32 rounded-full mx-auto mb-6 flex items-center justify-center text-white text-2xl font-bold" style="background: linear-gradient(135deg, var(--color-green), var(--color-teal));">
                    FA
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Name Name</h3>
                <p class="text-brand-green mb-4 font-medium">Head of Customer Success</p>
                <p class="text-gray-600 text-sm mb-6">Customer success expert with 8+ years helping Nigerian businesses optimize their operations and achieve growth.</p>
                <div class="flex justify-center space-x-4">
                    <a href="#" class="text-brand-green hover:text-brand-blue transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-brand-green hover:text-brand-blue transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="team-card bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-100">
                <div class="team-avatar w-32 h-32 rounded-full mx-auto mb-6 flex items-center justify-center text-white text-2xl font-bold" style="background: linear-gradient(135deg, var(--color-light-blue), var(--color-blue));">
                    OI
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Name Name</h3>
                <p class="text-brand-light-blue mb-4 font-medium">Lead Developer</p>
                <p class="text-gray-600 text-sm mb-6">Full-stack developer with expertise in Laravel, React, and cloud infrastructure. Passionate about building scalable solutions.</p>
                <div class="flex justify-center space-x-4">
                    <a href="#" class="text-brand-light-blue hover:text-brand-blue transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.017 1.154c-5.944 0-10.76 4.816-10.76 10.76 0 4.758 3.084 8.791 7.362 10.218.538-.099.73-.234.73-.519 0-.256-.009-.933-.014-1.831-2.993.651-3.627-1.444-3.627-1.444-.49-1.245-1.196-1.576-1.196-1.576-.978-.668.074-.655.074-.655 1.08.076 1.649 1.108 1.649 1.108.961 1.645 2.52 1.169 3.134.894.098-.695.376-1.169.684-1.438-2.39-.272-4.902-1.195-4.902-5.323 0-1.176.42-2.138 1.107-2.892-.111-.272-.48-1.364.105-2.843 0 0 .902-.289 2.956 1.104.857-.238 1.778-.357 2.693-.361.914.004 1.835.123 2.694.361 2.052-1.393 2.953-1.104 2.953-1.104.587 1.479.218 2.571.107 2.843.688.754 1.106 1.716 1.106 2.892 0 4.137-2.518 5.047-4.916 5.313.386.333.731.991.731 1.998 0 1.441-.013 2.601-.013 2.953 0 .288.189.624.739.518C20.922 19.9 24 15.87 24 11.077c0-5.944-4.816-10.76-10.76-10.76z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-brand-light-blue hover:text-brand-blue transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="team-card bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-100">
                <div class="team-avatar w-32 h-32 rounded-full mx-auto mb-6 flex items-center justify-center text-white text-2xl font-bold" style="background: linear-gradient(135deg, var(--color-gold), var(--color-purple));">
                    AA
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Name Name</h3>
                <p class="text-brand-gold mb-4 font-medium">Head of Marketing</p>
                <p class="text-gray-600 text-sm mb-6">Marketing strategist with deep knowledge of the Nigerian SME landscape and expertise in digital marketing and brand building.</p>
                <div class="flex justify-center space-x-4">
                    <a href="#" class="text-brand-gold hover:text-brand-blue transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-brand-gold hover:text-brand-blue transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Story Timeline -->
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Our <span class="text-brand-teal">Journey</span>
            </h2>
            <p class="text-lg text-gray-600">
                From a simple idea to empowering thousands of Nigerian businesses
            </p>
        </div>

        <div class="space-y-12">
            <div class="timeline-item">
                <div class="timeline-dot bg-brand-blue text-white">
                    2019
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">The Beginning</h3>
                    <p class="text-gray-600 mb-4">Founded by Nigerian entrepreneurs who experienced firsthand the challenges of managing a business with inadequate software tools. We saw the gap between expensive international solutions and the needs of Nigerian SMEs.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-brand-blue text-white px-3 py-1 rounded-full text-sm">Idea Conception</span>
                        <span class="bg-brand-blue text-white px-3 py-1 rounded-full text-sm">Market Research</span>
                        <span class="bg-brand-blue text-white px-3 py-1 rounded-full text-sm">Team Formation</span>
                    </div>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot bg-brand-teal text-white">
                    2020
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">First 100 Customers</h3>
                    <p class="text-gray-600 mb-4">Launched our MVP with basic accounting and invoicing features. Onboarded our first 100 customers, learning valuable lessons about Nigerian business needs and regulatory requirements.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-brand-teal text-white px-3 py-1 rounded-full text-sm">MVP Launch</span>
                        <span class="bg-brand-teal text-white px-3 py-1 rounded-full text-sm">Customer Feedback</span>
                        <span class="bg-brand-teal text-white px-3 py-1 rounded-full text-sm">Product Iteration</span>
                    </div>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot bg-brand-green text-white">
                    2021
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Major Milestone</h3>
                    <p class="text-gray-600 mb-4">Reached 1,000 active businesses and launched advanced features like inventory management, financial reporting, and Nigerian tax compliance. Established partnerships with local banks.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-brand-green text-white px-3 py-1 rounded-full text-sm">1K Users</span>
                        <span class="bg-brand-green text-white px-3 py-1 rounded-full text-sm">Advanced Features</span>
                        <span class="bg-brand-green text-white px-3 py-1 rounded-full text-sm">Bank Partnerships</span>
                    </div>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot bg-brand-purple text-white">
                    2022
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Expansion & Growth</h3>
                    <p class="text-gray-600 mb-4">Expanded our team to 25+ professionals, raised Series A funding, and launched integrations with major Nigerian payment processors. Introduced mobile apps for iOS and Android.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-brand-purple text-white px-3 py-1 rounded-full text-sm">Series A Funding</span>
                        <span class="bg-brand-purple text-white px-3 py-1 rounded-full text-sm">Mobile Apps</span>
                        <span class="bg-brand-purple text-white px-3 py-1 rounded-full text-sm">Payment Integration</span>
                    </div>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot bg-brand-gold text-gray-900">
                    2023
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Innovation & Recognition</h3>
                    <p class="text-gray-600 mb-4">Launched AI-powered features, POS system, and payroll management. Won multiple awards including "Best B2B Software Solution" at Lagos Startup Awards. Reached 5,000+ businesses.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-brand-gold text-gray-900 px-3 py-1 rounded-full text-sm">AI Features</span>
                        <span class="bg-brand-gold text-gray-900 px-3 py-1 rounded-full text-sm">Awards Won</span>
                        <span class="bg-brand-gold text-gray-900 px-3 py-1 rounded-full text-sm">5K+ Users</span>
                    </div>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot bg-brand-deep-purple text-white pulse-animation">
                    2024
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Today & Beyond</h3>
                    <p class="text-gray-600 mb-4">Serving over 8,500 Nigerian businesses with comprehensive business management tools. Continuing to innovate with new features, expanding across West Africa, and maintaining our commitment to availability and affordability.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-brand-deep-purple text-white px-3 py-1 rounded-full text-sm">8.5K+ Users</span>
                        <span class="bg-brand-deep-purple text-white px-3 py-1 rounded-full text-sm">West Africa Expansion</span>
                        <span class="bg-brand-deep-purple text-white px-3 py-1 rounded-full text-sm">Continuous Innovation</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recognition & Awards Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Recognition & <span class="text-brand-gold">Awards</span>
            </h2>
            <p class="text-lg text-gray-600">
                We're proud to be recognized for our contribution to the Nigerian tech ecosystem and business community.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="award-card text-center p-6 rounded-2xl bg-gray-50">
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">TechCrunch Startup</h3>
                <p class="text-gray-600 text-sm mb-3">Featured as a promising Nigerian startup revolutionizing business management</p>
                <span class="text-xs text-brand-gold font-medium">2023</span>
            </div>

            <div class="award-card text-center p-6 rounded-2xl bg-gray-50">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Lagos Startup Awards</h3>
                <p class="text-gray-600 text-sm mb-3">Best B2B Software Solution for innovative business management platform</p>
                <span class="text-xs text-brand-blue font-medium">2023</span>
            </div>

            <div class="award-card text-center p-6 rounded-2xl bg-gray-50">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">ISO 27001 Certified</h3>
                <p class="text-gray-600 text-sm mb-3">Information security management system certification for data protection</p>
                <span class="text-xs text-brand-green font-medium">2022</span>
            </div>

            <div class="award-card text-center p-6 rounded-2xl bg-gray-50">
                <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Techpoint Awards</h3>
                <p class="text-gray-600 text-sm mb-3">Most Innovative B2B Platform for transforming Nigerian business operations</p>
                <span class="text-xs text-brand-purple font-medium">2023</span>
            </div>

            <div class="award-card text-center p-6 rounded-2xl bg-gray-50">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Customer Choice Award</h3>
                <p class="text-gray-600 text-sm mb-3">Highest customer satisfaction rating among Nigerian business software providers</p>
                <span class="text-xs text-brand-teal font-medium">2024</span>
            </div>

            <div class="award-card text-center p-6 rounded-2xl bg-gray-50">
                <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Fast Company Africa</h3>
                <p class="text-gray-600 text-sm mb-3">Listed among Africa's most innovative companies for business transformation</p>
                <span class="text-xs text-brand-gold font-medium">2024</span>
            </div>

            <div class="award-card text-center p-6 rounded-2xl bg-gray-50">
                <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Security Excellence</h3>
                <p class="text-gray-600 text-sm mb-3">Recognized for outstanding data security and privacy protection standards</p>
                <span class="text-xs text-brand-teal font-medium">2023</span>
            </div>

            <div class="award-card text-center p-6 rounded-2xl bg-gray-50">
                <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Innovation Impact</h3>
                <p class="text-gray-600 text-sm mb-3">Recognized for significant impact on Nigerian SME digital transformation</p>
                <span class="text-xs text-brand-gold font-medium">2024</span>
            </div>
        </div>
    </div>
</section>

@include('cta')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Counter animation
        function animateCounter(element) {
            const target = parseInt(element.getAttribute('data-target'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }

                if (target === 99.9) {
                    element.textContent = current.toFixed(1);
                } else if (target === 4.9) {
                    element.textContent = current.toFixed(1);
                } else {
                    element.textContent = Math.floor(current).toLocaleString();
                }
            }, 16);
        }

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (entry.target.classList.contains('counter')) {
                        animateCounter(entry.target);
                    }

                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe counter elements
        document.querySelectorAll('.counter').forEach(counter => {
            observer.observe(counter);
        });

        // Observe fade-in elements
        document.querySelectorAll('.fade-in').forEach(element => {
            observer.observe(element);
        });

        // Add fade-in class to elements that should animate
        document.querySelectorAll('.team-card, .value-card, .award-card, .timeline-item').forEach(element => {
            element.classList.add('fade-in');
            observer.observe(element);
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add parallax effect to floating elements
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.floating-animation');

            parallaxElements.forEach((element, index) => {
                const speed = 0.5 + (index * 0.1);
                const yPos = -(scrolled * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
        });

        // Add hover effects to team social links
        document.querySelectorAll('.team-card a').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.2)';
            });

            link.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });


    });
</script>
@endsection
