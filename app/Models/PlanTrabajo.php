<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanTrabajo extends Model
{
    use HasFactory;

    // Los campos que permitimos llenar desde el formulario
    protected $fillable = [
        'actividad',
        'fecha_programada',
        'responsable_id',
        'estado',
        'evidencia_pdf',
        'observaciones'
    ];

    // Relación: Una actividad pertenece a un usuario (responsable)
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}