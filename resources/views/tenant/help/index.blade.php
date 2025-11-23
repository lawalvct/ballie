@extends('layouts.tenant')

@section('title', 'Help & Documentation')

@push('styles')
<style>[v-cloak] { display: none; }</style>
@endpush

@section('content')
<div id="helpApp" v-cloak>
    <help-header></help-header>
    <quick-links></quick-links>
    <help-content></help-content>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/vue@3.3.4/dist/vue.global.prod.js"></script>
<script>
const { createApp } = Vue;

createApp({
    components: {
        'help-header': {
            template: `
                <div class="container mx-auto px-4 py-8">
                    <div class="max-w-5xl mx-auto mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Help & Documentation</h1>
                        <p class="text-gray-600">Learn how to use Ballie to manage your business efficiently</p>
                    </div>
                </div>
            `
        },
        'quick-links': {
            template: `
                <div class="container mx-auto px-4">
                    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
                </div>
            `
        },
        'help-content': {
            template: `
                <div class="container mx-auto px-4 pb-8">
                    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow p-8">
                        <about-section></about-section>
                        <getting-started></getting-started>
                        <modules-overview></modules-overview>
                        <faq-section></faq-section>
                        <support-section></support-section>
                    </div>
                </div>
            `,
            components: {
                'about-section': {
                    template: `
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
                    `
                },
                'getting-started': {
                    template: `
                        <section id="getting-started" class="mb-12">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Getting Started</h2>
                            <div class="space-y-4">
                                <div v-for="(step, idx) in steps" :key="idx" class="border-l-4 border-green-500 pl-4">
                                    <h3 class="font-semibold text-lg mb-2" v-text="(idx + 1) + '. ' + step.title"></h3>
                                    <p class="text-gray-700" v-text="step.description"></p>
                                </div>
                            </div>
                        </section>
                    `,
                    data() {
                        return {
                            steps: [
                                { title: 'Complete Your Profile', description: 'Navigate to Settings → Company to update your business information, logo, and preferences.' },
                                { title: 'Set Up Your Chart of Accounts', description: 'Go to Accounting → Chart of Accounts to customize your accounting structure.' },
                                { title: 'Add Products & Services', description: 'Visit Inventory → Products to add your products and services.' },
                                { title: 'Add Customers & Vendors', description: 'Use CRM to add your customers and vendors for easy invoicing.' }
                            ]
                        }
                    }
                },
                'modules-overview': {
                    template: `
                        <section id="modules" class="mb-12">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Modules Overview</h2>
                            <div class="space-y-6">
                                <div v-for="module in modules" :key="module.name" class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-semibold text-lg mb-2" :class="module.color" v-text="module.icon + ' ' + module.name"></h3>
                                    <p class="text-gray-700" v-text="module.description"></p>
                                </div>
                            </div>
                        </section>
                    `,
                    data() {
                        return {
                            modules: [
                                { icon: '📊', name: 'Accounting', color: 'text-green-600', description: 'Manage invoices, vouchers, quotations, and financial transactions. Track income, expenses, and generate financial reports.' },
                                { icon: '📦', name: 'Inventory', color: 'text-purple-600', description: 'Track stock levels, manage products, categories, and units. Monitor stock movements and perform physical stock counts.' },
                                { icon: '👥', name: 'CRM', color: 'text-pink-600', description: 'Manage customer and vendor relationships. Track contact information, outstanding balances, and transaction history.' },
                                { icon: '🛒', name: 'POS', color: 'text-cyan-600', description: 'Point of Sale system for quick sales transactions. Manage cash registers and process payments efficiently.' },
                                { icon: '💰', name: 'Payroll', color: 'text-emerald-600', description: 'Manage employee salaries, deductions, and payroll runs. Calculate PAYE tax and generate payslips.' },
                                { icon: '👤', name: 'Admin', color: 'text-blue-600', description: 'Manage users, roles, and permissions. Control who has access to different parts of the system.' }
                            ]
                        }
                    }
                },
                'faq-section': {
                    template: `
                        <section id="faq" class="mb-12">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
                            <div class="space-y-4">
                                <div v-for="(faq, idx) in faqs" :key="idx" class="border-b pb-4">
                                    <h3 class="font-semibold text-lg mb-2" v-text="faq.question"></h3>
                                    <p class="text-gray-700" v-text="faq.answer"></p>
                                </div>
                            </div>
                        </section>
                    `,
                    data() {
                        return {
                            faqs: [
                                { question: 'How do I create an invoice?', answer: 'Go to Accounting → Invoices → Create New. Select a customer, add products, and save or post the invoice.' },
                                { question: "What's the difference between Draft and Posted?", answer: 'Draft documents can be edited or deleted. Posted documents are finalized and affect your accounts and inventory.' },
                                { question: 'How do I add team members?', answer: 'Navigate to Admin → Users → Add New User. Assign them a role to control their permissions.' },
                                { question: 'Can I customize my invoice template?', answer: 'Yes, go to Settings → Company to upload your logo and customize business information that appears on invoices.' },
                                { question: 'How do I track inventory?', answer: 'Enable "Maintain Stock" when creating products. Stock automatically updates when you post sales or purchase invoices.' }
                            ]
                        }
                    }
                },
                'support-section': {
                    template: `
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
                    `
                }
            }
        }
    }
}).mount('#helpApp');
</script>
@endpush
