<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OvertimeRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'employee_id', 'overtime_number', 'overtime_date',
        'calculation_method', 'start_time', 'end_time', 'total_hours', 'hourly_rate', 'multiplier',
        'total_amount', 'reason', 'work_description', 'overtime_type',
        'status', 'approved_by', 'approved_at', 'approval_remarks',
        'rejected_by', 'rejected_at', 'rejection_reason',
        'payroll_run_id', 'is_paid', 'paid_date',
        'created_by', 'updated_by'
    ];

    protected $casts = [
        'overtime_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_hours' => 'integer',
        'hourly_rate' => 'decimal:2',
        'multiplier' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'is_paid' => 'boolean',
        'paid_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($overtime) {
            if (empty($overtime->overtime_number)) {
                $overtime->overtime_number = static::generateOvertimeNumber($overtime->tenant_id);
            }

            // Calculate total amount based on method
            if ($overtime->calculation_method === 'hourly') {
                $overtime->total_amount = $overtime->total_hours * $overtime->hourly_rate * $overtime->multiplier;
            }
            // For fixed, total_amount is already set from user input
        });

        static::updating(function ($overtime) {
            // Recalculate only if hourly method and relevant fields change
            if ($overtime->calculation_method === 'hourly' && $overtime->isDirty(['total_hours', 'hourly_rate', 'multiplier'])) {
                $overtime->total_amount = $overtime->total_hours * $overtime->hourly_rate * $overtime->multiplier;
            }
        });
    }

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false)
            ->where('status', 'approved');
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('overtime_date', $year)
            ->whereMonth('overtime_date', $month);
    }

    // Methods
    public static function generateOvertimeNumber($tenantId): string
    {
        $prefix = 'OT-' . date('Y') . '-';
        $lastOvertime = static::where('tenant_id', $tenantId)
            ->where('overtime_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOvertime) {
            $lastNumber = intval(substr($lastOvertime->overtime_number, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function calculateHours(): void
    {
        if ($this->start_time && $this->end_time) {
            $this->total_hours = $this->start_time->diffInHours($this->end_time);
            $this->save();
        }
    }

    public function approve(int $userId, ?float $approvedHours = null, ?string $remarks = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = now();
        $this->approval_remarks = $remarks;

        // If approved hours provided and different from recorded hours, recalculate
        if ($approvedHours !== null && $this->calculation_method === 'hourly' && $approvedHours != $this->total_hours) {
            $this->total_hours = $approvedHours;
            // Recalculate total amount with approved hours
            $this->total_amount = $this->total_hours * $this->hourly_rate * $this->multiplier;
        }

        $this->save();

        return true;
    }

    public function reject(int $userId, string $reason): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->status = 'rejected';
        $this->rejected_by = $userId;
        $this->rejected_at = now();
        $this->rejection_reason = $reason;
        $this->save();

        return true;
    }

    public function markAsPaid(int $payrollRunId): void
    {
        $this->is_paid = true;
        $this->paid_date = now();
        $this->payroll_run_id = $payrollRunId;
        $this->status = 'paid';
        $this->save();
    }

    public function getMultiplierText(): string
    {
        return match($this->overtime_type) {
            'weekday' => '1.5x (Weekday)',
            'weekend' => '2.0x (Weekend)',
            'holiday' => '2.5x (Holiday)',
            'emergency' => '2.0x (Emergency)',
            default => $this->multiplier . 'x',
        };
    }
}
