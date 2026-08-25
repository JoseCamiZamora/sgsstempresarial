<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoCambio extends Model
{
    use HasFactory;

    protected $table = 'documento_cambios';

    protected $fillable = [
        'documento_id',
        'version',
        'fecha_vigencia_inicio',
        'fecha_vigencia_fin',
        'tipo_cambio',
        'observaciones',
        'registrado_por'
    ];

    protected $casts = [
        'fecha_vigencia_inicio' => 'date',
        'fecha_vigencia_fin' => 'date',
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}