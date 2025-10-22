# Date-Based Ledger Balance Implementation

## Overview

Implemented time-sensitive ledger account balance calculations, allowing users to view historical account balances for specific periods, similar to the existing product stock movement functionality.

## Problem Statement

Previously, ledger accounts only showed current balances without the ability to filter by date range. This made it difficult to:

-   View historical account balances as of specific dates
-   Generate period-specific financial reports
-   Reconcile accounts for past periods
-   Track balance changes over time

## Solution Implemented

### 1. Enhanced LedgerAccountController@show Method

**File:** `app/Http/Controllers/Tenant/Accounting/LedgerAccountController.php`

#### Key Features:

1. **Date Range Filtering:**

    ```php
    $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
    $toDate = $request->get('to_date', now()->toDateString());
    $asOfDate = $request->get('as_of_date', $toDate);
    ```

2. **Opening Balance Calculation:**

    - Calculates balance as of the day before `from_date`
    - Uses `getCurrentBalance()` with date parameter
    - Does not use cache for historical data

3. **Period Transactions:**

    - Filters voucher entries by `voucher_date` between `from_date` and `to_date`
    - Only includes posted vouchers
    - Ordered by voucher date descending

4. **Closing Balance:**

    - Calculates balance as of `as_of_date`
    - Reflects all transactions up to that date

5. **Period Movement:**
    - Calculates net change during period
    - Respects account type (asset/expense vs liability/income/equity)

### 2. New Ledger Statement Method

**File:** `app/Http/Controllers/Tenant/Accounting/VoucherController.php`

**Method:** `ledgerStatement()`

#### Features:

1. **Running Balance Calculation:**

    ```php
    $runningBalance = $openingBalance;
    foreach ($entries as $entry) {
        $movement = /* calculate based on account type */;
        $runningBalance += $movement;
        // Store line with running balance
    }
    ```

2. **Account Type Awareness:**

    - **Asset & Expense:** Debit increases, Credit decreases
    - **Liability, Equity, Income:** Credit increases, Debit decreases

3. **Statement Lines:**
   Each line includes:

    - Date
    - Voucher number and type
    - Particulars and reference
    - Debit/Credit amounts
    - Movement amount
    - Running balance

4. **Period Summary:**
    - Opening balance
    - Total debits for period
    - Total credits for period
    - Closing balance
    - Current balance (for comparison)

## Database Structure

### Existing Tables (No Changes Required)

#### vouchers table

```sql
- id
- tenant_id
- voucher_number
- voucher_date          ← KEY: Used for date filtering
- voucher_type_id
- status                ← Only 'posted' vouchers included
- total_amount
- narration
- reference_number
- created_at
- updated_at
```

#### voucher_entries table

```sql
- id
- voucher_id
- ledger_account_id     ← Links to account
- debit_amount
- credit_amount
- particulars
- created_at
- updated_at
```

#### ledger_accounts table

```sql
- id
- tenant_id
- name
- code
- account_type          ← KEY: Determines balance calculation logic
- opening_balance
- current_balance
- last_transaction_date
- is_active
- created_at
- updated_at
```

## Balance Calculation Logic

### For Asset & Expense Accounts

```
Opening Balance    = Balance as of (from_date - 1 day)
Period Movement    = Sum(Debits) - Sum(Credits)
Closing Balance    = Opening Balance + Period Movement
Current Balance    = Balance as of today
```

**Example - Expenses Account:**

```
10-10-2025: Opening Balance    = ₦4,500,000
10-10-2025: Entry 1            = ₦300,000 (Debit)
15-10-2025: Entry 2            = ₦200,000 (Debit)
22-10-2025: Entry 3            = ₦1,500,000 (Debit)

If user filters to 10-10-2025:
- Opening Balance: ₦4,500,000
- Period Debits: ₦300,000
- Period Credits: ₦0
- Closing Balance: ₦4,800,000

If user filters to 22-10-2025:
- Opening Balance: ₦4,500,000
- Period Debits: ₦2,000,000
- Period Credits: ₦0
- Closing Balance: ₦6,500,000 ← Current Balance
```

### For Liability, Equity & Income Accounts

```
Opening Balance    = Balance as of (from_date - 1 day)
Period Movement    = Sum(Credits) - Sum(Debits)
Closing Balance    = Opening Balance + Period Movement
Current Balance    = Balance as of today
```

