<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Barryvdh\DomPDF\Facade\Pdf;

class EnviarFacturaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;

    /**
     * Creamos la instancia del correo pasando el pedido.
     */
    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    /**
     * Definimos el asunto del correo.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Confirmación de Compra - Factura #{$this->pedido->id} de D'gala",
        );
    }

    /**
     * Definimos la vista HTML del cuerpo del correo.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.confirmar_compra', // 👈 Ahorita creamos esta vista sencilla
        );
    }

    /**
     * 📎 Adjuntamos el PDF generado en tiempo de ejecución.
     */
    public function attachments(): array
    {
        // Generamos el PDF usando la misma plantilla Blade que hicimos antes
        $pdf = Pdf::loadView('pdf.factura', ['pedido' => $this->pedido]);

        return [
            Attachment::fromData(fn() => $pdf->output(), "Factura-Dgala-#{$this->pedido->id}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
