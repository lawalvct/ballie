<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'tenant_id',
        'title',
        'description',
        'due_date',
        'completed_at',
        'amount',
        'is_billable',
        'invoice_id',
        'sort_order',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'amount' => 'decimal:2',
        'is_billable' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Voucher::class, 'invoice_id');
    }

    // ─── Scopes ───────────────────────────────────────────

    public function scopeBillable($query)
    {
        return $query->where('is_billable', true);
    }

    public function scopeUnbilled($query)
    {
        return $query->where('is_billable', true)
                     ->whereNull('invoice_id');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    // ─── Computed ─────────────────────────────────────────

    public function getIsCompletedAttribute(): bool
    {
        return !is_null($this->completed_at);
    }

    public function getIsBilledAttribute(): bool
    {
        return !is_null($this->invoice_id);
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_billed) return 'Billed';
        if ($this->is_completed) return 'Completed';
        if ($this->due_date && $this->due_date->isPast()) return 'Overdue';
        return 'Pending';
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->is_billed) return 'green';
        if ($this->is_completed) return 'blue';
        if ($this->due_date && $this->due_date->isPast()) return 'red';
        return 'gray';
    }
}
