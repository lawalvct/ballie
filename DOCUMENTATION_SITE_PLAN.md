# Ballie Public Documentation Site Plan (doc.ballie.co)

## TL;DR

Build a **VitePress documentation site** inside the existing Ballie Laravel repo (`docs/` folder) for `doc.ballie.co`. VitePress generates static HTML from Markdown — ideal for AI crawling — while giving users a Vue SPA experience. Same repo means AI assistants can read blade views + controllers and auto-generate accurate how-to guides. Host on the same server via Nginx subdomain config.

---

## Why Same Repo (Inside the Laravel Project)

| Reason | Detail |
|--------|--------|
| **AI can read blade views** | AI tools (Copilot, Claude, Cursor) see both `resources/views/` and `docs/` — can auto-generate docs from actual UI code |
| **Single workspace** | One VS Code window, one git repo, one `node_modules` |
| **No conflicts** | VitePress and Laravel's Vite are independent — separate build commands |
| **Content accuracy** | When a blade view changes, AI can update the matching docs page in the same commit |
| **Simpler maintenance** | No cross-repo syncing, no separate deploy pipeline |

**Location:** `c:\laragon\www\ballie\docs\` (inside existing project)

### Project Structure

```
c:\laragon\www\ballie\
├── app/                              # Laravel controllers, models
├── resources/views/                  # Blade views (AI reads these to write docs)
├── routes/                           # Routes (AI reads for URL paths)
├── public/                           # Laravel public (NOT where docs build goes)
├── docs/                             # ← VitePress lives here
│   ├── .vitepress/
│   │   ├── config.ts                 # Site config (nav, sidebar, head, hooks)
│   │   └── theme/
│   │       ├── index.ts              # Theme entry
│   │       └── custom.css            # Ballie brand CSS
│   ├── public/                       # Static assets (llms.txt, robots.txt, logo)
│   ├── index.md                      # Home page
│   ├── getting-started/              # Getting started guides
│   ├── accounting/                   # Accounting module docs
│   ├── inventory/                    # Inventory module docs
│   ├── crm/                          # CRM module docs
│   ├── pos/                          # POS module docs
│   ├── payroll/                      # Payroll & HR module docs
│   ├── banking/                      # Banking module docs
│   ├── projects/                     # Projects module docs
│   ├── ecommerce/                    # E-Commerce module docs
│   ├── procurement/                  # Procurement module docs
│   ├── reports/                      # Reports docs
│   ├── settings/                     # Settings docs
│   ├── mobile-app/                   # Mobile app docs
│   ├── audit-trail/                  # Audit trail docs
│   ├── support/                      # Support docs
│   └── faq.md                        # FAQ
├── package.json                      # Add docs:dev, docs:build scripts here
├── composer.json                     # Laravel (untouched)
└── .gitignore                        # Add docs/.vitepress/dist, docs/.vitepress/cache
```

### Build Output

VitePress builds to `docs/.vitepress/dist/` — Nginx serves that folder for `doc.ballie.co`. Laravel stays on `ballie.co`. **Same repo, two domains, independent builds.**

---

## Tech Stack

| Layer | Choice | Why |
|-------|--------|-----|
| **Framework** | VitePress (Vue-powered) | Purpose-built for docs, SPA feel, static HTML output |
| **Content** | Markdown files in `docs/` | Developer-friendly, version-controlled, great for AI crawling |
| **Search** | VitePress MiniSearch (built-in) | Local/offline, no external service |
| **Theme** | Default + Ballie brand overrides | Custom colors, logo, Inter font, dark mode |
| **Hosting** | Same server, Nginx subdomain | `doc.ballie.co` → static files from `docs/.vitepress/dist/` |
| **AI features** | `llms.txt`, `llms-full.txt`, sitemap, JSON-LD | Optimized for AI model crawling |

---

## Phase 1: Project Scaffolding

### Step 1 — Install VitePress (in existing project)

```bash
cd c:\laragon\www\ballie
npm add -D vitepress
npx vitepress init
```

VitePress init will ask:
- Where to put docs? → `./docs`
- Site title? → `Ballie Documentation`
- Description? → `Complete guide to using Ballie — the all-in-one business management platform`
- Theme? → Default Theme + Customization
- TypeScript config? → Yes

### Step 1b — Add Scripts to package.json

Add these alongside existing `build` and `dev` scripts:

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "docs:dev": "vitepress dev docs",
    "docs:build": "vitepress build docs",
    "docs:preview": "vitepress preview docs"
  }
}
```

### Step 1c — Update .gitignore

```
docs/.vitepress/dist
docs/.vitepress/cache
```

### Step 2 — Configure Site (`docs/.vitepress/config.ts`)

> **Note:** This file is auto-created by `npx vitepress init`. Replace its contents with:

