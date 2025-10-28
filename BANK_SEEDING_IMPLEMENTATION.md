# Bank Seeding Implementation

## Overview

Implemented Option 2 (B) from the architectural discussion: Create default banks via the Bank model during tenant onboarding, which automatically creates linked ledger accounts.

## Problem Solved

Previously, the `DefaultLedgerAccountsSeeder` was creating a generic "Bank Account - Current" ledger account, which would duplicate functionality when users created real banks via the Bank model (which auto-creates its own linked ledger accounts).

## Changes Made

### 1. DefaultLedgerAccountsSeeder.php

**File**: `database/seeders/DefaultLedgerAccountsSeeder.php`

**Change**: Removed the generic "Bank Account - Current" seed entry (lines 35-44)

**Kept**:

-   ✅ Cash in Hand (CASH-001) - For non-bank cash transactions
-   ✅ Petty Cash (PETTY-001) - For small cash expenses

**Removed**:

-   ❌ Bank Account - Current (BANK-001) - Redundant with Bank model

**Rationale**: Real banks created via the Bank model will automatically generate their own specific ledger accounts with descriptive names like "GTBank - 0123456789 (BANK-GTB-6789)".

### 2. DefaultBanksSeeder.php (NEW)

**File**: `database/seeders/DefaultBanksSeeder.php`

**Purpose**: Creates a placeholder bank account during tenant onboarding

**Features**:

-   Creates a "Primary Bank Account" with placeholder values
-   Account number set to "SETUP-REQUIRED" to prompt user update
-   Marked as `is_primary = true`
-   Includes helpful description and notes guiding users to update details
-   Bank model's `boot()` method automatically creates linked ledger account
-   Comprehensive logging for verification

**Auto-Created Ledger Account**:
When the bank is created, the Bank model's `boot()` method automatically:

1. Finds the "Current Assets" account group
2. Creates a linked `LedgerAccount` with code format: `BANK-{NAME}-{LAST4}`
3. Sets the relationship via `ledger_account_id`

### 3. OnboardingController.php

**File**: `app/Http/Controllers/Tenant/OnboardingController.php`

**Changes**:

1. **Import**: Added `use Database\Seeders\DefaultBanksSeeder;` (line 20)
2. **Seeding Call**: Added bank seeding after ledger accounts (lines 293-308)
3. **Verification**: Added banks count to final verification logs (line 323)

**Seeding Order** (Critical):

```
1. Account Groups
2. Voucher Types
3. Ledger Accounts ← Must be first
4. Banks           ← NEW: Must be after ledger accounts
5. Product Categories
6. Units
```

**Why Order Matters**: The Bank model's auto-ledger creation requires the "Current Assets" account group to exist first.

### 4. Tenant.php

**File**: `app/Models/Tenant.php`

**Change**: Added `banks()` relationship method (lines 390-393)

```php
public function banks()
{
    return $this->hasMany(Bank::class);
}
```

## How It Works

### Onboarding Flow:

1. **Tenant Created** → Registration or super-admin creation
2. **Account Groups Seeded** → Including "Current Assets"
3. **Ledger Accounts Seeded** → Cash in Hand, Petty Cash, etc. (NO generic bank)
4. **Default Bank Created** → "Primary Bank Account" with placeholder data
5. **Bank Boot Method Triggered** → Auto-creates linked ledger account
6. **Result**: Tenant has placeholder bank + auto-linked ledger account

### User Experience:

1. During onboarding, a placeholder bank is automatically created
2. User sees "Primary Bank Account" with account number "SETUP-REQUIRED"
3. Description prompts: "Please update with your actual bank details in the Banking module"
4. User navigates to **Banking > Banks** to update:
    - Real bank name (e.g., "Access Bank")
    - Real account number
    - Branch details, etc.
5. When updated, the linked ledger account automatically syncs (via `syncLedgerAccount()` method)

### Benefits:

✅ No duplicate "Bank Account - Current" entries
✅ All bank accounts have descriptive, specific names
✅ Automatic ledger account creation and sync
✅ Clearer distinction between cash and bank accounts
✅ Prompts users to set up real banking information
✅ Maintains proper accounting relationships

## Database Schema

### Banks Table:

-   `tenant_id` - Foreign key to tenants
-   `ledger_account_id` - Foreign key to ledger_accounts (auto-set by boot method)
-   `bank_name` - "Primary Bank Account" (placeholder)
-   `account_number` - "SETUP-REQUIRED" (prompts update)
-   `account_type` - "current"
-   `is_primary` - true
-   `is_active` - true
-   All balance fields initialized to 0.00

### Ledger Accounts Table (Auto-Created):

-   `tenant_id` - Foreign key to tenants
-   `account_group_id` - "Current Assets" group
-   `name` - Generated: "{bank_name} - {account_name}"
-   `code` - Generated: "BANK-{BANK_NAME}-{LAST_4_DIGITS}"
-   `account_type` - "asset"
-   `opening_balance` - Copied from bank
-   `current_balance` - Calculated from voucher entries
-   `is_system_account` - false (user can edit)
-   `is_active` - true

