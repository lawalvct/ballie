@extends('layouts.app')

@section('title', 'Terms of Service - Ballie')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 sm:py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-12 text-center">
                <div class="max-w-3xl mx-auto">
                    <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4">Terms of Service</h1>
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
                            Acceptance of Terms
                        </h2>
                        <p class="text-gray-700 leading-relaxed">By accessing and using Ballie ("the Service"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>
                    </section>

                    <section class="bg-gray-50 rounded-2xl p-6 border-l-4 border-gray-400">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-gray-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">2</span>
                            Description of Service
                        </h2>
                        <p class="text-gray-700 leading-relaxed">Ballie is a comprehensive business management platform that provides accounting, inventory management, CRM, POS, and other business tools. We reserve the right to modify, suspend, or discontinue any part of our service at any time.</p>
                    </section>

                    <section class="bg-green-50 rounded-2xl p-6 border-l-4 border-green-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">3</span>
                            User Accounts
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">To use our service, you must:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start"><span class="text-green-500 mr-2 mt-1">•</span>Provide accurate, current, and complete information during registration</li>
                            <li class="flex items-start"><span class="text-green-500 mr-2 mt-1">•</span>Maintain the security of your password and account</li>
                            <li class="flex items-start"><span class="text-green-500 mr-2 mt-1">•</span>Promptly update any changes to your account information</li>
                            <li class="flex items-start"><span class="text-green-500 mr-2 mt-1">•</span>Accept responsibility for all activities under your account</li>
                        </ul>
                    </section>

                    <section class="bg-red-50 rounded-2xl p-6 border-l-4 border-red-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">4</span>
                            Acceptable Use Policy
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">You agree not to use the service to:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start"><span class="text-red-500 mr-2 mt-1">•</span>Upload, post, or transmit any illegal, harmful, or inappropriate content</li>
                            <li class="flex items-start"><span class="text-red-500 mr-2 mt-1">•</span>Violate any applicable laws or regulations</li>
                            <li class="flex items-start"><span class="text-red-500 mr-2 mt-1">•</span>Interfere with or disrupt the service or servers</li>
                            <li class="flex items-start"><span class="text-red-500 mr-2 mt-1">•</span>Attempt to gain unauthorized access to any part of the service</li>
                            <li class="flex items-start"><span class="text-red-500 mr-2 mt-1">•</span>Use the service for any commercial purpose without our consent</li>
                        </ul>
                    </section>

                    <section class="bg-purple-50 rounded-2xl p-6 border-l-4 border-purple-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-purple-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">5</span>
                            Data and Privacy
                        </h2>
                        <p class="text-gray-700 leading-relaxed">Your privacy is important to us. Our collection and use of personal information is governed by our Privacy Policy. By using our service, you consent to the collection and use of your information as outlined in our Privacy Policy.</p>
                    </section>

                    <section class="bg-yellow-50 rounded-2xl p-6 border-l-4 border-yellow-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-yellow-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">6</span>
                            Payment Terms
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">For paid services:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start"><span class="text-yellow-500 mr-2 mt-1">•</span>Fees are charged in advance on a monthly or annual basis</li>
                            <li class="flex items-start"><span class="text-yellow-500 mr-2 mt-1">•</span>All fees are non-refundable except as required by law</li>
                            <li class="flex items-start"><span class="text-yellow-500 mr-2 mt-1">•</span>We reserve the right to change our pricing with 30 days notice</li>
                            <li class="flex items-start"><span class="text-yellow-500 mr-2 mt-1">•</span>Failure to pay may result in service suspension or termination</li>
                        </ul>
                    </section>

                    <section class="bg-indigo-50 rounded-2xl p-6 border-l-4 border-indigo-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-indigo-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">7</span>
                            Free Trial
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">We may offer a free trial period. During the trial:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start"><span class="text-indigo-500 mr-2 mt-1">•</span>You have access to premium features at no cost</li>
                            <li class="flex items-start"><span class="text-indigo-500 mr-2 mt-1">•</span>The trial automatically converts to a paid subscription unless cancelled</li>
                            <li class="flex items-start"><span class="text-indigo-500 mr-2 mt-1">•</span>We may limit certain features or usage during the trial period</li>
                        </ul>
                    </section>

                    <section class="bg-teal-50 rounded-2xl p-6 border-l-4 border-teal-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-teal-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">8</span>
                            Intellectual Property
                        </h2>
                        <p class="text-gray-700 leading-relaxed">The service and its original content, features, and functionality are owned by Ballie and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.</p>
                    </section>

                    <section class="bg-orange-50 rounded-2xl p-6 border-l-4 border-orange-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-orange-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">9</span>
                            Termination
                        </h2>
                        <p class="text-gray-700 leading-relaxed">We may terminate or suspend your account and access to the service immediately, without prior notice, for conduct that we believe violates these Terms of Service or is harmful to other users, us, or third parties, or for any other reason.</p>
                    </section>

                    <section class="bg-pink-50 rounded-2xl p-6 border-l-4 border-pink-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">10</span>
                            Disclaimer of Warranties
                        </h2>
                        <p class="text-gray-700 leading-relaxed">The service is provided on an "AS IS" and "AS AVAILABLE" basis. We disclaim all warranties, whether express or implied, including warranties of merchantability, fitness for a particular purpose, and non-infringement.</p>
                    </section>

                    <section class="bg-cyan-50 rounded-2xl p-6 border-l-4 border-cyan-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-cyan-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">11</span>
                            Limitation of Liability
                        </h2>
                        <p class="text-gray-700 leading-relaxed">In no event shall Ballie be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses.</p>
                    </section>

                    <section class="bg-emerald-50 rounded-2xl p-6 border-l-4 border-emerald-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-emerald-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">12</span>
                            Governing Law
                        </h2>
                        <p class="text-gray-700 leading-relaxed">These Terms shall be interpreted and governed by the laws of Nigeria, without regard to its conflict of law provisions. Any legal action or proceeding arising under these Terms will be brought exclusively in the courts of Nigeria.</p>
                    </section>

                    <section class="bg-violet-50 rounded-2xl p-6 border-l-4 border-violet-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-violet-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">13</span>
                            Changes to Terms
                        </h2>
                        <p class="text-gray-700 leading-relaxed">We reserve the right to modify these terms at any time. We will notify users of any material changes via email or through the service. Your continued use of the service after such modifications constitutes acceptance of the updated terms.</p>
                    </section>

                    <section class="bg-slate-50 rounded-2xl p-6 border-l-4 border-slate-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-slate-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">14</span>
                            Contact Information
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">If you have any questions about these Terms of Service, please contact us at:</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-slate-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="font-medium text-gray-900">Email</span>
                                </div>
                                <p class="text-gray-600">legal@ballie.ng</p>
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
