# Profit & Loss Stock Value Fix

## Issue

Stock values (Opening Stock and Closing Stock) were showing ₦0.00 in the P&L report, even though the Stock Summary Report correctly showed ₦11,500,000.00.

## Root Cause

The P&L report was summing `opening_stock_value` and `current_stock_value` columns directly from the database, which were NULL or not being updated. The Stock Summary report worked correctly because it calculated values from stock movements using the `getStockValueAsOfDate()` method.

## Solution Implemented

Updated the `profitLoss()` method in `app/Http/Controllers/Tenant/Reports/ReportsController.php` to:

1. **Calculate Opening Stock**: Stock value as of the day before the period start date
2. **Calculate Closing Stock**: Stock value as of the period end date
3. **Use Weighted Average Method**: Consistent with Stock Summary report calculations
4. **Iterate Through Products**: Calculate stock value for each product and sum them

### Code Changes

**Before:**

```php
$openingStock = Product::where('tenant_id', $tenant->id)
    ->where('maintain_stock', true)
    ->sum('opening_stock_value');

$closingStock = Product::where('tenant_id', $tenant->id)
    ->where('maintain_stock', true)
    ->sum('current_stock_value');
```

**After:**

```php
// Calculate stock values for the period (like Tally ERP)
// Opening Stock: Stock value as of the day before period start
// Closing Stock: Stock value as of period end
$openingStockDate = date('Y-m-d', strtotime($fromDate . ' -1 day'));

$products = Product::where('tenant_id', $tenant->id)
    ->where('maintain_stock', true)
    ->get();

$openingStock = 0;
$closingStock = 0;

foreach ($products as $product) {
    // Calculate opening stock value (day before period start)
    $openingStockData = $product->getStockValueAsOfDate($openingStockDate, 'weighted_average');
    $openingStock += $openingStockData['value'] ?? 0;

    // Calculate closing stock value (period end date)
    $closingStockData = $product->getStockValueAsOfDate($toDate, 'weighted_average');
    $closingStock += $closingStockData['value'] ?? 0;
}
```

## How It Works

1. **Opening Stock Date**: Calculated as the day before the period start date

    - If period is Nov 1 to Nov 22, opening stock is calculated as of Oct 31

2. **Stock Value Calculation**: Uses the `getStockValueAsOfDate()` method which:

    - Retrieves all stock movements up to the specified date
    - Applies weighted average costing method
    - Returns quantity, value, and average rate

3. **Period-Based**: Stock values are now period-specific, matching the selected date range

## Benefits

✅ Stock values now display correctly in P&L report
✅ Consistent with Stock Summary report calculations
✅ Period-specific calculations (like Tally ERP)
✅ Uses proper weighted average costing method
✅ Calculates from actual stock movements, not static columns

## Testing

To verify the fix:

1. Navigate to Profit & Loss Report
2. Select a date range (e.g., Nov 1, 2025 to Nov 22, 2025)
3. Check the Stock Summary section at the bottom
4. Opening Stock and Closing Stock should now show actual values
5. Compare with Stock Summary Report for the same date - values should match

## Files Modified

-   `app/Http/Controllers/Tenant/Reports/ReportsController.php`

## Related Files (Reference Only)

-   `app/Http/Controllers/Tenant/Reports/InventoryReportsController.php` - Stock Summary calculation reference
-   `app/Models/Product.php` - Contains `getStockValueAsOfDate()` method
-   `resources/views/tenant/reports/profit-loss.blade.php` - P&L view (no changes needed)
