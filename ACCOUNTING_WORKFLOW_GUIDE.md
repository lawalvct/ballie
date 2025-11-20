# Accounting Workflow Guide - Real-Life Scenario

## Understanding Your Scenario

### Your Test Entries

1. **Owner Capital Entry (Journal Voucher)**

    - Owner Capital Account (CR): ₦1,000,000
    - Primary Bank Account (DR): ₦1,000,000
    - ✅ **CORRECT** - This is equity injection

2. **Electricity Payment (Payment Voucher)**

    - Electricity Expense (DR): ₦200,000
    - Primary Bank Account (CR): ₦200,000
    - ✅ **CORRECT** - This is an expense

3. **Purchase Entry (Purchase Voucher)**
    - 5pcs HP Laptop @ ₦500,000 each = ₦2,500,000
    - ❌ **INCORRECT WORKFLOW** - See below for correct method

---

## The Problem You Identified

### What You Saw in P&L:

```
Total Income:     ₦0.00
Total Expenses:   ₦2,700,000.00  (₦200,000 + ₦2,500,000)
Net Loss:         ₦2,700,000.00
```

### The Issue:

The system was incorrectly showing:

-   Opening Stock as EXPENSE
-   Closing Stock as INCOME
-   Your ₦2,500,000 purchase was appearing in P&L

### Why This Is Wrong:

**Inventory purchases are ASSETS, not expenses!**

-   When you buy inventory, it goes to your **Balance Sheet** (Inventory account)
-   It only becomes an expense (COGS) when you **SELL** it
-   Until sold, it's just sitting in your warehouse as an asset

---

## How Inventory Accounting Works

### The Golden Rule:

```
Purchase → Balance Sheet (Inventory Asset)
   ↓
Sale → Profit & Loss (COGS Expense)
```

### Real-Life Example:

#### Day 1: You Buy Laptops

```
You pay ₦2,500,000 for 5 laptops
Effect on your business:
- Cash decreases by ₦2,500,000
- Inventory increases by ₦2,500,000
- Net Worth: UNCHANGED (just swapped cash for inventory)
- P&L: NO EFFECT (no profit/loss yet)
```

**Journal Entry:**

```
DR Inventory Account        ₦2,500,000
    CR Vendor/Bank Account           ₦2,500,000
```

#### Day 15: You Sell 2 Laptops for ₦700,000 each

```
Sale: 2 laptops @ ₦700,000 = ₦1,400,000
Cost: 2 laptops @ ₦500,000 = ₦1,000,000
Profit: ₦400,000
```

**Journal Entries (Two-part):**

**Part A: Record the Sale**

```
DR Customer Account         ₦1,400,000
    CR Sales Revenue                 ₦1,400,000
(This increases your income)
```

**Part B: Record the Cost**

```
DR Cost of Goods Sold (COGS)  ₦1,000,000
    CR Inventory                     ₦1,000,000
(This transfers cost from asset to expense)
```

**Result:**

-   Revenue: +₦1,400,000
-   COGS: -₦1,000,000
-   **Gross Profit: ₦400,000** ✅
-   Inventory remaining: ₦1,500,000 (3 laptops)

---

## Correct Accounting Workflow for Your Scenario

### Step 1: Owner Capital Injection

**Journal Voucher (JV)**

| Account               | Debit      | Credit     |
| --------------------- | ---------- | ---------- |
| Primary Bank Account  | ₦1,000,000 | -          |
| Owner Capital Account | -          | ₦1,000,000 |

**Effect:**

-   Assets (Bank): +₦1,000,000
-   Equity (Capital): +₦1,000,000
-   P&L: No effect

---

### Step 2: Purchase Inventory

**Purchase Invoice/Voucher**

| Account                     | Debit      | Credit     |
| --------------------------- | ---------- | ---------- |
| Inventory Account           | ₦2,500,000 | -          |
| Flex Plast Limited (Vendor) | -          | ₦2,500,000 |

**Effect:**

