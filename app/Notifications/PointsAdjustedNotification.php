<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PointsAdjustedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $previousBalance,
        protected int $delta,
        protected int $newBalance,
        protected string $reason,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tus puntos han sido actualizados')
            ->view('emails.points-modified', [
                'user' => $notifiable,
                'previousBalance' => $this->previousBalance,
                'delta' => $this->delta,
                'newBalance' => $this->newBalance,
                'reason' => $this->reason,
            ]);
    }
}
