# Ledger Accounts Import Feature - Quick Reference

## Overview

Bulk import ledger accounts from Excel/CSV files with support for opening balances, parent accounts, and double-entry bookkeeping.

## Quick Start

### 1. Access the Feature

-   Navigate to: `/{tenant-slug}/accounting/ledger-accounts`
-   Click: **"Upload Ledger Accounts"** (Green button)

### 2. Download Template

-   In the modal, click **"Download Template File"**
-   Template file: `ledger_accounts_import_template.xlsx`

### 2.1. Download Account Groups Reference (IMPORTANT!)

-   Click **"Download Account Groups Reference"** to download `account_groups_reference.xlsx`
-   This file shows all available account groups in your system with:
    -   Account Group Name (use this exact name in your import)
    -   Group Code
    -   Nature (assets, liabilities, equity, income, expenses)
    -   Account Type (what to use in the account_type column)
    -   Description
-   **Use this reference to ensure you're using correct account group names**

### 3. Fill the Template

Required fields:

-   `code`: Unique account code (e.g., CASH-001, BANK-001)
-   `name`: Account name (e.g., "Petty Cash")
-   `account_type`: Must be one of: asset, liability, income, expense, equity
-   `account_group`: Name of existing account group
-   `balance_type`: dr (debit) or cr (credit)

### 4. Upload & Import

-   Select your filled template file
-   Click **"Import Accounts"**
-   Wait for confirmation message

## Template Columns (13 total)

| Column               | Required | Description                           | Example                 |
| -------------------- | -------- | ------------------------------------- | ----------------------- |
| code                 | Yes      | Unique account code                   | CASH-001                |
| name                 | Yes      | Account name                          | Petty Cash              |
| account_type         | Yes      | asset/liability/income/expense/equity | asset                   |
| account_group        | Yes      | Name of existing account group        | Cash & Bank             |
| parent_code          | No       | Parent account code (for hierarchy)   | CASH-MAIN               |
| balance_type         | Yes      | dr (debit) or cr (credit)             | dr                      |
| opening_balance      | No       | Opening balance amount                | 5000.00                 |
| description          | No       | Account description                   | Petty cash for expenses |
| address              | No       | Account address                       | Main Office             |
| phone                | No       | Contact phone                         | +234-801-234-5678       |
| email                | No       | Contact email                         | accounts@example.com    |
| is_active            | No       | yes or no (default: yes)              | yes                     |
| opening_balance_date | No       | Date (YYYY-MM-DD format)              | 2024-01-01              |

## Account Types

### Asset Accounts

-   **Balance Type**: dr (debit increases, credit decreases)
-   **Examples**: Cash, Bank, Accounts Receivable, Inventory
-   **Use for**: Things you own or money owed to you

### Liability Accounts

-   **Balance Type**: cr (credit increases, debit decreases)
-   **Examples**: Accounts Payable, Loans, Credit Cards
-   **Use for**: Money you owe to others

### Equity Accounts

-   **Balance Type**: cr (credit increases, debit decreases)
-   **Examples**: Capital, Retained Earnings, Drawings
-   **Use for**: Owner's investment and retained profits

### Income Accounts

-   **Balance Type**: cr (credit increases, debit decreases)
-   **Examples**: Sales Revenue, Service Income, Interest Income
-   **Use for**: Money earned from business activities

### Expense Accounts

-   **Balance Type**: dr (debit increases, credit decreases)
-   **Examples**: Rent, Salaries, Utilities, Office Supplies
-   **Use for**: Costs of running the business

## Opening Balance Logic

### Debit Opening Balance

-   **For Assets**: Positive balance (you have cash/inventory)
-   **For Expenses**: Opening expense amount
-   **Journal Entry**:
    -   Debit: Account
    -   Credit: Opening Balance Equity

### Credit Opening Balance

-   **For Liabilities**: Money you owe
-   **For Income**: Opening revenue amount
-   **For Equity**: Owner's investment
-   **Journal Entry**:
    -   Debit: Opening Balance Equity
    -   Credit: Account

## File Requirements

✅ **Supported Formats**: .xlsx, .xls, .csv
✅ **Maximum File Size**: 10MB
✅ **Required Columns**: All 13 columns must be present (can be empty)
✅ **Code Uniqueness**: Each account code must be unique within tenant

## What Happens During Import

For each row, the system:

1. **Validates** account code uniqueness
2. **Validates** account type
3. **Checks** account group exists
4. **Validates** parent account (if specified)
5. **Creates** ledger account record
6. **Creates** opening balance journal voucher (if amount > 0)
7. **Updates** account balances
8. **Logs** any errors with row numbers

## Success Scenarios

### All Successful

```
✅ "10 ledger account(s) imported successfully!"
```

### Partial Success

```
⚠️ "7 account(s) imported successfully, but 3 failed."
+ List of errors with row numbers
```

### All Failed

```
❌ "Import failed. No accounts were imported."
+ List of errors with row numbers
```

## Common Errors & Solutions

### ❌ "Account name is required"

-   **Cause**: Empty name field
-   **Solution**: Fill the name field

### ❌ "Account code is required"

-   **Cause**: Empty code field
-   **Solution**: Provide unique account code

### ❌ "A ledger account with code 'XXX' already exists"

-   **Cause**: Duplicate account code
-   **Solution**: Use unique code or update existing account

