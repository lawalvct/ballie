<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmployeeAnnouncement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'created_by', 'title', 'message', 'priority',
        'delivery_method', 'recipient_type', 'department_ids', 'employee_ids',
        'status', 'scheduled_at', 'sent_at', 'total_recipients',
        'email_sent_count', 'sms_sent_count', 'failed_count', 'error_message',
        'requires_acknowledgment', 'expires_at', 'attachment_path'
    ];

    protected $casts = [
        'department_ids' => 'array',
        'employee_ids' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'expires_at' => 'date',
        'requires_acknowledgment' => 'boolean',
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

    public function recipients(): HasMany
    {
        return $this->hasMany(AnnouncementRecipient::class, 'announcement_id');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'announcement_recipients')
            ->withPivot(['email_sent', 'sms_sent', 'acknowledged', 'read', 'email_sent_at', 'sms_sent_at', 'acknowledged_at', 'read_at'])
            ->withTimestamps();
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'scheduled']);
    }

    // Helper Methods
    public function canBeSent(): bool
    {
        return in_array($this->status, ['draft', 'scheduled', 'failed']);
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'failed']);
    }

    public function canBeDeleted(): bool
    {
        return in_array($this->status, ['draft', 'failed']);
    }

    public function markAsSending(): void
    {
        $this->update(['status' => 'sending']);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage
        ]);
    }

    public function getTargetedEmployees()
    {
        $query = Employee::where('tenant_id', $this->tenant_id)
            ->where('status', 'active');

        if ($this->recipient_type === 'department' && !empty($this->department_ids)) {
            $query->whereIn('department_id', $this->department_ids);
        } elseif ($this->recipient_type === 'selected' && !empty($this->employee_ids)) {
            $query->whereIn('id', $this->employee_ids);
        }

        return $query->get();
    }

    public function getPriorityBadgeClass(): string
    {
        return match($this->priority) {
            'urgent' => 'bg-red-100 text-red-800',
            'high' => 'bg-orange-100 text-orange-800',
            'normal' => 'bg-blue-100 text-blue-800',
            'low' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'sent' => 'bg-green-100 text-green-800',
            'sending' => 'bg-blue-100 text-blue-800',
            'scheduled' => 'bg-purple-100 text-purple-800',
            'failed' => 'bg-red-100 text-red-800',
            'draft' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getAcknowledgmentRate(): float
    {
        if (!$this->requires_acknowledgment || $this->total_recipients === 0) {
            return 0;
        }

        $acknowledgedCount = $this->recipients()->where('acknowledged', true)->count();
        return ($acknowledgedCount / $this->total_recipients) * 100;
    }

    public function getReadRate(): float
    {
        if ($this->total_recipients === 0) {
            return 0;
        }

        $readCount = $this->recipients()->where('read', true)->count();
        return ($readCount / $this->total_recipients) * 100;
    }
}
