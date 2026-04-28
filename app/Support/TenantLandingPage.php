<?php

namespace App\Support;

use App\Models\Tenant;
use App\Models\User;
use App\Services\ModuleRegistry;
use Illuminate\Support\Facades\Route;

class TenantLandingPage
{
    public static function urlFor(User $user, Tenant $tenant): string
    {
        return route(self::routeNameFor($user, $tenant), ['tenant' => $tenant->slug]);
    }

    public static function routeNameFor(User $user, Tenant $tenant): string
    {
        foreach (self::candidates() as $candidate) {
            if (! Route::has($candidate['route'])) {
                continue;
            }

            if (! self::moduleIsEnabled($tenant, $candidate['module'] ?? null)) {
                continue;
            }

            if (self::userHasAnyPermission($user, $candidate['permissions'] ?? [])) {
                return $candidate['route'];
            }
        }

        return Route::has('tenant.support.index') ? 'tenant.support.index' : 'tenant.profile.index';
    }

    public static function intendedUrlOrLandingUrl(User $user, Tenant $tenant, ?string $intendedUrl): string
    {
        if ($intendedUrl && self::isAllowedIntendedUrl($user, $tenant, $intendedUrl)) {
            return $intendedUrl;
        }

        return self::urlFor($user, $tenant);
    }

    private static function candidates(): array
    {
        return [
            ['route' => 'tenant.dashboard', 'module' => 'dashboard', 'permissions' => ['dashboard.view']],
            ['route' => 'tenant.inventory.index', 'module' => 'inventory', 'permissions' => ['inventory.view']],
            ['route' => 'tenant.reports.index', 'module' => 'reports', 'permissions' => array_merge(['reports.view'], ReportPermissionMatrix::allSlugs())],
            ['route' => 'tenant.accounting.index', 'module' => 'accounting', 'permissions' => ['accounting.view']],
            ['route' => 'tenant.crm.index', 'module' => 'crm', 'permissions' => ['crm.view']],
            ['route' => 'tenant.projects.index', 'module' => 'projects', 'permissions' => ['projects.view']],
            ['route' => 'tenant.pos.index', 'module' => 'pos', 'permissions' => ['pos.access']],
            ['route' => 'tenant.ecommerce.settings.index', 'module' => 'ecommerce', 'permissions' => ['ecommerce.view']],
            ['route' => 'tenant.payroll.index', 'module' => 'payroll', 'permissions' => ['payroll.view']],
            ['route' => 'tenant.admin.index', 'module' => 'admin', 'permissions' => ['admin.users.manage', 'admin.roles.manage', 'admin.permissions.manage']],
            ['route' => 'tenant.statutory.index', 'module' => 'statutory', 'permissions' => ['statutory.view']],
            ['route' => 'tenant.audit.index', 'module' => 'audit', 'permissions' => ['audit.view']],
            ['route' => 'tenant.settings.company', 'module' => 'settings', 'permissions' => ['settings.view']],
        ];
    }

    private static function moduleIsEnabled(Tenant $tenant, ?string $module): bool
    {
        return ! $module || ModuleRegistry::isModuleEnabled($tenant, $module);
    }

    private static function userHasAnyPermission(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    private static function isAllowedIntendedUrl(User $user, Tenant $tenant, string $intendedUrl): bool
    {
        $dashboardUrl = route('tenant.dashboard', ['tenant' => $tenant->slug]);

        if (rtrim($intendedUrl, '/') !== rtrim($dashboardUrl, '/')) {
            return true;
        }

        return $user->hasPermission('dashboard.view');
    }
}
