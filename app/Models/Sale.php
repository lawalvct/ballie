<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToTenant;
use App\Traits\HasAudit;

class Sale extends Model
{
    use HasFactory, BelongsToTenant, HasAudit;

    protected $fillable = [
        'tenant_id',
        'sale_number',
        'customer_id',
        'user_id',
        'cash_register_id',
        'cash_register_session_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'change_amount',
        'status',
        'sale_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'sale_date' => 'datetime',
    ];

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function cashRegisterSession(): BelongsTo
    {
        return $this->belongsTo(CashRegisterSession::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    // Generate sale number
    public static function generateSaleNumber($tenant): string
    {
        $prefix = 'SALE-' . date('Y') . '-';
        $lastSale = static::where('tenant_id', $tenant->id)
            ->where('sale_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSale) {
            $lastNumber = intval(substr($lastSale->sale_number, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    // Calculate balance due
    public function getBalanceDueAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    // Check if fully paid
    public function getIsFullyPaidAttribute()
    {
        return $this->balance_due <= 0;
    }
}
