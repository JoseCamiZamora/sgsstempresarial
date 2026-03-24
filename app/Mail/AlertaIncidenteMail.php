<?php

namespace App\Mail;

use App\Models\Incidente;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlertaIncidenteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $incidente;

    // Recibimos el incidente cuando llamemos a esta clase
    public function __construct(Incidente $incidente)
    {
        $this->incidente = $incidente;
    }

    public function build()
    {
        return $this->subject('⚠️ ALERTA URGENTE: Nuevo Reporte de Incidente / Accidente')
                    ->view('emails.alerta_incidente');
    }
}