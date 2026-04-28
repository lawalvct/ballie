# Stock Location Module Plan

## Goal

Add an optional Stock Location module for tenants that need stock by department, warehouse, process area, or work-in-progress location.

When the module is disabled, the inventory system should behave exactly as it does today: product stock is calculated globally from stock movements, stock journal entries do not require locations, and stock transfer can remain hidden from the user.

When the module is enabled, users should be able to:

- Manage stock locations such as Store, Extrusion, Printing, Cutting, Warehouse, or WIP.
- Always have a default main location called Store.
- Transfer stock between locations using a stock transfer voucher.
- Select source and destination locations in production entries.
- See stock summary balances by location while still keeping total product stock correct.

## Current Codebase Observations

The current inventory stock flow is movement-based:

- Products calculate stock from `stock_movements` through `Product::getStockAsOfDate()` and `Product::getStockValueAsOfDate()`.
- `StockJournalEntry::post()` creates stock movements through `StockMovement::createFromStockJournal()`.
- Stock journal entries support `consumption`, `production`, `adjustment`, and `transfer` entry types.
- Production entries already use two logical sides:
  - consumption/source items are `movement_type = out`
  - production/destination items are `movement_type = in`
- Transfer entries currently use two visual sides, but the user flow can be simplified.
- Company Settings already has a Modules tab powered by `ModuleRegistry` and `tenants.enabled_modules`.
- Stock Summary currently shows global product stock, not stock by location.

## Recommended Product Decision

Use a single-row stock transfer UI with two location dropdowns at the top.

Recommended transfer voucher layout:

- From Location: Store
- To Location: Extrusion
- Item rows:
  - LD Exxon Mobil 1018, 200 kg
  - LD Marlex TR144, 100 kg

This is better than the current left/right transfer UI because a transfer moves the same items and quantities from one location to another. A single table prevents mismatch between left and right sides and makes validation simpler.

For production entries, keep the existing two-section UI because production naturally has different input and output items:

- From Location: Extrusion
  - LD Exxon Mobil 1018, 200 kg
  - LD Marlex TR144, 100 kg
- To Location: Store
  - Shopping Bags, 280 kg
  - LD Wastage, 20 kg

The Extrusion location can represent WIP. The stock is still counted, but it belongs to that location until moved or consumed.

## Module Toggle

Add a new module key to `ModuleRegistry`:

- key: `stock_locations`
- name: `Stock Locations`
- description: `Track inventory by store, department, warehouse, and work-in-progress location`
- icon: `fas fa-map-marker-alt`
- dependency: inventory should be enabled for this module to be useful

Suggested defaults:

- Manufacturing: recommended enabled by default.
- Hybrid: recommended enabled by default.
- Trading: optional, not necessarily recommended.
- Service: optional or hidden depending on whether inventory is enabled.

When disabled:

- Hide Stock Transfer button from stock journal index.
- Hide location management routes from navigation/search.
- Hide location dropdowns in stock journal forms.
- Continue creating stock movements with `stock_location_id = null` or default Store only internally.
- Stock Summary remains global, exactly like now.

When enabled:

- Show Stock Transfer button.
- Show Stock Locations menu/action under Inventory.
- Require location selection for transfer and production flows.
- Allow Stock Summary to filter/group by location.

## Data Model

### New Table: stock_locations

Fields:

- id
- tenant_id
- name
- code
- type
- description nullable
- is_main boolean default false
- is_wip boolean default false
- is_active boolean default true
- sort_order integer default 0
- created_by nullable
- updated_by nullable
- timestamps

Recommended location types:

- store
- warehouse
- department
- production
- wip
- branch
- other

Indexes and constraints:

- unique tenant_id + code
- unique tenant_id + name
- index tenant_id + is_active
- index tenant_id + is_main
- index tenant_id + is_wip

Business rule:

- Each tenant should have one main location.
- Create default `Store` location when the module is first enabled or during migration/backfill.
- If users attempt to disable/delete the main location, block it unless another active main location is assigned.

### stock_movements Changes

Add nullable location columns:

- stock_location_id nullable foreign key to stock_locations
- from_stock_location_id nullable foreign key to stock_locations
- to_stock_location_id nullable foreign key to stock_locations

Recommended meaning:

- For normal in/out movement, `stock_location_id` is the affected location.
- For transfer out movement, `stock_location_id` and `from_stock_location_id` are the source location.
- For transfer in movement, `stock_location_id` and `to_stock_location_id` are the destination location.
- Keep both transfer columns for audit clarity and report readability.

Indexes:

- tenant_id + stock_location_id + product_id + transaction_date
- tenant_id + product_id + stock_location_id
- from_stock_location_id
- to_stock_location_id

### stock_journal_entries Changes

Add header-level nullable columns:

- from_stock_location_id nullable
- to_stock_location_id nullable

Meaning:

