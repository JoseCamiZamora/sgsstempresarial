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
        'tipo_accion',
        'requiere_firma_empleados',
    ];

    protected $casts = [
        'requiere_firma_empleados' => 'boolean',
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

    public function firmaRequerimientos()
    {
        return $this->hasMany(DocumentoFirmaRequerimiento::class);
    }

    public function ultimoRequerimientoFirma(): ?DocumentoFirmaRequerimiento
    {
        return $this->firmaRequerimientos()->latest('id')->first();
    }
}