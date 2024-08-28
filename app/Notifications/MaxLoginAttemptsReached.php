<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaxLoginAttemptsReached extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $username;
    protected $ip;

    public function __construct($username, $ip)
    {
        $this->username = $username;
        $this->ip = $ip;
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
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Max Login Attempts Reached')
            ->line("User with username '{$this->username}' has reached the maximum login attempts.")
            ->line("IP Address: {$this->ip}")
            ->line('This may indicate a potential security threat.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
