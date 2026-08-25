<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class CommitteeOperationalAudit extends Model{public $timestamps=false;protected $fillable=['committee_id','event','subject_type','subject_id','user_id','metadata','created_at'];protected $casts=['metadata'=>'array','created_at'=>'datetime'];}
