<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'admin_id',
        'message',
        'is_internal_note',
        'is_automated',
    ];

    protected $casts = [
        'is_internal_note' => 'boolean',
        'is_automated' => 'boolean',
    ];

    /**
     * Get the ticket this reply belongs to.
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Get the user who created this reply (tenant side).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who created this reply (super admin side).
     */
    public function admin()
    {
        return $this->belongsTo(SuperAdmin::class, 'admin_id');
    }

    /**
     * Get attachments for this reply.
     */
    public function attachments()
    {
        return $this->hasMany(SupportTicketAttachment::class, 'reply_id');
    }

    /**
     * Check if reply is from admin.
     */
    public function isFromAdmin(): bool
    {
        return !is_null($this->admin_id);
    }

    /**
     * Check if reply is from user.
     */
    public function isFromUser(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Get the author of the reply.
     */
    public function getAuthorAttribute()
    {
        return $this->isFromAdmin() ? $this->admin : $this->user;
    }

    /**
     * Get the author name.
     */
    public function getAuthorNameAttribute(): string
    {
        if ($this->isFromAdmin()) {
            return $this->admin->name ?? 'Support Team';
        }

        return $this->user->name ?? 'User';
    }

    /**
     * Get the author type.
     */
    public function getAuthorTypeAttribute(): string
    {
        return $this->isFromAdmin() ? 'admin' : 'user';
    }

    /**
     * Scope for public replies only (not internal notes).
     */
    public function scopePublic($query)
    {
        return $query->where('is_internal_note', false);
    }

    /**
     * Scope for internal notes only.
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal_note', true);
    }
}
