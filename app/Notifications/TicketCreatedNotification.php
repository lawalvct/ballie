<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('super-admin.support.tickets.show', $this->ticket->id);

        return (new MailMessage)
                    ->subject('New Support Ticket: ' . $this->ticket->ticket_number)
                    ->greeting('Hello Admin!')
                    ->line('A new support ticket has been created.')
                    ->line('**Ticket:** ' . $this->ticket->ticket_number)
                    ->line('**From:** ' . $this->ticket->tenant->name)
                    ->line('**Subject:** ' . $this->ticket->subject)
                    ->line('**Priority:** ' . ucfirst($this->ticket->priority))
                    ->line('**Category:** ' . $this->ticket->category->name)
                    ->action('View Ticket', $url)
                    ->line('Please respond as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'tenant_name' => $this->ticket->tenant->name,
            'subject' => $this->ticket->subject,
            'priority' => $this->ticket->priority,
            'category' => $this->ticket->category->name,
        ];
    }
}
