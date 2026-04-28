# Mobile Offline Sync Architecture Plan

Date: 2026-04-28

This document plans how Ballie can add offline support to the React Native Expo mobile app while keeping the current Laravel web and mobile APIs stable. It is an architecture and implementation roadmap. After backend implementation, a separate React Native Expo developer handoff document should be generated with exact screens, local schema files, API payload examples, and code-level tasks.

## Goal

Enable a tenant user to keep using approved mobile modules when the phone has no internet, then sync safely to the live Laravel server when connectivity returns.

The offline system must protect accounting integrity, inventory integrity, tenant isolation, and role permissions. The mobile app should feel fast and usable offline, but the server remains the final authority for posted financial records, stock balances, approvals, and reports.

The first high-value offline workflows should include sales invoices, purchase invoices, invoice PDF access, and customer ledger statements. Users should be able to create invoice documents quickly, show or download a PDF immediately, and download a customer's statement of account from the phone. When the phone is offline, these outputs must be clearly marked as local/pending or last-synced; when online, the app should replace them with official server-generated versions.

## Current Mobile And Backend Flow

### What Exists Now

- Global mobile auth is available at `POST /api/v1/auth/login`, `POST /api/v1/auth/register`, and related tenant selection endpoints.
- Tenant mobile APIs are available under `/api/v1/tenant/{tenant}` and protected by `auth:sanctum`.
- Mobile routes already cover dashboard, company settings, onboarding, accounting, reports, banking, inventory, procurement, payroll, CRM, statutory, admin, e-commerce, POS, projects, and public downloads.
- The current mobile APIs are normal online CRUD/list endpoints. They use server integer IDs, pagination, Laravel validation, and server-side business rules.
- The web flow is the most complete and should remain the source of truth for posting, reports, permissions, stock movement, and accounting behavior.
- Existing mobile guides exist for specific modules, and an older `MOBILE_OFFLINE_IMPLEMENTATION.md` exists, but that guide is generic and does not fully match the current Ballie data model.

### Important Current Data Model Facts

- The app is multi-tenant. Most business tables carry `tenant_id`.
- Mobile authentication uses Sanctum personal access tokens.
- Invoices are not mainly a standalone `invoices` table in the current accounting flow. The mobile invoice API works with `vouchers` and `voucher_entries`, with inventory side effects through products and stock movements.
- Products and inventory use tables such as `products`, `product_categories`, `units`, `stock_journal_entries`, `stock_journal_entry_items`, `stock_movements`, and `stock_locations`.
- POS uses `sales`, `sale_items`, sale payments, cash registers, and cash register sessions.
- Many master-data tables already have `updated_at` and some have `deleted_at`, but not every sync-relevant table has soft deletes.
- There is no stable client UUID, sync version, mutation log, device registry, or sync endpoint yet.

### Current Gaps For Offline Support

- Existing CRUD endpoints cannot tell the mobile app what changed since the last sync.
- Existing records only expose server integer IDs. Offline-created records need client-generated IDs before the server sees them.
- There is no idempotency key for retrying the same offline mutation safely.
- There is no central conflict detection or conflict reporting.
- There is no server-side sync registry defining which models can be pulled, pushed, or kept online-only.
- There is no tombstone strategy for hard-deleted or non-soft-deletable records.
- There is no offline document export strategy for invoice PDFs or customer ledger statements.
- Reports and dashboards currently depend on live calculations and should be cached, not edited offline.

## Recommended Technology Stack

### React Native Expo

Use this combination:

- Expo with EAS Build and a custom development client for native modules.
- WatermelonDB for local relational data and offline-first screens.
- React Query persistence for online-only or read-cache endpoints such as reports and dashboard snapshots.
- `@react-native-community/netinfo` installed through Expo for network state.
- `expo-secure-store` for tokens, selected tenant, device UUID, and offline lock metadata.
- `expo-file-system` for cached official invoice PDFs and customer statement files.
- `expo-print` for local pending invoice PDF generation.
- `expo-sharing` for sharing/downloading PDFs and statements from the device.
- `expo-local-authentication` for PIN/biometric-style offline app unlock.
- `expo-task-manager` with Expo background task support for best-effort background sync where the platform allows it.
- Optional local DB encryption if sensitive financial data is stored offline. This may require a custom native module/config plugin and must be validated with EAS Build.

### Why WatermelonDB

WatermelonDB is a good fit because Ballie has relational tenant data, many list screens, and potentially large product/customer/voucher datasets. AsyncStorage or MMKV alone is not enough for relational offline writes. React Query persistence is useful, but by itself it is mainly a cache, not a durable offline database.

Because this app uses Expo, WatermelonDB should be planned with EAS Build and a custom development client, not plain Expo Go. If the team must stay inside Expo Go temporarily, use `expo-sqlite` as a short-term local database option while keeping the same sync API contract, then move to WatermelonDB when the custom dev client is ready.

## Architecture Overview

```text
React Native Expo App
  - WatermelonDB local tables
  - Local mutation queue
  - Sync state per tenant/user/device
  - Conflict inbox
  - React Query persisted cache for online reports
  - Expo FileSystem cache for PDFs/statements
        |
        | pull/push sync
        v
Laravel API
  - Existing module CRUD APIs remain
  - New sync endpoints under /api/v1/tenant/{tenant}/sync
  - Sync registry per table/model
  - Idempotent mutation applier
  - Conflict detector
  - Tombstone/deletion tracker
        |
        v
MySQL
  - Existing business tables
  - Added sync UUID/version columns on syncable tables
  - Mobile device and mutation log tables
  - Optional sync tombstone/conflict tables
```

## Core Principle

Do not rewrite the working web modules for offline support.

Instead:

1. Keep current Laravel module flows intact.
2. Add sync metadata to the tables that mobile must cache or edit offline.
3. Add dedicated sync endpoints for mobile pull/push.
4. Reuse existing domain logic for creating invoices, stock journals, POS sales, and other business records.
5. Keep sensitive server-calculated actions online-only at first.

## Offline Capability Policy By Module

Not every module should be fully offline. Ballie should classify each module by risk.

### Offline Read And Write, Phase 1 Candidates

| Area | Offline behavior | Notes |
| --- | --- | --- |
| Customers | Create/edit offline, sync later | Conflict can use last-write-wins or server-review for duplicate email/phone. |
| Vendors | Create/edit offline, sync later | Same as customers. |
| Product categories | Create/edit offline, sync later | Watch unique name/slug constraints. |
| Units | Read mostly, limited offline create/edit | Units affect products and invoices, so validate carefully. |
| Products | Create/edit descriptive data offline | Stock fields must be server-controlled. |
| Stock locations | Read, create/edit if user has permission | Important for store/warehouse flows. |
| Draft quotations | Create/edit offline | Server assigns final number on sync. |
| Sales invoices | Create/view/edit offline as pending invoices | Priority workflow. Mobile can generate a local pending PDF immediately; server assigns official number and official PDF on sync. |
| Purchase invoices | Create/view/edit offline as pending purchase invoices | Priority workflow. Server revalidates vendor, products, taxes, ledger entries, and stock effects on sync. |
| Invoice PDFs | View/download offline from local cache or local generation | Offline PDF must show pending/last-synced status. Official PDF is cached after successful server sync. |
| Customer ledger statements | View/download last-synced statement offline | Can include pending local invoices in a clearly separate provisional section. Official balance remains server-calculated. |
| Draft stock journals/transfers | Create/edit offline as drafts | Posting remains online-only initially. |

### Offline Read, Online Write

| Area | Offline behavior | Notes |
| --- | --- | --- |
| Ledger accounts | Read offline | Changes affect accounting structure. Keep online-only. |
| Account groups | Read offline | Same reason. |
| Voucher types | Read offline | Numbering and inventory effects are server policy. |
| Payment methods | Read offline | Usually setup data. |
| Banks | Read offline initially | Reconciliation needs server authority. |
| Cash registers | Read offline | Opening/closing sessions needs careful handling. |
| Permissions/modules/settings | Read offline | Used to show allowed screens and sync scopes. |

### Online Only Initially

| Area | Reason |
| --- | --- |
| Final posting/locking of invoices/vouchers | Posting affects ledger and stock; server must validate and generate official entries. Offline invoice creation is allowed as pending, but final authoritative posting remains server-controlled. |
| Cancelling posted financial documents | Requires server-side audit and reversal rules. |
| Bank reconciliation | Depends on live statement and ledger state. |
| Payroll processing/approval | High risk and often approval-driven. |
| Admin user/role management | Security-sensitive. |
| Company/module settings | Affects app behavior across devices. |
| Dashboard and reports | Server-calculated. Cache last known data only. |
| E-commerce payouts | Financial and external-service dependent. |

### POS Offline Option

POS may need offline support, but it should be handled as a dedicated phase because receipts, cash sessions, stock, and payments are involved.

Recommended first POS offline rule:

