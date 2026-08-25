<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class TransportScheduleDay extends Model{public$timestamps=false;protected$fillable=['transport_schedule_id','day_of_week'];public function schedule(){return$this->belongsTo(TransportSchedule::class,'transport_schedule_id');}}
