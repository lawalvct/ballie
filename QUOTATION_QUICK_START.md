# Quotation Feature - Quick Start Guide

## ✅ What's Been Created

### Database Migrations (Ready to Run)

1. ✅ `2025_01_15_000001_create_quotations_table.php`
2. ✅ `2025_01_15_000002_create_quotation_items_table.php`
3. ✅ `2025_01_15_000003_add_quotation_id_to_vouchers_table.php`

### Models (Complete)

1. ✅ `app/Models/Quotation.php` - Full quotation model with all methods
2. ✅ `app/Models/QuotationItem.php` - Quotation line items model

### Documentation

1. ✅ `QUOTATION_IMPLEMENTATION_PLAN.md` - Detailed 400+ line implementation guide
2. ✅ `QUOTATION_FEATURE_SUMMARY.md` - Executive summary
3. ✅ `QUOTATION_QUICK_START.md` - This file

## 🚀 Next Steps to Complete Implementation

### Step 1: Run Migrations

```bash
php artisan migrate
```

This will create the `quotations` and `quotation_items` tables and add `quotation_id` to the `vouchers` table.

### Step 2: Create the Controller

You need to create: `app/Http/Controllers/Tenant/Accounting/QuotationController.php`

**Key Methods Needed:**

-   `index()` - List quotations
-   `create()` - Show create form
-   `store()` - Save new quotation
-   `show()` - View quotation
-   `edit()` - Edit form
-   `update()` - Update quotation
-   `destroy()` - Delete quotation
-   `convertToInvoice()` - Convert to invoice ⭐
-   `print()` - Print view
-   `pdf()` - Generate PDF
-   `email()` - Email quotation

**Reference:** Look at `app/Http/Controllers/Tenant/Accounting/InvoiceController.php` for patterns.

### Step 3: Add Routes

Update `routes/tenant.php`:

```php
Route::prefix('accounting')->name('accounting.')->group(function () {
    // ... existing routes ...

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
    Route::get('quotations/{quotation}/print', [QuotationController::class, 'print'])
        ->name('quotations.print');
    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])
        ->name('quotations.pdf');
    Route::post('quotations/{quotation}/email', [QuotationController::class, 'email'])
        ->name('quotations.email');
});
```

### Step 4: Create Views

Create these blade files in `resources/views/tenant/accounting/quotations/`:

1. **index.blade.php** - List all quotations
2. **create.blade.php** - Create new quotation
3. **show.blade.php** - View quotation details
4. **edit.blade.php** - Edit quotation
5. **print.blade.php** - Print layout
6. **pdf.blade.php** - PDF template

**Reference:** Look at `resources/views/tenant/accounting/invoices/` for patterns.

### Step 5: Update UI Links

Update `resources/views/tenant/crm/partials/more-actions-section.blade.php` (lines 145 and 161):

```blade
<!-- Line 145: New Quote Card -->
<a href="{{ route('tenant.accounting.quotations.create', ['tenant' => $tenant->slug]) }}"
   class="action-card bg-gradient-to-br from-teal-600 to-teal-800...">

<!-- Line 161: Quote List Card -->
<a href="{{ route('tenant.accounting.quotations.index', ['tenant' => $tenant->slug]) }}"
   class="action-card bg-gradient-to-br from-cyan-600 to-cyan-800...">
```

## 🎯 Key Features Already Implemented in Models

### Quotation Model Methods

-   ✅ `getQuotationNumber()` - Format: QT-0001
-   ✅ `isExpired()` - Check if expired
-   ✅ `canBeConverted()` - Check if can convert to invoice
-   ✅ `canBeEdited()` - Check if can edit
-   ✅ `canBeDeleted()` - Check if can delete
-   ✅ `canBeSent()` - Check if can send
-   ✅ `markAsSent()` - Mark as sent
-   ✅ `markAsAccepted()` - Mark as accepted
-   ✅ `markAsRejected()` - Mark as rejected
-   ✅ `markAsExpired()` - Mark as expired
-   ✅ `calculateTotals()` - Recalculate all totals
-   ✅ `convertToInvoice()` - **Full conversion logic implemented!**
-   ✅ `getStatusColor()` - Get badge color
-   ✅ `getStatusLabel()` - Get status label

### Quotation Model Scopes

-   ✅ `active()` - Not expired/converted
-   ✅ `pending()` - Sent status
-   ✅ `expired()` - Expired quotes
-   ✅ `draft()` - Draft quotes

### Quotation Model Relationships

-   ✅ `tenant()` - Belongs to tenant
-   ✅ `customer()` - Belongs to customer
-   ✅ `vendor()` - Belongs to vendor
-   ✅ `customerLedger()` - Ledger account
-   ✅ `convertedToInvoice()` - Linked invoice
-   ✅ `createdBy()` - Creator user
-   ✅ `updatedBy()` - Last updater
-   ✅ `items()` - Has many items

### QuotationItem Model

-   ✅ Auto-calculates `amount` (quantity × rate)
-   ✅ Auto-calculates `total` (with tax/discount)
-   ✅ `calculateTotal()` method
-   ✅ Relationships to quotation and product

## 💡 How Conversion Works

The `convertToInvoice()` method in the Quotation model:

