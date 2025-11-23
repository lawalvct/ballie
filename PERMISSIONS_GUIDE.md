# Permission System Implementation Guide

## ✅ What Has Been Implemented

### 1. **Service Provider** (`app/Providers/PermissionServiceProvider.php`)
- Blade directives for permission checking
- Registered in `config/app.php`

### 2. **User Model** (`app/Models/User.php`)
- Enhanced `hasPermission()` method
- Added `hasAnyPermission()` method
- Added `hasAllPermissions()` method
- Owner role automatically has all permissions

### 3. **Middleware** (`app/Http/Middleware/CheckPermission.php`)
- Already exists and registered in Kernel
- Applied to AdminController

### 4. **AdminController** (`app/Http/Controllers/Tenant/Admin/AdminController.php`)
- Permission middleware activated in constructor
- Protects user, role, and permission management routes

### 5. **Helper Class** (`app/Helpers/PermissionHelper.php`)
- Constants for all permission slugs
- Easy reference in code

---

## 🎯 How to Use Permissions

### In Controllers

```php
// In constructor
$this->middleware('permission:accounting.invoices.manage')->only(['create', 'store', 'edit', 'update']);

// In methods
if (!auth()->user()->hasPermission('accounting.invoices.manage')) {
    abort(403, 'Unauthorized');
}
```

### In Routes (`routes/tenant.php`)

```php
// Single route
Route::get('/invoices', [InvoiceController::class, 'index'])
    ->middleware('permission:accounting.invoices.manage');

// Route group
Route::prefix('accounting')->middleware('permission:accounting.view')->group(function () {
    // All routes here require accounting.view permission
});
```

### In Blade Views

```blade
{{-- Show element only if user has permission --}}
@permission('admin.users.manage')
    <a href="{{ route('tenant.admin.users.create', tenant('slug')) }}">Add User</a>
@endpermission

{{-- Check for role --}}
@role('Owner')
    <div class="admin-panel">Owner Only Content</div>
@endrole

{{-- Check for any permission --}}
@hasAnyPermission('accounting.invoices.manage', 'accounting.view')
    <a href="#">Invoices</a>
@endhasAnyPermission

{{-- With else --}}
@permission('admin.roles.manage')
    <button>Edit Role</button>
@else
    <button disabled>No Permission</button>
@endpermission
```

### Using Helper Constants

```php
use App\Helpers\PermissionHelper;

// In controller
$this->middleware('permission:' . PermissionHelper::ADMIN_USERS_MANAGE);

// In code
if (auth()->user()->hasPermission(PermissionHelper::ACCOUNTING_INVOICES_MANAGE)) {
    // Do something
}
```

---

## 📋 Available Permissions (from PermissionsSeeder)

### Dashboard
- `dashboard.view` - View dashboard

### Admin Module
- `admin.users.manage` - Manage users
- `admin.roles.manage` - Manage roles
- `admin.permissions.manage` - Manage permissions
- `admin.teams.manage` - Manage teams
- `admin.security.view` - View security logs

### Accounting Module
- `accounting.view` - View accounting module
- `accounting.invoices.manage` - Manage invoices
- `accounting.invoices.post` - Post/unpost invoices
- `accounting.quotations.manage` - Manage quotations
- `accounting.vouchers.manage` - Manage vouchers
- `accounting.vouchers.post` - Post/unpost vouchers
- `accounting.ledgers.manage` - Manage ledger accounts
- `accounting.groups.manage` - Manage account groups
- `accounting.reports.view` - View financial reports

### Inventory Module
- `inventory.view` - View inventory module
- `inventory.products.manage` - Manage products
- `inventory.categories.manage` - Manage categories
- `inventory.journals.manage` - Manage stock journals
- `inventory.journals.post` - Post stock journals
- `inventory.physical.manage` - Manage physical stock
- `inventory.physical.approve` - Approve physical stock

### CRM Module
- `crm.view` - View CRM module
- `crm.customers.manage` - Manage customers
- `crm.customers.statements` - View customer statements
- `crm.vendors.manage` - Manage vendors
- `crm.activities.manage` - Manage activities
- `crm.reminders.send` - Send payment reminders

### POS Module
- `pos.access` - Access POS system
- `pos.sales.process` - Process sales
- `pos.register.manage` - Manage cash register
- `pos.transactions.void` - Void transactions
- `pos.refunds.process` - Process refunds
- `pos.reports.view` - View POS reports

### Payroll Module
- `payroll.view` - View payroll module
- `payroll.employees.manage` - Manage employees
- `payroll.departments.manage` - Manage departments
- `payroll.process` - Process payroll
- `payroll.approve` - Approve payroll
- `payroll.loans.manage` - Manage loans
- `payroll.attendance.manage` - Manage attendance
- `payroll.leaves.manage` - Manage leaves
- `payroll.leaves.approve` - Approve leaves

### Reports Module
- `reports.view` - View reports
- `reports.export` - Export reports

### Settings Module
- `settings.view` - View settings
- `settings.company.manage` - Manage company settings
- `settings.financial.manage` - Manage financial settings

---

## 🚀 Next Steps to Fully Implement

### 1. Protect More Controllers
Add middleware to other controllers:

```php
// In ProductController
$this->middleware('permission:inventory.products.manage')->except(['index', 'show']);

// In InvoiceController
$this->middleware('permission:accounting.invoices.manage')->except(['index', 'show']);
```

### 2. Update Navigation Views
Protect menu items in `resources/views/tenant/layouts/sidebar.blade.php`:

```blade
@permission('accounting.view')
    <a href="{{ route('tenant.accounting.index', tenant('slug')) }}">Accounting</a>
@endpermission
```

### 3. Protect Action Buttons
In list views (index.blade.php files):

```blade
@permission('admin.users.manage')
    <a href="{{ route('tenant.admin.users.edit', [tenant('slug'), $user->id]) }}">Edit</a>
@endpermission
```

### 4. Test Permissions
1. Create a test user with "Employee" role
2. Login and verify they can only access allowed features
3. Assign "Manager" role and verify expanded access

---

## 🔒 Security Best Practices

1. **Always check permissions in both controller AND view**
2. **Owner role bypasses all permission checks** (by design)
3. **Use middleware for route protection** (primary defense)
4. **Use blade directives for UI elements** (user experience)
5. **Never rely solely on hiding UI elements** (users can manipulate URLs)

---

## 🐛 Troubleshooting

### Permission not working?
1. Check if user has a role assigned
2. Check if role has the permission assigned
3. Clear cache: `php artisan optimize:clear`
4. Check permission slug matches exactly

### Getting 403 errors?
1. Verify permission exists in database
2. Check role has permission assigned
3. Verify middleware is registered in Kernel.php
4. Check user is authenticated

---

## ✨ Summary

**Permissions are now ACTIVE and ENFORCED on:**
- ✅ Admin user management
- ✅ Admin role management  
- ✅ Admin permission management
- ✅ Blade directives available in all views
- ✅ User model has permission checking methods

**To fully protect your app:**
1. Add middleware to remaining controllers
2. Update views with @permission directives
3. Test with different user roles