```ts
import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'Ballie Documentation',
  description: 'Complete guide to using Ballie — the all-in-one business management platform for Nigerian businesses',
  lang: 'en-US',
  cleanUrls: true,
  lastUpdated: true,

  sitemap: {
    hostname: 'https://doc.ballie.co'
  },

  head: [
    ['link', { rel: 'icon', href: '/favicon.ico' }],
    ['meta', { name: 'theme-color', content: '#2b6399' }],
    ['meta', { property: 'og:type', content: 'website' }],
    ['meta', { property: 'og:site_name', content: 'Ballie Documentation' }],
    ['meta', { property: 'og:image', content: 'https://doc.ballie.co/og-image.png' }],
  ],

  themeConfig: {
    logo: '/logo.svg',
    siteTitle: 'Ballie Docs',

    nav: [
      { text: 'Getting Started', link: '/getting-started/' },
      {
        text: 'Modules',
        items: [
          { text: 'Accounting', link: '/accounting/' },
          { text: 'Inventory', link: '/inventory/' },
          { text: 'CRM', link: '/crm/' },
          { text: 'POS', link: '/pos/' },
          { text: 'Payroll & HR', link: '/payroll/' },
          { text: 'Banking', link: '/banking/' },
          { text: 'Projects', link: '/projects/' },
          { text: 'E-Commerce', link: '/ecommerce/' },
          { text: 'Procurement', link: '/procurement/' },
        ]
      },
      { text: 'Reports', link: '/reports/' },
      { text: 'Settings', link: '/settings/' },
      { text: 'Mobile App', link: '/mobile-app/' },
      { text: 'FAQ', link: '/faq' },
    ],

    sidebar: {
      '/getting-started/': [
        {
          text: 'Getting Started',
          items: [
            { text: 'What is Ballie?', link: '/getting-started/' },
            { text: 'Registration', link: '/getting-started/registration' },
            { text: 'Onboarding Wizard', link: '/getting-started/onboarding' },
            { text: 'Navigating the Dashboard', link: '/getting-started/navigation' },
          ]
        }
      ],
      '/accounting/': [
        {
          text: 'Accounting',
          items: [
            { text: 'Overview', link: '/accounting/' },
            { text: 'Chart of Accounts', link: '/accounting/chart-of-accounts' },
            { text: 'Invoices', link: '/accounting/invoices' },
            { text: 'Quotations & Proforma', link: '/accounting/quotations' },
            { text: 'Vouchers', link: '/accounting/vouchers' },
            { text: 'Expenses', link: '/accounting/expenses' },
            { text: 'Payments', link: '/accounting/payments' },
            { text: 'Prepaid Expenses', link: '/accounting/prepaid-expenses' },
          ]
        }
      ],
      '/inventory/': [
        {
          text: 'Inventory',
          items: [
            { text: 'Overview', link: '/inventory/' },
            { text: 'Products', link: '/inventory/products' },
            { text: 'Categories & Units', link: '/inventory/categories' },
            { text: 'Stock Management', link: '/inventory/stock-management' },
            { text: 'Physical Stock Audit', link: '/inventory/physical-stock' },
          ]
        }
      ],
      '/crm/': [
        {
          text: 'CRM',
          items: [
            { text: 'Overview', link: '/crm/' },
            { text: 'Customers', link: '/crm/customers' },
            { text: 'Vendors', link: '/crm/vendors' },
            { text: 'CRM Reports', link: '/crm/reports' },
          ]
        }
      ],
      '/pos/': [
        {
          text: 'Point of Sale',
          items: [
            { text: 'Overview', link: '/pos/' },
            { text: 'Cash Register', link: '/pos/cash-register' },
            { text: 'Making Sales', link: '/pos/making-sales' },
          ]
        }
      ],
      '/payroll/': [
        {
          text: 'Payroll & HR',
          items: [
            { text: 'Overview', link: '/payroll/' },
            { text: 'Employees', link: '/payroll/employees' },
            { text: 'Departments & Positions', link: '/payroll/departments-positions' },
            { text: 'Salary Components', link: '/payroll/salary-components' },
            { text: 'Attendance & Shifts', link: '/payroll/attendance' },
            { text: 'Leave Management', link: '/payroll/leave-management' },
            { text: 'Loans & Advances', link: '/payroll/loans-advances' },
            { text: 'Running Payroll', link: '/payroll/payroll-processing' },
            { text: 'Statutory Compliance', link: '/payroll/statutory' },
          ]
        }
      ],
      '/banking/': [
        {
          text: 'Banking',
          items: [
            { text: 'Overview', link: '/banking/' },
            { text: 'Bank Accounts', link: '/banking/bank-accounts' },
            { text: 'Reconciliation', link: '/banking/reconciliation' },
          ]
        }
      ],
      '/projects/': [
        {
          text: 'Projects',
          items: [
            { text: 'Overview', link: '/projects/' },
            { text: 'Managing Projects', link: '/projects/managing-projects' },
            { text: 'Project Expenses', link: '/projects/project-expenses' },
          ]
        }
      ],
      '/ecommerce/': [
        {
          text: 'E-Commerce',
          items: [
            { text: 'Overview', link: '/ecommerce/' },
            { text: 'Store Setup', link: '/ecommerce/store-setup' },
            { text: 'Orders', link: '/ecommerce/orders' },
            { text: 'Coupons', link: '/ecommerce/coupons' },
            { text: 'Shipping', link: '/ecommerce/shipping' },
          ]
        }
      ],
      '/procurement/': [
        {
          text: 'Procurement',
          items: [
            { text: 'Overview', link: '/procurement/' },
            { text: 'Purchase Orders', link: '/procurement/purchase-orders' },
          ]
        }
      ],
      '/reports/': [
        {
          text: 'Reports',
          items: [
            { text: 'Overview', link: '/reports/' },
            { text: 'Financial Reports', link: '/reports/financial-reports' },
            { text: 'Sales Reports', link: '/reports/sales-reports' },
            { text: 'Purchase Reports', link: '/reports/purchase-reports' },
            { text: 'Inventory Reports', link: '/reports/inventory-reports' },
          ]
        }
      ],
      '/settings/': [
        {
          text: 'Settings',
          items: [
            { text: 'Overview', link: '/settings/' },
            { text: 'Company Settings', link: '/settings/company-settings' },
            { text: 'Users & Roles', link: '/settings/users-roles' },
            { text: 'Modules', link: '/settings/modules' },
            { text: 'Subscription & Billing', link: '/settings/subscription' },
          ]
        }
      ],
      '/mobile-app/': [
        {
          text: 'Mobile App',
          items: [
            { text: 'Overview', link: '/mobile-app/' },
            { text: 'Installation', link: '/mobile-app/installation' },
            { text: 'Mobile Features', link: '/mobile-app/features' },
          ]
        }
      ],
      '/audit-trail/': [
        {
          text: 'Audit Trail',
          items: [
            { text: 'Overview', link: '/audit-trail/' },
            { text: 'Viewing Audits', link: '/audit-trail/viewing-audits' },
          ]
        }
      ],
      '/support/': [
        {
          text: 'Support',
          items: [
            { text: 'Overview', link: '/support/' },
            { text: 'Support Tickets', link: '/support/tickets' },
          ]
        }
      ],
    },

    search: {
      provider: 'local',
      options: {
        detailedView: true,
      }
    },

    socialLinks: [
      { icon: 'twitter', link: 'https://twitter.com/ballieapp' },
    ],

    footer: {
      message: 'Ballie — The All-in-One Business Management Platform',
      copyright: 'Copyright © 2024-present Ballie Technologies'
    },

    editLink: {
      pattern: 'https://github.com/your-org/ballie-docs/edit/main/docs/:path',
      text: 'Edit this page'
    },
  },

  // Auto-generate llms-full.txt at build time
  async buildEnd(siteConfig) {
    const fs = await import('fs')
    const path = await import('path')
    const glob = await import('glob')

    const docsDir = siteConfig.srcDir
    const outDir = siteConfig.outDir
    const pages = glob.sync('**/*.md', { cwd: docsDir })

    let fullContent = '# Ballie Documentation — Full Content\n\n'
    fullContent += '> This file contains the complete documentation for Ballie, a multi-tenant business management platform.\n'
    fullContent += '> Generated automatically from doc.ballie.co\n\n'

    for (const page of pages) {
      const content = fs.readFileSync(path.join(docsDir, page), 'utf-8')
      const cleanContent = content
        .replace(/^---[\s\S]*?---\n/m, '') // Remove frontmatter
        .replace(/```[\s\S]*?```/g, '')     // Remove code blocks (keep it readable)
        .trim()

      if (cleanContent) {
        fullContent += `\n---\nSource: ${page}\n\n${cleanContent}\n`
      }
    }

    fs.writeFileSync(path.join(outDir, 'llms-full.txt'), fullContent)
    console.log('✅ Generated llms-full.txt')
  },

  // Inject JSON-LD structured data per page
  transformHead({ pageData }) {
    const jsonLd = {
      '@context': 'https://schema.org',
      '@type': 'TechArticle',
      'headline': pageData.title,
      'description': pageData.description || pageData.frontmatter?.description || '',
      'url': `https://doc.ballie.co/${pageData.relativePath.replace(/\.md$/, '')}`,
      'publisher': {
        '@type': 'Organization',
        'name': 'Ballie Technologies',
        'url': 'https://ballie.co'
      }
    }

    return [
      ['script', { type: 'application/ld+json' }, JSON.stringify(jsonLd)]
    ]
  }
})
```

### Step 3 — Brand Theme (`docs/.vitepress/theme/index.ts`)

```ts
import DefaultTheme from 'vitepress/theme'
import './custom.css'

