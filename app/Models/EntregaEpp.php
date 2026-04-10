<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaEpp extends Model
{
    use HasFactory;
    protected $table = 'entrega_epps';
    protected $fillable = [
        'empleado_id', 
        'epp_id', 
        'fecha_entrega', 
        'motivo', 
        'cantidad', 
        'talla_entregada', 
        'observaciones'
    ];

    /**
     * Relación: La entrega pertenece a un Empleado.
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    /**
     * Relación: La entrega incluye un EPP del catálogo.
     */
    public function epp()
    {
        return $this->belongsTo(Epp::class, 'epp_id');
    }
}
