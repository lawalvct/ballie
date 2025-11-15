# Overtime Payment Flow - Complete Guide

**Date:** November 15, 2025
**Status:** ✅ Complete & Integrated with Accounting

---

## 📋 COMPLETE OVERTIME WORKFLOW

### 1. **Create Overtime Request**

**Location:** `/tenant/{tenant}/payroll/overtime/create`

**Options:**

-   **Hourly Calculation**: Enter start time, end time, overtime type (weekday/weekend/holiday/emergency)

    -   System calculates hours automatically
    -   Applies multiplier (1.5x weekday, 2x weekend, 2.5x holiday)
    -   Calculates total amount based on hourly rate

-   **Fixed Amount**: Enter fixed payment amount directly
    -   No time tracking required
    -   Direct amount payment

**Fields:**

-   Employee
-   Date
-   Calculation Method (hourly/fixed)
-   Reason & Work Description

---

### 2. **Approval Process**

**Location:** `/tenant/{tenant}/payroll/overtime/{id}` (Show page)

**Status: Pending**

-   Manager/Admin can **Approve** or **Reject**
-   Approval requires optional remarks
-   Rejection requires mandatory reason

**After Approval:**

-   Status changes to "Approved"
-   Shows "Payment Pending" label
-   **"Mark as Paid"** button appears

---

### 3. **Payment Processing** ✨ NEW

**Location:** Same show page (approved status)

**Click "Mark as Paid" button** → Opens Payment Modal

#### Payment Options:

**A. Simple Payment Marking** (No Accounting Entry)

-   Just mark as paid with date
-   No voucher created
-   Use when payment is handled externally

**B. Payment with Accounting Voucher** (Recommended) ✅

-   ✅ Check "Create Payment Voucher"
-   Select Cash/Bank account to pay from
-   Enter reference number (cheque/transfer)
-   Add optional payment notes

**What Happens:**

1. Creates Payment Voucher (PV-XXXX) in accounting system
2. **Debits:** Overtime Expenses (EXP-OT)
3. **Credits:** Selected Cash/Bank Account
4. Updates ledger account balances
5. Marks overtime as paid
6. Links voucher to overtime record

---

## 💡 HOW IT WORKS

### Accounting Integration

When you check "Create Payment Voucher":

```
PAYMENT VOUCHER (PV-2025-001)
Date: [Payment Date]
Reference: [Overtime Number or Custom Reference]

Account                  | Debit      | Credit
-------------------------------------------------
Overtime Expenses        | ₦50,000    | ₦0
Cash/Bank Account       | ₦0         | ₦50,000
-------------------------------------------------
Narration: Overtime payment for John Doe - OT-2025-123
```

**Automatic Ledger Updates:**

-   Overtime Expenses account increases (Debit)
-   Cash/Bank account decreases (Credit)
-   Balances update in real-time

---

## 📍 WHERE TO FIND PAYMENT RECORDS

### 1. **Overtime Module**

`/tenant/{tenant}/payroll/overtime`

-   View all overtime records
-   Filter by status (pending/approved/paid)
-   See payment status at a glance

### 2. **Accounting Module - Vouchers**

`/tenant/{tenant}/accounting/vouchers`

-   Filter by Voucher Type: "Payment" (PV)
-   Search by reference number (overtime number)
-   View complete accounting entries

### 3. **Ledger Accounts**

`/tenant/{tenant}/accounting/ledger-accounts`

-   **Overtime Expenses (EXP-OT)**: See all overtime payments
-   **Cash/Bank Accounts**: See all outgoing payments

---

## 🎯 COMPLETE FLOW EXAMPLE

### Scenario: Pay ₦50,000 overtime to employee John Doe

**Step 1: Create Overtime**

-   Employee: John Doe
-   Date: Nov 15, 2025
-   Method: Fixed Amount
-   Amount: ₦50,000
-   Reason: Emergency server maintenance
-   Status: **Pending**

**Step 2: Approve**

