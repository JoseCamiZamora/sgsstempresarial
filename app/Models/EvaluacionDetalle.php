<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluacionDetalle extends Model
{
    protected $table = 'evaluacion_detalles';

    protected $fillable = [
        'evaluacion_id', 
        'item_estandar_id', 
        'cumplimiento', 
        'observaciones'
    ];

    // Relación con la cabecera de la evaluación
    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }

    // Relación con el ítem del estándar normativo
    public function itemEstandar()
    {
        return $this->belongsTo(ItemEstandar::class, 'item_estandar_id');
    }
}
