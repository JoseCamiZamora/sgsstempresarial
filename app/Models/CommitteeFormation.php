<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CommitteeFormation extends Model {
 protected $fillable=['committee_id','committee_period_id','election_id','formation_date','effective_from','effective_to','communication_date','communication_reference','communication_notes','workers_count_snapshot','regulation_reference','regulation_snapshot','electoral_snapshot','status','act_number','act_status','act_path','act_hash','documento_id','formed_by','notes'];
 protected $casts=['formation_date'=>'date','effective_from'=>'date','effective_to'=>'date','communication_date'=>'date','regulation_snapshot'=>'array','electoral_snapshot'=>'array'];
 public function committee(){return $this->belongsTo(Committee::class);} public function period(){return $this->belongsTo(CommitteePeriod::class,'committee_period_id');} public function election(){return $this->belongsTo(CommitteeElection::class);} public function document(){return $this->belongsTo(Documento::class,'documento_id');} public function audits(){return $this->hasMany(CommitteeFormationAudit::class);}
}
