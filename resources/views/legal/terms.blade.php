@extends('layouts.app')

@section('title', 'Terms of Service - Ballie')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Terms of Service</h1>
                <p class="text-lg text-gray-600">Last updated: {{ date('F d, Y') }}</p>
            </div>

            <div class="prose prose-lg max-w-none">
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing and using Ballie ("the Service"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>

                <h2>2. Description of Service</h2>
                <p>Ballie is a comprehensive business management platform that provides accounting, inventory management, CRM, POS, and other business tools. We reserve the right to modify, suspend, or discontinue any part of our service at any time.</p>

                <h2>3. User Accounts</h2>
                <p>To use our service, you must:</p>
                <ul>
                    <li>Provide accurate, current, and complete information during registration</li>
                    <li>Maintain the security of your password and account</li>
                    <li>Promptly update any changes to your account information</li>
                    <li>Accept responsibility for all activities under your account</li>
                </ul>

                <h2>4. Acceptable Use Policy</h2>
                <p>You agree not to use the service to:</p>
                <ul>
                    <li>Upload, post, or transmit any illegal, harmful, or inappropriate content</li>
                    <li>Violate any applicable laws or regulations</li>
                    <li>Interfere with or disrupt the service or servers</li>
                    <li>Attempt to gain unauthorized access to any part of the service</li>
                    <li>Use the service for any commercial purpose without our consent</li>
                </ul>

                <h2>5. Data and Privacy</h2>
                <p>Your privacy is important to us. Our collection and use of personal information is governed by our Privacy Policy. By using our service, you consent to the collection and use of your information as outlined in our Privacy Policy.</p>

                <h2>6. Payment Terms</h2>
                <p>For paid services:</p>
                <ul>
                    <li>Fees are charged in advance on a monthly or annual basis</li>
                    <li>All fees are non-refundable except as required by law</li>
                    <li>We reserve the right to change our pricing with 30 days notice</li>
                    <li>Failure to pay may result in service suspension or termination</li>
                </ul>

                <h2>7. Free Trial</h2>
                <p>We may offer a free trial period. During the trial:</p>
                <ul>
                    <li>You have access to premium features at no cost</li>
                    <li>The trial automatically converts to a paid subscription unless cancelled</li>
                    <li>We may limit certain features or usage during the trial period</li>
                </ul>

                <h2>8. Intellectual Property</h2>
                <p>The service and its original content, features, and functionality are owned by Ballie and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.</p>

                <h2>9. Termination</h2>
                <p>We may terminate or suspend your account and access to the service immediately, without prior notice, for conduct that we believe violates these Terms of Service or is harmful to other users, us, or third parties, or for any other reason.</p>

                <h2>10. Disclaimer of Warranties</h2>
                <p>The service is provided on an "AS IS" and "AS AVAILABLE" basis. We disclaim all warranties, whether express or implied, including warranties of merchantability, fitness for a particular purpose, and non-infringement.</p>

                <h2>11. Limitation of Liability</h2>
                <p>In no event shall Ballie be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses.</p>

                <h2>12. Governing Law</h2>
                <p>These Terms shall be interpreted and governed by the laws of Nigeria, without regard to its conflict of law provisions. Any legal action or proceeding arising under these Terms will be brought exclusively in the courts of Nigeria.</p>

                <h2>13. Changes to Terms</h2>
                <p>We reserve the right to modify these terms at any time. We will notify users of any material changes via email or through the service. Your continued use of the service after such modifications constitutes acceptance of the updated terms.</p>

                <h2>14. Contact Information</h2>
                <p>If you have any questions about these Terms of Service, please contact us at:</p>
                <ul>
                    <li>Email: legal@ballie.ng</li>
                    <li>Phone: +234 800 000 0000</li>
                    <li>Address: Lagos, Nigeria</li>
                </ul>
            </div>

            <div class="mt-12 text-center">
                <a href="javascript:history.back()" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Go Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
