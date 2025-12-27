# Invoice Payment Callback - Complete Guide

## Overview

When customers pay invoices using Nomba or Paystack payment links, the payment gateway redirects them back to your system's callback URL. This guide explains how the callback works and how to troubleshoot issues.

## How Payment Flow Works

### 1. Invoice Creation

```
Admin creates invoice → System generates payment links (Nomba + Paystack) → Links stored in vouchers.meta_data
```

### 2. Customer Pays

```
Customer clicks payment link → Redirected to gateway → Completes payment → Gateway redirects to callback URL
```

### 3. Payment Callback

```
Callback URL: /{tenant}/invoice/payment-callback/{invoice}
Callback receives payment data → Verifies with gateway API → Creates receipt voucher → Updates balances
```

## Callback URL Details

### Route Definition

**File:** `routes/web.php`

```php
Route::match(['get', 'post'], '/{tenant}/invoice/payment-callback/{invoice}',
    [InvoiceController::class, 'publicPaymentCallback'])
    ->middleware(['tenant'])  // Only tenant middleware, NO auth required
    ->name('tenant.invoice.payment.callback');
```

**Key Points:**

-   ✅ Accepts both GET and POST requests
-   ✅ Public access (no authentication required)
-   ✅ Must be defined BEFORE `require __DIR__.'/tenant.php'` to avoid auth middleware
-   ✅ Tenant middleware validates the tenant slug

### Method: publicPaymentCallback()

**File:** `app/Http/Controllers/Tenant/Accounting/InvoiceController.php`

**Flow:**

1. **Receive callback** - Log tenant, invoice, and query parameters
2. **Resolve tenant** - Convert slug to Tenant object
3. **Resolve invoice** - Get Voucher by ID
4. **Validate ownership** - Ensure invoice belongs to tenant
5. **Check status** - Only posted invoices can receive payments
6. **Get payment links** - Retrieve from `meta_data['payment_links']`
7. **Detect gateway** - Check query parameters:
    - Nomba: `?orderReference=XXX`
    - Paystack: `?reference=XXX&trxref=XXX`
8. **Verify payment** - Call gateway API to confirm payment success
9. **Check duplicates** - Ensure payment not already recorded
10. **Create receipt voucher** - Record payment in accounting system
11. **Update balances** - Customer and bank account balances
12. **Redirect** - Send customer to invoice page with success message

## Gateway-Specific Behavior

### Nomba

**Redirect Parameters:**

```
?orderReference=INV_SV-14
```

**Verification:**

```php
$nombaHelper->verifyPayment($nombaReference)
// Returns: ['status' => true, 'payment_status' => 'successful']
```

### Paystack

**Redirect Parameters:**

```
?reference=INV_SV-14&trxref=INV_SV-14&status=success
```

**Verification:**

```php
$paystackHelper->verifyTransaction($paystackReference)
// Returns: ['status' => true, 'payment_status' => 'successful', 'amount' => 5000]
```

## Testing the Callback

### 1. Check Route Registration

```bash
php artisan route:clear
php artisan route:list | grep payment-callback
```

**Expected Output:**

```
GET|POST  {tenant}/invoice/payment-callback/{invoice}  tenant.invoice.payment.callback
```

### 2. Test Route Accessibility (Without Auth)

Open in **incognito browser** (to test without login):

```
http://localhost:8000/{tenant-slug}/invoice/payment-callback/{invoice-id}
```

**Expected:** Should NOT redirect to login page

### 3. Create Test Invoice with Payment Links

```php
// Admin dashboard
1. Go to Invoices → Create Invoice
2. Select customer WITH email address
3. Add products/services
4. Click "Save & Post"
5. View invoice → Check "Payment Links" section
6. Should see Nomba and/or Paystack links
```

### 4. Simulate Payment Gateway Callback

```bash
# Test Nomba callback
curl "http://localhost:8000/your-tenant/invoice/payment-callback/123?orderReference=INV_SV-14"

# Test Paystack callback
curl "http://localhost:8000/your-tenant/invoice/payment-callback/123?reference=INV_SV-14&trxref=INV_SV-14"
```

### 5. Check Laravel Logs

**File:** `storage/logs/laravel.log`

**Expected Logs:**

