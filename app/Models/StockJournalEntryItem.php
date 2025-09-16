<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockJournalEntryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_journal_entry_id',
        'product_id',
        'movement_type',
        'quantity',
        'rate',
        'amount',
        'stock_before',
        'stock_after',
        'batch_number',
        'expiry_date',
        'remarks',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'stock_before' => 'decimal:4',
        'stock_after' => 'decimal:4',
        'expiry_date' => 'date',
    ];

    protected $dates = [
        'expiry_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Calculate amount
            $model->amount = $model->quantity * $model->rate;

            // Calculate stock after based on movement type
            if ($model->movement_type === 'in') {
                $model->stock_after = $model->stock_before + $model->quantity;
            } else {
                $model->stock_after = $model->stock_before - $model->quantity;
            }
        });
    }

    /**
     * Get the stock journal entry that owns the item.
     */
    public function stockJournalEntry(): BelongsTo
    {
        return $this->belongsTo(StockJournalEntry::class);
    }

    /**
     * Get the product that owns the item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the movement type display name.
     */
    public function getMovementTypeDisplayAttribute(): string
    {
        return match($this->movement_type) {
            'in' => 'Receipt/Production',
            'out' => 'Issue/Consumption',
            default => ucfirst($this->movement_type)
        };
    }

    /**
     * Get the movement type color for display.
     */
    public function getMovementTypeColorAttribute(): string
    {
        return match($this->movement_type) {
            'in' => 'green',
            'out' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get the movement type icon.
     */
    public function getMovementTypeIconAttribute(): string
    {
        return match($this->movement_type) {
            'in' => 'arrow-down',
            'out' => 'arrow-up',
            default => 'arrows-expand'
        };
    }

    /**
     * Check if item has batch tracking.
     */
    public function getHasBatchAttribute(): bool
    {
        return !empty($this->batch_number);
    }

    /**
     * Check if item has expiry tracking.
     */
    public function getHasExpiryAttribute(): bool
    {
        return !empty($this->expiry_date);
    }

    /**
     * Check if item is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->has_expiry && $this->expiry_date < now()->toDateString();
    }

    /**
     * Get days until expiry.
     */
    public function getDaysToExpiryAttribute(): ?int
    {
        if (!$this->has_expiry) {
            return null;
        }

        return now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Format quantity for display.
     */
    public function getFormattedQuantityAttribute(): string
    {
        return number_format($this->quantity, 4);
    }

    /**
     * Format rate for display.
     */
    public function getFormattedRateAttribute(): string
    {
        return number_format($this->rate, 2);
    }

    /**
     * Format amount for display.
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    /**
     * Scope for filtering by movement type
     */
    public function scopeMovementType($query, $type)
    {
        return $query->where('movement_type', $type);
    }

    /**
     * Scope for filtering by product
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope for expired items
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<', now()->toDateString());
    }

    /**
     * Scope for items expiring soon (within given days)
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                    ->whereBetween('expiry_date', [
                        now()->toDateString(),
                        now()->addDays($days)->toDateString()
                    ]);
    }
}
