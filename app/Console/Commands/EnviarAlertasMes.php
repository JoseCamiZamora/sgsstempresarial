<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CronogramaActividad;
use App\Models\User;
use App\Mail\NotificacionActividadesMes;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EnviarAlertasMes extends Command
{
    // El nombre del comando que usaremos en la terminal
    protected $signature = 'sgsst:alertas-mes';
    protected $description = 'Envía un correo a los responsables con sus actividades del mes en curso';

    public function handle()
    {
        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;
        
        $nombresMeses = [1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto', 9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre'];
        $mesTexto = $nombresMeses[$mesActual];

        // 1. Buscamos todas las actividades programadas para ESTE MES, de ESTE AÑO, que NO se han ejecutado
        $actividadesPendientes = CronogramaActividad::with(['actividad.responsable', 'actividad.plan'])
            ->where('mes', $mesActual)
            ->where('programado', true)
            ->where('ejecutado', false)
            ->whereHas('actividad.plan', function($q) use ($anioActual) {
                $q->where('anio', $anioActual);
            })
            ->get();

        if ($actividadesPendientes->isEmpty()) {
            $this->info('No hay actividades pendientes para este mes.');
            return;
        }

        // 2. Agrupamos mágicamente por el ID del responsable
        $actividadesPorUsuario = $actividadesPendientes->groupBy('actividad.responsable_id');

        // 3. Enviamos UN solo correo por usuario
        foreach ($actividadesPorUsuario as $userId => $actividades) {
            $usuario = User::find($userId);
            
            if ($usuario && $usuario->email) {
                Mail::to($usuario->email)->send(new NotificacionActividadesMes($usuario, $actividades, $mesTexto));
                $this->info('Correo enviado a: ' . $usuario->email);
            }
        }

        $this->info('Todas las alertas del mes enviadas correctamente.');
    }
}
