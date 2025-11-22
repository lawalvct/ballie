# Quotation (Proforma Invoice) Implementation Plan

## Overview

Implement a quotation/proforma invoice system that allows admins to create quotes for customers, which can optionally be converted to real invoices later.

## Database Changes

### 1. New Migration: Create Quotations Table

**File**: `database/migrations/2025_01_15_000001_create_quotations_table.php`

```php
- id
- tenant_id (foreign key to tenants)
- quotation_number (unique per tenant)
- quotation_date
- expiry_date (quote validity period)
- customer_id (foreign key to customers, nullable)
- vendor_id (foreign key to vendors, nullable)
- customer_ledger_id (foreign key to ledger_accounts)
- reference_number (nullable)
- subject (quote title/subject)
- terms_and_conditions (text, nullable)
- notes (text, nullable)
- subtotal (decimal 15,2)
- discount_amount (decimal 15,2, default 0)
- tax_amount (decimal 15,2, default 0)
- total_amount (decimal 15,2)
- status (enum: 'draft', 'sent', 'accepted', 'rejected', 'expired', 'converted')
- converted_to_invoice_id (foreign key to vouchers, nullable)
- converted_at (timestamp, nullable)
- sent_at (timestamp, nullable)
- accepted_at (timestamp, nullable)
- rejected_at (timestamp, nullable)
- rejection_reason (text, nullable)
- created_by (foreign key to users)
- updated_by (foreign key to users, nullable)
- timestamps
- softDeletes
```

### 2. New Migration: Create Quotation Items Table

**File**: `database/migrations/2025_01_15_000002_create_quotation_items_table.php`

```php
- id
- quotation_id (foreign key to quotations)
- product_id (foreign key to products)
- product_name (string)
- description (text, nullable)
- quantity (decimal 15,2)
- unit (string, nullable)
- rate (decimal 15,2)
- discount (decimal 15,2, default 0)
- tax (decimal 15,2, default 0)
- is_tax_inclusive (boolean, default false)
- amount (decimal 15,2) - before tax/discount
- total (decimal 15,2) - after tax/discount
- timestamps
```

### 3. Add Quotation Reference to Vouchers Table

**File**: `database/migrations/2025_01_15_000003_add_quotation_id_to_vouchers_table.php`

```php
- quotation_id (foreign key to quotations, nullable)
```

## Models

### 1. Quotation Model

**File**: `app/Models/Quotation.php`

**Relationships**:

-   belongsTo: tenant, customer, vendor, customerLedger, convertedToInvoice, createdBy, updatedBy
-   hasMany: items (QuotationItem)

**Methods**:

-   `getQuotationNumber()` - Format: QT-2025-0001
-   `isExpired()` - Check if quote has expired
-   `canBeConverted()` - Check if can be converted to invoice
-   `convertToInvoice()` - Convert quotation to invoice
-   `markAsSent()`, `markAsAccepted()`, `markAsRejected()`
-   `calculateTotals()` - Recalculate all totals

**Scopes**:

-   `active()` - Not expired, not converted
-   `pending()` - Sent but not accepted/rejected
-   `expired()` - Past expiry date

### 2. QuotationItem Model

**File**: `app/Models/QuotationItem.php`

**Relationships**:

-   belongsTo: quotation, product

**Methods**:

-   `calculateTotal()` - Calculate line total with tax/discount

## Controllers

### 1. QuotationController

**File**: `app/Http/Controllers/Tenant/Accounting/QuotationController.php`

**Methods**:

-   `index()` - List all quotations with filters
-   `create()` - Show create form
-   `store()` - Save new quotation
-   `show()` - View quotation details
-   `edit()` - Edit draft quotation
-   `update()` - Update quotation
-   `destroy()` - Delete draft quotation
-   `duplicate()` - Create copy of quotation
-   `print()` - Print quotation
-   `pdf()` - Generate PDF
-   `email()` - Email quotation to customer
-   `convertToInvoice()` - Convert to invoice
-   `markAsSent()` - Mark as sent
-   `markAsAccepted()` - Mark as accepted
-   `markAsRejected()` - Mark as rejected
-   `searchCustomers()` - AJAX customer search
-   `searchProducts()` - AJAX product search

