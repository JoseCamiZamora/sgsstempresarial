<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EmpleadoPortalAudit extends Model {public $timestamps=false;protected $fillable=['empleado_id','event','ip_address','user_agent','metadata','created_at'];protected $casts=['metadata'=>'array','created_at'=>'datetime'];public function empleado(){return$this->belongsTo(Empleado::class);}}
