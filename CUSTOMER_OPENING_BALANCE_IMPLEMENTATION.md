# Customer Opening Balance Implementation

## Overview

Added the ability to set opening balances for customers during creation. This allows importing customers with existing balances from previous systems.

## Changes Made

### 1. Customer Create Form (`resources/views/tenant/crm/customers/create.blade.php`)

Added a new **Opening Balance** section in the Financial Information (Section 5) with the following fields:

-   **Opening Balance Amount**: The amount of the opening balance (always positive)
-   **Balance Type**: Three options:
    -   `None` - No opening balance
    -   `Debit` - Customer owes you money (accounts receivable)
    -   `Credit` - You owe the customer (advance payment or credit)
-   **Opening Balance Date**: The date to record the opening balance (defaults to today)

#### Features:

-   Visual blue-highlighted section with icon and description
-   Automatic balance type selection when amount is entered
-   Helpful tooltips explaining debit vs credit
-   JavaScript validation to sync amount and type fields

### 2. Controller Updates (`app/Http/Controllers/Tenant/Crm/CustomerController.php`)

#### Added Imports:

```php
use App\Models\LedgerAccount;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
```

#### Updated `store()` Method:

-   Added validation for opening balance fields:
    -   `opening_balance_amount` - nullable, numeric, min:0
    -   `opening_balance_type` - nullable, in:none,debit,credit
    -   `opening_balance_date` - nullable, date
-   Wrapped customer creation in database transaction
-   Calls `createOpeningBalanceVoucher()` if opening balance is provided
-   Fixed all `\Log::` references to use the imported `Log` facade

#### Added New Method: `createOpeningBalanceVoucher()`

Creates a Journal Voucher (JV) to record the opening balance:

**For Debit Balance (Customer owes you):**

-   Debit: Customer Ledger Account
-   Credit: Opening Balance Equity

**For Credit Balance (You owe customer):**

-   Credit: Customer Ledger Account
-   Debit: Opening Balance Equity

The method:

1. Gets or creates the Journal Voucher type (code: 'JV')
2. Gets or creates the Opening Balance Equity account
3. Creates a voucher with status 'posted'
4. Creates appropriate debit/credit entries
5. Updates the customer's ledger account opening balance
6. Recalculates the ledger account balance

## How It Works

### Accounting Flow:

1. **Customer Creation**:

    - Customer record is created
    - Ledger account is automatically created (triggered by Customer model boot method)
    - If opening balance is provided, a Journal Voucher is created

2. **Opening Balance Recording**:

    - A posted Journal Voucher records the opening balance
    - The voucher links the customer's ledger account to Opening Balance Equity
    - The ledger account's `opening_balance` field is set
    - The `opening_balance_voucher_id` references the created voucher

3. **Balance Types**:
    - **Debit (DR)**: Indicates accounts receivable - customer owes money
        - Increases the asset (Accounts Receivable)
        - Balances against equity
    - **Credit (CR)**: Indicates customer has a credit balance
        - Decreases the asset or creates a liability
        - Customer has prepaid or you owe them

## Usage Example

### Scenario 1: Customer with Outstanding Invoice

A customer named "John Doe" owes $5,000 from a previous system:

```
Opening Balance Amount: 5000.00
Balance Type: Debit (Customer Owes You)
Opening Balance Date: 2025-01-01
```

**Result**:

-   Journal Voucher created
-   Debit: John Doe's Ledger Account $5,000
-   Credit: Opening Balance Equity $5,000
-   Customer's balance shows $5,000 DR

### Scenario 2: Customer with Advance Payment

A customer named "ABC Corp" has a $2,000 credit from advance payment:

```
Opening Balance Amount: 2000.00
Balance Type: Credit (You Owe Customer)
Opening Balance Date: 2025-01-01
```

**Result**:

-   Journal Voucher created
-   Credit: ABC Corp's Ledger Account $2,000
-   Debit: Opening Balance Equity $2,000
-   Customer's balance shows $2,000 CR

### Scenario 3: New Customer with No Balance

```
Opening Balance Type: None (No Opening Balance)
```

or leave the amount at 0.00

**Result**:

-   No opening balance voucher created
-   Customer starts with zero balance

## Database Structure

No database migrations required - uses existing structure:

-   **customers** table: Existing fields
-   **ledger_accounts** table:
    -   `opening_balance` (existing)
    -   `opening_balance_voucher_id` (existing)
-   **vouchers** table: Stores the opening balance journal voucher
-   **voucher_entries** table: Stores debit/credit entries

## Technical Notes

### Error Handling:

-   Wrapped in database transaction
-   Rolls back on any error
-   Logs errors for debugging
-   Shows user-friendly error messages

### System Requirements:

-   Journal Voucher type (code: 'JV') must exist
-   Will auto-create Opening Balance Equity account if missing
-   Requires authenticated user for voucher creation

### Validations:

-   Amount must be positive (>= 0)
-   Type must be one of: none, debit, credit
-   Date must be valid date format
-   All customer validations still apply

## Integration with Existing Features

### Customer Statements:

-   Opening balance is included in ledger balance calculations
-   Shows in customer statement reports
-   Affects total debits/credits

### Invoicing:

-   Opening balance contributes to total outstanding
-   Considered when calculating available credit

### Reports:

-   Opening balance vouchers appear in journal reports
-   Included in ledger account reports
-   Shows in Opening Balance Equity reconciliation

## Future Enhancements

Possible improvements:

1. Add opening balance edit functionality
2. Bulk import customers with opening balances
3. Opening balance adjustment vouchers
4. Historical balance tracking
5. Opening balance verification report

## Testing Checklist

-   [ ] Create customer with debit opening balance
-   [ ] Create customer with credit opening balance
-   [ ] Create customer with no opening balance
-   [ ] Verify journal voucher is created correctly
-   [ ] Check customer ledger account balance
-   [ ] Verify Opening Balance Equity account
-   [ ] Test form validation
-   [ ] Test with quick add modal (AJAX)
-   [ ] Verify transaction rollback on error
-   [ ] Check customer statements include opening balance

## Files Modified

1. `resources/views/tenant/crm/customers/create.blade.php`

    - Added opening balance UI section
    - Added JavaScript for field synchronization

2. `app/Http/Controllers/Tenant/Crm/CustomerController.php`
    - Added opening balance validation
    - Added `createOpeningBalanceVoucher()` method
    - Updated imports
    - Fixed Log facade references
    - Added database transaction support

## API/AJAX Support

The opening balance feature works with the quick-add customer modal:

-   Accepts opening balance parameters via AJAX
-   Returns customer with ledger account
-   Handles errors gracefully
-   Same validation as regular form submission
