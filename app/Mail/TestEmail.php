<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageText;

    public function __construct($message = null)
    {
        $this->messageText = $message ?? 'This is a test email from Ballie.';
    }

    public function build()
    {
        return $this->subject('Ballie Test Email')
                    ->view('emails.test_email')
                    ->with(['body' => $this->messageText]);
    }
}