- User must open/register a cash session online before going offline.
- Offline POS sales get a local receipt number.
- Server assigns official `sale_number` during sync.
- Product stock shown offline is last-known stock and may be stale.
- On sync, server validates stock, session, payment method, and duplicate mutations.
- If validation fails, sale enters conflict/review state rather than silently disappearing.

## Priority Invoice And Statement Workflow

Sales invoices, purchase invoices, invoice PDFs, and customer ledger statements should become a first-release vertical slice, not a late enhancement.

Recommended behavior:

- Mobile can create sales and purchase invoices offline as pending documents.
- The invoice form must use locally cached customers, vendors, products, units, stock locations, ledger accounts, voucher types, taxes, and company settings.
- If stock location is enabled, the offline invoice form should use the current store/main location default already established in the web flow.
- The app should validate required fields, totals, taxes, and line structure locally for a smooth user experience.
- The server must still revalidate all invoice data during sync and recalculate totals, ledger entries, stock effects, and official document numbers.
- Mobile can generate an immediate local invoice PDF from WatermelonDB data. This PDF must show a local/pending invoice number until the server returns the official one.
- When online, or after sync, the app should download/cache the official server-generated invoice PDF so the user can reopen or share it later even without internet.
- Customer ledger statements should be downloadable from cached server statement data. If offline, the file must show the statement's last synced time.
- Pending offline invoices may be shown in a separate provisional section on the customer statement, but they must not be mixed into the official closing balance until synced.
- Permissions must control statement access. A user who can create invoices should not automatically get customer statement access unless the role includes the required accounting/report/customer ledger permission.

## Server Changes Required

### 1. Add Sync Metadata To Syncable Tables

For each syncable table, add:

```text
sync_uuid uuid nullable initially, then required after backfill
server_version unsignedBigInteger default 1
client_created_at nullable timestamp
client_updated_at nullable timestamp
last_modified_by_device_id nullable string or foreign key
```

Indexes:

```text
unique tenant_id + sync_uuid
index tenant_id + updated_at
index tenant_id + deleted_at where table has soft deletes
index tenant_id + server_version if version-based sync is used
```

Recommended first syncable tables and document data sets:

```text
customers
vendors
product_categories
units
products
ledger_accounts
account_groups
voucher_types
vouchers
voucher_entries
invoice_pdf_exports
customer_statement_snapshots
quotations
quotation_items
stock_locations
stock_journal_entries
stock_journal_entry_items
sales
sale_items
sale_payments
cash_registers
cash_register_sessions
payment_methods
banks
```

Not all of these should be writable offline immediately. Some are read-only sync tables.

### 2. Backfill Sync UUIDs

Existing rows need stable UUIDs before the mobile app can sync reliably.

Backfill rules:

- Generate `sync_uuid` for every existing row in syncable tables.
- Keep server integer `id` as the backend primary key.
- Mobile stores both `server_id` and `sync_uuid`, but local relationships should prefer `sync_uuid`.
- New server-created records should receive `sync_uuid` automatically.

### 3. Add A Syncable Trait

Create a Laravel trait for syncable models.

Responsibilities:

- Assign `sync_uuid` on create.
- Increment or bump `server_version` on save.
- Track `client_created_at` and `client_updated_at` when supplied by mobile.
- Provide scopes for changed-since queries.

Example responsibilities only, not final implementation:

```php
trait Syncable
{
    protected static function bootSyncable(): void
    {
        static::creating(function ($model) {
            $model->sync_uuid ??= (string) Str::uuid();
            $model->server_version ??= 1;
        });

        static::saving(function ($model) {
            if ($model->exists && $model->isDirty()) {
                $model->server_version = ((int) $model->server_version) + 1;
            }
        });
    }
}
```

### 4. Add Sync Infrastructure Tables

Create migrations for these support tables.

#### `mobile_devices`

Tracks trusted devices.

```text
id
tenant_id
user_id
device_uuid
device_name
platform
app_version
last_seen_at
last_synced_at
revoked_at
timestamps
```

#### `mobile_mutations`

Makes queued mutations idempotent.

```text
id
tenant_id
user_id
device_uuid
client_mutation_id
table_name
record_sync_uuid
action
payload_hash
status pending|applied|conflict|failed
error_message
server_response json nullable
processed_at nullable
timestamps
```

Unique index:

```text
tenant_id + device_uuid + client_mutation_id
```

#### `sync_tombstones`

Tracks deletions for tables that are hard-deleted or do not have soft deletes.

```text
id
tenant_id
table_name
record_sync_uuid
server_id nullable
deleted_by nullable
deleted_at
created_at
```

#### `mobile_sync_conflicts`

Stores conflicts that need review or client display.

```text
id
tenant_id
user_id
device_uuid
client_mutation_id
table_name
record_sync_uuid
conflict_type
client_payload json
server_payload json
resolution nullable
resolved_by nullable
resolved_at nullable
timestamps
```

#### `mobile_document_exports`

Tracks server-generated files that the mobile app can download and cache.

```text
id
tenant_id
user_id nullable
document_type invoice_pdf|customer_statement
source_table nullable
source_sync_uuid nullable
customer_sync_uuid nullable
storage_path
mime_type
file_name
status pending|ready|failed
generated_at nullable
expires_at nullable
timestamps
```

This table is useful for official invoice PDFs and server-generated customer statements. The mobile app can still generate provisional offline PDFs locally, but official exports should come from the server and be cached locally after download.

### 5. Add A Sync Registry

Create a central PHP config/service that defines what can sync.

Example shape:

```php
return [
    'customers' => [
        'model' => App\Models\Customer::class,
        'module' => 'crm',
        'pull_permission' => 'crm.view',
        'push_permission' => 'crm.customers.manage',
        'mode' => 'read_write',
        'conflict_strategy' => 'last_write_wins',
        'allowed_fields' => [
            'customer_type', 'first_name', 'last_name', 'company_name',
            'email', 'phone', 'mobile', 'address_line1', 'city', 'state',
            'country', 'payment_terms', 'notes', 'status',
        ],
    ],
    'ledger_accounts' => [
        'model' => App\Models\LedgerAccount::class,
        'module' => 'accounting',
        'pull_permission' => 'accounting.view',
        'push_permission' => null,
        'mode' => 'read_only',
        'conflict_strategy' => 'server_wins',
    ],
];
```

The sync registry prevents the mobile app from pulling or pushing data outside the user's permissions and enabled modules.

### 6. Add Sync Endpoints

Add these under `routes/api/v1/tenant.php` inside the `auth:sanctum` group:

```text
POST /api/v1/tenant/{tenant}/sync/register-device
GET  /api/v1/tenant/{tenant}/sync/bootstrap
POST /api/v1/tenant/{tenant}/sync/pull
POST /api/v1/tenant/{tenant}/sync/push
POST /api/v1/tenant/{tenant}/sync/resolve-conflict
GET  /api/v1/tenant/{tenant}/sync/status
GET  /api/v1/tenant/{tenant}/sync/documents/invoices/{invoiceSyncUuid}/pdf
POST /api/v1/tenant/{tenant}/sync/documents/customers/{customerSyncUuid}/statement
```

#### Register Device

Purpose:

- Register or update device metadata.
- Return sync schema version and server time.

Request:

```json
{
  "device_uuid": "client-generated-uuid",
  "device_name": "Dare's iPhone",
  "platform": "ios",
  "app_version": "1.4.0"
}
```

#### Bootstrap

Purpose:

- Return tenant, user, enabled modules, permissions, syncable table list, and current sync schema version.
- Let the app know what to sync and what to hide.

Response shape:

```json
{
  "success": true,
  "data": {
    "server_time": "2026-04-28T12:00:00Z",
    "sync_schema_version": 1,
    "tenant": { "id": 1, "slug": "law-firm", "name": "Law Firm" },
    "user": { "id": 7, "name": "Store Manager" },
    "enabled_modules": ["inventory", "reports"],
    "permissions": ["inventory.view", "inventory.reports.view"],
    "tables": {
      "products": { "mode": "read_write", "page_size": 500 },
      "stock_movements": { "mode": "read_only", "page_size": 500 }
    }
  }
}
```

#### Pull

Purpose:

- Download server changes since the last successful pull.
- Supports initial sync and incremental sync.

Request:

```json
{
  "last_pulled_at": null,
  "tables": ["customers", "vendors", "products"],
  "page_size": 500,
  "cursor": null
}
```

Response:

```json
{
  "success": true,
  "data": {
    "timestamp": "2026-04-28T12:05:00Z",
    "has_more": false,
    "cursor": null,
    "changes": {
      "customers": {
        "created": [],
        "updated": [],
        "deleted": []
      },
      "products": {
        "created": [],
        "updated": [],
        "deleted": []
      }
    }
  }
}
```

#### Push

Purpose:

- Upload local mutations.
- Apply them with idempotency.
- Return applied results, server IDs, conflicts, and validation errors.

Request:

