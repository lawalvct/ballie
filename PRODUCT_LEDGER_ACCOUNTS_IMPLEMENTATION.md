# Product Ledger Accounts Implementation

## Overview

Updated the invoice accounting system to use **product-specific ledger accounts** for both sales and purchase transactions, ensuring accurate revenue and expense tracking per product.

## Problem Statement

Previously, the system used a generic "Sales Account" (found by searching for '%Sales%') for all sales transactions. This approach:

-   Did not respect individual product ledger account assignments
-   Could not differentiate between different product categories
-   Did not support purchase invoice ledger entries properly

## Solution Implemented

### 1. Product-Specific Account Usage

Each product now has three ledger account fields (from migration):

-   `stock_asset_account_id` - For inventory valuation (Asset account - e.g., "Inventory")
-   `sales_account_id` - For sales revenue (Income account - e.g., "Sales Revenue")
-   `purchase_account_id` - For purchase costs (Expense account - e.g., "Cost of Goods Sold")

### 2. Updated Invoice Controller

**File:** `app/Http/Controllers/Tenant/Accounting/InvoiceController.php`

**Method:** `createAccountingEntries()`

#### Key Changes:

1. **Voucher Type Detection:**

    ```php
    $isSales = in_array($voucher->voucherType->name, ['Sales', 'Sales Return']);
    $isPurchase = in_array($voucher->voucherType->name, ['Purchase', 'Purchase Return']);
    ```

2. **Account Grouping:**

    - Groups invoice items by their respective ledger accounts
    - Creates consolidated entries per unique account
    - Reduces number of voucher entries while maintaining accuracy

3. **Sales Invoice Entries:**

    ```
    Debit:  Customer Account (Accounts Receivable) - Total Amount
    Credit: Product's sales_account_id - Per product amount
    ```

4. **Purchase Invoice Entries:**

    ```
    Debit:  Product's purchase_account_id - Per product amount
    Credit: Supplier Account (Accounts Payable) - Total Amount
    ```

5. **Fallback Mechanism:**
    - If product doesn't have specific account assigned:
        - Sales: Falls back to "Sales Revenue" account
        - Purchase: Falls back to "Cost of Goods Sold" account
    - Throws exception if neither product account nor default exists

## Accounting Entry Examples

### Example 1: Sales Invoice with Multiple Products

**Products:**

-   Product A (Qty: 2, Rate: ₦1,000) → sales_account_id: "Sales Revenue" (ID: 15)
-   Product B (Qty: 1, Rate: ₦500) → sales_account_id: "Service Income" (ID: 16)

**Entries Created:**
| Account | Debit | Credit |
|---------|-------|--------|
| Customer (Aishat Lawal) | ₦2,500 | - |
| Sales Revenue | - | ₦2,000 |
| Service Income | - | ₦500 |

**Totals:** ₦2,500 = ₦2,500 ✓

### Example 2: Purchase Invoice

**Products:**

-   Product A (Qty: 5, Rate: ₦800) → purchase_account_id: "Cost of Goods Sold" (ID: 20)
-   Product B (Qty: 2, Rate: ₦300) → purchase_account_id: "Direct Expenses" (ID: 21)

**Entries Created:**
| Account | Debit | Credit |
|---------|-------|--------|
| Cost of Goods Sold | ₦4,000 | - |
| Direct Expenses | ₦600 | - |
| Supplier Account | - | ₦4,600 |

**Totals:** ₦4,600 = ₦4,600 ✓

## Database Structure

### Products Table (Relevant Fields)

```php
$table->foreignId('stock_asset_account_id')->nullable()->constrained('ledger_accounts')->onDelete('set null');
$table->foreignId('sales_account_id')->nullable()->constrained('ledger_accounts')->onDelete('set null');
$table->foreignId('purchase_account_id')->nullable()->constrained('ledger_accounts')->onDelete('set null');
```

### Default Account Setup (Per Tenant)

From `create()` method in ProductController:

-   Stock Asset: "Inventory"
-   Sales Account: "Sales Revenue"
-   Purchase Account: "Cost of Goods Sold"

## Benefits

1. **Accurate Revenue Tracking:**

    - Different product categories can use different revenue accounts
    - E.g., Goods → "Sales Revenue", Services → "Service Income"

2. **Proper Expense Categorization:**

    - Purchase costs go to appropriate expense accounts
    - COGS, Direct Expenses, etc. tracked separately

3. **Financial Reporting:**

    - Income statement shows revenue by category
    - Expense reports show costs by product type
    - Better insights into profitability per product line

4. **Flexibility:**

    - Each product can have unique accounting treatment
    - Supports multi-category businesses
    - Tenant-specific account structures respected

5. **Vendor Support:**
    - Purchase invoices now update vendor balances correctly
    - Accounts payable properly tracked

## Balance Updates

The system now updates:

1. **Party Account** (Customer or Supplier/Vendor)
2. **All Product Ledger Accounts** (sales or purchase accounts)
3. **Customer/Vendor Outstanding Balance** (if entity is linked to ledger account)

## Voucher Types Supported

### Sales-Type Vouchers:

-   Sales
-   Sales Return

### Purchase-Type Vouchers:

-   Purchase
-   Purchase Return

## Testing Recommendations

1. **Sales Invoice Test:**

    - Create invoice with products having different sales accounts
    - Verify correct entries: Customer debited, each sales account credited
    - Check customer outstanding balance updated

2. **Purchase Invoice Test:**

    - Create purchase with products having different purchase accounts
    - Verify correct entries: Each purchase account debited, supplier credited
    - Check vendor outstanding balance updated

3. **Mixed Accounts Test:**

    - Some products with specific accounts, some without
    - Verify fallback to default accounts works

4. **Account Balance Verification:**
    - Check all affected ledger accounts have correct balances
    - Verify trial balance still balances

## Migration Note

**No database migration required** - the product ledger account fields already exist from previous migrations:

-   `2023_01_01_000002_create_products_table.php`

The product creation form (`create.blade.php`) already has:

-   Smart filtering of accounts by type
-   Default account selection by name
-   Collapsible ledger accounts section

## Related Files Modified

1. `app/Http/Controllers/Tenant/Accounting/InvoiceController.php`
    - `createAccountingEntries()` method completely rewritten

## Future Enhancements

1. **COGS Tracking:**

    - When selling items, create additional entry:
        - Debit: COGS Account
        - Credit: Inventory Account
    - Tracks reduction in inventory value

2. **Inventory Valuation:**

    - Link stock_asset_account_id to inventory movements
    - Update inventory account balance when stock changes

3. **Multi-Tax Support:**
    - Separate ledger entries for tax components
    - Link to product tax rates

## Summary

The accounting system now properly respects product-level ledger account assignments, creating accurate journal entries for both sales and purchase transactions. This ensures:

-   ✅ Correct revenue recognition per product category
-   ✅ Proper expense categorization
-   ✅ Accurate accounts receivable/payable tracking
-   ✅ Better financial reporting and analysis
-   ✅ Support for both customer and supplier transactions
