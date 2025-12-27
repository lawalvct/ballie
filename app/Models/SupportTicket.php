<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SupportTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'tenant_id',
        'user_id',
        'category_id',
        'subject',
        'description',
        'priority',
        'status',
        'assigned_to',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'satisfaction_rating',
        'satisfaction_comment',
        'metadata',
    ];

    protected $casts = [
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'metadata' => 'array',
        'satisfaction_rating' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
        });
    }

    /**
     * Generate a unique ticket number.
     */
    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $lastTicket = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastTicket ? (int) substr($lastTicket->ticket_number, -6) + 1 : 1;

        return 'TKT-' . $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the tenant that owns the ticket.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who created the ticket.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the ticket.
     */
    public function category()
    {
        return $this->belongsTo(SupportCategory::class, 'category_id');
    }

    /**
     * Get the admin assigned to the ticket.
     */
    public function assignedAdmin()
    {
        return $this->belongsTo(SuperAdmin::class, 'assigned_to');
    }

    /**
     * Get all replies for this ticket.
     */
    public function replies()
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id')->orderBy('created_at');
    }

    /**
     * Get all attachments for this ticket.
     */
    public function attachments()
    {
        return $this->hasMany(SupportTicketAttachment::class, 'ticket_id');
    }

    /**
     * Get status history for this ticket.
     */
    public function statusHistory()
    {
        return $this->hasMany(SupportTicketStatusHistory::class, 'ticket_id')->orderBy('created_at');
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by priority.
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for open tickets.
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['new', 'open', 'in_progress', 'waiting_customer']);
    }

    /**
     * Scope for closed tickets.
     */
    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope for tickets awaiting admin response.
     */
    public function scopeAwaitingResponse($query)
    {
        return $query->whereIn('status', ['new', 'open']);
    }

    /**
     * Check if ticket is open.
     */
    public function isOpen(): bool
    {
        return in_array($this->status, ['new', 'open', 'in_progress', 'waiting_customer']);
    }

    /**
     * Check if ticket is closed.
     */
    public function isClosed(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Check if ticket can be reopened.
     */
    public function canReopen(): bool
    {
        if (!$this->closed_at) {
            return false;
        }

        // Can reopen within 30 days
        return $this->closed_at->diffInDays(now()) <= 30;
    }

    /**
     * Check if ticket has rating.
     */
    public function hasRating(): bool
    {
        return !is_null($this->satisfaction_rating);
    }

    /**
     * Get priority label with color.
     */
    public function getPriorityLabelAttribute(): array
    {
        return match($this->priority) {
            'low' => ['text' => 'Low', 'color' => 'gray'],
            'medium' => ['text' => 'Medium', 'color' => 'yellow'],
            'high' => ['text' => 'High', 'color' => 'orange'],
            'urgent' => ['text' => 'Urgent', 'color' => 'red'],
            default => ['text' => 'Unknown', 'color' => 'gray'],
        };
    }

    /**
     * Get status label with color.
     */
    public function getStatusLabelAttribute(): array
    {
        return match($this->status) {
            'new' => ['text' => 'New', 'color' => 'purple'],
            'open' => ['text' => 'Open', 'color' => 'blue'],
            'in_progress' => ['text' => 'In Progress', 'color' => 'yellow'],
            'waiting_customer' => ['text' => 'Waiting Customer', 'color' => 'orange'],
            'resolved' => ['text' => 'Resolved', 'color' => 'green'],
            'closed' => ['text' => 'Closed', 'color' => 'gray'],
            default => ['text' => 'Unknown', 'color' => 'gray'],
        };
    }

    /**
     * Get time since first response (SLA).
     */
    public function getFirstResponseTimeAttribute(): ?int
    {
        if (!$this->first_response_at) {
            return null;
        }

        return $this->created_at->diffInHours($this->first_response_at);
    }

    /**
     * Get time to resolution (SLA).
     */
    public function getResolutionTimeAttribute(): ?int
    {
        if (!$this->resolved_at) {
            return null;
        }

        return $this->created_at->diffInHours($this->resolved_at);
    }

    /**
     * Get age of ticket in hours.
     */
    public function getAgeInHoursAttribute(): int
    {
        return $this->created_at->diffInHours(now());
    }

    /**
     * Get last reply.
     */
    public function getLastReplyAttribute()
    {
        return $this->replies()->latest()->first();
    }

    /**
     * Get reply count.
     */
    public function getReplyCountAttribute(): int
    {
        return $this->replies()->count();
    }
}