**Example - Sales Revenue Account:**

```
10-10-2025: Opening Balance    = ₦10,000,000
10-10-2025: Sale 1             = ₦500,000 (Credit)
15-10-2025: Sale 2             = ₦800,000 (Credit)
22-10-2025: Sale 3             = ₦1,200,000 (Credit)

If user filters to 10-10-2025:
- Opening Balance: ₦10,000,000
- Period Credits: ₦500,000
- Period Debits: ₦0
- Closing Balance: ₦10,500,000

If user filters to 22-10-2025:
- Opening Balance: ₦10,000,000
- Period Credits: ₦2,500,000
- Period Debits: ₦0
- Closing Balance: ₦12,500,000 ← Current Balance
```

## Usage Examples

### Example 1: View Ledger Account Statement

**URL:** `/tenant/{tenant}/accounting/ledger-accounts/{account}?from_date=2025-10-01&to_date=2025-10-10`

**Parameters:**

-   `from_date`: Start date of period (default: start of current month)
-   `to_date`: End date of period (default: today)
-   `as_of_date`: Date for closing balance calculation (default: to_date)

**Result:**

-   Opening balance as of 2025-09-30
-   All transactions from 2025-10-01 to 2025-10-10
-   Closing balance as of 2025-10-10
-   Current balance (today's balance) for comparison

### Example 2: Generate Ledger Statement

**URL:** `/tenant/{tenant}/accounting/vouchers/ledger-statement/{account}?from_date=2025-10-01&to_date=2025-10-15`

**Result:**

```
Account: Expenses Account (EXP-001)
Period: 01-10-2025 to 15-10-2025

Opening Balance (30-09-2025):  ₦4,500,000.00

Date       | Voucher   | Particulars        | Debit        | Credit      | Balance
-----------|-----------|-------------------|--------------|-------------|-------------
01-10-2025 | PV-001    | Rent Payment      | ₦300,000.00  | ₦0.00       | ₦4,800,000.00
05-10-2025 | JV-045    | Salary Expense    | ₦800,000.00  | ₦0.00       | ₦5,600,000.00
10-10-2025 | PV-015    | Utilities         | ₦200,000.00  | ₦0.00       | ₦5,800,000.00

Period Totals:                               ₦1,300,000.00  ₦0.00
Closing Balance (15-10-2025):                               ₦5,800,000.00
Current Balance (22-10-2025):                               ₦6,500,000.00
```

## Integration with Existing Features

### 1. Compatible with Product Stock Logic

The implementation mirrors the product stock movement approach:

**Product Stock:**

```php
$asOfDate = request('as_of_date', now()->toDateString());
$product->getStockAsOfDate($asOfDate);
```

**Ledger Balance:**

```php
$asOfDate = $request->get('as_of_date', now()->toDateString());
$ledgerAccount->getCurrentBalance($asOfDate, false);
```

### 2. Uses Existing Model Methods

Leverages `LedgerAccount::getCurrentBalance()` method:

-   Already supports `$asOfDate` parameter
-   Already handles account type logic
-   Caching can be disabled for historical queries

### 3. Respects Voucher Status

Only includes posted vouchers:

```php
->whereHas('voucher', function ($query) {
    $query->where('status', 'posted');
})
```

Draft vouchers are excluded from balance calculations.

## Benefits

1. **Historical Analysis:**

    - View account balances at any point in time
    - Track balance evolution over periods

2. **Period Reconciliation:**

    - Reconcile accounts for specific months/quarters
    - Compare opening vs closing balances

3. **Financial Reporting:**

    - Generate accurate period-specific reports
    - P&L and Balance Sheet for any date range

4. **Audit Trail:**

    - Complete transaction history with running balances
    - Easy to identify when specific balances occurred

5. **Consistency:**
    - Same logic as product stock movements
    - Familiar interface for users

## View Requirements

The following views need to be updated/created:

### 1. Update: `ledger-accounts/show.blade.php`

Add date filter form:

```html
<form
    method="GET"
    action="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $ledgerAccount]) }}"
>
    <input type="date" name="from_date" value="{{ $fromDate }}" />
    <input type="date" name="to_date" value="{{ $toDate }}" />
    <button type="submit">Filter</button>
</form>

<div class="balance-summary">
    <div>Opening Balance: {{ number_format($openingBalance, 2) }}</div>
    <div>Period Debits: {{ number_format($totalDebits, 2) }}</div>
    <div>Period Credits: {{ number_format($totalCredits, 2) }}</div>
    <div>Closing Balance: {{ number_format($closingBalance, 2) }}</div>
    <div>Current Balance: {{ number_format($currentBalance, 2) }}</div>
</div>
```

### 2. Create: `vouchers/ledger-statement.blade.php`

Complete ledger statement view with:

-   Date range filter
-   Opening balance
-   Transaction list with running balances
-   Period totals
-   Closing balance
-   Export options (PDF, Excel)

## Testing Scenarios

### Scenario 1: Expense Account

**Setup:**

-   Account: Office Expenses
-   Opening (01-10-2025): ₦500,000

**Transactions:**

-   05-10-2025: Debit ₦100,000 (Stationery)
-   10-10-2025: Debit ₦150,000 (Supplies)
-   15-10-2025: Debit ₦50,000 (Misc)
-   20-10-2025: Debit ₦200,000 (Equipment)

**Test Cases:**

1. Filter 01-10-2025 to 10-10-2025:

    - Opening: ₦500,000
    - Closing: ₦750,000

2. Filter 01-10-2025 to 15-10-2025:

    - Opening: ₦500,000
    - Closing: ₦800,000

3. Filter 01-10-2025 to 22-10-2025:
    - Opening: ₦500,000
    - Closing: ₦1,000,000 (Current)

### Scenario 2: Revenue Account

**Setup:**

-   Account: Sales Revenue
-   Opening (01-10-2025): ₦2,000,000

**Transactions:**

-   03-10-2025: Credit ₦300,000 (Sale 1)
-   08-10-2025: Credit ₦450,000 (Sale 2)
-   12-10-2025: Credit ₦250,000 (Sale 3)
-   18-10-2025: Credit ₦500,000 (Sale 4)

**Test Cases:**

1. Filter 01-10-2025 to 08-10-2025:

    - Opening: ₦2,000,000
    - Closing: ₦2,750,000

2. Filter 01-10-2025 to 12-10-2025:

    - Opening: ₦2,000,000
    - Closing: ₦3,000,000

3. Filter 01-10-2025 to 22-10-2025:
    - Opening: ₦2,000,000
    - Closing: ₦3,500,000 (Current)

## Route Updates Required

Add to `routes/tenant.php`:

```php
// Ledger Statement
Route::get('/accounting/vouchers/ledger-statement/{ledgerAccount}',
    [VoucherController::class, 'ledgerStatement'])
    ->name('accounting.vouchers.ledger-statement');
```

## Performance Considerations

1. **Cache Management:**

    - Disable cache for historical queries: `getCurrentBalance($date, false)`
    - Current balance can use cache: `getCurrentBalance()`

2. **Query Optimization:**

    - Use `whereBetween('voucher_date', [$from, $to])`
    - Eager load relationships: `with(['voucher.voucherType'])`
    - Index on `voucher_date` recommended

3. **Pagination:**
    - Ledger statement paginated (50 per page)
    - Large date ranges handled efficiently

## Migration Notes

**No database migration required** - uses existing schema:

-   `vouchers.voucher_date` already exists
-   `ledger_accounts.account_type` already exists
-   `voucher_entries` relationships already established

## Future Enhancements

1. **Export Functionality:**

    - PDF export of ledger statements
    - Excel export with formulas
    - CSV download

2. **Comparative Analysis:**

    - Compare same period across years
    - Month-over-month trends
    - Year-to-date vs previous year

3. **Graphical Visualization:**

    - Balance trend charts
    - Period comparison graphs
    - Account activity heatmaps

4. **Advanced Filters:**
    - Filter by voucher type
    - Filter by amount range
    - Search by particulars

## Summary

The date-based ledger balance system provides:

-   ✅ Historical balance viewing for any date
-   ✅ Period-specific transaction filtering
-   ✅ Running balance calculations
-   ✅ Opening and closing balance tracking
-   ✅ Current vs historical comparison
-   ✅ Consistent with product stock logic
-   ✅ No database changes required
-   ✅ Respects account type for proper calculations

Users can now view ledger balances for any historical period, making reconciliation, auditing, and financial reporting much more flexible and accurate.
