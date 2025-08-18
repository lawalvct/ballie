<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOnboarding
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantSlug = $request->route('tenant');

        // If tenant is already a model instance (from route model binding)
        if ($tenantSlug instanceof Tenant) {
            $tenant = $tenantSlug;
        } else {
            // If tenant is a string, resolve it
            $tenant = Tenant::where('slug', $tenantSlug)->first();
            if (!$tenant) {
                abort(404, 'Tenant not found');
            }
        }

        // If onboarding is already completed, redirect to dashboard
        if ($tenant->hasCompletedOnboarding()) {
            return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug]);
        }

        return $next($request);
    }
}