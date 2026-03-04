# Inertia.js + Vue 3 Migration Plan

**Ballie SaaS — Blade → Inertia.js + Vue 3 + Pinia**
_Drafted: March 3, 2026 | Status: Pending_

---

## Strategy: SPA Per Module

Full page reload is acceptable when a user navigates **between modules** (e.g., Accounting → Inventory). Within a module, navigation is SPA (no page reload). This dramatically reduces complexity — each module becomes its own self-contained Vue SPA island, sharing a common layout shell.

```
/law-venture/accounting/invoices         → Vue SPA (no reload within accounting)
/law-venture/accounting/ledger-accounts  → Vue SPA (same app instance)
/law-venture/inventory/products          → Full reload from accounting (new module)
/law-venture/inventory/categories        → Vue SPA (no reload within inventory)
```

This means each module can be migrated independently with zero risk to other modules.

---

## Current Stack

| Item          | Detail                                                                   |
| ------------- | ------------------------------------------------------------------------ |
| PHP Framework | Laravel 10                                                               |
| Templating    | Blade (468 templates)                                                    |
| Frontend JS   | Alpine.js 3.x (30+ components)                                           |
| CSS           | Tailwind CSS 3.x                                                         |
| Charts        | Chart.js 4.x                                                             |
| Build tool    | Vite 5                                                                   |
| Auth          | Session (web), Sanctum (API), separate guards for super_admin & customer |
| Multi-tenancy | Path-based `/{tenant}/...` via `spatie/laravel-multitenancy`             |
| PDF           | DomPDF (18 Blade PDF templates — stay server-side forever)               |
| Mobile API    | Laravel Sanctum — already covers ~52% of modules                         |

---

## Target Stack (additions only)

| Package                     | Purpose                                                       |
| --------------------------- | ------------------------------------------------------------- |
| `inertiajs/inertia-laravel` | Server-side Inertia adapter                                   |
| `@inertiajs/vue3`           | Client-side Inertia adapter for Vue                           |
| `vue@3`                     | Vue 3 framework                                               |
| `pinia`                     | Vue state management (replaces Alpine cross-component hacks)  |
| `@vitejs/plugin-vue`        | Vite plugin to compile `.vue` files                           |
| `tightenco/ziggy`           | Laravel route helper in JS (for tenant-scoped URL generation) |

Alpine.js stays installed and keeps working on all unconverted Blade pages.

---

## Phase 0: Foundation Setup

**Estimate: 3–5 days**
**Risk: Low** — no user-facing changes

### Backend

- [ ] Install `inertiajs/inertia-laravel` via Composer
- [ ] Create `app/Http/Middleware/HandleInertiaRequests.php`
    - Share `tenant` (slug + name + id) as default prop on all Inertia responses
    - Share `auth.user` (id, name, email, role) for all guards
    - Share `flash` (success, error, warning) from session
    - Share `ziggy` routes
    - Set default root view to `layouts.tenant-inertia`
- [ ] Register `HandleInertiaRequests` in `app/Http/Kernel.php` inside the `web` middleware group (after `SubstituteBindings`)
- [ ] Install `tightenco/ziggy` via Composer and add `@routes` to the Inertia root layout

### Frontend

- [ ] `npm install @inertiajs/vue3 vue@3 pinia @vitejs/plugin-vue`
- [ ] Update `vite.config.js`:
    ```js
    import vue from "@vitejs/plugin-vue";
    // add vue() to plugins array alongside laravel()
    ```
- [ ] Create `resources/js/inertia-app.js` — entry point that boots Vue 3 + Inertia + Pinia
- [ ] Update `resources/js/app.js` — keep Alpine.js boot unchanged (for Blade pages)

### Layout & Root Views

- [ ] Create `resources/views/layouts/tenant-inertia.blade.php` — minimal root layout with `@inertia` directive, Vite assets, and `@routes` (Ziggy)
- [ ] Create `resources/js/Layouts/TenantLayout.vue` — Vue component containing sidebar + header + flash notifications (ported from `resources/views/layouts/tenant/sidebar.blade.php` and `header.blade.php`)
- [ ] Create `resources/js/Layouts/GuestLayout.vue` — for login/register pages once migrated

