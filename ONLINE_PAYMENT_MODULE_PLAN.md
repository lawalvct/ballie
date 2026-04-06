# Ballie Online Payment Collection Module — Implementation Plan

## Overview

Convert the current always-on invoice payment link generation into a toggleable module called `online_payments`. When enabled, Ballie collects payments on behalf of tenants via Nomba/Paystack, holds funds in a virtual ledger, and tenants can request withdrawals to their bank accounts.

---

## What Already Exists (Can Reuse)

| Component | Status | Location |
|-----------|--------|----------|
| PayoutRequest model | Complete | `app/Models/PayoutRequest.php` |
| PayoutSetting model | Complete | `app/Models/PayoutSetting.php` |
| Payout migrations | Complete | `2026_02_02_000001`, `000002` |
| Super admin payout routes | Complete | `routes/super-admin.php` — approve/reject/complete |
| Ecommerce payout UI | Complete | `resources/views/tenant/ecommerce/payouts/` (index, create, show) |
| Payment gateways | Complete | `app/Helpers/PaymentHelper.php`, `PaystackPaymentHelper.php` |
| Payment callback handler | Complete | `app/Http/Controllers/PublicPaymentCallbackController.php` |
| Module toggle system | Complete | `app/Services/ModuleRegistry.php`, `app/Http/Middleware/CheckModuleAccess.php` |

---

## Implementation Tasks (8 Steps)

### Step 1: Register `online_payments` Module in ModuleRegistry

**File:** `app/Services/ModuleRegistry.php`

- Add `MODULE_ONLINE_PAYMENTS = 'online_payments'` constant
- Add to `ALL_MODULES` array
- Add metadata: `name: "Online Payments"`, `description: "Collect invoice payments via Nomba/Paystack with payout management"`, `icon: "fas fa-credit-card"`
- Add to `CATEGORY_DEFAULTS` for `trading` and `hybrid` categories
- **Not** added to `CORE_MODULES` — this is optional, user can disable it

---

### Step 2: Gate Payment Link Generation Behind Module Check

**File:** `app/Http/Controllers/Tenant/Accounting/InvoiceController.php`

- In `store()` (~line 594): wrap `$this->generatePaymentLinks(...)` call with:
  ```php
  if (ModuleRegistry::isModuleEnabled($tenant, 'online_payments')) {
      $this->generatePaymentLinks($voucher, $tenant, $request->customer_id);
  }
  ```
- In `regeneratePaymentLinks()`: add same check, return error flash if module disabled
- In `show.blade.php`: hide payment link section and regenerate button when module disabled

---

### Step 3: Create "Ballie Collections" Ledger Account on Module Activation

**File:** `app/Http/Controllers/Tenant/CompanySettingsController.php`

- In `updateModules()`: when `online_payments` is newly enabled, auto-create a ledger account:
  - **Name:** "Ballie Collections Account"
  - **Account Group:** BA (Bank Accounts) — same group as bank accounts
  - **Code:** "BALLIE-COL"
  - **Type:** asset, debit balance
  - Store the ledger account ID in `$tenant->settings['ballie_collections_account_id']`
- This ledger serves as the virtual "wallet" — money collected via payment links credits this account

---

### Step 4: Update Payment Callback to Record Against Ballie Collections Ledger

**File:** `app/Http/Controllers/PublicPaymentCallbackController.php`

- Currently, the callback creates a Receipt Voucher crediting customer account and debiting the **first bank account found**
- **Change:** When `online_payments` module is enabled, debit the **Ballie Collections Account** (from `$tenant->settings['ballie_collections_account_id']`) instead of a random bank account
- This way, money collected via online links goes to the Ballie Collections ledger, not the tenant's actual bank

---

### Step 5: Adapt `calculateAvailableBalance` for Invoice Payments

**File:** `app/Models/PayoutRequest.php`

- Currently uses `Order::where('payment_status', 'paid')->sum('total_amount')` (ecommerce only)
- Add alternative calculation that reads the **Ballie Collections Account** balance from the ledger
- Or combine: total revenue = ecommerce orders paid + invoice payments received via Ballie Collections
- The ledger balance approach is more accurate since it's already tracked via voucher entries

---

### Step 6: Create Accounting Payout Controller & Route Card

**New file:** `app/Http/Controllers/Tenant/Accounting/PayoutController.php`
**Reuse views:** Can adapt from or share with ecommerce payout views
**Routes in:** `routes/tenant.php`

Add route group under accounting prefix:
```php
Route::prefix('payouts')->name('payouts.')->middleware('module.access:online_payments')->group(function () {
    Route::get('/', [PayoutController::class, 'index'])->name('index');
    Route::get('/create', [PayoutController::class, 'create'])->name('create');
    Route::post('/', [PayoutController::class, 'store'])->name('store');
    Route::get('/calculate-deduction', [PayoutController::class, 'calculateDeduction'])->name('calculate-deduction');
    Route::get('/{payout}', [PayoutController::class, 'show'])->name('show');
    Route::patch('/{payout}/cancel', [PayoutController::class, 'cancel'])->name('cancel');
});
```

