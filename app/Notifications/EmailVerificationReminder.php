<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public $daysRemaining;

    /**
     * Create a new notification instance.
     */
    public function __construct($daysRemaining = 7)
    {
        $this->daysRemaining = $daysRemaining;
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
        $message = (new MailMessage)
            ->subject('Reminder: Verify Your Email Address')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We noticed that you haven\'t verified your email address yet.');

        if ($this->daysRemaining > 0) {
            $message->line("Please verify your email within the next {$this->daysRemaining} days to ensure full access to all features.");
        } else {
            $message->line('Please verify your email to ensure full access to all features and receive important updates.');
        }

        $message->action('Verify Email Now', route('verification.notice'))
            ->line('If you did not create this account, no further action is required.')
            ->salutation('Best regards, The Ballie Team');

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
            'title' => 'Verify Your Email',
            'message' => $this->daysRemaining > 0
                ? "Please verify your email within {$this->daysRemaining} days."
                : 'Please verify your email to ensure full access.',
            'action_url' => route('verification.notice'),
            'action_text' => 'Verify Email',
            'type' => 'email_verification_reminder',
            'days_remaining' => $this->daysRemaining,
        ];
    }
}