-   Assets (Inventory): +₦2,500,000
-   Liabilities (Accounts Payable): +₦2,500,000
-   P&L: **NO EFFECT** ✅

**Payment to Vendor (if paid immediately):**

| Account                     | Debit      | Credit     |
| --------------------------- | ---------- | ---------- |
| Flex Plast Limited (Vendor) | ₦2,500,000 | -          |
| Primary Bank Account        | -          | ₦2,500,000 |

**Effect:**

-   Assets (Bank): -₦2,500,000
-   Liabilities (Accounts Payable): -₦2,500,000
-   P&L: Still **NO EFFECT** ✅

---

### Step 3: Pay Electricity Bill

**Payment Voucher (PV)**

| Account              | Debit    | Credit   |
| -------------------- | -------- | -------- |
| Electricity Expense  | ₦200,000 | -        |
| Primary Bank Account | -        | ₦200,000 |

**Effect:**

-   Assets (Bank): -₦200,000
-   Expenses: +₦200,000
-   P&L: -₦200,000 (loss)

---

### Step 4: Sell Inventory (When you make a sale)

**Sales Invoice**

**Example: Sell 1 laptop for ₦700,000**

**Part 1: Record Revenue**

| Account          | Debit    | Credit   |
| ---------------- | -------- | -------- |
| Customer Account | ₦700,000 | -        |
| Sales Revenue    | -        | ₦700,000 |

**Part 2: Record Cost (COGS)**

| Account                   | Debit    | Credit   |
| ------------------------- | -------- | -------- |
| Cost of Goods Sold (COGS) | ₦500,000 | -        |
| Inventory                 | -        | ₦500,000 |

**Effect:**

-   Revenue: +₦700,000
-   COGS: +₦500,000
-   Gross Profit: ₦200,000
-   Inventory reduced by ₦500,000

---

## Your Correct Profit & Loss (Current State)

### After Your 3 Entries (No Sales Yet):

```
PROFIT & LOSS STATEMENT
Period: Nov 01, 2025 to Nov 20, 2025

INCOME:
  (No sales recorded)
  Total Income:                         ₦0.00

EXPENSES:
  Electricity Expense                   ₦200,000.00
  Total Expenses:                       ₦200,000.00

NET LOSS:                               ₦200,000.00
```

### Your Balance Sheet (What You Own):

```
BALANCE SHEET
As of Nov 20, 2025

ASSETS:
  Primary Bank Account                  ₦300,000.00
    (₦1,000,000 - ₦200,000 - ₦2,500,000 + ₦2,000,000)
  Inventory (5 laptops)                 ₦2,500,000.00
  Total Assets:                         ₦2,800,000.00

LIABILITIES:
  Flex Plast Limited (Vendor)           ₦2,500,000.00
  Total Liabilities:                    ₦2,500,000.00

EQUITY:
  Owner Capital                         ₦1,000,000.00
  Retained Earnings (Loss)              (₦200,000.00)
  Total Equity:                         ₦800,000.00

Total Liabilities + Equity:             ₦3,300,000.00
```

**Note:** If you paid the vendor immediately, adjust accordingly.

---

## Key Accounting Principles

### 1. **Inventory is an Asset**

-   Appears in Balance Sheet
-   Does NOT appear in Profit & Loss until sold
-   Example: ₦2,500,000 laptops sitting in warehouse = Asset

### 2. **COGS is an Expense**

-   Only appears in Profit & Loss when goods are SOLD
-   Formula: `COGS = Beginning Inventory + Purchases - Ending Inventory`
-   Or directly: Cost of items sold

### 3. **Profit & Loss Shows:**

-   **Income:** Sales, Service Revenue, Interest Income
-   **Expenses:** COGS (only when sold), Salaries, Rent, Utilities
-   **NOT:** Inventory purchases (those are assets)

### 4. **Balance Sheet Shows:**

-   **Assets:** Cash, Bank, Inventory, Equipment
-   **Liabilities:** Vendors, Loans, Accounts Payable
-   **Equity:** Owner Capital, Retained Earnings

