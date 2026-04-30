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
                // Normalize the row so legacy/template header variants still resolve.
                // e.g. "Type* (item/service)" → "type_item_service" — map back to "type".
                $data = $this->normalizeRow($row->toArray());

                // Skip completely blank rows silently
                if ($this->isRowBlank($data)) {
                    continue;
                }

                // Validate required fields
                $validator = Validator::make($data, [
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

                // Use $data going forward (already normalized + sanitized)
                $row = $data;

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

    /**
     * Normalize the heading-row keys produced by Maatwebsite Excel.
     *
     * Templates may have headers like "Type* (item/service)" which get slugged to
     * "type_item_service". This maps known variants back to the canonical keys
     * the importer expects, and sanitizes numeric values (strips currency symbols,
     * commas and whitespace) so values like "₦1,500.00" pass `numeric` validation.
     */
    protected function normalizeRow(array $row): array
    {
        $aliasMap = [
            'product_name'             => ['product_name', 'product_name_required', 'name'],
            'type'                     => ['type', 'type_item_service', 'type_itemservice', 'product_type'],
            'sku'                      => ['sku', 'sku_code'],
            'description'              => ['description', 'desc'],
            'category'                 => ['category', 'category_name'],
            'brand'                    => ['brand'],
            'hsn_code'                 => ['hsn_code', 'hsn'],
            'purchase_rate'            => ['purchase_rate', 'purchase_price', 'cost_price', 'cost'],
            'sales_rate'               => ['sales_rate', 'sales_price', 'selling_price', 'price'],
            'mrp'                      => ['mrp'],
            'primary_unit'             => ['primary_unit', 'unit', 'unit_name'],
            'unit_conversion_factor'   => ['unit_conversion_factor', 'conversion_factor'],
            'opening_stock'            => ['opening_stock'],
            'opening_stock_date'       => ['opening_stock_date'],
            'reorder_level'            => ['reorder_level', 'minimum_stock', 'minimum_stock_level'],
            'stock_asset_account'      => ['stock_asset_account', 'stock_account'],
            'sales_account'            => ['sales_account', 'sales_income_account', 'income_account'],
            'purchase_account'         => ['purchase_account', 'purchase_expense_account', 'expense_account'],
            'tax_rate'                 => ['tax_rate', 'tax_rate_percent', 'tax', 'tax_percentage'],
            'tax_inclusive'            => ['tax_inclusive', 'tax_inclusive_yesno', 'tax_inclusive_yes_no'],
            'barcode'                  => ['barcode'],
            'maintain_stock'           => ['maintain_stock', 'maintain_stock_yesno', 'maintain_stock_yes_no', 'track_stock'],
            'is_active'                => ['is_active', 'is_active_yesno', 'is_active_yes_no', 'active'],
            'is_saleable'              => ['is_saleable', 'is_saleable_yesno', 'is_saleable_yes_no', 'saleable'],
            'is_purchasable'           => ['is_purchasable', 'is_purchasable_yesno', 'is_purchasable_yes_no', 'purchasable'],
        ];

        // Lower-case and trim every key once so lookup is consistent.
        $rowLower = [];
        foreach ($row as $k => $v) {
            $rowLower[strtolower(trim((string) $k))] = $v;
        }

        $normalized = [];
        foreach ($aliasMap as $canonical => $aliases) {
            $normalized[$canonical] = null;
            foreach ($aliases as $alias) {
                if (array_key_exists($alias, $rowLower) && $rowLower[$alias] !== null && $rowLower[$alias] !== '') {
                    $normalized[$canonical] = $rowLower[$alias];
                    break;
                }
            }
        }

        // Sanitize numeric fields: strip currency symbols, thousand separators and spaces.
        $numericFields = [
            'purchase_rate', 'sales_rate', 'mrp',
            'unit_conversion_factor', 'opening_stock', 'reorder_level', 'tax_rate',
        ];
        foreach ($numericFields as $field) {
            if ($normalized[$field] !== null && $normalized[$field] !== '') {
                $normalized[$field] = $this->sanitizeNumeric($normalized[$field]);
            }
        }

        return $normalized;
    }

    /**
     * Strip currency symbols, commas and whitespace from a numeric-looking value.
     */
    protected function sanitizeNumeric($value)
    {
        if (is_numeric($value)) {
            return $value;
        }

        $clean = preg_replace('/[^0-9.\-]/', '', (string) $value);
        return $clean === '' ? null : $clean;
    }

    /**
     * Return true if every value in the row is null/empty — i.e. a blank row.
     */
    protected function isRowBlank(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }
        return true;
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
