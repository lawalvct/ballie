@extends('layouts.tenant')

@section('title', 'Vendor Details')
@section('page-title', 'Vendor Details')
@section('page-description', 'View vendor information and transaction history.')

@section('content')
<div class="space-y-6">
    <!-- Vendor Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 rounded-2xl p-8 text-white shadow-2xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                    <span class="text-3xl font-bold text-white">
                        {{ substr($vendor->vendor_type == 'individual' ? ($vendor->first_name ?? 'V') : ($vendor->company_name ?? 'V'), 0, 1) }}
                    </span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold mb-2">
                        @if($vendor->vendor_type == 'individual')
                            {{ $vendor->first_name }} {{ $vendor->last_name }}
                        @else
                            {{ $vendor->company_name }}
                        @endif
                    </h1>
                    <div class="flex items-center space-x-4 text-purple-100">
                        <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm font-medium">
                            {{ ucfirst($vendor->vendor_type) }}
                        </span>
                        <span class="flex items-center">
                            <div class="w-2 h-2 {{ $vendor->status == 'active' ? 'bg-green-400' : 'bg-red-400' }} rounded-full mr-2"></div>
                            {{ ucfirst($vendor->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('tenant.vendors.edit', ['tenant' => tenant()->slug, 'vendor' => $vendor->id]) }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 backdrop-blur-sm border border-white border-opacity-20">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Vendor
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Total Purchases</h3>
                    <p class="text-2xl font-bold text-blue-600">₦{{ number_format($vendor->total_purchases ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Outstanding</h3>
                    <p class="text-2xl font-bold text-red-600">₦{{ number_format($vendor->outstanding_amount ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Total Orders</h3>
                    <p class="text-2xl font-bold text-green-600">{{ $vendor->total_orders ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Last Purchase</h3>
                    <p class="text-sm text-gray-600">{{ $vendor->last_purchase_date ? date('M d, Y', strtotime($vendor->last_purchase_date)) : 'Never' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Vendor Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Information -->
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Contact Information
                </h3>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <p class="text-gray-900">{{ $vendor->email ?: 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <p class="text-gray-900">{{ $vendor->phone ?: 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
                        <p class="text-gray-900">{{ $vendor->mobile ?: 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                        @if($vendor->website)
                            <a href="{{ $vendor->website }}" target="_blank" class="text-purple-600 hover:text-purple-800 hover:underline">
                                {{ $vendor->website }}
                            </a>
                        @else
                            <p class="text-gray-900">Not provided</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            @if($vendor->address_line1 || $vendor->city || $vendor->state || $vendor->country)
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Address Information
                    </h3>
                    <div class="space-y-2">
                        @if($vendor->address_line1)
                            <p class="text-gray-900">{{ $vendor->address_line1 }}</p>
                        @endif
                        @if($vendor->address_line2)
                            <p class="text-gray-900">{{ $vendor->address_line2 }}</p>
                        @endif
                        @if($vendor->city || $vendor->state || $vendor->postal_code)
                            <p class="text-gray-900">
                                {{ $vendor->city }}{{ $vendor->city && ($vendor->state || $vendor->postal_code) ? ', ' : '' }}
                                {{ $vendor->state }}{{ $vendor->state && $vendor->postal_code ? ' ' : '' }}
                                {{ $vendor->postal_code }}
                            </p>
                        @endif
                        @if($vendor->country)
                            <p class="text-gray-900">{{ $vendor->country }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Banking Information -->
            @if($vendor->bank_name || $vendor->bank_account_number)
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Banking Information
                    </h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                            <p class="text-gray-900">{{ $vendor->bank_name ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                            <p class="text-gray-900">{{ $vendor->bank_account_number ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                            <p class="text-gray-900">{{ $vendor->bank_account_name ?: 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Business Details (for business vendors) -->
            @if($vendor->vendor_type == 'business')
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m4 0v-3.5a1.5 1.5 0 013 0V21m-4-3h4"></path>
                        </svg>
                        Business Details
                    </h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tax ID</label>
                            <p class="text-gray-900">{{ $vendor->tax_id ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                            <p class="text-gray-900">{{ $vendor->registration_number ?: 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Transactions -->
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Recent Transactions
                    </h3>
                    <a href="#" class="text-sm font-medium text-purple-600 hover:text-purple-800">View All</a>
                </div>

                <div class="space-y-4">
                    <!-- Placeholder for transactions - this would come from your actual transaction data -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Purchase Order #PO-2024-001</p>
                                <p class="text-sm text-gray-500">Office Supplies</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-red-600">-₦85,000</p>
                            <p class="text-xs text-gray-500">2 days ago</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Payment Made</p>
                                <p class="text-sm text-gray-500">Invoice #INV-2024-045</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-green-600">+₦125,000</p>
                            <p class="text-xs text-gray-500">5 days ago</p>
                        </div>
                    </div>

                    <div class="text-center py-4">
                        <p class="text-sm text-gray-500">No recent transactions</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Financial Summary -->
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Financial Summary</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Currency</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $vendor->currency ?: 'NGN' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Payment Terms</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $vendor->payment_terms ?: 'Not specified' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Credit Limit</span>
                        <span class="text-sm font-semibold text-gray-900">₦{{ number_format($vendor->credit_limit ?? 0, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $vendor->credit_limit > 0 ? min(100, (($vendor->outstanding_amount ?? 0) / $vendor->credit_limit) * 100) : 0 }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500">Credit utilization</p>
                </div>
            </div>

            <!-- Ledger Account Info -->
            @if($vendor->ledgerAccount)
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Ledger Account</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Account Code</p>
                            <p class="font-semibold text-gray-900">{{ $vendor->ledgerAccount->code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Account Name</p>
                            <p class="font-semibold text-gray-900">{{ $vendor->ledgerAccount->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Current Balance</p>
                            <p class="font-semibold {{ $vendor->ledgerAccount->getCurrentBalance() >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ₦{{ number_format(abs($vendor->ledgerAccount->getCurrentBalance()), 2) }}
                                {{ $vendor->ledgerAccount->getCurrentBalance() >= 0 ? 'CR' : 'DR' }}
                            </p>
                        </div>
                        <a href="#" class="inline-flex items-center text-sm text-purple-600 hover:text-purple-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Ledger Details
                        </a>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button class="w-full text-left px-4 py-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors duration-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <span class="font-medium text-gray-900">Create Purchase Order</span>
                        </div>
                    </button>

                    <button class="w-full text-left px-4 py-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors duration-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="font-medium text-gray-900">Record Payment</span>
                        </div>
                    </button>

                    <button class="w-full text-left px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="font-medium text-gray-900">View Reports</span>
                        </div>
                    </button>

                    <a href="mailto:{{ $vendor->email }}" class="w-full block text-left px-4 py-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors duration-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="font-medium text-gray-900">Send Email</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Notes -->
            @if($vendor->notes)
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Notes</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 text-sm leading-relaxed">{{ $vendor->notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Vendor Timeline -->
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Vendor Timeline</h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Vendor Created</p>
                            <p class="text-xs text-gray-500">{{ $vendor->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    @if($vendor->updated_at != $vendor->created_at)
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Last Updated</p>
                                <p class="text-xs text-gray-500">{{ $vendor->updated_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($vendor->last_purchase_date)
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Last Purchase</p>
                                <p class="text-xs text-gray-500">{{ date('M d, Y', strtotime($vendor->last_purchase_date)) }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add animations to cards
    const cards = document.querySelectorAll('.bg-white');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        card.style.transitionDelay = (index * 0.1) + 's';

        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    });

    // Add hover effects to quick action buttons
    const actionButtons = document.querySelectorAll('[class*="hover:bg-"]');
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(4px)';
            this.style.transition = 'transform 0.2s ease';
        });

        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Add click ripple effect to action buttons
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});
</script>

<style>
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

/* Custom scrollbar for transaction section */
.space-y-4::-webkit-scrollbar {
    width: 6px;
}

.space-y-4::-webkit-scrollbar-track {
    background: rgba(243, 244, 246, 0.8);
    border-radius: 3px;
}

.space-y-4::-webkit-scrollbar-thumb {
    background: rgba(147, 51, 234, 0.3);
    border-radius: 3px;
}

.space-y-4::-webkit-scrollbar-thumb:hover {
    background: rgba(147, 51, 234, 0.5);
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .lg\:col-span-2 {
        grid-column: span 1;
    }
}

@media (max-width: 768px) {
    .grid-cols-4 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .grid-cols-4 {
        grid-template-columns: minmax(0, 1fr);
    }

    .grid-cols-2 {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>
@endsection