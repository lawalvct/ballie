<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(SupportTicket $ticket, string $oldStatus, string $newStatus)
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
        $statusText = ucwords(str_replace('_', ' ', $this->newStatus));

        $message = (new MailMessage)
                    ->subject('Ticket Status Updated: ' . $this->ticket->ticket_number)
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('The status of your support ticket has been updated.')
                    ->line('**Ticket:** ' . $this->ticket->ticket_number)
                    ->line('**Subject:** ' . $this->ticket->subject)
                    ->line('**Previous Status:** ' . ucwords(str_replace('_', ' ', $this->oldStatus)))
                    ->line('**New Status:** ' . $statusText);

        // Add specific messages based on status
        if ($this->newStatus === 'resolved') {
            $message->line('Your issue has been resolved! If you\'re satisfied with the resolution, please consider rating our support.');
        } elseif ($this->newStatus === 'waiting_customer') {
            $message->line('We need more information from you to proceed. Please check the ticket and provide the requested details.');
        } elseif ($this->newStatus === 'in_progress') {
            $message->line('Our support team is actively working on your ticket.');
        }

        $message->action('View Ticket', $url)
                ->line('Thank you for your patience!');

        return $message;
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
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}
