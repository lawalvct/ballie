<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportResponseTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'category_id',
        'is_active',
        'usage_count',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'usage_count' => 'integer',
    ];

    /**
     * Get the category this template belongs to.
     */
    public function category()
    {
        return $this->belongsTo(SupportCategory::class, 'category_id');
    }

    /**
     * Get the admin who created this template.
     */
    public function creator()
    {
        return $this->belongsTo(SuperAdmin::class, 'created_by');
    }

    /**
     * Scope for active templates only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by usage count.
     */
    public function scopePopular($query)
    {
        return $query->orderBy('usage_count', 'desc');
    }

    /**
     * Replace template variables with actual values.
     */
    public function render(array $variables = []): string
    {
        $content = $this->content;

        foreach ($variables as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }

        return $content;
    }

    /**
     * Increment usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Get available template variables.
     */
    public static function getAvailableVariables(): array
    {
        return [
            'customer_name' => 'Customer name',
            'company_name' => 'Company name',
            'ticket_number' => 'Ticket number',
            'ticket_subject' => 'Ticket subject',
            'admin_name' => 'Support agent name',
        ];
    }
}
