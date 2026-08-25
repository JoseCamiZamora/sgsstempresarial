<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanTrabajo extends Model
{
    protected $table = 'planes_trabajo';
    protected $fillable = ['anio', 'objetivo_general', 'presupuesto_asignado', 'estado'];

    // Un plan tiene muchas actividades
    public function actividades()
    {
        return $this->hasMany(ActividadPlan::class);
    }
}
