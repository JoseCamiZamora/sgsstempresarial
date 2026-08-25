<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class AttendanceAudit extends Model{public $timestamps=false;protected $fillable=['attendance_event_id','event','subject_type','subject_id','user_id','metadata','created_at'];protected $casts=['metadata'=>'array','created_at'=>'datetime'];}
