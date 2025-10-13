@extends('layouts.app')

@section('title', 'Cookie Policy - Ballie')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 sm:py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-12 text-center">
                <div class="max-w-3xl mx-auto">
                    <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4">Cookie Policy</h1>
                    <p class="text-xl text-blue-100 mb-2">Ballie Business Management Platform</p>
                    <p class="text-blue-200">Last updated: {{ date('F d, Y') }}</p>
                </div>
            </div>

            <!-- Content Section -->
            <div class="px-8 py-12 md:px-16">
                <div class="prose prose-lg max-w-none">
                <div class="space-y-8">
                    <section class="bg-blue-50 rounded-2xl p-6 border-l-4 border-blue-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">1</span>
                            What Are Cookies
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">Cookies are small text files that are placed on your computer or mobile device when you visit a website. They are widely used to make websites work more efficiently and provide information to the owners of the site.</p>
                        <p class="text-gray-700 leading-relaxed">Ballie uses cookies to enhance your experience, provide personalized content, analyze site traffic, and understand where our visitors are coming from.</p>
                    </section>

                    <section class="bg-gray-50 rounded-2xl p-6 border-l-4 border-gray-400">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-gray-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">2</span>
                            How We Use Cookies
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">We use cookies for the following purposes:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start"><span class="text-gray-500 mr-2 mt-1">•</span><strong>Authentication:</strong> To remember your login status and keep you signed in</li>
                            <li class="flex items-start"><span class="text-gray-500 mr-2 mt-1">•</span><strong>Preferences:</strong> To remember your settings and preferences</li>
                            <li class="flex items-start"><span class="text-gray-500 mr-2 mt-1">•</span><strong>Security:</strong> To protect your account and detect fraudulent activity</li>
                            <li class="flex items-start"><span class="text-gray-500 mr-2 mt-1">•</span><strong>Analytics:</strong> To understand how you use our service and improve it</li>
                            <li class="flex items-start"><span class="text-gray-500 mr-2 mt-1">•</span><strong>Performance:</strong> To monitor site performance and user experience</li>
                        </ul>
                    </section>

                    <section class="bg-green-50 rounded-2xl p-6 border-l-4 border-green-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">3</span>
                            Types of Cookies We Use
                        </h2>

                        <div class="space-y-4">
                            <div class="bg-white rounded-lg p-4 border border-green-200">
                                <h3 class="font-bold text-gray-900 mb-2">Essential Cookies</h3>
                                <p class="text-gray-700 text-sm leading-relaxed">These cookies are necessary for the website to function and cannot be switched off. They are usually set in response to actions made by you, such as logging in or filling in forms.</p>
                            </div>

                            <div class="bg-white rounded-lg p-4 border border-green-200">
                                <h3 class="font-bold text-gray-900 mb-2">Functionality Cookies</h3>
                                <p class="text-gray-700 text-sm leading-relaxed">These cookies enable enhanced functionality and personalization, such as remembering your preferences and settings.</p>
                            </div>

                            <div class="bg-white rounded-lg p-4 border border-green-200">
                                <h3 class="font-bold text-gray-900 mb-2">Analytics Cookies</h3>
                                <p class="text-gray-700 text-sm leading-relaxed">These cookies help us understand how visitors interact with our website by collecting and reporting information anonymously.</p>
                            </div>

                            <div class="bg-white rounded-lg p-4 border border-green-200">
                                <h3 class="font-bold text-gray-900 mb-2">Performance Cookies</h3>
                                <p class="text-gray-700 text-sm leading-relaxed">These cookies allow us to count visits and traffic sources so we can measure and improve the performance of our site.</p>
                            </div>
                        </div>
                    </section>

                    <section class="bg-purple-50 rounded-2xl p-6 border-l-4 border-purple-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-purple-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">4</span>
                            First-Party Cookies
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">These are cookies set by Ballie directly. We use them for:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="bg-white rounded-lg p-3 border border-purple-200">
                                <p class="text-gray-700 text-sm"><strong>Session Management:</strong> User authentication and login status</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-purple-200">
                                <p class="text-gray-700 text-sm"><strong>CSRF Protection:</strong> Security tokens to prevent cross-site attacks</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-purple-200">
                                <p class="text-gray-700 text-sm"><strong>User Preferences:</strong> Language, theme, and display settings</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-purple-200">
                                <p class="text-gray-700 text-sm"><strong>Feature Flags:</strong> To enable or disable specific features</p>
                            </div>
                        </div>
                    </section>

                    <section class="bg-yellow-50 rounded-2xl p-6 border-l-4 border-yellow-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-yellow-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">5</span>
                            Third-Party Cookies
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">We may use third-party services that set cookies on our behalf:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start"><span class="text-yellow-500 mr-2 mt-1">•</span><strong>Google Analytics:</strong> To analyze website traffic and user behavior</li>
                            <li class="flex items-start"><span class="text-yellow-500 mr-2 mt-1">•</span><strong>Social Media:</strong> For social sharing and login features</li>
                            <li class="flex items-start"><span class="text-yellow-500 mr-2 mt-1">•</span><strong>Payment Processors:</strong> To process transactions securely</li>
                            <li class="flex items-start"><span class="text-yellow-500 mr-2 mt-1">•</span><strong>Customer Support:</strong> For live chat and help desk functionality</li>
                        </ul>
                    </section>

                    <section class="bg-red-50 rounded-2xl p-6 border-l-4 border-red-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">6</span>
                            Cookie Duration
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">Cookies can be either session cookies or persistent cookies:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white rounded-lg p-4 border border-red-200">
                                <h3 class="font-bold text-gray-900 mb-2 flex items-center">
                                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Session Cookies
                                </h3>
                                <p class="text-gray-700 text-sm">Temporary cookies that are deleted when you close your browser. Used for essential functions like login sessions.</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-red-200">
                                <h3 class="font-bold text-gray-900 mb-2 flex items-center">
                                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Persistent Cookies
                                </h3>
                                <p class="text-gray-700 text-sm">Remain on your device for a set period or until you delete them. Used to remember preferences and improve user experience.</p>
                            </div>
                        </div>
                    </section>

                    <section class="bg-indigo-50 rounded-2xl p-6 border-l-4 border-indigo-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-indigo-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">7</span>
                            Managing Cookies
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">You have the right to decide whether to accept or reject cookies. You can exercise your cookie preferences by:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start"><span class="text-indigo-500 mr-2 mt-1">•</span><strong>Browser Settings:</strong> Most browsers allow you to refuse or accept cookies through their settings</li>
                            <li class="flex items-start"><span class="text-indigo-500 mr-2 mt-1">•</span><strong>Cookie Banner:</strong> Using our cookie consent banner when you first visit our site</li>
                            <li class="flex items-start"><span class="text-indigo-500 mr-2 mt-1">•</span><strong>Account Settings:</strong> Managing preferences within your Ballie account</li>
                        </ul>
                        <div class="bg-white rounded-lg p-4 border border-indigo-200 mt-4">
                            <p class="text-gray-700 text-sm"><strong>Note:</strong> Blocking all cookies may prevent you from using certain features of our service and may affect your experience.</p>
                        </div>
                    </section>

                    <section class="bg-teal-50 rounded-2xl p-6 border-l-4 border-teal-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-teal-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">8</span>
                            Browser Cookie Controls
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">Here's how to manage cookies in popular browsers:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="bg-white rounded-lg p-3 border border-teal-200">
                                <h3 class="font-bold text-gray-900 mb-1 text-sm">Google Chrome</h3>
                                <p class="text-gray-600 text-xs">Settings → Privacy and security → Cookies and other site data</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-teal-200">
                                <h3 class="font-bold text-gray-900 mb-1 text-sm">Mozilla Firefox</h3>
                                <p class="text-gray-600 text-xs">Options → Privacy & Security → Cookies and Site Data</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-teal-200">
                                <h3 class="font-bold text-gray-900 mb-1 text-sm">Safari</h3>
                                <p class="text-gray-600 text-xs">Preferences → Privacy → Manage Website Data</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-teal-200">
                                <h3 class="font-bold text-gray-900 mb-1 text-sm">Microsoft Edge</h3>
                                <p class="text-gray-600 text-xs">Settings → Cookies and site permissions → Cookies and site data</p>
                            </div>
                        </div>
                    </section>

                    <section class="bg-orange-50 rounded-2xl p-6 border-l-4 border-orange-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-orange-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">9</span>
                            Do Not Track Signals
                        </h2>
                        <p class="text-gray-700 leading-relaxed">Some browsers incorporate a "Do Not Track" (DNT) feature that signals to websites you visit that you do not want to have your online activity tracked. Currently, there is no uniform standard for recognizing and implementing DNT signals. We currently do not respond to DNT signals, but we are committed to providing you with meaningful choices about the information collected on our website.</p>
                    </section>

                    <section class="bg-pink-50 rounded-2xl p-6 border-l-4 border-pink-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">10</span>
                            Mobile Devices
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">Mobile devices use different technologies than browsers to store information:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start"><span class="text-pink-500 mr-2 mt-1">•</span><strong>Local Storage:</strong> Similar to cookies but with larger storage capacity</li>
                            <li class="flex items-start"><span class="text-pink-500 mr-2 mt-1">•</span><strong>Device Identifiers:</strong> Unique identifiers assigned to your mobile device</li>
                            <li class="flex items-start"><span class="text-pink-500 mr-2 mt-1">•</span><strong>App Settings:</strong> Manage data collection through your device or app settings</li>
                        </ul>
                    </section>

                    <section class="bg-cyan-50 rounded-2xl p-6 border-l-4 border-cyan-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-cyan-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">11</span>
                            Updates to This Policy
                        </h2>
                        <p class="text-gray-700 leading-relaxed">We may update this Cookie Policy from time to time to reflect changes in our practices or for other operational, legal, or regulatory reasons. We will notify you of any material changes by posting the new Cookie Policy on this page and updating the "Last Updated" date.</p>
                        <div class="bg-white rounded-lg p-4 border border-cyan-200 mt-4">
                            <p class="text-gray-700 text-sm">We encourage you to review this Cookie Policy periodically to stay informed about how we use cookies.</p>
                        </div>
                    </section>

                    <section class="bg-violet-50 rounded-2xl p-6 border-l-4 border-violet-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-violet-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">12</span>
                            Your Rights
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">Depending on your location, you may have certain rights regarding cookies and personal data:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="bg-white rounded-lg p-3 border border-violet-200">
                                <p class="text-gray-700 text-sm"><strong>Right to Access:</strong> Request information about cookies we use</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-violet-200">
                                <p class="text-gray-700 text-sm"><strong>Right to Withdraw:</strong> Withdraw consent for non-essential cookies</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-violet-200">
                                <p class="text-gray-700 text-sm"><strong>Right to Object:</strong> Object to certain types of cookie processing</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-violet-200">
                                <p class="text-gray-700 text-sm"><strong>Right to Complain:</strong> Lodge a complaint with data protection authority</p>
                            </div>
                        </div>
                    </section>

                    <section class="bg-slate-50 rounded-2xl p-6 border-l-4 border-slate-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-slate-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">13</span>
                            Contact Us
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">If you have any questions about our use of cookies or this Cookie Policy, please contact us:</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-slate-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="font-medium text-gray-900">Email</span>
                                </div>
                                <p class="text-gray-600">privacy@ballie.ng</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-slate-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span class="font-medium text-gray-900">Phone</span>
                                </div>
                                <p class="text-gray-600">+234 800 000 0000</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-slate-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="font-medium text-gray-900">Address</span>
                                </div>
                                <p class="text-gray-600">Lagos, Nigeria</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Footer Section -->
            <div class="bg-gray-50 px-8 py-8 text-center border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <a href="javascript:history.back()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Go Back
                    </a>
                    <a href="{{ route('terms') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Terms of Service
                    </a>
                    <a href="{{ route('privacy') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Privacy Policy
                    </a>
                </div>
                <p class="text-sm text-gray-500 mt-4">© {{ date('Y') }} Ballie. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>
@endsection
