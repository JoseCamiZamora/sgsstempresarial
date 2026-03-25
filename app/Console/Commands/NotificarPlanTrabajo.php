<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NotificarPlanTrabajo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notificar-plan-trabajo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Buscamos actividades que venzan exactamente en 7 días
        $fechaObjetivo = now()->addDays(7)->format('Y-m-d');
        
        $actividades = \App\Models\PlanTrabajo::where('fecha_programada', $fechaObjetivo)
                        ->where('estado', 'pendiente')
                        ->get();

        foreach ($actividades as $actividad) {
            // Aquí enviamos el correo que ya configuramos
            Mail::to($actividad->responsable_email)->send(new \App\Mail\RecordatorioPlanMail($actividad));
        }
    }
}
