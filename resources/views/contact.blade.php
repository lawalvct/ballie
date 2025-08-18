@extends('layouts.app')

@section('title', 'Contact Us - Get in Touch with Ballie Support | Ballie')
@section('description', 'Need help or have questions? Contact our friendly support team. We\'re here to help you succeed with Ballie.')

@section('content')
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

    .gradient-bg {
        background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-dark-purple) 50%, var(--color-deep-purple) 100%);
    }

    .gradient-bg-2 {
        background: linear-gradient(135deg, var(--color-teal) 0%, var(--color-green) 50%, var(--color-blue) 100%);
    }

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

    .contact-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .contact-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .contact-card:hover::before {
        left: 100%;
    }

    .contact-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        border-color: var(--color-gold);
    }

    .contact-icon {
        transition: all 0.3s ease;
    }

    .contact-card:hover .contact-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .form-input {
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
    }

    .form-input:focus {
        border-color: var(--color-teal);
        box-shadow: 0 0 0 3px rgba(105, 162, 164, 0.1);
        transform: translateY(-2px);
    }

    .form-input:hover {
        border-color: var(--color-blue);
    }

    .submit-btn {
        background: linear-gradient(135deg, var(--color-teal), var(--color-green));
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .submit-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .submit-btn:hover::before {
        left: 100%;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(36, 148, 132, 0.3);
    }

    .info-card {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        border-color: var(--color-gold);
    }

    .faq-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .faq-card:hover {
        border-color: var(--color-teal);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .faq-button {
        transition: all 0.3s ease;
    }

    .faq-button:hover {
        background-color: #f9fafb;
    }

    .floating-animation {
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    .pulse-animation {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .slide-in-left {
        animation: slideInLeft 0.8s ease-out;
    }

    .slide-in-right {
        animation: slideInRight 0.8s ease-out;
    }

    .slide-in-up {
        animation: slideInUp 0.8s ease-out;
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .map-container {
        background: linear-gradient(135deg, var(--color-teal), var(--color-blue));
        position: relative;
        overflow: hidden;
    }

    .map-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .social-icon {
        transition: all 0.3s ease;
    }

    .social-icon:hover {
        transform: translateY(-3px) scale(1.1);
    }

    .stats-counter {
        font-variant-numeric: tabular-nums;
        transition: all 0.3s ease;
    }

    .response-time-badge {
        background: linear-gradient(135deg, var(--color-green), var(--color-teal));
        animation: pulse 2s infinite;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .contact-card {
            margin-bottom: 1rem;
        }

        .form-input {
            font-size: 16px; /* Prevents zoom on iOS */
        }
    }

    /* Form validation styles */
    .form-input.error {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .form-input.success {
        border-color: var(--color-green);
        box-shadow: 0 0 0 3px rgba(36, 148, 132, 0.1);
    }

    /* Loading animation */
    .loading {
        position: relative;
        pointer-events: none;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid transparent;
        border-top: 2px solid #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Success message animation */
    .success-message {
        animation: slideInUp 0.5s ease-out;
        background: linear-gradient(135deg, var(--color-green), var(--color-teal));
    }

    /* Interactive elements */
    .interactive-element {
        cursor: pointer;
        user-select: none;
    }

    .interactive-element:active {
        transform: scale(0.98);
    }
</style>

<!-- Hero Section -->
<section class="gradient-bg text-white py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>

    <!-- Floating background elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-brand-gold opacity-20 rounded-full floating-animation"></div>
    <div class="absolute top-32 right-20 w-16 h-16 bg-brand-teal opacity-30 rounded-full floating-animation" style="animation-delay: -2s;"></div>
    <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-brand-lavender opacity-25 rounded-full floating-animation" style="animation-delay: -4s;"></div>
    <div class="absolute top-1/2 right-1/4 w-8 h-8 bg-brand-green opacity-20 rounded-full floating-animation" style="animation-delay: -1s;"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="response-time-badge inline-flex items-center px-4 py-2 rounded-full text-white text-sm font-medium mb-6">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Average Response Time: 2 Hours
            </div>

            <h1 class="text-4xl md:text-6xl font-bold mb-6 slide-in-left">
                Get in <span class="text-brand-gold">Touch</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-200 max-w-4xl mx-auto mb-8 slide-in-right">
                Have questions about Ballie? Need help getting started? Our friendly Nigerian team is here to help you succeed with <strong class="text-brand-gold">personalized support</strong> and expert guidance.
            </p>

            <!-- Quick stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-2xl mx-auto mb-8 slide-in-up">
                <div class="text-center">
                    <div class="text-2xl font-bold text-brand-gold stats-counter" data-target="8500">0</div>
                    <div class="text-gray-300 text-sm">Happy Customers</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-brand-gold">2hrs</div>
                    <div class="text-gray-300 text-sm">Avg Response</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-brand-gold stats-counter" data-target="98">0</div>
                    <div class="text-gray-300 text-sm">% Satisfaction</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-brand-gold">24/7</div>
                    <div class="text-gray-300 text-sm">Support</div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center slide-in-up">
                <a href="#contact-form" class="bg-brand-gold text-gray-900 px-8 py-4 rounded-lg hover:bg-yellow-400 font-semibold text-lg transition-all transform hover:scale-105 interactive-element">
                    Send Message
                </a>
                <a href="tel:+2348012345678" class="border-2 border-brand-gold text-brand-gold px-8 py-4 rounded-lg hover:bg-brand-gold hover:text-gray-900 font-semibold text-lg transition-all interactive-element">
                    Call Now
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Methods Section -->
<section class="py-20 bg-gray-50 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Multiple Ways to <span class="text-brand-teal">Reach Us</span>
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Choose the method that works best for you. We're committed to providing fast, helpful responses through all channels.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Phone Support -->
            <div class="contact-card rounded-2xl p-8 text-center">
                <div class="contact-icon w-16 h-16 bg-brand-green rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Phone Support</h3>
                <p class="text-gray-600 mb-4">Speak directly with our Nigerian support team for immediate assistance.</p>
                <div class="space-y-2">
                    <a href="tel:+2348012345678" class="block text-brand-blue hover:text-brand-teal font-medium transition-colors">
                        +234 801 234 5678
                    </a>
                    <a href="tel:+2347012345678" class="block text-brand-blue hover:text-brand-teal font-medium transition-colors">
                        +234 701 234 5678
                    </a>
                </div>
                <div class="mt-4 text-sm text-gray-500">
                    Mon-Fri: 8AM-8PM WAT<br>
                    Sat-Sun: 10AM-6PM WAT
                </div>
            </div>

            <!-- Email Support -->
            <div class="contact-card rounded-2xl p-8 text-center">
                <div class="contact-icon w-16 h-16 bg-brand-blue rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Email Support</h3>
                <p class="text-gray-600 mb-4">Send us detailed questions and get comprehensive responses within 2 hours.</p>
                <div class="space-y-2">
                    <a href="mailto:support@ballie.co" class="block text-brand-blue hover:text-brand-teal font-medium transition-colors">
                        support@ballie.co
                    </a>
                    <a href="mailto:sales@ballie.co" class="block text-brand-blue hover:text-brand-teal font-medium transition-colors">
                        sales@ballie.co
                    </a>
                </div>
                <div class="mt-4 text-sm text-gray-500">
                    Average response: 2 hours<br>
                    24/7 monitoring
                </div>
            </div>

            <!-- Live Chat -->
            <div class="contact-card rounded-2xl p-8 text-center">
                <div class="contact-icon w-16 h-16 bg-brand-teal rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Live Chat</h3>
                <p class="text-gray-600 mb-4">Get instant help from our support agents through our website chat.</p>
                <button onclick="openLiveChat()" class="bg-brand-teal text-white px-6 py-2 rounded-lg hover:bg-opacity-90 font-medium transition-all transform hover:scale-105 interactive-element">
                    Start Chat
                </button>
                <div class="mt-4 text-sm text-gray-500">
                    <span class="inline-flex items-center">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 pulse-animation"></span>
                        Online Now
                    </span>
                </div>
            </div>

            <!-- WhatsApp Support -->
            <div class="contact-card rounded-2xl p-8 text-center">
                <div class="contact-icon w-16 h-16 bg-brand-green rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.085"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">WhatsApp</h3>
                <p class="text-gray-600 mb-4">Quick support through WhatsApp for urgent issues and quick questions.</p>
                <a href="https://wa.me/2348012345678" target="_blank" class="bg-brand-green text-white px-6 py-2 rounded-lg hover:bg-opacity-90 font-medium transition-all transform hover:scale-105 interactive-element inline-block">
                    Message Us
                </a>
                <div class="mt-4 text-sm text-gray-500">
                    Quick responses<br>
                    Business hours
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section id="contact-form" class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Send Us a <span class="text-brand-blue">Message</span>
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Fill out the form below and we'll get back to you within 2 hours during business hours.
            </p>
        </div>

        <div class="bg-gray-50 rounded-2xl p-8 md:p-12">
            <form id="contactForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="firstName" name="firstName" required
                               class="form-input w-full px-4 py-3 rounded-lg focus:outline-none"
                               placeholder="Enter your first name">
                        <div class="error-message text-red-500 text-sm mt-1 hidden"></div>
                    </div>

                    <div>
                        <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="lastName" name="lastName" required
                               class="form-input w-full px-4 py-3 rounded-lg focus:outline-none"
                               placeholder="Enter your last name">
                        <div class="error-message text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" required
                               class="form-input w-full px-4 py-3 rounded-lg focus:outline-none"
                               placeholder="your.email@example.com">
                        <div class="error-message text-red-500 text-sm mt-1 hidden"></div>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input type="tel" id="phone" name="phone"
                               class="form-input w-full px-4 py-3 rounded-lg focus:outline-none"
                               placeholder="+234 800 000 0000">
                        <div class="error-message text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="company" class="block text-sm font-medium text-gray-700 mb-2">
                            Company Name
                        </label>
                        <input type="text" id="company" name="company"
                               class="form-input w-full px-4 py-3 rounded-lg focus:outline-none"
                               placeholder="Your company name">
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <select id="subject" name="subject" required
                                class="form-input w-full px-4 py-3 rounded-lg focus:outline-none">
                            <option value="">Select a subject</option>
                            <option value="general">General Inquiry</option>
                            <option value="sales">Sales Question</option>
                            <option value="support">Technical Support</option>
                            <option value="billing">Billing Issue</option>
                            <option value="feature">Feature Request</option>
                            <option value="partnership">Partnership</option>
                            <option value="demo">Schedule Demo</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="error-message text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <textarea id="message" name="message" rows="6" required
                              class="form-input w-full px-4 py-3 rounded-lg focus:outline-none resize-vertical"
                              placeholder="Tell us how we can help you..."></textarea>
                    <div class="error-message text-red-500 text-sm mt-1 hidden"></div>
                    <div class="text-sm text-gray-500 mt-1">
                        <span id="charCount">0</span>/1000 characters
                    </div>
                </div>

                <div class="flex items-start">
                    <input type="checkbox" id="newsletter" name="newsletter"
                           class="mt-1 h-4 w-4 text-brand-teal border-gray-300 rounded focus:ring-brand-teal">
                    <label for="newsletter" class="ml-3 text-sm text-gray-600">
                        I'd like to receive updates about Ballie features and Nigerian business tips
                    </label>
                </div>

                <div class="flex items-start">
                    <input type="checkbox" id="privacy" name="privacy" required
                           class="mt-1 h-4 w-4 text-brand-teal border-gray-300 rounded focus:ring-brand-teal">
                    <label for="privacy" class="ml-3 text-sm text-gray-600">
                        I agree to the <a href="#" class="text-brand-blue hover:text-brand-teal">Privacy Policy</a> and
                        <a href="#" class="text-brand-blue hover:text-brand-teal">Terms of Service</a> <span class="text-red-500">*</span>
                    </label>
                </div>

                <div class="pt-4">
                    <button type="submit" id="submitBtn"
                            class="submit-btn w-full md:w-auto px-8 py-4 text-white font-semibold rounded-lg focus:outline-none focus:ring-4 focus:ring-teal-200 interactive-element">
                        <span class="btn-text">Send Message</span>
                    </button>
                </div>
            </form>

            <!-- Success Message -->
            <div id="successMessage" class="success-message hidden mt-6 p-6 rounded-lg text-white text-center">
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Message Sent Successfully!</h3>
                <p>Thank you for contacting us. We'll get back to you within 2 hours during business hours.</p>
            </div>
        </div>
    </div>
</section>

<!-- Office Locations Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Our <span class="text-brand-teal">Locations</span>
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Visit us at our offices across Nigeria or connect with us virtually from anywhere.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Lagos Office -->
            <div class="info-card bg-white rounded-2xl p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Lagos HQ</h3>
                        <p class="text-brand-blue text-sm">Main Office</p>
                    </div>
                </div>

                <div class="space-y-3 text-gray-600">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-brand-teal mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Plot 123, Admiralty Way, Lekki Phase 1, Lagos State</span>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-brand-teal mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <a href="tel:+2348012345678" class="text-brand-blue hover:text-brand-teal">+234 801 234 5678</a>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-brand-teal mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Mon-Fri: 8AM-8PM, Sat: 10AM-6PM</span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button onclick="openMap('lagos')" class="text-brand-blue hover:text-brand-teal font-medium flex items-center interactive-element">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        View on Map
                    </button>
                </div>
            </div>

            <!-- Abuja Office -->
            <div class="info-card bg-white rounded-2xl p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Abuja Office</h3>
                        <p class="text-brand-teal text-sm">Regional Office</p>
                    </div>
                </div>

                <div class="space-y-3 text-gray-600">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-brand-teal mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Suite 45, Central Business District, Abuja FCT</span>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-brand-teal mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <a href="tel:+2349012345678" class="text-brand-blue hover:text-brand-teal">+234 901 234 5678</a>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-brand-teal mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Mon-Fri: 9AM-6PM, Sat: 10AM-4PM</span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button onclick="openMap('abuja')" class="text-brand-blue hover:text-brand-teal font-medium flex items-center interactive-element">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        View on Map
                    </button>
                </div>
            </div>

            <!-- Port Harcourt Office -->
            <div class="info-card bg-white rounded-2xl p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Port Harcourt</h3>
                        <p class="text-brand-green text-sm">Regional Office</p>
                    </div>
                </div>

                <div class="space-y-3 text-gray-600">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-brand-teal mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>No. 78, Aba Road, GRA Phase 2, Port Harcourt</span>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-brand-teal mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <a href="tel:+2348412345678" class="text-brand-blue hover:text-brand-teal">+234 841 234 5678</a>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-brand-teal mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Mon-Fri: 9AM-6PM, Sat: 10AM-4PM</span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button onclick="openMap('portharcourt')" class="text-brand-blue hover:text-brand-teal font-medium flex items-center interactive-element">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        View on Map
                    </button>
                </div>
            </div>
        </div>

        <!-- Interactive Map Section -->
        <div class="mt-16">
            <div class="map-container rounded-2xl p-8 text-center text-white relative">
                <div class="relative z-10">
                    <h3 class="text-2xl font-bold mb-4">Find Us on the Map</h3>
                    <p class="text-lg mb-6 opacity-90">
                        Click on any office location above to view detailed directions and nearby landmarks.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <button onclick="openMap('lagos')" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-3 rounded-lg font-medium transition-all interactive-element">
                            Lagos HQ
                        </button>
                        <button onclick="openMap('abuja')" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-3 rounded-lg font-medium transition-all interactive-element">
                            Abuja Office
                        </button>
                        <button onclick="openMap('portharcourt')" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-3 rounded-lg font-medium transition-all interactive-element">
                            Port Harcourt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Frequently Asked <span class="text-brand-blue">Questions</span>
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Quick answers to common questions. Can't find what you're looking for? Contact us directly.
            </p>
        </div>

        <div class="space-y-4">
            <div class="faq-card rounded-lg">
                <button class="faq-button w-full px-6 py-4 text-left flex items-center justify-between focus:outline-none" onclick="toggleFAQ(0)">
                    <span class="font-medium text-gray-900">How quickly do you respond to support requests?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform faq-icon-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content-0 hidden px-6 pb-4">
                    <p class="text-gray-600">We typically respond to all support requests within 2 hours during business hours (8AM-8PM WAT, Monday-Friday). For urgent issues, you can call us directly or use our live chat for immediate assistance.</p>
                </div>
            </div>

            <div class="faq-card rounded-lg">
                <button class="faq-button w-full px-6 py-4 text-left flex items-center justify-between focus:outline-none" onclick="toggleFAQ(1)">
                    <span class="font-medium text-gray-900">Do you offer phone support in local languages?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform faq-icon-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content-1 hidden px-6 pb-4">
                    <p class="text-gray-600">Yes! Our Nigerian support team can assist you in English, Hausa, Yoruba, and Igbo. We understand the importance of communicating in your preferred language for better support experience.</p>
                </div>
            </div>

            <div class="faq-card rounded-lg">
                <button class="faq-button w-full px-6 py-4 text-left flex items-center justify-between focus:outline-none" onclick="toggleFAQ(2)">
                    <span class="font-medium text-gray-900">Can I schedule a demo or training session?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform faq-icon-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content-2 hidden px-6 pb-4">
                    <p class="text-gray-600">Absolutely! We offer free personalized demos and training sessions. You can schedule one through our contact form, by calling us, or using the live chat. We also provide on-site training for larger teams in Lagos, Abuja, and Port Harcourt.</p>
                </div>
            </div>

            <div class="faq-card rounded-lg">
                <button class="faq-button w-full px-6 py-4 text-left flex items-center justify-between focus:outline-none" onclick="toggleFAQ(3)">
                    <span class="font-medium text-gray-900">What are your support hours?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform faq-icon-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content-3 hidden px-6 pb-4">
                    <p class="text-gray-600">Our main support hours are Monday-Friday 8AM-8PM WAT, and Saturday 10AM-6PM WAT. However, we monitor critical issues 24/7 and provide emergency support for urgent business-critical problems.</p>
                </div>
            </div>

            <div class="faq-card rounded-lg">
                <button class="faq-button w-full px-6 py-4 text-left flex items-center justify-between focus:outline-none" onclick="toggleFAQ(4)">
                    <span class="font-medium text-gray-900">Do you charge for support?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform faq-icon-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content-4 hidden px-6 pb-4">
                    <p class="text-gray-600">All standard support is included free with your Ballie subscription. This includes phone, email, chat support, and basic training. We only charge for extensive on-site training sessions or custom development work.</p>
                </div>
            </div>

            <div class="faq-card rounded-lg">
                <button class="faq-button w-full px-6 py-4 text-left flex items-center justify-between focus:outline-none" onclick="toggleFAQ(5)">
                    <span class="font-medium text-gray-900">Can you help with data migration from other software?</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform faq-icon-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content-5 hidden px-6 pb-4">
                    <p class="text-gray-600">Yes! We provide free data migration assistance for new customers. Our team can help you import data from Excel, QuickBooks, Sage, and other popular accounting software. Contact us to discuss your specific migration needs.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Social Media & Community Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Connect With Our <span class="text-brand-teal">Community</span>
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Join thousands of Nigerian business owners sharing tips, asking questions, and growing together.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Facebook -->
            <div class="info-card bg-white rounded-2xl p-8 text-center">
                <div class="social-icon w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Facebook</h3>
                <p class="text-gray-600 mb-4">Join our Facebook community for daily business tips and updates.</p>
                <div class="text-2xl font-bold text-brand-blue mb-2">12.5K</div>
                <div class="text-sm text-gray-500 mb-4">Followers</div>
                <a href="https://facebook.com/ballieNG" target="_blank" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium transition-all interactive-element">
                    Follow Us
                </a>
            </div>

            <!-- Twitter -->
            <div class="info-card bg-white rounded-2xl p-8 text-center">
                <div class="social-icon w-16 h-16 bg-sky-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Twitter</h3>
                <p class="text-gray-600 mb-4">Get real-time updates and quick support responses.</p>
                <div class="text-2xl font-bold text-brand-blue mb-2">8.2K</div>
                <div class="text-sm text-gray-500 mb-4">Followers</div>
                <a href="https://twitter.com/ballieNG" target="_blank" class="bg-sky-500 text-white px-6 py-2 rounded-lg hover:bg-sky-600 font-medium transition-all interactive-element">
                    Follow Us
                </a>
            </div>

            <!-- LinkedIn -->
            <div class="info-card bg-white rounded-2xl p-8 text-center">
                <div class="social-icon w-16 h-16 bg-blue-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">LinkedIn</h3>
                <p class="text-gray-600 mb-4">Professional insights and business growth strategies.</p>
                <div class="text-2xl font-bold text-brand-blue mb-2">15.3K</div>
                <div class="text-sm text-gray-500 mb-4">Connections</div>
                <a href="https://linkedin.com/company/ballie-ng" target="_blank" class="bg-blue-700 text-white px-6 py-2 rounded-lg hover:bg-blue-800 font-medium transition-all interactive-element">
                    Connect
                </a>
            </div>

            <!-- YouTube -->
            <div class="info-card bg-white rounded-2xl p-8 text-center">
                <div class="social-icon w-16 h-16 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">YouTube</h3>
                <p class="text-gray-600 mb-4">Video tutorials and feature demonstrations.</p>
                <div class="text-2xl font-bold text-brand-blue mb-2">5.8K</div>
                <div class="text-sm text-gray-500 mb-4">Subscribers</div>
                <a href="https://youtube.com/ballieNG" target="_blank" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 font-medium transition-all interactive-element">
                    Subscribe
                </a>
            </div>
        </div>

        <!-- Community Stats -->
        <div class="mt-16 gradient-bg-2 rounded-2xl p-8 text-white text-center">
            <h3 class="text-2xl font-bold mb-6">Join Our Growing Community</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <div class="text-3xl font-bold mb-2 stats-counter" data-target="42000">0</div>
                    <div class="text-sm opacity-90">Total Community Members</div>
                </div>
                <div>
                    <div class="text-3xl font-bold mb-2 stats-counter" data-target="150">0</div>
                    <div class="text-sm opacity-90">Daily Active Discussions</div>
                </div>
                <div>
                    <div class="text-3xl font-bold mb-2 stats-counter" data-target="95">0</div>
                    <div class="text-sm opacity-90">% Questions Answered</div>
                </div>
                <div>
                    <div class="text-3xl font-bold mb-2">24hrs</div>
                    <div class="text-sm opacity-90">Average Response Time</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Emergency Support Section -->
<section class="py-16 bg-red-50 border-l-4 border-red-400">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center mb-8">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mr-6">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Emergency Support</h3>
                <p class="text-gray-600">For critical business issues that can't wait</p>
            </div>
        </div>

        <div class="bg-white rounded-lg p-8 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">When to Use Emergency Support:</h4>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            System completely down during business hours
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Data loss or corruption issues
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Security breaches or suspicious activity
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Payment processing failures
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Emergency Contact Methods:</h4>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-red-50 rounded-lg">
                            <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-900">Emergency Hotline</div>
                                <a href="tel:+2348000BALLIE" class="text-red-600 font-medium">+234 800 0BALLIE</a>
                            </div>
                        </div>

                        <div class="flex items-center p-4 bg-red-50 rounded-lg">
                            <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-900">Emergency Email</div>
                                <a href="mailto:emergency@ballie.co" class="text-red-600 font-medium">emergency@ballie.co</a>
                            </div>
                        </div>

                        <div class="text-sm text-gray-500 mt-4">
                            <strong>Response Time:</strong> Within 15 minutes for critical issues<br>
                            <strong>Availability:</strong> 24/7 for emergency situations
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('cta')

<script>
// Carousel functionality
let slideIndex = 1;
let slideInterval;

function showSlides(n) {
    let slides = document.getElementsByClassName("carousel-slide");
    let indicators = document.getElementsByClassName("carousel-indicator");

    if (n > slides.length) { slideIndex = 1; }
    if (n < 1) { slideIndex = slides.length; }

    for (let i = 0; i < slides.length; i++) {
        slides[i].classList.remove("active");
    }

    for (let i = 0; i < indicators.length; i++) {
        indicators[i].classList.remove("active");
    }

    slides[slideIndex - 1].classList.add("active");
    indicators[slideIndex - 1].classList.add("active");
}

function plusSlides(n) {
    clearInterval(slideInterval);
    showSlides(slideIndex += n);
    startAutoSlide();
}

function currentSlide(n) {
    clearInterval(slideInterval);
    showSlides(slideIndex = n);
    startAutoSlide();
}

function startAutoSlide() {
    slideInterval = setInterval(function() {
        slideIndex++;
        showSlides(slideIndex);
    }, 5000);
}

// Form validation and submission
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Reset previous errors
    document.querySelectorAll('.error-message').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });

    let isValid = true;

    // Validate required fields
    const requiredFields = ['firstName', 'lastName', 'email', 'subject', 'message'];
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            showError(field, 'This field is required');
            isValid = false;
        }
    });

    // Validate email
    const email = document.getElementById('email').value;
    if (email && !isValidEmail(email)) {
        showError('email', 'Please enter a valid email address');
        isValid = false;
    }

    // Validate privacy checkbox
    if (!document.getElementById('privacy').checked) {
        alert('Please accept the Privacy Policy and Terms of Service');
        isValid = false;
    }

    if (isValid) {
        submitForm();
    }
});