---

## How to Record Purchase in Your System

### Method 1: Using Purchase Invoice/Voucher

1. **Navigate to:** Accounting → Invoices → Create Invoice
2. **Select Voucher Type:** Purchase (PUR)
3. **Select Vendor:** Flex Plast Limited
4. **Add Products:**
    - Product: HP Laptop
    - Quantity: 5
    - Rate: ₦500,000
    - Amount: ₦2,500,000
5. **Save and Post**

**System will automatically create:**

```
DR Inventory Account (or Product Purchase Account)  ₦2,500,000
    CR Vendor Account                                         ₦2,500,000
```

### Method 2: Using Journal Voucher (Manual)

1. **Navigate to:** Accounting → Vouchers → Create Voucher
2. **Select Type:** Journal Voucher (JV)
3. **Add Entries:**
    - **Entry 1:**
        - Account: Inventory
        - Debit: ₦2,500,000
    - **Entry 2:**
        - Account: Flex Plast Limited (Vendor)
        - Credit: ₦2,500,000
4. **Save and Post**

---

## Product Configuration (IMPORTANT!)

### Each Product Must Have Ledger Accounts:

1. **Sales Account:**

    - Example: "Sales Revenue" (Income account)
    - Used when product is SOLD

2. **Purchase Account:**

    - Example: "Inventory" or "Stock" (Asset account)
    - Used when product is PURCHASED
    - ⚠️ **NOT** "Purchases Expense" or "Cost of Goods Sold"

3. **COGS Account:**
    - Example: "Cost of Goods Sold" (Expense account)
    - Automatically debited when product is sold
    - Inventory account is credited simultaneously

### To Configure:

1. Go to **Inventory → Products → [Your Product]**
2. Click **Edit**
3. Set:
    - **Sales Account:** Sales Revenue
    - **Purchase Account:** Inventory (Stock)
    - **COGS tracking:** Enabled (if your system supports it)

---

## What Happens When You Sell

### Example: Sell 1 Laptop for ₦700,000

**Your P&L Will Show:**

```
INCOME:
  Sales Revenue                         ₦700,000.00
  Total Income:                         ₦700,000.00

EXPENSES:
  Cost of Goods Sold                    ₦500,000.00
  Electricity Expense                   ₦200,000.00
  Total Expenses:                       ₦700,000.00

NET PROFIT:                             ₦0.00
```

**Your Balance Sheet Will Show:**

```
ASSETS:
  Primary Bank Account                  ₦1,000,000.00
  Inventory (4 laptops)                 ₦2,000,000.00
  Total Assets:                         ₦3,000,000.00

LIABILITIES:
  Total Liabilities:                    ₦0.00

EQUITY:
  Owner Capital                         ₦1,000,000.00
  Retained Earnings                     ₦0.00
  Total Equity:                         ₦3,000,000.00
```

---

## Common Mistakes to Avoid

### ❌ WRONG: Recording Purchase as Expense

```
DR Purchases Expense    ₦2,500,000
    CR Bank                     ₦2,500,000
```

**Problem:** This puts purchases in P&L immediately, showing huge loss

### ✅ CORRECT: Recording Purchase as Asset

```
DR Inventory            ₦2,500,000
    CR Vendor/Bank              ₦2,500,000
```

**Benefit:** Inventory stays on Balance Sheet until sold

### ❌ WRONG: Not recording COGS on sale

```
(Only recording revenue, no cost entry)
DR Customer             ₦700,000
    CR Sales Revenue            ₦700,000
```

**Problem:** Shows 100% profit margin, inventory never reduces

### ✅ CORRECT: Recording Both Revenue and COGS

```
Entry 1 (Revenue):
DR Customer             ₦700,000
    CR Sales Revenue            ₦700,000

Entry 2 (Cost):
DR COGS                 ₦500,000
    CR Inventory                ₦500,000
```

**Benefit:** Accurate profit calculation, inventory tracking

---

## Quick Reference: Account Types

