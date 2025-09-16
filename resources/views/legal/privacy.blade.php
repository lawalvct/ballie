@extends('layouts.app')

@section('title', 'Privacy Policy - Ballie')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Privacy Policy</h1>
                <p class="text-lg text-gray-600">Last updated: {{ date('F d, Y') }}</p>
            </div>

            <div class="prose prose-lg max-w-none">
                <h2>1. Introduction</h2>
                <p>Ballie ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our business management platform and related services.</p>

                <h2>2. Information We Collect</h2>

                <h3>2.1 Personal Information</h3>
                <p>We may collect personal information that you voluntarily provide, including:</p>
                <ul>
                    <li>Name, email address, and contact information</li>
                    <li>Business information and company details</li>
                    <li>Payment and billing information</li>
                    <li>Profile information and preferences</li>
                </ul>

                <h3>2.2 Business Data</h3>
                <p>Through your use of our platform, we may collect:</p>
                <ul>
                    <li>Financial and accounting data</li>
                    <li>Customer and vendor information</li>
                    <li>Inventory and product data</li>
                    <li>Transaction records and invoices</li>
                    <li>Reports and analytics data</li>
                </ul>

                <h3>2.3 Technical Information</h3>
                <p>We automatically collect certain technical information:</p>
                <ul>
                    <li>IP address, browser type, and device information</li>
                    <li>Usage data and interaction patterns</li>
                    <li>Log files and error reports</li>
                    <li>Cookies and similar tracking technologies</li>
                </ul>

                <h2>3. How We Use Your Information</h2>
                <p>We use the collected information for:</p>
                <ul>
                    <li>Providing and maintaining our services</li>
                    <li>Processing transactions and payments</li>
                    <li>Customer support and communication</li>
                    <li>Service improvement and feature development</li>
                    <li>Security monitoring and fraud prevention</li>
                    <li>Legal compliance and regulatory requirements</li>
                    <li>Marketing communications (with your consent)</li>
                </ul>

                <h2>4. Information Sharing and Disclosure</h2>
                <p>We do not sell, trade, or rent your personal information. We may share information in these limited circumstances:</p>

                <h3>4.1 Service Providers</h3>
                <p>We may share information with trusted third-party service providers who assist us in operating our platform, including:</p>
                <ul>
                    <li>Cloud hosting and data storage providers</li>
                    <li>Payment processors and financial institutions</li>
                    <li>Email and communication service providers</li>
                    <li>Analytics and monitoring services</li>
                </ul>

                <h3>4.2 Legal Requirements</h3>
                <p>We may disclose information when required by law or to:</p>
                <ul>
                    <li>Comply with legal obligations or court orders</li>
                    <li>Protect our rights, property, or safety</li>
                    <li>Investigate potential violations of our terms</li>
                    <li>Respond to government requests</li>
                </ul>

                <h3>4.3 Business Transfers</h3>
                <p>In the event of a merger, acquisition, or sale of assets, your information may be transferred as part of the business transaction.</p>

                <h2>5. Data Security</h2>
                <p>We implement appropriate security measures to protect your information:</p>
                <ul>
                    <li>Encryption of data in transit and at rest</li>
                    <li>Regular security assessments and updates</li>
                    <li>Access controls and authentication measures</li>
                    <li>Employee training on data protection</li>
                    <li>Incident response and breach notification procedures</li>
                </ul>

                <h2>6. Data Retention</h2>
                <p>We retain your information for as long as necessary to:</p>
                <ul>
                    <li>Provide our services to you</li>
                    <li>Comply with legal obligations</li>
                    <li>Resolve disputes and enforce agreements</li>
                    <li>Maintain business records as required</li>
                </ul>

                <h2>7. Your Rights and Choices</h2>
                <p>You have the following rights regarding your personal information:</p>
                <ul>
                    <li><strong>Access:</strong> Request a copy of the personal information we hold about you</li>
                    <li><strong>Correction:</strong> Request correction of inaccurate or incomplete information</li>
                    <li><strong>Deletion:</strong> Request deletion of your personal information (subject to legal requirements)</li>
                    <li><strong>Portability:</strong> Request a copy of your data in a portable format</li>
                    <li><strong>Opt-out:</strong> Unsubscribe from marketing communications</li>
                </ul>

                <h2>8. Cookies and Tracking Technologies</h2>
                <p>We use cookies and similar technologies to:</p>
                <ul>
                    <li>Remember your preferences and settings</li>
                    <li>Analyze usage patterns and improve our services</li>
                    <li>Provide personalized content and features</li>
                    <li>Maintain security and prevent fraud</li>
                </ul>
                <p>You can control cookies through your browser settings, but this may affect the functionality of our services.</p>

                <h2>9. Third-Party Services</h2>
                <p>Our platform may integrate with third-party services (payment processors, banks, etc.). These services have their own privacy policies, and we encourage you to review them.</p>

                <h2>10. International Data Transfers</h2>
                <p>Your information may be processed and stored in countries other than your own. We ensure appropriate safeguards are in place to protect your information in accordance with this Privacy Policy.</p>

                <h2>11. Children's Privacy</h2>
                <p>Our services are not intended for individuals under the age of 18. We do not knowingly collect personal information from children under 18.</p>

                <h2>12. Changes to This Privacy Policy</h2>
                <p>We may update this Privacy Policy from time to time. We will notify you of any material changes via email or through our platform. Your continued use of our services after such modifications constitutes acceptance of the updated Privacy Policy.</p>

                <h2>13. Contact Information</h2>
                <p>If you have any questions about this Privacy Policy or our data practices, please contact us:</p>
                <ul>
                    <li>Email: privacy@ballie.ng</li>
                    <li>Phone: +234 800 000 0000</li>
                    <li>Address: Lagos, Nigeria</li>
                    <li>Data Protection Officer: dpo@ballie.ng</li>
                </ul>

                <h2>14. Governing Law</h2>
                <p>This Privacy Policy is governed by the laws of Nigeria and applicable data protection regulations.</p>
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