function showError(fieldId, message) {
    const errorEl = document.querySelector(`#${fieldId} + .error-message`);
    if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
    }
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function submitForm() {
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');

    // Show loading state
    submitBtn.disabled = true;
    btnText.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Sending...';

    // Simulate form submission (replace with actual API call)
    setTimeout(() => {
        document.getElementById('contactForm').style.display = 'none';
        document.getElementById('successMessage').classList.remove('hidden');

        // Reset form after success
        setTimeout(() => {
            document.getElementById('contactForm').reset();
            document.getElementById('contactForm').style.display = 'block';
            document.getElementById('successMessage').classList.add('hidden');
            submitBtn.disabled = false;
            btnText.textContent = 'Send Message';
        }, 5000);
    }, 2000);
}

// Character counter for message field
document.getElementById('message').addEventListener('input', function() {
    const charCount = this.value.length;
    document.getElementById('charCount').textContent = charCount;

    if (charCount > 1000) {
        this.value = this.value.substring(0, 1000);
        document.getElementById('charCount').textContent = 1000;
    }
});

// FAQ functionality
function toggleFAQ(index) {
    const content = document.querySelector(`.faq-content-${index}`);
    const icon = document.querySelector(`.faq-icon-${index}`);

    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

// Map functionality
function openMap(location) {
    const locations = {
        lagos: 'https://maps.google.com/?q=Plot+123+Admiralty+Way+Lekki+Lagos',
        abuja: 'https://maps.google.com/?q=Suite+45+Central+Business+District+Abuja',
        portharcourt: 'https://maps.google.com/?q=No+78+Aba+Road+GRA+Port+Harcourt'
    };

    if (locations[location]) {
        window.open(locations[location], '_blank');
    }
}

// Live chat functionality
function openLiveChat() {
    // Replace with actual live chat integration
    alert('Live chat would open here. Integration with your preferred chat service (Intercom, Zendesk, etc.)');
}

// Stats counter animation
function animateCounters() {
    const counters = document.querySelectorAll('.stats-counter');

    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const increment = target / 100;
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                counter.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current).toLocaleString();
            }
        }, 20);
    });
}

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    startAutoSlide();

    // Animate counters when they come into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.unobserve(entry.target);
            }
        });
    });

    const statsSection = document.querySelector('.stats-counter');
    if (statsSection) {
        observer.observe(statsSection.closest('section'));
    }
});