## Routes

### Tenant Routes

**File**: `routes/tenant.php`

```php
Route::prefix('accounting')->name('accounting.')->group(function () {
    // Quotations
    Route::resource('quotations', QuotationController::class);
    Route::post('quotations/{quotation}/convert', [QuotationController::class, 'convertToInvoice'])
        ->name('quotations.convert');
    Route::post('quotations/{quotation}/send', [QuotationController::class, 'markAsSent'])
        ->name('quotations.send');
    Route::post('quotations/{quotation}/accept', [QuotationController::class, 'markAsAccepted'])
        ->name('quotations.accept');
    Route::post('quotations/{quotation}/reject', [QuotationController::class, 'markAsRejected'])
        ->name('quotations.reject');
    Route::post('quotations/{quotation}/duplicate', [QuotationController::class, 'duplicate'])
        ->name('quotations.duplicate');
    Route::get('quotations/{quotation}/print', [QuotationController::class, 'print'])
        ->name('quotations.print');
    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])
        ->name('quotations.pdf');
    Route::post('quotations/{quotation}/email', [QuotationController::class, 'email'])
        ->name('quotations.email');

    // AJAX endpoints
    Route::get('quotations/search/customers', [QuotationController::class, 'searchCustomers'])
        ->name('quotations.search.customers');
    Route::get('quotations/search/products', [QuotationController::class, 'searchProducts'])
        ->name('quotations.search.products');
});
```

## Views

### 1. Index View

**File**: `resources/views/tenant/accounting/quotations/index.blade.php`

**Features**:

-   List all quotations with status badges
-   Filters: status, date range, customer, search
-   Actions: view, edit, convert, duplicate, delete
-   Status indicators with colors
-   Quick stats: total quotes, pending, accepted, expired

### 2. Create/Edit View

**File**: `resources/views/tenant/accounting/quotations/create.blade.php`
**File**: `resources/views/tenant/accounting/quotations/edit.blade.php`

**Features**:

-   Customer selection (searchable dropdown)
-   Quotation details: date, expiry date, reference, subject
-   Product line items (similar to invoice)
-   Add/remove items dynamically
-   Quick add product modal
-   Discount and tax calculations
-   Terms and conditions editor
-   Notes section
-   Save as draft or send
-   Real-time total calculations

### 3. Show View

**File**: `resources/views/tenant/accounting/quotations/show.blade.php`

**Features**:

-   Professional quotation display
-   Customer information
-   Line items with totals
-   Status badge
-   Action buttons:
    -   Convert to Invoice (if accepted/sent)
    -   Mark as Sent
    -   Mark as Accepted
    -   Mark as Rejected
    -   Duplicate
    -   Edit (if draft)
    -   Print
    -   Download PDF
    -   Email
    -   Delete (if draft)
-   Timeline of status changes
-   Link to converted invoice (if converted)

### 4. Print View

**File**: `resources/views/tenant/accounting/quotations/print.blade.php`

**Features**:

-   Professional print layout
-   Company logo and details
-   Customer details
-   Quotation number and dates
-   Line items table
-   Totals breakdown
-   Terms and conditions
-   Validity period
-   Print-friendly CSS

### 5. PDF Template

**File**: `resources/views/tenant/accounting/quotations/pdf.blade.php`

**Features**:

-   Similar to print view
-   Optimized for PDF generation
-   Professional formatting

## UI Updates

### 1. Update CRM More Actions Section

**File**: `resources/views/tenant/crm/partials/more-actions-section.blade.php`

**Changes** (Lines 140-153):

-   Update "New Quote" card href to actual route
-   Update "Quote List" card href to actual route

