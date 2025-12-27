@extends('layouts.tenant')

@section('title', 'Upgrade to ' . $plan->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Upgrade Your Plan</h1>
                <p class="text-gray-600 mt-1">You're upgrading to the {{ $plan->name }} plan</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('tenant.subscription.plans', tenant()->slug) }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Plans
                </a>
            </div>
        </div>
    </div>

    <!-- Current vs New Plan Comparison -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Current Plan -->
        @if($currentPlan)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Current Plan</h3>
                <div class="text-3xl font-bold text-gray-600 mb-2">{{ $currentPlan->name }}</div>
                <div class="text-2xl font-bold text-gray-900">
                    {{ $currentPlan->formatted_monthly_price }}<span class="text-sm font-normal text-gray-600">/month</span>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <h4 class="font-medium text-gray-900">Current Features:</h4>
                @if($currentPlan->features)
                    @foreach(array_slice($currentPlan->features, 0, 5) as $feature)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-700 text-sm">{{ $feature }}</span>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif

        <!-- New Plan -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm border border-blue-200 p-6 relative">
            <div class="absolute top-0 right-0 bg-blue-500 text-white px-3 py-1 rounded-tr-xl rounded-bl-xl text-sm font-medium">
                Upgrading To
            </div>

            <div class="text-center mt-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $plan->name }}</h3>
                <div class="pricing-container" id="newPlanPricing">
                    <div class="monthly-pricing">
                        <div class="text-3xl font-bold text-blue-600">{{ $plan->formatted_monthly_price }}</div>
                        <span class="text-gray-600">/month</span>
                    </div>
                    <div class="yearly-pricing hidden">
                        <div class="text-3xl font-bold text-blue-600">{{ $plan->formatted_yearly_price }}</div>
                        <span class="text-gray-600">/year</span>
                        @php
                            $monthlyCost = $plan->monthly_price * 12;
                            $yearlyCost = $plan->yearly_price;
                            $savings = $monthlyCost - $yearlyCost;
                        @endphp
                        @if($savings > 0)
                            <div class="mt-2 inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                                <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm3.707 6.293l-4 4a1 1 0 01-1.414 0l-2-2 1.414-1.414L9 10.586l3.293-3.293 1.414 1.414z"/></svg>
                                Save ₦{{ number_format($savings / 100, 2) }}
                            </div>
                        @endif
                    </div>
                </div>
                <p class="text-gray-600 mt-2">{{ $plan->description }}</p>
            </div>

            <div class="mt-6 space-y-3">
                <h4 class="font-medium text-gray-900">New Features:</h4>
                @if($plan->features)
                    @foreach(array_slice($plan->features, 0, 8) as $feature)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-700 text-sm">{{ $feature }}</span>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Billing Cycle Selection -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Choose Your Billing Cycle</h3>

        <div class="flex justify-center mb-6">
            <div class="inline-flex items-center bg-gray-100 rounded-lg p-1">
                <button id="monthlyBtn" type="button" class="billing-toggle px-4 py-2 text-sm font-medium rounded-md transition-all duration-200"
                        aria-pressed="true" data-cycle="monthly">
                    Monthly
                </button>
                <button id="yearlyBtn" type="button" class="billing-toggle px-4 py-2 text-sm font-medium rounded-md transition-all duration-200"
                        aria-pressed="false" data-cycle="yearly">
                    Yearly
                    <span class="text-green-700 text-xs ml-1 font-semibold">Save 20%</span>
                </button>
            </div>
        </div>

        <!-- Upgrade Form -->
        <form method="POST" action="{{ route('tenant.subscription.upgrade.process', ['tenant' => tenant()->slug, 'plan' => $plan->id]) }}" id="upgradeForm">
            @csrf
            <input type="hidden" name="billing_cycle" value="monthly" id="billingCycleInput">

            <!-- Payment Method Selection -->
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Choose Payment Method</h4>
                <div class="space-y-3">
                    <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio"
                               name="payment_method"
                               value="nomba"
                               class="mt-1"
                               checked
                               required>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800 flex items-center gap-2">
                                Pay with Nomba
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Secure</span>
                            </div>
                            <div class="text-gray-600 text-sm mt-1">Pay securely with card, bank transfer, or USSD via Nomba</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                        <input type="radio"
                               name="payment_method"
                               value="paystack"
                               class="mt-1"
                               required>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800 flex items-center gap-2">
                                Pay with Paystack
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full">Secure</span>
                            </div>
                            <div class="text-gray-600 text-sm mt-1">Pay securely with card, bank transfer, or USSD via Paystack</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="text-center">
                <button type="submit"
                        class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        id="upgradeBtn">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <span id="upgradeText">Upgrade to {{ $plan->name }} - Monthly</span>
                    </span>
                </button>
            </div>

            <div class="mt-4 text-center text-sm text-gray-600">
                <p>• Secure payment processing</p>
                <p>• Upgrade takes effect immediately</p>
                <p>• You can change or cancel anytime</p>
                @if($plan->trial_days > 0 && !$tenant->hasUsedTrial())
                    <p class="text-green-600 font-medium">• Includes {{ $plan->trial_days }} days free trial</p>
                @endif
            </div>
        </form>
    </div>

    <!-- FAQ Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Frequently Asked Questions</h3>

        <div class="space-y-4">
            <div class="border-b border-gray-200 pb-4">
                <h4 class="font-medium text-gray-900 mb-2">When will the upgrade take effect?</h4>
                <p class="text-gray-600 text-sm">Your upgrade will be activated immediately after successful payment. You'll have access to all new features right away.</p>
            </div>

            <div class="border-b border-gray-200 pb-4">
                <h4 class="font-medium text-gray-900 mb-2">What happens to my current billing cycle?</h4>
                <p class="text-gray-600 text-sm">We'll prorate your current plan and apply any credits to your new billing cycle. You'll only pay the difference.</p>
            </div>

            <div class="border-b border-gray-200 pb-4">
                <h4 class="font-medium text-gray-900 mb-2">Can I downgrade later?</h4>
                <p class="text-gray-600 text-sm">Yes, you can downgrade to a lower plan anytime. The downgrade will take effect at the end of your current billing period.</p>
            </div>

            <div>
                <h4 class="font-medium text-gray-900 mb-2">Is my payment secure?</h4>
                <p class="text-gray-600 text-sm">Yes, all payments are processed securely through our trusted payment partners (Nomba & Paystack). We never store your payment information.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyBtn = document.getElementById('monthlyBtn');
    const yearlyBtn = document.getElementById('yearlyBtn');
    const billingCycleInput = document.getElementById('billingCycleInput');
    const upgradeText = document.getElementById('upgradeText');
    const newPlanPricing = document.getElementById('newPlanPricing');

    function showMonthly() {
        monthlyBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
        monthlyBtn.classList.remove('text-gray-600');
        yearlyBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
        yearlyBtn.classList.add('text-gray-600');

        monthlyBtn.setAttribute('aria-pressed', 'true');
        yearlyBtn.setAttribute('aria-pressed', 'false');

        billingCycleInput.value = 'monthly';
        upgradeText.textContent = 'Upgrade to {{ $plan->name }} - Monthly';

        // Update pricing display
        const monthlyPricing = newPlanPricing.querySelector('.monthly-pricing');
        const yearlyPricing = newPlanPricing.querySelector('.yearly-pricing');
        monthlyPricing.classList.remove('hidden');
        yearlyPricing.classList.add('hidden');
    }

    function showYearly() {
        yearlyBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
        yearlyBtn.classList.remove('text-gray-600');
        monthlyBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
        monthlyBtn.classList.add('text-gray-600');

        yearlyBtn.setAttribute('aria-pressed', 'true');
        monthlyBtn.setAttribute('aria-pressed', 'false');

        billingCycleInput.value = 'yearly';
        upgradeText.textContent = 'Upgrade to {{ $plan->name }} - Yearly';

        // Update pricing display
        const monthlyPricing = newPlanPricing.querySelector('.monthly-pricing');
        const yearlyPricing = newPlanPricing.querySelector('.yearly-pricing');
        monthlyPricing.classList.add('hidden');
        yearlyPricing.classList.remove('hidden');
    }

    monthlyBtn.addEventListener('click', showMonthly);
    yearlyBtn.addEventListener('click', showYearly);

    // Initialize with monthly
    showMonthly();

    // Handle form submission
    document.getElementById('upgradeForm').addEventListener('submit', function() {
        const btn = document.getElementById('upgradeBtn');
        btn.innerHTML = `
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing Upgrade...
            </span>
        `;
        btn.disabled = true;
    });
});
</script>
@endsection
