<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly User $user,
        private readonly string $previousEmail,
        private readonly string $newEmail,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('El correo de tu cuenta ha sido cambiado')
            ->view('emails.AlertChangeEmail', [
                'user' => $this->user,
                'previousEmail' => $this->previousEmail,
                'newEmail' => $this->newEmail,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
