<?php

namespace App\Support;

use App\Models\Tenant\Permission;
use App\Models\User;
use Illuminate\Support\Collection;

class ReportPermissionMatrix
{
    public static function groups(): array
    {
        return [
            'financial' => [
                'label' => 'Financial Reports',
                'description' => 'Profit & loss, balance sheet, trial balance, cash flow, equity statement.',
                'slug' => 'reports.financial.view',
                'aliases' => ['accounting.reports.view'],
            ],
            'sales' => [
                'label' => 'Sales Reports',
                'description' => 'Sales summary, customer sales, product sales, sales by period.',
                'slug' => 'reports.sales.view',
                'aliases' => [],
            ],
            'purchase' => [
                'label' => 'Purchase Reports',
                'description' => 'Purchase summary, vendor purchases, product purchases, purchases by period.',
                'slug' => 'reports.purchase.view',
                'aliases' => ['procurement.reports.view'],
            ],
            'inventory' => [
                'label' => 'Inventory Reports',
                'description' => 'Stock summary, low stock, valuation, movement, bin card.',
                'slug' => 'reports.inventory.view',
                'aliases' => ['inventory.reports.view'],
            ],
            'payroll' => [
                'label' => 'Payroll Reports',
                'description' => 'Payroll summary, tax report, employee summary, bank schedule.',
                'slug' => 'reports.payroll.view',
                'aliases' => ['payroll.reports.view'],
            ],
            'crm' => [
                'label' => 'CRM Reports',
                'description' => 'Customer statements, payment reports, customer sales, activities.',
                'slug' => 'reports.crm.view',
                'aliases' => ['crm.reports.view'],
            ],
            'pos' => [
                'label' => 'POS Reports',
                'description' => 'Daily sales, product performance, transactions, POS overview.',
                'slug' => 'reports.pos.view',
                'aliases' => ['pos.reports.view'],
            ],
            'ecommerce' => [
                'label' => 'E-commerce Reports',
                'description' => 'Order, revenue, product, customer, and abandoned cart reports.',
                'slug' => 'reports.ecommerce.view',
                'aliases' => ['ecommerce.reports.view'],
            ],
            'projects' => [
                'label' => 'Project Reports',
                'description' => 'Project profitability, revenue by client, active/completed projects, cashflow.',
                'slug' => 'reports.projects.view',
                'aliases' => ['projects.reports.view'],
            ],
        ];
    }

    public static function allSlugs(): array
    {
        return collect(self::groups())
            ->flatMap(fn (array $group) => array_merge([$group['slug']], $group['aliases']))
            ->unique()
            ->values()
            ->all();
    }

    public static function accessMap(?User $user): array
    {
        return collect(self::groups())
            ->mapWithKeys(fn (array $group, string $key) => [$key => self::userCanView($user, $key)])
            ->all();
    }

    public static function userCanView(?User $user, string $key): bool
    {
        if (! $user || ! isset(self::groups()[$key])) {
            return false;
        }

        foreach (self::groupSlugs($key) as $slug) {
            if ($user->hasPermission($slug)) {
                return true;
            }
        }

        return false;
    }

    public static function groupSlugs(string $key): array
    {
        $group = self::groups()[$key] ?? null;

        if (! $group) {
            return [];
        }

        return array_values(array_unique(array_merge([$group['slug']], $group['aliases'])));
    }

    public static function formGroups(): Collection
    {
        $permissions = Permission::whereIn('slug', self::allSlugs())->get()->keyBy('slug');

        return collect(self::groups())
            ->map(function (array $group, string $key) use ($permissions) {
                $permission = $permissions->get($group['slug'])
                    ?: collect($group['aliases'])->map(fn (string $slug) => $permissions->get($slug))->filter()->first();

                if (! $permission) {
                    return null;
                }

                return array_merge($group, [
                    'key' => $key,
                    'permission' => $permission,
                    'permission_ids' => collect(self::groupSlugs($key))
                        ->map(fn (string $slug) => $permissions->get($slug)?->id)
                        ->filter()
                        ->values()
                        ->all(),
                ]);
            })
            ->filter()
            ->values();
    }
}
