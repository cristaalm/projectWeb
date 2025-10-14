<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    protected $signature = 'email:send-test {email : La dirección de correo a la que enviar el mensaje}';
    protected $description = 'Envía un correo de prueba a la dirección especificada';

    public function handle()
    {
        $toEmail = $this->argument('email');
        $fromAddress = config('mail.from.address'); // Usa lo definido en config/mail.php o .env
        $fromName = config('mail.from.name');

        Mail::raw('Este es un correo de prueba enviado desde un comando de Artisan.', function ($message) use ($toEmail, $fromAddress, $fromName) {
            $message->from($fromAddress, $fromName)
                    ->to($toEmail)
                    ->subject('Correo de Prueba - Laravel');
        });

        $this->info("Correo de prueba enviado a: {$toEmail} desde {$fromAddress}");
    }
}
