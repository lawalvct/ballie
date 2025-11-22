<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'product_id',
        'product_name',
        'description',
        'quantity',
        'unit',
        'rate',
        'discount',
        'tax',
        'is_tax_inclusive',
        'amount',
        'total',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'amount' => 'decimal:2',
        'total' => 'decimal:2',
        'is_tax_inclusive' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relationships
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate line total with tax and discount
     */
    public function calculateTotal(): float
    {
        $amount = $this->quantity * $this->rate;

        if ($this->is_tax_inclusive) {
            // Tax is already included in the rate
            $total = $amount - $this->discount;
        } else {
            // Add tax to amount
            $total = $amount + $this->tax - $this->discount;
        }

        return round($total, 2);
    }

    /**
     * Get the total amount for this item
     */
    public function getTotal(): float
    {
        $itemTotal = ($this->quantity * $this->rate) - $this->discount;
        $taxAmount = $itemTotal * ($this->tax / 100);
        return round($itemTotal + $taxAmount, 2);
    }

    /**
     * Auto-calculate totals before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            // Calculate amount (quantity * rate)
            $item->amount = $item->quantity * $item->rate;

            // Calculate total if not set
            if (is_null($item->total)) {
                $item->total = $item->calculateTotal();
            }
        });
    }
}
