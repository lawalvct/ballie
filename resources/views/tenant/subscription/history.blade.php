@extends('layouts.tenant')

@section('title', 'Subscription History')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Subscription History</h1>
                <p class="text-gray-600 mt-1">View your subscription changes and payment history</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('tenant.subscription.index', tenant()->slug) }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Subscription
                </a>
                @if($invoices->isNotEmpty())
                <a href="{{ route('tenant.subscription.invoices', tenant()->slug) }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    View Invoices
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Current Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Status</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Current Plan</p>
                        <p class="text-xl font-bold text-gray-900">{{ $currentPlan ? $currentPlan->name : 'Free Plan' }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        @if($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
                            <p class="text-xl font-bold text-green-700">Trial Active</p>
                        @elseif($tenant->isOnTrial())
                            <p class="text-xl font-bold text-green-700">Trial Active</p>
                        @elseif($currentPlan)
                            <p class="text-xl font-bold text-green-700">Active</p>
                        @else
                            <p class="text-xl font-bold text-gray-700">Free</p>
                        @endif
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Next Billing</p>
                        @if($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
                            <p class="text-xl font-bold text-gray-900">{{ $tenant->trial_ends_at->format('M j, Y') }}</p>
                        @elseif($currentPlan && isset($nextBillingDate))
                            <p class="text-xl font-bold text-gray-900">{{ $nextBillingDate->format('M j, Y') }}</p>
                        @else
                            <p class="text-xl font-bold text-gray-700">No billing</p>
                        @endif
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription History Timeline -->
    @if(isset($subscriptionHistory) && $subscriptionHistory->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Subscription Changes</h3>

        <div class="space-y-6">
            @foreach($subscriptionHistory as $event)
            <div class="flex items-start">
                <div class="flex-shrink-0 mr-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center
                        @if($event->type === 'upgrade') bg-green-100
                        @elseif($event->type === 'downgrade') bg-orange-100
                        @elseif($event->type === 'cancelled') bg-red-100
                        @elseif($event->type === 'activated') bg-blue-100
                        @else bg-gray-100 @endif">

                        @if($event->type === 'upgrade')
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        @elseif($event->type === 'downgrade')
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                            </svg>
                        @elseif($event->type === 'cancelled')
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        @elseif($event->type === 'activated')
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        @endif
                    </div>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">
                                @if($event->type === 'upgrade')
                                    Upgraded to {{ $event->plan_name }}
                                @elseif($event->type === 'downgrade')
                                    Downgraded to {{ $event->plan_name }}
                                @elseif($event->type === 'cancelled')
                                    Subscription Cancelled
                                @elseif($event->type === 'activated')
                                    Subscription Activated
                                @else
                                    {{ ucfirst($event->type) }}
                                @endif
                            </h4>
                            <p class="text-gray-600">{{ $event->created_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>

                        @if($event->amount)
                        <div class="text-right">
                            <span class="text-lg font-bold text-gray-900">₦{{ number_format($event->amount / 100, 2) }}</span>
                            @if($event->billing_cycle)
                                <span class="text-gray-600">/{{ $event->billing_cycle }}</span>
                            @endif
                        </div>
                        @endif
                    </div>

                    @if($event->description)
                    <p class="text-gray-600 mt-2">{{ $event->description }}</p>
                    @endif

                    @if($event->old_plan_name && $event->plan_name !== $event->old_plan_name)
                    <div class="mt-2 text-sm text-gray-500">
                        Changed from: {{ $event->old_plan_name }}
                    </div>
                    @endif
                </div>
            </div>

            @if(!$loop->last)
            <div class="ml-5 border-l-2 border-gray-200 h-4"></div>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Payment History -->
    @if(isset($payments) && $payments->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
            @if($payments->hasPages())
            <a href="{{ route('tenant.subscription.invoices', tenant()->slug) }}"
               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                View All Payments →
            </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Date</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Description</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Amount</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Status</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($payments->take(5) as $payment)
                    <tr>
                        <td class="py-4 px-4 text-gray-900">
                            {{ $payment->created_at->format('M j, Y') }}
                        </td>
                        <td class="py-4 px-4">
                            <div>
                                <p class="text-gray-900 font-medium">{{ $payment->description }}</p>
                                @if($payment->plan_name)
                                <p class="text-gray-600 text-sm">{{ $payment->plan_name }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-4 text-gray-900 font-medium">
                            ₦{{ number_format($payment->amount / 100, 2) }}
                        </td>
                        <td class="py-4 px-4">
                            @if($payment->status === 'successful')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">Successful</span>
                            @elseif($payment->status === 'pending')
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-sm font-medium">Pending</span>
                            @elseif($payment->status === 'failed')
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm font-medium">Failed</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-sm font-medium">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            @if($payment->invoice_url)
                            <a href="{{ $payment->invoice_url }}" target="_blank"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Invoice
                            </a>
                            @else
                            <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Usage Statistics -->
    @if(isset($usageStats))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Usage This Month</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @if(isset($usageStats['invoices']))
            <div class="text-center">
                <div class="bg-blue-100 p-3 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $usageStats['invoices'] }}</p>
                <p class="text-gray-600">Invoices Created</p>
            </div>
            @endif

            @if(isset($usageStats['customers']))
            <div class="text-center">
                <div class="bg-green-100 p-3 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $usageStats['customers'] }}</p>
                <p class="text-gray-600">Customers Added</p>
            </div>
            @endif

            @if(isset($usageStats['products']))
            <div class="text-center">
                <div class="bg-purple-100 p-3 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $usageStats['products'] }}</p>
                <p class="text-gray-600">Products Added</p>
            </div>
            @endif

            @if(isset($usageStats['revenue']))
            <div class="text-center">
                <div class="bg-yellow-100 p-3 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900">₦{{ number_format($usageStats['revenue'] / 100, 0) }}</p>
                <p class="text-gray-600">Revenue Generated</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Empty State -->
    @if((!isset($subscriptionHistory) || $subscriptionHistory->isEmpty()) && (!isset($payments) || $payments->isEmpty()))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="max-w-md mx-auto">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>

            <h3 class="text-xl font-semibold text-gray-900 mb-2">No History Yet</h3>
            <p class="text-gray-600 mb-6">Your subscription and payment history will appear here once you make your first transaction.</p>

            <a href="{{ route('tenant.subscription.plans', tenant()->slug) }}"
               class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                View Available Plans
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
