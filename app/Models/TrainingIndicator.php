<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TrainingIndicator extends Model { protected $guarded=[]; protected $casts=['target'=>'float','warning_threshold'=>'float','critical_threshold'=>'float','is_active'=>'boolean']; public function scopeForCompany($q,$id){return$q->where('company_id',$id);} }
