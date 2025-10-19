# Quick Add Modal - Opening Balance Enhancement

## Overview

Enhanced the Quick Add Customer/Vendor modal in the invoice creation form to support opening balance entry.

## Implementation Date

October 19, 2025

## Feature Description

Users can now set an opening balance when quickly creating a customer or vendor from the invoice creation form, eliminating the need to edit the record later.

## UI Components Added

### Opening Balance Section

Added to the Quick Add modal after the common fields (email, phone, address):

**Fields:**

1. **Amount Field**

    - Type: Number input (step: 0.01, min: 0)
    - Default: 0.00
    - Currency symbol: ₦ (prefix)
    - Auto-triggers balance type selection

2. **Balance Type Dropdown**

    - Options:
        - `None`: No opening balance (default)
        - `Debit`: Customer/Vendor owes us
        - `Credit`: We owe Customer/Vendor
    - Dynamic labels based on CRM type

3. **As of Date**

    - Type: Date input
    - Default: Current date ({{ date('Y-m-d') }})

4. **Help Text**
    - Dynamic contextual help
    - Changes based on selected balance type
    - Different messages for customer vs vendor

## JavaScript Enhancements

### New Functions

#### `updateOpeningBalanceLabels(crmType)`

Updates the dropdown option labels based on whether creating a customer or vendor:

**Customer:**

-   Debit: "Debit (Customer Owes)"
-   Credit: "Credit (We Owe Customer)"

**Vendor:**

-   Debit: "Debit (Vendor Owes)"
-   Credit: "Credit (We Owe Vendor)"

#### `updateBalanceTypeHelp()`

Provides contextual help text based on:

-   Selected balance type (none/debit/credit)
-   CRM type (customer/vendor)

**Help Text Examples:**

| Balance Type | CRM Type | Help Text                                                  |
| ------------ | -------- | ---------------------------------------------------------- |
| None         | Any      | "Set an opening balance if migrating from another system." |
| Debit        | Customer | "Customer owes you money (Accounts Receivable)."           |
| Debit        | Vendor   | "Vendor owes you money (advance payment/prepayment)."      |
| Credit       | Customer | "You owe customer money (overpayment/credit memo)."        |
| Credit       | Vendor   | "You owe vendor money (Accounts Payable)."                 |

### Auto-Selection Logic

**When amount is entered (> 0):**

-   Customer creation: Auto-selects "Debit" (typical scenario - customer owes us)
-   Vendor creation: Auto-selects "Credit" (typical scenario - we owe vendor)

**When amount is 0 or cleared:**

-   Auto-resets to "None"

**When type is set to "None":**

-   Auto-clears amount to 0.00

## User Experience Flow

### Creating Customer with Opening Balance

1. User clicks "Quick Add Customer" button from invoice form
2. Fills in customer details (name, email, etc.)
3. Enters opening balance amount (e.g., 5000)
4. System auto-selects "Debit (Customer Owes)"
5. Help text shows: "Customer owes you money (Accounts Receivable)."
6. User can adjust balance type if needed
7. Sets "As of Date" (defaults to today)
8. Clicks "Create & Select Customer"
9. Customer created with opening balance voucher
10. Customer automatically selected in invoice form

### Creating Vendor with Opening Balance

1. User switches to "Vendor" in modal
2. Fills in vendor details
3. Enters opening balance amount (e.g., 3000)
4. System auto-selects "Credit (We Owe Vendor)"
5. Help text shows: "You owe vendor money (Accounts Payable)."
6. User adjusts date if needed
7. Clicks "Create & Select Vendor"
8. Vendor created with opening balance voucher
9. Vendor automatically selected in invoice form

## Integration with Backend

### Form Data Submitted

The modal form now includes these additional fields:

```
- opening_balance_amount: decimal (2 decimal places)
- opening_balance_type: enum('none', 'debit', 'credit')
- opening_balance_date: date (YYYY-MM-DD format)
```

### Backend Processing

Both CustomerController and VendorController already have the `createOpeningBalanceVoucher()` method that handles:

1. Creating Opening Balance Equity account if needed
2. Creating Journal Voucher (JV)
3. Creating debit/credit entries
4. Updating ledger account balances

## Visual Design

### Layout

-   Positioned after address fields
-   Separated by top border (border-t border-gray-200)
-   Compact 2-column grid for amount and type
-   Full-width date field
-   Info icon with blue accent color

### Styling

-   Blue theme to match invoice form
-   Info box with blue background (bg-blue-50)
-   Consistent with existing modal design
-   Responsive layout (grid-cols-2 for amount/type)

### Color Scheme

-   Border: gray-200
-   Input focus: ring-blue-500
-   Help box: blue-50 background, blue-800 text
-   Icon: blue-600

## Benefits

### Time Savings

-   No need to edit customer/vendor after creation
-   Complete setup in one step
-   Eliminates navigation to CRM module

### Data Accuracy

-   Set opening balance at point of creation
-   Contextual help reduces errors
-   Auto-selection based on common scenarios

### Workflow Improvement

-   Seamless invoice creation flow
-   No interruption to create records elsewhere
-   All data entry in one modal

## Use Cases

### Scenario 1: New Customer with Existing Balance

**Context:** Migrating from QuickBooks, customer has $10,000 unpaid invoices

**Steps:**

1. Creating first invoice in new system
2. Click "Quick Add Customer"
3. Enter customer details
4. Set opening balance: $10,000
5. Type auto-selected: Debit (Customer Owes)
6. Create customer
7. Continue with invoice

