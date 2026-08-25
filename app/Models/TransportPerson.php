<?php
namespace App\Models;
use Illuminate\Database\Eloquent\{Builder,Model,SoftDeletes};
class TransportPerson extends Model {use SoftDeletes; protected $table='transport_personnel'; protected $fillable=['company_id','person_type','employee_id','name','document_type','document_number','phone','provider','is_driver','is_monitor','status','notes','created_by','updated_by']; protected $casts=['is_driver'=>'boolean','is_monitor'=>'boolean']; public function scopeForCompany(Builder $q,int $id):Builder{return $q->where('company_id',$id);} public function employee(){return $this->belongsTo(Empleado::class);} public function getDisplayNameAttribute():string{return $this->employee?->nombre_completo ?: (string)$this->name;}}
