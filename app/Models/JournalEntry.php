<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    protected $fillable = [
        'reference_number',
        'transaction_date',
        'description',
        'reference_type',
        'reference_id',
        'total_debit',
        'total_credit',
        'status',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($entry) {
            if (empty($entry->reference_number)) {
                $entry->reference_number = static::generateReferenceNumber();
            }
        });

        static::saved(function ($entry) {
            $entry->recalculateTotals();
        });
    }

    // Relationships
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalEntryDetails(): HasMany
    {
        return $this->hasMany(JournalEntryDetail::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    // Methods
    public static function generateReferenceNumber(): string
    {
        $prefix = 'JE-' . date('Y') . '-';
        $lastEntry = static::where('reference_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEntry) {
            $lastNumber = intval(substr($lastEntry->reference_number, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    public function recalculateTotals(): void
    {
        $this->total_debit = $this->journalEntryDetails()->sum('debit_amount');
        $this->total_credit = $this->journalEntryDetails()->sum('credit_amount');
        $this->saveQuietly();
    }

    public function isBalanced(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    public function post(): bool
    {
        if (!$this->isBalanced()) {
            throw new \Exception('Journal entry is not balanced');
        }

        $this->status = 'posted';
        return $this->save();
    }

    public function cancel(): bool
    {
        if ($this->status === 'posted') {
            throw new \Exception('Cannot cancel a posted journal entry');
        }

        $this->status = 'cancelled';
        return $this->save();
    }
}
