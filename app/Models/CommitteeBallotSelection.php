<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class CommitteeBallotSelection extends Model {public $timestamps=false;protected $fillable=['ballot_uuid','election_candidate_id'];public function candidate(){return $this->belongsTo(CommitteeElectionCandidate::class,'election_candidate_id');}}
