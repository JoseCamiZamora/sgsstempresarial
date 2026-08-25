<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class CommitteeScheduleChange extends Model{public $timestamps=false;protected $fillable=['schedule_item_id','original_date','new_date','change_type','reason','changed_by','changed_at'];protected $casts=['original_date'=>'date','new_date'=>'date','changed_at'=>'datetime'];}
