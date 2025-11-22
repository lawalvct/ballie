# Ballie Permissions System Guide

## Overview
The Ballie application now has a comprehensive permissions system that allows fine-grained control over user access to different modules and features.

## Setup Instructions

### 1. Run the Permissions Seeder
To populate all permissions in the database, run:

```bash
php artisan db:seed --class=PermissionsSeeder
```

Or run all seeders:
```bash
php artisan db:seed
```

### 2. Permission Structure

Each permission has:
- **Name**: Human-readable name (e.g., "View Dashboard")
- **Slug**: Unique identifier (e.g., "dashboard.view")
- **Module**: The module it belongs to (e.g., "Dashboard", "Accounting", "Inventory")
- **Description**: What the permission allows

## Available Modules & Permissions

### Dashboard
- `dashboard.view` - View dashboard and analytics

### Accounting Module
- `accounting.view` - View accounting module
- `accounting.invoices.manage` - Create, edit, delete invoices
- `accounting.invoices.post` - Post and unpost invoices
- `accounting.quotations.manage` - Create, edit, delete quotations
- `accounting.vouchers.manage` - Create, edit, delete vouchers
- `accounting.vouchers.post` - Post and unpost vouchers
- `accounting.ledgers.manage` - Create, edit, delete ledger accounts
- `accounting.groups.manage` - Create, edit, delete account groups
- `accounting.reports.view` - View financial reports

### Inventory Module
- `inventory.view` - View inventory module
- `inventory.products.manage` - Create, edit, delete products
- `inventory.categories.manage` - Create, edit, delete categories
- `inventory.journals.manage` - Create, edit, delete stock journals
- `inventory.journals.post` - Post and cancel stock journals
- `inventory.physical.manage` - Create, edit physical stock vouchers
- `inventory.physical.approve` - Approve physical stock vouchers

### CRM Module
- `crm.view` - View CRM module
- `crm.customers.manage` - Create, edit, delete customers
- `crm.customers.statements` - View customer statements
- `crm.vendors.manage` - Create, edit, delete vendors
- `crm.activities.manage` - Create, edit, delete customer activities
- `crm.reminders.send` - Send payment reminders

### POS Module
- `pos.access` - Access point of sale system
- `pos.sales.process` - Process POS sales
- `pos.register.manage` - Open and close cash register sessions
- `pos.transactions.void` - Void POS transactions
- `pos.refunds.process` - Process refunds
- `pos.reports.view` - View POS reports

### Payroll Module
- `payroll.view` - View payroll module
- `payroll.employees.manage` - Create, edit, delete employees
- `payroll.departments.manage` - Create, edit, delete departments
- `payroll.process` - Generate and process payroll
- `payroll.approve` - Approve payroll runs
- `payroll.loans.manage` - Create, edit, delete employee loans
- `payroll.attendance.manage` - Manage employee attendance
- `payroll.leaves.manage` - Manage employee leaves
- `payroll.leaves.approve` - Approve or reject leave requests

### Procurement Module
- `procurement.view` - View procurement module
- `procurement.po.manage` - Create, edit, delete purchase orders

### Banking Module
- `banking.view` - View banking module
- `banking.accounts.manage` - Create, edit, delete bank accounts
- `banking.reconcile.manage` - Create and manage bank reconciliations

### Reports Module
- `reports.view` - View all reports
- `reports.export` - Export reports to PDF/Excel

### Statutory Module
- `statutory.view` - View statutory and tax module
- `statutory.tax.manage` - Manage tax settings

### Audit Module
- `audit.view` - View audit trail and logs
- `audit.export` - Export audit logs

### Admin Management Module
- `admin.users.manage` - Create, edit, delete users
- `admin.roles.manage` - Create, edit, delete roles
- `admin.permissions.manage` - Manage permissions
- `admin.security.view` - View security and login logs
- `admin.teams.manage` - Create, edit, delete teams

### Settings Module
- `settings.view` - View settings
- `settings.company.manage` - Manage company information
- `settings.financial.manage` - Manage financial settings
- `settings.email.manage` - Manage email settings
- `settings.registers.manage` - Manage cash registers

## How to Use Permissions

### 1. Creating Roles with Permissions
When creating a role in Admin Management:
1. Go to Admin Management > Roles
2. Create a new role (e.g., "Accountant", "Sales Manager", "Cashier")
3. Select the appropriate permissions for that role
4. Save the role

### 2. Assigning Roles to Users
When creating or editing a user:
1. Go to Admin Management > Users
2. Create/Edit a user
3. Select the appropriate role from the dropdown
4. The user will inherit all permissions from that role

### 3. Checking Permissions in Code

In Controllers:
```php
// Check if user has permission
if (!auth()->user()->hasPermission('accounting.invoices.manage')) {
    abort(403, 'Unauthorized action.');
}
```

In Blade Templates:
```blade
@can('accounting.invoices.manage')
    <a href="{{ route('tenant.accounting.invoices.create') }}">Create Invoice</a>
@endcan
```

In Routes (Middleware):
```php
Route::get('/invoices', [InvoiceController::class, 'index'])
    ->middleware('permission:accounting.invoices.manage');
```

## Recommended Role Configurations

### Owner/Super Admin
- All permissions

### Accountant
- `accounting.*` (all accounting permissions)
- `reports.view`
- `reports.export`
- `crm.customers.statements`

### Sales Manager
- `crm.*` (all CRM permissions)
- `accounting.invoices.manage`
- `accounting.quotations.manage`
- `reports.view`

### Inventory Manager
- `inventory.*` (all inventory permissions)
- `reports.view`

### Cashier
- `pos.access`
- `pos.sales.process`
- `pos.register.manage`
- `crm.customers.manage` (basic)

### HR Manager
- `payroll.*` (all payroll permissions)
- `reports.view`

### Auditor (Read-Only)
- `dashboard.view`
- `accounting.reports.view`
- `reports.view`
- `audit.view`
- `audit.export`

## Adding New Permissions

To add new permissions:

1. Edit `database/seeders/PermissionsSeeder.php`
2. Add your new permission to the `$permissions` array:
```php
['name' => 'Your Permission', 'slug' => 'module.action', 'module' => 'ModuleName', 'description' => 'Description'],
```
3. Run the seeder again:
```bash
php artisan db:seed --class=PermissionsSeeder
```

## Best Practices

1. **Principle of Least Privilege**: Only grant permissions that are absolutely necessary
2. **Role-Based Access**: Use roles instead of assigning individual permissions
3. **Regular Audits**: Review user permissions regularly
4. **Documentation**: Document custom roles and their purposes
5. **Testing**: Test permission restrictions thoroughly before deploying

## Troubleshooting

### Permission Not Working
1. Check if the permission exists in the database
2. Verify the user has the correct role assigned
3. Ensure the role has the permission attached
4. Clear cache: `php artisan cache:clear`

### User Can't Access Module
1. Check if user has the base module permission (e.g., `accounting.view`)
2. Verify user status is "active"
3. Check if subscription is active

## Support
For issues or questions about the permissions system, contact the development team.
