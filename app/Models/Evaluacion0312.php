<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluacion0312 extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'fecha_evaluacion', 'puntaje_total', 'estado_resultado', 'respuestas'];
    
    protected $casts = [
        'respuestas' => 'array'
    ];

    // 👇 ESTA ES LA PIEZA QUE FALTA 👇
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Lógica del semáforo (la mantenemos igual)
    public static function calcularEstado($puntaje) {
        if ($puntaje < 60) return 'CRÍTICO';
        if ($puntaje >= 60 && $puntaje <= 85) return 'MODERADO';
        return 'ACEPTABLE';
    }
}