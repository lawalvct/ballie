<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function build()
    {
        return $this->subject('Welcome to Ballie - Your Business Setup is Complete!')
                    ->view('emails.welcome')
                    ->with([
                        'tenant' => $this->tenant,
                        'dashboardUrl' => route('tenant.dashboard', $this->tenant->slug)
                    ]);
    }
}