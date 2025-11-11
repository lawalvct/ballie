<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementRecipient extends Model
{
    protected $fillable = [
        'announcement_id', 'employee_id', 'email_sent', 'email_sent_at',
        'sms_sent', 'sms_sent_at', 'acknowledged', 'acknowledged_at',
        'acknowledgment_note', 'read', 'read_at'
    ];

    protected $casts = [
        'email_sent' => 'boolean',
        'sms_sent' => 'boolean',
        'acknowledged' => 'boolean',
        'read' => 'boolean',
        'email_sent_at' => 'datetime',
        'sms_sent_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function announcement(): BelongsTo
    {
        return $this->belongsTo(EmployeeAnnouncement::class, 'announcement_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Helper Methods
    public function markEmailSent(): void
    {
        $this->update([
            'email_sent' => true,
            'email_sent_at' => now()
        ]);
    }

    public function markSmsSent(): void
    {
        $this->update([
            'sms_sent' => true,
            'sms_sent_at' => now()
        ]);
    }

    public function markAsRead(): void
    {
        $this->update([
            'read' => true,
            'read_at' => now()
        ]);
    }

    public function markAsAcknowledged(string $note = null): void
    {
        $this->update([
            'acknowledged' => true,
            'acknowledged_at' => now(),
            'acknowledgment_note' => $note
        ]);
    }
}