// Phone number formatting
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.startsWith('234')) {
        value = '+' + value;
    } else if (value.startsWith('0')) {
        value = '+234' + value.substring(1);
    } else if (value.length > 0 && !value.startsWith('+')) {
        value = '+234' + value;
    }
    e.target.value = value;
});
</script>

<style>
/* Additional styles for enhanced interactivity */
.interactive-element {
    transition: all 0.3s ease;
}

.interactive-element:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.contact-card {
    background: white;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.contact-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.contact-icon {
    transition: all 0.3s ease;
}

.contact-card:hover .contact-icon {
    transform: scale(1.1);
}

.social-icon {
    transition: all 0.3s ease;
}

.info-card:hover .social-icon {
    transform: scale(1.1) rotate(5deg);
}

.form-input {
    border: 2px solid #e5e7eb;
    transition: all 0.3s ease;
}

.form-input:focus {
    border-color: var(--color-teal);
    box-shadow: 0 0 0 3px rgba(105, 162, 164, 0.1);
}

.submit-btn {
    background: linear-gradient(135deg, var(--color-teal) 0%, var(--color-blue) 100%);
    transition: all 0.3s ease;
}

.submit-btn:hover {
    background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-teal) 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(43, 99, 153, 0.3);
}

.success-message {
    background: linear-gradient(135deg, var(--color-green) 0%, var(--color-teal) 100%);
}