### Pinia Stores (global)

- [ ] `resources/js/stores/useTenantStore.js` — tenant slug, name, id, settings
- [ ] `resources/js/stores/useAuthStore.js` — current user, guard type, permissions
- [ ] `resources/js/stores/useNotificationStore.js` — unread count, notification list

### Reusable Base Components

- [ ] `<AppButton>` — primary/secondary/danger variants
- [ ] `<AppModal>` — accessible modal with slot-based content
- [ ] `<FlashMessage>` — success/error/warning banners (reads Pinia store or Inertia shared props)
- [ ] `<Pagination>` — Inertia-aware paginator (preserves query params)
- [ ] `<DataTable>` — sortable/filterable table shell
- [ ] `<SearchInput>` — debounced search field that drives Inertia visits

### Proof of Concept Verification

- [ ] Create `resources/js/Pages/Tenant/Dashboard.vue` (temporary, minimal — just render `Hello World` + tenant name)
- [ ] Convert `DashboardController::index()` to `Inertia::render('Tenant/Dashboard', [...])`
- [ ] Confirm: Vue page renders, sidebar works, Blade pages (accounting, inventory, etc.) still load via full reload
- [ ] Confirm: all 3 auth guards pass the correct user to `auth.user` shared prop
- [ ] Confirm: tenant slug in URL correctly resolves and is available in Vue as `useTenantStore().slug`
- [ ] Revert Dashboard to Blade after confirmation (or keep as first converted page)

---

## Phase 1: Accounting Module — SPA

**Estimate: 6–8 weeks**
**Scope:** All pages under `/{tenant}/accounting/...` become a Vue SPA

This is the most business-critical module and the highest-value target. Once done, users get instant navigation through invoices, vouchers, ledger accounts, and reports without any page reload.

### Index / List Pages

- [ ] `Pages/Tenant/Accounting/Invoices/Index.vue`
- [ ] `Pages/Tenant/Accounting/Invoices/Show.vue`
- [ ] `Pages/Tenant/Accounting/Quotations/Index.vue`
- [ ] `Pages/Tenant/Accounting/Quotations/Show.vue`
- [ ] `Pages/Tenant/Accounting/Vouchers/Index.vue`
- [ ] `Pages/Tenant/Accounting/Vouchers/Show.vue`
- [ ] `Pages/Tenant/Accounting/LedgerAccounts/Index.vue`
- [ ] `Pages/Tenant/Accounting/LedgerAccounts/Show.vue` (with statement tab)
- [ ] `Pages/Tenant/Accounting/AccountGroups/Index.vue`
- [ ] `Pages/Tenant/Accounting/VoucherTypes/Index.vue`

### Create / Edit Forms

- [ ] `Pages/Tenant/Accounting/Invoices/Create.vue` / `Edit.vue`
    - **Most complex** — 2,716-line Blade template. Dynamic line items, product/customer AJAX search, AI invoice parsing, inline product creation, tax calculations.
    - Pinia store: `useInvoiceFormStore` (line items, totals, selected customer/vendor)
- [ ] `Pages/Tenant/Accounting/Quotations/Create.vue` / `Edit.vue`
- [ ] `Pages/Tenant/Accounting/Vouchers/Create.vue` / `Edit.vue`
    - 7 voucher type sub-forms (payment, receipt, contra, debit note, credit note, inventory, journal)
    - Pinia store: `useVoucherFormStore` (entries, selected accounts, running total)
- [ ] `Pages/Tenant/Accounting/LedgerAccounts/Create.vue` / `Edit.vue`
- [ ] `Pages/Tenant/Accounting/AccountGroups/Create.vue` / `Edit.vue`
- [ ] `Pages/Tenant/Accounting/VoucherTypes/Create.vue` / `Edit.vue`

### Accounting-Specific Components

