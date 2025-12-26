<?php

namespace App\Http\Controllers\Tenant\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\Unit;
use App\Models\LedgerAccount;
use App\Models\Tenant;
use App\Models\StockMovement;
use App\Imports\ProductsImport;
use App\Exports\ProductsTemplateExport;
use App\Exports\ProductCategoriesReferenceExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());
        $valuationMethod = $request->get('valuation_method', 'weighted_average');

        $query = Product::where('tenant_id', $tenant->id)
            ->with(['category', 'primaryUnit']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
            }
        }

        $products = $query->latest()->paginate(15);

        // Calculate date-based stock for each product
        $products->getCollection()->transform(function ($product) use ($asOfDate, $valuationMethod) {
            if ($product->type === 'item' && $product->maintain_stock) {
                $stockData = $product->getStockValueAsOfDate($asOfDate, $valuationMethod);
                $product->calculated_stock = $stockData['quantity'];
                $product->calculated_stock_value = $stockData['value'];
                $product->calculated_average_rate = $stockData['average_rate'];

                // Determine stock status based on calculated stock
                if ($stockData['quantity'] <= 0) {
                    $product->calculated_stock_status = 'out_of_stock';
                } elseif ($stockData['quantity'] <= ($product->reorder_level ?? 0)) {
                    $product->calculated_stock_status = 'low_stock';
                } else {
                    $product->calculated_stock_status = 'in_stock';
                }
            } else {
                $product->calculated_stock = null;
                $product->calculated_stock_value = null;
                $product->calculated_stock_status = 'not_applicable';
            }

            return $product;
        });

        $categories = ProductCategory::where('tenant_id', $tenant->id)->active()->get();

        // Calculate statistics based on date
        $totalProducts = Product::where('tenant_id', $tenant->id)->count();
        $activeProducts = Product::where('tenant_id', $tenant->id)->where('is_active', true)->count();

        // For low stock and out of stock, we'll use the current calculation for performance
        // In a production system, you might want to cache these calculations
        $lowStockProducts = Product::where('tenant_id', $tenant->id)->lowStock()->count();
        $outOfStockProducts = Product::where('tenant_id', $tenant->id)->outOfStock()->count();

        return view('tenant.inventory.products.index', compact(
            'products',
            'categories',
            'tenant',
            'totalProducts',
            'activeProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'asOfDate',
            'valuationMethod'
        ));
    }

    public function create(Tenant $tenant)
    {
        $categories = ProductCategory::where('tenant_id', $tenant->id)->active()->get();
        $units = Unit::where('tenant_id', $tenant->id)->active()->get();
        $ledgerAccounts = LedgerAccount::where('tenant_id', $tenant->id)->active()->get();

        // Get default accounts by name for this tenant
        $defaultStockAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('name', 'Inventory')
            ->active()
            ->first();

        $defaultSalesAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('name', 'Sales Revenue')
            ->active()
            ->first();

        $defaultPurchaseAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('name', 'Cost of Goods Sold')
            ->active()
            ->first();

        return view('tenant.inventory.products.create', compact(
            'categories',
            'units',
            'ledgerAccounts',
            'tenant',
            'defaultStockAccount',
            'defaultSalesAccount',
            'defaultPurchaseAccount'
        ));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $rules = [
            'type' => 'required|in:item,service',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,NULL,id,tenant_id,' . $tenant->id,
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:product_categories,id',
            'brand' => 'nullable|string|max:255',
            'hsn_code' => 'nullable|string|max:50',
            'purchase_rate' => 'required|numeric|min:0',
            'sales_rate' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'primary_unit_id' => $request->type === 'service' ? 'nullable|exists:units,id' : 'required|exists:units,id',
            'opening_stock' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'barcode' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock_asset_account_id' => 'nullable|exists:ledger_accounts,id',
            'sales_account_id' => 'nullable|exists:ledger_accounts,id',
            'purchase_account_id' => 'nullable|exists:ledger_accounts,id',
            'opening_stock_date' => 'nullable|date',
            // E-commerce fields
            'slug' => 'nullable|string|max:255|unique:products,slug,NULL,id,tenant_id,' . $tenant->id,
            'short_description' => 'nullable|string|max:500',
            'long_description' => 'nullable|string',
            'is_visible_online' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['tenant_id'] = $tenant->id;
            $data['created_by'] = auth()->id();

            // Handle primary image upload
            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')->store('products', 'public');
            }

            // Set opening stock to 0 in product table - will be calculated from movements
            $openingStock = $request->filled('opening_stock') ? floatval($request->opening_stock) : 0;
            $openingStockDate = $request->filled('opening_stock_date') ? $request->opening_stock_date : now()->subDay()->toDateString();

            // Calculate opening stock value
            if ($openingStock > 0 && $request->filled('purchase_rate')) {
                $data['opening_stock_value'] = $openingStock * floatval($request->purchase_rate);
            } else {
                $data['opening_stock_value'] = 0;
            }

            // Set current_stock to 0 - will be calculated from movements
            $data['opening_stock'] = 0;
            $data['current_stock'] = 0;
            $data['current_stock_value'] = 0;

            $product = Product::create($data);

            // Create opening stock movement if opening stock is provided and product maintains stock
            if ($openingStock > 0 && $product->maintain_stock && $product->type === 'item') {
                StockMovement::create([
                    'tenant_id' => $tenant->id,
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $openingStock,
                    'old_stock' => 0,
                    'new_stock' => $openingStock,
                    'rate' => floatval($request->purchase_rate ?? 0),
                    'transaction_type' => 'opening_stock',
                    'transaction_date' => $openingStockDate,
                    'transaction_reference' => 'OPENING-' . $product->id,
                    'reference' => 'Opening Stock for ' . $product->name,
                    'remarks' => 'Initial opening stock entry',
                    'created_by' => auth()->id(),
                ]);

                // Clear cache to ensure fresh calculation
                $cacheKey = "product_stock_{$product->id}_" . now()->toDateString();
                Cache::forget($cacheKey);
            }

            // Handle gallery images upload
            if ($request->hasFile('gallery_images')) {
                $sortOrder = $product->images()->max('sort_order') ?? 0;

                foreach ($request->file('gallery_images') as $galleryImage) {
                    $sortOrder++;
                    $imagePath = $galleryImage->store('products/gallery', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imagePath,
                        'is_primary' => false,
                        'sort_order' => $sortOrder,
                    ]);
                }
            }

            DB::commit();

            // Check if it's an AJAX request
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product created successfully!',
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'sales_rate' => $product->sales_rate,
                        'purchase_rate' => $product->purchase_rate,
                        'current_stock' => $product->getStockAsOfDate(now()->toDateString()),
                        'unit_name' => $product->primaryUnit ? $product->primaryUnit->abbreviation : '',
                    ]
                ]);
            }

            return redirect()
                ->route('tenant.inventory.products.index', ['tenant' => $tenant->slug])
                ->with('success', 'Product created successfully!' . ($openingStock > 0 ? ' Opening stock of ' . $openingStock . ' has been recorded.' : ''));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage());

            // Check if it's an AJAX request
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create product: ' . $e->getMessage()
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

   public function show(Tenant $tenant, Product $product)
{
    // Ensure the product belongs to the tenant
    if ($product->tenant_id !== $tenant->id) {
        abort(404);
    }

    $product->load(['category', 'primaryUnit', 'stockAssetAccount', 'salesAccount', 'purchaseAccount', 'images']);

    // Clear cache to get fresh stock data
    $asOfDate = now()->toDateString();
    Cache::forget("product_stock_{$product->id}_{$asOfDate}");
    Cache::forget("product_stock_value_{$product->id}_{$asOfDate}_weighted_average");

    // Calculate real-time stock from movements
    $product->calculated_stock = $product->getStockAsOfDate($asOfDate);
    $product->calculated_stock_value = $product->getStockValueAsOfDate($asOfDate);

    return view('tenant.inventory.products.show', compact('tenant', 'product'));
}

