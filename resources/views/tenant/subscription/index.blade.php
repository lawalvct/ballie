@extends('layouts.tenant')

@section('title', 'Subscription Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Subscription Management</h1>
                <p class="text-gray-600 mt-1">Manage your current plan and billing settings</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('tenant.subscription.plans', tenant()->slug) }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    View All Plans
                </a>
            </div>
        </div>
    </div>

    <!-- Current Subscription -->
    @if($currentSubscription)
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl text-white p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">Current Plan</h2>
                <p class="text-blue-100 mt-1">{{ ucfirst($currentSubscription->plan) }} Plan</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold">{{ $currentSubscription->formatted_amount }}</div>
                <div class="text-blue-100">{{ $currentSubscription->billing_cycle }}</div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white bg-opacity-10 rounded-lg p-4">
                <div class="text-sm text-blue-100">Status</div>
                <div class="font-semibold">{{ ucfirst($currentSubscription->status) }}</div>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg p-4">
                <div class="text-sm text-blue-100">Renewal Date</div>
                <div class="font-semibold">{{ $currentSubscription->renewal_date }}</div>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg p-4">
                <div class="text-sm text-blue-100">Days Remaining</div>
                <div class="font-semibold">{{ $currentSubscription->days_until_expiration }} days</div>
            </div>
        </div>

        @if($currentSubscription->hasScheduledDowngrade())
        <div class="mt-4 bg-yellow-500 bg-opacity-20 border border-yellow-300 rounded-lg p-3">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-yellow-100">
                    Scheduled to downgrade to {{ ucfirst($currentSubscription->scheduled_downgrade['plan']) }}
                    on {{ \Carbon\Carbon::parse($currentSubscription->scheduled_downgrade['effective_date'])->format('M j, Y') }}
                </span>
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <div class="text-center">
            <h2 class="text-xl font-semibold text-yellow-800">No Active Subscription</h2>
            <p class="text-yellow-600 mt-2">Choose a plan to get started with premium features</p>
            <a href="{{ route('tenant.subscription.plans', tenant()->slug) }}"
               class="inline-block mt-4 bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                Choose a Plan
            </a>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900">Upgrade Plan</h3>
                <p class="text-gray-600 text-sm mt-1">Get more features and higher limits</p>
                <a href="{{ route('tenant.subscription.plans', tenant()->slug) }}"
                   class="inline-block mt-3 text-green-600 hover:text-green-700 text-sm font-medium">
                    View Plans →
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900">Billing History</h3>
                <p class="text-gray-600 text-sm mt-1">View past payments and invoices</p>
                <a href="{{ route('tenant.subscription.history', tenant()->slug) }}"
                   class="inline-block mt-3 text-blue-600 hover:text-blue-700 text-sm font-medium">
                    View History →
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900">Cancel Subscription</h3>
                <p class="text-gray-600 text-sm mt-1">Cancel your current subscription</p>
                @if($currentSubscription)
                <a href="{{ route('tenant.subscription.cancel', tenant()->slug) }}"
                   class="inline-block mt-3 text-red-600 hover:text-red-700 text-sm font-medium">
                    Cancel →
                </a>
                @else
                <span class="inline-block mt-3 text-gray-400 text-sm">
                    No active subscription
                </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    @if($recentPayments->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Recent Payments</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentPayments as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->formatted_amount }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {!! $payment->status_badge !!}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $payment->payment_reference }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('tenant.subscription.invoice', ['tenant' => tenant()->slug, 'payment' => $payment->id]) }}"
                               class="text-blue-600 hover:text-blue-900">
                                View Invoice
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
