<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class TrainingAudit extends Model{public $timestamps=false;protected $fillable=['company_id','event','subject_type','subject_id','user_id','metadata','created_at'];protected $casts=['metadata'=>'array','created_at'=>'datetime'];}