export default DefaultTheme
```

### Step 4 — Custom CSS (`docs/.vitepress/theme/custom.css`)

```css
/* Ballie Brand Colors */
:root {
  /* Brand palette */
  --ballie-blue: #2b6399;
  --ballie-dark-purple: #3c2c64;
  --ballie-teal: #69a2a4;
  --ballie-purple: #85729d;
  --ballie-gold: #d1b05e;
  --ballie-green: #249484;
  --ballie-lavender: #a48cb4;
  --ballie-violet: #614c80;
  --ballie-light-blue: #7b87b8;

  /* VitePress theme overrides */
  --vp-c-brand-1: #2b6399;
  --vp-c-brand-2: #3a7ab8;
  --vp-c-brand-3: #4a8ad0;
  --vp-c-brand-soft: rgba(43, 99, 153, 0.14);
  --vp-c-tip-1: #249484;
  --vp-c-tip-soft: rgba(36, 148, 132, 0.14);

  /* Font */
  --vp-font-family-base: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;

  /* Home hero gradient */
  --vp-home-hero-name-color: transparent;
  --vp-home-hero-name-background: linear-gradient(135deg, #2b6399 0%, #3c2c64 50%, #69a2a4 100%);
  --vp-home-hero-image-background-image: linear-gradient(135deg, #2b639940 0%, #3c2c6440 50%, #69a2a440 100%);
  --vp-home-hero-image-filter: blur(44px);
}

.dark {
  --vp-c-brand-1: #4a9ae6;
  --vp-c-brand-2: #5aace8;
  --vp-c-brand-3: #6abcf0;
  --vp-c-brand-soft: rgba(74, 154, 230, 0.14);
}

/* Sidebar active indicator */
.VPSidebar .is-active > .link > .text {
  color: var(--vp-c-brand-1) !important;
  font-weight: 600;
}
```

---

## Phase 2: Content Structure

### Full Folder Structure (inside `docs/`)

```
docs/
├── .vitepress/
│   ├── config.ts                     # Site configuration (nav, sidebar, head, hooks)
│   └── theme/
│       ├── index.ts                  # Theme entry (extends default)
│       └── custom.css                # Ballie brand overrides
├── public/
│   ├── logo.svg                      # Ballie logo
│   ├── favicon.ico                   # Favicon
│   ├── og-image.png                  # Open Graph social share image
│   ├── robots.txt                    # Crawler permissions
│   └── llms.txt                      # AI model instructions (hand-written)
├── index.md                          # Home / hero landing page
│
├── getting-started/
│   ├── index.md                      # What is Ballie
│   ├── registration.md               # Sign up, create business, choose plan
│   ├── onboarding.md                 # 4-step onboarding wizard
│   └── navigation.md                 # Dashboard layout, sidebar, global search
│
├── accounting/
│   ├── index.md                      # Accounting module overview
│   ├── chart-of-accounts.md          # Account groups, ledger accounts, default chart
│   ├── invoices.md                   # Create, edit, send, track, payment recording
│   ├── quotations.md                 # Proforma invoices, convert to invoice
│   ├── vouchers.md                   # Journal vouchers, voucher types, post/unpost
│   ├── expenses.md                   # Expense creation, categories, recurring
│   ├── payments.md                   # Payment methods, recording, bulk payments
│   └── prepaid-expenses.md           # Prepaid expense setup, amortization schedule
│
├── inventory/
│   ├── index.md                      # Inventory module overview
│   ├── products.md                   # Add/edit products, images, pricing, SKU
│   ├── categories.md                 # Product categories and units
│   ├── stock-management.md           # Stock journals, movements, transfers
│   └── physical-stock.md             # Physical stock audit, count vouchers
│
├── crm/
│   ├── index.md                      # CRM module overview
│   ├── customers.md                  # Customer CRUD, import, opening balances, activities
│   ├── vendors.md                    # Vendor management, import
│   └── reports.md                    # Customer/vendor reports, aging
│
├── pos/
│   ├── index.md                      # POS module overview
│   ├── cash-register.md              # Cash register setup, opening/closing sessions
│   └── making-sales.md               # POS checkout, payment split, receipts
│
├── payroll/
│   ├── index.md                      # Payroll & HR module overview
│   ├── employees.md                  # Employee profiles, documents, onboarding
│   ├── departments-positions.md      # Department structure, positions, hierarchy
│   ├── salary-components.md          # Earnings, deductions, allowances, tax brackets
│   ├── attendance.md                 # QR code check-in, shift schedules, overtime
│   ├── leave-management.md           # Leave types, requests, approvals, balances
│   ├── loans-advances.md             # Employee loans, repayment schedules
│   ├── payroll-processing.md         # Pay periods, running payroll, payslips
│   └── statutory.md                  # PAYE, pension (PFA), NHF, statutory deductions
│
├── banking/
│   ├── index.md                      # Banking module overview
│   ├── bank-accounts.md              # Add bank accounts, opening balances
│   └── reconciliation.md             # Bank reconciliation workflow
│
├── projects/
│   ├── index.md                      # Projects module overview
│   ├── managing-projects.md          # Create projects, tasks, milestones, attachments
│   └── project-expenses.md           # Track project-related expenses
│
├── ecommerce/
│   ├── index.md                      # E-Commerce module overview
│   ├── store-setup.md                # Storefront settings, branding, domain
│   ├── orders.md                     # Order management, fulfillment, status tracking
│   ├── coupons.md                    # Create discount coupons, usage limits
│   └── shipping.md                   # Shipping methods, rates, zones
│
├── procurement/
│   ├── index.md                      # Procurement module overview
│   └── purchase-orders.md            # Create, approve, receive purchase orders
│
├── reports/
│   ├── index.md                      # Reports overview
│   ├── financial-reports.md          # P&L, balance sheet, trial balance, cash flow
│   ├── sales-reports.md              # Sales by period, customer, product
│   ├── purchase-reports.md           # Purchase analytics, vendor spend
│   └── inventory-reports.md          # Stock valuation, movement reports
│
├── settings/
│   ├── index.md                      # Settings overview
│   ├── company-settings.md           # Company info, logo, branding, preferences
│   ├── users-roles.md                # Invite users, create roles, assign permissions
│   ├── modules.md                    # Enable/disable modules per business needs
│   └── subscription.md               # Plan tiers, upgrade, billing history
│
├── mobile-app/
│   ├── index.md                      # Mobile app overview
│   ├── installation.md               # Download APK/IPA, install, first login
│   └── features.md                   # Offline mode, push notifications, mobile POS
│
├── audit-trail/
│   ├── index.md                      # Audit trail overview
│   └── viewing-audits.md             # Filter, search, export audit logs
│
├── support/
│   ├── index.md                      # Support overview
│   └── tickets.md                    # Submit and track support tickets
│
└── faq.md                            # Frequently asked questions
```

**Total: ~55 documentation pages** covering all 15+ modules.

---

## Phase 3: AI Crawlability

### `llms.txt` (hand-written, placed in `docs/public/llms.txt`)

```txt
# Ballie Documentation

> Ballie is an all-in-one multi-tenant business management platform for Nigerian businesses. It includes accounting, inventory, CRM, POS, payroll & HR, banking, project management, e-commerce, procurement, and reporting modules. This documentation covers how to use every feature.

## Documentation URL
https://doc.ballie.co

## Key Sections

### Getting Started
- /getting-started/: Overview of what Ballie is and who it's for
- /getting-started/registration: How to sign up, create a business, and choose a plan
- /getting-started/onboarding: The 4-step onboarding wizard (company info, preferences, chart of accounts, completion)
- /getting-started/navigation: Dashboard layout, sidebar navigation, global search

### Accounting
- /accounting/: Accounting module overview and how double-entry bookkeeping works in Ballie
- /accounting/chart-of-accounts: Account groups, ledger accounts, default chart setup
- /accounting/invoices: Creating, editing, sending, tracking invoices, and recording payments
- /accounting/quotations: Creating proforma invoices and converting to invoices
- /accounting/vouchers: Journal vouchers, voucher types, posting and unposting
- /accounting/expenses: Recording expenses, categorization
- /accounting/payments: Payment methods and recording payments received/made
- /accounting/prepaid-expenses: Setting up prepaid expenses with amortization schedules

### Inventory
- /inventory/: Inventory module overview
- /inventory/products: Adding products, images, pricing, SKU management
- /inventory/categories: Product categories and units of measurement
- /inventory/stock-management: Stock journals, stock movements, transfers between locations
- /inventory/physical-stock: Physical stock audit process and count vouchers

### CRM
- /crm/: CRM module overview
- /crm/customers: Customer management, bulk import, opening balances, activity tracking
- /crm/vendors: Vendor management and import
- /crm/reports: Customer and vendor reports, aging analysis

### Point of Sale
- /pos/: POS module overview
- /pos/cash-register: Setting up cash registers, opening and closing sessions
- /pos/making-sales: POS checkout process, split payments, receipt printing

### Payroll & HR
- /payroll/: Payroll module overview
- /payroll/employees: Employee profiles, documents, onboarding
- /payroll/departments-positions: Organizational structure
- /payroll/salary-components: Earnings, deductions, allowances, tax brackets
- /payroll/attendance: QR code check-in/out, shift schedules, overtime tracking
- /payroll/leave-management: Leave types, requests, approvals, balance tracking
- /payroll/loans-advances: Employee loan management and repayment
- /payroll/payroll-processing: Running payroll, generating payslips, pay periods
- /payroll/statutory: PAYE tax, pension (PFA), NHF, statutory compliance

### Banking
- /banking/: Banking module overview
- /banking/bank-accounts: Adding bank accounts, setting opening balances
- /banking/reconciliation: Bank reconciliation workflow

### Projects
- /projects/: Projects module overview
- /projects/managing-projects: Tasks, milestones, team assignment, attachments
- /projects/project-expenses: Tracking project-specific expenses

### E-Commerce
- /ecommerce/: E-Commerce module overview
- /ecommerce/store-setup: Storefront configuration, branding, custom domain
- /ecommerce/orders: Order management and fulfillment
- /ecommerce/coupons: Discount coupon creation and management
- /ecommerce/shipping: Shipping methods and rate configuration

### Procurement
- /procurement/: Procurement module overview
- /procurement/purchase-orders: Creating, approving, and receiving purchase orders

### Reports
- /reports/: Reports overview
- /reports/financial-reports: Profit & loss, balance sheet, trial balance, cash flow
- /reports/sales-reports: Sales analytics by period, customer, product
- /reports/purchase-reports: Purchase analytics and vendor spend analysis
- /reports/inventory-reports: Stock valuation and movement reports

### Settings & Administration
- /settings/: Settings overview
- /settings/company-settings: Company information, branding, logo, preferences
- /settings/users-roles: User management, roles, permissions, teams
- /settings/modules: Enabling and disabling business modules
- /settings/subscription: Subscription plans, upgrades, billing

### Mobile App
- /mobile-app/: Mobile app overview
- /mobile-app/installation: Download and install for Android/iOS
- /mobile-app/features: Offline mode, push notifications, mobile POS

### Audit Trail
- /audit-trail/: Audit system overview
- /audit-trail/viewing-audits: Search, filter, and export audit logs

### Support
- /support/: Support overview
- /support/tickets: Creating and tracking support tickets

### FAQ
- /faq: Frequently asked questions about Ballie
```

### `llms-full.txt` — Auto-generated

The VitePress `buildEnd` hook in `config.ts` (shown above) automatically concatenates all markdown page content into a single `llms-full.txt` file at build time. This ensures:
- Always in sync with actual docs content
- No manual maintenance needed
- AI models can ingest the entire docs site in one request

### `robots.txt`

```txt
User-agent: *
Allow: /

Sitemap: https://doc.ballie.co/sitemap.xml
```

### Structured Data (JSON-LD)

Injected per page via the `transformHead` hook in `config.ts`:

```json
{
  "@context": "https://schema.org",
  "@type": "TechArticle",
  "headline": "Creating Invoices in Ballie",
  "description": "Step-by-step guide to creating, sending, and tracking invoices",
  "url": "https://doc.ballie.co/accounting/invoices",
  "publisher": {
    "@type": "Organization",
    "name": "Ballie Technologies",
    "url": "https://ballie.co"
  }
}
```

---

## Phase 4: Deployment

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name doc.ballie.co;

    root /var/www/ballie/docs/.vitepress/dist;
    index index.html;

    # SPA fallback — VitePress generates .html files with clean URLs
    location / {
        try_files $uri $uri/ $uri.html /404.html;
    }

    # Cache static assets aggressively
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff2?)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options DENY;
    add_header Referrer-Policy strict-origin-when-cross-origin;
    add_header X-XSS-Protection "1; mode=block";
}
```

For HTTPS (recommended), add Certbot/Let's Encrypt:
```bash
sudo certbot --nginx -d doc.ballie.co
```

### DNS Setup

Add an A record or CNAME for `doc.ballie.co` pointing to the same server IP as `ballie.co`.

### Deploy Script (`deploy-docs.sh`)

```bash
#!/bin/bash
cd /var/www/ballie
git pull origin main
npm install
npm run docs:build
echo "✅ Docs deployed at $(date)"
```

### Package.json Scripts

Already added in Step 1b:

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "docs:dev": "vitepress dev docs",
    "docs:build": "vitepress build docs",
    "docs:preview": "vitepress preview docs"
  }
}
```

---

## Phase 5: Content Writing Guidelines

### Page Template

Every documentation page should follow this structure:

```md
---
title: Creating Invoices
description: Step-by-step guide to creating, sending, and tracking invoices in Ballie
---

