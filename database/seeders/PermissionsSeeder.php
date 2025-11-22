<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'module' => 'Dashboard', 'description' => 'View dashboard and analytics'],

            // Accounting Module
            ['name' => 'View Accounting', 'slug' => 'accounting.view', 'module' => 'Accounting', 'description' => 'View accounting module'],
            ['name' => 'Manage Invoices', 'slug' => 'accounting.invoices.manage', 'module' => 'Accounting', 'description' => 'Create, edit, delete invoices'],
            ['name' => 'Post Invoices', 'slug' => 'accounting.invoices.post', 'module' => 'Accounting', 'description' => 'Post and unpost invoices'],
            ['name' => 'Manage Quotations', 'slug' => 'accounting.quotations.manage', 'module' => 'Accounting', 'description' => 'Create, edit, delete quotations'],
            ['name' => 'Manage Vouchers', 'slug' => 'accounting.vouchers.manage', 'module' => 'Accounting', 'description' => 'Create, edit, delete vouchers'],
            ['name' => 'Post Vouchers', 'slug' => 'accounting.vouchers.post', 'module' => 'Accounting', 'description' => 'Post and unpost vouchers'],
            ['name' => 'Manage Ledger Accounts', 'slug' => 'accounting.ledgers.manage', 'module' => 'Accounting', 'description' => 'Create, edit, delete ledger accounts'],
            ['name' => 'Manage Account Groups', 'slug' => 'accounting.groups.manage', 'module' => 'Accounting', 'description' => 'Create, edit, delete account groups'],
            ['name' => 'View Financial Reports', 'slug' => 'accounting.reports.view', 'module' => 'Accounting', 'description' => 'View trial balance, P&L, balance sheet'],

            // Inventory Module
            ['name' => 'View Inventory', 'slug' => 'inventory.view', 'module' => 'Inventory', 'description' => 'View inventory module'],
            ['name' => 'Manage Products', 'slug' => 'inventory.products.manage', 'module' => 'Inventory', 'description' => 'Create, edit, delete products'],
            ['name' => 'Manage Categories', 'slug' => 'inventory.categories.manage', 'module' => 'Inventory', 'description' => 'Create, edit, delete categories'],
            ['name' => 'Manage Stock Journals', 'slug' => 'inventory.journals.manage', 'module' => 'Inventory', 'description' => 'Create, edit, delete stock journals'],
            ['name' => 'Post Stock Journals', 'slug' => 'inventory.journals.post', 'module' => 'Inventory', 'description' => 'Post and cancel stock journals'],
            ['name' => 'Manage Physical Stock', 'slug' => 'inventory.physical.manage', 'module' => 'Inventory', 'description' => 'Create, edit physical stock vouchers'],
            ['name' => 'Approve Physical Stock', 'slug' => 'inventory.physical.approve', 'module' => 'Inventory', 'description' => 'Approve physical stock vouchers'],

            // CRM Module
            ['name' => 'View CRM', 'slug' => 'crm.view', 'module' => 'CRM', 'description' => 'View CRM module'],
            ['name' => 'Manage Customers', 'slug' => 'crm.customers.manage', 'module' => 'CRM', 'description' => 'Create, edit, delete customers'],
            ['name' => 'View Customer Statements', 'slug' => 'crm.customers.statements', 'module' => 'CRM', 'description' => 'View customer statements'],
            ['name' => 'Manage Vendors', 'slug' => 'crm.vendors.manage', 'module' => 'CRM', 'description' => 'Create, edit, delete vendors'],
            ['name' => 'Manage Activities', 'slug' => 'crm.activities.manage', 'module' => 'CRM', 'description' => 'Create, edit, delete customer activities'],
            ['name' => 'Send Payment Reminders', 'slug' => 'crm.reminders.send', 'module' => 'CRM', 'description' => 'Send payment reminders to customers'],

            // POS Module
            ['name' => 'Access POS', 'slug' => 'pos.access', 'module' => 'POS', 'description' => 'Access point of sale system'],
            ['name' => 'Process Sales', 'slug' => 'pos.sales.process', 'module' => 'POS', 'description' => 'Process POS sales'],
            ['name' => 'Manage Cash Register', 'slug' => 'pos.register.manage', 'module' => 'POS', 'description' => 'Open and close cash register sessions'],
            ['name' => 'Void Transactions', 'slug' => 'pos.transactions.void', 'module' => 'POS', 'description' => 'Void POS transactions'],
            ['name' => 'Process Refunds', 'slug' => 'pos.refunds.process', 'module' => 'POS', 'description' => 'Process refunds'],
            ['name' => 'View POS Reports', 'slug' => 'pos.reports.view', 'module' => 'POS', 'description' => 'View POS reports'],

            // Payroll Module
            ['name' => 'View Payroll', 'slug' => 'payroll.view', 'module' => 'Payroll', 'description' => 'View payroll module'],
            ['name' => 'Manage Employees', 'slug' => 'payroll.employees.manage', 'module' => 'Payroll', 'description' => 'Create, edit, delete employees'],
            ['name' => 'Manage Departments', 'slug' => 'payroll.departments.manage', 'module' => 'Payroll', 'description' => 'Create, edit, delete departments'],
            ['name' => 'Process Payroll', 'slug' => 'payroll.process', 'module' => 'Payroll', 'description' => 'Generate and process payroll'],
            ['name' => 'Approve Payroll', 'slug' => 'payroll.approve', 'module' => 'Payroll', 'description' => 'Approve payroll runs'],
            ['name' => 'Manage Loans', 'slug' => 'payroll.loans.manage', 'module' => 'Payroll', 'description' => 'Create, edit, delete employee loans'],
            ['name' => 'Manage Attendance', 'slug' => 'payroll.attendance.manage', 'module' => 'Payroll', 'description' => 'Manage employee attendance'],
            ['name' => 'Manage Leaves', 'slug' => 'payroll.leaves.manage', 'module' => 'Payroll', 'description' => 'Manage employee leaves'],
            ['name' => 'Approve Leaves', 'slug' => 'payroll.leaves.approve', 'module' => 'Payroll', 'description' => 'Approve or reject leave requests'],

            // Procurement Module
            ['name' => 'View Procurement', 'slug' => 'procurement.view', 'module' => 'Procurement', 'description' => 'View procurement module'],
            ['name' => 'Manage Purchase Orders', 'slug' => 'procurement.po.manage', 'module' => 'Procurement', 'description' => 'Create, edit, delete purchase orders'],

            // Banking Module
            ['name' => 'View Banking', 'slug' => 'banking.view', 'module' => 'Banking', 'description' => 'View banking module'],
            ['name' => 'Manage Bank Accounts', 'slug' => 'banking.accounts.manage', 'module' => 'Banking', 'description' => 'Create, edit, delete bank accounts'],
            ['name' => 'Manage Reconciliations', 'slug' => 'banking.reconcile.manage', 'module' => 'Banking', 'description' => 'Create and manage bank reconciliations'],

            // Reports Module
            ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'Reports', 'description' => 'View all reports'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'module' => 'Reports', 'description' => 'Export reports to PDF/Excel'],

            // Statutory Module
            ['name' => 'View Statutory', 'slug' => 'statutory.view', 'module' => 'Statutory', 'description' => 'View statutory and tax module'],
            ['name' => 'Manage Tax Settings', 'slug' => 'statutory.tax.manage', 'module' => 'Statutory', 'description' => 'Manage tax settings'],

            // Audit Module
            ['name' => 'View Audit Trail', 'slug' => 'audit.view', 'module' => 'Audit', 'description' => 'View audit trail and logs'],
            ['name' => 'Export Audit Logs', 'slug' => 'audit.export', 'module' => 'Audit', 'description' => 'Export audit logs'],

            // Admin Management Module
            ['name' => 'Manage Users', 'slug' => 'admin.users.manage', 'module' => 'Admin', 'description' => 'Create, edit, delete users'],
            ['name' => 'Manage Roles', 'slug' => 'admin.roles.manage', 'module' => 'Admin', 'description' => 'Create, edit, delete roles'],
            ['name' => 'Manage Permissions', 'slug' => 'admin.permissions.manage', 'module' => 'Admin', 'description' => 'Manage permissions'],
            ['name' => 'View Security Logs', 'slug' => 'admin.security.view', 'module' => 'Admin', 'description' => 'View security and login logs'],
            ['name' => 'Manage Teams', 'slug' => 'admin.teams.manage', 'module' => 'Admin', 'description' => 'Create, edit, delete teams'],

            // Settings Module
            ['name' => 'View Settings', 'slug' => 'settings.view', 'module' => 'Settings', 'description' => 'View settings'],
            ['name' => 'Manage Company Settings', 'slug' => 'settings.company.manage', 'module' => 'Settings', 'description' => 'Manage company information'],
            ['name' => 'Manage Financial Settings', 'slug' => 'settings.financial.manage', 'module' => 'Settings', 'description' => 'Manage financial settings'],
            ['name' => 'Manage Email Settings', 'slug' => 'settings.email.manage', 'module' => 'Settings', 'description' => 'Manage email settings'],
            ['name' => 'Manage Cash Registers', 'slug' => 'settings.registers.manage', 'module' => 'Settings', 'description' => 'Manage cash registers'],
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
}
