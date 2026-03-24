<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemEstandar extends Model
{
    use HasFactory;

    // 👇 AGREGA ESTA LÍNEA 👇
    protected $table = 'item_estandares';

    protected $fillable = ['nombre', 'porcentaje', 'activo'];
}