### ❌ "Invalid account type"

-   **Cause**: account_type not in allowed values
-   **Solution**: Use only: asset, liability, income, expense, equity (lowercase)

### ❌ "Account group 'XXX' not found"

-   **Cause**: Account group doesn't exist
-   **Solution**: Create the account group first or use existing group name

### ❌ "Parent account with code 'XXX' not found"

-   **Cause**: Specified parent doesn't exist
-   **Solution**: Create parent account first or leave parent_code empty

### ❌ "Invalid opening balance date"

-   **Cause**: Date format incorrect
-   **Solution**: Use YYYY-MM-DD format (e.g., 2024-01-01)

## Sample Data

### Example 1: Cash Account (Asset)

```
code: CASH-001
name: Petty Cash
account_type: asset
account_group: Cash & Bank
parent_code:
balance_type: dr
opening_balance: 5000.00
description: Petty cash for small expenses
address: Main Office
phone: +234-801-234-5678
email: accounts@example.com
is_active: yes
opening_balance_date: 2024-01-01
```

### Example 2: Bank Account (Asset)

```
code: BANK-001
name: First Bank - Current Account
account_type: asset
account_group: Cash & Bank
parent_code:
balance_type: dr
opening_balance: 100000.00
description: Main operating bank account
address:
phone:
email:
is_active: yes
opening_balance_date: 2024-01-01
```

### Example 3: Expense Account

```
code: EXP-001
name: Office Rent
account_type: expense
account_group: Operating Expenses
parent_code:
balance_type: dr
opening_balance: 0.00
description: Monthly office rent expense
address:
phone:
email:
is_active: yes
opening_balance_date:
```

## Technical Details

### Created Components

-   **Import Class**: `App\Imports\LedgerAccountsImport`
-   **Export Class**: `App\Exports\LedgerAccountsTemplateExport`
-   **Controller Methods**: `LedgerAccountController@downloadTemplate`, `LedgerAccountController@import`
-   **Routes**:
    -   GET: `{tenant}/accounting/ledger-accounts/export/template`
    -   POST: `{tenant}/accounting/ledger-accounts/import`

### Database Tables Affected

-   `ledger_accounts` - Account records
-   `account_groups` - Account group references
-   `vouchers` - Journal vouchers for opening balances
-   `voucher_entries` - Double-entry bookkeeping entries

### Accounting Integration

-   **Voucher Type**: Journal Voucher (JV)
-   **Opening Balance Account**: Opening Balance Equity
-   **Double-Entry**: All opening balances create proper journal entries
-   **Account Hierarchy**: Supports parent-child relationships

## Tips & Best Practices

1. **Create Account Groups First**: Ensure all account groups exist before importing
2. **Use Consistent Codes**: Follow a numbering scheme (e.g., 1000-1999 for Assets)
3. **Start with Parents**: Import parent accounts before child accounts
4. **Test Small Batch**: Test with 2-3 accounts first
5. **Backup Data**: Export existing accounts before large imports
6. **Check Balance Types**: Ensure correct dr/cr for each account type
7. **Verify Opening Balances**: Double-check amounts and dates
8. **Use Template**: Always start from downloaded template

## Account Code Naming Conventions

### Suggested Structure

```
1000-1999: Assets
  1000-1099: Cash & Bank
  1100-1199: Accounts Receivable
  1200-1299: Inventory
  1300-1399: Fixed Assets

2000-2999: Liabilities
  2000-2099: Accounts Payable
  2100-2199: Loans
  2200-2299: Credit Cards

3000-3999: Equity
  3000-3099: Capital
  3100-3199: Retained Earnings
  3200-3299: Drawings

4000-4999: Income
  4000-4099: Sales Revenue
  4100-4199: Service Income
  4200-4299: Other Income

5000-5999: Expenses
  5000-5099: Cost of Goods Sold
  5100-5199: Operating Expenses
  5200-5299: Administrative Expenses
```

## Testing Checklist

Before importing to production:

-   [ ] Download template successfully
-   [ ] Fill sample data (3-4 accounts of different types)
-   [ ] Import test file with 3-4 records
-   [ ] Verify accounts created correctly
-   [ ] Check account groups linked properly
-   [ ] Verify opening balance vouchers (if used)
-   [ ] Test with duplicate code (should fail)
-   [ ] Test with invalid account type (should fail)
-   [ ] Test with non-existent account group (should fail)
-   [ ] Review error messages clarity

## Support & Troubleshooting

If issues persist after following this guide:

1. Check `storage/logs/laravel.log` for detailed errors
2. Verify all required packages installed:
    ```bash
    composer show maatwebsite/excel
    ```
3. Clear application caches:
    ```bash
    php artisan optimize:clear
    php artisan config:clear
    ```
4. Verify account groups exist:
    - Navigate to Account Groups page
    - Ensure all groups referenced in import file exist
5. Check database connection and voucher types initialized

## Related Features

-   **Account Groups Management**: Create and manage account groups
-   **Chart of Accounts**: View complete account hierarchy
-   **Journal Vouchers**: Create manual journal entries
-   **Financial Reports**: Generate trial balance, income statement, balance sheet
-   **Opening Balance Management**: Adjust opening balances after initial setup

---

**Last Updated**: October 19, 2025
**Version**: 1.0.0
**Feature Status**: ✅ Production Ready