-   Manager reviews and approves
-   Status: **Approved** (Payment Pending)

**Step 3: Make Payment** ✨

-   Click "Mark as Paid" button
-   Select Payment Date: Nov 15, 2025
-   ✅ Check "Create Payment Voucher"
-   Pay From: **Cash in Hand** (or any bank account)
-   Reference: OT-2025-123
-   Notes: Paid via bank transfer
-   Click "Confirm Payment"

**Result:**
✅ Overtime marked as paid
✅ Payment voucher PV-2025-042 created
✅ Overtime Expenses increased by ₦50,000
✅ Cash account decreased by ₦50,000
✅ Complete audit trail maintained

**Step 4: Verify in Accounting**

-   Go to Accounting → Vouchers
-   Find PV-2025-042
-   View double-entry accounting
-   Check ledger balances updated

---

## 🔐 SYSTEM ACCOUNTS CREATED

The system automatically creates these accounts if they don't exist:

**Overtime Expenses (EXP-OT)**

-   Type: Expense
-   Nature: Debit increases expense
-   Purpose: Track all overtime payments

You can manually create:

-   Cash in Hand (CASH)
-   Bank Account (BANK)
-   Petty Cash (PETTY)

Or use any existing cash/bank account.

---

## ⚠️ IMPORTANT NOTES

1. **Voucher Creation is Optional**

    - You can mark as paid without creating voucher
    - Useful when payment is recorded elsewhere
    - Voucher recommended for complete accounting

2. **Double-Entry Accounting**

    - All voucher entries follow double-entry rules
    - Debit = Credit always balanced
    - Cannot be posted if unbalanced

3. **Audit Trail**

    - All actions logged with user and timestamp
    - Payment date recorded
    - Voucher linked to overtime record

4. **Payroll Integration**
    - Can link to payroll run (optional)
    - Useful when paying via monthly payroll
    - Standalone payments don't require payroll run

---

## 🛠️ TROUBLESHOOTING

### "No cash/bank accounts found"

**Solution:** Create cash or bank account in Accounting → Ledger Accounts

-   Code: CASH or BANK
-   Type: Asset
-   Active: Yes

### "Payment voucher type not found"

**Solution:** Run VoucherTypeSeeder for your tenant

```bash
php artisan db:seed --class=VoucherTypeSeeder
```

### "Cannot find overtime expenses account"

**Solution:** System auto-creates on first payment. If error persists, manually create:

-   Name: Overtime Expenses
-   Code: EXP-OT
-   Type: Expense

---

## 📊 REPORTS & INSIGHTS

**Available Data:**

-   Total overtime expenses by period
-   Outstanding unpaid overtime
-   Payment method breakdown
-   Employee-wise overtime analysis
-   Department-wise overtime costs

**Access via:**

-   Overtime Index (summary statistics)
-   Accounting Reports (expense analysis)
-   Ledger Account Statement (detailed transactions)

---

## ✅ CHECKLIST: Setting Up Overtime Payments

-   [x] ✅ Overtime records created
-   [x] ✅ Approval workflow configured
-   [x] ✅ Payment voucher integration complete
-   [x] ✅ Ledger accounts setup
-   [x] ✅ Cash/Bank accounts available
-   [x] ✅ Voucher types seeded
-   [x] ✅ Payment button on show page
-   [x] ✅ Accounting entries automated

**Status:** Ready to use! 🎉

---

## 🔄 INTEGRATION POINTS

### Current Integration:

✅ Accounting Module (Vouchers & Ledger Accounts)
✅ Employee Management
✅ Payroll System (optional linking)

### Future Enhancements (Optional):

-   Mobile app for overtime requests
-   Email notifications for approvals
-   SMS alerts for payments
-   Bulk payment processing
-   Overtime analytics dashboard
-   Export to Excel/PDF

---

**Need Help?** Check:

-   Accounting Module documentation
-   Voucher system guide
-   Ledger account setup guide
