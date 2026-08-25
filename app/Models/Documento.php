<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'categoria',
        'archivo_ruta',
        'extension_archivo',
        'subido_por',
        'codigo',
        'nombre_archivo',
        'fecha_vigencia_inicio',
        'fecha_vigencia_fin',
        'version',
        'tipo_accion'
    ];

    // Relación para saber qué administrador subió el documento
    public function autor()
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    public function cambios()
    {
        return $this->hasMany(DocumentoCambio::class);
    }
}