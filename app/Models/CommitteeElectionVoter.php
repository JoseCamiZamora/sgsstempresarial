<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class CommitteeElectionVoter extends Model {protected $fillable=['election_id','employee_id','status','has_voted','voted_at','credential_hash','credential_expires_at','credential_used_at'];protected $casts=['has_voted'=>'boolean','voted_at'=>'datetime','credential_expires_at'=>'datetime','credential_used_at'=>'datetime'];public function election(){return $this->belongsTo(CommitteeElection::class);}public function employee(){return $this->belongsTo(Empleado::class,'employee_id');}}
