<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class CreateTrainingNeedFromGapRequest extends FormRequest{public function authorize():bool{return true;}public function rules():array{return['employee_id'=>'required|integer|exists:empleados,id','training_requirement_id'=>'required|integer|exists:training_requirements,id','description'=>'nullable|string|max:2000'];}}
