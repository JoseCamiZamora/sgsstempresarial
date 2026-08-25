<?php
namespace App\Models;
use Illuminate\Database\Eloquent\{Builder,Model,SoftDeletes};
class TransportPassenger extends Model {use SoftDeletes; protected $fillable=['company_id','passenger_type','name','identification','grade_group','responsible_name','responsible_phone','status','created_by','updated_by']; public function scopeForCompany(Builder $q,int $id):Builder{return $q->where('company_id',$id);} public function routes(){return $this->belongsToMany(TransportRoute::class,'transport_route_passengers')->withPivot(['transport_route_stop_id','direction','valid_from','valid_until','status'])->withTimestamps();}}