- [ ] `<LineItemRow>` — invoice/quotation line item with product search, quantity, price, tax
- [ ] `<VoucherEntryRow>` — debit/credit entry row with ledger account autocomplete
- [ ] `<AccountSearch>` — async ledger account search dropdown
- [ ] `<CustomerSearch>` / `<VendorSearch>` — async customer/vendor search dropdown
- [ ] `<ProductSearch>` — async product search with price/unit auto-fill
- [ ] `<InvoiceTotals>` — subtotal/tax/discount/total summary block
- [ ] `<AIInvoiceParser>` — text input → AI parse → preview → apply to form
- [ ] `<InvoiceStatusBadge>` — draft/posted/paid/partial badge

### Pinia Stores (Accounting)

- [ ] `useInvoiceFormStore` — line items array, customer, voucher type, totals, AI parse state
- [ ] `useVoucherFormStore` — entries array, voucher type, narration, attachments

### PDF / Print (keep server-side)

No migration needed. Invoice PDF, voucher PDF, quotation PDF remain as Blade DomPDF templates served as download/print links from Vue pages via standard `<a href>` or `window.open()`.

---

## Phase 2: Inventory Module — SPA

**Estimate: 4–5 weeks**
**Scope:** All pages under `/{tenant}/inventory/...`

### Pages

- [ ] `Pages/Tenant/Inventory/Products/Index.vue` — with bulk actions, import/export
- [ ] `Pages/Tenant/Inventory/Products/Show.vue` — stock movements tab
- [ ] `Pages/Tenant/Inventory/Products/Create.vue` / `Edit.vue` — image uploads, category/unit inline creation
- [ ] `Pages/Tenant/Inventory/Categories/Index.vue` + CRUD
- [ ] `Pages/Tenant/Inventory/Units/Index.vue` + CRUD
- [ ] `Pages/Tenant/Inventory/StockJournal/Index.vue` + CRUD — production, transfer, adjustment sub-forms
- [ ] `Pages/Tenant/Inventory/PhysicalStock/Index.vue` + CRUD — product search with live stock levels

### Components

- [ ] `<ProductImageUpload>` — multi-image upload with preview/delete
- [ ] `<StockEntryRow>` — product search + quantity + unit
- [ ] `<StockBadge>` — in-stock/low-stock/out-of-stock indicator

---

## Phase 3: CRM Module — SPA

**Estimate: 3–4 weeks**
**Scope:** All pages under `/{tenant}/crm/...`

### Pages

- [ ] `Pages/Tenant/Crm/Customers/Index.vue` + Show + CRUD + Statement
- [ ] `Pages/Tenant/Crm/Vendors/Index.vue` + Show + CRUD
- [ ] `Pages/Tenant/Crm/Activities/Index.vue` + CRUD
- [ ] `Pages/Tenant/Crm/PaymentReminders/Index.vue`
- [ ] `Pages/Tenant/Crm/Dashboard.vue`

---

## Phase 4: Payroll Module — SPA

**Estimate: 5–6 weeks**
**Scope:** All pages under `/{tenant}/payroll/...`
**Note:** Largest module by feature surface — employees, attendance, processing, reports, loans, shifts, announcements

### Key Pages

- [ ] Employees CRUD + documents + salary components
- [ ] Departments + Positions CRUD
- [ ] Shifts + Assignments
- [ ] Attendance management (clock in/out, calendar view)
- [ ] Overtime approval list
- [ ] Leave requests + approval
- [ ] Payroll processing (run payroll, approve, mark paid)
- [ ] Payslips list + download links
- [ ] Loans CRUD + approval
- [ ] Salary advance form
- [ ] Reports (payroll summary, bank schedule, overtime, tax)
- [ ] Announcements CRUD
- [ ] Settings form

---

## Phase 5: Reports Module — SPA

**Estimate: 3–4 weeks**
**Scope:** All pages under `/{tenant}/reports/...` and `/{tenant}/accounting/` financial report pages

### Pages

- [ ] Financial reports: Profit & Loss, Balance Sheet, Trial Balance, Cash Flow
- [ ] Sales reports: Summary, By Customer, By Product, By Period
- [ ] Purchase reports: Summary, By Vendor, By Product
- [ ] Inventory reports: Stock Summary, Low Stock, Valuation, Bin Card
- [ ] CRM reports: Customer analysis, Payment reports
- [ ] POS reports: Daily sales, Monthly sales, Top products

