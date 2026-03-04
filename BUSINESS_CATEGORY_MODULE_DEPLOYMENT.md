# Business Category & Module Visibility — Deployment Guide

## Overview

This feature makes Ballie adapt to each business type by showing only relevant modules in the sidebar. A consultancy firm won't see Inventory or POS; a retail store gets everything.

**Zero disruption**: Existing tenants continue unchanged (defaults to `hybrid` = all modules enabled).

---

## Files Changed

### New Files

| File                                                                                      | Purpose                                                                                |
| ----------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------- |
| `database/migrations/2026_03_04_100001_add_business_category_to_business_types_table.php` | Adds `business_category` column to `business_types`                                    |
| `database/migrations/2026_03_04_100002_add_enabled_modules_to_tenants_table.php`          | Adds `enabled_modules` JSON column to `tenants`                                        |
| `app/Services/ModuleRegistry.php`                                                         | Central module registry — defines all modules, category defaults, enable/disable logic |
| `app/Services/TerminologyService.php`                                                     | Category-aware label mapping (e.g., "Customers" → "Clients" for service businesses)    |
| `app/Http/Middleware/CheckModuleAccess.php`                                               | Route-level middleware that blocks disabled module routes                              |

### Modified Files

| File                                               | Change                                                                                                   |
| -------------------------------------------------- | -------------------------------------------------------------------------------------------------------- |
| `app/Models/BusinessType.php`                      | Added `business_category` to fillable, `getBusinessCategory()` method                                    |
| `app/Models/Tenant.php`                            | Added `enabled_modules` to fillable/casts, `hasModule()`, `getBusinessCategory()`, `getEnabledModules()` |
| `database/seeders/BusinessTypeSeeder.php`          | Added `business_category` values for all 97 business types, uses `updateOrCreate`                        |
| `app/Http/Kernel.php`                              | Added `'module.access'` middleware alias                                                                 |
| `app/Providers/AppServiceProvider.php`             | Registered `@module`/`@endmodule` and `@term()` Blade directives                                         |
| `resources/views/layouts/tenant/sidebar.blade.php` | Wrapped 7 module menu items with `@module('xxx')` directives                                             |

---

## Deployment Steps

### 1. Pull Code

```bash
cd /www/wwwroot/ballie
git pull origin main
```

### 2. Run Migrations

```bash
php artisan migrate --path=database/migrations/2026_03_04_100001_add_business_category_to_business_types_table.php
php artisan migrate --path=database/migrations/2026_03_04_100002_add_enabled_modules_to_tenants_table.php
```

Or if no blocking migrations exist:

```bash
php artisan migrate
```

### 3. (Optional) Re-seed Business Types

Only needed if business types table was manually edited or you want to ensure `business_category` values are correct:

```bash
php artisan db:seed --class=BusinessTypeSeeder
```

### 4. Clear Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 5. Verify

```bash
php artisan tinker --execute="echo json_encode(\App\Services\ModuleRegistry::getDefaultModules('service'));"
# Should output: ["dashboard","accounting","crm","projects","payroll","banking","reports","statutory","audit","admin","settings","support","help"]
# Notice: no inventory, pos, ecommerce for service businesses
```

---

## How It Works

### Business Categories

| Category        | Description                       | Hidden Modules            |
| --------------- | --------------------------------- | ------------------------- |
| `trading`       | Retail, wholesale, e-commerce     | projects                  |
| `manufacturing` | Factories, production             | ecommerce                 |
| `service`       | Consulting, professional services | inventory, pos, ecommerce |
| `hybrid`        | Mixed businesses                  | none (all enabled)        |

### Always-On Modules

These modules are **always** visible regardless of category: `accounting`, `reports`, `admin`

### Module Override

Tenants can override defaults via the `enabled_modules` JSON column:

```php
// Enable a module
ModuleRegistry::enableModule($tenant, 'pos');

// Disable a module
ModuleRegistry::disableModule($tenant, 'inventory');

// Reset to category defaults
ModuleRegistry::resetToDefaults($tenant);

// Check if module is enabled
ModuleRegistry::isModuleEnabled($tenant, 'inventory');
```

### Blade Usage

**Module visibility** (sidebar already uses this):

```blade
@module('inventory')
    {{-- Only shown if inventory module is enabled --}}
    <li>Inventory menu item</li>
@endmodule
```

**Category-aware terminology**:

```blade
<h1>@term('Customers')</h1>
{{-- Outputs "Clients" for service businesses, "Customers" for others --}}
```

### Route Protection (Optional)

Apply to route groups to block URL access to disabled modules:

```php
Route::middleware(['module.access'])->group(function () {
    Route::get('/inventory', ...);
});
```

---

## Backward Compatibility

- **Existing tenants**: `enabled_modules` is `NULL` → falls back to category defaults
- **No business type set**: Defaults to `hybrid` (all modules enabled)
- **Migration is safe to re-run**: Uses `Schema::hasColumn` guards
- **Seeder is safe to re-run**: Uses `updateOrCreate`

---

## Next Phases (Not Yet Implemented)

1. **Module Management UI** — Settings tab where tenant admins can toggle modules on/off
2. **Onboarding Flow** — Ask business category during onboarding, auto-configure modules
3. **Dashboard Customization** — Category-specific dashboard widgets
4. **Terminology Integration** — Replace hardcoded labels with `@term()` across all views
