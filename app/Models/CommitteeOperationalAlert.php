<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class CommitteeOperationalAlert extends Model{protected $fillable=['committee_id','alert_type','reference_type','reference_id','alert_date','severity','message','resolved','notified_at'];protected $casts=['alert_date'=>'date','resolved'=>'boolean','notified_at'=>'datetime'];}
