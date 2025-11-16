# Bulk Payment Upload - Quick Reference Guide

## 🚀 Quick Start (5 Steps)

### 1. Navigate to Payment Voucher

Go to: **Accounting → Vouchers → Create Payment Voucher**

### 2. Click "Bulk Upload" Button

Look for the button next to "Add Entry" button

### 3. Download Template

-   Click green "Download Template" button in modal
-   Excel file will download with sample data

### 4. Fill Your Data

Open Excel and fill these columns:

-   **date**: Payment date (e.g., 15-11-2025)
-   **ledger**: Account name (e.g., Electricity Expense)
-   **description**: What the payment is for
-   **amount**: Payment amount (numbers only, no currency symbol)

### 5. Upload & Submit

-   Select your Bank/Cash account from dropdown
-   Choose your completed Excel file
-   Click "Upload & Create Voucher"
-   Done! Redirects to voucher page

---

## 📋 Excel Template Format

```
| date       | ledger              | description                    | amount |
|------------|---------------------|--------------------------------|--------|
| 15-11-2025 | Electricity Expense | November electricity bill      | 25000  |
| 15-11-2025 | Transportation      | Staff transport allowance      | 15000  |
| 16-11-2025 | Office Supplies     | Purchase of stationery         | 8500   |
```

---

## ✅ What Works

### Date Formats (All Supported)

-   ✅ 15-11-2025 (DD-MM-YYYY)
-   ✅ 15/11/2025 (DD/MM/YYYY)
-   ✅ 15-11-25 (DD-MM-YY)
-   ✅ 2025-11-15 (YYYY-MM-DD)
-   ✅ Excel date format (automatic conversion)

### Ledger Names (Smart Matching)

-   ✅ Exact match: "Electricity Expense"
-   ✅ With typos: "Electricity Expence" (85% similarity)
-   ✅ Partial match: "transport" → "Transportation"
-   ✅ Case insensitive: "ELECTRICITY EXPENSE" = "electricity expense"

### File Types

-   ✅ .xlsx (Excel 2007+)
-   ✅ .xls (Excel 97-2003)
-   ✅ .csv (Comma Separated Values)

---

## ❌ Common Errors & Fixes

### Error: "Date is required"

**Fix**: Make sure every row has a date in column A

### Error: "Ledger 'XYZ' not found"

**Fix**:

-   Check spelling (system tries to match similar names)
-   Use exact ledger name from your chart of accounts
-   Create the ledger account first if it doesn't exist

### Error: "Amount must be greater than 0"

**Fix**:

-   Enter positive numbers only
-   Remove currency symbols (₦, $)
-   Remove commas from numbers

### Error: "File size too large"

**Fix**: File must be under 10MB (usually 1000+ rows)

---

## 🎯 Best Practices

### Before Upload

1. ✅ Download template first (has your actual ledger accounts)
2. ✅ Save a backup copy of your Excel file
3. ✅ Verify date format is consistent
4. ✅ Double-check amounts (no typos)

### During Fill

1. ✅ Use copy-paste for repeated entries
2. ✅ Keep ledger names consistent
3. ✅ Add clear descriptions (helps with reconciliation)
4. ✅ Sort by date (optional, but organized)

### After Upload

1. ✅ Review the created voucher
2. ✅ Verify total amount matches your expectation
3. ✅ Check all entries are correct
4. ✅ Post the voucher when ready

---

## 🔒 How It Works (Behind the Scenes)

### Accounting Logic

```
Example: Upload 3 payments totaling ₦48,500

CREDIT Entry (Bank Account):
  Bank/Cash Account     Cr  ₦48,500

DEBIT Entries (From Excel):
  Electricity Expense   Dr  ₦25,000
  Transportation        Dr  ₦15,000
  Office Supplies       Dr  ₦8,500
                        ─────────────
  Total                     ₦48,500  ✅ Balanced
```

### Safety Features

-   ✅ **Atomic Transaction**: All entries save together or none (no partial vouchers)
-   ✅ **Validation**: Checks all rows before saving anything
-   ✅ **Audit Trail**: Stores original filename and bulk reference
-   ✅ **Draft Status**: Voucher created as draft (can review before posting)