```blade
<!-- New Quote Card -->
<a href="{{ route('tenant.accounting.quotations.create', ['tenant' => $tenant->slug]) }}"
   class="action-card bg-gradient-to-br from-teal-600 to-teal-800...">
   ...
</a>

<!-- Quote List Card -->
<a href="{{ route('tenant.accounting.quotations.index', ['tenant' => $tenant->slug]) }}"
   class="action-card bg-gradient-to-br from-cyan-600 to-cyan-800...">
   ...
</a>
```

### 2. Add Quotations to Navigation

**File**: `resources/views/layouts/tenant.blade.php` (or navigation partial)

Add quotations link in accounting section:

-   Quotations
-   Create Quotation

### 3. Dashboard Widget

**File**: `resources/views/tenant/dashboard.blade.php`

Add quotations summary widget:

-   Total quotations this month
-   Pending quotations
-   Accepted quotations
-   Conversion rate

## Email Templates

### 1. Quotation Email

**File**: `resources/views/emails/quotation.blade.php`

**Content**:

-   Professional email template
-   Quotation details summary
-   Download PDF link
-   Accept/Reject buttons (optional)
-   Company branding

## Conversion Logic

### Convert Quotation to Invoice Process

1. **Validation**:

    - Check quotation status (must be 'sent' or 'accepted')
    - Check not already converted
    - Check not expired (optional warning)

2. **Create Invoice**:

    - Get appropriate voucher type (Sales)
    - Generate invoice number
    - Copy all quotation items
    - Copy customer/vendor details
    - Copy amounts and calculations
    - Set reference to quotation number
    - Create voucher entries
    - Update product stock

3. **Update Quotation**:

    - Set status to 'converted'
    - Set converted_to_invoice_id
    - Set converted_at timestamp

4. **Redirect**:
    - Redirect to new invoice with success message
    - Show link back to original quotation

## Status Flow

```
draft → sent → accepted → converted
              ↓
            rejected
              ↓
            expired (automatic based on expiry_date)
```

## Permissions

Add new permissions:

-   `quotations.view`
-   `quotations.create`
-   `quotations.edit`
-   `quotations.delete`
-   `quotations.convert`
-   `quotations.send`
-   `quotations.accept`
-   `quotations.reject`

## Notifications

### Email Notifications:

1. **Quotation Sent** - To customer
2. **Quotation Accepted** - To admin
3. **Quotation Rejected** - To admin
4. **Quotation Expiring Soon** - To admin (3 days before)
5. **Quotation Expired** - To admin

### System Notifications:

-   New quotation created
-   Quotation converted to invoice
-   Quotation accepted/rejected

## Reports

### Quotation Reports:

1. **Quotation Summary** - By status, period
2. **Conversion Rate** - Quotes to invoices
3. **Customer Quotations** - By customer
4. **Expired Quotations** - List of expired quotes
5. **Pending Quotations** - Awaiting response

## Additional Features

### 1. Quotation Templates

-   Save frequently used quotations as templates
-   Quick create from template

### 2. Quotation Versioning

-   Track revisions of quotations
-   Version history

### 3. Customer Portal

-   Customers can view quotations
-   Accept/reject online
-   Download PDF

### 4. Automated Follow-ups

-   Send reminders for pending quotations
-   Expiry warnings

### 5. Quotation Analytics

-   Conversion rates
-   Average quote value
-   Time to conversion
-   Win/loss analysis

## Testing Checklist

-   [ ] Create quotation with items
-   [ ] Edit draft quotation
-   [ ] Delete draft quotation
-   [ ] Mark as sent
-   [ ] Mark as accepted
-   [ ] Mark as rejected
-   [ ] Convert to invoice
-   [ ] Check invoice created correctly
-   [ ] Check stock updated after conversion
-   [ ] Print quotation
-   [ ] Download PDF
-   [ ] Email quotation
-   [ ] Duplicate quotation
-   [ ] Check expiry logic
-   [ ] Test with VAT
-   [ ] Test with discounts
-   [ ] Test with multiple items
-   [ ] Test customer search
-   [ ] Test product search
-   [ ] Check permissions
-   [ ] Test on mobile

