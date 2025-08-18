<?php

namespace App\Http\Controllers\Tenant\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\LedgerAccount;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request, Tenant $tenant)
    {
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
        $categories = ProductCategory::where('tenant_id', $tenant->id)->active()->get();

        // Calculate statistics
        $totalProducts = Product::where('tenant_id', $tenant->id)->count();
        $activeProducts = Product::where('tenant_id', $tenant->id)->where('is_active', true)->count();
        $lowStockProducts = Product::where('tenant_id', $tenant->id)->lowStock()->count();
        $outOfStockProducts = Product::where('tenant_id', $tenant->id)->outOfStock()->count();

        return view('tenant.inventory.products.index', compact(
            'products',
            'categories',
            'tenant',
            'totalProducts',
            'activeProducts',
            'lowStockProducts',
            'outOfStockProducts'
        ));
    }

    public function create(Tenant $tenant)
    {
        $categories = ProductCategory::where('tenant_id', $tenant->id)->active()->get();
        $units = Unit::where('tenant_id', $tenant->id)->active()->get();
        $ledgerAccounts = LedgerAccount::where('tenant_id', $tenant->id)->active()->get();

        return view('tenant.inventory.products.create', compact('categories', 'units', 'ledgerAccounts', 'tenant'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,NULL,id,tenant_id,' . $tenant->id,
            'type' => 'required|in:item,service',
            'category_id' => 'nullable|exists:product_categories,id',
            'primary_unit_id' => 'nullable|exists:units,id',
            'purchase_rate' => 'required|numeric|min:0',
            'sales_rate' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'opening_stock' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'barcode' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock_asset_account_id' => 'nullable|exists:ledger_accounts,id',
            'sales_account_id' => 'nullable|exists:ledger_accounts,id',
            'purchase_account_id' => 'nullable|exists:ledger_accounts,id',
        ]);

        $data = $request->all();
        $data['tenant_id'] = $tenant->id;
        $data['created_by'] = auth()->id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        // Calculate opening stock value
        if ($request->filled('opening_stock') && $request->filled('purchase_rate')) {
            $data['opening_stock_value'] = $request->opening_stock * $request->purchase_rate;
            $data['current_stock'] = $request->opening_stock;
            $data['current_stock_value'] = $data['opening_stock_value'];
        }

        $product = Product::create($data);

        return redirect()
            ->route('tenant.inventory.products.show', ['tenant' => $tenant->slug, 'product' => $product->id])
            ->with('success', 'Product created successfully.');
    }

   public function show(Tenant $tenant, Product $product)
{
    // Ensure the product belongs to the tenant
    if ($product->tenant_id !== $tenant->id) {
        abort(404);
    }

    $product->load(['category', 'primaryUnit', 'stockAssetAccount', 'salesAccount', 'purchaseAccount']);

    return view('tenant.inventory.products.show', compact('tenant', 'product'));
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
        \Log::error('Error toggling product status: ' . $e->getMessage());

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
        'maintain_stock' => 'nullable|boolean',
        'is_active' => 'nullable|boolean',
        'is_saleable' => 'nullable|boolean',
        'is_purchasable' => 'nullable|boolean',
        'tax_inclusive' => 'nullable|boolean',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    try {
        $data = $request->except(['image']);

        // Handle boolean fields
        $data['maintain_stock'] = $request->has('maintain_stock');
        $data['is_active'] = $request->has('is_active');
        $data['is_saleable'] = $request->has('is_saleable');
        $data['is_purchasable'] = $request->has('is_purchasable');
        $data['tax_inclusive'] = $request->has('tax_inclusive');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path) {
                Storage::delete($product->image_path);
            }

            $imagePath = $request->file('image')->store('products', 'public');
            $data['image_path'] = $imagePath;
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

        return redirect()->route('tenant.inventory.products.show', ['tenant' => $tenant->slug, 'product' => $product->id])
            ->with('success', 'Product updated successfully.');

    } catch (\Exception $e) {
        \Log::error('Error updating product: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'An error occurred while updating the product. Please try again.')
            ->withInput();
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
        \Log::error('Error deleting product: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'An error occurred while deleting the product. Please try again.');
    }
}


}
