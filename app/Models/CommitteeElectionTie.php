<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class CommitteeElectionTie extends Model {protected $fillable=['election_id','candidate_ids','votes','affected_positions','status','resolution_method','observations','resolved_by','resolved_at'];protected $casts=['candidate_ids'=>'array','resolved_at'=>'datetime'];public function election(){return $this->belongsTo(CommitteeElection::class);}}
