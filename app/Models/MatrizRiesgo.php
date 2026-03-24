<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatrizRiesgo extends Model
{
    use HasFactory;

    // 1. Le decimos qué tabla es (por si acaso)
    protected $table = 'matriz_riesgos';

    // 2. Definimos los campos que se pueden llenar desde el formulario
    protected $fillable = [
        'proceso',
        'zona_lugar',
        'actividad',
        'es_rutinaria',
        'clasificacion_peligro',
        'descripcion_peligro',
        'efectos_posibles',
        'nivel_riesgo',
        'registrado_por'
    ];

    // 3. Creamos la relación para saber qué usuario registró este riesgo
    public function responsable()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}