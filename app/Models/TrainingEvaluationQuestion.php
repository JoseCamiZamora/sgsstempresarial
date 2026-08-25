<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class TrainingEvaluationQuestion extends Model{public $timestamps=false;protected $fillable=['training_evaluation_id','source_question_id','question_text','question_type','explanation','points','is_critical','sort_order'];protected $casts=['points'=>'decimal:2','is_critical'=>'boolean'];public function options(){return$this->hasMany(TrainingEvaluationQuestionOption::class,'evaluation_question_id')->orderBy('sort_order');}}