# Creating Invoices

Ballie's invoicing system lets you create professional invoices, send them to customers via email or WhatsApp, track payment status, and automatically post accounting entries. Invoices integrate with your chart of accounts, customer records, and inventory.

## Prerequisites

- You must have the **Accounting** module enabled (Settings → Modules)
- At least one customer must exist in your CRM
- Your chart of accounts should be configured (done during onboarding)

## Creating a New Invoice

1. Navigate to **Accounting → Invoices** from the sidebar
2. Click the **+ New Invoice** button
3. Fill in the invoice details:
   - **Customer**: Select from your customer list
   - **Invoice Date**: Defaults to today
   - **Due Date**: Set the payment deadline
   - **Items**: Add products/services with quantity and rate
4. Click **Save as Draft** or **Save & Send**

::: tip
You can convert a quotation directly into an invoice from the Quotations page — no need to re-enter items.
:::

## Invoice Statuses

| Status | Meaning |
|--------|---------|
| Draft | Not yet sent to customer |
| Sent | Emailed/shared with customer |
| Partially Paid | Some payment received |
| Paid | Fully paid |
| Overdue | Past due date with balance remaining |

## Recording a Payment

1. Open the invoice
2. Click **Record Payment**
3. Enter the amount and payment method
4. The ledger entries are created automatically

