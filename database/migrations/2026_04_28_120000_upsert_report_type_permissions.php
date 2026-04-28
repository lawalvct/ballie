<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            ['name' => 'View Reports Financial Analysis', 'slug' => 'reports.financial.view', 'module' => 'Reports', 'description' => 'View financial reports: profit & loss, balance sheet, trial balance, cash flow, equity statement'],
            ['name' => 'View Sales Reports', 'slug' => 'reports.sales.view', 'module' => 'Reports', 'description' => 'View sales summary, customer sales, product sales, and sales by period reports'],
            ['name' => 'View Reports Purchase Analysis', 'slug' => 'reports.purchase.view', 'module' => 'Reports', 'description' => 'View purchase summary, vendor purchases, product purchases, and purchases by period reports'],
            ['name' => 'View Reports Inventory Analysis', 'slug' => 'reports.inventory.view', 'module' => 'Reports', 'description' => 'View stock summary, low stock, valuation, movement, and bin card reports'],
            ['name' => 'View Reports Payroll Analysis', 'slug' => 'reports.payroll.view', 'module' => 'Reports', 'description' => 'View payroll summary, tax report, employee summary, and bank schedule reports'],
            ['name' => 'View Reports CRM Analysis', 'slug' => 'reports.crm.view', 'module' => 'Reports', 'description' => 'View CRM statements, payment reports, customer sales, and activity reports'],
            ['name' => 'View Reports POS Analysis', 'slug' => 'reports.pos.view', 'module' => 'Reports', 'description' => 'View POS daily sales, product performance, transaction, and overview reports'],
            ['name' => 'View Reports E-commerce Analysis', 'slug' => 'reports.ecommerce.view', 'module' => 'Reports', 'description' => 'View e-commerce order, revenue, product, customer, and abandoned cart reports'],
            ['name' => 'View Reports Project Analysis', 'slug' => 'reports.projects.view', 'module' => 'Reports', 'description' => 'View project profitability, revenue, active/completed project, and cashflow reports'],
            ['name' => 'View Procurement Reports', 'slug' => 'procurement.reports.view', 'module' => 'Procurement', 'description' => 'View procurement and purchase reports'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                array_merge($permission, [
                    'display_name' => $permission['name'],
                    'guard_name' => 'web',
                    'is_active' => true,
                    'priority' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('slug', [
            'reports.purchase.view',
            'reports.payroll.view',
            'reports.crm.view',
            'reports.pos.view',
            'reports.ecommerce.view',
            'reports.projects.view',
            'procurement.reports.view',
        ])->delete();
    }
};
