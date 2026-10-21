<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CredencialesEmpleadoMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $empleado;
    public $passwordPlano;

    public function __construct($empleado, $passwordPlano)
    {
        $this->empleado = $empleado;
        $this->passwordPlano = $passwordPlano;
    }

    public function build()
    {
        return $this->subject('Tus credenciales de acceso - Madeart B2B')
                    ->view('emails.credenciales');
    }
}
