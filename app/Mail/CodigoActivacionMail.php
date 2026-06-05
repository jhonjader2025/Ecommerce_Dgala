<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CodigoActivacionMail extends Mailable
{
    use Queueable, SerializesModels;

    // Definimos la variable pública para que la vista Blade la pueda leer solo con poner {{ $codigo }}
    public $codigo;

    public function __construct($codigo)
    {
        $this->codigo = $codigo;
    }

    // Configura el asunto del correo
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu código de activación para D\'gala 👔',
        );
    }

    // Apunta a la plantilla HTML de Bootstrap que creamos antes
    public function content(): Content
    {
        return new Content(
            view: 'emails.activation_code',
        );
    }
}