## Implementation Order

1. **Phase 1: Core Functionality**

    - Create migrations
    - Create models
    - Create controller with basic CRUD
    - Create basic views (index, create, show)
    - Update navigation

2. **Phase 2: Conversion**

    - Implement convert to invoice
    - Test conversion logic
    - Update quotation status

3. **Phase 3: Enhanced Features**

    - Print and PDF generation
    - Email functionality
    - Status management (send, accept, reject)
    - Duplicate functionality

4. **Phase 4: Polish**

    - Add to dashboard
    - Create reports
    - Add notifications
    - Improve UI/UX
    - Add permissions

5. **Phase 5: Advanced Features**
    - Templates
    - Customer portal
    - Automated follow-ups
    - Analytics

## Notes

-   Quotations do NOT affect inventory or accounting until converted
-   Quotations are separate from vouchers system
-   Only converted quotations create vouchers
-   Maintain audit trail of all status changes
-   Allow editing only in draft status
-   Prevent deletion of converted quotations
-   Show clear indication when quotation is converted
-   Link between quotation and invoice should be bidirectional

## Files to Create/Modify

### New Files (23):

1. `database/migrations/2025_01_15_000001_create_quotations_table.php`
2. `database/migrations/2025_01_15_000002_create_quotation_items_table.php`
3. `database/migrations/2025_01_15_000003_add_quotation_id_to_vouchers_table.php`
4. `app/Models/Quotation.php`
5. `app/Models/QuotationItem.php`
6. `app/Http/Controllers/Tenant/Accounting/QuotationController.php`
7. `resources/views/tenant/accounting/quotations/index.blade.php`
8. `resources/views/tenant/accounting/quotations/create.blade.php`
9. `resources/views/tenant/accounting/quotations/edit.blade.php`
10. `resources/views/tenant/accounting/quotations/show.blade.php`
11. `resources/views/tenant/accounting/quotations/print.blade.php`
12. `resources/views/tenant/accounting/quotations/pdf.blade.php`
13. `resources/views/tenant/accounting/quotations/partials/quotation-items.blade.php`
14. `resources/views/tenant/accounting/quotations/partials/customer-info.blade.php`
15. `resources/views/tenant/accounting/quotations/partials/status-badge.blade.php`
16. `resources/views/emails/quotation.blade.php`
17. `app/Mail/QuotationMail.php`
18. `app/Notifications/QuotationSent.php`
19. `app/Notifications/QuotationAccepted.php`
20. `app/Notifications/QuotationRejected.php`
21. `app/Notifications/QuotationExpiring.php`
22. `database/seeders/QuotationPermissionsSeeder.php`
23. `tests/Feature/QuotationTest.php`

### Modified Files (3):

1. `routes/tenant.php` - Add quotation routes
2. `resources/views/tenant/crm/partials/more-actions-section.blade.php` - Update links
3. `resources/views/layouts/tenant.blade.php` - Add navigation items

## Estimated Time

-   Phase 1: 8-10 hours
-   Phase 2: 4-6 hours
-   Phase 3: 6-8 hours
-   Phase 4: 4-6 hours
-   Phase 5: 8-12 hours
-   **Total: 30-42 hours**

## Priority Features for MVP

1. ✅ Create quotation
2. ✅ List quotations
3. ✅ View quotation
4. ✅ Convert to invoice
5. ✅ Print/PDF
6. ✅ Basic status management
7. ✅ Email quotation

## Future Enhancements

-   Online acceptance/rejection
-   E-signature integration
-   Multi-currency support
-   Quotation comparison
-   Bulk operations
-   Advanced analytics
-   Integration with CRM
-   Mobile app support
