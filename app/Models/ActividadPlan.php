<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadPlan extends Model
{
    protected $table = 'actividades_plan';
    protected $fillable = [
        'plan_trabajo_id', 'fase_phva', 'objetivo_especifico', 
        'actividad', 'recursos_necesarios', 'responsable_id'
    ];

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    // Una actividad tiene programación en varios meses
    public function cronograma()
    {
        return $this->hasMany(CronogramaActividad::class);
    }
    // Relación: Una actividad pertenece a un Plan de Trabajo
    public function plan()
    {
        return $this->belongsTo(PlanTrabajo::class, 'plan_trabajo_id');
    }
}
