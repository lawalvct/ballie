# Quick Setup Guide for Permissions System

## Step 1: Run the Permissions Seeder

```bash
php artisan db:seed --class=PermissionsSeeder
```

This will populate 65+ permissions across all modules.

## Step 2: Register the Middleware

Add to `app/Http/Kernel.php` in the `$middlewareAliases` array:

```php
'permission' => \App\Http\Middleware\CheckPermission::class,
```

## Step 3: Create Default Roles

Go to Admin Management > Roles and create these roles:

### Accountant Role
Select permissions:
- All Accounting permissions
- Reports > View Reports
- Reports > Export Reports
- CRM > View Customer Statements

### Sales Manager Role
Select permissions:
- All CRM permissions
- Accounting > Manage Invoices
- Accounting > Manage Quotations
- Reports > View Reports

### Cashier Role
Select permissions:
- POS > Access POS
- POS > Process Sales
- POS > Manage Cash Register
- CRM > Manage Customers (basic)

### Inventory Manager Role
Select permissions:
- All Inventory permissions
- Reports > View Reports

### HR Manager Role
Select permissions:
- All Payroll permissions
- Reports > View Reports

## Step 4: Assign Roles to Users

When creating/editing users in Admin Management > Users, select the appropriate role from the dropdown.

## Done!

Your permissions system is now active. Users will only see and access modules they have permissions for.
