<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Schema version
    |--------------------------------------------------------------------------
    | Bumped whenever the sync contract changes in a way the client must
    | notice (added required field, renamed table, etc.). Mobile clients
    | will compare this with their stored version and force a fresh
    | bootstrap if they fall behind.
    */
    'schema_version' => 1,

    /*
    |--------------------------------------------------------------------------
    | Pull window
    |--------------------------------------------------------------------------
    | Hard caps to keep pulls predictable on slow connections.
    */
    'pull' => [
        'default_limit' => 200,
        'max_limit' => 1000,
        'max_tables_per_request' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Push limits
    |--------------------------------------------------------------------------
    */
    'push' => [
        'max_mutations_per_request' => 200,
        'lock_timeout_seconds' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Document exports
    |--------------------------------------------------------------------------
    */
    'documents' => [
        'disk' => env('MOBILE_SYNC_DOCS_DISK', 'local'),
        'directory' => 'mobile-exports',
        'signed_url_ttl_minutes' => 30,
        'invoice_pdf_cache_minutes' => 60 * 24 * 7, // 7 days
        'statement_cache_minutes' => 60 * 24,       // 1 day
    ],

    /*
    |--------------------------------------------------------------------------
    | Backfill safety knobs
    |--------------------------------------------------------------------------
    */
    'backfill' => [
        'chunk_size' => 500,
        'sleep_ms_between_chunks' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Phase 6 — Cacheable reports manifest
    |--------------------------------------------------------------------------
    | Reports and dashboards stay online-calculated. Mobile persists the
    | LAST successful response per report (React Query persistence) and
    | shows it under an `Offline cached data` / `Last updated at` label.
    |
    | The server's job here is small:
    |   1. Tell mobile WHICH endpoints are safe to cache (manifest).
    |   2. Filter the manifest by the user's existing permissions so a
    |      role that lacks (e.g.) `dashboard.view` never receives a
    |      cache hint for dashboard data.
    |   3. Suggest a TTL per report so the client can dim stale cache.
    |
    | Each entry:
    |   - key            Stable mobile-side cache key.
    |   - method         HTTP method.
    |   - path           Path under /api/v1/tenant/{tenant} (no leading /).
    |   - permission     Existing tenant permission slug required to view.
    |                    Manifest filters out entries the user lacks.
    |   - ttl_minutes    How long mobile may treat the cache as "fresh".
    |   - max_age_minutes Hard cap — older than this and mobile must refuse
    |                    to display the snapshot offline (financial safety).
    |   - module         Logical grouping for UI.
    |   - description    Short label for the mobile sync inbox.
    */
    'reports' => [

        // Dashboard
        'dashboard.summary' => [
            'method' => 'GET', 'path' => 'dashboard/summary',
            'permission' => 'dashboard.view',
            'ttl_minutes' => 5, 'max_age_minutes' => 60 * 24,
            'module' => 'dashboard', 'description' => 'Dashboard summary',
        ],
        'dashboard.balances' => [
            'method' => 'GET', 'path' => 'dashboard/balances',
            'permission' => 'dashboard.view',
            'ttl_minutes' => 5, 'max_age_minutes' => 60 * 24,
            'module' => 'dashboard', 'description' => 'Asset / liability / equity balances',
        ],
        'dashboard.recent_transactions' => [
            'method' => 'GET', 'path' => 'dashboard/recent-transactions',
            'permission' => 'dashboard.view',
            'ttl_minutes' => 5, 'max_age_minutes' => 60 * 24,
            'module' => 'dashboard', 'description' => 'Recent transactions',
        ],
        'dashboard.outstanding_invoices' => [
            'method' => 'GET', 'path' => 'dashboard/outstanding-invoices',
            'permission' => 'dashboard.view',
            'ttl_minutes' => 15, 'max_age_minutes' => 60 * 24 * 3,
            'module' => 'dashboard', 'description' => 'Outstanding invoices',
        ],

        // Sales
        'reports.sales.summary' => [
            'method' => 'GET', 'path' => 'reports/sales/summary',
            'permission' => 'reports.view',
            'ttl_minutes' => 15, 'max_age_minutes' => 60 * 24 * 3,
            'module' => 'sales', 'description' => 'Sales summary',
        ],
        'reports.sales.by_period' => [
            'method' => 'GET', 'path' => 'reports/sales/by-period',
            'permission' => 'reports.view',
            'ttl_minutes' => 30, 'max_age_minutes' => 60 * 24 * 7,
            'module' => 'sales', 'description' => 'Sales by period',
        ],

        // Inventory
        'reports.inventory.stock_summary' => [
            'method' => 'GET', 'path' => 'reports/inventory/stock-summary',
            'permission' => 'inventory.view',
            'ttl_minutes' => 15, 'max_age_minutes' => 60 * 24 * 3,
            'module' => 'inventory', 'description' => 'Stock summary',
        ],
        'reports.inventory.low_stock' => [
            'method' => 'GET', 'path' => 'reports/inventory/low-stock-alert',
            'permission' => 'inventory.view',
            'ttl_minutes' => 15, 'max_age_minutes' => 60 * 24,
            'module' => 'inventory', 'description' => 'Low stock alerts',
        ],
        'reports.inventory.stock_valuation' => [
            'method' => 'GET', 'path' => 'reports/inventory/stock-valuation',
            'permission' => 'inventory.view',
            'ttl_minutes' => 60, 'max_age_minutes' => 60 * 24 * 7,
            'module' => 'inventory', 'description' => 'Stock valuation',
        ],

        // Financial — longer TTLs + tight max_age because they are
        // accounting-grade. Mobile must mark them as snapshot-only.
        'reports.financial.profit_loss' => [
            'method' => 'GET', 'path' => 'reports/financial/profit-loss',
            'permission' => 'reports.view',
            'ttl_minutes' => 60, 'max_age_minutes' => 60 * 24 * 3,
            'module' => 'financial', 'description' => 'Profit & loss',
        ],
        'reports.financial.balance_sheet' => [
            'method' => 'GET', 'path' => 'reports/financial/balance-sheet',
            'permission' => 'reports.view',
            'ttl_minutes' => 60, 'max_age_minutes' => 60 * 24 * 3,
            'module' => 'financial', 'description' => 'Balance sheet',
        ],
        'reports.financial.trial_balance' => [
            'method' => 'GET', 'path' => 'reports/financial/trial-balance',
            'permission' => 'reports.view',
            'ttl_minutes' => 60, 'max_age_minutes' => 60 * 24 * 3,
            'module' => 'financial', 'description' => 'Trial balance',
        ],
        'reports.financial.cash_flow' => [
            'method' => 'GET', 'path' => 'reports/financial/cash-flow',
            'permission' => 'reports.view',
            'ttl_minutes' => 60, 'max_age_minutes' => 60 * 24 * 3,
            'module' => 'financial', 'description' => 'Cash flow',
        ],

        // CRM
        'reports.crm.customer_statements' => [
            'method' => 'GET', 'path' => 'reports/crm/customer-statements',
            'permission' => 'reports.view',
            'ttl_minutes' => 30, 'max_age_minutes' => 60 * 24 * 3,
            'module' => 'crm', 'description' => 'Customer statements',
        ],
        'reports.crm.payment_reports' => [
            'method' => 'GET', 'path' => 'reports/crm/payment-reports',
            'permission' => 'reports.view',
            'ttl_minutes' => 30, 'max_age_minutes' => 60 * 24 * 3,
            'module' => 'crm', 'description' => 'Payment reports',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sync registry — single source of truth for what mobile can pull and push.
    |--------------------------------------------------------------------------
    | Each entry describes one syncable table with:
    |   - model              FQCN of the Eloquent model.
    |   - tenant_scoped      true if rows have a `tenant_id` column.
    |   - parent_via         When tenant scope must come from a parent
    |                        relation (e.g. voucher_entries via voucher).
    |   - pullable           Mobile may download this table.
    |   - pushable           Mobile may submit create/update/delete.
    |   - permissions        ['pull' => slug, 'push' => slug]
    |                        (slug `null` means no permission required).
    |   - allowed_actions    Subset of [create, update, delete] when pushable.
    |   - public_attributes  Whitelist of attributes returned to mobile.
    |   - writable_attributes Whitelist of attributes mobile may set.
    |   - dependencies       Tables that must be pulled before this one.
    */
    'registry' => [

        // ── Reference / read-only masters ────────────────────────────────
        'account_groups' => [
            'model' => \App\Models\AccountGroup::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => false,
            'permissions' => ['pull' => 'mobile.sync.read', 'push' => null],
            'public_attributes' => ['id', 'sync_uuid', 'tenant_id', 'name', 'code', 'parent_id', 'nature', 'created_at', 'updated_at', 'server_version'],
        ],

        'ledger_accounts' => [
            'model' => \App\Models\LedgerAccount::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => false,
            'permissions' => ['pull' => 'mobile.sync.read', 'push' => null],
            'dependencies' => ['account_groups'],
        ],

        'voucher_types' => [
            'model' => \App\Models\VoucherType::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => false,
            'permissions' => ['pull' => 'mobile.sync.read', 'push' => null],
        ],

        'stock_locations' => [
            'model' => \App\Models\StockLocation::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => false,
            'permissions' => ['pull' => 'mobile.sync.read', 'push' => null],
        ],

        'units' => [
            'model' => \App\Models\Unit::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => true,
            'allowed_actions' => ['create', 'update'],
            'permissions' => [
                'pull' => 'mobile.sync.read',
                'push' => 'mobile.sync.write.inventory',
            ],
        ],

        'product_categories' => [
            'model' => \App\Models\ProductCategory::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => true,
            'allowed_actions' => ['create', 'update'],
            'permissions' => [
                'pull' => 'mobile.sync.read',
                'push' => 'mobile.sync.write.inventory',
            ],
        ],

        'products' => [
            'model' => \App\Models\Product::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => true,
            'allowed_actions' => ['create', 'update'], // descriptive fields only
            'permissions' => [
                'pull' => 'mobile.sync.read',
                'push' => 'mobile.sync.write.inventory',
            ],
            'dependencies' => ['units', 'product_categories'],
            'protected_attributes' => [
                // Stock fields are server-derived. Mobile cannot push these.
                'current_stock',
                'opening_stock',
                'opening_stock_value',
                'last_purchase_price',
                'last_sale_price',
            ],
        ],

        // ── CRM ──────────────────────────────────────────────────────────
        'customers' => [
            'model' => \App\Models\Customer::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => true,
            'allowed_actions' => ['create', 'update', 'delete'],
            'permissions' => [
                'pull' => 'mobile.sync.read',
                'push' => 'mobile.sync.write.crm',
            ],
        ],

        'vendors' => [
            'model' => \App\Models\Vendor::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => true,
            'allowed_actions' => ['create', 'update', 'delete'],
            'permissions' => [
                'pull' => 'mobile.sync.read',
                'push' => 'mobile.sync.write.crm',
            ],
        ],

        // ── Documents ────────────────────────────────────────────────────
        // Phase 2: vouchers are pushable (offline invoice creation).
        // Domain rules (double-entry + stock) are enforced by
        // App\Services\MobileSync\InvoiceSyncService, which PushService
        // delegates to when table === 'vouchers'.
        'vouchers' => [
            'model' => \App\Models\Voucher::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => true,
            'allowed_actions' => ['create'],
            'permissions' => [
                'pull' => 'mobile.sync.read.invoices',
                'push' => 'mobile.sync.write.invoices',
            ],
            'dependencies' => ['voucher_types', 'ledger_accounts', 'customers', 'vendors', 'products'],
            // Marker telling PushService to delegate to InvoiceSyncService
            // instead of using the generic master-data create path.
            'custom_handler' => 'invoice_sync',
        ],

        'voucher_entries' => [
            'model' => \App\Models\VoucherEntry::class,
            'tenant_scoped' => false,
            'parent_via' => ['relation' => 'voucher', 'parent_table' => 'vouchers'],
            'pullable' => true,
            'pushable' => false,
            'permissions' => ['pull' => 'mobile.sync.read.invoices', 'push' => null],
            'dependencies' => ['vouchers'],
        ],

        'quotations' => [
            'model' => \App\Models\Quotation::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => true,
            'allowed_actions' => ['create'],
            'permissions' => [
                'pull' => 'mobile.sync.read',
                'push' => 'mobile.sync.write.invoices',
            ],
            'dependencies' => ['customers', 'vendors', 'products'],
            // Phase 3: dispatched to App\Services\MobileSync\QuotationSyncService
            // by PushService so the parent + items + totals are all created
            // in one transaction with a server-assigned quotation_number.
            'custom_handler' => 'quotation_sync',
        ],

        'quotation_items' => [
            'model' => \App\Models\QuotationItem::class,
            'tenant_scoped' => false,
            'parent_via' => ['relation' => 'quotation', 'parent_table' => 'quotations'],
            'pullable' => true,
            'pushable' => false,
            'permissions' => ['pull' => 'mobile.sync.read', 'push' => null],
            'dependencies' => ['quotations'],
        ],

        // ── Inventory operations (Phase 4) ───────────────────────────────
        // Mobile may push DRAFT stock journal entries (transfer,
        // production, consumption, adjustment). Posting to stock
        // movements remains online-only and is dispatched by
        // App\Services\MobileSync\StockJournalSyncService.
        'stock_journal_entries' => [
            'model' => \App\Models\StockJournalEntry::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => true,
            'allowed_actions' => ['create'],
            'permissions' => [
                'pull' => 'mobile.sync.read',
                'push' => 'mobile.sync.write.inventory',
            ],
            'dependencies' => ['products', 'stock_locations'],
            // Phase 4: dispatched to App\Services\MobileSync\StockJournalSyncService
            // by PushService so the parent + items + journal_number are all
            // created in one transaction with a server-assigned number.
            'custom_handler' => 'stock_journal_sync',
            'protected_attributes' => [
                // Posting + stock_movements writes happen online only.
                'status',
                'posted_at',
                'posted_by',
                'journal_number',
            ],
        ],

        'stock_journal_entry_items' => [
            'model' => \App\Models\StockJournalEntryItem::class,
            'tenant_scoped' => false,
            'parent_via' => ['relation' => 'stockJournalEntry', 'parent_table' => 'stock_journal_entries'],
            'pullable' => true,
            'pushable' => false,
            'permissions' => ['pull' => 'mobile.sync.read', 'push' => null],
            'dependencies' => ['stock_journal_entries'],
        ],

        // ── POS (Phase 5) ────────────────────────────────────────────────
        // Cash registers, sessions, and payment methods are pull-only
        // references. Sessions MUST be opened online — mobile may push
        // sales only against an already-open session (validated by
        // PosSaleSyncService).
        'cash_registers' => [
            'model' => \App\Models\CashRegister::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => false,
            'permissions' => ['pull' => 'mobile.sync.read', 'push' => null],
        ],

        'cash_register_sessions' => [
            'model' => \App\Models\CashRegisterSession::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => false,
            'permissions' => ['pull' => 'mobile.sync.read', 'push' => null],
            'dependencies' => ['cash_registers'],
        ],

        'payment_methods' => [
            'model' => \App\Models\PaymentMethod::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => false,
            'permissions' => ['pull' => 'mobile.sync.read', 'push' => null],
        ],

        // POS sales: mobile may push offline-captured sales. Server
        // assigns the official sale_number, validates stock, writes
        // stock_movements + receipts + accounting voucher.
        'sales' => [
            'model' => \App\Models\Sale::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => true,
            'allowed_actions' => ['create'],
            'permissions' => [
                'pull' => 'mobile.sync.read.invoices',
                'push' => 'mobile.sync.write.invoices',
            ],
            'dependencies' => [
                'customers', 'products', 'payment_methods',
                'cash_registers', 'cash_register_sessions',
            ],
            // Phase 5: dispatched to App\Services\MobileSync\PosSaleSyncService
            // by PushService so the server-side sale_number, stock writes,
            // receipt, and accounting voucher all happen authoritatively.
            'custom_handler' => 'pos_sale_sync',
            'protected_attributes' => [
                // Server-derived; clients must not push these.
                'sale_number',
                'status',
                'subtotal',
                'tax_amount',
                'discount_amount',
                'total_amount',
                'change_amount',
            ],
        ],

        'sale_items' => [
            'model' => \App\Models\SaleItem::class,
            'tenant_scoped' => true,
            'parent_via' => ['relation' => 'sale', 'parent_table' => 'sales'],
            'pullable' => true,
            'pushable' => false,
            'permissions' => ['pull' => 'mobile.sync.read.invoices', 'push' => null],
            'dependencies' => ['sales'],
        ],

        'sale_payments' => [
            'model' => \App\Models\SalePayment::class,
            'tenant_scoped' => true,
            'parent_via' => ['relation' => 'sale', 'parent_table' => 'sales'],
            'pullable' => true,
            'pushable' => false,
            'permissions' => ['pull' => 'mobile.sync.read.invoices', 'push' => null],
            'dependencies' => ['sales', 'payment_methods'],
        ],

    ],

];
