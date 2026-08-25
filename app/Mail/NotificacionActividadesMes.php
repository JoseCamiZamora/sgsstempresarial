<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionActividadesMes extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $actividades;
    public $mesTexto;

    /**
     * Create a new message instance.
     */
    public function __construct($usuario, $actividades, $mesTexto)
    {
        $this->usuario = $usuario;
        $this->actividades = $actividades;
        $this->mesTexto = $mesTexto;
    }

    /**
     * Get the message envelope (El Asunto).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔔 Tus actividades SGSST para el mes de ' . $this->mesTexto,
        );
    }

    /**
     * Get the message content definition (La Vista).
     */
    public function content(): Content
    {
        return new Content(
            // ¡Aquí es donde arreglamos el error! Reemplazamos 'view.name' por tu vista real
            view: 'emails.actividades_mes',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
