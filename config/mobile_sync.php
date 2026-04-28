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

        // ── Documents (Phase 1: pull only; push lands in Phase 2) ────────
        'vouchers' => [
            'model' => \App\Models\Voucher::class,
            'tenant_scoped' => true,
            'pullable' => true,
            'pushable' => false,
            'permissions' => ['pull' => 'mobile.sync.read.invoices', 'push' => null],
            'dependencies' => ['voucher_types', 'ledger_accounts', 'customers', 'vendors', 'products'],
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
            'pushable' => false,
            'permissions' => ['pull' => 'mobile.sync.read', 'push' => null],
            'dependencies' => ['customers', 'products'],
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

    ],

];
