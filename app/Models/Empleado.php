<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados'; // Asegúrate que coincida con tu migración

    protected $fillable = [
        'company_id', 'user_id', 'nombre_completo', 'cedula', 'email_personal', 'telefono',
        'cargo', 'area_departamento', 'tipo_contrato', 'fecha_ingreso', 'fecha_retiro', 'salario',
        'eps', 'afp', 'arl', 'caja_compensacion',
        'genero', 'rh', 'fecha_nacimiento', 'contacto_emergencia_nombre', 'contacto_emergencia_telefono',
        'talla_camisa', 'talla_pantalon', 'talla_calzado'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(PerfilEmpresa::class, 'company_id');
    }
    public function documentos()
    {
        return $this->hasMany(EmpleadoDocumento::class);
    }
    public function entregasEpp() {
        return $this->hasMany(EntregaEpp::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereNull('fecha_retiro')->orWhereDate('fecha_retiro', '>=', now()->toDateString());
        });
    }

    public function committeeMemberships()
    {
        return $this->hasMany(CommitteeMember::class, 'employee_id');
    }

    public function committeeCandidacies()
    {
        return $this->hasMany(CommitteeCandidate::class, 'employee_id');
    }

    public function transportProfile()
    {
        return $this->hasOne(TransportPerson::class, 'employee_id');
    }

    public function attendanceParticipants()
    {
        return $this->hasMany(AttendanceParticipant::class, 'employee_id');
    }

    public function portalCredential()
    {
        return $this->hasOne(EmpleadoPortalCredential::class);
    }

    public function portalAudits()
    {
        return $this->hasMany(EmpleadoPortalAudit::class);
    }

    public function portalReferenceSignatures()
    {
        return $this->hasMany(EmpleadoPortalReferenceSignature::class);
    }
}
