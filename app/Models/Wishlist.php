<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'customer_id',
    ];

    /**
     * Get the tenant that owns the wishlist
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the customer that owns the wishlist
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the wishlist items
     */
    public function items()
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Check if product is in wishlist
     */
    public function hasProduct($productId)
    {
        return $this->items()->where('product_id', $productId)->exists();
    }

    /**
     * Add product to wishlist
     */
    public function addProduct($productId)
    {
        if (!$this->hasProduct($productId)) {
            return $this->items()->create(['product_id' => $productId]);
        }
        return false;
    }

    /**
     * Remove product from wishlist
     */
    public function removeProduct($productId)
    {
        return $this->items()->where('product_id', $productId)->delete();
    }
}
