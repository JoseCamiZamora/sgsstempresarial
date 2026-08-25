<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class TrainingEvaluationAnswer extends Model{protected $fillable=['training_evaluation_attempt_id','evaluation_question_id','selected_option_ids','practical_result','evaluator_observations','points_awarded','is_correct'];protected $casts=['selected_option_ids'=>'array','points_awarded'=>'decimal:2','is_correct'=>'boolean'];}
