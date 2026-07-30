<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountRestoredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $justification,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Cuenta activada')
            ->view('emails.user_activate', [
                'user' => $notifiable,
                'justification' => $this->justification,
            ]);
    }
}
