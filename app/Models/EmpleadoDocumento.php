<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpleadoDocumento extends Model
{
    use HasFactory;
    
    protected $table = 'empleado_documentos';

    protected $fillable = [
        'empleado_id',
        'nombre_archivo',
        'tipo_documento',
        'ruta_archivo'
    ];

    public function documentos() {
        return $this->hasMany(EmpleadoDocumento::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
