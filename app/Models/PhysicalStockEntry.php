<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhysicalStockEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'physical_stock_voucher_id',
        'product_id',
        'book_quantity',
        'physical_quantity',
        'difference_quantity',
        'current_rate',
        'difference_value',
        'batch_number',
        'expiry_date',
        'location',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'book_quantity' => 'decimal:4',
        'physical_quantity' => 'decimal:4',
        'difference_quantity' => 'decimal:4',
        'current_rate' => 'decimal:2',
        'difference_value' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    /**
     * Get the voucher that owns this entry.
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(PhysicalStockVoucher::class, 'physical_stock_voucher_id');
    }

    /**
     * Get the product for this entry.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who created this entry.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this entry.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Calculate the difference quantity and value.
     */
    public function calculateDifference()
    {
        $this->difference_quantity = $this->physical_quantity - $this->book_quantity;
        $this->difference_value = $this->difference_quantity * $this->current_rate;

        $this->save();
        return $this;
    }

    /**
     * Set the book quantity from current stock.
     */
    public function setBookQuantityFromStock($asOfDate = null)
    {
        $asOfDate = $asOfDate ?? $this->voucher->voucher_date ?? now()->toDateString();
        $this->book_quantity = $this->product->getStockAsOfDate($asOfDate);
        $this->save();

        return $this;
    }

    /**
     * Set the current rate from product.
     */
    public function setCurrentRateFromProduct()
    {
        // Use weighted average or latest purchase rate
        $stockValue = $this->product->getStockValueAsOfDate(
            $this->voucher->voucher_date ?? now()->toDateString()
        );

        $this->current_rate = $stockValue['average_rate'] ?? $this->product->purchase_rate ?? 0;
        $this->save();

        return $this;
    }

    /**
     * Create stock movement for this entry.
     */
    public function createStockMovement()
    {
        if ($this->difference_quantity == 0) {
            return null;
        }

        return StockMovement::createFromPhysicalAdjustment($this);
    }

    /**
     * Get the difference type (shortage/excess).
     */
    public function getDifferenceType(): string
    {
        if ($this->difference_quantity > 0) {
            return 'excess';
        } elseif ($this->difference_quantity < 0) {
            return 'shortage';
        }
        return 'no_difference';
    }

    /**
     * Get the difference type display name.
     */
    public function getDifferenceTypeDisplay(): string
    {
        return match($this->getDifferenceType()) {
            'excess' => 'Stock Excess',
            'shortage' => 'Stock Shortage',
            'no_difference' => 'No Difference',
            default => 'Unknown'
        };
    }

    /**
     * Get the difference quantity as absolute value.
     */
    public function getAbsoluteDifferenceAttribute(): float
    {
        return abs($this->difference_quantity ?? 0);
    }

    /**
     * Get the difference value as absolute value.
     */
    public function getAbsoluteDifferenceValueAttribute(): float
    {
        return abs($this->difference_value ?? 0);
    }

    /**
     * Check if this entry has a difference.
     */
    public function hasDifference(): bool
    {
        return $this->difference_quantity != 0;
    }

    /**
     * Check if this is a stock shortage.
     */
    public function isShortage(): bool
    {
        return $this->difference_quantity < 0;
    }

    /**
     * Check if this is a stock excess.
     */
    public function isExcess(): bool
    {
        return $this->difference_quantity > 0;
    }

    /**
     * Get color class for difference display.
     */
    public function getDifferenceColorAttribute(): string
    {
        if ($this->difference_quantity > 0) {
            return 'text-green-600'; // Excess - positive
        } elseif ($this->difference_quantity < 0) {
            return 'text-red-600'; // Shortage - negative
        }
        return 'text-gray-600'; // No difference
    }

    /**
     * Get icon for difference type.
     */
    public function getDifferenceIconAttribute(): string
    {
        if ($this->difference_quantity > 0) {
            return 'arrow-up'; // Excess
        } elseif ($this->difference_quantity < 0) {
            return 'arrow-down'; // Shortage
        }
        return 'minus'; // No difference
    }

    /**
     * Scope for entries with differences.
     */
    public function scopeWithDifferences($query)
    {
        return $query->where('difference_quantity', '!=', 0);
    }

    /**
     * Scope for shortage entries.
     */
    public function scopeShortages($query)
    {
        return $query->where('difference_quantity', '<', 0);
    }

    /**
     * Scope for excess entries.
     */
    public function scopeExcess($query)
    {
        return $query->where('difference_quantity', '>', 0);
    }

    /**
     * Boot method to automatically calculate differences.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($entry) {
            // Automatically calculate difference when saving
            if ($entry->isDirty(['book_quantity', 'physical_quantity', 'current_rate'])) {
                $entry->difference_quantity = $entry->physical_quantity - $entry->book_quantity;
                $entry->difference_value = $entry->difference_quantity * $entry->current_rate;
            }
        });
    }
}
