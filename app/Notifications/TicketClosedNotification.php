<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketClosedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;
    public $closedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(SupportTicket $ticket, $closedBy = null)
    {
        $this->ticket = $ticket;
        $this->closedBy = $closedBy;
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
        $url = route('tenant.support.tickets.show', $this->ticket->id);

        return (new MailMessage)
                    ->subject('Ticket Closed: ' . $this->ticket->ticket_number)
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('Your support ticket has been closed.')
                    ->line('**Ticket:** ' . $this->ticket->ticket_number)
                    ->line('**Subject:** ' . $this->ticket->subject)
                    ->line('**Status:** Closed')
                    ->line('We hope your issue has been resolved to your satisfaction.')
                    ->action('View Ticket', $url)
                    ->line('If you need further assistance, you can reopen this ticket within 30 days or create a new one.')
                    ->line('Please rate our support to help us improve!');
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
            'subject' => $this->ticket->subject,
            'closed_at' => $this->ticket->closed_at,
        ];
    }
}
