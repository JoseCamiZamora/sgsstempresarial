<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;class AttendanceSignature extends Model{protected $fillable=['attendance_record_id','signature_method','file_path','file_hash','signed_at','verification_method','consent_text_version'];protected $casts=['signed_at'=>'datetime'];public function record(){return$this->belongsTo(AttendanceRecord::class,'attendance_record_id');}}
