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
    <div class="text-center">
        <div class="inline-flex items-center bg-gray-100 rounded-lg p-1">
            <button id="monthlyBtn" class="billing-toggle px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 active">
                Monthly
            </button>
            <button id="yearlyBtn" class="billing-toggle px-4 py-2 text-sm font-medium rounded-md transition-all duration-200">
                Yearly <span class="text-green-600 text-xs ml-1">(Save 20%)</span>
            </button>
        </div>
    </div>

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($plans as $plan)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300 {{ $plan->is_popular ? 'ring-2 ring-blue-500' : '' }}">
            @if($plan->is_popular)
            <div class="bg-blue-500 text-white text-center py-2 text-sm font-semibold">
                Most Popular
            </div>
            @endif

            <div class="p-6">
                <!-- Plan Header -->
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                    <div class="pricing-container">
                        <div class="monthly-pricing">
                            <span class="text-3xl font-bold text-gray-900">{{ $plan->formatted_monthly_price }}</span>
                            <span class="text-gray-600">/month</span>
                        </div>
                        <div class="yearly-pricing hidden">
                            <span class="text-3xl font-bold text-gray-900">{{ $plan->formatted_yearly_price }}</span>
                            <span class="text-gray-600">/year</span>
                            <div class="text-sm text-green-600 mt-1">
                                @php
                                    $monthlyCost = $plan->monthly_price * 12;
                                    $yearlyCost = $plan->yearly_price;
                                    $savings = $monthlyCost - $yearlyCost;
                                @endphp
                                @if($savings > 0)
                                    Save ₦{{ number_format($savings / 100, 2) }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 mt-2">{{ $plan->description }}</p>
                </div>

                <!-- Features -->
                <div class="space-y-3 mb-6">
                    @if($plan->features)
                        @foreach(array_slice($plan->features, 0, 8) as $feature)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700 text-sm">{{ $feature }}</span>
                        </div>
                        @endforeach
                    @endif
                </div>

                <!-- Action Button -->
                <div class="text-center">
                    @if($currentPlan && $currentPlan->id === $plan->id)
                        <button class="w-full bg-gray-200 text-gray-500 py-2 px-4 rounded-lg cursor-not-allowed" disabled>
                            Current Plan
                        </button>
                    @elseif($currentPlan && $currentPlan->monthly_price < $plan->monthly_price)
                        <a href="{{ route('tenant.subscription.upgrade', ['tenant' => tenant()->slug, 'plan' => $plan->id]) }}"
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors inline-block text-center">
                            Upgrade to {{ $plan->name }}
                        </a>
                    @elseif($currentPlan && $currentPlan->monthly_price > $plan->monthly_price)
                        <a href="{{ route('tenant.subscription.downgrade', ['tenant' => tenant()->slug, 'plan' => $plan->id]) }}"
                           class="w-full bg-orange-600 text-white py-2 px-4 rounded-lg hover:bg-orange-700 transition-colors inline-block text-center">
                            Downgrade to {{ $plan->name }}
                        </a>
                    @else
                        <a href="{{ route('tenant.subscription.upgrade', ['tenant' => tenant()->slug, 'plan' => $plan->id]) }}"
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors inline-block text-center">
                            Choose {{ $plan->name }}
                        </a>
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
            <h2 class="text-xl font-semibold text-gray-900">Compare Plans</h2>
            <p class="text-gray-600 mt-1">Detailed comparison of all plan features</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-900">Features</th>
                        @foreach($plans as $plan)
                        <th class="px-6 py-4 text-center text-sm font-medium text-gray-900">
                            {{ $plan->name }}
                            @if($currentPlan && $currentPlan->id === $plan->id)
                                <span class="block text-xs text-blue-600 font-normal">Current</span>
                            @endif
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Monthly Price -->
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">Monthly Price</td>
                        @foreach($plans as $plan)
                        <td class="px-6 py-4 text-center text-sm text-gray-700">{{ $plan->formatted_monthly_price }}</td>
                        @endforeach
                    </tr>

                    <!-- Yearly Price -->
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">Yearly Price</td>
                        @foreach($plans as $plan)
                        <td class="px-6 py-4 text-center text-sm text-gray-700">{{ $plan->formatted_yearly_price }}</td>
                        @endforeach
                    </tr>

                    <!-- Feature rows would go here based on your plan structure -->
                    @if($plans->first() && $plans->first()->limits)
                    @foreach($plans->first()->limits as $limitKey => $limitValue)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $limitKey)) }}</td>
                        @foreach($plans as $plan)
                        <td class="px-6 py-4 text-center text-sm text-gray-700">
                            {{ $plan->limits[$limitKey] ?? 'N/A' }}
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Frequently Asked Questions</h3>
        <div class="space-y-4">
            <div>
                <h4 class="font-medium text-gray-900">Can I change my plan at any time?</h4>
                <p class="text-gray-600 text-sm mt-1">Yes, you can upgrade or downgrade your plan at any time. Changes take effect immediately for upgrades, or at the end of your current billing cycle for downgrades.</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900">What happens to my data if I downgrade?</h4>
                <p class="text-gray-600 text-sm mt-1">Your data is always safe. If you exceed the limits of a lower plan, you'll have read-only access until you upgrade again or remove excess data.</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900">Do you offer refunds?</h4>
                <p class="text-gray-600 text-sm mt-1">We offer a 30-day money-back guarantee on all plans. Contact support if you're not satisfied with your subscription.</p>
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyBtn = document.getElementById('monthlyBtn');
    const yearlyBtn = document.getElementById('yearlyBtn');
    const monthlyPricings = document.querySelectorAll('.monthly-pricing');
    const yearlyPricings = document.querySelectorAll('.yearly-pricing');

    function showMonthly() {
        monthlyBtn.classList.add('active');
        yearlyBtn.classList.remove('active');
        monthlyPricings.forEach(el => el.classList.remove('hidden'));
        yearlyPricings.forEach(el => el.classList.add('hidden'));
    }

    function showYearly() {
        yearlyBtn.classList.add('active');
        monthlyBtn.classList.remove('active');
        monthlyPricings.forEach(el => el.classList.add('hidden'));
        yearlyPricings.forEach(el => el.classList.remove('hidden'));
    }

    monthlyBtn.addEventListener('click', showMonthly);
    yearlyBtn.addEventListener('click', showYearly);

    // Initialize with monthly view
    showMonthly();
});
</script>
@endsection
