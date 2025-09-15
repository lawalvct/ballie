<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Plan;

class TenantInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $invitationData;
    public $selectedPlan;
    public $acceptUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($token, $invitationData, Plan $selectedPlan)
    {
        $this->token = $token;
        $this->invitationData = $invitationData;
        $this->selectedPlan = $selectedPlan;
        $this->acceptUrl = url("/accept-invitation/{$token}");
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're invited to join {$this->invitationData['company_name']} on Ballie",
            replyTo: [
                config('mail.from.address'),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-invitation',
            with: [
                'token' => $this->token,
                'invitationData' => $this->invitationData,
                'selectedPlan' => $this->selectedPlan,
                'acceptUrl' => $this->acceptUrl,
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
