<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicHoliday extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'holiday_date', 'description',
        'is_recurring', 'recurring_day', 'recurring_month',
        'is_paid', 'is_working_day', 'overtime_multiplier',
        'is_active', 'created_by'
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'is_recurring' => 'boolean',
        'recurring_day' => 'integer',
        'recurring_month' => 'integer',
        'is_paid' => 'boolean',
        'is_working_day' => 'boolean',
        'overtime_multiplier' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForYear($query, $year)
    {
        return $query->whereYear('holiday_date', $year);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('holiday_date', '>=', now()->toDateString())
            ->orderBy('holiday_date');
    }

    // Methods
    public static function createDefaultHolidays($tenantId, $year = null): void
    {
        $year = $year ?? date('Y');

        $holidays = [
            ['name' => 'New Year Day', 'month' => 1, 'day' => 1],
            ['name' => 'Good Friday', 'month' => 4, 'day' => 7, 'recurring' => false], // Varies
            ['name' => 'Easter Monday', 'month' => 4, 'day' => 10, 'recurring' => false], // Varies
            ['name' => 'Workers Day', 'month' => 5, 'day' => 1],
            ['name' => 'Democracy Day', 'month' => 6, 'day' => 12],
            ['name' => 'Eid-el-Fitr', 'month' => 4, 'day' => 21, 'recurring' => false], // Varies
            ['name' => 'Eid-el-Kabir', 'month' => 6, 'day' => 28, 'recurring' => false], // Varies
            ['name' => 'Independence Day', 'month' => 10, 'day' => 1],
            ['name' => 'Christmas Day', 'month' => 12, 'day' => 25],
            ['name' => 'Boxing Day', 'month' => 12, 'day' => 26],
        ];

        foreach ($holidays as $holiday) {
            static::create([
                'tenant_id' => $tenantId,
                'name' => $holiday['name'],
                'holiday_date' => sprintf('%d-%02d-%02d', $year, $holiday['month'], $holiday['day']),
                'is_recurring' => $holiday['recurring'] ?? true,
                'recurring_month' => $holiday['month'],
                'recurring_day' => $holiday['day'],
                'is_paid' => true,
                'is_working_day' => false,
                'overtime_multiplier' => 2.5,
                'is_active' => true,
            ]);
        }
    }

    public function generateNextYearOccurrence(): ?self
    {
        if (!$this->is_recurring) {
            return null;
        }

        $nextYear = $this->holiday_date->year + 1;
        $nextDate = sprintf('%d-%02d-%02d', $nextYear, $this->recurring_month, $this->recurring_day);

        // Check if already exists
        $exists = static::where('tenant_id', $this->tenant_id)
            ->where('holiday_date', $nextDate)
            ->exists();

        if ($exists) {
            return null;
        }

        return static::create([
            'tenant_id' => $this->tenant_id,
            'name' => $this->name,
            'holiday_date' => $nextDate,
            'description' => $this->description,
            'is_recurring' => true,
            'recurring_month' => $this->recurring_month,
            'recurring_day' => $this->recurring_day,
            'is_paid' => $this->is_paid,
            'is_working_day' => $this->is_working_day,
            'overtime_multiplier' => $this->overtime_multiplier,
            'is_active' => true,
        ]);
    }

    public static function isHoliday($tenantId, $date): bool
    {
        return static::where('tenant_id', $tenantId)
            ->where('holiday_date', $date)
            ->where('is_active', true)
            ->exists();
    }
}
