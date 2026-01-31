@extends('brands.budlite.layouts.app')

@section('title', 'Budlite Group - Security, Technology & Innovation Solutions')
@section('description', 'Trusted provider of advanced security, technology, agriculture, and solar solutions. Protecting what matters most with innovation and excellence.')

@section('content')
<style>
    :root { 
        --color-gold: #d1b05e;
        --color-blue: #1a4d7a;
        --color-dark-blue: #0f2d4a;
        --color-red: #c41e3a;
        --color-gray: #2c3e50;
        --color-light-gray: #ecf0f1;
        --color-green: #27ae60;
        --color-orange: #e67e22;
    }

    .bg-brand-blue { background-color: var(--color-blue); }
    .bg-brand-gold { background-color: var(--color-gold); }
    .bg-brand-red { background-color: var(--color-red); }
    .bg-brand-green { background-color: var(--color-green); }
    .text-brand-gold { color: var(--color-gold); }
    .text-brand-blue { color: var(--color-blue); }
    .text-brand-red { color: var(--color-red); }

    .section-spacing {
        padding: 5rem 0;
    }

    .hero-overlay {
        background: linear-gradient(135deg, rgba(15, 45, 74, 0.95) 0%, rgba(26, 77, 122, 0.9) 100%);
    }

    .division-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .division-card:hover {
        transform: translateY(-10px);
        border-color: var(--color-gold);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .hero-image-container {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    }

    .hero-image-container img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 0.5s ease;
    }

    .hero-image-container:hover img {
        transform: scale(1.05);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.8s ease-out;
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
</style>


<!-- Hero Section -->
<section class="relative min-h-screen flex items-center bg-gradient-to-br from-gray-900 via-blue-900 to-gray-800">
    <div class="hero-overlay absolute inset-0"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="text-white animate-fadeInUp">
                <div class="inline-block mb-6">
                    <span class="bg-brand-gold text-gray-900 px-4 py-2 rounded-full text-sm font-semibold">
                        Security · Technology · Innovation
                    </span>
                </div>

                <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                    Budlite Group
                    <span class="text-brand-gold block mt-2">Security & Technology Ltd</span>
                </h1>

                <p class="text-xl md:text-2xl text-gray-200 mb-8 leading-relaxed">
                    Trusted provider of advanced security and technology solutions, committed to
                    <strong class="text-brand-gold">protecting what matters most</strong>.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    <a href="#services" class="bg-brand-gold text-gray-900 px-8 py-4 rounded-lg font-semibold hover:bg-opacity-90 transition-all text-center shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        Our Services
                    </a>
                    <a href="#contact" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-gray-900 transition-all text-center">
                        Contact Us
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-6 pt-8 border-t border-gray-600">
                    <div>
                        <div class="text-3xl font-bold text-brand-gold mb-1">10+</div>
                        <div class="text-sm text-gray-300">Years Experience</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-brand-gold mb-1">500+</div>
                        <div class="text-sm text-gray-300">Clients Served</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-brand-gold mb-1">24/7</div>
                        <div class="text-sm text-gray-300">Support</div>
                    </div>
                </div>
            </div>

            <!-- Right Image -->
            <div class="animate-fadeInUp" style="animation-delay: 0.2s;">
                <div class="hero-image-container">
                    <img src="{{ asset('brands/budlite/images/hero-image.png') }}" alt="Budlite Security Services" class="w-full">
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-6">
                        <p class="text-white font-semibold text-lg">Professional Security Solutions</p>
                        <p class="text-gray-300 text-sm">Innovation · Reliability · Excellence</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
    </div>
</section>

<!-- About Section -->
<section class="section-spacing bg-white" id="about">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                About <span class="text-brand-blue">Budlite Group</span>
            </h2>
            <div class="w-24 h-1 bg-brand-gold mx-auto mb-8"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-16">
            <div>
                <img src="{{ asset('brands/budlite/images/budlite-goup-picture.jpg') }}" alt="Budlite Group Team" class="w-full rounded-xl shadow-lg">
            </div>
            <div>
                <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                    Budlite Security & Technology Ltd is a trusted provider of advanced security and technology solutions,
                    committed to protecting what matters most. With a focus on innovation and reliability, we deliver tailored
                    services designed to safeguard businesses, properties, and individuals across various sectors.
                </p>

                <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                    From intelligent surveillance systems to state-of-the-art access control solutions, we integrate cutting-edge
                    technology into every aspect of our operations. Our mission is to provide clients with total peace of mind
                    through professional service, technical expertise, and a customer-first approach.
                </p>

                <p class="text-lg text-gray-700 mb-8 leading-relaxed">
                    At Budlite, we believe that true security goes beyond just protection — it's about
                    <strong class="text-brand-blue">trust, integrity, and excellence</strong>. Whether you're a small business,
                    a corporate facility, or a residential estate, we work closely with you to design and implement scalable,
                    effective solutions that fit your unique needs.
                </p>

                <div class="bg-brand-blue text-white p-6 rounded-lg">
                    <p class="text-lg font-semibold italic">
                        "Let us help you secure today and prepare for tomorrow — with technology that thinks ahead
                        and service you can depend on."
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-gray-50 p-6 rounded-xl border-l-4 border-brand-gold">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Our Vision</h3>
                    <p class="text-gray-700">
                        To be the leading provider of integrated security and technology solutions across Nigeria,
                        setting the standard for innovation, reliability, and customer satisfaction.
                    </p>
                </div>

                <div class="bg-gray-50 p-6 rounded-xl border-l-4 border-brand-blue">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Our Mission</h3>
                    <p class="text-gray-700">
                        To deliver comprehensive security and technology solutions that protect lives, assets, and businesses
                        through cutting-edge innovation, professional excellence, and unwavering commitment to client success.
                    </p>
                </div>

                <div class="bg-gray-50 p-6 rounded-xl border-l-4 border-brand-red">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Our Values</h3>
                    <ul class="text-gray-700 space-y-2">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-brand-gold mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Integrity & Trust
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-brand-gold mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Innovation & Excellence
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-brand-gold mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Customer-First Approach
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services/Divisions Section -->
<section class="section-spacing bg-gray-50" id="services">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                Our <span class="text-brand-gold">Divisions</span>
            </h2>
            <div class="w-24 h-1 bg-brand-blue mx-auto mb-8"></div>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Comprehensive solutions across multiple sectors to meet all your security, technology,
                agriculture, and energy needs.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Budlite Security -->
            <div class="division-card bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="relative bg-gradient-to-br from-blue-600 to-blue-800 p-8 text-white" style="background-image: url('{{ asset('brands/budlite/images/security.png') }}'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/30 to-blue-800/30"></div>
                    <div class="relative z-10">
                        <div class="feature-icon bg-white bg-opacity-20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Budlite Security</h3>
                        <p class="text-blue-100">Professional security personnel and mobile patrol services</p>
                    </div>
                </div>
                <div class="p-8">
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Trained security officers for companies</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">24/7 mobile patrol services</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Event security management</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Risk assessment & consulting</span>
                        </li>
                    </ul>
                    <a href="https://budlitesecurity.ng/" target="_blank" class="block w-full text-center bg-brand-blue text-white py-3 rounded-lg font-semibold hover:bg-opacity-90 transition-all">
                       Visit Website
                    </a>
                </div>
            </div>

            <!-- Budlite CCTV -->
            <div class="division-card bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="relative bg-gradient-to-br from-gray-700 to-gray-900 p-8 text-white" style="background-image: url('{{ asset('brands/budlite/images/security.png') }}'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-700/30 to-gray-900/30"></div>
                    <div class="relative z-10">
                        <div class="feature-icon bg-white bg-opacity-20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Budlite CCTV Surveillance</h3>
                        <p class="text-gray-200">Advanced surveillance systems for total monitoring coverage</p>
                    </div>
                </div>
                <div class="p-8">
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">HD & 4K camera installation</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Remote monitoring & playback</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Night vision technology</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Cloud storage solutions</span>
                        </li>
                    </ul>
                    <a href="#contact" class="block w-full text-center bg-brand-blue text-white py-3 rounded-lg font-semibold hover:bg-opacity-90 transition-all">
                        Learn More
                    </a>
                </div>
            </div>

            <!-- Security Equipment Sales -->
            <div class="division-card bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="relative bg-gradient-to-br from-red-600 to-red-800 p-8 text-white" style="background-image: url('{{ asset('brands/budlite/images/security.png') }}'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-600/30 to-red-800/30"></div>
                    <div class="relative z-10">
                        <div class="feature-icon bg-white bg-opacity-20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Security Equipment Sales</h3>
                        <p class="text-red-100">High-quality security equipment from trusted manufacturers</p>
                    </div>
                </div>
                <div class="p-8">
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">CCTV cameras & systems</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Access control systems</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Alarm systems & sensors</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Installation & after-sales support</span>
                        </li>
                    </ul>
                    <a href="#contact" class="block w-full text-center bg-brand-blue text-white py-3 rounded-lg font-semibold hover:bg-opacity-90 transition-all">
                        Shop Now
                    </a>
                </div>
            </div>

            <!-- Budlite Agriculture -->
            <div class="division-card bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="relative bg-gradient-to-br from-green-600 to-green-800 p-8 text-white" style="background-image: url('{{ asset('brands/budlite/images/Agriculture.png') }}'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-600/30 to-green-800/30"></div>
                    <div class="relative z-10">
                        <div class="feature-icon bg-white bg-opacity-20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Budlite Agriculture</h3>
                        <p class="text-green-100">Sustainable farming and agricultural solutions</p>
                    </div>
                </div>
                <div class="p-8">
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Modern farming techniques</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Crop production & management</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Agricultural consulting</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Supply chain solutions</span>
                        </li>
                    </ul>
                    <a href="#contact" class="block w-full text-center bg-brand-blue text-white py-3 rounded-lg font-semibold hover:bg-opacity-90 transition-all">
                        Learn More
                    </a>
                </div>
            </div>

            <!-- Budlite Solar -->
            <div class="division-card bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="relative bg-gradient-to-br from-yellow-500 to-orange-600 p-8 text-white" style="background-image: url('{{ asset('brands/budlite/images/Solar.png') }}'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/30 to-orange-600/30"></div>
                    <div class="relative z-10">
                        <div class="feature-icon bg-white bg-opacity-20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Budlite Solar</h3>
                        <p class="text-orange-100">Clean, renewable energy solutions for homes and businesses</p>
                    </div>
                </div>
                <div class="p-8">
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Solar panel installation</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Inverter & battery systems</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Energy audit & consultation</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Maintenance & support</span>
                        </li>
                    </ul>
                    <a href="#contact" class="block w-full text-center bg-brand-blue text-white py-3 rounded-lg font-semibold hover:bg-opacity-90 transition-all">
                        Learn More
                    </a>
                </div>
            </div>

            <!-- Additional Services -->
            <div class="division-card bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="relative bg-gradient-to-br from-purple-600 to-indigo-800 p-8 text-white" style="background-image: url('{{ asset('brands/budlite/images/Integrated-Solutions.png') }}'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-600/30 to-indigo-800/30"></div>
                    <div class="relative z-10">
                        <div class="feature-icon bg-white bg-opacity-20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Integrated Solutions</h3>
                        <p class="text-purple-100">Complete security & technology integration for your facility</p>
                    </div>
                </div>
                <div class="p-8">
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Comprehensive security audits</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Custom solution design</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Project management</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-gold mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Ongoing maintenance & support</span>
                        </li>
                    </ul>
                    <a href="#contact" class="block w-full text-center bg-brand-blue text-white py-3 rounded-lg font-semibold hover:bg-opacity-90 transition-all">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Best Practices Section -->
<section class="section-spacing bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                We Follow <span class="text-brand-gold">Best Practices</span>
            </h2>
            <div class="w-24 h-1 bg-brand-blue mx-auto mb-8"></div>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Budlite adheres to best practices, ensuring professional, compliant, and high-standard
                security and technology services.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center p-8 bg-gray-50 rounded-xl hover:shadow-lg transition-all">
                <div class="feature-icon bg-brand-blue mx-auto">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Compliance with Standards</h3>
                <p class="text-gray-600">
                    We adhere to international security standards and local regulations, ensuring all
                    operations meet or exceed industry requirements.
                </p>
            </div>

            <div class="text-center p-8 bg-gray-50 rounded-xl hover:shadow-lg transition-all">
                <div class="feature-icon bg-brand-gold mx-auto">
                    <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Highly Trained Personnel</h3>
                <p class="text-gray-600">
                    Our team undergoes rigorous training and continuous professional development to deliver
                    expert service you can trust.
                </p>
            </div>

            <div class="text-center p-8 bg-gray-50 rounded-xl hover:shadow-lg transition-all">
                <div class="feature-icon bg-brand-red mx-auto">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Advanced Security Technology</h3>
                <p class="text-gray-600">
                    We deploy cutting-edge technology and equipment from world-leading manufacturers to
                    provide superior protection.
                </p>
            </div>

            <div class="text-center p-8 bg-gray-50 rounded-xl hover:shadow-lg transition-all">
                <div class="feature-icon bg-brand-green mx-auto">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Tailored Risk Solutions</h3>
                <p class="text-gray-600">
                    Every client receives customized security solutions designed specifically for their
                    unique risks, environment, and budget.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="section-spacing bg-gradient-to-br from-blue-900 to-blue-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                Why Choose <span class="text-brand-gold">Budlite Group</span>
            </h2>
            <div class="w-24 h-1 bg-brand-gold mx-auto mb-8"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white bg-opacity-10 backdrop-blur-sm p-8 rounded-xl border border-white border-opacity-20">
                <div class="w-12 h-12 bg-brand-gold rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-4">24/7 Availability</h3>
                <p class="text-gray-200">
                    Round-the-clock monitoring, support, and rapid response teams ensure your security
                    is never compromised, any time of day or night.
                </p>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-sm p-8 rounded-xl border border-white border-opacity-20">
                <div class="w-12 h-12 bg-brand-gold rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-4">Competitive Pricing</h3>
                <p class="text-gray-200">
                    Professional security and technology services at affordable rates. We believe quality
                    protection should be accessible to all businesses.
                </p>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-sm p-8 rounded-xl border border-white border-opacity-20">
                <div class="w-12 h-12 bg-brand-gold rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-4">Proven Track Record</h3>
                <p class="text-gray-200">
                    Over 10 years of excellence serving 500+ satisfied clients across residential,
                    commercial, and industrial sectors.
                </p>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-sm p-8 rounded-xl border border-white border-opacity-20">
                <div class="w-12 h-12 bg-brand-gold rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-4">Integrated Solutions</h3>
                <p class="text-gray-200">
                    From security personnel to solar power, we offer complete integrated solutions
                    tailored to your specific business needs.
                </p>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-sm p-8 rounded-xl border border-white border-opacity-20">
                <div class="w-12 h-12 bg-brand-gold rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-4">Trusted & Reliable</h3>
                <p class="text-gray-200">
                    Built on a foundation of trust, integrity, and excellence. Our reputation speaks
                    for itself through countless successful partnerships.
                </p>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-sm p-8 rounded-xl border border-white border-opacity-20">
                <div class="w-12 h-12 bg-brand-gold rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-4">Technical Expertise</h3>
                <p class="text-gray-200">
                    Our team of certified professionals brings years of technical expertise and
                    industry knowledge to every project.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Client Testimonials -->
<section class="section-spacing bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                What Our <span class="text-brand-blue">Clients Say</span>
            </h2>
            <div class="w-24 h-1 bg-brand-gold mx-auto mb-8"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-xl shadow-lg">
                <div class="flex items-center mb-6">
                    <div class="flex text-brand-gold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-gray-700 mb-6 italic">
                    "Budlite has been our security partner for 5 years. Their professionalism and reliability
                    are unmatched. The CCTV system they installed has been flawless."
                </p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-brand-blue rounded-full flex items-center justify-center text-white font-bold mr-4">
                        AO
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Adebayo Ogunlesi</div>
                        <div class="text-sm text-gray-600">CEO, Phoenix Industries Ltd</div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-lg">
                <div class="flex items-center mb-6">
                    <div class="flex text-brand-gold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-gray-700 mb-6 italic">
                    "The solar installation was seamless. We've reduced our energy costs by 60% and the system
                    has been maintenance-free. Highly recommend Budlite Solar!"
                </p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-brand-green rounded-full flex items-center justify-center text-white font-bold mr-4">
                        CN
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Chioma Nwosu</div>
                        <div class="text-sm text-gray-600">Operations Manager, GreenTech Hub</div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-lg">
                <div class="flex items-center mb-6">
                    <div class="flex text-brand-gold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-gray-700 mb-6 italic">
                    "Outstanding service! Their security guards are well-trained and professional. We've had
                    zero incidents since partnering with Budlite. True peace of mind."
                </p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-brand-red rounded-full flex items-center justify-center text-white font-bold mr-4">
                        IA
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Ibrahim Abdullahi</div>
                        <div class="text-sm text-gray-600">Facility Manager, Mega Plaza</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section-spacing bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 text-white" id="contact">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">
            Ready to Secure Your Business?
        </h2>
        <p class="text-xl text-gray-200 mb-8">
            Contact us today for a free consultation and discover how Budlite can protect what matters most to you.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white bg-opacity-10 backdrop-blur-sm p-6 rounded-xl">
                <svg class="w-10 h-10 text-brand-gold mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                <div class="text-lg font-semibold mb-2">Call Us</div>
                <div class="text-gray-200">+234 814 429 7292</div>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-sm p-6 rounded-xl">
                <svg class="w-10 h-10 text-brand-gold mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <div class="text-lg font-semibold mb-2">Email Us</div>
                <div class="text-gray-200">info@budlitegroup.com</div>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-sm p-6 rounded-xl">
                <svg class="w-10 h-10 text-brand-gold mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <div class="text-lg font-semibold mb-2">Visit Us</div>
                <div class="text-gray-200">Lagos, Nigeria</div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#" class="bg-brand-gold text-gray-900 px-8 py-4 rounded-lg font-semibold hover:bg-opacity-90 transition-all text-center shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                Request a Quote
            </a>
            <a href="#" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-gray-900 transition-all text-center">
                Schedule Consultation
            </a>
        </div>

        <p class="mt-8 text-gray-300">
            Available 24/7 for emergencies and consultations
        </p>
    </div>
</section>

<script>
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

    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fadeInUp');
            }
        });
    }, observerOptions);

    // Observe all division cards
    document.querySelectorAll('.division-card').forEach(card => {
        observer.observe(card);
    });
</script>

@endsection
