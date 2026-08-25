<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TransportRouteStop extends Model {protected $fillable=['transport_route_id','stop_order','name','address_reference','planned_time','notes']; public function route(){return $this->belongsTo(TransportRoute::class,'transport_route_id');}}
