# Bank Account Management System

## Overview

Complete bank account management system similar to Customers and Vendors, with full CRUD operations, ledger account integration, and bank reconciliation support.

## Database Structure

### Banks Table (`banks`)

Created via migration: `2025_10_26_000001_create_banks_table.php`

#### Core Bank Information

-   `bank_name` - Bank institution name (e.g., First Bank, GTBank, Access Bank)
-   `account_name` - Account holder name
-   `account_number` - Unique bank account number
-   `account_type` - Account type (savings, current, fixed deposit, etc.)
-   `branch_name` - Bank branch name
-   `branch_code` - Bank branch code
-   `swift_code` - SWIFT/BIC code for international transfers
-   `iban` - International Bank Account Number
-   `routing_number` - For US banks
-   `sort_code` - For UK banks

#### Contact Information

-   `branch_address`, `branch_city`, `branch_state` - Branch location
-   `branch_phone`, `branch_email` - Branch contact details
-   `relationship_manager` - Account manager name
-   `manager_phone`, `manager_email` - Manager contact details

#### Account Details

-   `currency` - Account currency (default: NGN)
-   `opening_balance` - Initial balance when account created
-   `current_balance` - Current account balance
-   `minimum_balance` - Minimum balance requirement
-   `overdraft_limit` - Overdraft facility limit
-   `account_opening_date` - Date account was opened
-   `last_reconciliation_date` - Last reconciliation date
-   `last_reconciled_balance` - Last reconciled balance

#### Online Banking

-   `online_banking_url` - Internet banking URL
-   `online_banking_username` - Login username
-   `online_banking_notes` - Encrypted notes for login details

#### Bank Charges & Limits

-   `monthly_maintenance_fee` - Monthly account maintenance fee
-   `transaction_limit_daily` - Daily transaction limit
-   `transaction_limit_monthly` - Monthly transaction limit
-   `free_transactions_per_month` - Number of free transactions
-   `excess_transaction_fee` - Fee for excess transactions

#### Additional Fields

-   `description` - Account description
-   `notes` - Additional notes
-   `custom_fields` - JSON field for custom data

#### Status & Flags

-   `status` - active, inactive, closed, suspended
-   `is_primary` - Mark as primary bank account (only one per tenant)
-   `is_payroll_account` - Used for payroll processing
-   `enable_reconciliation` - Enable bank reconciliation
-   `enable_auto_import` - Enable automatic bank feed import

#### Relationships

-   `tenant_id` - Links to tenant
-   `ledger_account_id` - Auto-created ledger account for accounting integration

## Model: `App\Models\Bank`

### Key Features

#### Automatic Ledger Account Creation

When a bank account is created:

1. Automatically creates a ledger account in "Cash & Bank" account group
2. Account code: `BANK-{BANK_INITIALS}-{LAST_4_DIGITS}`
3. Account name: `{Bank Name} - {Account Number}`
4. Account type: Asset
5. Opening balance from bank account opening_balance

#### Primary Account Management

-   Only one bank can be marked as primary per tenant
-   When a bank is set as primary, others are automatically unmarked

#### Balance Calculations

-   **`getCurrentBalance()`** - Gets actual balance from ledger account
-   **`getAvailableBalance()`** - Includes overdraft limit
-   **`hasSufficientFunds($amount)`** - Checks if funds available for transaction

#### Reconciliation Features

-   **`needsReconciliation($daysThreshold)`** - Checks if reconciliation is overdue
-   **`getReconciliationStatus()`** - Returns: never, current, due, overdue

#### Helper Methods

-   **`getDisplayNameAttribute`** - Formatted display name
-   **`getMaskedAccountNumberAttribute`** - Shows \*\*\*\*1234
-   **`getFullBranchAddressAttribute`** - Complete branch address
-   **`isApproachingMinimumBalance()`** - Warns if near minimum
-   **`isBelowMinimumBalance()`** - Checks if below minimum
-   **`getAccountAge()`** - Days since account opening
-   **`getMonthlyTransactionsCount()`** - This month's transaction count
-   **`getTotalTransactionsCount()`** - All-time transaction count
-   **`canBeDeleted()`** - Checks if safe to delete

