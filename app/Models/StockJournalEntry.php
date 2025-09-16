<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StockJournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'journal_number',
        'journal_date',
        'reference_number',
        'narration',
        'entry_type',
        'status',
        'posted_at',
        'posted_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'journal_date' => 'date',
        'posted_at' => 'datetime',
    ];

    protected $dates = [
        'journal_date',
        'posted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->journal_number)) {
                $model->journal_number = static::generateJournalNumber($model->tenant_id);
            }
        });
    }

    /**
     * Generate unique journal number
     */
    public static function generateJournalNumber($tenantId): string
    {
        $prefix = 'SJ';
        $date = now()->format('Ymd');

        // Get the last journal number for today
        $lastJournal = static::where('tenant_id', $tenantId)
            ->where('journal_number', 'like', $prefix . $date . '%')
            ->orderBy('journal_number', 'desc')
            ->first();

        if ($lastJournal) {
            $lastNumber = (int) substr($lastJournal->journal_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the tenant that owns the stock journal entry.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who created the entry.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the entry.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who posted the entry.
     */
    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * Get the journal entry items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockJournalEntryItem::class);
    }

    /**
     * Get the journal entry items with product details.
     */
    public function itemsWithProducts(): HasMany
    {
        return $this->hasMany(StockJournalEntryItem::class)->with('product');
    }

    /**
     * Get the entry type display name.
     */
    public function getEntryTypeDisplayAttribute(): string
    {
        return match($this->entry_type) {
            'consumption' => 'Material Consumption',
            'production' => 'Production Receipt',
            'adjustment' => 'Stock Adjustment',
            'transfer' => 'Stock Transfer',
            default => ucfirst(str_replace('_', ' ', $this->entry_type))
        };
    }

    /**
     * Get the status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'posted' => 'Posted',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status)
        };
    }

    /**
     * Get the status color for display.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'yellow',
            'posted' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    /**
     * Check if the entry can be edited.
     */
    public function getCanEditAttribute(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the entry can be posted.
     */
    public function getCanPostAttribute(): bool
    {
        return $this->status === 'draft' && $this->items()->count() > 0;
    }

    /**
     * Check if the entry can be cancelled.
     */
    public function getCanCancelAttribute(): bool
    {
        return in_array($this->status, ['draft', 'posted']);
    }

    /**
     * Post the journal entry
     */
    public function post($userId = null): bool
    {
        if (!$this->can_post) {
            return false;
        }

        $this->update([
            'status' => 'posted',
            'posted_at' => now(),
            'posted_by' => $userId ?? auth()->id(),
        ]);

        // Update stock movements
        $this->updateStockMovements();

        return true;
    }

    /**
     * Cancel the journal entry
     */
    public function cancel(): bool
    {
        if (!$this->can_cancel) {
            return false;
        }

        // If it was posted, reverse the stock movements
        if ($this->status === 'posted') {
            $this->reverseStockMovements();
        }

        $this->update([
            'status' => 'cancelled',
        ]);

        return true;
    }

    /**
     * Update stock movements when posting
     */
    private function updateStockMovements(): void
    {
        foreach ($this->items as $item) {
            // Create stock movement record
            StockMovement::create([
                'tenant_id' => $this->tenant_id,
                'product_id' => $item->product_id,
                'type' => $this->entry_type,
                'quantity' => $item->movement_type === 'in' ? $item->quantity : -$item->quantity,
                'old_stock' => $item->stock_before,
                'new_stock' => $item->stock_after,
                'rate' => $item->rate,
                'reference' => $this->journal_number,
                'remarks' => $this->narration,
                'created_by' => $this->created_by,
            ]);

            // Update product stock
            $item->product()->update([
                'stock_quantity' => $item->stock_after,
            ]);
        }
    }

    /**
     * Reverse stock movements when cancelling
     */
    private function reverseStockMovements(): void
    {
        foreach ($this->items as $item) {
            // Create reverse stock movement record
            StockMovement::create([
                'tenant_id' => $this->tenant_id,
                'product_id' => $item->product_id,
                'type' => 'adjustment', // Mark as adjustment for reversal
                'quantity' => $item->movement_type === 'in' ? -$item->quantity : $item->quantity,
                'old_stock' => $item->stock_after,
                'new_stock' => $item->stock_before,
                'rate' => $item->rate,
                'reference' => $this->journal_number . ' (Reversed)',
                'remarks' => "Reversal of {$this->journal_number}: {$this->narration}",
                'created_by' => auth()->id(),
            ]);

            // Revert product stock
            $item->product()->update([
                'stock_quantity' => $item->stock_before,
            ]);
        }
    }

    /**
     * Calculate total amount
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->items()->sum('amount');
    }

    /**
     * Get total items count
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items()->count();
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('journal_date', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by entry type
     */
    public function scopeEntryType($query, $type)
    {
        return $query->where('entry_type', $type);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
