<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\LedgerAccount;
use App\Models\Tenant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductsImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    protected $tenant;
    protected $errors = [];
    protected $imported = 0;
    protected $skipped = 0;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-based index

            try {
                // Validate required fields
                $validator = Validator::make($row->toArray(), [
                    'product_name' => 'required|string|max:255',
                    'type' => 'required|in:item,service,Item,Service,ITEM,SERVICE',
                    'purchase_rate' => 'required|numeric|min:0',
                    'sales_rate' => 'required|numeric|min:0',
                    'primary_unit' => 'required|string',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    $this->skipped++;
                    continue;
                }

                // Normalize type
                $type = strtolower(trim($row['type']));

                // Check if SKU already exists for this tenant
                if (!empty($row['sku'])) {
                    $existingProduct = Product::where('tenant_id', $this->tenant->id)
                        ->where('sku', trim($row['sku']))
                        ->first();

                    if ($existingProduct) {
                        $this->errors[] = "Row {$rowNumber}: SKU '{$row['sku']}' already exists.";
                        $this->skipped++;
                        continue;
                    }
                }

                // Find or validate category
                $categoryId = null;
                if (!empty($row['category'])) {
                    $category = ProductCategory::where('tenant_id', $this->tenant->id)
                        ->where(function ($query) use ($row) {
                            $query->where('name', trim($row['category']))
                                  ->orWhere('id', trim($row['category']));
                        })
                        ->first();

                    if (!$category) {
                        $this->errors[] = "Row {$rowNumber}: Category '{$row['category']}' not found.";
                        $this->skipped++;
                        continue;
                    }
                    $categoryId = $category->id;
                }

                // Find primary unit
                $unit = Unit::where('tenant_id', $this->tenant->id)
                    ->where(function ($query) use ($row) {
                        $query->where('name', trim($row['primary_unit']))
                              ->orWhere('short_name', trim($row['primary_unit']))
                              ->orWhere('id', trim($row['primary_unit']));
                    })
                    ->first();

                if (!$unit) {
                    $this->errors[] = "Row {$rowNumber}: Unit '{$row['primary_unit']}' not found.";
                    $this->skipped++;
                    continue;
                }

                // Find ledger accounts if provided
                $stockAssetAccountId = $this->findLedgerAccount($row['stock_asset_account'] ?? null);
                $salesAccountId = $this->findLedgerAccount($row['sales_account'] ?? null);
                $purchaseAccountId = $this->findLedgerAccount($row['purchase_account'] ?? null);

                // Prepare product data
                $productData = [
                    'tenant_id' => $this->tenant->id,
                    'type' => $type,
                    'name' => trim($row['product_name']),
                    'sku' => !empty($row['sku']) ? trim($row['sku']) : $this->generateSKU(trim($row['product_name'])),
                    'description' => !empty($row['description']) ? trim($row['description']) : null,
                    'category_id' => $categoryId,
                    'brand' => !empty($row['brand']) ? trim($row['brand']) : null,
                    'hsn_code' => !empty($row['hsn_code']) ? trim($row['hsn_code']) : null,
                    'purchase_rate' => floatval($row['purchase_rate']),
                    'sales_rate' => floatval($row['sales_rate']),
                    'mrp' => !empty($row['mrp']) ? floatval($row['mrp']) : floatval($row['sales_rate']),
                    'primary_unit_id' => $unit->id,
                    'unit_conversion_factor' => !empty($row['unit_conversion_factor']) ? floatval($row['unit_conversion_factor']) : 1,
                    'opening_stock' => 0, // Set to 0, will be handled by stock movements
                    'current_stock' => 0, // Set to 0, will be calculated from stock movements
                    'reorder_level' => !empty($row['reorder_level']) ? floatval($row['reorder_level']) : null,
                    'stock_asset_account_id' => $stockAssetAccountId,
                    'sales_account_id' => $salesAccountId,
                    'purchase_account_id' => $purchaseAccountId,
                    'opening_stock_value' => 0,
                    'current_stock_value' => 0,
                    'tax_rate' => !empty($row['tax_rate']) ? floatval($row['tax_rate']) : 0,
                    'tax_inclusive' => $this->parseBoolean($row['tax_inclusive'] ?? 'no'),
                    'barcode' => !empty($row['barcode']) ? trim($row['barcode']) : null,
                    'maintain_stock' => $type === 'item' ? $this->parseBoolean($row['maintain_stock'] ?? 'yes') : false,
                    'is_active' => $this->parseBoolean($row['is_active'] ?? 'yes'),
                    'is_saleable' => $this->parseBoolean($row['is_saleable'] ?? 'yes'),
                    'is_purchasable' => $this->parseBoolean($row['is_purchasable'] ?? 'yes'),
                    'created_by' => auth()->id(),
                ];

                // Create product
                $product = Product::create($productData);

                // Create opening stock movement if provided
                if (!empty($row['opening_stock']) && floatval($row['opening_stock']) > 0 && $type === 'item' && $productData['maintain_stock']) {
                    $openingStock = floatval($row['opening_stock']);
                    $openingStockDate = !empty($row['opening_stock_date'])
                        ? date('Y-m-d', strtotime($row['opening_stock_date']))
                        : now()->subDay()->toDateString();

                    \App\Models\StockMovement::create([
                        'tenant_id' => $this->tenant->id,
                        'product_id' => $product->id,
                        'type' => 'in',
                        'quantity' => $openingStock,
                        'old_stock' => 0,
                        'new_stock' => $openingStock,
                        'rate' => floatval($row['purchase_rate']),
                        'transaction_type' => 'opening_stock',
                        'transaction_date' => $openingStockDate,
                        'transaction_reference' => 'OPENING-' . $product->id,
                        'reference' => 'Opening Stock for ' . $product->name,
                        'remarks' => 'Initial opening stock entry via import',
                        'created_by' => auth()->id(),
                    ]);

                    // Update opening stock value
                    $product->update([
                        'opening_stock_value' => $openingStock * floatval($row['purchase_rate']),
                    ]);
                }

                $this->imported++;

            } catch (\Exception $e) {
                Log::error("Product import error on row {$rowNumber}: " . $e->getMessage());
                $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                $this->skipped++;
            }
        }
    }

    protected function findLedgerAccount($accountName)
    {
        if (empty($accountName)) {
            return null;
        }

        $account = LedgerAccount::where('tenant_id', $this->tenant->id)
            ->where(function ($query) use ($accountName) {
                $query->where('name', trim($accountName))
                      ->orWhere('account_code', trim($accountName))
                      ->orWhere('id', trim($accountName));
            })
            ->first();

        return $account ? $account->id : null;
    }

    protected function parseBoolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['yes', 'true', '1', 'active', 'y']);
    }

    protected function generateSKU($name)
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3));
        $prefix = str_pad($prefix, 3, 'X');
        $random = mt_rand(100, 999);
        return $prefix . '-' . $random;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getImported()
    {
        return $this->imported;
    }

    public function getSkipped()
    {
        return $this->skipped;
    }
}
