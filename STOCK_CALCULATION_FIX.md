# Stock Calculation Fix - Implementation Summary

## Problem

Product stock was showing as "Out of Stock" even after a purchase entry with 200 qty was made. The system was reading from the `products.current_stock` database column instead of calculating from `stock_movements` table using `transaction_date`.

## Root Cause

1. The `getCurrentStockAttribute()` method was only calculating from movements when `as_of_date` parameter was present in the request
2. Otherwise, it was returning the stale database column value (`current_stock`)
3. The `stock_status` attribute was using `current_stock` which pointed to the old column value

## Solution Implemented

### 1. Updated Product Model (`app/Models/Product.php`)

#### Changed `getCurrentStockAttribute()` method:

**Before:**

```php
public function getCurrentStockAttribute($value)
{
    // Only calculate when as_of_date is present
    if (request()->has('as_of_date') || config('inventory.use_realtime_stock', false)) {
        // Calculate from movements
    }

    // Return stored database value
    return $value ?? 0;
}
```

**After:**

```php
public function getCurrentStockAttribute($value)
{
    // ALWAYS calculate from stock movements
    $asOfDate = request('as_of_date', now()->toDateString());
    $cacheKey = "product_stock_{$this->id}_{$asOfDate}";

    return Cache::remember($cacheKey, 300, function () use ($asOfDate) {
        return $this->getStockAsOfDate($asOfDate);
    });
}
```

#### Updated `getStockValueAttribute()` method:

```php
public function getStockValueAttribute()
{
    // Calculate stock value from movements
    $asOfDate = request('as_of_date', now()->toDateString());
    $valuationMethod = request('valuation_method', 'weighted_average');

    $cacheKey = "product_stock_value_{$this->id}_{$asOfDate}_{$valuationMethod}";

    return Cache::remember($cacheKey, 300, function () use ($asOfDate, $valuationMethod) {
        $result = $this->getStockValueAsOfDate($asOfDate, $valuationMethod);
        return $result['value'] ?? 0; // Fixed: was 'total_value'
    });
}
```

### 2. Updated ProductController (`app/Http/Controllers/Tenant/Inventory/ProductController.php`)

#### Added Cache import:

```php
use Illuminate\Support\Facades\Cache;
```

#### Updated `show()` method:

```php
public function show(Tenant $tenant, Product $product)
{
    if ($product->tenant_id !== $tenant->id) {
        abort(404);
    }

    $product->load(['category', 'primaryUnit', 'stockAssetAccount', 'salesAccount', 'purchaseAccount']);

    // Clear cache to get fresh stock data
    $asOfDate = now()->toDateString();
    Cache::forget("product_stock_{$product->id}_{$asOfDate}");
    Cache::forget("product_stock_value_{$product->id}_{$asOfDate}_weighted_average");

    // Calculate real-time stock from movements
    $product->calculated_stock = $product->getStockAsOfDate($asOfDate);
    $product->calculated_stock_value = $product->getStockValueAsOfDate($asOfDate);

    return view('tenant.inventory.products.show', compact('tenant', 'product'));
}
```

## How It Works Now

### Stock Calculation Flow:

1. When `$product->current_stock` is accessed, the `getCurrentStockAttribute()` is called
2. It calculates stock by summing all quantities from `stock_movements` table where `transaction_date <= today`
3. Results are cached for 5 minutes (300 seconds) per product per date
4. Cache is cleared when viewing product details to ensure fresh data

### Stock Movements Query:

```sql
SELECT SUM(quantity)
FROM stock_movements
WHERE product_id = ?
AND transaction_date <= ?
```

### Example with Product ID 48 (Milk):

-   **Purchase Entry**: 200 pcs on Oct 10, 2025 at ₦200.00
-   **Calculated Stock**: 200.00 pcs ✅
-   **Stock Value**: ₦40,000.00 ✅
-   **Stock Status**: IN STOCK ✅
-   **Database Column**: 0.00 pcs (ignored, not used)

## Benefits

1. ✅ **Accurate Stock**: Always reflects actual movements in real-time
2. ✅ **Date-Based**: Can calculate stock as of any historical date
3. ✅ **Auditable**: Stock tied to transaction dates, not update timestamps
4. ✅ **Performance**: Short-term caching (5 min) balances accuracy and speed
5. ✅ **Backward Compatible**: Existing code continues to work

## Migration Path

### Database Columns (No longer used):

-   `products.current_stock` - Kept for backward compatibility, but not used
-   `products.current_stock_value` - Kept for backward compatibility, but not used

### New Calculation:

-   Stock is calculated on-the-fly from `stock_movements` table
-   Values are cached per product per date for performance

## Testing

Run the test script:

```bash
php test_product_stock_fix.php
```

Expected output:

```
=== Product Stock Calculation Test ===
Testing Product ID: 48
Product Name: Milk

Stock Movements:
Date: 2025-10-10 | Type: purchase | IN: 200.00 | Running: 200.00

Calculated Stock: 200.00 pcs
Current Stock: 200.00 pcs
Stock Value: ₦40,000.00
Stock Status: ✅ IN STOCK
```

## Files Modified

1. `app/Models/Product.php`

    - `getCurrentStockAttribute()` - Always calculate from movements
    - `getStockValueAttribute()` - Always calculate from movements

2. `app/Http/Controllers/Tenant/Inventory/ProductController.php`

    - Added `Cache` import
    - Updated `show()` method to clear cache and recalculate

3. `test_product_stock_fix.php` (New)
    - Test script to verify stock calculations

## Next Steps

1. ✅ Clear all product stock caches when needed
2. ✅ Product show page displays correct stock
3. ✅ Product listing page already uses calculated stock
4. ⏳ Consider removing `current_stock` column in future migration (optional)
5. ⏳ Update any reports that directly query `current_stock` column

## Cache Management

Cache keys pattern:

-   Stock: `product_stock_{product_id}_{date}`
-   Value: `product_stock_value_{product_id}_{date}_{method}`

Cache duration: 5 minutes (300 seconds)

To clear cache for a specific product:

```php
Cache::forget("product_stock_{$productId}_{$date}");
Cache::forget("product_stock_value_{$productId}_{$date}_weighted_average");
```

To clear all product caches:

```php
Cache::flush(); // Clear all cache (use with caution)
```

---

**Status**: ✅ FIXED AND TESTED
**Date**: October 10, 2025
**Product ID Tested**: 48 (Milk)
