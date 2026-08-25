<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TransportArrivalSignature extends Model { protected $fillable=['company_id','transport_service_id','file_path','file_hash','evidence_hash','evidence_version','signed_at','captured_by']; protected $casts=['signed_at'=>'datetime']; }
