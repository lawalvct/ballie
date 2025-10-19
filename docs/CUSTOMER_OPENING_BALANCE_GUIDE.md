# Customer Opening Balance - Quick Reference Guide

## What is an Opening Balance?

An opening balance is the initial balance a customer has when you start using the system. This is useful when:

-   Migrating from another system
-   Customer has existing invoices/payments
-   Starting mid-period with existing balances

## How to Set Opening Balance

### Step 1: Navigate to Customer Creation

Go to: **CRM > Customers > Add New Customer**

### Step 2: Fill Basic Information

Complete required fields:

-   Customer Type (Individual/Business)
-   Name/Company Name
-   Email
-   Phone

### Step 3: Expand Financial Information Section

Click on "Section 5: Financial Information" to expand it.

### Step 4: Set Opening Balance

You'll see a blue-highlighted "Opening Balance" section with three fields:

#### Field 1: Opening Balance Amount

-   Enter the balance amount (always use positive numbers)
-   Example: `5000.00` for $5,000
-   Leave as `0.00` if no opening balance

#### Field 2: Balance Type

Choose one of:

**Option 1: None (No Opening Balance)**

-   Default option
-   For new customers with no previous balance

**Option 2: Debit (Customer Owes You)**

-   Use when: Customer has unpaid invoices
-   Example: Customer bought goods worth $5,000 and hasn't paid yet
-   Creates: Accounts Receivable (Asset)

**Option 3: Credit (You Owe Customer)**

-   Use when: Customer made advance payment or has credit
-   Example: Customer paid $2,000 in advance for future purchases
-   Creates: Customer Credit Balance (Liability)

#### Field 3: Opening Balance Date

-   Select the date for this opening balance
-   Defaults to today
-   Use the date from your previous system for accuracy

## Visual Guide

```
┌──────────────────────────────────────────────────────────┐
│  💰 Opening Balance                                      │
│  Set an initial balance if this customer has an          │
│  existing balance from a previous system                 │
│                                                           │
│  Opening Balance Amount:    [  5000.00  ]               │
│  Enter the balance amount (always positive)              │
│                                                           │
│  Balance Type:              [ Debit (Customer Owes You) ▼]│
│  Debit: Customer has outstanding balance                 │
│  Credit: Customer has advance payment or credit          │
│                                                           │
│  Opening Balance Date:      [ 2025-01-15   ]            │
│  The date when this opening balance should be recorded   │
└──────────────────────────────────────────────────────────┘
```

## Common Scenarios

### Scenario A: Customer with Unpaid Invoice

**Situation:** John Doe owes you $3,500 from last month

**Settings:**

-   Opening Balance Amount: `3500.00`
-   Balance Type: `Debit (Customer Owes You)`
-   Opening Balance Date: First day of current period

**Result:**

-   Customer shows $3,500 outstanding
-   Will appear in Accounts Receivable report

---

### Scenario B: Customer with Credit Balance

**Situation:** ABC Corporation paid $10,000 in advance

**Settings:**

-   Opening Balance Amount: `10000.00`
-   Balance Type: `Credit (You Owe Customer)`
-   Opening Balance Date: Date of advance payment

**Result:**

-   Customer has $10,000 credit
-   Can be used against future invoices
-   Shows as negative balance

---

### Scenario C: New Customer (No Previous Balance)

**Situation:** Brand new customer, no previous transactions

**Settings:**

-   Opening Balance Amount: `0.00` (or leave default)
-   Balance Type: `None (No Opening Balance)`

**Result:**

-   Customer starts with zero balance
-   No opening balance voucher created

## Important Notes

### ✅ Do's

-   Use positive numbers for amounts
-   Select correct balance type (Debit vs Credit)
-   Use accurate dates from previous system
-   Verify balance before saving

### ❌ Don'ts

-   Don't use negative numbers (use Balance Type instead)
-   Don't guess balances (verify from records)
-   Don't forget to select correct type
-   Don't mix up Debit/Credit meanings

## Behind the Scenes (Technical)

When you create a customer with an opening balance:

1. **Customer Record Created**

    - Basic customer information saved
    - Status set to "Active"

2. **Ledger Account Created**

    - Automatically creates a ledger account
    - Linked to "Accounts Receivable" group

3. **Journal Voucher Created** (if balance > 0)

    - Type: Journal Voucher (JV)
    - Status: Posted (automatically)
    - Records the opening balance

4. **Accounting Entries Made**

    For **Debit Balance**:

    ```
    Dr. Customer Ledger Account    $5,000
        Cr. Opening Balance Equity        $5,000
    ```

    For **Credit Balance**:

    ```
    Dr. Opening Balance Equity     $2,000
        Cr. Customer Ledger Account       $2,000
    ```

## Verifying the Opening Balance

After creating the customer:

1. **Check Customer List**

    - Go to: CRM > Customers
    - Find the customer
    - Look at "Outstanding Balance" column

2. **View Customer Statement**

    - Go to: CRM > Customer Statements
    - Find the customer
    - Opening balance should appear

3. **Check Ledger Account**

    - Go to: Accounting > Ledger Accounts
    - Search for customer name
    - View balance and entries

4. **View Journal Voucher**
    - Go to: Accounting > Vouchers
    - Filter by "Journal Voucher" type
    - Find the opening balance entry

## Troubleshooting

### Balance Not Showing

**Problem:** Created customer but balance doesn't appear
**Solution:**

-   Ensure amount was greater than 0
-   Check that type wasn't set to "None"
-   Verify customer was saved successfully

### Wrong Balance Type

**Problem:** Balance showing opposite of what intended
**Solution:**

-   Debit = Customer owes YOU (receivable)
-   Credit = YOU owe customer (payable/credit)
-   May need to create adjustment voucher

### Need to Change Opening Balance

**Problem:** Made mistake in opening balance
**Solution:**

-   For now, create a Journal Voucher to adjust
-   Contact support for opening balance modification

## FAQ

**Q: Can I change the opening balance after creation?**
A: Not directly in this version. You'll need to create an adjustment journal voucher.

**Q: What if I don't know the exact balance?**
A: Leave it at zero for now and add it later when confirmed, or use an estimated amount and adjust later.

**Q: Does opening balance affect my reports?**
A: Yes! It will appear in all financial reports, customer statements, and aging reports.

**Q: What account does the opening balance offset?**
A: It uses "Opening Balance Equity" account, which is automatically created.

**Q: Can I import multiple customers with opening balances?**
A: Currently, you need to add them individually. Bulk import feature coming soon.

**Q: What date should I use for opening balance?**
A: Use the date from your previous system, or the first day you started using this system.

## Need Help?

If you're unsure about:

-   Which balance type to use
-   The correct amount
-   Dating the opening balance

Contact your accountant or support team for guidance.

---

**Remember:**

-   Debit = They owe you 💵➡️
-   Credit = You owe them 💵⬅️
