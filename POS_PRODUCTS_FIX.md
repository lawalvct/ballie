# POS Products Not Showing - Fix Documentation

## Problem

Products were not appearing in the POS (Point of Sale) page even though they had stock available. The issue was specifically with Product ID 48 (Milk) which had 200 pieces in stock from a purchase entry but was not showing in the POS product list.

## Root Cause

The `PosController` was using a direct database column query to filter products:

```php
// OLD CODE - INCORRECT
'products' => Product::where('tenant_id', $tenant->id)
    ->where('is_active', true)
    ->where('current_stock', '>', 0)  // ❌ Reading stale DB column
    ->with(['category', 'unit'])
    ->orderBy('name')
    ->get(),
```

This query used `where('current_stock', '>', 0)` which reads from the `products.current_stock` database column. However, this column is outdated and doesn't reflect the actual stock movements.

### The System Flow:

1. ✅ Product stock is tracked via `stock_movements` table with `transaction_date`
2. ✅ Product model calculates stock from movements dynamically
3. ❌ POS controller was bypassing the calculation and reading the old column directly

### Result:

-   Database column `current_stock`: 0 (stale)
-   Calculated stock from movements: 200 (correct)
-   POS query filtered out the product because DB column = 0

## Solution Implemented

### File: `app/Http/Controllers/Tenant/Pos/PosController.php`

**Before:**

```php
if ($activeSession) {
    $data = array_merge($data, [
        'products' => Product::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->where('current_stock', '>', 0)  // ❌ Wrong - reads DB column
            ->with(['category', 'unit'])
            ->orderBy('name')
            ->get(),
        // ... rest of code
    ]);
}
```

**After:**

```php
if ($activeSession) {
    // Load products and filter by calculated stock (not database column)
    $allProducts = Product::where('tenant_id', $tenant->id)
        ->where('is_active', true)
        ->with(['category', 'unit'])
        ->orderBy('name')
        ->get();

    // Filter products with stock > 0 using calculated stock from movements
    $productsWithStock = $allProducts->filter(function ($product) {
        return $product->current_stock > 0; // ✅ Uses calculated stock from movements
    })->values();

    $data = array_merge($data, [
        'products' => $productsWithStock,
        // ... rest of code
    ]);
}
```

## How It Works Now

### Complete Flow:

1. **POS page loads** → `PosController@index()`
2. **Controller loads all active products** → No database column filtering
3. **For each product:**
    - Accesses `$product->current_stock`
    - Triggers `getCurrentStockAttribute()` in Product model
    - Model calculates stock by summing `stock_movements.quantity` where `transaction_date <= today`
    - Result is cached for 5 minutes
4. **Controller filters:** Only products with calculated stock > 0
5. **View displays:** Products that actually have stock available

### Stock Calculation:

```sql
SELECT SUM(quantity)
FROM stock_movements
WHERE product_id = ?
AND transaction_date <= CURRENT_DATE
```

## Testing Results

### Test Product: ID 48 (Milk)

```
Testing for Tenant: Graiden Richardsonnew (ID: 42)

PRODUCT DETAILS:
Product Name:      Milk
Current Stock:     200.00 pcs      ✅
Sales Price:       ₦250.00
Is Active:         Yes
Should Show in POS: ✅ YES

COMPARISON:
OLD METHOD (Direct DB Column): 0 products found  ❌
NEW METHOD (Calculated Stock):  1 product found  ✅
```

**Result:** ✅ Product now shows in POS!

## Benefits

1. ✅ **Accurate Product List** - Shows products with real-time stock
2. ✅ **Consistent with Inventory** - POS and inventory use same calculation
3. ✅ **Prevents Overselling** - Only shows products actually available
4. ✅ **Real-time Updates** - Reflects latest stock movements
5. ✅ **Audit Trail** - Stock tied to transaction dates

## Impact on Other Areas

Since we're now using calculated stock everywhere:

### ✅ Already Fixed:

1. Product show page
2. Product listing page
3. Inventory dashboard
4. Stock statistics
5. Low stock alerts
6. **POS product list** (this fix)

### Related Systems:

-   Sales transactions will properly deduct from calculated stock
-   Stock movements are the single source of truth
-   All views show consistent stock levels

## Performance Considerations

-   **Caching:** Stock is cached for 5 minutes per product
-   **Load Time:** Minimal impact - loads all products once, then filters in memory
-   **Scalability:** For tenants with 1000+ products:
    -   Consider eager loading relationships
    -   May need to increase cache duration
    -   Add database index: `stock_movements(product_id, transaction_date)`

## Testing

### Run Test Script:

```bash
php test_pos_products.php
```

### Expected Output:

```
AVAILABLE PRODUCTS FOR POS:
ID    Name            Stock        Price       Category
48    Milk           200.00       ₦250.00     Food & Beverages

✅ YES - Product shows in POS
```

### Manual Testing:

1. ✅ Open POS page (with active cash register session)
2. ✅ Product 48 (Milk) should appear in product list
3. ✅ Shows correct stock: 200 pcs
4. ✅ Shows correct price: ₦250.00
5. ✅ Can be added to cart and sold

## Files Modified

1. ✅ `app/Http/Controllers/Tenant/Pos/PosController.php`

    - `index()` method - Product loading logic changed from DB query to collection filtering

2. ✅ `app/Models/Product.php` (Previously fixed)
    - `getCurrentStockAttribute()` - Always calculates from movements

## Migration Notes

### No Database Changes Required

-   Old `current_stock` column still exists (for backward compatibility)
-   No data migration needed
-   System now ignores the column and calculates dynamically

### Deployment Steps:

1. ✅ Deploy updated `PosController.php`
2. ✅ Clear application cache: `php artisan cache:clear`
3. ✅ Test POS page with products that have stock movements
4. ✅ Verify products appear correctly

## Related Issues Fixed

This fix is part of a series of stock calculation improvements:

1. ✅ **STOCK_CALCULATION_FIX.md** - Product model stock calculation
2. ✅ **INVENTORY_DASHBOARD_FIX.md** - Dashboard statistics
3. ✅ **POS_PRODUCTS_FIX.md** (This document) - POS product loading

All three fixes ensure the entire system uses calculated stock from movements instead of stale database columns.

## Troubleshooting

### If products still don't show in POS:

1. **Check product is active:**

    ```sql
    SELECT id, name, is_active FROM products WHERE id = 48;
    ```

    Expected: `is_active = 1`

2. **Check stock movements exist:**

    ```sql
    SELECT * FROM stock_movements WHERE product_id = 48;
    ```

    Expected: At least one record with positive quantity

3. **Check calculated stock:**

    ```php
    php artisan tinker
    $product = Product::find(48);
    echo $product->current_stock;
    ```

    Expected: Value > 0

4. **Clear cache:**

    ```bash
    php artisan cache:clear
    ```

5. **Check cash register session is active:**
    - POS only loads products when there's an active session
    - Open a cash register session first

## Next Steps

1. ✅ Refresh POS page - Product should now appear
2. ✅ Test sales transactions - Stock should decrease correctly
3. ⏳ Monitor performance with larger product catalogs
4. ⏳ Consider adding real-time stock validation during checkout
5. ⏳ Update any custom reports that might use `current_stock` column

---

**Status:** ✅ FIXED AND TESTED
**Date:** October 10, 2025
**Issue:** Products not showing in POS
**Cause:** Direct database column query instead of calculated stock
**Solution:** Load all products and filter by calculated stock from movements
**Test Product:** Milk (ID: 48) with 200 pcs - Now shows in POS ✅
**Test Tenant:** Graiden Richardsonnew (ID: 42)
