@extends('layouts.tenant')

@section('title', 'Help & Documentation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Help & Documentation</h1>
            <p class="text-gray-600">Learn how to use Ballie to manage your business efficiently</p>
        </div>

        <!-- Quick Links -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="#getting-started" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <div class="text-blue-500 mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Getting Started</h3>
                <p class="text-gray-600 text-sm">Quick start guide for new users</p>
            </a>

            <a href="#modules" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <div class="text-green-500 mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Modules Guide</h3>
                <p class="text-gray-600 text-sm">Learn about each module</p>
            </a>

            <a href="#faq" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <div class="text-purple-500 mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">FAQ</h3>
                <p class="text-gray-600 text-sm">Frequently asked questions</p>
            </a>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-lg shadow p-8">
            <!-- About Ballie -->
            <section id="about" class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">About Ballie</h2>
                <p class="text-gray-700 mb-4">
                    Ballie is a comprehensive business management system designed to help small and medium-sized businesses
                    manage their operations efficiently. From accounting and inventory to CRM and payroll, Ballie provides
                    all the tools you need in one integrated platform.
                </p>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                    <p class="text-blue-700">
                        <strong>Multi-tenant Architecture:</strong> Each business operates in its own secure environment
                        with complete data isolation.
                    </p>
                </div>
            </section>

            <!-- Getting Started -->
            <section id="getting-started" class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Getting Started</h2>
                <div class="space-y-4">
                    <div class="border-l-4 border-green-500 pl-4">
                        <h3 class="font-semibold text-lg mb-2">1. Complete Your Profile</h3>
                        <p class="text-gray-700">Navigate to Settings → Company to update your business information, logo, and preferences.</p>
                    </div>
                    <div class="border-l-4 border-green-500 pl-4">
                        <h3 class="font-semibold text-lg mb-2">2. Set Up Your Chart of Accounts</h3>
                        <p class="text-gray-700">Go to Accounting → Chart of Accounts to customize your accounting structure.</p>
                    </div>
                    <div class="border-l-4 border-green-500 pl-4">
                        <h3 class="font-semibold text-lg mb-2">3. Add Products & Services</h3>
                        <p class="text-gray-700">Visit Inventory → Products to add your products and services.</p>
                    </div>
                    <div class="border-l-4 border-green-500 pl-4">
                        <h3 class="font-semibold text-lg mb-2">4. Add Customers & Vendors</h3>
                        <p class="text-gray-700">Use CRM to add your customers and vendors for easy invoicing.</p>
                    </div>
                </div>
            </section>

            <!-- Modules Overview -->
            <section id="modules" class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Modules Overview</h2>
                <div class="space-y-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-lg mb-2 text-green-600">📊 Accounting</h3>
                        <p class="text-gray-700">Manage invoices, vouchers, quotations, and financial transactions. Track income, expenses, and generate financial reports.</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-lg mb-2 text-purple-600">📦 Inventory</h3>
                        <p class="text-gray-700">Track stock levels, manage products, categories, and units. Monitor stock movements and perform physical stock counts.</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-lg mb-2 text-pink-600">👥 CRM</h3>
                        <p class="text-gray-700">Manage customer and vendor relationships. Track contact information, outstanding balances, and transaction history.</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-lg mb-2 text-cyan-600">🛒 POS</h3>
                        <p class="text-gray-700">Point of Sale system for quick sales transactions. Manage cash registers and process payments efficiently.</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-lg mb-2 text-emerald-600">💰 Payroll</h3>
                        <p class="text-gray-700">Manage employee salaries, deductions, and payroll runs. Calculate PAYE tax and generate payslips.</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-lg mb-2 text-blue-600">👤 Admin</h3>
                        <p class="text-gray-700">Manage users, roles, and permissions. Control who has access to different parts of the system.</p>
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <section id="faq" class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
                <div class="space-y-4">
                    <div class="border-b pb-4">
                        <h3 class="font-semibold text-lg mb-2">How do I create an invoice?</h3>
                        <p class="text-gray-700">Go to Accounting → Invoices → Create New. Select a customer, add products, and save or post the invoice.</p>
                    </div>
                    <div class="border-b pb-4">
                        <h3 class="font-semibold text-lg mb-2">What's the difference between Draft and Posted?</h3>
                        <p class="text-gray-700">Draft documents can be edited or deleted. Posted documents are finalized and affect your accounts and inventory.</p>
                    </div>
                    <div class="border-b pb-4">
                        <h3 class="font-semibold text-lg mb-2">How do I add team members?</h3>
                        <p class="text-gray-700">Navigate to Admin → Users → Add New User. Assign them a role to control their permissions.</p>
                    </div>
                    <div class="border-b pb-4">
                        <h3 class="font-semibold text-lg mb-2">Can I customize my invoice template?</h3>
                        <p class="text-gray-700">Yes, go to Settings → Company to upload your logo and customize business information that appears on invoices.</p>
                    </div>
                    <div class="border-b pb-4">
                        <h3 class="font-semibold text-lg mb-2">How do I track inventory?</h3>
                        <p class="text-gray-700">Enable "Maintain Stock" when creating products. Stock automatically updates when you post sales or purchase invoices.</p>
                    </div>
                </div>
            </section>

            <!-- Support -->
            <section id="support" class="bg-gradient-to-r from-blue-50 to-purple-50 p-6 rounded-lg">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Need More Help?</h2>
                <p class="text-gray-700 mb-4">
                    If you can't find the answer you're looking for, our support team is here to help.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="mailto:support@ballie.com" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Email Support
                    </a>
                    <a href="#" class="bg-white text-blue-600 border border-blue-600 px-6 py-2 rounded-lg hover:bg-blue-50 transition">
                        Video Tutorials
                    </a>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
