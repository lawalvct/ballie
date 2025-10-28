# Tenant Onboarding - Database Tables

## Tables Affected During Tenant Onboarding

The following tables are seeded/populated during the tenant onboarding process:

### 1. `account_groups`
- **Purpose**: Chart of accounts structure
- **Seeded by**: `DefaultAccountGroupsSeeder`
- **Contains**: Asset, Liability, Equity, Income, Expense groups
- **Note**: Must be seeded before `ledger_accounts`

### 2. `ledger_accounts`
- **Purpose**: Individual accounting ledger accounts
- **Seeded by**: `DefaultLedgerAccountsSeeder`
- **Contains**: Cash, Bank, Receivables, Payables, etc.
- **Dependencies**: Requires `account_groups` to exist first

### 3. `product_categories`
- **Purpose**: Product categorization for inventory
- **Seeded by**: `DefaultProductCategoriesSeeder`
- **Contains**: Default product categories
- **Note**: Optional, can be created by users later

### 4. `units`
- **Purpose**: Units of measurement for products
- **Seeded by**: `DefaultUnitsSeeder`
- **Contains**: Pieces, Kg, Liters, etc.
- **Note**: Required for inventory management

### 5. `voucher_types`
- **Purpose**: Transaction voucher types
- **Seeded by**: `DefaultVoucherTypesSeeder`
- **Contains**: Payment, Receipt, Journal, Sales, Purchase vouchers
- **Note**: Critical for accounting transactions

### 6. `users`
- **Purpose**: Tenant owner/admin user
- **Created during**: Tenant registration
- **Contains**: Initial admin user for the tenant
- **Note**: First user is automatically created

### 7. `telescope_entries` & `telescope_entries_tags`
- **Purpose**: Laravel Telescope debugging data
- **Note**: Development/debugging only, can be truncated safely

## Seeding Order

**Important**: Tables must be seeded in this order due to foreign key dependencies:

1. `account_groups` (no dependencies)
2. `ledger_accounts` (depends on account_groups)
3. `voucher_types` (no dependencies)
4. `units` (no dependencies)
5. `product_categories` (no dependencies)
6. `users` (created during registration)

## Truncation Warning

⚠️ **CAUTION**: Truncating these tables will:
- Remove all tenant data
- Break foreign key relationships
- Require re-running seeders
- Delete user accounts

Only truncate during:
- Development/testing
- Fresh tenant setup
- Database reset operations

## Related Files

- Seeders: `database/seeders/`
- Onboarding Controller: `app/Http/Controllers/Tenant/OnboardingController.php`
- Tenant Model: `app/Models/Tenant.php`
