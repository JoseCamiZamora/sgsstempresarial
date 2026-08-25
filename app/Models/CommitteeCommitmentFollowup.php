<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class CommitteeCommitmentFollowup extends Model{protected $fillable=['commitment_id','followup_date','progress','comment','evidence_path','created_by'];protected $casts=['followup_date'=>'date'];}
