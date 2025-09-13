@extends('layouts.tenant')

@section('title', 'Invoice #' . $payment->payment_reference)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 print:hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Invoice #{{ $payment->payment_reference }}</h1>
                <p class="text-gray-600 mt-1">Generated on {{ $payment->created_at->format('M j, Y') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('tenant.subscription.history', tenant()->slug) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>

                <button onclick="window.print()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>

                <a href="{{ route('tenant.subscription.invoice.download', ['tenant' => tenant()->slug, 'payment' => $payment->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download
                </a>
            </div>
        </div>
    </div>

    <!-- Invoice Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 print:shadow-none print:border-none invoice-animation" id="invoice-content">
        <div class="p-8 print:p-0">
            <!-- Invoice Header -->
            <div class="flex flex-col lg:flex-row justify-between items-start mb-8 print:mb-6 gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-4">
                        <h1 class="text-4xl font-bold text-gray-900 print:text-3xl">INVOICE</h1>
                        <div class="px-3 py-1 rounded-full text-sm font-medium
                            @if($payment->status === 'successful') bg-green-100 text-green-800
                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($payment->status === 'failed') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($payment->status) }}
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <h2 class="text-xl font-semibold text-gray-900">{{ config('app.name') }}</h2>
                        @if($businessInfo ?? false)
                        <div class="text-gray-600 space-y-1">
                            @if($businessInfo['address'] ?? false)
                            <p>{{ $businessInfo['address'] }}</p>
                            @endif
                            <div class="flex flex-wrap gap-4 text-sm">
                                @if($businessInfo['phone'] ?? false)
                                <span>📞 {{ $businessInfo['phone'] }}</span>
                                @endif
                                @if($businessInfo['email'] ?? false)
                                <span>✉️ {{ $businessInfo['email'] }}</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg border-l-4 border-blue-500 min-w-[280px]">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Invoice Number</p>
                            <p class="text-lg font-bold text-gray-900">#{{ $payment->payment_reference }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-600">Invoice Date</p>
                            <p class="font-semibold text-gray-900">{{ $payment->created_at->format('M j, Y') }}</p>
                        </div>

                        @if($payment->paid_at)
                        <div>
                            <p class="text-sm font-medium text-gray-600">Payment Date</p>
                            <p class="font-semibold text-gray-900">{{ $payment->paid_at->format('M j, Y') }}</p>
                        </div>
                        @endif

                        <div>
                            <p class="text-sm font-medium text-gray-600">Amount Due</p>
                            <p class="text-xl font-bold text-blue-600">{{ $payment->formatted_amount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 print:mb-6">
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Bill To
                    </h3>
                    <div class="space-y-2">
                        <p class="font-semibold text-gray-900 text-lg">{{ $tenant->company_name ?? $tenant->name }}</p>
                        @if($tenant->email)
                        <p class="text-gray-700 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                            {{ $tenant->email }}
                        </p>
                        @endif
                        @if($tenant->phone)
                        <p class="text-gray-700 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $tenant->phone }}
                        </p>
                        @endif
                        @if($tenant->address)
                        <p class="text-gray-700 flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $tenant->address }}
                            @if($tenant->city || $tenant->state)
                            <br>{{ implode(', ', array_filter([$tenant->city, $tenant->state])) }}
                            @endif
                            </span>
                        </p>
                        @endif
                    </div>
                </div>

                <div class="bg-blue-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Payment Information
                    </h3>
                    <div class="space-y-3">
                        @if($payment->paid_at)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Paid on:</span>
                            <span class="font-medium text-gray-900">{{ $payment->paid_at->format('M j, Y') }}</span>
                        </div>
                        @endif

                        @if($payment->payment_method)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Method:</span>
                            <span class="font-medium text-gray-900">{{ ucfirst($payment->payment_method) }}</span>
                        </div>
                        @endif

                        @if($payment->gateway_reference)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Reference:</span>
                            <span class="font-mono text-sm text-gray-900">{{ $payment->gateway_reference }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="mb-8 print:mb-6">
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Invoice Details</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="text-left py-4 px-6 font-semibold text-gray-700">Description</th>
                                    <th class="text-center py-4 px-6 font-semibold text-gray-700">Period</th>
                                    <th class="text-right py-4 px-6 font-semibold text-gray-700">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @if($payment->subscription)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-6 px-6">
                                            <div>
                                                <p class="font-semibold text-gray-900 text-lg">
                                                    @if($payment->subscription && $payment->subscription->plan && is_object($payment->subscription->plan))
                                                        {{ $payment->subscription->plan->name }}
                                                    @else
                                                        Subscription Plan
                                                    @endif
                                                </p>
                                                <p class="text-gray-600 mt-1">
                                                    {{ ucfirst($payment->subscription->billing_cycle ?? 'monthly') }} subscription
                                                    @if($payment->subscription && $payment->subscription->plan && is_object($payment->subscription->plan) && $payment->subscription->plan->description)
                                                    • {{ $payment->subscription->plan->description }}
                                                    @endif
                                                </p>
                                            </div>
                                        </td>
                                        <td class="py-6 px-6 text-center">
                                            <div class="text-gray-700">
                                                @if($payment->subscription->starts_at && $payment->subscription->ends_at)
                                                    <div class="font-medium">{{ $payment->subscription->starts_at->format('M j') }}</div>
                                                    <div class="text-sm text-gray-500">to</div>
                                                    <div class="font-medium">{{ $payment->subscription->ends_at->format('M j, Y') }}</div>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-6 px-6 text-right">
                                            <div class="text-2xl font-bold text-gray-900">{{ $payment->formatted_amount }}</div>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-6 px-6">
                                            <div>
                                                <p class="font-semibold text-gray-900 text-lg">Subscription Payment</p>
                                                <p class="text-gray-600 mt-1">Payment for subscription service</p>
                                            </div>
                                        </td>
                                        <td class="py-6 px-6 text-center text-gray-700">
                                            <div class="font-medium">{{ $payment->created_at->format('M j, Y') }}</div>
                                        </td>
                                        <td class="py-6 px-6 text-right">
                                            <div class="text-2xl font-bold text-gray-900">{{ $payment->formatted_amount }}</div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="flex justify-end mb-8 print:mb-6">
                <div class="w-full max-w-md bg-gray-50 p-6 rounded-lg border">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Payment Summary</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between py-2">
                            <span class="text-gray-700">Subtotal:</span>
                            <span class="font-medium text-gray-900">{{ $payment->formatted_amount }}</span>
                        </div>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-700">Tax:</span>
                            <span class="font-medium text-gray-900">$0.00</span>
                        </div>

                        <div class="border-t border-gray-300 pt-3">
                            <div class="flex justify-between py-2">
                                <span class="text-xl font-bold text-gray-900">Total:</span>
                                <span class="text-xl font-bold text-blue-600">{{ $payment->formatted_amount }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            @if($payment->status === 'successful' && $payment->gateway_response)
            <div class="border-t border-gray-200 pt-6 print:pt-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Payment Details</h3>
                <div class="bg-green-50 p-4 rounded-lg print:bg-green-25">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        @if($payment->gateway_reference)
                        <div>
                            <p class="text-gray-600">Gateway Reference:</p>
                            <p class="font-medium text-gray-900">{{ $payment->gateway_reference }}</p>
                        </div>
                        @endif

                        @if($payment->payment_reference)
                        <div>
                            <p class="text-gray-600">Payment Reference:</p>
                            <p class="font-medium text-gray-900">{{ $payment->payment_reference }}</p>
                        </div>
                        @endif

                        @if($payment->paid_at)
                        <div>
                            <p class="text-gray-600">Payment Date:</p>
                            <p class="font-medium text-gray-900">{{ $payment->paid_at->format('M j, Y g:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Footer -->
            <div class="border-t border-gray-200 pt-6 mt-8 print:pt-4 print:mt-6">
                <div class="text-center text-gray-600 text-sm space-y-2">
                    <p>Thank you for your business!</p>

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        @if($businessInfo['website'] ?? false)
                        <p>{{ $businessInfo['website'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    @if($payment->status !== 'successful')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 print:hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-full
                    @if($payment->status === 'pending') bg-yellow-100
                    @elseif($payment->status === 'failed') bg-red-100
                    @else bg-gray-100 @endif">
                    @if($payment->status === 'pending')
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @elseif($payment->status === 'failed')
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Payment {{ ucfirst($payment->status) }}</h3>
                    <p class="text-gray-600">
                        @if($payment->status === 'pending')
                            This payment is awaiting confirmation
                        @elseif($payment->status === 'failed')
                            This payment could not be processed
                        @else
                            Payment status: {{ $payment->status }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('tenant.subscription.index', tenant()->slug) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Subscription
                </a>
                @if($payment->status === 'failed')
                <a href="{{ route('tenant.subscription.index', tenant()->slug) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Retry Payment
                </a>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<style>
@media print {
    * {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
    
    body {
        font-size: 12pt;
        line-height: 1.4;
        margin: 0;
        padding: 20px;
    }

    .print\:hidden {
        display: none !important;
    }

    .print\:shadow-none {
        box-shadow: none !important;
    }

    .print\:border-none {
        border: none !important;
    }

    .print\:p-0 {
        padding: 0 !important;
    }

    .print\:text-3xl {
        font-size: 1.875rem !important;
    }

    .print\:mb-6 {
        margin-bottom: 1.5rem !important;
    }

    .print\:mb-4 {
        margin-bottom: 1rem !important;
    }

    .print\:pt-4 {
        padding-top: 1rem !important;
    }

    .print\:mt-6 {
        margin-top: 1.5rem !important;
    }

    .max-w-4xl {
        max-width: none !important;
    }

    .rounded-xl, .rounded-lg {
        border-radius: 0 !important;
    }

    .shadow-sm {
        box-shadow: none !important;
    }

    .bg-gray-50, .bg-blue-50 {
        background-color: #f8f9fa !important;
    }

    .border-l-4 {
        border-left: 2px solid #3b82f6 !important;
    }

    @page {
        margin: 0.5in;
        size: A4;
    }
}

.invoice-animation {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection
