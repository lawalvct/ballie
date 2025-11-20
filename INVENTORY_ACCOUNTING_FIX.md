# Inventory Accounting Fix - Standard Logic Implementation

## Problem Identified

**Before Fix:**

-   When you purchase ₦300,000 worth of inventory → P&L shows ₦300,000 expense immediately
-   Net Profit drops from ₦200,000 to **₦100,000 LOSS**
-   This is WRONG accounting

**Why It Was Wrong:**

-   Purchases were using `purchase_account_id` which might have been an expense account
-   Inventory purchases should NOT affect P&L until sold

---

## Solution Implemented (Tally/Zoho/QuickBooks Standard)

### Changes Made:

#### 1. **InvoiceController.php** - Line ~1139

```php
// OLD (WRONG):
elseif ($isPurchase) {
    $accountId = $product->purchase_account_id; // Could be expense account
}

// NEW (CORRECT):
elseif ($isPurchase) {
    // Use stock_asset_account_id (Inventory account - Asset)
    $accountId = $product->stock_asset_account_id;
}
```

#### 2. **InvoiceController.php** - Line ~1271-1320

Rewrote purchase accounting entries to follow standard logic:

```php
// PURCHASE INVOICE (Proper Accounting):
// Debit: Inventory/Stock Asset Account (Balance Sheet - Asset)
// Credit: Accounts Payable (Balance Sheet - Liability)
// → NO P&L IMPACT
```

---

## How It Works Now

### Product Table Has 3 Ledger Account Fields:

| Field                    | Purpose               | Account Type | Used When    |
| ------------------------ | --------------------- | ------------ | ------------ |
| `stock_asset_account_id` | **Inventory**         | Asset        | PURCHASE     |
| `sales_account_id`       | Sales Revenue         | Income       | SALES        |
| `purchase_account_id`    | _(Optional tracking)_ | -            | _(Reserved)_ |

---

## Standard Accounting Flow

### Scenario: You Buy 5 Laptops for ₦300,000

**BEFORE (Wrong):**

```
Purchase Entry:
DR: Purchase Expense ₦300,000  ← This goes to P&L immediately
CR: Accounts Payable ₦300,000

P&L Impact: Expense ₦300,000 → Loss!
```

**AFTER (Correct - Tally/Zoho/QB Logic):**

```
Purchase Entry:
DR: Inventory (Asset)     ₦300,000  ← Goes to Balance Sheet
CR: Accounts Payable      ₦300,000  ← Goes to Balance Sheet

P&L Impact: NONE! Your profit stays at ₦200,000
Balance Sheet: Assets increased ₦300,000, Liabilities increased ₦300,000
```

---

### When You SELL the Laptops for ₦600,000 (Cost ₦500,000)

**Sales Entry:**

```
1. Record Revenue:
   DR: Accounts Receivable (Asset)  ₦600,000
   CR: Sales Revenue (Income)        ₦600,000

2. Record Cost (COGS):
   DR: Cost of Goods Sold (Expense)  ₦500,000
   CR: Inventory (Asset)             ₦500,000
```

**P&L Impact:**

-   Income: ₦600,000
-   Expenses (COGS): ₦500,000
-   **Net Profit: ₦100,000** ✓

**Balance Sheet Impact:**

-   Inventory decreased ₦500,000
-   Accounts Receivable increased ₦600,000

---

## Complete Example Timeline

### Initial State:

-   **Net Profit:** ₦200,000
-   **Inventory:** ₦0

### After Purchasing ₦300,000 Inventory:

-   **Net Profit:** ₦200,000 (NO CHANGE!) ✓
-   **Inventory (Asset):** ₦300,000
-   **Accounts Payable (Liability):** ₦300,000

### After Selling for ₦600,000 (Cost ₦500,000):

-   **Net Profit:** ₦200,000 + ₦100,000 = **₦300,000** ✓
    -   Revenue: ₦600,000
    -   COGS: ₦500,000
    -   Gross Profit: ₦100,000
-   **Inventory (Asset):** ₦300,000 - ₦500,000 = -₦200,000 (need more stock)
-   **Accounts Receivable:** ₦600,000

---

## Key Points

### ✅ What's Correct Now:

1. **Purchases → Balance Sheet (Asset)**

    - Inventory account increases
    - No P&L impact until sold

2. **Sales → P&L (Income)**

    - Revenue recorded in Sales Revenue account
    - COGS recorded automatically (expense)
    - Inventory reduced

3. **Product Setup:**
    - All products assigned to Inventory (stock_asset_account_id)
    - All products assigned to Sales Revenue (sales_account_id)

### 🎯 Matches Industry Standards:

-   ✅ **Tally** - Inventory goes to Stock account (Asset)
-   ✅ **Zoho Books** - Purchase increases Inventory Asset
-   ✅ **QuickBooks** - Items tracked in Inventory Asset account
-   ✅ **SAP/Oracle** - Inventory capitalized on Balance Sheet

---

## Testing Your Fix

### Test 1: Purchase Only

1. Create purchase invoice for ₦100,000
2. Check P&L → Should show **NO CHANGE** in profit
3. Check Balance Sheet → Inventory increased ₦100,000

### Test 2: Purchase + Sale

1. Purchase: ₦100,000 (cost)
2. Sale: ₦150,000 (revenue)
3. Check P&L → Should show:
    - Income: ₦150,000
    - COGS: ₦100,000
    - **Net Profit: ₦50,000**

### Test 3: Multiple Purchases, Single Sale

1. Purchase #1: ₦50,000
2. Purchase #2: ₦80,000
3. Total Inventory: ₦130,000
4. P&L: **No change**
5. Sell for ₦200,000 (cost ₦130,000)
6. P&L Profit: ₦200,000 - ₦130,000 = **₦70,000** ✓

---

## What to Check in Your Products

Run this query to verify your products are configured:

```sql
SELECT
    name,
    sku,
    stock_asset_account_id,
    sales_account_id,
    purchase_account_id
FROM products
WHERE tenant_id = YOUR_TENANT_ID;
```

**Required:**

-   `stock_asset_account_id` → Must point to "Inventory" ledger account
-   `sales_account_id` → Must point to "Sales Revenue" ledger account

**Optional:**

-   `purchase_account_id` → Can be NULL or used for purchase-specific tracking

---

## Summary

| Action            | Old Behavior          | New Behavior (Correct)  |
| ----------------- | --------------------- | ----------------------- |
| Purchase ₦300,000 | P&L Loss ₦300,000 ❌  | P&L No Change ✅        |
| Inventory Value   | Not tracked properly  | ₦300,000 Asset ✅       |
| When Sold         | No COGS tracking ❌   | COGS = Purchase Cost ✅ |
| Net Profit Calc   | Revenue - Purchase ❌ | Revenue - COGS ✅       |

**Your accounting now follows international standards!** 🎉
