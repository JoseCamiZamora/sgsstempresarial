<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionRespuesta extends Model
{
    use HasFactory;

    protected $table = 'evaluacion_respuestas';

    protected $fillable = [
        'evaluacion_id',
        'item_estandar_id', // El ID del estándar/pregunta
        'calificacion',    // Cumple, No Cumple, N/A, etc.
        'puntaje_obtenido',
        'observaciones',
    ];

    // 👇 ESTA ES LA RELACIÓN QUE FALTA 👇
    public function itemEstandar()
    {
        // Relacionamos con el modelo ItemEstandar usando la columna item_estandar_id
        return $this->belongsTo(ItemEstandar::class, 'item_estandar_id');
    }

    // Opcional: Relación inversa con la evaluación
    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }
}