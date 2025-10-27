<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankReconciliation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'bank_id',
        'reconciliation_date',
        'statement_number',
        'statement_start_date',
        'statement_end_date',
        'opening_balance',
        'closing_balance_per_bank',
        'closing_balance_per_books',
        'difference',
        'status',
        'total_transactions',
        'reconciled_transactions',
        'unreconciled_transactions',
        'bank_charges',
        'interest_earned',
        'other_adjustments',
        'notes',
        'discrepancy_notes',
        'created_by',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'reconciliation_date' => 'date',
        'statement_start_date' => 'date',
        'statement_end_date' => 'date',
        'opening_balance' => 'decimal:2',
        'closing_balance_per_bank' => 'decimal:2',
        'closing_balance_per_books' => 'decimal:2',
        'difference' => 'decimal:2',
        'bank_charges' => 'decimal:2',
        'interest_earned' => 'decimal:2',
        'other_adjustments' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reconciliation) {
            $reconciliation->total_transactions = 0;
            $reconciliation->reconciled_transactions = 0;
            $reconciliation->unreconciled_transactions = 0;
        });

        static::updating(function ($reconciliation) {
            // Auto-calculate difference
            $reconciliation->difference = $reconciliation->closing_balance_per_bank - $reconciliation->closing_balance_per_books;

            // Update bank's last reconciliation date when completed
            if ($reconciliation->status === 'completed' && $reconciliation->isDirty('status')) {
                $reconciliation->completed_at = now();
                $reconciliation->completed_by = auth()->id();

                // Update bank record
                if ($reconciliation->bank) {
                    $reconciliation->bank->update([
                        'last_reconciliation_date' => $reconciliation->reconciliation_date,
                        'last_reconciled_balance' => $reconciliation->closing_balance_per_bank,
                    ]);
                }
            }
        });
    }

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function items()
    {
        return $this->hasMany(BankReconciliationItem::class);
    }

    public function clearedItems()
    {
        return $this->hasMany(BankReconciliationItem::class)->where('status', 'cleared');
    }

    public function unclearedItems()
    {
        return $this->hasMany(BankReconciliationItem::class)->where('status', 'uncleared');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeForBank($query, $bankId)
    {
        return $query->where('bank_id', $bankId);
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('reconciliation_date', [$startDate, $endDate]);
    }

    // Methods

    /**
     * Check if reconciliation is balanced
     */
    public function isBalanced()
    {
        return abs($this->difference) < 0.01;
    }

    /**
     * Get reconciliation progress percentage
     */
    public function getProgressPercentage()
    {
        if ($this->total_transactions == 0) {
            return 0;
        }

        return round(($this->reconciled_transactions / $this->total_transactions) * 100, 2);
    }

    /**
     * Update reconciliation statistics
     */
    public function updateStatistics()
    {
        $this->total_transactions = $this->items()->count();
        $this->reconciled_transactions = $this->clearedItems()->count();
        $this->unreconciled_transactions = $this->unclearedItems()->count();
        $this->save();
    }

    /**
     * Calculate adjusted bank balance
     */
    public function getAdjustedBankBalance()
    {
        $balance = $this->closing_balance_per_bank;
        $balance += $this->interest_earned;
        $balance -= $this->bank_charges;
        $balance += $this->other_adjustments;

        return $balance;
    }

    /**
     * Calculate adjusted book balance
     */
    public function getAdjustedBookBalance()
    {
        $balance = $this->closing_balance_per_books;

        // Add uncleared deposits (debits)
        $unclearedDebits = $this->unclearedItems()
            ->where('debit_amount', '>', 0)
            ->sum('debit_amount');

        // Subtract uncleared payments (credits)
        $unclearedCredits = $this->unclearedItems()
            ->where('credit_amount', '>', 0)
            ->sum('credit_amount');

        $balance += $unclearedDebits - $unclearedCredits;

        return $balance;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get formatted period
     */
    public function getPeriodAttribute()
    {
        return $this->statement_start_date->format('M d, Y') . ' - ' . $this->statement_end_date->format('M d, Y');
    }

    /**
     * Check if can be edited
     */
    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'in_progress']);
    }

    /**
     * Check if can be completed
     */
    public function canBeCompleted()
    {
        return $this->status !== 'completed' && $this->isBalanced();
    }

    /**
     * Check if can be deleted
     */
    public function canBeDeleted()
    {
        return $this->status !== 'completed';
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted()
    {
        if (!$this->canBeCompleted()) {
            throw new \Exception('Reconciliation cannot be completed. Ensure it is balanced.');
        }

        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ]);

        return $this;
    }

    /**
     * Cancel reconciliation
     */
    public function cancel()
    {
        if ($this->status === 'completed') {
            throw new \Exception('Cannot cancel a completed reconciliation.');
        }

        $this->update(['status' => 'cancelled']);

        return $this;
    }

    /**
     * Get summary data
     */
    public function getSummary()
    {
        return [
            'bank_name' => $this->bank->bank_name,
            'period' => $this->period,
            'opening_balance' => $this->opening_balance,
            'closing_balance_bank' => $this->closing_balance_per_bank,
            'closing_balance_books' => $this->closing_balance_per_books,
            'difference' => $this->difference,
            'is_balanced' => $this->isBalanced(),
            'progress' => $this->getProgressPercentage(),
            'total_transactions' => $this->total_transactions,
            'reconciled' => $this->reconciled_transactions,
            'unreconciled' => $this->unreconciled_transactions,
            'status' => $this->status,
        ];
    }
}