/**
 * Show stock movements for a product
 */
public function stockMovements(Request $request, Tenant $tenant, Product $product)
{
    // Ensure the product belongs to the tenant
    if ($product->tenant_id !== $tenant->id) {
        abort(404);
    }

    $fromDate = $request->get('from_date', now()->subMonth()->toDateString());
    $toDate = $request->get('to_date', now()->toDateString());
    $transactionType = $request->get('transaction_type');

    $query = $product->stockMovements()
        ->whereBetween('transaction_date', [$fromDate, $toDate])
        ->with(['creator']);

    if ($transactionType) {
        $query->where('transaction_type', $transactionType);
    }

    $movements = $query->orderBy('transaction_date', 'desc')
        ->orderBy('id', 'desc')
        ->paginate(50);

    // Calculate running stock balance
    $startingStock = $product->getStockAsOfDate($fromDate);
    $runningStock = $startingStock;

    // Apply running balance to movements (works correctly with ascending order)
    $movements->getCollection()->transform(function ($movement) use (&$runningStock) {
        $runningStock += $movement->quantity;
        $movement->running_stock = $runningStock;
        return $movement;
    });

    // Get transaction types for filter
    $transactionTypes = StockMovement::where('product_id', $product->id)
        ->select('transaction_type')
        ->whereNotNull('transaction_type')
        ->distinct()
        ->pluck('transaction_type')
        ->sort();

    return view('tenant.inventory.products.stock-movements', compact(
        'product',
        'movements',
        'fromDate',
        'toDate',
        'transactionType',
        'transactionTypes',
        'startingStock',
        'tenant'
    ));
}

