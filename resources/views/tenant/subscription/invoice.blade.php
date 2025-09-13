@extends('layouts.tenant')

@section('title', 'Invoice #' . $payment->payment_reference)

@php
use App\Helpers\NumberToWords;
$amountInWords = NumberToWords::convert(floatval(str_replace(['$', ','], '', $payment->amount ?? 0)));
@endphp

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
            <div class="flex justify-between items-start mb-4 print:mb-3">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl font-bold text-gray-900 print:text-xl">INVOICE</h1>
                        <span class="px-2 py-1 rounded text-xs font-medium
                            @if($payment->status === 'successful') bg-green-100 text-green-800
                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($payment->status === 'failed') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ config('app.name') }}</h2>
                    @if($businessInfo ?? false)
                    <div class="text-gray-600 text-sm mt-1">
                        @if($businessInfo['address'] ?? false)
                        <p>{{ $businessInfo['address'] }}</p>
                        @endif
                        @if($businessInfo['phone'] ?? false)
                        <span>{{ $businessInfo['phone'] }}</span>
                        @endif
                        @if($businessInfo['email'] ?? false)
                        <span class="ml-3">{{ $businessInfo['email'] }}</span>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="text-right">
                    <div class="space-y-1">
                        <div>
                            <p class="text-xs text-gray-600">Invoice #</p>
                            <p class="font-bold text-gray-900">{{ $payment->payment_reference }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Date</p>
                            <p class="text-sm text-gray-900">{{ $payment->created_at->format('M j, Y') }}</p>
                        </div>
                        @if($payment->paid_at)
                        <div>
                            <p class="text-xs text-gray-600">Paid</p>
                            <p class="text-sm text-gray-900">{{ $payment->paid_at->format('M j, Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Billing Information -->
            <div class="grid grid-cols-2 gap-6 mb-4 print:mb-3">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Bill To:</h3>
                    <div class="text-sm space-y-1">
                        <p class="font-semibold text-gray-900">{{ $tenant->company_name ?? $tenant->name }}</p>
                        @if($tenant->email)
                        <p class="text-gray-700">{{ $tenant->email }}</p>
                        @endif
                        @if($tenant->phone)
                        <p class="text-gray-700">{{ $tenant->phone }}</p>
                        @endif
                        @if($tenant->address)
                        <p class="text-gray-700">{{ $tenant->address }}
                        @if($tenant->city || $tenant->state)
                        <br>{{ implode(', ', array_filter([$tenant->city, $tenant->state])) }}
                        @endif
                        </p>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Payment Info:</h3>
                    <div class="text-sm space-y-1">
                        @if($payment->payment_method)
                        <p class="text-gray-700">Method: {{ ucfirst($payment->payment_method) }}</p>
                        @endif
                        @if($payment->gateway_reference)
                        <p class="text-gray-700">Ref: {{ $payment->gateway_reference }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="mb-4 print:mb-3">
                <table class="w-full border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="text-left py-2 px-3 text-sm font-semibold text-gray-700">Description</th>
                            <th class="text-center py-2 px-3 text-sm font-semibold text-gray-700">Period</th>
                            <th class="text-right py-2 px-3 text-sm font-semibold text-gray-700">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($payment->subscription)
                            <tr>
                                <td class="py-3 px-3">
                                    <p class="font-medium text-gray-900">
                                        @if($payment->subscription && $payment->subscription->plan && is_object($payment->subscription->plan))
                                            {{ $payment->subscription->plan->name }}
                                        @else
                                            Subscription Plan
                                        @endif
                                    </p>
                                    <p class="text-gray-600 text-sm">
                                        {{ ucfirst($payment->subscription->billing_cycle ?? 'monthly') }} subscription
                                    </p>
                                </td>
                                <td class="py-3 px-3 text-center text-sm">
                                    @if($payment->subscription->starts_at && $payment->subscription->ends_at)
                                        {{ $payment->subscription->starts_at->format('M j') }} - {{ $payment->subscription->ends_at->format('M j, Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-lg">{{ $payment->formatted_amount }}</td>
                            </tr>
                        @else
                            <tr>
                                <td class="py-3 px-3">
                                    <p class="font-medium text-gray-900">Subscription Payment</p>
                                    <p class="text-gray-600 text-sm">Payment for subscription service</p>
                                </td>
                                <td class="py-3 px-3 text-center text-sm">{{ $payment->created_at->format('M j, Y') }}</td>
                                <td class="py-3 px-3 text-right font-bold text-lg">{{ $payment->formatted_amount }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Amount in Words -->
            <div class="mb-4 print:mb-3">
                <div class="bg-gray-50 p-3 rounded border">
                    <p class="text-sm text-gray-700">
                        <span class="font-medium">Amount in words:</span> 
                        <span class="capitalize">{{ $amountInWords }}</span>
                    </p>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="flex justify-end mb-4 print:mb-3">
                <div class="w-64">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700">Subtotal:</span>
                            <span class="font-medium">{{ $payment->formatted_amount }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700">Tax:</span>
                            <span class="font-medium">$0.00</span>
                        </div>
                        <div class="border-t pt-2">
                            <div class="flex justify-between">
                                <span class="font-bold text-gray-900">Total:</span>
                                <span class="font-bold text-lg text-blue-600">{{ $payment->formatted_amount }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($payment->status === 'successful' && ($payment->gateway_reference || $payment->paid_at))
            <div class="border-t pt-3 mb-3 print:pt-2 print:mb-2">
                <div class="grid grid-cols-3 gap-4 text-xs">
                    @if($payment->gateway_reference)
                    <div>
                        <p class="text-gray-600">Gateway Ref:</p>
                        <p class="font-medium">{{ $payment->gateway_reference }}</p>
                    </div>
                    @endif
                    @if($payment->paid_at)
                    <div>
                        <p class="text-gray-600">Paid:</p>
                        <p class="font-medium">{{ $payment->paid_at->format('M j, Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Footer -->
            <div class="border-t pt-3 mt-4 print:pt-2 print:mt-3">
                <div class="text-center text-gray-600 text-xs">
                    <p>Thank you for your business!</p>
                    <p class="mt-2">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
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
        font-size: 10pt;
        line-height: 1.2;
        margin: 0;
        padding: 0;
        visibility: hidden;
    }

    #invoice-content,
    #invoice-content * {
        visibility: visible;
    }

    #invoice-content {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0 !important;
        padding: 20px !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
        background: white !important;
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

    .print\:text-xl {
        font-size: 1.25rem !important;
    }

    .print\:mb-3 {
        margin-bottom: 0.75rem !important;
    }

    .print\:mb-2 {
        margin-bottom: 0.5rem !important;
    }

    .print\:pt-2 {
        padding-top: 0.5rem !important;
    }

    .print\:mt-3 {
        margin-top: 0.75rem !important;
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