### Scopes

-   `active()` - Only active accounts
-   `primary()` - Primary bank account
-   `forPayroll()` - Payroll-designated accounts
-   `byBank($bankName)` - Filter by bank name

## Controller: `App\Http\Controllers\Tenant\Banking\BankController`

### Routes

Base URL: `{tenant}/banking/banks`

| Method    | Route               | Action    | Description                         |
| --------- | ------------------- | --------- | ----------------------------------- |
| GET       | `/`                 | index     | List all bank accounts with filters |
| GET       | `/create`           | create    | Show create form                    |
| POST      | `/`                 | store     | Create new bank account             |
| GET       | `/{bank}`           | show      | Show bank details                   |
| GET       | `/{bank}/edit`      | edit      | Show edit form                      |
| PUT/PATCH | `/{bank}`           | update    | Update bank account                 |
| DELETE    | `/{bank}`           | destroy   | Delete bank account                 |
| GET       | `/{bank}/statement` | statement | Bank statement with transactions    |

### Index Page Features

-   **Search**: Bank name, account number, account name, branch name
-   **Filters**: Status, bank name
-   **Sorting**: Any column with sort direction
-   **Statistics**:
    -   Total banks count
    -   Active banks count
    -   Total balance across all active banks
    -   Banks needing reconciliation count
-   **Pagination**: 20 items per page

### Show Page Features

-   Complete bank details
-   Recent 10 transactions from ledger
-   Monthly statistics
-   Reconciliation status
-   Account age
-   Quick actions (Edit, Delete, View Statement)

### Statement Page Features

-   Date range filter (defaults to current month)
-   Opening balance calculation
-   Transaction list with:
    -   Date
    -   Particulars
    -   Voucher type
    -   Voucher number
    -   Debit
    -   Credit
    -   Running balance
-   Totals: Opening, Total Debits, Total Credits, Closing Balance

## Integration with Accounting System

### Ledger Account Integration

Each bank account automatically:

1. Creates a ledger account in assets
2. Syncs name and status changes
3. Records opening balance via voucher
4. Tracks all transactions through voucher entries

### Voucher System Integration

Bank transactions are recorded via:

-   **Receipt Vouchers (RV)** - Money coming in
-   **Payment Vouchers (PV)** - Money going out
-   **Journal Vouchers (JV)** - Adjustments and transfers

### Balance Synchronization

-   Bank `current_balance` stays in sync with ledger account
-   Ledger account calculates from all voucher entries
-   Real-time balance updates on transactions

## Usage Examples

### Creating a Bank Account

```php
Bank::create([
    'tenant_id' => $tenant->id,
    'bank_name' => 'First Bank',
    'account_name' => 'Profund Solution Ltd',
    'account_number' => '3012345678',
    'account_type' => 'current',
    'currency' => 'NGN',
    'opening_balance' => 1000000.00,
    'status' => 'active',
    'is_primary' => true,
    'enable_reconciliation' => true,
]);
// Auto-creates ledger account: "First Bank - 3012345678"
```

### Checking Balance

```php
$bank = Bank::find(1);

// Get current balance from ledger
$balance = $bank->getCurrentBalance();

// Get available balance (with overdraft)
$available = $bank->getAvailableBalance();

// Check if funds sufficient
if ($bank->hasSufficientFunds(50000)) {
    // Process payment
}
```

### Reconciliation Check

```php
$bank = Bank::find(1);

// Check if needs reconciliation
if ($bank->needsReconciliation(30)) {
    // Notify user to reconcile
}

// Get status
$status = $bank->getReconciliationStatus();
// Returns: 'never', 'current', 'due', 'overdue'
```