::: warning
Once a payment is recorded and the voucher is posted, it affects your ledger balances. Use the Unpost feature if you need to reverse.
:::

## Related Pages

- [Quotations & Proforma](/accounting/quotations) — Create quotations and convert to invoices
- [Payments](/accounting/payments) — Payment recording and methods
- [Financial Reports](/reports/financial-reports) — View revenue in Profit & Loss
- [Customers](/crm/customers) — Managing customer records
```

### Writing Rules for AI Discoverability

1. **Start every page** with a 1–2 sentence summary of what the feature does
2. **Use descriptive headings** — "Creating a New Invoice" not just "Create"
3. **Include prerequisites** — what must be set up before using a feature
4. **Number the steps** — AI models parse numbered lists better than prose
5. **Add tables** for statuses, options, field descriptions
6. **Use callout blocks** — `:::tip`, `:::warning`, `:::danger` for important notes
7. **Cross-link related pages** in a "Related Pages" section at the bottom
8. **Include frontmatter** with `title` and `description` on every page
9. **Use real Ballie terminology** — sidebar names, button labels, field names exactly as they appear in the app

---

## Phase 6: Polish

### Home Page (`docs/index.md`)

```md
---
layout: home
title: Ballie Documentation
description: Complete guide to using Ballie — the all-in-one business management platform

