<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LogoutNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $logoutLink;

    public function __construct($logoutLink)
    {
        $this->logoutLink = $logoutLink;
    }

    public function build()
    {
        return $this->subject('Logout Other Sessions')
                    ->view('emails.logout')
                    ->with('logoutLink', $this->logoutLink);
    }
}
