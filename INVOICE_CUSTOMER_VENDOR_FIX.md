# Invoice Customer/Vendor Selection Fix

## Issue Description

Invoices were failing to save with validation error: "The customer id field is required."

### Root Cause

The invoice creation form has **two select fields** with the same name (`customer_id`):

1. **Customer select** - for sales transactions (visible by default)
2. **Vendor select** - for purchase transactions (hidden by default)

When switching between invoice types (sales ↔ purchase), both fields still existed in the DOM but:

-   The hidden field retained its `required` attribute
-   The hidden field was not `disabled`, so it was submitted with the form
-   Both fields submitted empty values when hidden, causing validation failures

## Error Log Evidence

```log
[2025-10-19 09:47:29] local.INFO: Request Data Received
{"customer_id":null,"inventory_items_count":1}

[2025-10-19 09:47:29] local.WARNING: Validation Failed
{"errors":{"customer_id":["The customer id field is required."]}}
```

## The Fix

### 1. Enhanced Logging (InvoiceController.php)

Added detailed logging to identify the issue:

```php
// Check if customer_id is missing and log the form state
if (is_null($request->input('customer_id')) || empty($request->input('customer_id'))) {
    Log::warning('Customer ID is missing from request', [
        'has_customer_id_key' => $request->has('customer_id'),
        'customer_id_value' => $request->input('customer_id'),
        'all_form_keys' => array_keys($request->except(['_token', 'current_tenant'])),
        'voucher_type_id' => $request->input('voucher_type_id')
    ]);
}
```

Added custom validation messages:

```php
'customer_id.required' => 'Please select a customer or vendor before saving the invoice.',
'customer_id.exists' => 'The selected customer or vendor is invalid.',
```

### 2. HTML Changes (create.blade.php)

**Customer Section:**

```blade
<select name="customer_id"
        id="customer_id"
        required
        class="...">
```

**Vendor Section:**

```blade
<select name="customer_id"
        id="vendor_select"
        disabled
        class="...">
```

### 3. JavaScript Fix (create.blade.php)

Updated `toggleCustomerVendorFields()` function to properly manage `disabled` and `required` attributes:

```javascript
toggleCustomerVendorFields(voucherType) {
    const customerSelect = document.getElementById('customer_id');
    const vendorSelect = document.getElementById('vendor_select');

    const isPurchase = voucherType.code.includes('PUR') ||
                       voucherType.code.includes('PURCHASE') ||
                       voucherType.name.toLowerCase().includes('purchase');

    if (isPurchase) {
        // Show vendor, hide customer
        vendorSelect.removeAttribute('disabled');
        vendorSelect.setAttribute('required', 'required');

        customerSelect.setAttribute('disabled', 'disabled');
        customerSelect.removeAttribute('required');
        customerSelect.value = '';

        console.log('✅ Switched to vendor field');
    } else {
        // Show customer, hide vendor
        customerSelect.removeAttribute('disabled');
        customerSelect.setAttribute('required', 'required');

        vendorSelect.setAttribute('disabled', 'disabled');
        vendorSelect.removeAttribute('required');
        vendorSelect.value = '';

        console.log('✅ Switched to customer field');
    }
}
```

## How It Works

### Sales Invoice Flow

1. User opens invoice creation page
2. **Customer field is enabled** (required + not disabled)
3. **Vendor field is disabled** (no required + disabled)
4. User selects customer → field has value
5. Form submits → customer_id is sent with value
6. ✅ Validation passes

### Purchase Invoice Flow

1. User selects "Purchase Invoice" voucher type
2. JavaScript toggles fields:
    - **Customer field disabled** (no validation)
    - **Vendor field enabled** (required + not disabled)
3. User selects vendor → field has value
4. Form submits → customer_id is sent with vendor's ledger account ID
5. ✅ Validation passes

### Key Principles

-   **Disabled fields don't submit** - Browser won't send disabled field values
-   **Only visible field is required** - No validation errors from hidden fields
-   **Same name for both** - Backend expects `customer_id` regardless of type
-   **Value is cleared** - When hiding a field, clear its value to prevent confusion

