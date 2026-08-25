<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EmpleadoPortalSignatureEvent extends Model {protected $fillable=['uuid','empleado_id','signable_type','signable_id','reference_signature_source','reference_signature_id','file_path','file_hash','signed_at','document_version_snapshot','evidence_hash','verification_code','signed_from_ip','user_agent'];protected $casts=['signed_at'=>'datetime'];public function empleado(){return$this->belongsTo(Empleado::class);}public function referenceSignature(){return$this->belongsTo(EmpleadoPortalReferenceSignature::class,'reference_signature_id');}}
