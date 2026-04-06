<?php

namespace App\Services;

use App\Models\Tenant;

/**
 * Central registry of all modules in Ballie.
 *
 * Defines which modules are available, their metadata, and default
 * visibility per business category. Used by sidebar, middleware,
 * company settings, and onboarding.
 *
 * Safe for existing tenants: if enabled_modules is NULL, defaults
 * to 'hybrid' category (all modules visible = current behavior).
 */
class ModuleRegistry
{
    // ─── Module Keys ──────────────────────────────────────────
    const MODULE_DASHBOARD    = 'dashboard';
    const MODULE_ACCOUNTING   = 'accounting';
    const MODULE_INVENTORY    = 'inventory';
    const MODULE_CRM          = 'crm';
    const MODULE_POS          = 'pos';
    const MODULE_ECOMMERCE    = 'ecommerce';
    const MODULE_PAYROLL      = 'payroll';
    const MODULE_PROCUREMENT  = 'procurement';
    const MODULE_BANKING      = 'banking';
    const MODULE_PROJECTS     = 'projects';
    const MODULE_REPORTS      = 'reports';
    const MODULE_STATUTORY    = 'statutory';
    const MODULE_AUDIT        = 'audit';
    const MODULE_ADMIN        = 'admin';
    const MODULE_SETTINGS     = 'settings';
    const MODULE_SUPPORT          = 'support';
    const MODULE_HELP             = 'help';
    const MODULE_ONLINE_PAYMENTS  = 'online_payments';

    /**
     * All known module keys.
     */
    const ALL_MODULES = [
        self::MODULE_DASHBOARD,
        self::MODULE_ACCOUNTING,
        self::MODULE_INVENTORY,
        self::MODULE_CRM,
        self::MODULE_POS,
        self::MODULE_ECOMMERCE,
        self::MODULE_PAYROLL,
        self::MODULE_PROCUREMENT,
        self::MODULE_BANKING,
        self::MODULE_PROJECTS,
        self::MODULE_REPORTS,
        self::MODULE_STATUTORY,
        self::MODULE_AUDIT,
        self::MODULE_ADMIN,
        self::MODULE_SETTINGS,
        self::MODULE_SUPPORT,
        self::MODULE_HELP,
        self::MODULE_ONLINE_PAYMENTS,
    ];

    /**
     * Core modules that can NEVER be disabled.
     */
    const CORE_MODULES = [
        self::MODULE_DASHBOARD,
        self::MODULE_ACCOUNTING,
        self::MODULE_ADMIN,
        self::MODULE_SETTINGS,
        self::MODULE_SUPPORT,
        self::MODULE_HELP,
    ];

    /**
     * Default module sets per business category.
     * Existing tenants without enabled_modules will resolve to 'hybrid'.
     */
    const CATEGORY_DEFAULTS = [
        'trading' => [
            'dashboard', 'accounting', 'inventory', 'crm', 'pos',
            'ecommerce', 'procurement', 'payroll', 'banking',
            'reports', 'statutory', 'audit', 'admin', 'settings',
            'support', 'help', 'online_payments',
        ],
        'manufacturing' => [
            'dashboard', 'accounting', 'inventory', 'crm',
            'procurement', 'payroll', 'banking', 'reports',
            'statutory', 'audit', 'admin', 'settings',
            'support', 'help',
        ],
        'service' => [
            'dashboard', 'accounting', 'crm', 'projects',
            'payroll', 'banking', 'reports', 'statutory',
            'audit', 'admin', 'settings', 'support', 'help',
        ],
        'hybrid' => [
            'dashboard', 'accounting', 'inventory', 'crm',
            'projects', 'pos', 'ecommerce', 'procurement',
            'payroll', 'banking', 'reports', 'statutory',
            'audit', 'admin', 'settings', 'support', 'help',
            'online_payments',
        ],
    ];

