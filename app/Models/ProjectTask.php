<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'tenant_id',
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
        'due_date',
        'completed_at',
        'sort_order',
        'estimated_hours',
        'actual_hours',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
    ];

    // ─── Boot ─────────────────────────────────────────────

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($task) {
            if ($task->isDirty('status') && $task->status === 'done' && !$task->completed_at) {
                $task->completed_at = now();
            }
            if ($task->isDirty('status') && $task->status !== 'done') {
                $task->completed_at = null;
            }
        });
    }

    // ─── Relationships ────────────────────────────────────

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ─── Scopes ───────────────────────────────────────────

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotIn('status', ['done'])
                     ->whereNotNull('due_date')
                     ->where('due_date', '<', now());
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['todo', 'in_progress', 'review']);
    }

    // ─── Computed ─────────────────────────────────────────

    public function getIsOverdueAttribute(): bool
    {
        return !in_array($this->status, ['done'])
            && $this->due_date
            && $this->due_date->isPast();
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'todo' => 'gray',
            'in_progress' => 'blue',
            'review' => 'yellow',
            'done' => 'green',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'todo' => 'To Do',
            'in_progress' => 'In Progress',
            'review' => 'In Review',
            'done' => 'Done',
            default => ucfirst($this->status),
        };
    }
}
