<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class CommitteeReport extends Model{protected $fillable=['committee_id','committee_period_id','report_type','period_start','period_end','status','summary','document_path','generated_by'];protected $casts=['period_start'=>'date','period_end'=>'date','summary'=>'array'];}
