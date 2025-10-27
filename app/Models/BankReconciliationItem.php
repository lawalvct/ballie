<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankReconciliationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_reconciliation_id',
        'voucher_entry_id',
        'transaction_date',
        'transaction_type',
        'reference_number',
        'description',
        'debit_amount',
        'credit_amount',
        'status',
        'cleared_date',
        'bank_statement_date',
        'bank_reference',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'cleared_date' => 'date',
        'bank_statement_date' => 'date',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($item) {
            // Update reconciliation statistics when item status changes
            if ($item->isDirty('status')) {
                $item->reconciliation->updateStatistics();
            }
        });

        static::deleted(function ($item) {
            // Update reconciliation statistics when item is deleted
            $item->reconciliation->updateStatistics();
        });
    }

    // Relationships
    public function reconciliation()
    {
        return $this->belongsTo(BankReconciliation::class, 'bank_reconciliation_id');
    }

    public function voucherEntry()
    {
        return $this->belongsTo(VoucherEntry::class);
    }

    // Scopes
    public function scopeCleared($query)
    {
        return $query->where('status', 'cleared');
    }

    public function scopeUncleared($query)
    {
        return $query->where('status', 'uncleared');
    }

    public function scopeExcluded($query)
    {
        return $query->where('status', 'excluded');
    }

    // Methods

    /**
     * Mark as cleared
     */
    public function markAsCleared($clearedDate = null)
    {
        $this->update([
            'status' => 'cleared',
            'cleared_date' => $clearedDate ?? now(),
        ]);

        return $this;
    }

    /**
     * Mark as uncleared
     */
    public function markAsUncleared()
    {
        $this->update([
            'status' => 'uncleared',
            'cleared_date' => null,
        ]);

        return $this;
    }

    /**
     * Get transaction amount (positive for debit, negative for credit)
     */
    public function getTransactionAmount()
    {
        return $this->debit_amount - $this->credit_amount;
    }

    /**
     * Get formatted transaction type
     */
    public function getTransactionTypeDisplayAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->transaction_type));
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'cleared' => 'bg-green-100 text-green-800',
            'uncleared' => 'bg-yellow-100 text-yellow-800',
            'excluded' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
