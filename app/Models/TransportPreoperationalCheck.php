<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TransportPreoperationalCheck extends Model { protected $fillable=['company_id','transport_service_id','transport_checklist_template_id','status','completed_at','completed_by','override_reason','override_by']; protected $casts=['completed_at'=>'datetime']; public function template(){return $this->belongsTo(TransportChecklistTemplate::class,'transport_checklist_template_id');} public function results(){return $this->hasMany(TransportPreoperationalResult::class);} }