```json
{
  "device_uuid": "client-generated-uuid",
  "mutations": [
    {
      "client_mutation_id": "uuid-per-operation",
      "table": "customers",
      "action": "create",
      "record_sync_uuid": "uuid-per-record",
      "base_server_version": null,
      "client_created_at": "2026-04-28T11:58:00Z",
      "client_updated_at": "2026-04-28T11:58:00Z",
      "data": {
        "customer_type": "business",
        "company_name": "Acme Limited",
        "email": "buyer@example.com",
        "phone": "08000000000",
        "status": "active"
      }
    }
  ]
}
```

Response:

```json
{
  "success": true,
  "data": {
    "applied": [
      {
        "client_mutation_id": "uuid-per-operation",
        "table": "customers",
        "record_sync_uuid": "uuid-per-record",
        "server_id": 1001,
        "server_version": 1,
        "status": "created"
      }
    ],
    "conflicts": [],
    "failed": []
  }
}
```

#### Invoice PDF Download

Purpose:

- Return the official invoice PDF if it already exists.
- Generate the official invoice PDF if the invoice is synced and valid.
- Let the mobile app cache the PDF locally for later offline access.

Rules:

- If the invoice only exists locally and is not synced, the server cannot return an official PDF yet.
- The mobile app should generate a local pending PDF instead.
- The official PDF response should include file metadata, generated time, and the invoice's current `server_version`.

Response:

```json
{
  "success": true,
  "data": {
    "invoice_sync_uuid": "invoice-record-uuid",
    "server_version": 3,
    "document_type": "invoice_pdf",
    "file_name": "INV-000123.pdf",
    "download_url": "https://example.com/storage/mobile-documents/INV-000123.pdf",
    "mime_type": "application/pdf",
    "generated_at": "2026-04-28T12:10:00Z"
  }
}
```

#### Customer Statement Download

Purpose:

- Generate or return a customer statement of account for a selected date range.
- Return statement metadata and downloadable PDF/JSON summary.
- Let the mobile app cache the last official statement for offline viewing and download.

Request:

```json
{
  "from_date": "2026-04-01",
  "to_date": "2026-04-28",
  "include_zero_balance": false
}
```

Rules:

- The official statement balance must be server-calculated.
- Offline mobile may show cached official statements only.
- Pending local invoices can be displayed under a separate `Pending local activity` section in the mobile-generated statement view/PDF.

Response:

```json
{
  "success": true,
  "data": {
    "customer_sync_uuid": "customer-record-uuid",
    "from_date": "2026-04-01",
    "to_date": "2026-04-28",
    "opening_balance": "15000.00",
    "closing_balance": "42000.00",
    "currency": "NGN",
    "file_name": "statement-2026-04-01-2026-04-28.pdf",
    "download_url": "https://example.com/storage/mobile-documents/statement.pdf",
    "generated_at": "2026-04-28T12:12:00Z"
  }
}
```

### 7. Keep Existing CRUD APIs

Do not remove current mobile APIs. They remain useful for online screens and compatibility.

Enhance them gradually:

- Accept optional `sync_uuid` and `client_mutation_id` on online creates/updates.
- Return `sync_uuid`, `server_version`, and `updated_at` in mobile responses.
- Return permission/module metadata in auth/bootstrap responses.
- Standardize pagination and `updated_since` filters where useful.

## Mobile App Changes Required

### 1. Local Storage Layers

| Storage | Use |
| --- | --- |
| Secure storage | Token, selected tenant slug, device UUID, offline unlock secret. |
| WatermelonDB | Domain records, pending mutations, conflicts, sync state. |
| React Query persisted cache | Dashboard/report snapshots and online-only responses. |

### 2. Local WatermelonDB Tables

Start with these local tables:

```text
sync_state
sync_conflicts
pending_mutations
tenants
users
permissions
modules
customers
vendors
product_categories
units
products
stock_locations
account_groups
ledger_accounts
voucher_types
vouchers
voucher_entries
quotations
quotation_items
stock_journal_entries
stock_journal_entry_items
stock_movements
cash_registers
cash_register_sessions
sales
sale_items
sale_payments
payment_methods
banks
invoice_pdf_cache
customer_statement_cache
customer_ledger_entries
```

Each domain table should have at least:

```text
server_id nullable number
sync_uuid string indexed
tenant_id number
server_version number nullable
created_at number/string
updated_at number/string
deleted_at nullable number/string
last_pulled_at nullable
sync_status synced|pending_create|pending_update|pending_delete|conflict
local_error nullable string
```

WatermelonDB already has `_status` and `_changed`; use those where they fit, but keep Ballie-specific sync metadata too.

### 3. Offline Login Rules

The first login must be online.

After first login:

- User may open the app offline if a valid token and tenant profile exist locally.
- Offline access should require device unlock, PIN, or biometrics.
- If the token is revoked on the server, the app discovers that on next sync and locks the tenant workspace.
- If the selected user has no local bootstrap data, the app must require internet.

### 4. Sync Flow

Recommended sync order:

1. Check network state.
2. Refresh bootstrap if online and needed.
3. Push pending mutations in dependency order.
4. Pull server changes by table in dependency order.
5. Apply server changes in one WatermelonDB batch per table group.
6. Update `last_pulled_at` only after local apply succeeds.
7. Show conflicts and failed mutations in a sync inbox.

Dependency order for push:

```text
customers
vendors
product_categories
units
products
quotations
quotation_items
vouchers
voucher_entries
invoice_pdf_cache
customer_statement_cache
stock_journal_entries
stock_journal_entry_items
cash_register_sessions
sales
sale_items
sale_payments
```

Dependency order for pull:

```text
permissions/modules/settings
account_groups
ledger_accounts
voucher_types
units
product_categories
products
stock_locations
customers
vendors
banks/payment_methods/cash_registers
documents and transaction headers
document lines
official invoice PDF metadata/cache
customer statement snapshots
stock_movements and derived ledger/read-only tables
```

### 5. Mutation Queue Rules

Every offline write creates one pending mutation.

Each mutation must include:

```text
client_mutation_id
record_sync_uuid
table
action
base_server_version
data
client_created_at
client_updated_at
attempt_count
last_error
```

Important rules:

- Retrying the same mutation must not create duplicates on the server.
- Never discard a pending mutation after a network error.
- Mark validation failures as failed/conflict, not pending forever.
- If the user edits the same local record multiple times before sync, the app may compact mutations into one final update when safe.

## Conflict Resolution Rules

Use different strategies by data risk.

| Data type | Strategy | Reason |
| --- | --- | --- |
| Customers/vendors | Last-write-wins with duplicate checks | Lower risk, common offline edits. |
| Product descriptive fields | Last-write-wins or server-review | Names/prices can change, but duplicates matter. |
| Product stock fields | Server-wins | Stock is derived from movements. |
| Ledger accounts/account groups | Server-wins | Accounting structure. |
| Voucher types | Server-wins | Numbering and effects. |
| Pending sales/purchase invoices | Server-wins if server changed; otherwise apply client after validation | Priority workflow, but server must recalculate totals, numbering, ledger, and stock effects. |
| Posted vouchers/invoices | Server-wins, no offline edits | Posted records affect ledger. |
| Invoice PDFs | Server official PDF wins; local PDF is provisional | Offline local PDF is for speed, official PDF comes from server after sync. |
| Customer statements | Server official statement wins; local pending activity is separate | Official opening/closing balances must remain server-calculated. |
| Stock journals | Draft only offline; server validates on sync | Stock can be stale. |
| POS sales | Server validates; conflict/review if invalid | Payments and stock risk. |
| Reports/dashboard | Pull-only cache | Server-calculated. |

## Numbering Strategy

Do not depend on final server document numbers while offline.

Use this rule first:

- Mobile creates a local number like `LOCAL-DEVICE-000001`.
- Server assigns official `voucher_number`, `journal_number`, `sale_number`, or quotation number during sync.
- Mobile replaces local display number after sync.
- If a user prints/shares while offline, clearly mark it as a local pending document.

Optional later improvement:

- Implement reserved number blocks per device and voucher type.
- This is more complex and should wait until the basic sync system is reliable.

## Stock Strategy

Stock needs special care.

Rules:

- `products.current_stock` is server-derived.
- `stock_movements` should be pull-only on mobile.
- Offline stock-changing actions should be saved as draft documents or pending POS sales.
- The mobile app may show an estimated local stock, but it must label it as estimated when offline.
- Server recalculates and validates stock on sync.
- If stock is no longer available, the mutation becomes a conflict/review item.

## Permissions And Modules

The sync bootstrap must respect the same role/module access as web and existing APIs.

Rules:

- Mobile only pulls tables that the user is allowed to view.
- Mobile only pushes tables/actions that the user is allowed to manage.
- If role permissions change while the device is offline, the next bootstrap/pull should remove or lock newly forbidden local data.
- Dashboard/report data should only be cached if the user has the matching report/dashboard permission.
- Admin and settings data should stay online-only unless there is a specific future requirement.

## Security Requirements

