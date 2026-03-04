<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'business_category',
        'icon',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get tenants with this business type
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Scope to get only active business types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get business types by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope by business category (trading, manufacturing, service, hybrid).
     */
    public function scopeByBusinessCategory($query, string $businessCategory)
    {
        return $query->where('business_category', $businessCategory);
    }

    /**
     * Get grouped business types by category
     */
    public static function getGroupedByCategory()
    {
        return self::active()
            ->ordered()
            ->get()
            ->groupBy('category');
    }

    /**
     * Valid business categories.
     */
    const CATEGORY_TRADING       = 'trading';
    const CATEGORY_MANUFACTURING = 'manufacturing';
    const CATEGORY_SERVICE       = 'service';
    const CATEGORY_HYBRID        = 'hybrid';

    const BUSINESS_CATEGORIES = [
        self::CATEGORY_TRADING,
        self::CATEGORY_MANUFACTURING,
        self::CATEGORY_SERVICE,
        self::CATEGORY_HYBRID,
    ];

    /**
     * Get the business category value, defaults to hybrid.
     */
    public function getBusinessCategoryValue(): string
    {
        return $this->business_category ?? self::CATEGORY_HYBRID;
    }
}
