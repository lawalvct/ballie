<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected $appends = ['image_url'];

    /**
     * Get the tenant that owns the product image
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return asset('images/no-image.png');
        }

        // If it's already a full URL, return as is
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }

        // Return storage URL
        return Storage::disk('public')->url($this->image_path);
    }

    /**
     * Set this image as primary for the product
     */
    public function setAsPrimary()
    {
        // Remove primary flag from other images
        static::where('product_id', $this->product_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        // Set this as primary
        $this->is_primary = true;
        $this->save();
    }

    /**
     * Delete the image file from storage when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            if ($image->image_path && !filter_var($image->image_path, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($image->image_path);
            }
        });
    }

    /**
     * Scope for primary images
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope for ordering by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
