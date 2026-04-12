# Invoice Mobile Create Form Alignment Guide

## Purpose

This document is the UI handoff for the mobile developer to align the mobile invoice create/edit screen with the approved web invoice form.

It supplements the broader `INVOICE_MOBILE_FRONTEND_GUIDE.md` and focuses only on the create/edit experience.

## Web Source of Truth

Use these files as the reference for layout, totals behavior, and field placement:

- `resources/views/tenant/accounting/invoices/create.blade.php`
- `resources/views/tenant/accounting/invoices/partials/invoice-items.blade.php`
- `app/Http/Controllers/Tenant/Accounting/InvoiceController.php`
- `app/Http/Controllers/Api/Tenant/Accounting/InvoiceController.php`

## Main Correction Summary

The current mobile create screen is not aligned with the web invoice form.

The approved direction is:

- Do not place discount on each item card.
- Do not place VAT on each item card.
- Show VAT once, below items and below additional charges.
- Show additional charges as a separate section.
- Keep item cards simple: item selection, description, quantity, rate, amount.
- Keep totals visible as a summary block and sticky footer action area.

## What Must Change From The Current Mobile Screen

| Current mobile behavior | Required mobile behavior |
|---|---|
| Each item card shows `Discount %` | Remove per-item discount from the item card |
| Each item card shows `VAT %` | Remove per-item VAT from the item card |
| VAT appears to belong to each item | VAT must be one global summary block below Additional Charges |
| No clear Additional Charges section | Add a dedicated `Additional Charges (Optional)` section |
| Item card is too heavy with too many financial fields | Keep each item card compact and focused on selection plus price calculation |
| VAT Output is implied inside item flow | Show VAT posting note once below the VAT section |
| Totals are not structured like web | Show `Items Subtotal`, `Additional Charges`, `VAT`, and `Grand Total` |

## Required Mobile Screen Structure

Build the create/edit screen in this order:

### 1. Header

- Back button
- Screen title: `Sales Invoice`, `Purchase Invoice`, `Sales Return`, or `Purchase Return`
- Optional action icon if needed

### 2. Invoice Information Card

Fields:

- `voucher_type_id` dropdown
- `voucher_date`
- `reference_number` optional input
- Read-only invoice number preview

Rules:

- Invoice number is preview only and auto-generated on save
- Voucher type remains editable until save/post

### 3. Party Selection Card

For sales flows:

- Label should be `Client` or `Customer`

For purchase flows:

- Label should be `Vendor` or `Supplier`

Behavior:

- Searchable selector
- Selected state card showing name
- Show ledger/balance summary if available
- Include quick add button if already supported in mobile
- Allow clearing and reselecting party

### 4. Description / Notes

- One multiline field for `narration`
- Keep it before the items section, same as web

### 5. Invoice Items Section

Section header:

- `Invoice Items`
- `+ Add Item`
- `+ Add Line Item` if API support is available

Important:

- If the mobile app is still using the current API controller only, pure service/line-item submission is not yet fully aligned because the API still expects `items[*].product_id`.
- If that API limitation remains, keep `Add Item` only for now and do not fake service items with a product.

### 6. Additional Charges Section

- Title: `Additional Charges (Optional)`
- Button: `+ Add Charge`
- Each row should contain:
  - ledger account selector
  - amount
  - narration/description
  - remove button

Examples:

- Transport
- Delivery
- Handling
- Loading
- Other non-VAT adjustments that are handled as ledger charges

### 7. VAT Section

This section must appear below `Additional Charges`.

Fields:

- Toggle: `Add VAT (7.5%)`
- Option: `Items only`
- Option: `Items + Additional Charges`
- Read-only VAT amount

Helper text:

- For sales invoices: `VAT will be automatically posted to VAT Output account`
- For purchase invoices: `VAT will be automatically posted to VAT Input account`

Never do this:

- Do not show VAT percent input inside each item card
- Do not show `VAT Output` or `VAT Input` on each item row

### 8. Totals Summary Card

