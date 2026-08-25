<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class TrainingQuestionOption extends Model{public $timestamps=false;protected $fillable=['training_question_id','option_text','is_correct','sort_order'];protected $casts=['is_correct'=>'boolean'];public function question(){return$this->belongsTo(TrainingQuestion::class);}}
