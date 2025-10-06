<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentsVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $status,
        public ?string $justification = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        if ($this->status === 1) {
            // APROBADO
            return (new MailMessage)
                ->subject('Tus documentos han sido aprobados')
                ->view('emails.documents-approved', ['user' => $notifiable]);
        } else {
            // RECHAZADO (status === 2)
            return (new MailMessage)
                ->subject('Tus documentos han sido rechazados')
                ->view('emails.documents-rejected', [
                    'user' => $notifiable,
                    'justification' => $this->justification,
                ]);
        }
    }
}
