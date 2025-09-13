@extends('layouts.tenant')

@section('title', 'Cancel Subscription')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Cancel Subscription</h1>
                <p class="text-gray-600 mt-1">We're sorry to see you go</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('tenant.subscription.index', tenant()->slug) }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Back to Subscription
                </a>
            </div>
        </div>
    </div>

    <!-- Warning Notice -->
    <div class="bg-red-50 border border-red-200 rounded-xl p-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-red-900 mb-2">Important: Subscription Cancellation</h3>
                <div class="text-red-800 space-y-2">
                    <p>• Your subscription will be cancelled immediately</p>
                    @if($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
                        <p>• You'll retain access until your trial ends on {{ $tenant->trial_ends_at->format('M j, Y') }}</p>
                    @else
                        <p>• You'll retain access until the end of your current billing period</p>
                    @endif
                    <p>• All data will be preserved for 30 days after cancellation</p>
                    <p>• You can reactivate anytime during this period</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Plan Info -->
    @if($currentPlan)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">You're Currently On</h3>

        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
                <h4 class="text-xl font-bold text-gray-900">{{ $currentPlan->name }}</h4>
                <p class="text-gray-600">{{ $currentPlan->description }}</p>
                <div class="mt-2">
                    <span class="text-2xl font-bold text-gray-900">{{ $currentPlan->formatted_monthly_price }}</span>
                    <span class="text-gray-600">/month</span>
                </div>
            </div>

            <div class="text-right">
                @if($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
                    <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        Trial: {{ $tenant->trialDaysRemaining() }} days left
                    </div>
                @else
                    <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        Active Subscription
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
            @if($currentPlan->features)
                @foreach(array_slice($currentPlan->features, 0, 4) as $feature)
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700 text-sm">{{ $feature }}</span>
                </div>
                @endforeach
            @endif
        </div>
    </div>
    @endif

    <!-- Before You Go Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Before You Go...</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="font-medium text-gray-900">Need Help?</h4>
                        <p class="text-gray-600 text-sm">Our support team is here to help with any issues you're facing.</p>
                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Contact Support</a>
                    </div>
                </div>

                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <div>
                        <h4 class="font-medium text-gray-900">Consider Downgrading</h4>
                        <p class="text-gray-600 text-sm">Maybe a lower plan would work better for your needs?</p>
                        <a href="{{ route('tenant.subscription.plans', tenant()->slug) }}" class="text-green-600 hover:text-green-800 text-sm font-medium">View Plans</a>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-purple-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    <div>
                        <h4 class="font-medium text-gray-900">Pause Instead?</h4>
                        <p class="text-gray-600 text-sm">You can pause your subscription for up to 3 months.</p>
                        <a href="#" class="text-purple-600 hover:text-purple-800 text-sm font-medium">Learn More</a>
                    </div>
                </div>

                <div class="flex items-start">
                    <svg class="w-5 h-5 text-orange-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="font-medium text-gray-900">Export Your Data</h4>
                        <p class="text-gray-600 text-sm">Download all your data before cancelling your subscription.</p>
                        <a href="#" class="text-orange-600 hover:text-orange-800 text-sm font-medium">Export Data</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellation Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cancellation Details</h3>

        <form method="POST" action="{{ route('tenant.subscription.cancel.process', tenant()->slug) }}" id="cancelForm">
            @csrf

            <!-- Reason for Cancellation -->
            <div class="mb-6">
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Why are you cancelling? <span class="text-red-500">*</span>
                </label>
                <select name="reason" id="reason" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">Select a reason</option>
                    <option value="too_expensive">Too expensive</option>
                    <option value="not_using">Not using enough</option>
                    <option value="missing_features">Missing features I need</option>
                    <option value="found_alternative">Found a better alternative</option>
                    <option value="business_closed">Business closed/changed</option>
                    <option value="technical_issues">Technical issues</option>
                    <option value="temporary">Temporary cancellation</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <!-- Additional Feedback -->
            <div class="mb-6">
                <label for="feedback" class="block text-sm font-medium text-gray-700 mb-2">
                    How can we improve? (optional)
                </label>
                <textarea name="feedback" id="feedback" rows="4"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                          placeholder="Your feedback helps us improve our service for everyone..."></textarea>
            </div>

            <!-- Final Confirmation -->
            <div class="mb-6">
                <label class="flex items-start">
                    <input type="checkbox" required class="mt-1 mr-3 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <span class="text-sm text-gray-700">
                        I understand that my subscription will be cancelled and I'll lose access to premium features
                        @if($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
                            when my trial ends on {{ $tenant->trial_ends_at->format('M j, Y') }}.
                        @else
                            at the end of my current billing period.
                        @endif
                    </span>
                </label>
            </div>

            <div class="flex gap-4">
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        id="cancelBtn">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span>Cancel My Subscription</span>
                    </span>
                </button>

                <a href="{{ route('tenant.subscription.index', tenant()->slug) }}"
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg transition-all duration-200 text-center">
                    Keep My Subscription
                </a>
            </div>

            <div class="mt-4 text-center text-sm text-gray-600">
                <p>• You can reactivate anytime within 30 days</p>
                <p>• Your data will be preserved during this period</p>
                <p>• No charges will occur after cancellation</p>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission
    document.getElementById('cancelForm').addEventListener('submit', function(e) {
        const confirmation = confirm('Are you absolutely sure you want to cancel your subscription? This action cannot be undone.');

        if (!confirmation) {
            e.preventDefault();
            return false;
        }

        const btn = document.getElementById('cancelBtn');
        btn.innerHTML = `
            <span class="flex items-center justify-center gap-2">
                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Cancelling Subscription...
            </span>
        `;
        btn.disabled = true;
    });
});
</script>
@endsection
