<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TrainingAlert extends Model { protected $guarded=[]; protected $casts=['due_at'=>'datetime','last_detected_at'=>'datetime','last_notified_at'=>'datetime','acknowledged_at'=>'datetime','resolved_at'=>'datetime']; public function scopeForCompany($q,$id){return$q->where('company_id',$id);} public function employee(){return$this->belongsTo(Empleado::class);} public function subject(){return$this->morphTo();} }