### Components

- [ ] `<ReportFilter>` — date range, comparison period, group-by selectors
- [ ] `<ChartCard>` — Chart.js wrapper Vue component (line, bar, doughnut, pie)
- [ ] `<ReportTable>` — sortable report table with running totals
- [ ] `<ExportButtons>` — PDF/Excel download button group

---

## Phase 6: POS Module — SPA

**Estimate: 3–4 weeks**
**Scope:** `/{tenant}/pos/...`

The POS terminal is the most interactive page in the entire app — product grid + cart + payment modal. It's currently a large Alpine.js component (`posSystem()`). This is a great candidate for Vue + Pinia.

### Pages

- [ ] `Pages/Tenant/Pos/Terminal.vue` — full POS interface
- [ ] `Pages/Tenant/Pos/Transactions/Index.vue` + Show
- [ ] `Pages/Tenant/Pos/Reports.vue`

### Pinia Stores (POS)

- [ ] `usePosStore` — cart items, active session, cash register, payment state, receipt data

---

## Phase 7: Banking Module — SPA

**Estimate: 2–3 weeks**
**Scope:** `/{tenant}/banking/...`

- [ ] Banks CRUD + Statement view
- [ ] Bank Reconciliation CRUD + reconcile interface

---

## Phase 8: Admin Module — SPA

**Estimate: 2–3 weeks**
**Scope:** `/{tenant}/admin/...`

- [ ] Users management (CRUD, invite, reset password, suspend)
- [ ] Roles + Permissions matrix
- [ ] Teams CRUD
- [ ] Security settings
- [ ] Activity log
- [ ] System info

---

## Phase 9: Settings Module — SPA

**Estimate: 2 weeks**
**Scope:** `/{tenant}/settings/...`

- [ ] General, Financial, Tax, Email settings forms
- [ ] Company settings (logo upload, business details)
- [ ] Cash register management
- [ ] Notification preferences

---

## Phase 10: Storefront — SPA (Optional)

**Estimate: 3–4 weeks**
**Note:** Consider Inertia SSR mode for this phase (SEO benefit for public product pages)

- [ ] Product catalog + product detail
- [ ] Shopping cart
- [ ] Checkout + payment (Nomba)
- [ ] Customer auth (login, register, Google OAuth)
- [ ] Customer account: orders, invoices, profile, disputes

---

## Phase 11: Super Admin Panel — SPA (Optional)

**Estimate: 3–4 weeks**
**Scope:** `/super-admin/...`

- [ ] Tenant management (list, show, suspend, activate, impersonate)
- [ ] Plans + subscriptions management
- [ ] Affiliate management + payouts
- [ ] Support tickets
- [ ] Email management
- [ ] Backup management
- [ ] Platform analytics

---

## Phase 12: Employee Self-Service Portal — SPA (Optional)

**Estimate: 1–2 weeks**
**Scope:** `/employee-portal/{token}/...`
**Note:** Token-based auth (no standard login). Pass token as shared prop.

- [ ] Dashboard + payslips
- [ ] Tax certificate download
- [ ] Attendance view
- [ ] Loan history
- [ ] Profile update

---

## Data Flow: Blade → Inertia Pattern

**Before (Blade):**

```php
// Controller
return view('tenant.accounting.invoices.index', compact('invoices', 'tenant'));

// Blade template
@foreach($invoices as $invoice)
    <tr>{{ $invoice->number }}</tr>
@endforeach
```

**After (Inertia):**

```php
// Controller — only change return statement
return Inertia::render('Tenant/Accounting/Invoices/Index', [
    'invoices' => InvoiceResource::collection($invoices),
]);
// $tenant is shared automatically via HandleInertiaRequests
```

```vue
<!-- Vue component receives data as props -->
<script setup>
const props = defineProps({ invoices: Object });
</script>
<template>
    <tr v-for="invoice in invoices.data" :key="invoice.id">
        {{
            invoice.number
        }}
    </tr>
</template>
```

