<?php

namespace App\Notifications;

use App\Models\SuperAdmin;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Spatie\Multitenancy\Jobs\NotTenantAware;

class NewUserRegisteredNotification extends Notification implements ShouldQueue, NotTenantAware
{
    use Queueable;

    public function __construct(
        public User $user,
        public Tenant $tenant
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        if ($notifiable instanceof SuperAdmin) {
            return ['mail', 'database'];
        }

        // On-demand (anonymous) notifiable — email only
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('super-admin.tenants.show', $this->tenant->id);

        return (new MailMessage)
            ->subject('New User Registration - ' . $this->user->name)
            ->greeting('Hello Admin!')
            ->line('A new user has just registered on Ballie.')
            ->line('**Name:** ' . $this->user->name)
            ->line('**Email:** ' . $this->user->email)
            ->line('**Business:** ' . $this->tenant->name)
            ->line('**Plan:** ' . ($this->tenant->plan->name ?? 'N/A'))
            ->line('**Registered At:** ' . $this->user->created_at->format('d M Y, H:i'))
            ->action('View Tenant', $url)
            ->line('Log in to the admin panel to manage this account.');
    }

    /**
     * Get the array representation of the notification (database channel).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'       => 'New User Registered',
            'message'     => $this->user->name . ' has registered with business "' . $this->tenant->name . '".',
            'type'        => 'new_registration',
            'user_id'     => $this->user->id,
            'user_email'  => $this->user->email,
            'tenant_id'   => $this->tenant->id,
            'tenant_name' => $this->tenant->name,
            'action_url'  => route('super-admin.tenants.show', $this->tenant->id),
            'action_text' => 'View Tenant',
        ];
    }
}
