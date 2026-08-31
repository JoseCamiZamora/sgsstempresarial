<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    /**
     * Catálogo de prefijos documentales (tipo de documento) del listado maestro SST.
     * Clave = valor guardado en BD y usado en el código (ej: SST-FT-001).
     */
    public const PREFIJOS = [
        'PO' => 'PO - Política',
        'MA' => 'MA - Manual',
        'PR' => 'PR - Procedimiento',
        'IN' => 'IN - Instructivo',
        'FT' => 'FT - Formato',
        'PG' => 'PG - Programa',
        'PL' => 'PL - Plan',
        'RG' => 'RG - Reglamento',
        'MT' => 'MT - Matriz',
        'CA' => 'CA - Capacitación / Certificado',
        'OT' => 'OT - Otro',
    ];

    protected $fillable = [
        'titulo',
        'descripcion',
        'categoria',
        'prefijo',
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
        'fecha_vigencia_inicio' => 'date',
        'fecha_vigencia_fin' => 'date',
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