<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliatePayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id',
        'total_amount',
        'fee_amount',
        'net_amount',
        'payout_method',
        'payout_details',
        'status',
        'notes',
        'requested_at',
        'processed_at',
        'completed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'payout_details' => 'array',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
