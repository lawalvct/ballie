@if($tenant->hasExpiredSubscription())
<div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">
                Subscription Expired
            </h3>
            <div class="mt-2 text-sm text-red-700">
                <p>
                    Your subscription expired on {{ $tenant->subscription_ends_at->format('M d, Y') }}.
                    Some features may be limited until you renew your subscription.
                </p>
            </div>
            <div class="mt-4">
                <div class="flex space-x-2">
                     <a href="{{ route('tenant.subscription.renew', tenant()->slug) }}"
                       class="bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700 transition-colors">
                        Renew Subscription
                    </a>
                    <a href="{{ route('tenant.subscription.index', ['tenant' => $tenant->slug]) }}"
                       class="bg-white text-red-600 border border-red-600 px-4 py-2 rounded-md text-sm font-medium hover:bg-red-50 transition-colors">
                        View Billing
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@elseif($tenant->isOnTrial() && $tenant->trialDaysRemaining() <= 3)
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">
                Trial Ending Soon
            </h3>
            <div class="mt-2 text-sm text-yellow-700">
                <p>
                    Your trial expires in {{ $tenant->trialDaysRemaining() }}
                    {{ $tenant->trialDaysRemaining() === 1 ? 'day' : 'days' }}.
                    Choose a plan to continue using our services.
                </p>
            </div>
            <div class="mt-4">
                <a href="{{ route('tenant.subscription.plans', ['tenant' => $tenant->slug]) }}"
                   class="bg-yellow-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-yellow-700 transition-colors">
                    Choose a Plan
                </a>
            </div>
        </div>
    </div>
</div>
@endif
