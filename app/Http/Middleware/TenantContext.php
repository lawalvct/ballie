<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantSlug = $request->route('tenant');

        // Resolve tenant if it's a string
        if (is_string($tenantSlug)) {
            $tenant = Tenant::where('slug', $tenantSlug)->first();
            if (!$tenant) {
                abort(404, 'Tenant not found');
            }
        } else {
            $tenant = $tenantSlug;
        }

        // Set tenant context for multitenancy
        if ($tenant) {

            // Check if tenant is active
            if ($tenant->is_active !== true) {
                abort(403, 'Tenant is not active');
            }

            // Make tenant current
            $tenant->makeCurrent();

            // Share tenant with all views
            view()->share('currentTenant', $tenant);

            // Add tenant to request for easy access
            $request->merge(['current_tenant' => $tenant]);
        }

        return $next($request);
    }
}