- Store tokens in `expo-secure-store`, not AsyncStorage.
- Store device UUID securely.
- Consider encrypting WatermelonDB/SQLite if sensitive financial data is stored offline.
- Add remote device revocation through Sanctum tokens and `mobile_devices.revoked_at`.
- Add a local app lock for offline access.
- Never sync data across tenants. Every sync query must include tenant scope.
- Do not trust client totals for financial documents. Recalculate totals server-side.
- Do not trust client stock balances. Recalculate stock server-side.

## Suggested Backend Implementation Phases

### Phase 0 - Audit And Contract Freeze

- Confirm the first offline vertical slice: sales invoices, purchase invoices, invoice PDF access, customer statement downloads, and the required supporting reference data.
- Decide which mobile screens must work offline first.
- Freeze the sync payload naming convention: use `sync_uuid`, `server_id`, `server_version`, `client_mutation_id`.
- Add tests for tenant scoping and permission scoping before exposing sync data.

### Phase 1 - Sync Foundation

- Add sync metadata columns to low-risk tables first:
  - customers
  - vendors
  - product_categories
  - units
  - products
  - stock_locations
  - ledger_accounts
  - account_groups
  - voucher_types
  - vouchers
  - voucher_entries
- Create `mobile_devices`, `mobile_mutations`, `sync_tombstones`, and `mobile_sync_conflicts`.
- Create `mobile_document_exports` for official invoice PDFs and customer statements.
- Add `Syncable` trait.
- Backfill UUIDs.
- Build `SyncRegistry`.
- Build bootstrap and pull endpoints.
- Mobile can now browse key data offline.

### Phase 2 - Priority Invoice Workflow

- Add push support for customers and vendors required during invoice creation.
- Add offline create/view/edit support for pending sales invoices and purchase invoices using `vouchers` and `voucher_entries`.
- Add invoice sync services that reuse the existing web/mobile invoice validation and posting logic where possible.
- Add local pending invoice number handling and official server number replacement after sync.
- Add official invoice PDF generation/download endpoint and mobile cache metadata.
- Add customer statement generation/download endpoint and cache metadata.
- Keep final posting, cancellation, and authoritative ledger/stock effects server-controlled.
- Add idempotent mutation handling.
- Add conflict response format.
- Add mobile sync inbox UX.
- Keep product stock fields server-wins.

### Phase 3 - Broader Low-Risk Offline Writes

- Add push support for product categories, units, and product descriptive fields.
- Add broader product editing while keeping stock fields server-wins.
- Add sync support for quotations and quotation items.
- Refactor remaining document creation logic out of controllers into reusable services if needed.

### Phase 4 - Inventory Operations Offline

- Add sync support for draft stock journal entries and items.
- Support transfer/production drafts if stock locations are enabled.
- Server validates stock and locations on sync.
- Posting remains online-only until conflict rules are mature.

### Phase 5 - POS Offline, If Required

- Support offline POS sale capture for already-open cash sessions.
- Queue sales, sale items, and payment records.
- Server validates session, stock, duplicate receipt, and payment totals.
- Add manager review for conflicts.

### Phase 6 - Reports And Dashboard Cache

- Keep reports/dashboard online-calculated.
- Persist last successful report responses using React Query persistence.
- Show `Last updated at` and `Offline cached data` labels.
- Respect role permissions for cached report visibility.

## Suggested React Native Implementation Phases

### Phase A - Local DB Foundation

- Confirm Expo workflow: use EAS Build with a custom development client for WatermelonDB and any native storage/encryption modules.
- Add required Expo packages: `expo-secure-store`, `expo-file-system`, `expo-print`, `expo-sharing`, `expo-local-authentication`, `expo-task-manager`, and network status support.
- Install WatermelonDB and configure native setup through Expo prebuild/config plugins where required.
- If staying in Expo Go during early UI work, use `expo-sqlite` only as a temporary adapter and keep the sync contract unchanged.
- Add local schema versioning.
- Add local models for Phase 1 tables.
- Add secure token/device storage.
- Add NetInfo and sync status store.

### Phase B - Bootstrap And Pull

- Login online.
- Register device.
- Download bootstrap.
- Initial pull in pages.
- Render Customers, Vendors, Products, invoice form reference data, Inventory reference data, and statement/PDF cache metadata from WatermelonDB.
- Keep current online APIs as fallback during transition.

### Phase C - Priority Invoice Mutations

- Add local create/edit forms for customers and vendors needed during invoice creation.
- Add local sales invoice and purchase invoice create/edit screens.
- Add local invoice totals, tax calculations, line validations, and pending invoice numbers.
- Add local pending invoice PDF generation with `expo-print`.
- Store generated pending PDFs with `expo-file-system` and share/download them with `expo-sharing`.
- Add pending mutation queue.
- Add sync on reconnect and manual `Sync now`.
- Add error/conflict inbox.

### Phase D - Documents And Statements

- Cache official invoice PDFs after sync.
- Add customer statement download/cache flow.
- Add offline customer statement viewing from the last successful server-generated statement.
- Show pending local invoices separately on customer statement screens while offline.
- Store official PDFs/statements in the Expo file cache and keep metadata in WatermelonDB.
- Implement local draft quotation creation.
- Use local UUID relationships for headers and lines.
- Replace local numbers with official server numbers after sync.
- Block posting while offline.

### Phase E - Polish

- Add best-effort background sync with Expo task/background support, while keeping manual `Sync now` because iOS and Android can limit background execution.
- Add sync badges per record.
- Add last-synced indicators per module.
- Add local app lock with `expo-local-authentication` and secure lock metadata in `expo-secure-store`.
- Add storage usage controls and old cache cleanup.

## API Compatibility Notes

The sync layer should not break current mobile screens.

During migration:

- Online-only screens call current CRUD APIs.
- Offline-capable screens read/write WatermelonDB.
- The sync service talks to new sync endpoints.
- Existing create/update endpoints can later accept `sync_uuid` and `client_mutation_id` for immediate online idempotency.

## Testing Plan

### Backend Tests

- User cannot pull tables without permission.
- User cannot push tables without manage permission.
- User cannot sync another tenant's data.
- Duplicate `client_mutation_id` returns the original result.
- Duplicate `sync_uuid` does not create duplicate records.
- Pull returns created, updated, and deleted/tombstoned records.
- Conflict is detected when `base_server_version` is stale.
- Server recalculates invoice totals and stock effects.
- Pending sales invoice sync returns official voucher number and official PDF metadata.
- Pending purchase invoice sync returns official voucher number and correct vendor/stock/accounting validation response.
- Customer statement endpoint respects permission, date range, tenant scope, and server-calculated balance.

### Mobile Tests

- First login requires internet.
- App opens offline after bootstrap.
- Create customer offline, reconnect, sync, verify server record.
- Edit same customer on web and mobile offline, reconnect, verify conflict behavior.
- Create sales invoice offline, generate local pending PDF, reconnect, sync, verify official voucher number and official PDF cache.
- Create purchase invoice offline, reconnect, sync, verify vendor, stock, tax, and ledger validation behavior.
- Download customer statement online, go offline, reopen/download cached statement with last-synced timestamp.
- Add pending local invoice while offline and verify customer statement shows it separately from official balance.
- Attempt to post invoice offline, verify blocked with clear message.
- Sync interruption keeps queue intact.
- User role changed on web, mobile reconnects, forbidden modules disappear or lock.
- Cached reports show last-updated timestamp and do not imply live data.

## Practical First Release Scope

For the first offline release, keep scope small and valuable:

1. Offline login after first online login.
2. Offline browse for products, categories, units, stock locations, customers, vendors, ledger accounts, voucher types.
3. Offline create/view/edit for pending sales invoices.
4. Offline create/view/edit for pending purchase invoices.
5. Immediate local invoice PDF generation for pending invoices, then official PDF download/cache after sync.
6. Customer ledger statement download/cache with clear last-synced timestamp.
7. Offline create/edit for customers and vendors needed during invoice creation.
8. Offline product descriptive edits, excluding stock balance edits.
9. Cached inventory report/dashboard snapshots only if the user has permissions.

This gives real value without putting final posting, bank reconciliation, payroll, or live reports at risk. The key distinction is that invoice creation can happen offline as a pending document, while authoritative posting, official numbering, ledger effects, and stock effects are confirmed by the server during sync.

## Files To Create During Implementation

### Laravel

```text
app/Http/Controllers/Api/Tenant/SyncController.php
app/Services/MobileSync/SyncRegistry.php
app/Services/MobileSync/PullService.php
app/Services/MobileSync/PushService.php
app/Services/MobileSync/MutationApplier.php
app/Services/MobileSync/ConflictResolver.php
app/Services/MobileSync/InvoiceSyncService.php
app/Services/MobileSync/DocumentExportService.php
app/Services/MobileSync/CustomerStatementSnapshotService.php
app/Models/MobileDevice.php
app/Models/MobileMutation.php
app/Models/MobileSyncConflict.php
app/Models/SyncTombstone.php
app/Models/MobileDocumentExport.php
app/Traits/Syncable.php
config/mobile_sync.php
database/migrations/xxxx_add_sync_columns_to_mobile_sync_tables.php
database/migrations/xxxx_create_mobile_sync_support_tables.php
database/migrations/xxxx_create_mobile_document_exports_table.php
tests/Feature/Api/Tenant/MobileSyncTest.php
tests/Feature/Api/Tenant/MobileInvoiceSyncTest.php
tests/Feature/Api/Tenant/MobileDocumentExportTest.php
```

