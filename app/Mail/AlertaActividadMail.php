<?php

namespace App\Mail;

use App\Models\PlanTrabajo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlertaActividadMail extends Mailable
{
    use Queueable, SerializesModels;

    public $actividad;

    public function __construct(PlanTrabajo $actividad)
    {
        $this->actividad = $actividad;
    }

    public function build()
    {
        return $this->subject('📅 Recordatorio: Tienes una actividad del SGSST próxima a vencer')
                    ->view('emails.alerta_actividad');
    }
}