| Account Type  | Normal Balance | Increases With | Examples                            |
| ------------- | -------------- | -------------- | ----------------------------------- |
| **Asset**     | Debit          | Debit          | Cash, Bank, Inventory, Equipment    |
| **Liability** | Credit         | Credit         | Vendor A/P, Loans, Salaries Payable |
| **Equity**    | Credit         | Credit         | Owner Capital, Retained Earnings    |
| **Income**    | Credit         | Credit         | Sales Revenue, Service Income       |
| **Expense**   | Debit          | Debit          | COGS, Rent, Salaries, Utilities     |

---

## System Features Supporting Inventory

### 1. **Automatic COGS Calculation**

-   When you create a Sales Invoice and post it
-   System automatically creates COGS entry
-   Based on product's purchase rate

### 2. **Stock Movement Tracking**

-   Every purchase increases stock
-   Every sale decreases stock
-   View: **Inventory → Stock Movements**

### 3. **Product Valuation**

-   Opening Stock Value
-   Current Stock Value
-   Average Cost Method
-   View: **Inventory → Products → Valuation**

### 4. **Purchase Voucher Type**

-   Code: **PUR**
-   Affects Inventory: **Yes**
-   Inventory Effect: **Increase**
-   Accounting: DR Inventory, CR Vendor

### 5. **Sales Voucher Type**

-   Code: **SV** or **SALES**
-   Affects Inventory: **Yes**
-   Inventory Effect: **Decrease**
-   Accounting:
    -   DR Customer, CR Sales Revenue
    -   DR COGS, CR Inventory (automatic)

---

## Testing Your Fix

### After Applying the Fix:

1. **Navigate to:** Reports → Profit & Loss
2. **You should see:**

    ```
    Total Income:     ₦0.00
    Total Expenses:   ₦200,000.00  (Only electricity)
    Net Loss:         ₦200,000.00
    ```

3. **Navigate to:** Reports → Balance Sheet
4. **You should see:**

    ```
    Assets:
      - Bank: ₦300,000 (if paid vendor)
      - Inventory: ₦2,500,000

    Liabilities:
      - Vendor: ₦2,500,000 (if not paid)

    Equity:
      - Capital: ₦1,000,000
      - Retained Earnings: -₦200,000
    ```

5. **Stock Summary Section:**
    - Should show for reference only
    - NOT included in P&L calculation

---

## Summary

### The Fix Applied:

✅ Removed Opening Stock from Expenses
✅ Removed Closing Stock from Income
✅ P&L now only shows actual Income and Expenses
✅ Inventory stays on Balance Sheet until sold

### Your Correct Workflow:

1. **Buy Inventory** → Debit Inventory (Asset)
2. **Pay Expenses** → Debit Expense Account
3. **Sell Goods** → Credit Sales Revenue + Debit COGS
4. **P&L shows** → Only Revenue and Expenses
5. **Balance Sheet shows** → Assets (Inventory, Cash) and Liabilities

### Key Takeaway:

**"Purchase ≠ Expense until Sold"**

Inventory is like money in a different form. You haven't lost anything by buying inventory—you've just converted cash to goods. The expense (COGS) only happens when you convert those goods to sales.

---

## Need Help?

### Check Your Accounts:

-   **Inventory Account:** Type = Asset
-   **COGS Account:** Type = Expense
-   **Sales Revenue:** Type = Income
-   **Vendor Accounts:** Type = Liability

### Check Voucher Types:

-   **Purchase Voucher:** affects_inventory = true, inventory_effect = increase
-   **Sales Voucher:** affects_inventory = true, inventory_effect = decrease

### Reports to Monitor:

1. **Profit & Loss** - Shows operational performance
2. **Balance Sheet** - Shows financial position
3. **Trial Balance** - Ensures books are balanced
4. **Stock Valuation** - Shows inventory worth

---

**Document Version:** 1.0
**Last Updated:** November 20, 2025
**Status:** ✅ Profit & Loss Fixed - Inventory Accounting Corrected
