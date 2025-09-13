@extends('layouts.tenant')

@section('title', 'Payment Cancelled')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-red-50 to-orange-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Cancel Icon -->
        <div class="text-center">
            <div class="mx-auto h-20 w-20 bg-red-100 rounded-full flex items-center justify-center mb-6">
                <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Cancelled</h1>
            <p class="text-gray-600 mb-8">Your payment was not completed</p>
        </div>

        <!-- Cancel Details Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 space-y-6">
            <!-- What Happened -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">What Happened?</h3>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h4 class="font-medium text-yellow-800 mb-1">Payment Process Interrupted</h4>
                            <p class="text-yellow-700 text-sm">
                                @if(request('reason'))
                                    {{ ucfirst(str_replace('_', ' ', request('reason'))) }}
                                @else
                                    Your payment was cancelled before completion. This could be due to:
                                @endif
                            </p>

                            @if(!request('reason'))
                            <ul class="mt-2 text-yellow-700 text-sm space-y-1">
                                <li>• You chose to cancel the payment</li>
                                <li>• Your session timed out</li>
                                <li>• A technical issue occurred</li>
                                <li>• Insufficient funds or card declined</li>
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Status -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Current Status</h3>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subscription Status:</span>
                        @if($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-medium">Trial Active</span>
                        @elseif($currentPlan ?? false)
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-medium">Active</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-sm font-medium">Free Plan</span>
                        @endif
                    </div>

                    @if($currentPlan ?? false)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Current Plan:</span>
                        <span class="font-medium text-gray-900">{{ $currentPlan->name }}</span>
                    </div>
                    @endif

                    @if($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
                    <div class="flex justify-between">
                        <span class="text-gray-600">Trial Ends:</span>
                        <span class="font-medium text-gray-900">{{ $tenant->trial_ends_at->format('M j, Y') }}</span>
                    </div>
                    @endif
                </div>

                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <p class="text-blue-800 text-sm">
                        <strong>Good news:</strong> Your account access remains unchanged. No changes were made to your subscription.
                    </p>
                </div>
            </div>

            <!-- Next Steps -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">What Would You Like to Do?</h3>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-semibold text-sm">1</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Try Payment Again</h4>
                            <p class="text-gray-600 text-sm">Complete your subscription upgrade with a different payment method</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-green-600 font-semibold text-sm">2</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Choose a Different Plan</h4>
                            <p class="text-gray-600 text-sm">Browse other plans that might better suit your needs</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-purple-600 font-semibold text-sm">3</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Continue with Current Plan</h4>
                            <p class="text-gray-600 text-sm">Return to your dashboard and upgrade later</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-4">
            <!-- Primary Actions -->
            <div class="grid grid-cols-1 gap-3">
                @if(session('intended_plan_id'))
                <a href="{{ route('tenant.subscription.upgrade', [tenant()->slug, session('intended_plan_id')]) }}"
                   class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Try Payment Again
                </a>
                @else
                <a href="{{ route('tenant.subscription.plans', tenant()->slug) }}"
                   class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Choose a Plan
                </a>
                @endif
            </div>

            <!-- Secondary Actions -->
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('tenant.subscription.plans', tenant()->slug) }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-lg transition-colors text-center">
                    View All Plans
                </a>

                <a href="{{ route('tenant.dashboard', tenant()->slug) }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-lg transition-colors text-center">
                    Go to Dashboard
                </a>
            </div>
        </div>

        <!-- Help Section -->
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <h4 class="font-medium text-gray-900 mb-2">Need Help with Payment?</h4>
            <p class="text-gray-600 text-sm mb-3">
                If you're experiencing issues with payment processing, our support team can help you resolve them.
            </p>
            <div class="space-y-2">
                <a href="#" class="block text-blue-600 hover:text-blue-800 text-sm font-medium">Contact Support</a>
                <a href="#" class="block text-blue-600 hover:text-blue-800 text-sm font-medium">Payment FAQ</a>
            </div>
        </div>

        <!-- Common Issues -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h4 class="font-medium text-gray-900 mb-3">Common Payment Issues</h4>

            <div class="space-y-3 text-sm">
                <details class="group">
                    <summary class="flex items-center justify-between text-gray-700 cursor-pointer hover:text-gray-900">
                        <span>My card was declined</span>
                        <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="mt-2 text-gray-600 pl-4">
                        <p>Check that your card details are correct, you have sufficient funds, and your card supports online payments. Contact your bank if the issue persists.</p>
                    </div>
                </details>

                <details class="group">
                    <summary class="flex items-center justify-between text-gray-700 cursor-pointer hover:text-gray-900">
                        <span>Payment page didn't load</span>
                        <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="mt-2 text-gray-600 pl-4">
                        <p>This could be due to a slow internet connection or browser issues. Try refreshing the page or using a different browser.</p>
                    </div>
                </details>

                <details class="group">
                    <summary class="flex items-center justify-between text-gray-700 cursor-pointer hover:text-gray-900">
                        <span>Session timeout</span>
                        <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="mt-2 text-gray-600 pl-4">
                        <p>Payment sessions expire after 15 minutes for security. Simply start the process again to create a new payment session.</p>
                    </div>
                </details>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="flex items-center justify-center text-center">
            <div class="flex items-center text-gray-500 text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Your account and data remain secure</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store the intended plan for retry attempts
    @if(request('plan_id'))
        sessionStorage.setItem('intended_plan_id', '{{ request('plan_id') }}');
    @endif

    // Auto-redirect suggestion after 60 seconds
    setTimeout(function() {
        if (confirm('Would you like to return to your dashboard?')) {
            window.location.href = "{{ route('tenant.dashboard', tenant()->slug) }}";
        }
    }, 60000);
});
</script>
@endsection
