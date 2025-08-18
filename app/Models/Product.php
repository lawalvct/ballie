<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'type',
        'name',
        'sku',
        'description',
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
        return $this->current_stock_value;
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
}