.faq-card {
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.faq-card:hover {
    border-color: var(--color-teal);
    box-shadow: 0 2px 8px rgba(105, 162, 164, 0.1);
}

.faq-button:hover {
    background-color: #f9fafb;
}

.gradient-bg {
    background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-teal) 50%, var(--color-green) 100%);
}

.gradient-bg-2 {
    background: linear-gradient(135deg, var(--color-teal) 0%, var(--color-blue) 100%);
}

.map-container {
    background: linear-gradient(135deg, var(--color-dark-purple) 0%, var(--color-blue) 100%);
    position: relative;
    overflow: hidden;
}

.map-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>') repeat;
    opacity: 0.3;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }

    .hero-title {
        font-size: 2.5rem;
    }

    .contact-card {
        padding: 1.5rem;
    }

    .social-icon {
        width: 3rem;
        height: 3rem;
    }
}

/* Animation keyframes */
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

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

.animate-pulse-slow {
    animation: pulse 3s ease-in-out infinite;
}

/* Loading states */
.loading {
    position: relative;
    overflow: hidden;
}

.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: var(--color-teal);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--color-blue);
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    .contact-card {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ccc;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .contact-card {
        border: 2px solid #000;
    }

    .form-input {
        border: 2px solid #000;
    }

    .submit-btn {
        background: #000;
        color: #fff;
        border: 2px solid #000;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .interactive-element,
    .contact-card,
    .social-icon,
    .form-input,
    .submit-btn {
        transition: none;
    }

    .animate-fade-in-up,
    .animate-pulse-slow {
        animation: none;
    }
}

/* Focus styles for accessibility */
.interactive-element:focus,
.form-input:focus,
.faq-button:focus {
    outline: 2px solid var(--color-teal);
    outline-offset: 2px;
}

/* Error states */
.form-input.error {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.error-message {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Success states */
.form-input.success {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* Loading spinner */
.spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255,255,255,.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Tooltip styles */
.tooltip {
    position: relative;
    display: inline-block;
}

.tooltip .tooltiptext {
    visibility: hidden;
    width: 200px;
    background-color: #333;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 8px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -100px;
    opacity: 0;
    transition: opacity 0.3s;
    font-size: 14px;
}

.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}

/* Badge styles */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.badge-success {
    background-color: #dcfce7;
    color: #166534;
}

.badge-warning {
    background-color: #fef3c7;
    color: #92400e;
}

.badge-info {
    background-color: #dbeafe;
    color: #1e40af;
}

/* Card hover effects */
.card-hover {
    transition: all 0.3s ease;
    cursor: pointer;
}

.card-hover:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Gradient text */
.gradient-text {
    background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-teal) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Floating action button */
.fab {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--color-teal) 0%, var(--color-blue) 100%);
    color: white;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 1000;
}

.fab:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

/* Progress bar */
.progress-bar {
    width: 100%;
    height: 4px;
    background-color: #e5e7eb;
    border-radius: 2px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--color-teal) 0%, var(--color-blue) 100%);
    border-radius: 2px;
    transition: width 0.3s ease;
}

/* Notification styles */
.notification {
    position: fixed;
    top: 1rem;
    right: 1rem;
    max-width: 400px;
    padding: 1rem;
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    transform: translateX(100%);
    transition: transform 0.3s ease;
}

.notification.show {
    transform: translateX(0);
}

.notification-success {
    background-color: #f0fdf4;
    border-left: 4px solid #22c55e;
    color: #166534;
}

.notification-error {
    background-color: #fef2f2;
    border-left: 4px solid #ef4444;
    color: #991b1b;
}

.notification-info {
    background-color: #eff6ff;
    border-left: 4px solid #3b82f6;
    color: #1e40af;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .contact-card {
        background-color: #1f2937;
        border-color: #374151;
        color: #f9fafb;
    }

    .form-input {
        background-color: #1f2937;
        border-color: #374151;
        color: #f9fafb;
    }

    .form-input::placeholder {
        color: #9ca3af;
    }

    .faq-card {
        background-color: #1f2937;
        border-color: #374151;
    }

    .faq-button:hover {
        background-color: #374151;
    }
}
</style>
@endsection
