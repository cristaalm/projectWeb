<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private const WEB_ACCESS_ROLES = ['moderador', 'admin_merchant'];

    public function __construct(
        protected string $password,
        protected string $roleName,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $accessText = in_array($this->roleName, self::WEB_ACCESS_ROLES, true)
            ? 'Tu cuenta tiene acceso al panel web y a la aplicación móvil.'
            : 'Tu cuenta tiene acceso a la aplicación móvil.';

        return (new MailMessage)
            ->subject('Bienvenido a EcoSort — tus credenciales de acceso')
            ->view('emails.user-credentials', [
                'user' => $notifiable,
                'password' => $this->password,
                'accessText' => $accessText,
            ]);
    }
}
