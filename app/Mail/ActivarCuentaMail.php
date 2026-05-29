<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActivarCuentaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $enlaceActivacion;

    /**
     * Creamos la instancia del correo pasando el usuario
     */
    public function __construct(User $user)
    {
        $this->user = $user;

        // Generamos un enlace simulado para la activación (en producción apuntará a su frontend)
        // Por ahora lo mandamos a una ruta temporal con un token único de seguridad
        $tokenSeguro = sha1($user->email . $user->created_at);
        $this->enlaceActivacion = "http://localhost/api/usuarios/activar?email=" . urlencode($user->email) . "&token=" . $tokenSeguro;
    }

    /**
     * El asunto del correo
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✨ ¡Active su cuenta en D\'gala Uniformes! ✨',
        );
    }

    /**
     * Definimos la vista HTML del correo
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.activar_cuenta', // Crearemos esta vista en el siguiente paso
        );
    }
}