Show these rows in this order:

- `Items Subtotal`
- `Additional Charges` only when greater than zero
- `VAT` only when enabled
- `Grand Total`

### 9. Stock Warning Banner

Show a warning banner below the totals when:

- invoice type is sales, and
- any product quantity is greater than available stock

Per item, also show:

- current stock
- unit
- `Low!` indicator when quantity exceeds stock

### 10. Sticky Footer Actions

Keep the same actions as web:

- `Save Draft`
- `Save & Post`
- `Save, Post & New Sales`

Mobile presentation can be adapted for space:

- either three visible buttons, or
- one primary button plus a bottom sheet / overflow menu

Functional parity is required even if the exact button layout differs.

## Required Item Card Layout

Each item should be a compact stacked card.

### Product item card

Show:

- item number or small badge
- product selector/search field
- optional description field
- quantity input
- rate input
- calculated amount, read-only
- stock info line
- remove button

Do not show:

- discount percent
- VAT percent
- VAT Output label
- long accounting fields inside the item card

### Line item / service card

If enabled later, show:

- type badge: `Line Item`
- description field instead of product picker
- quantity
- rate
- calculated amount
- remove button

## Discount Handling Decision

This is important.

### Required now

- Remove item-level discount fields from the mobile form.
- Do not keep `Discount %` inside each item card.

### Placement rule

If discount is introduced later, it must sit in the summary area near `Additional Charges`, not inside individual items.

In other words:

- discount should behave like a summary adjustment row in placement
- discount should not be treated as item-level VAT or item-level price breakdown UI

### API note

The current mobile API still accepts `items[*].discount`, but that is a legacy payload shape and should not drive the new mobile UI.

Until backend/API support is formally agreed for summary discount, the safest release behavior is:

- hide discount from the mobile create/edit UI
- keep mobile aligned with the current web UX

## VAT Backend Handling (Important)

The backend now automatically resolves the VAT ledger account when the mobile sends `vat_enabled: true` and `vat_amount`.

- For **sales** invoices: backend picks ledger account `VAT-OUT-001` (VAT Output).
- For **purchase** invoices: backend picks ledger account `VAT-IN-001` (VAT Input).

The mobile frontend does **not** need to:

- look up or pass the VAT ledger account ID
- add VAT as an `additional_ledger_accounts` entry manually

Just send:

```json
{
  "vat_enabled": true,
  "vat_amount": 90000,
  "vat_applies_to": "items_only"
}
```

The backend appends the VAT entry to `additional_ledger_accounts` internally before creating accounting entries.

If `vat_applies_to` is omitted, it defaults to `"items_only"`.

## Calculation Rules

Use the same calculation flow as the web form.

```text
item_amount = quantity * rate

items_subtotal = sum(all item_amount)

additional_charges_total = sum(all additional charge amounts)

if vat_enabled and vat_applies_to = items_only:
    vat_amount = items_subtotal * 0.075

if vat_enabled and vat_applies_to = items_and_charges:
    vat_amount = (items_subtotal + additional_charges_total) * 0.075

grand_total = items_subtotal + additional_charges_total + vat_amount
```

Notes:

- Amount on each item is read-only
- VAT amount is read-only
- Grand total is read-only
- Recalculate instantly when quantity, rate, charges, or VAT option changes

## Mobile API Mapping

Use the API controller field names for the mobile app.

### Preferred field mapping

| Mobile UI field | Preferred API payload field | Notes |
|---|---|---|
| Voucher type | `voucher_type_id` | Required |
| Invoice date | `voucher_date` | Required, format `YYYY-MM-DD` |
| Party | `party_id` | Preferred API field |
| Reference number | `reference_number` | Optional |
| Notes | `narration` | Optional |
| Items | `items[]` | Preferred API field |
| Additional charges | `additional_ledger_accounts[]` | Use this for charge rows |
| VAT switch | `vat_enabled` | Boolean or boolean-like |
| VAT amount | `vat_amount` | Client calculates same as web |
| Save action | `status` | `draft`, `save_and_post`, or `save_and_post_new_sales` |

