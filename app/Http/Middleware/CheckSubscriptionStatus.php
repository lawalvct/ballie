<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CheckSubscriptionStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $tenant = tenant();

        if (!$tenant) {
            return $next($request);
        }

        // Handle expired subscriptions (both active expired and already marked expired)
        if ($tenant->hasExpiredSubscription() || $tenant->subscription_status === 'expired') {
            // Update status if not already marked as expired
            if ($tenant->subscription_status !== 'expired') {
                $tenant->handleExpiredSubscription();
            }

            // Allow access to subscription and billing routes
            $allowedRoutes = [
                'tenant.subscription.*',
                'tenant.logout',
                'logout'
            ];

            foreach ($allowedRoutes as $allowedRoute) {
                if ($request->routeIs($allowedRoute)) {
                    return $next($request);
                }
            }

            // Redirect to subscription renewal page
            return redirect()
                ->route('tenant.subscription.renew', ['tenant' => $tenant->slug])
                ->with('warning', 'Your subscription has expired. Please renew to continue using our services.');
        }

        // Handle trial expiration
        if ($tenant->subscription_status === 'trial' && $tenant->trial_ends_at && $tenant->trial_ends_at->isPast()) {
            $tenant->update(['subscription_status' => 'expired']);

            return redirect()
                ->route('tenant.subscription.plans', ['tenant' => $tenant->slug])
                ->with('warning', 'Your trial period has ended. Please choose a plan to continue.');
        }

        return $next($request);
    }
}
