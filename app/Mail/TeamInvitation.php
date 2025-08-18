<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tenant;
    public $user;
    public $role;

    /**
     * Create a new message instance.
     */
    public function __construct(Tenant $tenant, User $user, string $role)
    {
        $this->tenant = $tenant;
        $this->user = $user;
        $this->role = $role;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You've been invited to join {$this->tenant->name} on Ballie",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.team-invitation',
            with: [
                'tenant' => $this->tenant,
                'user' => $this->user,
                'role' => $this->role,
                'acceptUrl' => route('tenant.invitation.accept', [
                    'tenant' => $this->tenant->slug,
                    'token' => encrypt($this->user->id . '|' . $this->tenant->id)
                ])
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
