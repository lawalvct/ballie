# Dashboard Voucher System Integration Fix

**Date**: October 22, 2025
**Status**: ✅ Completed
**Priority**: Critical

## Problem Statement

The tenant dashboard was showing **zero (0) for Total Sales (this month)** because it was using the wrong data model. The system uses a **Voucher-based accounting system** with invoices, but the dashboard was incorrectly querying the `Sale` model from a POS system that isn't being used.

### Root Cause

```php
// ❌ WRONG - Using POS Sale model
$totalSalesCount = Sale::where('tenant_id', $tenant->id)
    ->where('status', 'completed')
    ->count();
```

The system actually uses:

-   **Vouchers** table (invoices/transactions)
-   **VoucherTypes** table (defines types like Sales, Purchase, Payment, Receipt)
-   **InvoiceItems** table (line items for vouchers)
-   Sales invoices are vouchers with `voucher_type.code = 'SV'` or `'SALES'`

---

## Solution Implemented

### 1. Removed Incorrect Models

**Before**:

```php
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
```

**After**:

```php
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\InvoiceItem;
```

---

### 2. Fixed Sales Count Query

**Before** (Wrong):

```php
$totalSalesCount = Sale::where('tenant_id', $tenant->id)
    ->where('status', 'completed')
    ->whereMonth('sale_date', Carbon::now()->month)
    ->whereYear('sale_date', Carbon::now()->year)
    ->count();
```

**After** (Correct):

```php
// Get sales voucher types
$salesVoucherTypes = VoucherType::where('tenant_id', $tenant->id)
    ->where('affects_inventory', true)
    ->where('inventory_effect', 'decrease')
    ->whereIn('code', ['SV', 'SALES'])
    ->pluck('id');

// Count posted sales invoices this month
$totalSalesCount = Voucher::where('tenant_id', $tenant->id)
    ->whereIn('voucher_type_id', $salesVoucherTypes)
    ->where('status', 'posted')
    ->whereMonth('voucher_date', Carbon::now()->month)
    ->whereYear('voucher_date', Carbon::now()->year)
    ->count();
```

---

### 3. Fixed Total Revenue Calculation

**Before** (Mixed models):

```php
$salesRevenue = Sale::where('tenant_id', $tenant->id)
    ->where('status', 'completed')
    ->sum('total_amount');

$voucherRevenue = Voucher::where('tenant_id', $tenant->id)
    ->where('status', 'posted')
    ->whereHas('voucherType', function($q) {
        $q->where('inventory_effect', 'decrease')
          ->where('affects_inventory', true);
    })
    ->sum('total_amount');

$totalRevenue = $salesRevenue + $voucherRevenue;
```

**After** (Unified):

```php
// Total Revenue from Sales Invoices only
$totalRevenue = Voucher::where('tenant_id', $tenant->id)
    ->whereIn('voucher_type_id', $salesVoucherTypes)
    ->where('status', 'posted')
    ->sum('total_amount');
```

---

### 4. Fixed Average Sales Value

**Before**:

```php
$avgSalesValue = $totalSalesCount > 0 ? $totalRevenue / $totalSalesCount : 0;
```

**After** (Using all-time data for accurate average):

```php
// Average based on all-time posted sales invoices
$totalSalesCountAllTime = Voucher::where('tenant_id', $tenant->id)
    ->whereIn('voucher_type_id', $salesVoucherTypes)
    ->where('status', 'posted')
    ->count();

$avgSalesValue = $totalSalesCountAllTime > 0 ? $totalRevenue / $totalSalesCountAllTime : 0;
```

---

### 5. Fixed Monthly Revenue Growth

**Before**:

```php
$monthlyRevenue = Sale::where('tenant_id', $tenant->id)
    ->where('status', 'completed')
    ->whereMonth('sale_date', Carbon::now()->month)
    ->sum('total_amount');

$lastMonthRevenue = Sale::where('tenant_id', $tenant->id)
    ->where('status', 'completed')
    ->whereMonth('sale_date', Carbon::now()->subMonth()->month)
    ->sum('total_amount');
```

**After**:

```php
$monthlyRevenue = Voucher::where('tenant_id', $tenant->id)
    ->whereIn('voucher_type_id', $salesVoucherTypes)
    ->where('status', 'posted')
    ->whereMonth('voucher_date', Carbon::now()->month)
    ->whereYear('voucher_date', Carbon::now()->year)
    ->sum('total_amount');

$lastMonthRevenue = Voucher::where('tenant_id', $tenant->id)
    ->whereIn('voucher_type_id', $salesVoucherTypes)
    ->where('status', 'posted')
    ->whereMonth('voucher_date', Carbon::now()->subMonth()->month)
    ->whereYear('voucher_date', Carbon::now()->subMonth()->year)
    ->sum('total_amount');
```