## Testing

### Manual Test Steps:

1. **Register a new tenant**:

    ```
    Visit: /register
    Complete registration
    ```

2. **Complete onboarding**:

    ```
    Go through onboarding steps
    Skip or complete all steps
    ```

3. **Verify bank was created**:

    ```sql
    SELECT * FROM banks WHERE tenant_id = <your_tenant_id>;
    -- Should show 1 record: "Primary Bank Account"
    ```

4. **Verify ledger account was auto-created**:

    ```sql
    SELECT * FROM ledger_accounts
    WHERE tenant_id = <your_tenant_id>
    AND code LIKE 'BANK-%';
    -- Should show 1 record with auto-generated code
    ```

5. **Check relationship**:

    ```sql
    SELECT b.bank_name, b.account_number, l.name, l.code
    FROM banks b
    JOIN ledger_accounts l ON b.ledger_account_id = l.id
    WHERE b.tenant_id = <your_tenant_id>;
    -- Should show proper linking
    ```

6. **Update bank details**:

    ```
    Navigate to: /{tenant-slug}/banking/banks
    Edit the placeholder bank
    Update bank name, account number, etc.
    Save
    ```

7. **Verify ledger account updated**:
    ```sql
    SELECT name, code, opening_balance
    FROM ledger_accounts
    WHERE id = (SELECT ledger_account_id FROM banks WHERE tenant_id = <your_tenant_id>);
    -- Name and code should reflect updated bank details
    ```

### Artisan Command Test:

```bash
# Test seeding directly for a tenant
php artisan db:seed --class=DefaultBanksSeeder --tenant-id=1
```

## Rollback Plan (If Needed)

If you need to revert to the old approach:

1. **Restore generic bank account seed** in `DefaultLedgerAccountsSeeder.php`:

    ```php
    [
        'name' => 'Bank Account - Current',
        'code' => 'BANK-001',
        'account_group_id' => $accountGroups->get('Current Assets')?->id,
        'account_type' => 'asset',
        'description' => 'Primary bank current account',
        'opening_balance' => 0,
        'current_balance' => 0,
        'is_system_account' => true,
        'is_active' => true,
    ],
    ```

2. **Remove bank seeding** from `OnboardingController.php`:

    - Remove the `DefaultBanksSeeder::seedForTenant()` call
    - Remove the import
    - Remove from verification logs

3. **Delete** `database/seeders/DefaultBanksSeeder.php`

4. **Run**: `php artisan optimize:clear`

## Notes

### Cash vs Bank Accounts:

-   **Cash in Hand**: Physical cash in the business - NO bank linked
-   **Petty Cash**: Small cash fund for minor expenses - NO bank linked
-   **Bank Accounts**: Created via Bank model - ALWAYS has linked ledger account

### Account Codes:

-   Cash accounts: `CASH-001`, `PETTY-001` (manual codes)
-   Bank accounts: `BANK-{NAME}-{LAST4}` (auto-generated codes)

### System vs User Accounts:

-   Cash/Petty Cash: `is_system_account = true` (protected from deletion)
-   Bank-linked accounts: `is_system_account = false` (user can manage)

## Future Enhancements

1. **Multi-Currency Support**: Allow banks in different currencies
2. **Bank Templates**: Pre-configured templates for popular Nigerian banks
3. **Account Verification**: API integration to verify account numbers
4. **Import from Statement**: Allow users to import initial balance from bank statement
5. **Multiple Default Banks**: Option to create multiple placeholder banks during onboarding

## Troubleshooting

### Bank created but no ledger account:

**Check**: Does "Current Assets" account group exist?

```sql
SELECT * FROM account_groups WHERE name = 'Current Assets' AND tenant_id = <tenant_id>;
```

**Fix**: Run account group seeder first:

```php
AccountGroupSeeder::seedForTenant($tenantId);
```

### Ledger account created but not linked:

**Check**: Bank's `ledger_account_id` field

```sql
SELECT id, bank_name, ledger_account_id FROM banks WHERE tenant_id = <tenant_id>;
```

**Fix**: Manually trigger sync:

```php
$bank = Bank::find($bankId);
$bank->syncLedgerAccount();
```

### Seeding fails with "Prepared statement" error:

**Solution**: Already handled in `OnboardingController` with:

-   Connection refresh before seeding
-   Retry mechanism (5 attempts)
-   Transaction wrapping

## Related Files

-   `app/Models/Bank.php` - Bank model with auto-ledger creation logic
-   `app/Models/LedgerAccount.php` - Ledger account model
-   `routes/tenant.php` - Banking routes (lines 457-483)
-   `resources/views/tenant/banking/` - Banking UI views
-   `DATABASE_MIGRATION_SAFETY.md` - Migration safety guide

## Completion Date

October 27, 2025

## Status

✅ **IMPLEMENTED AND READY FOR TESTING**
