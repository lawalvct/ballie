<?php

namespace App\Http\Controllers\Api\Tenant\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\Unit;
use App\Models\LedgerAccount;
use App\Models\Tenant;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Get form data for creating a product
     * Returns categories, units, and ledger accounts
     *
     * @param Tenant $tenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Tenant $tenant)
    {
        try {
            $categories = ProductCategory::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'description']);

            $units = Unit::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'symbol']);

            $ledgerAccounts = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'account_type']);

            // Get default accounts
            $defaultStockAccount = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('name', 'like', '%Stock%')
                ->where('is_active', true)
                ->first(['id', 'name']);

            $defaultSalesAccount = LedgerAccount::where('tenant_id', $tenant->id)
                 ->where('name', 'Sales Revenue')
                ->where('is_active', true)
                ->first(['id', 'name']);

            $defaultPurchaseAccount = LedgerAccount::where('tenant_id', $tenant->id)
                  ->where('name', 'Cost of Goods Sold')
                ->where('is_active', true)
                ->first(['id', 'name']);

            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => $categories,
                    'units' => $units,
                    'ledger_accounts' => $ledgerAccounts,
                    'default_accounts' => [
                        'stock' => $defaultStockAccount,
                        'sales' => $defaultSalesAccount,
                        'purchase' => $defaultPurchaseAccount,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Product create API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load form data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new product
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Tenant $tenant)
    {
        try {
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
                'primary_unit_id' => 'required|exists:units,id',
                'opening_stock' => 'nullable|numeric|min:0',
                'reorder_level' => 'nullable|numeric|min:0',
                'maintain_stock' => 'boolean',
                'stock_asset_account_id' => 'nullable|exists:ledger_accounts,id',
                'sales_account_id' => 'nullable|exists:ledger_accounts,id',
                'purchase_account_id' => 'nullable|exists:ledger_accounts,id',
                'tax_rate' => 'nullable|numeric|min:0|max:100',
                'barcode' => 'nullable|string|max:100',
                'is_active' => 'boolean',
                'is_visible_online' => 'boolean',
                'is_featured' => 'boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'slug' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'gallery_images' => 'nullable|array',
                'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ];

            $validated = $request->validate($rules);

            // Generate slug if not provided
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Generate SKU if not provided
            if (empty($validated['sku'])) {
                $validated['sku'] = $this->generateSKU($validated['name'], $validated['category_id'] ?? null);
            }

            $product = DB::transaction(function () use ($request, $tenant, $validated) {
                $productData = array_merge($validated, [
                    'tenant_id' => $tenant->id,
                    'created_by' => Auth::id(),
                ]);

                // Handle primary image upload - save to products.image_path
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('products', $imageName, 'public');
                    $productData['image_path'] = $imagePath;
                }

                $product = Product::create($productData);

                // Handle gallery images upload - save to product_images table
                if ($request->hasFile('gallery_images')) {
                    $sortOrder = 1;
                    foreach ($request->file('gallery_images') as $galleryImage) {
                        $imageName = time() . '_' . uniqid() . '.' . $galleryImage->getClientOriginalExtension();
                        $imagePath = $galleryImage->storeAs('products/gallery', $imageName, 'public');

                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $imagePath,
                            'is_primary' => false,
                            'sort_order' => $sortOrder++,
                        ]);
                    }
                }

                // Create opening stock movement if provided
                if (isset($validated['opening_stock']) && $validated['opening_stock'] > 0) {
                    StockMovement::create([
                        'tenant_id' => $tenant->id,
                        'product_id' => $product->id,
                        'transaction_type' => 'opening_balance',
                        'transaction_date' => now(),
                        'quantity' => $validated['opening_stock'],
                        'rate' => $validated['purchase_rate'] ?? 0,
                        'amount' => $validated['opening_stock'] * ($validated['purchase_rate'] ?? 0),
                        'balance_quantity' => $validated['opening_stock'],
                        'description' => 'Opening stock',
                        'created_by' => Auth::id(),
                    ]);
                }

                return $product->fresh([
                    'category',
                    'primaryUnit',
                    'stockAssetAccount',
                    'salesAccount',
                    'purchaseAccount',
                    'images'
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $this->formatProductResponse($product),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Product store API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List products with filters and pagination
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, Tenant $tenant)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $asOfDate = $request->input('as_of_date', now()->toDateString());
            $valuationMethod = $request->input('valuation_method', 'weighted_average');

            $query = Product::where('tenant_id', $tenant->id)
                ->with(['category', 'primaryUnit']);

            // Search filter
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Category filter
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->input('category_id'));
            }

            // Type filter (item or service)
            if ($request->filled('type')) {
                $query->where('type', $request->input('type'));
            }

            // Status filter
            if ($request->filled('status')) {
                $isActive = $request->input('status') === 'active';
                $query->where('is_active', $isActive);
            }

            // Stock status filter
            if ($request->filled('stock_status')) {
                $stockStatus = $request->input('stock_status');
                if ($stockStatus === 'low_stock') {
                    $query->lowStock();
                } elseif ($stockStatus === 'out_of_stock') {
                    $query->outOfStock();
                } elseif ($stockStatus === 'in_stock') {
                    $query->inStock();
                }
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $products = $query->paginate($perPage);

            // Calculate date-based stock for each product
            $products->getCollection()->transform(function ($product) use ($asOfDate, $valuationMethod) {
                $product->calculated_stock = $product->getStockAsOfDate($asOfDate);
                $product->calculated_stock_value = $product->getStockValueAsOfDate($asOfDate, $valuationMethod);
                return $product;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $products->map(fn($product) => $this->formatProductResponse($product)),
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                        'last_page' => $products->lastPage(),
                        'from' => $products->firstItem(),
                        'to' => $products->lastItem(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Product index API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get product details
     *
     * @param Tenant $tenant
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Tenant $tenant, Product $product)
    {
        try {
            // Ensure the product belongs to the tenant
            if ($product->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            $product->load([
                'category',
                'primaryUnit',
                'stockAssetAccount',
                'salesAccount',
                'purchaseAccount',
                'images'
            ]);

            // Calculate real-time stock
            $asOfDate = now()->toDateString();
            $product->calculated_stock = $product->getStockAsOfDate($asOfDate);
            $product->calculated_stock_value = $product->getStockValueAsOfDate($asOfDate);

            return response()->json([
                'success' => true,
                'data' => $this->formatProductResponse($product, true),
            ]);
        } catch (\Exception $e) {
            Log::error('Product show API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update product
     *
     * @param Request $request
     * @param Tenant $tenant
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Tenant $tenant, Product $product)
    {
        try {
            // Ensure the product belongs to the tenant
            if ($product->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            $rules = [
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
                'reorder_level' => 'nullable|numeric|min:0',
                'maintain_stock' => 'boolean',
                'stock_asset_account_id' => 'nullable|exists:ledger_accounts,id',
                'sales_account_id' => 'nullable|exists:ledger_accounts,id',
                'purchase_account_id' => 'nullable|exists:ledger_accounts,id',
                'tax_rate' => 'nullable|numeric|min:0|max:100',
                'barcode' => 'nullable|string|max:100',
                'is_active' => 'boolean',
                'is_visible_online' => 'boolean',
                'is_featured' => 'boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'slug' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'gallery_images' => 'nullable|array',
                'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'remove_image_ids' => 'nullable|array',
                'remove_image_ids.*' => 'integer|exists:product_images,id',
            ];

            $validated = $request->validate($rules);

            DB::transaction(function () use ($request, $product, $validated) {
                $validated['updated_by'] = Auth::id();

                // Handle primary image upload - save to products.image_path
                if ($request->hasFile('image')) {
                    // Delete old primary image if exists
                    if ($product->image_path) {
                        Storage::disk('public')->delete($product->image_path);
                    }

                    // Upload new primary image
                    $image = $request->file('image');
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('products', $imageName, 'public');
                    $validated['image_path'] = $imagePath;
                }

                $product->update($validated);

                // Handle removing gallery images
                if ($request->has('remove_image_ids') && is_array($request->remove_image_ids)) {
                    $imagesToRemove = ProductImage::where('product_id', $product->id)
                        ->whereIn('id', $request->remove_image_ids)
                        ->get();

                    foreach ($imagesToRemove as $imageToRemove) {
                        // Delete file from storage
                        if (Storage::disk('public')->exists($imageToRemove->image_path)) {
                            Storage::disk('public')->delete($imageToRemove->image_path);
                        }
                        // Delete record
                        $imageToRemove->delete();
                    }
                }

                // Handle gallery images upload - save to product_images table
                if ($request->hasFile('gallery_images')) {
                    $maxSortOrder = ProductImage::where('product_id', $product->id)
                        ->max('sort_order') ?? 0;

                    $sortOrder = $maxSortOrder + 1;

                    foreach ($request->file('gallery_images') as $galleryImage) {
                        $imageName = time() . '_' . uniqid() . '.' . $galleryImage->getClientOriginalExtension();
                        $imagePath = $galleryImage->storeAs('products/gallery', $imageName, 'public');

                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $imagePath,
                            'is_primary' => false,
                            'sort_order' => $sortOrder++,
                        ]);
                    }
                }
            });

            $product->refresh([
                'category',
                'primaryUnit',
                'stockAssetAccount',
                'salesAccount',
                'purchaseAccount',
                'images'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $this->formatProductResponse($product),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Product update API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete product
     *
     * @param Tenant $tenant
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tenant $tenant, Product $product)
    {
        try {
            // Ensure the product belongs to the tenant
            if ($product->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            // Check if product has transactions
            $hasTransactions = $product->stockMovements()->exists();

            if ($hasTransactions) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete product with stock movements. Please deactivate instead.',
                ], 422);
            }

            DB::transaction(function () use ($product) {
                $product->images()->delete();
                $product->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Product destroy API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle product status (activate/deactivate)
     *
     * @param Tenant $tenant
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Tenant $tenant, Product $product)
    {
        try {
            // Ensure the product belongs to the tenant
            if ($product->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            $product->update([
                'is_active' => !$product->is_active,
                'updated_by' => Auth::id(),
            ]);

            $status = $product->is_active ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Product {$status} successfully",
                'data' => $this->formatProductResponse($product),
            ]);
        } catch (\Exception $e) {
            Log::error('Product toggle status API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle product status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get stock movements for a product
     *
     * @param Request $request
     * @param Tenant $tenant
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function stockMovements(Request $request, Tenant $tenant, Product $product)
    {
        try {
            // Ensure the product belongs to the tenant
            if ($product->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            $fromDate = $request->input('from_date', now()->subMonth()->toDateString());
            $toDate = $request->input('to_date', now()->toDateString());
            $transactionType = $request->input('transaction_type');
            $perPage = $request->input('per_page', 50);

            $query = $product->stockMovements()
                ->whereBetween('transaction_date', [$fromDate, $toDate])
                ->with(['creator']);

            if ($transactionType) {
                $query->where('transaction_type', $transactionType);
            }

            $movements = $query->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            // Calculate running stock balance
            $startingStock = $product->getStockAsOfDate($fromDate);

            return response()->json([
                'success' => true,
                'data' => [
                    'movements' => $movements->map(function ($movement) {
                        return [
                            'id' => $movement->id,
                            'transaction_type' => $movement->transaction_type,
                            'transaction_date' => $movement->transaction_date,
                            'quantity' => $movement->quantity,
                            'rate' => $movement->rate,
                            'amount' => $movement->amount,
                            'balance_quantity' => $movement->balance_quantity,
                            'description' => $movement->description,
                            'reference_number' => $movement->reference_number,
                            'created_by' => $movement->creator ? $movement->creator->name : null,
                            'created_at' => $movement->created_at,
                        ];
                    }),
                    'starting_stock' => $startingStock,
                    'pagination' => [
                        'current_page' => $movements->currentPage(),
                        'per_page' => $movements->perPage(),
                        'total' => $movements->total(),
                        'last_page' => $movements->lastPage(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Product stock movements API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load stock movements',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk actions on products
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAction(Request $request, Tenant $tenant)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|in:activate,deactivate,delete',
                'product_ids' => 'required|array|min:1',
                'product_ids.*' => 'required|exists:products,id',
            ]);

            $products = Product::where('tenant_id', $tenant->id)
                ->whereIn('id', $validated['product_ids'])
                ->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No products found',
                ], 404);
            }

            $action = $validated['action'];
            $successCount = 0;
            $failedCount = 0;

            DB::transaction(function () use ($products, $action, &$successCount, &$failedCount) {
                foreach ($products as $product) {
                    try {
                        if ($action === 'activate') {
                            $product->update(['is_active' => true, 'updated_by' => Auth::id()]);
                            $successCount++;
                        } elseif ($action === 'deactivate') {
                            $product->update(['is_active' => false, 'updated_by' => Auth::id()]);
                            $successCount++;
                        } elseif ($action === 'delete') {
                            // Check if product has transactions
                            if ($product->stockMovements()->exists()) {
                                $failedCount++;
                                continue;
                            }
                            $product->images()->delete();
                            $product->delete();
                            $successCount++;
                        }
                    } catch (\Exception $e) {
                        $failedCount++;
                        Log::error('Bulk action failed for product ' . $product->id . ': ' . $e->getMessage());
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => "Bulk action completed. Success: {$successCount}, Failed: {$failedCount}",
                'data' => [
                    'success_count' => $successCount,
                    'failed_count' => $failedCount,
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Product bulk action API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search products (for autocomplete)
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, Tenant $tenant)
    {
        try {
            $search = $request->input('q', '');
            $limit = $request->input('limit', 10);
            $type = $request->input('type'); // item or service
            $activeOnly = $request->input('active_only', true);

            $query = Product::where('tenant_id', $tenant->id)
                ->with(['category', 'primaryUnit']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            }

            if ($type) {
                $query->where('type', $type);
            }

            if ($activeOnly) {
                $query->where('is_active', true);
            }

            $products = $query->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'type' => $product->type,
                        'purchase_rate' => $product->purchase_rate,
                        'sales_rate' => $product->sales_rate,
                        'mrp' => $product->mrp,
                        'current_stock' => $product->current_stock,
                        'primary_unit' => $product->primaryUnit ? [
                            'id' => $product->primaryUnit->id,
                            'name' => $product->primaryUnit->name,
                            'short_name' => $product->primaryUnit->symbol,
                        ] : null,
                        'category' => $product->category ? [
                            'id' => $product->category->id,
                            'name' => $product->category->name,
                        ] : null,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            Log::error('Product search API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to search products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get product statistics
     *
     * @param Tenant $tenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Tenant $tenant)
    {
        try {
            $totalProducts = Product::where('tenant_id', $tenant->id)->count();
            $activeProducts = Product::where('tenant_id', $tenant->id)->where('is_active', true)->count();
            $lowStockProducts = Product::where('tenant_id', $tenant->id)->lowStock()->count();
            $outOfStockProducts = Product::where('tenant_id', $tenant->id)->outOfStock()->count();
            $totalCategories = ProductCategory::where('tenant_id', $tenant->id)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_products' => $totalProducts,
                    'active_products' => $activeProducts,
                    'inactive_products' => $totalProducts - $activeProducts,
                    'low_stock_products' => $lowStockProducts,
                    'out_of_stock_products' => $outOfStockProducts,
                    'total_categories' => $totalCategories,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Product statistics API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format product response
     *
     * @param Product $product
     * @param bool $includeDetails
     * @return array
     */
    private function formatProductResponse(Product $product, bool $includeDetails = false)
    {
        $response = [
            'id' => $product->id,
            'type' => $product->type,
            'name' => $product->name,
            'sku' => $product->sku,
            'slug' => $product->slug,
            'description' => $product->description,
            'brand' => $product->brand,
            'hsn_code' => $product->hsn_code,
            'barcode' => $product->barcode,
            'purchase_rate' => (float) $product->purchase_rate,
            'sales_rate' => (float) $product->sales_rate,
            'mrp' => (float) $product->mrp,
            'tax_rate' => (float) $product->tax_rate,
            'current_stock' => (float) ($product->calculated_stock ?? $product->current_stock),
            'opening_stock' => (float) $product->opening_stock,
            'reorder_level' => (float) $product->reorder_level,
            'maintain_stock' => (bool) $product->maintain_stock,
            'is_active' => (bool) $product->is_active,
            'is_visible_online' => (bool) $product->is_visible_online,
            'is_featured' => (bool) $product->is_featured,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
            ] : null,
            'primary_unit' => $product->primaryUnit ? [
                'id' => $product->primaryUnit->id,
                'name' => $product->primaryUnit->name,
                'short_name' => $product->primaryUnit->symbol,
            ] : null,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ];

        if ($includeDetails) {
            $response = array_merge($response, [
                'meta_title' => $product->meta_title,
                'meta_description' => $product->meta_description,
                'stock_value' => (float) ($product->calculated_stock_value ?? 0),
                'stock_asset_account' => $product->stockAssetAccount ? [
                    'id' => $product->stockAssetAccount->id,
                    'name' => $product->stockAssetAccount->name,
                    'account_code' => $product->stockAssetAccount->code,
                ] : null,
                'sales_account' => $product->salesAccount ? [
                    'id' => $product->salesAccount->id,
                    'name' => $product->salesAccount->name,
                    'account_code' => $product->salesAccount->code,
                ] : null,
                'purchase_account' => $product->purchaseAccount ? [
                    'id' => $product->purchaseAccount->id,
                    'name' => $product->purchaseAccount->name,
                    'account_code' => $product->purchaseAccount->code,
                ] : null,
                'primary_image' => $product->image_path ? Storage::disk('public')->url($product->image_path) : null,
                'gallery_images' => $product->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->image_url,
                        'sort_order' => $image->sort_order,
                    ];
                }),
            ]);
        }

        return $response;
    }

    /**
     * Generate SKU
     *
     * @param string $name
     * @param int|null $categoryId
     * @return string
     */
    private function generateSKU(string $name, ?int $categoryId = null): string
    {
        $namePrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3));
        $namePrefix = str_pad($namePrefix, 3, 'X');

        $categoryPrefix = 'GN';
        if ($categoryId) {
            $category = ProductCategory::find($categoryId);
            if ($category) {
                $categoryPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $category->name), 0, 2));
            }
        }

        $randomSuffix = rand(100, 999);

        return $namePrefix . $categoryPrefix . $randomSuffix;
    }
}