- Transfer entries use both fields.
- Production entries use both fields.
- Consumption entries may use `from_stock_location_id` only.
- Adjustment entries may use `to_stock_location_id` for stock in or `from_stock_location_id` for stock out, but this can be phase 2.

### stock_journal_entry_items Changes

Add line-level nullable column:

- stock_location_id nullable

Meaning:

- This stores the actual affected location per item.
- For transfer, source and destination movements can still be derived from the journal header.
- For production, consumption item locations come from `from_stock_location_id`, production output item locations come from `to_stock_location_id`.

Optional phase 2:

- Add `from_stock_location_id` and `to_stock_location_id` at item level only if a single voucher needs different locations per row.
- For now, header-level locations are cleaner and match the requested examples.

## Stock Calculation Design

Add location-aware methods without breaking existing ones:

- `Product::getStockAsOfDate($date = null, $includeTime = false)` remains global.
- Add `Product::getStockAtLocation($locationId, $date = null)`.
- Add `Product::getStockValueAtLocation($locationId, $date = null, $valuationMethod = 'weighted_average')`.
- Add helper/query scope on `StockMovement` for location filtering.

Global stock remains the sum of all location movements. This means existing reports and sales checks keep working.

When the module is enabled, UI screens can optionally show location balances, but total product stock should still equal the sum across locations.

## Default Store Backfill

Backfill plan:

1. Create `stock_locations` table.
2. For every tenant with inventory or stock movements, create a default active main location:
   - name: Store
   - code: STORE
   - type: store
   - is_main: true
3. For existing `stock_movements`, set `stock_location_id` to the tenant's Store location.
4. For existing stock journal entries/items, leave location nullable or backfill items to Store.
5. Future entries use location IDs when module is enabled.

Important: because old migrations have a known unrelated foreign key issue, implement this as targeted migrations and run by path when needed.

## Posting Rules

### Transfer Voucher

When posting a transfer from Store to Extrusion:

For each item row:

1. Create one OUT stock movement:
   - type: out
   - quantity: negative
   - transaction_type: transfer_out
   - stock_location_id: Store
   - from_stock_location_id: Store
   - to_stock_location_id: Extrusion

2. Create one IN stock movement:
   - type: in
   - quantity: positive
   - transaction_type: transfer_in
   - stock_location_id: Extrusion
   - from_stock_location_id: Store
   - to_stock_location_id: Extrusion

The net global quantity is zero, but location balances change.

### Production Voucher

When posting production from Extrusion to Store:

Consumption item movements:

- type: out
- quantity: negative
- transaction_type: manufacturing
- stock_location_id: Extrusion
- from_stock_location_id: Extrusion
- to_stock_location_id: Store

Output item movements:

- type: in
- quantity: positive
- transaction_type: manufacturing
- stock_location_id: Store
- from_stock_location_id: Extrusion
- to_stock_location_id: Store

Wastage can be handled as either:

- a produced product called LD Wastage with positive quantity into Store, if it is reusable/resalable, or
- waste_quantity metadata only, if it is not stock-controlled.

Recommended: keep both options. If wastage is selected as a stock-controlled product, it should increase stock. If it is only entered in the waste field, it should be report metadata and should not create separate stock.

## UI Plan

### Company Settings, Modules Tab

Add Stock Locations card/module toggle.

Display copy:

- Name: Stock Locations
- Description: Track stock by store, department, warehouse, and WIP location.
- Recommended badge for manufacturing tenants.

On enabling:

- Ensure default Store location exists.
- Show a success message with a link to manage locations.

On disabling:

- Hide location UI and stock transfer action.
- Do not delete existing location data.
- Existing stock movements retain location data for future re-enable.

### Stock Locations CRUD

Suggested route group:

- `tenant.inventory.stock-locations.index`
- `tenant.inventory.stock-locations.create`
- `tenant.inventory.stock-locations.store`
- `tenant.inventory.stock-locations.edit`
- `tenant.inventory.stock-locations.update`
- `tenant.inventory.stock-locations.destroy`
- optional `tenant.inventory.stock-locations.set-main`

Views:

- index: list locations, active state, type, WIP flag, product count, stock value summary.
- create/edit: name, code, type, WIP toggle, active toggle, description.

Guardrails:

- Cannot delete location with stock movements.
- Cannot delete or deactivate the only main location.
- Location names/codes are unique per tenant.

### Stock Journal Index

If Stock Locations module is enabled:

- Show Stock Transfer button.
- Show Manage Locations button or link.
- Optional: show location filters for transfer/production journal entries.

If disabled:

- Hide Stock Transfer button.
- Keep Material Consumption, Product Receipt, and Stock Adjustment as currently visible.

### Transfer Entry UI

Replace the current two-sided transfer partial with:

- From Location dropdown
- To Location dropdown
- Items table with product, stock at From Location, quantity, rate, amount, remarks
- Totals panel
- Save as Draft and Save and Post buttons

Validation:

- from_stock_location_id required when module enabled
- to_stock_location_id required when module enabled
- from and to must be different
- source location must have enough stock for each product unless negative stock is allowed

