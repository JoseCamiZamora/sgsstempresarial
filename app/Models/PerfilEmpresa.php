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

    
    public function committees()
    {
        return $this->hasMany(Committee::class, 'perfil_empresa_id');
    }
    public function trainingNeeds() { return $this->hasMany(TrainingNeed::class, 'company_id'); }
    public function trainingPrograms() { return $this->hasMany(TrainingProgram::class, 'company_id'); }
    public function transportVehicles() { return $this->hasMany(TransportVehicle::class, 'company_id'); }
    public function transportRoutes() { return $this->hasMany(TransportRoute::class, 'company_id'); }
    public function transportPersonnel() { return $this->hasMany(TransportPerson::class, 'company_id'); }
    public function transportPassengers() { return $this->hasMany(TransportPassenger::class, 'company_id'); }
}
