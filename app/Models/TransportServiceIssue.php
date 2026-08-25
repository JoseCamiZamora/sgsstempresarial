<?php
namespace App\Models;
use Illuminate\Database\Eloquent\{Builder,Model};
class TransportServiceIssue extends Model { protected $fillable=['company_id','transport_service_id','issue_type','severity','occurred_at','description','action_taken','status','reported_by','resolved_at','resolved_by']; protected $casts=['occurred_at'=>'datetime','resolved_at'=>'datetime']; public function scopeForCompany(Builder $q,int $id):Builder{return $q->where('company_id',$id);} public function service(){return $this->belongsTo(TransportService::class,'transport_service_id');} public function evidence(){return $this->hasMany(TransportIssueEvidence::class);} }
