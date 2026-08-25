<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class AttendanceEvidence extends Model{protected $table='attendance_evidences';protected $fillable=['attendance_event_id','version','file_path','file_hash','generated_at','generated_by'];protected $casts=['generated_at'=>'datetime'];public function event(){return$this->belongsTo(AttendanceEvent::class,'attendance_event_id');}}
