@extends('layouts.app')

@section('title', 'Privacy Policy - Ballie')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8 sm:py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-green-600 to-emerald-700 px-8 py-12 text-center">
                <div class="max-w-3xl mx-auto">
                    <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4">Privacy Policy</h1>
                    <p class="text-xl text-green-100 mb-2">Your Privacy Matters to Us</p>
                    <p class="text-green-200">Last updated: {{ date('F d, Y') }}</p>
                </div>
            </div>

            <!-- Content Section -->
            <div class="px-8 py-12 md:px-16">
                <div class="prose prose-lg max-w-none">
                <div class="space-y-8">
                    <section class="bg-green-50 rounded-2xl p-6 border-l-4 border-green-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">1</span>
                            Introduction
                        </h2>
                        <p class="text-gray-700 leading-relaxed">Ballie ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our business management platform and related services.</p>
                    </section>

                    <section class="bg-blue-50 rounded-2xl p-6 border-l-4 border-blue-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">2</span>
                            Information We Collect
                        </h2>

                        <div class="space-y-6">
                            <div class="bg-white rounded-xl p-5 border border-blue-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <span class="bg-blue-100 text-blue-600 rounded-lg w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">2.1</span>
                                    Personal Information
                                </h3>
                                <p class="text-gray-700 mb-3">We may collect personal information that you voluntarily provide, including:</p>
                                <ul class="space-y-2 text-gray-700">
                                    <li class="flex items-start"><span class="text-blue-500 mr-2 mt-1">•</span>Name, email address, and contact information</li>
                                    <li class="flex items-start"><span class="text-blue-500 mr-2 mt-1">•</span>Business information and company details</li>
                                    <li class="flex items-start"><span class="text-blue-500 mr-2 mt-1">•</span>Payment and billing information</li>
                                    <li class="flex items-start"><span class="text-blue-500 mr-2 mt-1">•</span>Profile information and preferences</li>
                                </ul>
                            </div>

                            <div class="bg-white rounded-xl p-5 border border-blue-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <span class="bg-blue-100 text-blue-600 rounded-lg w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">2.2</span>
                                    Business Data
                                </h3>
                                <p class="text-gray-700 mb-3">Through your use of our platform, we may collect:</p>
                                <ul class="space-y-2 text-gray-700">
                                    <li class="flex items-start"><span class="text-blue-500 mr-2 mt-1">•</span>Financial and accounting data</li>
                                    <li class="flex items-start"><span class="text-blue-500 mr-2 mt-1">•</span>Customer and vendor information</li>
                                    <li class="flex items-start"><span class="text-blue-500 mr-2 mt-1">•</span>Inventory and product data</li>
                                    <li class="flex items-start"><span class="text-blue-500 mr-2 mt-1">•</span>Transaction records and invoices</li>
                                    <li class="flex items-start"><span class="text-blue-500 mr-2 mt-1">•</span>Reports and analytics data</li>
                                </ul>
                            </div>

                            <div class="bg-white rounded-xl p-5 border border-blue-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <span class="bg-blue-100 text-blue-600 rounded-lg w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">2.3</span>
                                    Technical Information
                                </h3>
                                <p class="text-gray-700 mb-3">We automatically collect certain technical information:</p>
                                <ul class="space-y-2 text-gray-700">
                                    <li class="flex items-start"><span class="text-blue-500 mr-2 mt-1">•</span>IP address, browser type, and device information</li>
                                    <li class="flex items-start"><span class="text-blue-500 mr-2 mt-1">•</span>Usage data and interaction patterns</li>
                                    <li class="flex items-start"><span class="text-blue-500 mr-2 mt-1">•</span>Log files and error reports</li>
                                    <li class="flex items-start"><span class="text-blue-500 mr-2 mt-1">•</span>Cookies and similar tracking technologies</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <section class="bg-purple-50 rounded-2xl p-6 border-l-4 border-purple-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-purple-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">3</span>
                            How We Use Your Information
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">We use the collected information for:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start"><span class="text-purple-500 mr-2 mt-1">•</span>Providing and maintaining our services</li>
                            <li class="flex items-start"><span class="text-purple-500 mr-2 mt-1">•</span>Processing transactions and payments</li>
                            <li class="flex items-start"><span class="text-purple-500 mr-2 mt-1">•</span>Customer support and communication</li>
                            <li class="flex items-start"><span class="text-purple-500 mr-2 mt-1">•</span>Service improvement and feature development</li>
                            <li class="flex items-start"><span class="text-purple-500 mr-2 mt-1">•</span>Security monitoring and fraud prevention</li>
                            <li class="flex items-start"><span class="text-purple-500 mr-2 mt-1">•</span>Legal compliance and regulatory requirements</li>
                            <li class="flex items-start"><span class="text-purple-500 mr-2 mt-1">•</span>Marketing communications (with your consent)</li>
                        </ul>
                    </section>

                    <section class="bg-orange-50 rounded-2xl p-6 border-l-4 border-orange-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <span class="bg-orange-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">4</span>
                            Information Sharing and Disclosure
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-6">We do not sell, trade, or rent your personal information. We may share information in these limited circumstances:</p>

                        <div class="space-y-6">
                            <div class="bg-white rounded-xl p-5 border border-orange-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <span class="bg-orange-100 text-orange-600 rounded-lg w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">4.1</span>
                                    Service Providers
                                </h3>
                                <p class="text-gray-700 mb-3">We may share information with trusted third-party service providers who assist us in operating our platform, including:</p>
                                <ul class="space-y-2 text-gray-700">
                                    <li class="flex items-start"><span class="text-orange-500 mr-2 mt-1">•</span>Cloud hosting and data storage providers</li>
                                    <li class="flex items-start"><span class="text-orange-500 mr-2 mt-1">•</span>Payment processors and financial institutions</li>
                                    <li class="flex items-start"><span class="text-orange-500 mr-2 mt-1">•</span>Email and communication service providers</li>
                                    <li class="flex items-start"><span class="text-orange-500 mr-2 mt-1">•</span>Analytics and monitoring services</li>
                                </ul>
                            </div>

                            <div class="bg-white rounded-xl p-5 border border-orange-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <span class="bg-orange-100 text-orange-600 rounded-lg w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">4.2</span>
                                    Legal Requirements
                                </h3>
                                <p class="text-gray-700 mb-3">We may disclose information when required by law or to:</p>
                                <ul class="space-y-2 text-gray-700">
                                    <li class="flex items-start"><span class="text-orange-500 mr-2 mt-1">•</span>Comply with legal obligations or court orders</li>
                                    <li class="flex items-start"><span class="text-orange-500 mr-2 mt-1">•</span>Protect our rights, property, or safety</li>
                                    <li class="flex items-start"><span class="text-orange-500 mr-2 mt-1">•</span>Investigate potential violations of our terms</li>
                                    <li class="flex items-start"><span class="text-orange-500 mr-2 mt-1">•</span>Respond to government requests</li>
                                </ul>
                            </div>

                            <div class="bg-white rounded-xl p-5 border border-orange-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <span class="bg-orange-100 text-orange-600 rounded-lg w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">4.3</span>
                                    Business Transfers
                                </h3>
                                <p class="text-gray-700">In the event of a merger, acquisition, or sale of assets, your information may be transferred as part of the business transaction.</p>
                            </div>
                        </div>
                    </section>

                    <section class="bg-red-50 rounded-2xl p-6 border-l-4 border-red-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">5</span>
                            Data Security
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">We implement appropriate security measures to protect your information:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start"><span class="text-red-500 mr-2 mt-1">•</span>Encryption of data in transit and at rest</li>
                            <li class="flex items-start"><span class="text-red-500 mr-2 mt-1">•</span>Regular security assessments and updates</li>
                            <li class="flex items-start"><span class="text-red-500 mr-2 mt-1">•</span>Access controls and authentication measures</li>
                            <li class="flex items-start"><span class="text-red-500 mr-2 mt-1">•</span>Employee training on data protection</li>
                            <li class="flex items-start"><span class="text-red-500 mr-2 mt-1">•</span>Incident response and breach notification procedures</li>
                        </ul>
                    </section>

                    <section class="bg-yellow-50 rounded-2xl p-6 border-l-4 border-yellow-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-yellow-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">6</span>
                            Data Retention
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">We retain your information for as long as necessary to:</p>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start"><span class="text-yellow-500 mr-2 mt-1">•</span>Provide our services to you</li>
                            <li class="flex items-start"><span class="text-yellow-500 mr-2 mt-1">•</span>Comply with legal obligations</li>
                            <li class="flex items-start"><span class="text-yellow-500 mr-2 mt-1">•</span>Resolve disputes and enforce agreements</li>
                            <li class="flex items-start"><span class="text-yellow-500 mr-2 mt-1">•</span>Maintain business records as required</li>
                        </ul>
                    </section>

                    <section class="bg-indigo-50 rounded-2xl p-6 border-l-4 border-indigo-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-indigo-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">7</span>
                            Your Rights and Choices
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">You have the following rights regarding your personal information:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white rounded-lg p-4 border border-indigo-200">
                                <h4 class="font-semibold text-indigo-700 mb-2">Access</h4>
                                <p class="text-gray-600 text-sm">Request a copy of the personal information we hold about you</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-indigo-200">
                                <h4 class="font-semibold text-indigo-700 mb-2">Correction</h4>
                                <p class="text-gray-600 text-sm">Request correction of inaccurate or incomplete information</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-indigo-200">
                                <h4 class="font-semibold text-indigo-700 mb-2">Deletion</h4>
                                <p class="text-gray-600 text-sm">Request deletion of your personal information (subject to legal requirements)</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-indigo-200">
                                <h4 class="font-semibold text-indigo-700 mb-2">Portability</h4>
                                <p class="text-gray-600 text-sm">Request a copy of your data in a portable format</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-indigo-200 md:col-span-2">
                                <h4 class="font-semibold text-indigo-700 mb-2">Opt-out</h4>
                                <p class="text-gray-600 text-sm">Unsubscribe from marketing communications</p>
                            </div>
                        </div>
                    </section>

                    <section class="bg-teal-50 rounded-2xl p-6 border-l-4 border-teal-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-teal-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">8</span>
                            Cookies and Tracking Technologies
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">We use cookies and similar technologies to:</p>
                        <ul class="space-y-2 text-gray-700 mb-4">
                            <li class="flex items-start"><span class="text-teal-500 mr-2 mt-1">•</span>Remember your preferences and settings</li>
                            <li class="flex items-start"><span class="text-teal-500 mr-2 mt-1">•</span>Analyze usage patterns and improve our services</li>
                            <li class="flex items-start"><span class="text-teal-500 mr-2 mt-1">•</span>Provide personalized content and features</li>
                            <li class="flex items-start"><span class="text-teal-500 mr-2 mt-1">•</span>Maintain security and prevent fraud</li>
                        </ul>
                        <p class="text-gray-700 leading-relaxed">You can control cookies through your browser settings, but this may affect the functionality of our services.</p>
                    </section>

                    <section class="bg-pink-50 rounded-2xl p-6 border-l-4 border-pink-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-pink-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">9</span>
                            Third-Party Services
                        </h2>
                        <p class="text-gray-700 leading-relaxed">Our platform may integrate with third-party services (payment processors, banks, etc.). These services have their own privacy policies, and we encourage you to review them.</p>
                    </section>

                    <section class="bg-cyan-50 rounded-2xl p-6 border-l-4 border-cyan-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-cyan-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">10</span>
                            International Data Transfers
                        </h2>
                        <p class="text-gray-700 leading-relaxed">Your information may be processed and stored in countries other than your own. We ensure appropriate safeguards are in place to protect your information in accordance with this Privacy Policy.</p>
                    </section>

                    <section class="bg-emerald-50 rounded-2xl p-6 border-l-4 border-emerald-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-emerald-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">11</span>
                            Children's Privacy
                        </h2>
                        <p class="text-gray-700 leading-relaxed">Our services are not intended for individuals under the age of 18. We do not knowingly collect personal information from children under 18.</p>
                    </section>

                    <section class="bg-violet-50 rounded-2xl p-6 border-l-4 border-violet-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-violet-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">12</span>
                            Changes to This Privacy Policy
                        </h2>
                        <p class="text-gray-700 leading-relaxed">We may update this Privacy Policy from time to time. We will notify you of any material changes via email or through our platform. Your continued use of our services after such modifications constitutes acceptance of the updated Privacy Policy.</p>
                    </section>

                    <section class="bg-slate-50 rounded-2xl p-6 border-l-4 border-slate-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-slate-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">13</span>
                            Contact Information
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-4">If you have any questions about this Privacy Policy or our data practices, please contact us:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-slate-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    <span class="font-medium text-gray-900">Data Protection Officer</span>
                                </div>
                                <p class="text-gray-600">dpo@ballie.ng</p>
                            </div>
                        </div>
                    </section>
                </div>

                    <section class="bg-gray-50 rounded-2xl p-6 border-l-4 border-gray-500">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-gray-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">14</span>
                            Governing Law
                        </h2>
                        <p class="text-gray-700 leading-relaxed">This Privacy Policy is governed by the laws of Nigeria and applicable data protection regulations.</p>
                    </section>
            </div>

            <!-- Footer Section -->
            <div class="bg-gray-50 px-8 py-8 text-center border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <a href="javascript:history.back()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
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
                </div>
                <p class="text-sm text-gray-500 mt-4">© {{ date('Y') }} Ballie. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>
@endsection
