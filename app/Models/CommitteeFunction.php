<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class CommitteeFunction extends Model{protected $fillable=['committee_type','committee_id','code','name','description','source_type','regulation','article','phva_stage','is_mandatory','is_active','sort_order'];protected $casts=['is_mandatory'=>'boolean','is_active'=>'boolean'];}