### React Native Expo

```text
app.json or app.config.ts
eas.json
plugins/watermelondb-plugin.ts if a custom config plugin is needed
src/db/schema.ts
src/db/index.ts
src/db/models/*
src/sync/syncClient.ts
src/sync/pullChanges.ts
src/sync/pushChanges.ts
src/sync/mutationQueue.ts
src/sync/conflicts.ts
src/sync/invoiceSync.ts
src/documents/invoicePdf.ts
src/documents/customerStatement.ts
src/documents/fileCache.ts
src/hooks/useNetworkStatus.ts
src/hooks/useSyncStatus.ts
src/components/OfflineBanner.tsx
src/components/SyncBadge.tsx
src/screens/SyncInboxScreen.tsx
src/screens/invoices/OfflineSalesInvoiceScreen.tsx
src/screens/invoices/OfflinePurchaseInvoiceScreen.tsx
src/screens/customers/CustomerStatementScreen.tsx
src/storage/expoSecureAuthStorage.ts
src/storage/expoFileStorage.ts
```

## Open Decisions Before Coding

- Confirmed first vertical slice: sales invoices, purchase invoices, invoice PDFs, customer statement downloads, and required customer/vendor/product reference data.
- Should POS offline be included in first release or delayed?
- Should offline invoices receive only local pending numbers first, or should devices reserve official number blocks later?
- Should local pending invoice PDFs use the exact same design as the server PDF, or a simplified mobile template until sync?
- Should customer statements include pending unsynced invoices in a separate provisional section by default?
- Should local SQLite be encrypted from day one, knowing Expo encryption may require EAS Build, a custom native module, or a config plugin?
- How long should mobile keep cached data after a user's permissions are removed?


---

## Phase 4 Deployment Runbook (Inventory Operations Offline)

**Status:** Code-shipped. Adds offline draft creation for stock journal entries (transfer / production / consumption / adjustment).

### Scope

- Mobile may push DRAFT `stock_journal_entries` (parent + items) created while offline.
- Server assigns the official `journal_number` atomically.
- Posting (`status = 'posted'`, writing to `stock_movements`, ledger effects) remains online-only — Phase 5+ will introduce stock-validation conflict rules before flipping that lever.
- Stock locations are resolved by `sync_uuid` for transfer entries.
- Permission gate: `mobile.sync.write.inventory` (already provisioned in Phase 1).

### What Shipped

| Layer | File | Change |
|------|------|--------|
| Migration | `database/migrations/2026_05_05_000001_add_sync_columns_to_stock_journal_tables.php` | Adds `sync_uuid`, `server_version`, `client_created_at`, `client_updated_at`, `last_modified_by_device_id` (+ unique + indexes) to `stock_journal_entries` and `stock_journal_entry_items`. Idempotent, all guards `Schema::hasColumn` / `hasIndex`. |
| Model | `app/Models/StockJournalEntry.php` | `use Syncable` |
| Model | `app/Models/StockJournalEntryItem.php` | `use Syncable` |
| Service | `app/Services/MobileSync/StockJournalSyncService.php` | `applyCreate(Tenant, User, MobileDevice, syncUuid, payload)` — validates, resolves products + locations by `sync_uuid`, locks for next number, creates draft journal + items in one transaction. |
| Wiring | `app/Services/MobileSync/PushService.php` | Constructor injects `StockJournalSyncService`. New dispatch branch for `custom_handler === 'stock_journal_sync'` and private `applyStockJournalCreate()` (mirrors `applyQuotationCreate`). |
| Registry | `config/mobile_sync.php` | `stock_journal_entries` flipped to `pushable=true`, `allowed_actions=['create']`, `custom_handler='stock_journal_sync'`, `dependencies=['products','stock_locations']`, `protected_attributes=['status','posted_at','posted_by','journal_number']`. `stock_journal_entry_items` registered as pull-only child via parent relation `stockJournalEntry`. |
| Tests | `tests/Feature/Api/Tenant/MobileStockJournalSyncTest.php` | 6 feature tests: draft creation, transfer location resolution, idempotency, missing-items rejection, transfer-without-both-locations rejection, unknown-product rejection. |

### Deployment Steps (Production)

1. **Pull code** to staging / production.
2. **Run migration** in a low-traffic window:
   ```
   php artisan migrate --path=database/migrations/2026_05_05_000001_add_sync_columns_to_stock_journal_tables.php
   ```
   Migration is additive and idempotent — safe to re-run.
3. **Backfill existing rows** with `sync_uuid` (reuse the existing command):
   ```
   php artisan ballie:sync:backfill-uuids --tables=stock_journal_entries,stock_journal_entry_items --chunk=500
   ```
   *(Run during off-peak — chunked + throttled per `config/mobile_sync.backfill`.)*
4. **Clear config cache**:
   ```
   php artisan config:clear
   php artisan config:cache
   ```
5. **Verify registry**:
   ```
   php artisan tinker --execute="echo json_encode(app(\App\Services\MobileSync\SyncRegistry::class)->get('stock_journal_entries'), JSON_PRETTY_PRINT);"
   ```
   Expected: `pushable: true`, `custom_handler: stock_journal_sync`.
6. **Verify DI**:
   ```
   php artisan tinker --execute="app(\App\Services\MobileSync\PushService::class); echo 'OK';"
   ```

### Smoke Test (Post-Deploy)

POST to `/api/v1/tenant/{tenant}/sync/push` with a single `stock_journal_entries` create mutation. Confirm:

- Response `data.results.0.status == "applied"`.
- `data.results.0.server_response.official_journal_number` is populated and follows the server pattern (`SJYYYYMMDD####`).
- `data.results.0.server_response.status == "draft"`.
- DB row exists in `stock_journal_entries` with `status='draft'`, matching `sync_uuid`, and `posted_at IS NULL`.
- Zero rows in `stock_movements` referencing the new journal id.
- Re-pushing the same `client_mutation_id` returns `applied` again with `idempotent: true` in the server response.

### Permissions

