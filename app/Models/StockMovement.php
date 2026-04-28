<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'stock_location_id',
        'from_stock_location_id',
        'to_stock_location_id',
        'type',
        'quantity',
        'old_stock',
        'new_stock',
        'rate',
        'reference',
        'remarks',
        'created_by',
        'transaction_type',
        'transaction_date',
        'transaction_reference',
        'source_transaction_type',
        'source_transaction_id',
        'batch_number',
        'expiry_date',
        'additional_data',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'old_stock' => 'decimal:4',
        'new_stock' => 'decimal:4',
        'rate' => 'decimal:2',
        'transaction_date' => 'date',
        'expiry_date' => 'date',
        'additional_data' => 'json',
    ];

    protected $dates = [
        'transaction_date',
        'expiry_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the tenant that owns the stock movement.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the product that owns the stock movement.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockLocation(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }

    public function fromStockLocation(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'from_stock_location_id');
    }

    public function toStockLocation(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'to_stock_location_id');
    }

    /**
     * Resolve the default stock location id for a tenant when the
     * stock_locations module is enabled. Returns null otherwise.
     */
    protected static function defaultLocationIdForTenant($tenantId): ?int
    {
        $tenant = \App\Models\Tenant::find($tenantId);
        if (!$tenant) {
            return null;
        }

        if (!\App\Services\ModuleRegistry::isModuleEnabled($tenant, 'stock_locations')) {
            return null;
        }

        return optional(StockLocation::getMainForTenant($tenantId))->id;
    }

    /**
     * Get the user who created the stock movement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the source transaction (polymorphic relationship).
     */
    public function sourceTransaction()
    {
        return $this->morphTo();
    }

    /**
     * Create stock movement from invoice item.
     */
    public static function createFromInvoice($invoice, $item, $movementType = 'out')
    {
        $invoiceNumber = $invoice->invoice_number ?? $invoice->id;
        $locationId = self::defaultLocationIdForTenant($invoice->tenant_id);

        return self::create([
            'tenant_id' => $invoice->tenant_id,
            'product_id' => $item->product_id,
            'stock_location_id' => $locationId,
            'type' => $movementType,
            'quantity' => $movementType === 'out' ? -abs($item->quantity) : abs($item->quantity),
            'rate' => $item->rate ?? 0,
            'transaction_type' => 'sales',
            'transaction_date' => $invoice->invoice_date ?? now()->toDateString(),
            'transaction_reference' => $invoiceNumber,
            'reference' => "Sales Invoice #{$invoiceNumber}",
            'source_transaction_type' => get_class($invoice),
            'source_transaction_id' => $invoice->id,
            'created_by' => auth()->id(),
            'old_stock' => 0, // Will be calculated
            'new_stock' => 0, // Will be calculated
        ]);
    }

    /**
     * Create stock movement from voucher item.
     */
    public static function createFromVoucher($voucher, $item, $movementType = 'out')
    {
        $voucherNumber = $voucher->voucher_number ?? $voucher->id;
        $voucherType = $voucher->voucherType;

        // Determine transaction type based on voucher type
        $transactionType = 'sales'; // default
        if ($voucherType) {
            if (stripos($voucherType->name, 'purchase') !== false || stripos($voucherType->code, 'PUR') !== false) {
                $transactionType = 'purchase';
            } elseif (stripos($voucherType->name, 'sales') !== false || stripos($voucherType->code, 'SALES') !== false) {
                $transactionType = 'sales';
            }
        }

        // Calculate old stock before this movement
        $product = \App\Models\Product::find($item['product_id']);
        $oldStock = $product ? $product->getStockAsOfDate(now(), true) : 0;

        $quantity = $movementType === 'out' ? -abs($item['quantity']) : abs($item['quantity']);
        $newStock = $oldStock + $quantity;
        $locationId = self::defaultLocationIdForTenant($voucher->tenant_id);

        return self::create([
            'tenant_id' => $voucher->tenant_id,
            'product_id' => $item['product_id'],
            'stock_location_id' => $locationId,
            'type' => $movementType,
            'quantity' => $quantity,
            'rate' => $item['rate'] ?? 0,
            'transaction_type' => $transactionType,
            'transaction_date' => $voucher->voucher_date ?? now()->toDateString(),
            'transaction_reference' => $voucherNumber,
            'reference' => "{$voucherType->name} #{$voucherNumber}",
            'source_transaction_type' => get_class($voucher),
            'source_transaction_id' => $voucher->id,
            'created_by' => auth()->id(),
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
        ]);
    }

    /**
     * Create stock movement from stock journal item.
     */
    public static function createFromStockJournal($stockJournal, $item)
    {
        $movementType = $item->movement_type === 'in' ? 'in' : 'out';
        $quantity = $movementType === 'out' ? -abs($item->quantity) : abs($item->quantity);
        $journalNumber = $stockJournal->journal_number ?? $stockJournal->id;
        $entryType = $stockJournal->entry_type ?? null;
        $isProduction = $entryType === 'production';
        $isTransfer = $entryType === 'transfer';

        // Resolve location for this movement
        $itemLocationId = $item->stock_location_id ?? null;
        $fromLocationId = $stockJournal->from_stock_location_id ?? null;
        $toLocationId = $stockJournal->to_stock_location_id ?? null;

        if (!$itemLocationId) {
            $itemLocationId = $movementType === 'out' ? $fromLocationId : $toLocationId;
        }

        // Backfill default Store when module is enabled but nothing was provided
        if (!$itemLocationId) {
            $itemLocationId = self::defaultLocationIdForTenant($stockJournal->tenant_id);
        }

        $transactionType = $isProduction
            ? 'manufacturing'
            : ($isTransfer ? ($movementType === 'out' ? 'transfer_out' : 'transfer_in') : 'stock_journal');

        // Calculate old stock before this movement using date-based calculation
        $product = \App\Models\Product::find($item->product_id);
        $oldStock = $product ? $product->getStockAsOfDate($stockJournal->journal_date ?? now()) : 0;
        $newStock = $oldStock + $quantity;

        return self::create([
            'tenant_id' => $stockJournal->tenant_id,
            'product_id' => $item->product_id,
            'stock_location_id' => $itemLocationId,
            'from_stock_location_id' => $isTransfer || $isProduction ? $fromLocationId : null,
            'to_stock_location_id' => $isTransfer || $isProduction ? $toLocationId : null,
            'type' => $movementType,
            'quantity' => $quantity,
            'rate' => $item->rate ?? 0,
            'transaction_type' => $transactionType,
            'transaction_date' => $stockJournal->journal_date ?? now()->toDateString(),
            'transaction_reference' => $journalNumber,
            'reference' => $item->remarks ?? ($isProduction ? "Production Report #{$journalNumber}" : ($isTransfer ? "Stock Transfer #{$journalNumber}" : "Stock Journal #{$journalNumber}")),
            'source_transaction_type' => get_class($stockJournal),
            'source_transaction_id' => $stockJournal->id,
            'batch_number' => $item->batch_number,
            'expiry_date' => $item->expiry_date,
            'created_by' => $stockJournal->created_by ?? auth()->id(),
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'remarks' => $item->remarks,
            'additional_data' => $isProduction ? [
                'entry_type' => 'production',
                'movement_role' => $movementType === 'in' ? 'finished_good_output' : 'material_consumption',
                'operator_id' => $stockJournal->operator_id,
                'assistant_operator_id' => $stockJournal->assistant_operator_id,
                'production_batch_number' => $stockJournal->production_batch_number,
                'work_order_number' => $stockJournal->work_order_number,
                'production_shift' => $stockJournal->production_shift,
                'machine_name' => $stockJournal->machine_name,
                'production_started_at' => $stockJournal->production_started_at,
                'production_ended_at' => $stockJournal->production_ended_at,
                'unit_snapshot' => $item->unit_snapshot,
                'rejected_quantity' => (float) ($item->rejected_quantity ?? 0),
                'waste_quantity' => (float) ($item->waste_quantity ?? 0),
            ] : null,
        ]);
    }

    /**
     * Create stock movement from purchase.
     */
    public static function createFromPurchase($purchase, $item)
    {
        $purchaseNumber = $purchase->purchase_number ?? $purchase->id;
        $locationId = self::defaultLocationIdForTenant($purchase->tenant_id);

        return self::create([
            'tenant_id' => $purchase->tenant_id,
            'product_id' => $item->product_id,
            'stock_location_id' => $locationId,
            'type' => 'in',
            'quantity' => abs($item->quantity),
            'rate' => $item->rate ?? 0,
            'transaction_type' => 'purchase',
            'transaction_date' => $purchase->purchase_date ?? now()->toDateString(),
            'transaction_reference' => $purchaseNumber,
            'reference' => "Purchase #{$purchaseNumber}",
            'source_transaction_type' => get_class($purchase),
            'source_transaction_id' => $purchase->id,
            'created_by' => $purchase->created_by ?? auth()->id(),
            'old_stock' => 0, // Will be calculated
            'new_stock' => 0, // Will be calculated
        ]);
    }

    /**
     * Create stock movement from physical stock adjustment.
     */
    public static function createFromPhysicalAdjustment($physicalStockEntry)
    {
        $voucher = $physicalStockEntry->voucher;

        return self::create([
            'tenant_id' => $voucher->tenant_id,
            'product_id' => $physicalStockEntry->product_id,
            'type' => $physicalStockEntry->difference_quantity > 0 ? 'in' : 'out',
            'quantity' => $physicalStockEntry->difference_quantity,
            'rate' => $physicalStockEntry->current_rate,
            'transaction_type' => 'physical_adjustment',
            'transaction_date' => $voucher->voucher_date,
            'transaction_reference' => $voucher->voucher_number,
            'reference' => "Physical Stock Adjustment - {$voucher->voucher_number}",
            'source_transaction_type' => 'App\Models\PhysicalStockVoucher',
            'source_transaction_id' => $voucher->id,
            'batch_number' => $physicalStockEntry->batch_number,
            'expiry_date' => $physicalStockEntry->expiry_date,
            'created_by' => $voucher->created_by,
            'old_stock' => $physicalStockEntry->book_quantity,
            'new_stock' => $physicalStockEntry->physical_quantity,
            'remarks' => $physicalStockEntry->remarks ?? "Physical adjustment: {$physicalStockEntry->getDifferenceTypeDisplay()}",
            'additional_data' => [
                'voucher_id' => $voucher->id,
                'entry_id' => $physicalStockEntry->id,
                'difference_type' => $physicalStockEntry->getDifferenceType(),
                'location' => $physicalStockEntry->location,
                'adjustment_value' => $physicalStockEntry->difference_value,
            ],
        ]);
    }

    /**
     * Get the movement type display name.
     */
    public function getTypeDisplayAttribute(): string
    {
        return match($this->transaction_type ?? $this->type) {
            'opening_stock' => 'Opening Stock',
            'purchase' => 'Purchase',
            'sales', 'sale' => 'Sales',
            'stock_journal' => 'Stock Journal',
            'physical_adjustment', 'adjustment' => 'Stock Adjustment',
            'purchase_return', 'return' => 'Return',
            'transfer_in' => 'Transfer In',
            'transfer_out' => 'Transfer Out',
            'damage' => 'Damage/Loss',
            'manufacturing' => 'Manufacturing',
            'invoice' => 'Invoice',
            default => ucfirst(str_replace('_', ' ', $this->transaction_type ?? $this->type))
        };
    }

    /**
     * Check if movement increases stock.
     */
    public function getIsIncreaseAttribute(): bool
    {
        return $this->quantity > 0;
    }

    /**
     * Check if movement decreases stock.
     */
    public function getIsDecreaseAttribute(): bool
    {
        return $this->quantity < 0;
    }

    /**
     * Get the absolute quantity.
     */
    public function getAbsoluteQuantityAttribute(): float
    {
        return abs($this->quantity);
    }

    /**
     * Get the movement direction (in/out).
     */
    public function getDirectionAttribute(): string
    {
        return $this->quantity > 0 ? 'in' : 'out';
    }

    /**
     * Scope for date range filtering.
     */
    public function scopeDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('transaction_date', [$fromDate, $toDate]);
    }

    /**
     * Scope for transaction type filtering.
     */
    public function scopeTransactionType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope for movement direction filtering.
     */
    public function scopeDirection($query, $direction)
    {
        if ($direction === 'in') {
            return $query->where('quantity', '>', 0);
        } elseif ($direction === 'out') {
            return $query->where('quantity', '<', 0);
        }
        return $query;
    }

    /**
     * Scope for specific product.
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope for movements up to a specific date.
     */
    public function scopeUpToDate($query, $date)
    {
        return $query->where('transaction_date', '<=', $date);
    }
}
