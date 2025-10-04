<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserStatusAccountNotification extends Notification
{
    use Queueable;

    /**
     * The status of the user (true for activated, false for deactivated).
     *
     * @var bool
     */
    protected $status;

    /**
     * Justification for deactivation (only required when status is false).
     *
     * @var string|null
     */
    protected $justification;

    /**
     * Create a new notification instance.
     */
    public function __construct(bool $status, ?string $justification = null)
    {
        $this->status = $status;
        $this->justification = $justification;
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
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->status) {
            // Usuario activado
            return (new MailMessage)
                ->subject('Cuenta activada')
                ->view('emails.user_activate', [
                    'user' => $notifiable,
                ]);
        } else {
            // Usuario desactivado: se requiere justificación
            return (new MailMessage)
                ->subject('Cuenta desactivada')
                ->view('emails.user_desactivate', [
                    'user' => $notifiable,
                    'justification' => $this->justification,
                ]);
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'status' => $this->status,
            'justification' => $this->justification,
        ];
    }
}
