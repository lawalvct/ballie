@extends('layouts.tenant')

@section('title')@term('statutory') Management - {{ $tenant->name }}@endsection
@section('page-title')@term('statutory') Management @endsection
@section('page-description')
    <span class="hidden md:inline">
        Manage VAT, taxes, and statutory compliance
    </span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Overdue Alert -->
    @if($overdueFilingsCount > 0)
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ $overdueFilingsCount }} overdue tax filing(s) require attention.</p>
            </div>
            <div class="ml-auto">
                <a href="{{ route('tenant.statutory.filings.index', ['tenant' => $tenant->slug]) }}" class="text-sm font-medium text-red-700 hover:text-red-900 underline">View Filings</a>
            </div>
        </div>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <!-- VAT Output -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600">VAT Output</p>
            <p class="text-xl font-bold text-gray-900 mt-1">₦{{ number_format($vatOutput, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Current Month</p>
            <a href="{{ route('tenant.statutory.vat.output', ['tenant' => $tenant->slug]) }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium mt-2 inline-block">View Details →</a>
        </div>

        <!-- VAT Input -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600">VAT Input</p>
            <p class="text-xl font-bold text-gray-900 mt-1">₦{{ number_format($vatInput, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Current Month</p>
            <a href="{{ route('tenant.statutory.vat.input', ['tenant' => $tenant->slug]) }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium mt-2 inline-block">View Details →</a>
        </div>

        <!-- Net VAT Payable -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 {{ $netVatPayable >= 0 ? 'bg-amber-100' : 'bg-red-100' }} rounded-lg">
                    <svg class="w-5 h-5 {{ $netVatPayable >= 0 ? 'text-amber-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600">Net VAT Payable</p>
            <p class="text-xl font-bold {{ $netVatPayable >= 0 ? 'text-gray-900' : 'text-red-600' }} mt-1">₦{{ number_format(abs($netVatPayable), 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $netVatPayable >= 0 ? 'Payable' : 'Refundable' }}</p>
            <a href="{{ route('tenant.statutory.vat.report', ['tenant' => $tenant->slug]) }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium mt-2 inline-block">VAT Report →</a>
        </div>

        <!-- PAYE Tax -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600">PAYE Tax</p>
            <p class="text-xl font-bold text-gray-900 mt-1">₦{{ number_format($payeTaxTotal ?? 0, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Current Month</p>
            <a href="{{ route('tenant.statutory.paye.report', ['tenant' => $tenant->slug]) }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium mt-2 inline-block">View Report →</a>
        </div>

        <!-- Pension -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600">Pension</p>
            <p class="text-xl font-bold text-gray-900 mt-1">₦{{ number_format($pensionTotal ?? 0, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Current Month</p>
            <a href="{{ route('tenant.statutory.pension.report', ['tenant' => $tenant->slug]) }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium mt-2 inline-block">View Report →</a>
        </div>

        <!-- NSITF -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-amber-100 rounded-lg">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600">NSITF</p>
            <p class="text-xl font-bold text-gray-900 mt-1">₦{{ number_format($nsitfTotal ?? 0, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Current Month (1%)</p>
            <a href="{{ route('tenant.statutory.nsitf.report', ['tenant' => $tenant->slug]) }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium mt-2 inline-block">View Report →</a>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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

                <a href="{{ route('tenant.statutory.paye.report', ['tenant' => $tenant->slug]) }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0 p-2 bg-indigo-100 rounded-lg">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">PAYE Report</p>
                        <p class="text-xs text-gray-500">Employee income tax</p>
                    </div>
                </a>

                <a href="{{ route('tenant.statutory.pension.report', ['tenant' => $tenant->slug]) }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0 p-2 bg-purple-100 rounded-lg">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Pension Report</p>
                        <p class="text-xs text-gray-500">PFA contributions</p>
                    </div>
                </a>

                <a href="{{ route('tenant.statutory.nsitf.report', ['tenant' => $tenant->slug]) }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0 p-2 bg-amber-100 rounded-lg">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">NSITF Report</p>
                        <p class="text-xs text-gray-500">Social insurance (1%)</p>
                    </div>
                </a>

                <a href="{{ route('tenant.statutory.filings.index', ['tenant' => $tenant->slug]) }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0 p-2 bg-teal-100 rounded-lg">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Filing History</p>
                        <p class="text-xs text-gray-500">Compliance tracking</p>
                    </div>
                </a>

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
                <h3 class="text-sm font-medium text-blue-800">About Statutory Compliance</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>This module tracks all statutory obligations: VAT (7.5%), PAYE income tax (graduated), Pension (8% employee + 10% employer), and NSITF (1%). Use the Filing History to track due dates and payment status for regulatory compliance.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
