<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'affiliate_code',
        'company_name',
        'phone',
        'bio',
        'custom_commission_rate',
        'status',
        'total_referrals',
        'total_commissions',
        'total_paid',
        'payment_details',
        'approved_at',
        'last_payout_at',
    ];

    protected $casts = [
        'payment_details' => 'array',
        'custom_commission_rate' => 'decimal:2',
        'total_commissions' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'approved_at' => 'datetime',
        'last_payout_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($affiliate) {
            if (empty($affiliate->affiliate_code)) {
                $affiliate->affiliate_code = static::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode()
    {
        do {
          //    $code = 'AFF' . strtoupper(Str::random(8));
            $code = strtoupper(Str::random(6));
        } while (static::where('affiliate_code', $code)->exists());

        return $code;
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(AffiliateReferral::class);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'referred_by_affiliate_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(AffiliatePayout::class);
    }

    // Helper methods
    public function getCommissionRate()
    {
        return $this->custom_commission_rate ?? config('affiliate.default_commission_rate', 10.00);
    }

    public function getPendingCommissions()
    {
        return $this->commissions()->where('status', 'approved')->sum('commission_amount');
    }

    public function getMonthlyEarnings($month = null, $year = null)
    {
        $query = $this->commissions()->where('status', 'paid');

        if ($month && $year) {
            $query->whereMonth('paid_date', $month)->whereYear('paid_date', $year);
        }

        return $query->sum('commission_amount');
    }

    public function getReferralLink($path = '')
    {
        $baseUrl = config('app.url');
        return $baseUrl . ($path ? "/$path" : '') . "?ref={$this->affiliate_code}";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
