<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = config('app.url') . "/reset-password?token={$this->token}&email={$this->email}";

        return (new MailMessage)
            ->subject('Restablece tu contraseña')
            ->view('emails.reset-password-html', [
                'url' => $url,
                'user' => $notifiable
            ]);
    }
}
