# Vendor Opening Balance Implementation

## Overview

This document describes the implementation of opening balance functionality for vendor creation, mirroring the existing customer opening balance feature.

## Implementation Date

October 19, 2025

## Features Added

### 1. Opening Balance UI (Vendor Creation Form)

**File:** `resources/views/tenant/crm/vendors/create.blade.php`

Added a new section in the Financial Information collapsible section with:

-   **Opening Balance Amount**: Numeric input field for the balance amount
-   **Balance Type**: Dropdown with three options:
    -   `None`: No opening balance (default)
    -   `Credit`: We owe the vendor money (Accounts Payable)
    -   `Debit`: Vendor owes us money (advance payment/prepayment)
-   **As of Date**: Date picker for the opening balance date (defaults to current date)

**Visual Design:**

-   Purple-themed info box with icon
-   Clear explanation of what opening balance means
-   Helper text explaining the difference between credit and debit
-   Consistent with customer opening balance design

### 2. Controller Updates

**File:** `app/Http/Controllers/Tenant/Crm/VendorController.php`

#### Added Imports

```php
use App\Models\LedgerAccount;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use App\Models\AccountGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
```

#### Updated Validation Rules

Added validation for:

-   `opening_balance_amount`: nullable|numeric|min:0
-   `opening_balance_type`: nullable|in:none,debit,credit
-   `opening_balance_date`: nullable|date

#### Enhanced Store Method

-   Wrapped vendor creation in database transaction
-   Excludes opening balance fields from mass assignment
-   Calls `createOpeningBalanceVoucher()` if amount > 0 and type ≠ 'none'
-   Proper error handling with rollback on failure

#### New Method: createOpeningBalanceVoucher()

Creates a journal voucher for vendor opening balance:

**Credit Balance (We owe vendor):**

```
Debit:  Opening Balance Equity    $X
Credit: Vendor Ledger Account      $X
```

**Debit Balance (Vendor owes us - advance payment):**

```
Debit:  Vendor Ledger Account      $X
Credit: Opening Balance Equity     $X
```

**Features:**

-   Automatically finds or creates Journal Voucher type (JV)
-   Finds or creates Opening Balance Equity account
-   Creates equity account group if it doesn't exist
-   Generates unique account codes (OBE-001, OBE-002, etc.)
-   Creates posted voucher with proper entries
-   Updates vendor's ledger account balance
-   Links opening balance voucher to ledger account

### 3. JavaScript Enhancements

**File:** `resources/views/tenant/crm/vendors/create.blade.php`

Added client-side logic for better UX:

-   Auto-selects "Credit" balance type when amount > 0 (default: we owe vendor)
-   Resets amount to 0.00 when type changed to "None"
-   Integrated with form validation
-   Works with progress tracking

## Accounting Treatment

### Credit Opening Balance (Accounts Payable)

When a vendor has a **credit** opening balance:

-   **Meaning**: You owe the vendor money
-   **Common Scenario**: Outstanding invoices from previous system
-   **Journal Entry**:
    ```
    Date: [Opening Balance Date]
    Debit:  Opening Balance Equity    $1,000.00
    Credit: [Vendor Name]              $1,000.00
    Narration: Opening Balance for [Vendor Name]
    ```
-   **Effect on Vendor Ledger**: Increases credit balance (liability)

### Debit Opening Balance (Vendor Advance)

When a vendor has a **debit** opening balance:

-   **Meaning**: Vendor owes you money (advance payment/prepayment)
-   **Common Scenario**: Prepaid for goods/services not yet received
-   **Journal Entry**:
    ```
    Date: [Opening Balance Date]
    Debit:  [Vendor Name]              $500.00
    Credit: Opening Balance Equity     $500.00
    Narration: Opening Balance for [Vendor Name]
    ```
-   **Effect on Vendor Ledger**: Increases debit balance (asset)

## Database Schema

### Voucher Entry

The opening balance creates entries in:

-   **vouchers** table: Main voucher record
-   **voucher_entries** table: Debit and credit entries
-   **ledger_accounts** table: Updated with opening_balance_voucher_id

### Ledger Account Fields Used

-   `opening_balance`: Stores the opening balance amount (+ for credit, - for debit)
-   `opening_balance_voucher_id`: Links to the journal voucher
-   `current_balance`: Updated automatically via `updateCurrentBalance()`
-   `is_opening_balance_account`: Flag for Opening Balance Equity account

## Comparison with Customer Implementation

### Similarities

-   Same UI structure and layout
-   Same accounting logic (using Opening Balance Equity)
-   Same validation rules
-   Same transaction handling
-   Same voucher creation process

### Differences

-   **Default Balance Type**:
    -   Customer: Debit (customer owes us)
    -   Vendor: Credit (we owe vendor)
-   **Ledger Account Nature**:
    -   Customer: Asset (Accounts Receivable)
    -   Vendor: Liability (Accounts Payable)
-   **Color Scheme**:
    -   Customer: Blue theme
    -   Vendor: Purple theme

