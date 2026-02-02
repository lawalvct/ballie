<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'requested_by',
        'request_number',
        'requested_amount',
        'deduction_amount',
        'net_amount',
        'deduction_type',
        'deduction_rate',
        'available_balance',
        'bank_name',
        'account_name',
        'account_number',
        'bank_code',
        'status',
        'processed_by',
        'processed_at',
        'payment_reference',
        'admin_notes',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'deduction_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'deduction_rate' => 'decimal:2',
        'available_balance' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = self::generateRequestNumber();
            }
        });
    }

    /**
     * Generate a unique request number.
     */
    public static function generateRequestNumber(): string
    {
        $prefix = 'PAY';
        $year = date('Y');
        $month = date('m');

        $lastRequest = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest('id')
            ->first();

        $nextNumber = 1;
        if ($lastRequest && preg_match('/(\d+)$/', $lastRequest->request_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        return $prefix . $year . $month . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the tenant that owns the payout request.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who requested the payout.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the super admin who processed the payout.
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(SuperAdmin::class, 'processed_by');
    }

    /**
     * Scope for pending payouts.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for completed payouts.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_PROCESSING => 'primary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get progress percentage for timeline display.
     */
    public function getProgressPercentageAttribute(): int
    {
        return match ($this->status) {
            self::STATUS_PENDING => 25,
            self::STATUS_APPROVED => 50,
            self::STATUS_PROCESSING => 75,
            self::STATUS_COMPLETED => 100,
            self::STATUS_REJECTED => 0,
            self::STATUS_CANCELLED => 0,
            default => 0,
        };
    }

    /**
     * Get deduction description.
     */
    public function getDeductionDescriptionAttribute(): string
    {
        if ($this->deduction_type === 'percentage') {
            return $this->deduction_rate . '% Service Fee';
        }
        return '₦' . number_format($this->deduction_rate, 2) . ' Processing Fee';
    }

    /**
     * Check if payout can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING]);
    }

    /**
     * Check if payout can be processed.
     */
    public function canBeProcessed(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    /**
     * Calculate available balance for a tenant.
     */
    public static function calculateAvailableBalance(int $tenantId): float
    {
        // Get total revenue from completed/paid orders
        $totalRevenue = Order::where('tenant_id', $tenantId)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Get total completed payouts
        $totalPayouts = self::where('tenant_id', $tenantId)
            ->whereIn('status', [self::STATUS_COMPLETED, self::STATUS_PROCESSING, self::STATUS_APPROVED])
            ->sum('requested_amount');

        // Get pending payout requests (reserved)
        $pendingPayouts = self::where('tenant_id', $tenantId)
            ->where('status', self::STATUS_PENDING)
            ->sum('requested_amount');

        return max(0, $totalRevenue - $totalPayouts - $pendingPayouts);
    }

    /**
     * Calculate deduction based on settings.
     */
    public static function calculateDeduction(float $amount, PayoutSetting $settings): array
    {
        $deductionAmount = 0;

        if ($settings->deduction_type === 'percentage') {
            $deductionAmount = ($amount * $settings->deduction_value) / 100;
        } else {
            $deductionAmount = $settings->deduction_value;
        }

        $netAmount = $amount - $deductionAmount;

        return [
            'deduction_amount' => round($deductionAmount, 2),
            'net_amount' => round($netAmount, 2),
            'deduction_type' => $settings->deduction_type,
            'deduction_rate' => $settings->deduction_value,
        ];
    }
}
