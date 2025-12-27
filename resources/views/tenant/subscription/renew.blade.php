@extends('layouts.tenant')

@section('title', 'Renew Subscription')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Renew Subscription</h1>
                <p class="text-gray-600 mt-1">Continue with your {{ $currentPlan->name }} plan</p>
            </div>
            <a href="{{ route('tenant.subscription.index', tenant()->slug) }}"
               class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Subscription Expired Alert -->
    <div class="bg-red-50 border border-red-200 rounded-xl p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-red-800">Subscription Expired</h3>
                <p class="text-red-700 mt-1">
                    Your {{ $currentPlan->name }} subscription has expired.
                    @if($tenant->subscription_ends_at)
                        It expired on {{ $tenant->subscription_ends_at->format('M j, Y') }}.
                    @endif
                    Renew now to regain access to all features.
                </p>
            </div>
        </div>
    </div>

    <!-- Current Plan Details -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl text-white p-6">
        <div class="text-center">
            <h2 class="text-2xl font-bold">{{ $currentPlan->name }} Plan</h2>
            <p class="text-blue-100 mt-2">{{ $currentPlan->description }}</p>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            @if($currentPlan->features)
                @foreach(array_slice($currentPlan->features, 0, 3) as $feature)
                <div class="bg-white bg-opacity-10 rounded-lg p-3 text-center">
                    <div class="text-sm text-blue-100">{{ $feature }}</div>
                </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Renewal Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Choose Billing Cycle</h3>

        <form action="{{ route('tenant.subscription.renew.process', tenant()->slug) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Billing Cycle Options -->
            <div class="space-y-4">
                <!-- Monthly Option -->
                <div class="relative">
                    <input type="radio"
                           id="monthly"
                           name="billing_cycle"
                           value="monthly"
                           class="peer sr-only"
                           {{ old('billing_cycle', 'monthly') === 'monthly' ? 'checked' : '' }}>
                    <label for="monthly"
                           class="flex items-center justify-between w-full p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-4 h-4 border-2 border-gray-300 rounded-full peer-checked:border-blue-500 peer-checked:bg-blue-500 relative">
                                    <div class="w-2 h-2 bg-white rounded-full absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100"></div>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="font-medium text-gray-900">Monthly Billing</div>
                                <div class="text-sm text-gray-500">Billed every month</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900">{{ $currentPlan->formatted_monthly_price }}</div>
                            <div class="text-sm text-gray-500">per month</div>
                        </div>
                    </label>
                </div>

                <!-- Yearly Option -->
                <div class="relative">
                    <input type="radio"
                           id="yearly"
                           name="billing_cycle"
                           value="yearly"
                           class="peer sr-only"
                           {{ old('billing_cycle') === 'yearly' ? 'checked' : '' }}>
                    <label for="yearly"
                           class="flex items-center justify-between w-full p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:border-green-500 peer-checked:bg-green-50">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-4 h-4 border-2 border-gray-300 rounded-full peer-checked:border-green-500 peer-checked:bg-green-500 relative">
                                    <div class="w-2 h-2 bg-white rounded-full absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100"></div>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="font-medium text-gray-900">Yearly Billing</div>
                                <div class="text-sm text-gray-500">Billed annually</div>
                                @if($currentPlan->yearly_savings_percentage > 0)
                                <div class="text-sm text-green-600 font-medium">
                                    Save {{ $currentPlan->yearly_savings_percentage }}%
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900">{{ $currentPlan->formatted_yearly_price }}</div>
                            <div class="text-sm text-gray-500">per year</div>
                            @if($currentPlan->yearly_savings_percentage > 0)
                            <div class="text-xs text-green-600">
                                Save {{ $currentPlan->formatted_yearly_savings }}
                            </div>
                            @endif
                        </div>
                    </label>
                </div>
            </div>

            @error('billing_cycle')
                <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror

            <!-- Payment Method Selection -->
            <div class="border-t border-gray-200 pt-6">
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

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6">
                <a href="{{ route('tenant.subscription.plans', tenant()->slug) }}"
                   class="text-gray-600 hover:text-gray-800">
                    Choose Different Plan
                </a>

                <div class="flex space-x-3">
                    <a href="{{ route('tenant.subscription.index', tenant()->slug) }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Renew Subscription
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Security Notice -->
    <div class="bg-gray-50 rounded-xl p-4">
        <div class="flex items-center text-sm text-gray-600">
            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            Secure payment processing powered by Nomba & Paystack. Your payment information is encrypted and secure.
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-submit form when billing cycle changes (optional UX enhancement)
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="billing_cycle"]');
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Optional: Add visual feedback when selection changes
            console.log('Billing cycle changed to:', this.value);
        });
    });
});
</script>
@endpush