    /**
     * Module metadata — display names, descriptions, icons.
     */
    const MODULE_META = [
        'dashboard'   => ['name' => 'Dashboard',    'description' => 'Business overview and analytics',       'icon' => 'fas fa-tachometer-alt'],
        'accounting'  => ['name' => 'Accounting',   'description' => 'Chart of accounts, vouchers, ledger',   'icon' => 'fas fa-calculator'],
        'inventory'   => ['name' => 'Inventory',    'description' => 'Product stock tracking & management',   'icon' => 'fas fa-boxes'],
        'crm'         => ['name' => 'CRM',          'description' => 'Customer/client management',            'icon' => 'fas fa-users'],
        'pos'         => ['name' => 'POS',          'description' => 'Point of sale terminal',                 'icon' => 'fas fa-cash-register'],
        'ecommerce'   => ['name' => 'E-commerce',   'description' => 'Online storefront & orders',            'icon' => 'fas fa-shopping-cart'],
        'procurement' => ['name' => 'Procurement',  'description' => 'Purchase orders & vendor management',   'icon' => 'fas fa-truck'],
        'projects'    => ['name' => 'Projects',     'description' => 'Client projects, tasks & milestones',   'icon' => 'fas fa-project-diagram'],
        'payroll'     => ['name' => 'Payroll',      'description' => 'Employee salary & benefits management',  'icon' => 'fas fa-money-check-alt'],
        'banking'     => ['name' => 'Banking',      'description' => 'Bank accounts & reconciliation',        'icon' => 'fas fa-university'],
        'reports'     => ['name' => 'Reports',      'description' => 'Business & financial reports',           'icon' => 'fas fa-chart-bar'],
        'statutory'   => ['name' => 'Tax',          'description' => 'Tax compliance & statutory filings',     'icon' => 'fas fa-file-invoice'],
        'audit'       => ['name' => 'Audit',        'description' => 'Audit trail & activity logs',            'icon' => 'fas fa-clipboard-check'],
        'admin'       => ['name' => 'Admin',        'description' => 'User & role management',                 'icon' => 'fas fa-user-shield'],
        'settings'    => ['name' => 'Settings',     'description' => 'Company & app configuration',            'icon' => 'fas fa-cog'],
        'support'     => ['name' => 'Support',      'description' => 'Help desk & support tickets',            'icon' => 'fas fa-headset'],
        'help'              => ['name' => 'Help',              'description' => 'Documentation & guides',                                       'icon' => 'fas fa-question-circle'],
        'online_payments'   => ['name' => 'Online Payments',   'description' => 'Collect invoice payments online via Nomba/Paystack with payout management', 'icon' => 'fas fa-credit-card'],
    ];

    /**
     * Get default modules for a business category.
     */
    public static function getDefaultModules(string $category): array
    {
        return static::CATEGORY_DEFAULTS[$category] ?? static::CATEGORY_DEFAULTS['hybrid'];
    }

    /**
     * Check if a module is enabled for a tenant.
     *
     * If tenant has no enabled_modules set (NULL), falls back to
     * category defaults. If no category either, defaults to 'hybrid'
     * which has ALL modules — preserving existing tenant behavior.
     */
    public static function isModuleEnabled(?Tenant $tenant, string $module): bool
    {
        if (!$tenant) {
            return true; // No tenant context = show everything
        }

        // Core modules are always enabled
        if (in_array($module, static::CORE_MODULES)) {
            return true;
        }

        $enabledModules = static::getEnabledModules($tenant);

        return in_array($module, $enabledModules);
    }

    /**
     * Get the list of enabled modules for a tenant.
     */
    public static function getEnabledModules(?Tenant $tenant): array
    {
        if (!$tenant) {
            return static::CATEGORY_DEFAULTS['hybrid'];
        }

        // If tenant has explicit enabled_modules, use them
        if (!empty($tenant->enabled_modules)) {
            // Always ensure core modules are included
            return array_unique(array_merge(
                static::CORE_MODULES,
                $tenant->enabled_modules
            ));
        }

        // Fall back to category defaults
        $category = $tenant->getBusinessCategory();

        return static::getDefaultModules($category);
    }

    /**
     * Enable a module for a tenant.
     */
    public static function enableModule(Tenant $tenant, string $module): void
    {
        $current = static::getEnabledModules($tenant);

        if (!in_array($module, $current)) {
            $current[] = $module;
        }

        $tenant->update(['enabled_modules' => array_values(array_unique($current))]);
    }

    /**
     * Disable a module for a tenant (core modules cannot be disabled).
     */
    public static function disableModule(Tenant $tenant, string $module): void
    {
        if (in_array($module, static::CORE_MODULES)) {
            return; // Cannot disable core modules
        }

        $current = static::getEnabledModules($tenant);
        $current = array_values(array_filter($current, fn($m) => $m !== $module));

        $tenant->update(['enabled_modules' => $current]);
    }

    /**
     * Get all modules with metadata and enabled/recommended state for a tenant.
     * Used by the Company Settings > Modules tab.
     */
    public static function getAllModulesWithMeta(?Tenant $tenant): array
    {
        $category = $tenant?->getBusinessCategory() ?? 'hybrid';
        $defaults = static::CATEGORY_DEFAULTS[$category] ?? static::CATEGORY_DEFAULTS['hybrid'];
        $enabled = static::getEnabledModules($tenant);

        return collect(static::MODULE_META)->map(function ($meta, $key) use ($defaults, $enabled) {
            return [
                'key'         => $key,
                'name'        => $meta['name'],
                'description' => $meta['description'],
                'icon'        => $meta['icon'],
                'core'        => in_array($key, static::CORE_MODULES),
                'recommended' => in_array($key, $defaults),
                'enabled'     => in_array($key, $enabled),
            ];
        })->values()->toArray();
    }

    /**
     * Check if a module key is valid.
     */
    public static function isValidModule(string $module): bool
    {
        return in_array($module, static::ALL_MODULES);
    }
}