### Compatibility note

The API currently normalizes both old and new payload names. Mobile should still prefer the API-native keys below:

- use `party_id`, not `customer_id`
- use `items`, not `inventory_items`
- use `additional_ledger_accounts`, not `ledger_accounts`

## Endpoints Needed By Mobile

### Load create screen data

```text
GET /api/v1/tenant/{tenant}/accounting/invoices/create?type=sales
GET /api/v1/tenant/{tenant}/accounting/invoices/create?type=purchase
```

### Search party

```text
GET /api/v1/tenant/{tenant}/accounting/invoices/search-customers?search=abc&type=customer
GET /api/v1/tenant/{tenant}/accounting/invoices/search-customers?search=abc&type=vendor
```

### Search product

```text
GET /api/v1/tenant/{tenant}/accounting/invoices/search-products?search=rice&type=sales
GET /api/v1/tenant/{tenant}/accounting/invoices/search-products?search=rice&type=purchase
```

### Search additional charge ledger account

```text
GET /api/v1/tenant/{tenant}/accounting/invoices/search-ledger-accounts?search=transport
```

### Create invoice

```text
POST /api/v1/tenant/{tenant}/accounting/invoices
```

### Update invoice

```text
PUT /api/v1/tenant/{tenant}/accounting/invoices/{invoice}
```

## Recommended Payload Example

```json
{
  "voucher_type_id": 1,
  "voucher_date": "2026-04-12",
  "party_id": 49,
  "reference_number": "REF-2026-014",
  "narration": "Invoice for ABC company",
  "items": [
    {
      "product_id": 107,
      "description": "Bag of semo 25kg",
      "quantity": 1,
      "rate": 55000
    },
    {
      "product_id": 212,
      "description": "Bag of beans",
      "quantity": 5,
      "rate": 25000
    }
  ],
  "additional_ledger_accounts": [
    {
      "ledger_account_id": 33,
      "amount": 5000,
      "narration": "Transport"
    }
  ],
  "vat_enabled": true,
  "vat_amount": 13875,
  "status": "save_and_post"
}
```

## UX Notes For Mobile Developer

- Do not copy the desktop table literally; use clean stacked cards.
- Do not overload each item card with accounting controls.
- Keep summary logic below the items, not inside the items.
- If screen width is tight, use a sticky total bar plus bottom action sheet.
- Use clear section titles so users understand the order: party, notes, items, charges, VAT, totals.

## Suggested Mobile Wireframe

```text
[Back] Sales Invoice

[Invoice Information]
- Voucher Type
- Invoice Date
- Reference Number
- Invoice Number Preview

[Client / Vendor]
- Search / Select Party
- Quick Add
- Selected Party Summary

[Description / Notes]

[Invoice Items]
[+ Add Item] [+ Add Line Item]
- Item Card 1
- Item Card 2
- Items Subtotal

[Additional Charges]
[+ Add Charge]
- Charge Row 1

[VAT]
- Add VAT (7.5%)
- Items only / Items + Charges
- VAT amount
- VAT Output or VAT Input helper text

[Grand Total Summary]

[Stock Warning]

[Sticky Footer]
Total     Save Draft     Save & Post     More
```

## Acceptance Checklist

The mobile create/edit screen is only accepted when all items below are true:

- No per-item discount field is shown.
- No per-item VAT field is shown.
- Additional Charges is a separate section below items.
- VAT is a separate section below Additional Charges.
- VAT helper text appears once below VAT, not on each item.
- Each item only shows selection, description, quantity, rate, amount, and stock info.
- Amount and totals are read-only calculated values.
- Grand total matches the same order used on web.
- Stock warning appears for low stock sales items.
- Save Draft, Save & Post, and Save, Post & New Sales remain available.
- Edit invoice screen uses the same layout and calculations.

## Final Instruction

When there is any conflict between the current mobile screen and this document, follow this document and the web invoice form behavior.