hero:
  name: Ballie Documentation
  text: Your complete guide to Ballie
  tagline: Learn how to manage your business with accounting, inventory, CRM, POS, payroll, and more.
  actions:
    - theme: brand
      text: Get Started
      link: /getting-started/
    - theme: alt
      text: Browse Modules
      link: /accounting/

features:
  - icon: 📊
    title: Accounting
    details: Double-entry bookkeeping, invoices, vouchers, expenses, and financial reports.
    link: /accounting/
  - icon: 📦
    title: Inventory
    details: Product management, stock tracking, journals, and physical audits.
    link: /inventory/
  - icon: 👥
    title: CRM
    details: Customer and vendor management, import, activities, and aging reports.
    link: /crm/
  - icon: 🛒
    title: Point of Sale
    details: Cash register sessions, quick checkout, split payments, and receipts.
    link: /pos/
  - icon: 💰
    title: Payroll & HR
    details: Employees, attendance, leave, loans, salary processing, and statutory compliance.
    link: /payroll/
  - icon: 🏦
    title: Banking
    details: Bank account management and reconciliation.
    link: /banking/
  - icon: 📋
    title: Projects
    details: Project tracking with tasks, milestones, and expense management.
    link: /projects/
  - icon: 🌐
    title: E-Commerce
    details: Online storefront, orders, coupons, and shipping configuration.
    link: /ecommerce/
  - icon: 📱
    title: Mobile App
    details: Manage your business on the go with offline support.
    link: /mobile-app/
---
```

### Features to Enable

| Feature | Config |
|---------|--------|
| Last updated timestamps | `lastUpdated: true` in config |
| Edit this page links | `editLink` in themeConfig |
| Built-in local search | `search.provider: 'local'` in themeConfig |
| Sitemap | `sitemap.hostname` in config |
| Dark mode | Built-in, automatic |
| Clean URLs | `cleanUrls: true` in config |

---

## Files to Update in Main App (After Docs Go Live)

| File | Change |
|------|--------|
| `resources/views/layouts/website/footer.blade.php` | Update `href="#"` on "Documentation" link → `https://doc.ballie.co` |
| `resources/views/tenant/help/index.blade.php` | Replace "Documentation coming soon" → link to `https://doc.ballie.co` |
| `resources/views/layouts/tenant/sidebar.blade.php` | Update "Help & Documentation" link → `https://doc.ballie.co` |

---

## Implementation Checklist

### Setup
- [ ] Run `npm add -D vitepress` in the Ballie project root
- [ ] Run `npx vitepress init` (select `./docs` as location)
- [ ] Add `docs:dev`, `docs:build`, `docs:preview` scripts to `package.json`
- [ ] Add `docs/.vitepress/dist` and `docs/.vitepress/cache` to `.gitignore`
- [ ] Configure `docs/.vitepress/config.ts` (nav, sidebar, sitemap, head, hooks)
- [ ] Add brand theme CSS (`docs/.vitepress/theme/custom.css`)
- [ ] Add `robots.txt` and `llms.txt` to `docs/public/`
- [ ] Add Ballie logo SVG and favicon to `docs/public/`
- [ ] Test: `npm run docs:dev` — verify site loads at `localhost:5173`

### Content (write in priority order)
- [ ] Home page (`docs/index.md`)
- [ ] Getting Started (4 pages)
- [ ] Accounting (8 pages) — most used module
- [ ] Inventory (5 pages)
- [ ] CRM (4 pages)
- [ ] POS (3 pages)
- [ ] Payroll & HR (9 pages) — complex module
- [ ] Banking (3 pages)
- [ ] Reports (5 pages)
- [ ] Settings (5 pages)
- [ ] E-Commerce (5 pages)
- [ ] Projects (3 pages)
- [ ] Procurement (2 pages)
- [ ] Mobile App (3 pages)
- [ ] Audit Trail (2 pages)
- [ ] Support (2 pages)
- [ ] FAQ (1 page)

### Deploy
- [ ] DNS: Add A/CNAME record for `doc.ballie.co`
- [ ] Nginx: Add subdomain server block pointing to `docs/.vitepress/dist/`
- [ ] SSL: Run Certbot for HTTPS
- [ ] Build: `npm run docs:build`
- [ ] Verify: `llms.txt`, `sitemap.xml`, search, mobile responsive
- [ ] Update main app links (footer, help page, sidebar)

### Verify AI Crawlability
- [ ] `https://doc.ballie.co/llms.txt` returns structured page listing
- [ ] `https://doc.ballie.co/llms-full.txt` returns full content dump
- [ ] `https://doc.ballie.co/sitemap.xml` lists all 55+ pages
- [ ] `https://doc.ballie.co/robots.txt` allows all crawlers
- [ ] Each page has JSON-LD structured data in `<head>`
- [ ] Each page has `<meta name="description">` via frontmatter
- [ ] Test: Ask ChatGPT/Claude "How do I create an invoice in Ballie?" — verify it can answer

---

## Content Source Reference

The existing 170+ `.md` files in the Ballie project root contain internal feature documentation that can be used as source material when writing public-facing docs:

