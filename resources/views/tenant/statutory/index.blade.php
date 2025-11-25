@extends('layouts.tenant')

@section('title', 'Statutory & Tax Management - ' . $tenant->name)
@section('page-title', "Statutory & Tax Management")
@section('page-description')
    <span class="hidden md:inline">
        Manage VAT, taxes, and statutory compliance
    </span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- VAT Output -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">VAT Output (Sales)</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">₦{{ number_format($vatOutput, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Current Month</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('tenant.statutory.vat.output', ['tenant' => $tenant->slug]) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    View Details →
                </a>
            </div>
        </div>

        <!-- VAT Input -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">VAT Input (Purchases)</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">₦{{ number_format($vatInput, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Current Month</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('tenant.statutory.vat.input', ['tenant' => $tenant->slug]) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    View Details →
                </a>
            </div>
        </div>

        <!-- Net VAT Payable -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Net VAT Payable</p>
                    <p class="text-2xl font-bold {{ $netVatPayable >= 0 ? 'text-gray-900' : 'text-red-600' }} mt-2">
                        ₦{{ number_format(abs($netVatPayable), 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        @if($netVatPayable >= 0)
                            To be paid to tax authority
                        @else
                            Refundable/Claimable
                        @endif
                    </p>
                </div>
                <div class="p-3 {{ $netVatPayable >= 0 ? 'bg-amber-100' : 'bg-red-100' }} rounded-full">
                    <svg class="w-6 h-6 {{ $netVatPayable >= 0 ? 'text-amber-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('tenant.statutory.vat.report', ['tenant' => $tenant->slug]) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    Generate Report →
                </a>
            </div>
        </div>

        <!-- Pension Contributions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pension Contributions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">₦{{ number_format($pensionTotal ?? 0, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Current Month</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('tenant.statutory.pension.report', ['tenant' => $tenant->slug]) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    View Details →
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('tenant.statutory.vat.output', ['tenant' => $tenant->slug]) }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0 p-2 bg-green-100 rounded-lg">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">VAT Output</p>
                        <p class="text-xs text-gray-500">Sales VAT collected</p>
                    </div>
                </a>

                <a href="{{ route('tenant.statutory.vat.input', ['tenant' => $tenant->slug]) }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0 p-2 bg-blue-100 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">VAT Input</p>
                        <p class="text-xs text-gray-500">Purchase VAT paid</p>
                    </div>
                </a>

                <a href="{{ route('tenant.statutory.vat.report', ['tenant' => $tenant->slug]) }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0 p-2 bg-amber-100 rounded-lg">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">VAT Report</p>
                        <p class="text-xs text-gray-500">Generate VAT return</p>
                    </div>
                </a>

                <a href="{{ route('tenant.statutory.settings', ['tenant' => $tenant->slug]) }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0 p-2 bg-gray-200 rounded-lg">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Tax Settings</p>
                        <p class="text-xs text-gray-500">Configure tax rates</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Information Notice -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">About VAT Management</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>The statutory module helps you manage Value Added Tax (VAT) at 7.5% rate. When creating invoices, you can enable VAT which will be automatically posted to the appropriate VAT account (VAT Output for sales, VAT Input for purchases).</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
