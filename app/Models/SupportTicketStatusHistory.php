<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'support_ticket_status_history';

    const UPDATED_AT = null;

    protected $fillable = [
        'ticket_id',
        'old_status',
        'new_status',
        'changed_by_type',
        'changed_by_id',
        'notes',
    ];

    /**
     * Get the ticket this history belongs to.
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Get the user who made the change (polymorphic).
     */
    public function changedBy()
    {
        return $this->morphTo('changed_by', 'changed_by_type', 'changed_by_id');
    }

    /**
     * Get the status change label.
     */
    public function getChangeDescriptionAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->old_status)) . ' → ' .
               ucfirst(str_replace('_', ' ', $this->new_status));
    }

    /**
     * Get who made the change (name).
     */
    public function getChangedByNameAttribute(): string
    {
        if ($this->changed_by_type === 'App\Models\SuperAdmin') {
            $admin = SuperAdmin::find($this->changed_by_id);
            return $admin ? $admin->name : 'Support Team';
        }

        $user = User::find($this->changed_by_id);
        return $user ? $user->name : 'User';
    }

    /**
     * Record a status change.
     */
    public static function recordChange(SupportTicket $ticket, string $oldStatus, string $newStatus, $changedBy, ?string $notes = null): void
    {
        self::create([
            'ticket_id' => $ticket->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by_type' => get_class($changedBy),
            'changed_by_id' => $changedBy->id,
            'notes' => $notes,
        ]);
    }
}
