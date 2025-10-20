# Products Import Feature

## Overview

This feature allows tenants to bulk upload products using Excel files (XLSX, XLS, CSV) with support for opening stock, categories, and comprehensive product details.

## Features Implemented

### 1. **Import Template Generation**

-   **Route**: `GET /tenant/{tenant}/inventory/products/export/template`
-   **Controller**: `ProductController@downloadTemplate`
-   **Export Class**: `ProductsTemplateExport`
-   **File Name**: `products_import_template.xlsx`

**Template Columns**:

1. Product Name\* (required)
2. Type\* (item/service)
3. SKU
4. Description
5. Category
6. Brand
7. HSN Code
8. Purchase Rate\* (required)
9. Sales Rate\* (required)
10. MRP
11. Primary Unit\* (required)
12. Unit Conversion Factor
13. Opening Stock
14. Opening Stock Date
15. Reorder Level
16. Stock Asset Account
17. Sales Account
18. Purchase Account
19. Tax Rate (%)
20. Tax Inclusive (yes/no)
21. Barcode
22. Maintain Stock (yes/no)
23. Is Active (yes/no)
24. Is Saleable (yes/no)
25. Is Purchasable (yes/no)

### 2. **Categories Reference Export**

-   **Route**: `GET /tenant/{tenant}/inventory/products/export/categories-reference`
-   **Controller**: `ProductController@downloadCategoriesReference`
-   **Export Class**: `ProductCategoriesReferenceExport`
-   **File Name**: `product_categories_reference.xlsx`
-   **Purpose**: Provides list of available categories for the tenant

**Reference Columns**:

-   Category ID
-   Category Name
-   Slug
-   Parent Category
-   Description
-   Is Active

### 3. **Product Import Processing**

-   **Route**: `POST /tenant/{tenant}/inventory/products/import`
-   **Controller**: `ProductController@importProducts`
-   **Import Class**: `ProductsImport`
-   **Max File Size**: 10MB
-   **Accepted Formats**: .xlsx, .xls, .csv

## Import Logic

### Validation Rules

1. **Required Fields**:

    - Product Name
    - Type (item/service)
    - Purchase Rate
    - Sales Rate
    - Primary Unit

2. **Unique Constraints**:

    - SKU must be unique per tenant
    - Existing SKUs are skipped

3. **Foreign Key Validation**:
    - Category must exist for the tenant
    - Unit must exist for the tenant
    - Ledger accounts must exist if provided

### Data Processing

1. **Type Normalization**: Converts to lowercase (item/service)
2. **SKU Generation**: Auto-generates if not provided
3. **Boolean Fields**: Accepts yes/no, true/false, 1/0, active
4. **Stock Management**:
    - Sets `opening_stock` and `current_stock` to 0 in products table
    - Creates StockMovement entry for opening stock
    - Transaction type: `opening_stock`
    - Transaction reference: `OPENING-{product_id}`
    - Default date: yesterday if not provided

### Opening Stock Handling

When opening stock is provided:

```php
StockMovement::create([
    'tenant_id' => $tenant->id,
    'product_id' => $product->id,
    'type' => 'in',
    'quantity' => $openingStock,
    'old_stock' => 0,
    'new_stock' => $openingStock,
    'rate' => $purchaseRate,
    'transaction_type' => 'opening_stock',
    'transaction_date' => $openingStockDate,
    'transaction_reference' => 'OPENING-' . $product->id,
    'reference' => 'Opening Stock for ' . $product->name,
    'remarks' => 'Initial opening stock entry via import',
    'created_by' => auth()->id(),
]);
```

## User Interface

### Import Button

-   **Location**: Inventory Dashboard (`/tenant/{tenant}/inventory`)
-   **Button Text**: "Upload Products"
-   **Icon**: Cloud upload icon
-   **Action**: Opens import modal

### Import Modal

**Components**:

1. **Instructions Section**: Step-by-step guide
2. **Download Buttons**:
    - Template download (green button)
    - Categories reference download (blue button)
3. **File Upload Area**: Drag & drop or click to upload
4. **Important Notes**: List of validation rules and requirements
5. **Action Buttons**: Cancel and Import

### Success/Error Handling

-   **Success Message**: "{X} products imported successfully. {Y} rows skipped."
-   **Error Display**: Individual row errors shown in session flash
-   **Validation**: Client-side file validation before upload

## Technical Implementation

### Files Created

1. `app/Imports/ProductsImport.php` (287 lines)
2. `app/Exports/ProductsTemplateExport.php` (108 lines)
3. `app/Exports/ProductCategoriesReferenceExport.php` (77 lines)
4. `resources/views/tenant/inventory/products/partials/import-modal.blade.php` (178 lines)

### Controller Methods Added

1. `downloadTemplate()` - Generate and download template
2. `downloadCategoriesReference()` - Generate and download categories
3. `importProducts()` - Process uploaded file

### Routes Added

```php
Route::get('products/export/template', [ProductController::class, 'downloadTemplate'])
    ->name('tenant.inventory.products.export.template');

Route::get('products/export/categories-reference', [ProductController::class, 'downloadCategoriesReference'])
    ->name('tenant.inventory.products.export.categories-reference');

Route::post('products/import', [ProductController::class, 'importProducts'])
    ->name('tenant.inventory.products.import');
```

## Performance Optimization

-   **Batch Processing**: 100 rows per batch
-   **Chunk Reading**: Processes 100 rows at a time
-   **Memory Management**: Uses chunking to handle large files

## Error Handling

1. **Row-level errors**: Tracked and reported individually
2. **File validation**: Checks format and size before processing
3. **Database rollback**: On critical errors (handled per row)
4. **Logging**: All errors logged to Laravel log

## Usage Instructions

### For Users:

1. Click "Upload Products" button on inventory dashboard
2. Download the template file
3. (Optional) Download categories reference for valid category names
4. Fill in product data in the template
5. Save the file
6. Upload the file in the modal
7. Review import results

### For Developers:

```php
// Example: Import products programmatically
$import = new ProductsImport($tenant);
Excel::import($import, $filePath);

// Get results
$imported = $import->getImported();
$skipped = $import->getSkipped();
$errors = $import->getErrors();
```

## Important Notes

1. **Opening Stock**: Automatically creates stock movement entries
2. **Stock Calculation**: Products stock is calculated from movements
3. **Services**: Don't require stock-related fields
4. **Units**: Must be created before importing products
5. **Categories**: Optional but must exist if provided
6. **Ledger Accounts**: Optional but must exist if provided

## Future Enhancements

-   [ ] Support for product images via URL
-   [ ] Support for product variants
-   [ ] Update existing products option
-   [ ] Dry-run mode (preview before import)
-   [ ] Background job processing for large files
-   [ ] Email notification on completion
-   [ ] Import history tracking

## Testing Checklist

-   [ ] Template downloads correctly
-   [ ] Categories reference exports tenant's categories
-   [ ] Valid file imports successfully
-   [ ] Invalid rows are skipped with errors
-   [ ] Opening stock creates stock movements
-   [ ] SKU uniqueness validation works
-   [ ] Foreign key validation works
-   [ ] Large files process without timeout
-   [ ] Modal opens and closes correctly
-   [ ] Success/error messages display properly
