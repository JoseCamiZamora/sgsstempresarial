<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class TrainingSessionEvidence extends Model { protected $table='training_session_evidences'; protected $fillable=['company_id','training_session_id','evidence_type','title','file_path','original_name','mime_type','file_size','file_hash','is_required','uploaded_by']; protected $casts=['is_required'=>'boolean']; public function session(){return $this->belongsTo(TrainingSession::class,'training_session_id');} }
