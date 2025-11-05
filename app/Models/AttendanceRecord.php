<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'employee_id', 'attendance_date',
        'clock_in', 'clock_out', 'clock_in_ip', 'clock_out_ip',
        'clock_in_location', 'clock_out_location', 'clock_in_notes', 'clock_out_notes',
        'scheduled_in', 'scheduled_out', 'late_minutes', 'early_out_minutes',
        'work_hours_minutes', 'break_minutes', 'overtime_minutes',
        'status', 'absence_reason', 'is_approved', 'approved_by', 'approved_at',
        'shift_id', 'remarks', 'admin_notes', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'scheduled_in' => 'datetime',
        'scheduled_out' => 'datetime',
        'late_minutes' => 'integer',
        'early_out_minutes' => 'integer',
        'work_hours_minutes' => 'integer',
        'break_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(ShiftSchedule::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
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
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    public function scopeOnLeave($query)
    {
        return $query->where('status', 'on_leave');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month);
    }

    // Methods
    public function clockIn(?string $location = null, ?string $ip = null, ?string $notes = null): void
    {
        $this->clock_in = now();
        $this->clock_in_location = $location;
        $this->clock_in_ip = $ip;
        $this->clock_in_notes = $notes;
        $this->status = 'present';

        // Calculate if late
        if ($this->scheduled_in && $this->clock_in > $this->scheduled_in) {
            $this->late_minutes = $this->scheduled_in->diffInMinutes($this->clock_in);

            // Apply grace period if shift exists
            if ($this->shift && $this->late_minutes <= $this->shift->late_grace_minutes) {
                $this->late_minutes = 0;
            } else {
                $this->status = 'late';
            }
        }

        $this->save();
    }

    public function clockOut(?string $location = null, ?string $ip = null, ?string $notes = null): void
    {
        if (!$this->clock_in) {
            throw new \Exception('Cannot clock out without clocking in first.');
        }

        $this->clock_out = now();
        $this->clock_out_location = $location;
        $this->clock_out_ip = $ip;
        $this->clock_out_notes = $notes;

        // Calculate work hours
        $totalMinutes = $this->clock_in->diffInMinutes($this->clock_out);
        $this->work_hours_minutes = $totalMinutes - $this->break_minutes;

        // Calculate early out
        if ($this->scheduled_out && $this->clock_out < $this->scheduled_out) {
            $this->early_out_minutes = $this->clock_out->diffInMinutes($this->scheduled_out);

            // Apply grace period if shift exists
            if ($this->shift && $this->early_out_minutes <= $this->shift->early_out_grace_minutes) {
                $this->early_out_minutes = 0;
            }
        }

        // Calculate overtime
        if ($this->scheduled_out && $this->clock_out > $this->scheduled_out) {
            $this->overtime_minutes = $this->scheduled_out->diffInMinutes($this->clock_out);
        }

        $this->save();
    }

    public function markAbsent(string $reason = ''): void
    {
        $this->status = 'absent';
        $this->absence_reason = $reason;
        $this->save();
    }

    public function markHalfDay(): void
    {
        $this->status = 'half_day';
        $this->work_hours_minutes = ($this->shift ? $this->shift->work_hours : 8) * 30; // Half of standard hours
        $this->save();
    }

    public function approve(int $userId): void
    {
        $this->is_approved = true;
        $this->approved_by = $userId;
        $this->approved_at = now();
        $this->save();
    }

    public function calculateWorkHours(): float
    {
        return round($this->work_hours_minutes / 60, 2);
    }

    public function calculateOvertimeHours(): float
    {
        return round($this->overtime_minutes / 60, 2);
    }

    public function isLate(): bool
    {
        return $this->late_minutes > 0;
    }

    public function isEarlyOut(): bool
    {
        return $this->early_out_minutes > 0;
    }

    public function hasOvertime(): bool
    {
        return $this->overtime_minutes > 0;
    }
}
