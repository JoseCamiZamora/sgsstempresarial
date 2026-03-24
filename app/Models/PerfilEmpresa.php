<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilEmpresa extends Model
{
    use HasFactory;

    // Forzamos el nombre de la tabla en español
    protected $table = 'perfil_empresa';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre_empresa',
        'nit',
        'direccion',
        'telefono',
        'correo_contacto',
        'representante_legal',
        'licencia_sst',
        'logo_path'
    ];
}