### Production Entry UI

Keep the existing two-sided production layout, but add location selectors at the top of the production card:

- From Location: where materials are consumed from
- To Location: where finished goods and WIP output are received

Examples:

- From Location: Extrusion
- To Location: Store

Consumption side uses source location balance.
Production side receives into destination location.

If module is disabled, do not show these dropdowns and keep the current behavior.

### Product Picker / Stock Display

When module is enabled and a source location is selected:

- Product picker should show stock at that selected location.
- AJAX product stock endpoint should accept optional `stock_location_id`.
- Display should say `Stock at Store` or `Stock at Extrusion` to avoid confusion.

When module is disabled:

- Product picker continues showing global product stock.

## Reports Plan

### Stock Summary

When Stock Locations module is disabled:

- Current report remains unchanged.

When enabled:

- Add Location filter: All Locations, Store, Extrusion, etc.
- Add optional View Mode:
  - Product Summary: one row per product, total across all locations.
  - Location Breakdown: one row per product per location.

Recommended first version:

- Keep default Product Summary as current.
- Add a location filter.
- Add expandable/linkable location breakdown later.

### Stock Movement Report

Add location columns when module is enabled:

- Location
- From Location
- To Location

Allow filtering by location.

### Bin Card

Allow optional location filter.

- Product-only bin card remains global.
- Product + location bin card shows movements only for that location.

### Production History

Add from/to location columns and filters:

- Source Location
- Destination Location
- WIP Location flag

This helps show WIP and department output.

## Navigation and Search

Add Global Search entries only when the module route is valid and available:

- Stock Locations
- Create Stock Location
- Stock Transfer Voucher

If route availability is not dynamic in global search, search results should still pass module access checks before rendering links.

## Access Control

Use existing `module.access:inventory` for routes because stock locations are part of inventory.

In addition, UI should check:

- `ModuleRegistry::isModuleEnabled($tenant, 'stock_locations')`

Optional middleware in phase 2:

- Add `module.access:stock_locations` to location management routes if the app supports nested module keys cleanly.

## API Plan

Add or extend endpoints for mobile/API parity:

- List locations
- Create/update/deactivate location
- Product stock by location
- Stock transfer create/post
- Stock summary by location

Keep request fields optional when module disabled.

## Migration and Rollout Steps

1. Add `stock_locations` module key to `ModuleRegistry`.
2. Add `stock_locations` table and default Store creation/backfill.
3. Add nullable location columns to stock movement and stock journal tables.
4. Add `StockLocation` model and relationships.
5. Add location-aware stock calculation helpers.
6. Add Stock Location CRUD routes/controller/views.
7. Gate UI by `stock_locations` module state.
8. Update transfer voucher UI to single-table layout.
9. Update production entry UI with From and To location dropdowns.
10. Update posting logic to write location-aware stock movements.
11. Update stock summary, movement, bin card, and production history reports.
12. Add API endpoints or update existing API flows.
13. Add tests for module off, module on, transfers, production, and stock reports.

## Testing Checklist

Module off:

- Stock journal create page behaves as it does today.
- Stock transfer button is hidden.
- Existing production and consumption entries post without location fields.
- Stock Summary totals match current behavior.

Module on:

- Enabling module creates Store location if missing.
- User can create Extrusion location.
- Transfer Store to Extrusion reduces Store and increases Extrusion, while global product stock stays the same.
- Production from Extrusion to Store reduces raw materials in Extrusion and increases finished goods in Store.
- Stock Summary can show location-specific balances.
- Product picker shows source-location stock when a source location is selected.
- Disabling module hides UI without deleting existing location data.
- Re-enabling module shows the previous locations and balances.

PDF/print/report checks:

- Stock transfer print/PDF shows From and To locations.
- Production print/PDF shows source and destination locations.
- Stock Summary PDF respects selected location filter when module is enabled.

## Open Decisions

These can be confirmed before implementation:

1. Should Stock Locations be enabled by default for manufacturing tenants, or only recommended in the settings UI?
2. Should sales invoices later consume stock from a selected location, or should this first phase affect only stock journal and production flows?
3. Should WIP be a location type only, or should the system also show a dedicated WIP report?
4. Should negative stock by location be blocked, warned, or allowed?
5. Should one transfer voucher allow different From/To locations per item, or is one From and one To location per voucher enough?

## Recommended Phase 1 Scope

For the first implementation, keep scope focused:

- Add module toggle.
- Add default Store location.
- Add Stock Location CRUD.
- Show/hide Stock Transfer button based on module state.
- Convert transfer UI to From/To dropdowns plus one item table.
- Add From/To location dropdowns to production entries.
- Write location-aware stock movements.
- Add location filter to Stock Summary.

Defer to phase 2:

- Location-aware sales invoices.
- Location-aware purchases.
- Dedicated WIP report.
- Per-row transfer locations.
- Advanced location valuation reports.
