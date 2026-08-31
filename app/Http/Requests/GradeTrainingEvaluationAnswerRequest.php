<?php
namespace App\Http\Requests;use Illuminate\Foundation\Http\FormRequest;
class GradeTrainingEvaluationAnswerRequest extends FormRequest{
 public function authorize(){return$this->user()?->can('capacitaciones.resultados.calificar')??false;}
 public function rules(){return['is_correct'=>'required|boolean','evaluator_observations'=>'nullable|string|max:2000'];}
 public function attributes(){return['is_correct'=>'calificación','evaluator_observations'=>'observación'];}
}