```
=== PUBLIC PAYMENT CALLBACK RECEIVED ===
Resolved tenant and invoice
Attempting Nomba/Paystack verification
Payment verification result
=== PAYMENT RECORDED SUCCESSFULLY ===
```

## Common Issues & Solutions

### Issue 1: Callback redirects to login page

**Cause:** Route defined inside `tenant.php` require block
**Solution:** Move route definition BEFORE `require __DIR__.'/tenant.php'` in `web.php`

### Issue 2: Payment verified but not recorded

**Cause:**

-   Duplicate payment check finding existing payment
-   Receipt voucher type (RV) not found
-   Bank account not configured

**Solution:**

```sql
-- Check if RV voucher type exists
SELECT * FROM voucher_types WHERE code = 'RV';

-- Check if bank account exists
SELECT * FROM ledger_accounts la
JOIN account_groups ag ON la.account_group_id = ag.id
WHERE ag.code = 'BA';
```

### Issue 3: Payment links not generated

**Cause:** Customer has no email address
**Solution:** Ensure customer record has valid email:

```sql
SELECT c.*, la.name FROM customers c
JOIN ledger_accounts la ON c.ledger_account_id = la.id
WHERE c.email IS NULL OR c.email = '';
```

### Issue 4: Gateway verification fails

**Cause:**

-   Gateway credentials not configured
-   API timeout/downtime
-   Reference mismatch

**Debug:**

```php
// Check settings table
SELECT * FROM settings WHERE slug LIKE '%nomba%' OR slug LIKE '%paystack%';

// Check laravel.log for:
// "Nomba verification failed"
// "Paystack verification failed"
```

### Issue 5: Callback not called by gateway

**Cause:** Gateway not configured with callback URL
**Action Required:**

1. **Paystack:** Go to Dashboard → Settings → Webhooks → Add callback URL
2. **Nomba:** Contact support to configure callback URL

## Database Changes on Successful Payment

### 1. New Receipt Voucher Created

```sql
SELECT * FROM vouchers
WHERE reference_number LIKE 'Online Payment%'
ORDER BY id DESC LIMIT 1;
```

### 2. Voucher Entries

```sql
-- Debit: Bank Account (cash received)
-- Credit: Customer Account (debt reduced)
SELECT * FROM voucher_entries WHERE voucher_id = ?;
```

### 3. Updated Balances

```sql
-- Customer ledger balance decreased
SELECT current_balance FROM ledger_accounts WHERE id = ?;

-- Customer outstanding_balance decreased
SELECT outstanding_balance FROM customers WHERE ledger_account_id = ?;

-- Bank account balance increased
SELECT current_balance FROM ledger_accounts WHERE id = ?;
```

## Security Considerations

✅ **Payment verification:** Always verify with gateway API, never trust query parameters alone
✅ **Duplicate prevention:** Check for existing receipt vouchers with same reference
✅ **Tenant isolation:** Ensure invoice belongs to tenant before processing
✅ **No authentication:** Callback is public but validates payment server-side
✅ **Audit trail:** Comprehensive logging for payment forensics

## Next Steps

If callback still not working after following this guide:

1. **Check Laravel logs** for specific error messages
2. **Use Telescope** to see exact middleware and parameters
3. **Test with Postman** to isolate gateway vs system issues
4. **Contact gateway support** to confirm callback URL configuration
5. **Check firewall/proxy** settings that might block callbacks

## Key Files Reference

-   **Controller:** `app/Http/Controllers/Tenant/Accounting/InvoiceController.php`

    -   Method: `publicPaymentCallback()` (lines 2078-2323)
    -   Method: `generatePaymentLinks()` (lines 1940-2076)

-   **Routes:** `routes/web.php`

    -   Line: 117-120 (public callback route)

-   **Helpers:**

    -   `app/Helpers/PaymentHelper.php` (Nomba)
    -   `app/Helpers/PaystackPaymentHelper.php` (Paystack)

-   **View:** `resources/views/tenant/accounting/invoices/show.blade.php`
    -   Lines: 268-320 (payment links display)

## Support

For issues not covered in this guide, check:

-   Laravel logs: `storage/logs/laravel.log`
-   Telescope: `/telescope/requests`
-   Gateway documentation: Nomba API docs, Paystack API docs
