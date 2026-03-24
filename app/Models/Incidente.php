<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidente extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'fecha_incidente',
        'hora_incidente',
        'tipo_evento',
        'lugar_evento',
        'descripcion',
        'dias_incapacidad',
        'estado',
        'observaciones_sst'
    ];

    // Relación para saber quién reportó el incidente
    public function reportante()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}