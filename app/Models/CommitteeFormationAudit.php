<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CommitteeFormationAudit extends Model {public $timestamps=false;protected $fillable=['committee_formation_id','event','user_id','metadata','created_at'];protected $casts=['metadata'=>'array','created_at'=>'datetime'];}
