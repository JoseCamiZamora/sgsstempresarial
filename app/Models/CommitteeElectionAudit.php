<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class CommitteeElectionAudit extends Model {public $timestamps=false;protected $fillable=['election_id','event','user_id','metadata','created_at'];protected $casts=['metadata'=>'array','created_at'=>'datetime'];}