## Usage Examples

### Example 1: Vendor with Outstanding Invoice

**Scenario**: Migrating from old system, you owe ABC Supplies $2,500 for unpaid invoices.

**Input:**

-   Amount: 2500.00
-   Type: Credit (We Owe Vendor)
-   Date: 2024-12-31

**Result:**

-   Journal voucher created and posted
-   Vendor ledger shows $2,500 credit balance
-   Opening Balance Equity reduced by $2,500

### Example 2: Vendor with Advance Payment

**Scenario**: You paid XYZ Company $1,000 advance for future services.

**Input:**

-   Amount: 1000.00
-   Type: Debit (Vendor Owes Us)
-   Date: 2024-12-31

**Result:**

-   Journal voucher created and posted
-   Vendor ledger shows $1,000 debit balance
-   Opening Balance Equity increased by $1,000

## Testing Checklist

### Manual Testing

-   [ ] Create vendor with no opening balance
-   [ ] Create vendor with credit opening balance
-   [ ] Create vendor with debit opening balance
-   [ ] Verify journal voucher created correctly
-   [ ] Verify ledger account balance updated
-   [ ] Verify vendor listing shows correct balance
-   [ ] Test validation (negative amounts, invalid dates)
-   [ ] Test with AJAX (quick add modal)
-   [ ] Test "Save & Add New" functionality
-   [ ] Verify progress indicator includes opening balance fields

### Accounting Verification

-   [ ] Credit balance increases Accounts Payable
-   [ ] Debit balance creates Vendor Advance (asset)
-   [ ] Opening Balance Equity account balances correctly
-   [ ] Voucher entries are balanced (total debits = total credits)
-   [ ] Ledger account current_balance reflects opening balance
-   [ ] Posted voucher appears in journal reports

### Edge Cases

-   [ ] Opening balance of exactly 0.00
-   [ ] Very large opening balance amounts
-   [ ] Future opening balance dates
-   [ ] Past opening balance dates (years ago)
-   [ ] Creating multiple vendors with opening balances
-   [ ] Deleting vendor with opening balance voucher

## File Changes Summary

### Modified Files

1. **resources/views/tenant/crm/vendors/create.blade.php**

    - Added opening balance section (67 lines)
    - Added JavaScript handlers (18 lines)

2. **app/Http/Controllers/Tenant/Crm/VendorController.php**
    - Added 8 new imports
    - Updated validation rules (+3 fields)
    - Wrapped store() in DB transaction
    - Added createOpeningBalanceVoucher() method (145 lines)
    - Enhanced error handling

### Total Changes

-   **Lines Added**: ~240 lines
-   **Files Modified**: 2 files
-   **New Methods**: 1 (createOpeningBalanceVoucher)

## Migration Notes

### For Existing Vendors

If you have existing vendors without opening balances:

1. Edit the vendor record
2. Add opening balance in the Financial Information section
3. System will create the journal voucher automatically

### Data Migration Script

For bulk vendor migration with opening balances, create a seeder:

```php
foreach ($oldVendors as $oldVendor) {
    $vendor = Vendor::create([
        // ... vendor data
    ]);

    if ($oldVendor->opening_balance != 0) {
        $this->createOpeningBalanceVoucher(
            $vendor,
            abs($oldVendor->opening_balance),
            $oldVendor->opening_balance > 0 ? 'credit' : 'debit',
            $oldVendor->opening_balance_date
        );
    }
}
```

## Future Enhancements

### Potential Improvements

1. **Bulk Import**: Support CSV import with opening balances
2. **Opening Balance Report**: List all vendors with opening balances
3. **Balance Adjustment**: Allow editing opening balance after creation
4. **Audit Trail**: Track opening balance changes
5. **Multi-Currency**: Support opening balances in different currencies
6. **Opening Balance Summary**: Dashboard widget showing total opening balances

### Known Limitations

1. Opening balance cannot be edited after vendor creation (requires manual journal adjustment)
2. No warning if opening balance date is in the future
3. No validation against other vendor balances (could create data inconsistencies)

## Related Documentation

-   [Customer Opening Balance Implementation](./docs/USER_PROFILE_IMPLEMENTATION.md)
-   [Journal Voucher System](./docs/JOURNAL_VOUCHER_SYSTEM.md)
-   [Ledger Account Management](./docs/LEDGER_ACCOUNT_MANAGEMENT.md)
-   [Opening Balance Equity Account](./docs/OPENING_BALANCE_EQUITY_IMPLEMENTATION.md)

## Support

For issues or questions about vendor opening balance:

1. Check journal voucher was created in Accounting > Journals
2. Verify ledger account balance in Chart of Accounts
3. Review voucher entries for correct debit/credit amounts
4. Check application logs for any errors during creation

## Conclusion

The vendor opening balance feature provides seamless migration from legacy systems by allowing proper setup of vendor payables and advances. The implementation maintains accounting principles with double-entry bookkeeping and provides clear audit trails through journal vouchers.
