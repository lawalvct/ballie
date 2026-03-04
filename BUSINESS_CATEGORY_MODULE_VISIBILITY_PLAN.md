# Business Category & Module Visibility Plan

**Ballie SaaS — Making the App Work for Every Business Type**
_Drafted: March 3, 2026 | Status: Pending_

---

## The Problem

Ballie currently shows **all 16 sidebar modules** to every tenant — a consultancy firm sees Inventory, POS, E-commerce, and Manufacturing features they'll never use. This makes the app feel overwhelming and irrelevant for non-trading businesses.

A user registering a **law firm** should not see the same dashboard as a **supermarket**. Right now, both get:

- Inventory (useless for services)
- POS (a law firm doesn't sell at a counter)
- E-commerce (a consulting firm doesn't need a storefront)
- Products with stock tracking (services aren't stocked items)

**Result:** Users perceive Ballie as "not for my business type" and churn.

---

## The Solution: Business Categories → Module Profiles

Instead of 97 granular business types trying to map individually to features, we introduce **4 Business Categories** that each define a **module profile** — which modules are visible and which are hidden by default.

### The 4 Categories

| Category          | Description                                                           | Example Businesses                                                                                                              |
| ----------------- | --------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------- |
| **Trading**       | Businesses that buy and sell physical goods                           | Retail stores, wholesalers, supermarkets, e-commerce, auto sales, electronics stores                                            |
| **Manufacturing** | Businesses that produce/transform raw materials into finished goods   | Factories, bakeries, fabrication, food processing, packaging, construction                                                      |
| **Service**       | Businesses that sell expertise, time, or labor (no physical products) | Law firms, consultancies, marketing agencies, IT companies, accounting firms, healthcare                                        |
| **Hybrid**        | Businesses that combine trading/manufacturing with services           | Car dealerships + repairs, IT companies + hardware sales, restaurants (food production + service), salons (products + services) |

### How Existing 97 Business Types Map to Categories

Each of the 97 `BusinessType` records will get a new `business_category` field:

| Existing Business Type Category          | Maps To                                  |
| ---------------------------------------- | ---------------------------------------- |
| Retail, Commerce & Sales                 | **Trading**                              |
| Professional & Service-Based             | **Service**                              |
| Food & Hospitality                       | **Hybrid** (restaurants produce + serve) |
| Industrial, Manufacturing & Construction | **Manufacturing**                        |
| Agriculture, Agro & Natural Resources    | **Manufacturing**                        |
| Transport, Logistics & Mobility          | **Service**                              |
| Finance, Technology & Innovation         | **Service**                              |
| Nonprofit, Government & Social Services  | **Service**                              |
| Entertainment, Media & Arts              | **Service**                              |
| Personal & Miscellaneous Services        | **Service**                              |
| Other / Mixed Business                   | **Hybrid**                               |

Some individual types within a category may differ (e.g., "E-commerce / Online Store" is Trading, but "Marketplace Platform" might be Hybrid). We handle this at the granular `business_types` row level.

---

## Module Visibility Per Category

### Current Sidebar Modules (All 16)

| #   | Module              | Trading | Manufacturing | Service | Hybrid |
| --- | ------------------- | ------- | ------------- | ------- | ------ |
| 1   | **Dashboard**       | ✅      | ✅            | ✅      | ✅     |
| 2   | **Accounting**      | ✅      | ✅            | ✅      | ✅     |
| 3   | **Inventory**       | ✅      | ✅            | ❌      | ✅     |
| 4   | **CRM**             | ✅      | ✅            | ✅      | ✅     |
| 5   | **POS**             | ✅      | ❌            | ❌      | ✅     |
| 6   | **E-commerce**      | ✅      | ❌            | ❌      | ✅     |
| 7   | **Payroll**         | ✅      | ✅            | ✅      | ✅     |
| 8   | **Procurement**     | ✅      | ✅            | ❌      | ✅     |
| 9   | **Banking**         | ✅      | ✅            | ✅      | ✅     |
| 10  | **Reports**         | ✅      | ✅            | ✅      | ✅     |
| 11  | **Statutory (Tax)** | ✅      | ✅            | ✅      | ✅     |
| 12  | **Audit**           | ✅      | ✅            | ✅      | ✅     |
| 13  | **Admin**           | ✅      | ✅            | ✅      | ✅     |
| 14  | **Settings**        | ✅      | ✅            | ✅      | ✅     |
| 15  | **Support**         | ✅      | ✅            | ✅      | ✅     |
| 16  | **Help**            | ✅      | ✅            | ✅      | ✅     |

### New Module: Projects (Service & Hybrid only)

| #   | Module       | Trading | Manufacturing | Service | Hybrid |
| --- | ------------ | ------- | ------------- | ------- | ------ |
| NEW | **Projects** | ❌      | ❌            | ✅      | ✅     |

### Sidebar Per Category (what users actually see)

**Service Business Sidebar** (e.g., Consultancy, Law Firm):

```
Dashboard
Accounting
Clients              ← relabeled from "CRM"
Projects             ← NEW
Invoicing            ← deep link into Accounting > Invoices
Payroll
Banking
Tax                  ← relabeled from "Statutory"
Reports
Audit
Admin
Settings
Support
Help
```

**Trading Business Sidebar** (e.g., Retail Store, Wholesale):

```
Dashboard
Accounting
Inventory
Customers            ← relabeled from "CRM"
POS
E-commerce
Procurement
Payroll
Banking
Reports
Tax                  ← relabeled from "Statutory"
Audit
Admin
Settings
Support
Help
```

**Manufacturing Business Sidebar** (e.g., Factory, Bakery):

```
Dashboard
Accounting
Inventory
Customers            ← relabeled from "CRM"
Procurement
Payroll
Banking
Reports
Tax                  ← relabeled from "Statutory"
Audit
Admin
Settings
Support
Help
```

**Hybrid Business Sidebar** (e.g., Restaurant, Car Dealership + Repair):

```
Dashboard
Accounting
Inventory
Clients & Customers  ← relabeled from "CRM"
Projects
POS
E-commerce
Procurement
Payroll
Banking
Reports
Tax                  ← relabeled from "Statutory"
Audit
Admin
Settings
Support
Help
```

---

## Terminology Customization Per Category

Different business types use different language. A law firm says "clients" and "revenue", not "customers" and "sales". Using the wrong terms makes the app feel foreign to the user.

### Label Mapping

| Default Term                  | Trading              | Manufacturing          | Service               | Hybrid               |
| ----------------------------- | -------------------- | ---------------------- | --------------------- | -------------------- |
| **CRM** (sidebar)             | Customers            | Customers              | Clients               | Clients & Customers  |
| **Statutory** (sidebar)       | Tax                  | Tax                    | Tax                   | Tax                  |
| **Sales**                     | Sales                | Sales                  | Revenue               | Revenue / Sales      |
| **Sales Invoice**             | Sales Invoice        | Sales Invoice          | Invoice               | Invoice              |
| **Purchase Invoice**          | Purchase Invoice     | Purchase Invoice       | Expense / Bill        | Purchase / Bill      |
| **Customers** (throughout)    | Customers            | Customers              | Clients               | Clients              |
| **Products**                  | Products             | Products               | Services              | Products & Services  |
| **Quotation**                 | Quotation            | Quotation              | Proposal              | Proposal / Quotation |
| **Cost of Goods Sold**        | Cost of Goods Sold   | Cost of Production     | Cost of Service       | Cost of Sales        |
| **Sales Order**               | Sales Order          | Sales Order            | Engagement / Contract | Sales Order          |
| **Purchase Order**            | Purchase Order       | Purchase Order         | _(hidden)_            | Purchase Order       |
| **Stock / Inventory**         | Stock                | Raw Materials / Stock  | _(hidden)_            | Stock                |
| **Units Sold**                | Units Sold           | Units Produced & Sold  | Hours / Sessions      | Units / Hours        |
| **Ledger: Sales Account**     | Sales Revenue        | Sales Revenue          | Service Revenue       | Revenue              |
| **Ledger: Purchases**         | Purchases            | Raw Material Purchases | _(hidden)_            | Purchases            |
| **Ledger: COGS**              | Cost of Goods Sold   | Cost of Production     | Direct Costs          | Cost of Sales        |
| **Profit & Loss title**       | Profit & Loss        | Profit & Loss          | Income Statement      | Profit & Loss        |
| **Dashboard: Today's Sales**  | Today's Sales        | Today's Production     | Today's Revenue       | Today's Revenue      |
| **Dashboard: Top Products**   | Top Selling Products | Top Products           | Top Services          | Top Items            |
| **Dashboard: Low Stock**      | Low Stock Alert      | Low Stock Alert        | _(hidden)_            | Low Stock Alert      |
| **Reports: Sales Reports**    | Sales Reports        | Sales Reports          | Revenue Reports       | Revenue Reports      |
| **Reports: Purchase Reports** | Purchase Reports     | Purchase Reports       | Expense Reports       | Expense Reports      |
| **Invoice line item**         | Product              | Product                | Service / Deliverable | Item                 |

### Implementation: Terminology Service

```php
// app/Services/TerminologyService.php

class TerminologyService
{
    protected string $category;

    public function __construct(?Tenant $tenant = null)
    {
        $this->category = $tenant?->getBusinessCategory() ?? 'hybrid';
    }

    // Get the label for a term based on business category
    public function label(string $key): string
    {
        return static::LABELS[$this->category][$key]
            ?? static::LABELS['trading'][$key]
            ?? $key;
    }

    // Example usage in Blade: {{ $term->label('customers') }}
    // Example usage in controller: app(TerminologyService::class)->label('sales')

    public const LABELS = [
        'trading' => [
            'crm'           => 'Customers',
            'statutory'     => 'Tax',
            'sales'         => 'Sales',
            'customers'     => 'Customers',
            'products'      => 'Products',
            'quotation'     => 'Quotation',
            'sales_invoice' => 'Sales Invoice',
            'cogs'          => 'Cost of Goods Sold',
            'purchase'      => 'Purchase',
            'revenue'       => 'Sales Revenue',
            'line_item'     => 'Product',
            'pnl_title'     => 'Profit & Loss Statement',
            'sales_reports' => 'Sales Reports',
            'top_products'  => 'Top Selling Products',
        ],
        'manufacturing' => [
            'crm'           => 'Customers',
            'statutory'     => 'Tax',
            'sales'         => 'Sales',
            'customers'     => 'Customers',
            'products'      => 'Products',
            'quotation'     => 'Quotation',
            'sales_invoice' => 'Sales Invoice',
            'cogs'          => 'Cost of Production',
            'purchase'      => 'Raw Material Purchase',
            'revenue'       => 'Sales Revenue',
            'line_item'     => 'Product',
            'pnl_title'     => 'Profit & Loss Statement',
            'sales_reports' => 'Sales Reports',
            'top_products'  => 'Top Products',
        ],
        'service' => [
            'crm'           => 'Clients',
            'statutory'     => 'Tax',
            'sales'         => 'Revenue',
            'customers'     => 'Clients',
            'products'      => 'Services',
            'quotation'     => 'Proposal',
            'sales_invoice' => 'Invoice',
            'cogs'          => 'Direct Costs',
            'purchase'      => 'Expense',
            'revenue'       => 'Service Revenue',
            'line_item'     => 'Service',
            'pnl_title'     => 'Income Statement',
            'sales_reports' => 'Revenue Reports',
            'top_products'  => 'Top Services',
        ],
        'hybrid' => [
            'crm'           => 'Clients & Customers',
            'statutory'     => 'Tax',
            'sales'         => 'Revenue',
            'customers'     => 'Clients',
            'products'      => 'Products & Services',
            'quotation'     => 'Proposal',
            'sales_invoice' => 'Invoice',
            'cogs'          => 'Cost of Sales',
            'purchase'      => 'Purchase',
            'revenue'       => 'Revenue',
            'line_item'     => 'Item',
            'pnl_title'     => 'Profit & Loss Statement',
            'sales_reports' => 'Revenue Reports',
            'top_products'  => 'Top Items',
        ],
    ];
}
```

### Blade Integration

Share the service globally so every view can use it:

```php
// AppServiceProvider boot()
view()->share('term', app(TerminologyService::class));
```

Usage in Blade templates:

```blade
{{-- Sidebar --}}
<a href="...">{{ $term->label('crm') }}</a>           {{-- "Clients" for service --}}
<a href="...">{{ $term->label('statutory') }}</a>      {{-- "Tax" for all --}}

{{-- Invoice page --}}
<h1>Create {{ $term->label('sales_invoice') }}</h1>    {{-- "Invoice" for service --}}
<th>{{ $term->label('line_item') }}</th>                 {{-- "Service" for service --}}

{{-- Reports --}}
<h1>{{ $term->label('sales_reports') }}</h1>             {{-- "Revenue Reports" for service --}}

{{-- Dashboard --}}
<h3>{{ $term->label('top_products') }}</h3>              {{-- "Top Services" for service --}}

{{-- Profit & Loss --}}
<h1>{{ $term->label('pnl_title') }}</h1>                 {{-- "Income Statement" for service --}}
```

### Where Terminology Applies

| Location                 | What Changes                                                                |
| ------------------------ | --------------------------------------------------------------------------- |
| **Sidebar labels**       | CRM → Clients, Statutory → Tax, etc.                                        |
| **Page titles**          | "Sales Invoices" → "Invoices", "Quotations" → "Proposals"                   |
| **Table column headers** | "Product" → "Service", "Customer" → "Client"                                |
| **Dashboard widgets**    | "Today's Sales" → "Today's Revenue", "Top Products" → "Top Services"        |
| **Report names**         | "Sales Reports" → "Revenue Reports", "Purchase Reports" → "Expense Reports" |
| **Financial statements** | "Profit & Loss" → "Income Statement", "COGS" → "Direct Costs"               |
| **Form labels**          | "Customer Name" → "Client Name", "Product" → "Service"                      |
| **Flash messages**       | "Invoice created" stays same (generic enough)                               |
| **Breadcrumbs**          | Follow same label mapping                                                   |
| **Mobile API**           | Include `terminology` object in tenant config response                      |

### Files to Modify for Terminology

| File                                                       | Changes                                         |
| ---------------------------------------------------------- | ----------------------------------------------- |
| `app/Services/TerminologyService.php`                      | **New** — central terminology mapping           |
| `app/Providers/AppServiceProvider.php`                     | Share `$term` to all views                      |
| `resources/views/layouts/tenant/sidebar.blade.php`         | Use `$term->label()` for sidebar items          |
| `resources/views/tenant/accounting/invoices/*.blade.php`   | Page titles, column headers, form labels        |
| `resources/views/tenant/accounting/quotations/*.blade.php` | "Quotation" → `$term->label('quotation')`       |
| `resources/views/tenant/crm/*.blade.php`                   | "Customer" → `$term->label('customers')`        |
| `resources/views/tenant/dashboard/*.blade.php`             | Widget titles                                   |
| `resources/views/tenant/reports/*.blade.php`               | Report names and headers                        |
| `resources/views/tenant/inventory/*.blade.php`             | "Product" → `$term->label('products')`          |
| API controllers (mobile)                                   | Include terminology map in tenant info response |

---

## Custom Dashboard Per Category

The dashboard should highlight what matters for each category:

### Service Dashboard

| Widget                | Description                                    |
| --------------------- | ---------------------------------------------- |
| Total Revenue (MTD)   | From invoices                                  |
| Outstanding Invoices  | Unpaid invoice count + total                   |
| Active Projects       | Currently running projects                     |
| Upcoming Deadlines    | Project milestones due soon                    |
| Client Acquisition    | New clients this month                         |
| Expense Overview      | Monthly expenses chart                         |
| Time Tracking Summary | Billable hours (future)                        |
| Recent Activity       | Latest invoices, payments, client interactions |

### Trading Dashboard (current — mostly keep as-is)

| Widget               | Description                     |
| -------------------- | ------------------------------- |
| Total Revenue (MTD)  | From sales                      |
| Today's Sales        | POS + invoice sales today       |
| Low Stock Alerts     | Products below reorder level    |
| Top Selling Products | Best performers                 |
| Customer Analytics   | Repeat customers, new customers |
| Cash Flow Overview   | Income vs expenses              |
| Recent Transactions  | Latest vouchers/invoices        |

### Manufacturing Dashboard

| Widget               | Description                |
| -------------------- | -------------------------- |
| Total Revenue (MTD)  | From sales                 |
| Production Summary   | Units produced this period |
| Raw Material Stock   | Key materials status       |
| Work in Progress     | Active production jobs     |
| COGS Analysis        | Cost breakdown             |
| Procurement Overview | Pending purchase orders    |
| Expense Overview     | Manufacturing costs chart  |
| Recent Transactions  | Latest vouchers/invoices   |

### Hybrid Dashboard

Combines relevant widgets from both Service and Trading dashboards. User can customize widget layout in Settings (future feature).

---

## Chart of Accounts Per Category

Currently, all tenants get the same ~109 ledger accounts from `DefaultLedgerAccountsSeeder`. This should be replaced with an **interactive ledger account selection** during onboarding, where accounts are pre-selected based on business category and the user can add/remove optional ones.

### Master Ledger Account Catalog

Instead of multiple category-specific seeders, we maintain a **single master catalog** of all available accounts. Each account is tagged with:

- **`categories`** — which business categories it's relevant to: `['trading', 'manufacturing', 'service', 'hybrid']`
- **`is_core`** — if `true`, this account is **mandatory** for the category and cannot be unchecked by the user
- **`is_system`** — if `true`, this is a system account (non-deletable after creation)

### Account Classification by Category

#### Core Accounts (Mandatory — Cannot Be Unchecked)

These accounts are required for the accounting system to function correctly. Locked for ALL categories:

| Account                 | Code            | Group               | Why Core                            |
| ----------------------- | --------------- | ------------------- | ----------------------------------- |
| Cash in Hand            | CASH-001        | Current Assets      | Every business needs a cash account |
| Accounts Receivable     | AR-001          | Current Assets      | Required for invoice tracking       |
| Accounts Payable        | AP-001          | Current Liabilities | Required for bills/purchases        |
| VAT Input               | VAT-IN-001      | Current Assets      | Nigerian VAT compliance             |
| VAT Output              | VAT-OUT-001     | Current Liabilities | Nigerian VAT compliance             |
| Sales Revenue           | SALES-001       | Direct Income       | Core revenue tracking               |
| Bank Charges            | BANK-CHG-001    | Indirect Expenses   | Every bank account incurs charges   |
| Salaries & Wages        | SAL-001         | Indirect Expenses   | Payroll requires this               |
| Owner's Capital         | CAPITAL-001     | Capital Account     | Equity tracking                     |
| Retained Earnings       | RETAIN-001      | Capital Account     | Profit accumulation                 |
| PAYE Tax Payable        | PAYE-001        | Current Liabilities | Nigerian tax compliance             |
| Withholding Tax Payable | WHT-001         | Current Liabilities | Nigerian tax compliance             |
| Pension Payable         | PENSION-PAY-001 | Current Liabilities | Nigerian pension compliance         |

#### Category-Specific Core Accounts (Mandatory per category)

| Account                  | Code                  | Trading   | Manufacturing | Service   | Hybrid   |
| ------------------------ | --------------------- | --------- | ------------- | --------- | -------- |
| Inventory                | INV-001               | **Core**  | **Core**      | ❌ Hidden | **Core** |
| Stock in Hand            | STOCK-001             | **Core**  | **Core**      | ❌ Hidden | **Core** |
| Purchases                | PURCH-001             | **Core**  | **Core**      | ❌ Hidden | **Core** |
| Cost of Goods Sold       | COGS-001              | **Core**  | **Core**      | ❌ Hidden | **Core** |
| Sales Returns            | SALES-RET-001         | **Core**  | **Core**      | Optional  | **Core** |
| Purchase Returns         | PURCH-RET-001         | **Core**  | **Core**      | ❌ Hidden | **Core** |
| Raw Material Purchases   | RAW-MAT-PURCH-001     | ❌ Hidden | **Core**      | ❌ Hidden | Optional |
| Factory Overhead         | FACTORY-OVERHEAD-001  | ❌ Hidden | **Core**      | ❌ Hidden | Optional |
| Manufacturing Expenses   | MANUFACTURING-EXP-001 | ❌ Hidden | **Core**      | ❌ Hidden | Optional |
| Labor Costs              | LABOR-COSTS-001       | ❌ Hidden | **Core**      | ❌ Hidden | Optional |
| Service Income           | SERV-001              | Optional  | Optional      | **Core**  | **Core** |
| Professional Fees Income | PROF-FEES-INC-001     | ❌ Hidden | ❌ Hidden     | **Core**  | Optional |

#### Optional Accounts (Pre-checked per category, user can uncheck)

| Account                  | Code               | Trading | Manufacturing | Service | Hybrid |
| ------------------------ | ------------------ | ------- | ------------- | ------- | ------ |
| Petty Cash               | PETTY-001          | ✅      | ✅            | ✅      | ✅     |
| Short-term Investments   | ST-INV-001         | ☐       | ☐             | ☐       | ☐      |
| Prepaid Expenses         | PREPAID-EXP-001    | ✅      | ✅            | ✅      | ✅     |
| Accrued Income           | ACCRUED-INC-001    | ☐       | ☐             | ☐       | ☐      |
| Fixed Deposits           | FIXED-DEP-001      | ☐       | ☐             | ☐       | ☐      |
| Fixed Assets             | FIXED-001          | ✅      | ✅            | ✅      | ✅     |
| Accumulated Depreciation | ACCUM-DEPR-001     | ✅      | ✅            | ✅      | ✅     |
| Land                     | LAND-001           | ☐       | ✅            | ☐       | ☐      |
| Buildings                | BUILD-001          | ☐       | ✅            | ☐       | ☐      |
| Machinery                | MACH-001           | ☐       | ✅            | ☐       | ☐      |
| Vehicles                 | VEH-001            | ✅      | ✅            | ☐       | ✅     |
| Furniture & Fixtures     | FURN-001           | ✅      | ✅            | ✅      | ✅     |
| Computer Equipment       | COMP-EQ-001        | ✅      | ☐             | ✅      | ✅     |
| Office Equipment         | OFF-EQ-001         | ✅      | ✅            | ✅      | ✅     |
| Unearned Revenue         | UN-REV-001         | ☐       | ☐             | ✅      | ✅     |
| Accrued Expenses         | ACCRUED-EXP-001    | ✅      | ✅            | ✅      | ✅     |
| Commission Income        | COMM-INC-001       | ✅      | ☐             | ✅      | ✅     |
| Rental Income            | RENT-INC-001       | ☐       | ☐             | ☐       | ☐      |
| Interest Income          | INT-INC-001        | ✅      | ✅            | ✅      | ✅     |
| Discount Received        | DISC-REC-001       | ✅      | ✅            | ☐       | ✅     |
| Other Income             | OTHER-INC-001      | ✅      | ✅            | ✅      | ✅     |
| Shipping Costs           | SHIPPING-COSTS-001 | ✅      | ✅            | ☐       | ✅     |
| Freight Costs            | FREIGHT-COSTS-001  | ✅      | ✅            | ☐       | ✅     |
| Customs Duties           | CUSTOMS-DUTIES-001 | ✅      | ✅            | ☐       | ✅     |
| Office Rent              | RENT-001           | ✅      | ✅            | ✅      | ✅     |
| Electricity Expense      | ELEC-001           | ✅      | ✅            | ✅      | ✅     |
| Telephone & Internet     | TEL-001            | ✅      | ✅            | ✅      | ✅     |
| Office Supplies          | SUPP-001           | ✅      | ✅            | ✅      | ✅     |
| Diesel & Fuel            | FUEL-EXP-001       | ✅      | ✅            | ☐       | ✅     |
| Generator Maintenance    | GEN-MAINT-001      | ✅      | ✅            | ☐       | ✅     |
| Transportation           | TRANS-001          | ✅      | ✅            | ✅      | ✅     |
| Professional Fees        | PROF-001           | ☐       | ☐             | ✅      | ✅     |
| Marketing & Advertising  | MARK-001           | ✅      | ☐             | ✅      | ✅     |
| Insurance                | INS-001            | ✅      | ✅            | ✅      | ✅     |
| Depreciation Expense     | DEPR-001           | ✅      | ✅            | ✅      | ✅     |
| Repairs & Maintenance    | REPAIRS-MAINT-001  | ✅      | ✅            | ☐       | ✅     |
| Owner's Drawings         | DRAW-001           | ✅      | ✅            | ✅      | ✅     |

✅ = Pre-checked (suggested for this category)
☐ = Available but not pre-checked
❌ Hidden = Not shown at all for this category

#### Approximate Account Counts by Category

| Category          | Core (locked) | Pre-checked | Available (unchecked) | Hidden | Total Shown |
| ----------------- | ------------- | ----------- | --------------------- | ------ | ----------- |
| **Service**       | 15            | ~20         | ~15                   | ~20    | ~50         |
| **Trading**       | 18            | ~30         | ~20                   | ~8     | ~68         |
| **Manufacturing** | 22            | ~35         | ~15                   | ~5     | ~72         |
| **Hybrid**        | 20            | ~40         | ~20                   | ~0     | ~80         |

---

## Product/Service Behavior Per Category

### Service Category

- Products module is **hidden** (no sidebar link to Inventory)
- When creating an invoice, items are **"service items"** — no stock tracking, no quantity on hand
- Service items have: Name, Description, Rate (hourly/fixed), Tax rate
- No stock movements, no physical stock, no stock journal
- Invoice line items allow: Service description, Hours/Qty, Rate, Amount, Tax

### Trading Category

- Products module is **full featured** — stock tracking, units, categories, images
- Invoice line items are product-based with stock deduction
- POS is available
- Stock journal, physical stock, stock movements all enabled

### Manufacturing Category

- Products module is **full featured** + BOM (Bill of Materials) concept
- Stock journal includes: Production entries (raw materials → finished goods)
- Physical stock vouchers for production counts
- Products can be: raw materials, WIP, or finished goods (product_type field)

### Hybrid Category

- Both service items and stock products available
- Invoice create form has a toggle: "Product" / "Service" when adding line items
- POS can sell both physical products and services

---

## Projects Module (New — Service & Hybrid)

This is the key module that makes Ballie relevant for service businesses.

### Core Features (Phase 1)

| Feature               | Description                                                                        |
| --------------------- | ---------------------------------------------------------------------------------- |
| **Project CRUD**      | Name, client (linked to CRM customer), description, start/end date, status, budget |
| **Project Status**    | Draft → Active → On Hold → Completed → Archived                                    |
| **Tasks**             | Sub-items within a project with assignee, due date, status, priority               |
| **Milestones**        | Key deliverables with target dates                                                 |
| **Project Budget**    | Budget amount, track expenses against budget                                       |
| **Link to Invoicing** | Generate invoice from project (bill time/milestones to client)                     |
| **Project Notes**     | Internal notes and communication log                                               |
| **File Attachments**  | Upload project documents                                                           |

### Database Tables

```
projects
├── id
├── tenant_id (FK → tenants)
├── customer_id (FK → customers, nullable)
├── name
├── slug
├── description (text)
├── status (enum: draft, active, on_hold, completed, archived)
├── priority (enum: low, medium, high, urgent)
├── start_date (date, nullable)
├── end_date (date, nullable)
├── budget (bigint, nullable — in kobo)
├── actual_cost (bigint, default 0 — in kobo)
├── currency (default 'NGN')
├── assigned_to (FK → users, nullable — project manager)
├── created_by (FK → users)
├── completed_at (timestamp, nullable)
├── settings (json, nullable)
├── timestamps
├── soft_deletes

project_tasks
├── id
├── project_id (FK → projects)
├── tenant_id (FK → tenants)
├── title
├── description (text, nullable)
├── status (enum: todo, in_progress, review, done)
├── priority (enum: low, medium, high, urgent)
├── assigned_to (FK → users, nullable)
├── due_date (date, nullable)
├── completed_at (timestamp, nullable)
├── sort_order (integer)
├── estimated_hours (decimal, nullable)
├── actual_hours (decimal, nullable)
├── timestamps

project_milestones
├── id
├── project_id (FK → projects)
├── tenant_id (FK → tenants)
├── title
├── description (text, nullable)
├── due_date (date, nullable)
├── completed_at (timestamp, nullable)
├── amount (bigint, nullable — billable amount in kobo)
├── is_billable (boolean, default true)
├── invoice_id (FK → invoices, nullable — linked invoice once billed)
├── sort_order (integer)
├── timestamps

project_notes
├── id
├── project_id (FK → projects)
├── user_id (FK → users)
├── content (text)
├── is_internal (boolean, default true)
├── timestamps

project_attachments
├── id
├── project_id (FK → projects)
├── user_id (FK → users)
├── file_name
├── file_path
├── file_size (bigint)
├── mime_type
├── timestamps
```

### Routes

```
/{tenant}/projects                          → index
/{tenant}/projects/create                   → create
/{tenant}/projects/{project}                → show (detail + tasks + milestones)
/{tenant}/projects/{project}/edit           → edit
/{tenant}/projects/{project}/tasks          → task management (AJAX)
/{tenant}/projects/{project}/milestones     → milestone management (AJAX)
/{tenant}/projects/{project}/invoice        → generate invoice from project
/{tenant}/projects/{project}/notes          → notes (AJAX)
/{tenant}/projects/{project}/attachments    → file uploads
/{tenant}/projects/reports                  → project reports
```

### Phase 2 (Future Enhancements)

- Time tracking (start/stop timer per task)
- Gantt chart view
- Client portal (client can view project progress)
- Recurring projects (templates)
- Project-based profitability report
- Kanban board view for tasks
- Team workload view

---

## Implementation Plan

### Phase 1: Database & Backend Foundation

**Estimate: 3–5 days**

- [ ] **Migration: Add `business_category` to `business_types` table**

    ```
    ALTER TABLE business_types ADD COLUMN business_category
      ENUM('trading', 'manufacturing', 'service', 'hybrid')
      NOT NULL DEFAULT 'trading' AFTER category;
    ```

- [ ] **Migration: Add `enabled_modules` JSON column to `tenants` table**

    ```
    ALTER TABLE tenants ADD COLUMN enabled_modules JSON NULLABLE AFTER settings;
    ```

- [ ] **Update `BusinessType` model**: Add `business_category` to fillable, add scope `scopeByBusinessCategory()`, add constant arrays for category-to-module mapping

- [ ] **Update `BusinessTypeSeeder`**: Set correct `business_category` for all 97 types

- [ ] **Create `ModuleRegistry` service class** (`app/Services/ModuleRegistry.php`):

    ```php
    class ModuleRegistry
    {
        // Define all modules
        const MODULE_DASHBOARD = 'dashboard';
        const MODULE_ACCOUNTING = 'accounting';
        const MODULE_INVENTORY = 'inventory';
        const MODULE_CRM = 'crm';
        const MODULE_POS = 'pos';
        const MODULE_ECOMMERCE = 'ecommerce';
        const MODULE_PAYROLL = 'payroll';
        const MODULE_PROCUREMENT = 'procurement';
        const MODULE_BANKING = 'banking';
        const MODULE_PROJECTS = 'projects';    // NEW
        const MODULE_REPORTS = 'reports';
        const MODULE_STATUTORY = 'statutory';
        const MODULE_AUDIT = 'audit';
        const MODULE_ADMIN = 'admin';
        const MODULE_SETTINGS = 'settings';
        const MODULE_SUPPORT = 'support';
        const MODULE_HELP = 'help';

        // Default modules per category
        public static function getDefaultModules(string $category): array
        public static function isModuleEnabled(Tenant $tenant, string $module): bool
        public static function getEnabledModules(Tenant $tenant): array
        public static function enableModule(Tenant $tenant, string $module): void
        public static function disableModule(Tenant $tenant, string $module): void
    }
    ```

- [ ] **Add `hasModule()` method to Tenant model**:

    ```php
    public function hasModule(string $module): bool
    {
        return ModuleRegistry::isModuleEnabled($this, $module);
    }

    public function getBusinessCategory(): string
    {
        return $this->businessType?->business_category ?? 'hybrid';
    }
    ```

- [ ] **Create `@module` / `@endmodule` Blade directive** for sidebar and views:
    ```php
    // In AppServiceProvider boot()
    Blade::if('module', function (string $module) {
        $tenant = tenant();
        return $tenant && ModuleRegistry::isModuleEnabled($tenant, $module);
    });
    ```

### Phase 2: Sidebar, Navigation & Terminology

**Estimate: 3–5 days**

- [ ] **Create `TerminologyService`**: `app/Services/TerminologyService.php` with label mapping per category (see Terminology section above)

- [ ] **Share `$term` globally**: In `AppServiceProvider::boot()`:

    ```php
    view()->share('term', app(TerminologyService::class));
    ```

- [ ] **Update sidebar.blade.php**: Wrap each module link in `@module('module_name')` ... `@endmodule` directives AND use `$term->label()` for display text:

    ```blade
    @module('inventory')
    <a href="{{ route('tenant.inventory.index', $tenant) }}" ...>
        {{ $term->label('products') }}  {{-- "Products" / "Services" --}}
    </a>
    @endmodule

    @module('crm')
    <a href="{{ route('tenant.crm.index', $tenant) }}" ...>
        {{ $term->label('crm') }}  {{-- "Customers" / "Clients" --}}
    </a>
    @endmodule

    @module('statutory')
    <a href="{{ route('tenant.statutory.index', $tenant) }}" ...>
        {{ $term->label('statutory') }}  {{-- "Tax" for all --}}
    </a>
    @endmodule

    @module('projects')
    <a href="{{ route('tenant.projects.index', $tenant) }}" ...>
        Projects
    </a>
    @endmodule
    ```

- [ ] **Update page titles & headers**: Replace hard-coded strings in Blade templates with `$term->label()` calls across all modules (invoices, quotations, CRM, reports, dashboard, etc.)

- [ ] **Add Projects sidebar item** (between CRM and Invoicing for service businesses)

- [ ] **Update mobile API sidebar/menu endpoint** (if one exists) — share `enabled_modules` array AND `terminology` map in API response:

    ```php
    // In tenant config API response
    'terminology' => app(TerminologyService::class)->all(),
    ```

- [ ] **Share `enabledModules` to all views** via `AppServiceProvider` or middleware:
    ```php
    view()->share('enabledModules', ModuleRegistry::getEnabledModules(tenant()));
    ```

### Phase 3: Registration & Onboarding Update

**Estimate: 5–7 days**

The onboarding flow changes from 3 steps to **4 steps**:

| Step | Current      | New                                     |
| ---- | ------------ | --------------------------------------- |
| 1    | Company Info | Company Info _(unchanged)_              |
| 2    | Preferences  | Preferences + Module Selection          |
| 3    | _(complete)_ | **Chart of Accounts Selection** _(NEW)_ |
| 4    | —            | Complete                                |

- [ ] **Update registration form**: After user selects a business type, display the mapped **business category** (Trading/Manufacturing/Service/Hybrid) as a confirmation badge. User can override it if their business doesn't fit the default.

- [ ] **Update registration flow**: After tenant creation, auto-populate `enabled_modules` from `ModuleRegistry::getDefaultModules($category)`

- [ ] **Update onboarding Step 2 (Preferences)**: Add a **module toggle** section at the bottom of the existing preferences form. Replace the unused `features[]` checkboxes with a module grid:

    ```
    ── Modules for Your Business ──────────────────────────
    Based on your business type (Consultancy → Service), we've
    selected the most relevant modules for you:

    ✅ Accounting — Financial records, chart of accounts, vouchers    [locked]
    ✅ Clients — Client relationship management                      [toggle]
    ✅ Projects — Track client projects, tasks, and milestones        [toggle]
    ❌ Inventory — Product stock tracking (not typical for service)   [toggle]
    ❌ POS — Point of sale terminal                                   [toggle]
    ✅ Payroll — Employee salary management                           [toggle]
    ✅ Banking — Bank accounts & reconciliation                       [toggle]
    ✅ Tax — Tax compliance & statutory filings                       [toggle]
    ...
    ───────────────────────────────────────────────────────
    ```

    Pre-checked based on business category, but user can toggle on/off. Core modules (Dashboard, Accounting, Settings, Admin, Support, Help) are locked.

- [ ] **Add onboarding Step 3 — Chart of Accounts Selection** _(NEW STEP)_: See detailed spec below.

- [ ] **Update onboarding `seedDefaultData()`**: Instead of calling `DefaultLedgerAccountsSeeder::seedForTenant()` blindly, only seed the **user-selected accounts** from Step 3.

- [ ] **Update valid steps** in `OnboardingController::showStep()`: Add `'accounts'` to `['company', 'preferences', 'accounts', 'complete']`

- [ ] **Update progress calculation**: Change from 3-step to 4-step progress (25% per step)

---

### Phase 3.1: Chart of Accounts Selection Step (Onboarding)

**This is the core of the new onboarding experience.** Instead of silently seeding ~109 accounts for every tenant, we present a categorized, interactive account selection screen.

#### UI Design: `resources/views/tenant/onboarding/steps/accounts.blade.php`

```
┌──────────────────────────────────────────────────────────────────┐
│                    Step 3 of 4 — Chart of Accounts              │
│                                                                  │
│  Select the ledger accounts for your business. We've pre-       │
│  selected the most relevant accounts for a [Service] business.  │
│                                                                  │
│  🔒 = Required account (cannot be removed)                      │
│  ✅ = Suggested for your business type (can be removed)         │
│  ☐  = Available (can be added)                                  │
│                                                                  │
│  ┌─ Current Assets ──────────────────────────────────────────┐  │
│  │ 🔒 Cash in Hand (CASH-001)                                │  │
│  │    Every business needs a cash account                    │  │
│  │ 🔒 Accounts Receivable (AR-001)                           │  │
│  │    Track money owed to you by clients                     │  │
│  │ 🔒 VAT Input (VAT-IN-001)                                 │  │
│  │    Input VAT on purchases — required for tax compliance   │  │
│  │ ✅ Petty Cash (PETTY-001)                                  │  │
│  │    Small cash fund for minor office expenses              │  │
│  │ ✅ Prepaid Expenses (PREPAID-EXP-001)                      │  │
│  │    Expenses paid in advance (e.g., annual rent)           │  │
│  │ ☐  Short-term Investments (ST-INV-001)                    │  │
│  │    Treasury bills, money market funds                     │  │
│  │ ☐  Fixed Deposits (FIXED-DEP-001)                         │  │
│  │    Bank fixed deposit accounts                            │  │
│  │                                                            │  │
│  │ ❌ Inventory (INV-001)        ← HIDDEN for Service         │  │
│  │ ❌ Stock in Hand (STOCK-001)  ← HIDDEN for Service         │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ┌─ Fixed Assets ────────────────────────────────────────────┐  │
│  │ ✅ Computer Equipment (COMP-EQ-001)                        │  │
│  │ ✅ Furniture & Fixtures (FURN-001)                         │  │
│  │ ✅ Office Equipment (OFF-EQ-001)                           │  │
│  │ ☐  Vehicles (VEH-001)                                     │  │
│  │ ☐  Land (LAND-001)                                        │  │
│  │ ☐  Buildings (BUILD-001)                                  │  │
│  │ ☐  Machinery (MACH-001)      ← Not shown for Service      │  │
│  │ ...                                                        │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ┌─ Current Liabilities ─────────────────────────────────────┐  │
│  │ 🔒 Accounts Payable (AP-001)                              │  │
│  │ 🔒 VAT Output (VAT-OUT-001)                               │  │
│  │ 🔒 PAYE Tax Payable (PAYE-001)                            │  │
│  │ ...                                                        │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ┌─ Direct Income ───────────────────────────────────────────┐  │
│  │ 🔒 Service Revenue (SALES-001) ← Uses terminology label   │  │
│  │ 🔒 Professional Fees Income (PROF-FEES-INC-001)           │  │
│  │ ✅ Commission Income (COMM-INC-001)                        │  │
│  │ ☐  Rental Income (RENT-INC-001)                           │  │
│  │                                                            │  │
│  │ ❌ Sales Returns (SALES-RET-001) ← HIDDEN for Service      │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ... (more groups collapsed by default, expandable) ...          │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  Summary: 38 accounts selected (15 required + 23 optional)│   │
│  │                                                            │   │
│  │  💡 You can always add more accounts later from            │   │
│  │     Accounting → Chart of Accounts                        │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  [← Back to Preferences]                    [Continue →]         │
└──────────────────────────────────────────────────────────────────┘
```

#### Key UI Behaviors

| Behavior                                | Detail                                                                                                                           |
| --------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------- |
| **Grouped by Account Group**            | 9 collapsible sections (Current Assets, Fixed Assets, etc.)                                                                      |
| **Core accounts have 🔒 icon**          | Checkbox is checked + disabled — cannot be unchecked                                                                             |
| **Suggested accounts pre-checked**      | Based on business category — user can uncheck                                                                                    |
| **Hidden accounts not shown**           | Accounts irrelevant to the category are completely hidden (e.g., Inventory for Service)                                          |
| **Available accounts unchecked**        | Shown but not pre-selected — user can opt in                                                                                     |
| **Select All / Deselect All** per group | Toggle at group header level (only affects optional accounts)                                                                    |
| **Account descriptions**                | Brief one-line description below each account name                                                                               |
| **Account code visible**                | Shown in muted text next to the name                                                                                             |
| **Live counter**                        | "X accounts selected (Y required + Z optional)" updates in real-time                                                             |
| **Collapsible groups**                  | Groups with all-core accounts start expanded; others start collapsed                                                             |
| **Search/filter**                       | Optional: text filter to search accounts by name                                                                                 |
| **Terminology-aware**                   | Account display names use `$term->label()` where applicable (e.g., "Service Revenue" not "Sales Revenue" for service businesses) |

#### Backend: Account Catalog Service

```php
// app/Services/LedgerAccountCatalog.php

class LedgerAccountCatalog
{
    /**
     * Get all available accounts for a business category
     * Returns accounts grouped by account group with selection state
     */
    public static function getForCategory(string $category): array
    {
        return collect(static::MASTER_CATALOG)
            ->filter(fn($account) => in_array($category, $account['categories']))
            ->groupBy('group')
            ->map(function ($accounts, $group) use ($category) {
                return $accounts->map(function ($account) use ($category) {
                    return [
                        'code'        => $account['code'],
                        'name'        => $account['name'],
                        'description' => $account['description'],
                        'group'       => $account['group'],
                        'type'        => $account['type'],
                        'is_core'     => in_array($category, $account['core_for'] ?? []),
                        'is_suggested'=> in_array($category, $account['suggested_for'] ?? []),
                        'is_system'   => $account['is_system'] ?? false,
                    ];
                });
            });
    }

    /**
     * Get default selected account codes for a category
     * (core + suggested, but not merely "available")
     */
    public static function getDefaultSelection(string $category): array
    {
        return collect(static::MASTER_CATALOG)
            ->filter(function ($account) use ($category) {
                if (!in_array($category, $account['categories'])) return false;
                $isCore = in_array($category, $account['core_for'] ?? []);
                $isSuggested = in_array($category, $account['suggested_for'] ?? []);
                return $isCore || $isSuggested;
            })
            ->pluck('code')
            ->toArray();
    }

    /**
     * Get core (mandatory) account codes for a category
     */
    public static function getCoreAccounts(string $category): array
    {
        return collect(static::MASTER_CATALOG)
            ->filter(fn($a) => in_array($category, $a['core_for'] ?? []))
            ->pluck('code')
            ->toArray();
    }

    /**
     * Validate user selection — ensure all core accounts are included
     */
    public static function validateSelection(string $category, array $selectedCodes): array
    {
        $coreCodes = static::getCoreAccounts($category);
        return array_unique(array_merge($coreCodes, $selectedCodes));
    }

    // Example of a single catalog entry:
    public const MASTER_CATALOG = [
        [
            'code'          => 'CASH-001',
            'name'          => 'Cash in Hand',
            'description'   => 'Physical cash held by the business',
            'group'         => 'Current Assets',
            'type'          => 'asset',
            'is_system'     => true,
            'categories'    => ['trading', 'manufacturing', 'service', 'hybrid'],
            'core_for'      => ['trading', 'manufacturing', 'service', 'hybrid'],
            'suggested_for' => ['trading', 'manufacturing', 'service', 'hybrid'],
        ],
        [
            'code'          => 'INV-001',
            'name'          => 'Inventory',
            'description'   => 'Value of goods held in stock for sale',
            'group'         => 'Current Assets',
            'type'          => 'asset',
            'is_system'     => true,
            'categories'    => ['trading', 'manufacturing', 'hybrid'], // NOT service
            'core_for'      => ['trading', 'manufacturing', 'hybrid'],
            'suggested_for' => ['trading', 'manufacturing', 'hybrid'],
        ],
        [
            'code'          => 'SERV-001',
            'name'          => 'Service Income',
            'description'   => 'Revenue from professional services rendered',
            'group'         => 'Direct Income',
            'type'          => 'income',
            'is_system'     => false,
            'categories'    => ['service', 'hybrid', 'trading', 'manufacturing'],
            'core_for'      => ['service', 'hybrid'],
            'suggested_for' => ['service', 'hybrid'],
        ],
        // ... ~109 total entries
    ];
}
```

#### Controller: Onboarding Accounts Step

```php
// In OnboardingController

public function showAccountsStep(Tenant $tenant)
{
    $category = $tenant->getBusinessCategory();
    $catalog = LedgerAccountCatalog::getForCategory($category);
    $defaultSelection = LedgerAccountCatalog::getDefaultSelection($category);
    $coreAccounts = LedgerAccountCatalog::getCoreAccounts($category);

    return view('tenant.onboarding.steps.accounts', compact(
        'tenant', 'catalog', 'defaultSelection', 'coreAccounts', 'category'
    ));
}

public function saveAccountsStep(Request $request, Tenant $tenant)
{
    $category = $tenant->getBusinessCategory();

    $validated = $request->validate([
        'accounts'   => 'required|array|min:1',
        'accounts.*' => 'string|max:50',
    ]);

    // Ensure core accounts are always included (server-side enforcement)
    $selectedCodes = LedgerAccountCatalog::validateSelection(
        $category,
        $validated['accounts']
    );

    // Store selection in tenant's onboarding_progress for use during seeding
    $tenant->update([
        'onboarding_progress' => array_merge(
            $tenant->onboarding_progress ?? [],
            ['selected_accounts' => $selectedCodes]
        ),
    ]);

    return redirect()->route('tenant.onboarding.step', [
        'tenant' => $tenant->slug,
        'step' => 'complete',
    ]);
}
```

#### Updated `seedDefaultData()` — Only Seed Selected Accounts

```php
private function seedDefaultData(Tenant $tenant)
{
    // 1. Account Groups — always seed all 9 groups (required for structure)
    AccountGroupSeeder::seedForTenant($tenant->id);

    // 2. Voucher Types — always seed
    VoucherTypeSeeder::seedForTenant($tenant->id);

    // 3. Ledger Accounts — ONLY seed user-selected accounts
    $selectedCodes = $tenant->onboarding_progress['selected_accounts']
        ?? LedgerAccountCatalog::getDefaultSelection($tenant->getBusinessCategory());

    $this->seedSelectedLedgerAccounts($tenant, $selectedCodes);

    // 4. Banks, categories, units, shifts, PFAs, permissions (unchanged)
    DefaultBanksSeeder::seedForTenant($tenant->id);
    DefaultProductCategoriesSeeder::seedForTenant($tenant->id);
    DefaultUnitsSeeder::seedForTenant($tenant->id);
    DefaultShiftsSeeder::seedForTenant($tenant->id);
    DefaultPfasSeeder::seedForTenant($tenant->id);
    // ... permissions, roles
}

private function seedSelectedLedgerAccounts(Tenant $tenant, array $selectedCodes)
{
    $catalog = LedgerAccountCatalog::MASTER_CATALOG;
    $accountGroups = AccountGroup::where('tenant_id', $tenant->id)
        ->get()->keyBy('name');

    $accountsToSeed = collect($catalog)
        ->filter(fn($a) => in_array($a['code'], $selectedCodes))
        ->map(function ($account) use ($accountGroups, $tenant) {
            $group = $accountGroups->get($account['group']);
            if (!$group) return null;

            return [
                'tenant_id'         => $tenant->id,
                'name'              => $account['name'],
                'code'              => $account['code'],
                'account_group_id'  => $group->id,
                'account_type'      => $account['type'],
                'description'       => $account['description'],
                'opening_balance'   => 0,
                'current_balance'   => 0,
                'is_system_account' => $account['is_system'],
                'is_active'         => true,
            ];
        })
        ->filter()
        ->values();

    // Insert in chunks
    foreach ($accountsToSeed->chunk(20) as $chunk) {
        LedgerAccount::insert($chunk->toArray());
    }
}
```

#### Quick Start Flow (Skip Onboarding)

When a user clicks **"Quick Start"** on the onboarding index page (skips all steps), the system:

1. Auto-assigns business category from `business_type_id` (defaults to `hybrid`)
2. Auto-selects modules via `ModuleRegistry::getDefaultModules($category)`
3. Seeds **all default + suggested accounts** for the category (no user selection needed)
4. Seeds everything else as normal

This ensures the Quick Start path still works without user interaction.

### Phase 4: Master Ledger Account Catalog

**Estimate: 3–5 days**

- [ ] **Create `LedgerAccountCatalog` service** (`app/Services/LedgerAccountCatalog.php`):
    - Define `MASTER_CATALOG` const with all ~109 accounts
    - Each entry: `code`, `name`, `description`, `group`, `type`, `is_system`, `categories[]`, `core_for[]`, `suggested_for[]`
    - Methods: `getForCategory()`, `getDefaultSelection()`, `getCoreAccounts()`, `validateSelection()`

- [ ] **Populate catalog tags**: Tag every account with correct `categories`, `core_for`, and `suggested_for` arrays based on the classification tables above

- [ ] **Add account descriptions**: Every account gets a brief, user-friendly description (shown in the onboarding UI)

- [ ] **Create `accounts.blade.php` onboarding view**: Interactive account selection with collapsible groups, core locks, live counter

- [ ] **Update `OnboardingController`**:
    - Add `showAccountsStep()` and `saveAccountsStep()` methods
    - Update `showStep()` to accept `'accounts'` as valid step
    - Update `seedDefaultData()` to use `seedSelectedLedgerAccounts()`
    - Update `complete()` to handle Quick Start with auto-selection

- [ ] **Update onboarding progress bar**: 4 steps: Company Info → Preferences → Chart of Accounts → Complete

- [ ] **Update `preferences.blade.php`**: Change "Complete Setup" button to "Next: Chart of Accounts →"

- [ ] **Keep `DefaultLedgerAccountsSeeder`** as legacy fallback for edge cases (existing tenants, CLI seeding)

- [ ] **Add route**: `GET/POST /{tenant}/onboarding/accounts`

### Phase 5: Dashboard Customization

**Estimate: 5–7 days**

- [ ] **Create category-specific dashboard Blade partials** (or a single dynamic template):
    - `dashboard/service.blade.php` — Revenue, Outstanding Invoices, Active Projects, Upcoming Deadlines, Client Metrics
    - `dashboard/trading.blade.php` — Sales, Low Stock, Top Products, Cash Flow (current dashboard, refined)
    - `dashboard/manufacturing.blade.php` — Production, Raw Materials, COGS, Procurement
    - `dashboard/hybrid.blade.php` — Combined widgets with section tabs

- [ ] **Update `DashboardController::index()`**: Load different data based on `$tenant->getBusinessCategory()`:
    ```php
    $category = $tenant->getBusinessCategory();
    $viewData = match($category) {
        'service' => $this->getServiceDashboardData($tenant),
        'trading' => $this->getTradingDashboardData($tenant),
        'manufacturing' => $this->getManufacturingDashboardData($tenant),
        'hybrid' => $this->getHybridDashboardData($tenant),
    };
    return view("tenant.dashboard.{$category}", $viewData);
    ```

### Phase 6: Projects Module (New)

**Estimate: 2–3 weeks**

- [ ] **Create migrations**: `projects`, `project_tasks`, `project_milestones`, `project_notes`, `project_attachments`
- [ ] **Create models**: `Project`, `ProjectTask`, `ProjectMilestone`, `ProjectNote`, `ProjectAttachment`
- [ ] **Create controller**: `app/Http/Controllers/Tenant/Projects/ProjectController.php`
- [ ] **Create Blade views**:
    - `projects/index.blade.php` — list with filters (status, client, date range)
    - `projects/create.blade.php` — form with client search, budget, dates
    - `projects/show.blade.php` — tabbed view: Overview / Tasks / Milestones / Notes / Files
    - `projects/edit.blade.php` — edit form
    - Partials: `_task-list.blade.php`, `_milestone-list.blade.php`, `_notes.blade.php`
- [ ] **Register routes** in `routes/tenant.php` under `projects` prefix
- [ ] **Add permissions**: `projects.view`, `projects.create`, `projects.edit`, `projects.delete`, `projects.manage_tasks`
- [ ] **Seed default permissions** for Owner/Admin roles
- [ ] **Link to Invoicing**: "Generate Invoice from Project" action on completed milestones

### Phase 7: Service-Oriented Invoicing

**Estimate: 1 week**

- [ ] **Add "Service" item type** alongside physical products in invoice creation:
    - For service tenants: line items default to service type (no stock deduction)
    - For trading tenants: line items default to product type (with stock deduction)
    - For hybrid: toggle per line item

- [ ] **Service line item fields**: Description, Hours/Quantity, Rate, Discount, Tax, Amount
- [ ] **Product line item fields**: Product (search), Quantity, Unit Price, Discount, Tax, Amount (with stock deduction)

- [ ] **Update `InvoiceController::store()`**: Skip stock deduction logic for service items
- [ ] **Update voucher posting**: Service items don't affect inventory accounts

### Phase 8: Module Management in Company Settings

**Estimate: 3–5 days**

The module toggle lives **inside the existing Company Settings page** (`/{tenant}/settings/company`) as a new **"Modules" tab** — alongside the existing Company Information, Business Details, Branding & Logo, and Preferences tabs.

- [ ] **Add "Modules" tab** to `resources/views/tenant/settings/company.blade.php`:

    ```blade
    {{-- New tab button alongside existing 4 tabs --}}
    <button @click="activeTab = 'modules'"
            :class="activeTab === 'modules' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
        </svg>
        Modules
    </button>
    ```

- [ ] **Create Modules tab content panel** — grid of module cards with toggle switches:

    ```blade
    {{-- Modules Tab --}}
    <div x-show="activeTab === 'modules'" x-transition style="display: none;">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mt-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Module Management</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Enable or disable modules for your business.
                        Your business category ({{ ucfirst($tenant->getBusinessCategory()) }})
                        provides recommended defaults.
                    </p>
                </div>
                <button type="button" @click="resetToDefaults()"
                        class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                    Reset to Defaults
                </button>
            </div>

            <form method="POST" action="{{ route('tenant.settings.modules.update', $tenant) }}">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($allModules as $module)
                    <div class="border rounded-xl p-4 {{ $module['core'] ? 'bg-gray-50 border-gray-200' : 'bg-white border-gray-200 hover:border-purple-300' }} transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                    <i class="{{ $module['icon'] }} text-purple-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800">{{ $module['name'] }}</h4>
                                    <p class="text-xs text-gray-500">{{ $module['description'] }}</p>
                                </div>
                            </div>
                            <div>
                                @if($module['core'])
                                    <span class="text-xs text-gray-400 font-medium">Always On</span>
                                @else
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="modules[]" value="{{ $module['key'] }}"
                                               {{ in_array($module['key'], $enabledModules) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-purple-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                                    </label>
                                @endif
                            </div>
                        </div>
                        @if($module['recommended'])
                            <span class="inline-block mt-2 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                ✓ Recommended for {{ ucfirst($tenant->getBusinessCategory()) }}
                            </span>
                        @endif
                    </div>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <p class="text-xs text-gray-400">
                        Changes take effect immediately after saving. Disabling a module hides it from
                        the sidebar and blocks access to its routes. No data is deleted.
                    </p>
                    <button type="submit"
                            class="px-6 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium text-sm">
                        Save Module Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
    ```

- [ ] **Create `ModuleSettingsController`** (or add methods to existing `CompanySettingsController`):

    ```php
    // app/Http/Controllers/Tenant/Settings/ModuleSettingsController.php

    public function update(Request $request, Tenant $tenant)
    {
        $this->authorize('manage', $tenant); // Only Owner/Admin

        $validated = $request->validate([
            'modules'   => 'array',
            'modules.*' => 'string|in:' . implode(',', ModuleRegistry::ALL_MODULES),
        ]);

        // Always include core modules
        $modules = array_unique(array_merge(
            ModuleRegistry::CORE_MODULES,
            $validated['modules'] ?? []
        ));

        $tenant->update(['enabled_modules' => $modules]);

        return back()->with('success', 'Module settings updated successfully.');
    }
    ```

- [ ] **Pass module data to the settings view** from controller:

    ```php
    $allModules = ModuleRegistry::getAllModulesWithMeta($tenant);
    // Returns: [['key' => 'inventory', 'name' => 'Inventory', 'description' => '...',
    //            'icon' => 'fas fa-boxes', 'core' => false,
    //            'recommended' => true/false based on category], ...]
    ```

- [ ] **Add `getAllModulesWithMeta()` to `ModuleRegistry`**:

    ```php
    public static function getAllModulesWithMeta(Tenant $tenant): array
    {
        $category = $tenant->getBusinessCategory();
        $defaults = static::CATEGORY_DEFAULTS[$category] ?? static::CATEGORY_DEFAULTS['hybrid'];

        return collect(static::MODULE_META)->map(function ($meta, $key) use ($defaults) {
            return [
                'key'         => $key,
                'name'        => $meta['name'],
                'description' => $meta['description'],
                'icon'        => $meta['icon'],
                'core'        => in_array($key, static::CORE_MODULES),
                'recommended' => in_array($key, $defaults),
            ];
        })->values()->toArray();
    }

    public const MODULE_META = [
        'dashboard'   => ['name' => 'Dashboard',    'description' => 'Business overview and analytics',     'icon' => 'fas fa-tachometer-alt'],
        'accounting'  => ['name' => 'Accounting',   'description' => 'Chart of accounts, vouchers, ledger', 'icon' => 'fas fa-calculator'],
        'inventory'   => ['name' => 'Inventory',    'description' => 'Product stock tracking & management',   'icon' => 'fas fa-boxes'],
        'crm'         => ['name' => 'CRM',          'description' => 'Customer/client management',           'icon' => 'fas fa-users'],
        'pos'         => ['name' => 'POS',          'description' => 'Point of sale terminal',               'icon' => 'fas fa-cash-register'],
        'ecommerce'   => ['name' => 'E-commerce',   'description' => 'Online storefront & orders',           'icon' => 'fas fa-shopping-cart'],
        'procurement' => ['name' => 'Procurement',  'description' => 'Purchase orders & vendor management',  'icon' => 'fas fa-truck'],
        'projects'    => ['name' => 'Projects',     'description' => 'Client projects, tasks & milestones',  'icon' => 'fas fa-project-diagram'],
        'payroll'     => ['name' => 'Payroll',       'description' => 'Employee salary & benefits management','icon' => 'fas fa-money-check-alt'],
        'banking'     => ['name' => 'Banking',       'description' => 'Bank accounts & reconciliation',      'icon' => 'fas fa-university'],
        'reports'     => ['name' => 'Reports',       'description' => 'Business & financial reports',        'icon' => 'fas fa-chart-bar'],
        'statutory'   => ['name' => 'Tax',           'description' => 'Tax compliance & statutory filings',   'icon' => 'fas fa-file-invoice'],
        'audit'       => ['name' => 'Audit',         'description' => 'Audit trail & activity logs',          'icon' => 'fas fa-clipboard-check'],
        'admin'       => ['name' => 'Admin',         'description' => 'User & role management',               'icon' => 'fas fa-user-shield'],
        'settings'    => ['name' => 'Settings',      'description' => 'Company & app configuration',          'icon' => 'fas fa-cog'],
        'support'     => ['name' => 'Support',       'description' => 'Help desk & support tickets',          'icon' => 'fas fa-headset'],
        'help'        => ['name' => 'Help',          'description' => 'Documentation & guides',               'icon' => 'fas fa-question-circle'],
    ];
    ```

- [ ] **Add route**: `Route::put('settings/modules', [ModuleSettingsController::class, 'update'])->name('tenant.settings.modules.update')`

- [ ] **Permission gate**: Only users with `settings.manage` or `company.manage` permission can access the Modules tab. The toggle form is hidden for other roles.

- [ ] **API support for mobile**: `PUT /api/v1/tenant/{tenant}/settings/modules`

    ```php
    // Request: { "modules": ["accounting", "crm", "projects", "payroll", "banking", "reports", "statutory", "audit"] }
    // Response: { "success": true, "enabled_modules": [...], "message": "Module settings updated." }
    ```

- [ ] **Confirmation modal for disabling modules with existing data**:

    ```
    ⚠️ Disabling "Inventory" will hide it from the sidebar and block access.
    Your existing inventory data will NOT be deleted and can be restored
    by re-enabling the module. Continue?
    [Cancel] [Disable Module]
    ```

- [ ] **"Reset to Defaults" button**: Resets `enabled_modules` back to `ModuleRegistry::getDefaultModules($category)`. Shows confirmation: "This will reset your modules to the recommended defaults for a [Category] business. Continue?"

### Phase 9: Protect Routes for Disabled Modules

**Estimate: 2–3 days**

- [ ] **Create `CheckModuleAccess` middleware** (`app/Http/Middleware/CheckModuleAccess.php`):

    ```php
    public function handle($request, Closure $next, string $module)
    {
        if (!ModuleRegistry::isModuleEnabled(tenant(), $module)) {
            abort(403, 'This module is not enabled for your business.');
            // Or redirect to settings with a message
        }
        return $next($request);
    }
    ```

- [ ] **Register middleware alias** in `Kernel.php`: `'module' => CheckModuleAccess::class`

- [ ] **Apply to route groups** in `tenant.php`:

    ```php
    Route::prefix('inventory')->middleware('module:inventory')->group(function () { ... });
    Route::prefix('pos')->middleware('module:pos')->group(function () { ... });
    Route::prefix('projects')->middleware('module:projects')->group(function () { ... });
    ```

- [x] **Core modules skip the middleware**: Dashboard, Accounting, Settings, Admin, Support, Help — always accessible (`CORE_MODULES` in `ModuleRegistry` always pass; CheckModuleAccess implemented)

### Phase 10: Existing Tenant Migration

**Estimate: 2–3 days** ✅ COMPLETE

- [x] **Run `business_category` assignment** for all 97 business types in `BusinessTypeSeeder` update
- [x] **Create artisan command**: `php artisan tenants:assign-modules`
    - Loops through all existing tenants
    - Reads `business_type_id` → gets `business_category`
    - Sets `enabled_modules` to the default for that category
    - Tenants without `business_type_id`: default to `hybrid` (all modules enabled — no disruption)
    - `--force` flag to override already-configured tenants
    - `--dry-run` flag to preview without saving
    - **Safe for production**: Skips tenants that already have modules configured. Existing tenants keep seeing everything they currently see.

- [x] **Migration strategy for chart of accounts**: Do NOT re-seed existing tenants' ledger accounts. Category-specific seeding only applies to **new** tenants during onboarding. Existing tenants keep their current chart of accounts.

---

## Module Registry: Category-to-Module Mapping (Reference)

```php
// app/Services/ModuleRegistry.php

public const CATEGORY_DEFAULTS = [
    'trading' => [
        'dashboard', 'accounting', 'inventory', 'crm', 'pos',
        'ecommerce', 'procurement', 'payroll', 'banking',
        'reports', 'statutory', 'audit', 'admin', 'settings',
        'support', 'help',
    ],
    'manufacturing' => [
        'dashboard', 'accounting', 'inventory', 'crm',
        'procurement', 'payroll', 'banking', 'reports',
        'statutory', 'audit', 'admin', 'settings',
        'support', 'help',
    ],
    'service' => [
        'dashboard', 'accounting', 'crm', 'projects',
        'payroll', 'banking', 'reports', 'statutory',
        'audit', 'admin', 'settings', 'support', 'help',
    ],
    'hybrid' => [
        'dashboard', 'accounting', 'inventory', 'crm',
        'projects', 'pos', 'ecommerce', 'procurement',
        'payroll', 'banking', 'reports', 'statutory',
        'audit', 'admin', 'settings', 'support', 'help',
    ],
];

// Core modules that can never be disabled
public const CORE_MODULES = [
    'dashboard', 'accounting', 'admin', 'settings', 'support', 'help',
];
```

---

## Business Type → Category Mapping (Full Detail)

### Trading

| Business Type                  | Reason                  |
| ------------------------------ | ----------------------- |
| Retail Store                   | Physical goods sales    |
| E-commerce / Online Store      | Physical goods via web  |
| Wholesale & Distribution       | Bulk goods reselling    |
| Supermarket / Grocery Store    | Perishable goods retail |
| Boutique / Fashion Store       | Clothing/accessories    |
| Electronics & Appliances Store | Electronics reselling   |
| Furniture & Home Decor         | Home goods              |
| Automobile Sales               | Vehicle sales           |
| Vehicle Parts Sales            | Auto parts              |

### Manufacturing

| Business Type                           | Reason                  |
| --------------------------------------- | ----------------------- |
| Manufacturing / Production              | Core manufacturing      |
| Fabrication / Assembly                  | Assembly operations     |
| Construction Company                    | Build projects          |
| Civil Engineering / Building Contractor | Construction            |
| Chemical & Paint Production             | Chemical manufacturing  |
| Plastic, Paper, or Rubber Production    | Material production     |
| Packaging & Labelling                   | Production/packaging    |
| Crop Farming                            | Agricultural production |
| Livestock Farming                       | Animal production       |
| Agro-Processing & Packaging             | Food processing         |
| Oil & Gas                               | Extraction/refining     |
| Mining & Quarrying                      | Material extraction     |
| Food Processing & Packaging             | Food manufacturing      |

### Service

| Business Type                        | Reason                 |
| ------------------------------------ | ---------------------- |
| Consulting & Advisory Services       | Pure expertise         |
| Marketing & Advertising Agency       | Creative services      |
| Legal Services                       | Legal expertise        |
| Accounting & Financial Consulting    | Financial advisory     |
| Human Resource / Recruitment Agency  | HR services            |
| IT Services & Support                | Tech support           |
| Graphic Design / Branding Agency     | Design services        |
| Photography & Videography            | Creative production    |
| Education & Training                 | Knowledge delivery     |
| Healthcare & Wellness Services       | Health services        |
| Real Estate Agency                   | Property services      |
| Architecture / Engineering Services  | Design services        |
| Logistics & Delivery Services        | Transport services     |
| Haulage & Trucking                   | Freight services       |
| Ride-hailing / Taxi Service          | Mobility services      |
| Car Rentals & Leasing                | Rental services        |
| Freight Forwarding & Clearing        | Import/export services |
| Banking / Microfinance               | Financial services     |
| Insurance Services                   | Risk services          |
| Financial Technology (Fintech)       | Digital finance        |
| Software Development                 | Tech development       |
| Web & App Development                | Digital development    |
| SaaS / Cloud Services                | Cloud delivery         |
| Data Analytics / AI Solutions        | Data services          |
| Cybersecurity Services               | Security services      |
| Telecommunications / ISP             | Connectivity services  |
| NGO / Nonprofit Organization         | Social services        |
| Charity Foundation                   | Philanthropic          |
| Religious / Faith-based Organization | Faith services         |
| Community Development Project        | Social development     |
| Educational Institution              | Education delivery     |
| Government Department / Agency       | Public services        |
| Music Production / Record Label      | Creative services      |
| Film / Video Production              | Media production       |
| Event Promotion & Management         | Event services         |
| Content Creation / Influencer        | Digital content        |
| Media / Broadcasting                 | Media services         |
| Advertising / PR Agency              | Communications         |
| Laundry & Cleaning Services          | Cleaning services      |
| Security Services                    | Security provider      |
| Funeral Services                     | Mortuary services      |
| Pet Care & Grooming                  | Animal care            |
| Printing & Publishing                | Publishing services    |
| Printing & Stationery                | Print services         |
| Renewable Energy                     | Energy services        |
| Water & Irrigation Services          | Utility services       |
| Travel & Tourism Agency              | Travel services        |
| Investment / Asset Management        | Finance                |
| Cooperative / Credit Union           | Finance                |
| Cooperative Society                  | Social enterprise      |

### Hybrid

| Business Type                        | Reason                            |
| ------------------------------------ | --------------------------------- |
| Restaurant / Eatery                  | Food production + service         |
| Catering Services                    | Food production + delivery        |
| Bakery / Confectionery               | Bakes + sells                     |
| Bar / Lounge / Nightclub             | Beverages + entertainment         |
| Hotel / Guesthouse / Airbnb          | Hospitality (products + services) |
| Event Planning & Decoration          | Materials + services              |
| Interior Design & Renovation         | Materials + design                |
| Auto Repair Workshop                 | Parts + labor                     |
| Beauty Salon / Barbershop            | Products + services               |
| Tailoring & Fashion Design           | Materials + labor                 |
| Home Maintenance / Repair            | Parts + labor                     |
| Rental Services                      | Physical items + service          |
| Agricultural Equipment & Supplies    | Goods + support                   |
| Forestry & Logging                   | Extraction + processing           |
| Maritime / Shipping                  | Goods + transport                 |
| Aviation & Airline Services          | Transport + services              |
| Marketplace Platform                 | Platform + goods                  |
| Gaming & eSports                     | Digital + events                  |
| Performing Arts / Theatre            | Production + tickets              |
| Cryptocurrency / Blockchain Business | Digital + advisory                |
| Mixed or Multi-sector Business       | Multiple sectors                  |
| Import & Export                      | Goods + logistics                 |
| Start-up / Holding Company           | Multiple types                    |
| Cooperative Enterprise               | Combined                          |
| Other                                | Default to full access            |

---

## User Override & Flexibility

**Critical principle:** Module visibility is the **default** based on business category, but the **business owner can always override** it from **Company Settings > Modules tab**.

### How It Works

1. **At registration**: Business type auto-assigns a category (Trading/Manufacturing/Service/Hybrid)
2. **Category sets defaults**: `ModuleRegistry::getDefaultModules($category)` populates `enabled_modules`
3. **Owner can customize anytime**: Settings > Company > Modules tab shows all available modules with toggle switches
4. **Core modules are locked**: Dashboard, Accounting, Admin, Settings, Support, Help — always on, cannot be disabled
5. **No data loss**: Disabling a module only hides it from the sidebar and blocks route access. All data is preserved and restored when re-enabled.

### Example Scenarios

**Scenario 1 — Law firm that also sells legal books:**

1. Register as "Legal Services" → Service category → no Inventory by default
2. Go to Settings > Company > Modules tab → toggle ON "Inventory"
3. Inventory now appears in the sidebar, routes are accessible

**Scenario 2 — Restaurant that doesn't need E-commerce:**

1. Register as "Restaurant" → Hybrid category → all modules enabled by default
2. Go to Settings > Company > Modules tab → toggle OFF "E-commerce" and "POS"
3. Cleaner sidebar with only the modules they use

**Scenario 3 — Manufacturing company that takes on consulting:**

1. Register as "Manufacturing" → no Projects module by default
2. Go to Settings > Company > Modules tab → toggle ON "Projects"
3. Projects module appears in sidebar, full project management available

### Safety Guarantees

| Concern                    | How We Handle It                                                                         |
| -------------------------- | ---------------------------------------------------------------------------------------- |
| **Data loss on disable**   | No data is deleted. Ever. Disabling only hides the UI and blocks routes.                 |
| **Accidental disable**     | Confirmation modal: "Disabling X will hide it from the sidebar. Your data is preserved." |
| **Business owner only**    | Only users with `settings.manage` or Owner role can toggle modules.                      |
| **Core module protection** | Dashboard, Accounting, Admin, Settings, Support, Help cannot be disabled.                |
| **Reset option**           | "Reset to Defaults" button restores category-recommended modules.                        |
| **Mobile sync**            | Module changes sync to mobile app via API — bottom tabs update on next app launch.       |

This ensures:

- Clean UX for 90% of businesses (only see what's relevant)
- No limitation for the 10% who need cross-category features
- Zero business risk (nobody loses access to anything they already use)
- Full business owner control without needing to contact support

---

## Impact on Existing Tenants

| Concern                             | Resolution                                                                                                                                                                                           |
| ----------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Will existing tenants lose modules? | **No.** Tenants without `enabled_modules` or without `business_type_id` default to `hybrid` (all modules visible). No disruption.                                                                    |
| Will chart of accounts change?      | **No.** Category-specific COA only affects new tenants during onboarding. Existing tenants keep their current accounts.                                                                              |
| Will dashboard change?              | **Only if** the tenant has a `business_type_id` with a mapped category. Otherwise, current dashboard stays. Opt-in via Settings.                                                                     |
| Can tenants change their category?  | **Yes.** Settings > Company > Business Category dropdown. Changing category shows a confirmation: "This will update your recommended modules. You can re-enable any module from Settings > Modules." |

---

## Files to Create

| File                                                                   | Purpose                                             |
| ---------------------------------------------------------------------- | --------------------------------------------------- |
| `app/Http/Controllers/Tenant/Settings/ModuleSettingsController.php`    | Module toggle controller                            |
| `app/Services/ModuleRegistry.php`                                      | Central module registry with defaults per category  |
| `app/Services/LedgerAccountCatalog.php`                                | Master ledger account catalog with category tagging |
| `app/Services/TerminologyService.php`                                  | Business category terminology/label mapping         |
| `app/Http/Middleware/CheckModuleAccess.php`                            | Route-level module gate                             |
| `database/migrations/xxxx_add_business_category_to_business_types.php` | Add business_category enum                          |
| `database/migrations/xxxx_add_enabled_modules_to_tenants.php`          | Add enabled_modules JSON                            |
| `database/migrations/xxxx_create_projects_tables.php`                  | Projects, tasks, milestones, notes, attachments     |
| `app/Models/Tenant/Project.php`                                        | Project model                                       |
| `app/Models/Tenant/ProjectTask.php`                                    | Task model                                          |
| `app/Models/Tenant/ProjectMilestone.php`                               | Milestone model                                     |
| `app/Models/Tenant/ProjectNote.php`                                    | Note model                                          |
| `app/Models/Tenant/ProjectAttachment.php`                              | Attachment model                                    |
| `app/Http/Controllers/Tenant/Projects/ProjectController.php`           | Project CRUD controller                             |
| `resources/views/tenant/projects/*.blade.php`                          | Project views (~8-10 templates)                     |
| `resources/views/tenant/onboarding/steps/accounts.blade.php`           | NEW onboarding step — ledger account selection UI   |
| `resources/views/tenant/dashboard/service.blade.php`                   | Service dashboard                                   |
| `resources/views/tenant/dashboard/trading.blade.php`                   | Trading dashboard                                   |
| `resources/views/tenant/dashboard/manufacturing.blade.php`             | Manufacturing dashboard                             |
| `resources/views/tenant/dashboard/hybrid.blade.php`                    | Hybrid dashboard                                    |
| `app/Console/Commands/AssignTenantModules.php`                         | Artisan command for existing tenants                |

## Files to Modify

| File                                                            | Change                                                                              |
| --------------------------------------------------------------- | ----------------------------------------------------------------------------------- |
| `app/Models/BusinessType.php`                                   | Add `business_category` to fillable                                                 |
| `app/Models/Tenant.php`                                         | Add `enabled_modules` to fillable/casts, add `hasModule()`, `getBusinessCategory()` |
| `database/seeders/BusinessTypeSeeder.php`                       | Add `business_category` to all 97 types                                             |
| `resources/views/layouts/tenant/sidebar.blade.php`              | Wrap modules in `@module()` directives, use `$term->label()`                        |
| `resources/views/tenant/settings/company.blade.php`             | Add "Modules" tab with toggle grid UI                                               |
| `app/Http/Kernel.php`                                           | Register `module` middleware alias                                                  |
| `routes/tenant.php`                                             | Add `module:xxx` middleware to route groups + Projects routes + accounts onboarding |
| `app/Providers/AppServiceProvider.php`                          | Register `@module` Blade directive, share `enabledModules`, `$term`                 |
| `app/Http/Controllers/Tenant/DashboardController.php`           | Branch dashboard data by category                                                   |
| `app/Http/Controllers/Tenant/OnboardingController.php`          | Add accounts step, selective seeding, module toggle, 4-step flow                    |
| `resources/views/tenant/onboarding/steps/preferences.blade.php` | Add module selection section, change button to "Next: Chart of Accounts"            |
| `resources/views/tenant/onboarding/steps/complete.blade.php`    | Update progress bar to 4 steps                                                      |
| `resources/views/tenant/onboarding/index.blade.php`             | Update progress bar to 4 steps                                                      |
| `database/seeders/PermissionsSeeder.php`                        | Add project permissions + `settings.manage` permission                              |
| `app/Http/Controllers/Tenant/Accounting/InvoiceController.php`  | Support service items (no stock deduction)                                          |

---

## Rollout Order

| Phase     | Description                                       | Estimate        | Risk   |
| --------- | ------------------------------------------------- | --------------- | ------ |
| 1         | Database + ModuleRegistry + Tenant model          | 3–5 days        | Low    |
| 2         | Sidebar + navigation + TerminologyService         | 3–5 days        | Low    |
| 3         | Registration + onboarding (4-step flow + modules) | 5–7 days        | Medium |
| 4         | Master Ledger Account Catalog + selection UI      | 3–5 days        | Medium |
| 5         | Category-specific dashboards                      | 5–7 days        | Medium |
| 6         | Projects module (new)                             | 2–3 weeks       | Medium |
| 7         | Service-oriented invoicing                        | 1 week          | Medium |
| 8         | Module management in Company Settings             | 3–5 days        | Low    |
| 9         | Route middleware protection                       | 2–3 days        | Low    |
| 10        | Existing tenant migration command                 | 2–3 days        | Low    |
| **Total** |                                                   | **~8–10 weeks** |        |

---

## API Impact (Mobile)

The mobile API (`routes/api/v1/tenant.php`) must also respect module visibility:

- [ ] Share `enabled_modules` array in the auth/tenant info API response
- [ ] Mobile app hides/shows bottom tab items based on `enabled_modules`
- [ ] Add `module:xxx` middleware to API route groups
- [ ] Add Projects API endpoints when the module is built

---

## Testing Checklist

- [ ] Register a new "Consulting" business → confirm Service sidebar (no Inventory/POS)
- [ ] Register a new "Retail Store" → confirm Trading sidebar (no Projects)
- [ ] Register a new "Restaurant" → confirm Hybrid sidebar (everything + Projects)
- [ ] Register a new "Factory" → confirm Manufacturing sidebar (no POS/E-commerce/Projects)
- [ ] Existing tenant with no `business_type_id` → confirm all modules visible (hybrid default)
- [ ] Service tenant → enable Inventory from Settings > Modules → confirm it appears in sidebar
- [ ] Disabled module route → confirm 403 response when accessing URL directly
- [ ] New Service tenant → confirm simplified chart of accounts (no inventory/COGS accounts)
- [ ] New Trading tenant → confirm full chart of accounts with stock accounts
- [ ] **Onboarding Accounts Step**: Service category shows ~50 accounts, Inventory/COGS are hidden
- [ ] **Onboarding Accounts Step**: Trading category shows ~68 accounts, all stock accounts pre-checked
- [ ] **Onboarding Accounts Step**: Core accounts (🔒) cannot be unchecked — checkbox is disabled
- [ ] **Onboarding Accounts Step**: User can uncheck suggested accounts and add available ones
- [ ] **Onboarding Accounts Step**: Live counter updates ("X selected: Y required + Z optional")
- [ ] **Onboarding Accounts Step**: Select All / Deselect All works per group (skips core accounts)
- [ ] **Onboarding Accounts Step**: Quick Start seeds default+suggested accounts without user selection
- [ ] **Onboarding Accounts Step**: Only selected accounts are seeded to `ledger_accounts` table
- [ ] **Onboarding Accounts Step**: Server-side validation enforces core accounts even if bypassed in UI
- [ ] Dashboard renders correct category widgets
- [ ] **Module Settings**: Owner can access Modules tab in Company Settings
- [ ] **Module Settings**: Toggle a module OFF → sidebar hides it, route returns 403
- [ ] **Module Settings**: Toggle a module back ON → sidebar shows it, route works again
- [ ] **Module Settings**: Core modules (Dashboard, Accounting, etc.) cannot be toggled OFF
- [ ] **Module Settings**: "Reset to Defaults" restores category default modules
- [ ] **Module Settings**: Non-owner/admin users cannot see or access Modules tab
- [ ] **Module Settings**: Disabling a module does NOT delete any data
- [ ] **Module Settings**: Mobile API returns updated `enabled_modules` after toggle
- [ ] Projects module: CRUD, tasks, milestones, notes, attachments, invoice generation
- [ ] Invoice creation for Service tenant: service items only, no stock deduction
- [ ] Invoice creation for Hybrid tenant: toggle between product and service items