**More Actions card in** `resources/views/tenant/accounting/partials/more-actions-section.blade.php`:
- Add new card under a new "Online Payments" section (or under Account Management):
  - **Title:** "Payment Withdrawals"
  - **Description:** "Request withdrawal of collected online payments"
  - **Color:** gradient green-to-emerald
  - **Link:** `route('tenant.accounting.payouts.index')`
  - **Conditionally shown:** only when `online_payments` module is enabled

---

### Step 7: Add Payment Links to Invoice PDFs

**Files:** All 5 invoice PDF templates in `resources/views/tenant/accounting/invoices/templates/`

- When `online_payments` module is enabled and `$invoice->meta_data['payment_links']` is not empty:
  - Add a section below bank details showing:
    - **"Pay Online"** heading
    - Nomba payment link URL (text, since PDF can't have clickable links easily — or use QR code)
    - Paystack payment link URL
  - Optionally: generate a QR code pointing to the payment link (using a simple QR code library)
- Pass `$onlinePaymentsEnabled` flag from controller to PDF view

---

### Step 8: Invoice Show Page Module Gating

**File:** `resources/views/tenant/accounting/invoices/show.blade.php`

- Wrap the entire payment links section (Nomba/Paystack cards + regenerate button) in a module check
- When module is disabled, don't show payment link UI at all

---

## Data Flow Summary

```
Customer pays invoice via Nomba/Paystack link
    → PublicPaymentCallbackController verifies payment
    → Creates Receipt Voucher:
        Debit:  Ballie Collections Account (virtual wallet)
        Credit: Customer Account (reduces outstanding)
    → Ballie Collections Account balance increases

Tenant requests withdrawal:
    → PayoutController validates available balance
    → Creates PayoutRequest (status: pending)
    → Deduction calculated (e.g., 5% processing fee)

Super Admin processes payout:
    → Approves → Processing → Completed
    → Upon completion: create a Journal Voucher:
        Debit:  Ballie Collections Account (reduces balance)
        Credit: Tenant's actual bank ledger (or just mark as disbursed)
    → Or simply track via PayoutRequest status without journal entry
```

---

## Files to Create/Modify

| File | Action | Description |
|------|--------|-------------|
| `app/Services/ModuleRegistry.php` | Modify | Add `online_payments` module |
| `app/Http/Controllers/Tenant/Accounting/InvoiceController.php` | Modify | Gate payment links behind module |
| `app/Http/Controllers/Tenant/CompanySettingsController.php` | Modify | Create ledger on module activation |
| `app/Http/Controllers/PublicPaymentCallbackController.php` | Modify | Use Ballie Collections ledger |
| `app/Models/PayoutRequest.php` | Modify | Update balance calculation |
| `app/Http/Controllers/Tenant/Accounting/PayoutController.php` | **Create** | Accounting payout controller |
| `resources/views/tenant/accounting/payouts/index.blade.php` | **Create** | Payout dashboard |
| `resources/views/tenant/accounting/payouts/create.blade.php` | **Create** | Withdrawal request form |
| `resources/views/tenant/accounting/payouts/show.blade.php` | **Create** | Payout detail view |
| `resources/views/tenant/accounting/partials/more-actions-section.blade.php` | Modify | Add withdrawal card |
| `resources/views/tenant/accounting/invoices/show.blade.php` | Modify | Gate payment links UI |
| `resources/views/tenant/accounting/invoices/templates/ballie.blade.php` | Modify | Add pay online to PDF |
| `resources/views/tenant/accounting/invoices/templates/sage.blade.php` | Modify | Add pay online to PDF |
| `resources/views/tenant/accounting/invoices/templates/tally.blade.php` | Modify | Add pay online to PDF |
| `resources/views/tenant/accounting/invoices/templates/zoho.blade.php` | Modify | Add pay online to PDF |
| `resources/views/tenant/accounting/invoices/templates/quickbooks.blade.php` | Modify | Add pay online to PDF |
| `routes/tenant.php` | Modify | Add accounting payout routes |

---

## Key Design Decisions

1. **Ecommerce payout and accounting payout share the same `PayoutRequest` model** — same table, same super admin approval flow, just different source of funds.

2. **Ballie Collections ledger account is a `LedgerAccount` only** (not a full `Bank` model) — it's a virtual wallet, not a real bank account.

3. **QR codes on PDF** — nice-to-have, can add later with `simplesoftwareio/simple-qrcode` package.

4. **Journal entry on payout completion** — recommended: debit Ballie Collections, credit "Payout Disbursements" expense account to keep accounting clean.

---

## Implementation Order

1. **Step 1 + Step 2** — Register module + gate payment links (foundation)
2. **Step 3** — Create Ballie Collections ledger on activation
3. **Step 4** — Update callback to use Ballie Collections ledger
4. **Step 5** — Adapt available balance calculation
5. **Step 6** — Accounting payout controller, routes, views, more-actions card
6. **Step 7** — Payment links on invoice PDFs
7. **Step 8** — Show page module gating