### Getting Bank Statement

```php
// Visit: /banking/banks/1/statement?start_date=2025-01-01&end_date=2025-01-31
// Shows all transactions between dates with running balance
```

## Security Features

### Account Deletion Protection

Bank accounts cannot be deleted if:

1. They have any transactions
2. They have a non-zero balance

### Primary Account Protection

-   Only one primary bank per tenant
-   Setting a new primary automatically removes old primary flag

### Online Banking Security

-   `online_banking_notes` field for storing encrypted login details
-   Should implement encryption in application

## Future Enhancements

### Bank Reconciliation Module

-   Match bank statement with ledger transactions
-   Identify discrepancies
-   Record reconciliation history
-   Track unreconciled items

### Bank Feed Integration

-   Connect to bank APIs for automatic transaction import
-   Real-time balance updates
-   Automatic transaction matching

### Bank Reports

-   Cash flow by bank account
-   Bank fees analysis
-   Account utilization reports
-   Reconciliation history reports

### Multi-Currency Support

-   Handle multiple currencies per bank
-   Exchange rate tracking
-   Currency conversion on reports

## Views (To Be Created)

### Required Views

1. **`resources/views/tenant/banking/banks/index.blade.php`** - List view
2. **`resources/views/tenant/banking/banks/create.blade.php`** - Create form
3. **`resources/views/tenant/banking/banks/show.blade.php`** - Detail view
4. **`resources/views/tenant/banking/banks/edit.blade.php`** - Edit form
5. **`resources/views/tenant/banking/banks/statement.blade.php`** - Statement view

### View Components to Include

-   Search and filter form
-   Statistics cards
-   Bank account cards with key metrics
-   Transaction tables
-   Status badges
-   Action buttons
-   Reconciliation status indicators

## Navigation

### Accounting Dashboard

-   Added link in "Account Management" section
-   Route: `{{ route('tenant.banking.banks.index', ['tenant' => $tenant->slug]) }}`
-   Icon: Credit card icon
-   Color: Emerald gradient

## Testing Checklist

-   [ ] Create bank account with minimum fields
-   [ ] Create bank account with all fields
-   [ ] Verify ledger account auto-creation
-   [ ] Verify opening balance voucher creation
-   [ ] Test primary bank flag (only one primary)
-   [ ] Edit bank account
-   [ ] Verify ledger account sync on update
-   [ ] Delete bank account (should fail with transactions)
-   [ ] Delete bank account (should work with zero balance)
-   [ ] Search by bank name, account number
-   [ ] Filter by status, bank name
-   [ ] View bank statement with date range
-   [ ] Check reconciliation status calculations
-   [ ] Test balance calculations
-   [ ] Test available balance with overdraft
-   [ ] Test minimum balance warnings
-   [ ] Test payroll account flag
-   [ ] Test custom fields JSON storage

## Database Indexes

Optimized for:

-   Tenant lookups
-   Account number searches
-   Status filtering
-   Primary account queries
-   Reconciliation date filtering
-   Account opening date queries

## Logging

All operations are logged:

-   Bank account creation
-   Bank account updates
-   Bank account deletion
-   Ledger account creation
-   Errors and exceptions

Check logs at: `storage/logs/laravel.log`

## Related Models

-   **Tenant** - Multi-tenancy
-   **LedgerAccount** - Accounting integration
-   **AccountGroup** - Chart of accounts
-   **VoucherEntry** - Transactions
-   **Voucher** - Transaction documents

## Migration Status

✅ Migration created: `2025_10_26_000001_create_banks_table.php`
✅ Migration run successfully
✅ Table `banks` created in database

## Next Steps

1. Create view files (index, create, show, edit, statement)
2. Test complete CRUD operations
3. Implement bank reconciliation module
4. Add bank statement import functionality
5. Create bank reports
6. Add multi-currency support
7. Implement bank feed integration
