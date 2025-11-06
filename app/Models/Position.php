<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'department_id',
        'level',
        'reports_to_position_id',
        'min_salary',
        'max_salary',
        'requirements',
        'responsibilities',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'level' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the tenant that owns the position.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the department that owns the position.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the position this position reports to.
     */
    public function reportsTo(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'reports_to_position_id');
    }

    /**
     * Get the positions that report to this position.
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(Position::class, 'reports_to_position_id');
    }

    /**
     * Get the employees in this position.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the active employees in this position.
     */
    public function activeEmployees(): HasMany
    {
        return $this->hasMany(Employee::class)->where('status', 'active');
    }

    /**
     * Scope a query to only include active positions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to filter by level.
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Get the employee count for this position.
     */
    public function getEmployeeCountAttribute(): int
    {
        return $this->employees()->count();
    }

    /**
     * Get the active employee count for this position.
     */
    public function getActiveEmployeeCountAttribute(): int
    {
        return $this->activeEmployees()->count();
    }

    /**
     * Get the level name.
     */
    public function getLevelNameAttribute(): string
    {
        $levels = [
            1 => 'Entry Level',
            2 => 'Junior',
            3 => 'Mid-Level',
            4 => 'Senior',
            5 => 'Lead',
            6 => 'Manager',
            7 => 'Senior Manager',
            8 => 'Director',
            9 => 'Senior Director',
            10 => 'Executive',
        ];

        return $levels[$this->level] ?? 'Level ' . $this->level;
    }

    /**
     * Check if position has employees.
     */
    public function hasEmployees(): bool
    {
        return $this->employees()->exists();
    }

    /**
     * Check if position is at executive level.
     */
    public function isExecutive(): bool
    {
        return $this->level >= 8;
    }

    /**
     * Check if position is at management level.
     */
    public function isManagement(): bool
    {
        return $this->level >= 6;
    }

    /**
     * Get the salary range formatted.
     */
    public function getSalaryRangeAttribute(): string
    {
        if (!$this->min_salary && !$this->max_salary) {
            return 'Not specified';
        }

        if (!$this->max_salary) {
            return 'From ' . number_format($this->min_salary, 2);
        }

        if (!$this->min_salary) {
            return 'Up to ' . number_format($this->max_salary, 2);
        }

        return number_format($this->min_salary, 2) . ' - ' . number_format($this->max_salary, 2);
    }
}