1. ✅ Validates quotation can be converted
2. ✅ Gets sales voucher type
3. ✅ Generates new invoice number
4. ✅ Creates voucher with all items
5. ✅ Creates invoice items
6. ✅ Creates accounting entries (debit customer, credit sales)
7. ✅ Updates product stock
8. ✅ Updates quotation status to 'converted'
9. ✅ Links quotation to invoice bidirectionally
10. ✅ Full transaction with rollback on error

**Usage in Controller:**

```php
public function convertToInvoice(Tenant $tenant, Quotation $quotation)
{
    try {
        $invoice = $quotation->convertToInvoice();

        if (!$invoice) {
            return redirect()->back()
                ->with('error', 'Quotation cannot be converted.');
        }

        return redirect()
            ->route('tenant.accounting.invoices.show', [
                'tenant' => $tenant->slug,
                'invoice' => $invoice->id
            ])
            ->with('success', 'Quotation converted to invoice successfully!');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error converting quotation: ' . $e->getMessage());
    }
}
```

## 📊 Status Flow

```
draft → sent → accepted → converted
              ↓
            rejected
              ↓
            expired
```

### Status Rules

-   **Draft**: Can edit, can delete, can send
-   **Sent**: Can accept, can reject, can convert
-   **Accepted**: Can convert
-   **Rejected**: Cannot convert
-   **Expired**: Cannot convert
-   **Converted**: Cannot edit, cannot delete, cannot convert again

## 🎨 Status Badge Colors

```php
'draft' => 'gray'
'sent' => 'blue'
'accepted' => 'green'
'rejected' => 'red'
'expired' => 'yellow'
'converted' => 'purple'
```

## 📝 Example Controller Store Method

```php
public function store(Request $request, Tenant $tenant)
{
    $validated = $request->validate([
        'quotation_date' => 'required|date',
        'expiry_date' => 'nullable|date|after:quotation_date',
        'customer_ledger_id' => 'required|exists:ledger_accounts,id',
        'subject' => 'nullable|string|max:255',
        'terms_and_conditions' => 'nullable|string',
        'notes' => 'nullable|string',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.rate' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();
    try {
        // Generate quotation number
        $lastQuotation = Quotation::where('tenant_id', $tenant->id)
            ->latest('id')
            ->first();
        $nextNumber = $lastQuotation ? $lastQuotation->quotation_number + 1 : 1;

        // Create quotation
        $quotation = Quotation::create([
            'tenant_id' => $tenant->id,
            'quotation_number' => $nextNumber,
            'quotation_date' => $validated['quotation_date'],
            'expiry_date' => $validated['expiry_date'],
            'customer_ledger_id' => $validated['customer_ledger_id'],
            'subject' => $validated['subject'],
            'terms_and_conditions' => $validated['terms_and_conditions'],
            'notes' => $validated['notes'],
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        // Create items
        foreach ($validated['items'] as $index => $item) {
            $product = Product::find($item['product_id']);

            $quotation->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'description' => $item['description'] ?? $product->description,
                'quantity' => $item['quantity'],
                'unit' => $product->primaryUnit->symbol ?? 'Pcs',
                'rate' => $item['rate'],
                'discount' => $item['discount'] ?? 0,
                'tax' => $item['tax'] ?? 0,
                'is_tax_inclusive' => $item['is_tax_inclusive'] ?? false,
                'sort_order' => $index,
            ]);
        }

        // Calculate totals
        $quotation->load('items');
        $quotation->calculateTotals();
        $quotation->save();

        DB::commit();

        return redirect()
            ->route('tenant.accounting.quotations.show', [
                'tenant' => $tenant->slug,
                'quotation' => $quotation->id
            ])
            ->with('success', 'Quotation created successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating quotation: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'Error creating quotation.')
            ->withInput();
    }
}
```

## 🔍 Testing Checklist

After implementation, test:

-   [ ] Create quotation with items
-   [ ] View quotation list
-   [ ] View quotation details
-   [ ] Edit draft quotation
-   [ ] Delete draft quotation
-   [ ] Mark as sent
-   [ ] Mark as accepted
-   [ ] Mark as rejected
-   [ ] Convert to invoice
-   [ ] Verify invoice created correctly
-   [ ] Verify stock updated
-   [ ] Verify accounting entries
-   [ ] Print quotation
-   [ ] Download PDF
-   [ ] Email quotation
-   [ ] Check expiry logic
-   [ ] Test with VAT
-   [ ] Test with discounts

## 📚 Additional Resources

-   **Full Implementation Plan**: `QUOTATION_IMPLEMENTATION_PLAN.md`
-   **Feature Summary**: `QUOTATION_FEATURE_SUMMARY.md`
-   **Invoice Controller Reference**: `app/Http/Controllers/Tenant/Accounting/InvoiceController.php`
-   **Invoice Views Reference**: `resources/views/tenant/accounting/invoices/`

## ⚠️ Important Notes

1. **Quotations don't affect accounting** until converted
2. **Stock is not affected** until converted to invoice
3. **Only draft quotations** can be edited or deleted
4. **Converted quotations** are permanently linked to invoices
5. **Expiry date** is optional but recommended
6. **Status changes** are tracked with timestamps

## 🎉 You're Ready!

The foundation is complete. Follow the steps above to finish the implementation. The models handle all the complex logic, so your controller and views can focus on user interaction.

Good luck! 🚀
