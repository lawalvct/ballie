# Admin Management Flow (Updated Roles & Permissions Guide)

**Last updated:** 2026-02-18 12:55:34 +01:00

## Purpose

This document is the source of truth for the current RBAC (Role-Based Access Control) flow in Ballie.

It reflects the latest implementation in:

- `database/seeders/PermissionsSeeder.php`
- `app/Http/Controllers/Api/Tenant/OnboardingController.php`
- `app/Models/Tenant/Permission.php`

---

## Current RBAC Snapshot

- **Total seeded permissions:** 86
- **Permission modules:** 14
- **Default roles created during onboarding:** 10
- **Owner role behavior:** Always receives **all permissions** (`permissions => 'all'`)

---

## Permission Modules (Latest)

Permissions are seeded via `PermissionsSeeder` and grouped by module.

1. **Dashboard**
2. **Accounting**
3. **Inventory**
4. **CRM**
5. **POS**
6. **E-commerce**
7. **Payroll**
8. **Procurement**
9. **Banking**
10. **Reports**
11. **Statutory**
12. **Audit**
13. **Admin**
14. **Settings**

### Important Coverage Added

The latest update closes previous gaps by including:

- Full **E-commerce** permission set (`ecommerce.view`, settings/orders/shipping/coupons/payouts/reports)
- Expanded **Payroll** coverage (positions, overtime, shifts, payroll reports)
- Expanded **Banking** coverage (statements)
- Expanded **Procurement** coverage (PO approval)
- Expanded **CRM**, **Reports**, **Settings**, and **Statutory** coverage

---

## Default Roles Created in Onboarding

Roles are created in `OnboardingController::createDefaultRoles()` when tenant roles do not exist.

## Hierarchy & Intent

| Priority | Role                 | Intent                                                  |
| -------- | -------------------- | ------------------------------------------------------- |
| 100      | Owner                | Full unrestricted access to all modules and permissions |
| 90       | Admin                | Near-full administrative and operational access         |
| 80       | Manager              | Cross-functional day-to-day operations                  |
| 70       | Accountant           | Finance/accounting/banking/statutory focus              |
| 60       | Sales Representative | CRM, POS, invoicing, sales operations                   |
| 55       | Store Manager        | E-commerce operations and store workflow                |
| 50       | Inventory Manager    | Inventory, stock, procurement operations                |
| 50       | HR Manager           | Employees, payroll, attendance, leave, shifts           |
| 40       | Cashier              | POS transaction flow and limited supporting access      |
| 30       | Employee             | Minimal baseline access                                 |

### Owner Role Rule (Critical)

Owner must remain unrestricted:

- In role config: `permissions => 'all'`
- In assignment logic: role receives every permission ID from `Permission::all()`

Do not narrow Owner unless explicitly required by a product/security decision.

---

## Onboarding Role/Permission Flow

The runtime flow is:

1. Check if roles already exist for tenant.
2. If roles do not exist:
    - Run `PermissionsSeeder` (upsert by slug).
    - Create default role records.
    - Resolve permission IDs by slug.
    - Sync each role’s permissions.
3. Assign Owner role to onboarding user.

This ensures every new tenant starts with a complete, module-aware RBAC foundation.

---

## Sidebar / Module Access Alignment

Tenant sidebar module visibility relies on permission checks such as:

- `dashboard.view`
- `accounting.view`
- `inventory.view`
- `crm.view`
- `pos.access`
- `ecommerce.view`
- `payroll.view`
- `reports.view`
- `statutory.view`
- `audit.view`
- `settings.view`

Therefore, role assignment quality directly controls visible navigation and usable features.

---

## Permission Naming Standard

Use this slug style consistently:

- `<module>.view`
- `<module>.<resource>.manage`
- `<module>.<resource>.approve`
- `<module>.<resource>.report(s).view`

Examples:

- `ecommerce.orders.manage`
- `payroll.overtime.manage`
- `banking.statements.view`
- `statutory.returns.file`

---

## Operational Commands

Run these when applying RBAC updates to an existing environment:

```bash
php artisan db:seed --class=PermissionsSeeder
php artisan optimize:clear
```

If onboarding already created roles, reseeding updates permissions table, but existing role-permission links remain as previously synced unless you resync roles.

---

## Updating RBAC Safely (Future Changes)

When adding a new feature/module:

1. Add new permission slugs to `PermissionsSeeder`.
2. Add permissions to relevant default roles in `OnboardingController`.
3. Keep Owner as `all`.
4. If module is visualized in admin UI, ensure `Permission` model icon/color map includes module key.
5. Re-seed permissions.
6. Validate role assignment and sidebar visibility with a test tenant.

---

## Common Pitfalls to Avoid

- Adding a module route/UI without adding matching permission slugs.
- Creating role policies that skip `*.view` permissions for module entry points.
- Renaming permission slugs without migration strategy.
- Forgetting to update onboarding defaults after seeder changes.

---

## Verification Checklist

After RBAC changes, validate:

- Permissions table includes expected new slugs.
- New tenant onboarding creates all default roles.
- Owner has full access across all modules.
- Admin/Manager/functional roles align with intended hierarchy.
- Sidebar items show/hide correctly by role.
- Role edit/show screens load without tenant scoping issues.

---

## Maintainer Note

This guide intentionally tracks **implemented behavior**, not aspirational design.

If implementation changes, update this file in the same PR as:

- `PermissionsSeeder` changes
- `OnboardingController` role-mapping changes
- Any permission naming refactor

That keeps onboarding, access control, and documentation in sync.
