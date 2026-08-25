<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class TrainingEvaluationQuestionOption extends Model{public $timestamps=false;protected $fillable=['evaluation_question_id','option_text','is_correct','sort_order'];protected $casts=['is_correct'=>'boolean'];}
