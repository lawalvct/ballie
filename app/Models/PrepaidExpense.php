<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PrepaidExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'voucher_id',
        'voucher_entry_id',
        'prepaid_account_id',
        'expense_account_id',
        'total_amount',
        'installment_amount',
        'installments_count',
        'installments_posted',
        'frequency',
        'start_date',
        'next_posting_date',
        'end_date',
        'description',
        'status',
        'created_by',
        'meta_data',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'installments_count' => 'integer',
        'installments_posted' => 'integer',
        'start_date' => 'date',
        'next_posting_date' => 'date',
        'end_date' => 'date',
        'meta_data' => 'array',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_PAUSED = 'paused';

    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_QUARTERLY = 'quarterly';
    const FREQUENCY_YEARLY = 'yearly';

    // ── Relationships ──

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function voucherEntry()
    {
        return $this->belongsTo(VoucherEntry::class);
    }

    public function prepaidAccount()
    {
        return $this->belongsTo(LedgerAccount::class, 'prepaid_account_id');
    }

    public function expenseAccount()
    {
        return $this->belongsTo(LedgerAccount::class, 'expense_account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postings()
    {
        return $this->hasMany(PrepaidExpensePosting::class);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeDue($query, $date = null)
    {
        $date = $date ?: now()->toDateString();
        return $query->where('status', self::STATUS_ACTIVE)
                     ->whereNotNull('next_posting_date')
                     ->where('next_posting_date', '<=', $date);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    // ── Methods ──

    /**
     * Get the full amortization schedule as an array.
     */
    public function getSchedule(): array
    {
        $schedule = [];
        $remaining = (float) $this->total_amount;
        $date = Carbon::parse($this->start_date);

        for ($i = 1; $i <= $this->installments_count; $i++) {
            // Last installment absorbs rounding remainder
            $amount = ($i === $this->installments_count)
                ? round($remaining, 2)
                : (float) $this->installment_amount;

            $remaining -= $amount;

            $schedule[] = [
                'installment_number' => $i,
                'date' => $date->copy()->toDateString(),
                'amount' => $amount,
                'status' => $i <= $this->installments_posted ? 'posted' : 'pending',
            ];

            $date = $this->advanceDate($date);
        }

        return $schedule;
    }

    /**
     * Calculate the next posting date after a given date.
     */
    public function advanceDate(Carbon $date): Carbon
    {
        return match ($this->frequency) {
            self::FREQUENCY_QUARTERLY => $date->copy()->addMonths(3),
            self::FREQUENCY_YEARLY => $date->copy()->addYear(),
            default => $date->copy()->addMonth(), // monthly
        };
    }

    /**
     * Mark one installment as posted and advance the schedule.
     */
    public function markInstallmentPosted(): void
    {
        $this->installments_posted += 1;

        if ($this->installments_posted >= $this->installments_count) {
            $this->status = self::STATUS_COMPLETED;
            $this->next_posting_date = null;
        } else {
            $this->next_posting_date = $this->advanceDate(Carbon::parse($this->next_posting_date));
        }

        $this->save();
    }

    /**
     * Get the amount for the current (next) installment, handling rounding on last.
     */
    public function getNextInstallmentAmount(): float
    {
        $nextNumber = $this->installments_posted + 1;

        if ($nextNumber >= $this->installments_count) {
            // Last installment: total minus what's already been posted
            $postedTotal = $this->postings()->where('status', 'posted')->sum('amount');
            return round((float) $this->total_amount - $postedTotal, 2);
        }

        return (float) $this->installment_amount;
    }

    public function getRemainingAmount(): float
    {
        $postedTotal = $this->postings()->where('status', 'posted')->sum('amount');
        return round((float) $this->total_amount - $postedTotal, 2);
    }

    public function isComplete(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function getProgressPercentage(): float
    {
        if ($this->installments_count === 0) return 100;
        return round(($this->installments_posted / $this->installments_count) * 100, 1);
    }

    /**
     * Compute schedule fields from input data (used when creating).
     */
    public static function computeSchedule(float $totalAmount, int $installments, string $frequency, string $startDate): array
    {
        $installmentAmount = round($totalAmount / $installments, 2);
        $start = Carbon::parse($startDate);

        $endDate = $start->copy();
        for ($i = 1; $i < $installments; $i++) {
            $endDate = match ($frequency) {
                self::FREQUENCY_QUARTERLY => $endDate->addMonths(3),
                self::FREQUENCY_YEARLY => $endDate->addYear(),
                default => $endDate->addMonth(),
            };
        }

        return [
            'installment_amount' => $installmentAmount,
            'next_posting_date' => $start->toDateString(),
            'end_date' => $endDate->toDateString(),
        ];
    }
}