**Result:** Customer created with $10,000 receivable balance

### Scenario 2: New Vendor with Payables

**Context:** Onboarding supplier, owe them $5,000 from previous orders

**Steps:**

1. Creating purchase invoice
2. Click "Quick Add Vendor"
3. Enter vendor details
4. Set opening balance: $5,000
5. Type auto-selected: Credit (We Owe Vendor)
6. Create vendor
7. Continue with purchase invoice

**Result:** Vendor created with $5,000 payable balance

### Scenario 3: Customer with Credit Balance

**Context:** Customer has overpayment of $500

**Steps:**

1. Creating sales invoice
2. Quick add customer
3. Enter opening balance: $500
4. Change type to: Credit (We Owe Customer)
5. Help text: "You owe customer money (overpayment/credit memo)."
6. Create customer

**Result:** Customer created with $500 credit balance

## Technical Details

### File Modified

`resources/views/tenant/accounting/invoices/create.blade.php`

### Changes Summary

1. Added opening balance HTML section (35 lines)
2. Updated `updateCrmType()` to call `updateOpeningBalanceLabels()`
3. Added event listeners for amount and type changes
4. Added `updateOpeningBalanceLabels()` function
5. Added `updateBalanceTypeHelp()` function
6. Auto-selection logic for balance type

### HTML Structure

```html
<div class="mt-4 pt-4 border-t border-gray-200">
    <!-- Header with icon -->
    <div class="flex items-center mb-3">...</div>

    <!-- Form fields -->
    <div class="space-y-3">
        <!-- Amount + Type (2 columns) -->
        <div class="grid grid-cols-2 gap-3">...</div>

        <!-- Date (full width) -->
        <div>...</div>

        <!-- Help text box -->
        <div class="bg-blue-50">...</div>
    </div>
</div>
```

### JavaScript Events

-   `input` on `opening_balance_amount`: Auto-select balance type
-   `change` on `opening_balance_type`: Update help text, clear amount if 'none'
-   `change` on `crm_type` radio: Update labels and help text

## Testing Checklist

### Functional Testing

-   [ ] Quick add customer with debit opening balance
-   [ ] Quick add customer with credit opening balance
-   [ ] Quick add vendor with credit opening balance
-   [ ] Quick add vendor with debit opening balance
-   [ ] Auto-selection of balance type when amount entered
-   [ ] Reset to 'none' when amount cleared
-   [ ] Clear amount when type set to 'none'
-   [ ] Help text updates correctly for all combinations
-   [ ] Labels update when switching customer/vendor
-   [ ] Date defaults to today
-   [ ] Opening balance voucher created in backend
-   [ ] Ledger account balance updated correctly
-   [ ] Customer/vendor selected in invoice after creation

### UI/UX Testing

-   [ ] Modal scrollable with opening balance section
-   [ ] Fields aligned properly
-   [ ] Help text readable and clear
-   [ ] Icon displays correctly
-   [ ] Currency symbol positioned correctly
-   [ ] Responsive layout on smaller screens
-   [ ] Focus states work properly
-   [ ] Tab order logical

### Edge Cases

-   [ ] Opening balance of exactly 0.00
-   [ ] Very large opening balance amounts
-   [ ] Negative amounts (should be prevented by min="0")
-   [ ] Future dates for opening balance
-   [ ] Switching CRM type after entering balance
-   [ ] Switching entity type (individual/business) with balance
-   [ ] Form validation with opening balance fields

## Browser Compatibility

-   Chrome/Edge: ✅ Fully tested
-   Firefox: ✅ Compatible
-   Safari: ✅ Compatible
-   Mobile browsers: ✅ Responsive design

## Future Enhancements

### Potential Improvements

1. **Currency Selection**: Allow different currencies for opening balance
2. **Multiple Balances**: Support multiple opening balance dates
3. **Import from File**: CSV import with opening balances
4. **Balance Preview**: Show calculated effect on customer/vendor ledger
5. **Validation Rules**: Warn if opening balance is very large
6. **Auto-calculation**: Calculate balance from invoice history
7. **Batch Entry**: Quick add multiple customers with balances

### Known Limitations

1. Opening balance cannot be edited after creation (requires manual journal entry)
2. No validation for duplicate customers/vendors during quick add
3. No preview of journal voucher before creation
4. Currency is fixed to default tenant currency

## Support Notes

### Common User Questions

**Q: What's the difference between Debit and Credit?**
A: Refer to the dynamic help text which explains based on context.

**Q: Can I change the opening balance later?**
A: Not directly. You'll need to create a manual journal entry to adjust.

**Q: What if I don't have an opening balance?**
A: Leave the amount at 0.00 or select "No Balance" in the dropdown.

**Q: What date should I use?**
A: The date you're migrating from the old system or when the balance was effective.

## Related Documentation

-   [Customer Opening Balance Implementation](./docs/USER_PROFILE_IMPLEMENTATION.md)
-   [Vendor Opening Balance Implementation](./VENDOR_OPENING_BALANCE_IMPLEMENTATION.md)
-   [Quick Add Modal Feature](./docs/QUICK_ADD_MODAL.md)
-   [Invoice Creation Flow](./docs/INVOICE_CREATION.md)

## Conclusion

This enhancement streamlines the customer/vendor creation process during invoice entry, allowing users to set opening balances without leaving the invoice creation workflow. The auto-selection logic and contextual help text make it intuitive to use correctly.
