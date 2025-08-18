<?php

use App\Models\Tenant;

if (!function_exists('tenant')) {
    /**
     * Get the current tenant from the request.
     *
     * @return \App\Models\Tenant|null
     */
    function tenant()
    {
        // Get the tenant slug from the route parameter
        $tenantSlug = request()->route('tenant');

        // If it's already a Tenant model instance, return it
        if ($tenantSlug instanceof Tenant) {
            return $tenantSlug;
        }

        // If it's a string (slug), find the corresponding Tenant
        if (is_string($tenantSlug)) {
            return Tenant::where('slug', $tenantSlug)->first();
        }

        return null;
    }
}
