<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class CommitteeBallot extends Model {protected $primaryKey='uuid';public $incrementing=false;public $timestamps=false;protected $keyType='string';protected $fillable=['uuid','election_id','integrity_hash'];public function election(){return $this->belongsTo(CommitteeElection::class);}public function selections(){return $this->hasMany(CommitteeBallotSelection::class,'ballot_uuid','uuid');}}
