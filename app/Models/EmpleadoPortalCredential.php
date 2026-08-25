<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EmpleadoPortalCredential extends Model {protected $fillable=['empleado_id','code_hash','code_generated_at','generated_by','failed_attempts','locked_until','last_used_at','revoked_at'];protected $casts=['code_generated_at'=>'datetime','locked_until'=>'datetime','last_used_at'=>'datetime','revoked_at'=>'datetime'];public function empleado(){return$this->belongsTo(Empleado::class);}public function generator(){return$this->belongsTo(User::class,'generated_by');}}
