<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TransportChecklistItem extends Model { protected $fillable=['transport_checklist_template_id','label','sort_order','is_critical','is_required']; protected $casts=['is_critical'=>'boolean','is_required'=>'boolean']; public function template(){return $this->belongsTo(TransportChecklistTemplate::class,'transport_checklist_template_id');} }
