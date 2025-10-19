# Vendor Import Feature - Quick Reference

## Overview

Bulk import vendors from Excel/CSV files with support for opening balances and double-entry bookkeeping.

## Quick Start

### 1. Access the Feature

-   Navigate to: `/{tenant-slug}/crm/vendors`
-   Click: **"Bulk Upload Vendors"** (Green button)

### 2. Download Template

-   In the modal, click **"Download Template File"**
-   Template file: `vendors_import_template.xlsx`

### 3. Fill the Template

Required fields based on vendor type:

**For Individual Vendors:**

-   `vendor_type`: "individual"
-   `first_name`: Required
-   `last_name`: Required
-   `email`: Required (must be unique)

**For Business Vendors:**

-   `vendor_type`: "business"
-   `company_name`: Required
-   `email`: Required (must be unique)

### 4. Upload & Import

-   Select your filled template file
-   Click **"Import Vendors"**
-   Wait for confirmation message

## Template Columns (25 total)

### Basic Information

| Column       | Required        | Description             | Example             |
| ------------ | --------------- | ----------------------- | ------------------- |
| vendor_type  | Yes             | individual or business  | individual          |
| first_name   | Conditional\*   | Individual's first name | John                |
| last_name    | Conditional\*   | Individual's last name  | Doe                 |
| company_name | Conditional\*\* | Business name           | ABC Supplies Ltd    |
| email        | Yes             | Unique email address    | vendor@example.com  |
| phone        | No              | Contact phone number    | +234-801-234-5678   |
| mobile       | No              | Mobile phone number     | +234-802-234-5678   |
| website      | No              | Website URL             | https://example.com |

\*Required if vendor_type = "individual"
\*\*Required if vendor_type = "business"

### Tax & Registration

| Column              | Required | Description                  | Example       |
| ------------------- | -------- | ---------------------------- | ------------- |
| tax_id              | No       | Tax identification number    | TIN-123456789 |
| registration_number | No       | Business registration number | RC-987654     |

### Address Information

| Column        | Required | Description                | Example         |
| ------------- | -------- | -------------------------- | --------------- |
| address_line1 | No       | Street address             | 123 Main Street |
| address_line2 | No       | Additional address         | Suite 100       |
| city          | No       | City name                  | Lagos           |
| state         | No       | State/Province             | Lagos State     |
| postal_code   | No       | ZIP/Postal code            | 100001          |
| country       | No       | Country (default: Nigeria) | Nigeria         |

### Financial Information

| Column              | Required | Description                     | Example          |
| ------------------- | -------- | ------------------------------- | ---------------- |
| currency            | No       | Currency code (default: NGN)    | NGN              |
| payment_terms       | No       | Payment terms (default: Net 30) | Net 30           |
| bank_name           | No       | Vendor's bank name              | First Bank       |
| bank_account_number | No       | Account number                  | 1234567890       |
| bank_account_name   | No       | Account holder name             | John Doe         |
| notes               | No       | Additional notes                | Preferred vendor |

### Opening Balance (Optional)

| Column                 | Required | Description              | Example    |
| ---------------------- | -------- | ------------------------ | ---------- |
| opening_balance_amount | No       | Opening balance amount   | 5000.00    |
| opening_balance_type   | No       | none, debit, or credit   | credit     |
| opening_balance_date   | No       | Date (YYYY-MM-DD format) | 2024-01-01 |

## Opening Balance Types

### Credit Balance (Default for vendors)

-   **Meaning**: You OWE money to the vendor
-   **Use when**: Vendor has delivered goods/services but hasn't been paid yet
-   **Journal Entry**:
    -   Debit: Opening Balance Equity
    -   Credit: Vendor Ledger Account

### Debit Balance

-   **Meaning**: Vendor OWES money to you
-   **Use when**: You've made advance payment to vendor
-   **Journal Entry**:
    -   Debit: Vendor Ledger Account
    -   Credit: Opening Balance Equity

### None

-   **Meaning**: No opening balance
-   **Use when**: Fresh vendor relationship or zero balance

## File Requirements

✅ **Supported Formats**: .xlsx, .xls, .csv
✅ **Maximum File Size**: 10MB
✅ **Required Columns**: All 25 columns must be present (can be empty)
✅ **Email Uniqueness**: Each email must be unique within tenant

## What Happens During Import

For each row, the system:

1. **Validates** vendor type and required fields
2. **Checks** email uniqueness
3. **Creates** vendor record
4. **Creates** ledger account (under Accounts Payable)
5. **Creates** opening balance journal voucher (if provided)
6. **Updates** ledger account balances
7. **Logs** any errors with row numbers

