<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    public $verificationCode;

    /**
     * Create a new notification instance.
     */
    public function __construct($verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Welcome to Ballie - Verify Your Email')
                    ->greeting('Welcome to Ballie, ' . $notifiable->name . '!')
                    ->line('Thank you for registering with Ballie. We\'re excited to have you on board!')
                    ->line('To get started, please verify your email address using the verification code below:')
                    ->line('**Verification Code: ' . $this->verificationCode . '**')
                    ->line('This code will expire in 60 minutes.')
                    ->action('Verify Email Now', route('verification.notice'))
                    ->line('If you did not create an account, no further action is required.')
                    ->salutation('Best regards, The Ballie Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'verification_code' => $this->verificationCode,
        ];
    }
}
