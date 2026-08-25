<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CommitteeMemberRole extends Model {protected $fillable=['committee_period_id','committee_member_id','role','starts_at','ends_at','designation_method','designation_date','observations','created_by'];protected $casts=['starts_at'=>'date','ends_at'=>'date','designation_date'=>'date'];public function member(){return $this->belongsTo(CommitteeMember::class,'committee_member_id');}public function period(){return $this->belongsTo(CommitteePeriod::class,'committee_period_id');}}
