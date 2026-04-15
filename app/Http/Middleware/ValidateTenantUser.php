<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidateTenantUser
{
    /**
     * Ensure the authenticated user belongs to the tenant in the URL.
     *
     * If a user is logged in but their tenant_id does not match the tenant
     * resolved from the URL slug, redirect them to their own tenant's dashboard.
     * This prevents cross-tenant access after session expiry + re-login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        $tenant = $request->route('tenant');

        if (!$tenant) {
            return $next($request);
        }

        // Resolve tenant ID whether it's an object or slug string
        $tenantId = is_object($tenant) ? $tenant->id : null;

        if (!$tenantId) {
            return $next($request);
        }

        // Check if user belongs to this tenant
        if ((int) $user->tenant_id !== (int) $tenantId) {
            // User does not belong to this tenant - redirect to their own tenant
            if ($user->tenant) {
                return redirect()->route('tenant.dashboard', ['tenant' => $user->tenant->slug])
                    ->with('warning', 'You have been redirected to your own company dashboard.');
            }

            // User has no tenant assigned - log them out
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Your account is not associated with any company.');
        }

        return $next($request);
    }
}
