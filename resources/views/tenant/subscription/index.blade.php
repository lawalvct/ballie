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
    @if($currentPlan)
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl text-white p-6">
        <!-- Expired Subscription Alert -->
        @if($tenant->hasExpiredSubscription() || $tenant->subscription_status === 'expired')
        <div class="bg-red-500 bg-opacity-20 border border-red-300 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-200 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <div class="font-semibold text-red-100">Subscription Expired</div>
                        <div class="text-red-200 text-sm">
                            Your subscription expired on {{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('M j, Y') : 'N/A' }}
                        </div>
                    </div>
                </div>
                <a href="{{ route('tenant.subscription.renew', tenant()->slug) }}"
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Renew Now
                </a>
            </div>
        </div>
        @endif

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">Current Plan</h2>
                <p class="text-blue-100 mt-1">{{ $currentPlan->name }} Plan</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold">
                    @if($tenant->isOnTrial())
                        <span class="text-yellow-300">Trial</span>
                    @else
                        {{ $tenant->billing_cycle === 'yearly' ? $currentPlan->formatted_yearly_price : $currentPlan->formatted_monthly_price }}
                    @endif
                </div>
                <div class="text-blue-100">
                    @if($tenant->isOnTrial())
                        Trial Period
                    @else
                        {{ ucfirst($tenant->billing_cycle ?? 'monthly') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white bg-opacity-10 rounded-lg p-4">
                <div class="text-sm text-blue-100">Status</div>
                <div class="font-semibold">
                    @if($tenant->isOnTrial())
                        Trial
                    @elseif($tenant->hasActiveSubscription())
                        Active
                    @else
                        Inactive
                    @endif
                </div>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg p-4">
                <div class="text-sm text-blue-100">
                    @if($tenant->isOnTrial())
                        Trial Ends
                    @else
                        Renewal Date
                    @endif
                </div>
                <div class="font-semibold">
                    @if($tenant->trial_ends_at)
                        {{ $tenant->trial_ends_at->format('M j, Y') }}
                    @elseif($tenant->subscription_ends_at)
                        {{ $tenant->subscription_ends_at->format('M j, Y') }}
                    @else
                        N/A
                    @endif
                </div>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg p-4">
                <div class="text-sm text-blue-100">Days Remaining</div>
                <div class="font-semibold">
                    @if($tenant->isOnTrial())
                        {{ $tenant->trialDaysRemaining() }} days
                    @elseif($tenant->subscription_ends_at)
                        {{ now()->diffInDays($tenant->subscription_ends_at, false) }} days
                    @else
                        N/A
                    @endif
                </div>
            </div>
        </div>

        @if($tenant->isOnTrial())
        <div class="mt-4 bg-yellow-500 bg-opacity-20 border border-yellow-300 rounded-lg p-3">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-yellow-100">
                    Your {{ $currentPlan->name }} trial expires on {{ $tenant->trial_ends_at->format('M j, Y') }}
                    ({{ $tenant->trialDaysRemaining() }} days remaining)
                </span>
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <div class="text-center">
            <h2 class="text-xl font-semibold text-yellow-800">No Active Plan</h2>
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
        <!-- Renew/Upgrade Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-center">
                @if($tenant->hasExpiredSubscription() || $tenant->subscription_status === 'expired')
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900">Renew Subscription</h3>
                <p class="text-gray-600 text-sm mt-1">Renew your {{ $currentPlan->name }} plan</p>
                <a href="{{ route('tenant.subscription.renew', tenant()->slug) }}"
                   class="inline-block mt-3 text-red-600 hover:text-red-700 text-sm font-medium">
                    Renew Now →
                </a>
                @else
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
                @endif
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
                @if($currentPlan)
                <a href="{{ route('tenant.subscription.cancel', tenant()->slug) }}"
                   class="inline-block mt-3 text-red-600 hover:text-red-700 text-sm font-medium">
                    Cancel →
                </a>
                @else
                <span class="inline-block mt-3 text-gray-400 text-sm">
                    No active plan
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
