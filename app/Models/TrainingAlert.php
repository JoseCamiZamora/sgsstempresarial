<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TrainingAlert extends Model { protected $fillable=['company_id','alert_key','category','type','severity','title','message','subject_type','subject_id','employee_id','due_at','status','last_detected_at','last_notified_at','acknowledged_at','acknowledged_by','resolved_at','resolved_by','resolution_notes']; protected $casts=['due_at'=>'datetime','last_detected_at'=>'datetime','last_notified_at'=>'datetime','acknowledged_at'=>'datetime','resolved_at'=>'datetime']; public function scopeForCompany($q,$id){return$q->where('company_id',$id);} public function employee(){return$this->belongsTo(Empleado::class);} public function subject(){return$this->morphTo();} }