No new permissions. Reuses `mobile.sync.write.inventory` (already provisioned in Phase 1's `MobileSyncPermissionsSeeder`). Users who can create products / categories already have inventory write capability.

### Rollback

If issues are observed:

1. Revert the registry by editing `config/mobile_sync.php` and setting `pushable => false` for `stock_journal_entries`. Run `config:cache`. Pushes will start failing with `not_pushable` immediately.
2. The migration is additive and safe to leave in place. Do NOT run `migrate:rollback` blindly — it would attempt to drop sync columns that other phases may also depend on.
3. The `Syncable` trait no-ops on legacy tables that lack sync columns, so even a partial rollback won't break creates.

### Out of Scope (Phase 5+)

- Online posting from mobile (`status: 'draft' → 'posted'`).
- Stock availability checks / negative-stock rejection.
- Bin-level / batch-level conflict resolution on parallel adjustments.
- Multi-location production reports with operator handoff.

### Test Status

The new feature tests (6) are written but not run in CI yet due to a pre-existing legacy migration issue in the SQLite test database (unrelated to mobile sync — see prior Phase 3 notes). Production MySQL is unaffected because the migration is gated by `Schema::hasTable` / `hasColumn`. Manual smoke-testing on staging is the verification path until the test-infra issue is fixed.


---

## Phase 5 Deployment Runbook (POS Offline Sales)

**Status:** Code-shipped. Adds offline POS sale capture against an already-open cash register session, with full server-side stock validation, stock movement writes, receipt generation, and accounting voucher posting.

### Scope

- Mobile may push completed `sales` (parent + items + payments) captured offline.
- Server assigns the official `sale_number` atomically.
- The cash register session is server-validated: must exist, must be **open** (`closed_at IS NULL`), and must belong to the pushing user.
- Server runs **fresh stock checks** (`Product::getStockAsOfDate(now(), true)`) under `lockForUpdate()` per item; insufficient stock surfaces as a `failed` mutation with `error_code: insufficient_stock` for manager review.
- Server writes `stock_movements`, generates a `Receipt`, and posts an RV accounting voucher (debit Cash, credit Sales/Tax) — same logic as the canonical web/mobile POS controller.
- **Sessions remain online-only** — opening / closing a cash register session requires connectivity. Mobile pulls the session record and uses its `sync_uuid` while offline.
- Permission gate: `mobile.sync.write.invoices` (already provisioned in Phase 1).

### What Shipped

| Layer | File | Change |
|------|------|--------|
| Migration | `database/migrations/2026_05_12_000001_add_sync_columns_to_pos_tables.php` | Adds `sync_uuid` / `server_version` / `client_*_at` / `last_modified_by_device_id` (+ unique + indexes) to `cash_registers`, `cash_register_sessions`, `payment_methods`, `sales`, `sale_items`, `sale_payments`. Idempotent. |
| Models | `app/Models/Sale.php`, `SaleItem.php`, `SalePayment.php`, `CashRegister.php`, `CashRegisterSession.php`, `PaymentMethod.php` | `use Syncable` |
| Service | `app/Services/MobileSync/PosSaleSyncService.php` | `applyCreate(Tenant, User, MobileDevice, syncUuid, payload)` — validates session ownership + open state, resolves customer/products/payment_methods by `sync_uuid`, locks for fresh stock, creates Sale + items + payments, writes stock movements, generates receipt + RV voucher. |
| Wiring | `app/Services/MobileSync/PushService.php` | Constructor injects `PosSaleSyncService`. New dispatch branch for `custom_handler === 'pos_sale_sync'` and private `applyPosSaleCreate()`. |
| Registry | `config/mobile_sync.php` | Six new entries: `cash_registers`, `cash_register_sessions`, `payment_methods` (pull-only); `sales` (pushable, `custom_handler='pos_sale_sync'`, server-derived totals + `sale_number` listed in `protected_attributes`); `sale_items`, `sale_payments` (pull-only children). |
| Tests | `tests/Feature/Api/Tenant/MobilePosSaleSyncTest.php` | 5 feature tests: completed sale creation, closed-session rejection, session-user-mismatch rejection, idempotency, missing-payments rejection. |

### Deployment Steps (Production)

1. **Pull code** to staging / production.
2. **Run migration** in a low-traffic window:
   ```
   php artisan migrate --path=database/migrations/2026_05_12_000001_add_sync_columns_to_pos_tables.php
   ```
   Migration is additive and idempotent — safe to re-run.
3. **Backfill existing rows** with `sync_uuid`:
   ```
   php artisan ballie:sync:backfill-uuids --tables=cash_registers,cash_register_sessions,payment_methods,sales,sale_items,sale_payments --chunk=500
   ```
   Chunked + throttled per `config/mobile_sync.backfill`.
4. **Clear and warm caches**:
   ```
   php artisan config:clear
   php artisan config:cache
   php artisan route:cache
   ```
5. **Verify registry**:
   ```
   php artisan tinker --execute="echo json_encode(app(\App\Services\MobileSync\SyncRegistry::class)->get('sales'), JSON_PRETTY_PRINT);"
   ```
   Expected: `pushable: true`, `custom_handler: pos_sale_sync`, dependencies include `cash_register_sessions`.
6. **Verify DI**:
   ```
   php artisan tinker --execute="app(\App\Services\MobileSync\PushService::class); echo 'OK';"
   ```

### Smoke Test (Post-Deploy)

Open a cash register session online, then POST to `/api/v1/tenant/{tenant}/sync/push` with a single `sales` create mutation referencing that session's `sync_uuid`. Confirm:

- Response `data.results.0.status == "applied"`.
- `data.results.0.server_response.official_sale_number` is populated and matches the `SALE-YYYY-######` pattern.
- DB row exists in `sales` with `status='completed'`, matching `sync_uuid`, server-recalculated `total_amount`.
- Matching rows in `sale_items` and `sale_payments`.
- A `receipts` row exists for the new sale.
- An RV `vouchers` row exists with two/three `voucher_entries` (debit Cash, credit Sales, optional credit Tax) — provided the tenant has the standard `CASH-001` / `SALES-001` ledger accounts and `RV` voucher type. If not, a `Log::warning` is emitted and the sale still succeeds (sale + receipt + stock movement, but no voucher); this matches the canonical POS controller behavior.
- For products with `maintain_stock=true`, a `stock_movements` row exists with `type='out'`, `transaction_type='sales'`, `source_transaction_type=Sale::class`.
- Re-pushing the same `client_mutation_id` returns `applied` again with `idempotent: true` in the server response.
- Pushing against a closed session returns `failed` with `error_code: session_closed`.
- Pushing for an item that exceeds available stock returns `failed` with `error_code: insufficient_stock` and the available quantity in `errors.available`.

### Permissions

No new permissions. Reuses `mobile.sync.write.invoices` (already provisioned in Phase 1's `MobileSyncPermissionsSeeder`). Cashiers who can push offline invoices may also push offline POS sales.

### Rollback

If issues are observed:

1. Edit `config/mobile_sync.php` and set `pushable => false` on `sales`. Run `config:cache`. Pushes will fail with `not_pushable` immediately. Pull-only access to cash registers / sessions / payment methods can remain enabled (no harm).
2. The migration is additive — leave it in place. Sales already accepted via the new pipeline are standard `status='completed'` sales and remain valid records under the previous controller code.
3. Stock movements + receipts + accounting vouchers created via the sync path are indistinguishable from those created via the web/mobile POS controllers — no cleanup required.

### Out of Scope (Phase 6+)

- Offline session opening / closing (still requires connectivity).
- Refunds, voids, or sale corrections from offline.
- Manager-review UX for `insufficient_stock` conflicts (the data is in `mobile_mutations.error_code` and `mobile_sync_conflicts`; the UI is a Phase 6+ deliverable).
- Multi-currency POS sales.
- Per-device reserved `sale_number` blocks (currently each push gets the next number atomically; for very high-volume offline operations, a reservation scheme can be considered later).

### Test Status

The 5 new feature tests are written but not run in CI yet due to the same pre-existing legacy migration issue in the SQLite test database that affected Phases 3 and 4. Production MySQL is unaffected because the migration is gated by `Schema::hasTable` / `hasColumn`. Manual smoke-testing on staging per the steps above is the verification path until the test-infra cleanup ticket is picked up.


---

## Phase 6 Deployment Runbook (Reports & Dashboard Cache)

**Status:** Code-shipped. **Code-only, no migrations, no new permissions.** Reports and dashboards remain server-calculated at all times — the work here is purely metadata so the mobile React Query persistence layer can decide what to keep on disk and how stale to render it.

### Scope

- Reports stay **online-calculated** — there is no offline editing of report data, ever. Mobile only persists the *last successful response* per cacheable report.
- The new endpoint `GET /api/v1/tenant/{tenant}/sync/reports/manifest` returns the catalog of cacheable reports filtered by the user's existing tenant permissions. A user lacking `dashboard.view` does not even learn that dashboard endpoints exist in the cache catalog.
- Each manifest entry carries `ttl_minutes` (when to background-refresh) and `max_age_minutes` (the hard cap after which mobile must demote the snapshot to "stale" or refuse to render it offline). Financial reports use a tighter `max_age_minutes` than operational reports.
- **Mobile-side label semantics** (documented for the RN handoff):
  - `cached_at` is stamped client-side with the device wallclock when the response arrives. There is no server-side `generated_at` injected into the 57 existing report controllers — that would be high-risk multi-controller surgery for a label.
  - "Last updated at" = `cached_at`.
  - "Offline cached data" badge shown whenever `now() - cached_at > ttl_minutes`.
  - Snapshot is hidden / replaced by "Snapshot too old to display" when `now() - cached_at > max_age_minutes`.
- Permission gating is **double-layered**: (a) the manifest only lists reports the user can view; (b) the underlying report endpoints continue to enforce their own auth as before — a stolen manifest cannot be used to bypass anything.

### What Shipped

| Layer | File | Change |
|------|------|--------|
| Config | `config/mobile_sync.php` | New top-level `'reports'` block enumerating 15 cacheable report endpoints (dashboard ×4, sales ×2, inventory ×3, financial ×4, CRM ×2). Each entry: `method`, `path`, `permission`, `ttl_minutes`, `max_age_minutes`, `module`, `description`. |
| Controller | `app/Http/Controllers/Api/Tenant/SyncController.php` | New public `reportsManifest(Request, Tenant): JsonResponse` method. Coarse-gates on `mobile.sync.read` (or `mobile.sync.read.invoices`), then per-entry filters by the user's tenant permission. Returns `{ server_time, schema_version, reports: [...], manifest_ttl_minutes }`. |
| Route | `routes/api/v1/tenant.php` | `GET /sync/reports/manifest` mounted inside the existing `auth:sanctum` sync group, name `sync.reports.manifest`. |
| Tests | `tests/Feature/Api/Tenant/MobileReportsManifestTest.php` | 4 feature tests: 401 unauthenticated, 403 without `mobile.sync.read`, manifest filters by permission (no dashboard.view → no dashboard.* entries), TTL / max_age / absolute_path shape per entry. |

### Deployment Steps (Production)

1. **Pull code** to staging / production.
2. **No migration. No seeder. No backfill.**
3. **Refresh caches**:
   ```
   php artisan config:clear
   php artisan route:clear
   php artisan config:cache
   php artisan route:cache
   ```
4. **Verify route**:
   ```
   php artisan route:list --path=sync/reports
   ```
   Expected: `GET|HEAD api/v1/tenant/{tenant}/sync/reports/manifest`.
5. **Verify config**:
   ```
   php artisan tinker --execute="echo count(config('mobile_sync.reports')) . ' cacheable reports';"
   ```
   Expected: `15 cacheable reports`.

### Smoke Test (Post-Deploy)

With a Sanctum token whose role has `mobile.sync.read` plus at least `dashboard.view`:

```
GET /api/v1/tenant/{tenant}/sync/reports/manifest
```

Confirm:
- HTTP 200, `data.schema_version == 1`, `data.server_time` populated.
- `data.reports` is a non-empty array.
- Every entry contains `key`, `method`, `path`, `absolute_path` starting with `/api/v1/tenant/{tenant}/`, `ttl_minutes > 0`, `max_age_minutes >= ttl_minutes`, and a non-null `permission`.
- Re-issuing the request with a token whose role lacks `dashboard.view` returns the same envelope but **without** any `dashboard.*` keys.
- Re-issuing without `mobile.sync.read` returns HTTP 403.

### Permissions

No new permissions. Reuses:
- `mobile.sync.read` (Phase 1) — coarse gate on the manifest endpoint itself.
- `dashboard.view`, `reports.view`, `inventory.view` (already in tenant permission catalog) — used as per-report filters inside the manifest.

If your tenant permission catalog uses different slugs for the report modules, edit `config/mobile_sync.php` and adjust the `permission` field on each entry — no code change needed.

### Rollback

Trivial — this phase is read-only metadata:

1. To disable a single report from the cache catalog, remove its entry from `config/mobile_sync.php` and run `config:cache`. Mobile clients will drop it from their persisted set on the next manifest refresh (default `manifest_ttl_minutes: 60`).
2. To kill the entire feature, comment out the route line in `routes/api/v1/tenant.php` and run `route:cache`. The endpoint will return 404 and mobile clients will fall back to their last-known cached responses (which is exactly the safe behavior).
3. No data, no migrations, nothing to clean up.

### What Was Intentionally NOT Shipped

To keep Phase 6 small and safe, the following were considered and explicitly deferred:

- **Server-side `generated_at` field on the 57 existing report controllers.** Touching every controller for one display label would be a high-blast-radius change for negligible benefit. Mobile stamps `cached_at` on receipt instead. If a future audit need arises, a single `AppendsGeneratedAt` middleware on the reports route group can add an `X-Ballie-Generated-At` header without touching controllers.
- **Server-side report snapshot table (`mobile_report_snapshots`).** Persistence lives on the device via React Query persistence + WatermelonDB; storing snapshots server-side adds I/O and privacy surface area for no offline benefit.
- **Granular `reports.read.*` permission slugs.** The existing tenant permission catalog already gates report access via `dashboard.view`, `reports.view`, `inventory.view`. Splitting them per-module is a separate authorization-cleanup ticket, independent of mobile sync.
- **Push of cached report annotations.** Reports are read-only by policy.
- **Multi-tenant report aggregation.** Out of scope for this product.

### Out of Scope (Phase 7+)

- Server-side warm caching of report responses (currently only `dashboard.summary` uses `Cache::remember`).
- Report-specific incremental sync (e.g. delta dashboard updates).
- Push notifications when a cached report becomes stale due to upstream data changes.
- A unified mobile "what's stale" inbox combining sync conflicts + stale snapshots.

### Test Status

The 4 new feature tests are written but not run in CI yet due to the same pre-existing legacy SQLite migration blocker that affected Phases 3 / 4 / 5. Production MySQL is unaffected because Phase 6 ships **no migrations at all**. Manual smoke-test path documented above.
- Should conflicts be resolved only on mobile, only on web/admin, or both?
- Should the team commit to WatermelonDB with custom dev client immediately, or start UI work in Expo Go with `expo-sqlite` and switch before production?

## Recommendation

Use WatermelonDB and add a dedicated Laravel sync layer beside the current APIs.

Start with the invoice-first offline vertical slice: cached master data, offline pending sales invoices, offline pending purchase invoices, local invoice PDF generation, official PDF caching after sync, and customer statement download/cache. Then expand to broader low-risk master-data writes and other draft documents. Keep final posting, approvals, bank reconciliation, payroll processing, and live reports online-only until the sync foundation has been proven in production.

The next implementation document should be backend-focused first: migrations, sync registry, sync endpoints, and tests. After the backend sync contract is stable, generate the React Native Expo developer handoff with exact local schemas, Expo package setup, screen-by-screen behavior, and sample API payloads.

## Phase 1 Deployment Runbook

Phase 1 is strictly additive (new columns are nullable, new tables are new, no destructive changes). Safe to run on production during a low-traffic window after a backup.

### Pre-deployment checklist

1. **Take a fresh database backup.** `mysqldump -u<user> -p <db> > backup-pre-mobile-sync.sql`
2. Pull the new code on the server (the four `2026_04_28_000001..4` migrations, the seeder, the trait, the sync models/services/controller, and the new routes).
3. Run `composer install --no-dev --optimize-autoloader`.
4. **Confirm `phpunit.xml` is set to `DB_CONNECTION=sqlite` / `DB_DATABASE=:memory:`** so `RefreshDatabase` test runs can never hit production MySQL.

### Live server commands (run in order)

```bash
# 1. Apply the four new sync migrations only.
#    Running plain `php artisan migrate` is also safe â€” these are the only
#    pending migrations â€” but the explicit --path form documents intent and
#    avoids surprises if other branches add migrations.
php artisan migrate \
  --path=database/migrations/2026_04_28_000001_add_sync_columns_to_mobile_sync_tables.php \
  --force
php artisan migrate \
  --path=database/migrations/2026_04_28_000002_add_soft_deletes_to_voucher_tables.php \
  --force
php artisan migrate \
  --path=database/migrations/2026_04_28_000003_create_mobile_sync_support_tables.php \
  --force
php artisan migrate \
  --path=database/migrations/2026_04_28_000004_create_mobile_document_exports_table.php \
  --force

# 2. Seed the new mobile.sync.* permissions (idempotent).
php artisan db:seed --class=Database\\Seeders\\MobileSyncPermissionsSeeder --force

# 3. Preview the UUID backfill â€” read-only, just reports counts.
php artisan ballie:sync:backfill-uuids --dry-run

# 4. Run the real backfill. Chunked + idempotent; safe to re-run.
#    Optional flags:
#      --table=customers   limit to a single table
#      --chunk=1000        change chunk size (default 500)
php artisan ballie:sync:backfill-uuids

# 5. Confirm nothing remains.
php artisan ballie:sync:backfill-uuids --dry-run

# 6. Refresh caches.
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### Post-deployment verification

- `php artisan migrate:status | grep 2026_04_28_0000` shows all four as Ran.
- `SELECT COUNT(*) FROM customers WHERE sync_uuid IS NULL;` returns 0 (repeat for each syncable table).
- `SELECT COUNT(*) FROM permissions WHERE slug LIKE 'mobile.sync.%';` returns 5.
- `GET /api/v1/tenant/{tenant}/sync/bootstrap` (with a Sanctum token whose role has `mobile.sync.read`) returns 200 with `data.sync.schema_version: 1`.

### Rollback

Each migration's `down()` method is idempotent and drops only what it added. To revert Phase 1:

```bash
php artisan migrate:rollback --path=database/migrations/2026_04_28_000004_create_mobile_document_exports_table.php --force
php artisan migrate:rollback --path=database/migrations/2026_04_28_000003_create_mobile_sync_support_tables.php --force
php artisan migrate:rollback --path=database/migrations/2026_04_28_000002_add_soft_deletes_to_voucher_tables.php --force
php artisan migrate:rollback --path=database/migrations/2026_04_28_000001_add_sync_columns_to_mobile_sync_tables.php --force
```

The seeded permissions remain in `permissions` after rollback; remove them manually if desired:

```sql
DELETE FROM permissions WHERE slug LIKE 'mobile.sync.%';
```

> âš ï¸ **Test isolation note.** Earlier in development, `phpunit.xml` had its
> sqlite test-DB lines commented out, which caused `RefreshDatabase` feature
> tests to wipe the developer's local MySQL database. The file has been
> corrected to use `DB_CONNECTION=sqlite` + `DB_DATABASE=:memory:`. **Do not
> re-comment those lines.** Verify on every environment before running
> `php artisan test`.



## Phase 2 Deployment Runbook

Phase 2 is **code-only** â€” no new schema migrations, no data backfills, no
new permissions. It builds on the Phase 1 sync infrastructure (the
mobile_* tables, sync_uuid columns, and mobile.sync.* permissions
seeded in Phase 1) and ships:

- `app/Services/MobileSync/InvoiceSyncService.php` â€” applies offline
  invoice (voucher) creates from the mobile push pipeline.
- `app/Services/MobileSync/DocumentExportService.php` â€” generates and
  caches the official invoice PDF using the existing Barryvdh DomPDF
  templates already used by the web app.
- `app/Services/MobileSync/CustomerStatementSnapshotService.php` â€”
  generates and caches customer statement PDFs.
- `app/Http/Controllers/Api/Tenant/SyncController.php` (extended) â€”
  adds 4 endpoints: resolve-conflict, invoice PDF, customer statement,
  signed-URL document download.
- `config/mobile_sync.php` (vouchers entry) â€” registers `vouchers`
  with `custom_handler: 'invoice_sync'` and the appropriate
  permissions and dependencies.
- `app/Services/MobileSync/PushService.php` â€” wires the custom
  invoice handler into the push pipeline.

### Pre-deployment checklist

1. **Confirm Phase 1 is already deployed.** Required:
   - All four `2026_04_28_0000*` migrations are `Ran`.
   - `mobile_devices`, `mobile_mutations`, `mobile_sync_conflicts`,
     `mobile_document_exports` tables exist.
   - The five `mobile.sync.*` permissions exist in `permissions`.
   - At least one role with `mobile.sync.read` has been validated
     against `GET /sync/bootstrap`.
2. Take a fresh database backup before deploy (defensive â€” Phase 2 does
   not touch the schema, but production deploys should always have one).
3. Pull the new code.
4. Verify `config/mobile_sync.php` exists and contains the
   `documents` block (`invoice_pdf_cache_minutes`,
   `statement_cache_minutes`, `signed_url_ttl_minutes`, `disk`,
   `directory`). Defaults are safe; override only if you need a
   non-`local` disk for exports.
5. Confirm the storage directory `storage/app/mobile-exports` is
   writable by the web user. Subdirectories `invoices/` and
   `statements/` will be created automatically on first export.

### Live server commands (run in order)

```bash
# 1. Install / refresh dependencies (no new packages added in Phase 2,
#    but always run on deploy to lock down autoload/optimised classes).
composer install --no-dev --optimize-autoloader

# 2. Refresh caches so the new routes, config entries, and registry
#    bindings are picked up.
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# 3. (Optional but recommended) Re-warm caches for production.
php artisan config:cache
php artisan route:cache
```

> No `migrate`. No seeder. No backfill.

### Post-deployment verification

- `php artisan route:list | grep -E "sync/(resolve-conflict|documents)"`
  shows four routes:
  - `POST   api/v1/tenant/{tenant}/sync/resolve-conflict`
  - `GET    api/v1/tenant/{tenant}/sync/documents/invoices/{invoiceSyncUuid}/pdf`
  - `POST   api/v1/tenant/{tenant}/sync/documents/customers/{customerSyncUuid}/statement`
  - `GET    api/v1/tenant/{tenant}/sync/documents/{exportUuid}/download`
    (named `api.v1.tenant.sync.documents.download`; signature-protected,
    no Sanctum guard).
- Invoice push smoke test (against staging) â€” register a test device,
  push a `vouchers` create mutation with one item, expect `status:
  applied` and a populated `server_response.official_voucher_number`.
- Invoice PDF smoke test â€” `GET /sync/documents/invoices/{sync_uuid}/pdf`
  with the device's Sanctum token returns a 200 with a signed
  `download_url`; following that URL returns the PDF and
  `mobile_document_exports.downloaded_at` is populated.
- Customer statement smoke test â€” `POST /sync/documents/customers/{sync_uuid}/statement`
  with a `from_date` / `to_date` returns 200 with a signed
  `download_url` and a `summary` block.
- Conflict resolution smoke test â€” `POST /sync/resolve-conflict` with
  `strategy: server_wins` on an existing pending conflict marks
  `resolved_at` / `resolved_by` / `resolution` and stores the
  resolution payload under `diff.resolution_payload`.

### Rollback

Phase 2 is code-only, so rollback is a code revert:

```bash
git revert <phase-2 merge commit>
composer install --no-dev --optimize-autoloader
php artisan config:clear && php artisan route:clear && php artisan cache:clear
```

No data needs to be cleaned up. `mobile_document_exports` rows
generated under Phase 2 are harmless after rollback (just orphaned
cached PDFs); they can be expired via the existing `expires_at`
column or pruned manually:

```sql
DELETE FROM mobile_document_exports WHERE generated_at < NOW() - INTERVAL 7 DAY;
```

### Test-suite notes (Phase 2 carryover)

Running `php artisan test tests/Feature/Api/Tenant/MobileInvoiceSyncTest.php`
or `MobileDocumentExportTest.php` against the test sqlite `:memory:`
database currently requires the following migration patches (already
applied in this branch) to make MySQL-only DDL skip on sqlite:

- `2025_06_15_000001_add_item_type_to_invoice_items_table` â€”
  `hasTable` guard + skip `change()` on sqlite.
- `2025_10_19_070500_update_account_groups_nature_enum` â€” skip on sqlite.
- `2025_11_03_07_23_create_invoice_items_table` â€” `hasTable` guard,
  inlines `item_type` and `unit` columns for fresh installs.
- `2025_11_09_174626_update_payroll_run_details_component_type_enum` â€” skip on sqlite.
- `2025_11_10_153711_make_salary_component_id_nullable_in_payroll_run_details` â€” skip on sqlite.
- `2025_11_11_095831_fix_cash_register_sessions_foreign_keys` â€” skip on sqlite.
- `2025_11_14_204956_make_overtime_type_nullable_in_overtime_records` â€” skip on sqlite.

Production MySQL behaviour is unchanged by these guards. Additional
legacy migrations in the repository may still be MySQL-specific and
require similar guards before the full `php artisan test` suite is
green; this is tracked separately and is not a Phase 2 deliverable.

---

## Phase 3 Deployment Runbook

**Scope.** Code-only release. Adds:

- `app/Services/MobileSync/QuotationSyncService.php` (new) — applies offline quotation creates with server-assigned `quotation_number` and totals.
- `app/Services/MobileSync/PushService.php` — injects `QuotationSyncService` and adds a `quotation_sync` dispatch branch in `applyOne()`; tightens validation rules for `products`, `units`, `product_categories` master data.
- `config/mobile_sync.php` — `quotations` entry flipped to `pushable: true` with `allowed_actions: ['create']`, `custom_handler: quotation_sync`, dependencies extended to `[customers, vendors, products]`.
- `tests/Feature/Api/Tenant/MobileInventoryAndQuotationSyncTest.php` (new).

**No migrations. No new permissions. No seeders.** `mobile.sync.write.invoices` is reused as the push gate for quotations (any user already authorised to push invoices may push quotations).

### Pre-deploy checklist

- Phase 2 deployed and healthy in production (mobile devices already registered, `mobile_sync_*` tables present).
- Tag the previous release (`git tag pre-phase-3`) so rollback is a single `git reset`.

### Live deploy

```bash
git pull --ff-only
composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```n
### Post-deploy verification

1. **Routes unchanged** — no new endpoints; `php artisan route:list --path=sync` should match the Phase 2 output.
2. **Registry flip** — open a tinker shell:
   ```php
   config('mobile_sync.tables.quotations.pushable')    // => true
   config('mobile_sync.tables.quotations.custom_handler') // => 'quotation_sync'
   ```n3. **Smoke test** — using a Sanctum token belonging to a user with `mobile.sync.write.invoices`:
   - `POST /api/v1/tenant/{slug}/sync/register-device` with a fresh `device_uuid`.
   - `POST /api/v1/tenant/{slug}/sync/push` with a `quotations` `create` mutation referencing existing `customer_sync_uuid` and `product_sync_uuid`.
   - Expect `data.results[0].status == 'applied'` and an `official_quotation_number` in `server_response`. The new row in `quotations` should have `status='draft'` and recalculated `total_amount`.
4. **Inventory push regression** — push a `units` create and a `product_categories` create; confirm rows appear with the device-supplied `sync_uuid`.
5. **Stock-fields guard** — push a `products` create with `current_stock=9999`. The created product must NOT show 9999 stock; SyncRegistry strips server-derived columns from inbound payloads.

### Rollback

```bash
git reset --hard pre-phase-3
composer install --no-dev --optimize-autoloader
php artisan config:clear && php artisan route:clear && php artisan cache:clear
```n
No data rollback is required: any quotations created via the new pipeline are standard `draft` quotations and remain valid records under the previous code (only the inbound push path goes away).

### Out-of-scope for Phase 3

- Quotation **updates / status transitions** (sent / accepted / converted) remain online-only.
- Quotation **deletion** from mobile is not supported.
- Product **stock adjustments** still require the dedicated stock-adjustment voucher flow (Phase 2).

### Test suite notes (carried over from Phase 2)

The new feature test `tests/Feature/Api/Tenant/MobileInventoryAndQuotationSyncTest.php` could not be run end-to-end in this branch because the workspace `RefreshDatabase` SQLite path is broken by pre-existing legacy migrations (duplicate `Schema::create('attendance_records')`, `DB::statement('SHOW INDEX …')`, `dropForeign` on SQLite, etc.) that affect every feature test, not just this one. Production MySQL is unaffected. Smoke testing the live API path per the verification steps above is the recommended substitute until the test-infra cleanup ticket is picked up.


---

## Phase 4 Deployment Runbook (Inventory Operations Offline)
