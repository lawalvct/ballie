<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToTenant;

class CashRegisterSession extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'cash_register_id',
        'user_id',
        'opening_balance',
        'closing_balance',
        'expected_balance',
        'difference',
        'opened_at',
        'closed_at',
        'opening_notes',
        'closing_notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('closed_at');
    }

    public function scopeClosed($query)
    {
        return $query->whereNotNull('closed_at');
    }

    public function getTotalSalesAttribute()
    {
        return $this->sales()->sum('total_amount');
    }

    public function getTotalCashSalesAttribute()
    {
        return $this->sales()
            ->whereHas('payments', function($query) {
                $query->whereHas('paymentMethod', function($subQuery) {
                    $subQuery->where('code', 'cash');
                });
            })
            ->sum('total_amount');
    }
}