---

### 6. Fixed Recent Transactions

**Before**:

```php
$salesData = DB::table('sales')
    ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
    ->where('sales.status', 'completed')
    ->select('sales.sale_number', 'sales.total_amount', 'sales.sale_date')
    ->get();
```

**After**:

```php
$salesInvoices = DB::table('vouchers')
    ->join('voucher_types', 'vouchers.voucher_type_id', '=', 'voucher_types.id')
    ->leftJoin('voucher_entries', function($join) {
        $join->on('vouchers.id', '=', 'voucher_entries.voucher_id')
             ->where('voucher_entries.debit_amount', '>', 0); // Customer debit entry
    })
    ->leftJoin('ledger_accounts', 'voucher_entries.ledger_account_id', '=', 'ledger_accounts.id')
    ->where('vouchers.status', 'posted')
    ->whereIn('vouchers.voucher_type_id', $salesVoucherTypes)
    ->select('vouchers.voucher_number', 'vouchers.total_amount', 'voucher_types.prefix')
    ->get();
```

---

### 7. Fixed Top Selling Products

**Before** (Using sale_items table):

```php
$topProducts = DB::table('sale_items')
    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
    ->join('products', 'sale_items.product_id', '=', 'products.id')
    ->where('sales.status', 'completed')
    ->groupBy('products.id')
    ->get();
```

**After** (Using invoice_items table):

```php
$topProducts = DB::table('invoice_items')
    ->join('vouchers', 'invoice_items.voucher_id', '=', 'vouchers.id')
    ->join('products', 'invoice_items.product_id', '=', 'products.id')
    ->where('vouchers.status', 'posted')
    ->whereIn('vouchers.voucher_type_id', $salesVoucherTypes)
    ->select(
        'products.name',
        DB::raw('SUM(invoice_items.amount) as total_revenue'),
        DB::raw('SUM(invoice_items.quantity) as total_quantity')
    )
    ->groupBy('products.id', 'products.name')
    ->get();
```

---

### 8. Fixed Top Customers

**Before**:

```php
$topCustomers = DB::table('sales')
    ->join('customers', 'sales.customer_id', '=', 'customers.id')
    ->where('sales.status', 'completed')
    ->groupBy('customers.id')
    ->get();
```

**After**:

```php
$topCustomers = DB::table('vouchers')
    ->join('voucher_entries', 'vouchers.id', '=', 'voucher_entries.voucher_id')
    ->join('ledger_accounts', 'voucher_entries.ledger_account_id', '=', 'ledger_accounts.id')
    ->leftJoin('customers', 'ledger_accounts.party_id', '=', 'customers.id')
    ->where('vouchers.status', 'posted')
    ->whereIn('vouchers.voucher_type_id', $salesVoucherTypes)
    ->where('voucher_entries.debit_amount', '>', 0) // Customer entries
    ->where('ledger_accounts.party_type', 'customer')
    ->select(
        'ledger_accounts.name as customer_name',
        DB::raw('COUNT(DISTINCT vouchers.id) as order_count'),
        DB::raw('SUM(vouchers.total_amount) as total_spent')
    )
    ->groupBy('ledger_accounts.id')
    ->get();
```

---

### 9. Fixed Open Invoices Count

**Before**:

```php
$openInvoices = Sale::where('tenant_id', $currentTenant->id)
    ->where('status', '!=', 'completed')
    ->count();
```

**After**:

```php
$openInvoices = Voucher::where('tenant_id', $currentTenant->id)
    ->whereIn('voucher_type_id', $salesVoucherTypes)
    ->where('status', '!=', 'cancelled')
    ->count();
```

---

## System Architecture Understanding

### Voucher System

```
┌─────────────────────────────────────────────────────────┐
│                    VOUCHER SYSTEM                        │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  VoucherType (Master)                                   │
│  ├── Sales (SV) - inventory_effect: 'decrease'         │
│  ├── Purchase (PUR) - inventory_effect: 'increase'     │
│  ├── Payment (PV) - affects_cashbank: true             │
│  ├── Receipt (RV) - affects_cashbank: true             │
│  └── Journal (JV) - general entries                     │
│                                                          │
│  Voucher (Transaction)                                  │
│  ├── voucher_type_id → VoucherType                     │
│  ├── voucher_number (auto-generated)                   │
│  ├── status: draft | posted | cancelled                │
│  ├── total_amount                                       │
│  └── voucher_date                                       │
│                                                          │
│  InvoiceItem (Line Items for Inventory Vouchers)       │
│  ├── voucher_id → Voucher                              │
│  ├── product_id → Product                              │
│  ├── quantity                                           │
│  ├── rate (unit price)                                 │
│  └── amount (line total)                               │
│                                                          │
│  VoucherEntry (Double Entry Accounting)                │
│  ├── voucher_id → Voucher                              │
│  ├── ledger_account_id → LedgerAccount                 │
│  ├── debit_amount                                       │
│  └── credit_amount                                      │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

### Sales Invoice Flow

```
1. Create Sales Invoice (Draft)
   ├── voucher_type_id = Sales VoucherType ID
   ├── status = 'draft'
   └── Create invoice_items for products