| Topic Area | Reference Files |
|------------|----------------|
| Accounting | `VOUCHER_API_REFERENCE.md`, `CHART_OF_ACCOUNTS_VERIFICATION.md`, `ACCOUNTING_DASHBOARD_IMPROVEMENTS.md` |
| Invoices | `INVOICE_MOBILE_FRONTEND_GUIDE.md`, `INVOICE_PAYMENT_CALLBACK_GUIDE.md` |
| Inventory | `STOCK_JOURNAL_API_REFERENCE.md`, `STOCK_CALCULATION_FIX.md` |
| Payroll | `PAYROLL_EMPLOYEES_API_GUIDE.md`, `PAYROLL_PROCESSING_API_GUIDE.md`, `SALARY_COMPONENTS_GUIDE.md` |
| Attendance | `ATTENDANCE_SYSTEM_GUIDE.md`, `QR_CODE_ATTENDANCE_IMPLEMENTATION.md` |
| CRM | `CUSTOMER_IMPORT_FEATURE.md`, `CUSTOMER_OPENING_BALANCE_IMPLEMENTATION.md` |
| POS | `POS_MOBILE_API_GUIDE.md`, `POS_ACCOUNTING_INTEGRATION.md` |
| Banking | `BANK_ACCOUNT_MANAGEMENT.md`, `BANK_RECONCILIATION_IMPLEMENTATION.md` |
| E-Commerce | `ECOMMERCE_IMPLEMENTATION_PLAN.md`, `ECOMMERCE_MOBILE_API.md` |
| Reports | `FINANCIAL_REPORTS_API_GUIDE.md`, `SALES_PURCHASE_REPORTS_API_GUIDE.md` |
| Admin | `PERMISSIONS_GUIDE.md`, `ADMIN_MANAGEMENT_FLOW.md` |
| Mobile | `MOBILE_APP_SETUP.md`, `MOBILE_OFFLINE_IMPLEMENTATION.md` |

---

## How to Use AI to Generate Documentation from Blade Views

Since the docs live in the same repo as the Laravel app, any AI assistant (GitHub Copilot, Claude, Cursor, ChatGPT with codebase) can read the actual blade views, controllers, routes, and models — then generate accurate how-to guides automatically.

### The Core Workflow

```
1. Pick a feature page to write (e.g. docs/accounting/invoices.md)
2. Tell AI which blade view(s) and controller(s) to read
3. AI reads the actual code → extracts fields, buttons, routes, validation
4. AI generates a complete how-to guide in docs/ format
5. You review, tweak, commit
```

### Prompt Templates

#### Template 1: Generate a Full How-To Page

```
Read these files:
- resources/views/tenant/accounting/invoices/create.blade.php
- resources/views/tenant/accounting/invoices/index.blade.php
- app/Http/Controllers/Tenant/InvoiceController.php
- routes/tenant.php (the invoice routes section)

Then generate a complete user documentation page for docs/accounting/invoices.md

Follow this format:
- Frontmatter with title and description
- Opening paragraph summarizing the feature
- Prerequisites section
- Step-by-step "Creating a New Invoice" with every form field explained
- Table of invoice statuses
- "Editing an Invoice" section
- "Recording a Payment" section
- "Sending an Invoice" section
- Tips and warnings using ::: callout blocks
- Related Pages links at the bottom

Use the exact field labels, button text, and menu paths from the blade views.
Write for end users, not developers.
```

#### Template 2: Generate from a Single View

```
Read resources/views/tenant/payroll/employees/create.blade.php
and app/Http/Controllers/Tenant/PayrollController.php (the create/store methods)

Generate a how-to guide for docs/payroll/employees.md explaining:
1. How to navigate to the employee creation page
2. Every field in the form — what it means and what to enter
3. Required vs optional fields
4. What happens after saving

Format it as a VitePress markdown page with frontmatter.
```

#### Template 3: Generate Module Overview

```
Read the sidebar navigation in resources/views/layouts/tenant/sidebar.blade.php
and list all menu items under the "Accounting" section.

Then read the index/list blade views for each accounting feature:
- resources/views/tenant/accounting/invoices/index.blade.php
- resources/views/tenant/accounting/vouchers/index.blade.php
- resources/views/tenant/accounting/expenses/index.blade.php
(etc.)

Generate docs/accounting/index.md — an overview page that:
- Explains what the Accounting module does
- Lists all sub-features with 1-sentence descriptions
- Links to each sub-feature's detail page
- Includes a "Quick Start" numbered list for first-time users
```

#### Template 4: Update Docs When UI Changes

```
I just changed resources/views/tenant/crm/customers/create.blade.php —
I added a new "Tax ID" field.

Read the updated blade view and update docs/crm/customers.md to include
the new field in the correct section. Keep all existing content intact.
```

#### Template 5: Batch Generate Multiple Pages

```
I need documentation for the entire Inventory module. For each page below,
read the corresponding blade views and controller, then generate the docs:

1. docs/inventory/index.md
   → Read: sidebar.blade.php (inventory section), InventoryController@index

2. docs/inventory/products.md
   → Read: tenant/inventory/products/create.blade.php, ProductController

3. docs/inventory/categories.md
   → Read: tenant/inventory/categories/index.blade.php, ProductCategoryController

4. docs/inventory/stock-management.md
   → Read: tenant/inventory/stock-journals/*.blade.php, StockJournalController

5. docs/inventory/physical-stock.md
   → Read: tenant/inventory/physical-stock/*.blade.php, PhysicalStockController

Generate all 5 files with proper VitePress frontmatter and cross-links.
```

#### Template 6: Generate FAQ from Common Patterns

```
Read these files to understand the most common user-facing features:
- resources/views/tenant/accounting/invoices/create.blade.php
- resources/views/tenant/pos/index.blade.php
- resources/views/tenant/payroll/processing/index.blade.php
- resources/views/tenant/settings/company.blade.php

Generate docs/faq.md with 15-20 frequently asked questions covering:
- Account setup
- Invoice creation
- POS usage
- Payroll processing
- Common errors and how to fix them

Format as VitePress markdown with ## headings for each question.
```

### Mapping: Which Files to Read for Each Docs Page

