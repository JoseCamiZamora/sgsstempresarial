<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class TrainingSessionChange extends Model { public $timestamps=false; protected $fillable=['training_session_id','change_type','old_start_at','new_start_at','old_end_at','new_end_at','reason','changed_by','changed_at']; protected $casts=['old_start_at'=>'datetime','new_start_at'=>'datetime','old_end_at'=>'datetime','new_end_at'=>'datetime','changed_at'=>'datetime']; }
