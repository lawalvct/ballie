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

if (!function_exists('tenant_currency')) {
    /**
     * Get the current tenant's currency symbol.
     *
     * @return string
     */
    function tenant_currency()
    {
        $tenant = tenant();

        if ($tenant && isset($tenant->settings['currency_symbol'])) {
            return $tenant->settings['currency_symbol'];
        }

        // Default to Nigerian Naira symbol
        return '₦';
    }
}
