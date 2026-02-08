<?php

namespace App\Notifications;

use App\Models\CustomPlanInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomPlanInquiryNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected CustomPlanInquiry $inquiry
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🌟 New Custom Plan Inquiry: ' . $this->inquiry->business_name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new Custom Plan inquiry has been submitted.')
            ->line('**Business:** ' . $this->inquiry->business_name)
            ->line('**Contact:** ' . $this->inquiry->contact_name)
            ->line('**Email:** ' . $this->inquiry->email)
            ->line('**Phone:** ' . $this->inquiry->phone)
            ->line('**Interest:** ' . $this->inquiry->interest_label)
            ->line('**Companies:** ' . ($this->inquiry->num_companies ?: 'Not specified'))
            ->line('**Requirements:** ' . ($this->inquiry->requirements ?: 'None provided'))
            ->action('View in Admin Panel', url('/super-admin/custom-inquiries'))
            ->line('Please follow up within 24 hours.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'custom_plan_inquiry',
            'inquiry_id' => $this->inquiry->id,
            'business_name' => $this->inquiry->business_name,
            'contact_name' => $this->inquiry->contact_name,
            'email' => $this->inquiry->email,
            'phone' => $this->inquiry->phone,
            'interest' => $this->inquiry->interest_label,
            'message' => 'New custom plan inquiry from ' . $this->inquiry->business_name,
        ];
    }
}
