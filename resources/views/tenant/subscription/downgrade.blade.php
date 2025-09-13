@extends('layouts.tenant')

@section('title', 'Downgrade to ' . $plan->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Downgrade Your Plan</h1>
                <p class="text-gray-600 mt-1">You're downgrading to the {{ $plan->name }} plan</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('tenant.subscription.plans', tenant()->slug) }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Plans
                </a>
            </div>
        </div>
    </div>

    <!-- Warning Notice -->
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-orange-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-orange-900 mb-2">Important: Plan Downgrade Notice</h3>
                <div class="text-orange-800 space-y-2">
                    <p>• Your downgrade will take effect at the end of your current billing period</p>
                    <p>• You'll retain access to current features until then</p>
                    <p>• Some features may be limited after downgrade</p>
                    <p>• No immediate charge - changes apply to next billing cycle</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Current vs New Plan Comparison -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Current Plan -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm border border-blue-200 p-6 relative">
            <div class="absolute top-0 right-0 bg-blue-500 text-white px-3 py-1 rounded-tr-xl rounded-bl-xl text-sm font-medium">
                Current
            </div>

            <div class="text-center mt-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $currentPlan->name }}</h3>
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $currentPlan->formatted_monthly_price }}</div>
                <span class="text-gray-600">/month</span>
                <p class="text-gray-600 mt-2">{{ $currentPlan->description }}</p>
            </div>

            <div class="mt-6 space-y-3">
                <h4 class="font-medium text-gray-900">Current Features:</h4>
                @if($currentPlan->features)
                    @foreach(array_slice($currentPlan->features, 0, 8) as $feature)
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

        <!-- New Plan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $plan->name }}</h3>
                <div class="pricing-container" id="newPlanPricing">
                    <div class="monthly-pricing">
                        <div class="text-3xl font-bold text-gray-900">{{ $plan->formatted_monthly_price }}</div>
                        <span class="text-gray-600">/month</span>
                    </div>
                    <div class="yearly-pricing hidden">
                        <div class="text-3xl font-bold text-gray-900">{{ $plan->formatted_yearly_price }}</div>
                        <span class="text-gray-600">/year</span>
                    </div>
                </div>
                <p class="text-gray-600 mt-2">{{ $plan->description }}</p>
            </div>

            <div class="mt-6 space-y-3">
                <h4 class="font-medium text-gray-900">Available Features:</h4>
                @if($plan->features)
                    @foreach(array_slice($plan->features, 0, 5) as $feature)
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

    <!-- Downgrade Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Schedule Downgrade</h3>

        <form method="POST" action="{{ route('tenant.subscription.downgrade.process', ['tenant' => tenant()->slug, 'plan' => $plan->id]) }}" id="downgradeForm">
            @csrf

            <!-- Billing Cycle Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Choose Billing Cycle</label>
                <div class="flex justify-center">
                    <div class="inline-flex items-center bg-gray-100 rounded-lg p-1">
                        <button type="button" id="monthlyBtn" class="billing-toggle px-4 py-2 text-sm font-medium rounded-md transition-all duration-200"
                                aria-pressed="true" data-cycle="monthly">
                            Monthly
                        </button>
                        <button type="button" id="yearlyBtn" class="billing-toggle px-4 py-2 text-sm font-medium rounded-md transition-all duration-200"
                                aria-pressed="false" data-cycle="yearly">
                            Yearly
                        </button>
                    </div>
                </div>
                <input type="hidden" name="billing_cycle" value="monthly" id="billingCycleInput">
            </div>

            <!-- Reason for Downgrade -->
            <div class="mb-6">
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Why are you downgrading? <span class="text-red-500">*</span>
                </label>
                <select name="reason" id="reason" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a reason</option>
                    <option value="cost">Cost/Budget constraints</option>
                    <option value="features">Don't need all the features</option>
                    <option value="usage">Lower usage than expected</option>
                    <option value="temporary">Temporary downgrade</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <!-- Additional Feedback -->
            <div class="mb-6">
                <label for="feedback" class="block text-sm font-medium text-gray-700 mb-2">
                    Additional feedback (optional)
                </label>
                <textarea name="feedback" id="feedback" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Help us improve by sharing your experience..."></textarea>
            </div>

            <div class="text-center">
                <button type="submit"
                        class="bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                        id="downgradeBtn">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                        </svg>
                        <span>Schedule Downgrade</span>
                    </span>
                </button>
            </div>

            <div class="mt-4 text-center text-sm text-gray-600">
                <p>• Downgrade takes effect at the end of current billing period</p>
                <p>• You can cancel this downgrade anytime before it takes effect</p>
                <p>• No immediate charges or refunds</p>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyBtn = document.getElementById('monthlyBtn');
    const yearlyBtn = document.getElementById('yearlyBtn');
    const billingCycleInput = document.getElementById('billingCycleInput');
    const newPlanPricing = document.getElementById('newPlanPricing');

    function showMonthly() {
        monthlyBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
        monthlyBtn.classList.remove('text-gray-600');
        yearlyBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
        yearlyBtn.classList.add('text-gray-600');

        monthlyBtn.setAttribute('aria-pressed', 'true');
        yearlyBtn.setAttribute('aria-pressed', 'false');

        billingCycleInput.value = 'monthly';

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
    document.getElementById('downgradeForm').addEventListener('submit', function() {
        const btn = document.getElementById('downgradeBtn');
        btn.innerHTML = `
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Scheduling Downgrade...
            </span>
        `;
        btn.disabled = true;
    });
});
</script>
@endsection
