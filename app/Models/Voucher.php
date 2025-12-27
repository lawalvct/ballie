<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Traits\HasAudit;
use App\Traits\HasPosting;

class Voucher extends Model
{
    use HasFactory, HasAudit, HasPosting;

    protected $fillable = [
        'tenant_id',
        'voucher_type_id',
        'voucher_number',
        'voucher_date',
        'reference_number',
        'narration',
        'total_amount',
        'status',
        'created_by',
        'updated_by',
        'posted_at',
        'posted_by',
        'meta_data',
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'total_amount' => 'decimal:2',
        'posted_at' => 'datetime',
        'meta_data' => 'array',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_POSTED = 'posted';
    const STATUS_APPROVED = 'approved';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function voucherType()
    {
        return $this->belongsTo(VoucherType::class);
    }

    public function entries()
    {
        return $this->hasMany(VoucherEntry::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    // Scopes
    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByType($query, $typeCode)
    {
        return $query->whereHas('voucherType', function ($q) use ($typeCode) {
            $q->where('code', $typeCode);
        });
    }

    public function scopeDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('voucher_date', [$fromDate, $toDate]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('voucher_date', [$startDate, $endDate]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('voucher_date', now()->month)
                    ->whereYear('voucher_date', now()->year);
    }


    // Methods
    public function post($userId)
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Only draft vouchers can be posted');
        }

        if (!$this->isBalanced()) {
            throw new \Exception('Voucher is not balanced');
        }

        $this->update([
            'status' => 'posted',
            'posted_at' => now(),
            'posted_by' => $userId,
        ]);

        return $this;
    }

    public function unpost()
    {
        if ($this->status !== 'posted') {
            throw new \Exception('Only posted vouchers can be unposted');
        }

        $this->update([
            'status' => 'draft',
            'posted_at' => null,
            'posted_by' => null,
        ]);

        return $this;
    }

    public function isBalanced()
    {
        $totalDebits = $this->entries()->sum('debit_amount');
        $totalCredits = $this->entries()->sum('credit_amount');

        return abs($totalDebits - $totalCredits) < 0.01; // Allow for rounding differences
    }

    public function getTotalDebits()
    {
        return $this->entries()->sum('debit_amount');
    }

    public function getTotalCredits()
    {
        return $this->entries()->sum('credit_amount');
    }

    public function getDisplayNumber()
    {
        return $this->voucherType->abbreviation . '-' . $this->voucher_number;
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_POSTED => 'Posted',

            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function items()
{
    return $this->hasMany(InvoiceItem::class);
}

public function updatedBy()
{
    return $this->belongsTo(User::class, 'updated_by');
}


}
