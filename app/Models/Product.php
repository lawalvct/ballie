<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Traits\HasAudit;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $fillable = [
        'tenant_id',
        'type',
        'name',
        'sku',
        'slug',
        'description',
        'short_description',
        'long_description',
        'category_id',
        'brand',
        'hsn_code',
        'purchase_rate',
        'sales_rate',
        'selling_price', // Add this for compatibility
        'mrp',
        'primary_unit_id',
        'unit_conversion_factor',
        'opening_stock',
        'opening_stock_date',
        'current_stock',
        'quantity', // Add this for compatibility
        'reorder_level',
        'minimum_stock_level', // Add this for compatibility
        'stock_asset_account_id',
        'sales_account_id',
        'purchase_account_id',
        'opening_stock_value',
        'current_stock_value',
        'tax_rate',
        'tax_inclusive',
        'barcode',
        'image_path',
        'maintain_stock',
        'is_active',
        'is_saleable',
        'is_purchasable',
        'is_visible_online',
        'is_featured',
        'view_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_rate' => 'decimal:2',
        'sales_rate' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'unit_conversion_factor' => 'decimal:6',
        'opening_stock' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'quantity' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'minimum_stock_level' => 'decimal:2',
        'opening_stock_value' => 'decimal:2',
        'current_stock_value' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'maintain_stock' => 'boolean',
        'is_active' => 'boolean',
        'is_saleable' => 'boolean',
        'is_purchasable' => 'boolean',
        'tax_inclusive' => 'boolean',
        'is_visible_online' => 'boolean',
        'is_featured' => 'boolean',
        'view_count' => 'integer',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function primaryUnit()
    {
        return $this->belongsTo(Unit::class, 'primary_unit_id');
    }

    // Alias for compatibility
    public function unit()
    {
        return $this->primaryUnit();
    }

    // Ledger Account Relationships
    public function stockAssetAccount()
    {
        return $this->belongsTo(LedgerAccount::class, 'stock_asset_account_id');
    }

    public function salesAccount()
    {
        return $this->belongsTo(LedgerAccount::class, 'sales_account_id');
    }

    public function purchaseAccount()
    {
        return $this->belongsTo(LedgerAccount::class, 'purchase_account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class)->orderBy('transaction_date', 'desc')->orderBy('created_at', 'desc');
    }

    // E-commerce Relationships
    public function images()
    {
        return $this->hasMany(ProductImage::class)->ordered();
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Date-based Stock Calculation Methods

    /**
     * Calculate stock quantity as of a specific date
     */
    public function getStockAsOfDate($date = null, $includeTime = false)
    {
        $date = $date ?? now();

        if (!$includeTime) {
            $date = is_string($date) ? $date : $date->toDateString();
            $query = $this->stockMovements()
                ->where('transaction_date', '<=', $date);
        } else {
            $query = $this->stockMovements()
                ->where('created_at', '<=', $date);
        }

        return $query->sum('quantity') ?? 0;
    }

    /**
     * Calculate stock value as of a specific date with different valuation methods
     */
    public function getStockValueAsOfDate($date = null, $valuationMethod = 'weighted_average')
    {
        $date = $date ?? now()->toDateString();

        $movements = $this->stockMovements()
            ->where('transaction_date', '<=', $date)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        return $this->calculateStockValue($movements, $valuationMethod);
    }

    /**
     * Calculate stock value using different valuation methods
     */
    private function calculateStockValue($movements, $method = 'weighted_average')
    {
        $totalQuantity = 0;
        $totalValue = 0;
        $averageRate = 0;
        $transactions = [];

        foreach ($movements as $movement) {
            if ($movement->quantity > 0) {
                // Stock In - Add to inventory
                $totalValue += ($movement->quantity * $movement->rate);
                $totalQuantity += $movement->quantity;

                if ($totalQuantity > 0) {
                    $averageRate = $totalValue / $totalQuantity;
                }

                // Store for FIFO calculation
                if ($method === 'fifo') {
                    $transactions[] = [
                        'type' => 'in',
                        'quantity' => $movement->quantity,
                        'rate' => $movement->rate,
                        'remaining' => $movement->quantity
                    ];
                }

            } else {
                // Stock Out - Reduce from inventory
                $outQuantity = abs($movement->quantity);
                $totalQuantity -= $outQuantity;

                if ($method === 'weighted_average') {
                    $totalValue -= ($outQuantity * $averageRate);
                } elseif ($method === 'fifo') {
                    // FIFO Logic
                    $costOfGoodsSold = 0;
                    foreach ($transactions as &$transaction) {
                        if ($transaction['type'] === 'in' && $transaction['remaining'] > 0 && $outQuantity > 0) {
                            $usedQty = min($transaction['remaining'], $outQuantity);
                            $costOfGoodsSold += ($usedQty * $transaction['rate']);
                            $transaction['remaining'] -= $usedQty;
                            $outQuantity -= $usedQty;
                        }
                    }
                    $totalValue -= $costOfGoodsSold;
                }
            }
        }

        return [
            'quantity' => max(0, $totalQuantity),
            'value' => max(0, $totalValue),
            'average_rate' => $totalQuantity > 0 ? $totalValue / $totalQuantity : 0,
            'valuation_method' => $method
        ];
    }

    /**
     * Get stock movement history for a date range
     */
    public function getStockMovementHistory($fromDate = null, $toDate = null)
    {
        $fromDate = $fromDate ?? now()->subMonth()->toDateString();
        $toDate = $toDate ?? now()->toDateString();

        return $this->stockMovements()
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->with(['tenant', 'creator'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($movement) {
                return [
                    'id' => $movement->id,
                    'date' => $movement->transaction_date,
                    'type' => $movement->direction,
                    'transaction_type' => $movement->transaction_type,
                    'quantity' => $movement->absolute_quantity,
                    'rate' => $movement->rate,
                    'reference' => $movement->reference,
                    'transaction_reference' => $movement->transaction_reference,
                    'created_at' => $movement->created_at,
                    'created_by' => $movement->creator->name ?? 'System',
                ];
            });
    }

    /**
     * Get stock aging analysis
     */
    public function getStockAging($asOfDate = null)
    {
        $asOfDate = $asOfDate ?? now()->toDateString();

        $movements = $this->stockMovements()
            ->where('quantity', '>', 0) // Only incoming stock
            ->where('transaction_date', '<=', $asOfDate)
            ->orderBy('transaction_date', 'asc')
            ->get();

        $aging = [
            '0-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0
        ];

        foreach ($movements as $movement) {
            $daysOld = now()->diffInDays($movement->transaction_date);

            if ($daysOld <= 30) {
                $aging['0-30'] += $movement->quantity;
            } elseif ($daysOld <= 60) {
                $aging['31-60'] += $movement->quantity;
            } elseif ($daysOld <= 90) {
                $aging['61-90'] += $movement->quantity;
            } else {
                $aging['90+'] += $movement->quantity;
            }
        }

        return $aging;
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return Storage::url($this->image_path);
        }
        return null;
    }

    public function getStockStatusAttribute()
    {
        if (!$this->maintain_stock) {
            return 'not_tracked';
        }

        if ($this->current_stock <= 0) {
            return 'out_of_stock';
        }

        if ($this->reorder_level && $this->current_stock <= $this->reorder_level) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    public function getStockValueAttribute()
    {
        // Calculate stock value from movements
        $asOfDate = request('as_of_date', now()->toDateString());
        $valuationMethod = request('valuation_method', 'weighted_average');

        $cacheKey = "product_stock_value_{$this->id}_{$asOfDate}_{$valuationMethod}";

        return Cache::remember($cacheKey, 300, function () use ($asOfDate, $valuationMethod) {
            $result = $this->getStockValueAsOfDate($asOfDate, $valuationMethod);
            return $result['value'] ?? 0;
        });
    }

    /**
     * Override current_stock to always calculate from stock movements
     */
    public function getCurrentStockAttribute($value)
    {
        // Always calculate from stock movements based on transaction_date
        $asOfDate = request('as_of_date', now()->toDateString());

        // Use a short cache to avoid repeated calculations in same request
        $cacheKey = "product_stock_{$this->id}_{$asOfDate}";

        return Cache::remember($cacheKey, 300, function () use ($asOfDate) {
            return $this->getStockAsOfDate($asOfDate);
        });
    }

    // Compatibility accessors
    public function getQuantityAttribute($value)
    {
        return $value ?? $this->current_stock;
    }

    public function getSellingPriceAttribute($value)
    {
        return $value ?? $this->sales_rate;
    }

    public function getMinimumStockLevelAttribute($value)
    {
        return $value ?? $this->reorder_level;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSaleable($query)
    {
        return $query->where('is_saleable', true);
    }

    public function scopePurchasable($query)
    {
        return $query->where('is_purchasable', true);
    }

    public function scopeLowStock($query)
    {
        return $query->where('maintain_stock', true)
                    ->whereColumn('current_stock', '<=', 'reorder_level');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('maintain_stock', true)
                    ->where('current_stock', '<=', 0);
    }

    // Helper method for stock value calculation in P&L
    public function getStockValueForPeriod($fromDate, $toDate)
    {
        // Simple calculation - you can enhance this later
        return [
            'opening_value' => $this->opening_stock_value,
            'closing_value' => $this->current_stock_value,
        ];
    }

    // E-commerce Helper Methods

    /**
     * Get the primary image URL or fallback
     */
    public function getImageUrl()
    {
        if ($this->primaryImage) {
            return $this->primaryImage->image_url;
        }

        if ($this->image_path) {
            return Storage::disk('public')->url($this->image_path);
        }

        return asset('images/no-image.png');
    }

    /**
     * Get product slug for URL (generate if not exists)
     */
    public function getSlugAttribute()
    {
        return $this->attributes['slug'] ?? \Illuminate\Support\Str::slug($this->name);
    }

    /**
     * Check if product is available for e-commerce
     */
    public function isAvailableOnline()
    {
        return $this->is_active &&
               $this->is_saleable &&
               ($this->attributes['is_visible_online'] ?? true) &&
               (!$this->maintain_stock || $this->current_stock > 0);
    }

    /**
     * Scope for online products
     */
    public function scopeOnline($query)
    {
        return $query->where('is_active', true)
            ->where('is_saleable', true)
            ->where('is_visible_online', true);
    }

    /**
     * Scope for featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
