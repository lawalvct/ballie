<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhysicalStockVoucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'voucher_number',
        'voucher_date',
        'reference_number',
        'adjustment_type',
        'total_items',
        'total_adjustments',
        'remarks',
        'status',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'total_adjustments' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_CANCELLED = 'cancelled';

    const ADJUSTMENT_TYPE_SHORTAGE = 'shortage';
    const ADJUSTMENT_TYPE_EXCESS = 'excess';
    const ADJUSTMENT_TYPE_MIXED = 'mixed';

    /**
     * Get the tenant that owns the physical stock voucher.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who created the voucher.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the voucher.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who approved the voucher.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the entries for the voucher.
     */
    public function entries(): HasMany
    {
        return $this->hasMany(PhysicalStockEntry::class);
    }

    /**
     * Get the stock movements created from this voucher.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'source_transaction_id')
            ->where('source_transaction_type', self::class);
    }

    /**
     * Generate a unique voucher number.
     */
    public static function generateVoucherNumber($tenantId, $date = null)
    {
        $date = $date ?? now();

        // Ensure $date is a Carbon instance
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        $prefix = 'PSV';
        $year = $date->format('Y');
        $month = $date->format('m');

        $lastVoucher = self::where('tenant_id', $tenantId)
            ->whereYear('voucher_date', $year)
            ->whereMonth('voucher_date', $month)
            ->orderBy('voucher_number', 'desc')
            ->first();

        if (!$lastVoucher) {
            $sequence = 1;
        } else {
            // Extract sequence from last voucher number (PSV/2024/09/001)
            $parts = explode('/', $lastVoucher->voucher_number);
            $sequence = isset($parts[3]) ? intval($parts[3]) + 1 : 1;
        }

        return sprintf('%s/%s/%s/%03d', $prefix, $year, $month, $sequence);
    }

    /**
     * Calculate total adjustments based on entries.
     */
    public function calculateTotalAdjustments()
    {
        $total = $this->entries->sum(function ($entry) {
            return abs($entry->difference_quantity) * $entry->current_rate;
        });

        $this->update(['total_adjustments' => $total]);
        return $total;
    }

    /**
     * Calculate total items count.
     */
    public function calculateTotalItems()
    {
        $count = $this->entries->count();
        $this->update(['total_items' => $count]);
        return $count;
    }

    /**
     * Determine adjustment type based on entries.
     */
    public function determineAdjustmentType()
    {
        $entries = $this->entries;
        $hasShortage = $entries->where('difference_quantity', '<', 0)->count() > 0;
        $hasExcess = $entries->where('difference_quantity', '>', 0)->count() > 0;

        if ($hasShortage && $hasExcess) {
            $type = self::ADJUSTMENT_TYPE_MIXED;
        } elseif ($hasShortage) {
            $type = self::ADJUSTMENT_TYPE_SHORTAGE;
        } elseif ($hasExcess) {
            $type = self::ADJUSTMENT_TYPE_EXCESS;
        } else {
            $type = self::ADJUSTMENT_TYPE_MIXED;
        }

        $this->update(['adjustment_type' => $type]);
        return $type;
    }

    /**
     * Approve the voucher and create stock movements.
     */
    public function approve($userId = null)
    {
        if ($this->status === self::STATUS_APPROVED) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId ?? auth()->id(),
            'approved_at' => now(),
        ]);

        // Create stock movements for each entry
        foreach ($this->entries as $entry) {
            $entry->createStockMovement();
        }

        return true;
    }

    /**
     * Cancel the voucher.
     */
    public function cancel()
    {
        if ($this->status === self::STATUS_APPROVED) {
            throw new \Exception('Cannot cancel an approved voucher.');
        }

        $this->update(['status' => self::STATUS_CANCELLED]);
        return true;
    }

    /**
     * Check if voucher can be edited.
     */
    public function canEdit(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PENDING]);
    }

    /**
     * Check if voucher can be approved.
     */
    public function canApprove(): bool
    {
        return $this->status === self::STATUS_PENDING && $this->entries->count() > 0;
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_CANCELLED => 'red',
            default => 'gray'
        };
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown'
        };
    }

    /**
     * Get adjustment type display name.
     */
    public function getAdjustmentTypeDisplayAttribute(): string
    {
        return match($this->adjustment_type) {
            self::ADJUSTMENT_TYPE_SHORTAGE => 'Stock Shortage',
            self::ADJUSTMENT_TYPE_EXCESS => 'Stock Excess',
            self::ADJUSTMENT_TYPE_MIXED => 'Mixed Adjustments',
            default => 'Unknown'
        };
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by date range.
     */
    public function scopeDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('voucher_date', [$fromDate, $toDate]);
    }

    /**
     * Scope for current month vouchers.
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('voucher_date', now()->month)
                    ->whereYear('voucher_date', now()->year);
    }
}
