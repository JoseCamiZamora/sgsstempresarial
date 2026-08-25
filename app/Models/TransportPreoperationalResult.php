<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TransportPreoperationalResult extends Model { protected $fillable=['transport_preoperational_check_id','transport_checklist_item_id','result','observation']; public function item(){return $this->belongsTo(TransportChecklistItem::class,'transport_checklist_item_id');} }