| Docs Page | Blade Views to Read | Controller to Read |
|-----------|--------------------|--------------------|
| `getting-started/registration.md` | `resources/views/auth/register.blade.php` | `app/Http/Controllers/Auth/RegisteredUserController.php` |
| `getting-started/onboarding.md` | `resources/views/tenant/onboarding/steps/*.blade.php` | `app/Http/Controllers/Tenant/OnboardingController.php` |
| `getting-started/navigation.md` | `resources/views/layouts/tenant/sidebar.blade.php`, `resources/views/tenant/dashboard.blade.php` | `app/Http/Controllers/Tenant/DashboardController.php` |
| `accounting/invoices.md` | `resources/views/tenant/accounting/invoices/*.blade.php` | `app/Http/Controllers/Tenant/InvoiceController.php` |
| `accounting/vouchers.md` | `resources/views/tenant/accounting/vouchers/*.blade.php` | `app/Http/Controllers/Tenant/VoucherController.php` |
| `accounting/chart-of-accounts.md` | `resources/views/tenant/accounting/chart-of-accounts/*.blade.php` | `app/Http/Controllers/Tenant/ChartOfAccountsController.php` |
| `accounting/expenses.md` | `resources/views/tenant/accounting/expenses/*.blade.php` | `app/Http/Controllers/Tenant/ExpenseController.php` |
| `accounting/quotations.md` | `resources/views/tenant/accounting/quotations/*.blade.php` | `app/Http/Controllers/Tenant/QuotationController.php` |
| `accounting/payments.md` | `resources/views/tenant/accounting/payments/*.blade.php` | `app/Http/Controllers/Tenant/PaymentController.php` |
| `inventory/products.md` | `resources/views/tenant/inventory/products/*.blade.php` | `app/Http/Controllers/Tenant/ProductController.php` |
| `inventory/stock-management.md` | `resources/views/tenant/inventory/stock-journals/*.blade.php` | `app/Http/Controllers/Tenant/StockJournalController.php` |
| `crm/customers.md` | `resources/views/tenant/crm/customers/*.blade.php` | `app/Http/Controllers/Tenant/CustomerController.php` |
| `crm/vendors.md` | `resources/views/tenant/crm/vendors/*.blade.php` | `app/Http/Controllers/Tenant/VendorController.php` |
| `pos/making-sales.md` | `resources/views/tenant/pos/*.blade.php` | `app/Http/Controllers/Tenant/PosController.php` |
| `payroll/employees.md` | `resources/views/tenant/payroll/employees/*.blade.php` | `app/Http/Controllers/Tenant/PayrollController.php` |
| `payroll/attendance.md` | `resources/views/tenant/payroll/attendance/*.blade.php` | `app/Http/Controllers/Tenant/AttendanceController.php` |
| `payroll/payroll-processing.md` | `resources/views/tenant/payroll/processing/*.blade.php` | `app/Http/Controllers/Tenant/PayrollController.php` |
| `banking/reconciliation.md` | `resources/views/tenant/banking/reconciliation/*.blade.php` | `app/Http/Controllers/Tenant/BankReconciliationController.php` |
| `ecommerce/store-setup.md` | `resources/views/tenant/ecommerce/settings/*.blade.php` | `app/Http/Controllers/Tenant/EcommerceSettingsController.php` |
| `ecommerce/orders.md` | `resources/views/tenant/ecommerce/orders/*.blade.php` | `app/Http/Controllers/Tenant/OrderManagementController.php` |
| `projects/managing-projects.md` | `resources/views/tenant/projects/*.blade.php` | `app/Http/Controllers/Tenant/ProjectController.php` |
| `settings/company-settings.md` | `resources/views/tenant/settings/company.blade.php` | `app/Http/Controllers/Tenant/SettingsController.php` |
| `settings/users-roles.md` | `resources/views/tenant/admin/*.blade.php` | `app/Http/Controllers/Tenant/AdminController.php` |
| `audit-trail/viewing-audits.md` | `resources/views/tenant/audit/*.blade.php` | `app/Http/Controllers/Tenant/AuditController.php` |

### Tips for Best AI-Generated Results

1. **Always specify the blade view path** — AI reads the exact form fields, labels, and validation messages
2. **Include the controller** — AI reads what data is passed to views (dropdowns, defaults, options)
3. **Include routes** — AI knows the exact URL paths and menu navigation
4. **Ask for "end-user language"** — Tell AI to write for business users, not developers
5. **Review field names** — AI sometimes uses the PHP variable name instead of the UI label; always double-check
6. **One module at a time** — Better results than asking for all 55 pages at once
7. **Iterate** — Generate first draft, review, then ask AI to "add more detail to the X section" or "make the steps clearer"
8. **Keep the AI prompt in a reusable file** — Save your best prompts so you can re-run them when the UI changes

### Automated Docs Generation Script (Optional)

You can create a simple script that maps each docs page to its source files, making it easy to regenerate docs when the UI changes:

```js
// scripts/docs-map.js — Reference file for AI-assisted doc generation
// Use this when prompting AI: "Generate docs for [page] using the files listed here"

export const docsMap = {
  'docs/accounting/invoices.md': {
    views: [
      'resources/views/tenant/accounting/invoices/index.blade.php',
      'resources/views/tenant/accounting/invoices/create.blade.php',
      'resources/views/tenant/accounting/invoices/show.blade.php',
    ],
    controller: 'app/Http/Controllers/Tenant/InvoiceController.php',
    routes: 'routes/tenant.php',  // search for "invoice" routes
    description: 'How to create, edit, send, and track invoices',
  },
  'docs/accounting/vouchers.md': {
    views: [
      'resources/views/tenant/accounting/vouchers/index.blade.php',
      'resources/views/tenant/accounting/vouchers/create.blade.php',
    ],
    controller: 'app/Http/Controllers/Tenant/VoucherController.php',
    routes: 'routes/tenant.php',
    description: 'How to create and manage journal vouchers',
  },
  // ... add all other pages
}
```

---

## Future Considerations

1. **API Developer Docs** — Add a `/api/` section later using `vitepress-openapi` plugin for mobile/integration developers
2. **Multilingual** — VitePress supports i18n natively; add Yoruba, Hausa, Igbo translations later
3. **Video Embeds** — Add Loom/YouTube tutorial videos per page
4. **Changelog** — Add a `/changelog` page tracking Ballie version updates
5. **Analytics** — Add Plausible or Umami for privacy-friendly docs traffic analytics
6. **AI Chat Widget** — Once docs are indexed, add a "Ask Ballie AI" chatbot widget on the docs site that uses the docs as context
7. **Auto-sync docs on UI changes** — Use a Git pre-commit hook to warn when a blade view changes but its corresponding docs page wasn't updated
