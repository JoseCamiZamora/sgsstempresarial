<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class CommitteeDecision extends Model{protected $fillable=['meeting_id','agenda_item_id','description','decision_type','approved','notes'];protected $casts=['approved'=>'boolean'];}
