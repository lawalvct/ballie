<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Check if this is a storefront customer route
        if ($request->is('*/store/*')) {
            $tenant = $request->route('tenant');
            // Get slug whether it's a Tenant object or string
            $tenantSlug = is_object($tenant) ? $tenant->slug : $tenant;
            return route('storefront.login', ['tenant' => $tenantSlug]);
        }

        // Check if this is a super admin route
        if ($request->is('super-admin/*')) {
            // Clear any non-super-admin intended URL from session
            $intendedUrl = session('url.intended');
            if ($intendedUrl && !str_contains($intendedUrl, '/super-admin/')) {
                session()->forget('url.intended');
            }
            return route('super-admin.login');
        }

        // Default redirection for regular users
        return route('login');
    }
}
