<?php
namespace App\Models;
use Illuminate\Database\Eloquent\{Builder,Model,SoftDeletes};
class TransportVehicle extends Model {use SoftDeletes; protected $fillable=['company_id','plate','internal_code','vehicle_type','brand','model','year','capacity','owner_type','owner_name','status','notes','created_by','updated_by']; public function scopeForCompany(Builder $q,int $id):Builder{return $q->where('company_id',$id);} public function routes(){return $this->hasMany(TransportRoute::class,'vehicle_id');}}
