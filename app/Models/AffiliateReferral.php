<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateReferral extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id',
        'referred_tenant_id',
        'referral_source',
        'conversion_type',
        'conversion_value',
        'status',
        'conversion_date',
        'tracking_data',
    ];

    protected $casts = [
        'tracking_data' => 'array',
        'conversion_value' => 'decimal:2',
        'conversion_date' => 'datetime',
    ];

    // Relationships
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'referred_tenant_id');
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
