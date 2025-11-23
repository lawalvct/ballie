<?php

namespace App\Helpers;

class PermissionHelper
{
    // Dashboard
    const DASHBOARD_VIEW = 'dashboard.view';

    // Admin
    const ADMIN_USERS_MANAGE = 'admin.users.manage';
    const ADMIN_ROLES_MANAGE = 'admin.roles.manage';
    const ADMIN_PERMISSIONS_MANAGE = 'admin.permissions.manage';
    const ADMIN_TEAMS_MANAGE = 'admin.teams.manage';

    // Accounting
    const ACCOUNTING_VIEW = 'accounting.view';
    const ACCOUNTING_INVOICES_MANAGE = 'accounting.invoices.manage';
    const ACCOUNTING_INVOICES_POST = 'accounting.invoices.post';
    const ACCOUNTING_VOUCHERS_MANAGE = 'accounting.vouchers.manage';
    const ACCOUNTING_VOUCHERS_POST = 'accounting.vouchers.post';
    const ACCOUNTING_LEDGERS_MANAGE = 'accounting.ledgers.manage';
    const ACCOUNTING_GROUPS_MANAGE = 'accounting.groups.manage';
    const ACCOUNTING_REPORTS_VIEW = 'accounting.reports.view';

    // Inventory
    const INVENTORY_VIEW = 'inventory.view';
    const INVENTORY_PRODUCTS_MANAGE = 'inventory.products.manage';
    const INVENTORY_CATEGORIES_MANAGE = 'inventory.categories.manage';
    const INVENTORY_JOURNALS_MANAGE = 'inventory.journals.manage';
    const INVENTORY_JOURNALS_POST = 'inventory.journals.post';

    // CRM
    const CRM_VIEW = 'crm.view';
    const CRM_CUSTOMERS_MANAGE = 'crm.customers.manage';
    const CRM_VENDORS_MANAGE = 'crm.vendors.manage';

    // POS
    const POS_ACCESS = 'pos.access';
    const POS_SALES_PROCESS = 'pos.sales.process';

    // Payroll
    const PAYROLL_VIEW = 'payroll.view';
    const PAYROLL_EMPLOYEES_MANAGE = 'payroll.employees.manage';
    const PAYROLL_PROCESS = 'payroll.process';
    const PAYROLL_APPROVE = 'payroll.approve';

    // Reports
    const REPORTS_VIEW = 'reports.view';
    const REPORTS_EXPORT = 'reports.export';

    // Settings
    const SETTINGS_VIEW = 'settings.view';
    const SETTINGS_COMPANY_MANAGE = 'settings.company.manage';
}
