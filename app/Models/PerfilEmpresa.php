<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilEmpresa extends Model
{
    use HasFactory;

    // Forzamos el nombre de la tabla en español
    protected $table = 'perfil_empresas';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'razon_social',
        'nit',
        'representante_legal',
        'actividad_economica',
        'numero_trabajadores',
        'nivel_riesgo',
        'direccion',
        'telefono',
        'correo_contacto',
        'logo_path',
    ];

    
}