## Success Scenarios

### All Successful

```
✅ "10 vendor(s) imported successfully!"
```

### Partial Success

```
⚠️ "7 vendor(s) imported successfully, but 3 failed."
+ List of errors with row numbers
```

### All Failed

```
❌ "Import failed. No vendors were imported."
+ List of errors with row numbers
```

## Common Errors & Solutions

### ❌ "Invalid vendor type"

-   **Cause**: vendor_type is not "individual" or "business"
-   **Solution**: Use only "individual" or "business" (lowercase)

### ❌ "First name and last name are required for individual vendors"

-   **Cause**: Individual vendor missing name fields
-   **Solution**: Fill both first_name and last_name

### ❌ "Company name is required for business vendors"

-   **Cause**: Business vendor missing company_name
-   **Solution**: Fill company_name field

### ❌ "Email is required"

-   **Cause**: Empty email field
-   **Solution**: Provide valid email address

### ❌ "Invalid email format"

-   **Cause**: Email doesn't match standard format
-   **Solution**: Use format: user@domain.com

### ❌ "A vendor with email 'xxx' already exists"

-   **Cause**: Duplicate email in system
-   **Solution**: Use unique email or update existing vendor

### ❌ "Invalid opening balance date"

-   **Cause**: Date format incorrect
-   **Solution**: Use YYYY-MM-DD format (e.g., 2024-01-01)

## Sample Data

### Example 1: Individual Vendor with Credit Balance

```
vendor_type: individual
first_name: John
last_name: Doe
email: john.doe@example.com
phone: +234-801-234-5678
city: Lagos
country: Nigeria
currency: NGN
payment_terms: Net 30
opening_balance_amount: 1000.00
opening_balance_type: credit
opening_balance_date: 2024-01-01
```

### Example 2: Business Vendor with Debit Balance

```
vendor_type: business
company_name: ABC Supplies Ltd
email: contact@abcsupplies.com
phone: +234-803-345-6789
city: Abuja
country: Nigeria
currency: NGN
payment_terms: Net 45
bank_name: Access Bank
bank_account_number: 0987654321
opening_balance_amount: 5000.00
opening_balance_type: debit
opening_balance_date: 2024-01-01
```

## Technical Details

### Created Components

-   **Import Class**: `App\Imports\VendorsImport`
-   **Export Class**: `App\Exports\VendorsTemplateExport`
-   **Controller Methods**: `VendorController@exportTemplate`, `VendorController@import`
-   **Routes**:
    -   GET: `{tenant}/crm/vendors/export/template`
    -   POST: `{tenant}/crm/vendors/import`

### Database Tables Affected

-   `vendors` - Vendor records
-   `ledger_accounts` - Vendor ledger accounts
-   `vouchers` - Journal vouchers for opening balances
-   `voucher_entries` - Double-entry bookkeeping entries

### Accounting Integration

-   **Account Group**: Accounts Payable (AP)
-   **Voucher Type**: Journal Voucher (JV)
-   **Opening Balance Account**: Opening Balance Equity
-   **Nature**: Liability (Credit increases, Debit decreases)

## Tips & Best Practices

1. **Start Small**: Test with 2-3 vendors first
2. **Backup Data**: Export existing vendors before large imports
3. **Check Duplicates**: Ensure no duplicate emails
4. **Verify Balances**: Double-check opening balance types
5. **Use Template**: Always start from downloaded template
6. **Review Errors**: Address all errors before re-importing
7. **Test Dates**: Use proper YYYY-MM-DD format
8. **Currency Consistency**: Use same currency for all vendors

## Testing Checklist

Before importing to production:

-   [ ] Download template successfully
-   [ ] Fill sample data (individual + business)
-   [ ] Import test file with 2-3 records
-   [ ] Verify vendors created correctly
-   [ ] Check ledger accounts created
-   [ ] Verify opening balance vouchers (if used)
-   [ ] Test with duplicate email (should fail)
-   [ ] Test with invalid vendor type (should fail)
-   [ ] Test with missing required fields (should fail)
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
4. Ensure file permissions correct on storage directory
5. Verify database connection and voucher types initialized

## Related Features

-   **Customer Import**: Similar feature for customers
-   **Vendor Management**: Full CRUD operations
-   **Purchase Orders**: Create POs for vendors
-   **Payment Vouchers**: Record vendor payments
-   **Ledger Reports**: View vendor account statements

---

**Last Updated**: October 19, 2025
**Version**: 1.0.0
**Feature Status**: ✅ Production Ready
