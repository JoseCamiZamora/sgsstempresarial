<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados'; // Asegúrate que coincida con tu migración

    protected $fillable = [
        'user_id', 'nombre_completo', 'cedula', 'email_personal', 'telefono',
        'cargo', 'area_departamento', 'tipo_contrato', 'fecha_ingreso', 'fecha_retiro', 'salario',
        'eps', 'afp', 'arl', 'caja_compensacion',
        'genero', 'rh', 'fecha_nacimiento', 'contacto_emergencia_nombre', 'contacto_emergencia_telefono',
        'talla_camisa', 'talla_pantalon', 'talla_calzado'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function documentos()
    {
        return $this->hasMany(EmpleadoDocumento::class);
    }
    public function entregasEpp() {
        return $this->hasMany(EntregaEpp::class);
    }
}
