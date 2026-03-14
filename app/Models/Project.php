<?php

namespace App\Models;

use App\Traits\HasAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, SoftDeletes, HasAudit;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'name',
        'slug',
        'project_number',
        'description',
        'status',
        'priority',
        'start_date',
        'end_date',
        'budget',
        'actual_cost',
        'currency',
        'assigned_to',
        'created_by',
        'updated_by',
        'deleted_by',
        'completed_at',
        'settings',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'completed_at' => 'datetime',
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'settings' => 'array',
    ];

    // ─── Boot ─────────────────────────────────────────────

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->name) . '-' . Str::random(5);
            }
            if (empty($project->project_number)) {
                $project->project_number = static::generateProjectNumber($project->tenant_id);
            }
        });

        static::updating(function ($project) {
            if ($project->isDirty('status') && $project->status === 'completed' && !$project->completed_at) {
                $project->completed_at = now();
            }
        });
    }

    public static function generateProjectNumber($tenantId): string
    {
        $count = static::where('tenant_id', $tenantId)->withTrashed()->count() + 1;
        return 'PRJ-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // ─── Relationships ────────────────────────────────────

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class)->orderBy('sort_order');
    }

    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class)->orderBy('sort_order');
    }

    public function notes()
    {
        return $this->hasMany(ProjectNote::class)->latest();
    }

    public function attachments()
    {
        return $this->hasMany(ProjectAttachment::class)->latest();
    }

    public function expenses()
    {
        return $this->hasMany(ProjectExpense::class)->latest();
    }

    // ─── Scopes ───────────────────────────────────────────

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
                     ->whereNotNull('end_date')
                     ->where('end_date', '<', now());
    }

    public function scopeUpcoming($query, int $days = 7)
    {
        return $query->where('status', 'active')
                     ->whereNotNull('end_date')
                     ->whereBetween('end_date', [now(), now()->addDays($days)]);
    }

    // ─── Computed Attributes ──────────────────────────────

    public function getProgressAttribute(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;
        $done = $this->tasks()->where('status', 'done')->count();
        return (int) round(($done / $total) * 100);
    }

    public function getCompletedTasksCountAttribute(): int
    {
        return $this->tasks()->where('status', 'done')->count();
    }

    public function getTotalTasksCountAttribute(): int
    {
        return $this->tasks()->count();
    }

    public function getBudgetUsedPercentAttribute(): float
    {
        if (!$this->budget || $this->budget <= 0) return 0;
        return round(($this->actual_cost / $this->budget) * 100, 1);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'active'
            && $this->end_date
            && $this->end_date->isPast();
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->end_date) return null;
        return (int) now()->diffInDays($this->end_date, false);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'active' => 'blue',
            'on_hold' => 'yellow',
            'completed' => 'green',
            'archived' => 'slate',
            default => 'gray',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'gray',
            'medium' => 'blue',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray',
        };
    }

    // ─── Helpers ──────────────────────────────────────────

    public function getDisplayNumber(): string
    {
        return $this->project_number ?? $this->slug;
    }

    public function getBillableMilestonesTotal(): float
    {
        return (float) $this->milestones()
            ->where('is_billable', true)
            ->sum('amount');
    }

    public function getUnbilledMilestonesTotal(): float
    {
        return (float) $this->milestones()
            ->where('is_billable', true)
            ->whereNull('invoice_id')
            ->whereNotNull('completed_at')
            ->sum('amount');
    }
}