public function toggleStatus(Request $request, Tenant $tenant, Product $product)
{
    // Ensure the product belongs to the tenant
    if ($product->tenant_id !== $tenant->id) {
        abort(404);
    }

    try {
        $product->update([
            'is_active' => !$product->is_active
        ]);

        $status = $product->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Product {$status} successfully.");
    } catch (\Exception $e) {
        Log::error('Error toggling product status: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'An error occurred while updating the product status.');
    }
}


   public function edit(Tenant $tenant, Product $product)
{
    // Ensure the product belongs to the tenant
    if ($product->tenant_id !== $tenant->id) {
        abort(404);
    }

    $categories = ProductCategory::where('tenant_id', $tenant->id)
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    $units = Unit::where('tenant_id', $tenant->id)
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('tenant.inventory.products.edit', compact('tenant', 'product', 'categories', 'units'));
}

public function update(Request $request, Tenant $tenant, Product $product)
{
    // Ensure the product belongs to the tenant
    if ($product->tenant_id !== $tenant->id) {
        abort(404);
    }

    $validator = Validator::make($request->all(), [
        'type' => 'required|in:item,service',
        'name' => 'required|string|max:255',
        'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id . ',id,tenant_id,' . $tenant->id,
        'description' => 'nullable|string',
        'category_id' => 'nullable|exists:product_categories,id',
        'brand' => 'nullable|string|max:255',
        'hsn_code' => 'nullable|string|max:50',
        'purchase_rate' => 'required|numeric|min:0',
        'sales_rate' => 'required|numeric|min:0',
        'mrp' => 'nullable|numeric|min:0',
        'primary_unit_id' => 'required|exists:units,id',
        'opening_stock' => 'nullable|numeric|min:0',
        'current_stock' => 'nullable|numeric|min:0',
        'reorder_level' => 'nullable|numeric|min:0',
        'tax_rate' => 'nullable|numeric|min:0|max:100',
        'barcode' => 'nullable|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'maintain_stock' => 'nullable|boolean',
        'is_active' => 'nullable|boolean',
        'is_saleable' => 'nullable|boolean',
        'is_purchasable' => 'nullable|boolean',
        'tax_inclusive' => 'nullable|boolean',
        // E-commerce fields
        'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id . ',id,tenant_id,' . $tenant->id,
        'short_description' => 'nullable|string|max:500',
        'long_description' => 'nullable|string',
        'is_visible_online' => 'nullable|boolean',
        'is_featured' => 'nullable|boolean',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    try {
        DB::beginTransaction();

        $data = $request->except(['image', 'gallery_images']);

        // Handle boolean fields
        $data['maintain_stock'] = $request->has('maintain_stock');
        $data['is_active'] = $request->has('is_active');
        $data['is_saleable'] = $request->has('is_saleable');
        $data['is_purchasable'] = $request->has('is_purchasable');
        $data['tax_inclusive'] = $request->has('tax_inclusive');
        $data['is_visible_online'] = $request->has('is_visible_online');
        $data['is_featured'] = $request->has('is_featured');

        // Handle primary image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $imagePath = $request->file('image')->store('products', 'public');
            $data['image_path'] = $imagePath;
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $sortOrder = $product->images()->max('sort_order') ?? 0;

            foreach ($request->file('gallery_images') as $galleryImage) {
                $sortOrder++;
                $imagePath = $galleryImage->store('products/gallery', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'is_primary' => false,
                    'sort_order' => $sortOrder,
                ]);
            }
        }

        // Calculate stock values if stock is maintained
        if ($data['maintain_stock']) {
            $data['opening_stock_value'] = ($data['opening_stock'] ?? 0) * $data['purchase_rate'];
            $data['current_stock_value'] = ($data['current_stock'] ?? 0) * $data['purchase_rate'];
        } else {
            // Clear stock-related fields if stock is not maintained
            $data['opening_stock'] = null;
            $data['current_stock'] = null;
            $data['reorder_level'] = null;
            $data['opening_stock_value'] = null;
            $data['current_stock_value'] = null;
        }

        // Set updated_by
        $data['updated_by'] = auth()->id();

        $product->update($data);

        DB::commit();

        return redirect()->route('tenant.inventory.products.show', ['tenant' => $tenant->slug, 'product' => $product->id])
            ->with('success', 'Product updated successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating product: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'An error occurred while updating the product. Please try again.')
            ->withInput();
    }
}

    /**
     * Delete a product gallery image
     */
    public function deleteImage(Request $request, $imageId)
    {
        $tenant = $request->tenant;

        try {
            $image = ProductImage::findOrFail($imageId);

            // Verify the image belongs to a product owned by this tenant
            if ($image->product->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Delete the image file from storage
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }

            // Delete the database record
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting product image: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image'
            ], 500);
        }
    }

    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Invalid bulk action request.');
        }

        try {
            DB::beginTransaction();

            $products = Product::where('tenant_id', tenant()->id)
                              ->whereIn('id', $request->products)
                              ->get();

            switch ($request->action) {
                case 'activate':
                    $products->each(function ($product) {
                        $product->update(['is_active' => true]);
                    });
                    $message = 'Products activated successfully.';
                    break;

                case 'deactivate':
                    $products->each(function ($product) {
                        $product->update(['is_active' => false]);
                    });
                    $message = 'Products deactivated successfully.';
                    break;

                case 'delete':
                    foreach ($products as $product) {
                        // Check if product has transactions
                        $hasTransactions = $product->stockMovements()->count() > 0;

                        if (!$hasTransactions) {
                            // Delete product image if exists
                            if ($product->image_path) {
                                Storage::disk('public')->delete($product->image_path);
                            }
                            $product->delete();
                        }
                    }
                    $message = 'Products deleted successfully (excluding those with transaction history).';
                    break;
            }

            DB::commit();

            return redirect()->route('tenant.products.index', ['tenant' => tenant()->slug])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'An error occurred while performing bulk action. Please try again.');
        }
    }

    public function export(Request $request)
    {
        $query = Product::where('tenant_id', tenant()->id);

        // Apply same filters as index
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        if ($request->has('status') && $request->status != '') {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'low_stock':
                    $query->where('maintain_stock', true)
                          ->whereColumn('current_stock', '<=', 'reorder_level');
                    break;
                case 'out_of_stock':
                    $query->where('maintain_stock', true)
                          ->where('current_stock', '<=', 0);
                    break;
            }
        }

        $products = $query->orderBy('name')->get();

        $filename = 'products_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Name', 'SKU', 'Type', 'Category', 'Brand', 'Model',
                'Purchase Rate', 'Sales Rate', 'MRP', 'Primary Unit',
                'Current Stock', 'Reorder Level', 'Tax Rate', 'Status'
            ]);

            // CSV data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->sku,
                    ucfirst($product->type),
                    $product->category,
                    $product->brand,
                    $product->model,
                    $product->purchase_rate,
                    $product->sales_rate,
                    $product->mrp,
                    $product->primary_unit,
                    $product->current_stock,
                    $product->reorder_level,
                    $product->tax_rate,
                    $product->is_active ? 'Active' : 'Inactive'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function destroy(Tenant $tenant, Product $product)
{
    // Ensure the product belongs to the tenant
    if ($product->tenant_id !== $tenant->id) {
        abort(404);
    }

    try {
        // Check if product has any related transactions/records
        $hasTransactions = false;

        // You can add checks for related records here, for example:
        // $hasTransactions = $product->invoiceItems()->exists() ||
        //                   $product->purchaseItems()->exists() ||
        //                   $product->stockMovements()->exists();

        if ($hasTransactions) {
            return redirect()->back()
                ->with('error', 'Cannot delete product as it has related transaction records. You can deactivate it instead.');
        }

        // Delete product image if exists
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        // Delete the product
        $product->delete();

        return redirect()->route('tenant.inventory.products.index', ['tenant' => $tenant->slug])
            ->with('success', 'Product deleted successfully.');

    } catch (\Exception $e) {
        Log::error('Error deleting product: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'An error occurred while deleting the product. Please try again.');
    }
}


//import function

public function import(Request $request, Tenant $tenant){

return view('tenant.inventory.products.import', compact('tenant'));
}

public function importProcess(Request $request, Tenant $tenant)
{

    $file = $request->file('import_file');

    if (!$file) {
        return redirect()->back()->with('error', 'Please upload a valid CSV file.');
    }

    $imported = 0;
    $skipped = 0;
    $errors = [];

    DB::beginTransaction();
    try {
        $data = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_map('trim', array_shift($data));

        foreach ($data as $index => $row) {
            $row = array_map('trim', $row);
            $productData = array_combine($header, $row);
            if (!$productData) {
                $errors[] = "Row ".($index+2).": Invalid format.";
                $skipped++;
                continue;
            }

            // Set tenant and user context
            $productData['tenant_id'] = $tenant->id;
            $productData['created_by'] = auth()->id();

            // Check for duplicate by SKU (if present)
            if (!empty($productData['sku'])) {
                $exists = \App\Models\Product::where('tenant_id', $tenant->id)
                    ->where('sku', $productData['sku'])
                    ->exists();
                if ($exists) {
                    $errors[] = "Row ".($index+2).": Duplicate SKU (".$productData['sku']."), skipped.";
                    $skipped++;
                    continue;
                }
            }

            // Type casting and defaults
            $productData['type'] = $productData['type'] ?? 'item';
            $productData['purchase_rate'] = isset($productData['purchase_rate']) ? (float)$productData['purchase_rate'] : 0;
            $productData['sales_rate'] = isset($productData['sales_rate']) ? (float)$productData['sales_rate'] : 0;
            $productData['mrp'] = isset($productData['mrp']) ? (float)$productData['mrp'] : null;
            $productData['opening_stock'] = isset($productData['opening_stock']) ? (float)$productData['opening_stock'] : 0;
            $productData['current_stock'] = isset($productData['current_stock']) ? (float)$productData['current_stock'] : $productData['opening_stock'];
            $productData['reorder_level'] = isset($productData['reorder_level']) ? (float)$productData['reorder_level'] : null;
            $productData['unit_conversion_factor'] = isset($productData['unit_conversion_factor']) ? (float)$productData['unit_conversion_factor'] : 1.0;
            $productData['tax_rate'] = isset($productData['tax_rate']) ? (float)$productData['tax_rate'] : 0;
            $productData['maintain_stock'] = isset($productData['maintain_stock']) ? (bool)$productData['maintain_stock'] : true;
            $productData['is_active'] = isset($productData['is_active']) ? (bool)$productData['is_active'] : true;
            $productData['is_saleable'] = isset($productData['is_saleable']) ? (bool)$productData['is_saleable'] : true;
            $productData['is_purchasable'] = isset($productData['is_purchasable']) ? (bool)$productData['is_purchasable'] : true;
            $productData['tax_inclusive'] = isset($productData['tax_inclusive']) ? (bool)$productData['tax_inclusive'] : false;

            // Calculate stock values
            $productData['opening_stock_value'] = $productData['opening_stock'] * $productData['purchase_rate'];
            $productData['current_stock_value'] = $productData['current_stock'] * $productData['purchase_rate'];

            // Validate required fields
            $validator = Validator::make($productData, [
                'type' => 'required|in:item,service',
                'name' => 'required|string|max:255',
                'sku' => 'nullable|string|max:100',
                'category_id' => 'nullable|exists:product_categories,id',
                'brand' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'purchase_rate' => 'required|numeric|min:0',
                'sales_rate' => 'required|numeric|min:0',
                'mrp' => 'nullable|numeric|min:0',
                'primary_unit_id' => 'required|exists:units,id',
                'unit_conversion_factor' => 'numeric|min:0.000001',
                'barcode' => 'nullable|string|max:255',
                'hsn_code' => 'nullable|string|max:50',
                'tax_rate' => 'nullable|numeric|min:0|max:100',
                'opening_stock' => 'nullable|numeric|min:0',
                'current_stock' => 'nullable|numeric|min:0',
                'reorder_level' => 'nullable|numeric|min:0',
                'maintain_stock' => 'boolean',
                'is_active' => 'boolean',
                'is_saleable' => 'boolean',
                'is_purchasable' => 'boolean',
                'tax_inclusive' => 'boolean',
            ]);

            if ($validator->fails()) {
                $errors[] = "Row ".($index+2).": ".implode('; ', $validator->errors()->all());
                $skipped++;
                continue;
            }

            try {
                \App\Models\Product::create($productData);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row ".($index+2).": ".$e->getMessage();
                $skipped++;
            }
        }

        DB::commit();

        $message = "$imported products imported. $skipped skipped.";
        if (count($errors)) {
            $message .= ' Some errors occurred.';
        }

        return redirect()->route('tenant.inventory.products.index', ['tenant' => $tenant->slug])
            ->with('success', $message)
            ->with('import_errors', $errors);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error importing products: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'An error occurred while importing products. Please try again.');
    }
}

    /**
     * Download products import template
     */
    public function downloadTemplate(Tenant $tenant)
    {
        try {
            return Excel::download(
                new ProductsTemplateExport(),
                'products_import_template.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Error downloading products template: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to download template. Please try again.');
        }
    }

    /**
     * Download product categories reference
     */
    public function downloadCategoriesReference(Tenant $tenant)
    {
        try {
            return Excel::download(
                new ProductCategoriesReferenceExport($tenant),
                'product_categories_reference.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Error downloading categories reference: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to download categories reference. Please try again.');
        }
    }

    /**
     * Import products from Excel file
     */
    public function importProducts(Request $request, Tenant $tenant)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('import_file');
            $import = new ProductsImport($tenant);

            Excel::import($import, $file);

            $message = "{$import->getImported()} products imported successfully.";

            if ($import->getSkipped() > 0) {
                $message .= " {$import->getSkipped()} rows skipped.";
            }

            $response = redirect()
                ->route('tenant.inventory.products.index', ['tenant' => $tenant->slug])
                ->with('success', $message);

            if (count($import->getErrors()) > 0) {
                $response->with('import_errors', $import->getErrors());
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Error importing products: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to import products: ' . $e->getMessage())
                ->withInput();
        }
    }
}
