<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EmpleadoPortalReferenceSignature extends Model {protected $fillable=['empleado_id','source','file_path','file_hash','captured_at','superseded_at'];protected $casts=['captured_at'=>'datetime','superseded_at'=>'datetime'];public function empleado(){return$this->belongsTo(Empleado::class);}}
