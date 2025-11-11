# POS Accounting Integration - Quick Test Guide

## Testing Steps

### 1. Complete a Sale in POS

1. Navigate to POS module in your tenant
2. Add products to cart (at least 2 products for better testing)
3. Proceed to checkout
4. Complete the sale
5. Receipt should generate successfully

### 2. Verify Database Records

**Check Sale was created:**

```sql
SELECT * FROM sales ORDER BY id DESC LIMIT 1;
```

**Check Sale Items have tenant_id:**

```sql
SELECT id, sale_id, product_id, tenant_id, quantity, line_total
FROM sale_items
WHERE sale_id = [YOUR_SALE_ID];
```

**Check Voucher was created:**

```sql
SELECT v.id, v.voucher_number, v.voucher_date, v.reference_number, v.narration, v.total_amount, v.status
FROM vouchers v
WHERE v.reference_number LIKE 'SALE-%'
ORDER BY v.id DESC LIMIT 1;
```

**Check Voucher Entries (should be 2 or 4):**

```sql
SELECT
    ve.id,
    la.name as account_name,
    la.code as account_code,
    ve.debit_amount,
    ve.credit_amount,
    ve.particulars
FROM voucher_entries ve
JOIN ledger_accounts la ON ve.ledger_account_id = la.id
WHERE ve.voucher_id = [YOUR_VOUCHER_ID]
ORDER BY ve.id;
```

Expected results (with COGS):
| Account | Code | Debit | Credit | Particulars |
|---------|------|-------|--------|-------------|
| Cash in Hand | CASH-001 | 5000.00 | 0.00 | Cash received - SALE-... |
| Sales Revenue | SALES-001 | 0.00 | 5000.00 | Sales revenue - SALE-... |
| Cost of Goods Sold | COGS-001 | 3000.00 | 0.00 | Cost of goods sold - SALE-... |
| Inventory | INV-001 | 0.00 | 3000.00 | Inventory reduction - SALE-... |

**Verify Balance:**

```sql
SELECT
    SUM(debit_amount) as total_debits,
    SUM(credit_amount) as total_credits
FROM voucher_entries
WHERE voucher_id = [YOUR_VOUCHER_ID];
```

Should be equal!

### 3. Check Ledger Account Balances

**Cash Account:**

```sql
SELECT name, code, current_balance, last_transaction_date
FROM ledger_accounts
WHERE code = 'CASH-001';
```

**Sales Account:**

```sql
SELECT name, code, current_balance, last_transaction_date
FROM ledger_accounts
WHERE code = 'SALES-001';
```

**COGS Account:**

```sql
SELECT name, code, current_balance, last_transaction_date
FROM ledger_accounts
WHERE code = 'COGS-001';
```

**Inventory Account:**

```sql
SELECT name, code, current_balance, last_transaction_date
FROM ledger_accounts
WHERE code = 'INV-001';
```

### 4. View in Accounting UI

#### Cash Account Statement

1. Navigate to: **Accounting → Ledger Accounts**
2. Find "Cash in Hand" account
3. Click "View Statement"
4. You should see POS sale as a debit entry

#### Sales Revenue Statement

1. Navigate to: **Accounting → Ledger Accounts**
2. Find "Sales Revenue" account
3. Click "View Statement"
4. You should see POS sale as a credit entry

#### View Voucher

1. Navigate to: **Accounting → Vouchers**
2. Find voucher with your sale number in reference
3. Click to view details
4. Should show all 2-4 entries with balanced debits/credits

### 5. Check Laravel Logs

**Success Log:**

```
[timestamp] local.INFO: POS: Accounting entries created successfully
{
    "sale_id": 123,
    "voucher_id": 456,
    "voucher_number": "SV-0001"
}
```

**If accounts missing:**

```
[timestamp] local.WARNING: POS: Missing required ledger accounts for accounting entries
{
    "sale_id": 123,
    "cash_account": "missing",
    "sales_account": "found"
}
```

**If error occurred:**

```
[timestamp] local.ERROR: POS: Failed to create accounting entries
{
    "sale_id": 123,
    "error": "...",
    "trace": "..."
}
```

## Common Issues & Solutions

### Issue 1: No voucher created

**Symptom:** Sale completes but no voucher in database
**Check:**

-   Are ledger accounts (CASH-001, SALES-001) present?
-   Run: `SELECT * FROM ledger_accounts WHERE code IN ('CASH-001', 'SALES-001');`
    **Solution:**
-   Seed default ledger accounts for your tenant
-   Run: `php artisan db:seed --class=DefaultLedgerAccountsSeeder`

### Issue 2: Voucher entries not balanced

**Symptom:** Debits ≠ Credits in voucher
**Check:** Run the balance query above
**Solution:** This shouldn't happen - contact developer

### Issue 3: COGS entries missing

**Symptom:** Only 2 entries (Cash + Sales) instead of 4
**Possible Causes:**

