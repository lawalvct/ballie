<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SupportCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get all tickets for this category.
     */
    public function tickets()
    {
        return $this->hasMany(SupportTicket::class, 'category_id');
    }

    /**
     * Get all knowledge base articles for this category.
     */
    public function articles()
    {
        return $this->hasMany(KnowledgeBaseArticle::class, 'category_id');
    }

    /**
     * Get all response templates for this category.
     */
    public function responseTemplates()
    {
        return $this->hasMany(SupportResponseTemplate::class, 'category_id');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the count of open tickets in this category.
     */
    public function getOpenTicketsCountAttribute()
    {
        return $this->tickets()
            ->whereIn('status', ['new', 'open', 'in_progress', 'waiting_customer'])
            ->count();
    }

    /**
     * Get the count of published articles in this category.
     */
    public function getPublishedArticlesCountAttribute()
    {
        return $this->articles()->where('is_published', true)->count();
    }
}
