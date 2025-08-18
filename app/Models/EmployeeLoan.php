<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeLoan extends Model
{
    protected $fillable = [
        'employee_id',
        'loan_number',
        'loan_amount',
        'monthly_deduction',
        'duration_months',
        'start_date',
        'total_paid',
        'balance',
        'status',
        'purpose',
        'approved_by',
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'monthly_deduction' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'start_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loan) {
            if (empty($loan->loan_number)) {
                $loan->loan_number = static::generateLoanNumber();
            }
            $loan->balance = $loan->loan_amount;
        });
    }

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Methods
    public static function generateLoanNumber(): string
    {
        $prefix = 'LOAN-' . date('Y') . '-';
        $lastLoan = static::where('loan_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastLoan) {
            $lastNumber = intval(substr($lastLoan->loan_number, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function makePayment(float $amount): void
    {
        $this->total_paid += $amount;
        $this->balance -= $amount;

        if ($this->balance <= 0) {
            $this->status = 'completed';
            $this->balance = 0;
        }

        $this->save();
    }

    public function getRemainingMonthsAttribute(): int
    {
        if ($this->monthly_deduction <= 0) return 0;
        return (int) ceil($this->balance / $this->monthly_deduction);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->loan_amount <= 0) return 100;
        return ($this->total_paid / $this->loan_amount) * 100;
    }
}
