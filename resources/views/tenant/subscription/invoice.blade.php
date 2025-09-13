@extends('layouts.tenant')

@section('title', 'Payment Invoice')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Payment Invoice #{{ $payment->payment_reference }}</h1>
                <p class="text-gray-600 mt-1">{{ $payment->created_at->format('M j, Y') }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('tenant.subscription.history', tenant()->slug) }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to History
                </a>

                <button onclick="window.print()"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Invoice
                </button>

                <a href="{{ route('tenant.subscription.invoice.download', ['tenant' => tenant()->slug, 'payment' => $payment->id]) }}"
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>
    }</div>

    <!-- Invoice Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 print:shadow-none print:border-none" id="invoice-content">
        <div class="p-8 print:p-0">
            <!-- Invoice Header -->
            <div class="flex justify-between items-start mb-8 print:mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 print:text-2xl">INVOICE</h1>
                    <p class="text-lg text-gray-600 mt-1">{{ config('app.name') }}</p>
                    @if($businessInfo ?? false)
                    <div class="mt-4 text-gray-600 space-y-1">
                        @if($businessInfo['address'])
                        <p>{{ $businessInfo['address'] }}</p>
                        @endif
                        @if($businessInfo['phone'])
                        <p>Phone: {{ $businessInfo['phone'] }}</p>
                        @endif
                        @if($businessInfo['email'])
                        <p>Email: {{ $businessInfo['email'] }}</p>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="text-right">
                    <div class="bg-gray-100 p-4 rounded-lg print:bg-gray-50">
                        <p class="text-sm text-gray-600">Payment Reference</p>
                        <p class="text-xl font-bold text-gray-900">{{ $payment->payment_reference }}</p>

                        <p class="text-sm text-gray-600 mt-3">Payment Date</p>
                        <p class="font-semibold text-gray-900">{{ $payment->created_at->format('M j, Y') }}</p>

                        @if($payment->paid_at)
                        <p class="text-sm text-gray-600 mt-3">Paid Date</p>
                        <p class="font-semibold text-gray-900">{{ $payment->paid_at->format('M j, Y') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Billing Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 print:mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Bill To:</h3>
                    <div class="text-gray-700 space-y-1">
                        <p class="font-medium">{{ $tenant->company_name ?? $tenant->name }}</p>
                        @if($tenant->email)
                        <p>{{ $tenant->email }}</p>
                        @endif
                        @if($tenant->phone)
                        <p>{{ $tenant->phone }}</p>
                        @endif
                        @if($tenant->address)
                        <p>{{ $tenant->address }}</p>
                        @endif
                        @if($tenant->city || $tenant->state)
                        <p>{{ implode(', ', array_filter([$tenant->city, $tenant->state])) }}</p>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Payment Status:</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            @if($payment->status === 'successful')
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Paid</span>
                            @elseif($payment->status === 'pending')
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">Pending</span>
                            @elseif($payment->status === 'failed')
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">Failed</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </div>

                        @if($payment->paid_at)
                        <p class="text-sm text-gray-600">
                            Paid on {{ $payment->paid_at->format('M j, Y') }}
                        </p>
                        @endif

                        @if($payment->payment_method)
                        <p class="text-sm text-gray-600">
                            Payment Method: {{ ucfirst($payment->payment_method) }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="mb-8 print:mb-6">
                <table class="w-full border border-gray-200 print:border-gray-300">
                    <thead>
                        <tr class="bg-gray-100 print:bg-gray-50">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 border-b border-gray-200">Description</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-700 border-b border-gray-200">Period</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700 border-b border-gray-200">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($payment->subscription)
                            <tr class="border-b border-gray-200">
                                <td class="py-4 px-4">
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            @if($payment->subscription && $payment->subscription->plan && is_object($payment->subscription->plan))
                                                {{ $payment->subscription->plan->name }}
                                            @else
                                                Subscription Plan
                                            @endif
                                            - {{ ucfirst($payment->subscription->billing_cycle ?? 'monthly') }} Subscription
                                        </p>
                                        @if($payment->subscription && $payment->subscription->plan && is_object($payment->subscription->plan) && $payment->subscription->plan->description)
                                        <p class="text-gray-600 text-sm">{{ $payment->subscription->plan->description }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center text-gray-700">
                                    @if($payment->subscription->starts_at && $payment->subscription->ends_at)
                                        {{ $payment->subscription->starts_at->format('M j') }} - {{ $payment->subscription->ends_at->format('M j, Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right font-medium text-gray-900">
                                    {{ $payment->formatted_amount }}
                                </td>
                            </tr>
                        @else
                            <tr class="border-b border-gray-200">
                                <td class="py-4 px-4">
                                    <div>
                                        <p class="font-medium text-gray-900">Subscription Payment</p>
                                        <p class="text-gray-600 text-sm">Payment for subscription service</p>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center text-gray-700">
                                    {{ $payment->created_at->format('M j, Y') }}
                                </td>
                                <td class="py-4 px-4 text-right font-medium text-gray-900">
                                    {{ $payment->formatted_amount }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Payment Summary -->
            <div class="flex justify-end mb-8 print:mb-6">
                <div class="w-full max-w-md">
                    <div class="space-y-2">
                        <div class="flex justify-between py-2">
                            <span class="text-gray-700">Subtotal:</span>
                            <span class="font-medium text-gray-900">{{ $payment->formatted_amount }}</span>
                        </div>

                        </div>

                        <div class="border-t border-gray-200 pt-2">
                            <div class="flex justify-between py-2">
                                <span class="text-lg font-semibold text-gray-900">Total:</span>
                                <span class="text-lg font-bold text-gray-900">{{ $payment->formatted_amount }}</span>
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
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Payment Status</h3>
                <p class="text-gray-600">This payment is {{ $payment->status }}</p>
            </div>

            <div class="flex space-x-3">
                <button onclick="window.location.href='{{ route('tenant.subscription.index', tenant()->slug) }}'"
                        class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Subscription
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
@media print {
    body {
        font-size: 12pt;
        line-height: 1.4;
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

    .print\:text-2xl {
        font-size: 1.5rem !important;
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

    .print\:bg-gray-50 {
        background-color: #f9fafb !important;
    }

    .print\:bg-green-25 {
        background-color: #f0fdf4 !important;
    }

    .print\:border-gray-300 {
        border-color: #d1d5db !important;
    }
}
</style>
@endsection