-   COGS or Inventory account doesn't exist
-   Products don't have purchase_rate (cost price) set
    **Solution:**
-   Check: `SELECT * FROM ledger_accounts WHERE code IN ('COGS-001', 'INV-001');`
-   Check: `SELECT id, name, purchase_rate FROM products WHERE id IN (...);`
-   This is optional - system works fine without COGS tracking

### Issue 4: tenant_id error in sale_items

**Symptom:** Error when completing sale
**Check:**

```sql
DESCRIBE sale_items;
```

**Solution:** Migration should have fixed this. If not:

```bash
php artisan migrate --path=database/migrations/2025_11_11_104705_add_tenant_id_to_sale_items_table.php
```

## Accounting Reports to Verify

### Trial Balance

1. Navigate to: **Accounting → Reports → Trial Balance**
2. Select date range including your sale
3. Verify:
    - Cash in Hand shows in Debit column
    - Sales Revenue shows in Credit column
    - Total Debits = Total Credits

### Cash Book

1. Navigate to: **Accounting → Reports → Cash Book**
2. Should show POS sales as receipts

### Income Statement (Profit & Loss)

1. Navigate to: **Accounting → Reports → Income Statement**
2. Sales revenue should include POS sales
3. COGS should include cost of POS items sold
4. Gross Profit = Sales - COGS

### Balance Sheet

1. Navigate to: **Accounting → Reports → Balance Sheet**
2. Cash balance should reflect POS receipts
3. Inventory balance should reflect reduction

## Quick SQL Test Script

Run this after completing a sale:

```sql
-- Get latest sale
SELECT @sale_id := id, @sale_number := sale_number
FROM sales
ORDER BY id DESC LIMIT 1;

-- Show sale details
SELECT
    s.id as sale_id,
    s.sale_number,
    s.total_amount,
    s.sale_date,
    COUNT(si.id) as item_count
FROM sales s
LEFT JOIN sale_items si ON s.id = si.id
WHERE s.id = @sale_id
GROUP BY s.id;

-- Show voucher created
SELECT
    v.voucher_number,
    v.voucher_date,
    v.reference_number,
    v.total_amount,
    v.status
FROM vouchers v
WHERE v.reference_number = @sale_number;

-- Show all entries with account details
SELECT
    la.name as account,
    la.code,
    ag.name as group_name,
    ve.debit_amount,
    ve.credit_amount,
    ve.particulars
FROM voucher_entries ve
JOIN vouchers v ON ve.voucher_id = v.id
JOIN ledger_accounts la ON ve.ledger_account_id = la.id
JOIN account_groups ag ON la.account_group_id = ag.id
WHERE v.reference_number = @sale_number
ORDER BY ve.id;

-- Verify balanced
SELECT
    SUM(ve.debit_amount) as total_debits,
    SUM(ve.credit_amount) as total_credits,
    SUM(ve.debit_amount) - SUM(ve.credit_amount) as difference
FROM voucher_entries ve
JOIN vouchers v ON ve.voucher_id = v.id
WHERE v.reference_number = @sale_number;
```

## Success Criteria

✅ Sale completes without error
✅ Receipt generates and opens
✅ Sale record in database with correct total
✅ Sale items have tenant_id
✅ Voucher created with status 'posted'
✅ 2 or 4 voucher entries created
✅ Debits equal credits (balanced)
✅ Cash account balance increased
✅ Sales account balance increased
✅ Entries visible in accounting UI
✅ No errors in Laravel log

## Next Steps After Successful Test

1. **Test with multiple sales** - Ensure numbering works (SV-0001, SV-0002, etc.)
2. **Test with different products** - Verify COGS calculation
3. **Test with zero-cost products** - Should skip COGS entries
4. **Review Trial Balance** - Ensure all accounts balanced
5. **Generate reports** - Verify POS data shows correctly

## Troubleshooting Commands

**Check if voucher type exists:**

```bash
php artisan tinker
>>> VoucherType::where('code', 'SV')->first();
```

**Manually create voucher type if missing:**

```bash
php artisan tinker
>>> VoucherType::create([
    'tenant_id' => 1, // Change to your tenant ID
    'name' => 'Sales',
    'code' => 'SV',
    'abbreviation' => 'S',
    'description' => 'Sales vouchers from POS',
    'numbering_method' => 'auto',
    'prefix' => 'SV-',
    'starting_number' => 1,
    'current_number' => 0,
    'has_reference' => true,
    'affects_inventory' => true,
    'inventory_effect' => 'decrease',
    'affects_cashbank' => false,
    'is_system_defined' => true,
    'is_active' => true,
]);
```

**Clear caches if changes not reflecting:**

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Documentation References

-   Main Documentation: `POS_ACCOUNTING_INTEGRATION.md`
-   Product Ledger Accounts: `PRODUCT_LEDGER_ACCOUNTS_IMPLEMENTATION.md`
-   Ledger Balance Calculation: `DATE_BASED_LEDGER_BALANCE_IMPLEMENTATION.md`
