<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class TrainingSessionReminder extends Model { protected $fillable=['training_session_id','minutes_before','scheduled_for','sent_at','status','error']; protected $casts=['scheduled_for'=>'datetime','sent_at'=>'datetime']; public function session(){return $this->belongsTo(TrainingSession::class,'training_session_id');} }
