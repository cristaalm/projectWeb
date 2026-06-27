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

        Mail::raw('Correo de prueba enviado desde EcoSort vía Brevo.', function ($message) use ($toEmail) {
            $message->to($toEmail)->subject('Correo de Prueba - EcoSort');
        });

        $this->info("Correo enviado correctamente a: {$toEmail}");
    }
}
