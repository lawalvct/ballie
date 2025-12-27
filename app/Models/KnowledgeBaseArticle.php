<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class KnowledgeBaseArticle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'is_published',
        'is_featured',
        'view_count',
        'helpful_count',
        'not_helpful_count',
        'sort_order',
        'meta_title',
        'meta_description',
        'author_id',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'view_count' => 'integer',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('title') && empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    /**
     * Get the category this article belongs to.
     */
    public function category()
    {
        return $this->belongsTo(SupportCategory::class, 'category_id');
    }

    /**
     * Get the author of the article.
     */
    public function author()
    {
        return $this->belongsTo(SuperAdmin::class, 'author_id');
    }

    /**
     * Scope for published articles only.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for featured articles.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for ordering by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /**
     * Scope for popular articles (most viewed).
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('view_count', 'desc')->limit($limit);
    }

    /**
     * Scope for most helpful articles.
     */
    public function scopeMostHelpful($query, $limit = 10)
    {
        return $query->orderBy('helpful_count', 'desc')->limit($limit);
    }

    /**
     * Increment view count.
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    /**
     * Mark as helpful.
     */
    public function markHelpful(): void
    {
        $this->increment('helpful_count');
    }

    /**
     * Mark as not helpful.
     */
    public function markNotHelpful(): void
    {
        $this->increment('not_helpful_count');
    }

    /**
     * Get helpfulness percentage.
     */
    public function getHelpfulnessPercentageAttribute(): int
    {
        $total = $this->helpful_count + $this->not_helpful_count;

        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->helpful_count / $total) * 100);
    }

    /**
     * Get excerpt or generate from content.
     */
    public function getExcerptAttribute($value): string
    {
        if (!empty($value)) {
            return $value;
        }

        return Str::limit(strip_tags($this->content), 200);
    }

    /**
     * Get reading time in minutes.
     */
    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200); // Average reading speed

        return max(1, $minutes);
    }

    /**
     * Publish the article.
     */
    public function publish(): void
    {
        $this->update([
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    /**
     * Unpublish the article.
     */
    public function unpublish(): void
    {
        $this->update([
            'is_published' => false,
        ]);
    }
}
