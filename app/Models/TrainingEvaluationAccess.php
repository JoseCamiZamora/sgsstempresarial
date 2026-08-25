<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class TrainingEvaluationAccess extends Model{protected $fillable=['training_evaluation_id','attendance_participant_id','token_hash','expires_at','last_accessed_at','enabled'];protected $casts=['expires_at'=>'datetime','last_accessed_at'=>'datetime','enabled'=>'boolean'];public function evaluation(){return$this->belongsTo(TrainingEvaluation::class,'training_evaluation_id');}public function participant(){return$this->belongsTo(AttendanceParticipant::class,'attendance_participant_id');}}
