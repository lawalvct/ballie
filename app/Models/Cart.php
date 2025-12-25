<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'session_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the cart
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the customer that owns the cart
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the cart items
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calculate cart subtotal
     */
    public function getSubtotal()
    {
        return $this->items->sum(function ($item) {
            return $item->product->sales_rate * $item->quantity;
        });
    }

    /**
     * Get total items count
     */
    public function getTotalItemsCount()
    {
        return $this->items->sum('quantity');
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty()
    {
        return $this->items->count() === 0;
    }

    /**
     * Clear all items from cart
     */
    public function clear()
    {
        $this->items()->delete();
    }

    /**
     * Scope for expired carts
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }
}
