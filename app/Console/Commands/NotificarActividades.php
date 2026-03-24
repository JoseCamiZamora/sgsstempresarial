<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PlanTrabajo;
use App\Mail\AlertaActividadMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class NotificarActividades extends Command
{
    // El nombre del comando que usaremos en la terminal
    protected $signature = 'sgsst:notificar-actividades';
    protected $description = 'Envía correos a los responsables de actividades del plan de trabajo próximas a vencer';

    public function handle()
    {
        $hoy = Carbon::today();
        $proximaSemana = Carbon::today()->addDays(7);

        // Buscamos actividades "Pendientes" cuya fecha esté entre hoy y los próximos 7 días
        $actividadesProx = PlanTrabajo::with('responsable')
                            ->where('estado', 'Pendiente')
                            ->whereBetween('fecha_programada', [$hoy, $proximaSemana])
                            ->get();

        $enviados = 0;

        foreach ($actividadesProx as $actividad) {
            // Verificamos que el responsable tenga un correo
            if ($actividad->responsable && $actividad->responsable->email) {
                Mail::to($actividad->responsable->email)->send(new AlertaActividadMail($actividad));
                $enviados++;
            }
        }

        $this->info("¡Robot terminado! Se enviaron {$enviados} recordatorios.");
    }
}