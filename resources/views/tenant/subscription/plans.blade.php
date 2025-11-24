@extends('layouts.tenant')

@section('title', 'Subscription Plans')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Choose Your Plan</h1>
                <p class="text-gray-600 mt-1">Select the perfect plan for your business needs</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('tenant.subscription.index', tenant()->slug) }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Subscription
                </a>
            </div>
        </div>
    </div>

    <!-- Current Plan Alert -->
    @if($currentPlan)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-blue-800">
                You are currently on the <strong>{{ $currentPlan->name }}</strong> plan.
                @if($tenant->isOnTrial())
                    <span class="text-blue-600">({{ $tenant->trialDaysRemaining() }} days left in trial)</span>
                @endif
            </span>
        </div>
    </div>
    @endif

    <!-- Billing Toggle -->
    <div class="text-center" role="group" aria-label="Billing cycle toggle">
        <div class="inline-flex items-center bg-gray-100 rounded-lg p-1 shadow-inner">
            <button id="monthlyBtn" type="button" class="billing-toggle px-4 py-2 text-sm font-medium rounded-md transition-all duration-200"
                    aria-pressed="true" data-cycle="monthly">
                Monthly
            </button>
            <button id="yearlyBtn" type="button" class="billing-toggle px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 relative"
                    aria-pressed="false" data-cycle="yearly">
                Yearly
                <span class="text-green-700 text-xs ml-1 font-semibold">Save 20%</span>
                <span class="absolute -top-2 right-2 bg-green-100 text-green-700 text-[10px] px-2 py-0.5 rounded-full tracking-wide hidden md:inline">Best Value</span>
            </button>
        </div>
        <p class="mt-2 text-xs text-gray-500" id="billingHint">Toggle to see {{ strtolower($plans->first()->name ?? 'each') }} plan monthly vs yearly pricing.</p>
    </div>

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="plansGrid">
        @foreach($plans as $plan)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 relative focus-within:ring-2 focus-within:ring-blue-400 {{ $plan->is_popular ? 'ring-2 ring-blue-500 transform scale-105' : '' }} {{ $currentPlan && $currentPlan->id === $plan->id ? 'ring-2 ring-green-400 bg-green-50' : '' }}" tabindex="0" aria-label="Subscription plan {{ $plan->name }}" data-plan-id="{{ $plan->id }}">
            @if($plan->is_popular)
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white text-center py-2 text-sm font-semibold relative">
                <span class="relative z-10">Most Popular</span>
                <div class="absolute inset-0 bg-blue-400 opacity-20 animate-pulse"></div>
            </div>
            @endif

            @if($currentPlan && $currentPlan->id === $plan->id)
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white text-center py-2 text-sm font-semibold">
                <span class="inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Your Current Plan
                </span>
            </div>
            @endif

            <div class="p-6">
                <!-- Plan Header -->
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                    <div class="pricing-container" aria-live="polite">
                        <div class="monthly-pricing" data-price-monthly="{{ $plan->monthly_price }}">
                            <span class="text-3xl font-bold text-gray-900">{{ $plan->formatted_monthly_price }}</span>
                            <span class="text-gray-600">/month</span>
                        </div>
                        <div class="yearly-pricing hidden" data-price-yearly="{{ $plan->yearly_price }}">
                            <span class="text-3xl font-bold text-gray-900">{{ $plan->formatted_yearly_price }}</span>
                            <span class="text-gray-600">/year</span>
                            @php
                                $monthlyCost = $plan->monthly_price * 12;
                                $yearlyCost = $plan->yearly_price;
                                $savings = $monthlyCost - $yearlyCost;
                            @endphp
                            @if($savings > 0)
                                <div class="mt-2 inline-flex items-center gap-1 bg-green-50 text-green-700 text-xs font-medium px-2 py-1 rounded-full border border-green-200" title="You save when paying yearly">
                                    <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm3.707 6.293l-4 4a1 1 0 01-1.414 0l-2-2 1.414-1.414L9 10.586l3.293-3.293 1.414 1.414z"/></svg>
                                    <span>Save ₦{{ number_format($savings / 100, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <p class="text-gray-600 mt-2">{{ $plan->description }}</p>
                </div>

                <!-- Features -->
                <div class="space-y-3 mb-6">
                    @if($plan->features)
                        @foreach(array_slice($plan->features, 0, 8) as $index => $feature)
                        <div class="flex items-center group" title="{{ $feature }}">
                            <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700 text-sm group-hover:text-gray-900 transition-colors">{{ $feature }}</span>
                            @if(str_contains(strtolower($feature), 'unlimited') || str_contains(strtolower($feature), 'premium'))
                            <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                ✨
                            </span>
                            @endif
                        </div>
                        @endforeach

                        @if(count($plan->features) > 8)
                        <div class="text-center">
                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors" onclick="toggleFeatures({{ $plan->id }})">
                                <span id="toggleText-{{ $plan->id }}">Show {{ count($plan->features) - 8 }} more features</span>
                                <svg class="w-4 h-4 inline ml-1 transform transition-transform" id="toggleIcon-{{ $plan->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/>
                                </svg>
                            </button>
                        </div>
                        <div id="extraFeatures-{{ $plan->id }}" class="hidden space-y-3">
                            @foreach(array_slice($plan->features, 8) as $feature)
                            <div class="flex items-center group">
                                <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700 text-sm">{{ $feature }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    @endif
                </div>

                <!-- Action Button -->
                <div class="text-center">
                    @php
                        $isCurrent = $currentPlan && $currentPlan->id === $plan->id;
                        $isUpgrade = $currentPlan && $currentPlan->monthly_price < $plan->monthly_price;
                        $isDowngrade = $currentPlan && $currentPlan->monthly_price > $plan->monthly_price;
                        $actionLabel = $isCurrent ? 'Current Plan' : ($isUpgrade ? 'Upgrade to ' . $plan->name : ($isDowngrade ? 'Downgrade to ' . $plan->name : 'Choose ' . $plan->name));
                        $actionRoute = $isDowngrade ? route('tenant.subscription.downgrade.process', ['tenant' => tenant()->slug, 'plan' => $plan->id]) : route('tenant.subscription.upgrade.process', ['tenant' => tenant()->slug, 'plan' => $plan->id]);
                    @endphp
                    @if($isCurrent)
                        <div class="space-y-2">
                            <button class="w-full bg-gradient-to-r from-gray-200 to-gray-300 text-gray-600 py-3 px-4 rounded-lg cursor-not-allowed flex items-center justify-center gap-2 font-medium" disabled aria-disabled="true">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                {{ $actionLabel }}
                            </button>
                            <a href="{{ route('tenant.subscription.renew', tenant()->slug) }}" class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white py-3 px-4 rounded-lg flex items-center justify-center gap-2 font-medium transition-all duration-200 transform hover:scale-105">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Renew Plan
                            </a>
                        </div>
                    @else
                        <form method="POST" action="{{ $actionRoute }}" class="inline-block w-full" onsubmit="this.querySelector('[name=billing_cycle]').value = window.__billingCycle || 'monthly'; this.querySelector('button').innerHTML = '<svg class=\'w-4 h-4 animate-spin mr-2\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg>Processing...'; this.querySelector('button').disabled = true;">
                            @csrf
                            <input type="hidden" name="billing_cycle" value="">
                            <button type="submit"
                                class="w-full py-3 px-4 rounded-lg transition-all duration-200 inline-flex items-center justify-center gap-2 text-sm font-medium transform hover:scale-105 focus:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2
                                    {{ $isUpgrade ? 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white focus:ring-blue-500' : '' }}
                                    {{ $isDowngrade ? 'bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white focus:ring-orange-500' : '' }}
                                    {{ !$isUpgrade && !$isDowngrade ? 'bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white focus:ring-purple-500' : '' }}
                                    {{ (!$isUpgrade && !$isDowngrade) ? 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white focus:ring-blue-500' : '' }}"
                                aria-label="{{ $actionLabel }} with selected billing cycle">
                                @if($isUpgrade)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                @elseif($isDowngrade)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                @endif
                                <span>{{ $actionLabel }}</span>
                                <span class="text-[10px] uppercase tracking-wide bg-white/20 px-2 py-0.5 rounded hidden md:inline" id="cycleBadge-{{ $plan->id }}">Monthly</span>
                            </button>
                        </form>
                    @endif
                </div>

                @if($plan->trial_days > 0)
                <div class="text-center mt-3">
                    <span class="text-sm text-gray-500">{{ $plan->trial_days }}-day free trial</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Features Comparison Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Compare Plans</h2>
                    <p class="text-gray-600 mt-1">Detailed comparison of all plan features and limits</p>
                </div>
                <button onclick="toggleComparison()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    <span id="comparisonToggleText">Show Details</span>
                    <svg class="w-4 h-4 inline ml-1 transform transition-transform" id="comparisonToggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto" id="comparisonTable">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-900 sticky left-0 bg-gray-50 z-10">Features</th>
                        @foreach($plans as $plan)
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-900 min-w-[150px]">
                            <div class="flex flex-col items-center">
                                <span class="font-semibold">{{ $plan->name }}</span>
                                @if($currentPlan && $currentPlan->id === $plan->id)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Current
                                    </span>
                                @endif
                                @if($plan->is_popular)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                        ⭐ Popular
                                    </span>
                                @endif
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Pricing Section -->
                    <tr class="bg-blue-50">
                        <td colspan="{{ count($plans) + 1 }}" class="px-6 py-3 text-sm font-semibold text-blue-900 sticky left-0 bg-blue-50 z-10">
                            💰 Pricing
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky left-0 bg-white z-10">Monthly Price</td>
                        @foreach($plans as $plan)
                        <td class="px-6 py-4 text-center text-sm">
                            <span class="font-semibold text-gray-900">{{ $plan->formatted_monthly_price }}</span>
                        </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky left-0 bg-white z-10">Yearly Price</td>
                        @foreach($plans as $plan)
                        <td class="px-6 py-4 text-center text-sm">
                            <span class="font-semibold text-gray-900">{{ $plan->formatted_yearly_price }}</span>
                            @php
                                $monthlyCost = $plan->monthly_price * 12;
                                $yearlyCost = $plan->yearly_price;
                                $savings = $monthlyCost - $yearlyCost;
                            @endphp
                            @if($savings > 0)
                                <div class="text-green-600 text-xs mt-1 font-medium">
                                    Save ₦{{ number_format($savings / 100, 2) }}
                                </div>
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    <!-- Core Features Section -->
                    @if($plans->first() && $plans->first()->limits)
                    <tr class="bg-purple-50">
                        <td colspan="{{ count($plans) + 1 }}" class="px-6 py-3 text-sm font-semibold text-purple-900 sticky left-0 bg-purple-50 z-10">
                            📊 Plan Limits
                        </td>
                    </tr>
                    @foreach($plans->first()->limits as $limitKey => $limitValue)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky left-0 bg-white z-10">
                            {{ ucfirst(str_replace('_', ' ', $limitKey)) }}
                        </td>
                        @foreach($plans as $plan)
                        <td class="px-6 py-4 text-center text-sm text-gray-700">
                            @php
                                $limit = $plan->limits[$limitKey] ?? 'N/A';
                                $isUnlimited = $limit === 'unlimited' || $limit === -1;
                                $isHighValue = is_numeric($limit) && $limit > 1000;
                            @endphp
                            @if($isUnlimited)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ∞ Unlimited
                                </span>
                            @elseif($isHighValue)
                                <span class="font-semibold text-gray-900">{{ number_format($limit) }}</span>
                            @else
                                <span class="text-gray-700">{{ $limit }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                    @endif

                    <!-- Support Section -->
                    <tr class="bg-orange-50">
                        <td colspan="{{ count($plans) + 1 }}" class="px-6 py-3 text-sm font-semibold text-orange-900 sticky left-0 bg-orange-50 z-10">
                            🛟 Support & Trial
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky left-0 bg-white z-10">Free Trial</td>
                        @foreach($plans as $plan)
                        <td class="px-6 py-4 text-center text-sm text-gray-700">
                            @if($plan->trial_days > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $plan->trial_days }} days
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Frequently Asked Questions
        </h3>
        <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <h4 class="font-medium text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">?</span>
                        Can I change my plan at any time?
                    </h4>
                    <p class="text-gray-600 text-sm mt-2 ml-8">Yes, you can upgrade or downgrade your plan at any time. Changes take effect immediately for upgrades, or at the end of your current billing cycle for downgrades.</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <h4 class="font-medium text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">?</span>
                        Do you offer refunds?
                    </h4>
                    <p class="text-gray-600 text-sm mt-2 ml-8">We offer a 30-day money-back guarantee on all plans. Contact support if you're not satisfied with your subscription.</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <h4 class="font-medium text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">?</span>
                        How does billing work?
                    </h4>
                    <p class="text-gray-600 text-sm mt-2 ml-8">You'll be billed automatically based on your selected cycle (monthly or yearly). You can update your payment method or cancel anytime from your account settings.</p>
                </div>
            </div>
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <h4 class="font-medium text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">?</span>
                        What happens to my data if I downgrade?
                    </h4>
                    <p class="text-gray-600 text-sm mt-2 ml-8">Your data is always safe. If you exceed the limits of a lower plan, you'll have read-only access until you upgrade again or remove excess data.</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <h4 class="font-medium text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">?</span>
                        Is there customer support?
                    </h4>
                    <p class="text-gray-600 text-sm mt-2 ml-8">Yes! We provide email support for all plans, with priority support for premium plans. Enterprise customers get dedicated account management.</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <h4 class="font-medium text-gray-900 flex items-center gap-2">
                        <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">?</span>
                        Can I get a custom plan?
                    </h4>
                    <p class="text-gray-600 text-sm mt-2 ml-8">Absolutely! Contact our sales team for custom enterprise solutions tailored to your specific needs and requirements.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.billing-toggle.active {
    background-color: white;
    color: #1f2937;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}
.billing-toggle { outline: none; }
.billing-toggle:focus-visible { box-shadow: 0 0 0 2px #2563eb, 0 0 0 4px rgba(37,99,235,0.3); }

/* Loading skeleton animations */
.plan-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.feature-appear {
    animation: slideInUp 0.3s ease-out forwards;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Smooth transitions for plan cards */
.plan-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.plan-card:hover {
    transform: translateY(-2px);
}

/* Comparison table responsive improvements */
@media (max-width: 768px) {
    .comparison-table {
        font-size: 0.875rem;
    }

    .sticky-column {
        min-width: 120px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyBtn = document.getElementById('monthlyBtn');
    const yearlyBtn = document.getElementById('yearlyBtn');
    const monthlyPricings = document.querySelectorAll('.monthly-pricing');
    const yearlyPricings = document.querySelectorAll('.yearly-pricing');
    const cycleBadges = document.querySelectorAll('[id^="cycleBadge-"]');

    function setActive(button, active) {
        button.classList.toggle('active', active);
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
    }

    function updateCycleBadges(label) {
        cycleBadges.forEach(b => b.textContent = label);
    }

    function showMonthly() {
        window.__billingCycle = 'monthly';
        localStorage.setItem('billingCycle', 'monthly');
        setActive(monthlyBtn, true);
        setActive(yearlyBtn, false);
        monthlyPricings.forEach(el => el.classList.remove('hidden'));
        yearlyPricings.forEach(el => el.classList.add('hidden'));
        updateCycleBadges('Monthly');
    }

    function showYearly() {
        window.__billingCycle = 'yearly';
        localStorage.setItem('billingCycle', 'yearly');
        setActive(yearlyBtn, true);
        setActive(monthlyBtn, false);
        monthlyPricings.forEach(el => el.classList.add('hidden'));
        yearlyPricings.forEach(el => el.classList.remove('hidden'));
        updateCycleBadges('Yearly');
    }

    monthlyBtn.addEventListener('click', showMonthly);
    yearlyBtn.addEventListener('click', showYearly);

    // Initialize with persisted or default cycle
    const saved = localStorage.getItem('billingCycle');
    if(saved === 'yearly') {
        showYearly();
    } else {
        showMonthly();
    }

    // Add plan card hover effects
    document.querySelectorAll('[data-plan-id]').forEach(card => {
        card.classList.add('plan-card');
    });
});

// Toggle feature visibility for plans with many features
function toggleFeatures(planId) {
    const extraFeatures = document.getElementById(`extraFeatures-${planId}`);
    const toggleText = document.getElementById(`toggleText-${planId}`);
    const toggleIcon = document.getElementById(`toggleIcon-${planId}`);

    if (extraFeatures.classList.contains('hidden')) {
        extraFeatures.classList.remove('hidden');
        extraFeatures.classList.add('feature-appear');
        toggleText.textContent = 'Show less features';
        toggleIcon.style.transform = 'rotate(180deg)';
    } else {
        extraFeatures.classList.add('hidden');
        extraFeatures.classList.remove('feature-appear');
        const featuresCount = extraFeatures.children.length;
        toggleText.textContent = `Show ${featuresCount} more features`;
        toggleIcon.style.transform = 'rotate(0deg)';
    }
}

// Toggle comparison table details
function toggleComparison() {
    const table = document.getElementById('comparisonTable');
    const toggleText = document.getElementById('comparisonToggleText');
    const toggleIcon = document.getElementById('comparisonToggleIcon');

    if (table.classList.contains('hidden')) {
        table.classList.remove('hidden');
        toggleText.textContent = 'Hide Details';
        toggleIcon.style.transform = 'rotate(180deg)';
    } else {
        table.classList.add('hidden');
        toggleText.textContent = 'Show Details';
        toggleIcon.style.transform = 'rotate(0deg)';
    }
}

// Add loading states and smooth transitions
function addLoadingState(button) {
    button.innerHTML = `
        <svg class="w-4 h-4 animate-spin mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Processing...
    `;
    button.disabled = true;
}

// Keyboard navigation for plan cards
document.addEventListener('keydown', function(e) {
    if (e.target.dataset.planId && (e.key === 'Enter' || e.key === ' ')) {
        e.preventDefault();
        const button = e.target.querySelector('button[type="submit"]');
        if (button && !button.disabled) {
            button.click();
        }
    }
});
</script>
@endsection
