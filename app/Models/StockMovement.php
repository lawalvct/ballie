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
        'type',
        'quantity',
        'old_stock',
        'new_stock',
        'rate',
        'reference',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'old_stock' => 'decimal:2',
        'new_stock' => 'decimal:2',
        'rate' => 'decimal:2',
    ];

    protected $dates = [
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

    /**
     * Get the user who created the stock movement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the movement type display name.
     */
    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            'opening_stock' => 'Opening Stock',
            'purchase' => 'Purchase',
            'sale' => 'Sale',
            'adjustment' => 'Stock Adjustment',
            'return' => 'Return',
            'transfer_in' => 'Transfer In',
            'transfer_out' => 'Transfer Out',
            'damage' => 'Damage/Loss',
            default => ucfirst(str_replace('_', ' ', $this->type))
        };
    }

    /**
     * Check if movement increases stock.
     */
    public function getIsIncreaseAttribute(): bool
    {
        return in_array($this->type, ['opening_stock', 'purchase', 'return', 'transfer_in', 'adjustment']) && $this->quantity > 0;
    }

    /**
     * Check if movement decreases stock.
     */
    public function getIsDecreaseAttribute(): bool
    {
        return in_array($this->type, ['sale', 'transfer_out', 'damage', 'adjustment']) && $this->quantity < 0;
    }
}
