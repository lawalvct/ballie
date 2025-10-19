# Customer Import - Quick Reference Guide

## 📥 How to Import Customers

### Step 1: Access Import Feature

1. Navigate to **Customers** page
2. Click the **"Bulk Upload Customers"** button (green button at top)
3. Import modal will open

### Step 2: Download Template

1. Click **"Download Template File"** button in the modal
2. File will download as: `customers_import_template_YYYY-MM-DD.xlsx`
3. Open the file in Excel or Google Sheets

### Step 3: Fill Template with Data

#### Required Columns:

-   **email** - Must be unique (e.g., customer@example.com)
-   **customer_type** - Either `individual` or `business`

#### For Individual Customers:

-   **first_name** - Customer's first name
-   **last_name** - Customer's last name

#### For Business Customers:

-   **company_name** - Business name

#### Optional Columns:

| Column        | Description        | Example           |
| ------------- | ------------------ | ----------------- |
| phone         | Contact phone      | +234-800-123-4567 |
| mobile        | Mobile number      | +234-800-123-4567 |
| address_line1 | Street address     | 123 Main Street   |
| address_line2 | Additional address | Suite 100         |
| city          | City name          | Lagos             |
| state         | State/Province     | Lagos State       |
| postal_code   | Zip/Postal code    | 100001            |
| country       | Country name       | Nigeria           |
| currency      | Currency code      | NGN               |
| payment_terms | Payment terms      | Net 30            |
| tax_id        | Tax ID number      | 12345678          |
| notes         | Additional notes   | VIP customer      |

#### Opening Balance Columns (Optional):

| Column                 | Description  | Valid Values                 |
| ---------------------- | ------------ | ---------------------------- |
| opening_balance_amount | Amount       | 5000.00                      |
| opening_balance_type   | Balance type | `none`, `debit`, or `credit` |
| opening_balance_date   | Date         | 2024-01-01 (YYYY-MM-DD)      |

### Step 4: Upload File

1. Click **"Upload a file"** or drag file to upload area
2. Select your filled Excel/CSV file
3. Verify filename appears below upload area
4. Click **"Import Customers"** button

### Step 5: Review Results

-   ✅ **Success**: "X customers imported successfully!"
-   ⚠️ **Warning**: "X succeeded, Y failed" + detailed error list
-   ❌ **Error**: Validation errors with row numbers

## 📋 Template Format

### Sample Individual Customer Row:

```
customer_type: individual
first_name: John
last_name: Doe
company_name: (leave empty)
email: john.doe@example.com
phone: +234-800-123-4567
mobile: +234-800-123-4567
address_line1: 123 Main Street
city: Lagos
state: Lagos State
country: Nigeria
currency: NGN
opening_balance_amount: 5000.00
opening_balance_type: debit
opening_balance_date: 2024-01-01
```

### Sample Business Customer Row:

```
customer_type: business
first_name: (leave empty)
last_name: (leave empty)
company_name: XYZ Trading Company
email: contact@xyztrading.com
phone: +234-800-999-8888
city: Abuja
state: FCT
country: Nigeria
currency: NGN
opening_balance_amount: 10000.00
opening_balance_type: credit
opening_balance_date: 2024-01-01
```

## ⚠️ Common Errors and Solutions

### Error: "Email is required"

**Solution**: Fill in the email column for every customer

### Error: "Customer with email X already exists"

**Solution**: Check for duplicate emails in your file or database

### Error: "First name and last name are required for individual customers"

**Solution**: For `customer_type: individual`, both first_name and last_name must be filled

### Error: "Company name is required for business customers"

**Solution**: For `customer_type: business`, company_name must be filled

### Error: "Invalid customer_type"

**Solution**: customer_type must be exactly `individual` or `business` (lowercase)

### Error: "The file is not a valid Excel or CSV file"

**Solution**:

-   Ensure file is saved as .xlsx, .xls, or .csv
-   Don't change the column headers
-   Check file isn't corrupted

### Error: "The file may not be greater than 10240 kilobytes"

**Solution**: File size limit is 10MB. Split into multiple files if needed

## 💡 Pro Tips

### 1. Opening Balance Types Explained

-   **none** - No opening balance (new customer)
-   **debit** - Customer owes you money (Accounts Receivable)
-   **credit** - You owe customer money (Customer Credit)

### 2. Customer Type Selection

-   **individual** - For personal customers (requires first_name + last_name)
-   **business** - For company customers (requires company_name)

### 3. Data Validation

-   Emails must be unique across all customers in your tenant
-   Phone numbers can be in any format
-   Dates must be in YYYY-MM-DD format (e.g., 2024-01-15)
-   Currency codes should be 3 letters (NGN, USD, EUR, GBP)

### 4. Best Practices

-   Always download fresh template before importing
-   Keep a backup of your customer data
-   Test with 2-3 customers first before bulk import
-   Review error messages carefully for failed rows
-   Don't include duplicate emails in the same file

### 5. Performance

-   Small imports (< 100 customers): Very fast (< 5 seconds)
-   Medium imports (100-1000 customers): 10-60 seconds
-   Large imports (1000+ customers): Consider splitting into batches

## 🔍 Verification After Import

### Check Customer List

1. Go to Customers page
2. Verify count increased by number of successful imports
3. Search for specific customers to confirm they were added

### Check Customer Details

1. Click on imported customer
2. Verify all information is correct
3. Check contact details, address, etc.

### Check Opening Balance (if applicable)

1. Open customer details
2. Check "Ledger Account" section
3. Verify opening balance amount and type
4. Go to vouchers tab
5. Should see Journal Voucher for opening balance

## 📞 Support

If you encounter issues:

1. Check the error message displayed
2. Review this guide for common solutions
3. Check Laravel logs at `storage/logs/laravel.log`
4. Ensure all required system accounts exist (Journal Voucher type, Opening Balance Equity account)

## 🎯 Quick Checklist Before Import

-   [ ] Downloaded latest template
-   [ ] Filled all required fields (email, customer_type, first_name/last_name or company_name)
-   [ ] Checked for duplicate emails
-   [ ] customer_type is either "individual" or "business"
-   [ ] Opening balance dates in YYYY-MM-DD format
-   [ ] Opening balance types are "none", "debit", or "credit"
-   [ ] File saved as .xlsx, .xls, or .csv
-   [ ] File size under 10MB
-   [ ] Reviewed first few rows for accuracy
-   [ ] Have backup of data

Ready to import! 🚀
