<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemEstandar extends Model
{
    use HasFactory;

   // Asegúrate de que tu tabla esté bien referenciada
    protected $table = 'item_estandares';

    // 👇 ESTA ES LA CLAVE: La lista de campos permitidos
    protected $fillable = [
        'ciclo',
        'numeral',
        'nombre',
        'modo_verificacion',
        'tipo_plantilla',
        'porcentaje',
        'activo',
    ];
}