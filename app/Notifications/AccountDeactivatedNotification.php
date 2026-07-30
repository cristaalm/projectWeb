<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDeactivatedNotification extends Notification implements ShouldQueue
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
            ->subject('Cuenta desactivada')
            ->view('emails.user_desactivate', [
                'user' => $notifiable,
                'justification' => $this->justification,
            ]);
    }
}