## Testing Checklist

### Test 1: Sales Invoice

-   [ ] Select "Sales Invoice" type
-   [ ] Customer dropdown is visible and enabled
-   [ ] Vendor dropdown is hidden
-   [ ] Can select a customer
-   [ ] Invoice saves successfully
-   [ ] No validation errors

### Test 2: Purchase Invoice

-   [ ] Select "Purchase Invoice" type
-   [ ] Vendor dropdown is visible and enabled
-   [ ] Customer dropdown is hidden
-   [ ] Can select a vendor
-   [ ] Invoice saves successfully
-   [ ] No validation errors

### Test 3: Switching Types

-   [ ] Start with Sales Invoice selected
-   [ ] Select a customer
-   [ ] Switch to Purchase Invoice
-   [ ] Customer selection cleared
-   [ ] Vendor dropdown now active
-   [ ] Select a vendor
-   [ ] Invoice saves with vendor

### Test 4: Quick Add

-   [ ] Click "Quick Add Customer" button
-   [ ] Create customer from modal
-   [ ] Customer auto-selected in dropdown
-   [ ] Can save invoice successfully

### Test 5: Validation

-   [ ] Try to submit without selecting customer/vendor
-   [ ] See user-friendly error message: "Please select a customer or vendor"
-   [ ] Select customer/vendor
-   [ ] Can now submit successfully

## Browser Console Logs

When working correctly, you should see:

```
✅ Invoice form initialized
✅ Switched to customer field
// or
✅ Switched to vendor field
```

## Database Impact

No database changes required. This is purely a frontend and validation fix.

## Backwards Compatibility

✅ Fully backwards compatible

-   Existing invoices not affected
-   Same field name (`customer_id`) used
-   Same validation rules
-   Only form behavior changed

## Related Files

-   `app/Http/Controllers/Tenant/Accounting/InvoiceController.php` - Enhanced logging
-   `resources/views/tenant/accounting/invoices/create.blade.php` - Form and JavaScript fixes

## Additional Benefits

### 1. Better Error Messages

Before:

```
The customer id field is required.
```

After:

```
Please select a customer or vendor before saving the invoice.
```

### 2. Enhanced Logging

Logs now show:

-   Whether customer_id is null vs empty
-   Which form fields were submitted
-   Voucher type being used
-   Exact validation failures

### 3. Console Feedback

Developers can see in browser console when fields toggle:

```
✅ Switched to vendor field
```

## Future Improvements

### Potential Enhancements

1. **Remember Selection**: Save last selected customer/vendor in localStorage
2. **Default Selection**: Auto-select if only one customer/vendor exists
3. **Search Functionality**: Add search/filter for large customer/vendor lists
4. **Keyboard Shortcuts**: Allow quick selection with keyboard
5. **Visual Indicators**: Show active field with colored border

### Known Limitations

1. Both fields share same `name` attribute - could be confusing in code
2. No visual feedback when field is disabled (relies on hidden parent div)
3. Console logs might clutter production logs (consider removing for production)

## Troubleshooting

### Issue: Validation still fails

**Solution:**

1. Check browser console for "Switched to..." log
2. Inspect element - verify only one select is enabled
3. Check if customer/vendor actually selected (not empty option)
4. Review Laravel log for exact validation error

### Issue: Wrong field showing

**Solution:**

1. Check voucher type configuration in database
2. Verify `code` or `name` contains 'PUR' or 'PURCHASE'
3. Check JavaScript console for errors
4. Manually call `updateVoucherType()` from console

### Issue: Selection not saving

**Solution:**

1. Verify ledger accounts exist for customers/vendors
2. Check if ledger_account_id is properly set
3. Review database constraints
4. Check if customer/vendor was soft deleted

## Conclusion

This fix ensures that only the visible and relevant select field (customer OR vendor) is validated and submitted, preventing validation errors caused by hidden fields. The enhanced logging also makes future debugging much easier.
