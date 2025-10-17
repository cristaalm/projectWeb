<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PointsModifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $originalPoints;
    public $newPoints;
    public $justify;

    public function __construct($user, $originalPoints, $newPoints, $justify)
    {
        $this->user = $user;
        $this->originalPoints = $originalPoints;
        $this->newPoints = $newPoints;
        $this->justify = $justify;
    }

    public function build()
    {
        return $this->subject('Tus puntos han sido actualizados')
                    ->view('emails.points-modified');
    }
}