---

## 📊 Example Use Cases

### Monthly Recurring Expenses

Upload 15-20 regular payments at once:

-   Electricity bills
-   Water bills
-   Internet/Phone
-   Office rent
-   Staff allowances
-   Transport costs
-   Stationery purchases

### Petty Cash Reimbursements

Upload all petty cash expenses from the month:

-   Small purchases
-   Minor repairs
-   Office supplies
-   Staff welfare

### Vendor Payments

Process multiple vendor invoices:

-   Supplier A - ₦50,000
-   Supplier B - ₦35,000
-   Supplier C - ₦28,500

---

## 🛠️ Troubleshooting

### Problem: Button doesn't show

**Check**: Are you on the Payment Voucher creation page?
**Solution**: Navigate to Accounting → Vouchers → Create → Payment

### Problem: Template downloads but is empty

**Check**: Do you have expense ledger accounts created?
**Solution**: Create some ledger accounts first (e.g., Electricity Expense)

### Problem: Upload successful but entries look wrong

**Check**: Did you select the correct bank account?
**Solution**: Delete the draft voucher and re-upload with correct bank

### Problem: Modal won't close

**Solution**: Click "Cancel" button or press ESC key

---

## 💡 Pro Tips

### Tip 1: Monthly Templates

Save your filled Excel file as "Monthly_Expenses_Template.xlsx" and reuse it each month by:

1. Updating dates
2. Adjusting amounts
3. Re-uploading

### Tip 2: Bulk Editing

Use Excel's power to:

-   AutoFill dates (drag down)
-   Apply formulas for amounts
-   Sort/filter before upload
-   Remove duplicates

### Tip 3: Verification

After upload, voucher shows:

-   Number of entries
-   Total amount
-   Bulk upload reference
-   Original filename

### Tip 4: Error Prevention

Common mistakes to avoid:

-   ❌ Empty rows in middle of data
-   ❌ Text in amount column
-   ❌ Wrong date format (use DD-MM-YYYY)
-   ❌ Non-existent ledger names

---

## 📞 Quick Help

### Where to Find Things

**Template Download**:
Bulk Upload Modal → Green "Download Template" button

**Bulk Reference**:
After upload, check voucher detail page → "Bulk Upload Reference" field

**Error Details**:
Modal shows all errors with row numbers (e.g., "Row 5: Amount required")

**Uploaded Filename**:
Stored in voucher for audit trail

### Success Indicators

-   ✅ Green success message
-   ✅ Automatic redirect to voucher page
-   ✅ All entries listed in voucher
-   ✅ Total amount matches

### Failure Indicators

-   ❌ Red error box in modal
-   ❌ List of validation errors
-   ❌ Modal stays open
-   ❌ No voucher created

---

## 🎓 Training Checklist

New user should practice:

-   [ ] Download template
-   [ ] Fill 3-5 sample entries
-   [ ] Upload with correct bank account
-   [ ] Review created voucher
-   [ ] Post the voucher
-   [ ] Try intentional error (e.g., wrong ledger name)
-   [ ] Fix error and re-upload
-   [ ] Upload 10+ entries successfully

**Time Required**: 15 minutes for first-time users

---

## 📈 Efficiency Gains

### Manual Entry vs Bulk Upload

**Manual Entry** (10 payments):

-   Time: 5-10 minutes
-   Steps: 50+ clicks
-   Error risk: High (repetitive data entry)

**Bulk Upload** (10 payments):

-   Time: 30-60 seconds
-   Steps: 5 clicks
-   Error risk: Low (validated before save)

**Time Saved**: 80-90% for recurring payments

---

## 🔗 Related Features

-   **Document Upload**: Each payment entry can have attached documents (receipts)
-   **Voucher Posting**: Review draft voucher before posting to ledger
-   **Ledger Statement**: View all posted entries in ledger account
-   **Voucher Duplicate**: Create similar voucher from existing one

---

**Quick Access**: Bookmark this page for easy reference!
**Last Updated**: November 16, 2025
