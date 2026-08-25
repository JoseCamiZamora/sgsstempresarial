<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CronogramaActividad extends Model
{
    protected $table = 'cronogramas_actividad';
    protected $fillable = [
        'actividad_plan_id', 'mes', 'programado', 'ejecutado', 
        'fecha_ejecucion_real', 'evidencia_pdf', 'observaciones'
    ];

    public function actividad()
    {
        return $this->belongsTo(ActividadPlan::class, 'actividad_plan_id');
    }
}
