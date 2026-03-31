<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones'; // Asegúrate que coincida con tu migración

    protected $fillable = [
        'perfil_empresa_id',      // En lugar de empresa_id
        'fecha_evaluacion',       // En lugar de fecha_ejecucion
        'tipo_plantilla_aplicada',// En lugar de tipo_plantilla
        'puntaje_final',          // En lugar de puntaje_total
        'nivel_madurez',
        'evaluador',
        'user_id'
    ];

    // En Evaluacion.php
    public function empresa()
    {
        // El segundo parámetro debe ser el nombre exacto de la columna en tu tabla 'evaluaciones'
        return $this->belongsTo(\App\Models\PerfilEmpresa::class, 'perfil_empresa_id');
    }

    // Relación: Una evaluación tiene muchas respuestas
    public function respuestas()
    {
        return $this->hasMany(\App\Models\EvaluacionRespuesta::class);
    }

    // Relación con el Usuario (El que hizo la evaluación)
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}