---

## State Management: Alpine → Pinia Pattern

**Before (Alpine.js inline):**

```js
// Inline in Blade template, server data injected via @json()
function invoiceForm() {
    return {
        items: @json($items ?? []),
        totalAmount: 0,
        addItem() { this.items.push({...}) },
    }
}
```

**After (Pinia store):**

```js
// stores/useInvoiceFormStore.js
export const useInvoiceFormStore = defineStore("invoiceForm", {
    state: () => ({ items: [], totalAmount: 0 }),
    actions: {
        addItem(item) {
            this.items.push(item);
        },
        // Server data comes from Inertia props, not @json()
    },
});
```

---

## Migration Rules

1. **Never break a Blade page when migrating a Vue page** — they coexist and both must work
2. **Links between unconverted modules use `<a href>`** (full reload) — not Inertia `<Link>`
3. **Links within the same converted module use `<Link>`** (SPA navigation)
4. **PDF templates stay as Blade forever** — DomPDF is server-side only, Vue pages link to them as `<a href="/download-pdf">`
5. **Forms use Inertia's `useForm()` composable** — handles validation errors, loading states, redirect after submit
6. **No API endpoint needed** — Inertia posts to the same Laravel route used by Blade forms (same controller, same validation)
7. **Alpine.js is not removed** until every page in a module is converted to Vue
8. **Shared props (tenant, auth.user, flash)** are always available in every Vue page — never pass them as page props manually

---

## Rollout Order (Recommended Priority)

| Priority | Module                     | Reason                                                    |
| -------- | -------------------------- | --------------------------------------------------------- |
| 1        | **Foundation (Phase 0)**   | Unlocks everything                                        |
| 2        | **Accounting (Phase 1)**   | Highest user activity, most complex forms, biggest UX win |
| 3        | **Inventory (Phase 2)**    | High daily use, simpler forms                             |
| 4        | **CRM (Phase 3)**          | Medium complexity                                         |
| 5        | **Payroll (Phase 4)**      | Large feature set, high impact for HR users               |
| 6        | **Reports (Phase 5)**      | Read-only pages — fastest to migrate                      |
| 7        | **POS (Phase 6)**          | Complex but isolated, high UX value                       |
| 8        | **Banking (Phase 7)**      | Low frequency, moderate complexity                        |
| 9        | **Admin (Phase 8)**        | Low frequency                                             |
| 10       | **Settings (Phase 9)**     | Low frequency                                             |
| 11       | Storefront (Phase 10)      | Optional — consider SSR                                   |
| 12       | Super Admin (Phase 11)     | Optional                                                  |
| 13       | Employee Portal (Phase 12) | Optional                                                  |

---

## Total Estimate

| Phase                   | Estimate                      |
| ----------------------- | ----------------------------- |
| Phase 0: Foundation     | 3–5 days                      |
| Phase 1: Accounting     | 6–8 weeks                     |
| Phase 2: Inventory      | 4–5 weeks                     |
| Phase 3: CRM            | 3–4 weeks                     |
| Phase 4: Payroll        | 5–6 weeks                     |
| Phase 5: Reports        | 3–4 weeks                     |
| Phase 6: POS            | 3–4 weeks                     |
| Phase 7: Banking        | 2–3 weeks                     |
| Phase 8: Admin          | 2–3 weeks                     |
| Phase 9: Settings       | 2 weeks                       |
| **Total (Phases 0–9)**  | **~7–9 months (1 developer)** |
| Optional phases (10–12) | +2–3 months                   |

---

## Key References

- [Inertia.js docs](https://inertiajs.com)
- [Vue 3 docs](https://vuejs.org)
- [Pinia docs](https://pinia.vuejs.org)
- [Ziggy (Laravel routes in JS)](https://github.com/tightenco/ziggy)
- [inertiajs/inertia-laravel](https://github.com/inertiajs/inertia-laravel)
- [routes/tenant.php](routes/tenant.php) — all web routes to migrate
- [routes/api/v1/tenant.php](routes/api/v1/tenant.php) — mobile API (reference for data shapes)
