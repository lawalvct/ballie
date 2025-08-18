<?php

namespace App\Helpers;

use App\Models\Tenant;
use Illuminate\Support\Str;

class TenantHelper
{
    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public static function getCurrentTenant()
    {
        return app('tenant');
    }

    public static function tenantRoute(string $name, array $parameters = [])
    {
        $tenant = self::getCurrentTenant();
        $parameters['tenant'] = $tenant->slug;

        return route($name, $parameters);
    }

    public static function getDashboardUrl($tenant = null)
    {
        if (!$tenant) {
            $tenant = self::getCurrentTenant();
        }

        if (is_object($tenant)) {
            return route('tenant.dashboard', ['tenant' => $tenant->slug]);
        }

        return route('tenant.dashboard', ['tenant' => $tenant]);
    }

    public static function getPlanFeatures(string $plan): array
    {
        $features = [
            'starter' => [
                'users' => 5,
                'invoices' => 100,
                'products' => 500,
                'customers' => 1000,
                'storage' => '1GB',
                'support' => 'Email',
                'features' => [
                    'Basic Invoicing',
                    'Customer Management',
                    'Product Catalog',
                    'Basic Reports',
                    'Email Support'
                ]
            ],
            'professional' => [
                'users' => 15,
                'invoices' => 1000,
                'products' => 2000,
                'customers' => 5000,
                'storage' => '5GB',
                'support' => 'Priority Email',
                'features' => [
                    'Advanced Invoicing',
                    'Inventory Management',
                    'Financial Reports',
                    'Multi-user Access',
                    'API Access',
                    'Priority Support'
                ]
            ],
            'enterprise' => [
                'users' => 'Unlimited',
                'invoices' => 'Unlimited',
                'products' => 'Unlimited',
                'customers' => 'Unlimited',
                'storage' => '50GB',
                'support' => 'Phone & Email',
                'features' => [
                    'Full Accounting Suite',
                    'Payroll Management',
                    'Advanced Analytics',
                    'Custom Integrations',
                    'Dedicated Support',
                    'White-label Options'
                ]
            ]
        ];

        return $features[$plan] ?? [];
    }

    public static function canAccessFeature(string $feature): bool
    {
        $tenant = self::getCurrentTenant();
        $planFeatures = self::getPlanFeatures($tenant->subscription_plan);

        return in_array($feature, $planFeatures['features'] ?? []);
    }

    public static function formatCurrency(float $amount, string $currency = 'NGN'): string
    {
        $symbols = [
            'NGN' => '₦',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£'
        ];

        $symbol = $symbols[$currency] ?? $currency;

        return $symbol . number_format($amount, 2);
    }

    public static function getTrialDaysRemaining(): int
    {
        $tenant = self::getCurrentTenant();

        if (!$tenant->trial_ends_at) {
            return 0;
        }

        return max(0, now()->diffInDays($tenant->trial_ends_at, false));
    }
}

if (!function_exists('current_tenant')) {
    /**
     * Get the current tenant
     */
    function current_tenant(): ?\App\Models\Tenant
    {
        return app('current_tenant');
    }
}

if (!function_exists('tenant_route')) {
    /**
     * Generate a tenant-aware route
     */
    function tenant_route(string $name, array $parameters = [], bool $absolute = true): string
    {
        $tenant = current_tenant();
        if ($tenant && !isset($parameters['tenant'])) {
            $parameters['tenant'] = $tenant->slug;
        }

        return route($name, $parameters, $absolute);
    }
}
