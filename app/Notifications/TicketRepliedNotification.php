<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;
    public $reply;
    public $isAdminReply;

    /**
     * Create a new notification instance.
     */
    public function __construct(SupportTicket $ticket, SupportTicketReply $reply, bool $isAdminReply = false)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
        $this->isAdminReply = $isAdminReply;
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
        if ($this->isAdminReply) {
            // Notification to tenant user
            $url = route('tenant.support.tickets.show', $this->ticket->id);
            $replierName = $this->reply->admin->name ?? 'Support Team';

            return (new MailMessage)
                        ->subject('Reply to Your Ticket: ' . $this->ticket->ticket_number)
                        ->greeting('Hello ' . $notifiable->name . '!')
                        ->line('You have received a reply to your support ticket.')
                        ->line('**Ticket:** ' . $this->ticket->ticket_number)
                        ->line('**Subject:** ' . $this->ticket->subject)
                        ->line('**From:** ' . $replierName)
                        ->line('**Message:**')
                        ->line(substr($this->reply->message, 0, 200) . '...')
                        ->action('View Full Reply', $url)
                        ->line('Thank you for contacting support!');
        } else {
            // Notification to admin
            $url = route('super-admin.support.tickets.show', $this->ticket->id);
            $replierName = $this->reply->user->name ?? 'Customer';

            return (new MailMessage)
                        ->subject('Customer Reply: ' . $this->ticket->ticket_number)
                        ->greeting('Hello Admin!')
                        ->line('A customer has replied to their support ticket.')
                        ->line('**Ticket:** ' . $this->ticket->ticket_number)
                        ->line('**From:** ' . $this->ticket->tenant->name . ' - ' . $replierName)
                        ->line('**Subject:** ' . $this->ticket->subject)
                        ->line('**Message:**')
                        ->line(substr($this->reply->message, 0, 200) . '...')
                        ->action('View & Reply', $url)
                        ->line('Please respond promptly.');
        }
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
            'reply_id' => $this->reply->id,
            'is_admin_reply' => $this->isAdminReply,
            'subject' => $this->ticket->subject,
            'message_preview' => substr($this->reply->message, 0, 100),
        ];
    }
}