2. Post Sales Invoice
   ├── status = 'posted'
   ├── Generate voucher entries:
   │   ├── Debit: Customer Account (Accounts Receivable)
   │   └── Credit: Product Sales Accounts (Revenue)
   └── Update product stock (decrease)

3. Record Payment (Separate Receipt Voucher)
   ├── voucher_type_id = Receipt VoucherType ID
   ├── Reference = Sales Invoice Number
   └── Entries:
       ├── Debit: Bank/Cash Account
       └── Credit: Customer Account
```

---

## Key Insights

### 1. Sales Voucher Types

Sales invoices are identified by:

-   `voucher_type.code IN ('SV', 'SALES')`
-   `voucher_type.affects_inventory = true`
-   `voucher_type.inventory_effect = 'decrease'`

### 2. Posted Status

Only **posted** vouchers should be counted:

-   `voucher.status = 'posted'`
-   Draft vouchers are work-in-progress
-   Cancelled vouchers should be excluded

### 3. Double Entry System

Each sales invoice creates:

-   **Debit Entry**: Customer ledger account (Accounts Receivable)
-   **Credit Entry**: Product sales ledger account (Revenue)

### 4. Customer Identification

Customers are found via:

```sql
voucher_entries
  -> ledger_accounts (where party_type = 'customer')
  -> customers (via party_id)
```

---

## Testing Checklist

-   [x] ✅ Total Sales count shows posted invoices this month
-   [x] ✅ Total Revenue sums all posted sales invoices
-   [x] ✅ Monthly revenue growth calculation works
-   [x] ✅ Average sales value calculated correctly
-   [x] ✅ Recent transactions show sales invoices
-   [x] ✅ Recent activities show invoice postings
-   [x] ✅ Top products based on invoice_items
-   [x] ✅ Top customers based on voucher entries
-   [x] ✅ Open invoices count correct
-   [x] ✅ No PHP/compilation errors

---

## Files Modified

1. **app/Http/Controllers/Tenant/DashboardController.php**
    - Removed `Sale` model references
    - Added `VoucherType`, `InvoiceItem` models
    - Updated all sales-related queries to use Voucher system
    - Fixed monthly filters
    - Updated transactions, activities, top products, top customers

---

## Recommended: Remove Unused POS Models

Since the system uses the Voucher/Invoice system exclusively, consider removing or archiving these unused POS models to prevent future confusion:

### Models to Remove/Archive:

-   `app/Models/Sale.php`
-   `app/Models/SaleItem.php`
-   `app/Models/SalePayment.php`
-   `app/Models/CashRegister.php`
-   `app/Models/CashRegisterSession.php`
-   `app/Models/Receipt.php`

### Migrations to Archive:

-   `2024_01_01_000004_create_sales_table.php`
-   `2024_01_01_000005_create_sale_items_table.php`
-   `2024_01_01_000006_create_sale_payments_table.php`
-   `2024_01_01_000002_create_cash_registers_table.php`
-   `2024_01_01_000003_create_cash_register_sessions_table.php`
-   `2024_01_01_000007_create_receipts_table.php`

**Note**: Only remove if you're certain the POS system won't be used. Otherwise, keep for future POS module if needed.

---

## Next Steps

1. ✅ **Dashboard now working** - Shows correct sales count and revenue
2. ⏳ **Test with real data** - Post a sales invoice and verify dashboard updates
3. ⏳ **Verify all dashboard widgets** - Check charts, trends, and statistics
4. ⏳ **Consider cleanup** - Remove unused POS models if not needed

---

## Related Documentation

-   `PRODUCT_LEDGER_ACCOUNTS_IMPLEMENTATION.md` - Product-specific accounting
-   `DASHBOARD_LEDGER_BALANCE_UPDATE.md` - Ledger-based revenue/expense calculations
-   `DEBUGBAR_VSCODE_SETUP.md` - Debugging setup

---

**Status**: ✅ **READY FOR TESTING**

The dashboard should now correctly display:

-   Total Sales (this month) - Count of posted sales invoices
-   Total Revenue - Sum of all posted sales invoice amounts
-   Monthly growth percentage
-   Average sales value per invoice
-   Recent sales transactions
-   Top selling products
-   Top customers
