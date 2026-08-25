<?php
namespace App\Models;
use Illuminate\Database\Eloquent\{Builder,Model};
class TransportChecklistTemplate extends Model { protected $fillable=['company_id','name','blocks_on_critical_failure','status']; protected $casts=['blocks_on_critical_failure'=>'boolean']; public function scopeForCompany(Builder $q,int $id):Builder{return $q->where('company_id',$id);} public function items(){return $this->hasMany(TransportChecklistItem::class)->orderBy('sort_order');} }
