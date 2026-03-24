<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadisticaMensual extends Model
{
    use HasFactory;

    // 👇 ESTO ASEGURA QUE LARAVEL SIEMPRE BUSQUE LA TABLA CORRECTA 👇
    protected $table = 'estadistica_mensuales';

    protected $fillable = [
        'mes', 
        'anio', 
        'num_trabajadores', 
        'horas_trabajadas', 
        'dias_programados'
    ];
}
